<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->text('player_token')->nullable()->after('timezone');
        });

        // Generate player tokens for existing users
        \App\Models\User::all()->each(function ($user) {
            $user->update([
                'player_token' => base64_encode(Str::random(64))
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('player_token');
        });
    }
};
