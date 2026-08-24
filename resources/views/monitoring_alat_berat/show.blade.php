@extends('layouts.simoli')

@section('title', 'Detail Log Monitoring Alat Berat')
@section('page-title', 'Detail Log Monitoring Alat Berat')
@section('page-description', 'Rincian Data Operasional, Jam Kerja (HM) & Titik Koordinat GPS')

@section('breadcrumb')
    <li>Input Data</li>
    <li class="separator">/</li>
    <li><a href="{{ route('monitoring-alat-berat.index') }}" style="color:inherit;text-decoration:none;">Monitoring Alat Berat</a></li>
    <li class="separator">/</li>
    <li>Detail Log</li>
@endsection

@section('page-actions')
<div class="d-flex align-items-center gap-2 flex-wrap">
    @if(Auth::user()->isUnit())
    <a href="{{ route('monitoring-alat-berat.edit', $log->id) }}" class="btn-ptpn btn-ptpn-primary" style="padding:9px 16px;font-size:13px;border-radius:12px;">
        <i class="feather-edit-2 me-1" style="font-size:14px;"></i> Edit Log Data
    </a>
    @endif
    <a href="{{ route('monitoring-alat-berat.index') }}" class="btn-ptpn btn-ptpn-outline" style="padding:9px 16px;font-size:13px;border-radius:12px;">
        <i class="feather-arrow-left me-1" style="font-size:14px;"></i> Kembali
    </a>
</div>
@endsection

@section('styles')
<style>
    /* ================================================================
       MONITORING ALAT BERAT SHOW — PTPN GREEN GLOSSY THEME
       ================================================================ */
    @keyframes fadeUpCard {
        from { opacity:0; transform:translateY(12px); }
        to   { opacity:1; transform:translateY(0); }
    }

    /* === HERO SUMMARY CARD === */
    .detail-hero {
        border-radius: 20px;
        background: linear-gradient(135deg, #052e16 0%, #0a2317 35%, #166534 75%, #16a34a 100%);
        border: 1px solid rgba(34,197,94,.3);
        box-shadow: 0 14px 40px rgba(5,46,22,.25);
        padding: clamp(18px, 3vw + 10px, 32px);
        color: #ffffff;
        margin-bottom: 24px;
        position: relative;
        overflow: hidden;
        animation: fadeUpCard .4s ease-out;
    }

    .detail-hero::before {
        content: '';
        position: absolute;
        top: -90px;
        right: -90px;
        width: 300px;
        height: 300px;
        border-radius: 50%;
        background: radial-gradient(circle, rgba(134,239,172,.2) 0%, transparent 70%);
        pointer-events: none;
    }

    .detail-hero-pks-badge {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 5px 14px;
        border-radius: 50px;
        background: rgba(255,255,255,.15);
        border: 1px solid rgba(255,255,255,.25);
        font-size: 12px;
        font-weight: 800;
        color: #ffffff;
        margin-bottom: 12px;
    }

    .detail-hero-title {
        font-family: 'Outfit', sans-serif;
        font-size: clamp(20px, 2.5vw + 10px, 30px);
        font-weight: 900;
        color: #ffffff;
        letter-spacing: -0.5px;
        line-height: 1.15;
        margin-bottom: 12px;
        word-break: break-word;
    }

    .detail-hero-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        margin-bottom: 20px;
    }

    .detail-hero-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 14px;
        border-radius: 50px;
        background: rgba(255,255,255,.12);
        border: 1px solid rgba(255,255,255,.18);
        font-size: 12px;
        font-weight: 600;
        color: rgba(209,250,229,.95);
    }

    /* Stats Grid in Hero (Responsive Auto-fit) */
    .hero-stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(130px, 1fr));
        gap: 10px;
        margin-top: 6px;
    }

    .hero-stat-tile {
        background: rgba(255,255,255,.08);
        border: 1px solid rgba(255,255,255,.14);
        border-radius: 14px;
        padding: 12px 14px;
        backdrop-filter: blur(8px);
        transition: all .2s ease;
    }

    .hero-stat-tile:hover {
        background: rgba(255,255,255,.15);
        border-color: rgba(134,239,172,.4);
        transform: translateY(-2px);
    }

    .hero-stat-val {
        font-family: 'Outfit', sans-serif;
        font-size: clamp(18px, 1.8vw + 8px, 24px);
        font-weight: 900;
        color: #ffffff;
        line-height: 1.1;
        margin-bottom: 3px;
    }

    .hero-stat-lbl {
        font-size: 10px;
        font-weight: 700;
        color: rgba(187,247,208,.75);
        text-transform: uppercase;
        letter-spacing: .5px;
    }

    /* === DETAIL CARDS === */
    .detail-card {
        background: #ffffff;
        border-radius: 18px;
        border: 1px solid rgba(22,163,74,.12);
        box-shadow: 0 2px 12px rgba(22,163,74,.06);
        overflow: hidden;
        margin-bottom: 20px;
        animation: fadeUpCard .4s ease-out;
    }

    .detail-card-header {
        padding: 14px 20px;
        border-bottom: 1px solid rgba(22,163,74,.08);
        display: flex;
        align-items: center;
        gap: 10px;
        background: linear-gradient(135deg, #f0fdf4 0%, #ffffff 100%);
    }

    .detail-card-title {
        font-family: 'Outfit', sans-serif;
        font-size: 14px;
        font-weight: 800;
        color: #14532d;
        margin: 0;
    }

    .detail-card-body { padding: 20px; }

    .detail-item {
        padding: 12px 0;
        border-bottom: 1px solid rgba(22,163,74,.07);
    }
    .detail-item:last-child { border-bottom: none; }

    .detail-label {
        font-size: 11px;
        font-weight: 700;
        color: #6b7280;
        text-transform: uppercase;
        letter-spacing: .4px;
        margin-bottom: 5px;
        display: block;
    }

    .detail-value {
        font-size: 14.5px;
        font-weight: 600;
        color: #1f2937;
        word-break: break-word;
    }

    /* GPS Interactive Links */
    .gps-link-btn {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 14px;
        border-radius: 50px;
        background: #f0fdf4;
        color: #15803d;
        border: 1px solid #86efac;
        font-size: 11.5px;
        font-weight: 700;
        text-decoration: none;
        transition: all .2s ease;
        word-break: break-all;
    }
    .gps-link-btn:hover {
        background: #16a34a;
        color: #ffffff;
        border-color: #15803d;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(22,163,74,.25);
    }

    .gps-link-btn-akhir {
        background: #e0f2fe;
        color: #0369a1;
        border-color: #7dd3fc;
    }
    .gps-link-btn-akhir:hover {
        background: #0284c7;
        color: #ffffff;
        border-color: #0369a1;
        box-shadow: 0 4px 12px rgba(2,132,199,.25);
    }

    /* Photo Cards */
    .photo-display-card {
        border-radius: 14px;
        overflow: hidden;
        border: 1px solid rgba(22,163,74,.15);
        background: #f9fafb;
        position: relative;
        cursor: pointer;
        transition: all .25s ease;
    }
    .photo-display-card:hover {
        box-shadow: 0 8px 24px rgba(22,163,74,.15);
        transform: translateY(-2px);
    }

    .photo-display-img {
        width: 100%;
        height: 230px;
        object-fit: cover;
        display: block;
        transition: transform .3s ease;
    }
    .photo-display-card:hover .photo-display-img { transform: scale(1.03); }

    .photo-overlay-badge {
        position: absolute;
        bottom: 10px;
        right: 10px;
        background: rgba(5,46,22,.75);
        backdrop-filter: blur(6px);
        color: #86efac;
        padding: 4px 10px;
        border-radius: 50px;
        font-size: 10.5px;
        font-weight: 700;
        display: flex;
        align-items: center;
        gap: 4px;
        border: 1px solid rgba(255,255,255,.2);
    }

    /* Dark mode */
    html.app-skin-dark .detail-card { background:#0a2317 !important;border-color:rgba(34,197,94,.15) !important; }
    html.app-skin-dark .detail-card-header { background:linear-gradient(135deg,#0e3b26 0%,#0a2317 100%);border-color:rgba(34,197,94,.15); }
    html.app-skin-dark .detail-card-title { color:#d1fae5 !important; }
    html.app-skin-dark .detail-item { border-color:rgba(34,197,94,.07) !important; }
    html.app-skin-dark .detail-label { color:#6b8f72 !important; }
    html.app-skin-dark .detail-value { color:#d1fae5 !important; }
    html.app-skin-dark .photo-display-card { background:#0e3b26;border-color:rgba(34,197,94,.15); }
    html.app-skin-dark .gps-link-btn { background:#0e3b26;color:#86efac;border-color:rgba(34,197,94,.3); }

    /* Responsive Queries */
    @media (max-width: 991.98px) {
        .detail-hero-title { font-size: 22px; }
        .hero-stats-grid { grid-template-columns: repeat(2, 1fr); }
    }

    @media (max-width: 575.98px) {
        .detail-hero { padding: 18px; }
        .hero-stats-grid { grid-template-columns: repeat(2, 1fr); gap: 8px; }
        .hero-stat-tile { padding: 10px; }
        .hero-stat-val { font-size: 17px; }
        .detail-card-body { padding: 14px; }
        .photo-display-img { height: 180px; }
    }
</style>
@endsection

@section('content')
<article class="mab-detail">

    {{-- ================================================================
         1. HERO CARD — Ringkasan Visual Utama
         ================================================================ --}}
    <section class="detail-hero" aria-label="Ringkasan Log Monitoring">
        <div class="row align-items-start g-4">
            <div class="col-lg-8 col-12">
                <div class="detail-hero-pks-badge">
                    <i class="feather-home" style="font-size:14px;color:#86efac;"></i>
                    {{ $log->pks ? $log->pks->nama : 'PKS N/A' }} ({{ $log->pks ? $log->pks->akro : 'N/A' }})
                </div>
                <div class="detail-hero-title">
                    {{ $log->alatBerat ? $log->alatBerat->kode_alat : '-' }} &bull; {{ $log->alatBerat ? $log->alatBerat->nama_alat : '-' }}
                </div>
                <div class="detail-hero-meta">
                    <div class="detail-hero-pill">
                        <i class="feather-calendar" style="font-size:13px;color:#86efac;"></i>
                        {{ \Carbon\Carbon::parse($log->tanggal)->translatedFormat('l, d F Y') }}
                    </div>
                    <div class="detail-hero-pill">
                        <i class="feather-user" style="font-size:13px;color:#86efac;"></i>
                        Operator: {{ $log->operator }}
                    </div>
                    <div class="detail-hero-pill">
                        <i class="feather-activity" style="font-size:13px;color:#86efac;"></i>
                        Kondisi: {{ $log->kondisi_alat }}
                    </div>
                </div>

                <div class="hero-stats-grid">
                    <div class="hero-stat-tile">
                        <div class="hero-stat-val" style="color:#86efac;">{{ $log->total_hm_formatted }}</div>
                        <div class="hero-stat-lbl">Jam Kerja (HM)</div>
                    </div>
                    <div class="hero-stat-tile">
                        <div class="hero-stat-val" style="color:#fbbf24;">{{ number_format($log->bbm_liter, 0) }} L</div>
                        <div class="hero-stat-lbl">Konsumsi BBM</div>
                    </div>
                    <div class="hero-stat-tile">
                        <div class="hero-stat-val" style="color:#60a5fa;">{{ number_format($log->jumlah_bed) }}</div>
                        <div class="hero-stat-lbl">Total Bed</div>
                    </div>
                    <div class="hero-stat-tile">
                        <div class="hero-stat-val" style="color:#2dd4bf;">{{ $log->flat_bed ?? 0 }} / {{ $log->long_bed ?? 0 }}</div>
                        <div class="hero-stat-lbl">Flat / Long Bed</div>
                    </div>
                </div>
            </div>

            {{-- Status Badge Card (Right Side) --}}
            <div class="col-lg-4 col-12">
                <div style="background:rgba(255,255,255,.08);border:1px solid rgba(255,255,255,.14);border-radius:18px;padding:20px;text-align:center;backdrop-filter:blur(10px);">
                    <div style="font-size:11px;font-weight:700;color:rgba(187,247,208,.8);text-transform:uppercase;letter-spacing:.6px;margin-bottom:8px;">
                        Kondisi Alat Saat Kerja
                    </div>
                    <div style="font-family:'Outfit',sans-serif;font-size:24px;font-weight:900;color:#ffffff;margin-bottom:6px;">
                        @if($log->kondisi_alat == 'Normal')
                            <span style="color:#86efac;"><i class="feather-check-circle me-1"></i> Normal / Baik</span>
                        @elseif($log->kondisi_alat == 'Perlu Perbaikan')
                            <span style="color:#fde68a;"><i class="feather-alert-triangle me-1"></i> Perlu Perbaikan</span>
                        @else
                            <span style="color:#fca5a5;"><i class="feather-x-circle me-1"></i> Breakdown</span>
                        @endif
                    </div>
                    <div style="font-size:12.5px;color:rgba(209,250,229,.85);font-weight:600;">
                        {{ $log->kegiatan }}
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ================================================================
         2. DETAIL SECTIONS
         ================================================================ --}}
    <div class="row g-4">

        {{-- LEFT COLUMN: Rincian Kegiatan, HM, BBM & GPS --}}
        <div class="col-lg-8 col-12">

            {{-- 1. Kegiatan & Jam Kerja (Hour Meter) --}}
            <div class="detail-card">
                <div class="detail-card-header">
                    <i class="feather-truck" style="color:#16a34a;font-size:18px;"></i>
                    <h3 class="detail-card-title">Kegiatan &amp; Jam Kerja (Hour Meter)</h3>
                </div>
                <div class="detail-card-body">
                    <div class="row g-3">
                        <div class="col-md-6 col-12">
                            <div class="detail-item">
                                <span class="detail-label">Jenis Kegiatan Pengolahan</span>
                                <div class="detail-value" style="color:#14532d;font-weight:800;">{{ $log->kegiatan }}</div>
                            </div>
                        </div>

                        <div class="col-md-6 col-12">
                            <div class="detail-item">
                                <span class="detail-label">Lokasi / Kolam / Blok Kerja</span>
                                <div class="detail-value">{{ $log->lokasi_blok ?? '-' }}</div>
                            </div>
                        </div>

                        <div class="col-md-4 col-6">
                            <div class="detail-item">
                                <span class="detail-label">HM Awal</span>
                                <div class="detail-value" style="color:#16a34a;font-family:'Outfit',sans-serif;font-size:18px;font-weight:800;">
                                    {{ $log->hm_awal_formatted }}
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4 col-6">
                            <div class="detail-item">
                                <span class="detail-label">HM Akhir</span>
                                <div class="detail-value" style="color:#16a34a;font-family:'Outfit',sans-serif;font-size:18px;font-weight:800;">
                                    {{ $log->hm_akhir_formatted }}
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4 col-12">
                            <div class="detail-item">
                                <span class="detail-label">Total Jam Kerja</span>
                                <div class="detail-value">
                                    <span class="mod-pill mod-pill-ok" style="font-size:13px;font-weight:800;">
                                        {{ $log->total_hm_formatted }} Jam
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 col-12">
                            <div class="detail-item">
                                <span class="detail-label">Konsumsi BBM Solar</span>
                                <div class="detail-value">
                                    <span class="mod-pill mod-pill-warn" style="font-size:13px;font-weight:800;">
                                        {{ number_format($log->bbm_liter, 1) }} Liter
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 col-12">
                            <div class="detail-item">
                                <span class="detail-label">Jumlah Bed Dikerjakan</span>
                                <div class="detail-value">
                                    <span class="mod-pill mod-pill-info" style="font-size:12px;">
                                        Flat: {{ $log->flat_bed ?? 0 }} | Long: {{ $log->long_bed ?? 0 }} | Total: {{ $log->jumlah_bed }} Bed
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- 2. Titik Koordinat GPS (Interaktif ke Google Maps) --}}
            @php
                $latAwal = $log->latitude_awal ?? $log->latitude;
                $longAwal = $log->longitude_awal ?? $log->longitude;
                $latAkhir = $log->latitude_akhir;
                $longAkhir = $log->longitude_akhir;
            @endphp
            <div class="detail-card">
                <div class="detail-card-header">
                    <i class="feather-map-pin" style="color:#16a34a;font-size:18px;"></i>
                    <h3 class="detail-card-title">Titik Koordinat GPS Kerja</h3>
                </div>
                <div class="detail-card-body">
                    <div class="row g-3">
                        <div class="col-md-6 col-12">
                            <div class="detail-item">
                                <span class="detail-label">Koordinat Awal Kerja</span>
                                <div class="detail-value">
                                    @if($latAwal && $longAwal)
                                        <div class="mt-1">
                                            <a href="{{ $log->google_maps_url_awal }}" target="_blank" class="gps-link-btn" title="Buka Koordinat Awal di Google Maps">
                                                <i class="feather-navigation" style="font-size:12px;"></i>
                                                <span>Awal: {{ number_format((float)$latAwal, 5) }}, {{ number_format((float)$longAwal, 5) }}</span>
                                                <i class="feather-external-link ms-1" style="font-size:11px;"></i>
                                            </a>
                                        </div>
                                    @else
                                        <span style="color:#9ca3af;font-size:13px;">Tidak direkam</span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 col-12">
                            <div class="detail-item">
                                <span class="detail-label">Koordinat Akhir Kerja</span>
                                <div class="detail-value">
                                    @if($latAkhir && $longAkhir)
                                        <div class="mt-1">
                                            <a href="{{ $log->google_maps_url_akhir }}" target="_blank" class="gps-link-btn gps-link-btn-akhir" title="Buka Koordinat Akhir di Google Maps">
                                                <i class="feather-navigation" style="font-size:12px;"></i>
                                                <span>Akhir: {{ number_format((float)$latAkhir, 5) }}, {{ number_format((float)$longAkhir, 5) }}</span>
                                                <i class="feather-external-link ms-1" style="font-size:11px;"></i>
                                            </a>
                                        </div>
                                    @else
                                        <span style="color:#9ca3af;font-size:13px;">Tidak direkam</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- 3. Catatan Lapangan --}}
            @if($log->catatan)
            <div class="detail-card">
                <div class="detail-card-header">
                    <i class="feather-message-square" style="color:#16a34a;font-size:18px;"></i>
                    <h3 class="detail-card-title">Catatan Lapangan &amp; Kendala</h3>
                </div>
                <div class="detail-card-body">
                    <div style="padding:14px 16px;border-left:4px solid #16a34a;border-radius:12px;background:#f0fdf4;font-size:13.5px;color:#14532d;line-height:1.5;">
                        {{ $log->catatan }}
                    </div>
                </div>
            </div>
            @endif

        </div>

        {{-- RIGHT COLUMN: Foto Dokumentasi --}}
        <div class="col-lg-4 col-12">

            {{-- Foto Sebelum --}}
            <div class="detail-card">
                <div class="detail-card-header justify-content-between">
                    <div class="d-flex align-items-center gap-2">
                        <i class="feather-camera" style="color:#dc2626;font-size:17px;"></i>
                        <h3 class="detail-card-title">Foto Sebelum Kerja</h3>
                    </div>
                    <span class="mod-pill mod-pill-err" style="font-size:9.5px;">Awal Shift</span>
                </div>
                <div class="detail-card-body" style="padding:14px;">
                    @if($log->foto_sebelum_url)
                        <div class="photo-display-card" onclick="showPhoto('{{ $log->foto_sebelum_url }}', 'Foto Sebelum Jam Kerja (Awal Shift)')">
                            <img src="{{ $log->foto_sebelum_url }}" class="photo-display-img" alt="Foto Sebelum">
                            <div class="photo-overlay-badge">
                                <i class="feather-maximize-2"></i> Perbesar
                            </div>
                        </div>
                    @else
                        <div class="text-center py-4" style="color:#9ca3af;">
                            <i class="feather-image d-block mb-2" style="font-size:36px;opacity:.35;"></i>
                            <span style="font-size:12px;">Foto Sebelum Belum Ada</span>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Foto Sesudah --}}
            <div class="detail-card">
                <div class="detail-card-header justify-content-between">
                    <div class="d-flex align-items-center gap-2">
                        <i class="feather-camera" style="color:#16a34a;font-size:17px;"></i>
                        <h3 class="detail-card-title">Foto Sesudah Kerja</h3>
                    </div>
                    <span class="mod-pill mod-pill-ok" style="font-size:9.5px;">Akhir Shift</span>
                </div>
                <div class="detail-card-body" style="padding:14px;">
                    @if($log->foto_sesudah_url)
                        <div class="photo-display-card" onclick="showPhoto('{{ $log->foto_sesudah_url }}', 'Foto Sesudah Jam Kerja (Akhir Shift)')">
                            <img src="{{ $log->foto_sesudah_url }}" class="photo-display-img" alt="Foto Sesudah">
                            <div class="photo-overlay-badge">
                                <i class="feather-maximize-2"></i> Perbesar
                            </div>
                        </div>
                    @else
                        <div class="text-center py-4" style="color:#9ca3af;">
                            <i class="feather-image d-block mb-2" style="font-size:36px;opacity:.35;"></i>
                            <span style="font-size:12px;">Foto Sesudah Belum Ada</span>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Foto Lampiran Utama --}}
            @if($log->foto && $log->foto !== $log->foto_sebelum && $log->foto !== $log->foto_sesudah)
            <div class="detail-card">
                <div class="detail-card-header">
                    <i class="feather-image" style="color:#1d4ed8;font-size:17px;"></i>
                    <h3 class="detail-card-title">Foto Utama Lampiran</h3>
                </div>
                <div class="detail-card-body" style="padding:14px;">
                    <div class="photo-display-card" onclick="showPhoto('{{ asset('gallery/' . $log->foto) }}', 'Foto Lampiran Utama Operasional')">
                        <img src="{{ asset('gallery/' . $log->foto) }}" class="photo-display-img" alt="Foto Lampiran">
                        <div class="photo-overlay-badge">
                            <i class="feather-maximize-2"></i> Perbesar
                        </div>
                    </div>
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
    function showPhoto(url, title) {
        Swal.fire({
            title: title,
            imageUrl: url,
            imageAlt: title,
            imageWidth: 700,
            showConfirmButton: false,
            showCloseButton: true,
            customClass: { image: 'rounded-4 border shadow-sm' },
            borderRadius: '20px'
        });
    }
</script>
@endsection
