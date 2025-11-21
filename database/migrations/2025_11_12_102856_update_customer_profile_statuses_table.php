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
        Schema::table('customer_profile_statuses', function (Blueprint $table) {

            // Ensure foreign key exists
            if (!Schema::hasColumn('customer_profile_statuses', 'customer_id')) {
                $table->foreignId('customer_id')->constrained()->onDelete('cascade');
            }

            // Basic structure fields
            if (!Schema::hasColumn('customer_profile_statuses', 'service_name')) {
                $table->string('service_name')->after('customer_id');
            }

            if (!Schema::hasColumn('customer_profile_statuses', 'section_name')) {
                $table->string('section_name')->after('service_name');
            }

            if (!Schema::hasColumn('customer_profile_statuses', 'subpoint_name')) {
                $table->string('subpoint_name')->after('section_name');
            }

            // Status flags
            if (!Schema::hasColumn('customer_profile_statuses', 'planning')) {
                $table->boolean('planning')->default(false)->after('subpoint_name');
            }

            if (!Schema::hasColumn('customer_profile_statuses', 'documentation')) {
                $table->boolean('documentation')->default(false)->after('planning');
            }

            if (!Schema::hasColumn('customer_profile_statuses', 'training')) {
                $table->boolean('training')->default(false)->after('documentation');
            }

            // ✅ New field: Implementation status
            if (!Schema::hasColumn('customer_profile_statuses', 'implementation')) {
                $table->boolean('implementation')->default(false)->after('training');
            }

            // File upload & remarks
            if (!Schema::hasColumn('customer_profile_statuses', 'file_path')) {
                $table->string('file_path')->nullable()->after('implementation');
            }

            if (!Schema::hasColumn('customer_profile_statuses', 'remarks')) {
                $table->text('remarks')->nullable()->after('file_path');
            }

            // Add indexes for performance
          // Add indexes for performance (shorter name)
$table->index(['customer_id', 'service_name', 'section_name'], 'cust_prof_idx');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customer_profile_statuses', function (Blueprint $table) {
            $table->dropIndex(['customer_id', 'service_name', 'section_name']);
            $table->dropIndex('cust_prof_idx');


            // Optional: Uncomment these if you ever want to rollback column changes
            /*
            $table->dropColumn([
                'planning', 'documentation', 'training', 'implementation',
                'file_path', 'remarks'
            ]);
            */
        });
    }
};
