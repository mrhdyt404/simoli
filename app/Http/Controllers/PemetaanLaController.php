<?php

namespace App\Http\Controllers;

use App\Models\Pks;
use App\Models\PerizinanLa;
use App\Models\PetaBlokLa;
use App\Models\Pengaliran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class PemetaanLaController extends Controller
{
    /**
     * Tampilan Utama Peta Interaktif Land Application (GIS Leaflet)
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $daftarPks = Pks::whereNotIn('id_pks', [13, 14, 15])->get();

        // Tentukan PKS aktif
        if (!$user->isAdmin()) {
            $selectedPksId = $user->id_pks;
        } else {
            $selectedPksId = $request->get('id_pks', 1); // default ke PKS Tanah Putih (1)
        }

        $pksAktif = Pks::with(['perizinanLa.sumurPantau', 'petaBlokLa'])->find($selectedPksId);

        if (!$pksAktif && $daftarPks->count() > 0) {
            $pksAktif = $daftarPks->first();
            $selectedPksId = $pksAktif->id_pks;
        }

        // Ambil data izin terbaru
        $perizinan = $pksAktif ? $pksAktif->perizinanLa : null;

        // Ambil blok-blok LA untuk PKS ini
        $blokList = $pksAktif ? $pksAktif->petaBlokLa : collect();

        // Ambil status pengaliran terakhir per blok dalam 30 hari
        $recentFlows = Pengaliran::where('id_pks', $selectedPksId)
            ->where('tanggal', '>=', Carbon::now()->subDays(30))
            ->orderBy('tanggal', 'desc')
            ->get();

        // Map status pengaliran ke setiap blok
        $blokStatus = [];
        foreach ($blokList as $blok) {
            $matchingFlow = $recentFlows->first(function($f) use ($blok) {
                return str_contains(strtoupper($f->blok ?? ''), strtoupper($blok->nama_blok));
            });

            if ($matchingFlow) {
                $daysAgo = Carbon::parse($matchingFlow->tanggal)->diffInDays(Carbon::now());
                $blokStatus[$blok->id] = [
                    'last_date' => $matchingFlow->tanggal->format('d/m/Y'),
                    'vol_limbah' => $matchingFlow->vol_limbah_dialirkan,
                    'is_active_7d' => $daysAgo <= 7,
                    'days_ago' => $daysAgo,
                    'status_text' => $daysAgo <= 7 ? 'Aktif Dialiri (' . $daysAgo . ' hari lalu)' : 'Dialiri ' . $daysAgo . ' hari lalu',
                ];
            } else {
                $blokStatus[$blok->id] = [
                    'last_date' => null,
                    'vol_limbah' => 0,
                    'is_active_7d' => false,
                    'days_ago' => null,
                    'status_text' => 'Istirahat / Belum Dialiri',
                ];
            }
        }

        // Summary metrics
        $totalLuasHa = $blokList->sum('luas_ha');
        $totalFlatBed = $blokList->sum('jumlah_flat_bed');
        $totalBak = $blokList->sum('jumlah_bak');
        $totalSumurPantau = $perizinan && $perizinan->sumurPantau ? $perizinan->sumurPantau->count() : 0;

        return view('pemetaan_la.index', compact(
            'daftarPks',
            'pksAktif',
            'selectedPksId',
            'perizinan',
            'blokList',
            'blokStatus',
            'totalLuasHa',
            'totalFlatBed',
            'totalBak',
            'totalSumurPantau'
        ));
    }

    /**
     * API JSON GeoData per PKS untuk kebutuhan Leaflet Map
     */
    public function getPksGeoData($idPks)
    {
        $pks = Pks::with(['perizinanLa.sumurPantau', 'petaBlokLa'])->findOrFail($idPks);
        $perizinan = $pks->perizinanLa;

        $recentFlows = Pengaliran::where('id_pks', $idPks)
            ->where('tanggal', '>=', Carbon::now()->subDays(30))
            ->orderBy('tanggal', 'desc')
            ->get();

        $bloks = $pks->petaBlokLa->map(function($blok) use ($recentFlows) {
            $matchingFlow = $recentFlows->first(function($f) use ($blok) {
                return str_contains(strtoupper($f->blok ?? ''), strtoupper($blok->nama_blok));
            });

            $daysAgo = $matchingFlow ? Carbon::parse($matchingFlow->tanggal)->diffInDays(Carbon::now()) : null;

            return [
                'id' => $blok->id,
                'nama_blok' => $blok->nama_blok,
                'afdeling' => $blok->afdeling,
                'luas_ha' => $blok->luas_ha,
                'jumlah_bak' => $blok->jumlah_bak,
                'jumlah_flat_bed' => $blok->jumlah_flat_bed,
                'panjang_parit_meter' => $blok->panjang_parit_meter,
                'latitude' => $blok->latitude_center,
                'longitude' => $blok->longitude_center,
                'polygon_geojson' => $blok->polygon_geojson,
                'last_flow_date' => $matchingFlow ? $matchingFlow->tanggal->format('d/m/Y') : null,
                'vol_limbah' => $matchingFlow ? $matchingFlow->vol_limbah_dialirkan : 0,
                'is_active_7d' => $daysAgo !== null && $daysAgo <= 7,
            ];
        });

        return response()->json([
            'status' => 'success',
            'pks' => [
                'id' => $pks->id_pks,
                'nama' => $pks->nama,
                'kode' => $pks->kode,
                'akro' => $pks->akro,
                'manager' => $pks->manager,
            ],
            'perizinan' => $perizinan ? [
                'id' => $perizinan->id,
                'nomor_sk' => $perizinan->nomor_sk,
                'instansi' => $perizinan->instansi_penerbit,
                'tanggal_terbit' => $perizinan->tanggal_terbit ? $perizinan->tanggal_terbit->format('d/m/Y') : null,
                'tanggal_berakhir' => $perizinan->tanggal_berakhir ? $perizinan->tanggal_berakhir->format('d/m/Y') : null,
                'status' => $perizinan->status_label,
                'bod_max' => $perizinan->bod_maksimal,
                'ph_range' => $perizinan->ph_min . ' - ' . $perizinan->ph_max,
                'debit_max' => $perizinan->debit_maksimal_harian,
                'luas_izin' => $perizinan->luas_areal_izin,
                'saluran' => $perizinan->saluran_distribusi,
                'titik_penaatan' => [
                    'nama' => $perizinan->nama_titik_penaatan,
                    'lat' => $perizinan->lat_titik_penaatan,
                    'long' => $perizinan->long_titik_penaatan,
                    'text' => $perizinan->koordinat_penaatan_text,
                ],
                'sumur_pantau' => $perizinan->sumurPantau->map(function($s) {
                    return [
                        'id' => $s->id,
                        'nama' => $s->nama_sumur,
                        'jenis' => $s->jenis_sumur,
                        'blok' => $s->lokasi_blok,
                        'lat' => $s->latitude,
                        'long' => $s->longitude,
                        'text' => $s->koordinat_text,
                        'parameter' => $s->parameter_pantau,
                    ];
                }),
            ] : null,
            'bloks' => $bloks,
        ]);
    }

    /**
     * Tampilan Digital Kartografi Layout Peta Resmi GIS PTPN
     */
    public function petaDigital($idPks)
    {
        $pks = Pks::with(['perizinanLa.sumurPantau', 'petaBlokLa'])->findOrFail($idPks);
        $perizinan = $pks->perizinanLa;
        $blokList = $pks->petaBlokLa;

        return view('pemetaan_la.digital_peta', compact('pks', 'perizinan', 'blokList'));
    }
}
