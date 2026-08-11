<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AlatBerat extends Model
{
    protected $table = 'alat_berat';

    protected $fillable = [
        'id_pks',
        'kode_alat',
        'nama_alat',
        'jenis_alat',
        'merk_tipe',
        'tahun_pengadaan',
        'status',
        'keterangan',
    ];

    /**
     * Relasi ke PKS
     */
    public function pks()
    {
        return $this->belongsTo(Pks::class, 'id_pks', 'id_pks');
    }

    /**
     * Relasi ke Monitoring Alat Berat
     */
    public function logs()
    {
        return $this->hasMany(MonitoringAlatBerat::class, 'alat_berat_id');
    }
}
