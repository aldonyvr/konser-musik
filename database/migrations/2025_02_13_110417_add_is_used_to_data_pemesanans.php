<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('data_pemesanans', function (Blueprint $table) {
            $table->boolean('is_used')->default(false)->after('status_pembayaran');
        });
    }

    public function down()
    {
        Schema::table('data_pemesanans', function (Blueprint $table) {
            $table->dropColumn('is_used');
        });
    }
};
