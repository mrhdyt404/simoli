<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MonitoringAlatBerat extends Model
{
    protected $table = 'monitoring_alat_berat';

    protected $fillable = [
        'id_pks',
        'alat_berat_id',
        'tanggal',
        'operator',
        'kegiatan',
        'lokasi_blok',
        'flat_bed',
        'long_bed',
        'jumlah_bed',
        'latitude',
        'longitude',
        'latitude_awal',
        'longitude_awal',
        'latitude_akhir',
        'longitude_akhir',
        'hm_awal',
        'hm_akhir',
        'total_hm',
        'bbm_liter',
        'kondisi_alat',
        'foto',
        'foto_sebelum',
        'foto_sesudah',
        'catatan',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'flat_bed' => 'integer',
        'long_bed' => 'integer',
        'jumlah_bed' => 'integer',
        'latitude' => 'float',
        'longitude' => 'float',
        'latitude_awal' => 'float',
        'longitude_awal' => 'float',
        'latitude_akhir' => 'float',
        'longitude_akhir' => 'float',
        'hm_awal' => 'datetime',
        'hm_akhir' => 'datetime',
        'total_hm' => 'float',
        'bbm_liter' => 'float',
    ];

    /**
     * Accessor untuk total jumlah bed (flat_bed + long_bed)
     */
    public function getJumlahBedAttribute($value)
    {
        $sum = ($this->flat_bed ?? 0) + ($this->long_bed ?? 0);
        if ($sum === 0 && !is_null($value) && (int)$value > 0) {
            return (int)$value;
        }
        return $sum;
    }

    /**
     * Link ke Google Maps berdasarkan koordinat
     */
    public function getGoogleMapsUrlAttribute()
    {
        $lat = $this->latitude_awal ?? $this->latitude;
        $long = $this->longitude_awal ?? $this->longitude;
        if ($lat && $long) {
            return "https://www.google.com/maps?q={$lat},{$long}";
        }
        return null;
    }

    public function getGoogleMapsUrlAwalAttribute()
    {
        $lat = $this->latitude_awal ?? $this->latitude;
        $long = $this->longitude_awal ?? $this->longitude;
        if ($lat && $long) {
            return "https://www.google.com/maps?q={$lat},{$long}";
        }
        return null;
    }

    public function getGoogleMapsUrlAkhirAttribute()
    {
        $lat = $this->latitude_akhir ?? $this->latitude;
        $long = $this->longitude_akhir ?? $this->longitude;
        if ($lat && $long) {
            return "https://www.google.com/maps?q={$lat},{$long}";
        }
        return null;
    }

    /**
     * Relasi ke PKS
     */
    public function pks()
    {
        return $this->belongsTo(Pks::class, 'id_pks', 'id_pks');
    }

    /**
     * Relasi ke Master Alat Berat
     */
    public function alatBerat()
    {
        return $this->belongsTo(AlatBerat::class, 'alat_berat_id');
    }

    /**
     * Konversi jam desimal ke format waktu 2 digit jam : 2 digit menit (HH:MM)
     * Contoh: 8.5 -> "08:30"
     */
    public static function formatHm($decimalHours, $showUnit = false)
    {
        if (is_null($decimalHours) || $decimalHours === '') {
            return '-';
        }
        $totalMins = (int) round(((float) $decimalHours) * 60);
        $hours = (int) floor($totalMins / 60);
        $mins = $totalMins % 60;

        $formatted = sprintf('%02d:%02d', $hours, $mins);
        return $showUnit ? $formatted . ' Jam' : $formatted;
    }

    /**
     * Parser input jam/timestamp menjadi format timestamp 'Y-m-d H:i:s'
     */
    public static function parseTimestamp($input, $defaultDate = null)
    {
        if (empty($input)) {
            return null;
        }

        try {
            $str = trim((string) $input);

            // Format datetime-local (misal 2026-08-07T08:30 atau 2026-08-07 08:30:00)
            if (strpos($str, 'T') !== false || strpos($str, '-') !== false) {
                return \Carbon\Carbon::parse($str, 'Asia/Jakarta')->format('Y-m-d H:i:s');
            }

            // Jika input jam menggunakan titik seperti 08.30 -> ubah ke 08:30
            if (strpos($str, '.') !== false && strpos($str, ':') === false) {
                $parts = explode('.', $str);
                if (count($parts) == 2 && is_numeric($parts[0]) && is_numeric($parts[1])) {
                    $h = sprintf('%02d', (int)$parts[0]);
                    $m = sprintf('%02d', (int)substr(str_pad($parts[1], 2, '0'), 0, 2));
                    $str = "{$h}:{$m}";
                }
            }

            // Format jam sederhana HH:MM (misal 08:30)
            if (strpos($str, ':') !== false) {
                $baseDate = $defaultDate ? \Carbon\Carbon::parse($defaultDate, 'Asia/Jakarta')->format('Y-m-d') : \Carbon\Carbon::now('Asia/Jakarta')->format('Y-m-d');
                return \Carbon\Carbon::parse("{$baseDate} {$str}", 'Asia/Jakarta')->format('Y-m-d H:i:s');
            }

            return \Carbon\Carbon::parse($str, 'Asia/Jakarta')->format('Y-m-d H:i:s');
        } catch (\Exception $e) {
            return null;
        }
    }

    public function getHmAwalFormattedAttribute()
    {
        if (!$this->hm_awal) return '-';
        return $this->hm_awal instanceof \Carbon\Carbon ? $this->hm_awal->setTimezone('Asia/Jakarta')->format('H:i') : \Carbon\Carbon::parse($this->hm_awal, 'Asia/Jakarta')->format('H:i');
    }

    public function getHmAkhirFormattedAttribute()
    {
        if (!$this->hm_akhir) return '-';
        return $this->hm_akhir instanceof \Carbon\Carbon ? $this->hm_akhir->setTimezone('Asia/Jakarta')->format('H:i') : \Carbon\Carbon::parse($this->hm_akhir, 'Asia/Jakarta')->format('H:i');
    }

    public function getTotalHmFormattedAttribute()
    {
        $hm = $this->total_hm;
        if ((is_null($hm) || (float)$hm === 0.0) && $this->hm_awal && $this->hm_akhir) {
            $cStart = \Carbon\Carbon::parse($this->hm_awal);
            $cEnd = \Carbon\Carbon::parse($this->hm_akhir);
            if ($cEnd->lessThan($cStart)) {
                $cEnd->addDay();
            }
            $diffMins = abs($cStart->diffInMinutes($cEnd));
            $hm = round($diffMins / 60, 2);
        }
        return self::formatHm($hm);
    }

    /**
     * Photo URL accessors that check both gallery/ and uploads/monitoring_alat_berat/
     */
    public function getFotoSebelumUrlAttribute()
    {
        if (!$this->foto_sebelum) return null;
        if (file_exists(public_path('gallery/' . $this->foto_sebelum))) {
            return asset('gallery/' . $this->foto_sebelum);
        }
        if (file_exists(public_path('uploads/monitoring_alat_berat/' . $this->foto_sebelum))) {
            return asset('uploads/monitoring_alat_berat/' . $this->foto_sebelum);
        }
        return asset('gallery/' . $this->foto_sebelum);
    }

    public function getFotoSesudahUrlAttribute()
    {
        if (!$this->foto_sesudah) return null;
        if (file_exists(public_path('gallery/' . $this->foto_sesudah))) {
            return asset('gallery/' . $this->foto_sesudah);
        }
        if (file_exists(public_path('uploads/monitoring_alat_berat/' . $this->foto_sesudah))) {
            return asset('uploads/monitoring_alat_berat/' . $this->foto_sesudah);
        }
        return asset('gallery/' . $this->foto_sesudah);
    }

    public function getFotoUrlAttribute()
    {
        $file = $this->foto ?? $this->foto_sebelum ?? $this->foto_sesudah;
        if (!$file) return null;
        if (file_exists(public_path('gallery/' . $file))) {
            return asset('gallery/' . $file);
        }
        if (file_exists(public_path('uploads/monitoring_alat_berat/' . $file))) {
            return asset('uploads/monitoring_alat_berat/' . $file);
        }
        return asset('gallery/' . $file);
    }

    /**
     * Check if work shift report is completed.
     * Report is completed only when all mandatory shift fields are populated.
     */
    public function isCompleted(): bool
    {
        return !empty($this->alat_berat_id)
            && !empty($this->tanggal)
            && !empty($this->operator)
            && !empty($this->kegiatan)
            && !empty($this->lokasi_blok)
            && !empty($this->hm_awal)
            && !empty($this->hm_akhir)
            && !is_null($this->bbm_liter)
            && !empty($this->kondisi_alat)
            && !empty($this->foto_sebelum)
            && !empty($this->foto_sesudah);
    }
}
