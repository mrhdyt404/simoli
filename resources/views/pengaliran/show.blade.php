@extends('layouts.simoli')

@section('title', 'Detail Pengaliran — ' . $data->pks->akro)
@section('page-title', 'Detail Data Pengaliran')
@section('page-description', 'Rincian data pengaliran limbah Land Aplikasi')

@section('breadcrumb')
    <li>Input Data</li>
    <li class="separator">/</li>
    <li><a href="{{ route('pengaliran.index') }}" style="color:inherit;text-decoration:none;">Pengaliran</a></li>
    <li class="separator">/</li>
    <li>Detail</li>
@endsection

@section('page-actions')
    <div class="d-flex align-items-center gap-2 flex-wrap">
        <a href="{{ route('pengaliran.index') }}" class="btn-ptpn btn-ptpn-outline" style="padding:8px 14px;font-size:13px;">
            <i class="feather-arrow-left" style="font-size:14px;"></i>
            Kembali
        </a>
        @if(Auth::user()->isAdmin() || Auth::user()->id_pks == $data->id_pks)
        <a href="{{ route('pengaliran.edit', $data->id_pengaliran) }}" class="btn-ptpn btn-ptpn-primary" style="padding:8px 14px;font-size:13px;">
            <i class="feather-edit-2" style="font-size:14px;"></i>
            Edit Data
        </a>
        <form action="{{ route('pengaliran.destroy', $data->id_pengaliran) }}" method="POST" class="d-inline form-delete">
            @csrf @method('DELETE')
            <button type="submit" class="btn-ptpn" style="padding:8px 14px;font-size:13px;background:linear-gradient(135deg,#dc2626,#ef4444);color:#fff;border:none;border-radius:12px;cursor:pointer;display:flex;align-items:center;gap:6px;font-weight:700;">
                <i class="feather-trash-2" style="font-size:14px;"></i>
                Hapus
            </button>
        </form>
        @endif
    </div>
@endsection

@section('styles')
<style>
    @keyframes fadeUpCard {
        from { opacity:0; transform:translateY(12px); }
        to   { opacity:1; transform:translateY(0); }
    }

    /* === HERO SUMMARY CARD (STUNNING EXECUTIVE DESIGN) === */
    .detail-hero {
        border-radius: 24px;
        background: linear-gradient(135deg, #022c14 0%, #064e3b 45%, #047857 100%);
        border: 1px solid rgba(52,211,153,.3);
        box-shadow: 0 16px 40px rgba(2,44,20,.25), 0 0 0 1px rgba(255,255,255,.05) inset;
        padding: clamp(22px, 3vw + 14px, 36px);
        color: #ffffff;
        margin-bottom: 28px;
        position: relative;
        overflow: hidden;
        animation: fadeUpCard .4s cubic-bezier(0.16, 1, 0.3, 1);
    }

    .detail-hero::before {
        content: '';
        position: absolute;
        top: -100px;
        right: -100px;
        width: 320px;
        height: 320px;
        border-radius: 50%;
        background: radial-gradient(circle, rgba(52,211,153,.22) 0%, rgba(16,185,129,0) 70%);
        pointer-events: none;
    }

    .detail-hero::after {
        content: '';
        position: absolute;
        bottom: -80px;
        left: -80px;
        width: 240px;
        height: 240px;
        border-radius: 50%;
        background: radial-gradient(circle, rgba(16,185,129,.12) 0%, transparent 70%);
        pointer-events: none;
    }

    /* Badges */
    .detail-hero-pks-badge {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 6px 16px;
        border-radius: 50px;
        background: rgba(255,255,255,.14);
        border: 1px solid rgba(255,255,255,.22);
        backdrop-filter: blur(12px);
        font-size: 13px;
        font-weight: 800;
        color: #ffffff;
    }

    .hero-akro-pill {
        color: #86efac;
        font-weight: 800;
    }

    .detail-hero-code-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 14px;
        border-radius: 50px;
        background: rgba(0,0,0,.25);
        border: 1px solid rgba(255,255,255,.15);
        backdrop-filter: blur(12px);
        font-size: 12.5px;
        font-weight: 600;
        color: rgba(209,250,229,.9);
        font-family: monospace;
    }

    /* Date */
    .detail-hero-date {
        font-family: 'Outfit', sans-serif;
        font-size: clamp(22px, 2.5vw + 10px, 32px);
        font-weight: 900;
        color: #ffffff;
        letter-spacing: -0.5px;
        line-height: 1.15;
        margin-top: 4px;
        margin-bottom: 14px;
        text-shadow: 0 2px 10px rgba(0,0,0,.2);
    }

    /* Meta Pills */
    .detail-hero-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        margin-bottom: 22px;
    }

    .detail-hero-pill {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 6px 14px;
        border-radius: 50px;
        background: rgba(255,255,255,.1);
        border: 1px solid rgba(255,255,255,.16);
        backdrop-filter: blur(10px);
        font-size: 12.5px;
        font-weight: 600;
        color: rgba(236,253,245,.95);
    }
    .detail-hero-pill i {
        color: #4ade80;
        font-size: 14px;
    }

    /* Quick stats grid */
    .hero-stats-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 12px;
        margin-top: 6px;
    }

    .hero-stat-tile {
        background: rgba(0,0,0,.2);
        border: 1px solid rgba(255,255,255,.14);
        border-radius: 16px;
        padding: 12px 14px;
        backdrop-filter: blur(12px);
        display: flex;
        align-items: center;
        gap: 12px;
        transition: all .25s ease;
    }

    .hero-stat-tile:hover {
        background: rgba(255,255,255,.12);
        border-color: rgba(52,211,153,.4);
        transform: translateY(-2px);
    }

    .hero-stat-icon {
        width: 38px;
        height: 38px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
        flex-shrink: 0;
    }

    .hero-stat-val {
        font-family: 'Outfit', sans-serif;
        font-size: clamp(17px, 1.8vw + 8px, 22px);
        font-weight: 900;
        line-height: 1.1;
        margin-bottom: 2px;
    }

    .hero-stat-val .unit {
        font-size: 11px;
        font-weight: 600;
        opacity: .8;
        margin-left: 2px;
    }

    .hero-stat-lbl {
        font-size: 10px;
        font-weight: 700;
        color: rgba(209,250,229,.75);
        text-transform: uppercase;
        letter-spacing: .5px;
    }

    /* Efficiency / Status card on right side of hero */
    .hero-efficiency-card {
        background: rgba(0, 0, 0, 0.22);
        border: 1px solid rgba(255, 255, 255, 0.16);
        border-radius: 20px;
        padding: 22px 20px;
        text-align: center;
        backdrop-filter: blur(14px);
        box-shadow: 0 8px 24px rgba(0,0,0,.12);
        height: 100%;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }

    .hero-eff-header {
        font-size: 11px;
        font-weight: 800;
        color: rgba(187,247,208,.85);
        text-transform: uppercase;
        letter-spacing: .8px;
        margin-bottom: 8px;
    }

    .efficiency-display {
        font-family: 'Outfit', sans-serif;
        font-size: clamp(32px, 4vw + 16px, 46px);
        font-weight: 900;
        color: #4ade80;
        line-height: 1;
        text-shadow: 0 4px 16px rgba(74,222,128,.3);
    }

    .hero-eff-bar-wrap {
        margin: 14px auto;
        height: 8px;
        border-radius: 8px;
        background: rgba(255,255,255,.14);
        max-width: 220px;
        width: 100%;
        overflow: hidden;
    }

    .hero-eff-bar {
        height: 100%;
        border-radius: 8px;
        background: linear-gradient(90deg, #34d399 0%, #a7f3d0 100%);
        box-shadow: 0 0 10px rgba(52,211,153,.5);
        transition: width 1s ease;
    }

    .hero-eff-sub {
        font-size: 11.5px;
        color: rgba(209,250,229,.75);
        font-weight: 500;
    }

    .hero-eff-status {
        margin-top: 14px;
    }

    .status-active {
        display: inline-flex;
        align-items: center;
        padding: 7px 16px;
        border-radius: 50px;
        background: rgba(52,211,153,.18);
        border: 1px solid rgba(52,211,153,.35);
        color: #86efac;
        font-size: 12.5px;
        font-weight: 800;
    }

    .status-empty {
        display: inline-flex;
        align-items: center;
        padding: 7px 16px;
        border-radius: 50px;
        background: rgba(239,68,68,.18);
        border: 1px solid rgba(239,68,68,.35);
        color: #fca5a5;
        font-size: 12.5px;
        font-weight: 800;
    }

    /* === DETAIL CARDS === */
    .detail-card {
        background: #ffffff;
        border-radius: 18px;
        border: 1px solid rgba(22,163,74,.1);
        box-shadow: 0 2px 12px rgba(22,163,74,.06);
        overflow: hidden;
        animation: fadeUpCard .4s ease-out;
    }

    .detail-card-header {
        padding: 14px 20px;
        border-bottom: 1px solid rgba(22,163,74,.08);
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .detail-card-title {
        font-family: 'Outfit', sans-serif;
        font-size: 13.5px;
        font-weight: 800;
        color: #14532d;
        margin: 0;
    }

    .detail-card-body { padding: 20px; }

    .detail-item {
        padding: 11px 0;
        border-bottom: 1px solid rgba(22,163,74,.07);
    }
    .detail-item:last-child { border-bottom: none; }

    .detail-label {
        font-size: 10.5px;
        font-weight: 700;
        color: #6b7280;
        text-transform: uppercase;
        letter-spacing: .4px;
        margin-bottom: 4px;
        display: block;
    }

    .detail-value {
        font-size: 14.5px;
        font-weight: 600;
        color: #1f2937;
    }

    .keterangan-box {
        padding: 16px;
        border-left: 4px solid #16a34a;
        border-radius: 12px;
        background: #f0fdf4;
        font-size: 13.5px;
        line-height: 1.6;
        color: #14532d;
        font-weight: 500;
    }    /* Dark mode */
    html.app-skin-dark .detail-card { background:#0a2317 !important;border-color:rgba(34,197,94,.15) !important; }
    html.app-skin-dark .detail-card-title { color:#d1fae5 !important; }
    html.app-skin-dark .detail-item { border-color:rgba(34,197,94,.07) !important; }
    html.app-skin-dark .detail-label { color:#6b8f72 !important; }
    html.app-skin-dark .detail-value { color:#d1fae5 !important; }
    html.app-skin-dark .keterangan-box { background:#0e3b26;border-color:#16a34a;color:#bbf7d0; }
    html.app-skin-dark .pks-subbox { background:#0e3b26 !important; border-color:rgba(34,197,94,.2) !important; }
    html.app-skin-dark .pks-subval { color:#d1fae5 !important; }

    /* Responsive */
    @media (max-width: 991.98px) {
        .hero-stats-grid { grid-template-columns: repeat(2,1fr); gap:10px; }
    }
</style>
@endsection

@section('content')
@php
    $efisiensi = $data->vol_limbah_dihasilkan > 0 ? round(($data->vol_limbah_dialirkan / $data->vol_limbah_dihasilkan) * 100) : 0;
@endphp

<article class="pengaliran-detail">

    {{-- ================================================================
         1. HERO CARD — Summary visual
         ================================================================ --}}
    <section class="detail-hero" aria-label="Ringkasan Data Pengaliran">
        <div class="row align-items-center g-4">
            <div class="col-lg-7">
                {{-- PKS Badges --}}
                <div class="d-flex align-items-center gap-2 flex-wrap mb-3">
                    <div class="detail-hero-pks-badge">
                        <i class="feather-home" style="font-size:14px;color:#4ade80;"></i>
                        <span>PKS {{ $data->pks->nama ?? '' }}</span>
                        @if(isset($data->pks->akro) && $data->pks->akro)
                            <span class="hero-akro-pill">({{ $data->pks->akro }})</span>
                        @endif
                    </div>
                    @if(isset($data->pks->kode) && $data->pks->kode)
                    <div class="detail-hero-code-badge">
                        <i class="feather-hash" style="font-size:13px;color:#86efac;"></i>
                        <span>Kode: <strong>{{ $data->pks->kode }}</strong></span>
                    </div>
                    @endif
                </div>

                {{-- Date --}}
                <h1 class="detail-hero-date">
                    {{ \Carbon\Carbon::parse($data->tanggal)->locale('id')->translatedFormat('l, d F Y') }}
                </h1>

                {{-- Meta Pills --}}
                <div class="detail-hero-meta">
                    <div class="detail-hero-pill">
                        <i class="feather-clock"></i>
                        <span>{{ $data->jam_mulai ? substr($data->jam_mulai,0,5) : '-' }} — {{ $data->jam_selesai ? substr($data->jam_selesai,0,5) : '-' }}</span>
                    </div>
                    <div class="detail-hero-pill">
                        <i class="feather-map-pin"></i>
                        <span>Blok <strong>{{ $data->blok ?? '-' }}</strong> | Bak <strong>{{ $data->no_bak ?? '-' }}</strong></span>
                    </div>
                    @if($data->rotasi)
                    <div class="detail-hero-pill">
                        <i class="feather-rotate-cw"></i>
                        <span>Rotasi: <strong>{{ $data->rotasi }}</strong></span>
                    </div>
                    @endif
                    @if(isset($data->pks->manager) && $data->pks->manager)
                    <div class="detail-hero-pill">
                        <i class="feather-user"></i>
                        <span>Manager: <strong>{{ $data->pks->manager }}</strong></span>
                    </div>
                    @endif
                </div>

                {{-- Stats Grid (4 columns) --}}
                <div class="hero-stats-grid">
                    <div class="hero-stat-tile">
                        <div class="hero-stat-icon" style="color:#60a5fa;background:rgba(96,165,250,.15);"><i class="feather-arrow-down-circle"></i></div>
                        <div>
                            <div class="hero-stat-val" style="color:#93c5fd;">{{ number_format($data->vol_limbah_dihasilkan) }} <span class="unit">m³</span></div>
                            <div class="hero-stat-lbl">Vol. Dihasilkan</div>
                        </div>
                    </div>
                    <div class="hero-stat-tile">
                        <div class="hero-stat-icon" style="color:#34d399;background:rgba(52,211,153,.15);"><i class="feather-droplet"></i></div>
                        <div>
                            <div class="hero-stat-val" style="color:#6ee7b7;">{{ number_format($data->vol_limbah_dialirkan) }} <span class="unit">m³</span></div>
                            <div class="hero-stat-lbl">Vol. Dialirkan</div>
                        </div>
                    </div>
                    <div class="hero-stat-tile">
                        <div class="hero-stat-icon" style="color:#4ade80;background:rgba(74,222,128,.15);"><i class="feather-layers"></i></div>
                        <div>
                            <div class="hero-stat-val" style="color:#86efac;">{{ number_format($data->flat_bed ?? 0) }} <span class="unit">Bed</span></div>
                            <div class="hero-stat-lbl">Bed Dialirkan</div>
                        </div>
                    </div>
                    <div class="hero-stat-tile">
                        <div class="hero-stat-icon" style="color:#fbbf24;background:rgba(251,191,36,.15);"><i class="feather-maximize-2"></i></div>
                        <div>
                            <div class="hero-stat-val" style="color:#fde68a;">{{ number_format($data->luas_area ?? 0, 1) }} <span class="unit">Ha</span></div>
                            <div class="hero-stat-lbl">Luas Area</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Right Column: Efficiency Panel --}}
            <div class="col-lg-5">
                <div class="hero-efficiency-card">
                    <div class="hero-eff-header">
                        <i class="feather-activity me-1" style="color:#4ade80;"></i>
                        Efisiensi Pengaliran
                    </div>
                    <div class="efficiency-display">{{ $efisiensi }}%</div>
                    <div class="hero-eff-bar-wrap">
                        <div class="hero-eff-bar" style="width: {{ min(100, $efisiensi) }}%;"></div>
                    </div>
                    <div class="hero-eff-sub">
                        Ratio: Volume Dialirkan / Volume Dihasilkan
                    </div>
                    <div class="hero-eff-status">
                        @if($data->vol_limbah_dialirkan > 0)
                            <span class="status-active">
                                <i class="feather-check-circle me-1.5"></i> Pengaliran Aktif &amp; Berjalan
                            </span>
                        @else
                            <span class="status-empty">
                                <i class="feather-alert-circle me-1.5"></i> Belum Ada Volume
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ================================================================
         2. DETAIL CARDS
         ================================================================ --}}
    <div class="row g-4">
        <div class="col-lg-8">

            {{-- Informasi Umum --}}
            <div class="detail-card mb-4">
                <div class="detail-card-header">
                    <i class="feather-info" style="color:#16a34a;font-size:17px;"></i>
                    <h3 class="detail-card-title">Informasi Umum</h3>
                </div>
                <div class="detail-card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="detail-item">
                                <span class="detail-label">Tanggal Pengaliran</span>
                                <div class="detail-value">{{ \Carbon\Carbon::parse($data->tanggal)->locale('id')->translatedFormat('l, d F Y') }}</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="detail-item">
                                <span class="detail-label">Jam Operasional</span>
                                <div class="detail-value">
                                    {{ $data->jam_mulai ? substr($data->jam_mulai,0,5) : '-' }} —
                                    {{ $data->jam_selesai ? substr($data->jam_selesai,0,5) : '-' }}
                                    @php
                                        try { $durasi = \Carbon\Carbon::parse($data->jam_mulai)->diff(\Carbon\Carbon::parse($data->jam_selesai)); }
                                        catch(\Exception $e) { $durasi = null; }
                                    @endphp
                                    @if($durasi)
                                    <span style="margin-left:8px;font-size:11.5px;color:#6b7280;">
                                        ({{ $durasi->format('%H jam %I menit') }})
                                    </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="detail-item">
                                <span class="detail-label">Unit PKS</span>
                                <div class="detail-value d-flex align-items-center gap-2 flex-wrap">
                                    <span class="pks-badge" style="display:inline-flex;align-items:center;gap:5px;padding:4px 10px;border-radius:8px;font-size:11.5px;font-weight:800;background:linear-gradient(135deg,#052e16,#166534);color:#86efac;">
                                        <i class="feather-home" style="font-size:12px;"></i>
                                        {{ $data->pks->akro ?? 'N/A' }}
                                    </span>
                                    <span style="font-size:14px;font-weight:700;color:#1f2937;">{{ $data->pks->nama ?? '' }}</span>
                                    @if(isset($data->pks->kode) && $data->pks->kode)
                                        <span class="badge" style="background:#f3f4f6;color:#4b5563;border:1px solid #e5e7eb;font-family:monospace;font-size:11px;font-weight:700;">
                                            {{ $data->pks->kode }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="detail-item">
                                <span class="detail-label">ID Pengaliran</span>
                                <div class="detail-value" style="font-family:monospace;color:#6b7280;">#{{ $data->id_pengaliran }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Lokasi Pengaliran --}}
            <div class="detail-card mb-4">
                <div class="detail-card-header">
                    <i class="feather-map-pin" style="color:#16a34a;font-size:17px;"></i>
                    <h3 class="detail-card-title">Lokasi Pengaliran Land Aplikasi</h3>
                </div>
                <div class="detail-card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="detail-item">
                                <span class="detail-label">Block Pengaliran</span>
                                <div class="detail-value" style="font-size:16px;font-weight:800;color:#14532d;">{{ $data->blok ?? '—' }}</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="detail-item">
                                <span class="detail-label">Bak Distribusi</span>
                                <div class="detail-value" style="font-weight:800;color:#1d4ed8;">{{ $data->no_bak ?? '—' }}</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="detail-item">
                                <span class="detail-label">Rotasi Pengaliran</span>
                                <div class="detail-value">{{ $data->rotasi ?? '—' }}</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="detail-item">
                                <span class="detail-label">Luas Area</span>
                                <div class="detail-value">{{ number_format($data->luas_area ?? 0, 1) }} Ha</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Data Bed & Volume --}}
            <div class="detail-card mb-4">
                <div class="detail-card-header">
                    <i class="feather-droplet" style="color:#16a34a;font-size:17px;"></i>
                    <h3 class="detail-card-title">Data Bed &amp; Volume Limbah</h3>
                </div>
                <div class="detail-card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="detail-item">
                                <span class="detail-label">Volume Dihasilkan</span>
                                <div class="detail-value" style="font-size:18px;font-weight:800;color:#0d9488;">
                                    {{ number_format($data->vol_limbah_dihasilkan) }} <span style="font-size:12px;font-weight:600;color:#6b7280;">m³</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="detail-item">
                                <span class="detail-label">Volume Dialirkan</span>
                                <div class="detail-value" style="font-family:'Outfit',sans-serif;font-size:22px;font-weight:900;color:#059669;">
                                    {{ number_format($data->vol_limbah_dialirkan) }} <span style="font-size:13px;font-weight:600;color:#6b7280;">m³</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="detail-item">
                                <span class="detail-label">Bed di alirkan</span>
                                <div class="detail-value" style="font-family:'Outfit',sans-serif;font-size:22px;font-weight:900;color:#16a34a;">
                                    {{ number_format($data->flat_bed) }} <span style="font-size:13px;font-weight:600;color:#6b7280;">Bed</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="detail-item">
                                <span class="detail-label">Efisiensi Pengaliran</span>
                                <div class="detail-value" style="font-size:18px;font-weight:800;color:{{ $efisiensi >= 80 ? '#16a34a' : ($efisiensi >= 50 ? '#d97706' : '#dc2626') }};">
                                    {{ $efisiensi }}%
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Keterangan --}}
            @if($data->keterangan)
            <div class="detail-card">
                <div class="detail-card-header">
                    <i class="feather-message-square" style="color:#16a34a;font-size:17px;"></i>
                    <h3 class="detail-card-title">Keterangan</h3>
                </div>
                <div class="detail-card-body">
                    <div class="keterangan-box">{{ $data->keterangan }}</div>
                </div>
            </div>
            @endif
        </div>

        {{-- RIGHT: Unit PKS + Waktu + Foto --}}
        <div class="col-lg-4">

            {{-- Card Information Unit PKS --}}
            <div class="detail-card mb-4">
                <div class="detail-card-header" style="background: linear-gradient(135deg, #052e16 0%, #14532d 100%); padding: 14px 18px;">
                    <div class="d-flex align-items-center gap-2.5">
                        <div style="width:38px;height:38px;border-radius:10px;background:rgba(255,255,255,.12);border:1px solid rgba(255,255,255,.2);display:flex;align-items:center;justify-content:center;color:#86efac;flex-shrink:0;">
                            <i class="feather-home" style="font-size:18px;"></i>
                        </div>
                        <div>
                            <h3 style="font-family:'Outfit',sans-serif;font-size:14.5px;font-weight:800;color:#ffffff;margin:0;line-height:1.2;">
                                PKS {{ $data->pks->nama ?? 'Unit PKS' }}
                            </h3>
                            <span style="font-size:10.5px;font-weight:700;color:#86efac;letter-spacing:.4px;text-transform:uppercase;">
                                Pabrik Kelapa Sawit (PKS)
                            </span>
                        </div>
                    </div>
                </div>
                <div class="detail-card-body" style="padding:16px;">
                    <div class="row g-2.5">
                        <div class="col-6">
                            <div class="pks-subbox">
                                <span class="detail-label" style="margin-bottom:2px;font-size:10px;"><i class="feather-hash me-1"></i>Kode Unit</span>
                                <div class="detail-value pks-subval" style="font-family:monospace;font-weight:800;color:#166534;font-size:12.5px;">
                                    {{ $data->pks->kode ?? '—' }}
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="pks-subbox">
                                <span class="detail-label" style="margin-bottom:2px;font-size:10px;"><i class="feather-tag me-1"></i>Akronim</span>
                                <div>
                                    <span class="badge" style="background:#dcfce7;color:#15803d;border:1px solid #86efac;font-weight:800;font-size:11px;padding:3px 8px;border-radius:6px;">
                                        {{ $data->pks->akro ?? '—' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="pks-subbox" style="background:#f0fdf4;border:1px solid #bbf7d0;">
                                <span class="detail-label" style="margin-bottom:2px;font-size:10px;color:#15803d;"><i class="feather-user me-1"></i>Manager / PJ Unit</span>
                                <div class="pks-subval" style="font-size:13.5px;font-weight:800;color:#14532d;">
                                    {{ $data->pks->manager ?? 'Belum ditentukan' }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Waktu Ringkas --}}
            <div class="detail-card mb-4">
                <div class="detail-card-header">
                    <i class="feather-calendar" style="color:#16a34a;font-size:17px;"></i>
                    <h3 class="detail-card-title">Informasi Waktu</h3>
                </div>
                <div class="detail-card-body">
                    <div class="detail-item">
                        <span class="detail-label">Hari Pengaliran</span>
                        <div class="detail-value">{{ \Carbon\Carbon::parse($data->tanggal)->locale('id')->translatedFormat('l') }}</div>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Tanggal</span>
                        <div class="detail-value">{{ \Carbon\Carbon::parse($data->tanggal)->format('d-m-Y') }}</div>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Jam Mulai</span>
                        <div class="detail-value">{{ $data->jam_mulai ? substr($data->jam_mulai,0,5) : '—' }}</div>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Jam Selesai</span>
                        <div class="detail-value">{{ $data->jam_selesai ? substr($data->jam_selesai,0,5) : '—' }}</div>
                    </div>
                    @if(isset($durasi) && $durasi)
                    <div class="detail-item">
                        <span class="detail-label">Durasi</span>
                        <div class="detail-value">{{ $durasi->format('%H jam %I menit') }}</div>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Foto Dokumentasi --}}
            @if($data->foto && file_exists(public_path('gallery/' . $data->foto)))
            <div class="detail-card mb-4">
                <div class="detail-card-header">
                    <i class="feather-camera" style="color:#16a34a;font-size:17px;"></i>
                    <h3 class="detail-card-title">Foto Dokumentasi</h3>
                </div>
                <div class="detail-card-body" style="padding:12px;">
                    <img src="{{ asset('gallery/' . $data->foto) }}" alt="Foto Dokumentasi"
                         style="width:100%;border-radius:12px;object-fit:cover;max-height:240px;">
                </div>
            </div>
            @endif

        </div>
    </div>

</article>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.12/dist/sweetalert2.all.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.form-delete').forEach(function(form) {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                Swal.fire({
                    title: 'Konfirmasi Hapus Data',
                    text: 'Yakin ingin menghapus data pengaliran ini? Aksi ini tidak dapat dibatalkan.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc2626',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: 'Ya, Hapus',
                    cancelButtonText: 'Batal',
                    borderRadius: '16px'
                }).then((result) => {
                    if (result.isConfirmed) { form.submit(); }
                });
            });
        });
    });
</script>
@endsection