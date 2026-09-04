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
        <a href="{{ route('pengaliran.edit', $data->id_pengaliran) }}" class="btn-ptpn btn-ptpn-primary" style="padding:8px 14px;font-size:13px;">
            <i class="feather-edit-2" style="font-size:14px;"></i>
            Edit Data
        </a>
        @if(Auth::user()->isAdmin())
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

    .detail-hero-date {
        font-family: 'Outfit', sans-serif;
        font-size: clamp(22px,2.5vw+10px,32px);
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

    /* Quick stats in hero */
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

    /* Efficiency ring */
    .efficiency-display {
        font-family: 'Outfit', sans-serif;
        font-size: clamp(28px,4vw+14px,42px);
        font-weight: 900;
        color: #4ade80;
        line-height: 1;
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

    /* Dark mode */
    html.app-skin-dark .detail-card { background:#0a2317 !important;border-color:rgba(34,197,94,.15) !important; }
    html.app-skin-dark .detail-card-title { color:#d1fae5 !important; }
    html.app-skin-dark .detail-item { border-color:rgba(34,197,94,.07) !important; }
    html.app-skin-dark .detail-label { color:#6b8f72 !important; }
    html.app-skin-dark .detail-value { color:#d1fae5 !important; }
    html.app-skin-dark .keterangan-box { background:#0e3b26;border-color:#16a34a;color:#bbf7d0; }

    /* Responsive */
    @media (max-width: 767.98px) {
        .hero-stats-grid { grid-template-columns: repeat(2,1fr); gap:8px; }
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
        <div class="row align-items-start g-4">
            <div class="col-lg-7">
                <div class="detail-hero-pks-badge">
                    <i class="feather-home" style="font-size:15px;"></i>
                    PKS {{ $data->pks->akro ?? '—' }} — {{ $data->pks->nama ?? '' }}
                </div>
                <div class="detail-hero-date">
                    {{ \Carbon\Carbon::parse($data->tanggal)->locale('id')->translatedFormat('l, d F Y') }}
                </div>
                <div class="detail-hero-meta">
                    <div class="detail-hero-pill">
                        <i class="feather-clock" style="font-size:13px;color:#4ade80;"></i>
                        {{ $data->jam_mulai ? substr($data->jam_mulai,0,5) : '-' }} — {{ $data->jam_selesai ? substr($data->jam_selesai,0,5) : '-' }}
                    </div>
                    <div class="detail-hero-pill">
                        <i class="feather-map-pin" style="font-size:13px;color:#4ade80;"></i>
                        Blok {{ $data->blok ?? '-' }} | Bak {{ $data->no_bak ?? '-' }}
                    </div>
                    @if($data->rotasi)
                    <div class="detail-hero-pill">
                        <i class="feather-rotate-cw" style="font-size:13px;color:#4ade80;"></i>
                        Rotasi: {{ $data->rotasi }}
                    </div>
                    @endif
                </div>

                <div class="hero-stats-grid">
                    <div class="hero-stat-tile">
                        <div class="hero-stat-val" style="color:#4ade80;">{{ number_format($data->flat_bed ?? 0) }}</div>
                        <div class="hero-stat-lbl">Bed Dialirkan</div>
                    </div>
                    <div class="hero-stat-tile">
                        <div class="hero-stat-val">{{ number_format($data->vol_limbah_dialirkan) }}</div>
                        <div class="hero-stat-lbl">Vol. Dialirkan m³</div>
                    </div>
                    <div class="hero-stat-tile">
                        <div class="hero-stat-val" style="color:#93c5fd;">{{ number_format($data->vol_limbah_dihasilkan) }}</div>
                        <div class="hero-stat-lbl">Vol. Dihasilkan m³</div>
                    </div>
                    <div class="hero-stat-tile">
                        <div class="hero-stat-val" style="color:#fde68a;">{{ number_format($data->luas_area ?? 0, 1) }}</div>
                        <div class="hero-stat-lbl">Luas Area Ha</div>
                    </div>
                </div>
            </div>

            {{-- Right: Efficiency --}}
            <div class="col-lg-5">
                <div style="background:rgba(255,255,255,.08);border:1px solid rgba(255,255,255,.12);border-radius:16px;padding:20px;text-align:center;backdrop-filter:blur(10px);">
                    <div style="font-size:11px;font-weight:700;color:rgba(187,247,208,.7);text-transform:uppercase;letter-spacing:.6px;margin-bottom:10px;">
                        Efisiensi Pengaliran
                    </div>
                    <div class="efficiency-display">{{ $efisiensi }}%</div>
                    <div style="margin:12px auto 14px;height:8px;border-radius:8px;background:rgba(255,255,255,.12);max-width:200px;overflow:hidden;">
                        <div style="height:100%;width:{{ $efisiensi }}%;border-radius:8px;background:linear-gradient(90deg,#4ade80,#86efac);transition:width 1s ease;"></div>
                    </div>
                    <div style="font-size:11.5px;color:rgba(187,247,208,.7);">
                        Vol. Dialirkan / Vol. Dihasilkan
                    </div>
                    <div style="margin-top:12px;padding:10px;border-radius:10px;background:rgba(34,197,94,.15);border:1px solid rgba(34,197,94,.25);">
                        @if($data->vol_limbah_dialirkan > 0)
                            <span style="color:#86efac;font-size:12.5px;font-weight:800;">
                                <i class="feather-check-circle me-1"></i> Pengaliran Aktif &amp; Berjalan
                            </span>
                        @else
                            <span style="color:#fca5a5;font-size:12.5px;font-weight:800;">
                                <i class="feather-alert-circle me-1"></i> Belum Ada Volume
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

            {{-- Status Kesesuaian Izin LA --}}
            <div class="detail-card mb-4" style="border-left: 4px solid {{ $data->isDiLuarIzin() ? '#f59e0b' : '#16a34a' }};">
                <div class="detail-card-header" style="background: {{ $data->isDiLuarIzin() ? '#fffbeb' : '#f0fdf4' }};">
                    <i class="feather-{{ $data->isDiLuarIzin() ? 'alert-triangle' : 'shield' }}"
                       style="color:{{ $data->isDiLuarIzin() ? '#d97706' : '#16a34a' }};font-size:18px;"></i>
                    <h3 class="detail-card-title" style="color:{{ $data->isDiLuarIzin() ? '#92400e' : '#14532d' }};">
                        Status Kepatuhan Izin Land Application
                    </h3>
                    <span class="badge {{ $data->isDiLuarIzin() ? 'bg-warning text-dark' : 'bg-success' }} ms-auto"
                          style="font-size:11.5px;font-weight:800;padding:6px 12px;border-radius:50px;">
                        {{ $data->kesesuaian_izin ?? 'Sesuai Izin' }}
                    </span>
                </div>
                <div class="detail-card-body">
                    @if($data->isDiLuarIzin())
                        <div class="alert alert-warning d-flex align-items-start gap-2 mb-0" style="border-radius:12px;background:#fef3c7;border:1px solid #fde68a;">
                            <i class="feather-alert-circle text-warning fs-5 flex-shrink-0 mt-0.5"></i>
                            <div>
                                <strong class="d-block mb-1 text-dark">Alasan Pengaliran di Luar Izin:</strong>
                                <span class="text-dark" style="font-size:13.5px;line-height:1.5;">
                                    {{ $data->alasan_tidak_sesuai_izin ?: 'Tidak ada alasan terlampir.' }}
                                </span>
                            </div>
                        </div>
                    @else
                        <div class="d-flex align-items-center gap-2 text-success" style="font-size:13px;font-weight:600;">
                            <i class="feather-check-circle" style="font-size:16px;"></i>
                            <span>Pengaliran pada Blok <strong>{{ $data->blok }}</strong> (Bak {{ $data->no_bak }}) terdaftar resmi dalam Surat Izin &amp; Peta Land Application PKS.</span>
                        </div>
                    @endif
                </div>
            </div>

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
                                <div class="detail-value">{{ \Carbon\Carbon::parse($data->tanggal)->locale('id')->translatedFormat('dddd, d MMMM Y') }}</div>
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
                                <div class="detail-value">
                                    <span class="pks-badge" style="display:inline-flex;align-items:center;gap:5px;padding:4px 10px;border-radius:8px;font-size:11px;font-weight:800;background:linear-gradient(135deg,#052e16,#166534);color:#86efac;">
                                        {{ $data->pks->akro ?? 'N/A' }}
                                    </span>
                                    <span style="margin-left:8px;font-size:13px;font-weight:600;color:#374151;">{{ $data->pks->nama ?? '' }}</span>
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
                                <span class="detail-label">Bed di alirkan</span>
                                <div class="detail-value" style="font-family:'Outfit',sans-serif;font-size:22px;font-weight:900;color:#16a34a;">
                                    {{ number_format($data->flat_bed) }} <span style="font-size:13px;font-weight:600;color:#6b7280;">Bed</span>
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
                                <span class="detail-label">Volume Dihasilkan</span>
                                <div class="detail-value" style="font-size:18px;font-weight:800;color:#0d9488;">
                                    {{ number_format($data->vol_limbah_dihasilkan) }} <span style="font-size:12px;font-weight:600;color:#6b7280;">m³</span>
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

        {{-- RIGHT: Waktu + Foto --}}
        <div class="col-lg-4">

            {{-- Waktu Ringkas --}}
            <div class="detail-card mb-4">
                <div class="detail-card-header">
                    <i class="feather-calendar" style="color:#16a34a;font-size:17px;"></i>
                    <h3 class="detail-card-title">Informasi Waktu</h3>
                </div>
                <div class="detail-card-body">
                    <div class="detail-item">
                        <span class="detail-label">Hari Pengaliran</span>
                        <div class="detail-value">{{ \Carbon\Carbon::parse($data->tanggal)->locale('id')->translatedFormat('dddd') }}</div>
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