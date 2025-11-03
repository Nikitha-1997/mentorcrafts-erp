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
        Schema::create('reminders', function (Blueprint $table) {
            $table->id();
            $table->string('title');               // Reminder title
            $table->text('description')->nullable(); // Optional notes
            $table->dateTime('remind_at');        // When to remind
            $table->string('type')->nullable();   // e.g., lead_followup, domain_renewal
            $table->foreignId('related_id')->nullable(); // Related entity id (lead_id etc.)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reminders');
    }
};
