<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/** Stores the bilingual terms & conditions / privacy policy clauses shown to mobile users. */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('policy_terms', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('order');
            $table->string('title_ar');
            $table->string('title_en');
            $table->text('body_ar');
            $table->text('body_en');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('policy_terms');
    }
};
