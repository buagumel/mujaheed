<?php

namespace Database\Seeders;

use App\Models\ExamPackage;
use App\Models\User;
use Illuminate\Database\Seeder;

class AdvancedEcosystemSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Seed Exam Packages
        $packages = [
            [
                'code' => 'WAEC',
                'name' => 'WAEC Result Checker PIN',
                'standard_price' => 3800.00,
                'reseller_price' => 3500.00,
                'vip_price' => 3400.00,
                'status' => 'active',
                'description' => 'Check May/June and GCE Nov/Dec WASSCE results online instantly.',
            ],
            [
                'code' => 'NECO',
                'name' => 'NECO Result Token PIN',
                'standard_price' => 1200.00,
                'reseller_price' => 1050.00,
                'vip_price' => 980.00,
                'status' => 'active',
                'description' => 'Instant token for checking SSCE Internal & External NECO examination results.',
            ],
            [
                'code' => 'NABTEB',
                'name' => 'NABTEB Scratch Card PIN',
                'standard_price' => 1400.00,
                'reseller_price' => 1250.00,
                'vip_price' => 1150.00,
                'status' => 'active',
                'description' => 'Direct PIN for checking National Technical and Business Certificate Examination results.',
            ],
            [
                'code' => 'JAMB',
                'name' => 'JAMB UTME Profile / e-PIN',
                'standard_price' => 7500.00,
                'reseller_price' => 7200.00,
                'vip_price' => 7000.00,
                'status' => 'active',
                'description' => 'Original JAMB UTME Registration e-PIN delivered instantly.',
            ],
        ];

        foreach ($packages as $pkg) {
            ExamPackage::updateOrCreate(['code' => $pkg['code']], $pkg);
        }

        // 2. Ensure all users have referral codes and virtual bank accounts
        $users = User::all();
        foreach ($users as $u) {
            $u->getOrCreateReferralCode();
            $u->getOrCreateVirtualAccounts();
        }
    }
}
