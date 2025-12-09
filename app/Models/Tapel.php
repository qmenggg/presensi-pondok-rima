<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tapel extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'nama_tapel',
        'tanggal_mulai',
        'tanggal_selesai',
        'aktif',
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
            'aktif' => 'boolean',
        ];
    }

    /**
     * Boot method for model events.
     * Auto-deactivate other tapels when setting one as active.
     */
    protected static function boot()
    {
        parent::boot();

        // When creating or updating a tapel and setting it as active
        static::saving(function ($tapel) {
            if ($tapel->aktif) {
                // Deactivate all other tapels
                static::where('id', '!=', $tapel->id ?? 0)
                    ->where('aktif', true)
                    ->update(['aktif' => false]);
            }
        });
    }

    /**
     * Scope: Get only active tapel.
     */
    public function scopeAktif($query)
    {
        return $query->where('aktif', true);
    }

    /**
     * Static: Get the active tapel.
     */
    public static function getAktif(): ?self
    {
        return static::where('aktif', true)->first();
    }

    /**
     * Set this tapel as active (will auto-deactivate others via boot).
     */
    public function setAsAktif(): void
    {
        $this->aktif = true;
        $this->save();
    }

    /**
     * Get the kegiatans for the tapel.
     */
    public function kegiatans(): HasMany
    {
        return $this->hasMany(Kegiatan::class);
    }
}

