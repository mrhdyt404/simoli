<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Pks;
use App\Models\AlatBerat;
use App\Models\MonitoringAlatBerat;
use Illuminate\Support\Str;
use Carbon\Carbon;

echo "=== SEEDING DUMMY MONITORING ALAT BERAT UNTUK SEMUA PKS ===" . PHP_EOL;

$pksList = Pks::all();
if ($pksList->isEmpty()) {
    echo "ERROR: Tidak ada data PKS di database!" . PHP_EOL;
    exit(1);
}

echo "Total PKS ditemukan: " . $pksList->count() . PHP_EOL;

// 1. Pastikan setiap PKS memiliki minimal 2 unit Alat Berat
$jenisAlat = ['Excavator', 'Wheel Loader', 'Bulldozer', 'Dump Truck', 'Compactor'];
$merkList  = ['Komatsu PC200-8', 'Caterpillar 320D', 'Hitachi ZAXIS 200', 'Kobelco SK200', 'XCMG LW300FN'];

foreach ($pksList as $pks) {
    $existingAb = AlatBerat::where('id_pks', $pks->id_pks)->get();
    if ($existingAb->count() < 2) {
        $needed = 2 - $existingAb->count();
        for ($i = 1; $i <= $needed; $i++) {
            $num = $existingAb->count() + $i;
            $jenis = $jenisAlat[array_rand($jenisAlat)];
            $merk  = $merkList[array_rand($merkList)];
            
            AlatBerat::create([
                'id_pks' => $pks->id_pks,
                'kode_alat' => 'EX-' . $pks->akro . '-' . sprintf('%02d', $num),
                'nama_alat' => $jenis . ' ' . $pks->akro . ' ' . sprintf('%02d', $num),
                'jenis_alat' => $jenis,
                'merk_tipe' => $merk,
                'tahun_pengadaan' => rand(2018, 2024),
                'status' => 'Operational',
                'keterangan' => 'Unit siap pakai pengolahan limbah PKS ' . $pks->nama
            ]);
            echo "  [+] Dibuat Alat Berat baru untuk PKS {$pks->akro}: EX-{$pks->akro}-" . sprintf('%02d', $num) . PHP_EOL;
        }
    }
}

// Reload all Alat Berat per PKS
$pksAlatMap = [];
foreach ($pksList as $pks) {
    $pksAlatMap[$pks->id_pks] = AlatBerat::where('id_pks', $pks->id_pks)->get();
}

// 2. Daftar Operator & Kegiatan
$operatorList = [
    'Budi Santoso', 'Ahmad Subagyo', 'Hendra Wijaya', 'Rudi Hartono', 
    'Eko Prasetyo', 'Slamet Widodo', 'Dedi Kurniawan', 'Rian Hidayat',
    'Agus Setiawan', 'Bambang Tri', 'Yudi Permana', 'Fajar Ramadhan'
];

$kegiatanList = [
    'Pembersihan & Pengerukan Kolam Limbah',
    'Aplikasi Lahan (Land Application)',
    'Pengadukan Kolam Limbah / Anaerob',
    'Transportasi & Loading Sludge / Solid',
    'Perbaikan Pematang / Tanggul Kolam',
    'Pemeliharaan Routine Alat Berat',
    'Cuci Flatbed Bedengan IPAL',
    'Meratakan Tanah Timbun di Bedengan'
];

$lokasiList = [
    'Afd 5 Blok K 38 BD 11',
    'AFD 1 Blok K 3',
    'Afd 1 Blok 8D',
    'Kolam 2 Anaerob IPAL',
    'Kolam 4 Fakultas Sludge',
    'Blok C18 Aplikasi Lahan',
    'PKS IPAL Bedengan 2',
    'Afd 3 Blok H 1 BD 4'
];

// Base GPS Coordinates per PKS area (Riau region ~0.4°N to 1.5°N, 100.5°E to 101.8°E)
$baseCoords = [
    1  => ['lat' => 0.5382, 'long' => 101.4483], // TPU
    2  => ['lat' => 1.3412, 'long' => 100.7512], // TME
    3  => ['lat' => 0.6512, 'long' => 101.1245], // SGO
    4  => ['lat' => 0.3541, 'long' => 101.3812], // SPA
    5  => ['lat' => 0.7123, 'long' => 101.8512], // SBT
    6  => ['lat' => 0.6812, 'long' => 101.9123], // LDA
    7  => ['lat' => 0.5123, 'long' => 101.2514], // SGH
    8  => ['lat' => 0.7812, 'long' => 100.5512], // TAN
    9  => ['lat' => 0.4214, 'long' => 101.1124], // TER
    10 => ['lat' => 0.8123, 'long' => 100.6124], // STA
    11 => ['lat' => 0.9512, 'long' => 100.4124], // SRO
    12 => ['lat' => 0.8812, 'long' => 100.4812], // SIN
    13 => ['lat' => 0.5214, 'long' => 101.4124], // TEP
    14 => ['lat' => 0.9124, 'long' => 100.8124], // DTM
    15 => ['lat' => 0.7512, 'long' => 100.3512], // DBR
];

$months = ['07', '08']; // Juli & Agustus 2026
$year = 2026;
$insertedTotal = 0;

foreach ($months as $bulan) {
    $daysInMonth = ($bulan === '07') ? 31 : 21; // Full July, August up to 21
    
    echo "Processing Month {$bulan}-{$year} ({$daysInMonth} days)..." . PHP_EOL;

    for ($day = 1; $day <= $daysInMonth; $day++) {
        $dateStr = sprintf('%04d-%02d-%02d', $year, $bulan, $day);

        // For each PKS, generate 1 to 2 activity logs on this day
        foreach ($pksList as $pks) {
            $alatUnits = $pksAlatMap[$pks->id_pks] ?? collect();
            if ($alatUnits->isEmpty()) continue;

            // Pick 1 or 2 units to operate on this day
            $unitsToUse = $alatUnits->random(min(rand(1, 2), $alatUnits->count()));

            foreach ($unitsToUse as $abUnit) {
                // Check if record already exists for this unit on this day
                $exists = MonitoringAlatBerat::where('id_pks', $pks->id_pks)
                    ->where('alat_berat_id', $abUnit->id)
                    ->whereDate('tanggal', $dateStr)
                    ->exists();

                if ($exists) {
                    continue;
                }

                $kegiatan = $kegiatanList[array_rand($kegiatanList)];
                $lokasi   = $lokasiList[array_rand($lokasiList)];
                $operator = $operatorList[array_rand($operatorList)];

                // Condition logic (90% Normal, 7% Perlu Perbaikan, 3% Breakdown)
                $randCond = rand(1, 100);
                if ($randCond <= 90) {
                    $kondisi = 'Normal';
                } elseif ($randCond <= 97) {
                    $kondisi = 'Perlu Perbaikan';
                } else {
                    $kondisi = 'Breakdown';
                }

                if ($kondisi === 'Breakdown') {
                    $kegiatan = 'OFF';
                    $lokasi   = 'OFF';
                    $flatBed  = 0;
                    $longBed  = 0;
                    $totalHm  = 0;
                    $bbmLiter = 0;
                    $hmAwal   = null;
                    $hmAkhir  = null;
                } else {
                    $flatBed  = (rand(1, 10) > 3) ? rand(20, 80) : 0;
                    $longBed  = ($flatBed === 0 || rand(1, 10) > 5) ? rand(15, 45) : 0;
                    $totalHm  = round(rand(55, 85) / 10, 2); // 5.5 - 8.5 hours
                    $bbmLiter = rand(45, 110);
                    $hmAwal   = "{$dateStr} 08:00:00";
                    $hmAkhir  = "{$dateStr} " . sprintf('%02d:%02d:00', 14 + (int)floor($totalHm), rand(0, 59));
                }

                $base = $baseCoords[$pks->id_pks] ?? ['lat' => 0.5, 'long' => 101.4];
                $latAwal  = round($base['lat'] + (rand(-50, 50) / 10000), 6);
                $longAwal = round($base['long'] + (rand(-50, 50) / 10000), 6);
                $latAkhir  = round($latAwal + (rand(-20, 20) / 10000), 6);
                $longAkhir = round($longAwal + (rand(-20, 20) / 10000), 6);

                MonitoringAlatBerat::create([
                    'uuid' => (string) Str::uuid(),
                    'id_pks' => $pks->id_pks,
                    'alat_berat_id' => $abUnit->id,
                    'tanggal' => $dateStr,
                    'operator' => $operator,
                    'kegiatan' => $kegiatan,
                    'lokasi_blok' => $lokasi,
                    'flat_bed' => $flatBed,
                    'long_bed' => $longBed,
                    'jumlah_bed' => $flatBed + $longBed,
                    'latitude_awal' => $latAwal,
                    'longitude_awal' => $longAwal,
                    'latitude_akhir' => $latAkhir,
                    'longitude_akhir' => $longAkhir,
                    'latitude' => $latAwal,
                    'longitude' => $longAwal,
                    'hm_awal' => $hmAwal,
                    'hm_akhir' => $hmAkhir,
                    'total_hm' => $totalHm,
                    'bbm_liter' => $bbmLiter,
                    'kondisi_alat' => $kondisi,
                    'catatan' => $kondisi === 'Normal' ? 'Operasional lancar tanpa kendala' : ($kondisi === 'Perlu Perbaikan' ? 'Perlu penggantian oli hidrolik' : 'Perbaikan rutin engine breakdown'),
                ]);

                $insertedTotal++;
            }
        }
    }
}

echo "=== SEEDING SELESAI ===" . PHP_EOL;
echo "Total data dummy monitoring alat berat baru yang berhasil dibuat: {$insertedTotal} record." . PHP_EOL;
