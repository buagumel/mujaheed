<?php

namespace Tests\Feature;

use App\Models\AirtimeCashRequest;
use App\Models\ExamPackage;
use App\Models\ExamPinTransaction;
use App\Models\ReferralCommission;
use App\Models\User;
use App\Models\Wallet;
use App\Services\AirtimeCashService;
use App\Services\ExamPinService;
use App\Services\ReferralService;
use App\Services\TierUpgradeService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdvancedEcosystemTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Seed basic exam packages
        ExamPackage::create([
            'code' => 'WAEC',
            'name' => 'WAEC Result Checker PIN',
            'standard_price' => 3800.00,
            'reseller_price' => 3500.00,
            'vip_price' => 3400.00,
            'status' => 'active',
        ]);
    }

    protected function createVerifiedUser(array $attributes = []): User
    {
        $user = User::factory()->create(array_merge([
            'role' => 'customer',
            'status' => 'active',
            'tier' => 'standard',
            'email_verified_at' => now(),
            'transaction_pin' => Hash::make('1234'),
        ], $attributes));

        $user->getOrCreateWallet();
        $user->getOrCreateReferralCode();
        return $user;
    }

    public function test_user_generates_dedicated_virtual_bank_accounts(): void
    {
        $user = $this->createVerifiedUser(['phone' => '08012345678']);
        $accounts = $user->getOrCreateVirtualAccounts();

        $this->assertNotEmpty($accounts);
        $this->assertDatabaseHas('virtual_bank_accounts', [
            'user_id' => $user->id,
            'provider' => 'payrant',
        ]);
    }

    public function test_user_can_purchase_exam_pins_with_wallet_debit(): void
    {
        $user = $this->createVerifiedUser();
        $user->wallet->update(['balance' => 10000.00]);

        $response = $this->actingAs($user)->post('/exam-pins/purchase', [
            'exam_code' => 'WAEC',
            'quantity' => 2,
            'pin' => '1234',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('exam_pin_transactions', [
            'user_id' => $user->id,
            'exam_code' => 'WAEC',
            'quantity' => 2,
            'total_amount' => 7600.00,
        ]);

        $this->assertEquals(2400.00, (float) $user->fresh()->wallet->balance);
    }

    public function test_user_tier_upgrade_unlocks_reseller_discounts(): void
    {
        $user = $this->createVerifiedUser();
        $user->wallet->update(['balance' => 5000.00]);

        $response = $this->actingAs($user)->post('/membership-tier/upgrade', [
            'tier' => 'reseller',
            'pin' => '1234',
        ]);

        $response->assertRedirect('/membership-tier');
        $this->assertEquals('reseller', $user->fresh()->tier);
        $this->assertEquals(3500.00, (float) $user->fresh()->wallet->balance); // 5000 - 1500

        // Test that reseller gets wholesale price on WAEC (3500 instead of 3800)
        $pkg = ExamPackage::where('code', 'WAEC')->first();
        $this->assertEquals(3500.00, $pkg->getPriceForTier($user->fresh()->tier));
    }

    public function test_referral_registration_and_commission_payout(): void
    {
        $referrer = $this->createVerifiedUser();
        $refCode = $referrer->getOrCreateReferralCode();

        // 1. Register Downline
        $this->post('/register', [
            'name' => 'Downline User',
            'email' => 'downline@example.com',
            'phone' => '08099887766',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
            'referral_code' => $refCode,
            'terms' => '1',
        ]);

        $downline = User::where('email', 'downline@example.com')->first();
        $this->assertNotNull($downline);
        $this->assertEquals($referrer->id, $downline->referred_by_id);

        // 2. Simulate First Wallet Funding Referral Reward
        app(ReferralService::class)->rewardFirstFunding($downline, 5000.00);

        $referrer = $referrer->fresh();
        $this->assertEquals(100.00, (float) $referrer->referral_balance);
        $this->assertDatabaseHas('referral_commissions', [
            'user_id' => $referrer->id,
            'referred_user_id' => $downline->id,
            'amount' => 100.00,
        ]);

        // 3. Withdraw Referral Bonus to Main Wallet
        $initBalance = (float) $referrer->wallet->balance;
        $this->actingAs($referrer)->post('/referrals/withdraw', [
            'amount' => 100.00,
        ]);

        $this->assertEquals(0.00, (float) $referrer->fresh()->referral_balance);
        $this->assertEquals($initBalance + 100.00, (float) $referrer->fresh()->wallet->balance);
    }

    public function test_airtime_to_cash_submission_and_admin_approval(): void
    {
        $user = $this->createVerifiedUser();
        $admin = \App\Models\Admin::create([
            'name' => 'Admin User',
            'email' => 'admin_cash@example.com',
            'password' => Hash::make('Password123!'),
            'role' => 'super_admin',
            'status' => 'active',
        ]);

        // 1. Submit Airtime to Cash request
        $response = $this->actingAs($user)->post('/airtime-to-cash', [
            'network' => 'MTN',
            'amount' => 2000.00,
            'sender_phone' => '08031112233',
            'payout_method' => 'wallet',
        ]);

        $req = AirtimeCashRequest::where('user_id', $user->id)->first();
        $this->assertNotNull($req);
        $this->assertEquals('pending', $req->status);
        $this->assertEquals(1600.00, (float) $req->amount_to_receive); // 80% of 2000

        // 2. Admin Approves Request
        $this->actingAs($admin, 'admin')->post("/admin/airtime-to-cash/{$req->id}/approve");

        $this->assertEquals('approved', $req->fresh()->status);
        $this->assertEquals(1600.00, (float) $user->fresh()->wallet->balance);
    }

    public function test_admin_can_configure_all_ecosystem_settings(): void
    {
        $admin = \App\Models\Admin::create([
            'name' => 'Admin Setting User',
            'email' => 'admin_setting@example.com',
            'password' => Hash::make('Password123!'),
            'role' => 'super_admin',
            'status' => 'active',
        ]);

        $response = $this->actingAs($admin, 'admin')->post('/admin/settings', [
            'platform_name' => 'VTU King Global',
            'primary_color' => '#00A76F',
            'secondary_color' => '#FFAB00',
            'logo_display_mode' => 'image_and_text',
            'logo_height' => '40',
            'support_email' => 'admin@vtuking.ng',
            'support_phone' => '08000000000',
            'currency' => 'NGN',
            'currency_symbol' => '₦',
            'maintenance_mode' => 'off',
            'mail_mailer' => 'smtp',
            'mail_host' => 'smtp.gmail.com',
            'mail_port' => 587,
            'mail_encryption' => 'tls',
            'mail_from_address' => 'noreply@vtuking.ng',
            'mail_from_name' => 'VTU King',

            // Ecosystem Configs
            'dva_status' => 'active',
            'dva_bank_1_name' => 'Kuda Bank',
            'dva_bank_2_name' => 'Moniepoint MFB',
            'dva_bank_3_name' => 'Providus Bank',
            'dva_fee_percent' => 1.0,
            'reseller_upgrade_fee' => 2000.0,
            'vip_upgrade_fee' => 4500.0,
            'reseller_airtime_discount' => 4.0,
            'vip_airtime_discount' => 5.0,
            'referral_status' => 'active',
            'referral_reward_type' => 'flat',
            'referral_flat_bonus' => 250.0,
            'referral_percent_bonus' => 3.0,
            'referral_min_withdrawal' => 200.0,
            'airtime_cash_status' => 'active',
            'airtime_cash_rate_mtn' => 85.0,
            'airtime_cash_rate_airtel' => 80.0,
            'airtime_cash_rate_glo' => 75.0,
            'airtime_cash_rate_9mobile' => 75.0,
            'airtime_cash_receiver_mtn' => '08039998877',
            'airtime_cash_receiver_airtel' => '08029998877',
            'airtime_cash_receiver_glo' => '08059998877',
            'airtime_cash_receiver_9mobile' => '08099998877',
        ]);

        $response->assertSessionHas('success');
        $this->assertEquals(2000.00, (float) \App\Models\SystemSetting::get('reseller_upgrade_fee'));
        $this->assertEquals('Kuda Bank', \App\Models\SystemSetting::get('dva_bank_1_name'));
        $this->assertEquals(85.00, (float) \App\Models\SystemSetting::get('airtime_cash_rate_mtn'));
        $this->assertEquals('08039998877', \App\Models\SystemSetting::get('airtime_cash_receiver_mtn'));
    }
}
