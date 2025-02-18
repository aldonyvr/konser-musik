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
            $table->foreignId('konsers_id')->references('id')->on('konsers')->onDelete('cascade')->nullable();
            $table->decimal('harga_vip', 15, 2)->nullable();
            $table->decimal('harga_regular', 15, 2)->nullable();
            $table->string('tiket_tersedia')->nullable();
            $table->integer('reguler')->nullable();
            $table->integer('vip')->nullable();
            $table->string('opengate')->nullable();
            $table->string('closegate')->nullable();
            $table->integer('gate_a_capacity')->default(0)->nullable();
            $table->integer('gate_b_capacity')->default(0)->nullable();
            $table->integer('gate_c_capacity')->default(0)->nullable();
            $table->integer('gate_d_capacity')->default(0)->nullable();
            $table->integer('gate_e_capacity')->default(0)->nullable();
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
