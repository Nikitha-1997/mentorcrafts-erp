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
        Schema::create('customer_profile_statuses', function (Blueprint $table) {
            $table->id();
             $table->foreignId('customer_id')->constrained()->onDelete('cascade');
            $table->string('service_name'); // e.g. Business Mentoring
            $table->string('section_name'); // e.g. Human Resources
            $table->string('subpoint_name'); // e.g. Organizational Structure

            // Status flags
            $table->boolean('planning')->default(false);
            $table->boolean('documentation')->default(false);
            $table->boolean('training')->default(false);

            // File upload & remarks
            $table->string('file_path')->nullable();
            $table->text('remarks')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_profile_statuses');
    }
};
