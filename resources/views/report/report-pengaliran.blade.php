@extends('layouts.simoli')

@section('title', 'Report Pengaliran Land Aplikasi')
@section('page-title', 'Report Pengaliran')

@section('breadcrumb')
<li class="breadcrumb-item">Report</li>
<li class="breadcrumb-item active">Report Pengaliran</li>
@endsection

@section('styles')
<link rel="stylesheet" type="text/css" href="{{ asset('duraluxadmin/assets/vendors/css/select2.min.css') }}" />
<link rel="stylesheet" type="text/css" href="{{ asset('duraluxadmin/assets/vendors/css/select2-theme.min.css') }}" />
<style>
    /* ================================================================
       REPORT PENGALIRAN — PTPN GREEN THEME
       ================================================================ */

    @keyframes fadeUpCard {
        from { opacity:0; transform:translateY(12px); }
        to   { opacity:1; transform:translateY(0); }
    }

    /* === FILTER CARD === */
    .filter-card {
        background: #ffffff;
        border-radius: 16px;
        border: 1px solid rgba(22,163,74,.15);
        box-shadow: 0 2px 10px rgba(22,163,74,.05);
        margin-bottom: 20px;
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

    .filter-card .form-label {
        font-size: 11px;
        font-weight: 700;
        color: #6b7280;
        text-transform: uppercase;
        letter-spacing: .4px;
    }

    .filter-card .select2-container--default .select2-selection--single {
        height: 42px; border-radius: 12px; border: 1.5px solid #e5e7eb;
        background: #f9fafb; padding: 6px 12px;
    }
    .filter-card .select2-container--default .select2-selection--single .select2-selection__rendered { line-height: 28px; font-size: 13px; }
    .filter-card .select2-container--default .select2-selection--single .select2-selection__arrow { height: 40px; }

    /* === KPI STAT CARDS === */
    .stat-card {
        background: #ffffff;
        border-radius: 18px;
        border: 1px solid rgba(22,163,74,.1);
        box-shadow: 0 2px 12px rgba(22,163,74,.06);
        transition: all .25s ease;
        animation: fadeUpCard .4s ease-out;
    }

    .stat-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 26px rgba(22,163,74,.1);
    }

    /* === NAV TABS === */
    .nav-tabs-custom {
        border-bottom: 2px solid rgba(22,163,74,.12);
        gap: 8px;
    }

    .nav-tabs-custom .nav-link {
        border: none;
        border-radius: 12px 12px 0 0;
        padding: 10px 18px;
        font-weight: 700;
        font-size: 13px;
        color: #6b7280;
        background: #f0fdf4;
        transition: all .2s ease;
    }

    .nav-tabs-custom .nav-link.active {
        color: #14532d;
        background: #ffffff;
        border: 1px solid rgba(22,163,74,.15);
        border-bottom: 2px solid #ffffff;
        margin-bottom: -2px;
        box-shadow: 0 -2px 8px rgba(22,163,74,.06);
    }

    /* === INFOGRAFIS CARD (Report Format) === */
    .report-wrapper {
        background: #f0fdf4;
        border-radius: 16px;
        padding: 8px;
    }
        background: #f8fafc;
        border-radius: 16px;
        padding: 8px;
    }

    .filter-card {
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        background: #ffffff;
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.04);
    }

    /* Infographic Card (Exact Match to Image) */
    .infografis-card {
        background: #ffffff;
        border-radius: 18px;
        box-shadow: 0 10px 40px rgba(0, 78, 204, 0.08);
        border: 1px solid #dbeafe;
        overflow: hidden;
        position: relative;
    }

    /* Top Header Section */
    .info-header-bg {
        padding: 24px 32px 16px 32px;
        background: linear-gradient(180deg, #ffffff 0%, #f0f7ff 100%);
        position: relative;
    }

    .info-title-main {
        font-family: 'Plus Jakarta Sans', 'Poppins', sans-serif;
        font-size: 26px;
        font-weight: 900;
        line-height: 1.15;
        letter-spacing: -0.5px;
        color: #0b2545;
        text-transform: uppercase;
        margin-bottom: 2px;
    }

    .info-title-sub {
        font-family: 'Plus Jakarta Sans', 'Poppins', sans-serif;
        font-size: 26px;
        font-weight: 900;
        line-height: 1.15;
        letter-spacing: -0.5px;
        color: #2e7d32;
        text-transform: uppercase;
        margin-bottom: 12px;
    }

    .info-date-pill {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: linear-gradient(135deg, #0056cc 0%, #003a8c 100%);
        color: #ffffff;
        font-weight: 800;
        font-size: 13px;
        letter-spacing: 0.5px;
        padding: 6px 18px;
        border-radius: 50px;
        box-shadow: 0 4px 12px rgba(0, 86, 204, 0.25);
    }

    .info-date-pill i, .info-date-pill svg {
        font-size: 14px;
    }

    /* Corporate Logos */
    .corp-logos-container {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 20px;
        flex-wrap: wrap;
    }

    .corp-logo-item {
        height: 52px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .corp-logo-item img, .corp-logo-item svg {
        max-height: 48px;
        width: auto;
        object-fit: contain;
    }

    /* Table Container & Banner */
    .rekap-table-container {
        padding: 0 28px 20px 28px;
    }

    .rekap-banner-title {
        background: linear-gradient(90deg, #004ecc 0%, #003da6 100%);
        color: #ffffff;
        text-align: center;
        font-weight: 800;
        font-size: 15px;
        padding: 10px 16px;
        border-radius: 12px 12px 0 0;
        letter-spacing: 0.3px;
        box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.2);
    }

    /* Main Data Table */
    .table-simoli-report {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
        border: 1px solid #bfdbfe;
        border-top: none;
        border-radius: 0 0 12px 12px;
        overflow: hidden;
        background: #ffffff;
    }

    .table-simoli-report th {
        vertical-align: middle;
        text-align: center;
        color: #ffffff;
        font-weight: 700;
        padding: 7px 6px;
        font-size: 12px;
        line-height: 1.25;
        border: 1px solid rgba(255, 255, 255, 0.25);
    }

    /* Table Header Colors */
    .th-blue {
        background-color: #004ecc !important;
    }

    .th-green-main {
        background-color: #70a329 !important;
        font-size: 13px !important;
    }

    .th-green-sub-dark {
        background-color: #58871b !important;
        font-size: 11px !important;
    }

    .th-green-sub {
        background-color: #70a329 !important;
        font-size: 11px !important;
    }

    .th-orange-main {
        background-color: #ea6500 !important;
        font-size: 13px !important;
    }

    .th-orange-sub-dark {
        background-color: #c95200 !important;
        font-size: 11px !important;
    }

    .th-orange-sub {
        background-color: #ea6500 !important;
        font-size: 11px !important;
    }

    /* Table Body Rows */
    .table-simoli-report tbody tr {
        transition: background-color 0.15s ease;
    }

    .table-simoli-report tbody tr:nth-child(even) {
        background-color: #fbfdff;
    }

    .table-simoli-report tbody tr:hover {
        background-color: #f0f7ff;
    }

    .table-simoli-report td {
        padding: 6px 8px;
        font-size: 11.5px;
        vertical-align: middle;
        border-top: 1px solid #e2e8f0;
        border-right: 1px solid #eef2f6;
        color: #1e293b;
    }

    .table-simoli-report td:last-child {
        border-right: none;
    }

    /* Number Badge */
    .no-badge-simoli {
        background: #004ecc;
        color: #ffffff;
        font-weight: 800;
        font-size: 11px;
        width: 24px;
        height: 24px;
        border-radius: 6px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto;
        box-shadow: 0 2px 4px rgba(0, 78, 204, 0.25);
    }

    /* PKS Code & Icon */
    .pks-cell {
        display: flex;
        align-items: center;
        gap: 6px;
        justify-content: center;
        font-weight: 800;
        font-size: 12px;
        color: #0f172a;
    }

    .pks-icon {
        width: 20px;
        height: 20px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    /* Status Keterangan Badge */
    .status-badge-container {
        display: flex;
        align-items: center;
        gap: 6px;
        font-size: 11px;
        font-weight: 600;
        line-height: 1.25;
        text-align: left;
    }

    .status-icon-circle {
        width: 20px;
        height: 20px;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        color: #ffffff;
        font-size: 10px;
    }

    .status-icon-circle.leaf {
        background: #689f38;
        box-shadow: 0 2px 5px rgba(104, 159, 56, 0.3);
    }

    .status-icon-circle.water {
        background: #0077e6;
        box-shadow: 0 2px 5px rgba(0, 119, 230, 0.3);
    }

    /* Footer Section */
    .info-footer-bg {
        padding: 16px 28px 20px 28px;
        background: linear-gradient(180deg, #ffffff 0%, #eef6ff 100%);
        border-top: 1px solid #e0eeff;
        display: flex;
        align-items: flex-end;
        justify-content: space-between;
        position: relative;
        overflow: hidden;
    }

    .akhlak-container {
        display: flex;
        align-items: center;
        gap: 12px;
        z-index: 2;
    }

    .akhlak-logo-badge {
        width: 52px;
        height: 52px;
        flex-shrink: 0;
    }

    .akhlak-text-hashtag {
        font-weight: 900;
        font-size: 12px;
        letter-spacing: 0.3px;
        line-height: 1.3;
    }

    .akhlak-company {
        font-weight: 800;
        font-size: 11px;
        color: #0f2942;
        margin-top: 2px;
    }

    /* Vector Landscape in Footer */
    .footer-landscape-graphic {
        position: absolute;
        right: 0;
        bottom: 0;
        height: 85px;
        max-width: 60%;
        opacity: 0.95;
        pointer-events: none;
        z-index: 1;
    }

    /* Nav Tabs Styling */
    .nav-tabs-custom {
        border-bottom: 2px solid #e2e8f0;
        gap: 8px;
    }

    .nav-tabs-custom .nav-link {
        border: none;
        border-radius: 10px 10px 0 0;
        padding: 10px 18px;
        font-weight: 700;
        font-size: 13px;
        color: #64748b;
        background: #f1f5f9;
        transition: all 0.2s ease;
    }

    .nav-tabs-custom .nav-link.active {
        color: #0056cc;
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-bottom: 2px solid #ffffff;
        margin-bottom: -2px;
        box-shadow: 0 -2px 8px rgba(0, 0, 0, 0.03);
    }

    /* Detail Table Styles */
    .pks-group-header {
        background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
        padding: 10px 20px;
        font-weight: 700;
        font-size: 13px;
        color: #166534;
        border-top: 1px solid #bbf7d0;
        border-bottom: 1px solid #bbf7d0;
    }

    /* Dark Mode Overrides */
    html.app-skin-dark .infografis-card {
        background: #111827;
        border-color: #1f2937;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
    }

    html.app-skin-dark .info-header-bg {
        background: linear-gradient(180deg, #1e293b 0%, #0f172a 100%);
    }

    html.app-skin-dark .info-title-main {
        color: #f8fafc;
    }

    html.app-skin-dark .table-simoli-report {
        background: #111827;
        border-color: #1e293b;
    }

    html.app-skin-dark .table-simoli-report td {
        color: #e2e8f0;
        border-color: #1e293b;
    }

    html.app-skin-dark .table-simoli-report tbody tr:nth-child(even) {
        background-color: #161e2e;
    }

    html.app-skin-dark .table-simoli-report tbody tr:hover {
        background-color: #1e293b;
    }

    html.app-skin-dark .info-footer-bg {
        background: linear-gradient(180deg, #111827 0%, #0f172a 100%);
        border-color: #1e293b;
    }

    html.app-skin-dark .akhlak-company {
        color: #94a3b8;
    }

    /* Print View */
    /* === INFOGRAFIS HEADER === */
    .infografis-card {
        background: #ffffff;
        border-radius: 18px;
        box-shadow: 0 10px 40px rgba(22,163,74,.08);
        border: 1px solid rgba(22,163,74,.15);
        overflow: hidden;
        position: relative;
    }

    .info-header-bg {
        padding: 24px 32px 16px 32px;
        background: linear-gradient(180deg, #ffffff 0%, #f0fdf4 100%);
        position: relative;
    }

    .info-title-main {
        font-family: 'Outfit', 'Plus Jakarta Sans', sans-serif;
        font-size: 26px; font-weight: 900; line-height: 1.15;
        letter-spacing: -0.5px; color: #052e16;
        text-transform: uppercase; margin-bottom: 2px;
    }

    .info-title-sub {
        font-family: 'Outfit', 'Plus Jakarta Sans', sans-serif;
        font-size: 26px; font-weight: 900; line-height: 1.15;
        letter-spacing: -0.5px; color: #16a34a;
        text-transform: uppercase; margin-bottom: 12px;
    }

    .info-date-pill {
        display: inline-flex; align-items: center; gap: 8px;
        background: linear-gradient(135deg, #052e16 0%, #166534 100%);
        color: #86efac; font-weight: 800; font-size: 13px;
        letter-spacing: .5px; padding: 6px 18px;
        border-radius: 50px;
        box-shadow: 0 4px 12px rgba(22,163,74,.25);
    }

    .corp-logos-container { display:flex;align-items:center;justify-content:flex-end;gap:20px;flex-wrap:wrap; }
    .corp-logo-item { height:52px;display:flex;align-items:center;justify-content:center; }
    .corp-logo-item img, .corp-logo-item svg { max-height:48px;width:auto;object-fit:contain; }

    /* === REKAP TABLE === */
    .rekap-table-container { padding: 0 28px 20px 28px; }

    .rekap-banner-title {
        background: linear-gradient(90deg, #052e16 0%, #166534 100%);
        color: #86efac; text-align: center; font-weight: 800; font-size: 15px;
        padding: 10px 16px; border-radius: 12px 12px 0 0; letter-spacing: .3px;
        box-shadow: inset 0 1px 0 rgba(255,255,255,.15);
    }

    .table-simoli-report {
        width: 100%; border-collapse: separate; border-spacing: 0;
        border: 1px solid rgba(22,163,74,.2); border-top: none;
        border-radius: 0 0 12px 12px; overflow: hidden; background: #ffffff;
    }

    .table-simoli-report th {
        vertical-align: middle; text-align: center; color: #ffffff;
        font-weight: 700; padding: 7px 6px; font-size: 12px; line-height: 1.25;
        border: 1px solid rgba(255,255,255,.2);
    }

    .th-blue         { background-color: #14532d !important; }
    .th-green-main   { background-color: #16a34a !important; font-size: 13px !important; }
    .th-green-sub-dark{ background-color: #15803d !important; font-size: 11px !important; }
    .th-green-sub    { background-color: #16a34a !important; font-size: 11px !important; }
    .th-orange-main  { background-color: #d97706 !important; font-size: 13px !important; }
    .th-orange-sub-dark { background-color: #b45309 !important; font-size: 11px !important; }
    .th-orange-sub   { background-color: #d97706 !important; font-size: 11px !important; }

    .table-simoli-report tbody tr { transition: background .15s ease; }
    .table-simoli-report tbody tr:nth-child(even) { background-color: #f9fafb; }
    .table-simoli-report tbody tr:hover { background-color: #f0fdf4; }
    .table-simoli-report td {
        padding: 6px 8px; font-size: 11.5px; vertical-align: middle;
        border-top: 1px solid rgba(22,163,74,.08);
        border-right: 1px solid rgba(22,163,74,.06);
        color: #1e293b;
    }
    .table-simoli-report td:last-child { border-right: none; }

    .no-badge-simoli {
        background: #052e16; color: #86efac; font-weight: 800; font-size: 11px;
        width: 24px; height: 24px; border-radius: 6px; display: inline-flex;
        align-items: center; justify-content: center; margin: 0 auto;
        box-shadow: 0 2px 4px rgba(5,46,22,.3);
    }

    .pks-cell { display:flex;align-items:center;gap:6px;justify-content:center;font-weight:800;font-size:12px;color:#0f172a; }
    .pks-icon { width:20px;height:20px;display:inline-flex;align-items:center;justify-content:center; }

    .status-badge-container { display:flex;align-items:center;gap:6px;font-size:11px;font-weight:600;line-height:1.25;text-align:left; }
    .status-icon-circle { width:20px;height:20px;border-radius:50%;display:inline-flex;align-items:center;justify-content:center;flex-shrink:0;color:#fff;font-size:10px; }
    .status-icon-circle.leaf  { background:#16a34a;box-shadow:0 2px 5px rgba(22,163,74,.3); }
    .status-icon-circle.water { background:#0d9488;box-shadow:0 2px 5px rgba(13,148,136,.3); }

    /* === FOOTER === */
    .info-footer-bg {
        padding: 16px 28px 20px 28px;
        background: linear-gradient(180deg, #ffffff 0%, #f0fdf4 100%);
        border-top: 1px solid rgba(22,163,74,.1);
        display: flex; align-items: flex-end; justify-content: space-between;
        position: relative; overflow: hidden;
    }
    .akhlak-container { display:flex;align-items:center;gap:12px;z-index:2; }
    .akhlak-logo-badge { width:52px;height:52px;flex-shrink:0; }
    .akhlak-text-hashtag { font-weight:900;font-size:12px;letter-spacing:.3px;line-height:1.3; }
    .akhlak-company { font-weight:800;font-size:11px;color:#14532d;margin-top:2px; }
    .footer-landscape-graphic { position:absolute;right:0;bottom:0;height:85px;max-width:60%;opacity:.95;pointer-events:none;z-index:1; }

    /* === DETAIL TABLE === */
    .pks-group-header {
        background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
        padding: 10px 20px; font-weight: 700; font-size: 13px;
        color: #166534; border-top: 1px solid #bbf7d0; border-bottom: 1px solid #bbf7d0;
    }

    /* === DARK MODE === */
    html.app-skin-dark .infografis-card { background:#0a2317;border-color:rgba(34,197,94,.15);box-shadow:0 10px 30px rgba(0,0,0,.3); }
    html.app-skin-dark .info-header-bg { background:linear-gradient(180deg,#0e3b26,#052e16); }
    html.app-skin-dark .info-title-main { color:#d1fae5; }
    html.app-skin-dark .table-simoli-report { background:#0a2317;border-color:rgba(34,197,94,.1); }
    html.app-skin-dark .table-simoli-report td { color:#d1fae5;border-color:rgba(34,197,94,.08); }
    html.app-skin-dark .table-simoli-report tbody tr:nth-child(even) { background-color:#0e3b26; }
    html.app-skin-dark .table-simoli-report tbody tr:hover { background-color:#0a2317; }
    html.app-skin-dark .info-footer-bg { background:linear-gradient(180deg,#0a2317,#052e16);border-color:rgba(34,197,94,.1); }
    html.app-skin-dark .filter-card { background:#0a2317 !important;border-color:rgba(34,197,94,.15) !important; }
    html.app-skin-dark .stat-card { background:#0a2317 !important;border-color:rgba(34,197,94,.12) !important; }
    html.app-skin-dark .pks-group-header { background:linear-gradient(135deg,#0e3b26,#052e16);border-color:rgba(34,197,94,.1); }

    /* === PRINT === */
    @media print {
        @page { size: A4 landscape; margin: 5mm 6mm 6mm 6mm; }
        html, body { width:100% !important;margin:0 !important;padding:0 !important;background:#fff !important;color:#000 !important;-webkit-print-color-adjust:exact !important;print-color-adjust:exact !important; }
        .nxl-navigation, .nxl-header, .filter-card, .no-print,
        .page-hero-strip, .page-header, .breadcrumb, .nxl-footer,
        .nav-tabs-custom, .stat-card, .btn, .btn-ptpn { display:none !important; }
        .main-content, .nxl-container, .nxl-content { padding:0 !important;margin:0 !important; }
        .infografis-card { box-shadow:none !important;border:1px solid rgba(22,163,74,.2) !important;border-radius:12px !important;page-break-inside:avoid !important;break-inside:avoid !important;margin:0 !important;width:100% !important; }
        .info-header-bg { padding:12px 18px 8px 18px !important; }
        .info-title-main, .info-title-sub { font-size:20px !important; }
        .rekap-table-container { padding:0 16px 12px 16px !important; }
        .table-simoli-report th { padding:4px 3px !important;font-size:10px !important; }
        .table-simoli-report td { padding:3px 4px !important;font-size:9.5px !important; }
    }
</style>
@endsection

@section('content')

{{-- Filter Card --}}
<div class="card filter-card mb-4 no-print">
    <div class="card-body p-3">
        <form action="{{ route('report-pengaliran') }}" method="GET" id="formFilterPengaliran">
            <div class="row g-3 align-items-end">
                @if(Auth::user()->isAdmin())
                <div class="col-lg-3 col-md-6">
                    <label class="form-label">
                        <i class="feather-home me-1" style="color:#16a34a;"></i> Unit PKS
                    </label>
                    <select name="id_pks" class="form-control" data-select2-selector="status">
                        <option value="">Semua PKS (12 Unit Mill)</option>
                        @foreach($allPks as $pks)
                        <option value="{{ $pks->id_pks }}" {{ request('id_pks') == $pks->id_pks ? 'selected' : '' }}>
                            {{ $pks->nama }} ({{ $pks->akro }})
                        </option>
                        @endforeach
                    </select>
                </div>
                @endif

                <div class="col-lg-2 col-md-6">
                    <label class="form-label">
                        <i class="feather-calendar me-1" style="color:#16a34a;"></i> Bulan
                    </label>
                    <select name="bulan" class="form-select">
                        @foreach($namaBulan as $i => $bln)
                            @if($i > 0)
                            <option value="{{ $i }}" {{ $bulan == $i ? 'selected' : '' }}>{{ $bln }}</option>
                            @endif
                        @endforeach
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

                <div class="col-lg-2 col-md-6">
                    <label class="form-label">
                        <i class="feather-clock me-1" style="color:#16a34a;"></i> Periode Minggu
                    </label>
                    <select name="minggu" class="form-select">
                        <option value="all" {{ ($minggu == 'all' || empty($minggu)) ? 'selected' : '' }}>Minggu Aktif ({{ $weekLabel ?? 'Berjalan' }})</option>
                        <option value="1" {{ $minggu == '1' ? 'selected' : '' }}>Minggu 1 (Tgl 01 - 07)</option>
                        <option value="2" {{ $minggu == '2' ? 'selected' : '' }}>Minggu 2 (Tgl 08 - 14)</option>
                        <option value="3" {{ $minggu == '3' ? 'selected' : '' }}>Minggu 3 (Tgl 15 - 21)</option>
                        <option value="4" {{ $minggu == '4' ? 'selected' : '' }}>Minggu 4 (Tgl 22 - 28)</option>
                        <option value="5" {{ $minggu == '5' ? 'selected' : '' }}>Minggu 5 (Tgl 29 - Akhir)</option>
                    </select>
                </div>

                <div class="col-lg-3 col-md-12">
                    <label class="form-label">&nbsp;</label>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn-ptpn btn-ptpn-primary flex-grow-1" style="padding:10px 14px;font-size:13px;border-radius:12px;">
                            <i class="feather-search" style="font-size:14px;"></i> Tampilkan
                        </button>
                        <a href="{{ route('report-pengaliran') }}" class="btn-ptpn btn-ptpn-outline" style="padding:10px 12px;border-radius:12px;" title="Reset">
                            <i class="feather-refresh-cw" style="font-size:14px;"></i>
                        </a>
                        <button type="button" onclick="window.print()" class="btn-ptpn" style="padding:10px 12px;border-radius:12px;background:linear-gradient(135deg,#052e16,#16a34a);color:#86efac;border:none;cursor:pointer;font-weight:700;font-size:13px;" title="Cetak PDF Landscape">
                            <i class="feather-printer" style="font-size:14px;"></i>
                        </button>
                        <button type="button" id="btnDownloadImage" class="btn-ptpn" style="padding:10px 12px;border-radius:12px;background:linear-gradient(135deg,#0d9488,#14b8a6);color:#fff;border:none;cursor:pointer;font-weight:700;font-size:13px;" title="Simpan PNG">
                            <i class="feather-image" style="font-size:14px;"></i>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- Top KPI Stat Summary Cards --}}
<div class="row g-3 mb-4 no-print">
    <div class="col-xl-3 col-sm-6">
        <div class="card stat-card h-100">
            <div class="card-body p-3">
                <div class="d-flex align-items-center gap-3">
                    <div style="width:48px;height:48px;border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:22px;background:#f0fdf4;color:#16a34a;border:1px solid #bbf7d0;">
                        <i class="feather-grid"></i>
                    </div>
                    <div>
                        <h4 style="font-family:'Outfit',sans-serif;font-weight:900;color:#14532d;margin-bottom:2px;">{{ number_format($summary['total_bed_all'], 0, ',', '.') }}</h4>
                        <span style="font-size:11.5px;color:#6b7280;font-weight:600;">Total Kapasitas Bed (PKS)</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-sm-6">
        <div class="card stat-card h-100" style="border-color:rgba(22,163,74,.15);background:#fbfdf9;">
            <div class="card-body p-3">
                <div class="d-flex align-items-center gap-3">
                    <div style="width:48px;height:48px;border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:22px;background:#16a34a;color:#ffffff;">
                        <i class="feather-calendar"></i>
                    </div>
                    <div>
                        <h4 style="font-family:'Outfit',sans-serif;font-weight:900;color:#15803d;margin-bottom:2px;">{{ number_format($summary['bed_minggu_ini_all'], 0, ',', '.') }} <span style="font-size:13px;font-weight:600;color:#6b7280;">Bed</span></h4>
                        <span style="font-size:11.5px;color:#6b7280;font-weight:600;">Progress {{ $weekLabel ?? 'Minggu Ini' }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-sm-6">
        <div class="card stat-card h-100" style="border-color:rgba(217,119,6,.15);background:#fffaf7;">
            <div class="card-body p-3">
                <div class="d-flex align-items-center gap-3">
                    <div style="width:48px;height:48px;border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:22px;background:#d97706;color:#ffffff;">
                        <i class="feather-clock"></i>
                    </div>
                    <div>
                        <h4 style="font-family:'Outfit',sans-serif;font-weight:900;color:#b45309;margin-bottom:2px;">{{ number_format($summary['bed_sd_bulan_all'], 0, ',', '.') }} <span style="font-size:13px;font-weight:600;color:#6b7280;">Bed</span></h4>
                        <span style="font-size:11.5px;color:#6b7280;font-weight:600;">Progress S.d Bulan Ini</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-sm-6">
        <div class="card stat-card h-100" style="border-color:rgba(22,163,74,.12);background:#f7faff;">
            <div class="card-body p-3">
                <div class="d-flex align-items-center gap-3">
                    <div style="width:48px;height:48px;border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:22px;background:linear-gradient(135deg,#052e16,#16a34a);color:#86efac;">
                        <i class="feather-droplet"></i>
                    </div>
                    <div>
                        <h4 style="font-family:'Outfit',sans-serif;font-weight:900;color:#14532d;margin-bottom:2px;">{{ number_format($summary['vol_dialirkan'], 0, ',', '.') }} <span style="font-size:13px;font-weight:600;color:#6b7280;">m³</span></h4>
                        <span style="font-size:11.5px;color:#6b7280;font-weight:600;">Vol. Limbah Dialirkan</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Navigation Tabs --}}
<ul class="nav nav-tabs nav-tabs-custom mb-3 no-print" role="tablist">
    <li class="nav-item">
        <button class="nav-link active fw-bold" data-bs-toggle="tab" data-bs-target="#tab-infografis" type="button" role="tab">
            <i class="feather-layout me-1.5 text-primary"></i> Format Rekap SIMOLI (Sesuai Gambar)
        </button>
    </li>
    <li class="nav-item">
        <button class="nav-link fw-bold" data-bs-toggle="tab" data-bs-target="#tab-detail" type="button" role="tab">
            <i class="feather-list me-1.5 text-success"></i> Detail Transaksi Harian ({{ $allPengaliranRecords->count() }} Data)
        </button>
    </li>
</ul>

{{-- Tab Contents --}}
<div class="tab-content">
    {{-- TAB 1: INFOGRAFIS FORMAT SESUAI GAMBAR --}}
    <div class="tab-pane fade show active" id="tab-infografis" role="tabpanel">
        <div class="infografis-card mb-4" id="infografisReportCard">
            {{-- Header Infografis --}}
            <div class="info-header-bg">
                <div class="row align-items-center">
                    {{-- Left Header Title & Graphic --}}
                    <div class="col-md-7 col-12 mb-3 mb-md-0">
                        <div class="d-flex align-items-center gap-3">
                            {{-- Eco Globe Illustration --}}
                            <div class="flex-shrink-0 d-none d-sm-block">
                                <svg width="78" height="78" viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <defs>
                                        <radialGradient id="globeGrad" cx="40%" cy="40%" r="60%">
                                            <stop offset="0%" stop-color="#38bdf8"/>
                                            <stop offset="60%" stop-color="#0284c7"/>
                                            <stop offset="100%" stop-color="#0369a1"/>
                                        </radialGradient>
                                        <linearGradient id="leafGrad" x1="0%" y1="0%" x2="100%" y2="100%">
                                            <stop offset="0%" stop-color="#4ade80"/>
                                            <stop offset="100%" stop-color="#15803d"/>
                                        </linearGradient>
                                    </defs>
                                    <!-- City building silhouettes behind -->
                                    <rect x="10" y="46" width="7" height="20" fill="#0369a1" rx="1"/>
                                    <rect x="19" y="38" width="8" height="28" fill="#0284c7" rx="1"/>
                                    <rect x="29" y="42" width="6" height="24" fill="#0ea5e9" rx="1"/>
                                    <!-- Globe Circle -->
                                    <circle cx="56" cy="56" r="36" fill="url(#globeGrad)" />
                                    <!-- Continents / Earth shapes -->
                                    <path d="M42 32C46 30 52 32 56 36C60 40 56 46 50 48C44 50 38 46 38 40C38 36 40 33 42 32Z" fill="#22c55e" opacity="0.9"/>
                                    <path d="M64 45C72 45 80 50 82 58C84 66 78 72 70 74C64 76 60 70 62 64C64 58 58 54 60 48C61 46 62 45 64 45Z" fill="#16a34a" opacity="0.95"/>
                                    <path d="M36 62C40 60 48 64 46 72C44 80 34 84 30 78C26 72 32 64 36 62Z" fill="#22c55e" opacity="0.9"/>
                                    <!-- Lush Leaves overlay on top left of globe -->
                                    <path d="M12 28C12 28 24 16 46 18C44 32 30 42 12 28Z" fill="url(#leafGrad)"/>
                                    <path d="M12 28C24 24 38 28 46 18" stroke="#ffffff" stroke-width="1.5" stroke-linecap="round" opacity="0.7"/>
                                    <path d="M28 12C32 2 48 0 60 10C56 22 42 26 28 12Z" fill="#15803d"/>
                                    <path d="M38 34C48 24 64 26 72 38C64 48 48 48 38 34Z" fill="#84cc16"/>
                                </svg>
                            </div>
                            <div>
                                <h1 class="info-title-main m-0">PENGALIRAN LAND APLIKASI</h1>
                                <h2 class="info-title-sub m-0">OF THE MONTH</h2>
                                <div class="info-date-pill">
                                    <i class="feather-calendar"></i>
                                    <span>{{ $periodeLabel }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Right Corporate Logos --}}
                    <div class="col-md-5 col-12">
                        <div class="corp-logos-container">
                            {{-- BUMN Logo SVG --}}
                            <div class="corp-logo-item" title="BUMN Untuk Indonesia">
                                <svg width="125" height="42" viewBox="0 0 200 60" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <text x="5" y="38" font-family="'Plus Jakarta Sans', Arial, sans-serif" font-weight="900" font-size="34" fill="#0066b2" letter-spacing="-1">BUMN</text>
                                    <path d="M125 18H132V38H125V18Z" fill="#00a3e0"/>
                                    <text x="138" y="27" font-family="'Plus Jakarta Sans', Arial, sans-serif" font-weight="800" font-size="10" fill="#0066b2" letter-spacing="0.5">UNTUK</text>
                                    <text x="138" y="38" font-family="'Plus Jakarta Sans', Arial, sans-serif" font-weight="900" font-size="10" fill="#0066b2" letter-spacing="0.5">INDONESIA</text>
                                </svg>
                            </div>

                            {{-- PTPN 4 Logo SVG --}}
                            <div class="corp-logo-item" title="PTPN 4">
                                <svg width="75" height="46" viewBox="0 0 90 60" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <!-- Sprout Top Leaf (Orange) -->
                                    <path d="M45 4C45 4 38 12 45 20C52 12 45 4 45 4Z" fill="#f97316"/>
                                    <!-- Left Leaf Wing (Orange/Gold) -->
                                    <path d="M34 16C34 16 33 22 41 22C39 16 34 16 34 16Z" fill="#f59e0b"/>
                                    <!-- Right Leaf Wing (Orange/Gold) -->
                                    <path d="M56 16C56 16 57 22 49 22C51 16 56 16 56 16Z" fill="#f59e0b"/>
                                    <!-- Bottom Palm Fronds (Green) -->
                                    <path d="M26 26C32 24 45 25 45 34C45 25 58 24 64 26C60 38 52 38 45 36C38 38 30 38 26 26Z" fill="#15803d"/>
                                    <path d="M22 34C28 32 45 33 45 42C45 33 62 32 68 34C64 44 54 44 45 42C36 44 26 44 22 34Z" fill="#166534"/>
                                    <!-- Text PTPN 4 -->
                                    <text x="45" y="55" text-anchor="middle" font-family="'Plus Jakarta Sans', Arial, sans-serif" font-weight="900" font-size="11" fill="#15803d" letter-spacing="0.5">PTPN 4</text>
                                </svg>
                            </div>

                            {{-- Perkebunan Nusantara Logo SVG --}}
                            <div class="corp-logo-item" title="Perkebunan Nusantara">
                                <svg width="135" height="42" viewBox="0 0 160 55" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <!-- Ribbon waving shapes -->
                                    <path d="M8 22C24 6 52 38 72 16C62 26 40 10 24 24C16 30 10 26 8 22Z" fill="#16a34a"/>
                                    <path d="M18 26C34 12 60 40 80 20C70 28 50 16 34 28C26 34 20 30 18 26Z" fill="#eab308"/>
                                    <path d="M28 30C44 18 68 42 88 24C78 30 58 22 44 32C36 38 30 34 28 30Z" fill="#0284c7"/>
                                    <!-- Subtitle text -->
                                    <text x="48" y="48" text-anchor="middle" font-family="'Plus Jakarta Sans', Arial, sans-serif" font-weight="800" font-size="8.5" fill="#0f172a" letter-spacing="0.2">Perkebunan Nusantara</text>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Table Rekap Section --}}
            <div class="rekap-table-container">
                <div class="rekap-banner-title">
                    Rekap Pengaliran Land Aplikasi SIMOLI
                </div>

                <div class="table-responsive">
                    <table class="table-simoli-report">
                        <thead>
                            {{-- Header Tier 1 --}}
                            <tr>
                                <th rowspan="2" class="th-blue" style="width: 44px;">NO</th>
                                <th rowspan="2" class="th-blue" style="width: 86px;">PKS</th>
                                <th rowspan="2" class="th-blue" style="width: 86px;">Total Bed</th>
                                <th colspan="3" class="th-green-main">Progress Minggu Ini</th>
                                <th colspan="3" class="th-orange-main">Progress S.d Bulan Ini</th>
                                <th rowspan="2" class="th-blue" style="width: 210px;">Keterangan</th>
                            </tr>
                            {{-- Header Tier 2 --}}
                            <tr>
                                <th class="th-green-sub-dark" style="width: 76px;">Bed di alirkan</th>
                                <th class="th-green-sub" style="width: 100px;">Block Pengaliran</th>
                                <th class="th-green-sub" style="width: 140px;">Bak Distribusi</th>
                                <th class="th-orange-sub-dark" style="width: 76px;">Bed di alirkan</th>
                                <th class="th-orange-sub" style="width: 110px;">Block Pengaliran</th>
                                <th class="th-orange-sub" style="width: 170px;">Bak Distribusi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($rekapPengaliran as $row)
                            <tr>
                                {{-- NO --}}
                                <td class="text-center">
                                    <div class="no-badge-simoli">{{ $row->no }}</div>
                                </td>

                                {{-- PKS --}}
                                <td class="text-center">
                                    <div class="pks-cell">
                                        <div class="pks-icon" style="color: {{ $row->color }};">
                                            {{-- Factory / Mill SVG Icon with distinctive color per row --}}
                                            <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
                                                <path d="M12 3L2 12H5V20H19V12H22L12 3M7 18V14H9V18H7M11 18V14H13V18H11M15 18V14H17V18H15Z"/>
                                            </svg>
                                        </div>
                                        <span>{{ $row->akro }}</span>
                                    </div>
                                </td>

                                {{-- Total Bed --}}
                                <td class="text-center fw-bold">
                                    {{ number_format($row->total_bed, 0, ',', '.') }}
                                </td>

                                {{-- Progress Minggu Ini: Bed di alirkan --}}
                                <td class="text-center fw-bold">
                                    {{ $row->minggu_ini->bed_dialirkan > 0 ? number_format($row->minggu_ini->bed_dialirkan, 0, ',', '.') : '-' }}
                                </td>

                                {{-- Progress Minggu Ini: Block Pengaliran --}}
                                <td class="text-center">
                                    {{ $row->minggu_ini->blok }}
                                </td>

                                {{-- Progress Minggu Ini: Bak Distribusi --}}
                                <td class="text-center" style="word-break: break-word;">
                                    {{ $row->minggu_ini->bak }}
                                </td>

                                {{-- Progress S.d Bulan Ini: Bed di alirkan --}}
                                <td class="text-center fw-bold">
                                    {{ $row->sd_bulan_ini->bed_dialirkan > 0 ? number_format($row->sd_bulan_ini->bed_dialirkan, 0, ',', '.') : '-' }}
                                </td>

                                {{-- Progress S.d Bulan Ini: Block Pengaliran --}}
                                <td class="text-center">
                                    {{ $row->sd_bulan_ini->blok }}
                                </td>

                                {{-- Progress S.d Bulan Ini: Bak Distribusi --}}
                                <td class="text-center" style="word-break: break-word; font-size: 11px;">
                                    {{ $row->sd_bulan_ini->bak }}
                                </td>

                                {{-- Keterangan --}}
                                <td>
                                    <div class="status-badge-container">
                                        @if($row->status_type === 'leaf')
                                        <div class="status-icon-circle leaf" title="Lancar">
                                            <i class="feather-check"></i>
                                        </div>
                                        @else
                                        <div class="status-icon-circle water" title="Lancar">
                                            <i class="feather-droplet"></i>
                                        </div>
                                        @endif
                                        <span>{{ $row->keterangan }}</span>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="10" class="text-center py-4 text-muted">
                                    Tidak ada data pengaliran untuk filter periode yang dipilih.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Footer Graphic Section --}}
            <div class="info-footer-bg">
                {{-- Left: AKHLAK logo badge & tagline --}}
                <div class="akhlak-container">
                    {{-- Hexagon AKHLAK badge SVG --}}
                    <div class="akhlak-logo-badge">
                        <svg width="50" height="50" viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <!-- Hexagon Shield -->
                            <polygon points="50,4 92,26 92,74 50,96 8,74 8,26" fill="#0284c7" stroke="#22c55e" stroke-width="4"/>
                            <!-- Inner Silhouette / Worker Head -->
                            <circle cx="50" cy="40" r="14" fill="#ffffff"/>
                            <!-- Safety Helmet on worker -->
                            <path d="M34 38C34 28 42 22 50 22C58 22 66 28 66 38Z" fill="#fbbf24"/>
                            <!-- Worker Shoulders -->
                            <path d="M28 70C28 56 38 52 50 52C62 52 72 56 72 70Z" fill="#ffffff"/>
                            <!-- AKHLAK text -->
                            <rect x="20" y="74" width="60" height="15" rx="3" fill="#0f172a"/>
                            <text x="50" y="85" text-anchor="middle" font-family="'Plus Jakarta Sans', Arial, sans-serif" font-weight="900" font-size="9.5" fill="#ffffff" letter-spacing="1">AKHLAK</text>
                        </svg>
                    </div>

                    <div>
                        <div class="akhlak-text-hashtag">
                            <span style="color: #0f172a;">#AKHLAK-</span><span style="color: #16a34a;">AMANAH</span>,
                            <span style="color: #0284c7;">KOMPETEN</span>,
                            <span style="color: #ea580c;">HARMONIS</span>,
                            <span style="color: #db2777;">LOYAL</span>,
                            <span style="color: #0284c7;">ADAPTIF</span>,
                            <span style="color: #dc2626;">KOLABORATIF</span>
                        </div>
                        <div class="akhlak-company">
                            {{ $tahun }}, PT. PERKEBUNAN NUSANTARA IV REGIONAL III
                        </div>
                    </div>
                </div>

                {{-- Right: Landscape Vector Illustration --}}
                <div class="footer-landscape-graphic">
                    <svg width="450" height="85" viewBox="0 0 500 90" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <!-- Sky Soft Cloud -->
                        <path d="M300 25C320 20 340 30 350 25C360 20 375 25 385 28C395 32 390 40 380 40H290C280 40 285 30 300 25Z" fill="#e0f2fe" opacity="0.6"/>
                        <!-- Distant Blue City Silhouette -->
                        <rect x="360" y="32" width="10" height="38" fill="#bae6fd" rx="1"/>
                        <rect x="372" y="24" width="14" height="46" fill="#7dd3fc" rx="1"/>
                        <rect x="388" y="28" width="12" height="42" fill="#bae6fd" rx="1"/>
                        <rect x="402" y="18" width="16" height="52" fill="#38bdf8" rx="1"/>
                        <rect x="420" y="30" width="12" height="40" fill="#7dd3fc" rx="1"/>
                        <rect x="434" y="22" width="14" height="48" fill="#bae6fd" rx="1"/>
                        <rect x="450" y="35" width="20" height="35" fill="#38bdf8" rx="1"/>
                        <!-- Rolling Green Hills -->
                        <path d="M220 70C280 50 360 45 440 60C470 65 490 70 500 75V90H220V70Z" fill="#86efac" opacity="0.7"/>
                        <path d="M120 75C200 55 320 52 420 68C460 74 485 80 500 85V90H120V75Z" fill="#4ade80" opacity="0.8"/>
                        <path d="M0 80C80 62 200 60 340 74C400 80 460 84 500 88V90H0V80Z" fill="#22c55e"/>
                        <!-- Palm Trees -->
                        <!-- Palm Tree 1 -->
                        <path d="M470 78C470 65 474 55 477 48" stroke="#713f12" stroke-width="2.5" stroke-linecap="round"/>
                        <path d="M477 48C472 44 464 46 462 50" stroke="#15803d" stroke-width="2" stroke-linecap="round"/>
                        <path d="M477 48C482 44 490 46 492 50" stroke="#15803d" stroke-width="2" stroke-linecap="round"/>
                        <path d="M477 48C475 42 477 38 480 37" stroke="#16a34a" stroke-width="2" stroke-linecap="round"/>
                        <path d="M477 48C470 50 465 54 464 60" stroke="#15803d" stroke-width="2" stroke-linecap="round"/>
                        <path d="M477 48C484 50 489 54 490 60" stroke="#16a34a" stroke-width="2" stroke-linecap="round"/>
                        <!-- Palm Tree 2 (Smaller) -->
                        <path d="M488 82C488 72 492 64 494 58" stroke="#713f12" stroke-width="2" stroke-linecap="round"/>
                        <path d="M494 58C490 54 484 56 482 60" stroke="#15803d" stroke-width="1.8" stroke-linecap="round"/>
                        <path d="M494 58C498 54 504 56 506 60" stroke="#15803d" stroke-width="1.8" stroke-linecap="round"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    {{-- TAB 2: DETAIL TRANSAKSI HARIAN --}}
    <div class="tab-pane fade" id="tab-detail" role="tabpanel">
        {{-- Stats Summary Widgets --}}
        <div class="row g-3 mb-4 no-print">
            <div class="col-xl-3 col-sm-6">
                <div class="card stat-card shadow-sm border-0">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center gap-3">
                            <div class="stat-icon bg-soft-primary text-primary" style="width: 48px; height: 48px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                                <i class="feather-file-text"></i>
                            </div>
                            <div>
                                <h3 class="fw-bold mb-0">{{ $summary['count'] }}</h3>
                                <span class="fs-12 text-muted">Total Data Catatan</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6">
                <div class="card stat-card shadow-sm border-0">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center gap-3">
                            <div class="stat-icon bg-soft-success text-success" style="width: 48px; height: 48px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                                <i class="feather-droplet"></i>
                            </div>
                            <div>
                                <h3 class="fw-bold mb-0">{{ number_format($summary['vol_dialirkan'], 0, ',', '.') }}</h3>
                                <span class="fs-12 text-muted">Vol. Dialirkan (m³)</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6">
                <div class="card stat-card shadow-sm border-0">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center gap-3">
                            <div class="stat-icon bg-soft-info text-info" style="width: 48px; height: 48px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                                <i class="feather-layers"></i>
                            </div>
                            <div>
                                <h3 class="fw-bold mb-0">{{ number_format($summary['flat_bed'], 0, ',', '.') }}</h3>
                                <span class="fs-12 text-muted">Total Bed Dialirkan</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6">
                <div class="card stat-card shadow-sm border-0">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center gap-3">
                            <div class="stat-icon bg-soft-warning text-warning" style="width: 48px; height: 48px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                                <i class="feather-map"></i>
                            </div>
                            <div>
                                <h3 class="fw-bold mb-0">{{ number_format($summary['luas_area'], 2, ',', '.') }}</h3>
                                <span class="fs-12 text-muted">Total Luas Area (Ha)</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Log Transaksi Grouped by PKS --}}
        @if($data->count() > 0)
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header py-3 bg-white border-bottom">
                <h6 class="mb-0 fw-bold">
                    <i class="feather-database me-2 text-primary"></i>
                    Detail Log Transaksi Pengaliran — {{ $namaBulan[(int)$bulan] }} {{ $tahun }}
                </h6>
            </div>
            <div class="card-body p-0">
                @foreach($dataByPks as $pksName => $items)
                <div class="pks-group-header">
                    <i class="feather-home me-1"></i> PKS {{ $pksName }}
                    <span class="badge bg-success ms-2">{{ $items->count() }} transaksi</span>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 40px;">No</th>
                                <th>Tanggal</th>
                                <th>Jam</th>
                                <th>No Bak</th>
                                <th>Blok</th>
                                <th class="text-end">Flat Bed</th>
                                <th class="text-end">Vol. Dihasilkan (m³)</th>
                                <th class="text-end">Vol. Dialirkan (m³)</th>
                                <th class="text-end">Luas Area (Ha)</th>
                                <th>Rotasi</th>
                                <th>Keterangan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($items as $j => $item)
                            <tr>
                                <td>{{ $j + 1 }}</td>
                                <td>{{ \Carbon\Carbon::parse($item->tanggal)->format('d/m/Y') }}</td>
                                <td>{{ $item->jam_mulai }} - {{ $item->jam_selesai }}</td>
                                <td>{{ $item->no_bak ?? '-' }}</td>
                                <td><span class="badge bg-light text-dark border">{{ $item->blok ?? '-' }}</span></td>
                                <td class="text-end fw-semibold">{{ number_format($item->flat_bed, 0, ',', '.') }}</td>
                                <td class="text-end">{{ number_format($item->vol_limbah_dihasilkan, 0, ',', '.') }}</td>
                                <td class="text-end fw-bold text-success">{{ number_format($item->vol_limbah_dialirkan, 0, ',', '.') }}</td>
                                <td class="text-end">{{ number_format($item->luas_area, 2, ',', '.') }}</td>
                                <td>{{ $item->rotasi ?? '-' }}</td>
                                <td><small class="text-muted">{{ $item->keterangan ?? '-' }}</small></td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="table-light">
                            <tr>
                                <td colspan="5" class="text-end fw-bold">Subtotal {{ $pksName }}</td>
                                <td class="text-end fw-bold">{{ number_format($items->sum('flat_bed'), 0, ',', '.') }}</td>
                                <td class="text-end fw-bold">{{ number_format($items->sum('vol_limbah_dihasilkan'), 0, ',', '.') }}</td>
                                <td class="text-end fw-bold text-success">{{ number_format($items->sum('vol_limbah_dialirkan'), 0, ',', '.') }}</td>
                                <td class="text-end fw-bold">{{ number_format($items->sum('luas_area'), 2, ',', '.') }}</td>
                                <td colspan="2"></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                @endforeach
            </div>
            <div class="card-footer bg-light py-3">
                <div class="row text-center fw-bold fs-13">
                    <div class="col">Total Records: {{ $summary['count'] }}</div>
                    <div class="col">Vol. Dihasilkan: {{ number_format($summary['vol_dihasilkan'], 0, ',', '.') }} m³</div>
                    <div class="col text-success">Vol. Dialirkan: {{ number_format($summary['vol_dialirkan'], 0, ',', '.') }} m³</div>
                    <div class="col">Flat Bed: {{ number_format($summary['flat_bed'], 0, ',', '.') }}</div>
                    <div class="col">Luas Area: {{ number_format($summary['luas_area'], 2, ',', '.') }} Ha</div>
                </div>
            </div>
        </div>
        @else
        <div class="card shadow-sm border-0">
            <div class="card-body text-center py-5">
                <i class="feather-inbox fs-1 text-muted d-block mb-3"></i>
                <h5 class="text-muted">Tidak ada data transaksi pengaliran</h5>
                <p class="text-muted fs-13">Untuk periode bulan {{ $namaBulan[(int)$bulan] }} {{ $tahun }}</p>
            </div>
        </div>
        @endif
    </div>
</div>

@endsection

@section('scripts')
<script src="{{ asset('duraluxadmin/assets/vendors/js/select2.min.js') }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script>
    $(document).ready(function() {
        $('[data-select2-selector]').select2({ width: '100%' });

        // Download Infografis as Image
        $('#btnDownloadImage').on('click', function() {
            const element = document.getElementById('infografisReportCard');
            const originalBtnText = $(this).html();
            $(this).prop('disabled', true).html('<i class="feather-loader me-1"></i> Memproses...');

            html2canvas(element, {
                scale: 2,
                useCORS: true,
                backgroundColor: '#ffffff',
                logging: false
            }).then(canvas => {
                const link = document.createElement('a');
                link.download = 'Report_Pengaliran_SIMOLI_{{ $namaBulan[(int)$bulan] }}_{{ $tahun }}.png';
                link.href = canvas.toDataURL('image/png');
                link.click();

                $('#btnDownloadImage').prop('disabled', false).html(originalBtnText);
                
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil Diunduh!',
                    text: 'Gambar Rekap Pengaliran SIMOLI telah berhasil disimpan.',
                    timer: 2000,
                    showConfirmButton: false
                });
            }).catch(err => {
                console.error(err);
                $('#btnDownloadImage').prop('disabled', false).html(originalBtnText);
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal Mengunduh',
                    text: 'Terjadi kesalahan saat memproses gambar.'
                });
            });
        });
    });
</script>
@endsection
