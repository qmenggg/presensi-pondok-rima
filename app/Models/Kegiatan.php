<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Kegiatan extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'nama_kegiatan',
        'keterangan',
        'tanggal_mulai',
        'tanggal_selesai',
        'tapel_id',
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
        ];
    }

    /**
     * Get the tapel that owns the kegiatan.
     */
    public function tapel(): BelongsTo
    {
        return $this->belongsTo(Tapel::class);
    }

    /**
     * Get the sub kegiatans for the kegiatan.
     */
    public function subKegiatans(): HasMany
    {
        return $this->hasMany(SubKegiatan::class);
    }
}
