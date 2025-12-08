<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SubKegiatanHari extends Model
{
    use HasFactory;

    protected $fillable = [
        'sub_kegiatan_id',
        'hari',
    ];

    /**
     * Get the sub kegiatan that owns this hari.
     */
    public function subKegiatan(): BelongsTo
    {
        return $this->belongsTo(SubKegiatan::class);
    }
}
