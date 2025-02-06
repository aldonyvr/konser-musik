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
        'last_scanned_at',
        'scan_count',
        'is_valid'
    ];

    protected $casts = [
        'last_scanned_at' => 'datetime',
        'is_valid' => 'boolean'
    ];

    public function tiket(): BelongsTo
    {
        return $this->belongsTo(Tiket::class);
    }

    public function scanLogs()
    {
        return $this->hasMany(TiketScanner::class, 'ticket_id');
    }

    public function getLastScanAttribute()
    {
        return $this->scanLogs()->latest('scanned_at')->first();
    }

    public function isValidForEntry()
    {
        return $this->is_valid && 
               $this->status_pembayaran === 'Successfully' && 
               $this->scan_count < config('tickets.max_scans', 1);
    }
}
