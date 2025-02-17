<?php

namespace App\Models;

use App\Traits\Uuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class DataPemesanan extends Model
{
    use HasFactory;
    use Uuid;

    protected $fillable = [
        'tiket_id',
        'user_id',
        'order_id',
        'nama_pemesan',
        'email_pemesan',
        'telepon_pemesan',
        'alamat_pemesan',
        'tanggal_pemesan',
        'jumlah_tiket',
        'status_pembayaran',
        'total_harga',
        'gate',
        'is_used',
        'gate_type'  // Make sure this is included
    ];

    protected $casts = [
        'last_scanned_at' => 'datetime',
        'is_valid' => 'boolean',
        'is_scanned' => 'boolean',
        'scanned_at' => 'datetime',
        'is_used' => 'boolean',
        'tanggal_pemesan' => 'datetime'
    ];

    public function tiket(): BelongsTo
    {
        return $this->belongsTo(Tiket::class, 'tiket_id');
    }

    public function konser()
    {
        return $this->hasOneThrough(
            Konser::class,
            Tiket::class,
            'id',
            'id',
            'tiket_id',
            'konsers_id'
        );
    }

    // Add an accessor to normalize gate_type
    public function getGateTypeAttribute($value)
    {
        return strtolower($value);
    }

    // Add a mutator to ensure consistent storage
    public function setGateTypeAttribute($value)
    {
        $this->attributes['gate_type'] = strtolower($value);
    }
}
