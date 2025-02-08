<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('data_pemesanans', function (Blueprint $table) {
            $table->boolean('is_scanned')->default(false);
            $table->timestamp('scanned_at')->nullable();
            $table->string('scan_status')->nullable();
            $table->foreignId('scanned_by')->nullable()->constrained('users');
        });
    }

    public function down()
    {
        Schema::table('data_pemesanans', function (Blueprint $table) {
            $table->dropColumn(['is_scanned', 'scanned_at', 'scan_status']);
            $table->dropForeign(['scanned_by']);
            $table->dropColumn('scanned_by');
        });
    }
};
