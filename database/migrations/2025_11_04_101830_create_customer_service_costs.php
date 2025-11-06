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
        Schema::create('customer_service_costs', function (Blueprint $table) {
          $table->unsignedBigInteger('customer_id')->index();
    $table->unsignedBigInteger('service_id')->index();

    // Name of cost entry (e.g., Hosting, SSL Certificate)
    $table->string('name');

    // Quoted amount (copied from lead)
    $table->decimal('quoted_amount', 14, 2)->default(0);

    // Approved amount (finalized with customer, editable)
    $table->decimal('approved_amount', 14, 2)->nullable();

    // Billing type - one_time, monthly, yearly, etc.
    $table->string('billing_type')->nullable();

    $table->timestamps();

    // Foreign key constraints
    $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');
    $table->foreign('service_id')->references('id')->on('services')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_service_costs');
    }
};
