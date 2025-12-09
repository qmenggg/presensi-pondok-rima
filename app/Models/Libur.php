<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Libur extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'tanggal_mulai',
        'tanggal_selesai',
        'keterangan',
        'jenis',
        'untuk_jenis_santri',
        'rutin_mingguan',
        'hari_rutin',
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
            'rutin_mingguan' => 'boolean',
        ];
    }

    /**
     * Check if a date is a holiday.
     * 
     * @param string|Carbon $tanggal
     * @param string $jenisSantri 'putra', 'putri', atau 'semua'
     * @return Libur|null
     */
    public static function isLibur($tanggal, $jenisSantri = 'semua')
    {
        $tanggalCarbon = is_string($tanggal) ? \Carbon\Carbon::parse($tanggal) : $tanggal;
        $hari = strtolower($tanggalCarbon->locale('id')->isoFormat('dddd'));
        
        // Map Indonesian days
        $hariMap = [
            'senin' => 'senin',
            'selasa' => 'selasa',
            'rabu' => 'rabu',
            'kamis' => 'kamis',
            'jumat' => 'jumat',
            'sabtu' => 'sabtu',
            'minggu' => 'minggu',
        ];
        $hari = $hariMap[$hari] ?? $hari;
        
        // Check date range libur
        $libur = self::where('tanggal_mulai', '<=', $tanggalCarbon->format('Y-m-d'))
            ->where('tanggal_selesai', '>=', $tanggalCarbon->format('Y-m-d'))
            ->where('rutin_mingguan', false)
            ->where(function($q) use ($jenisSantri) {
                $q->where('untuk_jenis_santri', $jenisSantri)
                  ->orWhere('untuk_jenis_santri', 'semua');
            })
            ->first();
            
        if ($libur) return $libur;
        
        // Check rutin mingguan
        return self::where('rutin_mingguan', true)
            ->where('hari_rutin', $hari)
            ->where(function($q) use ($jenisSantri) {
                $q->where('untuk_jenis_santri', $jenisSantri)
                  ->orWhere('untuk_jenis_santri', 'semua');
            })
            ->first();
    }
}
