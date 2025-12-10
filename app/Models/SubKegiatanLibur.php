<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SubKegiatanLibur extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'sub_kegiatan_id',
        'tanggal',
        'keterangan',
        'created_by',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'tanggal' => 'date',
        ];
    }

    /**
     * Get the sub kegiatan that owns this libur.
     */
    public function subKegiatan(): BelongsTo
    {
        return $this->belongsTo(SubKegiatan::class);
    }

    /**
     * Get the user who created this libur.
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Check if a sub kegiatan is on holiday for a specific date.
     * 
     * @param int $subKegiatanId
     * @param string|\Carbon\Carbon $tanggal
     * @return SubKegiatanLibur|null
     */
    public static function isLibur($subKegiatanId, $tanggal)
    {
        $tanggalStr = is_string($tanggal) ? $tanggal : $tanggal->format('Y-m-d');
        
        return self::where('sub_kegiatan_id', $subKegiatanId)
            ->where('tanggal', $tanggalStr)
            ->first();
    }
}
