<?php

namespace Tests\Feature;

use App\Models\CablePlan;
use App\Models\DataPlan;
use App\Models\ElectricityProvider;
use App\Models\EmailOtp;
use App\Models\NetworkSetting;
use App\Models\SupportTicket;
use App\Models\SystemSetting;
use App\Models\User;
use App\Models\Wallet;
use App\Services\OtpService;
use App\Services\WalletService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class VtuPlatformTest extends TestCase
{
    use RefreshDatabase;

    protected User $customer;
    protected \App\Models\Admin $admin;

    protected function setUp(): void
    {
        parent::setUp();

        // Seed basic settings
        NetworkSetting::updateOrCreate(
            ['network' => 'MTN'],
            ['airtime_discount_percent' => 2.50, 'airtime_min_amount' => 50.00, 'airtime_max_amount' => 50000.00, 'status' => 'active']
        );

        DataPlan::updateOrCreate(
            ['code' => 'TEST-MTN-1GB'],
            [
                'network' => 'MTN',
                'name' => '1.0 GB Test Plan',
                'type' => 'SME',
                'size' => '1GB',
                'validity' => '30 Days',
                'provider_price' => 260.00,
                'selling_price' => 290.00,
                'status' => 'active',
            ]
        );

        ElectricityProvider::updateOrCreate(
            ['code' => 'IKEDC'],
            [
                'name' => 'Ikeja Electric',
                'type' => 'prepaid_and_postpaid',
                'min_amount' => 500.00,
                'max_amount' => 100000.00,
                'convenience_fee' => 100.00,
                'status' => 'active',
            ]
        );

        CablePlan::updateOrCreate(
            ['code' => 'test-gotv-max'],
            [
                'provider' => 'GOTV',
                'name' => 'GOtv Max',
                'provider_price' => 7000.00,
                'selling_price' => 7200.00,
                'status' => 'active',
            ]
        );

        $this->customer = User::create([
            'name' => 'Test Customer',
            'email' => 'test_customer@example.com',
            'phone' => '08099887766',
            'password' => Hash::make('Password123!'),
            'role' => 'customer',
            'status' => 'active',
            'email_verified_at' => now(),
            'transaction_pin' => Hash::make('1234'),
        ]);

        $wallet = $this->customer->getOrCreateWallet();
        $wallet->update(['balance' => 50000.00]);

        $this->admin = \App\Models\Admin::create([
            'name' => 'Test Admin',
            'email' => 'test_admin@example.com',
            'phone' => '08011223344',
            'password' => Hash::make('Password123!'),
            'role' => 'super_admin',
            'status' => 'active',
        ]);
    }

    public function test_user_registration_dispatches_otp_and_requires_verification(): void
    {
        $email = 'newuser_' . time() . '@example.com';
        $response = $this->post('/register', [
            'name' => 'New User',
            'email' => $email,
            'phone' => '08012349988',
            'password' => 'SecurePass123!',
            'password_confirmation' => 'SecurePass123!',
            'terms' => '1',
        ]);

        $response->assertRedirect('/verify-email');
        $this->assertAuthenticated();

        $user = User::where('email', $email)->first();
        $this->assertNull($user->email_verified_at);

        // Fetch generated OTP
        $otp = EmailOtp::where('email', $email)->where('purpose', 'email_verification')->first();
        $this->assertNotNull($otp);

        // Verify with OTP
        $verifyRes = $this->post('/verify-email', [
            'code' => $otp->code,
        ]);

        $verifyRes->assertRedirect('/dashboard');
        $user->refresh();
        $this->assertNotNull($user->email_verified_at);
    }

    public function test_forgot_password_and_otp_reset_flow(): void
    {
        // 1. Request Password Reset OTP
        $reqRes = $this->post('/forgot-password', [
            'email' => $this->customer->email,
        ]);
        $reqRes->assertRedirect('/reset-password');

        $otp = EmailOtp::where('email', $this->customer->email)->where('purpose', 'password_reset')->latest()->first();
        $this->assertNotNull($otp);

        // 2. Reset Password with OTP
        $resetRes = $this->post('/reset-password', [
            'email' => $this->customer->email,
            'code' => $otp->code,
            'password' => 'NewSecurePassword123!',
            'password_confirmation' => 'NewSecurePassword123!',
        ]);

        $resetRes->assertRedirect('/login');

        // Test logging in with new password
        $loginRes = $this->post('/login', [
            'email' => $this->customer->email,
            'password' => 'NewSecurePassword123!',
        ]);

        $loginRes->assertRedirect('/dashboard');
    }

    public function test_customer_cannot_access_admin_portal(): void
    {
        $response = $this->actingAs($this->customer)->get('/admin');
        $response->assertRedirect(route('admin.login'));
    }

    public function test_admin_can_access_admin_portal(): void
    {
        $response = $this->actingAs($this->admin, 'admin')->get('/admin');
        $response->assertStatus(200);
    }

    public function test_admin_can_configure_branding_and_smtp_settings(): void
    {
        Storage::fake('public');

        $file = UploadedFile::fake()->image('custom_logo.png', 200, 60);

        $response = $this->actingAs($this->admin, 'admin')->post('/admin/settings', [
            'platform_name' => 'Custom VTU Hub',
            'platform_tagline' => 'FAST TOP-UP',
            'primary_color' => '#00A76F',
            'secondary_color' => '#FFAB00',
            'logo_display_mode' => 'image_only',
            'logo_height' => 45,
            'logo_image' => $file,
            'support_email' => 'support@customvtu.ng',
            'support_phone' => '08000000000',
            'currency' => 'NGN',
            'currency_symbol' => '₦',
            'maintenance_mode' => 'off',
            'mail_mailer' => 'smtp',
            'mail_host' => 'smtp.gmail.com',
            'mail_port' => 587,
            'mail_username' => 'test@gmail.com',
            'mail_password' => 'abcdefghijklmnop',
            'mail_encryption' => 'tls',
            'mail_from_address' => 'noreply@customvtu.ng',
            'mail_from_name' => 'Custom VTU Hub',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertEquals('Custom VTU Hub', SystemSetting::get('platform_name'));
        $this->assertEquals('#00A76F', SystemSetting::get('primary_color'));
        $this->assertEquals('smtp.gmail.com', SystemSetting::get('mail_host'));
        $this->assertEquals('587', SystemSetting::get('mail_port'));
    }

    public function test_support_ticket_creation_and_admin_reply_flow(): void
    {
        // 1. User creates ticket
        $ticketRes = $this->actingAs($this->customer)->post('/support/tickets', [
            'subject' => 'Delayed MTN Data Delivery',
            'category' => 'data',
            'priority' => 'high',
            'message' => 'I ordered 1GB data for 08012345678 and it is not yet delivered.',
        ]);

        $ticket = SupportTicket::where('user_id', $this->customer->id)->latest()->first();
        $this->assertNotNull($ticket);
        $ticketRes->assertRedirect('/support/tickets/' . $ticket->id);

        // 2. Admin replies to ticket
        $replyRes = $this->actingAs($this->admin, 'admin')->post('/admin/tickets/' . $ticket->id . '/reply', [
            'message' => 'We have checked your order and re-processed it successfully.',
            'status' => 'resolved',
        ]);

        $replyRes->assertRedirect();
        $ticket->refresh();
        $this->assertEquals('resolved', $ticket->status);
        $this->assertCount(2, $ticket->messages);
    }

    public function test_admin_broadcast_campaign_dispatch(): void
    {
        $response = $this->actingAs($this->admin, 'admin')->post('/admin/broadcast', [
            'title' => 'Weekend Flash Sale!',
            'message' => 'Get 5% discount on all data purchases this weekend only.',
            'target_audience' => 'selected',
            'user_ids' => [$this->customer->id],
            'channel' => 'both',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');
    }

    public function test_wallet_atomic_credit_and_debit(): void
    {
        $walletService = app(WalletService::class);

        $balanceBefore = (float) $this->customer->wallet->balance;

        $creditTx = $walletService->credit($this->customer, 1000, 'Test Credit Deposit');
        $this->assertEquals($balanceBefore + 1000, (float) $creditTx->balance_after);

        $debitTx = $walletService->debit($this->customer, 500, 'Test Debit Purchase');
        $this->assertEquals($balanceBefore + 500, (float) $debitTx->balance_after);
    }

    public function test_airtime_purchase_flow(): void
    {
        $response = $this->actingAs($this->customer)->post('/airtime/purchase', [
            'network' => 'MTN',
            'phone' => '08012345678',
            'amount' => 500,
            'pin' => '1234',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');
    }

    public function test_data_purchase_flow(): void
    {
        $plan = DataPlan::where('code', 'TEST-MTN-1GB')->first();

        $response = $this->actingAs($this->customer)->post('/data/purchase', [
            'plan_id' => $plan->id,
            'phone' => '08012345678',
            'pin' => '1234',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');
    }

    public function test_electricity_verification_and_payment(): void
    {
        // 1. Verify Meter
        $verifyRes = $this->actingAs($this->customer)->postJson('/electricity/verify', [
            'disco' => 'IKEDC',
            'meter_number' => '14209182390',
            'type' => 'prepaid',
        ]);

        $verifyRes->assertJsonFragment(['success' => true]);

        // 2. Pay Bill
        $payRes = $this->actingAs($this->customer)->post('/electricity/pay', [
            'disco' => 'IKEDC',
            'meter_number' => '14209182390',
            'meter_type' => 'prepaid',
            'amount' => 1000,
            'phone' => '08012345678',
            'pin' => '1234',
            'customer_name' => 'EMMANUEL OKECHUKWU OKAFOR',
        ]);

        $payRes->assertRedirect();
        $payRes->assertSessionHas('success');
    }

    public function test_cable_verification_and_payment(): void
    {
        // 1. Verify Smartcard
        $verifyRes = $this->actingAs($this->customer)->postJson('/cable/verify', [
            'provider' => 'GOTV',
            'smartcard_number' => '7029104928',
        ]);

        $verifyRes->assertJsonFragment(['success' => true]);

        // 2. Pay Subscription
        $plan = CablePlan::where('code', 'test-gotv-max')->first();

        $payRes = $this->actingAs($this->customer)->post('/cable/pay', [
            'provider' => 'GOTV',
            'plan_id' => $plan->id,
            'smartcard_number' => '7029104928',
            'phone' => '08012345678',
            'pin' => '1234',
            'customer_name' => 'CHIBUZOR MICHAEL ABEGBUNDE',
        ]);

        $payRes->assertRedirect();
        $payRes->assertSessionHas('success');
    }

    public function test_incorrect_pin_fails_purchase(): void
    {
        $response = $this->actingAs($this->customer)->post('/airtime/purchase', [
            'network' => 'MTN',
            'phone' => '08012345678',
            'amount' => 500,
            'pin' => '9999',
        ]);

        $response->assertSessionHasErrors('error');
    }
}
