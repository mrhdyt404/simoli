<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Pks extends Model
{
    protected $table = 'pks';
    protected $primaryKey = 'id_pks';
    public $timestamps = false;

    protected $fillable = [
        'kode',
        'nama',
        'manager',
        'asisten',
        'wa_asisten',
        'akro',
    ];

    public function pengaliran()
    {
        return $this->hasMany(Pengaliran::class, 'id_pks', 'id_pks');
    }

    public function pemeliharaan()
    {
        return $this->hasMany(Pemeliharaan::class, 'id_pks', 'id_pks');
    }

    public function rencana()
    {
        return $this->hasMany(Rencana::class, 'id_pks', 'id_pks');
    }

    public function user()
    {
        return $this->hasOne(User::class, 'id_pks', 'id_pks');
    }

    public function alatBerat()
    {
        return $this->hasMany(AlatBerat::class, 'id_pks', 'id_pks');
    }

    public function monitoringAlatBerat()
    {
        return $this->hasMany(MonitoringAlatBerat::class, 'id_pks', 'id_pks');
    }

    public function perizinanLa()
    {
        return $this->hasOne(PerizinanLa::class, 'id_pks', 'id_pks')->latest('tanggal_terbit');
    }

    public function daftarPerizinanLa()
    {
        return $this->hasMany(PerizinanLa::class, 'id_pks', 'id_pks');
    }

    public function petaBlokLa()
    {
        return $this->hasMany(PetaBlokLa::class, 'id_pks', 'id_pks');
    }
}

