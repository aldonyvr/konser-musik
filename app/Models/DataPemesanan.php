<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DataPemesanan extends Model
{
    use HasFactory;

    protected $fillable = [
        'tiket_id',
        'user_id',
        'nama_pemesan',
        'email_pemesan',
        'telepon_pemesan',
        'alamat_pemesan',
        'tanggal_pemesanan',
        'total_harga',
    ];
}
