<?php

namespace Database\Seeders;

use App\Models\AlatBerat;
use App\Models\MonitoringAlatBerat;
use App\Models\Pks;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class AlatBeratSeeder extends Seeder
{
    public function run(): void
    {
        $pksTerantam = Pks::where('akro', 'TER')->first() ?? Pks::first();
        $pksTandun = Pks::where('akro', 'TAN')->first() ?? Pks::first();

        if (!$pksTerantam) return;

        // Sample Master Alat Berat
        $ab1 = AlatBerat::firstOrCreate(
            ['id_pks' => $pksTerantam->id_pks, 'kode_alat' => 'EX-01'],
            [
                'nama_alat' => 'Excavator Komatsu PC200 Pengolahan Sludge',
                'jenis_alat' => 'Excavator',
                'merk_tipe' => 'Komatsu PC200-8',
                'tahun_pengadaan' => 2022,
                'status' => 'Operational',
                'keterangan' => 'Unit utama pengerukan kolam anaerob dan pembersihan sedimentasi sludge.',
            ]
        );

        $ab2 = AlatBerat::firstOrCreate(
            ['id_pks' => $pksTerantam->id_pks, 'kode_alat' => 'WL-01'],
            [
                'nama_alat' => 'Wheel Loader CAT 924K Land Application',
                'jenis_alat' => 'Wheel Loader',
                'merk_tipe' => 'Caterpillar 924K',
                'tahun_pengadaan' => 2021,
                'status' => 'Operational',
                'keterangan' => 'Unit loading solid tankos & pendistribusian limbah ke lahan perkebunan.',
            ]
        );

        $ab3 = AlatBerat::firstOrCreate(
            ['id_pks' => $pksTerantam->id_pks, 'kode_alat' => 'DT-05'],
            [
                'nama_alat' => 'Dump Truck Hino 500 Transport Sludge',
                'jenis_alat' => 'Dump Truck',
                'merk_tipe' => 'Hino FM 260 TI',
                'tahun_pengadaan' => 2020,
                'status' => 'Maintenance',
                'keterangan' => 'Perbaikan sistem hidrolik dump bak.',
            ]
        );

        $ab4 = AlatBerat::firstOrCreate(
            ['id_pks' => $pksTerantam->id_pks, 'kode_alat' => 'EX-02'],
            [
                'nama_alat' => 'Excavator Sany SY215C Pengerukan Kolam 4',
                'jenis_alat' => 'Excavator',
                'merk_tipe' => 'Sany SY215C',
                'tahun_pengadaan' => 2024,
                'status' => 'Operational',
                'keterangan' => 'Unit tambahan pengerukan dan perawatan tanggul kolam anaerob.',
            ]
        );

        if ($pksTandun) {
            AlatBerat::firstOrCreate(
                ['id_pks' => $pksTandun->id_pks, 'kode_alat' => 'BL-01'],
                [
                    'nama_alat' => 'Bulldozer Shantui SD16 Perbaikan Tanggul',
                    'jenis_alat' => 'Bulldozer',
                    'merk_tipe' => 'Shantui SD16',
                    'tahun_pengadaan' => 2023,
                    'status' => 'Operational',
                    'keterangan' => 'Perataan pematang kolam limbah.',
                ]
            );
        }

        // Sample Monitoring Logs
        $today = Carbon::now();

        MonitoringAlatBerat::create([
            'id_pks' => $pksTerantam->id_pks,
            'alat_berat_id' => $ab1->id,
            'tanggal' => $today->copy()->subDays(2)->format('Y-m-d'),
            'operator' => 'Budi Santoso',
            'kegiatan' => 'Pembersihan & Pengerukan Kolam Limbah',
            'lokasi_blok' => 'Kolam 2 Anaerob',
            'flat_bed' => 2,
            'long_bed' => 1,
            'jumlah_bed' => 3,
            'hm_awal' => 8.0,   // 08:00
            'hm_akhir' => 15.5, // 15:30
            'total_hm' => 7.5,  // 07:30
            'bbm_liter' => 85.0,
            'kondisi_alat' => 'Normal',
            'catatan' => 'Pengerukan sedimentasi lumpur kolam berjalan lancar.',
        ]);

        MonitoringAlatBerat::create([
            'id_pks' => $pksTerantam->id_pks,
            'alat_berat_id' => $ab2->id,
            'tanggal' => $today->copy()->subDays(1)->format('Y-m-d'),
            'operator' => 'Joko Rahmat',
            'kegiatan' => 'Aplikasi Lahan (Land Application)',
            'lokasi_blok' => 'Blok C18 / Flat Bed 19',
            'flat_bed' => 3,
            'long_bed' => 0,
            'jumlah_bed' => 3,
            'hm_awal' => 8.0,   // 08:00
            'hm_akhir' => 14.5, // 14:30
            'total_hm' => 6.5,  // 06:30
            'bbm_liter' => 70.0,
            'kondisi_alat' => 'Normal',
            'catatan' => 'Aplikasi limbah ke flat bed blok C18.',
        ]);

        MonitoringAlatBerat::create([
            'id_pks' => $pksTerantam->id_pks,
            'alat_berat_id' => $ab1->id,
            'tanggal' => $today->format('Y-m-d'),
            'operator' => 'Budi Santoso',
            'kegiatan' => 'Pengadukan Kolam Limbah / Anaerob',
            'lokasi_blok' => 'Kolam 3 Anaerob',
            'flat_bed' => 1,
            'long_bed' => 2,
            'jumlah_bed' => 3,
            'hm_awal' => 8.0,   // 08:00
            'hm_akhir' => 14.0, // 14:00
            'total_hm' => 6.0,  // 06:00
            'bbm_liter' => 75.0,
            'kondisi_alat' => 'Normal',
            'catatan' => 'Pengadukan sirkulasi kolam anaerob.',
        ]);
    }
}
