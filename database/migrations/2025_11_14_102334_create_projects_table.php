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
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
             $table->unsignedBigInteger('customer_id')->nullable();
        $table->unsignedBigInteger('lead_id')->nullable();

        $table->string('project_name');
        $table->text('description')->nullable();
        $table->unsignedBigInteger('assigned_to')->nullable();

        $table->date('start_date')->nullable();
        $table->date('end_date')->nullable();

        $table->timestamps();

        // Foreign Keys
        $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');
        $table->foreign('lead_id')->references('id')->on('leads')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
