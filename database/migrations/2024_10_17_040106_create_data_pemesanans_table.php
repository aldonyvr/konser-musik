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
        Schema::create('data_pemesanans', function (Blueprint $table) {
            $table->id();
            $table->uuid()->unique();
            $table->foreignId('tiket_id')->references('id')->on('tikets')->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('nama_pemesan');
            $table->string('email_pemesan');
            $table->string('telepon_pemesan');
            $table->string('alamat_pemesan');
            $table->string('tanggal_pemesan');
            $table->string('jumlah_tiket');
            $table->string('status_pembayaran');
            $table->string('total_harga');
            $table->string('gate_type')->nullable(); // Add this column to store which gate was selected
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('data_pemesanans');
    }
};
