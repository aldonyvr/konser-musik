<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('tiket_scanners', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ticket_id')->constrained('data_pemesanans');
            $table->foreignId('scanner_id')->constrained('users');
            $table->timestamp('scanned_at');
            $table->enum('status', ['success', 'failed', 'invalid']);
            $table->string('notes')->nullable();
            $table->string('location')->nullable();
            $table->string('gate_number')->nullable();
            $table->timestamps();
            
            // Add index for better query performance
            $table->index(['ticket_id', 'scanner_id']);
            $table->index('scanned_at');
        });
    }

    public function down()
    {
        Schema::dropIfExists('tiket_scanners');
    }
};
