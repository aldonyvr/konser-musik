<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TiketScanner extends Model
{
    protected $table = 'tiket_scanners';

    protected $fillable = [
        'ticket_id',
        'scanner_id',
        'scanned_at',
        'status',
        'notes',
        'location',
        'gate_number'
    ];

    protected $casts = [
        'scanned_at' => 'datetime'
    ];

    public function ticket(): BelongsTo
    {
        return $this->belongsTo(DataPemesanan::class, 'ticket_id');
    }

    public function scanner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'scanner_id');
    }
}
