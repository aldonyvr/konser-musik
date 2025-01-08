<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Uuid;


class Register extends Model
{
    use HasFactory, Uuid;

    protected $table = 'register';

    protected $fillable = [
        'nama',
        'email',
        'phone',
        'password',
        'photo',
    ];


}
