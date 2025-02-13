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
        Schema::table('data_pemesanans', function (Blueprint $table) {
            $table->string('order_id')->after('user_id')->nullable();
            $table->string('gate')->after('status_pembayaran')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('data_pemesanans', function (Blueprint $table) {
            $table->dropColumn('order_id');
            $table->dropColumn('gate');
        });
    }
};