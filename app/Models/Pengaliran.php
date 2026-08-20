<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pengaliran extends Model
{
    protected $table = 'pengaliran';
    protected $primaryKey = 'id_pengaliran';

    protected $fillable = [
        'tanggal',
        'jam_mulai',
        'jam_selesai',
        'no_bak',
        'blok',
        'flat_bed',
        'vol_limbah_dihasilkan',
        'vol_limbah_dialirkan',
        'luas_area',
        'rotasi',
        'keterangan',
        'id_pks',
        'tags',
        'foto',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'flat_bed' => 'integer',
        'vol_limbah_dihasilkan' => 'integer',
        'vol_limbah_dialirkan' => 'integer',
        'luas_area' => 'float',
    ];

    public function pks()
    {
        return $this->belongsTo(Pks::class, 'id_pks', 'id_pks');
    }
}
