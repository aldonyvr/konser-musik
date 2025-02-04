<?php

namespace App\Models;

use App\Traits\Uuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DataPemesanan extends Model
{
    use HasFactory;
    use Uuid;

    protected $fillable = [
        'tiket_id',
        'user_id',
        'nama_pemesan',
        'email_pemesan',
        'telepon_pemesan',
        'alamat_pemesan',
        'tanggal_pemesan',
        'jumlah_tiket',
        'total_harga'
    ];
}
