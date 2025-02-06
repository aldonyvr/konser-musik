<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tiket_gate', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tiket_id')->constrained()->onDelete('cascade');
            $table->foreignId('gate_id')->constrained()->onDelete('cascade');
            $table->integer('capacity')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tiket_gate');
    }
};
