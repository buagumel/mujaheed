<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Alter users table
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone')->nullable()->unique()->after('email');
            $table->string('role')->default('customer')->index()->after('password'); // admin, customer
            $table->string('status')->default('active')->index()->after('role'); // active, suspended, blocked
            $table->string('transaction_pin')->nullable()->after('status');
            $table->unsignedTinyInteger('pin_attempts')->default(0)->after('transaction_pin');
            $table->timestamp('pin_locked_until')->nullable()->after('pin_attempts');
            $table->string('avatar')->nullable()->after('pin_locked_until');
        });

        // Wallets
        Schema::create('wallets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->decimal('balance', 16, 2)->default(0.00);
            $table->string('currency', 3)->default('NGN');
            $table->string('status')->default('active')->index(); // active, frozen
            $table->timestamps();
        });

        // Wallet transactions (ledger)
        Schema::create('wallet_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('wallet_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('type', 20)->index(); // credit, debit, refund, reversal
            $table->decimal('amount', 16, 2);
            $table->decimal('balance_before', 16, 2);
            $table->decimal('balance_after', 16, 2);
            $table->string('reference', 64)->unique()->index();
            $table->string('description');
            $table->string('status', 20)->default('successful')->index(); // pending, successful, failed, reversed
            $table->json('metadata')->nullable();
            $table->timestamps();
        });

        // VTU Transactions
        Schema::create('vtu_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('wallet_transaction_id')->nullable()->constrained('wallet_transactions')->nullOnDelete();
            $table->string('reference', 64)->unique()->index();
            $table->string('service_type', 30)->index(); // airtime, data, electricity, cable
            $table->string('provider', 50)->index(); // MTN, AIRTEL, GLO, 9MOBILE, IKEDC, DSTV, etc.
            $table->string('provider_reference', 100)->nullable()->index();
            $table->string('plan_code', 50)->nullable();
            $table->string('plan_name', 100)->nullable();
            $table->string('recipient', 50)->index();
            $table->decimal('amount', 16, 2);
            $table->decimal('cost_price', 16, 2)->default(0.00);
            $table->decimal('discount_amount', 16, 2)->default(0.00);
            $table->decimal('fee', 16, 2)->default(0.00);
            $table->string('status', 20)->default('pending')->index(); // pending, successful, failed, reversed
            $table->string('token', 255)->nullable(); // Electricity prepaid token or recharge pin
            $table->string('customer_name', 150)->nullable();
            $table->string('units', 50)->nullable(); // Electricity units
            $table->text('error_message')->nullable();
            $table->json('response_payload')->nullable();
            $table->timestamps();
        });

        // Payment Transactions (Wallet funding)
        Schema::create('payment_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('reference', 64)->unique()->index();
            $table->string('payment_provider', 30)->default('mock'); // mock, paystack, flutterwave, monnify
            $table->decimal('amount', 16, 2);
            $table->decimal('fee', 16, 2)->default(0.00);
            $table->string('status', 20)->default('pending')->index(); // pending, successful, failed
            $table->string('channel', 50)->nullable();
            $table->text('payment_url')->nullable();
            $table->string('provider_reference', 100)->nullable()->index();
            $table->timestamp('paid_at')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
        });

        // Data Plans
        Schema::create('data_plans', function (Blueprint $table) {
            $table->id();
            $table->string('network', 20)->index(); // MTN, AIRTEL, GLO, 9MOBILE
            $table->string('name', 100);
            $table->string('code', 50)->unique();
            $table->string('type', 30)->default('SME'); // SME, Gifting, Corporate
            $table->string('size', 20); // 500MB, 1GB, 2GB, 5GB, 10GB
            $table->string('validity', 50); // 30 Days, 1 Day
            $table->decimal('provider_price', 16, 2)->default(0.00);
            $table->decimal('selling_price', 16, 2);
            $table->string('status', 20)->default('active')->index(); // active, inactive
            $table->timestamps();
        });

        // Cable Plans
        Schema::create('cable_plans', function (Blueprint $table) {
            $table->id();
            $table->string('provider', 30)->index(); // DSTV, GOTV, STARTIMES
            $table->string('name', 100);
            $table->string('code', 50)->unique();
            $table->decimal('provider_price', 16, 2)->default(0.00);
            $table->decimal('selling_price', 16, 2);
            $table->string('status', 20)->default('active')->index();
            $table->timestamps();
        });

        // Electricity Providers
        Schema::create('electricity_providers', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('code', 30)->unique(); // IKEDC, EKEDC, AEDC, IBEDC, etc.
            $table->string('type', 30)->default('prepaid_and_postpaid');
            $table->decimal('min_amount', 16, 2)->default(500.00);
            $table->decimal('max_amount', 16, 2)->default(100000.00);
            $table->decimal('convenience_fee', 16, 2)->default(0.00);
            $table->string('status', 20)->default('active')->index();
            $table->timestamps();
        });

        // Network Settings (for Airtime & network-level config)
        Schema::create('network_settings', function (Blueprint $table) {
            $table->id();
            $table->string('network', 20)->unique(); // MTN, AIRTEL, GLO, 9MOBILE
            $table->decimal('airtime_discount_percent', 5, 2)->default(2.00); // 2% discount for user
            $table->decimal('airtime_min_amount', 16, 2)->default(50.00);
            $table->decimal('airtime_max_amount', 16, 2)->default(50000.00);
            $table->string('status', 20)->default('active');
            $table->timestamps();
        });

        // In-app Notifications
        Schema::create('app_notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('title', 150);
            $table->text('message');
            $table->string('type', 30)->default('system'); // wallet, vtu, security, system
            $table->boolean('is_read')->default(false)->index();
            $table->string('action_url')->nullable();
            $table->timestamps();
        });

        // Audit Logs
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('action', 100)->index();
            $table->text('description');
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
        });

        // System Settings
        Schema::create('system_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key', 50)->unique();
            $table->text('value')->nullable();
            $table->string('group', 30)->default('general');
            $table->timestamps();
        });

        // Personal Access Tokens for Sanctum
        if (!Schema::hasTable('personal_access_tokens')) {
            Schema::create('personal_access_tokens', function (Blueprint $table) {
                $table->id();
                $table->morphs('tokenable');
                $table->string('name');
                $table->string('token', 64)->unique();
                $table->text('abilities')->nullable();
                $table->timestamp('last_used_at')->nullable();
                $table->timestamp('expires_at')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('personal_access_tokens');
        Schema::dropIfExists('system_settings');
        Schema::dropIfExists('audit_logs');
        Schema::dropIfExists('app_notifications');
        Schema::dropIfExists('network_settings');
        Schema::dropIfExists('electricity_providers');
        Schema::dropIfExists('cable_plans');
        Schema::dropIfExists('data_plans');
        Schema::dropIfExists('payment_transactions');
        Schema::dropIfExists('vtu_transactions');
        Schema::dropIfExists('wallet_transactions');
        Schema::dropIfExists('wallets');

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'phone',
                'role',
                'status',
                'transaction_pin',
                'pin_attempts',
                'pin_locked_until',
                'avatar',
            ]);
        });
    }
};
