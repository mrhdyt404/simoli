<?php

namespace App\Http\Controllers;

use App\Models\Pengaliran;
use App\Models\Pemeliharaan;
use App\Models\Pks;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LaporanHarianController extends Controller
{
    private function numericSum($items, string $field): float|int
    {
        return $items->sum(function ($item) use ($field) {
            $value = $item->{$field} ?? 0;

            if (is_numeric($value)) {
                return $value + 0;
            }

            $sanitized = preg_replace('/[^0-9.-]/', '', (string) $value);

            return is_numeric($sanitized) ? $sanitized + 0 : 0;
        });
    }

    public function index(Request $request)
    {
        $user = Auth::user();

        $pksList = Pks::orderBy('NAMA')
            ->get();

        // Default to today's date range
        $tanggalAwal = $request->input('tanggal_awal', date('Y-m-d'));
        $tanggalAkhir = $request->input('tanggal_akhir', $tanggalAwal);

        if ($tanggalAwal > $tanggalAkhir) {
            [$tanggalAwal, $tanggalAkhir] = [$tanggalAkhir, $tanggalAwal];
        }

        $periodeLabel = Carbon::parse($tanggalAwal)->translatedFormat('d F Y');

        if ($tanggalAwal !== $tanggalAkhir) {
            $periodeLabel .= ' s/d ' . Carbon::parse($tanggalAkhir)->translatedFormat('d F Y');
        }

        // Build pengaliran query for selected date range
        $pengaliranQuery = Pengaliran::with('pks')
            ->whereBetween('tanggal', [$tanggalAwal, $tanggalAkhir]);

        // Unit user: hanya lihat data milik PKS-nya
        if ($user->isUnit()) {
            $pengaliranQuery->where('id_pks', $user->id_pks);
        } elseif ($request->filled('id_pks')) {
            $pengaliranQuery->where('id_pks', $request->id_pks);
        }

        $pengaliranData = $pengaliranQuery->orderBy('id_pks')->get();

        // Build pemeliharaan query for selected date range
        $pemeliharaanQuery = Pemeliharaan::with('pks')
            ->whereBetween('tanggal', [$tanggalAwal, $tanggalAkhir]);

        if ($user->isUnit()) {
            $pemeliharaanQuery->where('id_pks', $user->id_pks);
        } elseif ($request->filled('id_pks')) {
            $pemeliharaanQuery->where('id_pks', $request->id_pks);
        }

        $pemeliharaanData = $pemeliharaanQuery->orderBy('id_pks')->get();

        // Summary stats for pengaliran
        $summaryPengaliran = [
            'count' => $pengaliranData->count(),
            'vol_dihasilkan' => $this->numericSum($pengaliranData, 'vol_limbah_dihasilkan'),
            'vol_dialirkan' => $this->numericSum($pengaliranData, 'vol_limbah_dialirkan'),
            'flat_bed' => $this->numericSum($pengaliranData, 'flat_bed'),
            'luas_area' => $this->numericSum($pengaliranData, 'luas_area'),
        ];

        // Summary stats for pemeliharaan
        $summaryPemeliharaan = [
            'count' => $pemeliharaanData->count(),
            'flat_bed' => $this->numericSum($pemeliharaanData, 'flat_bed'),
            'long_bed' => $this->numericSum($pemeliharaanData, 'long_bed'),
            'jumlah_hk' => $this->numericSum($pemeliharaanData, 'jumlah_hk'),
        ];

        // Group pengaliran by PKS
        $pengaliranByPks = $pengaliranData->groupBy(function ($item) {
            return $item->pks ? $item->pks->AKRO : 'N/A';
        });

        // Group pemeliharaan by PKS
        $pemeliharaanByPks = $pemeliharaanData->groupBy(function ($item) {
            return $item->pks ? $item->pks->AKRO : 'N/A';
        });

        return view('report.laporan-harian', compact(
            'pksList',
            'tanggalAwal',
            'tanggalAkhir',
            'periodeLabel',
            'pengaliranData',
            'pemeliharaanData',
            'summaryPengaliran',
            'summaryPemeliharaan',
            'pengaliranByPks',
            'pemeliharaanByPks'
        ));
    }
}
