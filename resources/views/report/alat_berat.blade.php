@extends('layouts.simoli')

@section('title', 'Report Monitoring Alat Berat')
@section('page-title', 'Report Alat Berat')
@section('page-description', 'Laporan Operasional Periodik & Jam Kerja (HM) Alat Berat Pengolahan Limbah')

@section('breadcrumb')
    <li>Report</li>
    <li class="separator">/</li>
    <li>Report Alat Berat</li>
@endsection

@php
    $namaBulan = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
@endphp

@section('styles')
<link rel="stylesheet" type="text/css" href="{{ asset('duraluxadmin/assets/vendors/css/select2.min.css') }}" />
<link rel="stylesheet" type="text/css" href="{{ asset('duraluxadmin/assets/vendors/css/select2-theme.min.css') }}" />
<style>
    /* ================================================================
       REPORT ALAT BERAT — PTPN GREEN THEME
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
    .rab-kpi {
        background: #ffffff;
        border-radius: 18px;
        border: 1px solid rgba(22,163,74,.1);
        box-shadow: 0 2px 12px rgba(22,163,74,.06);
        padding: 18px;
        height: 100%;
        transition: all .25s ease;
        animation: fadeUpCard .4s ease-out;
    }
    .rab-kpi:hover { transform: translateY(-3px); box-shadow: 0 10px 26px rgba(22,163,74,.12); }

    .rab-kpi-icon {
        width: 46px;
        height: 46px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        flex-shrink: 0;
    }
    .rab-kpi-icon.icon-green { background: #f0fdf4; color: #16a34a; border: 1px solid #bbf7d0; }
    .rab-kpi-icon.icon-teal  { background: #f0fdfa; color: #0d9488; border: 1px solid #99f6e4; }
    .rab-kpi-icon.icon-amber { background: #fffbeb; color: #b45309; border: 1px solid #fde68a; }
    .rab-kpi-icon.icon-blue  { background: #eff6ff; color: #1d4ed8; border: 1px solid #bfdbfe; }

    .rab-kpi-val {
        font-family: 'Outfit', sans-serif;
        font-weight: 900;
        margin: 0 0 2px;
    }
    .rab-kpi-val.val-green { color: #14532d; }
    .rab-kpi-val.val-teal  { color: #0d9488; }
    .rab-kpi-val.val-amber { color: #b45309; }
    .rab-kpi-val.val-blue  { color: #1d4ed8; }

    .rab-kpi-label {
        font-size: 11.5px;
        color: #6b7280;
        font-weight: 600;
        display: block;
    }
    .rab-kpi-sub {
        font-size: 11px;
        color: #6b7280;
        font-weight: 600;
    }

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
        color: #1a2e22;
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
    .filter-card .select2-container--default .select2-selection--single .select2-selection__rendered { line-height: 28px; font-size: 13px; color: #1a2e22; }
    .filter-card .select2-container--default .select2-selection--single .select2-selection__arrow { height: 40px; }

    /* === DATA TABLE SECTION === */
    .section-card {
        background: #ffffff;
        border-radius: 18px;
        border: 1px solid rgba(22,163,74,.1);
        box-shadow: 0 2px 12px rgba(22,163,74,.06);
        overflow: hidden;
    }

    .section-card-header {
        padding: 16px 20px;
        border-bottom: 1px solid rgba(22,163,74,.08);
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .section-card-title {
        font-family: 'Outfit', sans-serif;
        font-size: 15px;
        font-weight: 800;
        color: #14532d;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .pks-group-header {
        background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
        font-weight: 800;
        font-size: 13px;
        color: #166534;
        border-top: 1px solid #bbf7d0;
        border-bottom: 1px solid #bbf7d0;
    }
    .pks-group-header td {
        padding: 12px 20px !important;
    }

    .report-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
        font-size: clamp(11px, .65vw+8.5px, 12.5px);
    }

    .report-table thead th {
        background: #052e16;
        color: #86efac;
        font-size: 10px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .5px;
        padding: 11px 14px;
        border: none;
        white-space: nowrap;
    }

    .report-table tbody td {
        padding: 10px 14px;
        border-bottom: 1px solid rgba(22,163,74,.07);
        vertical-align: middle;
        color: #374151;
    }

    .report-table tbody tr:hover td { background: rgba(22,163,74,.03); }

    .pks-subtotal-row td {
        background: #f0fdf4;
        border-top: 1px solid #bbf7d0;
        border-bottom: 1px solid #bbf7d0;
        font-weight: 800;
        color: #14532d;
        padding: 11px 14px;
    }

    /* Module Pill */
    .mod-pill {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 3px 9px;
        border-radius: 50px;
        font-size: 10.5px;
        font-weight: 700;
        white-space: nowrap;
    }
    .mod-pill-ok   { background: #dcfce7; color: #15803d; border: 1px solid #bbf7d0; }
    .mod-pill-info { background: #eff6ff; color: #1d4ed8; border: 1px solid #bfdbfe; }
    .mod-pill-warn { background: #fef3c7; color: #b45309; border: 1px solid #fde68a; }
    .mod-pill-err  { background: #fee2e2; color: #dc2626; border: 1px solid #fca5a5; }

    /* Specific Table Data Styles */
    .tbl-code { font-weight: 800; color: #16a34a; font-size: 12.5px; }
    .tbl-name { color: #6b7280; font-size: 11px; }
    .tbl-operator { font-weight: 600; color: #374151; }
    .tbl-kegiatan { font-weight: 700; color: #14532d; }
    .tbl-subtext { color: #6b7280; font-size: 11px; }
    .tbl-hm-range { font-weight: 600; color: #6b7280; }
    .tbl-hm-total { font-weight: 800; color: #16a34a; }
    .tbl-bbm-val { font-weight: 800; color: #b45309; }
    .tbl-catatan { color: #6b7280; }

    .pks-badge {
        display: inline-flex;
        align-items: center;
        padding: 2px 8px;
        border-radius: 6px;
        font-size: 10.5px;
        font-weight: 800;
        background: linear-gradient(135deg, #052e16, #166534);
        color: #86efac;
        border: 1px solid rgba(134,239,172,.2);
    }

    /* ================================================================
       DARK MODE OVERRIDES
       ================================================================ */
    html.app-skin-dark .filter-card,
    html.app-skin-dark .rab-kpi,
    html.app-skin-dark .section-card {
        background: #0a2317 !important;
        border-color: rgba(34,197,94,.18) !important;
    }

    html.app-skin-dark .rab-kpi:hover {
        box-shadow: 0 10px 26px rgba(0,0,0,.4) !important;
    }

    /* KPI in Dark Mode */
    html.app-skin-dark .rab-kpi-val.val-green { color: #4ade80 !important; }
    html.app-skin-dark .rab-kpi-val.val-teal  { color: #2dd4bf !important; }
    html.app-skin-dark .rab-kpi-val.val-amber { color: #fbbf24 !important; }
    html.app-skin-dark .rab-kpi-val.val-blue  { color: #60a5fa !important; }
    html.app-skin-dark .rab-kpi-label,
    html.app-skin-dark .rab-kpi-sub { color: #9ca3af !important; }

    html.app-skin-dark .rab-kpi-icon.icon-green { background: rgba(34,197,94,.15) !important; border-color: rgba(34,197,94,.3) !important; color: #4ade80 !important; }
    html.app-skin-dark .rab-kpi-icon.icon-teal  { background: rgba(20,184,166,.15) !important; border-color: rgba(20,184,166,.3) !important; color: #2dd4bf !important; }
    html.app-skin-dark .rab-kpi-icon.icon-amber { background: rgba(245,158,11,.15) !important; border-color: rgba(245,158,11,.3) !important; color: #fbbf24 !important; }
    html.app-skin-dark .rab-kpi-icon.icon-blue  { background: rgba(59,130,246,.15) !important; border-color: rgba(59,130,246,.3) !important; color: #60a5fa !important; }

    /* Filter in Dark Mode */
    html.app-skin-dark .filter-card label.form-label { color: #86efac !important; }
    html.app-skin-dark .filter-card .form-control,
    html.app-skin-dark .filter-card .form-select {
        background: #0e3b26 !important;
        border-color: rgba(34,197,94,.25) !important;
        color: #d1fae5 !important;
    }
    html.app-skin-dark .filter-card .form-select option {
        background: #0a2317 !important;
        color: #d1fae5 !important;
    }

    /* Select2 in Dark Mode */
    html.app-skin-dark .select2-container--default .select2-selection--single {
        background: #0e3b26 !important;
        border-color: rgba(34,197,94,.25) !important;
    }
    html.app-skin-dark .select2-container--default .select2-selection--single .select2-selection__rendered {
        color: #d1fae5 !important;
    }
    html.app-skin-dark .select2-container--default .select2-selection--single .select2-selection__arrow b {
        border-color: #86efac transparent transparent transparent !important;
    }
    html.app-skin-dark .select2-dropdown {
        background-color: #0a2317 !important;
        border-color: rgba(34,197,94,.3) !important;
        color: #d1fae5 !important;
    }
    html.app-skin-dark .select2-container--default .select2-search--dropdown .select2-search__field {
        background-color: #0e3b26 !important;
        border-color: rgba(34,197,94,.25) !important;
        color: #d1fae5 !important;
    }
    html.app-skin-dark .select2-container--default .select2-results__option {
        color: #d1fae5 !important;
    }
    html.app-skin-dark .select2-container--default .select2-results__option--highlighted[aria-selected] {
        background-color: #166534 !important;
        color: #ffffff !important;
    }
    html.app-skin-dark .select2-container--default .select2-results__option[aria-selected=true] {
        background-color: #0e3b26 !important;
        color: #86efac !important;
    }

    /* Section Card Header */
    html.app-skin-dark .section-card-header { border-bottom-color: rgba(34,197,94,.12) !important; }
    html.app-skin-dark .section-card-title { color: #d1fae5 !important; }

    /* Table in Dark Mode */
    html.app-skin-dark .pks-group-header {
        background: linear-gradient(135deg, #0e3b26 0%, #052e16 100%) !important;
        border-color: rgba(34,197,94,.2) !important;
        color: #86efac !important;
    }
    html.app-skin-dark .report-table thead th { background: #021a0b !important; }
    html.app-skin-dark .report-table tbody td {
        border-color: rgba(34,197,94,.08) !important;
        color: #d1fae5 !important;
    }
    html.app-skin-dark .report-table tbody tr:hover td { background: rgba(34,197,94,.05) !important; }

    html.app-skin-dark .pks-subtotal-row td {
        background: #0e3b26 !important;
        border-color: rgba(34,197,94,.25) !important;
        color: #86efac !important;
    }

    /* Row Text in Dark Mode */
    html.app-skin-dark .tbl-code { color: #4ade80 !important; }
    html.app-skin-dark .tbl-name { color: #9ca3af !important; }
    html.app-skin-dark .tbl-operator { color: #e2f5ea !important; }
    html.app-skin-dark .tbl-kegiatan { color: #86efac !important; }
    html.app-skin-dark .tbl-subtext { color: #9ca3af !important; }
    html.app-skin-dark .tbl-hm-range { color: #d1fae5 !important; }
    html.app-skin-dark .tbl-hm-total { color: #4ade80 !important; }
    html.app-skin-dark .tbl-bbm-val { color: #fbbf24 !important; }
    html.app-skin-dark .tbl-catatan { color: #9ca3af !important; }

    /* Mod Pills in Dark Mode */
    html.app-skin-dark .mod-pill-ok {
        background: rgba(34,197,94,.18) !important;
        color: #86efac !important;
        border-color: rgba(34,197,94,.35) !important;
    }
    html.app-skin-dark .mod-pill-info {
        background: rgba(59,130,246,.18) !important;
        color: #93c5fd !important;
        border-color: rgba(59,130,246,.35) !important;
    }
    html.app-skin-dark .mod-pill-warn {
        background: rgba(245,158,11,.18) !important;
        color: #fde047 !important;
        border-color: rgba(245,158,11,.35) !important;
    }
    html.app-skin-dark .mod-pill-err {
        background: rgba(239,68,68,.18) !important;
        color: #fca5a5 !important;
        border-color: rgba(239,68,68,.35) !important;
    }

    /* === PRINT === */
    @media print {
        @page { size: A4 landscape; margin: 6mm 10mm 10mm 10mm; }
        html, body { width:100% !important;margin:0 !important;padding:0 !important;background:#fff !important;color:#000 !important;-webkit-print-color-adjust:exact !important;print-color-adjust:exact !important; }
        .nxl-navigation, .nxl-header, .filter-card, .no-print,
        .page-hero-strip, .page-header, .breadcrumb, .nxl-footer,
        .report-header-card, .rab-kpi, .btn, .btn-ptpn { display:none !important; }
        .main-content, .nxl-container, .nxl-content { padding:0 !important;margin:0 !important; }
        .print-only { display:block !important; }
        .print-report-header { border-bottom:1px solid #000;margin-bottom:12px;padding-bottom:6px; }
        .print-report-header-table { width:100%;border-collapse:collapse; }
        .print-report-header-table td { border:none !important;vertical-align:middle; }
        .print-report-table { width:100%;border-collapse:collapse;margin-top:8px; }
        .print-report-table th, .print-report-table td { border:1px solid #000 !important;padding:4px 6px;font-size:8.5px; }
        .print-report-table th { background:#f2f2f2 !important;font-weight:bold;text-align:center; }
        .print-subtotal-row td { background:#f9f9f9 !important;font-weight:bold; }
    }
</style>
@endsection

@section('content')

{{-- Print Header --}}
<div class="print-only print-report-header">
    <table class="print-report-header-table">
        <tr>
            <td style="width:70px;">
                <img src="{{ asset('logo/Icon%20SIMOLI.png') }}" style="width:55px;height:auto;" alt="SIMOLI">
            </td>
            <td style="text-align:center;">
                <h3 style="margin:0;font-size:15px;font-weight:800;text-transform:uppercase;">Report Monitoring Alat Berat SIMOLI</h3>
                <div style="font-size:11px;font-weight:700;">PT. Perkebunan Nusantara IV Regional III</div>
                <div style="font-size:10px;">Sistem Monitoring Limbah Land Aplikasi</div>
            </td>
            <td style="width:110px;text-align:right;font-size:10px;">
                <div><strong>Periode:</strong></div>
                <div>{{ $namaBulan[(int)$bulan] }} {{ $tahun }}</div>
            </td>
        </tr>
    </table>
</div>

<div class="print-only">
    <p style="font-size:10px;margin-bottom:10px;">Berikut report monitoring operasional alat berat bulan {{ $namaBulan[(int)$bulan] }} {{ $tahun }} per PKS:</p>

    @if($data->count() > 0)
        @foreach($dataByPks as $pksName => $items)
        <div style="margin-top:14px;margin-bottom:16px;">
            <div style="font-size:11px;font-weight:800;margin-bottom:4px;text-transform:uppercase;">PKS {{ $pksName }}</div>
            <table class="print-report-table">
                <thead>
                    <tr>
                        <th style="width:25px;">No</th>
                        <th>Tanggal</th>
                        <th>Kode Alat</th>
                        <th>Nama Alat</th>
                        <th>Operator</th>
                        <th>Kegiatan &amp; Lokasi</th>
                        <th>Bed</th>
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
                            @php
                                $latAwal = $item->latitude_awal ?? $item->latitude;
                                $longAwal = $item->longitude_awal ?? $item->longitude;
                                $latAkhir = $item->latitude_akhir;
                                $longAkhir = $item->longitude_akhir;
                            @endphp
                            @if(($latAwal && $longAwal) || ($latAkhir && $longAkhir))
                                <div style="font-size: 8px; color: #4b5563; margin-top: 2px;">
                                    @if($latAwal && $longAwal)<div>GPS Awal: {{ $latAwal }}, {{ $longAwal }}</div>@endif
                                    @if($latAkhir && $longAkhir)<div>GPS Akhir: {{ $latAkhir }}, {{ $longAkhir }}</div>@endif
                                </div>
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
                        <td style="text-align:center;">F: {{ $items->sum('flat_bed') }}<br>L: {{ $items->sum('long_bed') }}</td>
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
        <div style="padding:20px;text-align:center;">Tidak ada data monitoring alat berat pada periode ini.</div>
    @endif
</div>

{{-- Header Banner Card Web --}}
<div class="report-header-card no-print">
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
        <div>
            <h3 style="font-family:'Outfit',sans-serif;font-weight:900;margin:0 0 4px;font-size:22px;color:#ffffff;display:flex;align-items:center;gap:10px;">
                <i class="feather-truck" style="color:#86efac;font-size:24px;"></i>
                Report Monitoring Alat Berat
            </h3>
            <p style="margin:0;color:rgba(209,250,229,.9);font-size:13px;font-weight:600;">
                Laporan operasional periodik &amp; jam kerja (HM) alat berat bulan <strong>{{ $namaBulan[(int)$bulan] }} {{ $tahun }}</strong>
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

{{-- Stat KPI Cards Web --}}
<div class="row g-3 mb-4 no-print">
    <div class="col-xl-3 col-sm-6">
        <div class="rab-kpi">
            <div class="d-flex align-items-center gap-3">
                <div class="rab-kpi-icon icon-green">
                    <i class="feather-clock"></i>
                </div>
                <div>
                    <h3 class="rab-kpi-val val-green">{{ \App\Models\MonitoringAlatBerat::formatHm($summary['total_hm'], true) }}</h3>
                    <span class="rab-kpi-label">Total Jam Kerja (HM)</span>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-sm-6">
        <div class="rab-kpi">
            <div class="d-flex align-items-center gap-3">
                <div class="rab-kpi-icon icon-teal">
                    <i class="feather-grid"></i>
                </div>
                <div>
                    <h3 class="rab-kpi-val val-teal">{{ number_format($summary['total_bed'] ?? 0) }} <span class="rab-kpi-sub">Bed</span></h3>
                    <span class="rab-kpi-sub d-block">Flat: {{ number_format($summary['total_flat_bed'] ?? 0) }} | Long: {{ number_format($summary['total_long_bed'] ?? 0) }}</span>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-sm-6">
        <div class="rab-kpi">
            <div class="d-flex align-items-center gap-3">
                <div class="rab-kpi-icon icon-amber">
                    <i class="feather-droplet"></i>
                </div>
                <div>
                    <h3 class="rab-kpi-val val-amber">{{ number_format($summary['total_bbm'], 0) }} <span class="rab-kpi-sub">L</span></h3>
                    <span class="rab-kpi-label">Total Konsumsi BBM</span>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-sm-6">
        <div class="rab-kpi">
            <div class="d-flex align-items-center gap-3">
                <div class="rab-kpi-icon icon-blue">
                    <i class="feather-activity"></i>
                </div>
                <div>
                    <h3 class="rab-kpi-val val-blue">{{ $summary['total_kegiatan'] }} <span class="rab-kpi-sub">Log</span></h3>
                    <span class="rab-kpi-label">Total Log Kegiatan</span>
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
                    @if(!auth()->user() || !auth()->user()->isUnit())
                        <option value="">Semua PKS Unit</option>
                    @endif
                    @foreach($pksList as $pks)
                        <option value="{{ $pks->id_pks }}" {{ (request('id_pks') == $pks->id_pks || (auth()->user() && auth()->user()->isUnit() && auth()->user()->id_pks == $pks->id_pks)) ? 'selected' : '' }}>
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

<!-- Table Report Web -->
<div class="section-card no-print mb-4">
    <div class="section-card-header">
        <h4 class="section-card-title">
            <i class="feather-truck" style="color:#16a34a;font-size:18px;"></i>
            Laporan Operasional Periodik — {{ $namaBulan[(int)$bulan] }} {{ $tahun }}
        </h4>
        <span class="mod-pill mod-pill-ok" style="font-size:10.5px;">
            <i class="feather-database" style="font-size:11px;"></i>
            {{ $summary['total_kegiatan'] }} Total Record
        </span>
    </div>

    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="report-table mb-0">
                <thead>
                    <tr>
                        <th style="width:40px;text-align:center;">No</th>
                        <th style="min-width:90px;">Tanggal</th>
                        <th style="width:70px;text-align:center;">PKS</th>
                        <th style="min-width:140px;">Kode &amp; Nama Alat</th>
                        <th style="min-width:110px;">Operator</th>
                        <th style="min-width:180px;">Kegiatan &amp; Lokasi</th>
                        <th style="min-width:100px;">Flat / Long Bed</th>
                        <th style="min-width:100px;">HM Awal-Akhir</th>
                        <th style="min-width:90px;text-align:right;">Total HM</th>
                        <th style="min-width:80px;text-align:right;">BBM (L)</th>
                        <th style="width:110px;text-align:center;">Kondisi</th>
                        <th style="min-width:140px;">Catatan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($dataByPks as $pksNama => $logsGroup)
                        <tr class="pks-group-header">
                            <td colspan="12">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div><i class="feather-home me-1"></i> Unit PKS: {{ $pksNama }}</div>
                                    <span class="mod-pill mod-pill-info" style="font-size:10.5px;">{{ $logsGroup->count() }} kegiatan</span>
                                </div>
                            </td>
                        </tr>
                        @foreach($logsGroup as $idx => $item)
                            <tr>
                                <td style="text-align:center;font-weight:700;" class="tbl-subtext">{{ $loop->iteration }}</td>
                                <td>{{ \Carbon\Carbon::parse($item->tanggal)->format('d/m/Y') }}</td>
                                <td style="text-align:center;">
                                    <span class="pks-badge">
                                        {{ $item->pks ? $item->pks->akro : '-' }}
                                    </span>
                                </td>
                                <td>
                                    <div class="tbl-code">{{ $item->alatBerat ? $item->alatBerat->kode_alat : '-' }}</div>
                                    <small class="tbl-name">{{ $item->alatBerat ? $item->alatBerat->nama_alat : '-' }}</small>
                                </td>
                                <td><span class="tbl-operator">{{ $item->operator }}</span></td>
                                <td>
                                    <div class="tbl-kegiatan">{{ $item->kegiatan }}</div>
                                    @if($item->lokasi_blok)
                                        <small class="tbl-subtext"><i class="feather-map-pin me-1"></i>{{ $item->lokasi_blok }}</small>
                                    @endif
                                    @php
                                        $latAwal = $item->latitude_awal ?? $item->latitude;
                                        $longAwal = $item->longitude_awal ?? $item->longitude;
                                        $latAkhir = $item->latitude_akhir;
                                        $longAkhir = $item->longitude_akhir;
                                    @endphp
                                    @if(($latAwal && $longAwal) || ($latAkhir && $longAkhir))
                                        <div style="margin-top:4px;" class="d-flex flex-wrap gap-1">
                                            @if($latAwal && $longAwal)
                                                <a href="{{ $item->google_maps_url_awal }}" target="_blank" class="mod-pill mod-pill-info" style="font-size:9.5px;text-decoration:none;" title="GPS Awal Kerja">
                                                    <i class="feather-map-pin me-1"></i>Awal: {{ number_format((float)$latAwal, 4) }}, {{ number_format((float)$longAwal, 4) }}
                                                </a>
                                            @endif
                                            @if($latAkhir && $longAkhir)
                                                <a href="{{ $item->google_maps_url_akhir }}" target="_blank" class="mod-pill mod-pill-ok" style="font-size:9.5px;text-decoration:none;" title="GPS Akhir Kerja">
                                                    <i class="feather-map-pin me-1"></i>Akhir: {{ number_format((float)$latAkhir, 4) }}, {{ number_format((float)$longAkhir, 4) }}
                                                </a>
                                            @endif
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    <span class="mod-pill mod-pill-info" style="font-size:10.5px;">
                                        F: {{ $item->flat_bed ?? 0 }} | L: {{ $item->long_bed ?? 0 }}
                                    </span>
                                </td>
                                <td><small class="tbl-hm-range">{{ $item->hm_awal_formatted }} - {{ $item->hm_akhir_formatted }}</small></td>
                                <td style="text-align:right;" class="tbl-hm-total">{{ $item->total_hm_formatted }}</td>
                                <td style="text-align:right;" class="tbl-bbm-val">{{ number_format($item->bbm_liter, 0) }} L</td>
                                <td style="text-align:center;">
                                    @if($item->kondisi_alat == 'Normal')
                                        <span class="mod-pill mod-pill-ok" style="font-size:10.5px;">Normal</span>
                                    @elseif($item->kondisi_alat == 'Perlu Perbaikan')
                                        <span class="mod-pill mod-pill-warn" style="font-size:10.5px;">Perlu Perbaikan</span>
                                    @else
                                        <span class="mod-pill mod-pill-err" style="font-size:10.5px;">Breakdown</span>
                                    @endif
                                </td>
                                <td><small class="tbl-catatan">{{ $item->catatan ?? '-' }}</small></td>
                            </tr>
                        @endforeach
                        <tr class="pks-subtotal-row">
                            <td colspan="6" style="text-align:right;">Subtotal {{ $pksNama }}:</td>
                            <td><span class="mod-pill mod-pill-ok" style="font-size:10.5px;">F: {{ $logsGroup->sum('flat_bed') }} | L: {{ $logsGroup->sum('long_bed') }}</span></td>
                            <td colspan="2" style="text-align:right;" class="tbl-hm-total">{{ \App\Models\MonitoringAlatBerat::formatHm($logsGroup->sum('total_hm'), true) }}</td>
                            <td style="text-align:right;" class="tbl-bbm-val">{{ number_format($logsGroup->sum('bbm_liter'), 0) }} L</td>
                            <td colspan="2"></td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="12" class="text-center py-5 text-muted">
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
