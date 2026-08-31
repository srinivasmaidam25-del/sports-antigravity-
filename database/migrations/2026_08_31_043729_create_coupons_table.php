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
        Schema::create('coupons', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('discount_type'); // FIXED, PERCENTAGE
            $table->decimal('discount_value', 8, 2);
            $table->decimal('min_booking_amount', 8, 2)->default(0.00);
            $table->decimal('max_discount_amount', 8, 2)->nullable();
            $table->dateTime('starts_at');
            $table->dateTime('ends_at');
            $table->integer('usage_limit')->nullable(); // Total times this coupon can be used overall
            $table->integer('times_used')->default(0);
            $table->integer('user_limit')->default(1); // Times a single user can use it
            $table->json('facility_restrictions')->nullable(); // Array of facility IDs, null = all
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('coupons');
    }
};
