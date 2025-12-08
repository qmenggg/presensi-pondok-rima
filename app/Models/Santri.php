<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Santri extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'kamar_id',
        'tempat_lahir',
        'tanggal_lahir',
        'alamat',
        'nama_wali',
        'foto',
        'qr_code',
        'qr_code_file',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'tanggal_lahir' => 'date',
        ];
    }

    /**
     * Get the user that owns the santri.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the kamar that owns the santri.
     */
    public function kamar(): BelongsTo
    {
        return $this->belongsTo(Kamar::class);
    }

    /**
     * Get the presensis for the santri.
     */
    public function presensis(): HasMany
    {
        return $this->hasMany(Presensi::class);
    }

    /**
     * Get the izins for the santri.
     */
    public function izins(): HasMany
    {
        return $this->hasMany(Izin::class);
    }

    /**
     * Get the sub kegiatan santris for the santri.
     */
    public function subKegiatanSantris(): HasMany
    {
        return $this->hasMany(SubKegiatanSantri::class);
    }
}
