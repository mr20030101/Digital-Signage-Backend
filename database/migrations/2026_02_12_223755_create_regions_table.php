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
        Schema::create('regions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('layout_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->integer('width'); // percentage or pixels
            $table->integer('height');
            $table->integer('top');
            $table->integer('left');
            $table->integer('z_index')->default(1);
            $table->foreignId('playlist_id')->nullable()->constrained()->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('regions');
    }
};
