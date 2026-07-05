<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/** Stores per-platform maintenance and upgrade configuration for the mobile app. */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('app_configs', function (Blueprint $table) {
            $table->id();
            $table->string('platform')->unique();

            $table->boolean('maintenance_enabled')->default(false);
            $table->string('maintenance_message')->nullable();

            $table->string('min_version');
            $table->string('current_version');
            $table->boolean('force_upgrade')->default(false);
            $table->string('upgrade_message')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('app_configs');
    }
};
