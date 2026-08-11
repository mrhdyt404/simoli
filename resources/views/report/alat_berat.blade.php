@extends('layouts.simoli')

@section('title', 'Report Monitoring Alat Berat')
@section('page-title', 'Report Monitoring Alat Berat')

@section('breadcrumb')
<li class="breadcrumb-item">Report</li>
<li class="breadcrumb-item active">Report Alat Berat</li>
@endsection

@php
    $namaBulan = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
@endphp

@section('styles')
<style>
    .print-only {
        display: none;
    }
    .report-header-card {
        border: none;
        border-radius: 12px;
        background: linear-gradient(135deg, #1e40af 0%, #3b82f6 50%, #60a5fa 100%);
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
        background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
        padding: 10px 24px;
        font-weight: 700;
        font-size: 13px;
        color: #1e40af;
        border-top: 1px solid #bfdbfe;
        border-bottom: 1px solid #bfdbfe;
    }
    .table thead th {
        background: #eff6ff;
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
    .table tbody tr:hover { background-color: #eff6ff; }
    .table tfoot td { font-weight: 700; background: #dbeafe; border-top: 2px solid #bfdbfe; font-size: 13px; }
    .empty-report { padding: 50px 20px; text-align: center; }
    .empty-report i { font-size: 56px; color: #d1d5db; margin-bottom: 12px; }

    /* Dark mode overrides */
    html.app-skin-dark .filter-card { border-color: #1b2436 !important; background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%) !important; }
    html.app-skin-dark .pks-group-header { background: linear-gradient(135deg, #1e3a8a 0%, #1e40af 100%) !important; color: #93c5fd !important; border-color: #1e40af !important; }
    html.app-skin-dark .table thead th { background: #1e293b !important; color: #94a3b8 !important; }
    html.app-skin-dark .table tbody td { border-color: #1b2436 !important; }
    html.app-skin-dark .table tbody tr:hover { background-color: rgba(59,130,246,0.08) !important; }
    html.app-skin-dark .table tfoot td { background: #172554 !important; border-color: #1e40af !important; color: #93c5fd !important; }
    html.app-skin-dark .section-card { border-color: #1b2436 !important; }

    @media print {
        @page {
            size: A4 landscape;
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
        .report-header-card, .stat-card, .section-card, .page-header-right-open-toggle,
        .d-md-none.d-flex.align-items-center {
            display: none !important;
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
            width: 100px;
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
            background: #f3f4f6 !important;
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

        .print-subtotal-row td,
        .print-total-row td {
            background: #f2f2f2 !important;
            font-weight: 700;
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
    }
</style>
@endsection

@section('content')

{{-- Header Cetak --}}
<div class="print-only print-report-header">
    <table class="print-report-header-table">
        <tr>
            <td class="print-header-logo-cell">
                <img class="print-header-logo" src="{{ asset('logo/Icon%20SIMOLI.png') }}" alt="SIMOLI">
            </td>
            <td class="print-header-text-cell">
                <div class="print-header-title">Report Alat Berat SIMOLI</div>
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

{{-- Content Cetak --}}
<div class="print-only print-report-content">
    <p>Berikut report monitoring alat berat SIMOLI untuk bulan {{ $namaBulan[(int)$bulan] }} {{ $tahun }}, yang memuat data per PKS:</p>

    @if($data->count() > 0)
        @foreach($dataByPks as $pksName => $items)
        <div class="print-data-section">
            <div class="print-data-title">PKS {{ $pksName }}</div>
            <table class="print-report-table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Tanggal</th>
                        <th>Kode Alat</th>
                        <th>Nama Alat</th>
                        <th>Operator</th>
                        <th>Kegiatan & Lokasi</th>
                        <th>Flat / Long Bed</th>
                        <th>HM Awal</th>
                        <th>HM Akhir</th>
                        <th>Total HM</th>
                        <th>BBM (L)</th>
                        <th>Kondisi</th>
                        <th>Catatan</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($items as $j => $item)
                    <tr>
                        <td style="text-align:center;">{{ $j + 1 }}</td>
                        <td>{{ \Carbon\Carbon::parse($item->tanggal)->format('d/m/Y') }}</td>
                        <td>{{ $item->alatBerat ? $item->alatBerat->kode_alat : '-' }}</td>
                        <td>{{ $item->alatBerat ? $item->alatBerat->nama_alat : '-' }}</td>
                        <td>{{ $item->operator }}</td>
                        <td>
                            {{ $item->kegiatan }} {{ $item->lokasi_blok ? '('.$item->lokasi_blok.')' : '' }}
                            @if($item->latitude && $item->longitude)
                                <div style="font-size: 8px; color: #4b5563;">GPS: {{ $item->latitude }}, {{ $item->longitude }}</div>
                            @endif
                        </td>
                        <td style="text-align:center;">F: {{ $item->flat_bed ?? 0 }}<br>L: {{ $item->long_bed ?? 0 }}</td>
                        <td style="text-align:center;">{{ $item->hm_awal_formatted }}</td>
                        <td style="text-align:center;">{{ $item->hm_akhir_formatted }}</td>
                        <td style="text-align:right;">{{ $item->total_hm_formatted }}</td>
                        <td style="text-align:right;">{{ number_format($item->bbm_liter, 0) }}</td>
                        <td style="text-align:center;">{{ $item->kondisi_alat }}</td>
                        <td>{{ $item->catatan ?? '-' }}</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="print-subtotal-row">
                        <td colspan="6" style="text-align:right;">Subtotal {{ $pksName }}</td>
                        <td style="text-align:center;">Flat: {{ $items->sum('flat_bed') }}<br>Long: {{ $items->sum('long_bed') }}</td>
                        <td colspan="2"></td>
                        <td style="text-align:right;">{{ \App\Models\MonitoringAlatBerat::formatHm($items->sum('total_hm'), true) }}</td>
                        <td style="text-align:right;">{{ number_format($items->sum('bbm_liter'), 0) }} L</td>
                        <td colspan="2"></td>
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
            <div class="print-empty-state">Tidak ada data monitoring alat berat pada periode ini.</div>
        </div>
    @endif
</div>

{{-- Header Banner Card Web --}}
<div class="card report-header-card shadow-sm mb-4">
    <div class="card-body p-4">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
            <div>
                <h4 class="fw-bold mb-1 text-white"><i class="feather-truck me-2 text-white"></i>Report Alat Berat</h4>
                <p class="text-white opacity-90 mb-0 fs-13">
                    Laporan data operasional dan jam kerja (HM) alat berat bulan {{ $namaBulan[(int)$bulan] }} {{ $tahun }}
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

{{-- Summary Metric Cards Web --}}
<div class="row mb-4 no-print">
    <div class="col-xl-3 col-sm-6 col-12 mb-3">
        <div class="card stat-card shadow-sm h-100">
            <div class="card-body p-3">
                <div class="d-flex align-items-center gap-3 overflow-hidden">
                    <div class="stat-icon bg-soft-primary text-primary flex-shrink-0"><i class="feather-clock"></i></div>
                    <div class="overflow-hidden me-1">
                        <h4 class="fw-bold mb-0 text-nowrap text-primary fs-18">{{ \App\Models\MonitoringAlatBerat::formatHm($summary['total_hm'], true) }}</h4>
                        <span class="fs-12 text-muted text-truncate d-block">Total Jam Kerja (HM)</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-sm-6 col-12 mb-3">
        <div class="card stat-card shadow-sm h-100">
            <div class="card-body p-3">
                <div class="d-flex align-items-center gap-3 overflow-hidden">
                    <div class="stat-icon bg-soft-info text-info flex-shrink-0"><i class="feather-grid"></i></div>
                    <div class="overflow-hidden me-1">
                        <h4 class="fw-bold mb-0 text-nowrap text-info fs-18">{{ number_format($summary['total_bed'] ?? 0) }} Bed</h4>
                        <span class="fs-12 text-muted text-truncate d-block">Flat: {{ number_format($summary['total_flat_bed'] ?? 0) }} | Long: {{ number_format($summary['total_long_bed'] ?? 0) }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-sm-6 col-12 mb-3">
        <div class="card stat-card shadow-sm h-100">
            <div class="card-body p-3">
                <div class="d-flex align-items-center gap-3 overflow-hidden">
                    <div class="stat-icon bg-soft-warning text-warning flex-shrink-0"><i class="feather-droplet"></i></div>
                    <div class="overflow-hidden me-1">
                        <h4 class="fw-bold mb-0 text-nowrap text-warning fs-18">{{ number_format($summary['total_bbm'], 0) }} L</h4>
                        <span class="fs-12 text-muted text-truncate d-block">Total Konsumsi BBM</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-sm-6 col-12 mb-3">
        <div class="card stat-card shadow-sm h-100">
            <div class="card-body p-3">
                <div class="d-flex align-items-center gap-3 overflow-hidden">
                    <div class="stat-icon bg-soft-success text-success flex-shrink-0"><i class="feather-activity"></i></div>
                    <div class="overflow-hidden me-1">
                        <h4 class="fw-bold mb-0 text-nowrap text-success fs-18">{{ $summary['total_kegiatan'] }} Log</h4>
                        <span class="fs-12 text-muted text-truncate d-block">Total Kegiatan</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Filter Card Web --}}
<div class="card filter-card shadow-sm mb-4 no-print">
    <div class="card-body py-3">
        <form method="GET" action="{{ route('report-alat-berat') }}">
            <div class="row g-3 align-items-end">
                <div class="col-lg-3 col-md-6">
                    <label class="form-label fs-12 fw-semibold text-muted"><i class="feather-home me-1"></i> Unit PKS</label>
                    <select name="id_pks" class="form-select">
                        <option value="">Semua PKS Unit</option>
                        @foreach($pksList as $pks)
                            <option value="{{ $pks->id_pks }}" {{ request('id_pks') == $pks->id_pks ? 'selected' : '' }}>
                                {{ $pks->nama }} ({{ $pks->akro }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-lg-3 col-md-6">
                    <label class="form-label fs-12 fw-semibold text-muted"><i class="feather-truck me-1"></i> Alat Berat</label>
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
                    <label class="form-label fs-12 fw-semibold text-muted"><i class="feather-calendar me-1"></i> Bulan</label>
                    <select name="bulan" class="form-select">
                        @for($m = 1; $m <= 12; $m++)
                            <option value="{{ sprintf('%02d', $m) }}" {{ $bulan == sprintf('%02d', $m) ? 'selected' : '' }}>
                                {{ $namaBulan[$m] }}
                            </option>
                        @endfor
                    </select>
                </div>

                <div class="col-lg-2 col-md-6">
                    <label class="form-label fs-12 fw-semibold text-muted"><i class="feather-calendar me-1"></i> Tahun</label>
                    <select name="tahun" class="form-select">
                        @foreach($years as $y)
                            <option value="{{ $y }}" {{ $tahun == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-lg-2 col-md-12">
                    <button type="submit" class="btn btn-primary w-100"><i class="feather-search me-1"></i> Tampilkan</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Table Report Web -->
<div class="card section-card shadow-sm">
    <div class="card-header py-3 d-flex justify-content-between align-items-center">
        <h6 class="mb-0 fw-bold"><i class="feather-truck me-2 text-primary"></i>Laporan Operasional Periodik — {{ $namaBulan[(int)$bulan] }} {{ $tahun }}</h6>
        <span class="badge bg-primary text-white">Total {{ $summary['total_kegiatan'] }} Record Log</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover table-bordered mb-0">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Tanggal</th>
                        <th>Unit PKS</th>
                        <th>Kode & Nama Alat Berat</th>
                        <th>Operator</th>
                        <th>Jenis Kegiatan Pengolahan</th>
                        <th>Flat / Long Bed</th>
                        <th>HM Awal</th>
                        <th>HM Akhir</th>
                        <th>Total HM</th>
                        <th>BBM (L)</th>
                        <th>Kondisi Alat</th>
                        <th>Catatan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($dataByPks as $pksNama => $logsGroup)
                        <tr class="pks-group-header">
                            <td colspan="13">
                                <i class="feather-home me-2"></i>Unit PKS: {{ $pksNama }} (Total: {{ $logsGroup->count() }} Kegiatan)
                            </td>
                        </tr>
                        @foreach($logsGroup as $idx => $item)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ \Carbon\Carbon::parse($item->tanggal)->format('d/m/Y') }}</td>
                                <td>{{ $item->pks ? $item->pks->akro : '-' }}</td>
                                <td>
                                    <span class="fw-bold text-primary">{{ $item->alatBerat ? $item->alatBerat->kode_alat : '-' }}</span>
                                    <div class="small text-muted">{{ $item->alatBerat ? $item->alatBerat->nama_alat : '-' }}</div>
                                </td>
                                <td>{{ $item->operator }}</td>
                                <td>
                                    {{ $item->kegiatan }}
                                    @if($item->lokasi_blok)
                                        <div class="small text-muted">Loc: {{ $item->lokasi_blok }}</div>
                                    @endif
                                    @if($item->latitude && $item->longitude)
                                        <div class="mt-1">
                                            <a href="{{ $item->google_maps_url }}" target="_blank" class="badge bg-soft-primary text-primary text-decoration-none" title="Buka di Maps">
                                                <i class="feather-map-pin me-1"></i>{{ number_format($item->latitude, 4) }}, {{ number_format($item->longitude, 4) }}
                                            </a>
                                        </div>
                                    @endif
                                </td>
                                <td><span class="badge bg-soft-info text-info fs-12" title="Flat: {{ $item->flat_bed ?? 0 }} / Long: {{ $item->long_bed ?? 0 }}">F: {{ $item->flat_bed ?? 0 }} | L: {{ $item->long_bed ?? 0 }} Bed</span></td>
                                <td>{{ $item->hm_awal_formatted }}</td>
                                <td>{{ $item->hm_akhir_formatted }}</td>
                                <td><span class="fw-bold text-primary">{{ $item->total_hm_formatted }}</span></td>
                                <td>{{ number_format($item->bbm_liter, 0) }}</td>
                                <td>
                                    @if($item->kondisi_alat == 'Normal')
                                        <span class="badge bg-success text-white">Normal</span>
                                    @elseif($item->kondisi_alat == 'Perlu Perbaikan')
                                        <span class="badge bg-warning text-dark">Perlu Perbaikan</span>
                                    @else
                                        <span class="badge bg-danger text-white">Breakdown</span>
                                    @endif
                                </td>
                                <td class="text-truncate" style="max-width: 150px;">{{ $item->catatan ?? '-' }}</td>
                            </tr>
                        @endforeach
                        <tr class="fw-bold bg-soft-primary">
                            <td colspan="6" class="text-end">Subtotal {{ $pksNama }}:</td>
                            <td>F: {{ $logsGroup->sum('flat_bed') }} | L: {{ $logsGroup->sum('long_bed') }} Bed</td>
                            <td colspan="2"></td>
                            <td>{{ \App\Models\MonitoringAlatBerat::formatHm($logsGroup->sum('total_hm'), true) }}</td>
                            <td>{{ number_format($logsGroup->sum('bbm_liter'), 0) }} L</td>
                            <td colspan="2"></td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="13" class="text-center py-5 text-muted">
                                <i class="feather-inbox fs-3 d-block mb-2"></i>
                                Tidak ada data monitoring alat berat pada periode ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
