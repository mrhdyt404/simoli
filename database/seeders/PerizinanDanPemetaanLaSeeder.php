<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PerizinanLa;
use App\Models\PerizinanSumurPantau;
use App\Models\PetaBlokLa;
use App\Models\Pks;

class PerizinanDanPemetaanLaSeeder extends Seeder
{
    public function run(): void
    {
        // 1. PKS TANAH PUTIH (id_pks = 1, TPU)
        $tpu = PerizinanLa::updateOrCreate(
            ['id_pks' => 1, 'nomor_sk' => '41/DLH/2017'],
            [
                'tentang' => 'Izin Pemanfaatan Air Limbah Pabrik Kelapa Sawit Pada Tanah di Perkebunan Kelapa Sawit PT. Perkebunan Nusantara V Kebun Tanah Putih',
                'instansi_penerbit' => 'Dinas Lingkungan Hidup Kabupaten Rokan Hilir',
                'tanggal_terbit' => '2017-04-27',
                'tanggal_berakhir' => '2027-04-27',
                'masa_berlaku_tahun' => 5,
                'bod_maksimal' => 5000,
                'ph_min' => 6.00,
                'ph_max' => 9.00,
                'debit_maksimal_harian' => 400.00,
                'luas_areal_izin' => 250.00,
                'saluran_distribusi' => 'Pipa PVC ukuran 6 inchi',
                'nama_titik_penaatan' => 'IPAL Kolam Anaerob Pond IV PKS Tanah Putih',
                'lat_titik_penaatan' => 1.74252500,
                'long_titik_penaatan' => 100.51001100,
                'koordinat_penaatan_text' => 'N 01°44\' 33.09" E 100°30\' 36.04"',
                'status_izin' => 'Aktif',
                'keterangan' => 'Pemanfaatan air limbah ke tanah (Land Application) seluas 250 Ha di Afdeling II & Afdeling III.',
            ]
        );

        PerizinanSumurPantau::updateOrCreate(
            ['perizinan_la_id' => $tpu->id, 'nama_sumur' => 'Sumur Pantau Lahan Aplikasi'],
            [
                'jenis_sumur' => 'Sumur Pantau Aplikasi',
                'lokasi_blok' => 'AFD III Blok C 27',
                'latitude' => 1.74956400,
                'longitude' => 100.52151400,
                'koordinat_text' => 'N 01°44\' 58.43" E 100°31\' 17.45"',
                'frekuensi_pantau' => '6 bulan sekali',
                'parameter_pantau' => 'BOD, DO, pH, NO3, NH3-N, Cd, Cu, Pb, Zn, Cl, SO4-2',
            ]
        );

        PerizinanSumurPantau::updateOrCreate(
            ['perizinan_la_id' => $tpu->id, 'nama_sumur' => 'Sumur Pantau Lahan Kontrol'],
            [
                'jenis_sumur' => 'Sumur Pantau Kontrol',
                'lokasi_blok' => 'AFD II Blok G.25',
                'latitude' => 1.72977800,
                'longitude' => 100.51175000,
                'koordinat_text' => 'N 01°43\' 47.2" E 100°30\' 42.3"',
                'frekuensi_pantau' => '6 bulan sekali',
                'parameter_pantau' => 'BOD, DO, pH, NO3, NH3-N, Cd, Cu, Pb, Zn, Cl, SO4-2',
            ]
        );

        PerizinanSumurPantau::updateOrCreate(
            ['perizinan_la_id' => $tpu->id, 'nama_sumur' => 'Sumur Pantau Pemukiman / Perumahan Karyawan'],
            [
                'jenis_sumur' => 'Sumur Pantau Pemukiman',
                'lokasi_blok' => 'AFD III Blok D.27',
                'latitude' => 1.74405600,
                'longitude' => 100.52147800,
                'koordinat_text' => 'N 01°44\' 38.60" E 100°31\' 17.32"',
                'frekuensi_pantau' => '6 bulan sekali',
                'parameter_pantau' => 'BOD, DO, pH, NO3, NH3-N, Cd, Cu, Pb, Zn, Cl, SO4-2',
            ]
        );

        // Blok LA Tanah Putih
        $tpuBloks = [
            ['afdeling' => 'AFD II', 'nama_blok' => 'E25', 'luas_ha' => 12.06, 'bak_awal' => 1, 'bak_akhir' => 2, 'jumlah_bak' => 16, 'flat_bed' => 951, 'panjang' => 945, 'lat' => 1.7440, 'lng' => 100.5050],
            ['afdeling' => 'AFD II', 'nama_blok' => 'L25', 'luas_ha' => 14.21, 'bak_awal' => 3, 'bak_akhir' => 3, 'jumlah_bak' => 8, 'flat_bed' => 470, 'panjang' => 624, 'lat' => 1.7420, 'lng' => 100.5080],
            ['afdeling' => 'AFD II', 'nama_blok' => 'F26', 'luas_ha' => 24.76, 'bak_awal' => 4, 'bak_akhir' => 5, 'jumlah_bak' => 25, 'flat_bed' => 1525, 'panjang' => 1247, 'lat' => 1.7390, 'lng' => 100.5110],
            ['afdeling' => 'AFD II', 'nama_blok' => 'E26', 'luas_ha' => 29.11, 'bak_awal' => 6, 'bak_akhir' => 6, 'jumlah_bak' => 18, 'flat_bed' => 1186, 'panjang' => 330, 'lat' => 1.7460, 'lng' => 100.5140],
            ['afdeling' => 'AFD II', 'nama_blok' => 'F27', 'luas_ha' => 31.24, 'bak_awal' => 7, 'bak_akhir' => 7, 'jumlah_bak' => 7, 'flat_bed' => 213, 'panjang' => 353, 'lat' => 1.7380, 'lng' => 100.5170],
            ['afdeling' => 'AFD III', 'nama_blok' => 'E27', 'luas_ha' => 41.54, 'bak_awal' => 8, 'bak_akhir' => 9, 'jumlah_bak' => 25, 'flat_bed' => 1030, 'panjang' => 1045, 'lat' => 1.7480, 'lng' => 100.5180],
            ['afdeling' => 'AFD III', 'nama_blok' => 'E28', 'luas_ha' => 49.04, 'bak_awal' => 10, 'bak_akhir' => 10, 'jumlah_bak' => 13, 'flat_bed' => 750, 'panjang' => 809, 'lat' => 1.7500, 'lng' => 100.5220],
            ['afdeling' => 'AFD III', 'nama_blok' => 'C28', 'luas_ha' => 54.08, 'bak_awal' => 11, 'bak_akhir' => 11, 'jumlah_bak' => 11, 'flat_bed' => 504, 'panjang' => 362, 'lat' => 1.7540, 'lng' => 100.5200],
            ['afdeling' => 'AFD III', 'nama_blok' => 'E29', 'luas_ha' => 81.16, 'bak_awal' => 12, 'bak_akhir' => 13, 'jumlah_bak' => 25, 'flat_bed' => 1507, 'panjang' => 1972, 'lat' => 1.7510, 'lng' => 100.5260],
            ['afdeling' => 'AFD III', 'nama_blok' => 'D29', 'luas_ha' => 98.53, 'bak_awal' => 14, 'bak_akhir' => 16, 'jumlah_bak' => 34, 'flat_bed' => 1737, 'panjang' => 1553, 'lat' => 1.7470, 'lng' => 100.5280],
            ['afdeling' => 'AFD III', 'nama_blok' => 'C29', 'luas_ha' => 123.89, 'bak_awal' => 17, 'bak_akhir' => 19, 'jumlah_bak' => 25, 'flat_bed' => 1715, 'panjang' => 1454, 'lat' => 1.7530, 'lng' => 100.5290],
            ['afdeling' => 'AFD III', 'nama_blok' => 'D28', 'luas_ha' => 133.55, 'bak_awal' => 20, 'bak_akhir' => 20, 'jumlah_bak' => 12, 'flat_bed' => 966, 'panjang' => 527, 'lat' => 1.7450, 'lng' => 100.5240],
            ['afdeling' => 'AFD II', 'nama_blok' => 'F28', 'luas_ha' => 139.68, 'bak_awal' => 21, 'bak_akhir' => 21, 'jumlah_bak' => 8, 'flat_bed' => 613, 'panjang' => 468, 'lat' => 1.7370, 'lng' => 100.5230],
        ];

        foreach ($tpuBloks as $b) {
            PetaBlokLa::updateOrCreate(
                ['id_pks' => 1, 'nama_blok' => $b['nama_blok']],
                [
                    'afdeling' => $b['afdeling'],
                    'luas_ha' => $b['luas_ha'],
                    'no_bak_awal' => $b['bak_awal'],
                    'no_bak_akhir' => $b['bak_akhir'],
                    'jumlah_bak' => $b['jumlah_bak'],
                    'jumlah_flat_bed' => $b['flat_bed'],
                    'panjang_parit_meter' => $b['panjang'],
                    'latitude_center' => $b['lat'],
                    'longitude_center' => $b['lng'],
                    'status_aktif' => true,
                ]
            );
        }

        // 2. PKS TANJUNG MEDAN (id_pks = 2, TME)
        $tme = PerizinanLa::updateOrCreate(
            ['id_pks' => 2, 'nomor_sk' => '42/DLH/2017'],
            [
                'tentang' => 'Izin Pemanfaatan Air Limbah Pabrik Kelapa Sawit Pada Tanah di Perkebunan Kelapa Sawit PT. Perkebunan Nusantara V Kebun Tanjung Medan',
                'instansi_penerbit' => 'Dinas Lingkungan Hidup Kabupaten Rokan Hilir',
                'tanggal_terbit' => '2017-05-03',
                'tanggal_berakhir' => '2027-05-03',
                'masa_berlaku_tahun' => 5,
                'bod_maksimal' => 5000,
                'ph_min' => 6.00,
                'ph_max' => 9.00,
                'debit_maksimal_harian' => 422.00,
                'luas_areal_izin' => 240.00,
                'saluran_distribusi' => 'Pipa PVC ukuran 6 inchi',
                'nama_titik_penaatan' => 'IPAL Kolam Anaerob Pond IV PKS Tanjung Medan',
                'lat_titik_penaatan' => 1.58363300,
                'long_titik_penaatan' => 100.59085300,
                'koordinat_penaatan_text' => 'N 01°35\' 01.08" E 100°35\' 27.07"',
                'status_izin' => 'Aktif',
                'keterangan' => 'Pemanfaatan air limbah dengan sistem bak distribusi (21 Bak) dan 2.786 flatbed.',
            ]
        );

        PerizinanSumurPantau::updateOrCreate(
            ['perizinan_la_id' => $tme->id, 'nama_sumur' => 'Sumur Pantau Lokasi Pemanfaatan'],
            [
                'jenis_sumur' => 'Sumur Pantau Aplikasi',
                'lokasi_blok' => 'Blok L.28',
                'latitude' => 1.57700000,
                'longitude' => 100.58997200,
                'koordinat_text' => 'N 01°34\' 37.2" E 100°35\' 23.9"',
                'frekuensi_pantau' => '6 bulan sekali',
                'parameter_pantau' => 'BOD, DO, pH, NO3, NH3-N, Cd, Cu, Pb, Zn, Cl, SO4-2',
            ]
        );

        PerizinanSumurPantau::updateOrCreate(
            ['perizinan_la_id' => $tme->id, 'nama_sumur' => 'Sumur Pantau Lahan Kontrol'],
            [
                'jenis_sumur' => 'Sumur Pantau Kontrol',
                'lokasi_blok' => 'Blok L.22',
                'latitude' => 1.56075000,
                'longitude' => 100.59094400,
                'koordinat_text' => 'N 01°33\' 38.7" E 100°35\' 27.4"',
                'frekuensi_pantau' => '6 bulan sekali',
                'parameter_pantau' => 'BOD, DO, pH, NO3, NH3-N, Cd, Cu, Pb, Zn, Cl, SO4-2',
            ]
        );

        PerizinanSumurPantau::updateOrCreate(
            ['perizinan_la_id' => $tme->id, 'nama_sumur' => 'Sumur Pantau Perumahan Karyawan'],
            [
                'jenis_sumur' => 'Sumur Pantau Pemukiman',
                'lokasi_blok' => 'Blok L.24',
                'latitude' => 1.56636100,
                'longitude' => 100.59352800,
                'koordinat_text' => 'N 01°33\' 58.9" E 100°35\' 36.7"',
                'frekuensi_pantau' => '6 bulan sekali',
                'parameter_pantau' => 'BOD, DO, pH, NO3, NH3-N, Cd, Cu, Pb, Zn, Cl, SO4-2',
            ]
        );

        // Blok TME
        $tmeBloks = [
            ['afdeling' => 'AFD I', 'nama_blok' => 'M 28', 'luas_ha' => 20.5, 'bak_awal' => 1, 'bak_akhir' => 1, 'jumlah_bak' => 1, 'flat_bed' => 98, 'panjang' => 63, 'lat' => 1.5810, 'lng' => 100.5890],
            ['afdeling' => 'AFD I', 'nama_blok' => 'L 28', 'luas_ha' => 22.0, 'bak_awal' => 2, 'bak_akhir' => 2, 'jumlah_bak' => 1, 'flat_bed' => 109, 'panjang' => 174, 'lat' => 1.5790, 'lng' => 100.5900],
            ['afdeling' => 'AFD I', 'nama_blok' => 'L 27', 'luas_ha' => 28.4, 'bak_awal' => 3, 'bak_akhir' => 7, 'jumlah_bak' => 5, 'flat_bed' => 741, 'panjang' => 662, 'lat' => 1.5760, 'lng' => 100.5920],
            ['afdeling' => 'AFD II', 'nama_blok' => 'L 26', 'luas_ha' => 32.8, 'bak_awal' => 8, 'bak_akhir' => 14, 'jumlah_bak' => 7, 'flat_bed' => 885, 'panjang' => 690, 'lat' => 1.5720, 'lng' => 100.5940],
            ['afdeling' => 'AFD II', 'nama_blok' => 'L 25', 'luas_ha' => 35.6, 'bak_awal' => 15, 'bak_akhir' => 18, 'jumlah_bak' => 4, 'flat_bed' => 709, 'panjang' => 548, 'lat' => 1.5680, 'lng' => 100.5960],
            ['afdeling' => 'AFD II', 'nama_blok' => 'L 24', 'luas_ha' => 18.2, 'bak_awal' => 19, 'bak_akhir' => 21, 'jumlah_bak' => 3, 'flat_bed' => 248, 'panjang' => 181, 'lat' => 1.5650, 'lng' => 100.5970],
        ];

        foreach ($tmeBloks as $b) {
            PetaBlokLa::updateOrCreate(
                ['id_pks' => 2, 'nama_blok' => $b['nama_blok']],
                [
                    'afdeling' => $b['afdeling'],
                    'luas_ha' => $b['luas_ha'],
                    'no_bak_awal' => $b['bak_awal'],
                    'no_bak_akhir' => $b['bak_akhir'],
                    'jumlah_bak' => $b['jumlah_bak'],
                    'jumlah_flat_bed' => $b['flat_bed'],
                    'panjang_parit_meter' => $b['panjang'],
                    'latitude_center' => $b['lat'],
                    'longitude_center' => $b['lng'],
                    'status_aktif' => true,
                ]
            );
        }

        // 3. PKS SEI GARO (id_pks = 3, SGO)
        $sgo = PerizinanLa::updateOrCreate(
            ['id_pks' => 3, 'nomor_sk' => '503/DPM-PTSP.PEL/LA/2017/09'],
            [
                'tentang' => 'Izin Pemanfaatan Air Limbah Industri Minyak Sawit Pada Tanah Perkebunan Kelapa Sawit PT. Perkebunan Nusantara V Unit PKS Sei Garo',
                'instansi_penerbit' => 'Dinas Penanaman Modal dan Pelayanan Terpadu Satu Pintu Kabupaten Kampar',
                'tanggal_terbit' => '2017-12-20',
                'tanggal_berakhir' => '2027-12-20',
                'masa_berlaku_tahun' => 5,
                'bod_maksimal' => 5000,
                'ph_min' => 6.00,
                'ph_max' => 9.00,
                'debit_maksimal_harian' => 380.00,
                'luas_areal_izin' => 213.34,
                'saluran_distribusi' => 'Pipa PVC ukuran 6 inchi',
                'nama_titik_penaatan' => 'IPAL Kolam 4 ke Land Aplikasi PKS Sei Garo',
                'lat_titik_penaatan' => 0.64172200,
                'long_titik_penaatan' => 101.11355600,
                'koordinat_penaatan_text' => 'N 00°38\' 30.2" E 101°06\' 48.8"',
                'status_izin' => 'Aktif',
                'keterangan' => 'Luas areal terizin 213.34 Ha terbagi dalam 6 Blok (L22, K22, K24, M22, L20, K20) di Kec. Tapung, Kab. Kampar.',
            ]
        );

        PerizinanSumurPantau::updateOrCreate(
            ['perizinan_la_id' => $sgo->id, 'nama_sumur' => 'Sumur Pantau Lahan Aplikasi'],
            [
                'jenis_sumur' => 'Sumur Pantau Aplikasi',
                'lokasi_blok' => 'Blok L22',
                'latitude' => 0.64822200,
                'longitude' => 101.12352800,
                'koordinat_text' => 'N 00°38\' 53.6" E 101°07\' 24.7"',
                'frekuensi_pantau' => '6 bulan sekali',
                'parameter_pantau' => 'BOD, DO, pH, NO3, NH3-N, Cd, Cu, Pb, Zn, Cl, SO4-2',
            ]
        );

        PerizinanSumurPantau::updateOrCreate(
            ['perizinan_la_id' => $sgo->id, 'nama_sumur' => 'Sumur Pantau Lahan Kontrol'],
            [
                'jenis_sumur' => 'Sumur Pantau Kontrol',
                'lokasi_blok' => 'Blok M22',
                'latitude' => 0.64505600,
                'longitude' => 101.13122200,
                'koordinat_text' => 'N 00°38\' 42.2" E 101°07\' 52.4"',
                'frekuensi_pantau' => '6 bulan sekali',
                'parameter_pantau' => 'BOD, DO, pH, NO3, NH3-N, Cd, Cu, Pb, Zn, Cl, SO4-2',
            ]
        );

        PerizinanSumurPantau::updateOrCreate(
            ['perizinan_la_id' => $sgo->id, 'nama_sumur' => 'Sumur Pantau Pemukiman / Emplasmen'],
            [
                'jenis_sumur' => 'Sumur Pantau Pemukiman',
                'lokasi_blok' => 'Emplasmen PKS Sei Garo',
                'latitude' => 0.63866700,
                'longitude' => 101.11419400,
                'koordinat_text' => 'N 00°38\' 19.2" E 101°06\' 51.1"',
                'frekuensi_pantau' => '6 bulan sekali',
                'parameter_pantau' => 'BOD, DO, pH, NO3, NH3-N, Cd, Cu, Pb, Zn, Cl, SO4-2',
            ]
        );

        // Blok SGO
        $sgoBloks = [
            ['afdeling' => 'AFD I', 'nama_blok' => 'L22', 'luas_ha' => 40.00, 'flat_bed' => 450, 'lat' => 0.6482, 'lng' => 101.1235],
            ['afdeling' => 'AFD I', 'nama_blok' => 'K22', 'luas_ha' => 40.00, 'flat_bed' => 450, 'lat' => 0.6475, 'lng' => 101.1155],
            ['afdeling' => 'AFD I', 'nama_blok' => 'K24', 'luas_ha' => 26.84, 'flat_bed' => 310, 'lat' => 0.6421, 'lng' => 101.1145],
            ['afdeling' => 'AFD II', 'nama_blok' => 'M22', 'luas_ha' => 28.50, 'flat_bed' => 330, 'lat' => 0.6450, 'lng' => 101.1312],
            ['afdeling' => 'AFD II', 'nama_blok' => 'L20', 'luas_ha' => 40.00, 'flat_bed' => 460, 'lat' => 0.6515, 'lng' => 101.1240],
            ['afdeling' => 'AFD II', 'nama_blok' => 'K20', 'luas_ha' => 38.00, 'flat_bed' => 420, 'lat' => 0.6510, 'lng' => 101.1150],
        ];

        foreach ($sgoBloks as $b) {
            PetaBlokLa::updateOrCreate(
                ['id_pks' => 3, 'nama_blok' => $b['nama_blok']],
                [
                    'afdeling' => $b['afdeling'],
                    'luas_ha' => $b['luas_ha'],
                    'jumlah_flat_bed' => $b['flat_bed'],
                    'latitude_center' => $b['lat'],
                    'longitude_center' => $b['lng'],
                    'status_aktif' => true,
                ]
            );
        }

        // 4. PKS TERANTAM (id_pks = 9, TER)
        $ter = PerizinanLa::updateOrCreate(
            ['id_pks' => 9, 'nomor_sk' => '503/DLH-KPR/LA/2021/14'],
            [
                'tentang' => 'Izin Pemanfaatan Air Limbah Land Application PT. Perkebunan Nusantara V PKS Terantam',
                'instansi_penerbit' => 'Dinas Lingkungan Hidup Kabupaten Kampar',
                'tanggal_terbit' => '2021-08-15',
                'tanggal_berakhir' => '2026-08-15',
                'masa_berlaku_tahun' => 5,
                'bod_maksimal' => 5000,
                'ph_min' => 6.00,
                'ph_max' => 9.00,
                'debit_maksimal_harian' => 450.00,
                'luas_areal_izin' => 225.50,
                'saluran_distribusi' => 'Pipa PVC ukuran 6 inchi',
                'nama_titik_penaatan' => 'IPAL Kolam Anaerob Outlet Pond 4 PKS Terantam',
                'lat_titik_penaatan' => 0.47010000,
                'long_titik_penaatan' => 101.42580000,
                'koordinat_penaatan_text' => 'N 00°28\' 12.36" E 101°25\' 32.88"',
                'status_izin' => 'Aktif',
                'keterangan' => 'Pemanfaatan air limbah Land Application di areal Afdeling I & II Kebun Terantam.',
            ]
        );

        PerizinanSumurPantau::updateOrCreate(
            ['perizinan_la_id' => $ter->id, 'nama_sumur' => 'Sumur Pantau Lahan Aplikasi Terantam'],
            [
                'jenis_sumur' => 'Sumur Pantau Aplikasi',
                'lokasi_blok' => 'Blok C20',
                'latitude' => 0.47150000,
                'longitude' => 101.42850000,
                'koordinat_text' => 'N 00°28\' 17.4" E 101°25\' 42.6"',
                'frekuensi_pantau' => '6 bulan sekali',
                'parameter_pantau' => 'BOD, DO, pH, NO3, NH3-N, Logam Berat',
            ]
        );

        PerizinanSumurPantau::updateOrCreate(
            ['perizinan_la_id' => $ter->id, 'nama_sumur' => 'Sumur Pantau Kontrol Terantam'],
            [
                'jenis_sumur' => 'Sumur Pantau Kontrol',
                'lokasi_blok' => 'Blok B26',
                'latitude' => 0.46820000,
                'longitude' => 101.42300000,
                'koordinat_text' => 'N 00°28\' 05.5" E 101°25\' 22.8"',
                'frekuensi_pantau' => '6 bulan sekali',
                'parameter_pantau' => 'BOD, DO, pH, NO3, NH3-N, Logam Berat',
            ]
        );

        // Blok TER
        $terBloks = [
            ['afdeling' => 'AFD I', 'nama_blok' => 'C18', 'luas_ha' => 24.5, 'flat_bed' => 420, 'lat' => 0.4720, 'lng' => 101.4270],
            ['afdeling' => 'AFD I', 'nama_blok' => 'C20', 'luas_ha' => 28.0, 'flat_bed' => 530, 'lat' => 0.4715, 'lng' => 101.4285],
            ['afdeling' => 'AFD I', 'nama_blok' => 'C26', 'luas_ha' => 26.5, 'flat_bed' => 480, 'lat' => 0.4695, 'lng' => 101.4260],
            ['afdeling' => 'AFD II', 'nama_blok' => 'B26', 'luas_ha' => 22.0, 'flat_bed' => 360, 'lat' => 0.4682, 'lng' => 101.4230],
            ['afdeling' => 'AFD II', 'nama_blok' => 'A22', 'luas_ha' => 30.2, 'flat_bed' => 510, 'lat' => 0.4735, 'lng' => 101.4295],
            ['afdeling' => 'AFD II', 'nama_blok' => 'A20', 'luas_ha' => 27.8, 'flat_bed' => 460, 'lat' => 0.4745, 'lng' => 101.4310],
            ['afdeling' => 'AFD II', 'nama_blok' => 'A18', 'luas_ha' => 25.5, 'flat_bed' => 490, 'lat' => 0.4755, 'lng' => 101.4325],
            ['afdeling' => 'AFD II', 'nama_blok' => 'D22', 'luas_ha' => 23.0, 'flat_bed' => 440, 'lat' => 0.4670, 'lng' => 101.4275],
        ];

        foreach ($terBloks as $b) {
            PetaBlokLa::updateOrCreate(
                ['id_pks' => 9, 'nama_blok' => $b['nama_blok']],
                [
                    'afdeling' => $b['afdeling'],
                    'luas_ha' => $b['luas_ha'],
                    'jumlah_flat_bed' => $b['flat_bed'],
                    'latitude_center' => $b['lat'],
                    'longitude_center' => $b['lng'],
                    'status_aktif' => true,
                ]
            );
        }
    }
}
