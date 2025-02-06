<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Gate extends Model
{
    protected $fillable = ['name'];

    public function tikets()
    {
        return $this->belongsToMany(Tiket::class)
                    ->withPivot('capacity')
                    ->withTimestamps();
    }
}
