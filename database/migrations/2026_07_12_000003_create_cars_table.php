<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /** Creates the cars table — the central listing entity with a loosely polymorphic seller (seller_type + seller_id). */
    public function up(): void
    {
        Schema::create('cars', function (Blueprint $table) {
            $table->id();

            // Seller — polymorphic-ish, supports 3 phases (admin/individual/showroom)
            $table->string('seller_type')->default('admin');
            $table->unsignedBigInteger('seller_id')->nullable();

            // Relations
            $table->foreignId('brand_id')->constrained();
            $table->foreignId('car_model_id')->constrained();
            $table->foreignId('city_id')->constrained();
            $table->foreignId('color_id')->nullable()->constrained();

            // Basic Info
            $table->year('year');
            $table->string('title_ar');
            $table->string('title_en')->nullable();
            $table->text('description_ar')->nullable();
            $table->text('description_en')->nullable();

            // Specs
            $table->decimal('price', 12, 2);
            $table->string('payment_type')->default('cash');
            $table->unsignedInteger('mileage')->nullable();
            $table->string('condition')->default('used');
            $table->string('transmission');
            $table->string('fuel_type');
            $table->string('body_type');
            $table->unsignedTinyInteger('owners_count')->default(1);

            // Inspection — file stored in Spatie Media collection 'inspection_report'
            $table->boolean('has_inspection_report')->default(false);

            // Status
            $table->string('status')->default('pending');
            $table->text('rejection_reason')->nullable();
            $table->boolean('is_featured')->default(false);

            // Stats
            $table->unsignedInteger('views_count')->default(0);
            $table->unsignedInteger('favorites_count')->default(0);
            $table->decimal('rating_avg', 3, 2)->default(0);

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /** Drops the cars table. */
    public function down(): void
    {
        Schema::dropIfExists('cars');
    }
};
