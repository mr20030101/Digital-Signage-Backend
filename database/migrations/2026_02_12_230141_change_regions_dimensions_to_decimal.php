<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('regions', function (Blueprint $table) {
            $table->decimal('width', 5, 1)->change(); // 5 digits total, 1 decimal place (e.g., 100.0)
            $table->decimal('height', 5, 1)->change();
            $table->decimal('top', 5, 1)->change();
            $table->decimal('left', 5, 1)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('regions', function (Blueprint $table) {
            $table->integer('width')->change();
            $table->integer('height')->change();
            $table->integer('top')->change();
            $table->integer('left')->change();
        });
    }
};
