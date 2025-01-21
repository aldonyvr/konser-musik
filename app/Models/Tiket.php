<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Uuid;

class Tiket extends Model
{
    use HasFactory, Uuid;

    protected $fillable = [
        // 'users_id', 
        'konsers_id',
        'tiket_tersedia',
        'regular', 
        'harga_vip', 
        'harga_regular', 
        'vip', 
        'reguler', 
        'opengate', 
        'closegate', 
    ];

    public function konsers () {
        return $this->belongsTo(Konser::class);
    }
    public function user () {
        return $this->belongsTo(User::class);
    }
    public function pembayaran () {
        return $this->hasMany(User::class);
    }
}
