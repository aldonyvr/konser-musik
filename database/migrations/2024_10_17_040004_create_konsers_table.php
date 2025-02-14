<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('konsers', function (Blueprint $table) {
            $table->id();
            $table->uuid()->unique();
            $table->string('title');
            $table->date('tanggal');
            $table->string('lokasi');
            $table->string('jam')->nullable();
            $table->string('kontak')->nullable(); // Changed from number to stringrcont
            $table->string('tiket_tersedia')->nullable(); // Changed from number to stringrcont
            $table->string('nama_sosmed')->nullable();
            $table->string('image')->nullable();
            $table->text('deskripsi')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('konsers');
    }
};
