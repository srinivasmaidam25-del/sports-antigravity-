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
        Schema::create('memberships', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Bronze, Silver, Gold, Platinum
            $table->decimal('price', 8, 2);
            $table->integer('total_hours'); // Hours included
            $table->decimal('discount_percentage', 5, 2)->default(0.00); // Standing discount on additional hours
            $table->boolean('priority_booking')->default(false);
            $table->boolean('kit_rental')->default(false);
            $table->json('special_benefits')->nullable(); // For weekend discount, birthday offer, tournament discount etc.
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('memberships');
    }
};
