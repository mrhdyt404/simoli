<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pemeliharaan extends Model
{
    protected $table = 'pemeliharaan';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id_pks',
        'tanggal',
        'blok',
        'no_bak',
        'flat_bed',
        'long_bed',
        'sebelum',
        'sesudah',
        'jumlah_hk',
        'keterangan',
        'jenis_pemeliharaan',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'flat_bed' => 'double',
        'long_bed' => 'double',
    ];

    /**
     * Jenis Pemeliharaan:
     * 1 = Pemeliharaan Mekanis
     * 2 = Pemeliharaan Manual
     */
    public function getJenisLabelAttribute(): string
    {
        return match ($this->jenis_pemeliharaan) {
            '1' => 'Mekanis',
            '2' => 'Manual',
            default => '-',
        };
    }

    public function pks()
    {
        return $this->belongsTo(Pks::class, 'id_pks', 'id_pks');
    }
}
