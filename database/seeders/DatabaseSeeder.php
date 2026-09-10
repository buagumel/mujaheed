<?php

namespace Database\Seeders;

use App\Models\CablePlan;
use App\Models\DataPlan;
use App\Models\ElectricityProvider;
use App\Models\NetworkSetting;
use App\Models\SystemSetting;
use App\Models\User;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use App\Models\VtuTransaction;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Admin Account in dedicated admins table
        \App\Models\Admin::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Platform Administrator',
                'phone' => '08012345678',
                'password' => Hash::make('ChangeMe123!'),
                'role' => 'super_admin',
                'status' => 'active',
            ]
        );

        // 2. Demo Customer User
        $customer = User::firstOrCreate(
            ['email' => 'user@example.com'],
            [
                'name' => 'Chinedu Eze',
                'phone' => '08123456789',
                'password' => Hash::make('ChangeMe123!'),
                'role' => 'customer',
                'status' => 'active',
                'transaction_pin' => Hash::make('1234'),
                'email_verified_at' => now(),
            ]
        );

        $wallet = $customer->getOrCreateWallet();
        if ($wallet->balance <= 0) {
            $wallet->update(['balance' => 25000.00]);

            WalletTransaction::firstOrCreate(
                ['reference' => 'WAL-SEED-INIT-001'],
                [
                    'wallet_id' => $wallet->id,
                    'user_id' => $customer->id,
                    'type' => 'credit',
                    'amount' => 25000.00,
                    'balance_before' => 0.00,
                    'balance_after' => 25000.00,
                    'description' => 'Welcome demo wallet funding bonus',
                    'status' => 'successful',
                ]
            );
        }

        // 3. Network Settings
        $networks = [
            ['network' => 'MTN', 'airtime_discount_percent' => 2.50, 'airtime_min_amount' => 50.00, 'airtime_max_amount' => 50000.00],
            ['network' => 'AIRTEL', 'airtime_discount_percent' => 2.00, 'airtime_min_amount' => 50.00, 'airtime_max_amount' => 50000.00],
            ['network' => 'GLO', 'airtime_discount_percent' => 3.00, 'airtime_min_amount' => 50.00, 'airtime_max_amount' => 50000.00],
            ['network' => '9MOBILE', 'airtime_discount_percent' => 3.50, 'airtime_min_amount' => 50.00, 'airtime_max_amount' => 50000.00],
        ];

        foreach ($networks as $net) {
            NetworkSetting::updateOrCreate(['network' => $net['network']], $net);
        }

        // 4. Data Plans
        $dataPlans = [
            // MTN
            ['network' => 'MTN', 'name' => '500 MB SME', 'code' => 'MTN-500MB', 'type' => 'SME', 'size' => '500MB', 'validity' => '30 Days', 'provider_price' => 135.00, 'selling_price' => 150.00],
            ['network' => 'MTN', 'name' => '1.0 GB SME', 'code' => 'MTN-1GB-SME', 'type' => 'SME', 'size' => '1GB', 'validity' => '30 Days', 'provider_price' => 265.00, 'selling_price' => 290.00],
            ['network' => 'MTN', 'name' => '2.0 GB SME', 'code' => 'MTN-2GB-SME', 'type' => 'SME', 'size' => '2GB', 'validity' => '30 Days', 'provider_price' => 530.00, 'selling_price' => 580.00],
            ['network' => 'MTN', 'name' => '3.0 GB SME', 'code' => 'MTN-3GB-SME', 'type' => 'SME', 'size' => '3GB', 'validity' => '30 Days', 'provider_price' => 795.00, 'selling_price' => 870.00],
            ['network' => 'MTN', 'name' => '5.0 GB SME', 'code' => 'MTN-5GB-SME', 'type' => 'SME', 'size' => '5GB', 'validity' => '30 Days', 'provider_price' => 1325.00, 'selling_price' => 1450.00],
            ['network' => 'MTN', 'name' => '10.0 GB Corporate', 'code' => 'MTN-10GB-CORP', 'type' => 'Corporate', 'size' => '10GB', 'validity' => '30 Days', 'provider_price' => 2700.00, 'selling_price' => 2900.00],

            // AIRTEL
            ['network' => 'AIRTEL', 'name' => '500 MB Corporate Gifting', 'code' => 'AIR-500MB', 'type' => 'Corporate', 'size' => '500MB', 'validity' => '30 Days', 'provider_price' => 140.00, 'selling_price' => 160.00],
            ['network' => 'AIRTEL', 'name' => '1.0 GB Corporate Gifting', 'code' => 'AIR-1GB', 'type' => 'Corporate', 'size' => '1GB', 'validity' => '30 Days', 'provider_price' => 275.00, 'selling_price' => 300.00],
            ['network' => 'AIRTEL', 'name' => '2.0 GB Corporate Gifting', 'code' => 'AIR-2GB', 'type' => 'Corporate', 'size' => '2GB', 'validity' => '30 Days', 'provider_price' => 550.00, 'selling_price' => 600.00],
            ['network' => 'AIRTEL', 'name' => '5.0 GB Corporate Gifting', 'code' => 'AIR-5GB', 'type' => 'Corporate', 'size' => '5GB', 'validity' => '30 Days', 'provider_price' => 1375.00, 'selling_price' => 1500.00],

            // GLO
            ['network' => 'GLO', 'name' => '1.0 GB Corporate Gifting', 'code' => 'GLO-1GB', 'type' => 'Corporate', 'size' => '1GB', 'validity' => '30 Days', 'provider_price' => 250.00, 'selling_price' => 280.00],
            ['network' => 'GLO', 'name' => '2.0 GB Corporate Gifting', 'code' => 'GLO-2GB', 'type' => 'Corporate', 'size' => '2GB', 'validity' => '30 Days', 'provider_price' => 500.00, 'selling_price' => 560.00],
            ['network' => 'GLO', 'name' => '5.0 GB Corporate Gifting', 'code' => 'GLO-5GB', 'type' => 'Corporate', 'size' => '5GB', 'validity' => '30 Days', 'provider_price' => 1250.00, 'selling_price' => 1400.00],

            // 9MOBILE
            ['network' => '9MOBILE', 'name' => '1.0 GB Corporate Gifting', 'code' => '9MOB-1GB', 'type' => 'Corporate', 'size' => '1GB', 'validity' => '30 Days', 'provider_price' => 220.00, 'selling_price' => 250.00],
            ['network' => '9MOBILE', 'name' => '2.0 GB Corporate Gifting', 'code' => '9MOB-2GB', 'type' => 'Corporate', 'size' => '2GB', 'validity' => '30 Days', 'provider_price' => 440.00, 'selling_price' => 500.00],
            ['network' => '9MOBILE', 'name' => '5.0 GB Corporate Gifting', 'code' => '9MOB-5GB', 'type' => 'Corporate', 'size' => '5GB', 'validity' => '30 Days', 'provider_price' => 1100.00, 'selling_price' => 1250.00],
        ];

        foreach ($dataPlans as $plan) {
            DataPlan::updateOrCreate(['code' => $plan['code']], array_merge($plan, ['status' => 'active']));
        }

        // 5. Cable Plans
        $cablePlans = [
            // DSTV
            ['provider' => 'DSTV', 'name' => 'DStv Padi', 'code' => 'dstv-padi', 'provider_price' => 3500.00, 'selling_price' => 3600.00],
            ['provider' => 'DSTV', 'name' => 'DStv Yanga', 'code' => 'dstv-yanga', 'provider_price' => 5000.00, 'selling_price' => 5100.00],
            ['provider' => 'DSTV', 'name' => 'DStv Confam', 'code' => 'dstv-confam', 'provider_price' => 9300.00, 'selling_price' => 9450.00],
            ['provider' => 'DSTV', 'name' => 'DStv Compact', 'code' => 'dstv-compact', 'provider_price' => 15700.00, 'selling_price' => 15900.00],
            ['provider' => 'DSTV', 'name' => 'DStv Compact Plus', 'code' => 'dstv-compact-plus', 'provider_price' => 25000.00, 'selling_price' => 25300.00],
            ['provider' => 'DSTV', 'name' => 'DStv Premium', 'code' => 'dstv-premium', 'provider_price' => 37000.00, 'selling_price' => 37500.00],

            // GOTV
            ['provider' => 'GOTV', 'name' => 'GOtv Smallie', 'code' => 'gotv-smallie', 'provider_price' => 1575.00, 'selling_price' => 1650.00],
            ['provider' => 'GOTV', 'name' => 'GOtv Jinja', 'code' => 'gotv-jinja', 'provider_price' => 3300.00, 'selling_price' => 3400.00],
            ['provider' => 'GOTV', 'name' => 'GOtv Jolli', 'code' => 'gotv-jolli', 'provider_price' => 4850.00, 'selling_price' => 4950.00],
            ['provider' => 'GOTV', 'name' => 'GOtv Max', 'code' => 'gotv-max', 'provider_price' => 7200.00, 'selling_price' => 7350.00],
            ['provider' => 'GOTV', 'name' => 'GOtv Supa', 'code' => 'gotv-supa', 'provider_price' => 9600.00, 'selling_price' => 9800.00],

            // STARTIMES
            ['provider' => 'STARTIMES', 'name' => 'Startimes Nova', 'code' => 'startimes-nova', 'provider_price' => 1700.00, 'selling_price' => 1800.00],
            ['provider' => 'STARTIMES', 'name' => 'Startimes Basic', 'code' => 'startimes-basic', 'provider_price' => 3300.00, 'selling_price' => 3450.00],
            ['provider' => 'STARTIMES', 'name' => 'Startimes Smart', 'code' => 'startimes-smart', 'provider_price' => 4200.00, 'selling_price' => 4350.00],
            ['provider' => 'STARTIMES', 'name' => 'Startimes Classic', 'code' => 'startimes-classic', 'provider_price' => 5000.00, 'selling_price' => 5200.00],
            ['provider' => 'STARTIMES', 'name' => 'Startimes Super', 'code' => 'startimes-super', 'provider_price' => 8200.00, 'selling_price' => 8400.00],
        ];

        foreach ($cablePlans as $cable) {
            CablePlan::updateOrCreate(['code' => $cable['code']], array_merge($cable, ['status' => 'active']));
        }

        // 6. Electricity Providers
        $electricity = [
            ['name' => 'Ikeja Electric (IKEDC)', 'code' => 'IKEDC', 'type' => 'prepaid_and_postpaid', 'min_amount' => 500.00, 'max_amount' => 100000.00, 'convenience_fee' => 100.00],
            ['name' => 'Eko Electric (EKEDC)', 'code' => 'EKEDC', 'type' => 'prepaid_and_postpaid', 'min_amount' => 500.00, 'max_amount' => 100000.00, 'convenience_fee' => 100.00],
            ['name' => 'Abuja Electricity (AEDC)', 'code' => 'AEDC', 'type' => 'prepaid_and_postpaid', 'min_amount' => 500.00, 'max_amount' => 100000.00, 'convenience_fee' => 100.00],
            ['name' => 'Ibadan Electricity (IBEDC)', 'code' => 'IBEDC', 'type' => 'prepaid_and_postpaid', 'min_amount' => 500.00, 'max_amount' => 100000.00, 'convenience_fee' => 100.00],
            ['name' => 'Kano Electricity (KEDCO)', 'code' => 'KEDCO', 'type' => 'prepaid_and_postpaid', 'min_amount' => 500.00, 'max_amount' => 100000.00, 'convenience_fee' => 100.00],
            ['name' => 'Port Harcourt Electric (PHED)', 'code' => 'PHED', 'type' => 'prepaid_and_postpaid', 'min_amount' => 500.00, 'max_amount' => 100000.00, 'convenience_fee' => 100.00],
            ['name' => 'Enugu Electricity (EEDC)', 'code' => 'EEDC', 'type' => 'prepaid_and_postpaid', 'min_amount' => 500.00, 'max_amount' => 100000.00, 'convenience_fee' => 100.00],
            ['name' => 'Jos Electricity (JED)', 'code' => 'JED', 'type' => 'prepaid_and_postpaid', 'min_amount' => 500.00, 'max_amount' => 100000.00, 'convenience_fee' => 100.00],
        ];

        foreach ($electricity as $disco) {
            ElectricityProvider::updateOrCreate(['code' => $disco['code']], array_merge($disco, ['status' => 'active']));
        }

        // 7. System Settings
        $settings = [
            'platform_name' => 'BJ Data Sub',
            'support_email' => 'support@bjdatasub.com',
            'support_phone' => '+234 800 123 4567',
            'support_whatsapp' => '+234 812 345 6789',
            'currency' => 'NGN',
            'currency_symbol' => '₦',
            'maintenance_mode' => 'off',
            'vtu_mode' => 'live',
            'payment_mode' => 'live',
            'active_vtu_provider' => 'bilalsada',
            'provider_bilalsada_status' => 'enabled',
            'provider_alrahuz_status' => 'enabled',
            'provider_mock_status' => 'disabled',
            'payrant_secret_key' => '99bf6a1275cdc47f25e2305d6e6913450e6ac73baf5d54137991a38a107e9fb2',
            'payrant_live_secret_key' => '99bf6a1275cdc47f25e2305d6e6913450e6ac73baf5d54137991a38a107e9fb2',
            'payrant_webhook_secret' => '99bf6a1275cdc47f25e2305d6e6913450e6ac73baf5d54137991a38a107e9fb2',
        ];

        foreach ($settings as $key => $val) {
            SystemSetting::set($key, $val);
        }

        // 8. Sample transactions for demo user
        if (VtuTransaction::count() === 0) {
            VtuTransaction::create([
                'user_id' => $customer->id,
                'reference' => 'AIR-DEMO-001',
                'service_type' => 'airtime',
                'provider' => 'MTN',
                'recipient' => '08123456789',
                'amount' => 1000.00,
                'discount_amount' => 25.00,
                'cost_price' => 975.00,
                'status' => 'successful',
                'created_at' => now()->subHours(5),
            ]);

            VtuTransaction::create([
                'user_id' => $customer->id,
                'reference' => 'DAT-DEMO-002',
                'service_type' => 'data',
                'provider' => 'MTN',
                'plan_code' => 'MTN-1GB-SME',
                'plan_name' => '1.0 GB SME',
                'recipient' => '08123456789',
                'amount' => 290.00,
                'cost_price' => 265.00,
                'status' => 'successful',
                'created_at' => now()->subHours(3),
            ]);

            VtuTransaction::create([
                'user_id' => $customer->id,
                'reference' => 'ELC-DEMO-003',
                'service_type' => 'electricity',
                'provider' => 'IKEDC',
                'recipient' => '45028491823',
                'amount' => 5000.00,
                'fee' => 100.00,
                'token' => '4820-9182-4910-2940-1823',
                'units' => '73.0 kWh',
                'customer_name' => 'CHINEDU EZE',
                'status' => 'successful',
                'created_at' => now()->subHours(1),
            ]);
        }
    }
}
