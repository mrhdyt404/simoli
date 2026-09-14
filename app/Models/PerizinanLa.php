<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class PerizinanLa extends Model
{
    protected $table = 'perizinan_la';

    protected $fillable = [
        'id_pks',
        'nomor_sk',
        'tentang',
        'instansi_penerbit',
        'tanggal_terbit',
        'tanggal_berakhir',
        'masa_berlaku_tahun',
        'bod_maksimal',
        'ph_min',
        'ph_max',
        'debit_maksimal_harian',
        'luas_areal_izin',
        'saluran_distribusi',
        'nama_titik_penaatan',
        'lat_titik_penaatan',
        'long_titik_penaatan',
        'koordinat_penaatan_text',
        'file_sk',
        'file_peta',
        'status_izin',
        'keterangan',
        'is_locked',
    ];

    protected $casts = [
        'tanggal_terbit' => 'date',
        'tanggal_berakhir' => 'date',
        'bod_maksimal' => 'integer',
        'ph_min' => 'float',
        'ph_max' => 'float',
        'debit_maksimal_harian' => 'float',
        'luas_areal_izin' => 'float',
        'lat_titik_penaatan' => 'float',
        'long_titik_penaatan' => 'float',
        'is_locked' => 'boolean',
    ];

    public function pks()
    {
        return $this->belongsTo(Pks::class, 'id_pks', 'id_pks');
    }

    public function sumurPantau()
    {
        return $this->hasMany(PerizinanSumurPantau::class, 'perizinan_la_id');
    }

    /**
     * Hitung sisa hari masa berlaku SK
     */
    public function getSisaHariAttribute(): ?int
    {
        if (!$this->tanggal_berakhir) {
            return null;
        }

        return (int) Carbon::now()->diffInDays($this->tanggal_berakhir, false);
    }

    /**
     * Status izin terhitung otomatis
     */
    public function getStatusLabelAttribute(): string
    {
        if (!$this->tanggal_berakhir) {
            return $this->status_izin ?? 'Aktif';
        }

        $sisa = $this->sisa_hari;

        if ($sisa < 0) {
            return 'Kedaluwarsa';
        } elseif ($sisa <= 60) {
            return 'Segera Perpanjang (<60 Hari)';
        } elseif ($sisa <= 180) {
            return 'Perlu Perhatian (<6 Bulan)';
        }

        return 'Aktif';
    }

    public function getBadgeClassAttribute(): string
    {
        $status = $this->status_label;

        if (str_contains($status, 'Kedaluwarsa')) {
            return 'danger';
        } elseif (str_contains($status, 'Segera') || str_contains($status, 'Perhatian')) {
            return 'warning';
        }

        return 'success';
    }
}
