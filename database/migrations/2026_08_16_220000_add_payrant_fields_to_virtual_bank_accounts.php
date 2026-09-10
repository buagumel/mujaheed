<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('virtual_bank_accounts', function (Blueprint $table) {
            $table->string('account_reference')->nullable()->after('reference');
            $table->string('customer_name')->nullable()->after('account_name');
            $table->string('identity_type')->nullable()->after('provider');
            $table->string('license_number')->nullable()->after('identity_type');
            $table->decimal('total_funded', 14, 2)->default(0.00)->after('status');
            $table->timestamp('last_funded_at')->nullable()->after('total_funded');
            $table->json('raw_response')->nullable()->after('last_funded_at');
        });
    }

    public function down(): void
    {
        Schema::table('virtual_bank_accounts', function (Blueprint $table) {
            $table->dropColumn([
                'account_reference',
                'customer_name',
                'identity_type',
                'license_number',
                'total_funded',
                'last_funded_at',
                'raw_response',
            ]);
        });
    }
};
