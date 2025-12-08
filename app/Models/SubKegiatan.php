<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SubKegiatan extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'kegiatan_id',
        'nama_sub_kegiatan',
        'keterangan',
        'waktu_mulai',
        'waktu_selesai',
        'untuk_jenis_santri',
        'lokasi',
        'guru_penanggung_jawab',
    ];

    /**
     * Get the kegiatan that owns the sub kegiatan.
     */
    public function kegiatan(): BelongsTo
    {
        return $this->belongsTo(Kegiatan::class);
    }

    /**
     * Get the guru penanggung jawab that owns the sub kegiatan.
     */
    public function guruPenanggungJawab(): BelongsTo
    {
        return $this->belongsTo(User::class, 'guru_penanggung_jawab');
    }

    /**
     * Get the hari for the sub kegiatan.
     */
    public function subKegiatanHaris(): HasMany
    {
        return $this->hasMany(SubKegiatanHari::class);
    }

    /**
     * Get the sub kegiatan kamars for the sub kegiatan.
     */
    public function subKegiatanKamars(): HasMany
    {
        return $this->hasMany(SubKegiatanKamar::class);
    }

    /**
     * Get the sub kegiatan santris for the sub kegiatan.
     */
    public function subKegiatanSantris(): HasMany
    {
        return $this->hasMany(SubKegiatanSantri::class);
    }

    /**
     * Get the presensis for the sub kegiatan.
     */
    public function presensis(): HasMany
    {
        return $this->hasMany(Presensi::class);
    }

    /**
     * Get hari names as array.
     */
    public function getHariArrayAttribute(): array
    {
        return $this->subKegiatanHaris->pluck('hari')->toArray();
    }
}

