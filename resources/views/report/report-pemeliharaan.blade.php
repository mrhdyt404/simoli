@extends('layouts.simoli')

@section('title', 'Report Pemeliharaan Land Aplikasi')
@section('page-title', 'Report Pemeliharaan')
@section('page-description', 'Laporan Data Rekapitulasi Pemeliharaan Kolam & Bed Land Aplikasi')

@section('breadcrumb')
    <li>Report</li>
    <li class="separator">/</li>
    <li>Report Pemeliharaan</li>
@endsection

@php
    $namaBulan = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
@endphp

@section('styles')
<link rel="stylesheet" type="text/css" href="{{ asset('duraluxadmin/assets/vendors/css/select2.min.css') }}" />
<link rel="stylesheet" type="text/css" href="{{ asset('duraluxadmin/assets/vendors/css/select2-theme.min.css') }}" />
<style>
    /* ================================================================
       REPORT PEMELIHARAAN — PTPN GREEN THEME
       ================================================================ */
    @keyframes fadeUpCard {
        from { opacity:0; transform:translateY(12px); }
        to   { opacity:1; transform:translateY(0); }
    }

    .print-only { display: none; }

    /* === HEADER CARD === */
    .report-header-card {
        border-radius: 20px;
        background: linear-gradient(135deg, #052e16 0%, #0a2317 40%, #166534 80%, #16a34a 100%);
        border: 1px solid rgba(34,197,94,.25);
        box-shadow: 0 10px 30px rgba(0,0,0,.12);
        color: #ffffff;
        padding: 24px 28px;
        position: relative;
        overflow: hidden;
        margin-bottom: 24px;
        animation: fadeUpCard .4s ease-out;
    }

    .report-header-card::after {
        content:'';position:absolute;top:-60px;right:-60px;
        width:220px;height:220px;border-radius:50%;
        background:radial-gradient(circle,rgba(34,197,94,.2) 0%,transparent 70%);
        pointer-events:none;
    }

    /* === STAT CARDS === */
    .rpm-kpi {
        background: #ffffff;
        border-radius: 18px;
        border: 1px solid rgba(22,163,74,.1);
        box-shadow: 0 2px 12px rgba(22,163,74,.06);
        padding: 18px;
        height: 100%;
        transition: all .25s ease;
        animation: fadeUpCard .4s ease-out;
    }
    .rpm-kpi:hover { transform: translateY(-3px); box-shadow: 0 10px 26px rgba(22,163,74,.12); }

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
        transition: all .2s ease;
        background: #f9fafb;
    }

    .filter-card .form-control:focus,
    .filter-card .form-select:focus {
        border-color: rgba(22,163,74,.4);
        background: #fff;
        box-shadow: 0 0 0 3px rgba(34,197,94,.08);
    }

    .filter-card label.form-label {
        font-size: 11px;
        font-weight: 700;
        color: #6b7280;
        text-transform: uppercase;
        letter-spacing: .4px;
        margin-bottom: 6px;
        display: block;
    }

    .filter-card .select2-container--default .select2-selection--single {
        height: 42px; border-radius: 12px; border: 1.5px solid #e5e7eb;
        background: #f9fafb; padding: 6px 12px;
    }
    .filter-card .select2-container--default .select2-selection--single .select2-selection__rendered { line-height: 28px; font-size: 13px; }
    .filter-card .select2-container--default .select2-selection--single .select2-selection__arrow { height: 40px; }

    /* === DATA TABLE SECTION === */
    .section-card {
        background: #ffffff;
        border-radius: 18px;
        border: 1px solid rgba(22,163,74,.1);
        box-shadow: 0 2px 12px rgba(22,163,74,.06);
        overflow: hidden;
    }

    .pks-group-header {
        background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
        padding: 12px 20px;
        font-weight: 800;
        font-size: 13px;
        color: #166534;
        border-top: 1px solid #bbf7d0;
        border-bottom: 1px solid #bbf7d0;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .report-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
        font-size: clamp(11.5px, .7vw+9px, 13px);
    }

    .report-table thead th {
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

    .report-table tbody td {
        padding: 11px 16px;
        border-bottom: 1px solid rgba(22,163,74,.07);
        vertical-align: middle;
    }

    .report-table tbody tr:hover td { background: rgba(22,163,74,.03); }

    .report-table tfoot td {
        background: #f0fdf4;
        border-top: 2px solid #bbf7d0;
        font-weight: 800;
        color: #14532d;
        padding: 12px 16px;
        font-size: 13px;
    }

    .empty-report { padding: 60px 20px; text-align: center; }
    .empty-report i { font-size: 56px; color: #d1d5db; margin-bottom: 12px; }

    /* Dark mode */
    html.app-skin-dark .filter-card,
    html.app-skin-dark .rpm-kpi,
    html.app-skin-dark .section-card { background:#0a2317 !important; border-color:rgba(34,197,94,.15) !important; }
    html.app-skin-dark .filter-card .form-control,
    html.app-skin-dark .filter-card .form-select { background:#0e3b26;border-color:rgba(34,197,94,.2);color:#d1fae5; }
    html.app-skin-dark .pks-group-header { background:linear-gradient(135deg,#0e3b26,#052e16);border-color:rgba(34,197,94,.15);color:#86efac; }
    html.app-skin-dark .report-table thead th { background:#021a0b !important; }
    html.app-skin-dark .report-table tbody td { border-color:rgba(34,197,94,.07) !important; color:#d1fae5; }
    html.app-skin-dark .report-table tfoot td { background:#0e3b26;border-color:rgba(34,197,94,.2);color:#86efac; }

    /* === PRINT === */
    @media print {
        @page { size: A4 portrait; margin: 6mm 12mm 12mm 12mm; }
        html, body { width:100% !important;margin:0 !important;padding:0 !important;background:#fff !important;color:#000 !important;-webkit-print-color-adjust:exact !important;print-color-adjust:exact !important; }
        .nxl-navigation, .nxl-header, .filter-card, .no-print,
        .page-hero-strip, .page-header, .breadcrumb, .nxl-footer,
        .report-header-card, .rpm-kpi, .btn, .btn-ptpn { display:none !important; }
        .main-content, .nxl-container, .nxl-content { padding:0 !important;margin:0 !important; }
        .print-only { display:block !important; }
        .print-report-header { border-bottom:1px solid #000;margin-bottom:12px;padding-bottom:6px; }
        .print-report-header-table { width:100%;border-collapse:collapse; }
        .print-report-header-table td { border:none !important;vertical-align:middle; }
        .print-report-table { width:100%;border-collapse:collapse;margin-top:8px; }
        .print-report-table th, .print-report-table td { border:1px solid #000 !important;padding:4px 6px;font-size:9px; }
        .print-report-table th { background:#f2f2f2 !important;font-weight:bold;text-align:center; }
        .print-subtotal-row td { background:#f9f9f9 !important;font-weight:bold; }
    }
</style>
@endsection

@section('content')

{{-- Print Only Header --}}
<div class="print-only print-report-header">
    <table class="print-report-header-table">
        <tr>
            <td style="width:70px;">
                <img src="{{ asset('logo/Icon%20SIMOLI.png') }}" style="width:55px;height:auto;" alt="SIMOLI">
            </td>
            <td style="text-align:center;">
                <h3 style="margin:0;font-size:15px;font-weight:800;text-transform:uppercase;">Report Pemeliharaan SIMOLI</h3>
                <div style="font-size:11px;font-weight:700;">PT. Perkebunan Nusantara IV Regional III</div>
                <div style="font-size:10px;">Sistem Monitoring Limbah Land Aplikasi</div>
            </td>
            <td style="width:100px;text-align:right;font-size:10px;">
                <div><strong>Periode:</strong></div>
                <div>{{ $namaBulan[(int)$bulan] }} {{ $tahun }}</div>
            </td>
        </tr>
    </table>
</div>

<div class="print-only">
    @if($data->count() > 0)
        @foreach($dataByPks as $pksName => $items)
        <div style="margin-top:14px;margin-bottom:16px;">
            <div style="font-size:11px;font-weight:800;margin-bottom:4px;text-transform:uppercase;">PKS {{ $pksName }}</div>
            <table class="print-report-table">
                <thead>
                    <tr>
                        <th style="width:30px;">No</th>
                        <th>Tanggal</th>
                        <th>Blok</th>
                        <th>No Bak</th>
                        <th>Jenis</th>
                        <th style="text-align:right;">Flat Bed</th>
                        <th style="text-align:right;">Long Bed</th>
                        <th style="text-align:right;">Jumlah HK</th>
                        <th>Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($items as $j => $item)
                    <tr>
                        <td style="text-align:center;">{{ $j + 1 }}</td>
                        <td>{{ $item->tanggal ? $item->tanggal->format('d/m/Y') : '-' }}</td>
                        <td>{{ $item->blok ?? '-' }}</td>
                        <td>{{ $item->no_bak ?? '-' }}</td>
                        <td>{{ $item->jenis_pemeliharaan == '1' ? 'Mekanis' : ($item->jenis_pemeliharaan == '2' ? 'Manual' : '-') }}</td>
                        <td style="text-align:right;">{{ number_format($item->flat_bed, 2) }}</td>
                        <td style="text-align:right;">{{ number_format($item->long_bed, 2) }}</td>
                        <td style="text-align:right;">{{ $item->jumlah_hk }}</td>
                        <td>{{ $item->keterangan ?? '-' }}</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    @php
                        $subtotalFlatBed = $items->sum(fn($row) => is_numeric($row->flat_bed) ? $row->flat_bed + 0 : (float) preg_replace('/[^0-9.-]/', '', (string) $row->flat_bed));
                        $subtotalLongBed = $items->sum(fn($row) => is_numeric($row->long_bed) ? $row->long_bed + 0 : (float) preg_replace('/[^0-9.-]/', '', (string) $row->long_bed));
                        $subtotalJumlahHk = $items->sum(fn($row) => is_numeric($row->jumlah_hk) ? $row->jumlah_hk + 0 : (float) preg_replace('/[^0-9.-]/', '', (string) $row->jumlah_hk));
                    @endphp
                    <tr class="print-subtotal-row">
                        <td colspan="5" style="text-align:right;">Subtotal {{ $pksName }}</td>
                        <td style="text-align:right;">{{ number_format($subtotalFlatBed, 2) }}</td>
                        <td style="text-align:right;">{{ number_format($subtotalLongBed, 2) }}</td>
                        <td style="text-align:right;">{{ $subtotalJumlahHk }}</td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>
        @endforeach
    @else
        <div style="padding:20px;text-align:center;">Tidak ada data pemeliharaan pada periode ini.</div>
    @endif
</div>

{{-- Header Banner Card --}}
<div class="report-header-card no-print">
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
        <div>
            <h3 style="font-family:'Outfit',sans-serif;font-weight:900;margin:0 0 4px;font-size:22px;color:#ffffff;display:flex;align-items:center;gap:10px;">
                <i class="feather-tool" style="color:#86efac;font-size:24px;"></i>
                Report Pemeliharaan SIMOLI
            </h3>
            <p style="margin:0;color:rgba(209,250,229,.9);font-size:13px;font-weight:600;">
                Laporan rekapitulasi data pemeliharaan bulan <strong>{{ $namaBulan[(int)$bulan] }} {{ $tahun }}</strong> seluruh PKS
            </p>
        </div>
        <div>
            <button onclick="window.print()" class="btn-ptpn" style="padding:10px 18px;font-size:13px;border-radius:12px;background:rgba(255,255,255,.15);border:1px solid rgba(255,255,255,.25);color:#ffffff;font-weight:800;">
                <i class="feather-printer" style="font-size:14px;"></i>
                Cetak Landscape
            </button>
        </div>
    </div>
</div>

{{-- Stat KPI Cards --}}
<div class="row g-3 mb-4 no-print">
    <div class="col-xl-3 col-sm-6">
        <div class="rpm-kpi">
            <div class="d-flex align-items-center gap-3">
                <div style="width:46px;height:46px;border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:20px;background:#f0fdf4;color:#16a34a;border:1px solid #bbf7d0;">
                    <i class="feather-file-text"></i>
                </div>
                <div>
                    <h3 style="font-family:'Outfit',sans-serif;font-weight:900;color:#14532d;margin:0 0 2px;">{{ number_format($summary['count']) }}</h3>
                    <span style="font-size:11.5px;color:#6b7280;font-weight:600;">Total Record Data</span>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-sm-6">
        <div class="rpm-kpi">
            <div class="d-flex align-items-center gap-3">
                <div style="width:46px;height:46px;border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:20px;background:#eff6ff;color:#1d4ed8;border:1px solid #bfdbfe;">
                    <i class="feather-layers"></i>
                </div>
                <div>
                    <h3 style="font-family:'Outfit',sans-serif;font-weight:900;color:#1d4ed8;margin:0 0 2px;">{{ number_format($summary['flat_bed'], 2) }}</h3>
                    <span style="font-size:11.5px;color:#6b7280;font-weight:600;">Total Flat Bed</span>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-sm-6">
        <div class="rpm-kpi">
            <div class="d-flex align-items-center gap-3">
                <div style="width:46px;height:46px;border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:20px;background:#fffbeb;color:#b45309;border:1px solid #fde68a;">
                    <i class="feather-maximize-2"></i>
                </div>
                <div>
                    <h3 style="font-family:'Outfit',sans-serif;font-weight:900;color:#b45309;margin:0 0 2px;">{{ number_format($summary['long_bed'], 2) }}</h3>
                    <span style="font-size:11.5px;color:#6b7280;font-weight:600;">Total Long Bed</span>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-sm-6">
        <div class="rpm-kpi">
            <div class="d-flex align-items-center gap-3">
                <div style="width:46px;height:46px;border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:20px;background:#f0fdfa;color:#0d9488;border:1px solid #99f6e4;">
                    <i class="feather-users"></i>
                </div>
                <div>
                    <h3 style="font-family:'Outfit',sans-serif;font-weight:900;color:#0d9488;margin:0 0 2px;">{{ number_format($summary['jumlah_hk']) }}</h3>
                    <span style="font-size:11.5px;color:#6b7280;font-weight:600;">Total HK (Hari Kerja)</span>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Filter Card --}}
<div class="filter-card no-print">
    <form action="{{ route('report-pemeliharaan') }}" method="GET">
        <div class="row g-3 align-items-end">
            @if(Auth::user()->isAdmin())
            <div class="col-md-3 col-lg-3">
                <label class="form-label">
                    <i class="feather-home me-1" style="color:#16a34a;"></i> Unit PKS
                </label>
                <select name="id_pks" class="form-select" data-select2-selector="status">
                    <option value="">Semua PKS</option>
                    @foreach($pksList as $pks)
                    <option value="{{ $pks->ID }}" {{ request('id_pks') == $pks->ID ? 'selected' : '' }}>{{ $pks->nama }} ({{ $pks->akro }})</option>
                    @endforeach
                </select>
            </div>
            @endif

            <div class="col-md-3 col-lg-2">
                <label class="form-label">
                    <i class="feather-calendar me-1" style="color:#16a34a;"></i> Bulan
                </label>
                <select name="bulan" class="form-select">
                    @foreach(['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'] as $i => $bln)
                    <option value="{{ $i + 1 }}" {{ $bulan == ($i + 1) ? 'selected' : '' }}>{{ $bln }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-3 col-lg-2">
                <label class="form-label">
                    <i class="feather-calendar me-1" style="color:#16a34a;"></i> Tahun
                </label>
                <select name="tahun" class="form-select">
                    @foreach($years as $y)
                    <option value="{{ $y }}" {{ $tahun == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-3 col-lg-2">
                <label class="form-label">
                    <i class="feather-tool me-1" style="color:#16a34a;"></i> Jenis
                </label>
                <select name="jenis" class="form-select">
                    <option value="">Semua Jenis</option>
                    <option value="1" {{ request('jenis') == '1' ? 'selected' : '' }}>Mekanis</option>
                    <option value="2" {{ request('jenis') == '2' ? 'selected' : '' }}>Manual</option>
                </select>
            </div>

            <div class="col-12 col-lg-3">
                <label class="form-label">&nbsp;</label>
                <div class="d-flex gap-2">
                    <button type="submit" class="btn-ptpn btn-ptpn-primary flex-grow-1" style="padding:10px 14px;font-size:13px;border-radius:12px;">
                        <i class="feather-search" style="font-size:14px;"></i> Tampilkan
                    </button>
                    <a href="{{ route('report-pemeliharaan') }}" class="btn-ptpn btn-ptpn-outline" style="padding:10px 12px;border-radius:12px;" title="Reset">
                        <i class="feather-refresh-cw" style="font-size:14px;"></i>
                    </a>
                </div>
            </div>
        </div>
    </form>
</div>

{{-- Data Table --}}
@if($data->count() > 0)
<div class="section-card no-print mb-4">
    <div style="padding:16px 20px;border-bottom:1px solid rgba(22,163,74,.08);display:flex;align-items:center;justify-content:space-between;">
        <h4 style="font-family:'Outfit',sans-serif;font-size:15px;font-weight:800;color:#14532d;margin:0;display:flex;align-items:center;gap:8px;">
            <i class="feather-tool" style="color:#16a34a;font-size:18px;"></i>
            Data Pemeliharaan — {{ $namaBulan[(int)$bulan] }} {{ $tahun }}
        </h4>
        <span class="mod-pill mod-pill-ok" style="font-size:10.5px;">
            <i class="feather-database" style="font-size:11px;"></i>
            {{ $summary['count'] }} Total Record
        </span>
    </div>

    <div class="card-body p-0">
        @foreach($dataByPks as $pksName => $items)
        <div class="pks-group-header">
            <div>
                <i class="feather-home me-1"></i> PKS {{ $pksName }}
            </div>
            <span class="mod-pill mod-pill-info" style="font-size:11px;">{{ $items->count() }} transaksi</span>
        </div>
        <div class="table-responsive">
            <table class="report-table mb-0">
                <thead>
                    <tr>
                        <th style="width:40px;text-align:center;">No</th>
                        <th style="min-width:110px;">Tanggal</th>
                        <th style="min-width:100px;">Blok</th>
                        <th style="min-width:100px;">No Bak</th>
                        <th style="min-width:110px;">Jenis</th>
                        <th style="text-align:right;min-width:100px;">Flat Bed</th>
                        <th style="text-align:right;min-width:100px;">Long Bed</th>
                        <th style="text-align:right;min-width:100px;">Jumlah HK</th>
                        <th style="min-width:180px;">Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($items as $j => $item)
                    <tr>
                        <td style="text-align:center;font-weight:700;color:#9ca3af;">{{ $j + 1 }}</td>
                        <td>{{ $item->tanggal ? $item->tanggal->format('d/m/Y') : '-' }}</td>
                        <td><span style="font-weight:700;color:#14532d;">{{ $item->blok ?? '-' }}</span></td>
                        <td><span style="font-weight:700;color:#1d4ed8;">{{ $item->no_bak ?? '-' }}</span></td>
                        <td>
                            @if($item->jenis_pemeliharaan == '1')
                                <span class="mod-pill mod-pill-info" style="font-size:10.5px;"><i class="feather-settings me-1"></i> Mekanis</span>
                            @elseif($item->jenis_pemeliharaan == '2')
                                <span class="mod-pill mod-pill-warn" style="font-size:10.5px;"><i class="feather-user me-1"></i> Manual</span>
                            @else
                                <span style="color:#9ca3af;">-</span>
                            @endif
                        </td>
                        <td style="text-align:right;font-weight:700;color:#1d4ed8;">{{ number_format($item->flat_bed, 2) }}</td>
                        <td style="text-align:right;font-weight:700;color:#b45309;">{{ number_format($item->long_bed, 2) }}</td>
                        <td style="text-align:right;font-weight:700;color:#0d9488;">{{ $item->jumlah_hk }}</td>
                        <td><small style="color:#6b7280;">{{ $item->keterangan ?? '-' }}</small></td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    @php
                        $subtotalFlatBed = $items->sum(fn($row) => is_numeric($row->flat_bed) ? $row->flat_bed + 0 : (float) preg_replace('/[^0-9.-]/', '', (string) $row->flat_bed));
                        $subtotalLongBed = $items->sum(fn($row) => is_numeric($row->long_bed) ? $row->long_bed + 0 : (float) preg_replace('/[^0-9.-]/', '', (string) $row->long_bed));
                        $subtotalJumlahHk = $items->sum(fn($row) => is_numeric($row->jumlah_hk) ? $row->jumlah_hk + 0 : (float) preg_replace('/[^0-9.-]/', '', (string) $row->jumlah_hk));
                    @endphp
                    <tr>
                        <td colspan="5" style="text-align:right;">Subtotal PKS {{ $pksName }}</td>
                        <td style="text-align:right;color:#1d4ed8;">{{ number_format($subtotalFlatBed, 2) }}</td>
                        <td style="text-align:right;color:#b45309;">{{ number_format($subtotalLongBed, 2) }}</td>
                        <td style="text-align:right;color:#0d9488;">{{ $subtotalJumlahHk }}</td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>
        @endforeach
    </div>

    <div style="padding:16px 20px;background:#f0fdf4;border-top:1px solid #bbf7d0;">
        <div class="row text-center font-weight-bold" style="font-size:13px;font-weight:800;color:#14532d;">
            <div class="col">Total Records: {{ $summary['count'] }}</div>
            <div class="col" style="color:#1d4ed8;">Flat Bed: {{ number_format($summary['flat_bed'], 2) }}</div>
            <div class="col" style="color:#b45309;">Long Bed: {{ number_format($summary['long_bed'], 2) }}</div>
            <div class="col" style="color:#0d9488;">Jumlah HK: {{ $summary['jumlah_hk'] }}</div>
        </div>
    </div>
</div>
@else
<div class="section-card no-print">
    <div class="empty-report">
        <i class="feather-inbox d-block"></i>
        <h5 style="font-family:'Outfit',sans-serif;font-weight:800;color:#374151;margin-top:12px;">Tidak Ada Data Pemeliharaan</h5>
        <p style="color:#6b7280;font-size:13px;">Untuk bulan {{ $namaBulan[(int)$bulan] }} {{ $tahun }}</p>
    </div>
</div>
@endif

@endsection

@section('scripts')
<script src="{{ asset('duraluxadmin/assets/vendors/js/select2.min.js') }}"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof $ !== 'undefined' && $.fn.select2) {
            $('[data-select2-selector]').select2({ width: '100%' });
        }
    });
</script>
@endsection
