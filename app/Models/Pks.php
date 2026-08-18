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
}
