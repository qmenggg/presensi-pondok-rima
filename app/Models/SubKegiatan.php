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
        'hari',
        'waktu_mulai',
        'waktu_selesai',
        'untuk_jenis_santri',
        'lokasi',
        'wajib_hadir',
        'rutin_mingguan',
        'guru_penanggung_jawab',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'wajib_hadir' => 'boolean',
            'rutin_mingguan' => 'boolean',
        ];
    }

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
}
