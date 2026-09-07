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
    <div class="d-flex gap-2">
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
       PREVIEW PETA TERKINI (HERO CARD)
       ================================================================ */
    .hero-map-card {
        background: #ffffff;
        border-radius: 20px;
        border: 1px solid rgba(22,163,74,.2);
        box-shadow: 0 4px 20px rgba(22,163,74,.08);
        overflow: hidden;
        margin-bottom: 30px;
        animation: fadeUp .4s ease-out;
    }

    @keyframes fadeUp {
        from { opacity: 0; transform: translateY(12px); }
        to   { opacity: 1; transform: translateY(0); }
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
        height: 160px;
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
        background: rgba(15, 23, 42, 0.8);
        backdrop-filter: blur(4px);
        color: #ffffff;
        font-size: 10px;
        font-weight: 700;
        padding: 3px 8px;
        border-radius: 50px;
    }

    html.app-skin-dark .hero-map-card,
    html.app-skin-dark .hero-map-meta-panel,
    html.app-skin-dark .map-card {
        background: #0a2317 !important;
        border-color: rgba(34,197,94,.18) !important;
    }
    html.app-skin-dark .map-thumb-wrap {
        background: #052e16 !important;
        border-color: rgba(34,197,94,.2) !important;
    }
</style>
@endsection

@section('content')

{{-- ================================================================
     1. HERO PRATINJAU PETA TERKINI (BERDASARKAN TAHUN PALING BARU)
     ================================================================ --}}
<div class="hero-map-card">
    <div class="hero-map-header">
        <div class="d-flex align-items-center gap-2">
            <h4 class="hero-map-title">
                <i class="feather-map" style="font-size:18px;"></i>
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
                    <select name="id_pks" class="form-select form-select-sm" style="min-width:200px;font-weight:700;border-radius:8px;" onchange="document.getElementById('pksFilterForm').submit();">
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
                        <p style="opacity:.8;font-size:13px;">Format ini dapat diunduh untuk dibuka pada software SIG.</p>
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
                    <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1 rounded-pill mb-2" style="font-size:11px;font-weight:700;">
                        {{ $petaTerbaru->kategori_peta }}
                    </span>
                    <h4 style="font-family:'Outfit',sans-serif;font-weight:800;color:#1f2937;margin-bottom:6px;line-height:1.3;">
                        {{ $petaTerbaru->nama_peta }}
                    </h4>
                    <div class="text-muted" style="font-size:12px;">
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
                    <span class="text-muted d-block" style="font-size:11px;font-weight:700;text-transform:uppercase;">Keterangan Teknis:</span>
                    <p style="font-size:12.5px;color:#4b5563;line-height:1.4;margin:4px 0 0 0;">
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
     2. DAFTAR & RIWAYAT SELURUH ARSIP PETA
     ================================================================ --}}
<div class="d-flex align-items-center justify-content-between mb-3">
    <div>
        <h4 style="font-family:'Outfit',sans-serif;font-weight:800;font-size:18px;color:#1f2937;margin:0;">
            <i class="feather-folder text-success"></i> Riwayat &amp; Galeri Seluruh Arsip Peta
        </h4>
        <small class="text-muted">Daftar seluruh versi dan kategori dokumen peta yang tersimpan di sistem</small>
    </div>
    <span class="badge bg-light text-dark border px-3 py-2 rounded-pill font-12" style="font-weight:700;">
        Total: {{ $totalPeta }} Berkas Peta
    </span>
</div>

{{-- Filter & Pencarian Arsip Peta --}}
<div class="simoli-card mb-4">
    <div class="simoli-card-body p-3">
        <form method="GET" action="{{ route('pemetaan-la.index') }}" class="row g-2 align-items-center">
            <div class="col-md-4">
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0"><i class="feather-search text-muted"></i></span>
                    <input type="text" name="search" class="form-control border-start-0" placeholder="Cari nama peta atau deskripsi..." value="{{ request('search') }}">
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

            <div class="col-md-3">
                <select name="kategori" class="form-select">
                    <option value="">-- Semua Kategori Peta --</option>
                    @foreach($kategoriList as $kat)
                        <option value="{{ $kat }}" {{ request('kategori') == $kat ? 'selected' : '' }}>{{ $kat }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-2 d-flex gap-2">
                <button type="submit" class="btn-ptpn btn-ptpn-primary w-100 justify-content-center">
                    <i class="feather-filter"></i> Filter
                </button>
                @if(request()->hasAny(['search', 'id_pks', 'kategori']))
                    <a href="{{ route('pemetaan-la.index') }}" class="btn btn-light border px-3" title="Reset Filter">
                        <i class="feather-refresh-cw"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>
</div>

{{-- Grid Galeri Berkas Peta --}}
@if($petaList->count() > 0)
<div class="row g-4 mb-4">
    @foreach($petaList as $peta)
    <div class="col-md-6 col-xl-4">
        <div class="map-card">
            <div class="map-thumb-wrap">
                <span class="badge-category">{{ $peta->kategori_peta }}</span>
                @if($petaTerbaru && $peta->id == $petaTerbaru->id)
                    <span class="badge bg-success" style="position:absolute;top:10px;right:10px;font-size:10px;font-weight:800;border-radius:50px;padding:3px 8px;">
                        🌟 Peta Terkini (Aktif)
                    </span>
                @endif

                @if($peta->is_image)
                    <img src="{{ asset('uploads/peta_la/' . $peta->file_peta) }}" alt="{{ $peta->nama_peta }}" class="map-thumb-img">
                @elseif($peta->is_pdf)
                    <div class="map-thumb-placeholder text-center">
                        <i class="feather-file-text text-danger" style="font-size:58px;margin-bottom:8px;display:inline-block;"></i>
                        <span style="font-size:11.5px;font-weight:800;color:#64748b;letter-spacing:.3px;">DOKUMEN PETA PDF</span>
                    </div>
                @else
                    <div class="map-thumb-placeholder text-center">
                        <i class="feather-map text-success" style="font-size:58px;margin-bottom:8px;display:inline-block;"></i>
                        <span style="font-size:11.5px;font-weight:800;color:#64748b;letter-spacing:.3px;">BERKAS SPASIAL / DATA</span>
                    </div>
                @endif
            </div>

            <div class="p-3 d-flex flex-column flex-grow-1">
                <div class="mb-2">
                    <span class="badge bg-light text-dark border mb-1" style="font-size:11px;font-weight:700;">
                        <i class="feather-home text-success"></i> {{ $peta->pks ? $peta->pks->nama : 'Unit PKS' }}
                    </span>
                    <h5 style="font-family:'Outfit',sans-serif;font-weight:800;font-size:15px;color:#1f2937;margin-bottom:4px;line-height:1.3;">
                        {{ $peta->nama_peta }}
                    </h5>
                    @if($peta->keterangan)
                    <p style="font-size:12px;color:#64748b;margin-bottom:10px;line-height:1.4;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;">
                        {{ $peta->keterangan }}
                    </p>
                    @endif
                </div>

                <div class="bg-light rounded-3 p-2 mb-3 mt-auto" style="font-size:11.5px;color:#374151;">
                    <div class="d-flex justify-content-between mb-1">
                        <span class="text-muted">Tahun Peta:</span>
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
                            <i class="feather-eye" style="font-size:14.5px;"></i> Buka Peta
                        </a>
                        <a href="{{ asset('uploads/peta_la/' . $peta->file_peta) }}" download class="btn btn-sm btn-light border text-muted d-inline-flex align-items-center justify-content-center px-2.5 py-1.5" style="border-radius:9px;font-size:12.5px;" title="Unduh File">
                            <i class="feather-download" style="font-size:14.5px;"></i>
                        </a>
                    </div>

                    <div class="d-flex align-items-center gap-2 flex-nowrap">
                        <a href="{{ route('pemetaan-la.show', $peta->id) }}" class="btn btn-sm btn-light border text-secondary d-inline-flex align-items-center justify-content-center px-2.5 py-1.5" title="Detail Arsip" style="border-radius:9px;">
                            <i class="feather-info" style="font-size:15px;"></i>
                        </a>
                        @if(Auth::user()->isAdmin() || Auth::user()->id_pks == $peta->id_pks)
                        <a href="{{ route('pemetaan-la.edit', $peta->id) }}" class="btn btn-sm btn-light border text-warning d-inline-flex align-items-center justify-content-center px-2.5 py-1.5" title="Edit Metadata" style="border-radius:9px;">
                            <i class="feather-edit-2" style="font-size:15px;"></i>
                        </a>
                        <form action="{{ route('pemetaan-la.destroy', $peta->id) }}" method="POST" class="d-inline m-0 p-0" onsubmit="return confirm('Apakah Anda yakin ingin menghapus arsip peta ini?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-light border text-danger d-inline-flex align-items-center justify-content-center px-2.5 py-1.5" title="Hapus Arsip" style="border-radius:9px;">
                                <i class="feather-trash-2" style="font-size:15px;"></i>
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

<div class="d-flex justify-content-center">
    {{ $petaList->links('pagination::bootstrap-5') }}
</div>
@else
<div class="simoli-card text-center py-4">
    <p style="font-size:13px;color:#6b7280;margin:0;">Tidak ada berkas peta lain yang sesuai dengan filter pencarian.</p>
</div>
@endif

@endsection
