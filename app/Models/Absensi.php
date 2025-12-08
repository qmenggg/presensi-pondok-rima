<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Absensi extends Model
{
    use HasFactory;

    protected $table = 'absensis';

    protected $fillable = [
        'sub_kegiatan_id',
        'santri_id',
        'tanggal',
        'status',
        'keterangan',
        'pencatat_id',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    /**
     * Get the sub kegiatan that owns the absensi.
     */
    public function subKegiatan()
    {
        return $this->belongsTo(SubKegiatan::class);
    }

    /**
     * Get the santri that owns the absensi.
     */
    public function santri()
    {
        return $this->belongsTo(Santri::class);
    }

    /**
     * Get the user who recorded the absensi.
     */
    public function pencatat()
    {
        return $this->belongsTo(User::class, 'pencatat_id');
    }

    /**
     * Get status badge color
     */
    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'hadir' => 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400',
            'izin' => 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400',
            'sakit' => 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400',
            'alpha' => 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400',
            default => 'bg-gray-100 text-gray-700',
        };
    }

    /**
     * Get status label
     */
    public function getStatusLabelAttribute()
    {
        return match($this->status) {
            'hadir' => 'Hadir',
            'izin' => 'Izin',
            'sakit' => 'Sakit',
            'alpha' => 'Alpha',
            default => '-',
        };
    }
}
