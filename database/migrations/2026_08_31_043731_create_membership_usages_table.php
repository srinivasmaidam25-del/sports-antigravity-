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
        Schema::create('membership_usages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_membership_id')->constrained('user_memberships')->onDelete('cascade');
            $table->unsignedBigInteger('booking_id')->index(); // Linked booking that consumed the quota
            $table->integer('hours_consumed');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('membership_usages');
    }
};
