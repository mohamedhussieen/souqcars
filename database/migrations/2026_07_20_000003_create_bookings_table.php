<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /** Creates the bookings table. */
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('maintenance_center_id')->constrained();
            $table->foreignId('maintenance_service_id')->constrained();
            $table->foreignId('car_id')->nullable()->constrained()->nullOnDelete();
            $table->string('status')->default('pending');
            $table->date('date');
            $table->time('time');
            $table->decimal('price', 10, 2);
            $table->text('notes')->nullable();
            $table->text('cancellation_reason')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /** Drops the bookings table. */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
