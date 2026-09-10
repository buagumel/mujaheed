<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Add tier and referral fields to users table
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'tier')) {
                $table->string('tier')->default('standard')->after('role'); // standard, reseller, vip
            }
            if (!Schema::hasColumn('users', 'referral_code')) {
                $table->string('referral_code')->unique()->nullable()->after('tier');
            }
            if (!Schema::hasColumn('users', 'referred_by_id')) {
                $table->foreignId('referred_by_id')->nullable()->constrained('users')->nullOnDelete()->after('referral_code');
            }
            if (!Schema::hasColumn('users', 'referral_balance')) {
                $table->decimal('referral_balance', 16, 2)->default(0.00)->after('referred_by_id');
            }
        });

        // 2. Dedicated Virtual Bank Accounts Table
        Schema::create('virtual_bank_accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('bank_name'); // Wema Bank, Moniepoint, Sterling, PalmPay
            $table->string('account_number');
            $table->string('account_name');
            $table->string('provider')->default('system'); // paystack, monnify, system
            $table->string('reference')->unique()->nullable();
            $table->string('status')->default('active'); // active, inactive
            $table->timestamps();

            $table->index(['user_id', 'status']);
            $table->index('account_number');
        });

        // 3. Exam Providers & Packages Table
        Schema::create('exam_packages', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique(); // WAEC, NECO, NABTEB, JAMB
            $table->string('name'); // WAEC Result Checker PIN
            $table->decimal('standard_price', 12, 2);
            $table->decimal('reseller_price', 12, 2);
            $table->decimal('vip_price', 12, 2);
            $table->string('status')->default('active');
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // 4. Exam PIN Purchases Table
        Schema::create('exam_pin_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('exam_code'); // WAEC, NECO, etc.
            $table->integer('quantity')->default(1);
            $table->decimal('unit_price', 12, 2);
            $table->decimal('total_amount', 12, 2);
            $table->json('pins_data'); // array of ['pin' => '...', 'serial' => '...']
            $table->string('reference')->unique();
            $table->string('status')->default('successful'); // successful, failed
            $table->timestamps();

            $table->index(['user_id', 'created_at']);
        });

        // 5. Referral Commission Log Table
        Schema::create('referral_commissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete(); // Upline earning commission
            $table->foreignId('referred_user_id')->constrained('users')->cascadeOnDelete(); // Downline who transacted
            $table->decimal('amount', 12, 2);
            $table->string('description');
            $table->string('status')->default('credited'); // credited, withdrawn
            $table->timestamps();

            $table->index(['user_id', 'created_at']);
        });

        // 6. Airtime to Cash Requests Table
        Schema::create('airtime_cash_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('network'); // MTN, AIRTEL, GLO, 9MOBILE
            $table->decimal('amount', 12, 2); // Airtime amount sent
            $table->decimal('exchange_rate_percent', 5, 2); // e.g. 80%
            $table->decimal('amount_to_receive', 12, 2); // Cash amount
            $table->string('sender_phone'); // Customer's phone sending airtime
            $table->string('receiver_phone'); // Platform's designated receiving SIM
            $table->string('payout_method')->default('wallet'); // wallet, bank
            $table->string('bank_name')->nullable();
            $table->string('bank_account_number')->nullable();
            $table->string('bank_account_name')->nullable();
            $table->string('reference')->unique();
            $table->string('status')->default('pending'); // pending, approved, rejected
            $table->text('admin_note')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('airtime_cash_requests');
        Schema::dropIfExists('referral_commissions');
        Schema::dropIfExists('exam_pin_transactions');
        Schema::dropIfExists('exam_packages');
        Schema::dropIfExists('virtual_bank_accounts');
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['referred_by_id']);
            $table->dropColumn(['tier', 'referral_code', 'referred_by_id', 'referral_balance']);
        });
    }
};
