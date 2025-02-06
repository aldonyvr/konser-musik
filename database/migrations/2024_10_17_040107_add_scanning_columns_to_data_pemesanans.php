<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('data_pemesanans', function (Blueprint $table) {
            $table->timestamp('last_scanned_at')->nullable();
            $table->integer('scan_count')->default(0);
            $table->boolean('is_valid')->default(true);
            $table->string('invalidation_reason')->nullable();
        });
    }

    public function down()
    {
        Schema::table('data_pemesanans', function (Blueprint $table) {
            $table->dropColumn([
                'last_scanned_at',
                'scan_count',
                'is_valid',
                'invalidation_reason'
            ]);
        });
    }
};
