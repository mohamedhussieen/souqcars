<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /** Creates the car_ratings table (one rating+comment per user per car). */
    public function up(): void
    {
        Schema::create('car_ratings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('car_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('rating');
            $table->text('comment')->nullable();
            $table->timestamps();
            $table->unique(['user_id', 'car_id']);
        });
    }

    /** Drops the car_ratings table. */
    public function down(): void
    {
        Schema::dropIfExists('car_ratings');
    }
};
