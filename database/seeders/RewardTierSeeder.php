<?php

namespace Database\Seeders;

use App\Models\RewardProgram;
use Illuminate\Database\Seeder;

class RewardTierSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $programs = [
            [
                'name' => 'Default Points Program',
                'description' => 'Earn 1 point for every 100k spent. Redeem 100 points for 10k discount.',
                'type' => 'points',
                'earn_rule' => json_encode(['per_amount' => 100000, 'points' => 1]),
                'redeem_rule' => json_encode(['points' => 100, 'discount' => 10000]),
                'points_per_transaction' => 0,
                'min_transaction_amount' => 100000,
                'is_active' => true,
            ],
            [
                'name' => 'New Customer Bonus',
                'description' => 'Get 50 bonus points on first transaction over 500k',
                'type' => 'gift',
                'earn_rule' => json_encode(['points' => 50]),
                'redeem_rule' => null,
                'points_per_transaction' => 50,
                'min_transaction_amount' => 500000,
                'is_active' => true,
            ]
        ];

        foreach ($programs as $program) {
            RewardProgram::updateOrCreate(['name' => $program['name']], $program);
        }
    }
}
