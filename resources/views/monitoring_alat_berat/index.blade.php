@extends('layouts.simoli')

@section('title', 'Monitoring Alat Berat')
@section('page-title', 'Monitoring Log Alat Berat')
@section('page-description', 'Pencatatan & Monitoring Jam Kerja (HM) Alat Berat Pengolahan Limbah')

@section('breadcrumb')
    <li>Input Data</li>
    <li class="separator">/</li>
    <li>Monitoring Alat Berat</li>
@endsection

@section('page-actions')
    <a href="{{ route('report-alat-berat') }}" class="btn-ptpn btn-ptpn-outline me-2">
        <i class="feather-printer" style="font-size:15px;"></i>
        <span>Export / Cetak PDF</span>
    </a>
    @if(Auth::user()->isUnit())
    <a href="{{ route('monitoring-alat-berat.create') }}" class="btn-ptpn btn-ptpn-primary">
        <i class="feather-plus" style="font-size:15px;"></i>
        <span>Tambah Log Monitoring</span>
    </a>
    @endif
@endsection

@section('styles')
<link rel="stylesheet" type="text/css" href="{{ asset('duraluxadmin/assets/vendors/css/select2.min.css') }}" />
<link rel="stylesheet" type="text/css" href="{{ asset('duraluxadmin/assets/vendors/css/select2-theme.min.css') }}" />
<style>
    /* ================================================================
       MONITORING ALAT BERAT INDEX — PTPN GREEN THEME
       ================================================================ */
    @keyframes fadeUpCard {
        from { opacity:0; transform:translateY(14px); }
        to   { opacity:1; transform:translateY(0); }
    }

    /* === KPI SUMMARY CARDS === */
    .mab-kpi {
        background: #ffffff;
        border-radius: 18px;
        border: 1px solid rgba(22,163,74,.1);
        box-shadow: 0 2px 12px rgba(22,163,74,.06);
        padding: 18px 20px;
        height: 100%;
        transition: all .25s ease;
        position: relative;
        overflow: hidden;
        animation: fadeUpCard .4s ease-out both;
    }
    .mab-kpi::after {
        content:'';position:absolute;top:0;left:0;right:0;height:3px;border-radius:18px 18px 0 0;
    }
    .mab-kpi-green::after  { background: linear-gradient(90deg,#16a34a,#4ade80); }
    .mab-kpi-amber::after  { background: linear-gradient(90deg,#d97706,#fbbf24); }
    .mab-kpi-teal::after   { background: linear-gradient(90deg,#0d9488,#2dd4bf); }
    .mab-kpi-blue::after   { background: linear-gradient(90deg,#1d4ed8,#60a5fa); }
    
    .mab-kpi:hover { transform:translateY(-4px); box-shadow:0 12px 28px rgba(22,163,74,.12); }

    .mab-kpi-label { font-size:10.5px;font-weight:700;color:;text-transform:uppercase;letter-spacing:.5px;margin-bottom:5px; }
    .mab-kpi-value { font-family:'Outfit',sans-serif;font-size:clamp(18px,1.8vw+8px,24px);font-weight:900;line-height:1.1;letter-spacing:-.5px;margin-bottom:6px; }
    .mab-kpi-sub   { font-size:11px;color:; }
    .mab-kpi-icon  { width:44px;height:44px;border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:18px;flex-shrink:0; }
    
    .kpi-icon-g { background:#f0fdf4;color:#16a34a;border:1px solid #bbf7d0; }
    .kpi-icon-a { background:#fffbeb;color:#b45309;border:1px solid #fde68a; }
    .kpi-icon-t { background:#f0fda4;color:#0d9488;border:1px solid #99f6e4; }
    .kpi-icon-b { background:#eff6ff;color:#1d4ed8;border:1px solid #bfdbfe; }

    /* === FILTER CARD === */
    .mab-filter {
        background: #ffffff;
        border-radius: 16px;
        border: 1px solid rgba(22,163,74,.15);
        box-shadow: 0 2px 10px rgba(22,163,74,.05);
        padding: 18px 20px;
        margin-bottom: 20px;
    }

    .mab-filter .form-control,
    .mab-filter .form-select {
        border-radius: 12px;
        border: 1.5px solid #e5e7eb;
        font-size: 13px;
        padding: 9px 14px;
        transition: all .2s ease;
        background: #f9fafb;
    }

    .mab-filter .form-control:focus,
    .mab-filter .form-select:focus {
        border-color: rgba(22,163,74,.5);
        background: #ffffff;
        box-shadow: 0 0 0 3px rgba(34,197,94,.08);
    }

    .mab-filter label { font-size:11.5px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.4px;margin-bottom:5px; }

    .mab-filter .select2-container--default .select2-selection--single {
        height: 42px; border-radius: 12px; border: 1.5px solid #e5e7eb;
        background: #f9fafb; padding: 6px 12px;
    }
    .mab-filter .select2-container--default .select2-selection--single .select2-selection__rendered { line-height: 28px; font-size: 13px; }
    .mab-filter .select2-container--default .select2-selection--single .select2-selection__arrow { height: 40px; }

    /* === DATA TABLE CARD === */
    .mab-table-card {
        background: #ffffff;
        border-radius: 18px;
        border: 1px solid rgba(22,163,74,.1);
        box-shadow: 0 2px 12px rgba(22,163,74,.06);
        overflow: hidden;
    }

    .mab-table-header {
        padding: 16px 20px;
        border-bottom: 1px solid rgba(22,163,74,.08);
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 10px;
    }

    .mab-table-title {
        font-family: 'Outfit', sans-serif;
        font-size: 15px;
        font-weight: 800;
        color: #14532d;
        display: flex;
        align-items: center;
        gap: 8px;
        margin: 0;
    }

    .mab-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
        font-size: clamp(11.5px, .65vw+8.5px, 13px);
    }

    .mab-table thead th {
        background: #052e16;
        color: #86efac;
        font-size: 10px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .6px;
        padding: 12px 16px;
        border: none;
        white-space: nowrap;
    }

    .mab-table tbody td {
        padding: 12px 16px;
        border-bottom: 1px solid rgba(22,163,74,.07);
        vertical-align: middle;
    }

    .mab-table tbody tr { transition: background .15s ease; }
    .mab-table tbody tr:hover td { background: rgba(22,163,74,.03); }
    .mab-table tbody tr:last-child td { border-bottom: none; }

    /* Action btns */
    .tbl-action { display:inline-flex;align-items:center;justify-content:center;width:32px;height:32px;border-radius:9px;border:none;cursor:pointer;transition:all .2s ease;font-size:14px; }
    .tbl-action-view   { background:;color:#16a34a;border:1px solid #bbf7d0; }
    .tbl-action-edit   { background:#eff6ff;color:#1d4ed8;border:1px solid #bfdbfe; }
    .tbl-action-delete { background:#fef2f2;color:#dc2626;border:1px solid #fca5a5; }
    /* .tbl-action-view:hover   { background:#dcfce7;transform:scale(1.1); } */
    .tbl-action-edit:hover   { background:#dbeafe;transform:scale(1.1); }
    .tbl-action-delete:hover { background:#fee2e2;transform:scale(1.1); }

    /* Empty state */
    .empty-mab {
        padding: 60px 24px;
        text-align: center;
    }
    .empty-mab-icon { font-size: 52px; color: #d1d5db; margin-bottom: 14px; }

    /* Pagination */
    .mab-pagination { display:flex;align-items:center;justify-content:space-between;padding:14px 20px;border-top:1px solid rgba(22,163,74,.08);flex-wrap:wrap;gap:10px; }

    /* Dark mode */
    html.app-skin-dark .mab-kpi,
    html.app-skin-dark .mab-filter,
    html.app-skin-dark .mab-table-card { background:#0a2317 !important; border-color:rgba(34,197,94,.15) !important; }
    html.app-skin-dark .mab-table-title,
    html.app-skin-dark .mab-kpi-value { color:#d1fae5 !important; }
    html.app-skin-dark .mab-table thead th { background:#021a0b !important; }
    html.app-skin-dark .mab-table tbody td { border-color:rgba(34,197,94,.07) !important; color:#d1fae5; }
    html.app-skin-dark .mab-table tbody tr:hover td { background:rgba(34,197,94,.04) !important; }
    html.app-skin-dark .mab-filter .form-control,
    html.app-skin-dark .mab-filter .form-select { background:#0e3b26;border-color:rgba(34,197,94,.2);color:#d1fae5; }
</style>
@endsection

@section('content')
<article class="monitoring-alat-berat-index">

    {{-- ================================================================
         1. KPI SUMMARY CARDS
         ================================================================ --}}
    <section aria-label="Ringkasan Monitoring Alat Berat" class="mb-4">
        <div class="row g-3">
            <div class="col-xl-3 col-sm-6" style="animation-delay:.03s">
                <div class="mab-kpi mab-kpi-green">
                    <div class="d-flex align-items-start justify-content-between mb-2">
                        <div>
                            <div class="mab-kpi-label">Total Jam Kerja (HM)</div>
                            <div class="mab-kpi-value" style="color:#16a34a;">{{ \App\Models\MonitoringAlatBerat::formatHm($totalHm, true) }}</div>
                        </div>
                        <div class="mab-kpi-icon kpi-icon-g"><i class="feather-clock"></i></div>
                    </div>
                    <div class="mab-kpi-sub">total jam operasional</div>
                </div>
            </div>

            <div class="col-xl-3 col-sm-6" style="animation-delay:.06s">
                <div class="mab-kpi mab-kpi-amber">
                    <div class="d-flex align-items-start justify-content-between mb-2">
                        <div>
                            <div class="mab-kpi-label">Konsumsi BBM</div>
                            <div class="mab-kpi-value" style="color:#b45309;">{{ number_format($totalBbm, 0) }} <span style="font-size:13px;font-weight:600;">Liter</span></div>
                        </div>
                        <div class="mab-kpi-icon kpi-icon-a"><i class="feather-droplet"></i></div>
                    </div>
                    <div class="mab-kpi-sub">total BBM digunakan</div>
                </div>
            </div>

            <div class="col-xl-3 col-sm-6" style="animation-delay:.09s">
                <div class="mab-kpi mab-kpi-teal">
                    <div class="d-flex align-items-start justify-content-between mb-2">
                        <div>
                            <div class="mab-kpi-label">Total Bed Dikerjakan</div>
                            <div class="mab-kpi-value" style="color:#0d9488;">{{ number_format($totalBed ?? 0) }} <span style="font-size:13px;font-weight:600;">Bed</span></div>
                        </div>
                        <div class="mab-kpi-icon kpi-icon-t"><i class="feather-grid"></i></div>
                    </div>
                    <div class="mab-kpi-sub">Flat: {{ number_format($totalFlatBed ?? 0) }} | Long: {{ number_format($totalLongBed ?? 0) }}</div>
                </div>
            </div>

            <div class="col-xl-3 col-sm-6" style="animation-delay:.12s">
                <div class="mab-kpi mab-kpi-blue">
                    <div class="d-flex align-items-start justify-content-between mb-2">
                        <div>
                            <div class="mab-kpi-label">Kegiatan Operasional</div>
                            <div class="mab-kpi-value" style="color:#1d4ed8;">{{ $totalKegiatan }} <span style="font-size:13px;font-weight:600;">Log</span></div>
                        </div>
                        <div class="mab-kpi-icon kpi-icon-b"><i class="feather-activity"></i></div>
                    </div>
                    <div class="mab-kpi-sub">total transaksi log</div>
                </div>
            </div>
        </div>
    </section>

    {{-- ================================================================
         2. FILTER BAR
         ================================================================ --}}
    <div class="mab-filter no-print">
        <form method="GET" action="{{ route('monitoring-alat-berat.index') }}">
            <div class="row g-3 align-items-end">
                @if(Auth::user()->isAdmin())
                <div class="col-lg-3 col-md-6">
                    <label><i class="feather-home me-1" style="color:#16a34a;"></i> Unit PKS</label>
                    <select name="id_pks" class="form-select" data-select2-selector="status">
                        <option value="">Semua PKS</option>
                        @foreach($pksList as $pks)
                            <option value="{{ $pks->id_pks }}" {{ request('id_pks') == $pks->id_pks ? 'selected' : '' }}>
                                {{ $pks->nama }} ({{ $pks->akro }})
                            </option>
                        @endforeach
                    </select>
                </div>
                @endif

                <div class="col-lg-3 col-md-6">
                    <label><i class="feather-truck me-1" style="color:#16a34a;"></i> Alat Berat</label>
                    <select name="alat_berat_id" class="form-select">
                        <option value="">Semua Alat Berat</option>
                        @foreach($alatBeratList as $ab)
                            <option value="{{ $ab->id }}" {{ request('alat_berat_id') == $ab->id ? 'selected' : '' }}>
                                {{ $ab->kode_alat }} - {{ $ab->nama_alat }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-lg-2 col-md-4 col-6">
                    <label><i class="feather-calendar me-1" style="color:#16a34a;"></i> Bulan</label>
                    <select name="bulan" class="form-select">
                        <option value="">Semua Bulan</option>
                        @for($m=1; $m<=12; $m++)
                            <option value="{{ sprintf('%02d', $m) }}" {{ request('bulan') == sprintf('%02d', $m) ? 'selected' : '' }}>
                                {{ date('F', mktime(0, 0, 0, $m, 1)) }}
                            </option>
                        @endfor
                    </select>
                </div>

                <div class="col-lg-2 col-md-4 col-6">
                    <label><i class="feather-hash me-1" style="color:#16a34a;"></i> Tahun</label>
                    <select name="tahun" class="form-select">
                        <option value="">Semua Tahun</option>
                        @for($y=date('Y'); $y>=2024; $y--)
                            <option value="{{ $y }}" {{ request('tahun') == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endfor
                    </select>
                </div>

                <div class="col-lg-2 col-md-4">
                    <label><i class="feather-activity me-1" style="color:#16a34a;"></i> Kondisi</label>
                    <select name="kondisi_alat" class="form-select">
                        <option value="">Semua Kondisi</option>
                        <option value="Normal" {{ request('kondisi_alat') == 'Normal' ? 'selected' : '' }}>Normal</option>
                        <option value="Perlu Perbaikan" {{ request('kondisi_alat') == 'Perlu Perbaikan' ? 'selected' : '' }}>Perlu Perbaikan</option>
                        <option value="Breakdown" {{ request('kondisi_alat') == 'Breakdown' ? 'selected' : '' }}>Breakdown</option>
                    </select>
                </div>

                <div class="col-12 text-end">
                    <div class="d-inline-flex gap-2">
                        <button type="submit" class="btn-ptpn btn-ptpn-primary" style="padding:9px 18px;">
                            <i class="feather-filter" style="font-size:14px;"></i> Filter
                        </button>
                        <a href="{{ route('monitoring-alat-berat.index') }}" class="btn-ptpn btn-ptpn-outline" style="padding:9px 14px;">
                            <i class="feather-refresh-cw" style="font-size:14px;"></i> Reset
                        </a>
                    </div>
                </div>
            </div>
        </form>
    </div>

    {{-- ================================================================
         3. DATA TABLE
         ================================================================ --}}
    <div class="mab-table-card">
        <div class="mab-table-header">
            <h3 class="mab-table-title">
                <i class="feather-truck" style="color:#16a34a;font-size:18px;"></i>
                Riwayat Operasional Alat Berat
            </h3>
            <div class="d-flex align-items-center gap-3">
                <small style="color:#6b7280;font-size:11.5px;">
                    Menampilkan {{ $logs->firstItem() ?? 0 }}–{{ $logs->lastItem() ?? 0 }}
                    dari <strong>{{ $logs->total() }}</strong> data
                </small>
                <span class="mod-pill mod-pill-ok" style="font-size:10.5px;">
                    <i class="feather-database" style="font-size:11px;"></i>
                    {{ $logs->total() }} Total
                </span>
            </div>
        </div>

        @if($logs->count() > 0)
        <div class="table-responsive" style="-webkit-overflow-scrolling: touch;">
            <table class="mab-table" role="table" aria-label="Daftar Log Monitoring">
                <thead>
                    <tr>
                        <th style="width:40px;text-align:center;">#</th>
                        <th style="min-width:100px;">Tanggal</th>
                        @if(Auth::user()->isAdmin())
                        <th style="width:70px;text-align:center;">PKS</th>
                        @endif
                        <th style="min-width:140px;">Alat Berat</th>
                        <th style="min-width:110px;">Operator</th>
                        <th style="min-width:160px;">Kegiatan Pengolahan</th>
                        <th style="min-width:110px;">Bed (F/L)</th>
                        <th style="min-width:120px;">HM (Awal - Akhir)</th>
                        <th style="min-width:90px;text-align:right;">Total HM</th>
                        <th style="min-width:80px;text-align:right;">BBM (L)</th>
                        <th style="width:110px;text-align:center;">Kondisi</th>
                        <th style="width:80px;text-align:center;">Foto</th>
                        <th style="width:100px;text-align:center;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($logs as $index => $log)
                    <tr>
                        <td style="text-align:center;font-weight:700;color:#9ca3af;font-size:12px;">
                            {{ $logs->firstItem() + $index }}
                        </td>

                        <td>
                            <div style="font-weight:700;font-size:13px;">
                                {{ \Carbon\Carbon::parse($log->tanggal)->format('d/m/Y') }}
                            </div>
                        </td>

                        @if(Auth::user()->isAdmin())
                        <td style="text-align:center;">
                            <span class="mod-pill mod-pill-ok" style="font-size:10px;">
                                {{ $log->pks ? $log->pks->akro : '-' }}
                            </span>
                        </td>
                        @endif

                        <td>
                            <div style="font-weight:800;color:#16a34a;font-size:13px;">{{ $log->alatBerat ? $log->alatBerat->kode_alat : '-' }}</div>
                            <small style="font-size:11px;">{{ $log->alatBerat ? $log->alatBerat->nama_alat : '-' }}</small>
                        </td>

                        <td>
                            <span style="font-weight:600;">{{ $log->operator }}</span>
                        </td>

                        <td>
                            <div style="font-weight:700;">{{ $log->kegiatan }}</div>
                            @if($log->lokasi_blok)
                                <small style="font-size:11px;"><i class="feather-map-pin me-1"></i>{{ $log->lokasi_blok }}</small>
                            @endif
                            @php
                                $latAwal = $log->latitude_awal ?? $log->latitude;
                                $longAwal = $log->longitude_awal ?? $log->longitude;
                                $latAkhir = $log->latitude_akhir;
                                $longAkhir = $log->longitude_akhir;
                            @endphp
                            @if($latAwal || $latAkhir)
                                <div style="margin-top:4px;" class="d-flex flex-wrap gap-1">
                                    @if($latAwal && $longAwal)
                                        <a href="{{ $log->google_maps_url_awal }}" target="_blank" class="mod-pill mod-pill-info" style="font-size:9.5px;text-decoration:none;" title="GPS Awal Kerja">
                                            <i class="feather-navigation me-1"></i>Awal: {{ number_format((float)$latAwal, 4) }}, {{ number_format((float)$longAwal, 4) }}
                                        </a>
                                    @endif
                                    @if($latAkhir && $longAkhir)
                                        <a href="{{ $log->google_maps_url_akhir }}" target="_blank" class="mod-pill mod-pill-ok" style="font-size:9.5px;text-decoration:none;" title="GPS Akhir Kerja">
                                            <i class="feather-navigation me-1"></i>Akhir: {{ number_format((float)$latAkhir, 4) }}, {{ number_format((float)$longAkhir, 4) }}
                                        </a>
                                    @endif
                                </div>
                            @endif
                        </td>

                        <td>
                            <span class="mod-pill mod-pill-info" style="font-size:10.5px;">
                                F: {{ $log->flat_bed ?? 0 }} | L: {{ $log->long_bed ?? 0 }}
                            </span>
                        </td>

                        <td>
                            <small style="font-weight:600;color:;">{{ $log->hm_awal_formatted }} - {{ $log->hm_akhir_formatted }}</small>
                        </td>

                        <td style="text-align:right;font-weight:800;color:#16a34a;">
                            {{ $log->total_hm_formatted }} Jam
                        </td>

                        <td style="text-align:right;font-weight:800;color:#b45309;">
                            {{ number_format($log->bbm_liter, 0) }} L
                        </td>

                        <td style="text-align:center;">
                            @if($log->kondisi_alat == 'Normal')
                                <span class="mod-pill mod-pill-ok" style="font-size:10.5px;">Normal</span>
                            @elseif($log->kondisi_alat == 'Perlu Perbaikan')
                                <span class="mod-pill mod-pill-warn" style="font-size:10.5px;">Perlu Perbaikan</span>
                            @else
                                <span class="mod-pill mod-pill-err" style="font-size:10.5px;">Breakdown</span>
                            @endif
                        </td>

                        <td style="text-align:center;">
                            <div class="d-flex justify-content-center gap-1">
                                @if($log->foto_sebelum_url)
                                    <a href="{{ $log->foto_sebelum_url }}" target="_blank" title="Foto Sebelum Kerja">
                                        <img src="{{ $log->foto_sebelum_url }}" alt="Sebelum" style="width:34px;height:34px;object-fit:cover;border-radius:6px;border:1px solid rgba(22,163,74,.2);">
                                    </a>
                                @endif
                                @if($log->foto_sesudah_url)
                                    <a href="{{ $log->foto_sesudah_url }}" target="_blank" title="Foto Sesudah Kerja">
                                        <img src="{{ $log->foto_sesudah_url }}" alt="Sesudah" style="width:34px;height:34px;object-fit:cover;border-radius:6px;border:1px solid rgba(22,163,74,.2);">
                                    </a>
                                @endif
                                @if(!$log->foto_sebelum && !$log->foto_sesudah && $log->foto_url)
                                    <a href="{{ $log->foto_url }}" target="_blank" title="Foto Laporan">
                                        <img src="{{ $log->foto_url }}" alt="Foto" style="width:34px;height:34px;object-fit:cover;border-radius:6px;border:1px solid rgba(22,163,74,.2);">
                                    </a>
                                @endif
                                @if(!$log->foto_sebelum && !$log->foto_sesudah && !$log->foto)
                                    <span style="color:#9ca3af;font-size:11px;">-</span>
                                @endif
                            </div>
                        </td>

                        <td style="text-align:center;">
                            <div class="d-flex gap-1 justify-content-center">
                                <a href="{{ route('monitoring-alat-berat.show', $log->id) }}" class="tbl-action tbl-action-view" title="Detail">
                                    <i class="feather-eye"></i>
                                </a>
                                @if(Auth::user()->isUnit())
                                    <a href="{{ route('monitoring-alat-berat.edit', $log->id) }}" class="tbl-action tbl-action-edit" title="Edit">
                                        <i class="feather-edit-2"></i>
                                    </a>
                                    <form action="{{ route('monitoring-alat-berat.destroy', $log->id) }}" method="POST" class="d-inline form-delete">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="tbl-action tbl-action-delete btn-delete" title="Hapus">
                                            <i class="feather-trash-2"></i>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div class="mab-pagination">
            <small style="color:#6b7280;font-size:11.5px;">
                Halaman <strong>{{ $logs->currentPage() }}</strong> dari <strong>{{ $logs->lastPage() }}</strong>
            </small>
            <div>{{ $logs->links() }}</div>
        </div>

        @else
        <div class="empty-mab">
            <div class="empty-mab-icon"><i class="feather-inbox"></i></div>
            <h5 style="color:#374151;font-family:'Outfit',sans-serif;font-weight:800;margin-bottom:8px;">Belum Ada Log Monitoring</h5>
            <p style="color:#6b7280;font-size:13.5px;margin-bottom:20px;">Data log monitoring alat berat belum tersedia.</p>
            @if(Auth::user()->isUnit())
            <a href="{{ route('monitoring-alat-berat.create') }}" class="btn-ptpn btn-ptpn-primary" style="display:inline-flex;">
                <i class="feather-plus" style="font-size:15px;"></i>
                Tambah Log Pertama
            </a>
            @endif
        </div>
        @endif
    </div>

</article>
@endsection

@section('scripts')
<!-- <script src="{{ asset('duraluxadmin/assets/vendors/js/select2.min.js') }}"></script> -->
<script src="{{ asset('duraluxadmin/assets/vendors/js/select2-active.min.js') }}"></script>
<script src="{{ asset('duraluxadmin/assets/vendors/js/sweetalert2.min.js') }}"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    if (typeof $ !== 'undefined' && $.fn.select2) {
        $('[data-select2-selector]').select2({ width: '100%' });
    }

    document.querySelectorAll('.btn-delete').forEach(function(btn) {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const form = this.closest('form');
            Swal.fire({
                title: 'Hapus Log Monitoring?',
                text: 'Data yang dihapus tidak dapat dikembalikan!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc2626',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal',
                reverseButtons: true,
                borderRadius: '16px'
            }).then((result) => {
                if (result.value) {
                    form.submit();
                }
            });
        });
    });
});
</script>
@endsection
