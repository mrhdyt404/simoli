@extends('layouts.simoli')

@section('title', 'Report Rencana')
@section('page-title', 'Report Rencana')

@section('breadcrumb')
<li class="breadcrumb-item">Report</li>
<li class="breadcrumb-item active">Report Rencana</li>
@endsection

@section('styles')
<link rel="stylesheet" type="text/css" href="{{ asset('duraluxadmin/assets/vendors/css/select2.min.css') }}" />
<link rel="stylesheet" type="text/css" href="{{ asset('duraluxadmin/assets/vendors/css/select2-theme.min.css') }}" />
<style>
    .print-only { display: none; }
    .report-header-card {
        border: none;
        border-radius: 12px;
        background: linear-gradient(135deg, #4338ca 0%, #6366f1 50%, #818cf8 100%);
        color: #fff;
    }
    .report-header-card .text-muted-light { color: rgba(255, 255, 255, 0.7); }
    .stat-card { border: none; border-radius: 12px; transition: transform 0.2s ease, box-shadow 0.2s ease; }
    .stat-card:hover { transform: translateY(-4px); box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1); }
    .stat-icon { width: 48px; height: 48px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 20px; }
    .filter-card { border: 1px solid #e9ecef; border-radius: 12px; background: linear-gradient(135deg, #f8f9ff 0%, #ffffff 100%); }
    .section-card { border: none; border-radius: 12px; overflow: hidden; }
    .pks-group-header {
        background: linear-gradient(135deg, #eef2ff 0%, #e0e7ff 100%);
        padding: 10px 24px;
        font-weight: 700;
        font-size: 13px;
        color: #4338ca;
        border-top: 1px solid #c7d2fe;
        border-bottom: 1px solid #c7d2fe;
    }
    .table thead th {
        background: #eef2ff;
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
    .table tbody tr:hover { background-color: #f8f9ff; }
    .table tfoot td { font-weight: 700; background: #eef2ff; border-top: 2px solid #c7d2fe; font-size: 13px; }
    .empty-report { padding: 50px 20px; text-align: center; }
    .empty-report i { font-size: 56px; color: #d1d5db; margin-bottom: 12px; }

    html.app-skin-dark .filter-card { border-color: #1b2436 !important; background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%) !important; }
    html.app-skin-dark .pks-group-header { background: linear-gradient(135deg, #1e1b4b 0%, #312e81 100%) !important; color: #a5b4fc !important; border-color: #3730a3 !important; }
    html.app-skin-dark .table thead th { background: #1e293b !important; color: #94a3b8 !important; }
    html.app-skin-dark .table tbody td { border-color: #1b2436 !important; }
    html.app-skin-dark .table tbody tr:hover { background-color: rgba(99,102,241,0.08) !important; }
    html.app-skin-dark .table tfoot td { background: #1e1b4b !important; border-color: #3730a3 !important; color: #a5b4fc !important; }
    html.app-skin-dark .section-card { border-color: #1b2436 !important; }

    @media print {
        @page { size: A4 portrait; margin: 6mm 12mm 12mm 12mm; }
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
        html.app-skin-dark .print-only {
            background: #fff !important;
            color: #000 !important;
        }
        html.app-skin-dark * { box-shadow: none !important; text-shadow: none !important; }
        .nxl-navigation, .nxl-header, .filter-card, .no-print,
        .page-header, .page-header-left, .page-header-title, .page-header-breadcrumb,
        .breadcrumb, .nxl-footer, .page-header-right-items,
        .report-header-card, .stat-card, .page-header-right-open-toggle,
        .d-md-none.d-flex.align-items-center { display: none !important; }
        .main-content > * { display: none !important; }
        .main-content > .print-only { display: block !important; }
        .print-only { display: block !important; }
        .nxl-container { top: 0 !important; margin-left: 0 !important; padding: 0 !important; min-height: auto !important; }
        .main-content { padding: 0 !important; }
        .nxl-content { padding-top: 0 !important; margin-top: 0 !important; }
        .nxl-container .page-header + .nxl-content { padding-top: 0 !important; }
        .nxl-header,
        .page-header { display: none !important; visibility: hidden !important; height: 0 !important; min-height: 0 !important; padding: 0 !important; margin: 0 !important; border: 0 !important; }
        .print-report-header { padding: 0; margin: 0 0 2px 0; border-bottom: 1px solid #000; }
        .print-report-header-table { width: 100%; border-collapse: collapse; border: none !important; margin-bottom: 0; }
        .print-report-header-table td { border: none !important; vertical-align: middle; padding: 0; }
        .print-header-logo-cell { width: 72px; text-align: left; }
        .print-header-logo { width: 60px; height: auto; }
        .print-header-text-cell { text-align: center; }
        .print-header-title { margin: 0; font-size: 14px; font-weight: 700; line-height: 1.1; text-transform: uppercase; }
        .print-header-subtitle { margin: 0; font-size: 10px; font-weight: 700; line-height: 1.1; }
        .print-header-meta-cell { width: 72px; text-align: right; vertical-align: middle; font-size: 9px; }
        .print-report-content { margin-top: 0; color: #000; }
        .print-report-content p { margin: 8px 0; }
        .print-data-section { margin-top: 16px; margin-bottom: 22px; }
        .print-data-title { margin: 0 0 8px; font-size: 10px; font-weight: 700; }
        .print-report-table { width: 100%; border-collapse: collapse; margin-top: 6px; margin-bottom: 16px; }
        .print-report-table th,
        .print-report-table td { border: 1px solid #000 !important; padding: 4px 5px; font-size: 9px; text-align: left; vertical-align: top; }
        .print-report-table th { font-weight: 700; text-align: center; }
        .print-report-table thead { display: table-header-group; }
        .print-report-table tfoot { display: table-footer-group; }
        .print-report-table tr { break-inside: avoid; page-break-inside: avoid; }
        .print-subtotal-row td,
        .print-total-row td { background: #f2f2f2; font-weight: 700; }
        .print-footer { margin-top: 18px; padding-top: 8px; border-top: 1px solid #000; display: flex; justify-content: space-between; align-items: center; font-size: 9px; }
        .print-footer-left { font-weight: 700; }
        .print-footer-right { text-align: right; }
        .table-responsive { overflow: visible !important; }
        .table { width: 100%; margin: 0 !important; font-size: 9px; border-collapse: collapse; }
        .table thead { display: table-header-group; }
        .table tfoot { display: table-footer-group; }
        .table thead th { background: #fff !important; color: #000 !important; border: 1px solid #000 !important; padding: 5px 4px !important; font-size: 8px !important; font-weight: 700 !important; text-transform: uppercase; white-space: nowrap; }
        .table tbody td,
        .table tfoot td { border: 1px solid #000 !important; padding: 3px 4px !important; font-size: 8px !important; color: #000 !important; }
        .pks-group-header { background: #eef2ff !important; color: #4338ca !important; border: 1px solid #000 !important; padding: 3px 4px !important; font-size: 8px !important; font-weight: 700 !important; text-transform: uppercase; }
        .badge { border: none !important; background: transparent !important; color: #000 !important; font-size: 8px !important; padding: 0 !important; }
        .empty-report { padding: 20px; text-align: center; }
    }
</style>
@endsection

@section('content')
@php
    $tahun = $tahun ?? date('Y');
@endphp

<div class="print-only print-report-header">
    <table class="print-report-header-table">
        <tr>
            <td class="print-header-logo-cell">
                <img class="print-header-logo" src="{{ asset('logo/Icon%20SIMOLI.png') }}" alt="SIMOLI">
            </td>
            <td class="print-header-text-cell">
                <div class="print-header-title">Report Rencana SIMOLI</div>
                <div class="print-header-subtitle">PT. Perkebunan Nusantara V</div>
                <div class="print-header-subtitle">Sistem Monitoring Limbah</div>
            </td>
            <td class="print-header-meta-cell">
                <div><strong>Periode</strong></div>
                <div>Tahun {{ $tahun }}</div>
            </td>
        </tr>
    </table>
</div>

<div class="print-only print-report-content">
    <p>Berikut report rencana SIMOLI untuk tahun {{ $tahun }}, yang memuat data per PKS:</p>

    @if($rencanaData->count() > 0)
    @foreach($rencanaByPks as $pksName => $items)
    <div class="print-data-section">
        <div class="print-data-title">PKS {{ $pksName }}</div>
        <table class="print-report-table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Tahun</th>
                    <th>Flat Bed</th>
                    <th>Long Bed</th>
                    <th>Total Bed</th>
                </tr>
            </thead>
            <tbody>
                @foreach($items as $j => $item)
                <tr>
                    <td style="text-align:center;">{{ $j + 1 }}</td>
                    <td>{{ $item->tahun }}</td>
                    <td style="text-align:right;">{{ number_format($item->flat_bed) }}</td>
                    <td style="text-align:right;">{{ number_format($item->long_bed) }}</td>
                    <td style="text-align:right;">{{ number_format($item->flat_bed + $item->long_bed) }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr class="print-subtotal-row">
                    <td colspan="2" class="text-end">Subtotal {{ $pksName }}</td>
                    <td style="text-align:right;">{{ number_format($items->sum('flat_bed')) }}</td>
                    <td style="text-align:right;">{{ number_format($items->sum('long_bed')) }}</td>
                    <td style="text-align:right;">{{ number_format($items->sum('flat_bed') + $items->sum('long_bed')) }}</td>
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
        <div class="print-empty-state">Tidak ada data rencana pada periode ini.</div>
    </div>
    @endif
</div>

<div class="card report-header-card shadow-sm mb-4">
    <div class="card-body p-4">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
            <div>
                <h4 class="fw-bold mb-1"><i class="feather-clipboard me-2"></i>Report Rencana</h4>
                <p class="text-muted-light mb-0 fs-13">Laporan rencana pengaliran dan pemeliharaan tahun {{ $tahun }}</p>
            </div>
            <div class="no-print">
                <button onclick="window.print()" class="btn btn-light btn-sm">
                    <i class="feather-printer me-1"></i> Print
                </button>
            </div>
        </div>
    </div>
</div>

<div class="row mb-4 no-print">
    <div class="col-md-4">
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
    <div class="col-md-4">
        <div class="card stat-card shadow-sm">
            <div class="card-body p-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="stat-icon bg-soft-success text-success"><i class="feather-layers"></i></div>
                    <div>
                        <h3 class="fw-bold mb-0">{{ number_format($summary['flat_bed']) }}</h3>
                        <span class="fs-12 text-muted">Flat Bed Rencana</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card stat-card shadow-sm">
            <div class="card-body p-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="stat-icon bg-soft-warning text-warning"><i class="feather-maximize-2"></i></div>
                    <div>
                        <h3 class="fw-bold mb-0">{{ number_format($summary['long_bed']) }}</h3>
                        <span class="fs-12 text-muted">Long Bed Rencana</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card filter-card shadow-sm mb-4 no-print">
    <div class="card-body py-3">
        <form action="{{ route('report-rencana') }}" method="GET">
            <div class="row g-3 align-items-end">
                @if(Auth::user()->isAdmin())
                <div class="col-lg-4 col-md-6">
                    <label class="form-label fs-12 fw-semibold text-muted"><i class="feather-home me-1"></i> Unit PKS</label>
                    <select name="id_pks" class="form-control" data-select2-selector="status">
                        <option value="">Semua PKS</option>
                        @foreach($pksList as $pks)
                        <option value="{{ $pks->ID }}" {{ request('id_pks') == $pks->ID ? 'selected' : '' }}>{{ $pks->nama }} ({{ $pks->akro }})</option>
                        @endforeach
                    </select>
                </div>
                @endif
                <div class="col-lg-3 col-md-6">
                    <label class="form-label fs-12 fw-semibold text-muted"><i class="feather-calendar me-1"></i> Tahun</label>
                    <select name="tahun" class="form-select">
                        @foreach($years as $y)
                        <option value="{{ $y }}" {{ $tahun == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-lg-5 col-md-12">
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary flex-grow-1"><i class="feather-search me-1"></i> Tampilkan</button>
                        <a href="{{ route('report-rencana') }}" class="btn btn-outline-secondary"><i class="feather-refresh-cw me-1"></i> Reset</a>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

@if($rencanaData->count() > 0)
<div class="card section-card shadow-sm">
    <div class="card-header py-3">
        <h6 class="mb-0 fw-bold"><i class="feather-clipboard me-2 text-primary"></i>Data Rencana — Tahun {{ $tahun }}</h6>
    </div>
    <div class="card-body p-0">
        @foreach($rencanaByPks as $pksName => $items)
        <div class="pks-group-header">
            <i class="feather-home me-1"></i> {{ $pksName }}
            <span class="badge bg-primary ms-2">{{ $items->count() }} data</span>
        </div>
        <div class="table-responsive">
            <table class="table mb-0">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Tahun</th>
                        <th class="text-end">Flat Bed</th>
                        <th class="text-end">Long Bed</th>
                        <th class="text-end">Total Bed</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($items as $j => $item)
                    <tr>
                        <td>{{ $j + 1 }}</td>
                        <td>{{ $item->tahun }}</td>
                        <td class="text-end">{{ number_format($item->flat_bed) }}</td>
                        <td class="text-end">{{ number_format($item->long_bed) }}</td>
                        <td class="text-end">{{ number_format($item->flat_bed + $item->long_bed) }}</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="2" class="text-end fw-bold">Subtotal {{ $pksName }}</td>
                        <td class="text-end">{{ number_format($items->sum('flat_bed')) }}</td>
                        <td class="text-end">{{ number_format($items->sum('long_bed')) }}</td>
                        <td class="text-end">{{ number_format($items->sum('flat_bed') + $items->sum('long_bed')) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
        @endforeach
    </div>
    <div class="card-footer py-3">
        <div class="row text-center">
            <div class="col"><strong>Total Records:</strong> {{ $summary['count'] }}</div>
            <div class="col"><strong>Flat Bed:</strong> {{ number_format($summary['flat_bed']) }}</div>
            <div class="col"><strong>Long Bed:</strong> {{ number_format($summary['long_bed']) }}</div>
            <div class="col"><strong>Total Bed:</strong> {{ number_format($summary['total_bed']) }}</div>
        </div>
    </div>
</div>
@else
<div class="card section-card shadow-sm">
    <div class="card-body">
        <div class="empty-report">
            <i class="feather-inbox d-block"></i>
            <h5 class="text-muted mt-3">Tidak ada data rencana</h5>
            <p class="text-muted fs-13">Untuk tahun {{ $tahun }}</p>
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
