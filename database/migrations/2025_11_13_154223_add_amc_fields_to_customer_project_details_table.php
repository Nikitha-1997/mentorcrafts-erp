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
             $table->text('amc_description')->nullable()->after('notes');
            $table->string('amc_month', 50)->nullable()->after('amc_description');
            $table->decimal('amc_amount', 10, 2)->nullable()->after('amc_month');
            $table->text('amc_remarks')->nullable()->after('amc_amount');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customer_project_details', function (Blueprint $table) {
           $table->dropColumn([
                'amc_description',
                'amc_month',
                'amc_amount',
                'amc_remarks',
            ]);
        });
    }
};
