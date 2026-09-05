<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\DigitalCertificate;
use App\Models\GoldPrice;
use App\Models\InstallmentPayment;
use App\Models\InstallmentPlan;
use App\Models\InstallmentTransaction;
use App\Models\PaymentMethod;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class InstallmentPaymentShopeePegadaianTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $customer;
    private Product $product;
    private InstallmentPlan $plan;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');

        $this->admin = User::factory()->create(['role' => 'admin']);
        $this->customer = User::factory()->create(['role' => 'customer']);

        Category::create([
            'name' => 'Emas',
            'slug' => 'emas',
            'is_active' => true,
        ]);

        $goldPrice = GoldPrice::create([
            'price_date' => today()->toDateString(),
            'buy_price_per_gram' => 1000000,
            'sell_price_per_gram' => 1100000,
            'source' => 'Test',
            'recorded_by' => $this->admin->id,
        ]);

        $this->product = Product::factory()->create([
            'name' => 'Cincin Emas Test',
            'gold_purity' => '24K',
            'weight_gram' => 5.0,
            'base_price' => 3000000,
            'stock' => 10,
            'is_available' => true,
        ]);

        PaymentMethod::create([
            'name' => 'BCA Virtual Account',
            'code' => 'bca',
            'type' => 'bank_transfer',
            'bank_name' => 'BCA',
            'account_number' => '1234567890',
            'account_holder' => 'Toko Emas Sinar Baru II',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        // Buat transaksi cicilan tenor 3 bulan
        $transaction = Transaction::create([
            'transaction_code' => 'TRX-INST-TEST',
            'user_id' => $this->customer->id,
            'type' => 'installment',
            'gold_price_id' => $goldPrice->id,
            'payment_method' => 'transfer',
            'subtotal' => 3000000,
            'total_amount' => 3000000,
            'payment_date' => today()->toDateString(),
            'status' => 'in_progress',
            'processed_by' => $this->admin->id,
        ]);

        $transaction->items()->create([
            'product_id'     => $this->product->id,
            'product_name'   => $this->product->name,
            'gold_purity'    => '24K',
            'weight_gram'    => 5.0,
            'quantity'       => 1,
            'price_per_unit' => 3000000,
            'subtotal'       => 3000000,
        ]);

        $this->plan = InstallmentPlan::create([
            'transaction_id' => $transaction->id,
            'down_payment' => 0,
            'total_installment' => 3000000,
            'tenure_months' => 3,
            'monthly_amount' => 1000000,
            'start_date' => today(),
            'end_date' => today()->addMonths(3),
            'status' => 'active',
        ]);

        for ($i = 1; $i <= 3; $i++) {
            $this->plan->payments()->create([
                'installment_number' => $i,
                'due_date' => today()->addMonths($i),
                'amount_due' => 1000000,
                'status' => 'pending',
            ]);
        }
    }

    public function test_customer_can_upload_payment_proof_for_single_month()
    {
        $file = UploadedFile::fake()->image('bukti_transfer.jpg', 600, 600);

        $response = $this->actingAs($this->customer)->post(route('customer.installments.pay', $this->plan), [
            'payment_option' => 'next_month',
            'payment_method' => 'bca',
            'proof_image'    => $file,
            'sender_bank'    => 'BCA',
            'sender_name'    => 'Pelanggan Setia',
            'notes'          => 'Bayar angsuran 1',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        // Pastikan InstallmentTransaction terbuat
        $trx = InstallmentTransaction::where('installment_plan_id', $this->plan->id)->firstOrFail();
        $this->assertEquals('waiting_verification', $trx->status);
        $this->assertEquals(1000000, $trx->amount);
        $this->assertEquals([1], $trx->months_paid);
        $this->assertNotNull($trx->proof_image);
        Storage::disk('public')->assertExists($trx->proof_image);

        // Pastikan status angsuran bulan ke-1 berubah menjadi waiting_verification
        $p1 = $this->plan->payments()->where('installment_number', 1)->first();
        $this->assertEquals('waiting_verification', $p1->status);
        $this->assertEquals($trx->id, $p1->installment_transaction_id);
    }

    public function test_customer_can_pay_multiple_months_simultaneously()
    {
        $file = UploadedFile::fake()->image('bukti_transfer_multi.png');

        $response = $this->actingAs($this->customer)->post(route('customer.installments.pay', $this->plan), [
            'payment_option'  => 'custom_months',
            'selected_months' => [1, 2], // 2 bulan sekaligus
            'payment_method'  => 'bca',
            'proof_image'     => $file,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $trx = InstallmentTransaction::where('installment_plan_id', $this->plan->id)->firstOrFail();
        $this->assertEquals(2000000, $trx->amount);
        $this->assertEquals([1, 2], $trx->months_paid);
        $this->assertEquals(2, $trx->month_count);

        $p1 = $this->plan->payments()->where('installment_number', 1)->first();
        $p2 = $this->plan->payments()->where('installment_number', 2)->first();
        $this->assertEquals('waiting_verification', $p1->status);
        $this->assertEquals('waiting_verification', $p2->status);
    }

    public function test_customer_can_pay_all_remaining_months_at_once()
    {
        $file = UploadedFile::fake()->image('bukti_lunas.jpg');

        $response = $this->actingAs($this->customer)->post(route('customer.installments.pay', $this->plan), [
            'payment_option' => 'pay_all',
            'payment_method' => 'bca',
            'proof_image'    => $file,
        ]);

        $response->assertRedirect();

        $trx = InstallmentTransaction::where('installment_plan_id', $this->plan->id)->firstOrFail();
        $this->assertEquals(3000000, $trx->amount);
        $this->assertEquals([1, 2, 3], $trx->months_paid);
        $this->assertEquals(3, $trx->month_count);

        $this->assertEquals(3, $this->plan->payments()->where('status', 'waiting_verification')->count());
    }

    public function test_admin_can_verify_and_approve_installment_payment()
    {
        // Customer upload bukti
        $file = UploadedFile::fake()->image('bukti.jpg');
        $this->actingAs($this->customer)->post(route('customer.installments.pay', $this->plan), [
            'payment_option' => 'pay_all',
            'payment_method' => 'bca',
            'proof_image'    => $file,
        ]);

        $trx = InstallmentTransaction::where('installment_plan_id', $this->plan->id)->firstOrFail();

        // Admin approve
        $response = $this->actingAs($this->admin)->post(route('admin.installments.transactions.verify', $trx));
        $response->assertRedirect();
        $response->assertSessionHas('success');

        $trx->refresh();
        $this->assertEquals('verified', $trx->status);
        $this->assertEquals($this->admin->id, $trx->verified_by);
        $this->assertNotNull($trx->verified_at);

        // Seluruh angsuran lunas
        $this->assertEquals(3, $this->plan->payments()->where('status', 'paid')->count());
        $this->assertEquals('completed', $this->plan->fresh()->status);
        $this->assertEquals('completed', $this->plan->transaction->fresh()->status);

        // Sertifikat digital terbit
        $this->assertTrue(DigitalCertificate::where('transaction_id', $this->plan->transaction_id)->exists());
    }

    public function test_admin_can_reject_installment_payment_with_reason()
    {
        $file = UploadedFile::fake()->image('bukti_palsu.jpg');
        $this->actingAs($this->customer)->post(route('customer.installments.pay', $this->plan), [
            'payment_option' => 'next_month',
            'payment_method' => 'bca',
            'proof_image'    => $file,
        ]);

        $trx = InstallmentTransaction::where('installment_plan_id', $this->plan->id)->firstOrFail();

        // Admin reject
        $response = $this->actingAs($this->admin)->post(route('admin.installments.transactions.reject', $trx), [
            'rejection_reason' => 'Foto mutasi tidak terbaca dengan jelas.',
        ]);

        $response->assertRedirect();
        $trx->refresh();
        $this->assertEquals('rejected', $trx->status);
        $this->assertEquals('Foto mutasi tidak terbaca dengan jelas.', $trx->rejection_reason);

        // Angsuran kembali berstatus pending
        $p1 = $this->plan->payments()->where('installment_number', 1)->first();
        $this->assertEquals('pending', $p1->status);
    }

    public function test_admin_can_record_cashier_batch_payment()
    {
        // Admin catat bayar 2 bulan langsung di kasir
        $response = $this->actingAs($this->admin)->post(route('admin.installments.pay-batch', $this->plan), [
            'month_count'    => 2,
            'payment_method' => 'cash',
            'notes'          => 'Bayar tunai di toko',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertEquals(2, $this->plan->paidCount());
        $p1 = $this->plan->payments()->where('installment_number', 1)->first();
        $p2 = $this->plan->payments()->where('installment_number', 2)->first();
        $p3 = $this->plan->payments()->where('installment_number', 3)->first();

        $this->assertEquals('paid', $p1->status);
        $this->assertEquals('paid', $p2->status);
        $this->assertEquals('pending', $p3->status);
    }
}
