<?php

namespace Tests\Feature;

use App\Models\SystemSetting;
use App\Models\User;
use App\Models\VirtualBankAccount;
use App\Services\Payment\PayrantService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PayrantIntegrationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Seed basic settings
        SystemSetting::set('platform_name', 'BJ Data Sub');
        SystemSetting::set('payrant_status', 'active');
        SystemSetting::set('payrant_webhook_secret', 'test_secret_123456');
        SystemSetting::set('payrant_bank_display_name', 'PalmPay / Payrant Bank');
    }

    public function test_user_can_get_payrant_virtual_account(): void
    {
        $user = User::factory()->create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'phone' => '08012345678',
        ]);
        $user->wallet()->create(['balance' => 0.00]);

        $accounts = $user->getOrCreateVirtualAccounts();

        $this->assertNotEmpty($accounts);
        $va = $accounts->first();
        $this->assertEquals('PalmPay / Payrant Bank', $va->bank_name);
        $this->assertEquals('payrant', $va->provider);
        $this->assertEquals($user->id, $va->user_id);
    }

    public function test_payrant_webhook_automatically_credits_user_wallet(): void
    {
        $user = User::factory()->create([
            'name' => 'Amaka Obi',
            'email' => 'amaka@example.com',
            'phone' => '08098765432',
        ]);
        $wallet = $user->wallet()->create(['balance' => 1000.00]);

        $va = VirtualBankAccount::create([
            'user_id' => $user->id,
            'bank_name' => 'PalmPay / Payrant Bank',
            'account_number' => '6699479580',
            'account_name' => 'BJ Data Sub / AMAKA OBI',
            'provider' => 'payrant',
            'reference' => 'PR-testref123',
            'account_reference' => '-686fe77b5fe0c',
            'status' => 'active',
        ]);

        $payload = [
            'status' => 'success',
            'transaction' => [
                'reference' => 'MI1944373825147994112',
                'amount' => 5000,
                'net_amount' => 5000,
                'fee' => 0,
                'currency' => 'NGN',
                'timestamp' => '2025-07-13 12:30:19',
                'user_id' => $user->id,
                'account_details' => [
                    'account_number' => '6699479580',
                    'account_name' => 'Amaka@2025(Payrant)',
                ],
                'payer_details' => [
                    'account_number' => '3145980158',
                    'account_name' => 'OGBADA CHRISTIANA ELIMA',
                    'bank_name' => 'FIRST BANK PLC',
                ],
                'metadata' => [
                    'session_id' => '000016250713133014000309166995',
                    'reference' => 'FBNMOBILE:TRANSFER /',
                    'account_reference' => '-686fe77b5fe0c',
                ],
            ],
        ];

        $rawBody = json_encode($payload);
        $signature = hash_hmac('sha256', $rawBody, 'test_secret_123456');

        $response = $this->postJson('/webhook/payrant', $payload, [
            'X-Payrant-Signature' => $signature,
        ]);

        $response->assertStatus(200);
        $response->assertJson(['status' => 'received']);

        // Check that user wallet was credited ₦5,000
        $this->assertEquals(6000.00, (float) $wallet->fresh()->balance);

        // Check that virtual account total_funded was incremented
        $this->assertEquals(5000.00, (float) $va->fresh()->total_funded);
    }
}
