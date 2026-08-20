@extends('layouts.simoli')

@section('title', 'Detail Log Monitoring Alat Berat')
@section('page-title', 'Detail Log Monitoring Alat Berat')
@section('page-description', 'Rincian Data Operasional, Jam Kerja (HM) & Koordinat GPS')

@section('breadcrumb')
    <li>Input Data</li>
    <li class="separator">/</li>
    <li><a href="{{ route('monitoring-alat-berat.index') }}" style="color:inherit;text-decoration:none;">Monitoring Alat Berat</a></li>
    <li class="separator">/</li>
    <li>Detail Log</li>
@endsection

@section('page-actions')
<div class="d-flex align-items-center gap-2">
    @if(Auth::user()->isUnit())
    <a href="{{ route('monitoring-alat-berat.edit', $log->id) }}" class="btn-ptpn btn-ptpn-primary" style="padding:8px 14px;font-size:13px;">
        <i class="feather-edit-2" style="font-size:14px;"></i> Edit Log Data
    </a>
    @endif
    <a href="{{ route('monitoring-alat-berat.index') }}" class="btn-ptpn btn-ptpn-outline" style="padding:8px 14px;font-size:13px;">
        <i class="feather-arrow-left" style="font-size:14px;"></i> Kembali
    </a>
</div>
@endsection

@section('styles')
<style>
    /* ================================================================
       MONITORING ALAT BERAT SHOW — PTPN GREEN THEME
       ================================================================ */
    @keyframes fadeUpCard {
        from { opacity:0; transform:translateY(12px); }
        to   { opacity:1; transform:translateY(0); }
    }

    /* === HERO SUMMARY CARD === */
    .detail-hero {
        border-radius: 20px;
        background: linear-gradient(135deg, #052e16 0%, #0a2317 35%, #166534 70%, #16a34a 100%);
        border: 1px solid rgba(34,197,94,.25);
        box-shadow: 0 12px 40px rgba(0,0,0,.15);
        padding: clamp(20px,3vw+12px,36px);
        color: #ffffff;
        margin-bottom: 24px;
        position: relative;
        overflow: hidden;
        animation: fadeUpCard .4s ease-out;
    }

    .detail-hero::before {
        content:'';position:absolute;top:-80px;right:-80px;
        width:260px;height:260px;border-radius:50%;
        background:radial-gradient(circle,rgba(34,197,94,.2) 0%,transparent 70%);
        pointer-events:none;
    }

    .detail-hero-pks-badge {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 5px 14px;
        border-radius: 50px;
        background: rgba(255,255,255,.15);
        border: 1px solid rgba(255,255,255,.25);
        font-size: 13px;
        font-weight: 800;
        color: #ffffff;
        margin-bottom: 12px;
    }

    .detail-hero-title {
        font-family: 'Outfit', sans-serif;
        font-size: clamp(22px,2.5vw+10px,30px);
        font-weight: 900;
        color: #ffffff;
        letter-spacing: -0.5px;
        line-height: 1.1;
        margin-bottom: 10px;
    }

    .detail-hero-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        margin-bottom: 20px;
    }

    .detail-hero-pill {
        display: flex;
        align-items: center;
        gap: 6px;
        padding: 5px 12px;
        border-radius: 50px;
        background: rgba(255,255,255,.1);
        border: 1px solid rgba(255,255,255,.15);
        font-size: 12px;
        font-weight: 600;
        color: rgba(209,250,229,.9);
    }

    /* Stats Grid in Hero */
    .hero-stats-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 10px;
        margin-top: 4px;
    }

    .hero-stat-tile {
        background: rgba(255,255,255,.08);
        border: 1px solid rgba(255,255,255,.12);
        border-radius: 12px;
        padding: 12px 14px;
        backdrop-filter: blur(8px);
        transition: all .2s ease;
    }

    .hero-stat-tile:hover {
        background: rgba(255,255,255,.14);
        border-color: rgba(34,197,94,.4);
        transform: translateY(-2px);
    }

    .hero-stat-val {
        font-family: 'Outfit', sans-serif;
        font-size: clamp(18px,2vw+10px,24px);
        font-weight: 900;
        color: #ffffff;
        line-height: 1;
        margin-bottom: 4px;
    }

    .hero-stat-lbl {
        font-size: 10px;
        font-weight: 700;
        color: rgba(187,247,208,.65);
        text-transform: uppercase;
        letter-spacing: .5px;
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
    html.app-skin-dark .photo-display-card { background:#0e3b26;border-color:rgba(34,197,94,.15); }

    @media (max-width: 767.98px) {
        .hero-stats-grid { grid-template-columns: repeat(2,1fr); gap:8px; }
    }
</style>
@endsection

@section('content')
<article class="mab-detail">

    {{-- ================================================================
         1. HERO CARD — Summary visual
         ================================================================ --}}
    <section class="detail-hero" aria-label="Ringkasan Log Monitoring">
        <div class="row align-items-start g-4">
            <div class="col-lg-8">
                <div class="detail-hero-pks-badge">
                    <i class="feather-home" style="font-size:15px;"></i>
                    {{ $log->pks ? $log->pks->nama : 'PKS N/A' }} ({{ $log->pks ? $log->pks->akro : 'N/A' }})
                </div>
                <div class="detail-hero-title">
                    {{ $log->alatBerat ? $log->alatBerat->kode_alat : '-' }} &bull; {{ $log->alatBerat ? $log->alatBerat->nama_alat : '-' }}
                </div>
                <div class="detail-hero-meta">
                    <div class="detail-hero-pill">
                        <i class="feather-calendar" style="font-size:13px;color:#4ade80;"></i>
                        {{ \Carbon\Carbon::parse($log->tanggal)->translatedFormat('l, d F Y') }}
                    </div>
                    <div class="detail-hero-pill">
                        <i class="feather-user" style="font-size:13px;color:#4ade80;"></i>
                        Operator: {{ $log->operator }}
                    </div>
                    <div class="detail-hero-pill">
                        <i class="feather-activity" style="font-size:13px;color:#4ade80;"></i>
                        Kondisi: {{ $log->kondisi_alat }}
                    </div>
                </div>

                <div class="hero-stats-grid">
                    <div class="hero-stat-tile">
                        <div class="hero-stat-val" style="color:#4ade80;">{{ $log->total_hm_formatted }}</div>
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

            {{-- Right: Status badge --}}
            <div class="col-lg-4">
                <div style="background:rgba(255,255,255,.08);border:1px solid rgba(255,255,255,.12);border-radius:16px;padding:20px;text-align:center;backdrop-filter:blur(10px);">
                    <div style="font-size:11px;font-weight:700;color:rgba(187,247,208,.7);text-transform:uppercase;letter-spacing:.6px;margin-bottom:8px;">
                        Kondisi Alat Berat
                    </div>
                    <div style="font-family:'Outfit',sans-serif;font-size:24px;font-weight:900;color:#ffffff;margin-bottom:6px;">
                        @if($log->kondisi_alat == 'Normal')
                            <span style="color:#4ade80;"><i class="feather-check-circle me-1"></i> Normal</span>
                        @elseif($log->kondisi_alat == 'Perlu Perbaikan')
                            <span style="color:#fde68a;"><i class="feather-alert-triangle me-1"></i> Perlu Perbaikan</span>
                        @else
                            <span style="color:#fca5a5;"><i class="feather-x-circle me-1"></i> Breakdown</span>
                        @endif
                    </div>
                    <div style="font-size:12px;color:rgba(187,247,208,.7);">
                        {{ $log->kegiatan }}
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

            {{-- Informasi Operasional --}}
            <div class="detail-card mb-4">
                <div class="detail-card-header">
                    <i class="feather-truck" style="color:#16a34a;font-size:17px;"></i>
                    <h3 class="detail-card-title">Kegiatan &amp; Jam Kerja (Hour Meter)</h3>
                </div>
                <div class="detail-card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="detail-item">
                                <span class="detail-label">Jenis Kegiatan</span>
                                <div class="detail-value" style="color:#14532d;">{{ $log->kegiatan }}</div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="detail-item">
                                <span class="detail-label">Lokasi / Kolam / Blok</span>
                                <div class="detail-value">{{ $log->lokasi_blok ?? '-' }}</div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="detail-item">
                                <span class="detail-label">HM Awal</span>
                                <div class="detail-value" style="color:#16a34a;font-family:'Outfit',sans-serif;font-size:18px;font-weight:800;">{{ $log->hm_awal_formatted }}</div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="detail-item">
                                <span class="detail-label">HM Akhir</span>
                                <div class="detail-value" style="color:#16a34a;font-family:'Outfit',sans-serif;font-size:18px;font-weight:800;">{{ $log->hm_akhir_formatted }}</div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="detail-item">
                                <span class="detail-label">Total Jam Kerja</span>
                                <div class="detail-value">
                                    <span class="mod-pill mod-pill-ok" style="font-size:13px;font-weight:800;">{{ $log->total_hm_formatted }} Jam</span>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="detail-item">
                                <span class="detail-label">Konsumsi BBM Solar</span>
                                <div class="detail-value">
                                    <span class="mod-pill mod-pill-warn" style="font-size:13px;font-weight:800;">{{ number_format($log->bbm_liter, 1) }} Liter</span>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="detail-item">
                                <span class="detail-label">Bed Dikerjakan (Flat / Long / Total)</span>
                                <div class="detail-value">
                                    <span class="mod-pill mod-pill-info" style="font-size:12px;">Flat: {{ $log->flat_bed ?? 0 }} | Long: {{ $log->long_bed ?? 0 }} | Total: {{ $log->jumlah_bed }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Koordinat GPS --}}
            @php
                $latAwal = $log->latitude_awal ?? $log->latitude;
                $longAwal = $log->longitude_awal ?? $log->longitude;
                $latAkhir = $log->latitude_akhir;
                $longAkhir = $log->longitude_akhir;
            @endphp
            <div class="detail-card mb-4">
                <div class="detail-card-header">
                    <i class="feather-map-pin" style="color:#16a34a;font-size:17px;"></i>
                    <h3 class="detail-card-title">Titik Koordinat GPS Kerja</h3>
                </div>
                <div class="detail-card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="detail-item">
                                <span class="detail-label">Koordinat Awal Kerja</span>
                                <div class="detail-value">
                                    @if($latAwal && $longAwal)
                                        <div class="d-flex align-items-center gap-2 flex-wrap mt-1">
                                            <span class="mod-pill mod-pill-info"><i class="feather-navigation me-1"></i>{{ $latAwal }}, {{ $longAwal }}</span>
                                            <a href="{{ $log->google_maps_url_awal }}" target="_blank" class="btn-ptpn btn-ptpn-outline" style="padding:4px 10px;font-size:11px;">
                                                <i class="feather-external-link me-1"></i> Buka Peta
                                            </a>
                                        </div>
                                    @else
                                        <span style="color:#9ca3af;font-size:13px;">Tidak direkam</span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="detail-item">
                                <span class="detail-label">Koordinat Akhir Kerja</span>
                                <div class="detail-value">
                                    @if($latAkhir && $longAkhir)
                                        <div class="d-flex align-items-center gap-2 flex-wrap mt-1">
                                            <span class="mod-pill mod-pill-ok"><i class="feather-navigation me-1"></i>{{ $latAkhir }}, {{ $longAkhir }}</span>
                                            <a href="{{ $log->google_maps_url_akhir }}" target="_blank" class="btn-ptpn btn-ptpn-outline" style="padding:4px 10px;font-size:11px;">
                                                <i class="feather-external-link me-1"></i> Buka Peta
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

            {{-- Catatan --}}
            @if($log->catatan)
            <div class="detail-card">
                <div class="detail-card-header">
                    <i class="feather-message-square" style="color:#16a34a;font-size:17px;"></i>
                    <h3 class="detail-card-title">Catatan Lapangan</h3>
                </div>
                <div class="detail-card-body">
                    <div style="padding:14px;border-left:4px solid #16a34a;border-radius:10px;background:#f0fdf4;font-size:13.5px;color:#14532d;">
                        {{ $log->catatan }}
                    </div>
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
                    <h3 class="detail-card-title">Foto Sebelum Kerja</h3>
                </div>
                <div class="detail-card-body" style="padding:14px;">
                    @if($log->foto_sebelum_url)
                        <div class="photo-display-card">
                            <img src="{{ $log->foto_sebelum_url }}" class="photo-display-img" alt="Foto Sebelum" onclick="showPhoto('{{ $log->foto_sebelum_url }}', 'Foto Sebelum Jam Kerja')">
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
            <div class="detail-card mb-4">
                <div class="detail-card-header">
                    <i class="feather-camera" style="color:#16a34a;font-size:17px;"></i>
                    <h3 class="detail-card-title">Foto Sesudah Kerja</h3>
                </div>
                <div class="detail-card-body" style="padding:14px;">
                    @if($log->foto_sesudah_url)
                        <div class="photo-display-card">
                            <img src="{{ $log->foto_sesudah_url }}" class="photo-display-img" alt="Foto Sesudah" onclick="showPhoto('{{ $log->foto_sesudah_url }}', 'Foto Sesudah Jam Kerja')">
                        </div>
                    @else
                        <div class="text-center py-4" style="color:#9ca3af;">
                            <i class="feather-image d-block mb-2" style="font-size:36px;opacity:.4;"></i>
                            <span style="font-size:12px;">Foto Sesudah Belum Ada</span>
                        </div>
                    @endif
                </div>
            </div>

            @if($log->foto && $log->foto !== $log->foto_sebelum && $log->foto !== $log->foto_sesudah)
            <div class="detail-card">
                <div class="detail-card-header">
                    <i class="feather-image" style="color:#1d4ed8;font-size:17px;"></i>
                    <h3 class="detail-card-title">Foto Utama Lampiran</h3>
                </div>
                <div class="detail-card-body" style="padding:14px;">
                    <div class="photo-display-card">
                        <img src="{{ asset('gallery/' . $log->foto) }}" class="photo-display-img" alt="Foto Lampiran" onclick="showPhoto('{{ asset('gallery/' . $log->foto) }}', 'Foto Lampiran Utama')">
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
            imageWidth: 600,
            showConfirmButton: false,
            showCloseButton: true,
            customClass: { image: 'rounded-4' }
        });
    }
</script>
@endsection
