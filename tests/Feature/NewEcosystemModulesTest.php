<?php

namespace Tests\Feature;

use App\Models\Beneficiary;
use App\Models\Coupon;
use App\Models\User;
use App\Models\VtuTransaction;
use App\Models\Wallet;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class NewEcosystemModulesTest extends TestCase
{
    use RefreshDatabase;

    protected \App\Models\Admin $admin;
    protected User $customer;
    protected Wallet $customerWallet;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = \App\Models\Admin::create([
            'name' => 'Test Admin',
            'email' => 'test_admin_eco@example.com',
            'phone' => '08011223399',
            'password' => Hash::make('Password123!'),
            'role' => 'super_admin',
            'status' => 'active',
        ]);

        $this->customer = User::factory()->create([
            'email' => 'customer@example.com',
            'role' => 'customer',
            'status' => 'active',
        ]);

        $this->customerWallet = $this->customer->getOrCreateWallet();
    }

    public function test_admin_can_adjust_user_wallet_balance(): void
    {
        // 1. Credit ₦5000
        $response = $this->actingAs($this->admin, 'admin')->post(route('admin.wallets.adjust', $this->customerWallet->id), [
            'action_type' => 'credit',
            'amount' => 5000,
            'reason' => 'Offline Bank Deposit Approved',
        ]);

        $response->assertSessionHas('success');
        $this->assertEquals(5000, (float) $this->customerWallet->fresh()->balance);

        // 2. Debit ₦2000
        $debitResponse = $this->actingAs($this->admin, 'admin')->post(route('admin.wallets.adjust', $this->customerWallet->id), [
            'action_type' => 'debit',
            'amount' => 2000,
            'reason' => 'Chargeback or service correction',
        ]);

        $debitResponse->assertSessionHas('success');
        $this->assertEquals(3000, (float) $this->customerWallet->fresh()->balance);
    }

    public function test_admin_can_view_and_export_financial_profit_analytics(): void
    {
        // Create sample successful transaction
        VtuTransaction::create([
            'user_id' => $this->customer->id,
            'reference' => 'TX-TEST-PROFIT-01',
            'service_type' => 'data',
            'provider' => 'MTN',
            'recipient' => '08012345678',
            'amount' => 1000.00,
            'cost_price' => 850.00,
            'status' => 'successful',
        ]);

        $response = $this->actingAs($this->admin, 'admin')->get(route('admin.pricing.index'));
        $response->assertStatus(200);
        $response->assertSee('Profit Breakdown by Service');

        // CSV export
        $csvResponse = $this->actingAs($this->admin, 'admin')->get(route('admin.pricing.export_profit'));
        $csvResponse->assertStatus(200);
        $this->assertStringContainsString('text/csv', $csvResponse->headers->get('Content-Type'));
    }

    public function test_customer_can_manage_saved_beneficiaries_via_api(): void
    {
        $response = $this->actingAs($this->customer, 'sanctum')->postJson('/api/beneficiaries', [
            'service_type' => 'data',
            'identifier' => '08012345678',
            'name_nickname' => "Mum's MTN Line",
            'network_or_disco' => 'mtn',
        ]);

        $response->assertStatus(200);
        $response->assertJsonFragment(['name_nickname' => "Mum's MTN Line"]);
        $this->assertDatabaseHas('beneficiaries', ['identifier' => '08012345678']);

        // List beneficiaries
        $listResponse = $this->actingAs($this->customer, 'sanctum')->getJson('/api/beneficiaries');
        $listResponse->assertStatus(200);
        $listResponse->assertJsonFragment(['identifier' => '08012345678']);
    }

    public function test_coupon_engine_creation_and_api_verification(): void
    {
        $coupon = Coupon::create([
            'code' => 'TEST50',
            'discount_type' => 'fixed',
            'discount_value' => 50.00,
            'min_order_amount' => 500.00,
            'service_type' => 'data',
            'status' => 'active',
        ]);

        // API verify
        $apiResponse = $this->actingAs($this->customer, 'sanctum')->postJson('/api/coupons/verify', [
            'code' => 'TEST50',
            'amount' => 1000,
            'service_type' => 'data',
        ]);

        $apiResponse->assertStatus(200);
        $apiResponse->assertJsonFragment([
            'discount_applied' => 50,
            'final_payable' => 950,
        ]);
    }

    public function test_transaction_receipt_page_renders_successfully(): void
    {
        $tx = VtuTransaction::create([
            'user_id' => $this->customer->id,
            'reference' => 'TX-RECEIPT-999',
            'service_type' => 'electricity',
            'provider' => 'IKEDC',
            'recipient' => '01234567890',
            'amount' => 5000.00,
            'cost_price' => 4900.00,
            'token' => '1234-5678-9012-3456-7890',
            'units' => '45.2 kWh',
            'status' => 'successful',
        ]);

        $response = $this->get(route('receipt.show', $tx->reference));
        $response->assertStatus(200);
        $response->assertSee('TX-RECEIPT-999');
        $response->assertSee('1234-5678-9012-3456-7890');
    }

    public function test_admin_can_view_logs_and_download_database_backup(): void
    {
        $response = $this->actingAs($this->admin, 'admin')->get(route('admin.system.logs'));
        $response->assertStatus(200);
        $response->assertSee('System Error Logs');

        $backupResponse = $this->actingAs($this->admin, 'admin')->get(route('admin.system.backup'));
        $backupResponse->assertStatus(200);
        $this->assertEquals('application/sql', $backupResponse->headers->get('Content-Type'));
    }
}
