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
        'reguler',
        'vip',
        'harga_regular',
        'harga_vip',
        'opengate',
        'closegate',
        'gate_a_capacity',
        'gate_b_capacity',
        'gate_c_capacity',
        'gate_d_capacity',
        'gate_e_capacity',
    ];

    protected $casts = [
        'reguler' => 'integer',
        'vip' => 'integer',
        'gate_a_capacity' => 'integer',
        'gate_b_capacity' => 'integer',
        'gate_c_capacity' => 'integer',
        'gate_d_capacity' => 'integer',
        'gate_e_capacity' => 'integer',
    ];

    public function konser(): BelongsTo
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
