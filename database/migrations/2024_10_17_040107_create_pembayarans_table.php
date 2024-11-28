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
            $table->string('harga');
            $table->foreignId('tiket_id')->references('id')->on('tikets')->onDelete('cascade');
            $table->string('metode_pembayaran');
            $table->string('status_pembayaran')->default('pending');
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
