<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PerizinanSumurPantau extends Model
{
    protected $table = 'perizinan_sumur_pantau';

    protected $fillable = [
        'perizinan_la_id',
        'nama_sumur',
        'jenis_sumur',
        'lokasi_blok',
        'latitude',
        'longitude',
        'koordinat_text',
        'frekuensi_pantau',
        'parameter_pantau',
    ];

    protected $casts = [
        'latitude' => 'float',
        'longitude' => 'float',
    ];

    public function perizinanLa()
    {
        return $this->belongsTo(PerizinanLa::class, 'perizinan_la_id');
    }
}
