@extends('layouts.simoli')

@section('title', 'Detail Pemeliharaan — ' . ($data->pks->AKRO ?? 'PKS'))
@section('page-title', 'Detail Data Pemeliharaan')
@section('page-description', 'Rincian data pemeliharaan kolam & bed Land Aplikasi')

@section('breadcrumb')
    <li>Input Data</li>
    <li class="separator">/</li>
    <li><a href="{{ route('pemeliharaan.index') }}" style="color:inherit;text-decoration:none;">Pemeliharaan</a></li>
    <li class="separator">/</li>
    <li>Detail</li>
@endsection

@section('page-actions')
    <div class="d-flex align-items-center gap-2 flex-wrap">
        <a href="{{ route('pemeliharaan.index') }}" class="btn-ptpn btn-ptpn-outline" style="padding:8px 14px;font-size:13px;">
            <i class="feather-arrow-left" style="font-size:14px;"></i>
            Kembali
        </a>
        <a href="{{ route('pemeliharaan.edit', $data) }}" class="btn-ptpn btn-ptpn-primary" style="padding:8px 14px;font-size:13px;">
            <i class="feather-edit-2" style="font-size:14px;"></i>
            Edit Data
        </a>
        @if(Auth::user()->isAdmin())
        <form action="{{ route('pemeliharaan.destroy', $data) }}" method="POST" class="d-inline form-delete">
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
    /* ================================================================
       PEMELIHARAAN SHOW / DETAIL — PTPN GREEN THEME
       ================================================================ */
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
        font-size: clamp(26px, 3vw + 10px, 36px);
        font-weight: 900;
        color: #4ade80;
        line-height: 1;
        text-shadow: 0 4px 16px rgba(74,222,128,.3);
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
    }

    /* Photo Cards */
    .photo-display-card {
        border-radius: 14px;
        overflow: hidden;
        border: 1px solid rgba(22,163,74,.15);
        background: #f9fafb;
    }

    .photo-display-img {
        width: 100%;
        height: 220px;
        object-fit: cover;
        cursor: pointer;
        transition: transform .3s ease;
    }
    .photo-display-img:hover { transform: scale(1.03); }

    /* Dark mode */
    html.app-skin-dark .detail-card { background:#0a2317 !important;border-color:rgba(34,197,94,.15) !important; }
    html.app-skin-dark .detail-card-title { color:#d1fae5 !important; }
    html.app-skin-dark .detail-item { border-color:rgba(34,197,94,.07) !important; }
    html.app-skin-dark .detail-label { color:#6b8f72 !important; }
    html.app-skin-dark .detail-value { color:#d1fae5 !important; }
    html.app-skin-dark .keterangan-box { background:#0e3b26;border-color:#16a34a;color:#bbf7d0; }
    html.app-skin-dark .photo-display-card { background:#0e3b26;border-color:rgba(34,197,94,.15); }

    @media (max-width: 991.98px) {
        .hero-stats-grid { grid-template-columns: repeat(2,1fr); gap:10px; }
    }
</style>
@endsection

@section('content')
<article class="pemeliharaan-detail">

    {{-- ================================================================
         1. HERO CARD — Summary visual
         ================================================================ --}}
    <section class="detail-hero" aria-label="Ringkasan Data Pemeliharaan">
        <div class="row align-items-center g-4">
            <div class="col-lg-7">
                {{-- PKS Badges --}}
                <div class="d-flex align-items-center gap-2 flex-wrap mb-3">
                    <div class="detail-hero-pks-badge">
                        <i class="feather-home" style="font-size:14px;color:#4ade80;"></i>
                        <span>PKS {{ $data->pks->nama ?? $data->pks->NAMA ?? '' }}</span>
                        @if(isset($data->pks->akro) || isset($data->pks->AKRO))
                            <span class="hero-akro-pill">({{ $data->pks->akro ?? $data->pks->AKRO }})</span>
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
                    {{ $data->tanggal ? $data->tanggal->locale('id')->translatedFormat('l, d F Y') : '-' }}
                </h1>

                {{-- Meta Pills --}}
                <div class="detail-hero-meta">
                    <div class="detail-hero-pill">
                        <i class="feather-tool"></i>
                        <span>Jenis: <strong>{{ $data->jenis_pemeliharaan == 1 ? 'Mekanis' : 'Manual' }}</strong></span>
                    </div>
                    <div class="detail-hero-pill">
                        <i class="feather-map-pin"></i>
                        <span>Blok <strong>{{ $data->blok ?? '-' }}</strong> | Bak <strong>{{ $data->no_bak ?? '-' }}</strong></span>
                    </div>
                    <div class="detail-hero-pill">
                        <i class="feather-users"></i>
                        <span>Tenaga Kerja: <strong>{{ $data->jumlah_hk ?? '-' }} HK</strong></span>
                    </div>
                    @if(isset($data->pks->manager) && $data->pks->manager)
                    <div class="detail-hero-pill">
                        <i class="feather-user"></i>
                        <span>Manager: <strong>{{ $data->pks->manager }}</strong></span>
                    </div>
                    @endif
                </div>

                {{-- Stats Grid --}}
                <div class="hero-stats-grid">
                    <div class="hero-stat-tile">
                        <div class="hero-stat-icon" style="color:#60a5fa;background:rgba(96,165,250,.15);"><i class="feather-square"></i></div>
                        <div>
                            <div class="hero-stat-val" style="color:#93c5fd;">{{ number_format($data->flat_bed ?? 0) }} <span class="unit">Bed</span></div>
                            <div class="hero-stat-lbl">Flat Bed</div>
                        </div>
                    </div>
                    <div class="hero-stat-tile">
                        <div class="hero-stat-icon" style="color:#fbbf24;background:rgba(251,191,36,.15);"><i class="feather-grid"></i></div>
                        <div>
                            <div class="hero-stat-val" style="color:#fde68a;">{{ number_format($data->long_bed ?? 0) }} <span class="unit">Bed</span></div>
                            <div class="hero-stat-lbl">Long Bed</div>
                        </div>
                    </div>
                    <div class="hero-stat-tile">
                        <div class="hero-stat-icon" style="color:#4ade80;background:rgba(74,222,128,.15);"><i class="feather-layers"></i></div>
                        <div>
                            <div class="hero-stat-val" style="color:#86efac;">{{ number_format(($data->flat_bed ?? 0) + ($data->long_bed ?? 0)) }} <span class="unit">Bed</span></div>
                            <div class="hero-stat-lbl">Total Bed</div>
                        </div>
                    </div>
                    <div class="hero-stat-tile">
                        <div class="hero-stat-icon" style="color:#2dd4bf;background:rgba(45,212,191,.15);"><i class="feather-users"></i></div>
                        <div>
                            <div class="hero-stat-val" style="color:#5eead4;">{{ $data->jumlah_hk ?? 0 }} <span class="unit">HK</span></div>
                            <div class="hero-stat-lbl">Tenaga Kerja</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Right Column: Status Card --}}
            <div class="col-lg-5">
                <div class="hero-efficiency-card">
                    <div class="hero-eff-header">
                        <i class="feather-shield-check me-1" style="color:#4ade80;"></i>
                        Jenis Pemeliharaan
                    </div>
                    <div class="efficiency-display">
                        @if($data->jenis_pemeliharaan == 1)
                            <span style="color:#93c5fd;"><i class="feather-settings me-2"></i>Mekanis</span>
                        @else
                            <span style="color:#fde68a;"><i class="feather-user me-2"></i>Manual</span>
                        @endif
                    </div>
                    <div class="hero-eff-sub mt-2">
                        Lokasi: Blok {{ $data->blok ?? '-' }} &bull; Bak {{ $data->no_bak ?? '-' }}
                    </div>
                    <div class="hero-eff-status">
                        <span class="status-active">
                            <i class="feather-check-circle me-1.5"></i> Pemeliharaan Selesai / Tuntas
                        </span>
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
                                <span class="detail-label">Tanggal</span>
                                <div class="detail-value">
                                    {{ $data->tanggal ? $data->tanggal->locale('id')->translatedFormat('l, d F Y') : '-' }}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="detail-item">
                                <span class="detail-label">Unit PKS</span>
                                <div class="detail-value">
                                    <span class="pks-badge" style="display:inline-flex;align-items:center;gap:5px;padding:4px 10px;border-radius:8px;font-size:11px;font-weight:800;background:linear-gradient(135deg,#052e16,#166534);color:#86efac;">
                                        {{ $data->pks->AKRO ?? 'N/A' }}
                                    </span>
                                    <span style="margin-left:8px;font-size:13px;font-weight:600;color:#374151;">{{ $data->pks->NAMA ?? '' }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="detail-item">
                                <span class="detail-label">Jenis Pemeliharaan</span>
                                <div class="detail-value">
                                    @if($data->jenis_pemeliharaan == 1)
                                        <span class="mod-pill mod-pill-info"><i class="feather-settings me-1"></i> Mekanis</span>
                                    @else
                                        <span class="mod-pill mod-pill-warn"><i class="feather-user me-1"></i> Manual</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="detail-item">
                                <span class="detail-label">Jumlah Tenaga Kerja (HK)</span>
                                <div class="detail-value">{{ $data->jumlah_hk ?? '-' }} Orang</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Detail Lokasi --}}
            <div class="detail-card mb-4">
                <div class="detail-card-header">
                    <i class="feather-map-pin" style="color:#16a34a;font-size:17px;"></i>
                    <h3 class="detail-card-title">Detail Lokasi</h3>
                </div>
                <div class="detail-card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="detail-item">
                                <span class="detail-label">No. Bak</span>
                                <div class="detail-value" style="font-size:16px;font-weight:800;color:#1d4ed8;">{{ $data->no_bak ?? '—' }}</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="detail-item">
                                <span class="detail-label">Blok</span>
                                <div class="detail-value" style="font-size:16px;font-weight:800;color:#14532d;">{{ $data->blok ?? '—' }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Area Pemeliharaan --}}
            <div class="detail-card mb-4">
                <div class="detail-card-header">
                    <i class="feather-layers" style="color:#16a34a;font-size:17px;"></i>
                    <h3 class="detail-card-title">Area Pemeliharaan</h3>
                </div>
                <div class="detail-card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="detail-item">
                                <span class="detail-label">Flat Bed</span>
                                <div class="detail-value" style="font-family:'Outfit',sans-serif;font-size:22px;font-weight:900;color:#1d4ed8;">
                                    {{ number_format($data->flat_bed) }} <span style="font-size:13px;font-weight:600;color:#6b7280;">Unit</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="detail-item">
                                <span class="detail-label">Long Bed</span>
                                <div class="detail-value" style="font-family:'Outfit',sans-serif;font-size:22px;font-weight:900;color:#b45309;">
                                    {{ number_format($data->long_bed) }} <span style="font-size:13px;font-weight:600;color:#6b7280;">Unit</span>
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

        {{-- RIGHT COLUMN: Foto Dokumentasi --}}
        <div class="col-lg-4">

            {{-- Foto Sebelum --}}
            <div class="detail-card mb-4">
                <div class="detail-card-header">
                    <i class="feather-camera" style="color:#dc2626;font-size:17px;"></i>
                    <h3 class="detail-card-title">Foto Sebelum</h3>
                </div>
                <div class="detail-card-body" style="padding:14px;">
                    @if($data->sebelum && file_exists(public_path('gallery/'.$data->sebelum)))
                        <div class="photo-display-card">
                            <img src="{{ asset('gallery/'.$data->sebelum) }}"
                                 class="photo-display-img" alt="Foto Sebelum"
                                 onclick="showPhoto('{{ asset('gallery/'.$data->sebelum) }}', 'Foto Sebelum Pemeliharaan')">
                        </div>
                    @else
                        <div class="text-center py-4" style="color:#9ca3af;">
                            <i class="feather-image d-block mb-2" style="font-size:36px;opacity:.4;"></i>
                            <span style="font-size:12px;">Foto Sebelum Belum Ada</span>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Foto Sesudah --}}
            <div class="detail-card">
                <div class="detail-card-header">
                    <i class="feather-camera" style="color:#16a34a;font-size:17px;"></i>
                    <h3 class="detail-card-title">Foto Sesudah</h3>
                </div>
                <div class="detail-card-body" style="padding:14px;">
                    @if($data->sesudah && file_exists(public_path('gallery/'.$data->sesudah)))
                        <div class="photo-display-card">
                            <img src="{{ asset('gallery/'.$data->sesudah) }}"
                                 class="photo-display-img" alt="Foto Sesudah"
                                 onclick="showPhoto('{{ asset('gallery/'.$data->sesudah) }}', 'Foto Sesudah Pemeliharaan')">
                        </div>
                    @else
                        <div class="text-center py-4" style="color:#9ca3af;">
                            <i class="feather-image d-block mb-2" style="font-size:36px;opacity:.4;"></i>
                            <span style="font-size:12px;">Foto Sesudah Belum Ada</span>
                        </div>
                    @endif
                </div>
            </div>

        </div>
    </div>
</article>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.12/dist/sweetalert2.all.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.form-delete').forEach(function(form){
            form.addEventListener('submit', function(e){
                e.preventDefault();
                Swal.fire({
                    title: 'Konfirmasi Hapus Data',
                    text: 'Yakin ingin menghapus data pemeliharaan ini? Aksi ini tidak dapat dibatalkan.',
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

    function showPhoto(url, title) {
        Swal.fire({
            title: title,
            imageUrl: url,
            imageAlt: title,
            imageWidth: 600,
            showConfirmButton: false,
            showCloseButton: true,
            customClass: { image: 'rounded-4' }
        });
    }
</script>
@endsection