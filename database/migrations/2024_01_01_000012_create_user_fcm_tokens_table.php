<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/** Stores one FCM token per registered device per user, allowing concurrent multi-device push notifications. */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_fcm_tokens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('token')->unique();
            $table->timestamps();
        });

        $this->migrateExistingTokens();

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('fcm_token');
        });
    }

    /** Copies any already-stored single fcm_token per user into the new table before dropping the column. */
    private function migrateExistingTokens(): void
    {
        DB::table('users')
            ->whereNotNull('fcm_token')
            ->select('id', 'fcm_token')
            ->orderBy('id')
            ->each(function (object $user) {
                DB::table('user_fcm_tokens')->insertOrIgnore([
                    'user_id'    => $user->id,
                    'token'      => $user->fcm_token,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('fcm_token')->nullable();
        });

        Schema::dropIfExists('user_fcm_tokens');
    }
};
