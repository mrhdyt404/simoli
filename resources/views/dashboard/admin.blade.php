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
        background: radial-gradient(circle, var(--theme-glow, rgba(212, 160, 23, 0.12)) 0%, transparent 70%);
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

    /* === DARK MODE === */
    html.app-skin-dark .kpi-card,
    html.app-skin-dark .chart-card,
    html.app-skin-dark .compliance-table-wrap,
    html.app-skin-dark .report-tile {
        background: #0a2317 !important;
        border-color: rgba(34, 197, 94, 0.15) !important;
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
         3. ANALYTICS CHARTS
         ================================================================ --}}
    <section aria-label="Analytics Charts" class="mb-4">
        <div class="section-header">
            <h3 class="section-title">
                <span class="section-title-dot"></span>
                Analitik & Visualisasi Data
            </h3>
            <span class="mod-pill mod-pill-ok" style="font-size:11px;">Harian & Bulanan</span>
        </div>

        <div class="row g-4 mb-4">
            {{-- Trend Chart --}}
            <div class="col-lg-8">
                <div class="chart-card">
                    <div class="chart-card-header">
                        <div>
                            <div class="chart-title">
                                <i class="feather-trending-up" style="color:#16a34a;margin-right:6px;"></i>
                                Trend Monitoring 7 Hari Terakhir
                            </div>
                            <div class="chart-sub">Jumlah PKS yang menginput laporan harian</div>
                        </div>
                        <span class="mod-pill mod-pill-ok" style="font-size:10px;">Harian</span>
                    </div>
                    <div style="padding:16px;">
                        <div id="trendChart" style="min-height:280px;"></div>
                    </div>
                </div>
            </div>

            {{-- Donut Chart --}}
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
                    <div style="padding:16px;">
                        <div id="statusDonutChart" style="min-height:230px;"></div>
                        <div class="row g-2 text-center pt-2" style="border-top:1px solid rgba(22,163,74,0.1);">
                            <div class="col-4">
                                <div style="font-size:18px;font-weight:900;color:#16a34a;">{{ $sudahLengkap }}</div>
                                <small style="font-size:10px;color:#6b7280;font-weight:700;">Lengkap</small>
                            </div>
                            <div class="col-4">
                                <div style="font-size:18px;font-weight:900;color:#d97706;">{{ $sebagian }}</div>
                                <small style="font-size:10px;color:#6b7280;font-weight:700;">Sebagian</small>
                            </div>
                            <div class="col-4">
                                <div style="font-size:18px;font-weight:900;color:#ef4444;">{{ $belumAda }}</div>
                                <small style="font-size:10px;color:#6b7280;font-weight:700;">Belum Ada</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Volume Per PKS + Pemeliharaan Per PKS --}}
        <div class="row g-4">
            <div class="col-lg-6">
                <div class="chart-card">
                    <div class="chart-card-header">
                        <div>
                            <div class="chart-title">
                                <i class="feather-bar-chart-2" style="color:#16a34a;margin-right:6px;"></i>
                                Volume Limbah Dialirkan per PKS
                            </div>
                            <div class="chart-sub">Akumulasi bulan {{ $bulanLabel }} (m³)</div>
                        </div>
                    </div>
                    <div style="padding:16px;">
                        <div id="volPerPksChart" style="min-height:280px;"></div>
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
                            <div class="chart-sub">Flat Bed &amp; Long Bed dikerjakan</div>
                        </div>
                    </div>
                    <div style="padding:16px;">
                        <div id="maintPerPksChart" style="min-height:280px;"></div>
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
    var secondary = (curTheme && curTheme.themeData) ? curTheme.themeData.secondary : '#d97706';

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

    /* ── 1. DONUT: Kelengkapan Hari Ini ── */
    var chartStatusDonut = new ApexCharts(document.querySelector('#statusDonutChart'), {
        series: [{{ $sudahLengkap }}, {{ $sebagian }}, {{ $belumAda }}],
        chart: { ...chartDefaults, type: 'donut', height: 230 },
        labels: ['Lengkap', 'Sebagian', 'Belum Input'],
        colors: [primary, '#d97706', '#ef4444'],
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

    /* ── 2. AREA: Trend 7 Hari ── */
    var trendDays   = {!! json_encode(array_column((array)($trendData ?? []), 'label')) !!};
    var trendPengal = {!! json_encode(array_column((array)($trendData ?? []), 'pengaliran')) !!};
    var trendPemeli = {!! json_encode(array_column((array)($trendData ?? []), 'pemeliharaan')) !!};

    var chartTrend = new ApexCharts(document.querySelector('#trendChart'), {
        series: [
            { name: 'Pengaliran LA', data: trendPengal },
            { name: 'Pemeliharaan',  data: trendPemeli }
        ],
        chart: { ...chartDefaults, type: 'area', height: 280 },
        colors: [primary, secondary],
        fill: {
            type: 'gradient',
            gradient: {
                shadeIntensity: 1, opacityFrom: 0.35, opacityTo: 0.05, stops: [0, 100]
            }
        },
        stroke: { curve: 'smooth', width: 2.5 },
        xaxis: {
            categories: trendDays,
            labels: { style: { colors: labelClr, fontSize: '11px', fontFamily: fontFam } },
            axisBorder: { show: false }, axisTicks: { show: false }
        },
        yaxis: {
            min: 0, max: {{ $totalPks > 0 ? $totalPks : 5 }},
            tickAmount: {{ $totalPks > 0 ? $totalPks : 5 }},
            labels: { style: { colors: labelClr, fontSize: '11px', fontFamily: fontFam } }
        },
        grid: { borderColor: gridColor, strokeDashArray: 4 },
        legend: { position: 'top', horizontalAlign: 'right', fontSize: '11px', fontFamily: fontFam, labels: { colors: labelClr } },
        markers: { size: 4, strokeWidth: 0, hover: { size: 6 } },
        tooltip: {
            theme: isDark ? 'dark' : 'light',
            style: { fontSize: '12px', fontFamily: fontFam }
        }
    });
    chartTrend.render();

    /* ── 3. COLUMN: Volume per PKS ── */
    var pksNames = {!! json_encode(array_column((array)($statsPerPks ?? []), 'akro')) !!};
    var pksVols  = {!! json_encode(array_column((array)($statsPerPks ?? []), 'vol_dialirkan')) !!};

    var chartVolPerPks = new ApexCharts(document.querySelector('#volPerPksChart'), {
        series: [{ name: 'Vol. Dialirkan (m³)', data: pksVols }],
        chart: { ...chartDefaults, type: 'bar', height: 280 },
        colors: [primary],
        fill: {
            type: 'gradient',
            gradient: { shade: 'light', type: 'vertical', shadeIntensity: 0.3, gradientToColors: [accent], stops: [0, 100] }
        },
        plotOptions: { bar: { borderRadius: 6, columnWidth: '55%' } },
        xaxis: {
            categories: pksNames,
            labels: { style: { colors: labelClr, fontSize: '10px', fontFamily: fontFam } },
            axisBorder: { show: false }
        },
        yaxis: { labels: { style: { colors: labelClr, fontSize: '11px', fontFamily: fontFam } } },
        grid: { borderColor: gridColor, strokeDashArray: 4 },
        dataLabels: { enabled: false },
        tooltip: { theme: isDark ? 'dark' : 'light', style: { fontSize: '11px', fontFamily: fontFam } }
    });
    chartVolPerPks.render();

    /* ── 4. STACKED: Pemeliharaan per PKS ── */
    var maintFlat = {!! json_encode(array_column((array)($statsPerPks ?? []), 'flat_bed_m')) !!};
    var maintLong = {!! json_encode(array_column((array)($statsPerPks ?? []), 'long_bed')) !!};
    var maintPks  = {!! json_encode(array_column((array)($statsPerPks ?? []), 'akro')) !!};

    var chartMaintPerPks = new ApexCharts(document.querySelector('#maintPerPksChart'), {
        series: [
            { name: 'Flat Bed', data: maintFlat },
            { name: 'Long Bed', data: maintLong }
        ],
        chart: { ...chartDefaults, type: 'bar', height: 280, stacked: true },
        colors: [secondary, primary],
        plotOptions: { bar: { borderRadius: 4, columnWidth: '55%' } },
        xaxis: {
            categories: maintPks,
            labels: { style: { colors: labelClr, fontSize: '10px', fontFamily: fontFam } },
            axisBorder: { show: false }
        },
        yaxis: { labels: { style: { colors: labelClr, fontSize: '11px', fontFamily: fontFam } } },
        grid: { borderColor: gridColor, strokeDashArray: 4 },
        dataLabels: { enabled: false },
        legend: { position: 'top', horizontalAlign: 'right', fontSize: '11px', fontFamily: fontFam, labels: { colors: labelClr } },
        tooltip: { theme: isDark ? 'dark' : 'light', style: { fontSize: '11px', fontFamily: fontFam } }
    });
    chartMaintPerPks.render();

    /* ── Reactive Listener for Theme Changes ── */
    window.addEventListener('simoli:theme-changed', function(e) {
        var d = e.detail;
        var p = d.primary;
        var sec = d.secondary || '#d97706';
        var acc = d.accent || p;
        var dark = d.isDark;
        var lClr = dark ? '#9ca3af' : '#6b7280';
        var gClr = dark ? 'rgba(255,255,255,0.06)' : 'rgba(0,0,0,0.06)';
        var bg = dark ? '#0a2317' : '#ffffff';

        if (chartStatusDonut) {
            chartStatusDonut.updateOptions({
                colors: [p, sec, '#ef4444'],
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

        if (chartTrend) {
            chartTrend.updateOptions({
                colors: [p, sec],
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
                colors: [sec, p],
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