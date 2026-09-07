<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ArsipPetaLa extends Model
{
    protected $table = 'arsip_peta_la';

    protected $fillable = [
        'id_pks',
        'nama_peta',
        'kategori_peta',
        'tahun_peta',
        'file_peta',
        'tipe_file',
        'ukuran_file',
        'keterangan',
    ];

    public function pks()
    {
        return $this->belongsTo(Pks::class, 'id_pks', 'id_pks');
    }

    public function getFormattedSizeAttribute(): string
    {
        $bytes = $this->ukuran_file ?? 0;
        if ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 1) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 0) . ' KB';
        }
        return $bytes . ' B';
    }

    public function getIsPdfAttribute(): bool
    {
        return strtolower($this->tipe_file ?? '') === 'pdf' || str_ends_with(strtolower($this->file_peta ?? ''), '.pdf');
    }

    public function getIsImageAttribute(): bool
    {
        $ext = strtolower(pathinfo($this->file_peta ?? '', PATHINFO_EXTENSION));
        return in_array($ext, ['jpg', 'jpeg', 'png', 'webp', 'svg']);
    }
}
