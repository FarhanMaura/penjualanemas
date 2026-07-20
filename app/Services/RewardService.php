<?php

namespace App\Services;

use App\Models\CustomerReward;
use App\Models\Transaction;
use App\Models\User;

class RewardService
{
    /**
     * Tier thresholds berdasarkan jumlah transaksi selesai (bukan spending).
     * Sesuai konsep bisnis toko: reward ke-10 transaksi.
     */
    public const TIER_BY_TRANSACTIONS = [
        'bronze'   => 0,
        'silver'   => 5,
        'gold'     => 10,
        'platinum' => 20,
    ];

    /**
     * Tambahkan 1 poin dan perbarui tier setelah transaksi selesai.
     */
    public function awardPoint(User $user, Transaction $transaction): void
    {
        $reward = CustomerReward::firstOrCreate(
            ['user_id' => $user->id],
            [
                'current_points'        => 0,
                'total_earned_points'   => 0,
                'total_redeemed_points' => 0,
                'tier'                  => 'bronze',
                'lifetime_spending'     => 0,
            ]
        );

        // Tambah poin & spending
        $reward->increment('current_points', 1);
        $reward->increment('total_earned_points', 1);
        $reward->increment('lifetime_spending', $transaction->total_amount);

        // Recalculate tier berdasarkan jumlah transaksi selesai
        $this->recalculateTierByTransactions($user, $reward->fresh());
    }

    /**
     * Hitung tier berdasarkan total transaksi completed.
     */
    public function recalculateTierByTransactions(User $user, CustomerReward $reward): void
    {
        $completedCount = Transaction::where('user_id', $user->id)
            ->where('status', 'completed')
            ->count();

        $newTier = 'bronze';
        foreach (self::TIER_BY_TRANSACTIONS as $tier => $minTransactions) {
            if ($completedCount >= $minTransactions) {
                $newTier = $tier;
            }
        }

        if ($reward->tier !== $newTier) {
            $reward->update([
                'tier'            => $newTier,
                'tier_updated_at' => now(),
            ]);
        }
    }

    /**
     * Cek apakah user layak mendapat reward milestone (ke-10 transaksi).
     */
    public function checkMilestoneReward(User $user): bool
    {
        $completedCount = Transaction::where('user_id', $user->id)
            ->where('status', 'completed')
            ->count();

        // Berlaku setiap kelipatan 10
        return $completedCount > 0 && $completedCount % 10 === 0;
    }

    /**
     * Ambil data reward + progress ke tier berikutnya.
     */
    public function getRewardSummary(User $user): array
    {
        $reward = CustomerReward::firstOrCreate(
            ['user_id' => $user->id],
            ['tier' => 'bronze', 'current_points' => 0, 'total_earned_points' => 0,
             'total_redeemed_points' => 0, 'lifetime_spending' => 0]
        );

        $completedCount = Transaction::where('user_id', $user->id)
            ->where('status', 'completed')
            ->count();

        $tiers = array_keys(self::TIER_BY_TRANSACTIONS);
        $currentIdx = array_search($reward->tier, $tiers);
        $nextTier   = $tiers[$currentIdx + 1] ?? null;
        $nextMin    = $nextTier ? self::TIER_BY_TRANSACTIONS[$nextTier] : null;
        $progressPct = $nextMin
            ? min(100, round(($completedCount / $nextMin) * 100))
            : 100;

        return [
            'reward'           => $reward,
            'completed_count'  => $completedCount,
            'next_tier'        => $nextTier,
            'next_min'         => $nextMin,
            'transactions_left'=> $nextMin ? max(0, $nextMin - $completedCount) : 0,
            'progress_pct'     => $progressPct,
            'milestone_eligible' => $this->checkMilestoneReward($user),
        ];
    }
}
