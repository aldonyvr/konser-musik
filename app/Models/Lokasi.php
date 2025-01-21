<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class lokasi extends Model
{
    use HasFactory;

    protected $fillable = [
        'konsers_id',
        'lokasi',
    ];

    public function konsers () {
        return $this->belongsTo(Konser::class);
    }
}
