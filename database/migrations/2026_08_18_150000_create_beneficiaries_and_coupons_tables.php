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
        // 1. Saved Beneficiaries (Phone numbers, meter numbers, decoder IUCs)
        if (!Schema::hasTable('beneficiaries')) {
            Schema::create('beneficiaries', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
                $table->string('service_type'); // airtime, data, electricity, cable
                $table->string('identifier'); // phone number, meter number, smartcard IUC
                $table->string('name_nickname')->nullable(); // e.g. "Mum's Line", "Home Meter"
                $table->string('network_or_disco')->nullable(); // mtn, airtel, glo, 9mobile, ikeja-electric, etc.
                $table->timestamps();

                $table->index(['user_id', 'service_type']);
            });
        }

        // 2. Coupons & Promo Codes
        if (!Schema::hasTable('coupons')) {
            Schema::create('coupons', function (Blueprint $table) {
                $table->id();
                $table->string('code')->unique();
                $table->string('discount_type')->default('fixed'); // fixed, percentage
                $table->decimal('discount_value', 10, 2)->default(0.00); // ₦50 or 5%
                $table->decimal('min_order_amount', 10, 2)->default(0.00);
                $table->decimal('max_discount_amount', 10, 2)->nullable(); // cap for percentage
                $table->integer('usage_limit')->nullable(); // null = unlimited
                $table->integer('used_count')->default(0);
                $table->string('service_type')->default('all'); // all, airtime, data, electricity, cable
                $table->dateTime('starts_at')->nullable();
                $table->dateTime('expires_at')->nullable();
                $table->string('status')->default('active'); // active, disabled, expired
                $table->timestamps();
            });
        }

        // 3. Coupon Usages Record
        if (!Schema::hasTable('coupon_usages')) {
            Schema::create('coupon_usages', function (Blueprint $table) {
                $table->id();
                $table->foreignId('coupon_id')->constrained('coupons')->onDelete('cascade');
                $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
                $table->foreignId('transaction_id')->nullable()->constrained('vtu_transactions')->nullOnDelete();
                $table->decimal('discount_applied', 10, 2)->default(0.00);
                $table->timestamps();

                $table->index(['coupon_id', 'user_id']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('coupon_usages');
        Schema::dropIfExists('coupons');
        Schema::dropIfExists('beneficiaries');
    }
};
