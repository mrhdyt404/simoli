<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rencana extends Model
{
    protected $table = 'rencana';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'id_pks',
        'nama',
        'flat_bed',
        'long_bed',
        'tahun',
    ];

    protected $casts = [
        'flat_bed' => 'double',
        'long_bed' => 'double',
        'tahun' => 'integer',
    ];

    public function pks()
    {
        return $this->belongsTo(Pks::class, 'id_pks', 'id_pks');
    }
}
