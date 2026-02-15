<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('displays', function (Blueprint $table) {
            $table->string('ip_address')->nullable()->after('code');
            $table->boolean('auto_register')->default(false)->after('ip_address');
        });
    }

    public function down(): void
    {
        Schema::table('displays', function (Blueprint $table) {
            $table->dropColumn(['ip_address', 'auto_register']);
        });
    }
};
