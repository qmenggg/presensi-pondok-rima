<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Izin extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'santri_id',
        'tanggal_mulai',
        'tanggal_selesai',
        'keterangan',
        'status',
        'disetujui_oleh',
        'disetujui_pada',
        'alasan_reject',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'tanggal_mulai' => 'date',
            'tanggal_selesai' => 'date',
            'disetujui_pada' => 'datetime',
        ];
    }

    /**
     * Get the santri that owns the izin.
     */
    public function santri(): BelongsTo
    {
        return $this->belongsTo(Santri::class);
    }

    /**
     * Get the user that approved the izin.
     */
    public function disetujuiOleh(): BelongsTo
    {
        return $this->belongsTo(User::class, 'disetujui_oleh');
    }

    /**
     * Scope for approved izin.
     */
    public function scopeApproved($query)
    {
        return $query->whereNotNull('disetujui_oleh')->whereNull('alasan_reject');
    }

    /**
     * Get active izin for a santri on a specific date.
     */
    public static function getActiveForSantri($santriId, $tanggal)
    {
        return self::where('santri_id', $santriId)
            ->where('tanggal_mulai', '<=', $tanggal)
            ->where('tanggal_selesai', '>=', $tanggal)
            ->approved()
            ->first();
    }
}
