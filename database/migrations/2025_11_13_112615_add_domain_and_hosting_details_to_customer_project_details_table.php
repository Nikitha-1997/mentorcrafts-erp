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
        Schema::table('customer_project_details', function (Blueprint $table) {
              // 🟦 Domain details
            $table->string('domain_type')->nullable()->after('domain_name')->comment('Type fetched from settings e.g. Client Owned | MC Domain Service');
            $table->string('domain_service_provider')->nullable()->after('domain_type');
            $table->date('domain_purchase_date')->nullable()->after('domain_service_provider');
            $table->string('domain_subscription_duration')->nullable()->after('domain_purchase_date');
            $table->string('domain_renewal_month')->nullable()->after('domain_subscription_duration');
            $table->string('domain_url')->nullable()->after('domain_renewal_month');
            $table->string('domain_username')->nullable()->after('domain_url');
            $table->string('domain_password')->nullable()->after('domain_username');
            $table->boolean('domain_not_included_in_amc')->default(false)->after('domain_password');

            // 🟩 Hosting details
            $table->string('hosting_type')->nullable()->after('hosting_provider')->comment('Type fetched from settings e.g. Client Owned | MC Shared Hosting 1');
            $table->string('hosting_service_provider')->nullable()->after('hosting_type');
            $table->date('hosting_purchase_date')->nullable()->after('hosting_service_provider');
            $table->string('hosting_subscription_duration')->nullable()->after('hosting_purchase_date');
            $table->string('hosting_renewal_month')->nullable()->after('hosting_subscription_duration');
            $table->string('hosting_url')->nullable()->after('hosting_renewal_month');
            $table->string('hosting_username')->nullable()->after('hosting_url');
            $table->string('hosting_password')->nullable()->after('hosting_username');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customer_project_details', function (Blueprint $table) {
            $table->dropColumn([
                'domain_type', 'domain_service_provider', 'domain_purchase_date', 'domain_subscription_duration',
                'domain_renewal_month', 'domain_url', 'domain_username', 'domain_password', 'domain_not_included_in_amc',
                'hosting_type', 'hosting_service_provider', 'hosting_purchase_date', 'hosting_subscription_duration',
                'hosting_renewal_month', 'hosting_url', 'hosting_username', 'hosting_password',
            ]);
        });
    }
};
