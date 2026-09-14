@extends('layouts.simoli')

@section('title', 'Detail Arsip SK ' . $perizinan->nomor_sk)
@section('page-title', 'Detail Arsip Surat Keputusan (SK) LA')
@section('page-description', 'Pratinjau Dokumen & Informasi Legalitas Perizinan Land Application')

@section('breadcrumb')
    <li><a href="{{ route('perizinan-la.index') }}">Arsip Perizinan LA</a></li>
    <li class="separator">/</li>
    <li>Detail Arsip</li>
@endsection

@section('page-actions')
    <div class="d-flex gap-2">
        <a href="{{ route('perizinan-la.index') }}" class="btn-ptpn btn-ptpn-outline">
            <i class="feather-arrow-left"></i> Kembali
        </a>
        @if(Auth::user()->isAdmin() || (Auth::user()->id_pks == $perizinan->id_pks && !$perizinan->is_locked))
        <a href="{{ route('perizinan-la.edit', $perizinan->id) }}" class="btn-ptpn btn-ptpn-primary">
            <i class="feather-edit-2"></i> Edit Metadata / Ganti Berkas
        </a>
        @elseif(Auth::user()->id_pks == $perizinan->id_pks && $perizinan->is_locked)
        <button type="button" class="btn-ptpn btn-ptpn-outline text-muted opacity-75" title="Data arsip dikunci oleh Admin" disabled>
            <i class="feather-lock text-danger"></i> Terkunci oleh Admin
        </button>
        @endif

        @if(Auth::user()->isAdmin())
        <form action="{{ route('perizinan-la.toggle-lock', $perizinan->id) }}" method="POST" class="d-inline m-0 p-0">
            @csrf
            <button type="submit" class="btn-ptpn {{ $perizinan->is_locked ? 'btn-ptpn-outline text-danger' : 'btn-ptpn-outline' }}" title="{{ $perizinan->is_locked ? 'Buka Kunci Arsip' : 'Kunci Arsip SK' }}">
                <i class="{{ $perizinan->is_locked ? 'feather-lock text-danger' : 'feather-unlock' }}"></i>
                <span>{{ $perizinan->is_locked ? 'Buka Kunci' : 'Kunci Arsip' }}</span>
            </button>
        </form>
        <form action="{{ route('perizinan-la.destroy', $perizinan->id) }}" method="POST" class="d-inline m-0 p-0" onsubmit="return confirm('Apakah Anda yakin ingin menghapus arsip SK ini?');">
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
    {{-- Left Column: Metadata Arsip --}}
    <div class="col-lg-4">
        <div class="simoli-card mb-4">
            <div class="simoli-card-header">
                <h3 class="simoli-card-title">
                    <i class="feather-info text-success"></i>
                    <span>Informasi Dokumen SK</span>
                </h3>
            </div>
            <div class="simoli-card-body p-3">
                <div class="mb-3 text-center p-3 bg-light rounded-3">
                    <div style="font-size:36px;color:#16a34a;margin-bottom:6px;">
                        <i class="feather-file-text"></i>
                    </div>
                    @if($perizinan->is_locked)
                    <div class="mb-2">
                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-3 py-1 rounded-pill" style="font-size:11.5px;font-weight:700;">
                            <i class="feather-lock"></i> Dikunci oleh Admin
                        </span>
                    </div>
                    @endif
                    <span class="badge bg-success px-3 py-1 rounded-pill mb-2" style="font-size:12px;">
                        {{ $perizinan->status_label }}
                    </span>
                    <h5 style="font-family:'Outfit',sans-serif;font-weight:800;color:#1f2937;margin:0;">
                        {{ $perizinan->pks ? $perizinan->pks->nama : 'Unit PKS' }}
                    </h5>
                    <small class="text-muted">{{ $perizinan->pks ? $perizinan->pks->akro ?? $perizinan->pks->kode : '' }}</small>
                </div>

                <div class="d-flex flex-column gap-2" style="font-size:13px;">
                    <div class="border-bottom pb-2">
                        <span class="text-muted d-block" style="font-size:11.5px;">Nomor SK:</span>
                        <strong style="color:#1f2937;">{{ $perizinan->nomor_sk }}</strong>
                    </div>

                    <div class="border-bottom pb-2">
                        <span class="text-muted d-block" style="font-size:11.5px;">Perihal / Tentang:</span>
                        <span style="color:#374151;">{{ $perizinan->tentang }}</span>
                    </div>

                    <div class="border-bottom pb-2">
                        <span class="text-muted d-block" style="font-size:11.5px;">Instansi Penerbit:</span>
                        <strong style="color:#374151;">{{ $perizinan->instansi_penerbit }}</strong>
                    </div>

                    <div class="border-bottom pb-2 bg-success-subtle p-2 rounded-2">
                        <span class="text-success d-block" style="font-size:11px;font-weight:700;text-transform:uppercase;">Batas Debit Maksimal Pengaliran:</span>
                        <strong style="font-size:15px;color:#15803d;">{{ number_format($perizinan->debit_maksimal_harian ?? 0, 0, ',', '.') }} m³ / hari</strong>
                        <small class="text-muted d-block" style="font-size:10.5px;">Sesuai batasan baku teknis SK legalitas</small>
                    </div>

                    <div class="border-bottom pb-2">
                        <span class="text-muted d-block" style="font-size:11.5px;">Tanggal Penetapan (Terbit):</span>
                        <strong>{{ $perizinan->tanggal_terbit ? $perizinan->tanggal_terbit->format('d F Y') : '-' }}</strong>
                    </div>

                    <div class="border-bottom pb-2">
                        <span class="text-muted d-block" style="font-size:11.5px;">Tanggal Berakhir:</span>
                        <strong>{{ $perizinan->tanggal_berakhir ? $perizinan->tanggal_berakhir->format('d F Y') : '5 Tahun' }}</strong>
                        @if($perizinan->sisa_hari !== null)
                            <small class="d-block text-muted">
                                ({{ $perizinan->sisa_hari > 0 ? 'Sisa ' . $perizinan->sisa_hari . ' hari' : 'Kedaluwarsa ' . abs($perizinan->sisa_hari) . ' hari lalu' }})
                            </small>
                        @endif
                    </div>

                    @if($perizinan->keterangan)
                    <div class="pt-1">
                        <span class="text-muted d-block" style="font-size:11.5px;">Catatan:</span>
                        <span style="color:#4b5563;">{{ $perizinan->keterangan }}</span>
                    </div>
                    @endif
                </div>

                @if($perizinan->file_sk)
                <div class="d-grid gap-2 mt-4">
                    <a href="{{ asset('uploads/perizinan_la/' . $perizinan->file_sk) }}" download class="btn btn-ptpn btn-ptpn-primary justify-content-center">
                        <i class="feather-download"></i> Unduh Salinan Dokumen SK
                    </a>
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Right Column: Preview Dokumen Viewer --}}
    <div class="col-lg-8">
        <div class="simoli-card" style="height: calc(100vh - 220px); min-height: 600px; display: flex; flex-direction: column;">
            <div class="simoli-card-header d-flex justify-content-between align-items-center">
                <h3 class="simoli-card-title">
                    <i class="feather-eye text-success"></i>
                    <span>Pratinjau Salinan Berkas SK</span>
                </h3>
                @if($perizinan->file_sk)
                <a href="{{ asset('uploads/perizinan_la/' . $perizinan->file_sk) }}" target="_blank" class="btn btn-sm btn-light border text-muted">
                    <i class="feather-external-link"></i> Buka di Tab Baru
                </a>
                @endif
            </div>
            <div class="simoli-card-body p-0 flex-grow-1" style="background:#525659;border-radius:0 0 16px 16px;overflow:hidden;">
                @if($perizinan->file_sk)
                    @php
                        $ext = strtolower(pathinfo($perizinan->file_sk, PATHINFO_EXTENSION));
                    @endphp
                    @if($ext === 'pdf')
                        <iframe src="{{ asset('uploads/perizinan_la/' . $perizinan->file_sk) }}" width="100%" height="100%" style="border:none;"></iframe>
                    @else
                        <div class="d-flex align-items-center justify-content-center h-100 p-3 bg-dark">
                            <img src="{{ asset('uploads/perizinan_la/' . $perizinan->file_sk) }}" alt="Salinan SK" style="max-width:100%;max-height:100%;object-fit:contain;border-radius:8px;">
                        </div>
                    @endif
                @else
                    <div class="d-flex flex-column align-items-center justify-content-center h-100 text-white p-4">
                        <i class="feather-file-minus" style="font-size:48px;opacity:.5;margin-bottom:12px;"></i>
                        <h5>Belum Ada File Dokumen SK</h5>
                        <p style="opacity:.8;font-size:13px;">Silakan unggah salinan berkas SK pada menu edit.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
