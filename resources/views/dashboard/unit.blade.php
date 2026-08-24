@extends('layouts.simoli')
@section('title', 'Dashboard Unit — ' . (Auth::user()->pks->nama ?? ''))
@section('page-title', 'Dashboard Unit ' . (Auth::user()->pks->akro ?? ''))
@section('page-description', 'Monitoring Harian & Statistik Bulanan — ' . (Auth::user()->pks->nama ?? 'PKS'))

@section('breadcrumb')
    <li>PKS {{ Auth::user()->pks->akro ?? '' }}</li>
    <li class="separator">/</li>
    <li>Dashboard</li>
@endsection

@php
    $namaBulan = [
        1 => 'Januari', 2 => 'Februari', 3 => 'Maret',
        4 => 'April',   5 => 'Mei',       6 => 'Juni',
        7 => 'Juli',    8 => 'Agustus',   9 => 'September',
        10 => 'Oktober',11 => 'November', 12 => 'Desember',
    ];
    $isComplete = $hasPengaliran && $hasPemeliharaan;
    $pctPengaliran   = $daysInMonth > 0 ? round(($monthlyPengaliran / $daysInMonth) * 100) : 0;
    $pctPemeliharaan = $daysInMonth > 0 ? round(($monthlyPemeliharaan / $daysInMonth) * 100) : 0;
@endphp

@section('styles')
<style>
    /* ================================================================
       UNIT DASHBOARD — PTPN GREEN THEME
       ================================================================ */

    @keyframes fadeUpCard {
        from { opacity: 0; transform: translateY(16px); }
        to   { opacity: 1; transform: translateY(0); }
    }

    @keyframes livePulse {
        0%   { box-shadow: 0 0 0 0 rgba(34,197,94,.7); transform: scale(.95); }
        70%  { box-shadow: 0 0 0 8px rgba(34,197,94,0); transform: scale(1.05); }
        100% { box-shadow: 0 0 0 0 rgba(34,197,94,0); transform: scale(.95); }
    }

    /* Disable default hero strip */
    .page-hero-strip { display: none; }

    /* === COMPLIANCE HERO CARD === */
    .unit-hero {
        border-radius: 20px;
        border: 1px solid transparent;
        box-shadow: 0 8px 30px rgba(0,0,0,.1);
        position: relative;
        overflow: hidden;
        margin-bottom: 24px;
        animation: fadeUpCard .4s ease-out;
    }

    .unit-hero-ok {
        background: linear-gradient(135deg, #030d07 0%, #0a2317 40%, #166534 80%, #16a34a 100%);
        border-color: rgba(34,197,94,.25);
    }

    .unit-hero-warn {
        background: linear-gradient(135deg, #1a0a00 0%, #3b1600 50%, #92400e 100%);
        border-color: rgba(251,146,60,.3);
    }

    .unit-hero-danger {
        background: linear-gradient(135deg, #1a0000 0%, #450a0a 50%, #991b1b 100%);
        border-color: rgba(248,113,113,.3);
    }

    .unit-hero-inner {
        position: relative;
        z-index: 2;
        padding: clamp(22px, 3vw + 12px, 40px);
    }

    .unit-hero-badge {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        padding: 5px 13px;
        border-radius: 50px;
        font-size: 10.5px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.7px;
        margin-bottom: 14px;
    }

    .badge-ok     { background: rgba(34,197,94,.15); border: 1px solid rgba(34,197,94,.3); color: #86efac; }
    .badge-warn   { background: rgba(251,146,60,.15); border: 1px solid rgba(251,146,60,.3); color: #fed7aa; }
    .badge-danger { background: rgba(248,113,113,.15); border: 1px solid rgba(248,113,113,.3); color: #fca5a5; }

    .live-dot-sm {
        width: 7px; height: 7px;
        border-radius: 50%;
        display: inline-block;
        animation: livePulse 2s ease-in-out infinite;
    }

    .dot-green  { background: #4ade80; }
    .dot-orange { background: #fb923c; }
    .dot-red    { background: #f87171; }

    .unit-hero-title {
        font-family: 'Outfit', sans-serif;
        font-size: clamp(20px, 2.5vw + 10px, 30px);
        font-weight: 900;
        color: #ffffff;
        line-height: 1.15;
        letter-spacing: -0.5px;
        margin-bottom: 6px;
    }

    .unit-hero-sub {
        font-size: 13px;
        color: rgba(209,250,229,.7);
        margin-bottom: 20px;
    }

    /* Status Message Banner */
    .status-banner {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 10px 16px;
        border-radius: 12px;
        font-size: 13px;
        font-weight: 600;
        margin-bottom: 20px;
    }

    .status-banner-ok     { background: rgba(34,197,94,.12); border: 1px solid rgba(34,197,94,.25); color: #bbf7d0; }
    .status-banner-warn   { background: rgba(251,146,60,.12); border: 1px solid rgba(251,146,60,.25); color: #fed7aa; }
    .status-banner-danger { background: rgba(248,113,113,.12); border: 1px solid rgba(248,113,113,.25); color: #fca5a5; }

    /* Compliance Module Tiles */
    .module-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 12px;
    }

    .module-tile {
        border-radius: 14px;
        padding: 16px;
        border: 1px solid transparent;
        transition: all .2s ease;
    }

    .module-tile-ok {
        background: rgba(34,197,94,.1);
        border-color: rgba(34,197,94,.25);
    }

    .module-tile-err {
        background: rgba(248,113,113,.12);
        border-color: rgba(248,113,113,.3);
    }

    .module-tile-neutral {
        background: rgba(255,255,255,.07);
        border-color: rgba(255,255,255,.12);
    }

    .module-tile-title {
        font-size: 11px;
        font-weight: 700;
        color: rgba(209,250,229,.7);
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 8px;
    }

    .module-tile-status {
        font-size: 13px;
        font-weight: 800;
        color: #ffffff;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .module-tile-action {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        margin-top: 10px;
        padding: 5px 12px;
        border-radius: 8px;
        font-size: 11px;
        font-weight: 700;
        background: rgba(255,255,255,.15);
        color: #ffffff;
        text-decoration: none;
        transition: all .2s ease;
        border: 1px solid rgba(255,255,255,.2);
    }

    .module-tile-action:hover {
        background: rgba(255,255,255,.25);
        color: #ffffff;
        transform: translateY(-1px);
    }

    .module-tile-action-danger {
        background: rgba(239,68,68,.25);
        border-color: rgba(239,68,68,.4);
    }

    /* === STAT CARDS === */
    .stat-card {
        background: #ffffff;
        border-radius: 16px;
        border: 1px solid rgba(22,163,74,.1);
        box-shadow: 0 2px 12px rgba(22,163,74,.06);
        padding: 18px 20px;
        height: 100%;
        transition: all .2s ease;
        animation: fadeUpCard .4s ease-out;
    }

    .stat-card:hover { transform: translateY(-3px); box-shadow: 0 8px 24px rgba(22,163,74,.1); }

    .stat-card-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 16px;
        padding-bottom: 12px;
        border-bottom: 1px solid rgba(22,163,74,.08);
    }

    .stat-card-title {
        font-family: 'Outfit', sans-serif;
        font-size: 14px;
        font-weight: 800;
        color: #14532d;
        display: flex;
        align-items: center;
        gap: 7px;
        margin: 0;
    }

    .stat-mini-item {
        display: flex;
        flex-direction: column;
        align-items: center;
        text-align: center;
        padding: 14px 10px;
        border-radius: 12px;
        transition: all .2s ease;
    }

    .stat-mini-item:hover { transform: translateY(-2px); }

    .stat-mini-val {
        font-family: 'Outfit', sans-serif;
        font-size: clamp(18px, 2vw + 10px, 22px);
        font-weight: 900;
        line-height: 1.1;
        margin-bottom: 4px;
    }

    .stat-mini-lbl {
        font-size: 10.5px;
        font-weight: 700;
        color: #6b7280;
        text-transform: uppercase;
        letter-spacing: 0.4px;
    }

    .bg-g50  { background: #f0fdf4; } .text-g  { color: #16a34a; }
    .bg-b50  { background: #eff6ff; } .text-b  { color: #1d4ed8; }
    .bg-cy50 { background: #ecfeff; } .text-cy { color: #0e7490; }
    .bg-a50  { background: #fffbeb; } .text-a  { color: #b45309; }
    .bg-r50  { background: #fef2f2; } .text-r  { color: #dc2626; }
    .bg-p50  { background: #faf5ff; } .text-p  { color: #7c3aed; }

    /* === PROGRESS BARS === */
    .progress-simoli {
        height: 8px;
        border-radius: 10px;
        background: rgba(22,163,74,.1);
        overflow: hidden;
    }

    .progress-bar-simoli {
        height: 100%;
        border-radius: 10px;
        background: linear-gradient(90deg, #16a34a, #4ade80);
        transition: width 1s ease;
    }

    .progress-bar-warning {
        background: linear-gradient(90deg, #d97706, #fbbf24);
    }

    /* === ACTIVITY TIMELINE === */
    .activity-item {
        display: flex;
        align-items: flex-start;
        gap: 12px;
        padding: 10px 0;
        border-bottom: 1px solid rgba(22,163,74,.07);
    }

    .activity-item:last-child { border-bottom: none; }

    .activity-icon {
        width: 36px; height: 36px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 16px;
        flex-shrink: 0;
    }

    .activity-text { font-size: 13px; font-weight: 600; color: #374151; }
    .activity-meta { font-size: 11px; color: #9ca3af; margin-top: 2px; }

    /* === ALAT BERAT STATUS === */
    .alatberat-stat {
        display: flex;
        flex-direction: column;
        align-items: center;
        text-align: center;
        padding: 16px 12px;
        border-radius: 14px;
    }

    .alatberat-val {
        font-family: 'Outfit', sans-serif;
        font-size: 26px;
        font-weight: 900;
        line-height: 1;
        margin-bottom: 4px;
    }

    .alatberat-lbl {
        font-size: 11px;
        color: #6b7280;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.4px;
    }

    /* === CHART CARD === */
    .chart-card-unit {
        background: #ffffff;
        border-radius: 16px;
        border: 1px solid rgba(22,163,74,.1);
        box-shadow: 0 2px 12px rgba(22,163,74,.06);
        overflow: hidden;
        animation: fadeUpCard .4s ease-out .15s both;
    }

    .chart-card-unit-header {
        padding: 16px 20px;
        border-bottom: 1px solid rgba(22,163,74,.08);
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 12px;
    }

    .chart-card-unit-title {
        font-family: 'Outfit', sans-serif;
        font-size: 14px;
        font-weight: 800;
        color: #14532d;
        display: flex;
        align-items: center;
        gap: 7px;
        margin: 0 0 2px;
    }

    /* === SECTION HEADER === */
    .unit-section-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 10px;
        margin-bottom: 16px;
        padding-bottom: 10px;
        border-bottom: 2px solid rgba(22,163,74,.1);
    }

    .unit-section-title {
        font-family: 'Outfit', sans-serif;
        font-size: clamp(14px, 1vw + 10px, 16px);
        font-weight: 800;
        color: #14532d;
        display: flex;
        align-items: center;
        gap: 8px;
        margin: 0;
    }

    .unit-section-dot {
        width: 8px; height: 8px;
        border-radius: 50%;
        background: linear-gradient(135deg, #16a34a, #4ade80);
        box-shadow: 0 0 6px rgba(34,197,94,.5);
    }

    /* === DARK MODE === */
    html.app-skin-dark .stat-card,
    html.app-skin-dark .chart-card-unit {
        background: #0a2317 !important;
        border-color: rgba(34,197,94,.15) !important;
    }

    html.app-skin-dark .stat-card-title,
    html.app-skin-dark .chart-card-unit-title,
    html.app-skin-dark .unit-section-title { color: #d1fae5 !important; }

    html.app-skin-dark .activity-text { color: #d1fae5; }
    html.app-skin-dark .unit-section-header { border-color: rgba(34,197,94,.1) !important; }

    /* Responsive */
    @media (max-width: 767.98px) {
        .module-grid { grid-template-columns: 1fr; gap: 8px; }
    }

    @media (max-width: 575.98px) {
        .unit-hero-inner { padding: 18px; }
    }
</style>
@endsection

@section('content')
<article class="unit-dashboard">

    {{-- ================================================================
         1. COMPLIANCE HERO BANNER — Status Monitoring Hari Ini
         ================================================================ --}}
    @php
        $heroClass  = $isComplete ? 'unit-hero-ok'   : ($hasPengaliran || $hasPemeliharaan ? 'unit-hero-warn' : 'unit-hero-danger');
        $badgeClass = $isComplete ? 'badge-ok'        : ($hasPengaliran || $hasPemeliharaan ? 'badge-warn' : 'badge-danger');
        $dotClass   = $isComplete ? 'dot-green'       : ($hasPengaliran || $hasPemeliharaan ? 'dot-orange' : 'dot-red');
        $bannerClass= $isComplete ? 'status-banner-ok': ($hasPengaliran || $hasPemeliharaan ? 'status-banner-warn' : 'status-banner-danger');
        $statusIcon = $isComplete ? 'feather-check-circle' : ($hasPengaliran || $hasPemeliharaan ? 'feather-clock' : 'feather-alert-triangle');
    @endphp

    <section class="unit-hero {{ $heroClass }}" aria-label="Status Monitoring Hari Ini">
        <div class="unit-hero-inner">
            <div class="unit-hero-badge {{ $badgeClass }}">
                <span class="live-dot-sm {{ $dotClass }}"></span>
                Status Monitoring Hari Ini
            </div>

            <div class="row align-items-start g-4">
                <div class="col-lg-6">
                    <h2 class="unit-hero-title">
                        Halo, {{ Auth::user()->pks->nama ?? Auth::user()->username }}
                    </h2>
                    <p class="unit-hero-sub">
                        {{ \Carbon\Carbon::parse($tanggal)->locale('id')->translatedFormat('l, d F Y') }}
                    </p>

                    <div class="status-banner {{ $bannerClass }}">
                        <i class="{{ $statusIcon }}" style="font-size:18px;flex-shrink:0;"></i>
                        <span>{{ $simoliMessage }}</span>
                    </div>

                    {{-- To-do list --}}
                    <div style="margin-top:4px;">
                        <div style="font-size:11px;font-weight:700;color:rgba(187,247,208,.6);text-transform:uppercase;letter-spacing:.6px;margin-bottom:8px;">
                            Yang Perlu Dilakukan:
                        </div>
                        @if($simoliStatus == 'NORMAL')
                            <div style="color:#86efac;font-size:13px;font-weight:600;display:flex;align-items:center;gap:6px;">
                                <i class="feather-check-circle"></i> Semua pekerjaan monitoring hari ini telah selesai!
                            </div>
                        @else
                            @if(!$hasPengaliran)
                                <div style="color:#fca5a5;font-size:13px;font-weight:600;display:flex;align-items:center;gap:6px;margin-bottom:4px;">
                                    <i class="feather-circle"></i> Input data Pengaliran LA hari ini
                                </div>
                            @endif
                            @if(!$hasPemeliharaan)
                                <div style="color:#fde68a;font-size:13px;font-weight:600;display:flex;align-items:center;gap:6px;">
                                    <i class="feather-circle"></i> Input data Pemeliharaan Bed hari ini
                                </div>
                            @endif
                        @endif
                    </div>
                </div>

                {{-- Module Compliance Grid --}}
                <div class="col-lg-6">
                    <div class="module-grid">
                        {{-- Pengaliran --}}
                        <div class="module-tile {{ $hasPengaliran ? 'module-tile-ok' : 'module-tile-err' }}">
                            <div class="module-tile-title">Pengaliran LA</div>
                            <div class="module-tile-status">
                                <i class="{{ $hasPengaliran ? 'feather-check-circle' : 'feather-x-circle' }}"
                                   style="color:{{ $hasPengaliran ? '#86efac' : '#fca5a5' }};font-size:16px;"></i>
                                {{ $hasPengaliran ? 'Sudah Input' : 'Belum Input' }}
                            </div>
                            @if(!$hasPengaliran)
                                <a href="{{ route('pengaliran.create') }}" class="module-tile-action module-tile-action-danger">
                                    <i class="feather-edit-2" style="font-size:12px;"></i> Input Sekarang
                                </a>
                            @else
                                <a href="{{ route('pengaliran.index') }}" class="module-tile-action">
                                    <i class="feather-eye" style="font-size:12px;"></i> Lihat Data
                                </a>
                            @endif
                        </div>

                        {{-- Pemeliharaan --}}
                        <div class="module-tile {{ $hasPemeliharaan ? 'module-tile-ok' : 'module-tile-err' }}">
                            <div class="module-tile-title">Pemeliharaan Bed</div>
                            <div class="module-tile-status">
                                <i class="{{ $hasPemeliharaan ? 'feather-check-circle' : 'feather-x-circle' }}"
                                   style="color:{{ $hasPemeliharaan ? '#86efac' : '#fca5a5' }};font-size:16px;"></i>
                                {{ $hasPemeliharaan ? 'Sudah Input' : 'Belum Input' }}
                            </div>
                            @if(!$hasPemeliharaan)
                                <a href="{{ route('pemeliharaan.create') }}" class="module-tile-action module-tile-action-danger">
                                    <i class="feather-edit-2" style="font-size:12px;"></i> Input Sekarang
                                </a>
                            @else
                                <a href="{{ route('pemeliharaan.index') }}" class="module-tile-action">
                                    <i class="feather-eye" style="font-size:12px;"></i> Lihat Data
                                </a>
                            @endif
                        </div>

                        {{-- Rencana --}}
                        <div class="module-tile module-tile-neutral">
                            <div class="module-tile-title">Rencana Tahunan</div>
                            <div class="module-tile-status">
                                <i class="feather-calendar" style="color:#86efac;font-size:16px;"></i>
                                Lihat Rencana
                            </div>
                            <a href="{{ route('rencana.index') }}" class="module-tile-action">
                                <i class="feather-clipboard" style="font-size:12px;"></i> Kelola
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ================================================================
         2. ALAT BERAT STATUS ROW
         ================================================================ --}}
    <section aria-label="Status Alat Berat" class="mb-4">
        <div class="unit-section-header">
            <h3 class="unit-section-title">
                <span class="unit-section-dot"></span>
                Status Alat Berat Pengolahan Limbah
            </h3>
            <div class="d-flex gap-2">
                <a href="{{ route('alat-berat.index') }}" class="btn-ptpn btn-ptpn-outline" style="padding:6px 14px;font-size:12px;">
                    <i class="feather-list" style="font-size:13px;"></i> Master Alat
                </a>
                <a href="{{ route('monitoring-alat-berat.index') }}" class="btn-ptpn btn-ptpn-primary" style="padding:6px 14px;font-size:12px;">
                    <i class="feather-activity" style="font-size:13px;"></i> Log Monitoring
                </a>
            </div>
        </div>

        <div class="stat-card">
            <div class="row g-3">
                <div class="col-6 col-md-3">
                    <div class="alatberat-stat" style="background:rgba(22,163,74,.06);border-radius:14px;">
                        <div class="alatberat-val text-g">{{ $statAlatBerat['total_unit'] ?? 0 }}</div>
                        <div class="alatberat-lbl">Total Unit</div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="alatberat-stat" style="background:#f0fdf4;border-radius:14px;">
                        <div class="alatberat-val" style="color:#16a34a;">{{ $statAlatBerat['ready'] ?? 0 }}</div>
                        <div class="alatberat-lbl">Ready / Operational</div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="alatberat-stat" style="background:#fffbeb;border-radius:14px;">
                        <div class="alatberat-val" style="color:#d97706;">{{ $statAlatBerat['maintenance'] ?? 0 }}</div>
                        <div class="alatberat-lbl">Dalam Perbaikan</div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="alatberat-stat" style="background:#fef2f2;border-radius:14px;">
                        <div class="alatberat-val" style="color:#dc2626;">{{ $statAlatBerat['breakdown'] ?? 0 }}</div>
                        <div class="alatberat-lbl">Breakdown / Rusak</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ================================================================
         3. VOLUME CHART — GRAFIK PENGALIRAN
         ================================================================ --}}
    <section aria-label="Grafik Volume Pengaliran" class="mb-4">
        <div class="chart-card-unit">
            <div class="chart-card-unit-header">
                <div>
                    <h3 class="chart-card-unit-title">
                        <i class="feather-trending-up" style="color:#16a34a;"></i>
                        Grafik Total Volume Pengaliran
                    </h3>
                    <div style="display:flex;gap:6px;flex-wrap:wrap;margin-top:4px;">
                        <span id="filterLabelBulan" class="mod-pill mod-pill-ok" style="font-size:10px;">
                            {{ $namaBulan[(int)$bulan] }} {{ $tahun }}
                        </span>
                        <span id="filterLabelJenis" class="mod-pill mod-pill-info" style="background:#dbeafe;color:#1d4ed8;border:1px solid #bfdbfe;font-size:10px;">
                            {{ $jenis == 'blok' ? 'Blok' : 'Flat Bed' }}
                        </span>
                        @if($nilai)
                        <span id="filterLabelNilai" class="mod-pill mod-pill-muted" style="font-size:10px;">{{ $nilai }}</span>
                        @endif
                    </div>
                </div>

                {{-- Filter Form --}}
                <form id="filterGrafik" class="d-flex flex-wrap gap-2 align-items-center">
                    <select name="tahun" id="tahun" class="form-select form-select-sm" style="width:85px;border-radius:10px;border-color:rgba(22,163,74,.3);font-size:12px;">
                        @foreach($tahunList as $t)
                            <option value="{{ $t }}" {{ $tahun == $t ? 'selected' : '' }}>{{ $t }}</option>
                        @endforeach
                    </select>
                    <select name="bulan" id="bulan" class="form-select form-select-sm" style="width:110px;border-radius:10px;border-color:rgba(22,163,74,.3);font-size:12px;">
                        @for($i = 1; $i <= 12; $i++)
                            <option value="{{ $i }}" {{ $bulan == $i ? 'selected' : '' }}>
                                {{ \Carbon\Carbon::create()->month($i)->translatedFormat('F') }}
                            </option>
                        @endfor
                    </select>
                    <select name="jenis" id="jenis" class="form-select form-select-sm" style="width:110px;border-radius:10px;border-color:rgba(22,163,74,.3);font-size:12px;">
                        <option value="blok" {{ $jenis == 'blok' ? 'selected' : '' }}>Blok</option>
                        <option value="flat_bed" {{ $jenis == 'flat_bed' ? 'selected' : '' }}>Nomor Bak</option>
                    </select>
                    <select name="nilai" id="nilai" class="form-select form-select-sm" style="width:120px;border-radius:10px;border-color:rgba(22,163,74,.3);font-size:12px;">
                        <option value="">Semua</option>
                        @foreach($pilihan as $item)
                            <option value="{{ $item }}" {{ $nilai == $item ? 'selected' : '' }}>{{ $item }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="btn-ptpn btn-ptpn-primary" style="padding:7px 14px;font-size:12px;">
                        <i class="feather-filter" style="font-size:13px;"></i>
                        <span class="d-none d-sm-inline">Filter</span>
                    </button>
                </form>
            </div>
            <div style="padding:20px;">
                <div style="position:relative;min-height:320px;">
                    <div id="volumeChart" style="min-height:320px;"></div>
                </div>
            </div>
        </div>
    </section>

    {{-- ================================================================
         4. STATS + MONITORING BULAN + AKTIVITAS
         ================================================================ --}}
    <section aria-label="Ringkasan Monitoring" class="mb-4">
        <div class="unit-section-header">
            <h3 class="unit-section-title">
                <span class="unit-section-dot"></span>
                Ringkasan Monitoring {{ \Carbon\Carbon::parse($tanggal)->translatedFormat('F Y') }}
            </h3>
        </div>

        <div class="row g-4">
            {{-- Statistik Pengaliran --}}
            <div class="col-md-6 col-lg-4">
                <div class="stat-card">
                    <div class="stat-card-header">
                        <h4 class="stat-card-title">
                            <i class="feather-droplet" style="color:#16a34a;"></i>
                            Statistik Pengaliran
                        </h4>
                        <span class="mod-pill mod-pill-success" style="font-size:10px;">Bulan Ini</span>
                    </div>
                    <div class="row g-2">
                        <div class="col-6">
                            <div class="stat-mini-item bg-g50">
                                <i class="feather-database" style="font-size:20px;color:#16a34a;margin-bottom:6px;"></i>
                                <div class="stat-mini-val text-g">{{ number_format($statPengaliran['total_records']) }}</div>
                                <div class="stat-mini-lbl">Total Record</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="stat-mini-item bg-b50">
                                <i class="feather-trending-up" style="font-size:20px;color:#1d4ed8;margin-bottom:6px;"></i>
                                <div class="stat-mini-val text-b">{{ number_format($statPengaliran['vol_dihasilkan']) }}</div>
                                <div class="stat-mini-lbl">Vol. Dihasilkan m³</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="stat-mini-item bg-cy50">
                                <i class="feather-droplet" style="font-size:20px;color:#0e7490;margin-bottom:6px;"></i>
                                <div class="stat-mini-val text-cy">{{ number_format($statPengaliran['vol_dialirkan']) }}</div>
                                <div class="stat-mini-lbl">Vol. Dialirkan m³</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="stat-mini-item bg-a50">
                                <i class="feather-layers" style="font-size:20px;color:#b45309;margin-bottom:6px;"></i>
                                <div class="stat-mini-val text-a">{{ number_format($statPengaliran['total_flat_bed']) }}</div>
                                <div class="stat-mini-lbl">Total Flat Bed</div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="stat-mini-item bg-r50">
                                <i class="feather-map" style="font-size:20px;color:#dc2626;margin-bottom:6px;"></i>
                                <div class="stat-mini-val text-r">{{ number_format($statPengaliran['total_luas_area'], 1) }}</div>
                                <div class="stat-mini-lbl">Luas Area (Ha)</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Statistik Pemeliharaan --}}
            <div class="col-md-6 col-lg-4">
                <div class="stat-card">
                    <div class="stat-card-header">
                        <h4 class="stat-card-title">
                            <i class="feather-tool" style="color:#d97706;"></i>
                            Statistik Pemeliharaan
                        </h4>
                        <span class="mod-pill" style="background:#fef3c7;color:#92400e;border:1px solid #fde68a;font-size:10px;">Bulan Ini</span>
                    </div>
                    <div class="row g-2">
                        @php
                            $maintStats = [
                                ['icon'=>'database',   'val'=>$statPemeliharaan['total_records'],  'lbl'=>'Total Record', 'bg'=>'bg-a50','cl'=>'text-a'],
                                ['icon'=>'layers',     'val'=>$statPemeliharaan['total_flat_bed'], 'lbl'=>'Flat Bed',     'bg'=>'bg-g50','cl'=>'text-g'],
                                ['icon'=>'maximize-2', 'val'=>$statPemeliharaan['total_long_bed'], 'lbl'=>'Long Bed',     'bg'=>'bg-b50','cl'=>'text-b'],
                                ['icon'=>'users',      'val'=>$statPemeliharaan['total_hk'],       'lbl'=>'Total HK',     'bg'=>'bg-cy50','cl'=>'text-cy'],
                                ['icon'=>'settings',   'val'=>$statPemeliharaan['total_mekanis'],  'lbl'=>'Mekanis',      'bg'=>'bg-p50','cl'=>'text-p'],
                                ['icon'=>'user',       'val'=>$statPemeliharaan['total_manual'],   'lbl'=>'Manual',       'bg'=>'bg-g50','cl'=>'text-g'],
                            ];
                        @endphp
                        @foreach($maintStats as $s)
                        <div class="col-6">
                            <div class="stat-mini-item {{ $s['bg'] }}">
                                <i class="feather-{{ $s['icon'] }}" style="font-size:18px;margin-bottom:5px;" class="{{ $s['cl'] }}"></i>
                                <div class="stat-mini-val {{ $s['cl'] }}">{{ number_format($s['val']) }}</div>
                                <div class="stat-mini-lbl">{{ $s['lbl'] }}</div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Monitoring Bulan + Aktivitas Terakhir --}}
            <div class="col-lg-4">
                {{-- Progress Bulanan --}}
                <div class="stat-card mb-4">
                    <div class="stat-card-header">
                        <h4 class="stat-card-title">
                            <i class="feather-calendar" style="color:#16a34a;"></i>
                            Monitoring Bulan Berjalan
                        </h4>
                    </div>
                    <div class="mb-4">
                        <div style="display:flex;justify-content:space-between;margin-bottom:6px;">
                            <span style="font-size:12.5px;font-weight:700;color:#374151;">
                                <i class="feather-droplet text-success me-1"></i> Pengaliran
                            </span>
                            <span style="font-size:13px;font-weight:900;color:#16a34a;">{{ $pctPengaliran }}%</span>
                        </div>
                        <div class="progress-simoli mb-1">
                            <div class="progress-bar-simoli" style="width:{{ $pctPengaliran }}%;"></div>
                        </div>
                        <small style="font-size:11px;color:#6b7280;">{{ $monthlyPengaliran }} dari {{ $daysInMonth }} hari</small>
                    </div>
                    <div class="mb-2">
                        <div style="display:flex;justify-content:space-between;margin-bottom:6px;">
                            <span style="font-size:12.5px;font-weight:700;color:#374151;">
                                <i class="feather-tool text-warning me-1"></i> Pemeliharaan
                            </span>
                            <span style="font-size:13px;font-weight:900;color:#d97706;">{{ $pctPemeliharaan }}%</span>
                        </div>
                        <div class="progress-simoli mb-1">
                            <div class="progress-bar-simoli progress-bar-warning" style="width:{{ $pctPemeliharaan }}%;"></div>
                        </div>
                        <small style="font-size:11px;color:#6b7280;">{{ $monthlyPemeliharaan }} dari {{ $daysInMonth }} hari</small>
                    </div>
                    @if($pctPengaliran >= 80 && $pctPemeliharaan >= 80)
                        <div style="margin-top:14px;padding:10px 14px;border-radius:10px;background:#f0fdf4;border:1px solid #bbf7d0;font-size:12.5px;color:#15803d;font-weight:700;display:flex;align-items:center;gap:7px;">
                            <i class="feather-check-circle"></i> Monitoring bulan ini berjalan sangat baik!
                        </div>
                    @else
                        <div style="margin-top:14px;padding:10px 14px;border-radius:10px;background:#fffbeb;border:1px solid #fde68a;font-size:12.5px;color:#92400e;font-weight:700;display:flex;align-items:center;gap:7px;">
                            <i class="feather-alert-circle"></i> Masih perlu peningkatan konsistensi.
                        </div>
                    @endif
                </div>

                {{-- Aktivitas Terakhir --}}
                <div class="stat-card">
                    <div class="stat-card-header">
                        <h4 class="stat-card-title">
                            <i class="feather-clock" style="color:#16a34a;"></i>
                            Aktivitas Terakhir
                        </h4>
                    </div>

                    @forelse($recentPengaliran->take(3) as $item)
                    <div class="activity-item">
                        <div class="activity-icon" style="background:#f0fdf4;">
                            <i class="feather-droplet" style="color:#16a34a;"></i>
                        </div>
                        <div>
                            <div class="activity-text">Pengaliran {{ $item->blok }}</div>
                            <div class="activity-meta">
                                {{ $item->tanggal->format('d M Y') }} &bull; {{ number_format($item->vol_limbah_dialirkan ?? 0) }} m³
                            </div>
                        </div>
                    </div>
                    @empty
                    @endforelse

                    @forelse($recentPemeliharaan->take(2) as $item)
                    <div class="activity-item">
                        <div class="activity-icon" style="background:#fffbeb;">
                            <i class="feather-tool" style="color:#d97706;"></i>
                        </div>
                        <div>
                            <div class="activity-text">Pemeliharaan {{ $item->blok }}</div>
                            <div class="activity-meta">{{ $item->tanggal->format('d M Y') }} &bull; {{ $item->jenis_label ?? '' }}</div>
                        </div>
                    </div>
                    @empty
                    @endforelse

                    @if($recentPengaliran->isEmpty() && $recentPemeliharaan->isEmpty())
                        <p style="color:#9ca3af;font-size:12.5px;text-align:center;padding:16px 0;">
                            Belum ada aktivitas yang tercatat.
                        </p>
                    @endif
                </div>
            </div>
        </div>
    </section>

</article>
@endsection

@section('scripts')
<script src="{{ asset('duraluxadmin/assets/vendors/js/apexcharts.min.js') }}"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    var isDark    = document.documentElement.classList.contains('app-skin-dark');
    var fontFam   = "'Outfit','Plus Jakarta Sans',sans-serif";
    var labelClr  = isDark ? '#6b8f72' : '#6b7280';
    var gridColor = isDark ? 'rgba(34,197,94,0.06)' : 'rgba(22,163,74,0.06)';

    var chartEl = document.querySelector('#volumeChart');
    if (!chartEl) return;

    var volumeChartOptions = {
        series: [{
            name: 'Volume Limbah Dialirkan (m³)',
            data: @json($volumeGrafik)
        }],
        chart: {
            type: 'area',
            height: 320,
            toolbar: { show: false },
            fontFamily: fontFam,
            background: 'transparent',
            animations: { enabled: true, easing: 'easeinout', speed: 700 }
        },
        colors: ['#16a34a'],
        fill: {
            type: 'gradient',
            gradient: {
                shadeIntensity: 1,
                opacityFrom: 0.35,
                opacityTo: 0.05,
                stops: [0, 100]
            }
        },
        stroke: { curve: 'smooth', width: 2.5 },
        xaxis: {
            categories: @json($labelHari),
            labels: { style: { colors: labelClr, fontSize: '11px', fontFamily: fontFam } },
            axisBorder: { show: false },
            axisTicks: { show: false }
        },
        yaxis: {
            min: 0,
            labels: { style: { colors: labelClr, fontSize: '11px', fontFamily: fontFam } }
        },
        grid: { borderColor: gridColor, strokeDashArray: 4 },
        markers: { size: 4, strokeWidth: 0, hover: { size: 6 } },
        tooltip: {
            theme: isDark ? 'dark' : 'light',
            style: { fontSize: '12px', fontFamily: fontFam },
            y: { formatter: function(val) { return val + ' m³'; } }
        }
    };

    var volumeChart = new ApexCharts(chartEl, volumeChartOptions);
    volumeChart.render();

    /* Filter AJAX reload */
    var form = document.getElementById('filterGrafik');
    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            var params = new URLSearchParams({
                tahun: document.getElementById('tahun').value,
                bulan: document.getElementById('bulan').value,
                jenis: document.getElementById('jenis').value,
                nilai: document.getElementById('nilai').value
            });
            fetch("{{ route('dashboard.grafik-volume') }}?" + params.toString(), {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            }).then(r => r.json()).then(data => {
                volumeChart.updateOptions({
                    xaxis: { categories: data.labelHari }
                });
                volumeChart.updateSeries([{
                    name: 'Volume Limbah Dialirkan (m³)',
                    data: data.volumeGrafik
                }]);

                /* update filter badges */
                var lblBulan  = document.getElementById('filterLabelBulan');
                var lblJenis  = document.getElementById('filterLabelJenis');
                var lblNilai  = document.getElementById('filterLabelNilai');
                var bulanOpt  = document.getElementById('bulan');
                if (lblBulan)  lblBulan.textContent  = bulanOpt.options[bulanOpt.selectedIndex].text + ' ' + params.get('tahun');
                if (lblJenis)  lblJenis.textContent  = params.get('jenis') === 'blok' ? 'Blok' : 'Flat Bed';
                if (lblNilai && params.get('nilai')) lblNilai.textContent = params.get('nilai');

                /* repopulate nilai options */
                var nilaiSel = document.getElementById('nilai');
                if (nilaiSel) {
                    nilaiSel.innerHTML = '<option value="">Semua</option>';
                    (data.pilihan || []).forEach(function(item) {
                        nilaiSel.innerHTML += '<option value="' + item + '">' + item + '</option>';
                    });
                }
            });
        });
    }
});
</script>
@endsection