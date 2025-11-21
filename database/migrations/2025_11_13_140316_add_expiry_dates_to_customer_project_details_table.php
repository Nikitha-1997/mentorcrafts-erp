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
             if (!Schema::hasColumn('customer_project_details', 'domain_expiry_date')) {
                $table->date('domain_expiry_date')->nullable()->after('domain_provider');
            }
            if (!Schema::hasColumn('customer_project_details', 'hosting_expiry_date')) {
                $table->date('hosting_expiry_date')->nullable()->after('hosting_provider');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customer_project_details', function (Blueprint $table) {
                 $table->dropColumn(['domain_expiry_date', 'hosting_expiry_date']);
        });
    }
};
