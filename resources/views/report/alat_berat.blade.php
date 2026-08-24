@extends('layouts.simoli')

@section('title', 'Highlight Monitoring Alat Berat')
@section('page-title', 'Report Monitoring Alat Berat')
@section('page-description', 'Highlight Laporan Operasional, Hasil Pekerjaan & Koordinat GPS Alat Berat')

@section('breadcrumb')
    <li>Report</li>
    <li class="separator">/</li>
    <li>Report Alat Berat</li>
@endsection

@php
    $namaBulan = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
    
    // Group data by date for Highlight format
    $dataByTanggal = $data->groupBy(function($item) {
        return $item->tanggal;
    });

    // Start & End date for period pill
    $firstDate = $data->min('tanggal');
    $lastDate = $data->max('tanggal');
    
    $startDateStr = $firstDate ? \Carbon\Carbon::parse($firstDate)->translatedFormat('d F Y') : '01 ' . $namaBulan[(int)$bulan] . ' ' . $tahun;
    $endDateStr = $lastDate ? \Carbon\Carbon::parse($lastDate)->translatedFormat('d F Y') : \Carbon\Carbon::createFromDate($tahun, $bulan, 1)->endOfMonth()->translatedFormat('d F Y');
@endphp

@section('styles')
<link rel="stylesheet" type="text/css" href="{{ asset('duraluxadmin/assets/vendors/css/select2.min.css') }}" />
<link rel="stylesheet" type="text/css" href="{{ asset('duraluxadmin/assets/vendors/css/select2-theme.min.css') }}" />
<style>
    /* ================================================================
       REPORT ALAT BERAT — PTPN GREEN GLOSSY & GRADIENT THEME
       ================================================================ */
    @keyframes fadeUpCard {
        from { opacity:0; transform:translateY(12px); }
        to   { opacity:1; transform:translateY(0); }
    }

    .print-only { display: none; }

    /* === INFOGRAFIS HIGHLIGHT HEADER BANNER (PTPN GREEN GRADIENT) === */
    .highlight-banner {
        background: linear-gradient(135deg, #052e16 0%, #0a2317 35%, #166534 75%, #16a34a 100%);
        border-radius: 20px;
        padding: 24px 30px;
        color: #ffffff;
        position: relative;
        overflow: hidden;
        box-shadow: 0 14px 35px rgba(5,46,22,.3);
        margin-bottom: 24px;
        border: 1px solid rgba(34,197,94,.3);
    }

    .highlight-banner::before {
        content: '';
        position: absolute;
        top: -100px;
        right: -100px;
        width: 320px;
        height: 320px;
        border-radius: 50%;
        background: radial-gradient(circle, rgba(134,239,172,.2) 0%, transparent 70%);
        pointer-events: none;
    }

    .highlight-title-badge {
        display: inline-block;
        background: rgba(255,255,255,.15);
        backdrop-filter: blur(10px);
        padding: 4px 14px;
        border-radius: 50px;
        font-size: 11px;
        font-weight: 800;
        letter-spacing: 2px;
        text-transform: uppercase;
        color: #86efac;
        margin-bottom: 8px;
        border: 1px solid rgba(255,255,255,.2);
    }

    .highlight-title-main {
        font-family: 'Outfit', 'Plus Jakarta Sans', sans-serif;
        font-size: clamp(24px, 3vw + 12px, 38px);
        font-weight: 900;
        line-height: 1.05;
        letter-spacing: -0.5px;
        text-transform: uppercase;
        color: #ffffff;
        text-shadow: 0 3px 10px rgba(0,0,0,.3);
        margin-bottom: 12px;
    }

    .highlight-title-main span {
        color: #fbbf24;
        text-shadow: 0 0 20px rgba(251,191,36,.5);
    }

    .period-pill {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: linear-gradient(135deg, #d4a017 0%, #b45309 100%);
        color: #ffffff;
        padding: 8px 18px;
        border-radius: 50px;
        font-size: 13px;
        font-weight: 800;
        letter-spacing: 0.5px;
        box-shadow: 0 4px 15px rgba(180,83,9,.35);
        border: 1px solid rgba(255,255,255,.3);
    }

    /* Ribbon badge top right */
    .motto-ribbon {
        background: linear-gradient(135deg, #052e16 0%, #166534 100%);
        border: 2px solid #fbbf24;
        color: #ffffff;
        padding: 10px 16px;
        border-radius: 12px;
        text-align: center;
        box-shadow: 0 6px 20px rgba(0,0,0,.25);
        transform: rotate(1deg);
    }
    .motto-ribbon-text {
        font-family: 'Outfit', sans-serif;
        font-size: 12px;
        font-weight: 900;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #fbbf24;
        line-height: 1.2;
    }

    /* === STAT KPI CARDS === */
    .rab-kpi {
        background: #ffffff;
        border-radius: 16px;
        border: 1px solid rgba(22,163,74,.12);
        box-shadow: 0 2px 10px rgba(22,163,74,.05);
        padding: 16px;
        height: 100%;
        transition: all .2s ease;
    }
    .rab-kpi:hover { transform: translateY(-3px); box-shadow: 0 10px 24px rgba(22,163,74,.12); }

    /* === FILTER CARD === */
    .filter-card {
        background: #ffffff;
        border-radius: 16px;
        border: 1px solid rgba(22,163,74,.15);
        box-shadow: 0 2px 10px rgba(22,163,74,.05);
        padding: 20px;
        margin-bottom: 24px;
    }
    .filter-card .form-control,
    .filter-card .form-select {
        border-radius: 12px;
        border: 1.5px solid #e5e7eb;
        font-size: 13px;
        padding: 9px 14px;
        background: #f9fafb;
    }

    /* === HIGHLIGHT TABLE DESIGN (PTPN Green Gradient Accent Headers) === */
    .highlight-table-card {
        background: #ffffff;
        border-radius: 20px;
        border: 1px solid rgba(22,163,74,.15);
        box-shadow: 0 4px 20px rgba(22,163,74,.08);
        overflow: hidden;
        margin-bottom: 24px;
    }

    .highlight-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 12px;
    }

    .highlight-table thead th {
        padding: 14px 12px;
        font-weight: 800;
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #ffffff;
        text-align: center;
        border: 1px solid rgba(255,255,255,.2);
        white-space: nowrap;
    }

    /* Color coded headers - Dashboard PTPN Palette */
    .th-tanggal   { background: #052e16 !important; color: #86efac !important; } /* Dark Forest */
    .th-asal      { background: #14532d !important; } /* Deep PTPN Green */
    .th-kerja     { background: #0d9488 !important; } /* Teal */
    .th-pekerjaan { background: #b45309 !important; } /* Amber / Gold */
    .th-lokasi    { background: #166534 !important; } /* PTPN Emerald */
    .th-hasil     { background: #16a34a !important; } /* Bright PTPN Green */

    .highlight-table tbody td {
        padding: 10px 12px;
        border: 1px solid #e2e8f0;
        vertical-align: middle;
        font-size: 12px;
    }

    .td-tanggal {
        background: #f0fdf4;
        font-weight: 800;
        color: #14532d;
        text-align: center;
        vertical-align: middle !important;
        font-size: 12px;
        white-space: nowrap;
        border-right: 2px solid #bbf7d0 !important;
    }

    /* PKS Akronim Badge Pills */
    .badge-pks-asal {
        display: inline-block;
        padding: 4px 10px;
        border-radius: 6px;
        font-size: 11px;
        font-weight: 800;
        background: linear-gradient(135deg, #052e16 0%, #166534 100%);
        color: #86efac;
        border: 1px solid #22c55e;
        text-align: center;
        min-width: 48px;
    }

    .badge-pks-kerja {
        display: inline-block;
        padding: 4px 10px;
        border-radius: 6px;
        font-size: 11px;
        font-weight: 800;
        background: #ccfbf1;
        color: #0f766e;
        border: 1px solid #5eead4;
        text-align: center;
        min-width: 48px;
    }

    .badge-pks-off {
        display: inline-block;
        padding: 4px 10px;
        border-radius: 6px;
        font-size: 11px;
        font-weight: 800;
        background: #fee2e2;
        color: #991b1b;
        border: 1px solid #fca5a5;
        text-align: center;
        min-width: 48px;
    }

    /* GPS Interactive Links */
    .gps-link-pill {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 3px 9px;
        border-radius: 50px;
        background: #f0fdf4;
        color: #15803d;
        border: 1px solid #86efac;
        font-size: 10px;
        font-weight: 700;
        text-decoration: none;
        transition: all .2s ease;
        margin-top: 4px;
    }
    .gps-link-pill:hover {
        background: #16a34a;
        color: #ffffff;
        border-color: #15803d;
        transform: translateY(-1px);
        box-shadow: 0 3px 8px rgba(22,163,74,.25);
    }

    .gps-link-akhir {
        background: #e0f2fe;
        color: #0369a1;
        border-color: #7dd3fc;
    }
    .gps-link-akhir:hover {
        background: #0284c7;
        color: #ffffff;
        border-color: #0369a1;
        box-shadow: 0 3px 8px rgba(2,132,199,.25);
    }

    .text-hasil {
        font-weight: 700;
        color: #0f172a;
    }

    /* Footer AKHLAK */
    .akhlak-footer {
        background: linear-gradient(135deg, #052e16 0%, #14532d 100%);
        color: #ffffff;
        padding: 12px 24px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        font-size: 11px;
        font-weight: 700;
        border-radius: 0 0 20px 20px;
    }
    .akhlak-tag { color: #86efac; font-weight: 800; letter-spacing: 0.5px; }

    /* Dark Mode */
    html.app-skin-dark .highlight-table-card,
    html.app-skin-dark .filter-card,
    html.app-skin-dark .rab-kpi { background: #0a2317 !important; border-color: rgba(34,197,94,.15) !important; }
    html.app-skin-dark .highlight-table tbody td { border-color: rgba(34,197,94,.1) !important; color: #d1fae5; }
    html.app-skin-dark .td-tanggal { background: #0e3b26 !important; color: #86efac !important; border-right-color: rgba(34,197,94,.2) !important; }
    html.app-skin-dark .gps-link-pill { background: #0e3b26; color: #86efac; border-color: rgba(34,197,94,.3); }
    html.app-skin-dark .header-oprasional{background: #0e3b26 !important;}
    /* ================================================================
       PRINT STYLES — PTPN Green Infografis Print Format
       ================================================================ */
    @media print {
        @page { size: A4 landscape; margin: 4mm 6mm 6mm 6mm; }
        html, body { width: 100% !important; margin: 0 !important; padding: 0 !important; background: #fff !important; color: #000 !important; -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; }
        .nxl-navigation, .nxl-header, .filter-card, .no-print,
        .page-hero-strip, .page-header, .breadcrumb, .nxl-footer,
        .rab-kpi, .btn, .btn-ptpn, .nav-tabs-custom { display: none !important; }
        .main-content, .nxl-container, .nxl-content { padding: 0 !important; margin: 0 !important; }
        .print-only { display: block !important; }

        .print-banner {
            background: linear-gradient(135deg, #052e16 0%, #166534 60%, #16a34a 100%) !important;
            color: #ffffff !important;
            padding: 14px 20px !important;
            border-radius: 12px !important;
            margin-bottom: 10px !important;
            display: flex !important;
            align-items: center !important;
            justify-content: space-between !important;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        .print-title {
            font-size: 22px !important;
            font-weight: 900 !important;
            text-transform: uppercase !important;
            color: #ffffff !important;
            margin: 0 !important;
        }
        .print-title span { color: #fbbf24 !important; }

        .print-period-pill {
            background: #b45309 !important;
            color: #ffffff !important;
            padding: 4px 12px !important;
            border-radius: 30px !important;
            font-size: 11px !important;
            font-weight: 800 !important;
            display: inline-block !important;
            margin-top: 4px !important;
        }

        .print-table {
            width: 100% !important;
            border-collapse: collapse !important;
            margin-top: 6px !important;
        }
        .print-table th {
            padding: 7px 6px !important;
            font-size: 9.5px !important;
            font-weight: 800 !important;
            text-transform: uppercase !important;
            color: #ffffff !important;
            text-align: center !important;
            border: 1px solid #000 !important;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }
        .print-table td {
            border: 1px solid #94a3b8 !important;
            padding: 5px 7px !important;
            font-size: 9.5px !important;
            color: #000000 !important;
            vertical-align: middle !important;
        }
        .print-td-tanggal {
            background: #f0fdf4 !important;
            font-weight: 800 !important;
            color: #14532d !important;
            text-align: center !important;
            font-size: 9.5px !important;
            white-space: nowrap !important;
            border-right: 2px solid #16a34a !important;
        }

        .print-akhlak-footer {
            background: #052e16 !important;
            color: #ffffff !important;
            padding: 6px 14px !important;
            font-size: 9px !important;
            font-weight: 700 !important;
            display: flex !important;
            align-items: center !important;
            justify-content: space-between !important;
            margin-top: 10px !important;
            border-radius: 6px !important;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        .gps-link-pill {
            text-decoration: none !important;
            color: #15803d !important;
            border: 1px solid #86efac !important;
            background: #f0fdf4 !important;
            padding: 2px 5px !important;
            font-size: 8.5px !important;
        }
    }
</style>
@endsection

@section('content')

{{-- ================================================================
     PRINT CONTAINER (PTPN Green Infografis Print Format)
     ================================================================ --}}
<div class="print-only">
    <div class="print-banner">
        <div>
            <div style="font-size:9px;font-weight:800;letter-spacing:1.5px;color:#86efac;text-transform:uppercase;">
                REPORT MONITORING EXCAVATOR &amp; ALAT BERAT — PTPN IV REGIONAL III
            </div>
            <div class="print-title">
                HIGHLIGHT <span>EXCAVATOR &amp; ALAT BERAT</span>
            </div>
            <div class="print-period-pill">
                📅 {{ strtoupper($startDateStr) }} – {{ strtoupper($endDateStr) }}
            </div>
        </div>

        <div style="display:flex;align-items:center;gap:14px;">
            <div style="background:#052e16;border:2px solid #fbbf24;color:#fbbf24;padding:6px 12px;border-radius:8px;text-align:center;font-size:9.5px;font-weight:900;text-transform:uppercase;">
                KOMPAK KERJA KERAS<br>HASIL JELAS!
            </div>
        </div>
    </div>

    @if($data->count() > 0)
    <table class="print-table">
        <thead>
            <tr>
                <th class="th-tanggal" style="width:120px;">📅 TANGGAL</th>
                <th class="th-asal" style="width:75px;">ASAL EXC.</th>
                <th class="th-kerja" style="width:75px;">KERJA EXC.</th>
                <th class="th-pekerjaan">⚙️ PEKERJAAN</th>
                <th class="th-lokasi" style="min-width:160px;">📍 LOKASI &amp; GPS KOORDINAT</th>
                <th class="th-hasil" style="width:150px;">✔️ HASIL PEKERJAAN</th>
            </tr>
        </thead>
        <tbody>
            @foreach($dataByTanggal as $tgl => $itemsOnDate)
                @foreach($itemsOnDate as $idx => $item)
                <tr>
                    @if($idx === 0)
                        <td rowspan="{{ $itemsOnDate->count() }}" class="print-td-tanggal">
                            <i class="feather-calendar me-1"></i>
                            {{ strtoupper(\Carbon\Carbon::parse($tgl)->translatedFormat('j F Y')) }}
                        </td>
                    @endif

                    <td style="text-align:center;font-weight:800;">
                        <span class="badge-pks-asal">{{ $item->alatBerat && $item->alatBerat->pks ? $item->alatBerat->pks->akro : ($item->pks ? $item->pks->akro : 'N/A') }}</span>
                    </td>

                    <td style="text-align:center;font-weight:800;">
                        @if($item->kondisi_alat == 'Breakdown' || $item->kegiatan == 'OFF')
                            <span class="badge-pks-off">OFF</span>
                        @else
                            <span class="badge-pks-kerja">{{ $item->pks ? $item->pks->akro : '-' }}</span>
                        @endif
                    </td>

                    <td style="font-weight:600;">
                        {{ $item->kegiatan }}
                    </td>

                    <td>
                        <div style="font-weight:700;">{{ $item->lokasi_blok ?? ($item->kegiatan == 'OFF' ? 'OFF' : '-') }}</div>
                        @php
                            $latAwal = $item->latitude_awal ?? $item->latitude;
                            $longAwal = $item->longitude_awal ?? $item->longitude;
                            $latAkhir = $item->latitude_akhir;
                            $longAkhir = $item->longitude_akhir;
                        @endphp
                        @if(($latAwal && $longAwal) || ($latAkhir && $longAkhir))
                            <div style="margin-top:2px;">
                                @if($latAwal && $longAwal)
                                    <a href="{{ $item->google_maps_url_awal }}" target="_blank" class="gps-link-pill">
                                        GPS Awal: {{ number_format((float)$latAwal, 4) }}, {{ number_format((float)$longAwal, 4) }}
                                    </a>
                                @endif
                                @if($latAkhir && $longAkhir)
                                    <a href="{{ $item->google_maps_url_akhir }}" target="_blank" class="gps-link-pill gps-link-akhir">
                                        GPS Akhir: {{ number_format((float)$latAkhir, 4) }}, {{ number_format((float)$longAkhir, 4) }}
                                    </a>
                                @endif
                            </div>
                        @endif
                    </td>

                    <td style="font-weight:700;">
                        @if($item->flat_bed || $item->long_bed)
                            @if($item->flat_bed > 0) {{ $item->flat_bed }} FB @endif
                            @if($item->long_bed > 0) {{ $item->long_bed }} LB @endif
                        @elseif($item->kondisi_alat == 'Perlu Perbaikan')
                            <span style="color:#b45309;">Perbaikan</span>
                        @elseif($item->kondisi_alat == 'Breakdown')
                            <span style="color:#dc2626;">Breakdown</span>
                        @else
                            {{ $item->catatan ?? '-' }}
                        @endif
                    </td>
                </tr>
                @endforeach
            @endforeach
        </tbody>
    </table>

    <div class="print-akhlak-footer">
        <div><span style="color:#86efac;">#AKHLAK</span> - AMANAH, KOMPETEN, HARMONIS, LOYAL, ADAPTIF, KOLABORATIF</div>
        <div>{{ $tahun }}, PT. PERKEBUNAN NUSANTARA IV REGIONAL III</div>
    </div>
    @else
        <div style="padding:20px;text-align:center;font-size:11px;">Tidak ada data monitoring alat berat pada periode ini.</div>
    @endif
</div>


{{-- ================================================================
     WEB CONTAINER (Highlight Header PTPN Green + Filter + Interactive Table)
     ================================================================ --}}

{{-- Infografis Highlight Banner (PTPN Green Theme) --}}
<div class="highlight-banner no-print">
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
        <div>
            <div class="highlight-title-badge">
                <i class="feather-truck me-1"></i> REPORT MONITORING ALAT BERAT &bull; PTPN IV
            </div>
            <h1 class="highlight-title-main">
                HIGHLIGHT <span>EXCAVATOR &amp; ALAT BERAT</span>
            </h1>
            <div class="period-pill">
                <i class="feather-calendar"></i>
                <span>{{ strtoupper($startDateStr) }} – {{ strtoupper($endDateStr) }}</span>
            </div>
        </div>

        <div class="d-flex align-items-center gap-3">
            <div class="motto-ribbon d-none d-md-block">
                <div class="motto-ribbon-text">
                    KOMPAK KERJA KERAS<br>HASIL JELAS!
                </div>
            </div>
            <div>
                <button onclick="window.print()" class="btn-ptpn" style="padding:12px 20px;font-size:13.5px;border-radius:14px;background:linear-gradient(135deg,#d4a017,#b45309);color:#ffffff;font-weight:900;box-shadow:0 6px 20px rgba(180,83,9,.4);border:none;">
                    <i class="feather-printer me-1" style="font-size:16px;"></i>
                    Cetak Landscape Infografis
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Stat KPI Cards --}}
<div class="row g-3 mb-4 no-print">
    <div class="col-xl-3 col-sm-6">
        <div class="rab-kpi">
            <div class="d-flex align-items-center gap-3">
                <div style="width:44px;height:44px;border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:18px;background:#f0fdf4;color:#16a34a;border:1px solid #bbf7d0;">
                    <i class="feather-clock"></i>
                </div>
                <div>
                    <h3 style="font-family:'Outfit',sans-serif;font-weight:900;color:#14532d;margin:0 0 2px;">{{ \App\Models\MonitoringAlatBerat::formatHm($summary['total_hm'], true) }}</h3>
                    <span style="font-size:11.5px;color:#6b7280;font-weight:600;">Total Jam Kerja (HM)</span>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-sm-6">
        <div class="rab-kpi">
            <div class="d-flex align-items-center gap-3">
                <div style="width:44px;height:44px;border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:18px;background:#f0fdfa;color:#0d9488;border:1px solid #99f6e4;">
                    <i class="feather-grid"></i>
                </div>
                <div>
                    <h3 style="font-family:'Outfit',sans-serif;font-weight:900;color:#0d9488;margin:0 0 2px;">{{ number_format($summary['total_bed'] ?? 0) }} <span style="font-size:12px;font-weight:600;color:#6b7280;">Bed</span></h3>
                    <span style="font-size:11px;color:#6b7280;font-weight:600;">Flat: {{ number_format($summary['total_flat_bed'] ?? 0) }} | Long: {{ number_format($summary['total_long_bed'] ?? 0) }}</span>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-sm-6">
        <div class="rab-kpi">
            <div class="d-flex align-items-center gap-3">
                <div style="width:44px;height:44px;border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:18px;background:#fffbeb;color:#b45309;border:1px solid #fde68a;">
                    <i class="feather-droplet"></i>
                </div>
                <div>
                    <h3 style="font-family:'Outfit',sans-serif;font-weight:900;color:#b45309;margin:0 0 2px;">{{ number_format($summary['total_bbm'], 0) }} <span style="font-size:12px;font-weight:600;color:#6b7280;">L</span></h3>
                    <span style="font-size:11.5px;color:#6b7280;font-weight:600;">Total Konsumsi BBM</span>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-sm-6">
        <div class="rab-kpi">
            <div class="d-flex align-items-center gap-3">
                <div style="width:44px;height:44px;border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:18px;background:#eff6ff;color:#1d4ed8;border:1px solid #bfdbfe;">
                    <i class="feather-activity"></i>
                </div>
                <div>
                    <h3 style="font-family:'Outfit',sans-serif;font-weight:900;color:#1d4ed8;margin:0 0 2px;">{{ $summary['total_kegiatan'] }} <span style="font-size:12px;font-weight:600;color:#6b7280;">Log</span></h3>
                    <span style="font-size:11.5px;color:#6b7280;font-weight:600;">Total Log Kegiatan</span>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Filter Card Web --}}
<div class="filter-card no-print">
    <form method="GET" action="{{ route('report-alat-berat') }}">
        <div class="row g-3 align-items-end">
            <div class="col-lg-3 col-md-6">
                <label class="form-label">
                    <i class="feather-home me-1" style="color:#16a34a;"></i> Unit PKS
                </label>
                <select name="id_pks" class="form-select" data-select2-selector="status">
                    <option value="">Semua PKS Unit</option>
                    @foreach($pksList as $pks)
                        <option value="{{ $pks->id_pks }}" {{ request('id_pks') == $pks->id_pks ? 'selected' : '' }}>
                            {{ $pks->nama }} ({{ $pks->akro }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-lg-3 col-md-6">
                <label class="form-label">
                    <i class="feather-truck me-1" style="color:#16a34a;"></i> Alat Berat
                </label>
                <select name="alat_berat_id" class="form-select">
                    <option value="">Semua Alat Berat</option>
                    @foreach($alatBeratList as $ab)
                        <option value="{{ $ab->id }}" {{ request('alat_berat_id') == $ab->id ? 'selected' : '' }}>
                            {{ $ab->kode_alat }} - {{ $ab->nama_alat }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-lg-2 col-md-6">
                <label class="form-label">
                    <i class="feather-calendar me-1" style="color:#16a34a;"></i> Bulan
                </label>
                <select name="bulan" class="form-select">
                    @for($m = 1; $m <= 12; $m++)
                        <option value="{{ sprintf('%02d', $m) }}" {{ $bulan == sprintf('%02d', $m) ? 'selected' : '' }}>
                            {{ $namaBulan[$m] }}
                        </option>
                    @endfor
                </select>
            </div>

            <div class="col-lg-2 col-md-6">
                <label class="form-label">
                    <i class="feather-calendar me-1" style="color:#16a34a;"></i> Tahun
                </label>
                <select name="tahun" class="form-select">
                    @foreach($years as $y)
                        <option value="{{ $y }}" {{ $tahun == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-lg-2 col-md-12">
                <label class="form-label">&nbsp;</label>
                <div class="d-flex gap-2">
                    <button type="submit" class="btn-ptpn btn-ptpn-primary flex-grow-1" style="padding:10px 14px;font-size:13px;border-radius:12px;">
                        <i class="feather-search" style="font-size:14px;"></i> Tampilkan
                    </button>
                    <a href="{{ route('report-alat-berat') }}" class="btn-ptpn btn-ptpn-outline" style="padding:10px 12px;border-radius:12px;" title="Reset">
                        <i class="feather-refresh-cw" style="font-size:14px;"></i>
                    </a>
                </div>
            </div>
        </div>
    </form>
</div>

<!-- Highlight Table Web (Identik Format Gambar + PTPN Green Palette & GPS Links) -->
<div class="highlight-table-card no-print">
    <div class="header-oprasional" style="padding:16px 20px;border-bottom:1px solid rgba(22,163,74,.15);display:flex;align-items:center;justify-content:space-between;background:linear-gradient(135deg,#f0fdf4 0%,#dcfce7 100%);">
        <h4 style="font-family:'Outfit',sans-serif;font-size:15px;font-weight:800;color:#14532d;margin:0;display:flex;align-items:center;gap:8px;">
            <i class="feather-calendar" style="color:#16a34a;font-size:18px;"></i>
            Highlight Operasional &amp; Koordinat GPS per Tanggal — {{ $namaBulan[(int)$bulan] }} {{ $tahun }}
        </h4>
        <span class="mod-pill mod-pill-ok" style="font-size:11px;">
            <i class="feather-check-circle" style="font-size:11px;"></i>
            {{ $summary['total_kegiatan'] }} Activity Logs
        </span>
    </div>

    <div class="card-body p-0">
        @if($data->count() > 0)
        <div class="table-responsive">
            <table class="highlight-table mb-0">
                <thead>
                    <tr>
                        <th class="th-tanggal" style="width:140px;">📅 TANGGAL</th>
                        <th class="th-asal" style="width:90px;">ASAL EXC.</th>
                        <th class="th-kerja" style="width:90px;">KERJA EXC.</th>
                        <th class="th-pekerjaan">⚙️ PEKERJAAN</th>
                        <th class="th-lokasi" style="min-width:210px;">📍 LOKASI &amp; GPS MAPS</th>
                        <th class="th-hasil" style="min-width:180px;">✔️ HASIL PEKERJAAN</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($dataByTanggal as $tgl => $itemsOnDate)
                        @foreach($itemsOnDate as $idx => $item)
                        <tr>
                            @if($idx === 0)
                                <td rowspan="{{ $itemsOnDate->count() }}" class="td-tanggal">
                                    <i class="feather-calendar me-1" style="color:#16a34a;"></i>
                                    {{ strtoupper(\Carbon\Carbon::parse($tgl)->translatedFormat('j F Y')) }}
                                </td>
                            @endif

                            <td style="text-align:center;">
                                <span class="badge-pks-asal">
                                    {{ $item->alatBerat && $item->alatBerat->pks ? $item->alatBerat->pks->akro : ($item->pks ? $item->pks->akro : 'N/A') }}
                                </span>
                            </td>

                            <td style="text-align:center;">
                                @if($item->kondisi_alat == 'Breakdown' || $item->kegiatan == 'OFF')
                                    <span class="badge-pks-off">OFF</span>
                                @else
                                    <span class="badge-pks-kerja">{{ $item->pks ? $item->pks->akro : '-' }}</span>
                                @endif
                            </td>

                            <td>
                                <div style="font-weight:700;">{{ $item->kegiatan }}</div>
                                <small style="color:;font-size:11px;">
                                    Unit: {{ $item->alatBerat ? $item->alatBerat->kode_alat : '-' }} ({{ $item->operator }})
                                </small>
                            </td>

                            <td>
                                <div style="font-weight:700;color:;">
                                    {{ $item->lokasi_blok ?? ($item->kegiatan == 'OFF' ? 'OFF' : '-') }}
                                </div>
                                @php
                                    $latAwal = $item->latitude_awal ?? $item->latitude;
                                    $longAwal = $item->longitude_awal ?? $item->longitude;
                                    $latAkhir = $item->latitude_akhir;
                                    $longAkhir = $item->longitude_akhir;
                                @endphp
                                @if(($latAwal && $longAwal) || ($latAkhir && $longAkhir))
                                    <div class="d-flex flex-wrap gap-1 mt-1">
                                        @if($latAwal && $longAwal)
                                            <a href="{{ $item->google_maps_url_awal }}" target="_blank" class="gps-link-pill" title="Buka GPS Awal di Google Maps">
                                                <i class="feather-navigation" style="font-size:10px;"></i>
                                                Awal: {{ number_format((float)$latAwal, 4) }}, {{ number_format((float)$longAwal, 4) }}
                                            </a>
                                        @endif
                                        @if($latAkhir && $longAkhir)
                                            <a href="{{ $item->google_maps_url_akhir }}" target="_blank" class="gps-link-pill gps-link-akhir" title="Buka GPS Akhir di Google Maps">
                                                <i class="feather-navigation" style="font-size:10px;"></i>
                                                Akhir: {{ number_format((float)$latAkhir, 4) }}, {{ number_format((float)$longAkhir, 4) }}
                                            </a>
                                        @endif
                                    </div>
                                @else
                                    <small style="color:#9ca3af;font-size:10px;" class="d-block mt-0.5">GPS tidak direkam</small>
                                @endif
                            </td>

                            <td>
                                <div class="text-hasil">
                                    @if($item->flat_bed || $item->long_bed)
                                        @if($item->flat_bed > 0) <span class="badge bg-soft-info text-info me-1" style="font-size:11px;">{{ $item->flat_bed }} FB</span> @endif
                                        @if($item->long_bed > 0) <span class="badge bg-soft-warning text-warning" style="font-size:11px;">{{ $item->long_bed }} LB</span> @endif
                                    @elseif($item->kondisi_alat == 'Perlu Perbaikan')
                                        <span class="mod-pill mod-pill-warn" style="font-size:10.5px;">Perbaikan</span>
                                    @elseif($item->kondisi_alat == 'Breakdown')
                                        <span class="mod-pill mod-pill-err" style="font-size:10.5px;">Breakdown</span>
                                    @else
                                        <span style="font-size:11.5px;color:#475569;">{{ $item->catatan ?? '-' }}</span>
                                    @endif
                                </div>
                                <small style="color:;font-size:10.5px;" class="d-block mt-1">
                                    HM: {{ $item->total_hm_formatted }} | BBM: {{ number_format($item->bbm_liter, 0) }}L
                                </small>
                            </td>
                        </tr>
                        @endforeach
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="akhlak-footer no-print">
            <div><span class="akhlak-tag">#AKHLAK</span> - AMANAH, KOMPETEN, HARMONIS, LOYAL, ADAPTIF, KOLABORATIF</div>
            <div>{{ $tahun }}, PT. PERKEBUNAN NUSANTARA IV REGIONAL III</div>
        </div>
        @else
        <div class="empty-report">
            <i class="feather-inbox fs-3 d-block mb-2"></i>
            Tidak ada data monitoring alat berat pada periode ini.
        </div>
        @endif
    </div>
</div>

@endsection

@section('scripts')
<!-- <script src="{{ asset('duraluxadmin/assets/vendors/js/select2.min.js') }}"></script> -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof $ !== 'undefined' && $.fn.select2) {
            $('[data-select2-selector]').select2({ width: '100%' });
        }
    });
</script>
@endsection
