<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PetaBlokLa extends Model
{
    protected $table = 'peta_blok_la';

    protected $fillable = [
        'id_pks',
        'afdeling',
        'nama_blok',
        'luas_ha',
        'no_bak_awal',
        'no_bak_akhir',
        'jumlah_bak',
        'jumlah_flat_bed',
        'panjang_parit_meter',
        'latitude_center',
        'longitude_center',
        'polygon_geojson',
        'status_aktif',
    ];

    protected $casts = [
        'luas_ha' => 'float',
        'jumlah_bak' => 'integer',
        'jumlah_flat_bed' => 'integer',
        'panjang_parit_meter' => 'float',
        'latitude_center' => 'float',
        'longitude_center' => 'float',
        'status_aktif' => 'boolean',
    ];

    public function pks()
    {
        return $this->belongsTo(Pks::class, 'id_pks', 'id_pks');
    }
}
