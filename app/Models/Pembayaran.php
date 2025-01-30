<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pembayaran extends Model
{
    use HasFactory;

    protected $fillable = [ 
        'datapemesanan_id',
        'jumlah', 
        'metode_pembayaran', 
        'status_pembayaran',
    ];

    public function tiket () {
        return $this->belongsTo(Tiket::class);
    }
};
