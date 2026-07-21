<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /** Creates the car_watch_requests table ("notify me" requests for sold-out brand/model combos). */
    public function up(): void
    {
        Schema::create('car_watch_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('brand_id')->constrained();
            $table->foreignId('car_model_id')->constrained();
            $table->boolean('is_active')->default(true);
            $table->timestamp('notified_at')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'brand_id', 'car_model_id']);
        });
    }

    /** Drops the car_watch_requests table. */
    public function down(): void
    {
        Schema::dropIfExists('car_watch_requests');
    }
};
