@extends('layouts.simoli')

@section('title', 'Laporan Harian')
@section('page-title', 'Laporan Harian')

@section('breadcrumb')
<li class="breadcrumb-item">Report</li>
<li class="breadcrumb-item active">Laporan Harian</li>
@endsection

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
        background: linear-gradient(135deg, #4338ca 0%, #6366f1 50%, #818cf8 100%);
        color: #fff;
    }
    .report-header-card .text-muted-light {
        color: rgba(255, 255, 255, 0.7);
    }
    .stat-card {
        border: none;
        border-radius: 12px;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .stat-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
    }
    .stat-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
    }
    .filter-card {
        border: 1px solid #e9ecef;
        border-radius: 12px;
        background: linear-gradient(135deg, #f8f9ff 0%, #ffffff 100%);
    }
    .section-card {
        border: none;
        border-radius: 12px;
        overflow: hidden;
    }
    .section-header {
        padding: 16px 24px;
        border-bottom: 2px solid #f0f3ff;
    }
    .section-header h6 {
        margin: 0;
        font-weight: 700;
    }
    .badge-pks {
        font-size: 11px;
        padding: 4px 10px;
        border-radius: 6px;
        font-weight: 600;
    }
    .pks-group-header {
        background: linear-gradient(135deg, #f0f3ff 0%, #e8edff 100%);
        padding: 10px 24px;
        font-weight: 700;
        font-size: 13px;
        color: #4338ca;
        border-top: 1px solid #e0e7ff;
        border-bottom: 1px solid #e0e7ff;
    }
    .table thead th {
        background: #f8f9ff;
        border: none;
        font-weight: 600;
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #5b6b8a;
        white-space: nowrap;
        padding: 10px 12px;
    }
    .table tbody td {
        vertical-align: middle;
        font-size: 13px;
        border-bottom: 1px solid #f0f3f5;
        padding: 8px 12px;
    }
    .table tbody tr:hover {
        background-color: #f8f9ff;
    }
    .table tfoot td {
        font-weight: 700;
        background: #f0f3ff;
        border-top: 2px solid #d0d5ff;
        font-size: 13px;
    }
    .summary-row {
        background: linear-gradient(135deg, #f0fdf4 0%, #ecfdf5 100%);
    }
    .summary-row td {
        font-weight: 700 !important;
        color: #059669;
    }
    .empty-report {
        padding: 50px 20px;
        text-align: center;
    }
    .empty-report i {
        font-size: 56px;
        color: #d1d5db;
        margin-bottom: 12px;
    }
    .badge-jenis {
        font-size: 10px;
        padding: 3px 8px;
        border-radius: 6px;
        font-weight: 600;
    }

    /* Dark mode overrides */
    html.app-skin-dark .filter-card { border-color: #1b2436 !important; background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%) !important; }
    html.app-skin-dark .section-header { background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%) !important; border-color: #334155 !important; }
    html.app-skin-dark .pks-group-header { background: linear-gradient(135deg, #1e1b4b 0%, #312e81 100%) !important; color: #a5b4fc !important; border-color: #3730a3 !important; }
    html.app-skin-dark .table thead th { background: #1e293b !important; color: #94a3b8 !important; }
    html.app-skin-dark .table tbody td { border-color: #1b2436 !important; }
    html.app-skin-dark .table tbody tr:hover { background-color: rgba(99,102,241,0.08) !important; }
    html.app-skin-dark .table tfoot td { background: #1e1b4b !important; border-color: #3730a3 !important; color: #a5b4fc !important; }
    html.app-skin-dark .summary-row { background: linear-gradient(135deg, #064e3b 0%, #065f46 100%) !important; }
    html.app-skin-dark .summary-row td { color: #6ee7b7 !important; }
    html.app-skin-dark .section-card { border-color: #1b2436 !important; }
    html.app-skin-dark .summary-box-pengaliran { background: linear-gradient(135deg, #172554 0%, #1e3a5f 100%) !important; border-color: #1e40af !important; }
    html.app-skin-dark .summary-box-pemeliharaan { background: linear-gradient(135deg, #422006 0%, #451a03 100%) !important; border-color: #92400e !important; }

    /* Print styles */
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

        .print-data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }

        .print-data-table td {
            padding: 4px 0;
            vertical-align: top;
            font-size: 10px;
        }

        .print-data-table .label {
            width: 120px;
            font-weight: 700;
        }

        .print-data-table .separator {
            width: 10px;
            text-align: center;
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
            background: #e8e8e8;
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

        .print-signature {
            margin-top: 24px;
            display: flex;
            justify-content: space-between;
        }

        .print-signature div {
            width: 45%;
            text-align: center;
        }

        .print-signature-title {
            font-weight: 700;
            margin-bottom: 40px;
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
            background: #e8e8e8 !important;
            color: #000 !important;
            border: 1px solid #000 !important;
            padding: 3px 4px !important;
            font-size: 8px !important;
            font-weight: 700 !important;
            text-transform: uppercase;
        }

        .summary-row {
            background: #e8e8e8 !important;
        }

        .summary-row td {
            color: #000 !important;
            font-weight: 700 !important;
            border: 1px solid #000 !important;
        }

        .badge,
        .badge-pks,
        .badge-jenis {
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

@section('page-actions')
<div class="page-header-right-items">
    <div class="d-flex d-md-none">
        <a href="javascript:void(0)" class="page-header-right-close-toggle">
            <i class="feather-arrow-left me-2"></i>
            <span>Back</span>
        </a>
    </div>
    <div class="d-flex align-items-center gap-2 page-header-right-items-wrapper">
        <button onclick="window.print()" class="btn btn-outline-primary">
            <i class="feather-printer me-2"></i>
            <span>Cetak</span>
        </button>
    </div>
</div>
<div class="d-md-none d-flex align-items-center">
    <a href="javascript:void(0)" class="page-header-right-open-toggle">
        <i class="feather-align-right fs-20"></i>
    </a>
</div>
@endsection

@section('content')
<div class="print-only print-report-header">
    <table class="print-report-header-table">
        <tr>
            <td class="print-header-logo-cell">
                <img class="print-header-logo" src="{{ asset('logo/Icon%20SIMOLI.png') }}" alt="SIMOLI">
            </td>
            <td class="print-header-text-cell">
                <div class="print-header-title">Laporan Harian SIMOLI</div>
                <div class="print-header-subtitle">PT. Perkebunan Nusantara V</div>
                <div class="print-header-subtitle">Sistem Monitoring Limbah</div>
            </td>
            <td class="print-header-meta-cell">
                <div><strong>Periode</strong></div>
                <div>{{ $periodeLabel }}</div>
            </td>
        </tr>
    </table>
</div>

<div class="print-only print-report-content">
    <p>Berikut laporan harian SIMOLI untuk periode {{ $periodeLabel }}, yang memuat data pengaliran dan pemeliharaan:</p>

    <div class="print-data-section">
        <div class="print-data-title">I. Laporan Pengaliran</div>
        @if($pengaliranData->count() > 0)
        <table class="print-report-table">
            <thead>
                <tr>
                    <th>Serial Number</th>
                    <th>PKS</th>
                    <th>Jam</th>
                    <th>No Bak</th>
                    <th>Blok</th>
                    <th>Flat Bed</th>
                    <th>Vol. Dihasilkan</th>
                    <th>Vol. Dialirkan</th>
                    <th>Luas (Ha)</th>
                    <th>Rotasi</th>
                    <th>Keterangan</th>
                </tr>
            </thead>
            <tbody>
                @php $pengaliranNo = 1; @endphp
                @foreach($pengaliranByPks as $pksName => $items)
                <tr class="print-group-row"><td colspan="11">{{ $pksName }}</td></tr>
                @foreach($items as $item)
                <tr>
                    <td style="text-align:center;">{{ $pengaliranNo++ }}</td>
                    <td>{{ $pksName }}</td>
                    <td>{{ $item->jam_mulai ? substr($item->jam_mulai, 0, 5) : '-' }} - {{ $item->jam_selesai ? substr($item->jam_selesai, 0, 5) : '-' }}</td>
                    <td>{{ $item->no_bak ?? '-' }}</td>
                    <td>{{ $item->blok ?? '-' }}</td>
                    <td style="text-align:right;">{{ number_format($item->flat_bed) }}</td>
                    <td style="text-align:right;">{{ number_format($item->vol_limbah_dihasilkan) }}</td>
                    <td style="text-align:right;">{{ number_format($item->vol_limbah_dialirkan) }}</td>
                    <td style="text-align:right;">{{ number_format($item->luas_area, 1) }}</td>
                    <td>{{ $item->rotasi ?? '-' }}</td>
                    <td>{{ Str::limit($item->keterangan, 35) ?: '-' }}</td>
                </tr>
                @endforeach
                <tr class="print-subtotal-row">
                    <td colspan="5">Subtotal {{ $pksName }}</td>
                    <td style="text-align:right;">{{ number_format($items->sum('flat_bed')) }}</td>
                    <td style="text-align:right;">{{ number_format($items->sum('vol_limbah_dihasilkan')) }}</td>
                    <td style="text-align:right;">{{ number_format($items->sum('vol_limbah_dialirkan')) }}</td>
                    <td style="text-align:right;">{{ number_format($items->sum('luas_area'), 1) }}</td>
                    <td colspan="2"></td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr class="print-total-row">
                    <td colspan="5">Grand Total Pengaliran</td>
                    <td style="text-align:right;">{{ number_format($summaryPengaliran['flat_bed']) }}</td>
                    <td style="text-align:right;">{{ number_format($summaryPengaliran['vol_dihasilkan']) }}</td>
                    <td style="text-align:right;">{{ number_format($summaryPengaliran['vol_dialirkan']) }}</td>
                    <td style="text-align:right;">{{ number_format($summaryPengaliran['luas_area'], 1) }}</td>
                    <td colspan="2"></td>
                </tr>
            </tfoot>
        </table>
        @else
        <div class="print-empty-state">Tidak ada data pengaliran pada periode ini.</div>
        @endif
    </div>

    <div class="print-data-section" style="margin-top: 16px;">
        <div class="print-data-title">II. Laporan Pemeliharaan</div>
        @if($pemeliharaanData->count() > 0)
        <table class="print-report-table">
            <thead>
                <tr>
                    <th>Serial Number</th>
                    <th>PKS</th>
                    <th>Jenis</th>
                    <th>No Bak</th>
                    <th>Blok</th>
                    <th>Flat Bed</th>
                    <th>Long Bed</th>
                    <th>Jml HK</th>
                    <th>Keterangan</th>
                </tr>
            </thead>
            <tbody>
                @php $pemeliharaanNo = 1; @endphp
                @foreach($pemeliharaanByPks as $pksName => $items)
                <tr class="print-group-row"><td colspan="9">{{ $pksName }}</td></tr>
                @foreach($items as $item)
                <tr>
                    <td style="text-align:center;">{{ $pemeliharaanNo++ }}</td>
                    <td>{{ $pksName }}</td>
                    <td>
                        @if($item->jenis_pemeliharaan == '1')
                            Mekanis
                        @elseif($item->jenis_pemeliharaan == '2')
                            Manual
                        @else
                            -
                        @endif
                    </td>
                    <td>{{ $item->no_bak ?? '-' }}</td>
                    <td>{{ $item->blok ?? '-' }}</td>
                    <td style="text-align:right;">{{ number_format($item->flat_bed) }}</td>
                    <td style="text-align:right;">{{ number_format($item->long_bed) }}</td>
                    <td style="text-align:right;">{{ $item->jumlah_hk ?? '-' }}</td>
                    <td>{{ Str::limit($item->keterangan, 40) ?: '-' }}</td>
                </tr>
                @endforeach
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
                    <td colspan="5">Subtotal {{ $pksName }}</td>
                    <td style="text-align:right;">{{ number_format($subtotalFlatBed) }}</td>
                    <td style="text-align:right;">{{ number_format($subtotalLongBed) }}</td>
                    <td style="text-align:right;">{{ number_format($subtotalJumlahHk) }}</td>
                    <td></td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr class="print-total-row">
                    <td colspan="5">Grand Total Pemeliharaan</td>
                    <td style="text-align:right;">{{ number_format($summaryPemeliharaan['flat_bed']) }}</td>
                    <td style="text-align:right;">{{ number_format($summaryPemeliharaan['long_bed']) }}</td>
                    <td style="text-align:right;">{{ $summaryPemeliharaan['jumlah_hk'] }}</td>
                    <td></td>
                </tr>
            </tfoot>
        </table>
        @else
        <div class="print-empty-state">Tidak ada data pemeliharaan pada periode ini.</div>
        @endif
    </div>

    <div class="print-footer">
        <div class="print-footer-left">Dokumen internal SIMOLI - PT. Perkebunan Nusantara V</div>
        <div class="print-footer-right">Dicetak: {{ now()->translatedFormat('d F Y H:i') }}</div>
    </div>
</div>

{{-- Report Header --}}
<div class="card report-header-card shadow-sm mb-4">
    <div class="card-body p-4">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h4 class="fw-bold mb-1">
                    <i class="feather-file-text me-2"></i> Laporan Harian SIMOLI
                </h4>
                <p class="text-muted-light mb-0">
                    PT. Perkebunan Nusantara V &mdash; Sistem Monitoring Limbah
                </p>
            </div>
            <div class="col-md-4 text-md-end mt-3 mt-md-0">
                <div class="fs-12 text-muted-light">Periode Laporan</div>
                <h3 class="fw-bold mb-0">
                    {{ $periodeLabel }}
                </h3>
            </div>
        </div>
    </div>
</div>

{{-- Filter Card --}}
<div class="card filter-card mb-4 shadow-sm no-print">
    <div class="card-body py-3">
        <form action="{{ route('laporan-harian') }}" method="GET">
            <div class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label fs-12 fw-semibold text-muted">
                        <i class="feather-calendar me-1"></i> Tanggal Awal
                    </label>
                    <input type="date" name="tanggal_awal" class="form-control" value="{{ $tanggalAwal }}">
                </div>
                @if(Auth::user()->isAdmin())
                <div class="col-md-3">
                    <label class="form-label fs-12 fw-semibold text-muted">
                        <i class="feather-home me-1"></i> Unit PKS
                    </label>
                    <select name="id_pks" class="form-control" data-select2-selector="status">
                        <option value="">Semua PKS</option>
                        @foreach($pksList as $pks)
                        <option value="{{ $pks->ID }}" {{ request('id_pks') == $pks->ID ? 'selected' : '' }}>
                            {{ $pks->nama }} ({{ $pks->akro }})
                        </option>
                        @endforeach
                    </select>
                </div>
                @endif
                <div class="col-md-3">
                    <label class="form-label fs-12 fw-semibold text-muted">
                        <i class="feather-calendar me-1"></i> Tanggal Akhir
                    </label>
                    <input type="date" name="tanggal_akhir" class="form-control" value="{{ $tanggalAkhir }}">
                </div>
                <div class="col-md-3">
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="feather-search me-1"></i> Tampilkan
                        </button>
                        <a href="{{ route('laporan-harian') }}" class="btn btn-outline-secondary">
                            <i class="feather-refresh-cw me-1"></i> Reset
                        </a>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- Summary Stats --}}
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card stat-card shadow-sm">
            <div class="card-body py-3">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <p class="text-muted fs-11 fw-medium mb-0">Data Pengaliran</p>
                        <h4 class="fw-bold mb-0">{{ $summaryPengaliran['count'] }}</h4>
                    </div>
                    <div class="stat-icon bg-soft-primary text-primary">
                        <i class="feather-droplet"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card shadow-sm">
            <div class="card-body py-3">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <p class="text-muted fs-11 fw-medium mb-0">Vol. Dialirkan</p>
                        <h4 class="fw-bold mb-0">{{ number_format($summaryPengaliran['vol_dialirkan']) }} <small class="fs-11 text-muted">m&sup3;</small></h4>
                    </div>
                    <div class="stat-icon bg-soft-success text-success">
                        <i class="feather-trending-up"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card shadow-sm">
            <div class="card-body py-3">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <p class="text-muted fs-11 fw-medium mb-0">Data Pemeliharaan</p>
                        <h4 class="fw-bold mb-0">{{ $summaryPemeliharaan['count'] }}</h4>
                    </div>
                    <div class="stat-icon bg-soft-warning text-warning">
                        <i class="feather-tool"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card shadow-sm">
            <div class="card-body py-3">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <p class="text-muted fs-11 fw-medium mb-0">Total Bed (FB+LB)</p>
                        <h4 class="fw-bold mb-0">{{ number_format($summaryPengaliran['flat_bed'] + $summaryPemeliharaan['flat_bed'] + $summaryPemeliharaan['long_bed']) }}</h4>
                    </div>
                    <div class="stat-icon bg-soft-info text-info">
                        <i class="feather-layers"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ========= PENGALIRAN SECTION ========= --}}
<div class="card section-card shadow-sm mb-4">
    <div class="section-header d-flex align-items-center justify-content-between">
        <h6>
            <i class="feather-droplet me-2 text-primary"></i>
            Laporan Pengaliran
            <span class="badge bg-primary ms-2 fs-11">{{ $summaryPengaliran['count'] }} Data</span>
        </h6>
    </div>
    <div class="card-body p-0">
        @if($pengaliranData->count() > 0)
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th class="ps-3">#</th>
                        <th>PKS</th>
                        <th>Jam</th>
                        <th>No Bak</th>
                        <th>Blok</th>
                        <th class="text-end">Flat Bed</th>
                        <th class="text-end">Vol. Dihasilkan</th>
                        <th class="text-end">Vol. Dialirkan</th>
                        <th class="text-end">Luas (Ha)</th>
                        <th>Rotasi</th>
                        <th>Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($pengaliranByPks as $pksName => $items)
                    <tr>
                        <td colspan="11" class="pks-group-header">
                            <i class="feather-home me-1"></i> {{ $pksName }}
                            <span class="badge bg-primary ms-2" style="font-size:10px;">{{ $items->count() }} record</span>
                        </td>
                    </tr>
                    @foreach($items as $i => $item)
                    <tr>
                        <td class="ps-3 fw-medium text-muted">{{ $i + 1 }}</td>
                        <td>
                            <span class="badge badge-pks bg-soft-primary text-primary">{{ $pksName }}</span>
                        </td>
                        <td>
                            <small class="text-nowrap">
                                {{ $item->jam_mulai ? substr($item->jam_mulai, 0, 5) : '-' }} - {{ $item->jam_selesai ? substr($item->jam_selesai, 0, 5) : '-' }}
                            </small>
                        </td>
                        <td>{{ $item->no_bak ?? '-' }}</td>
                        <td>{{ $item->blok ?? '-' }}</td>
                        <td class="text-end fw-bold">{{ number_format($item->flat_bed) }}</td>
                        <td class="text-end">
                            {{ number_format($item->vol_limbah_dihasilkan) }}
                            <small class="text-muted">m&sup3;</small>
                        </td>
                        <td class="text-end">
                            <span class="fw-bold text-success">{{ number_format($item->vol_limbah_dialirkan) }}</span>
                            <small class="text-muted">m&sup3;</small>
                        </td>
                        <td class="text-end">{{ $item->luas_area ?? '-' }}</td>
                        <td><small>{{ $item->rotasi ?? '-' }}</small></td>
                        <td><small class="text-muted">{{ Str::limit($item->keterangan, 30) ?: '-' }}</small></td>
                    </tr>
                    @endforeach
                    <tr class="summary-row">
                        <td colspan="5" class="ps-3">
                            <i class="feather-corner-down-right me-1"></i> Subtotal {{ $pksName }}
                        </td>
                        <td class="text-end">{{ number_format($items->sum('flat_bed')) }}</td>
                        <td class="text-end">{{ number_format($items->sum('vol_limbah_dihasilkan')) }}</td>
                        <td class="text-end">{{ number_format($items->sum('vol_limbah_dialirkan')) }}</td>
                        <td class="text-end">{{ number_format($items->sum('luas_area'), 1) }}</td>
                        <td colspan="2"></td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="5" class="ps-3">
                            <i class="feather-bar-chart me-1"></i> GRAND TOTAL PENGALIRAN
                        </td>
                        <td class="text-end">{{ number_format($summaryPengaliran['flat_bed']) }}</td>
                        <td class="text-end">{{ number_format($summaryPengaliran['vol_dihasilkan']) }} m&sup3;</td>
                        <td class="text-end">{{ number_format($summaryPengaliran['vol_dialirkan']) }} m&sup3;</td>
                        <td class="text-end">{{ number_format($summaryPengaliran['luas_area'], 1) }} Ha</td>
                        <td colspan="2"></td>
                    </tr>
                </tfoot>
            </table>
        </div>
        @else
        <div class="empty-report">
            <i class="feather-droplet d-block"></i>
            <h6 class="text-muted">Tidak Ada Data Pengaliran</h6>
            <p class="text-muted fs-13 mb-0">Belum ada data pengaliran pada periode {{ $periodeLabel }}</p>
        </div>
        @endif
    </div>
</div>

{{-- ========= PEMELIHARAAN SECTION ========= --}}
<div class="card section-card shadow-sm mb-4">
    <div class="section-header d-flex align-items-center justify-content-between">
        <h6>
            <i class="feather-tool me-2 text-warning"></i>
            Laporan Pemeliharaan
            <span class="badge bg-warning ms-2 fs-11">{{ $summaryPemeliharaan['count'] }} Data</span>
        </h6>
    </div>
    <div class="card-body p-0">
        @if($pemeliharaanData->count() > 0)
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th class="ps-3">#</th>
                        <th>PKS</th>
                        <th>Jenis</th>
                        <th>No Bak</th>
                        <th>Blok</th>
                        <th class="text-end">Flat Bed</th>
                        <th class="text-end">Long Bed</th>
                        <th class="text-end">Jml HK</th>
                        <th>Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($pemeliharaanByPks as $pksName => $items)
                    <tr>
                        <td colspan="9" class="pks-group-header">
                            <i class="feather-home me-1"></i> {{ $pksName }}
                            <span class="badge bg-warning ms-2" style="font-size:10px;">{{ $items->count() }} record</span>
                        </td>
                    </tr>
                    @foreach($items as $i => $item)
                    <tr>
                        <td class="ps-3 fw-medium text-muted">{{ $i + 1 }}</td>
                        <td>
                            <span class="badge badge-pks bg-soft-primary text-primary">{{ $pksName }}</span>
                        </td>
                        <td>
                            @if($item->jenis_pemeliharaan == '1')
                            <span class="badge badge-jenis bg-soft-info text-info">Mekanis</span>
                            @elseif($item->jenis_pemeliharaan == '2')
                            <span class="badge badge-jenis bg-soft-warning text-warning">Manual</span>
                            @else
                            <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>{{ $item->no_bak ?? '-' }}</td>
                        <td>{{ $item->blok ?? '-' }}</td>
                        <td class="text-end fw-bold">{{ number_format($item->flat_bed) }}</td>
                        <td class="text-end fw-bold text-warning">{{ number_format($item->long_bed) }}</td>
                        <td class="text-end">{{ $item->jumlah_hk ?? '-' }}</td>
                        <td><small class="text-muted">{{ Str::limit($item->keterangan, 35) ?: '-' }}</small></td>
                    </tr>
                    @endforeach
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
                    <tr class="summary-row">
                        <td colspan="5" class="ps-3">
                            <i class="feather-corner-down-right me-1"></i> Subtotal {{ $pksName }}
                        </td>
                        <td class="text-end">{{ number_format($subtotalFlatBed) }}</td>
                        <td class="text-end">{{ number_format($subtotalLongBed) }}</td>
                        <td class="text-end">{{ $subtotalJumlahHk }}</td>
                        <td></td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="5" class="ps-3">
                            <i class="feather-bar-chart me-1"></i> GRAND TOTAL PEMELIHARAAN
                        </td>
                        <td class="text-end">{{ number_format($summaryPemeliharaan['flat_bed']) }}</td>
                        <td class="text-end">{{ number_format($summaryPemeliharaan['long_bed']) }}</td>
                        <td class="text-end">{{ $summaryPemeliharaan['jumlah_hk'] }}</td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>
        @else
        <div class="empty-report">
            <i class="feather-tool d-block"></i>
            <h6 class="text-muted">Tidak Ada Data Pemeliharaan</h6>
            <p class="text-muted fs-13 mb-0">Belum ada data pemeliharaan pada periode {{ $periodeLabel }}</p>
        </div>
        @endif
    </div>
</div>

{{-- ========= RINGKASAN HARIAN ========= --}}
@if($pengaliranData->count() > 0 || $pemeliharaanData->count() > 0)
<div class="card section-card shadow-sm mb-4">
    <div class="section-header" style="background: linear-gradient(135deg, #f0f3ff 0%, #e8edff 100%);">
        <h6>
            <i class="feather-clipboard me-2 text-primary"></i>
            Ringkasan Harian &mdash; {{ $periodeLabel }}
        </h6>
    </div>
    <div class="card-body">
        <div class="row g-4">
            <div class="col-md-6">
                <div class="p-3 rounded-3 summary-box-pengaliran" style="background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%); border: 1px solid #bfdbfe;">
                    <h6 class="fw-bold text-primary mb-3">
                        <i class="feather-droplet me-1"></i> Pengaliran
                    </h6>
                    <div class="row g-2">
                        <div class="col-6">
                            <div class="d-flex justify-content-between">
                                <span class="text-muted fs-13">Jumlah Data</span>
                                <span class="fw-bold fs-13">{{ $summaryPengaliran['count'] }}</span>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="d-flex justify-content-between">
                                <span class="text-muted fs-13">Flat Bed</span>
                                <span class="fw-bold fs-13">{{ number_format($summaryPengaliran['flat_bed']) }}</span>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="d-flex justify-content-between">
                                <span class="text-muted fs-13">Vol. Dihasilkan</span>
                                <span class="fw-bold fs-13">{{ number_format($summaryPengaliran['vol_dihasilkan']) }} m&sup3;</span>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="d-flex justify-content-between">
                                <span class="text-muted fs-13">Vol. Dialirkan</span>
                                <span class="fw-bold text-success fs-13">{{ number_format($summaryPengaliran['vol_dialirkan']) }} m&sup3;</span>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="d-flex justify-content-between">
                                <span class="text-muted fs-13">Luas Area</span>
                                <span class="fw-bold fs-13">{{ number_format($summaryPengaliran['luas_area'], 1) }} Ha</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="p-3 rounded-3 summary-box-pemeliharaan" style="background: linear-gradient(135deg, #fffbeb 0%, #fef3c7 100%); border: 1px solid #fde68a;">
                    <h6 class="fw-bold text-warning mb-3">
                        <i class="feather-tool me-1"></i> Pemeliharaan
                    </h6>
                    <div class="row g-2">
                        <div class="col-6">
                            <div class="d-flex justify-content-between">
                                <span class="text-muted fs-13">Jumlah Data</span>
                                <span class="fw-bold fs-13">{{ $summaryPemeliharaan['count'] }}</span>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="d-flex justify-content-between">
                                <span class="text-muted fs-13">Flat Bed</span>
                                <span class="fw-bold fs-13">{{ number_format($summaryPemeliharaan['flat_bed']) }}</span>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="d-flex justify-content-between">
                                <span class="text-muted fs-13">Long Bed</span>
                                <span class="fw-bold fs-13">{{ number_format($summaryPemeliharaan['long_bed']) }}</span>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="d-flex justify-content-between">
                                <span class="text-muted fs-13">Jumlah HK</span>
                                <span class="fw-bold text-warning fs-13">{{ $summaryPemeliharaan['jumlah_hk'] }} Orang</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endif

@endsection

@section('scripts')
<script src="{{ asset('duraluxadmin/assets/vendors/js/select2.min.js') }}"></script>
<script src="{{ asset('duraluxadmin/assets/vendors/js/select2-active.min.js') }}"></script>
@endsection
