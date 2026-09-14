@extends('layouts.simoli')

@section('title', 'Detail Arsip Peta - ' . $peta->nama_peta)
@section('page-title', 'Detail Arsip Dokumen Peta LA')
@section('page-description', 'Pratinjau Dokumen & Informasi Berkas Peta Land Application')

@section('breadcrumb')
    <li><a href="{{ route('pemetaan-la.index') }}">Arsip Peta LA</a></li>
    <li class="separator">/</li>
    <li>Detail Peta</li>
@endsection

@section('page-actions')
    <div class="d-flex gap-2">
        <a href="{{ route('pemetaan-la.index') }}" class="btn-ptpn btn-ptpn-outline">
            <i class="feather-arrow-left"></i> Kembali
        </a>
        @if(Auth::user()->isAdmin() || (Auth::user()->id_pks == $peta->id_pks && !$peta->is_locked))
        <a href="{{ route('pemetaan-la.edit', $peta->id) }}" class="btn-ptpn btn-ptpn-primary">
            <i class="feather-edit-2"></i> Edit Metadata / Ganti Berkas
        </a>
        @elseif(Auth::user()->id_pks == $peta->id_pks && $peta->is_locked)
        <button type="button" class="btn-ptpn btn-ptpn-outline text-muted opacity-75" title="Data arsip dikunci oleh Admin" disabled>
            <i class="feather-lock text-danger"></i> Terkunci oleh Admin
        </button>
        @endif

        @if(Auth::user()->isAdmin())
        <form action="{{ route('pemetaan-la.toggle-lock', $peta->id) }}" method="POST" class="d-inline m-0 p-0">
            @csrf
            <button type="submit" class="btn-ptpn {{ $peta->is_locked ? 'btn-ptpn-outline text-danger' : 'btn-ptpn-outline' }}" title="{{ $peta->is_locked ? 'Buka Kunci Arsip' : 'Kunci Arsip Peta' }}">
                <i class="{{ $peta->is_locked ? 'feather-lock text-danger' : 'feather-unlock' }}"></i>
                <span>{{ $peta->is_locked ? 'Buka Kunci' : 'Kunci Arsip' }}</span>
            </button>
        </form>
        <form action="{{ route('pemetaan-la.destroy', $peta->id) }}" method="POST" class="d-inline m-0 p-0" onsubmit="return confirm('Apakah Anda yakin ingin menghapus arsip peta ini?');">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn-ptpn btn-ptpn-outline text-danger" title="Hapus Arsip">
                <i class="feather-trash-2"></i> Hapus
            </button>
        </form>
        @endif
    </div>
@endsection

@section('content')
<div class="row g-4">
    {{-- Left Column: Metadata Arsip Peta --}}
    <div class="col-lg-4">
        <div class="simoli-card mb-4">
            <div class="simoli-card-header">
                <h3 class="simoli-card-title">
                    <i class="feather-info text-success"></i>
                    <span>Informasi Berkas Peta</span>
                </h3>
            </div>
            <div class="simoli-card-body p-3">
                <div class="mb-3 text-center p-3 bg-light rounded-3">
                    <div style="font-size:36px;color:#16a34a;margin-bottom:6px;">
                        <i class="feather-map"></i>
                    </div>
                    @if($peta->is_locked)
                    <div class="mb-2">
                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-3 py-1 rounded-pill" style="font-size:11.5px;font-weight:700;">
                            <i class="feather-lock"></i> Dikunci oleh Admin
                        </span>
                    </div>
                    @endif
                    <span class="badge bg-success px-3 py-1 rounded-pill mb-2" style="font-size:12px;">
                        {{ $peta->kategori_peta }}
                    </span>
                    <h5 style="font-family:'Outfit',sans-serif;font-weight:800;color:#1f2937;margin:0;">
                        {{ $peta->pks ? $peta->pks->nama : 'Unit PKS' }}
                    </h5>
                    <small class="text-muted">{{ $peta->pks ? $peta->pks->akro ?? $peta->pks->kode : '' }}</small>
                </div>

                <div class="d-flex flex-column gap-2" style="font-size:13px;">
                    <div class="border-bottom pb-2">
                        <span class="text-muted d-block" style="font-size:11.5px;">Nama / Judul Peta:</span>
                        <strong style="color:#1f2937;">{{ $peta->nama_peta }}</strong>
                    </div>

                    <div class="border-bottom pb-2">
                        <span class="text-muted d-block" style="font-size:11.5px;">Kategori:</span>
                        <span style="color:#374151;">{{ $peta->kategori_peta }}</span>
                    </div>

                    <div class="border-bottom pb-2">
                        <span class="text-muted d-block" style="font-size:11.5px;">Tahun Peta:</span>
                        <strong>{{ $peta->tahun_peta ?: '-' }}</strong>
                    </div>

                    <div class="border-bottom pb-2">
                        <span class="text-muted d-block" style="font-size:11.5px;">Tipe &amp; Ukuran Berkas:</span>
                        <strong class="text-uppercase">{{ $peta->tipe_file ?: '-' }}</strong>
                        <span class="text-muted">({{ $peta->formatted_size }})</span>
                    </div>

                    <div class="border-bottom pb-2">
                        <span class="text-muted d-block" style="font-size:11.5px;">Tanggal Unggah:</span>
                        <span>{{ $peta->created_at ? $peta->created_at->format('d F Y, H:i') : '-' }} WIB</span>
                    </div>

                    @if($peta->keterangan)
                    <div class="pt-1">
                        <span class="text-muted d-block" style="font-size:11.5px;">Keterangan:</span>
                        <span style="color:#4b5563;">{{ $peta->keterangan }}</span>
                    </div>
                    @endif
                </div>

                @if($peta->file_peta)
                <div class="d-grid gap-2 mt-4">
                    <a href="{{ asset('uploads/peta_la/' . $peta->file_peta) }}" download class="btn btn-ptpn btn-ptpn-primary justify-content-center">
                        <i class="feather-download"></i> Unduh Berkas Peta
                    </a>
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Right Column: Preview Dokumen/Gambar Peta Viewer --}}
    <div class="col-lg-8">
        <div class="simoli-card" style="height: calc(100vh - 220px); min-height: 600px; display: flex; flex-direction: column;">
            <div class="simoli-card-header d-flex justify-content-between align-items-center">
                <h3 class="simoli-card-title">
                    <i class="feather-eye text-success"></i>
                    <span>Pratinjau Berkas Peta</span>
                </h3>
                @if($peta->file_peta)
                <a href="{{ asset('uploads/peta_la/' . $peta->file_peta) }}" target="_blank" class="btn btn-sm btn-light border text-muted">
                    <i class="feather-external-link"></i> Buka Ukuran Penuh
                </a>
                @endif
            </div>
            <div class="simoli-card-body p-0 flex-grow-1" style="background:#525659;border-radius:0 0 16px 16px;overflow:hidden;">
                @if($peta->file_peta)
                    @if($peta->is_pdf)
                        <iframe src="{{ asset('uploads/peta_la/' . $peta->file_peta) }}" width="100%" height="100%" style="border:none;"></iframe>
                    @elseif($peta->is_image)
                        <div class="d-flex align-items-center justify-content-center h-100 p-3 bg-dark" style="overflow:auto;">
                            <img src="{{ asset('uploads/peta_la/' . $peta->file_peta) }}" alt="{{ $peta->nama_peta }}" style="max-width:100%;max-height:100%;object-fit:contain;border-radius:8px;">
                        </div>
                    @else
                        <div class="d-flex flex-column align-items-center justify-content-center h-100 text-white p-4">
                            <i class="feather-file" style="font-size:48px;opacity:.5;margin-bottom:12px;"></i>
                            <h5>Berkas {{ strtoupper($peta->tipe_file) }}</h5>
                            <p style="opacity:.8;font-size:13px;">Berkas ini dapat langsung diunduh melalui tombol unduh di samping.</p>
                            <a href="{{ asset('uploads/peta_la/' . $peta->file_peta) }}" download class="btn btn-light btn-sm mt-2">
                                <i class="feather-download"></i> Unduh Berkas
                            </a>
                        </div>
                    @endif
                @else
                    <div class="d-flex flex-column align-items-center justify-content-center h-100 text-white p-4">
                        <i class="feather-map-pin" style="font-size:48px;opacity:.5;margin-bottom:12px;"></i>
                        <h5>Belum Ada File Peta</h5>
                        <p style="opacity:.8;font-size:13px;">Silakan unggah salinan peta pada menu edit.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
