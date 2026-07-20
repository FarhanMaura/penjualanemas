<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Product;
use App\Models\GoldPrice;
use App\Models\Transaction;
use App\Models\InstallmentPlan;
use App\Models\Pawn;
use App\Models\Category;
use App\Models\DigitalCertificate;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InstallmentAndPawnTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $customer;
    private Product $product;
    private GoldPrice $goldPrice;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['role' => 'admin']);
        $this->customer = User::factory()->create(['role' => 'customer']);
        
        Category::create([
            'name' => 'Emas',
            'slug' => 'emas',
            'is_active' => true,
        ]);

        $this->goldPrice = GoldPrice::create([
            'price_date' => today()->toDateString(),
            'buy_price_per_gram' => 1000000,
            'sell_price_per_gram' => 1100000,
            'source' => 'Test',
            'recorded_by' => $this->admin->id,
        ]);

        $this->product = Product::factory()->create([
            'name' => 'Emas Test',
            'base_price' => 1100000,
            'stock' => 5,
            'is_available' => true,
            'is_reservable' => true,
        ]);
    }

    public function test_standard_purchase_transaction_generates_digital_certificate()
    {
        $response = $this->actingAs($this->admin)->post(route('admin.transactions.store'), [
            'user_id' => $this->customer->id,
            'type' => 'purchase',
            'gold_price_id' => $this->goldPrice->id,
            'payment_method' => 'cash',
            'payment_date' => today()->toDateString(),
            'admin_fee' => 5000,
            'discount' => 0,
            'items' => [
                [
                    'product_id' => $this->product->id,
                    'quantity' => 1,
                    'unit_price' => 1100000,
                ]
            ]
        ]);

        $response->assertRedirect(route('admin.transactions.index'));

        // Verify transaction was created with status completed
        $transaction = Transaction::where('user_id', $this->customer->id)->firstOrFail();
        $this->assertEquals('completed', $transaction->status);

        // Verify digital certificate was generated
        $this->assertTrue(DigitalCertificate::where('transaction_id', $transaction->id)->exists());
    }

    public function test_installment_payment_flow_and_certificate_generation()
    {
        // 1. Create installment transaction
        $response = $this->actingAs($this->admin)->post(route('admin.transactions.store'), [
            'user_id' => $this->customer->id,
            'type' => 'installment',
            'gold_price_id' => $this->goldPrice->id,
            'payment_method' => 'cash',
            'payment_date' => today()->toDateString(),
            'admin_fee' => 0,
            'discount' => 0,
            'installment_tenure' => 3,
            'installment_down_payment' => 200000,
            'items' => [
                [
                    'product_id' => $this->product->id,
                    'quantity' => 1,
                    'unit_price' => 1100000,
                ]
            ]
        ]);

        $response->assertRedirect(route('admin.transactions.index'));

        $transaction = Transaction::where('user_id', $this->customer->id)->firstOrFail();
        $this->assertEquals('in_progress', $transaction->status);

        $plan = InstallmentPlan::where('transaction_id', $transaction->id)->firstOrFail();
        $this->assertEquals('active', $plan->status);
        $this->assertCount(3, $plan->payments);

        // 2. Pay first monthly payment
        $payment1 = $plan->payments()->where('installment_number', 1)->firstOrFail();
        
        $response = $this->actingAs($this->admin)->post(route('admin.installments.payments.pay', [$plan, $payment1]), [
            'payment_method' => 'transfer',
            'amount_paid' => $payment1->amount_due,
            'notes' => 'Bayar cicilan 1',
        ]);

        $response->assertRedirect();
        $this->assertEquals('paid', $payment1->fresh()->status);
        $this->assertEquals('active', $plan->fresh()->status);
        $this->assertEquals('in_progress', $transaction->fresh()->status);

        // 3. Pay remaining monthly payments
        $payment2 = $plan->payments()->where('installment_number', 2)->firstOrFail();
        $payment3 = $plan->payments()->where('installment_number', 3)->firstOrFail();

        $this->actingAs($this->admin)->post(route('admin.installments.payments.pay', [$plan, $payment2]), [
            'payment_method' => 'transfer',
            'amount_paid' => $payment2->amount_due,
        ]);
        
        $this->actingAs($this->admin)->post(route('admin.installments.payments.pay', [$plan, $payment3]), [
            'payment_method' => 'transfer',
            'amount_paid' => $payment3->amount_due,
        ]);

        // Verify completion
        $this->assertEquals('completed', $plan->fresh()->status);
        $this->assertEquals('completed', $transaction->fresh()->status);
        
        // Verify digital certificate was generated
        $this->assertTrue(DigitalCertificate::where('transaction_id', $transaction->id)->exists());
    }

    public function test_pawn_redemption_flow()
    {
        // 1. Create pawn transaction
        $response = $this->actingAs($this->admin)->post(route('admin.transactions.store'), [
            'user_id' => $this->customer->id,
            'type' => 'pawn',
            'gold_price_id' => $this->goldPrice->id,
            'payment_method' => 'cash',
            'payment_date' => today()->toDateString(),
            'pawn_gold_description' => 'Kalung emas',
            'pawn_gold_purity' => '18K',
            'pawn_weight_gram' => 10.5,
            'pawn_appraised_value' => 8000000,
            'pawn_loan_amount' => 5000000,
            'pawn_interest_rate' => 2.0,
            'pawn_due_date' => today()->addMonths(4)->toDateString(),
        ]);

        $response->assertRedirect(route('admin.transactions.index'));

        $transaction = Transaction::where('user_id', $this->customer->id)->firstOrFail();
        $this->assertEquals('in_progress', $transaction->status);

        $pawn = Pawn::where('transaction_id', $transaction->id)->firstOrFail();
        $this->assertEquals('active', $pawn->status);

        // 2. Redeem pawn
        $response = $this->actingAs($this->admin)->post(route('admin.pawns.redeem', $pawn), [
            'redemption_amount' => 5000000,
            'notes' => 'Tebus lunas',
        ]);

        $response->assertRedirect();

        $this->assertEquals('redeemed', $pawn->fresh()->status);
        $this->assertEquals('completed', $transaction->fresh()->status);
    }
}
