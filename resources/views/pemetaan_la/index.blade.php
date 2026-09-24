@extends('layouts.simoli')

@section('title', 'Arsip & Pratinjau Peta Land Application')
@section('page-title', 'Arsip & Pratinjau Peta Land Application')
@section('page-description', 'Pratinjau Peta Terkini Tiap PKS & Pusat Pengarsipan Dokumen Peta Land Application')

@section('breadcrumb')
    <li>Arsip & Legalitas</li>
    <li class="separator">/</li>
    <li>Arsip Peta LA</li>
@endsection

@section('page-actions')
    <div class="d-flex gap-2 flex-wrap">
        @if(Auth::user()->isAdmin())
        <div class="dropdown">
            <button class="btn-ptpn btn-ptpn-outline dropdown-toggle d-inline-flex align-items-center gap-1.5" type="button" data-bs-toggle="dropdown" aria-expanded="false" style="border-color:#dc2626;color:#dc2626;">
                <i class="feather-lock text-danger" style="font-size:15px;"></i>
                <span>Kunci / Buka Semua</span>
            </button>
            <ul class="dropdown-menu dropdown-menu-end shadow border-0" style="border-radius:12px;font-size:13px;min-width:260px;">
                <li><h6 class="dropdown-header text-uppercase fw-bold text-muted" style="font-size:11px;">Kontrol Arsip Peta LA</h6></li>
                <li>
                    <form action="{{ route('pemetaan-la.bulk-lock') }}" method="POST" class="m-0 p-0" onsubmit="return confirm('Apakah Anda yakin ingin MENGUNCI SEMUA data arsip peta untuk SEMUA UNIT? Unit PKS tidak akan dapat mengedit arsip peta.');">
                        @csrf
                        <input type="hidden" name="action" value="lock">
                        <input type="hidden" name="id_pks" value="all">
                        <button type="submit" class="dropdown-item d-flex align-items-center gap-2 py-2 text-danger fw-semibold">
                            <i class="feather-lock text-danger"></i>
                            <span>Kunci Semua Peta (Semua Unit)</span>
                        </button>
                    </form>
                </li>
                <li>
                    <form action="{{ route('pemetaan-la.bulk-lock') }}" method="POST" class="m-0 p-0" onsubmit="return confirm('Apakah Anda yakin ingin MEMBUKA KUNCI SEMUA data arsip peta untuk SEMUA UNIT?');">
                        @csrf
                        <input type="hidden" name="action" value="unlock">
                        <input type="hidden" name="id_pks" value="all">
                        <button type="submit" class="dropdown-item d-flex align-items-center gap-2 py-2 text-success fw-semibold">
                            <i class="feather-unlock text-success"></i>
                            <span>Buka Kunci Semua Peta</span>
                        </button>
                    </form>
                </li>
                <li><hr class="dropdown-divider"></li>
                <li><h6 class="dropdown-header text-uppercase fw-bold text-muted" style="font-size:11px;">Kontrol Global (Peta &amp; SK Izin)</h6></li>
                <li>
                    <form action="{{ route('pemetaan-la.bulk-lock-all') }}" method="POST" class="m-0 p-0" onsubmit="return confirm('PERINGATAN: Apakah Anda yakin ingin MENGUNCI SEMUA data Arsip Peta DAN Dokumen SK Izin untuk SEMUA UNIT sekaligus?');">
                        @csrf
                        <input type="hidden" name="action" value="lock">
                        <button type="submit" class="dropdown-item d-flex align-items-center gap-2 py-2 text-danger fw-bold">
                            <i class="feather-shield text-danger"></i>
                            <span>Kunci Semua Peta &amp; Izin (Global)</span>
                        </button>
                    </form>
                </li>
                <li>
                    <form action="{{ route('pemetaan-la.bulk-lock-all') }}" method="POST" class="m-0 p-0" onsubmit="return confirm('Apakah Anda yakin ingin MEMBUKA KUNCI SEMUA data Arsip Peta DAN Dokumen SK Izin untuk SEMUA UNIT?');">
                        @csrf
                        <input type="hidden" name="action" value="unlock">
                        <button type="submit" class="dropdown-item d-flex align-items-center gap-2 py-2 text-success fw-bold">
                            <i class="feather-check-circle text-success"></i>
                            <span>Buka Semua Peta &amp; Izin (Global)</span>
                        </button>
                    </form>
                </li>
            </ul>
        </div>
        @endif

        <a href="{{ route('perizinan-la.index') }}" class="btn-ptpn btn-ptpn-outline">
            <i class="feather-file-text" style="font-size:15px;"></i>
            <span>Arsip SK Izin LA</span>
        </a>
        <a href="{{ route('pemetaan-la.create') }}" class="btn-ptpn btn-ptpn-primary">
            <i class="feather-upload-cloud" style="font-size:15px;"></i>
            <span>Unggah Arsip Peta</span>
        </a>
    </div>
@endsection

@section('styles')
<style>
    /* ================================================================
       KPI SUMMARY STAT CARDS (PTPN CONSISTENT DESIGN)
       ================================================================ */
    @keyframes fadeUpCard {
        from { opacity: 0; transform: translateY(12px); }
        to   { opacity: 1; transform: translateY(0); }
    }

    .peta-kpi {
        background: #ffffff;
        border-radius: 18px;
        border: 1px solid rgba(22,163,74,.12);
        box-shadow: 0 2px 12px rgba(22,163,74,.05);
        padding: 18px 20px;
        height: 100%;
        transition: all .25s ease;
        position: relative;
        overflow: hidden;
        animation: fadeUpCard .4s ease-out both;
    }
    .peta-kpi::after {
        content: '';
        position: absolute;
        top: 0; left: 0; right: 0;
        height: 3.5px;
        border-radius: 18px 18px 0 0;
    }
    .peta-kpi-green::after  { background: linear-gradient(90deg, #16a34a, #4ade80); }
    .peta-kpi-blue::after   { background: linear-gradient(90deg, #1d4ed8, #60a5fa); }
    .peta-kpi-red::after    { background: linear-gradient(90deg, #dc2626, #f87171); }
    .peta-kpi-teal::after   { background: linear-gradient(90deg, #0d9488, #2dd4bf); }
    .peta-kpi:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 24px rgba(22,163,74,.12);
    }

    .peta-kpi-label {
        font-size: 11px;
        font-weight: 700;
        color: #6b7280;
        text-transform: uppercase;
        letter-spacing: .5px;
        margin-bottom: 4px;
    }
    .peta-kpi-val {
        font-family: 'Outfit', sans-serif;
        font-size: 24px;
        font-weight: 800;
        line-height: 1.1;
        margin-bottom: 4px;
    }
    .peta-kpi-sub {
        font-size: 11.5px;
        color: #6b7280;
    }
    .peta-kpi-icon {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 19px;
        flex-shrink: 0;
    }
    .kpi-icon-g { background: #f0fdf4; color: #16a34a; border: 1px solid #bbf7d0; }
    .kpi-icon-b { background: #eff6ff; color: #1d4ed8; border: 1px solid #bfdbfe; }
    .kpi-icon-r { background: #fef2f2; color: #dc2626; border: 1px solid #fca5a5; }
    .kpi-icon-t { background: #f0fdfa; color: #0d9488; border: 1px solid #99f6e4; }

    /* ================================================================
       PREVIEW PETA TERKINI (HERO CARD)
       ================================================================ */
    .hero-map-card {
        background: #ffffff;
        border-radius: 20px;
        border: 1px solid rgba(22,163,74,.2);
        box-shadow: 0 4px 20px rgba(22,163,74,.08);
        overflow: hidden;
        margin-bottom: 30px;
        animation: fadeUpCard .4s ease-out;
    }

    .hero-map-header {
        padding: 16px 22px;
        background: linear-gradient(90deg, #052e16 0%, #14532d 50%, #166534 100%);
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 12px;
    }

    .hero-map-title {
        font-family: 'Outfit', sans-serif;
        font-size: 15px;
        font-weight: 800;
        color: #86efac;
        display: flex;
        align-items: center;
        gap: 8px;
        margin: 0;
    }

    .hero-map-viewer-wrap {
        height: 520px;
        background: #1e293b;
        position: relative;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
    }

    .hero-map-img {
        max-width: 100%;
        max-height: 100%;
        object-fit: contain;
        transition: transform .3s ease;
    }

    .hero-map-meta-panel {
        padding: 24px;
        background: #ffffff;
        height: 100%;
        display: flex;
        flex-direction: column;
    }

    .badge-terkini {
        background: linear-gradient(135deg, #16a34a, #22c55e);
        color: #ffffff;
        font-size: 11.5px;
        font-weight: 800;
        padding: 5px 12px;
        border-radius: 50px;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        box-shadow: 0 2px 8px rgba(34,197,94,.3);
    }

    /* ================================================================
       FILTER & VIEW CONTROLS
       ================================================================ */
    .filter-peta-card {
        background: #ffffff;
        border-radius: 16px;
        border: 1px solid rgba(22,163,74,.15);
        box-shadow: 0 2px 10px rgba(22,163,74,.05);
        padding: 16px 20px;
        margin-bottom: 24px;
    }

    .filter-peta-card .form-control,
    .filter-peta-card .form-select {
        border-radius: 10px;
        border: 1.5px solid #e5e7eb;
        font-size: 13px;
        padding: 8px 12px;
        transition: all .2s ease;
        background: #f9fafb;
    }

    .filter-peta-card .form-control:focus,
    .filter-peta-card .form-select:focus {
        border-color: rgba(22,163,74,.5);
        background: #ffffff;
        box-shadow: 0 0 0 3px rgba(34,197,94,.08);
    }

    .view-toggle-group {
        display: inline-flex;
        background: #f1f5f9;
        padding: 3px;
        border-radius: 10px;
        border: 1px solid #e2e8f0;
    }

    .view-toggle-btn {
        padding: 5px 12px;
        border-radius: 8px;
        border: none;
        background: transparent;
        color: #64748b;
        font-size: 12.5px;
        font-weight: 700;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: all .2s ease;
    }

    .view-toggle-btn.active {
        background: #ffffff;
        color: #16a34a;
        box-shadow: 0 2px 6px rgba(0,0,0,.08);
    }

    /* ================================================================
       GALLERY CARDS
       ================================================================ */
    .map-card {
        background: #ffffff;
        border-radius: 16px;
        border: 1px solid rgba(22,163,74,.15);
        box-shadow: 0 2px 12px rgba(22,163,74,.05);
        transition: all .25s ease;
        display: flex;
        flex-direction: column;
        height: 100%;
        overflow: hidden;
    }
    .map-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 10px 25px rgba(22,163,74,.12);
        border-color: rgba(22,163,74,.35);
    }
    .map-thumb-wrap {
        height: 170px;
        background: #f1f5f9;
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
        overflow: hidden;
        border-bottom: 1px solid #e2e8f0;
    }
    .map-thumb-img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform .3s ease;
    }
    .map-card:hover .map-thumb-img {
        transform: scale(1.05);
    }
    .map-thumb-placeholder {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        color: #94a3b8;
    }
    .badge-category {
        position: absolute;
        top: 10px;
        left: 10px;
        background: rgba(15, 23, 42, 0.82);
        backdrop-filter: blur(6px);
        color: #ffffff;
        font-size: 10.5px;
        font-weight: 700;
        padding: 4px 9px;
        border-radius: 50px;
        z-index: 2;
        box-shadow: 0 2px 6px rgba(0,0,0,.2);
    }

    /* ================================================================
       TABLE RIWAYAT PETA
       ================================================================ */
    .table-peta-wrapper {
        background: #ffffff;
        border-radius: 16px;
        border: 1px solid rgba(22,163,74,.15);
        box-shadow: 0 2px 12px rgba(22,163,74,.05);
        overflow: hidden;
        margin-bottom: 24px;
    }
    .table-peta {
        margin-bottom: 0;
    }
    .table-peta thead th {
        background: #f8fafc;
        color: #475569;
        font-size: 11.5px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: .5px;
        padding: 13px 16px;
        border-bottom: 1.5px solid #e2e8f0;
        white-space: nowrap;
    }
    .table-peta tbody td {
        padding: 13px 16px;
        font-size: 13px;
        color: #334155;
        border-bottom: 1px solid #f1f5f9;
        vertical-align: middle;
    }
    .table-peta tbody tr:hover {
        background: #f0fdf4;
    }

    /* ================================================================
       DARK MODE OVERRIDES
       ================================================================ */
    html.app-skin-dark .peta-kpi,
    html.app-skin-dark .hero-map-card,
    html.app-skin-dark .hero-map-meta-panel,
    html.app-skin-dark .filter-peta-card,
    html.app-skin-dark .map-card,
    html.app-skin-dark .table-peta-wrapper {
        background: #0a2317 !important;
        border-color: rgba(34,197,94,.18) !important;
        color: #f1f5f9 !important;
    }
    html.app-skin-dark .kpi-icon-g { background: #052e16 !important; border-color: rgba(34,197,94,.3) !important; color: #86efac !important; }
    html.app-skin-dark .kpi-icon-b { background: #082f49 !important; border-color: rgba(56,189,248,.3) !important; color: #7dd3fc !important; }
    html.app-skin-dark .kpi-icon-r { background: #450a0a !important; border-color: rgba(248,113,113,.3) !important; color: #fca5a5 !important; }
    html.app-skin-dark .kpi-icon-t { background: #042f2e !important; border-color: rgba(45,212,191,.3) !important; color: #99f6e4 !important; }
    
    html.app-skin-dark .filter-peta-card .form-control,
    html.app-skin-dark .filter-peta-card .form-select {
        background: #052e16 !important;
        border-color: rgba(34,197,94,.25) !important;
        color: #f1f5f9 !important;
    }
    html.app-skin-dark .view-toggle-group {
        background: #052e16 !important;
        border-color: rgba(34,197,94,.2) !important;
    }
    html.app-skin-dark .view-toggle-btn {
        color: #86efac !important;
    }
    html.app-skin-dark .view-toggle-btn.active {
        background: #166534 !important;
        color: #ffffff !important;
    }
    html.app-skin-dark .map-thumb-wrap {
        background: #052e16 !important;
        border-color: rgba(34,197,94,.2) !important;
    }
    html.app-skin-dark .table-peta thead th {
        background: #052e16 !important;
        color: #86efac !important;
        border-bottom-color: rgba(34,197,94,.25) !important;
    }
    html.app-skin-dark .table-peta tbody td {
        color: #e2e8f0 !important;
        border-bottom-color: rgba(34,197,94,.12) !important;
    }
    html.app-skin-dark .table-peta tbody tr:hover {
        background: rgba(34,197,94,.08) !important;
    }
</style>
@endsection

@section('content')

{{-- ================================================================
     1. KPI RINGKASAN ARSIP PETA
     ================================================================ --}}
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="peta-kpi peta-kpi-green">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <div class="peta-kpi-label">Total Arsip Peta</div>
                <div class="peta-kpi-icon kpi-icon-g">
                    <i class="feather-map"></i>
                </div>
            </div>
            <div class="peta-kpi-val text-success">{{ $totalPeta }}</div>
            <div class="peta-kpi-sub">Total berkas peta tersimpan</div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="peta-kpi peta-kpi-blue">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <div class="peta-kpi-label">Format Gambar / Visual</div>
                <div class="peta-kpi-icon kpi-icon-b">
                    <i class="feather-image"></i>
                </div>
            </div>
            <div class="peta-kpi-val text-primary">{{ $totalGambar }}</div>
            <div class="peta-kpi-sub">Berkas JPG, PNG, WEBP</div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="peta-kpi peta-kpi-red">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <div class="peta-kpi-label">Dokumen Peta PDF</div>
                <div class="peta-kpi-icon kpi-icon-r">
                    <i class="feather-file-text"></i>
                </div>
            </div>
            <div class="peta-kpi-val text-danger">{{ $totalPdf }}</div>
            <div class="peta-kpi-sub">Dokumen PDF resolusi tinggi</div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="peta-kpi peta-kpi-teal">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <div class="peta-kpi-label">Berkas SIG &amp; Spasial</div>
                <div class="peta-kpi-icon kpi-icon-t">
                    <i class="feather-layers"></i>
                </div>
            </div>
            <div class="peta-kpi-val text-info">{{ $totalSpasial }}</div>
            <div class="peta-kpi-sub">Data Shapefile, GeoJSON &amp; CAD</div>
        </div>
    </div>
</div>

{{-- ================================================================
     2. HERO PRATINJAU PETA TERKINI (BERDASARKAN TAHUN PALING BARU)
     ================================================================ --}}
<div class="hero-map-card">
    <div class="hero-map-header">
        <div class="d-flex align-items-center gap-2 flex-wrap">
            <h4 class="hero-map-title">
                <i class="feather-map-pin" style="font-size:18px;"></i>
                <span>Pratinjau Peta Terkini — {{ $pksAktif ? $pksAktif->nama : 'PKS' }}</span>
            </h4>
            @if($petaTerbaru)
                <span class="badge-terkini">
                    <i class="feather-star" style="font-size:12px;"></i> Tahun {{ $petaTerbaru->tahun_peta ?: date('Y') }}
                </span>
            @endif
        </div>

        {{-- Selector PKS (untuk Admin) atau Badge Unit (untuk Non-Admin) --}}
        <div>
            @if(Auth::user()->isAdmin())
                <form method="GET" action="{{ route('pemetaan-la.index') }}" id="pksFilterForm" class="d-flex align-items-center gap-2 m-0">
                    <span style="font-size:11.5px;color:#86efac;font-weight:700;white-space:nowrap;">Pilih Unit PKS:</span>
                    <select name="id_pks" class="form-select form-select-sm" style="min-width:210px;font-weight:700;border-radius:8px;" onchange="document.getElementById('pksFilterForm').submit();">
                        @foreach($daftarPks as $pks)
                            <option value="{{ $pks->id_pks }}" {{ $selectedPksId == $pks->id_pks ? 'selected' : '' }}>
                                {{ $pks->nama }} ({{ $pks->akro ?? $pks->kode }})
                            </option>
                        @endforeach
                    </select>
                </form>
            @else
                <span class="badge bg-light text-dark px-3 py-2 rounded-pill font-12" style="font-weight:800;">
                    <i class="feather-home text-success"></i> {{ $pksAktif ? $pksAktif->nama : 'PKS Saya' }}
                </span>
            @endif
        </div>
    </div>

    @if($petaTerbaru)
    <div class="row g-0">
        {{-- Viewer Peta Terkini (Col 8) --}}
        <div class="col-lg-8">
            <div class="hero-map-viewer-wrap">
                @if($petaTerbaru->is_pdf)
                    <iframe src="{{ asset('uploads/peta_la/' . $petaTerbaru->file_peta) }}" width="100%" height="100%" style="border:none;"></iframe>
                @elseif($petaTerbaru->is_image)
                    <img src="{{ asset('uploads/peta_la/' . $petaTerbaru->file_peta) }}" alt="{{ $petaTerbaru->nama_peta }}" class="hero-map-img">
                @else
                    <div class="text-center text-white p-4">
                        <i class="feather-file" style="font-size:54px;opacity:.6;margin-bottom:12px;"></i>
                        <h5>Berkas Spasial {{ strtoupper($petaTerbaru->tipe_file) }}</h5>
                        <p style="opacity:.8;font-size:13px;">Format ini dapat diunduh untuk dibuka pada software SIG seperti QGIS atau ArcGIS.</p>
                        <a href="{{ asset('uploads/peta_la/' . $petaTerbaru->file_peta) }}" download class="btn btn-light btn-sm mt-2">
                            <i class="feather-download"></i> Unduh Berkas
                        </a>
                    </div>
                @endif
            </div>
        </div>

        {{-- Panel Informasi Peta Terkini (Col 4) --}}
        <div class="col-lg-4">
            <div class="hero-map-meta-panel">
                <div class="mb-3">
                    <span class="badge bg-success-subtle text-success border border-success-subtle px-2.5 py-1 rounded-pill mb-2" style="font-size:11.5px;font-weight:700;">
                        {{ $petaTerbaru->kategori_peta }}
                    </span>
                    <h4 style="font-family:'Outfit',sans-serif;font-weight:800;color:#1f2937;margin-bottom:6px;line-height:1.3;font-size:18px;">
                        {{ $petaTerbaru->nama_peta }}
                    </h4>
                    <div class="text-muted" style="font-size:12.5px;">
                        <i class="feather-calendar text-success"></i> Tahun Pembuatan: <strong>{{ $petaTerbaru->tahun_peta ?: '-' }}</strong>
                    </div>
                </div>

                <div class="bg-light rounded-3 p-3 mb-3" style="font-size:12.5px;color:#374151;">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Unit PKS:</span>
                        <strong>{{ $petaTerbaru->pks ? $petaTerbaru->pks->nama : 'PKS' }}</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Format Berkas:</span>
                        <strong class="text-uppercase">{{ $petaTerbaru->tipe_file ?: '-' }}</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Ukuran Berkas:</span>
                        <strong>{{ $petaTerbaru->formatted_size }}</strong>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="text-muted">Tanggal Diunggah:</span>
                        <strong>{{ $petaTerbaru->created_at ? $petaTerbaru->created_at->format('d/m/Y') : '-' }}</strong>
                    </div>
                </div>

                @if($petaTerbaru->keterangan)
                <div class="mb-3 flex-grow-1">
                    <span class="text-muted d-block" style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.3px;">Keterangan Teknis:</span>
                    <p style="font-size:12.5px;color:#4b5563;line-height:1.45;margin:4px 0 0 0;">
                        {{ $petaTerbaru->keterangan }}
                    </p>
                </div>
                @else
                <div class="flex-grow-1"></div>
                @endif

                <div class="d-grid gap-2 mt-auto pt-3 border-top">
                    <a href="{{ asset('uploads/peta_la/' . $petaTerbaru->file_peta) }}" target="_blank" class="btn btn-outline-success">
                        <i class="feather-maximize-2"></i> Buka Ukuran Penuh
                    </a>
                    <a href="{{ asset('uploads/peta_la/' . $petaTerbaru->file_peta) }}" download class="btn btn-ptpn btn-ptpn-primary justify-content-center">
                        <i class="feather-download"></i> Unduh Peta Terkini
                    </a>
                </div>
            </div>
        </div>
    </div>
    @else
    {{-- Empty state jika belum ada peta untuk PKS ini --}}
    <div class="text-center py-5 px-3">
        <div style="font-size:48px;color:#86efac;margin-bottom:12px;">
            <i class="feather-map-pin"></i>
        </div>
        <h5 style="font-family:'Outfit',sans-serif;font-weight:800;color:#374151;">Belum Ada Berkas Peta untuk {{ $pksAktif ? $pksAktif->nama : 'PKS Ini' }}</h5>
        <p style="font-size:13px;color:#6b7280;max-width:420px;margin:0 auto 20px auto;">
            Unit PKS ini belum memiliki berkas peta yang diunggah. Silakan klik tombol di bawah untuk mengunggah berkas peta pertama.
        </p>
        <a href="{{ route('pemetaan-la.create') }}" class="btn-ptpn btn-ptpn-primary" style="display:inline-flex;">
            <i class="feather-upload-cloud"></i> Unggah Peta untuk {{ $pksAktif ? $pksAktif->akro ?? $pksAktif->nama : 'PKS' }}
        </a>
    </div>
    @endif
</div>


{{-- ================================================================
     3. DAFTAR & RIWAYAT SELURUH ARSIP PETA
     ================================================================ --}}
<div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
    <div>
        <h4 style="font-family:'Outfit',sans-serif;font-weight:800;font-size:18px;color:#1f2937;margin:0;">
            <i class="feather-folder text-success"></i> Riwayat &amp; Galeri Seluruh Arsip Peta
        </h4>
        <small class="text-muted">Daftar seluruh versi dan kategori dokumen peta yang tersimpan di sistem</small>
    </div>
    <div class="d-flex align-items-center gap-2">
        <span class="badge bg-light text-dark border px-3 py-2 rounded-pill font-12" style="font-weight:700;">
            Total: {{ $totalPeta }} Berkas Peta
        </span>
        {{-- View Switcher Buttons --}}
        <div class="view-toggle-group">
            <button type="button" class="view-toggle-btn active" id="btnViewGrid" onclick="switchView('grid')" title="Tampilan Galeri Kartu">
                <i class="feather-grid"></i> Galeri
            </button>
            <button type="button" class="view-toggle-btn" id="btnViewTable" onclick="switchView('table')" title="Tampilan Tabel Riwayat">
                <i class="feather-list"></i> Tabel
            </button>
        </div>
    </div>
</div>

{{-- Filter & Pencarian Arsip Peta --}}
<div class="filter-peta-card">
    <form method="GET" action="{{ route('pemetaan-la.index') }}" class="row g-2 align-items-center">
        <div class="col-md-3">
            <div class="input-group">
                <span class="input-group-text bg-light border-end-0"><i class="feather-search text-muted"></i></span>
                <input type="text" name="search" class="form-control border-start-0" placeholder="Cari nama peta / deskripsi..." value="{{ request('search') }}">
            </div>
        </div>
        
        @if(Auth::user()->isAdmin())
        <div class="col-md-3">
            <select name="id_pks" class="form-select">
                <option value="">-- Semua Unit PKS --</option>
                @foreach($daftarPks as $pks)
                    <option value="{{ $pks->id_pks }}" {{ request('id_pks') == $pks->id_pks ? 'selected' : '' }}>
                        {{ $pks->nama }} ({{ $pks->akro ?? $pks->kode }})
                    </option>
                @endforeach
            </select>
        </div>
        @endif

        <div class="col-md-{{ Auth::user()->isAdmin() ? '2' : '3' }}">
            <select name="kategori" class="form-select">
                <option value="">-- Semua Kategori --</option>
                @foreach($kategoriList as $kat)
                    <option value="{{ $kat }}" {{ request('kategori') == $kat ? 'selected' : '' }}>{{ $kat }}</option>
                @endforeach
            </select>
        </div>

        <div class="col-md-{{ Auth::user()->isAdmin() ? '2' : '3' }}">
            <select name="format" class="form-select">
                <option value="">-- Format Berkas --</option>
                <option value="image" {{ request('format') == 'image' ? 'selected' : '' }}>Gambar (JPG, PNG)</option>
                <option value="pdf" {{ request('format') == 'pdf' ? 'selected' : '' }}>Dokumen PDF</option>
                <option value="spasial" {{ request('format') == 'spasial' ? 'selected' : '' }}>Spasial / SIG / ZIP</option>
            </select>
        </div>

        <div class="col-md-2 d-flex gap-2">
            <button type="submit" class="btn-ptpn btn-ptpn-primary w-100 justify-content-center">
                <i class="feather-filter"></i> Filter
            </button>
            @if(request()->hasAny(['search', 'id_pks', 'kategori', 'format']))
                <a href="{{ route('pemetaan-la.index') }}" class="btn btn-light border px-3" title="Reset Filter">
                    <i class="feather-refresh-cw"></i>
                </a>
            @endif
        </div>
    </form>
</div>

@if($petaList->count() > 0)
    {{-- ================================================================
         VIEW 1: MODE GALERI KARTU (GRID VIEW)
         ================================================================ --}}
    <div id="containerGridView" class="row g-4 mb-4">
        @foreach($petaList as $peta)
        <div class="col-md-6 col-xl-4">
            <div class="map-card">
                <div class="map-thumb-wrap">
                    <span class="badge-category">{{ $peta->kategori_peta }}</span>
                    @if($peta->is_locked)
                        <span class="badge bg-danger text-white" style="position:absolute;top:10px;right:10px;font-size:10px;font-weight:800;border-radius:50px;padding:4px 9px;z-index:3;box-shadow:0 2px 6px rgba(0,0,0,.2);">
                            <i class="feather-lock"></i> Terkunci
                        </span>
                    @elseif($petaTerbaru && $peta->id == $petaTerbaru->id)
                        <span class="badge bg-success" style="position:absolute;top:10px;right:10px;font-size:10px;font-weight:800;border-radius:50px;padding:4px 9px;z-index:2;box-shadow:0 2px 6px rgba(0,0,0,.2);">
                            🌟 Peta Terkini (Aktif)
                        </span>
                    @endif

                    @if($peta->is_image)
                        <img src="{{ asset('uploads/peta_la/' . $peta->file_peta) }}" alt="{{ $peta->nama_peta }}" class="map-thumb-img">
                    @elseif($peta->is_pdf)
                        <div class="map-thumb-placeholder text-center">
                            <i class="feather-file-text text-danger" style="font-size:54px;margin-bottom:8px;display:inline-block;"></i>
                            <span style="font-size:11.5px;font-weight:800;color:#64748b;letter-spacing:.3px;">DOKUMEN PETA PDF</span>
                        </div>
                    @else
                        <div class="map-thumb-placeholder text-center">
                            <i class="feather-map text-success" style="font-size:54px;margin-bottom:8px;display:inline-block;"></i>
                            <span style="font-size:11.5px;font-weight:800;color:#64748b;letter-spacing:.3px;">BERKAS SPASIAL / DATA</span>
                        </div>
                    @endif
                </div>

                <div class="p-3 d-flex flex-column flex-grow-1">
                    <div class="mb-2">
                        <div class="d-flex align-items-center justify-content-between flex-wrap gap-1 mb-1">
                            <span class="badge bg-light text-dark border" style="font-size:11px;font-weight:700;">
                                <i class="feather-home text-success"></i> {{ $peta->pks ? $peta->pks->nama : 'Unit PKS' }}
                            </span>
                            @if($peta->is_locked)
                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-0.5" style="font-size:10px;font-weight:700;border-radius:50px;">
                                    <i class="feather-lock"></i> Dikunci Admin
                                </span>
                            @endif
                        </div>
                        <h5 style="font-family:'Outfit',sans-serif;font-weight:800;font-size:15px;color:#1f2937;margin-bottom:4px;line-height:1.3;">
                            {{ $peta->nama_peta }}
                        </h5>
                        @if($peta->keterangan)
                        <p style="font-size:12px;color:#64748b;margin-bottom:8px;line-height:1.4;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;">
                            {{ $peta->keterangan }}
                        </p>
                        @endif
                    </div>

                    <div class="bg-light rounded-3 p-2.5 mb-3 mt-auto" style="font-size:11.5px;color:#374151;">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="text-muted">Tahun Pembuatan:</span>
                            <strong>{{ $peta->tahun_peta ?: '-' }}</strong>
                        </div>
                        <div class="d-flex justify-content-between mb-1">
                            <span class="text-muted">Tipe File:</span>
                            <strong class="text-uppercase">{{ $peta->tipe_file ?: '-' }}</strong>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">Ukuran Berkas:</span>
                            <strong>{{ $peta->formatted_size }}</strong>
                        </div>
                    </div>

                    <div class="pt-2.5 mt-auto border-top d-flex align-items-center justify-content-between gap-2 flex-wrap">
                        <div class="d-flex align-items-center gap-2 flex-nowrap">
                            <a href="{{ asset('uploads/peta_la/' . $peta->file_peta) }}" target="_blank" class="btn btn-sm btn-outline-success d-inline-flex align-items-center gap-1.5 px-3 py-1.5 fw-bold" style="border-radius:9px;font-size:12.5px;">
                                <i class="feather-eye" style="font-size:14px;"></i> Buka Peta
                            </a>
                            <a href="{{ asset('uploads/peta_la/' . $peta->file_peta) }}" download class="btn btn-sm btn-light border text-muted d-inline-flex align-items-center justify-content-center px-2.5 py-1.5" style="border-radius:9px;font-size:12.5px;" title="Unduh File">
                                <i class="feather-download" style="font-size:14px;"></i>
                            </a>
                        </div>

                        <div class="d-flex align-items-center gap-1 flex-nowrap">
                            <a href="{{ route('pemetaan-la.show', $peta->id) }}" class="btn btn-sm btn-light border text-secondary d-inline-flex align-items-center justify-content-center px-2.5 py-1.5" title="Detail Arsip" style="border-radius:9px;">
                                <i class="feather-info" style="font-size:14.5px;"></i>
                            </a>

                            {{-- Edit: Admin atau Unit jika tidak terkunci --}}
                            @if(Auth::user()->isAdmin() || (Auth::user()->id_pks == $peta->id_pks && !$peta->is_locked))
                            <a href="{{ route('pemetaan-la.edit', $peta->id) }}" class="btn btn-sm btn-light border text-warning d-inline-flex align-items-center justify-content-center px-2.5 py-1.5" title="Edit Metadata" style="border-radius:9px;">
                                <i class="feather-edit-2" style="font-size:14.5px;"></i>
                            </a>
                            @elseif(Auth::user()->id_pks == $peta->id_pks && $peta->is_locked)
                            <button type="button" class="btn btn-sm btn-light border text-muted opacity-50 d-inline-flex align-items-center justify-content-center px-2.5 py-1.5" title="Data dikunci oleh Admin (Tidak dapat diedit)" style="border-radius:9px;" disabled>
                                <i class="feather-lock text-danger" style="font-size:14.5px;"></i>
                            </button>
                            @endif

                            {{-- Admin Only Actions: Lock/Unlock & Delete --}}
                            @if(Auth::user()->isAdmin())
                            <form action="{{ route('pemetaan-la.toggle-lock', $peta->id) }}" method="POST" class="d-inline m-0 p-0">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-light border {{ $peta->is_locked ? 'text-danger' : 'text-secondary' }} d-inline-flex align-items-center justify-content-center px-2.5 py-1.5" title="{{ $peta->is_locked ? 'Buka Kunci Arsip (Unlock)' : 'Kunci Arsip (Lock)' }}" style="border-radius:9px;">
                                    <i class="{{ $peta->is_locked ? 'feather-lock text-danger' : 'feather-unlock' }}" style="font-size:14.5px;"></i>
                                </button>
                            </form>
                            <form action="{{ route('pemetaan-la.destroy', $peta->id) }}" method="POST" class="d-inline m-0 p-0" onsubmit="return confirm('Apakah Anda yakin ingin menghapus arsip peta ini?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-light border text-danger d-inline-flex align-items-center justify-content-center px-2.5 py-1.5" title="Hapus Arsip" style="border-radius:9px;">
                                    <i class="feather-trash-2" style="font-size:14.5px;"></i>
                                </button>
                            </form>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    {{-- ================================================================
         VIEW 2: MODE TABEL RIWAYAT (TABLE VIEW)
         ================================================================ --}}
    <div id="containerTableView" class="table-peta-wrapper d-none mb-4">
        <div class="table-responsive">
            <table class="table table-peta table-hover align-middle">
                <thead>
                    <tr>
                        <th style="width: 50px;" class="text-center">No</th>
                        <th>Unit PKS</th>
                        <th>Judul &amp; Kategori Peta</th>
                        <th class="text-center">Tahun</th>
                        <th>Format &amp; Ukuran</th>
                        <th>Tgl Unggah</th>
                        <th class="text-center">Status</th>
                        <th class="text-center" style="width: 170px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($petaList as $index => $peta)
                    <tr>
                        <td class="text-center font-monospace text-muted" style="font-size:12px;">
                            {{ $petaList->firstItem() + $index }}
                        </td>
                        <td>
                            <strong style="color:#15803d;"><i class="feather-home me-1"></i> {{ $peta->pks ? $peta->pks->nama : 'PKS' }}</strong>
                            <small class="d-block text-muted">{{ $peta->pks ? $peta->pks->akro ?? $peta->pks->kode : '' }}</small>
                        </td>
                        <td>
                            <div class="fw-bold" style="color:#1e293b;">{{ $peta->nama_peta }}</div>
                            <span class="badge bg-light text-dark border mt-1" style="font-size:10.5px;font-weight:600;">
                                {{ $peta->kategori_peta }}
                            </span>
                        </td>
                        <td class="text-center">
                            <span class="badge bg-success-subtle text-success px-2.5 py-1 rounded-pill fw-bold" style="font-size:12px;">
                                {{ $peta->tahun_peta ?: '-' }}
                            </span>
                        </td>
                        <td>
                            <span class="badge bg-secondary-subtle text-secondary text-uppercase px-2 py-0.5 rounded" style="font-size:11px;font-weight:700;">
                                {{ $peta->tipe_file ?: '-' }}
                            </span>
                            <small class="d-block text-muted mt-0.5" style="font-size:11.5px;">{{ $peta->formatted_size }}</small>
                        </td>
                        <td style="font-size:12px;color:#64748b;">
                            {{ $peta->created_at ? $peta->created_at->format('d/m/Y') : '-' }}
                        </td>
                        <td class="text-center">
                            @if($peta->is_locked)
                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-0.5 rounded-pill mb-1 d-inline-block" style="font-size:10px;">
                                    <i class="feather-lock"></i> Terkunci
                                </span>
                            @endif
                            @if($petaTerbaru && $peta->id == $petaTerbaru->id)
                                <span class="badge bg-success text-white px-2.5 py-1 rounded-pill" style="font-size:10.5px;font-weight:700;">
                                    🌟 Terkini
                                </span>
                            @else
                                <span class="badge bg-light text-muted border px-2.5 py-1 rounded-pill" style="font-size:10.5px;">
                                    Arsip
                                </span>
                            @endif
                        </td>
                        <td class="text-center">
                            <div class="d-inline-flex align-items-center gap-1">
                                <a href="{{ asset('uploads/peta_la/' . $peta->file_peta) }}" target="_blank" class="btn btn-sm btn-outline-success px-2 py-1" title="Buka File" style="border-radius:7px;">
                                    <i class="feather-eye"></i>
                                </a>
                                <a href="{{ asset('uploads/peta_la/' . $peta->file_peta) }}" download class="btn btn-sm btn-light border text-muted px-2 py-1" title="Unduh File" style="border-radius:7px;">
                                    <i class="feather-download"></i>
                                </a>
                                <a href="{{ route('pemetaan-la.show', $peta->id) }}" class="btn btn-sm btn-light border text-secondary px-2 py-1" title="Detail Arsip" style="border-radius:7px;">
                                    <i class="feather-info"></i>
                                </a>
                                @if(Auth::user()->isAdmin() || (Auth::user()->id_pks == $peta->id_pks && !$peta->is_locked))
                                <a href="{{ route('pemetaan-la.edit', $peta->id) }}" class="btn btn-sm btn-light border text-warning px-2 py-1" title="Edit" style="border-radius:7px;">
                                    <i class="feather-edit-2"></i>
                                </a>
                                @elseif(Auth::user()->id_pks == $peta->id_pks && $peta->is_locked)
                                <button type="button" class="btn btn-sm btn-light border text-muted opacity-50 px-2 py-1" title="Data dikunci oleh Admin" style="border-radius:7px;" disabled>
                                    <i class="feather-lock text-danger"></i>
                                </button>
                                @endif
                                @if(Auth::user()->isAdmin())
                                <form action="{{ route('pemetaan-la.toggle-lock', $peta->id) }}" method="POST" class="d-inline m-0 p-0">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-light border {{ $peta->is_locked ? 'text-danger' : 'text-secondary' }} px-2 py-1" title="{{ $peta->is_locked ? 'Buka Kunci' : 'Kunci Arsip' }}" style="border-radius:7px;">
                                        <i class="{{ $peta->is_locked ? 'feather-lock text-danger' : 'feather-unlock' }}"></i>
                                    </button>
                                </form>
                                @endif

                                @if(Auth::user()->isAdmin() || (Auth::user()->id_pks == $peta->id_pks && !$peta->is_locked))
                                <form action="{{ route('pemetaan-la.destroy', $peta->id) }}" method="POST" class="d-inline m-0 p-0" onsubmit="return confirm('Apakah Anda yakin ingin menghapus arsip peta ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-light border text-danger px-2 py-1" title="Hapus" style="border-radius:7px;">
                                        <i class="feather-trash-2"></i>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- Pagination --}}
    <div class="d-flex justify-content-center mb-4">
        {{ $petaList->links('pagination::bootstrap-5') }}
    </div>
@else
    {{-- Empty State jika tidak ada peta sesuai filter --}}
    <div class="filter-peta-card text-center py-5">
        <div style="font-size:48px;color:#86efac;margin-bottom:12px;">
            <i class="feather-inbox"></i>
        </div>
        <h5 style="font-family:'Outfit',sans-serif;font-weight:800;color:#374151;">Tidak Ada Berkas Peta Ditemukan</h5>
        <p style="font-size:13px;color:#6b7280;max-width:420px;margin:0 auto 20px auto;">
            Tidak ditemukan berkas peta yang sesuai dengan filter pencarian. Silakan reset filter atau unggah berkas peta baru.
        </p>
        <div class="d-flex justify-content-center gap-2">
            @if(request()->hasAny(['search', 'id_pks', 'kategori', 'format']))
                <a href="{{ route('pemetaan-la.index') }}" class="btn btn-light border px-3">
                    <i class="feather-refresh-cw me-1"></i> Reset Filter
                </a>
            @endif
            <a href="{{ route('pemetaan-la.create') }}" class="btn-ptpn btn-ptpn-primary" style="display:inline-flex;">
                <i class="feather-upload-cloud me-1"></i> Unggah Arsip Peta
            </a>
        </div>
    </div>
@endif

@endsection

@section('scripts')
<script>
    function switchView(mode) {
        const gridView = document.getElementById('containerGridView');
        const tableView = document.getElementById('containerTableView');
        const btnGrid = document.getElementById('btnViewGrid');
        const btnTable = document.getElementById('btnViewTable');

        if (!gridView || !tableView) return;

        if (mode === 'table') {
            gridView.classList.add('d-none');
            tableView.classList.remove('d-none');
            btnTable.classList.add('active');
            btnGrid.classList.remove('active');
            localStorage.setItem('simoli_peta_view_mode', 'table');
        } else {
            tableView.classList.add('d-none');
            gridView.classList.remove('d-none');
            btnGrid.classList.add('active');
            btnTable.classList.remove('active');
            localStorage.setItem('simoli_peta_view_mode', 'grid');
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        const savedMode = localStorage.getItem('simoli_peta_view_mode');
        if (savedMode === 'table') {
            switchView('table');
        }
    });
</script>
@endsection
