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
            $table->integer('width')->change(); // pixels
            $table->integer('height')->change(); // pixels
            $table->integer('top')->change(); // pixels
            $table->integer('left')->change(); // pixels
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('regions', function (Blueprint $table) {
            $table->decimal('width', 5, 1)->change();
            $table->decimal('height', 5, 1)->change();
            $table->decimal('top', 5, 1)->change();
            $table->decimal('left', 5, 1)->change();
        });
    }
};
