<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /** Creates the maintenance_services table. */
    public function up(): void
    {
        Schema::create('maintenance_services', function (Blueprint $table) {
            $table->id();
            $table->foreignId('maintenance_center_id')->constrained()->cascadeOnDelete();
            $table->string('name_ar');
            $table->string('name_en');
            $table->text('description_ar')->nullable();
            $table->text('description_en')->nullable();
            $table->decimal('price', 10, 2);
            $table->boolean('is_active')->default(true);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    /** Drops the maintenance_services table. */
    public function down(): void
    {
        Schema::dropIfExists('maintenance_services');
    }
};
