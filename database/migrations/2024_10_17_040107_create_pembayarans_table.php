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
        Schema::create('pembayarans', function (Blueprint $table) {
            $table->id();
            $table->uuid()->unique();
            $table->foreignId('data_pemesanan_id')->references('id')->on('data_pemesanans')->onDelete('cascade');
            $table->string('status_pembayaran')->default('pending');
            $table->string('metode_pembayaran');
            $table->timestamps();
        
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pembayarans');
    }
};
