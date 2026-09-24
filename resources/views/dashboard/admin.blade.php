@extends('layouts.simoli')

@section('title', 'Dashboard Admin — Executive Monitoring SIMOLI')
@section('page-title', 'Dashboard Eksekutif')
@section('page-description', 'Monitoring Pengaliran Land Aplikasi, Pemeliharaan Kolam & Alat Berat — 12 Unit PKS PTPN IV')

@section('breadcrumb')
    <li>Dashboard Admin</li>
@endsection

@section('page-actions-hero')
    <form action="{{ route('dashboard') }}" method="GET" class="d-flex align-items-center gap-2 flex-wrap">
        <div class="d-flex align-items-center gap-1 rounded-pill px-3 py-2"
             style="background:rgba(22,163,74,0.08);border:1.5px solid rgba(22,163,74,0.2);">
            <i class="feather-calendar text-success" style="font-size:15px;"></i>
            <input type="date" name="tanggal" class="border-0 bg-transparent fw-semibold"
                   style="font-size:13px;color:var(--ptpn-900,#14532d);outline:none;width:130px;"
                   value="{{ $tanggal }}">
        </div>
        <button type="submit" class="btn-ptpn btn-ptpn-primary" style="padding:8px 16px;">
            <i class="feather-filter" style="font-size:14px;"></i>
            <span class="d-none d-sm-inline">Terapkan</span>
        </button>
        <a href="{{ route('dashboard') }}" class="btn-ptpn btn-ptpn-outline" style="padding:8px 12px;" title="Reset">
            <i class="feather-refresh-cw" style="font-size:14px;"></i>
        </a>
    </form>
@endsection

@section('styles')
<style>
    /* ================================================================
       ADMIN DASHBOARD — PTPN GREEN EXECUTIVE THEME
       ================================================================ */

    /* Disable default page-hero-strip (admin has its own) */
    .page-hero-strip { display: none; }

    /* === ANIMATIONS === */
    @keyframes fadeUpCard {
        from { opacity: 0; transform: translateY(18px); }
        to   { opacity: 1; transform: translateY(0); }
    }

    @keyframes countUp {
        from { opacity: 0; transform: scale(0.8); }
        to   { opacity: 1; transform: scale(1); }
    }

    @keyframes livePulse {
        0%   { box-shadow: 0 0 0 0 rgba(34, 197, 94, 0.7); transform: scale(0.95); }
        70%  { box-shadow: 0 0 0 9px rgba(34, 197, 94, 0); transform: scale(1.05); }
        100% { box-shadow: 0 0 0 0 rgba(34, 197, 94, 0); transform: scale(0.95); }
    }

    @keyframes shimmerLine {
        0%   { background-position: -200% center; }
        100% { background-position: 200% center; }
    }

    /* === HERO BANNER === */
    .admin-hero {
        background: linear-gradient(135deg, var(--theme-hero-from, #030d07) 0%, var(--theme-hero-mid, #0a2317) 35%, var(--theme-hero-to, #0e3b26) 65%, var(--theme-hero-accent, #16a34a) 100%);
        border-radius: 20px;
        border: 1px solid var(--theme-hero-border, rgba(34, 197, 94, 0.2));
        box-shadow: 0 12px 40px rgba(0, 0, 0, 0.2), 0 0 0 1px rgba(255, 255, 255, 0.05) inset;
        position: relative;
        overflow: hidden;
        margin-bottom: 28px;
        animation: fadeUpCard 0.4s ease-out;
    }

    .admin-hero::before {
        content: '';
        position: absolute;
        top: -100px; right: -100px;
        width: 400px; height: 400px;
        border-radius: 50%;
        background: radial-gradient(circle, var(--theme-glow, rgba(34, 197, 94, 0.25)) 0%, transparent 70%);
        pointer-events: none;
    }

    .admin-hero::after {
        content: '';
        position: absolute;
        bottom: -80px; left: 20%;
        width: 300px; height: 300px;
        border-radius: 50%;
        /* background: radial-gradient(circle, var(--theme-glow, rgba(212, 160, 23, 0.12)) 0%, transparent 70%); */
        pointer-events: none;
    }

    .admin-hero-inner {
        position: relative;
        z-index: 2;
        padding: clamp(24px, 3vw + 14px, 44px);
    }

    /* Live badge */
    .live-badge {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        padding: 5px 13px;
        border-radius: 50px;
        background: rgba(255, 255, 255, 0.12);
        border: 1px solid rgba(255, 255, 255, 0.25);
        color: var(--ptpn-200, #bbf7d0);
        font-size: 10.5px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.8px;
        margin-bottom: 16px;
    }

    .live-dot {
        width: 8px; height: 8px;
        border-radius: 50%;
        background: var(--theme-accent, #4ade80);
        box-shadow: 0 0 8px var(--theme-glow, rgba(34, 197, 94, 0.8));
        animation: livePulse 2s ease-in-out infinite;
        display: inline-block;
    }

    .admin-hero-title {
        font-family: 'Outfit', sans-serif;
        font-size: clamp(20px, 2.5vw + 12px, 34px);
        font-weight: 900;
        color: #ffffff;
        line-height: 1.15;
        letter-spacing: -0.5px;
        margin-bottom: 10px;
    }

    .admin-hero-sub {
        font-size: clamp(12px, 1vw + 9px, 14px);
        color: rgba(255, 255, 255, 0.82);
        line-height: 1.6;
        margin-bottom: 24px;
        max-width: 540px;
    }

    /* Date + Role pills */
    .hero-meta-pills {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
    }

    .hero-meta-pill {
        display: flex;
        align-items: center;
        gap: 7px;
        padding: 6px 13px;
        border-radius: 50px;
        background: rgba(255, 255, 255, 0.08);
        border: 1px solid rgba(255, 255, 255, 0.12);
        font-size: clamp(11px, 0.8vw + 8px, 13px);
        font-weight: 600;
        color: rgba(209, 250, 229, 0.9);
    }

    /* Hero stat grid */
    .hero-stat-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 12px;
    }

    .hero-stat-tile {
        background: rgba(255, 255, 255, 0.07);
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 14px;
        padding: 14px 16px;
        transition: all 0.2s ease;
        backdrop-filter: blur(8px);
    }

    .hero-stat-tile:hover {
        background: rgba(255, 255, 255, 0.13);
        border-color: rgba(34, 197, 94, 0.4);
        transform: translateY(-2px);
    }

    .hero-stat-val {
        font-family: 'Outfit', sans-serif;
        font-size: clamp(22px, 3vw + 10px, 32px);
        font-weight: 900;
        color: #ffffff;
        line-height: 1;
        animation: countUp 0.6s ease-out;
    }

    .hero-stat-lbl {
        font-size: 10.5px;
        font-weight: 700;
        color: rgba(187, 247, 208, 0.65);
        text-transform: uppercase;
        letter-spacing: 0.6px;
        margin-top: 5px;
    }

    .hero-stat-icon {
        font-size: 20px;
        margin-bottom: 8px;
    }

    /* === SECTION HEADER === */
    .section-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 10px;
        margin-bottom: 18px;
        padding-bottom: 12px;
        border-bottom: 2px solid var(--theme-border, rgba(22, 163, 74, 0.12));
    }

    .section-title {
        font-family: 'Outfit', sans-serif;
        font-size: clamp(14px, 1vw + 10px, 17px);
        font-weight: 800;
        color: var(--ptpn-900, #14532d);
        display: flex;
        align-items: center;
        gap: 8px;
        margin: 0;
    }

    .section-title-dot {
        width: 8px; height: 8px;
        border-radius: 50%;
        background: linear-gradient(135deg, var(--theme-primary, #16a34a), var(--theme-accent, #4ade80));
        box-shadow: 0 0 6px var(--theme-glow, rgba(34, 197, 94, 0.6));
    }

    /* === KPI CARDS === */
    .kpi-card {
        background: #ffffff;
        border-radius: 18px;
        border: 1px solid var(--theme-border, rgba(22, 163, 74, 0.1));
        padding: 20px;
        box-shadow: 0 2px 12px var(--theme-border, rgba(22, 163, 74, 0.06));
        transition: all 0.25s ease;
        height: 100%;
        position: relative;
        overflow: hidden;
        animation: fadeUpCard 0.4s ease-out;
    }

    .kpi-card::after {
        content: '';
        position: absolute;
        top: 0; left: 0; right: 0;
        height: 3px;
        border-radius: 18px 18px 0 0;
    }

    .kpi-card-green::after  { background: linear-gradient(90deg, var(--theme-primary, #16a34a), var(--theme-accent, #4ade80)); }
    .kpi-card-gold::after   { background: linear-gradient(90deg, #d4a017, #fbbf24); }
    .kpi-card-blue::after   { background: linear-gradient(90deg, #1d4ed8, #60a5fa); }
    .kpi-card-purple::after { background: linear-gradient(90deg, #7c3aed, #c084fc); }

    .kpi-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 30px var(--theme-glow, rgba(22, 163, 74, 0.12));
        border-color: var(--theme-primary, rgba(22, 163, 74, 0.25));
    }

    .kpi-label {
        font-size: 10.5px;
        font-weight: 700;
        color: #6b7280;
        text-transform: uppercase;
        letter-spacing: 0.6px;
        margin-bottom: 6px;
    }

    .kpi-value {
        font-family: 'Outfit', sans-serif;
        font-size: clamp(20px, 2vw + 12px, 28px);
        font-weight: 900;
        line-height: 1.1;
        letter-spacing: -0.5px;
        margin-bottom: 8px;
    }

    .kpi-sub {
        font-size: 11.5px;
        color: #6b7280;
        display: flex;
        align-items: center;
        gap: 6px;
        flex-wrap: wrap;
    }

    .kpi-icon {
        width: 48px; height: 48px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        flex-shrink: 0;
    }

    .kpi-icon-green  { background: linear-gradient(135deg, #dcfce7, #f0fdf4); color: #16a34a; border: 1px solid #bbf7d0; }
    .kpi-icon-gold   { background: linear-gradient(135deg, #fef3c7, #fffbeb); color: #b45309; border: 1px solid #fde68a; }
    .kpi-icon-blue   { background: linear-gradient(135deg, #dbeafe, #eff6ff); color: #1d4ed8; border: 1px solid #bfdbfe; }
    .kpi-icon-purple { background: linear-gradient(135deg, #f3e8ff, #faf5ff); color: #7c3aed; border: 1px solid #e9d5ff; }

    /* === CHART CARDS === */
    .chart-card {
        background: #ffffff;
        border-radius: 18px;
        border: 1px solid rgba(22, 163, 74, 0.1);
        box-shadow: 0 2px 12px rgba(22, 163, 74, 0.06);
        overflow: hidden;
        height: 100%;
        transition: box-shadow 0.2s ease;
    }

    .chart-card:hover {
        box-shadow: 0 8px 24px rgba(22, 163, 74, 0.1);
    }

    .chart-card-header {
        padding: 18px 20px 14px;
        border-bottom: 1px solid rgba(22, 163, 74, 0.08);
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 12px;
    }

    .chart-title {
        font-family: 'Outfit', sans-serif;
        font-size: 14px;
        font-weight: 800;
        color: #14532d;
        margin-bottom: 2px;
    }

    .chart-sub {
        font-size: 11.5px;
        color: #6b7280;
    }

    /* === PKS COMPLIANCE TABLE === */
    .compliance-table-wrap {
        background: #ffffff;
        border-radius: 18px;
        border: 1px solid rgba(22, 163, 74, 0.1);
        box-shadow: 0 2px 12px rgba(22, 163, 74, 0.06);
        overflow: hidden;
    }

    .compliance-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
        font-size: clamp(11.5px, 0.7vw + 8px, 13px);
    }

    .compliance-table thead th {
        background: #052e16;
        color: #86efac;
        font-size: 10px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.7px;
        padding: 12px 16px;
        border: none;
        white-space: nowrap;
    }

    .compliance-table tbody td {
        padding: 12px 16px;
        border-bottom: 1px solid rgba(22, 163, 74, 0.07);
        vertical-align: middle;
    }

    .compliance-table tbody tr:hover td {
        background: rgba(22, 163, 74, 0.03);
    }

    .compliance-table tbody tr:last-child td { border-bottom: none; }

    /* PKS Avatar */
    .pks-avatar {
        width: 38px; height: 38px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 800;
        font-size: 12px;
        color: #ffffff;
        flex-shrink: 0;
    }

    /* Module pill */
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

    .mod-pill-ok  { background: #dcfce7; color: #15803d; border: 1px solid #bbf7d0; }
    .mod-pill-err { background: #fee2e2; color: #b91c1c; border: 1px solid #fca5a5; }

    /* Status chip */
    .status-chip-tbl {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 4px 10px;
        border-radius: 50px;
        font-size: 10.5px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.3px;
    }

    .chip-safe    { background: #dcfce7; color: #15803d; border: 1px solid #86efac; }
    .chip-warning { background: #fef3c7; color: #b45309; border: 1px solid #fde68a; }
    .chip-danger  { background: #fee2e2; color: #dc2626; border: 1px solid #fca5a5; }

    /* === QUICK REPORT TILES === */
    .report-tile {
        display: flex;
        align-items: center;
        gap: 14px;
        padding: 16px 18px;
        background: #ffffff;
        border-radius: 16px;
        border: 1px solid rgba(22, 163, 74, 0.1);
        text-decoration: none !important;
        transition: all 0.25s cubic-bezier(0.34, 1.56, 0.64, 1);
        height: 100%;
        overflow: hidden;
        position: relative;
    }

    .report-tile::before {
        content: '';
        position: absolute;
        left: 0; top: 0; bottom: 0;
        width: 3px;
        background: linear-gradient(180deg, #16a34a, #4ade80);
        border-radius: 16px 0 0 16px;
    }

    .report-tile:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 28px rgba(22, 163, 74, 0.14);
        border-color: rgba(22, 163, 74, 0.3);
    }

    .report-tile-icon {
        width: 46px; height: 46px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        color: #ffffff;
        flex-shrink: 0;
    }

    .report-tile-title {
        font-family: 'Outfit', sans-serif;
        font-size: 13.5px;
        font-weight: 800;
        color: #14532d;
        margin-bottom: 2px;
    }

    .report-tile-desc {
        font-size: 11px;
        color: #6b7280;
        line-height: 1.4;
    }

    /* === STAGGER ANIMATIONS === */
    .kpi-card:nth-child(1) { animation-delay: 0.05s; }
    .kpi-card:nth-child(2) { animation-delay: 0.10s; }
    .kpi-card:nth-child(3) { animation-delay: 0.15s; }
    .kpi-card:nth-child(4) { animation-delay: 0.20s; }

    /* === FILTER TABS & METRICS === */
    .filter-card-admin {
        background: #ffffff;
        border-radius: 18px;
        border: 1.5px solid rgba(22, 163, 74, 0.15);
        box-shadow: 0 4px 20px rgba(22, 163, 74, 0.06);
        padding: 18px 20px;
        margin-bottom: 22px;
        transition: all 0.25s ease;
    }

    .filter-tabs-wrapper {
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
        background: rgba(22, 163, 74, 0.06);
        padding: 5px;
        border-radius: 12px;
        border: 1px solid rgba(22, 163, 74, 0.12);
    }

    .btn-periode-tab {
        border-radius: 8px;
        font-size: 12px;
        font-weight: 700;
        color: #374151;
        border: none;
        padding: 6px 14px;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        background: transparent;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        cursor: pointer;
    }

    .btn-periode-tab:hover {
        background: rgba(22, 163, 74, 0.12);
        color: #14532d;
    }

    .btn-periode-tab.active {
        background: var(--theme-primary, #16a34a) !important;
        color: #ffffff !important;
        box-shadow: 0 3px 10px var(--theme-glow, rgba(22, 163, 74, 0.3));
    }

    .metric-mini-badge {
        background: #f8fafc;
        border: 1px solid rgba(22, 163, 74, 0.1);
        border-radius: 14px;
        padding: 12px 16px;
        display: flex;
        align-items: center;
        gap: 12px;
        transition: all 0.2s ease;
        height: 100%;
    }

    .metric-mini-badge:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(22, 163, 74, 0.08);
        border-color: rgba(22, 163, 74, 0.25);
    }

    .metric-mini-icon {
        width: 38px;
        height: 38px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 16px;
        flex-shrink: 0;
    }

    .chart-param-guide {
        border-radius: 16px;
        background: #f0fdf4;
        border: 1px solid rgba(22, 163, 74, 0.15);
        padding: 18px 22px;
    }

    /* === DARK MODE === */
    html.app-skin-dark .kpi-card,
    html.app-skin-dark .chart-card,
    html.app-skin-dark .filter-card-admin,
    html.app-skin-dark .compliance-table-wrap,
    html.app-skin-dark .report-tile {
        background: #0a2317 !important;
        border-color: rgba(34, 197, 94, 0.15) !important;
    }

    html.app-skin-dark .filter-tabs-wrapper {
        background: rgba(34, 197, 94, 0.08) !important;
        border-color: rgba(34, 197, 94, 0.18) !important;
    }

    html.app-skin-dark .btn-periode-tab {
        color: #9ca3af;
    }

    html.app-skin-dark .btn-periode-tab:hover {
        background: rgba(34, 197, 94, 0.15);
        color: #86efac;
    }

    html.app-skin-dark .btn-periode-tab.active {
        background: var(--theme-primary, #16a34a) !important;
        color: #ffffff !important;
    }

    html.app-skin-dark .metric-mini-badge {
        background: rgba(255, 255, 255, 0.04) !important;
        border-color: rgba(34, 197, 94, 0.15) !important;
    }

    html.app-skin-dark .chart-param-guide {
        background: #0e3b26 !important;
        border-color: rgba(34, 197, 94, 0.2) !important;
    }

    html.app-skin-dark .kpi-value,
    html.app-skin-dark .chart-title,
    html.app-skin-dark .section-title,
    html.app-skin-dark .report-tile-title { color: #d1fae5 !important; }

    html.app-skin-dark .kpi-label,
    html.app-skin-dark .chart-sub,
    html.app-skin-dark .kpi-sub,
    html.app-skin-dark .report-tile-desc { color: #6b8f72 !important; }

    html.app-skin-dark .compliance-table tbody td {
        border-color: rgba(34, 197, 94, 0.08) !important;
        color: #d1fae5;
    }

    html.app-skin-dark .compliance-table tbody tr:hover td { background: rgba(34, 197, 94, 0.04) !important; }

    html.app-skin-dark .section-header { border-color: rgba(34, 197, 94, 0.12) !important; }

    /* === RESPONSIVE === */
    @media (max-width: 991.98px) {
        .hero-stat-grid { grid-template-columns: repeat(2, 1fr); }
    }

    @media (max-width: 575.98px) {
        .hero-stat-grid { grid-template-columns: repeat(2, 1fr); gap: 8px; }
        .admin-hero-inner { padding: 20px; }
        .hero-stat-val { font-size: 22px; }
        .filter-tabs-wrapper { overflow-x: auto; flex-wrap: nowrap; padding: 4px; }
    }
</style>
@endsection

@php
    $selectedDate = \Carbon\Carbon::parse($tanggal);
    $bulanLabel   = $selectedDate->translatedFormat('F Y');
    $filterBulan  = $selectedDate->format('m');
    $filterTahun  = $selectedDate->format('Y');

    $pksAvatarColors = [
        'TPU' => 'linear-gradient(135deg,#0284c7,#0369a1)',
        'TME' => 'linear-gradient(135deg,#2563eb,#1d4ed8)',
        'SGO' => 'linear-gradient(135deg,#0d9488,#0f766e)',
        'SPA' => 'linear-gradient(135deg,#16a34a,#15803d)',
        'SGH' => 'linear-gradient(135deg,#4f46e5,#4338ca)',
        'SBT' => 'linear-gradient(135deg,#7c3aed,#6d28d9)',
        'LDA' => 'linear-gradient(135deg,#9333ea,#7e22ce)',
        'TAN' => 'linear-gradient(135deg,#c026d3,#a21caf)',
        'TER' => 'linear-gradient(135deg,#e11d48,#be123c)',
        'STA' => 'linear-gradient(135deg,#ea580c,#c2410c)',
        'SRO' => 'linear-gradient(135deg,#d97706,#b45309)',
        'SIN' => 'linear-gradient(135deg,#059669,#047857)',
    ];
@endphp

@section('content')
<article class="dashboard-admin">

    {{-- ================================================================
         1. HERO EXECUTIVE BANNER
         ================================================================ --}}
    <section class="admin-hero" aria-label="Executive Summary Banner">
        <div class="admin-hero-inner">
            <div class="row align-items-start g-4">
                {{-- Left: Title & meta --}}
                <div class="col-lg-6">
                    <div class="live-badge">
                        <span class="live-dot"></span>
                        Monitoring Real-Time SIMOLI PTPN IV
                    </div>
                    <h2 class="admin-hero-title">
                        Executive Dashboard<br>
                        <span style="color:#4ade80;">Land Aplikasi & IPAL</span>
                    </h2>
                    <p class="admin-hero-sub">
                        Pengawasan terintegrasi 12 Unit PKS — pengaliran limbah, pemeliharaan kolam IPAL &amp; bed,
                        serta operasional alat berat secara real-time.
                    </p>
                    <div class="hero-meta-pills">
                        <div class="hero-meta-pill">
                            <i class="feather-calendar" style="font-size:14px;color:#4ade80;"></i>
                            {{ $selectedDate->locale('id')->translatedFormat('l, d F Y') }}
                        </div>
                        <div class="hero-meta-pill">
                            <i class="feather-shield" style="font-size:14px;color:#4ade80;"></i>
                            Administrator Regional SIMOLI
                        </div>
                    </div>
                </div>

                {{-- Right: Stats Grid --}}
                <div class="col-lg-6">
                    <div class="hero-stat-grid">
                        <div class="hero-stat-tile">
                            <div class="hero-stat-icon"><i class="feather-home" style="color:#4ade80;"></i></div>
                            <div class="hero-stat-val">{{ $totalPks }}</div>
                            <div class="hero-stat-lbl">PKS Terdaftar</div>
                        </div>
                        <div class="hero-stat-tile">
                            <div class="hero-stat-icon"><i class="feather-check-circle" style="color:#86efac;"></i></div>
                            <div class="hero-stat-val" style="color:#86efac;">{{ $sudahLengkap }}</div>
                            <div class="hero-stat-lbl">PKS Lengkap</div>
                        </div>
                        <div class="hero-stat-tile">
                            <div class="hero-stat-icon"><i class="feather-clock" style="color:#fde68a;"></i></div>
                            <div class="hero-stat-val" style="color:#fde68a;">{{ $sebagian }}</div>
                            <div class="hero-stat-lbl">Sebagian Input</div>
                        </div>
                        <div class="hero-stat-tile">
                            <div class="hero-stat-icon"><i class="feather-alert-triangle" style="color:#fca5a5;"></i></div>
                            <div class="hero-stat-val" style="color:#fca5a5;">{{ $belumAda }}</div>
                            <div class="hero-stat-lbl">Belum Input</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ================================================================
         2. KPI CARDS — Indikator Kinerja Utama
         ================================================================ --}}
    <section aria-label="Indikator Kinerja Utama">
        <div class="section-header">
            <h3 class="section-title">
                <span class="section-title-dot"></span>
                Indikator Kinerja Utama — Bulan {{ $bulanLabel }}
            </h3>
            <span class="mod-pill mod-pill-ok" style="font-size:11px;">Akumulasi Bulan Berjalan</span>
        </div>

        <div class="row g-3 mb-4">
            {{-- KPI 1: Pengaliran Volume --}}
            <div class="col-xl-3 col-md-6">
                <div class="kpi-card kpi-card-green">
                    <div class="d-flex align-items-start justify-content-between mb-3">
                        <div class="min-w-0">
                            <div class="kpi-label">Vol. Limbah Dialirkan</div>
                            <div class="kpi-value" style="color:#16a34a;">
                                {{ number_format($statPengaliran['vol_dialirkan'] ?? 0, 0, ',', '.') }}
                                <span style="font-size:14px;font-weight:600;color:#6b7280;">m³</span>
                            </div>
                        </div>
                        <div class="kpi-icon kpi-icon-green"><i class="feather-droplet"></i></div>
                    </div>
                    <div class="kpi-sub">
                        <span>Vol. Dihasilkan:</span>
                        <strong style="">{{ number_format($statPengaliran['vol_dihasilkan'] ?? 0, 0, ',', '.') }} m³</strong>
                    </div>
                </div>
            </div>

            {{-- KPI 2: Bed Land Aplikasi --}}
            <div class="col-xl-3 col-md-6">
                <div class="kpi-card kpi-card-green">
                    <div class="d-flex align-items-start justify-content-between mb-3">
                        <div class="min-w-0">
                            <div class="kpi-label">Bed Land Aplikasi</div>
                            <div class="kpi-value" style="color:#059669;">
                                {{ number_format($statPengaliran['total_flat_bed'] ?? 0, 0, ',', '.') }}
                                <span style="font-size:14px;font-weight:600;color:#6b7280;">Bed</span>
                            </div>
                        </div>
                        <div class="kpi-icon kpi-icon-green"><i class="feather-grid"></i></div>
                    </div>
                    <div class="kpi-sub">
                        <span>Luas Area:</span>
                        <strong style="">{{ number_format($statPengaliran['total_luas_area'] ?? 0, 1, ',', '.') }} Ha</strong>
                    </div>
                </div>
            </div>

            {{-- KPI 3: Pemeliharaan --}}
            <div class="col-xl-3 col-md-6">
                <div class="kpi-card kpi-card-gold">
                    <div class="d-flex align-items-start justify-content-between mb-3">
                        <div class="min-w-0">
                            <div class="kpi-label">Pemeliharaan Bed</div>
                            <div class="kpi-value" style="color:#b45309;">
                                {{ number_format(($statPemeliharaan['total_flat_bed'] ?? 0) + ($statPemeliharaan['total_long_bed'] ?? 0), 0, ',', '.') }}
                                <span style="font-size:14px;font-weight:600;color:#6b7280;">Bed</span>
                            </div>
                        </div>
                        <div class="kpi-icon kpi-icon-gold"><i class="feather-tool"></i></div>
                    </div>
                    <div class="kpi-sub">
                        <span>Tenaga Kerja:</span>
                        <strong style="">{{ number_format($statPemeliharaan['total_hk'] ?? 0, 0, ',', '.') }} HK</strong>
                    </div>
                </div>
            </div>

            {{-- KPI 4: Alat Berat --}}
            <div class="col-xl-3 col-md-6">
                <div class="kpi-card kpi-card-blue">
                    <div class="d-flex align-items-start justify-content-between mb-3">
                        <div class="min-w-0">
                            <div class="kpi-label">Alat Berat Operasional</div>
                            <div class="kpi-value" style="color:#1d4ed8;">
                                {{ $statAlatBerat['ready'] ?? 0 }}
                                <span style="font-size:14px;font-weight:600;color:#6b7280;">/ {{ $statAlatBerat['total_unit'] ?? 0 }} Unit</span>
                            </div>
                        </div>
                        <div class="kpi-icon kpi-icon-blue"><i class="feather-truck"></i></div>
                    </div>
                    <div class="kpi-sub">
                        <span>BBM Solar:</span>
                        <strong style="color:;">{{ number_format($statAlatBerat['total_bbm'] ?? 0, 0, ',', '.') }} Ltr</strong>
                        <span class="ms-2">HM:</span>
                        <strong style="color:;">{{ number_format($statAlatBerat['total_hm'] ?? 0, 1, ',', '.') }} Jam</strong>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ================================================================
         3. ANALYTICS & INTERACTIVE MULTI-PERIOD CHARTS
         ================================================================ --}}
    <section aria-label="Analitik & Visualisasi Data SIMOLI" class="mb-4">
@php
    $curBulanName = \Carbon\Carbon::create()->month((int)($bulan ?? date('m')))->translatedFormat('F');
    $initialPeriodeLabels = [
        'semua'    => 'Semua Periode Data',
        'harian'   => 'Harian: ' . $curBulanName . ' ' . ($tahun ?? date('Y')),
        'mingguan' => 'Mingguan: ' . $curBulanName . ' ' . ($tahun ?? date('Y')),
        'bulanan'  => 'Bulanan: Tahun ' . ($tahun ?? date('Y')),
        'tahunan'  => 'Grafik Per Tahun',
        'custom'   => 'Rentang: ' . ($tglMulai ?? '') . ' s.d ' . ($tglSelesai ?? ''),
    ];
    $initialPeriodeText = $initialPeriodeLabels[$periode ?? 'semua'] ?? 'Semua Periode Data';
    $selectedPksModel = !empty($idPksFilter) ? $pksList->firstWhere('id_pks', $idPksFilter) : null;
    $initialPksText = $selectedPksModel ? ($selectedPksModel->akro ?? $selectedPksModel->nama) . ' — ' . $selectedPksModel->nama : 'Semua PKS (12 Unit)';
@endphp
        <div class="section-header">
            <h3 class="section-title">
                <span class="section-title-dot"></span>
                Analitik &amp; Visualisasi Data Eksekutif
            </h3>
            <div class="d-flex align-items-center gap-2">
                <span class="mod-pill mod-pill-ok" id="adminActiveFilterBadge" style="font-size:11px;">
                    <i class="feather-check-circle me-1"></i> {{ $initialPeriodeText }}{{ !empty($idPksFilter) ? ' • ' . $initialPksText : '' }}
                </span>
            </div>
        </div>

        {{-- Filter Control Panel --}}
        <div class="filter-card-admin">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-3">
                {{-- Periode Switcher Tab Buttons --}}
                <div class="filter-tabs-wrapper" id="adminPeriodeFilterContainer">
                    <button type="button" class="btn-periode-tab {{ ($periode ?? 'semua') == 'semua' ? 'active' : '' }}"
                        data-periode="semua" onclick="window.setAdminPeriodeFilter('semua')">
                        <i class="feather-layers" style="font-size:13px;"></i> Semua Data (Default)
                    </button>
                    <button type="button" class="btn-periode-tab {{ ($periode ?? '') == 'harian' ? 'active' : '' }}"
                        data-periode="harian" onclick="window.setAdminPeriodeFilter('harian')">
                        <i class="feather-sun" style="font-size:13px;"></i> Harian
                    </button>
                    <button type="button" class="btn-periode-tab {{ ($periode ?? '') == 'mingguan' ? 'active' : '' }}"
                        data-periode="mingguan" onclick="window.setAdminPeriodeFilter('mingguan')">
                        <i class="feather-calendar" style="font-size:13px;"></i> Mingguan
                    </button>
                    <button type="button" class="btn-periode-tab {{ ($periode ?? '') == 'bulanan' ? 'active' : '' }}"
                        data-periode="bulanan" onclick="window.setAdminPeriodeFilter('bulanan')">
                        <i class="feather-grid" style="font-size:13px;"></i> Bulanan
                    </button>
                    <button type="button" class="btn-periode-tab {{ ($periode ?? '') == 'tahunan' ? 'active' : '' }}"
                        data-periode="tahunan" onclick="window.setAdminPeriodeFilter('tahunan')">
                        <i class="feather-bar-chart-2" style="font-size:13px;"></i> Tahunan
                    </button>
                    <button type="button" class="btn-periode-tab {{ ($periode ?? '') == 'custom' ? 'active' : '' }}"
                        data-periode="custom" onclick="window.setAdminPeriodeFilter('custom')">
                        <i class="feather-sliders" style="font-size:13px;"></i> Custom Tanggal
                    </button>
                </div>

                {{-- Status Pills / Indicators --}}
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <span class="badge rounded-pill px-3 py-2 fw-semibold" id="adminFilterLabelPeriode"
                          style="background:rgba(22,163,74,0.1);color:#15803d;border:1px solid rgba(22,163,74,0.25);font-size:11.5px;">
                        {{ $initialPeriodeText }}
                    </span>
                    <span class="badge rounded-pill px-3 py-2 fw-semibold" id="adminFilterLabelPks"
                          style="background:rgba(2,132,199,0.1);color:#0369a1;border:1px solid rgba(2,132,199,0.25);font-size:11.5px;">
                        {{ $initialPksText }}
                    </span>
                </div>
            </div>

            {{-- Filter Parameter Form --}}
            <form id="filterGrafikAdmin" class="row g-3 align-items-end pt-2" style="border-top:1px solid rgba(22,163,74,0.08);">
                <input type="hidden" name="periode" id="adminPeriodeVal" value="{{ $periode ?? 'semua' }}">

                {{-- Filter PKS --}}
                <div class="col-md-3 col-sm-6">
                    <label class="form-label mb-1 fw-bold text-muted" style="font-size:11px;">Pilih Unit PKS</label>
                    <select name="id_pks" id="adminIdPks" class="form-select form-select-sm fw-semibold" style="border-radius:10px;border-color:rgba(22,163,74,0.3);font-size:12px;">
                        <option value="" {{ empty($idPksFilter) ? 'selected' : '' }}>Semua PKS (12 Unit Regional)</option>
                        @foreach($pksList as $p)
                            <option value="{{ $p->id_pks }}" {{ ($idPksFilter ?? '') == $p->id_pks ? 'selected' : '' }}>
                                {{ $p->akro ?? $p->nama }} — {{ $p->nama }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Filter Tahun --}}
                <div class="col-md-2 col-sm-6 filter-admin-year-group {{ in_array($periode ?? 'semua', ['semua', 'tahunan', 'custom']) ? 'd-none' : '' }}" id="adminTahunGroup">
                    <label class="form-label mb-1 fw-bold text-muted" style="font-size:11px;">Tahun</label>
                    <select name="tahun" id="adminTahun" class="form-select form-select-sm fw-semibold" style="border-radius:10px;border-color:rgba(22,163,74,0.3);font-size:12px;">
                        @foreach($tahunList as $y)
                            <option value="{{ $y }}" {{ ($tahun ?? date('Y')) == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Filter Bulan --}}
                <div class="col-md-2 col-sm-6 filter-admin-month-group {{ in_array($periode ?? 'semua', ['harian', 'mingguan']) ? '' : 'd-none' }}" id="adminBulanGroup">
                    <label class="form-label mb-1 fw-bold text-muted" style="font-size:11px;">Bulan</label>
                    <select name="bulan" id="adminBulan" class="form-select form-select-sm fw-semibold" style="border-radius:10px;border-color:rgba(22,163,74,0.3);font-size:12px;">
                        @for($i = 1; $i <= 12; $i++)
                            <option value="{{ $i }}" {{ ($bulan ?? date('m')) == $i ? 'selected' : '' }}>
                                {{ \Carbon\Carbon::create()->month($i)->translatedFormat('F') }}
                            </option>
                        @endfor
                    </select>
                </div>

                {{-- Custom Date Range --}}
                <div class="col-md-2 col-sm-6 filter-admin-custom-group {{ ($periode ?? '') == 'custom' ? '' : 'd-none' }}" id="adminCustomStartGroup">
                    <label class="form-label mb-1 fw-bold text-success" style="font-size:11px;">Dari Tanggal</label>
                    <input type="date" name="tgl_mulai" id="adminTglMulai" class="form-control form-control-sm fw-semibold"
                           value="{{ $tglMulai ?? date('Y-m-01') }}" style="border-radius:10px;border-color:rgba(22,163,74,0.4);font-size:12px;">
                </div>
                <div class="col-md-2 col-sm-6 filter-admin-custom-group {{ ($periode ?? '') == 'custom' ? '' : 'd-none' }}" id="adminCustomEndGroup">
                    <label class="form-label mb-1 fw-bold text-success" style="font-size:11px;">Sampai Tanggal</label>
                    <input type="date" name="tgl_selesai" id="adminTglSelesai" class="form-control form-control-sm fw-semibold"
                           value="{{ $tglSelesai ?? date('Y-m-d') }}" style="border-radius:10px;border-color:rgba(22,163,74,0.4);font-size:12px;">
                </div>

                {{-- Location Type Filter --}}
                <div class="col-md-2 col-sm-6">
                    <label class="form-label mb-1 fw-bold text-muted" style="font-size:11px;">Jenis Lokasi</label>
                    <select name="jenis" id="adminJenis" class="form-select form-select-sm fw-semibold" style="border-radius:10px;border-color:rgba(22,163,74,0.3);font-size:12px;">
                        <option value="flat_bed" {{ ($jenis ?? 'flat_bed') == 'flat_bed' ? 'selected' : '' }}>Flat Bed</option>
                        <option value="no_bak" {{ ($jenis ?? '') == 'no_bak' ? 'selected' : '' }}>Nomor Bak</option>
                        <option value="blok" {{ ($jenis ?? '') == 'blok' ? 'selected' : '' }}>Blok Lahan</option>
                    </select>
                </div>

                {{-- Specific Filter --}}
                <div class="col-md-2 col-sm-6">
                    <label class="form-label mb-1 fw-bold text-muted" style="font-size:11px;">Filter Spesifik</label>
                    <select name="nilai" id="adminNilai" class="form-select form-select-sm fw-semibold" style="border-radius:10px;border-color:rgba(22,163,74,0.3);font-size:12px;">
                        <option value="">Semua Lokasi</option>
                        @foreach($grafikRes['pilihan'] ?? [] as $item)
                            <option value="{{ $item }}" {{ ($nilai !== null && $nilai !== '' && (string)$nilai === (string)$item) ? 'selected' : '' }}>{{ $item }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Apply Button --}}
                <div class="col-auto filter-tabs-wrapper">
                    <button type="button" id="btnTerapkanFilterAdmin" class="btn-ptpn btn-ptpn-primary" onclick="window.applyAdminFilterGrafik()"
                            style="padding:7px 18px;font-size:12px;font-weight:800;border-radius:10px;">
                        <i class="feather-filter" style="font-size:13px;"></i> Terapkan
                    </button><br>
                    <button type="button" class="btn btn-sm btn-light border ms-1 fw-bold" onclick="window.resetAdminFilterGrafik()"
                            style="border-radius:10px;padding:7px 12px;font-size:12px;" title="Reset Filter">
                        <i class="feather-refresh-cw" style="font-size:12px;"></i>
                    </button>
                </div>
            </form>
        </div>

        {{-- Live Metric KPI Strip (Filtered Data Summary) --}}
        <div class="row g-3 mb-4">
            <div class="col-xl-3 col-md-6">
                <div class="metric-mini-badge">
                    <div class="metric-mini-icon" style="background:#dcfce7;color:#16a34a;border:1px solid #bbf7d0;">
                        <i class="feather-droplet"></i>
                    </div>
                    <div>
                        <div style="font-size:10.5px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:0.5px;">Total Vol. Dialirkan</div>
                        <div class="fw-black" style="font-family:'Outfit',sans-serif;font-size:20px;color:#16a34a;line-height:1.2;">
                            <span id="metricTotalDialirkan">{{ number_format($grafikRes['totalDialirkan'] ?? 0, 0, ',', '.') }}</span>
                            <small style="font-size:12px;font-weight:600;color:#6b7280;">m³</small>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6">
                <div class="metric-mini-badge">
                    <div class="metric-mini-icon" style="background:#e0f2fe;color:#0284c7;border:1px solid #bae6fd;">
                        <i class="feather-layers"></i>
                    </div>
                    <div>
                        <div style="font-size:10.5px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:0.5px;">Total Vol. Dihasilkan</div>
                        <div class="fw-black" style="font-family:'Outfit',sans-serif;font-size:20px;color:#0284c7;line-height:1.2;">
                            <span id="metricTotalDihasilkan">{{ number_format($grafikRes['totalDihasilkan'] ?? 0, 0, ',', '.') }}</span>
                            <small style="font-size:12px;font-weight:600;color:#6b7280;">m³</small>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6">
                <div class="metric-mini-badge">
                    <div class="metric-mini-icon" style="background:#fef3c7;color:#d97706;border:1px solid #fde68a;">
                        <i class="feather-percent"></i>
                    </div>
                    <div>
                        <div style="font-size:10.5px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:0.5px;">Rasio Efisiensi LA</div>
                        <div class="fw-black" style="font-family:'Outfit',sans-serif;font-size:20px;color:#d97706;line-height:1.2;">
                            <span id="metricEfficiency">{{ $grafikRes['efficiency'] ?? 0 }}</span>%
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6">
                <div class="metric-mini-badge">
                    <div class="metric-mini-icon" style="background:#f3e8ff;color:#7c3aed;border:1px solid #e9d5ff;">
                        <i class="feather-tool"></i>
                    </div>
                    <div>
                        <div style="font-size:10.5px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:0.5px;">Total Pemeliharaan Bed</div>
                        <div class="fw-black" style="font-family:'Outfit',sans-serif;font-size:20px;color:#7c3aed;line-height:1.2;">
                            <span id="metricTotalBed">{{ number_format(($grafikRes['totalFlatBed'] ?? 0) + ($grafikRes['totalLongBed'] ?? 0), 0, ',', '.') }}</span>
                            <small style="font-size:12px;font-weight:600;color:#6b7280;">Bed</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Visual Charts Grid --}}
        <div class="row g-4 mb-4">
            {{-- Main Volume Trend Chart --}}
            <div class="col-lg-8">
                <div class="chart-card">
                    <div class="chart-card-header">
                        <div>
                            <div class="chart-title">
                                <i class="feather-trending-up" style="color:#16a34a;margin-right:6px;"></i>
                                Trend Volume Limbah (Dialirkan vs Dihasilkan)
                            </div>
                            <div class="chart-sub" id="mainChartSub">Visualisasi data debit limbah cair IPAL &amp; Land Application (m³)</div>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge bg-success-subtle text-success border border-success-subtle fw-bold" id="mainChartBadgePeriode">
                                {{ strtoupper($periode ?? 'semua') }}
                            </span>
                        </div>
                    </div>
                    <div style="padding:20px;">
                        <div id="volumeChart" style="min-height:320px;"></div>
                    </div>
                </div>
            </div>

            {{-- Donut Status Kepatuhan Hari Ini --}}
            <div class="col-lg-4">
                <div class="chart-card">
                    <div class="chart-card-header">
                        <div>
                            <div class="chart-title">
                                <i class="feather-pie-chart" style="color:#16a34a;margin-right:6px;"></i>
                                Status Kelengkapan Hari Ini
                            </div>
                            <div class="chart-sub">Kepatuhan input 12 PKS</div>
                        </div>
                    </div>
                    <div style="padding:18px;">
                        <div id="statusDonutChart" style="min-height:230px;"></div>
                        <div class="row g-2 text-center pt-2 mt-2" style="border-top:1px solid rgba(22,163,74,0.1);">
                            <div class="col-4">
                                <div style="font-size:18px;font-weight:900;color:#16a34a;">{{ $sudahLengkap }}</div>
                                <small style="font-size:10.5px;color:#6b7280;font-weight:700;">Lengkap</small>
                            </div>
                            <div class="col-4">
                                <div style="font-size:18px;font-weight:900;color:#d97706;">{{ $sebagian }}</div>
                                <small style="font-size:10.5px;color:#6b7280;font-weight:700;">Sebagian</small>
                            </div>
                            <div class="col-4">
                                <div style="font-size:18px;font-weight:900;color:#ef4444;">{{ $belumAda }}</div>
                                <small style="font-size:10.5px;color:#6b7280;font-weight:700;">Belum Ada</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Volume Per PKS + Pemeliharaan Per PKS --}}
        <div class="row g-4 mb-4">
            <div class="col-lg-6">
                <div class="chart-card">
                    <div class="chart-card-header">
                        <div>
                            <div class="chart-title">
                                <i class="feather-bar-chart-2" style="color:#16a34a;margin-right:6px;"></i>
                                Volume Limbah Dialirkan per PKS
                            </div>
                            <div class="chart-sub" id="pksVolChartSub">Perbandingan akumulasi volume per unit PKS (m³)</div>
                        </div>
                        <span class="mod-pill mod-pill-ok" id="badgeVolPerPks" style="font-size:10px;">{{ !empty($idPksFilter) ? $initialPksText : '12 PKS Regional' }}</span>
                    </div>
                    <div style="padding:18px;">
                        <div id="volPerPksChart" style="min-height:290px;"></div>
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="chart-card">
                    <div class="chart-card-header">
                        <div>
                            <div class="chart-title">
                                <i class="feather-sliders" style="color:#d97706;margin-right:6px;"></i>
                                Hasil Pemeliharaan Bed per PKS
                            </div>
                            <div class="chart-sub" id="pksMaintChartSub">Normalisasi Flat Bed &amp; Long Bed dikerjakan</div>
                        </div>
                        <span class="mod-pill mod-pill-ok" id="badgeMaintPerPks" style="font-size:10px;">{{ !empty($idPksFilter) ? $initialPksText : 'Flat & Long Bed' }}</span>
                    </div>
                    <div style="padding:18px;">
                        <div id="maintPerPksChart" style="min-height:290px;"></div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Trend Monitoring Chart --}}
        <div class="row g-4 mb-4">
            <div class="col-12">
                <div class="chart-card">
                    <div class="chart-card-header">
                        <div>
                            <div class="chart-title">
                                <i class="feather-activity" style="color:#16a34a;margin-right:6px;"></i>
                                Trend Frekuensi &amp; Aktivitas Pelaporan Monitoring
                            </div>
                            <div class="chart-sub">Pergerakan intensitas pelaporan Pengaliran LA vs Pemeliharaan Bed</div>
                        </div>
                    </div>
                    <div style="padding:18px;">
                        <div id="trendChart" style="min-height:260px;"></div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Panduan & Penjelasan Parameter Grafik --}}
        <div class="chart-param-guide">
            <div class="d-flex align-items-center gap-2 mb-3" style="font-size:13px;font-weight:800;color:#14532d;">
                <i class="feather-info" style="color:#16a34a;font-size:17px;"></i>
                Panduan &amp; Standar Parameter Analitik Grafik SIMOLI PTPN IV
            </div>
            <div class="row g-3">
                <div class="col-md-3 col-sm-6">
                    <div class="d-flex align-items-start gap-2">
                        <div style="width:10px;height:10px;border-radius:50%;background:#16a34a;margin-top:4px;flex-shrink:0;"></div>
                        <div>
                            <strong style="font-size:11.5px;color:#15803d;display:block;">Vol. Dialirkan (m³)</strong>
                            <span style="font-size:11px;color:#4b5563;line-height:1.4;display:block;">
                                Debit limbah cair matang yang dialirkan dari kolam IPAL ke flat bed / blok Land Application.
                            </span>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="d-flex align-items-start gap-2">
                        <div style="width:10px;height:10px;border-radius:50%;background:#0284c7;margin-top:4px;flex-shrink:0;"></div>
                        <div>
                            <strong style="font-size:11.5px;color:#0369a1;display:block;">Vol. Dihasilkan (m³)</strong>
                            <span style="font-size:11px;color:#4b5563;line-height:1.4;display:block;">
                                Estimasi volume limbah cair hasil olahan Pabrik Kelapa Sawit (TBS).
                            </span>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="d-flex align-items-start gap-2">
                        <div style="width:10px;height:10px;border-radius:50%;background:#d97706;margin-top:4px;flex-shrink:0;"></div>
                        <div>
                            <strong style="font-size:11.5px;color:#b45309;display:block;">Rasio Efisiensi LA (%)</strong>
                            <span style="font-size:11px;color:#4b5563;line-height:1.4;display:block;">
                                Persentase pemanfaatan limbah dialirkan terhadap total limbah dihasilkan.
                            </span>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="d-flex align-items-start gap-2">
                        <div style="width:10px;height:10px;border-radius:50%;background:#7c3aed;margin-top:4px;flex-shrink:0;"></div>
                        <div>
                            <strong style="font-size:11.5px;color:#6d28d9;display:block;">Mode Filter Multi-Periode</strong>
                            <span style="font-size:11px;color:#4b5563;line-height:1.4;display:block;">
                                Akses visualisasi <strong>Semua Data, Harian, Mingguan, Bulanan, Tahunan</strong>, atau <strong>Rentang Kustom</strong>.
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ================================================================
         4. PKS COMPLIANCE MATRIX TABLE
         ================================================================ --}}
    <section aria-label="Matriks Kepatuhan PKS" class="mb-4">
        <div class="section-header">
            <h3 class="section-title">
                <span class="section-title-dot"></span>
                Matriks Kelengkapan Harian Seluruh PKS
            </h3>
            <span class="mod-pill mod-pill-ok" style="font-size:11px;">
                {{ $selectedDate->translatedFormat('d F Y') }}
            </span>
        </div>

        <div class="compliance-table-wrap">
            <div class="table-responsive" style="-webkit-overflow-scrolling: touch;">
                <table class="compliance-table" role="table" aria-label="Status Kepatuhan PKS">
                    <thead>
                        <tr>
                            <th style="width:48px;text-align:center;">#</th>
                            <th style="min-width:200px;">PKS (Pabrik Kelapa Sawit)</th>
                            <th>Pengaliran LA</th>
                            <th>Pemeliharaan Bed</th>
                            <th>Rencana Tahunan</th>
                            <th style="width:130px;text-align:center;">Kelengkapan</th>
                            <th style="width:140px;text-align:center;">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $no = 1; @endphp
                        @foreach($monitoringData as $item)
                        @php
                            $akro   = strtoupper($item['pks']->akro ?? '');
                            $avatar = $pksAvatarColors[$akro] ?? 'linear-gradient(135deg,#16a34a,#059669)';
                            $pksId  = $item['pks']->id_pks ?? $item['pks']->ID;
                            $adaRencana = $rencanaMap[$pksId] ?? false;
                            $done = ($item['pengaliran'] ? 1 : 0) + ($item['pemeliharaan'] ? 1 : 0) + ($adaRencana ? 1 : 0);
                            $chipClass = $done == 3 ? 'chip-safe' : ($done > 0 ? 'chip-warning' : 'chip-danger');
                            $chipLabel = $done == 3 ? 'Lengkap' : ($done > 0 ? 'Sebagian' : 'Belum Input');
                            $chipIcon  = $done == 3 ? 'feather-check-circle' : ($done > 0 ? 'feather-clock' : 'feather-x-circle');
                        @endphp
                        <tr>
                            <td style="text-align:center;font-weight:700;color:#9ca3af;font-size:12px;">{{ $no++ }}</td>
                            <td>
                                <div style="display:flex;align-items:center;gap:10px;">
                                    <div class="pks-avatar" style="background:{{ $avatar }};">{{ $akro }}</div>
                                    <div>
                                        <div style="font-weight:700;font-size:13px;">{{ $item['pks']->nama }}</div>
                                        <div style="font-size:10.5px;color:#6b7280;font-weight:600;">PKS {{ $akro }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                @if($item['pengaliran'])
                                    <span class="mod-pill mod-pill-ok"><i class="feather-check-circle"></i> Sudah Input</span>
                                @else
                                    <span class="mod-pill mod-pill-err"><i class="feather-x-circle"></i> Belum Ada</span>
                                @endif
                            </td>
                            <td>
                                @if($item['pemeliharaan'])
                                    <span class="mod-pill mod-pill-ok"><i class="feather-check-circle"></i> Sudah Input</span>
                                @else
                                    <span class="mod-pill mod-pill-err"><i class="feather-x-circle"></i> Belum Ada</span>
                                @endif
                            </td>
                            <td>
                                @if($adaRencana)
                                    <span class="mod-pill mod-pill-ok"><i class="feather-check-circle"></i> Tersedia</span>
                                @else
                                    <span class="mod-pill mod-pill-err"><i class="feather-x-circle"></i> Belum Ada</span>
                                @endif
                            </td>
                            <td style="text-align:center;">
                                <div style="font-size:12px;font-weight:800;margin-bottom:6px;">{{ $done }} / 3</div>
                                <div style="height:6px;border-radius:10px;background:rgba(22,163,74,0.1);overflow:hidden;">
                                    <div style="height:100%;border-radius:10px;width:{{ ($done/3)*100 }}%;
                                        background:{{ $done==3 ? 'linear-gradient(90deg,#16a34a,#4ade80)' : ($done>0 ? 'linear-gradient(90deg,#d97706,#fbbf24)' : '#ef4444') }};
                                        transition:width 1s ease;"></div>
                                </div>
                            </td>
                            <td style="text-align:center;">
                                <span class="status-chip-tbl {{ $chipClass }}">
                                    <i class="{{ $chipIcon }}" style="font-size:12px;"></i>
                                    {{ $chipLabel }}
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    {{-- ================================================================
         5. QUICK REPORT ACCESS TILES
         ================================================================ --}}
    <section aria-label="Akses Cepat Laporan" class="mb-4">
        <div class="section-header">
            <h3 class="section-title">
                <span class="section-title-dot"></span>
                Pusat Akses Laporan Eksekutif
            </h3>
            <span class="mod-pill mod-pill-ok" style="font-size:11px;">Format Resmi SIMOLI</span>
        </div>

        <div class="row g-3">
            <div class="col-xl-3 col-md-6">
                <a href="{{ route('report-pengaliran', ['bulan' => $filterBulan, 'tahun' => $filterTahun]) }}" class="report-tile">
                    <div class="report-tile-icon" style="background:linear-gradient(135deg,#16a34a,#22c55e);">
                        <i class="feather-droplet"></i>
                    </div>
                    <div>
                        <div class="report-tile-title">Laporan Pengaliran LA</div>
                        <div class="report-tile-desc">Format Land Aplikasi of the Month &amp; rekap per PKS</div>
                    </div>
                    <i class="feather-arrow-right" style="font-size:16px;color:#16a34a;margin-left:auto;flex-shrink:0;"></i>
                </a>
            </div>
            <div class="col-xl-3 col-md-6">
                <a href="{{ route('report-pemeliharaan', ['bulan' => $filterBulan, 'tahun' => $filterTahun]) }}" class="report-tile">
                    <div class="report-tile-icon" style="background:linear-gradient(135deg,#d97706,#f59e0b);">
                        <i class="feather-tool"></i>
                    </div>
                    <div>
                        <div class="report-tile-title">Laporan Pemeliharaan</div>
                        <div class="report-tile-desc">Normalisasi bed, pengerukan kolam &amp; HK tenaga kerja</div>
                    </div>
                    <i class="feather-arrow-right" style="font-size:16px;color:#d97706;margin-left:auto;flex-shrink:0;"></i>
                </a>
            </div>
            <div class="col-xl-3 col-md-6">
                <a href="{{ route('report-rencana', ['tahun' => $filterTahun]) }}" class="report-tile">
                    <div class="report-tile-icon" style="background:linear-gradient(135deg,#7c3aed,#8b5cf6);">
                        <i class="feather-clipboard"></i>
                    </div>
                    <div>
                        <div class="report-tile-title">Laporan Rencana Tahunan</div>
                        <div class="report-tile-desc">Target flat bed, long bed &amp; kapasitas kolam IPAL</div>
                    </div>
                    <i class="feather-arrow-right" style="font-size:16px;color:#7c3aed;margin-left:auto;flex-shrink:0;"></i>
                </a>
            </div>
            <div class="col-xl-3 col-md-6">
                <a href="{{ route('monitoring-alat-berat.index') }}" class="report-tile">
                    <div class="report-tile-icon" style="background:linear-gradient(135deg,#0d9488,#14b8a6);">
                        <i class="feather-truck"></i>
                    </div>
                    <div>
                        <div class="report-tile-title">Log Operasional Alat Berat</div>
                        <div class="report-tile-desc">Monitoring HM, BBM solar &amp; dokumentasi lapangan</div>
                    </div>
                    <i class="feather-arrow-right" style="font-size:16px;color:#0d9488;margin-left:auto;flex-shrink:0;"></i>
                </a>
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
    var curTheme  = window.SimoliTheme && window.SimoliTheme.currentConfig ? window.SimoliTheme.currentConfig : null;
    var primary   = (curTheme && curTheme.themeData) ? curTheme.themeData.primary : '#16a34a';
    var accent    = (curTheme && curTheme.themeData) ? curTheme.themeData.accent : '#4ade80';
    var secondary = (curTheme && curTheme.themeData) ? curTheme.themeData.secondary : '#0284c7';
    var amber     = '#d97706';

    var gridColor = isDark ? 'rgba(255,255,255,0.06)' : 'rgba(0,0,0,0.06)';
    var labelClr  = isDark ? '#9ca3af' : '#6b7280';
    var bgCard    = isDark ? '#0a2317' : '#ffffff';
    var fontFam   = "'Outfit', 'Plus Jakarta Sans', sans-serif";

    var chartDefaults = {
        toolbar: { show: false },
        fontFamily: fontFam,
        background: 'transparent',
        animations: { enabled: true, easing: 'easeinout', speed: 700 }
    };

    var initialGrafik = @json($grafikRes ?? []);

    /* ── 1. MAIN AREA CHART: Trend Volume Limbah (Dialirkan vs Dihasilkan) ── */
    var chartElVolume = document.querySelector('#volumeChart');
    var chartVolume = null;
    if (chartElVolume) {
        chartVolume = new ApexCharts(chartElVolume, {
            series: [
                {
                    name: 'Volume Limbah Dialirkan (m³)',
                    data: initialGrafik.volumeDialirkan || []
                },
                {
                    name: 'Volume Limbah Dihasilkan (m³)',
                    data: initialGrafik.volumeDihasilkan || []
                }
            ],
            chart: {
                ...chartDefaults,
                type: 'area',
                height: 320,
                toolbar: { show: true, tools: { download: true, selection: true, zoom: true, zoomin: true, zoomout: true, pan: true, reset: true } }
            },
            colors: [primary, secondary],
            fill: {
                type: 'gradient',
                gradient: {
                    shadeIntensity: 1, opacityFrom: 0.38, opacityTo: 0.05, stops: [0, 100]
                }
            },
            stroke: { curve: 'smooth', width: 2.8 },
            xaxis: {
                categories: initialGrafik.labels || [],
                labels: { style: { colors: labelClr, fontSize: '11px', fontFamily: fontFam } },
                axisBorder: { show: false }, axisTicks: { show: false }
            },
            yaxis: {
                min: 0,
                labels: {
                    style: { colors: labelClr, fontSize: '11px', fontFamily: fontFam },
                    formatter: function(val) { return Math.round(val).toLocaleString('id-ID'); }
                }
            },
            grid: { borderColor: gridColor, strokeDashArray: 4 },
            markers: { size: 4, strokeWidth: 0, hover: { size: 6 } },
            legend: { position: 'top', horizontalAlign: 'right', fontSize: '11.5px', fontFamily: fontFam, labels: { colors: labelClr } },
            tooltip: {
                theme: isDark ? 'dark' : 'light',
                style: { fontSize: '12px', fontFamily: fontFam },
                y: { formatter: function(val) { return (val || 0).toLocaleString('id-ID') + ' m³'; } }
            }
        });
        chartVolume.render();
    }

    /* ── 2. DONUT: Kelengkapan Hari Ini ── */
    var chartElDonut = document.querySelector('#statusDonutChart');
    var chartStatusDonut = null;
    if (chartElDonut) {
        chartStatusDonut = new ApexCharts(chartElDonut, {
            series: [{{ $sudahLengkap }}, {{ $sebagian }}, {{ $belumAda }}],
            chart: { ...chartDefaults, type: 'donut', height: 230 },
            labels: ['Lengkap', 'Sebagian', 'Belum Input'],
            colors: [primary, amber, '#ef4444'],
            plotOptions: {
                pie: {
                    donut: {
                        size: '72%',
                        labels: {
                            show: true,
                            name: { show: true, fontSize: '12px', color: labelClr },
                            value: { show: true, fontSize: '20px', fontWeight: 900, color: isDark ? '#d1fae5' : '#14532d' },
                            total: {
                                show: true, label: 'Total PKS',
                                fontSize: '10px', fontWeight: 600, color: labelClr,
                                formatter: function() { return {{ $totalPks }}; }
                            }
                        }
                    }
                }
            },
            legend: { position: 'bottom', fontSize: '11px', fontFamily: fontFam, labels: { colors: labelClr } },
            stroke: { width: 2, colors: [bgCard] },
            dataLabels: { enabled: false }
        });
        chartStatusDonut.render();
    }

    /* ── 3. COLUMN: Volume per PKS ── */
    var chartElVolPks = document.querySelector('#volPerPksChart');
    var chartVolPerPks = null;
    if (chartElVolPks) {
        chartVolPerPks = new ApexCharts(chartElVolPks, {
            series: [{ name: 'Vol. Dialirkan (m³)', data: initialGrafik.pksVols || [] }],
            chart: { ...chartDefaults, type: 'bar', height: 290 },
            colors: [primary],
            fill: {
                type: 'gradient',
                gradient: { shade: 'light', type: 'vertical', shadeIntensity: 0.3, gradientToColors: [accent], stops: [0, 100] }
            },
            plotOptions: { bar: { borderRadius: 6, columnWidth: '50%' } },
            xaxis: {
                categories: initialGrafik.pksNames || [],
                labels: { style: { colors: labelClr, fontSize: '10.5px', fontFamily: fontFam } },
                axisBorder: { show: false }
            },
            yaxis: {
                labels: {
                    style: { colors: labelClr, fontSize: '11px', fontFamily: fontFam },
                    formatter: function(val) { return Math.round(val).toLocaleString('id-ID'); }
                }
            },
            grid: { borderColor: gridColor, strokeDashArray: 4 },
            dataLabels: { enabled: false },
            tooltip: {
                theme: isDark ? 'dark' : 'light',
                style: { fontSize: '11px', fontFamily: fontFam },
                y: { formatter: function(val) { return (val || 0).toLocaleString('id-ID') + ' m³'; } }
            }
        });
        chartVolPerPks.render();
    }

    /* ── 4. STACKED COLUMN: Pemeliharaan per PKS ── */
    var chartElMaintPks = document.querySelector('#maintPerPksChart');
    var chartMaintPerPks = null;
    if (chartElMaintPks) {
        chartMaintPerPks = new ApexCharts(chartElMaintPks, {
            series: [
                { name: 'Flat Bed', data: initialGrafik.pksFlatBed || [] },
                { name: 'Long Bed', data: initialGrafik.pksLongBed || [] }
            ],
            chart: { ...chartDefaults, type: 'bar', height: 290, stacked: true },
            colors: [amber, primary],
            plotOptions: { bar: { borderRadius: 4, columnWidth: '50%' } },
            xaxis: {
                categories: initialGrafik.pksNames || [],
                labels: { style: { colors: labelClr, fontSize: '10.5px', fontFamily: fontFam } },
                axisBorder: { show: false }
            },
            yaxis: {
                labels: {
                    style: { colors: labelClr, fontSize: '11px', fontFamily: fontFam },
                    formatter: function(val) { return Math.round(val).toLocaleString('id-ID'); }
                }
            },
            grid: { borderColor: gridColor, strokeDashArray: 4 },
            dataLabels: { enabled: false },
            legend: { position: 'top', horizontalAlign: 'right', fontSize: '11px', fontFamily: fontFam, labels: { colors: labelClr } },
            tooltip: {
                theme: isDark ? 'dark' : 'light',
                style: { fontSize: '11px', fontFamily: fontFam },
                y: { formatter: function(val) { return (val || 0).toLocaleString('id-ID') + ' Bed'; } }
            }
        });
        chartMaintPerPks.render();
    }

    /* ── 5. AREA: Trend Pelaporan Monitoring ── */
    var chartElTrend = document.querySelector('#trendChart');
    var chartTrend = null;
    if (chartElTrend) {
        chartTrend = new ApexCharts(chartElTrend, {
            series: [
                { name: 'Pengaliran LA', data: initialGrafik.trendPengaliran || [] },
                { name: 'Pemeliharaan Bed', data: initialGrafik.trendPemeliharaan || [] }
            ],
            chart: { ...chartDefaults, type: 'area', height: 260 },
            colors: [primary, amber],
            fill: {
                type: 'gradient',
                gradient: {
                    shadeIntensity: 1, opacityFrom: 0.35, opacityTo: 0.05, stops: [0, 100]
                }
            },
            stroke: { curve: 'smooth', width: 2.5 },
            xaxis: {
                categories: initialGrafik.labels || [],
                labels: { style: { colors: labelClr, fontSize: '11px', fontFamily: fontFam } },
                axisBorder: { show: false }, axisTicks: { show: false }
            },
            yaxis: {
                min: 0,
                labels: { style: { colors: labelClr, fontSize: '11px', fontFamily: fontFam } }
            },
            grid: { borderColor: gridColor, strokeDashArray: 4 },
            legend: { position: 'top', horizontalAlign: 'right', fontSize: '11px', fontFamily: fontFam, labels: { colors: labelClr } },
            markers: { size: 3.5, strokeWidth: 0, hover: { size: 5.5 } },
            tooltip: {
                theme: isDark ? 'dark' : 'light',
                style: { fontSize: '12px', fontFamily: fontFam }
            }
        });
        chartTrend.render();
    }

    /* ── AJAX FILTER APPLICATION FUNCTION ── */
    window.applyAdminFilterGrafik = function() {
        var periode = document.getElementById('adminPeriodeVal') ? document.getElementById('adminPeriodeVal').value : 'semua';
        var idPksVal = document.getElementById('adminIdPks') ? document.getElementById('adminIdPks').value : '';
        var tahunVal = document.getElementById('adminTahun') ? document.getElementById('adminTahun').value : '{{ $tahun ?? date('Y') }}';
        var bulanVal = document.getElementById('adminBulan') ? document.getElementById('adminBulan').value : '{{ $bulan ?? date('m') }}';
        var jenisVal = document.getElementById('adminJenis') ? document.getElementById('adminJenis').value : 'flat_bed';
        var nilaiVal = document.getElementById('adminNilai') ? document.getElementById('adminNilai').value : '';
        var tglMulaiVal = document.getElementById('adminTglMulai') ? document.getElementById('adminTglMulai').value : '';
        var tglSelesaiVal = document.getElementById('adminTglSelesai') ? document.getElementById('adminTglSelesai').value : '';

        var params = new URLSearchParams();
        params.append('periode', periode);
        if (idPksVal) params.append('id_pks', idPksVal);
        params.append('jenis', jenisVal);
        if (nilaiVal) params.append('nilai', nilaiVal);

        if (periode === 'custom') {
            if (tglMulaiVal) params.append('tgl_mulai', tglMulaiVal);
            if (tglSelesaiVal) params.append('tgl_selesai', tglSelesaiVal);
        } else if (periode === 'bulanan') {
            params.append('tahun', tahunVal);
        } else if (periode === 'harian' || periode === 'mingguan') {
            params.append('tahun', tahunVal);
            params.append('bulan', bulanVal);
        }

        // Show subtle loading indication
        var btnApply = document.getElementById('btnTerapkanFilterAdmin');
        if (btnApply) {
            btnApply.disabled = true;
            btnApply.innerHTML = '<i class="feather-loader me-1 spin-fast"></i> Memuat...';
        }

        fetch("{{ route('dashboard.grafik-volume') }}?" + params.toString(), {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(function(r) { return r.json(); })
        .then(function(res) {
            var payload = (res && res.data) ? res.data : res;
            if (!payload) return;

            var categories = payload.labels || payload.labelHari || [];
            var seriesDialirkan = payload.volumeDialirkan || payload.volumeGrafik || [];
            var seriesDihasilkan = payload.volumeDihasilkan || [];

            // 1. Update Volume Chart
            if (chartVolume) {
                chartVolume.updateOptions({
                    xaxis: { categories: categories },
                    series: [
                        { name: 'Volume Limbah Dialirkan (m³)', data: seriesDialirkan },
                        { name: 'Volume Limbah Dihasilkan (m³)', data: seriesDihasilkan }
                    ]
                }, true, true);
            }

            // 2. Update Vol per PKS Chart
            if (chartVolPerPks && payload.pksNames) {
                chartVolPerPks.updateOptions({
                    xaxis: { categories: payload.pksNames },
                    series: [{ name: 'Vol. Dialirkan (m³)', data: payload.pksVols || [] }]
                }, true, true);
            }

            // 3. Update Maint per PKS Chart
            if (chartMaintPerPks && payload.pksNames) {
                chartMaintPerPks.updateOptions({
                    xaxis: { categories: payload.pksNames },
                    series: [
                        { name: 'Flat Bed', data: payload.pksFlatBed || [] },
                        { name: 'Long Bed', data: payload.pksLongBed || [] }
                    ]
                }, true, true);
            }

            // 4. Update Trend Activity Chart
            if (chartTrend) {
                chartTrend.updateOptions({
                    xaxis: { categories: categories },
                    series: [
                        { name: 'Pengaliran LA', data: payload.trendPengaliran || [] },
                        { name: 'Pemeliharaan Bed', data: payload.trendPemeliharaan || [] }
                    ]
                }, true, true);
            }

            // 5. Update KPI Summary Badges
            var elTotDialirkan = document.getElementById('metricTotalDialirkan');
            var elTotDihasilkan = document.getElementById('metricTotalDihasilkan');
            var elEfficiency = document.getElementById('metricEfficiency');
            var elTotBed = document.getElementById('metricTotalBed');

            if (elTotDialirkan) elTotDialirkan.textContent = Math.round(payload.totalDialirkan || 0).toLocaleString('id-ID');
            if (elTotDihasilkan) elTotDihasilkan.textContent = Math.round(payload.totalDihasilkan || 0).toLocaleString('id-ID');
            if (elEfficiency) elEfficiency.textContent = payload.efficiency || 0;
            if (elTotBed) {
                var sumBed = (payload.totalFlatBed || 0) + (payload.totalLongBed || 0);
                elTotBed.textContent = Math.round(sumBed).toLocaleString('id-ID');
            }

            // 6. Update Status Badges & Subtitles
            var badgePeriode = document.getElementById('adminFilterLabelPeriode');
            var badgePks = document.getElementById('adminFilterLabelPks');
            var pksSelect = document.getElementById('adminIdPks');
            var isAllPks = !idPksVal || !pksSelect || pksSelect.selectedIndex <= 0;
            var pksText = isAllPks ? 'Semua PKS (12 Unit)' : (pksSelect.options[pksSelect.selectedIndex] ? pksSelect.options[pksSelect.selectedIndex].text : '');

            var bulanOpt = document.getElementById('adminBulan');
            var bulanText = (bulanOpt && bulanOpt.options[bulanOpt.selectedIndex]) ? bulanOpt.options[bulanOpt.selectedIndex].text : '';

            var pLabels = {
                'semua': 'Semua Periode Data',
                'harian': 'Harian: ' + bulanText + ' ' + tahunVal,
                'mingguan': 'Mingguan: ' + bulanText + ' ' + tahunVal,
                'bulanan': 'Bulanan: Tahun ' + tahunVal,
                'tahunan': 'Grafik Per Tahun',
                'custom': 'Rentang: ' + (tglMulaiVal || '') + ' s.d ' + (tglSelesaiVal || '')
            };

            var curPLabel = pLabels[periode] || 'Semua Periode';
            if (badgePeriode) badgePeriode.textContent = curPLabel;
            if (badgePks) badgePks.textContent = pksText;

            var activeBadge = document.getElementById('adminActiveFilterBadge');
            if (activeBadge) {
                activeBadge.innerHTML = '<i class="feather-check-circle me-1"></i> ' + curPLabel + (isAllPks ? '' : ' • ' + pksText);
            }

            var badgePeriodeChart = document.getElementById('mainChartBadgePeriode');
            if (badgePeriodeChart) {
                badgePeriodeChart.textContent = periode.toUpperCase();
            }

            var badgeVolPks = document.getElementById('badgeVolPerPks');
            if (badgeVolPks) {
                badgeVolPks.textContent = isAllPks ? '12 PKS Regional' : pksText;
            }

            var badgeMaintPks = document.getElementById('badgeMaintPerPks');
            if (badgeMaintPks) {
                badgeMaintPks.textContent = isAllPks ? 'Flat & Long Bed' : pksText;
            }

            var elMainSub = document.getElementById('mainChartSub');
            if (elMainSub) {
                elMainSub.textContent = 'Visualisasi debit limbah ' + curPLabel + ' (' + pksText + ')';
            }

            var elPksVolSub = document.getElementById('pksVolChartSub');
            if (elPksVolSub) {
                elPksVolSub.textContent = 'Perbandingan volume dialirkan ' + curPLabel;
            }

            var elPksMaintSub = document.getElementById('pksMaintChartSub');
            if (elPksMaintSub) {
                elPksMaintSub.textContent = 'Pemeliharaan bed periode ' + curPLabel;
            }

            // 7. Repopulate location filter options if matching
            var nilaiSel = document.getElementById('adminNilai');
            if (nilaiSel && payload.pilihan) {
                var curVal = nilaiVal;
                var html = '<option value="">Semua Lokasi</option>';
                (payload.pilihan || []).forEach(function(item) {
                    var sel = (curVal !== null && curVal !== '' && String(item) === String(curVal)) ? 'selected' : '';
                    html += '<option value="' + item + '" ' + sel + '>' + item + '</option>';
                });
                nilaiSel.innerHTML = html;
            }
        })
        .catch(function(err) {
            console.error('Error fetching admin grafik data:', err);
        })
        .finally(function() {
            if (btnApply) {
                btnApply.disabled = false;
                btnApply.innerHTML = '<i class="feather-filter" style="font-size:13px;"></i> Terapkan';
            }
        });
    };

    /* ── PERIODE TAB SWITCHER ── */
    window.setAdminPeriodeFilter = function(periode) {
        var pInput = document.getElementById('adminPeriodeVal');
        if (pInput) pInput.value = periode;

        document.querySelectorAll('#adminPeriodeFilterContainer .btn-periode-tab').forEach(function(btn) {
            if (btn.getAttribute('data-periode') === periode) {
                btn.classList.add('active');
            } else {
                btn.classList.remove('active');
            }
        });

        var yearGroup = document.getElementById('adminTahunGroup');
        var monthGroup = document.getElementById('adminBulanGroup');
        var customStartGroup = document.getElementById('adminCustomStartGroup');
        var customEndGroup = document.getElementById('adminCustomEndGroup');

        if (periode === 'custom') {
            if (yearGroup) yearGroup.classList.add('d-none');
            if (monthGroup) monthGroup.classList.add('d-none');
            if (customStartGroup) customStartGroup.classList.remove('d-none');
            if (customEndGroup) customEndGroup.classList.remove('d-none');
        } else if (periode === 'semua' || periode === 'tahunan') {
            if (yearGroup) yearGroup.classList.add('d-none');
            if (monthGroup) monthGroup.classList.add('d-none');
            if (customStartGroup) customStartGroup.classList.add('d-none');
            if (customEndGroup) customEndGroup.classList.add('d-none');
        } else if (periode === 'bulanan') {
            if (yearGroup) yearGroup.classList.remove('d-none');
            if (monthGroup) monthGroup.classList.add('d-none');
            if (customStartGroup) customStartGroup.classList.add('d-none');
            if (customEndGroup) customEndGroup.classList.add('d-none');
        } else { // harian & mingguan
            if (yearGroup) yearGroup.classList.remove('d-none');
            if (monthGroup) monthGroup.classList.remove('d-none');
            if (customStartGroup) customStartGroup.classList.add('d-none');
            if (customEndGroup) customEndGroup.classList.add('d-none');
        }

        window.applyAdminFilterGrafik();
    };

    /* ── RESET FILTER ── */
    window.resetAdminFilterGrafik = function() {
        var pksSel = document.getElementById('adminIdPks');
        var jenisSel = document.getElementById('adminJenis');
        var nilaiSel = document.getElementById('adminNilai');
        var tahunSel = document.getElementById('adminTahun');
        var bulanSel = document.getElementById('adminBulan');
        var tglMulaiInp = document.getElementById('adminTglMulai');
        var tglSelesaiInp = document.getElementById('adminTglSelesai');

        if (pksSel) pksSel.value = '';
        if (jenisSel) jenisSel.value = 'flat_bed';
        if (nilaiSel) nilaiSel.value = '';
        if (tahunSel) tahunSel.value = '{{ date('Y') }}';
        if (bulanSel) bulanSel.value = '{{ date('m') }}';
        if (tglMulaiInp) tglMulaiInp.value = '{{ date('Y-m-01') }}';
        if (tglSelesaiInp) tglSelesaiInp.value = '{{ date('Y-m-d') }}';

        fetch("{{ route('dashboard.pilihan-filter') }}?jenis=flat_bed&id_pks=", {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        }).then(r => r.json()).then(res => {
            var list = (res && res.data) ? res.data : res;
            if (nilaiSel) {
                var html = '<option value="">Semua Lokasi</option>';
                (list || []).forEach(function(item) {
                    html += '<option value="' + item + '">' + item + '</option>';
                });
                nilaiSel.innerHTML = html;
            }
        });

        window.setAdminPeriodeFilter('semua');
    };

    /* ── LOCATION TYPE CHANGE LISTENER ── */
    var jenisSel = document.getElementById('adminJenis');
    if (jenisSel) {
        jenisSel.addEventListener('change', function() {
            var jenisVal = this.value;
            var idPksVal = document.getElementById('adminIdPks') ? document.getElementById('adminIdPks').value : '';
            var nilaiSel = document.getElementById('adminNilai');
            if (nilaiSel) nilaiSel.value = '';
            fetch("{{ route('dashboard.pilihan-filter') }}?jenis=" + jenisVal + "&id_pks=" + idPksVal, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            }).then(r => r.json()).then(res => {
                var list = (res && res.data) ? res.data : res;
                if (nilaiSel) {
                    var html = '<option value="">Semua Lokasi</option>';
                    (list || []).forEach(function(item) {
                        html += '<option value="' + item + '">' + item + '</option>';
                    });
                    nilaiSel.innerHTML = html;
                }
                window.applyAdminFilterGrafik();
            });
        });
    }

    /* ── PKS SELECT CHANGE LISTENER ── */
    var pksSel = document.getElementById('adminIdPks');
    if (pksSel) {
        pksSel.addEventListener('change', function() {
            var idPksVal = this.value;
            var jenisVal = document.getElementById('adminJenis') ? document.getElementById('adminJenis').value : 'flat_bed';
            var nilaiSel = document.getElementById('adminNilai');
            if (nilaiSel) nilaiSel.value = '';
            fetch("{{ route('dashboard.pilihan-filter') }}?jenis=" + jenisVal + "&id_pks=" + idPksVal, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            }).then(r => r.json()).then(res => {
                var list = (res && res.data) ? res.data : res;
                if (nilaiSel) {
                    var html = '<option value="">Semua Lokasi</option>';
                    (list || []).forEach(function(item) {
                        html += '<option value="' + item + '">' + item + '</option>';
                    });
                    nilaiSel.innerHTML = html;
                }
                window.applyAdminFilterGrafik();
            });
        });
    }

    /* ── AUTO-APPLY ON FILTER INPUTS CHANGE ── */
    ['adminTahun', 'adminBulan', 'adminNilai', 'adminTglMulai', 'adminTglSelesai'].forEach(function(id) {
        var el = document.getElementById(id);
        if (el) {
            el.addEventListener('change', function() {
                window.applyAdminFilterGrafik();
            });
        }
    });

    /* Prevent Default Form Submission */
    var formAdmin = document.getElementById('filterGrafikAdmin');
    if (formAdmin) {
        formAdmin.addEventListener('submit', function(e) {
            e.preventDefault();
            window.applyAdminFilterGrafik();
            return false;
        });
    }

    /* ── Reactive Listener for Theme Changes ── */
    window.addEventListener('simoli:theme-changed', function(e) {
        var d = e.detail;
        var p = d.primary;
        var sec = d.secondary || '#0284c7';
        var acc = d.accent || p;
        var dark = d.isDark;
        var lClr = dark ? '#9ca3af' : '#6b7280';
        var gClr = dark ? 'rgba(255,255,255,0.06)' : 'rgba(0,0,0,0.06)';
        var bg = dark ? '#0a2317' : '#ffffff';

        if (chartStatusDonut) {
            chartStatusDonut.updateOptions({
                colors: [p, amber, '#ef4444'],
                stroke: { colors: [bg] },
                plotOptions: {
                    pie: {
                        donut: {
                            labels: {
                                name: { color: lClr },
                                value: { color: dark ? '#d1fae5' : p },
                                total: { color: lClr }
                            }
                        }
                    }
                },
                legend: { labels: { colors: lClr } }
            });
        }

        if (chartVolume) {
            chartVolume.updateOptions({
                colors: [p, sec],
                grid: { borderColor: gClr },
                xaxis: { labels: { style: { colors: lClr } } },
                yaxis: { labels: { style: { colors: lClr } } },
                legend: { labels: { colors: lClr } },
                tooltip: { theme: dark ? 'dark' : 'light' }
            });
        }

        if (chartTrend) {
            chartTrend.updateOptions({
                colors: [p, amber],
                grid: { borderColor: gClr },
                xaxis: { labels: { style: { colors: lClr } } },
                yaxis: { labels: { style: { colors: lClr } } },
                legend: { labels: { colors: lClr } },
                tooltip: { theme: dark ? 'dark' : 'light' }
            });
        }

        if (chartVolPerPks) {
            chartVolPerPks.updateOptions({
                colors: [p],
                fill: {
                    type: 'gradient',
                    gradient: { shade: 'light', type: 'vertical', shadeIntensity: 0.3, gradientToColors: [acc], stops: [0, 100] }
                },
                grid: { borderColor: gClr },
                xaxis: { labels: { style: { colors: lClr } } },
                yaxis: { labels: { style: { colors: lClr } } },
                tooltip: { theme: dark ? 'dark' : 'light' }
            });
        }

        if (chartMaintPerPks) {
            chartMaintPerPks.updateOptions({
                colors: [amber, p],
                grid: { borderColor: gClr },
                xaxis: { labels: { style: { colors: lClr } } },
                yaxis: { labels: { style: { colors: lClr } } },
                legend: { labels: { colors: lClr } },
                tooltip: { theme: dark ? 'dark' : 'light' }
            });
        }
    });
});
</script>
@endsection