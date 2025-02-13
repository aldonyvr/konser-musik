<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Tiket;
use App\Models\Lokasi;
use App\Models\JenisTiket;
use App\Traits\Uuid;

class Konser extends Model
{
    use HasFactory, Uuid;

    protected $fillable = [
        'title',
        'tanggal',
        'jam',
        'nama_sosmed',
        'lokasi',
        'tiket_tersedia',
        'image',
        'deskripsi',
        'kontak'
    ];

    public function tiket()
    {
        return $this->hasOne(Tiket::class, 'konsers_id');
    }

    public function mitra()
    {
        return $this->hasOne(User::class, 'konser_id');
    }

    public function tikets()
    {
        return $this->hasMany(Tiket::class, 'konsers_id');
    }

    public function pemesanan()
    {
        return $this->hasManyThrough(
            DataPemesanan::class,
            Tiket::class,
            'konsers_id',
            'tiket_id'
        );
    }
}
