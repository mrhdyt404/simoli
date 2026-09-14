<?php

namespace Database\Seeders;

use App\Models\Rencana;
use Illuminate\Database\Seeder;

class DummyRencanaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Baseline capacity per operational PKS
        $pksTargets = [
            1  => ['akro' => 'TPU', 'flat_bed' => 12000, 'long_bed' => 3112],
            2  => ['akro' => 'TME', 'flat_bed' => 5400,  'long_bed' => 1368],
            3  => ['akro' => 'SGO', 'flat_bed' => 2700,  'long_bed' => 682],
            4  => ['akro' => 'SPA', 'flat_bed' => 6000,  'long_bed' => 1490],
            5  => ['akro' => 'SBT', 'flat_bed' => 12500, 'long_bed' => 2870],
            6  => ['akro' => 'LDA', 'flat_bed' => 5500,  'long_bed' => 1485],
            7  => ['akro' => 'SGH', 'flat_bed' => 2800,  'long_bed' => 660],
            8  => ['akro' => 'TAN', 'flat_bed' => 8200,  'long_bed' => 2144],
            9  => ['akro' => 'TER', 'flat_bed' => 4500,  'long_bed' => 1191],
            10 => ['akro' => 'STA', 'flat_bed' => 6700,  'long_bed' => 1670],
            11 => ['akro' => 'SRO', 'flat_bed' => 5000,  'long_bed' => 1297],
            12 => ['akro' => 'SIN', 'flat_bed' => 3700,  'long_bed' => 957],
        ];

        // Seed for 2025, 2026, 2027
        $years = [
            2025 => 0.95, // Target tahun 2025 (historis)
            2026 => 1.00, // Target tahun 2026 (tahun aktif)
            2027 => 1.05, // Target tahun 2027 (proyeksi)
        ];

        foreach ($years as $year => $multiplier) {
            foreach ($pksTargets as $idPks => $target) {
                $flatBed = (int) round($target['flat_bed'] * $multiplier);
                $longBed = (int) round($target['long_bed'] * $multiplier);

                Rencana::updateOrCreate(
                    [
                        'id_pks' => $idPks,
                        'tahun' => $year,
                    ],
                    [
                        'flat_bed' => $flatBed,
                        'long_bed' => $longBed,
                    ]
                );
            }
        }
    }
}
