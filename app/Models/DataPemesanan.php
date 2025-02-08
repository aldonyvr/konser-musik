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
        'status_pembayaran',
        'nama_pemesan',
        'email_pemesan',
        'telepon_pemesan',
        'alamat_pemesan',
        'tanggal_pemesan',
        'jumlah_tiket',
        'total_harga',
        'gate_type',
        'is_scanned',
        'scanned_at',
        'scan_status',
        'scanned_by'
    ];

    protected $casts = [
        'last_scanned_at' => 'datetime',
        'is_valid' => 'boolean',
        'is_scanned' => 'boolean',
        'scanned_at' => 'datetime'
    ];

    public function tiket(): BelongsTo
    {
        return $this->belongsTo(Tiket::class);
    }
}
