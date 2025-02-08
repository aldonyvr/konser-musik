<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Uuid;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Tiket extends Model
{
    use HasFactory, Uuid;

    protected $fillable = [
        'konsers_id',
        'tiket_tersedia',
        'regular', 
        'harga_vip', 
        'harga_regular', 
        'vip', 
        'reguler', 
        'opengate', 
        'closegate',
        'gate_a_capacity',
        'gate_b_capacity',
        'gate_c_capacity',
        'gate_d_capacity',
        'gate_e_capacity',
    ];

    public function konsers(): BelongsTo
    {
        return $this->belongsTo(Konser::class, 'konsers_id');
    }
    public function user () {
        return $this->belongsTo(User::class);
    }
    public function pembayaran () {
        return $this->hasMany(User::class);
    }
    public function gates()
    {
        return $this->belongsToMany(Gate::class)
                    ->withPivot('capacity')
                    ->withTimestamps();
    }
}
