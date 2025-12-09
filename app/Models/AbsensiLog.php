<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AbsensiLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'absensi_id',
        'santri_id',
        'sub_kegiatan_id',
        'tanggal',
        'status_lama',
        'status_baru',
        'diubah_oleh',
        'disetujui_oleh',
        'approval_status',
        'disetujui_pada',
    ];

    protected function casts(): array
    {
        return [
            'tanggal' => 'date',
            'disetujui_pada' => 'datetime',
        ];
    }

    public function absensi(): BelongsTo
    {
        return $this->belongsTo(Absensi::class);
    }

    public function santri(): BelongsTo
    {
        return $this->belongsTo(Santri::class);
    }

    public function subKegiatan(): BelongsTo
    {
        return $this->belongsTo(SubKegiatan::class);
    }

    public function diubahOleh(): BelongsTo
    {
        return $this->belongsTo(User::class, 'diubah_oleh');
    }

    public function disetujuiOleh(): BelongsTo
    {
        return $this->belongsTo(User::class, 'disetujui_oleh');
    }

    public function scopePending($query)
    {
        return $query->where('approval_status', 'pending');
    }
}
