<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /** Creates the maintenance_centers table. */
    public function up(): void
    {
        Schema::create('maintenance_centers', function (Blueprint $table) {
            $table->id();
            $table->string('name_ar');
            $table->string('name_en');
            $table->string('phone');
            $table->string('whatsapp')->nullable();
            $table->string('address_ar')->nullable();
            $table->string('address_en')->nullable();
            $table->decimal('lat', 10, 7)->nullable();
            $table->decimal('lng', 10, 7)->nullable();
            $table->decimal('rating', 3, 2)->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /** Drops the maintenance_centers table. */
    public function down(): void
    {
        Schema::dropIfExists('maintenance_centers');
    }
};
