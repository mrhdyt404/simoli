@extends('layouts.simoli')

@section('title', 'Edit Arsip Peta LA')
@section('page-title', 'Edit Metadata & Berkas Peta Land Application')
@section('page-description', 'Perbarui Informasi atau Unggah Ulang Berkas Peta')

@section('breadcrumb')
    <li><a href="{{ route('pemetaan-la.index') }}">Arsip Peta LA</a></li>
    <li class="separator">/</li>
    <li>Edit Arsip Peta</li>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        @if($peta->is_locked)
        <div class="alert alert-warning d-flex align-items-center gap-2 mb-3 rounded-3" role="alert">
            <i class="feather-lock text-danger" style="font-size:18px;"></i>
            <div style="font-size:13px;">
                <strong>Status Arsip Terkunci:</strong> Arsip ini dikunci oleh Admin sehingga user Unit tidak dapat mengubahnya.
            </div>
        </div>
        @endif

        <form action="{{ route('pemetaan-la.update', $peta->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            
            <div class="simoli-card mb-4">
                <div class="simoli-card-header">
                    <h3 class="simoli-card-title">
                        <i class="feather-edit text-success"></i>
                        <span>Informasi &amp; Berkas Peta</span>
                    </h3>
                </div>
                <div class="simoli-card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-simoli-label">Pabrik Kelapa Sawit (PKS) <span class="text-danger">*</span></label>
                            @if(Auth::user()->isAdmin())
                                <select name="id_pks" class="form-select form-simoli-control @error('id_pks') is-invalid @enderror" required>
                                    <option value="">-- Pilih PKS --</option>
                                    @foreach($daftarPks as $pks)
                                    <option value="{{ $pks->id_pks }}" {{ old('id_pks', $peta->id_pks) == $pks->id_pks ? 'selected' : '' }}>
                                        {{ $pks->nama }} ({{ $pks->akro ?? $pks->kode }})
                                    </option>
                                    @endforeach
                                </select>
                            @else
                                <input type="hidden" name="id_pks" value="{{ $peta->id_pks }}">
                                <input type="text" class="form-control form-simoli-control" value="{{ $peta->pks ? $peta->pks->nama . ' (' . ($peta->pks->akro ?? $peta->pks->kode) . ')' : 'PKS Unit' }}" disabled readonly style="background:#f0fdf4;color:#15803d;font-weight:700;">
                            @endif
                            @error('id_pks')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-simoli-label">Kategori Peta <span class="text-danger">*</span></label>
                            <select name="kategori_peta" class="form-select form-simoli-control @error('kategori_peta') is-invalid @enderror" required>
                                @foreach($kategoriList as $kat)
                                    <option value="{{ $kat }}" {{ old('kategori_peta', $peta->kategori_peta) == $kat ? 'selected' : '' }}>{{ $kat }}</option>
                                @endforeach
                            </select>
                            @error('kategori_peta')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-9">
                            <label class="form-simoli-label">Nama / Judul Peta <span class="text-danger">*</span></label>
                            <input type="text" name="nama_peta" class="form-control form-simoli-control @error('nama_peta') is-invalid @enderror" 
                                   value="{{ old('nama_peta', $peta->nama_peta) }}" required>
                            @error('nama_peta')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-3">
                            <label class="form-simoli-label">Tahun Pembuatan Peta</label>
                            <input type="text" name="tahun_peta" class="form-control form-simoli-control @error('tahun_peta') is-invalid @enderror" 
                                   value="{{ old('tahun_peta', $peta->tahun_peta) }}">
                            @error('tahun_peta')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        {{-- File Peta --}}
                        <div class="col-md-12">
                            <label class="form-simoli-label">Berkas Dokumen Peta</label>
                            @if($peta->file_peta)
                            <div class="d-flex align-items-center justify-content-between p-3 border rounded-3 bg-light mb-2">
                                <div class="d-flex align-items-center gap-2">
                                    <i class="feather-map text-success" style="font-size:24px;"></i>
                                    <div>
                                        <div style="font-size:13px;font-weight:700;color:#1f2937;">{{ $peta->file_peta }}</div>
                                        <div style="font-size:11px;color:#6b7280;">Ukuran: {{ $peta->formatted_size }}</div>
                                    </div>
                                </div>
                                <a href="{{ asset('uploads/peta_la/' . $peta->file_peta) }}" target="_blank" class="btn btn-sm btn-outline-success">
                                    <i class="feather-eye"></i> Buka Berkas
                                </a>
                            </div>
                            @endif

                            <div class="p-3 border rounded-3 text-center bg-light">
                                <input type="file" name="file_peta" class="form-control form-simoli-control @error('file_peta') is-invalid @enderror" accept=".pdf,.jpg,.jpeg,.png,.webp,.svg,.zip,.geojson,.json">
                                <small class="text-muted d-block mt-2" style="font-size:11.5px;">
                                    Kosongkan jika tidak ingin mengubah berkas. Format: <strong>PDF, Gambar (JPG, PNG, WEBP), ZIP Shapefile, GeoJSON</strong> (Maks 25 MB).
                                </small>
                            </div>
                            @error('file_peta')<div class="text-danger mt-1" style="font-size:12px;">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-12">
                            <label class="form-simoli-label">Deskripsi / Keterangan Peta</label>
                            <textarea name="keterangan" rows="3" class="form-control form-simoli-control" placeholder="Tuliskan keterangan detail mengenai peta ini...">{{ old('keterangan', $peta->keterangan) }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            {{-- TOMBOL SUBMIT --}}
            <div class="d-flex justify-content-end gap-2 mb-5">
                <a href="{{ route('pemetaan-la.show', $peta->id) }}" class="btn btn-light border px-4">Batal</a>
                <button type="submit" class="btn-ptpn btn-ptpn-primary px-4">
                    <i class="feather-save me-1"></i> Perbarui Arsip Peta
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
