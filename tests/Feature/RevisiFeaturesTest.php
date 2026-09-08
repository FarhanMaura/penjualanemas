<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\CustomerReward;
use App\Models\GoldPrice;
use App\Models\Notification;
use App\Models\Product;
use App\Models\Reservation;
use App\Models\Transaction;
use App\Models\User;
use App\Services\RewardService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RevisiFeaturesTest extends TestCase
{
    use RefreshDatabase;

    protected User $customer;
    protected User $admin;
    protected Product $product;
    protected GoldPrice $goldPrice;

    protected function setUp(): void
    {
        parent::setUp();

        $this->customer = User::factory()->create([
            'role'  => 'customer',
            'email' => 'customer_test@example.com',
        ]);

        $this->admin = User::factory()->create([
            'role'  => 'admin',
            'email' => 'admin_test@example.com',
        ]);

        $cat = Category::create([
            'name' => 'Cincin',
            'slug' => 'cincin',
        ]);

        $this->product = Product::create([
            'category_id'    => $cat->id,
            'name'           => 'Cincin Emas 24K Murni',
            'slug'           => 'cincin-emas-24k-murni',
            'sku'            => 'CNC-24K-TEST',
            'gold_purity'    => '24K',
            'weight_gram'    => 2.5,
            'base_price'     => 3000000,
            'buy_back_price' => 2900000,
            'stock'          => 10,
            'is_available'   => true,
            'is_reservable'  => true,
            'is_basic'       => true,
        ]);

        $this->goldPrice = GoldPrice::create([
            'price_date'          => today(),
            'buy_price_per_gram'  => 1200000,
            'sell_price_per_gram' => 1350000,
            'recorded_by'         => $this->admin->id,
        ]);
    }

    /**
     * Test User Request 2: Fitur Dropdown Menu Jual Emas (Buyback)
     */
    public function test_customer_can_create_buyback_reservation(): void
    {
        $response = $this->actingAs($this->customer)->post(route('customer.reservations.store'), [
            'type'                   => 'buyback',
            'preferred_date'         => today()->addDays(2)->toDateString(),
            'preferred_time'         => '14:00',
            'payment_method'         => 'transfer',
            'pawn_gold_description'  => 'Kalung Emas 24K Warisan',
            'pawn_gold_purity'       => '24K',
            'pawn_weight_gram'       => 5.0,
            'notes'                  => 'Mau jual emas batangan',
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect(route('customer.reservations.index'));
        $this->assertDatabaseHas('reservations', [
            'user_id'               => $this->customer->id,
            'type'                  => 'buyback',
            'pawn_gold_description' => 'Kalung Emas 24K Warisan',
            'pawn_weight_gram'      => 5.0,
        ]);
    }

    /**
     * Test User Request 4: Halaman Notifikasi & Mark as Read
     */
    public function test_customer_and_admin_notifications(): void
    {
        $notif = Notification::create([
            'user_id' => $this->customer->id,
            'type'    => 'reward.earned',
            'title'   => 'Bonus Poin',
            'message' => 'Anda mendapat 1 poin.',
            'data'    => [],
        ]);

        // Customer view notifications page
        $res = $this->actingAs($this->customer)->get(route('customer.notifications.index'));
        $res->assertOk();
        $res->assertSee('Bonus Poin');

        // Customer mark as read
        $readRes = $this->actingAs($this->customer)->post(route('customer.notifications.read', $notif));
        $readRes->assertRedirect();
        $this->assertNotNull($notif->fresh()->read_at);

        // Admin view notifications page
        $adminNotif = Notification::create([
            'user_id' => $this->admin->id,
            'type'    => 'reservation.created',
            'title'   => 'Reservasi Masuk',
            'message' => 'Ada reservasi baru.',
            'data'    => [],
        ]);

        $adminRes = $this->actingAs($this->admin)->get(route('admin.notifications.index'));
        $adminRes->assertOk();
        $adminRes->assertSee('Reservasi Masuk');
    }

    /**
     * Test User Request 3: Reward Konsistensi Dashboard & Awarding
     */
    public function test_reward_service_and_dashboard_consistency(): void
    {
        $rewardService = app(RewardService::class);

        // Create 2 completed transactions
        for ($i = 0; $i < 2; $i++) {
            $trx = Transaction::create([
                'transaction_code' => 'TRX-TEST-' . ($i + 1),
                'user_id'          => $this->customer->id,
                'type'             => 'purchase',
                'gold_price_id'    => $this->goldPrice->id,
                'subtotal'         => 1000000,
                'total_amount'     => 1000000,
                'payment_method'   => 'cash',
                'payment_date'     => today(),
                'status'           => 'completed',
                'processed_by'     => $this->admin->id,
            ]);
            $rewardService->awardPoint($this->customer, $trx);
        }

        $summary = $rewardService->getRewardSummary($this->customer);
        $this->assertEquals(2, $summary['completed_count']);
        $this->assertEquals(2, $summary['reward']->current_points);

        $res = $this->actingAs($this->customer)->get(route('customer.dashboard'));
        $res->assertOk();
        $res->assertSee('Bronze');
        $res->assertSee('2 transaksi');
    }

    /**
     * Test User Request 5: Backend Total Transaksi, Pembelian, dan Penjualan (Buyback)
     */
    public function test_transaction_summary_includes_buyback_and_purchase(): void
    {
        // 1 Purchase
        Transaction::create([
            'transaction_code' => 'TRX-PURCHASE-1',
            'user_id'          => $this->customer->id,
            'type'             => 'purchase',
            'gold_price_id'    => $this->goldPrice->id,
            'subtotal'         => 5000000,
            'total_amount'     => 5000000,
            'payment_method'   => 'cash',
            'payment_date'     => today(),
            'status'           => 'completed',
            'processed_by'     => $this->admin->id,
        ]);

        // 1 Buyback (Customer sells gold to shop)
        Transaction::create([
            'transaction_code' => 'TRX-BUYBACK-1',
            'user_id'          => $this->customer->id,
            'type'             => 'buyback',
            'gold_price_id'    => $this->goldPrice->id,
            'subtotal'         => 3000000,
            'total_amount'     => 3000000,
            'payment_method'   => 'transfer',
            'payment_date'     => today(),
            'status'           => 'completed',
            'processed_by'     => $this->admin->id,
        ]);

        // Customer Transactions page
        $res = $this->actingAs($this->customer)->get(route('customer.transactions.index'));
        $res->assertOk();
        $res->assertSee('Rp 5.000.000'); // Total Pembelian Emas
        $res->assertSee('Rp 3.000.000'); // Total Penjualan Emas (Pemasukan)

        // Admin Reports page
        $adminRes = $this->actingAs($this->admin)->get(route('admin.reports.index'));
        $adminRes->assertOk();
        $adminRes->assertSee('Rp 5.000.000'); // Omzet Pembelian
        $adminRes->assertSee('Rp 3.000.000'); // Total Buyback
    }

    /**
     * Test Poin 1 & 6: Customer Reservasi Cicilan (Tanpa Jadwal Kunjungan & Tanpa DP)
     */
    public function test_customer_can_create_installment_reservation_without_visit_schedule(): void
    {
        $res = $this->actingAs($this->customer)->post(route('customer.reservations.store'), [
            'type'               => 'installment',
            'product_id'         => $this->product->id,
            'quantity'           => 1,
            'payment_method'     => 'transfer',
            'installment_tenure' => 3,
            'notes'              => 'Pengajuan cicilan emas 3 bulan',
        ]);

        $res->assertSessionHasNoErrors();
        $res->assertRedirect(route('customer.reservations.index'));
        $this->assertDatabaseHas('reservations', [
            'user_id'            => $this->customer->id,
            'type'               => 'installment',
            'product_id'         => $this->product->id,
            'installment_tenure' => 3,
        ]);
    }

    /**
     * Test Poin 2: Admin CRUD Metode Pembayaran (Nomor Rekening, Bank, A.N.)
     */
    public function test_admin_can_manage_payment_methods(): void
    {
        // 1. Admin Store
        $storeRes = $this->actingAs($this->admin)->post(route('admin.payment-methods.store'), [
            'name'           => 'Bank BCA Bisnis',
            'code'           => 'bca_bisnis',
            'type'           => 'bank_transfer',
            'bank_name'      => 'Bank Central Asia',
            'account_number' => '1234567890',
            'account_name'   => 'TOKO EMAS SINAR BARU II',
            'instructions'   => 'Transfer via KlikBCA',
            'is_active'      => 1,
            'sort_order'     => 1,
        ]);
        $storeRes->assertRedirect(route('admin.payment-methods.index'));
        $this->assertDatabaseHas('payment_methods', [
            'code'           => 'bca_bisnis',
            'account_number' => '1234567890',
        ]);

        $pm = \App\Models\PaymentMethod::where('code', 'bca_bisnis')->first();

        // 2. Admin Index
        $indexRes = $this->actingAs($this->admin)->get(route('admin.payment-methods.index'));
        $indexRes->assertOk();
        $indexRes->assertSee('Bank BCA Bisnis');
        $indexRes->assertSee('1234567890');

        // 3. Admin Update
        $updateRes = $this->actingAs($this->admin)->put(route('admin.payment-methods.update', $pm), [
            'name'           => 'Bank BCA Bisnis Updated',
            'code'           => 'bca_bisnis',
            'type'           => 'bank_transfer',
            'bank_name'      => 'BCA Syariah',
            'account_number' => '999888777',
            'account_name'   => 'SINAR BARU PUSAT',
            'instructions'   => 'Transfer via mobile',
            'is_active'      => 1,
            'sort_order'     => 2,
        ]);
        $updateRes->assertRedirect(route('admin.payment-methods.index'));
        $this->assertDatabaseHas('payment_methods', [
            'id'             => $pm->id,
            'name'           => 'Bank BCA Bisnis Updated',
            'account_number' => '999888777',
        ]);

        // 4. Admin Delete
        $deleteRes = $this->actingAs($this->admin)->delete(route('admin.payment-methods.destroy', $pm));
        $deleteRes->assertRedirect(route('admin.payment-methods.index'));
        $this->assertDatabaseMissing('payment_methods', ['id' => $pm->id]);
    }

    /**
     * Test Poin 7: Aturan Pembukaan Jadwal Reservasi Pengambilan Emas Cicilan di Bulan Terakhir
     */
    public function test_installment_pickup_reservation_unlocks_only_in_final_month(): void
    {
        // Buat transaksi installment 3 bulan
        $trx = Transaction::create([
            'transaction_code' => 'TRX-INST-3MO',
            'user_id'          => $this->customer->id,
            'type'             => 'installment',
            'gold_price_id'    => $this->goldPrice->id,
            'subtotal'         => 3000000,
            'total_amount'     => 3000000,
            'payment_method'   => 'transfer',
            'payment_date'     => today(),
            'status'           => 'in_progress',
            'processed_by'     => $this->admin->id,
        ]);

        \App\Models\TransactionItem::create([
            'transaction_id' => $trx->id,
            'product_id'     => $this->product->id,
            'product_name'   => $this->product->name,
            'gold_purity'    => '24K',
            'weight_gram'    => 2.5,
            'quantity'       => 1,
            'price_per_unit' => 3000000,
            'subtotal'       => 3000000,
        ]);

        $plan = \App\Models\InstallmentPlan::create([
            'transaction_id'    => $trx->id,
            'down_payment'      => 600000,
            'total_installment' => 2400000,
            'tenure_months'     => 3,
            'monthly_amount'    => 800000,
            'start_date'        => today(),
            'end_date'          => today()->addMonths(3),
            'status'            => 'active',
        ]);

        // Buat 3 pembayaran angsuran (Bulan 1, Bulan 2, Bulan 3)
        $payment1 = \App\Models\InstallmentPayment::create([
            'installment_plan_id' => $plan->id,
            'installment_number'  => 1,
            'due_date'            => today()->addMonth(1),
            'amount_due'          => 800000,
            'status'              => 'pending',
        ]);

        $payment2 = \App\Models\InstallmentPayment::create([
            'installment_plan_id' => $plan->id,
            'installment_number'  => 2,
            'due_date'            => today()->addMonth(2),
            'amount_due'          => 800000,
            'status'              => 'pending',
        ]);

        $payment3 = \App\Models\InstallmentPayment::create([
            'installment_plan_id' => $plan->id,
            'installment_number'  => 3,
            'due_date'            => today()->addMonth(3),
            'amount_due'          => 800000,
            'status'              => 'pending',
        ]);

        // FASE 1: Belum ada pembayaran atau baru bulan 1 -> canSchedulePickup() harus FALSE
        $this->assertFalse($plan->canSchedulePickup());

        // Coba jadwalkan pickup saat belum bulan terakhir -> harus ditolak
        $failScheduleRes = $this->actingAs($this->customer)->post(route('customer.installments.schedule-pickup', $plan), [
            'preferred_date' => today()->addDays(5)->toDateString(),
            'preferred_time' => '10:00',
        ]);
        $failScheduleRes->assertSessionHas('error');
        $this->assertNull($plan->fresh()->pickup_reservation_id);

        // FASE 2: Bayar bulan 1
        $payment1->update(['status' => 'paid', 'paid_date' => now()]);
        $this->assertFalse($plan->fresh()->canSchedulePickup()); // 1/3 bulan masih belum cukup (harus bulan 1 dan 2 kelar)

        // FASE 3: Bayar bulan 2 -> Sekarang memasuki angsuran bulan terakhir (2/3 kelar)
        $payment2->update(['status' => 'paid', 'paid_date' => now()]);
        $this->assertTrue($plan->fresh()->canSchedulePickup()); // canSchedulePickup() kini TRUE!

        // Halaman detail cicilan sekarang menampilkan jadwal pengambilan emas
        $viewRes = $this->actingAs($this->customer)->get(route('customer.installments.show', $plan));
        $viewRes->assertOk();
        $viewRes->assertSee('Jadwal Pengambilan Emas Fisik di Toko');

        // Customer sekarang berhasil menjadwalkan pengambilan emas fisik
        $successScheduleRes = $this->actingAs($this->customer)->post(route('customer.installments.schedule-pickup', $plan), [
            'preferred_date' => today()->addDays(7)->toDateString(),
            'preferred_time' => '14:30',
            'notes'          => 'Saya bawa KTP asli',
        ]);
        $successScheduleRes->assertSessionHas('success');

        $updatedPlan = $plan->fresh();
        $this->assertNotNull($updatedPlan->pickup_reservation_id);
        $this->assertDatabaseHas('reservations', [
            'id'             => $updatedPlan->pickup_reservation_id,
            'user_id'        => $this->customer->id,
            'preferred_time' => '14:30',
        ]);
    }
}
