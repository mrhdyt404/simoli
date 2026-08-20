<?php

namespace Database\Seeders;

use App\Models\Pengaliran;
use App\Models\Pks;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DummyPengaliranSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 12 Target PKS Units with realistic characteristic blocks and bak numbers
        $pksConfigs = [
            1 => [ // TPU - TANAH PUTIH
                'akro' => 'TPU',
                'blocks' => ['F4', 'F2', 'F6', 'F8', 'E2'],
                'baks' => ['11', '12', '11, 12', '10'],
                'daily_bed_range' => [35, 65],
                'vol_rate' => [650, 950],
                'luas_rate' => [45, 75],
            ],
            2 => [ // TME - TANJUNG MEDAN
                'akro' => 'TME',
                'blocks' => ['G5', 'G3', 'G1', 'G7', 'H2'],
                'baks' => ['8', '9', '8, 9', '7'],
                'daily_bed_range' => [30, 55],
                'vol_rate' => [550, 850],
                'luas_rate' => [35, 60],
            ],
            3 => [ // SGO - SEI GARO
                'akro' => 'SGO',
                'blocks' => ['H2', 'H4', 'H6', 'H1'],
                'baks' => ['3', '4', '3, 4', '2'],
                'daily_bed_range' => [25, 45],
                'vol_rate' => [450, 700],
                'luas_rate' => [25, 45],
            ],
            4 => [ // SPA - SEI PAGAR
                'akro' => 'SPA',
                'blocks' => ['E1', 'E3', 'E5', 'E7', 'F1'],
                'baks' => ['15', '16', '15, 16', '14'],
                'daily_bed_range' => [40, 70],
                'vol_rate' => [700, 1050],
                'luas_rate' => [50, 80],
            ],
            5 => [ // SBT - SEI BUATAN
                'akro' => 'SBT',
                'blocks' => ['K2', 'K5', 'K7', 'K9', 'L1'],
                'baks' => ['21', '22', '21, 22', '20'],
                'daily_bed_range' => [45, 80],
                'vol_rate' => [800, 1200],
                'luas_rate' => [60, 95],
            ],
            6 => [ // LDA - LUBUK DALAM
                'akro' => 'LDA',
                'blocks' => ['L3', 'L6', 'L8', 'L10', 'M2'],
                'baks' => ['10', '11', '10, 11', '12'],
                'daily_bed_range' => [35, 60],
                'vol_rate' => [600, 900],
                'luas_rate' => [40, 70],
            ],
            7 => [ // SGH - SEI GALUH
                'akro' => 'SGH',
                'blocks' => ['D4', 'D6', 'D2', 'D8', 'C3'],
                'baks' => ['7', '8', '7, 8', '6'],
                'daily_bed_range' => [25, 50],
                'vol_rate' => [500, 750],
                'luas_rate' => [30, 50],
            ],
            8 => [ // TAN - TANDUN
                'akro' => 'TAN',
                'blocks' => ['M4', 'M8', 'M2', 'M10', 'N1'],
                'baks' => ['18', '19', '18, 19', '17'],
                'daily_bed_range' => [45, 75],
                'vol_rate' => [750, 1150],
                'luas_rate' => [55, 85],
            ],
            9 => [ // TER - TERANTAM
                'akro' => 'TER',
                'blocks' => ['C6', 'C8', 'C4', 'C10', 'B5'],
                'baks' => ['5', '6', '5, 6', '4'],
                'daily_bed_range' => [30, 55],
                'vol_rate' => [550, 800],
                'luas_rate' => [35, 60],
            ],
            10 => [ // STA - SEI TAPUNG
                'akro' => 'STA',
                'blocks' => ['N2', 'N5', 'N7', 'N9', 'P1'],
                'baks' => ['14', '15', '14, 15', '13'],
                'daily_bed_range' => [40, 70],
                'vol_rate' => [700, 1000],
                'luas_rate' => [50, 75],
            ],
            11 => [ // SRO - SEI ROKAN
                'akro' => 'SRO',
                'blocks' => ['P3', 'P7', 'P5', 'P9', 'Q1'],
                'baks' => ['9', '10', '9, 10', '8'],
                'daily_bed_range' => [35, 60],
                'vol_rate' => [600, 880],
                'luas_rate' => [40, 65],
            ],
            12 => [ // SIN - SEI INTAN
                'akro' => 'SIN',
                'blocks' => ['Q2', 'Q4', 'Q6', 'Q8', 'R1'],
                'baks' => ['6', '7', '6, 7', '5'],
                'daily_bed_range' => [25, 50],
                'vol_rate' => [480, 720],
                'luas_rate' => [30, 55],
            ],
        ];

        $keteranganOptions = [
            'Pengaliran Limbah lancar',
            'Pengaliran Limbah dari kolam IPAL ke LA berjalan lancar',
            'Pengaliran Limbah lancar dan normal',
            'Pengaliran Limbah dari kolam IPAL ke LA berjalan lancar',
        ];

        // Specific dates to generate for each month to ensure every week has realistic data:
        // Juni 2026 (Weeks 1 to 5)
        $juniDates = [
            '2026-06-03', '2026-06-05', // W1
            '2026-06-09', '2026-06-12', // W2
            '2026-06-16', '2026-06-19', // W3
            '2026-06-23', '2026-06-26', // W4
            '2026-06-29', '2026-06-30', // W5
        ];

        // Juli 2026 (Weeks 1 to 5)
        $juliDates = [
            '2026-07-02', '2026-07-06', // W1
            '2026-07-09', '2026-07-13', // W2
            '2026-07-16', '2026-07-20', // W3
            '2026-07-23', '2026-07-27', // W4
            '2026-07-29', '2026-07-31', // W5
        ];

        // Agustus 2026 (Weeks 1, 2, and current Week 3 up to today Aug 19)
        $agustusDates = [
            '2026-08-03', '2026-08-06', // W1 (01-07)
            '2026-08-10', '2026-08-13', // W2 (08-14)
            '2026-08-17', '2026-08-18', '2026-08-19', // W3 (15-21)
        ];

        $allDates = array_merge($juniDates, $juliDates, $agustusDates);

        $insertedCount = 0;

        foreach ($pksConfigs as $idPks => $cfg) {
            // Verify PKS exists in database
            $pks = Pks::find($idPks);
            if (!$pks) continue;

            foreach ($allDates as $dateStr) {
                // Check if data already exists for this PKS and date
                $exists = Pengaliran::where('id_pks', $idPks)
                    ->whereDate('tanggal', $dateStr)
                    ->exists();

                if ($exists) {
                    continue;
                }

                $blockIndex = rand(0, count($cfg['blocks']) - 1);
                $bakIndex = rand(0, count($cfg['baks']) - 1);
                $block = $cfg['blocks'][$blockIndex];
                $bak = $cfg['baks'][$bakIndex];

                $flatBed = rand($cfg['daily_bed_range'][0], $cfg['daily_bed_range'][1]);
                $volDialirkan = rand($cfg['vol_rate'][0], $cfg['vol_rate'][1]);
                $volDihasilkan = $volDialirkan + rand(20, 80);
                $luasArea = rand($cfg['luas_rate'][0], $cfg['luas_rate'][1]);
                $ket = $keteranganOptions[rand(0, count($keteranganOptions) - 1)];

                Pengaliran::create([
                    'id_pks' => $idPks,
                    'tanggal' => $dateStr,
                    'jam_mulai' => '07:30',
                    'jam_selesai' => '17:30',
                    'blok' => $block,
                    'no_bak' => $bak,
                    'flat_bed' => $flatBed,
                    'vol_limbah_dialirkan' => $volDialirkan,
                    'vol_limbah_dihasilkan' => $volDihasilkan,
                    'luas_area' => $luasArea,
                    'rotasi' => '1',
                    'keterangan' => $ket,
                ]);

                $insertedCount++;
            }
        }

        echo "Berhasil menambahkan {$insertedCount} data dummy pengaliran untuk bulan Juni, Juli, dan Agustus 2026!\n";
    }
}
