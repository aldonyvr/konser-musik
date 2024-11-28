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
        Schema::create('tikets', function (Blueprint $table) {
            $table->id();
            $table->uuid()->unique();
            // $table->foreignId('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreignId('konsers_id')->references('id')->on('konsers')->onDelete('cascade')->nullable();
            $table->integer('reguler')->nullable();
            $table->integer('vip')->nullable();
            $table->string('opengate')->nullable();
            $table->string('closegate')->nullable();
            $table->timestamps();
        
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tikets');
    }
};
