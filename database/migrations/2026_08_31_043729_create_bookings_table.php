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
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('facility_id')->constrained('facilities')->onDelete('cascade');
            $table->date('booking_date');
            $table->time('start_time');
            $table->time('end_time');
            $table->decimal('original_price', 8, 2);
            $table->decimal('discount_amount', 8, 2)->default(0.00);
            $table->decimal('final_price', 8, 2);
            $table->string('status')->default('PENDING'); // PENDING, CONFIRMED, CANCELLED, EXPIRED
            $table->timestamp('reserved_until')->nullable(); // For slot temporary locking during checkout
            $table->string('payment_type'); // FULL, ADVANCE, MEMBERSHIP
            $table->unsignedBigInteger('coupon_id')->nullable()->index();
            $table->unsignedBigInteger('user_membership_id')->nullable()->index();
            $table->text('notes')->nullable();
            $table->timestamps();

            // Add index for fast occupancy checks and lock querying
            $table->index(['facility_id', 'booking_date', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
