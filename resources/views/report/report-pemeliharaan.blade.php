@extends('layouts.simoli')

@section('title', 'Report Pemeliharaan')
@section('page-title', 'Report Pemeliharaan')

@section('breadcrumb')
<li class="breadcrumb-item">Report</li>
<li class="breadcrumb-item active">Report Pemeliharaan</li>
@endsection

@php
    $namaBulan = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
@endphp

@section('styles')
<link rel="stylesheet" type="text/css" href="{{ asset('duraluxadmin/assets/vendors/css/select2.min.css') }}" />
<link rel="stylesheet" type="text/css" href="{{ asset('duraluxadmin/assets/vendors/css/select2-theme.min.css') }}" />
<style>
    .print-only {
        display: none;
    }
    .report-header-card {
        border: none;
        border-radius: 12px;
        background: linear-gradient(135deg, #7c2d12 0%, #ea580c 50%, #fb923c 100%);
        color: #fff;
    }
    .report-header-card h4, .report-header-card h4 i {
        color: #ffffff !important;
    }
    .report-header-card .text-muted-light { color: rgba(255, 255, 255, 0.9) !important; }
    .stat-card { border: none; border-radius: 12px; transition: transform 0.2s ease, box-shadow 0.2s ease; }
    .stat-card:hover { transform: translateY(-4px); box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1); }
    .stat-icon { width: 48px; height: 48px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 20px; }
    .filter-card { border: 1px solid #e9ecef; border-radius: 12px; background: linear-gradient(135deg, #f8f9ff 0%, #ffffff 100%); }
    .section-card { border: none; border-radius: 12px; overflow: hidden; }
    .pks-group-header {
        background: linear-gradient(135deg, #fff7ed 0%, #ffedd5 100%);
        padding: 10px 24px;
        font-weight: 700;
        font-size: 13px;
        color: #7c2d12;
        border-top: 1px solid #fed7aa;
        border-bottom: 1px solid #fed7aa;
    }
    .table thead th {
        background: #fff7ed;
        border: none;
        font-weight: 600;
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #5b6b8a;
        white-space: nowrap;
        padding: 10px 12px;
    }
    .table tbody td { vertical-align: middle; font-size: 13px; border-bottom: 1px solid #f0f3f5; padding: 8px 12px; }
    .table tbody tr:hover { background-color: #fff7ed; }
    .table tfoot td { font-weight: 700; background: #ffedd5; border-top: 2px solid #fed7aa; font-size: 13px; }
    .empty-report { padding: 50px 20px; text-align: center; }
    .empty-report i { font-size: 56px; color: #d1d5db; margin-bottom: 12px; }
    .badge-mekanis { background: #dbeafe; color: #1e40af; font-size: 11px; padding: 3px 8px; border-radius: 6px; font-weight: 600; }
    .badge-manual { background: #fef3c7; color: #92400e; font-size: 11px; padding: 3px 8px; border-radius: 6px; font-weight: 600; }
    
    /* Filter form styling */
    .filter-card .form-label {
        font-size: 12px;
        font-weight: 700;
        color: #5b6b8a;
        letter-spacing: 0.3px;
        margin-bottom: 6px;
        display: flex;
        align-items: center;
    }
    .filter-card .form-control,
    .filter-card .form-select {
        font-size: 13px;
        font-weight: 500;
        border: 1px solid #d1d9e6;
        border-radius: 8px;
        padding: 8px 12px;
        height: 40px;
        background-color: #ffffff;
        color: #2b3e50;
        transition: border-color 0.2s ease, box-shadow 0.2s ease;
    }
    .filter-card .form-control:focus,
    .filter-card .form-select:focus {
        border-color: #ea580c;
        box-shadow: 0 0 0 3px rgba(234, 88, 12, 0.08);
        color: #2b3e50;
    }
    .filter-card .btn {
        font-size: 13px;
        font-weight: 600;
        letter-spacing: 0.3px;
        border-radius: 8px;
        height: 40px;
        transition: all 0.2s ease;
    }
    .filter-card .btn-primary {
        background-color: #ea580c;
        border-color: #ea580c;
    }
    .filter-card .btn-primary:hover {
        background-color: #d04906;
        border-color: #d04906;
        box-shadow: 0 4px 12px rgba(234, 88, 12, 0.25);
    }
    .filter-card .btn-outline-secondary {
        border-width: 1.5px;
    }
    .filter-card .btn-outline-secondary:hover {
        background-color: #f3f4f6;
    }

    /* Dark mode overrides */
    html.app-skin-dark .filter-card { border-color: #1b2436 !important; background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%) !important; }
    html.app-skin-dark .filter-card .form-label { color: #cbd5e1 !important; }
    html.app-skin-dark .filter-card .form-control,
    html.app-skin-dark .filter-card .form-select {
        background-color: #0f172a !important;
        color: #e2e8f0 !important;
        border-color: #334155 !important;
    }
    html.app-skin-dark .filter-card .form-control:focus,
    html.app-skin-dark .filter-card .form-select:focus {
        background-color: #0f172a !important;
        color: #e2e8f0 !important;
        border-color: #ea580c !important;
        box-shadow: 0 0 0 3px rgba(234, 88, 12, 0.15) !important;
    }
    html.app-skin-dark .filter-card .form-control::placeholder,
    html.app-skin-dark .filter-card .form-select::placeholder {
        color: #94a3b8 !important;
    }
    html.app-skin-dark .filter-card .btn-outline-secondary {
        color: #cbd5e1;
        border-color: #475569;
    }
    html.app-skin-dark .filter-card .btn-outline-secondary:hover {
        background-color: #1e293b;
        color: #e2e8f0;
        border-color: #64748b;
    }
    html.app-skin-dark .pks-group-header { background: linear-gradient(135deg, #431407 0%, #7c2d12 100%) !important; color: #fdba74 !important; border-color: #7c2d12 !important; }
    html.app-skin-dark .table thead th { background: #1e293b !important; color: #94a3b8 !important; }
    html.app-skin-dark .table tbody td { border-color: #1b2436 !important; color: #e2e8f0 !important; }
    html.app-skin-dark .table tbody tr:hover { background-color: rgba(234,88,12,0.08) !important; }
    html.app-skin-dark .table tfoot td { background: #1c1207 !important; border-color: #7c2d12 !important; color: #fdba74 !important; }
    html.app-skin-dark .section-card { border-color: #1b2436 !important; }
    html.app-skin-dark .badge-mekanis { background: #1e3a5f !important; color: #93c5fd !important; }
    html.app-skin-dark .badge-manual { background: #422006 !important; color: #fcd34d !important; }

    @media print {
        @page {
            size: A4 portrait;
            margin: 6mm 12mm 12mm 12mm;
        }

        html, body {
            width: 100%;
            margin: 0 !important;
            padding: 0 !important;
            background: #fff !important;
            color: #000 !important;
            font-family: Arial, sans-serif;
            font-size: 10px;
            line-height: 1.35;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        html.app-skin-dark,
        html.app-skin-dark body,
        html.app-skin-dark .nxl-container,
        html.app-skin-dark .main-content,
        html.app-skin-dark .nxl-content,
        html.app-skin-dark .print-only,
        html.app-skin-dark div {
            background: #fff !important;
            background-color: #fff !important;
            color: #000 !important;
        }

        html.app-skin-dark * {
            box-shadow: none !important;
            text-shadow: none !important;
        }

        html.app-skin-dark .print-only,
        html.app-skin-dark .print-only * {
            color: #000 !important;
        }

        .nxl-navigation, .nxl-header, .filter-card, .no-print,
        .page-header, .page-header-left, .page-header-title, .page-header-breadcrumb,
        .breadcrumb, .nxl-footer, .page-header-right-items,
        .report-header-card, .stat-card, .summary-box-pengaliran,
        .summary-box-pemeliharaan, .page-header-right-open-toggle,
        .d-md-none.d-flex.align-items-center {
            display: none !important;
        }

        .main-content > * {
            display: none !important;
        }

        .main-content > .print-only {
            display: block !important;
        }

        .print-only {
            display: block !important;
        }

        .nxl-container {
            top: 0 !important;
            margin-left: 0 !important;
            padding: 0 !important;
            min-height: auto !important;
        }

        .main-content {
            padding: 0 !important;
        }

        .nxl-content {
            padding-top: 0 !important;
            margin-top: 0 !important;
        }

        .nxl-container .page-header + .nxl-content {
            padding-top: 0 !important;
        }

        .nxl-header,
        .page-header {
            display: none !important;
            visibility: hidden !important;
            height: 0 !important;
            min-height: 0 !important;
            padding: 0 !important;
            margin: 0 !important;
            border: 0 !important;
        }

        .print-report-header {
            padding: 0;
            margin: 0 0 2px 0;
            border-bottom: 1px solid #000;
        }

        .print-report-header-table {
            width: 100%;
            border-collapse: collapse;
            border: none !important;
            margin-bottom: 0;
        }

        .print-report-header-table td {
            border: none !important;
            vertical-align: middle;
            padding: 0;
        }

        .print-header-logo-cell {
            width: 72px;
            text-align: left;
        }

        .print-header-logo {
            width: 60px;
            height: auto;
        }

        .print-header-text-cell {
            text-align: center;
        }

        .print-header-title {
            margin: 0;
            font-size: 14px;
            font-weight: 700;
            line-height: 1.1;
            text-transform: uppercase;
        }

        .print-header-subtitle {
            margin: 0;
            font-size: 10px;
            font-weight: 700;
            line-height: 1.1;
        }

        .print-header-meta-cell {
            width: 72px;
            text-align: right;
            vertical-align: middle;
            font-size: 9px;
        }

        .print-report-content {
            margin-top: 0;
            color: #000;
        }

        .print-report-content p {
            margin: 8px 0;
        }

        .print-data-section {
            margin-top: 16px;
            margin-bottom: 22px;
        }

        .print-data-title {
            margin: 0 0 8px;
            font-size: 10px;
            font-weight: 700;
        }

        .print-report-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 6px;
            margin-bottom: 16px;
        }

        .print-report-table th,
        .print-report-table td {
            border: 1px solid #000 !important;
            padding: 4px 5px;
            font-size: 9px;
            text-align: left;
            vertical-align: top;
        }

        .print-report-table th {
            font-weight: 700;
            text-align: center;
        }

        .print-report-table thead {
            display: table-header-group;
        }

        .print-report-table tfoot {
            display: table-footer-group;
        }

        .print-report-table tr {
            break-inside: avoid;
            page-break-inside: avoid;
        }

        .print-report-table tbody tr,
        .print-report-table tbody td,
        .print-group-row,
        .print-subtotal-row,
        .print-total-row {
            break-inside: avoid;
            page-break-inside: avoid;
            page-break-after: auto;
            page-break-before: auto;
        }

        .print-group-row td {
            background: #ffedd5;
            font-weight: 700;
            text-transform: uppercase;
        }

        .print-subtotal-row td,
        .print-total-row td {
            background: #f2f2f2;
            font-weight: 700;
        }

        .print-data-section + .print-data-section {
            margin-top: 24px;
        }

        .print-footer {
            margin-top: 18px;
            padding-top: 8px;
            border-top: 1px solid #000;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 9px;
        }

        .print-footer-left {
            font-weight: 700;
        }

        .print-footer-right {
            text-align: right;
        }

        .table-responsive {
            overflow: visible !important;
        }

        .table {
            width: 100%;
            margin: 0 !important;
            font-size: 9px;
            border-collapse: collapse;
        }

        .table thead {
            display: table-header-group;
        }

        .table tfoot {
            display: table-footer-group;
        }

        .table thead th {
            background: #fff !important;
            color: #000 !important;
            border: 1px solid #000 !important;
            padding: 5px 4px !important;
            font-size: 8px !important;
            font-weight: 700 !important;
            text-transform: uppercase;
            white-space: nowrap;
        }

        .table tbody td,
        .table tfoot td {
            border: 1px solid #000 !important;
            padding: 3px 4px !important;
            font-size: 8px !important;
            color: #000 !important;
        }

        .pks-group-header {
            background: #ffedd5 !important;
            color: #7c2d12 !important;
            border: 1px solid #000 !important;
            padding: 3px 4px !important;
            font-size: 8px !important;
            font-weight: 700 !important;
            text-transform: uppercase;
        }

        .summary-row {
            background: #ffedd5 !important;
        }

        .summary-row td {
            color: #000 !important;
            font-weight: 700 !important;
            border: 1px solid #000 !important;
        }

        .badge,
        .badge-mekanis,
        .badge-manual {
            border: none !important;
            background: transparent !important;
            color: #000 !important;
            font-size: 8px !important;
            padding: 0 !important;
        }

        .empty-report {
            padding: 20px;
            text-align: center;
        }
    }
</style>
@endsection

@section('content')

<div class="print-only print-report-header">
    <table class="print-report-header-table">
        <tr>
            <td class="print-header-logo-cell">
                <img class="print-header-logo" src="{{ asset('logo/Icon%20SIMOLI.png') }}" alt="SIMOLI">
            </td>
            <td class="print-header-text-cell">
                <div class="print-header-title">Report Pemeliharaan SIMOLI</div>
                <div class="print-header-subtitle">PT. Perkebunan Nusantara V</div>
                <div class="print-header-subtitle">Sistem Monitoring Limbah</div>
            </td>
            <td class="print-header-meta-cell">
                <div><strong>Periode</strong></div>
                <div>{{ $namaBulan[(int)$bulan] }} {{ $tahun }}</div>
            </td>
        </tr>
    </table>
</div>

<div class="print-only print-report-content">
    <p>Berikut report pemeliharaan SIMOLI untuk bulan {{ $namaBulan[(int)$bulan] }} {{ $tahun }}, yang memuat data per PKS:</p>

    @if($data->count() > 0)
    @foreach($dataByPks as $pksName => $items)
    <div class="print-data-section">
        <div class="print-data-title">PKS {{ $pksName }}</div>
        <table class="print-report-table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Tanggal</th>
                    <th>Blok</th>
                    <th>No Bak</th>
                    <th>Jenis</th>
                    <th>Flat Bed</th>
                    <th>Long Bed</th>
                    <th>Jumlah HK</th>
                    <th>Keterangan</th>
                </tr>
            </thead>
            <tbody>
                @foreach($items as $j => $item)
                <tr>
                    <td style="text-align:center;">{{ $j + 1 }}</td>
                    <td>{{ $item->tanggal->format('d/m/Y') }}</td>
                    <td>{{ $item->blok ?? '-' }}</td>
                    <td>{{ $item->no_bak ?? '-' }}</td>
                    <td>
                        @if($item->jenis_pemeliharaan == '1')
                            Mekanis
                        @elseif($item->jenis_pemeliharaan == '2')
                            Manual
                        @else
                            -
                        @endif
                    </td>
                    <td style="text-align:right;">{{ number_format($item->flat_bed, 2) }}</td>
                    <td style="text-align:right;">{{ number_format($item->long_bed, 2) }}</td>
                    <td style="text-align:right;">{{ $item->jumlah_hk }}</td>
                    <td>{{ $item->keterangan ?? '-' }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                @php
                    $subtotalFlatBed = $items->sum(function ($row) {
                        return is_numeric($row->flat_bed) ? $row->flat_bed + 0 : (float) preg_replace('/[^0-9.-]/', '', (string) $row->flat_bed);
                    });
                    $subtotalLongBed = $items->sum(function ($row) {
                        return is_numeric($row->long_bed) ? $row->long_bed + 0 : (float) preg_replace('/[^0-9.-]/', '', (string) $row->long_bed);
                    });
                    $subtotalJumlahHk = $items->sum(function ($row) {
                        return is_numeric($row->jumlah_hk) ? $row->jumlah_hk + 0 : (float) preg_replace('/[^0-9.-]/', '', (string) $row->jumlah_hk);
                    });
                @endphp
                <tr class="print-subtotal-row">
                    <td colspan="5" class="text-end">Subtotal {{ $pksName }}</td>
                    <td style="text-align:right;">{{ number_format($subtotalFlatBed, 2) }}</td>
                    <td style="text-align:right;">{{ number_format($subtotalLongBed, 2) }}</td>
                    <td style="text-align:right;">{{ $subtotalJumlahHk }}</td>
                    <td></td>
                </tr>
            </tfoot>
        </table>
    </div>
    @endforeach

    <div class="print-footer">
        <div class="print-footer-left">Dokumen internal SIMOLI - PT. Perkebunan Nusantara V</div>
        <div class="print-footer-right">Dicetak: {{ now()->translatedFormat('d F Y H:i') }}</div>
    </div>
    @else
    <div class="print-data-section">
        <div class="print-empty-state">Tidak ada data pemeliharaan pada periode ini.</div>
    </div>
    @endif
</div>

{{-- Header --}}
<div class="card report-header-card shadow-sm mb-4">
    <div class="card-body p-4">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
            <div>
                <h4 class="fw-bold mb-1 text-white"><i class="feather-tool me-2 text-white"></i>Report Pemeliharaan</h4>
                <p class="text-white opacity-90 mb-0 fs-13">
                    Laporan data pemeliharaan bulan {{ $namaBulan[(int)$bulan] }} {{ $tahun }}
                </p>
            </div>
            <div class="no-print">
                <button onclick="window.print()" class="btn btn-light btn-sm fw-semibold">
                    <i class="feather-printer me-1"></i> Print
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Stats --}}
<div class="row mb-4 no-print">
    <div class="col-md-3">
        <div class="card stat-card shadow-sm">
            <div class="card-body p-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="stat-icon bg-soft-primary text-primary"><i class="feather-file-text"></i></div>
                    <div>
                        <h3 class="fw-bold mb-0">{{ $summary['count'] }}</h3>
                        <span class="fs-12 text-muted">Total Records</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card shadow-sm">
            <div class="card-body p-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="stat-icon bg-soft-success text-success"><i class="feather-layers"></i></div>
                    <div>
                        <h3 class="fw-bold mb-0">{{ number_format($summary['flat_bed'], 2) }}</h3>
                        <span class="fs-12 text-muted">Total Flat Bed</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card shadow-sm">
            <div class="card-body p-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="stat-icon bg-soft-info text-info"><i class="feather-maximize-2"></i></div>
                    <div>
                        <h3 class="fw-bold mb-0">{{ number_format($summary['long_bed'], 2) }}</h3>
                        <span class="fs-12 text-muted">Total Long Bed</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card shadow-sm">
            <div class="card-body p-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="stat-icon bg-soft-warning text-warning"><i class="feather-users"></i></div>
                    <div>
                        <h3 class="fw-bold mb-0">{{ $summary['jumlah_hk'] }}</h3>
                        <span class="fs-12 text-muted">Total HK</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Filter --}}
<div class="card filter-card shadow-sm mb-4 no-print">
    <div class="card-body p-4">
        <form action="{{ route('report-pemeliharaan') }}" method="GET">
            <div class="row g-3 align-items-end">
                @if(Auth::user()->isAdmin())
                <div class="col-md-3 col-lg-2">
                    <label class="form-label"><i class="feather-home me-1"></i>Unit PKS</label>
                    <select name="id_pks" class="form-select" data-select2-selector="status">
                        <option value="">Semua PKS</option>
                        @foreach($pksList as $pks)
                        <option value="{{ $pks->ID }}" {{ request('id_pks') == $pks->ID ? 'selected' : '' }}>{{ $pks->nama }} ({{ $pks->akro }})</option>
                        @endforeach
                    </select>
                </div>
                @endif
                <div class="col-md-3 col-lg-2">
                    <label class="form-label"><i class="feather-calendar me-1"></i>Bulan</label>
                    <select name="bulan" class="form-select">
                        @foreach(['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'] as $i => $bln)
                        <option value="{{ $i + 1 }}" {{ $bulan == ($i + 1) ? 'selected' : '' }}>{{ $bln }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 col-lg-2">
                    <label class="form-label"><i class="feather-calendar me-1"></i>Tahun</label>
                    <select name="tahun" class="form-select">
                        @foreach($years as $y)
                        <option value="{{ $y }}" {{ $tahun == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 col-lg-2">
                    <label class="form-label"><i class="feather-tool me-1"></i>Jenis</label>
                    <select name="jenis" class="form-select">
                        <option value="">Semua</option>
                        <option value="1" {{ request('jenis') == '1' ? 'selected' : '' }}>Mekanis</option>
                        <option value="2" {{ request('jenis') == '2' ? 'selected' : '' }}>Manual</option>
                    </select>
                </div>
                <div class="col-12 col-lg-4">
                    <div class="d-flex gap-2 w-100">
                        <button type="submit" class="btn btn-primary flex-grow-1"><i class="feather-search me-1"></i>Tampilkan</button>
                        <a href="{{ route('report-pemeliharaan') }}" class="btn btn-outline-secondary"><i class="feather-refresh-cw me-1"></i>Reset</a>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- Data Table --}}
@if($data->count() > 0)
<div class="card section-card shadow-sm">
    <div class="card-header py-3">
        <h6 class="mb-0 fw-bold"><i class="feather-tool me-2 text-warning"></i>Data Pemeliharaan — {{ $namaBulan[(int)$bulan] }} {{ $tahun }}</h6>
    </div>
    <div class="card-body p-0">
        @foreach($dataByPks as $pksName => $items)
        <div class="pks-group-header">
            <i class="feather-home me-1"></i> {{ $pksName }}
            <span class="badge bg-warning text-dark ms-2">{{ $items->count() }} data</span>
        </div>
        <div class="table-responsive">
            <table class="table mb-0">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Tanggal</th>
                        <th>Blok</th>
                        <th>No Bak</th>
                        <th>Jenis</th>
                        <th class="text-end">Flat Bed</th>
                        <th class="text-end">Long Bed</th>
                        <th class="text-end">Jumlah HK</th>
                        <th>Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($items as $j => $item)
                    <tr>
                        <td>{{ $j + 1 }}</td>
                        <td>{{ $item->tanggal->format('d/m/Y') }}</td>
                        <td>{{ $item->blok }}</td>
                        <td>{{ $item->no_bak }}</td>
                        <td>
                            @if($item->jenis_pemeliharaan == '1')
                                <span class="badge-mekanis">Mekanis</span>
                            @elseif($item->jenis_pemeliharaan == '2')
                                <span class="badge-manual">Manual</span>
                            @else
                                -
                            @endif
                        </td>
                        <td class="text-end">{{ number_format($item->flat_bed, 2) }}</td>
                        <td class="text-end">{{ number_format($item->long_bed, 2) }}</td>
                        <td class="text-end">{{ $item->jumlah_hk }}</td>
                        <td>{{ $item->keterangan ?? '-' }}</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    @php
                        $subtotalFlatBed = $items->sum(function ($row) {
                            return is_numeric($row->flat_bed) ? $row->flat_bed + 0 : (float) preg_replace('/[^0-9.-]/', '', (string) $row->flat_bed);
                        });
                        $subtotalLongBed = $items->sum(function ($row) {
                            return is_numeric($row->long_bed) ? $row->long_bed + 0 : (float) preg_replace('/[^0-9.-]/', '', (string) $row->long_bed);
                        });
                        $subtotalJumlahHk = $items->sum(function ($row) {
                            return is_numeric($row->jumlah_hk) ? $row->jumlah_hk + 0 : (float) preg_replace('/[^0-9.-]/', '', (string) $row->jumlah_hk);
                        });
                    @endphp
                    <tr>
                        <td colspan="5" class="text-end fw-bold">Subtotal {{ $pksName }}</td>
                        <td class="text-end">{{ number_format($subtotalFlatBed, 2) }}</td>
                        <td class="text-end">{{ number_format($subtotalLongBed, 2) }}</td>
                        <td class="text-end">{{ $subtotalJumlahHk }}</td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>
        @endforeach
    </div>
    <div class="card-footer py-3">
        <div class="row text-center">
            <div class="col"><strong>Total Records:</strong> {{ $summary['count'] }}</div>
            <div class="col"><strong>Flat Bed:</strong> {{ number_format($summary['flat_bed'], 2) }}</div>
            <div class="col"><strong>Long Bed:</strong> {{ number_format($summary['long_bed'], 2) }}</div>
            <div class="col"><strong>Jumlah HK:</strong> {{ $summary['jumlah_hk'] }}</div>
        </div>
    </div>
</div>
@else
<div class="card section-card shadow-sm">
    <div class="card-body">
        <div class="empty-report">
            <i class="feather-inbox d-block"></i>
            <h5 class="text-muted mt-3">Tidak ada data pemeliharaan</h5>
            <p class="text-muted fs-13">Untuk bulan {{ $namaBulan[(int)$bulan] }} {{ $tahun }}</p>
        </div>
    </div>
</div>
@endif

@endsection

@section('scripts')
<script src="{{ asset('duraluxadmin/assets/vendors/js/select2.min.js') }}"></script>
<script>
    $(document).ready(function() {
        $('[data-select2-selector]').select2({ width: '100%' });
    });
</script>
@endsection
