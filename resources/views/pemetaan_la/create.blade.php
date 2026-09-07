@extends('layouts.simoli')

@section('title', 'Unggah Arsip Peta Land Application')
@section('page-title', 'Unggah Arsip Peta Land Application')
@section('page-description', 'Penyimpanan Salinan Dokumen Peta Lokasi LA, Layout IPAL, dan Peta Sebaran Sumur Pantau')

@section('breadcrumb')
    <li><a href="{{ route('pemetaan-la.index') }}">Arsip Peta LA</a></li>
    <li class="separator">/</li>
    <li>Unggah Berkas Peta</li>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <form action="{{ route('pemetaan-la.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            <div class="simoli-card mb-4">
                <div class="simoli-card-header">
                    <h3 class="simoli-card-title">
                        <i class="feather-map text-success"></i>
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
                                    <option value="{{ $pks->id_pks }}" {{ old('id_pks') == $pks->id_pks ? 'selected' : '' }}>
                                        {{ $pks->nama }} ({{ $pks->akro ?? $pks->kode }})
                                    </option>
                                    @endforeach
                                </select>
                            @else
                                <input type="hidden" name="id_pks" value="{{ Auth::user()->id_pks }}">
                                <input type="text" class="form-control form-simoli-control" value="{{ Auth::user()->pks ? Auth::user()->pks->nama . ' (' . (Auth::user()->pks->akro ?? Auth::user()->pks->kode) . ')' : 'PKS Unit' }}" disabled readonly style="background:#f0fdf4;color:#15803d;font-weight:700;">
                            @endif
                            @error('id_pks')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-simoli-label">Kategori Peta <span class="text-danger">*</span></label>
                            <select name="kategori_peta" class="form-select form-simoli-control @error('kategori_peta') is-invalid @enderror" required>
                                @foreach($kategoriList as $kat)
                                    <option value="{{ $kat }}" {{ old('kategori_peta') == $kat ? 'selected' : '' }}>{{ $kat }}</option>
                                @endforeach
                            </select>
                            @error('kategori_peta')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-9">
                            <label class="form-simoli-label">Nama / Judul Peta <span class="text-danger">*</span></label>
                            <input type="text" name="nama_peta" class="form-control form-simoli-control @error('nama_peta') is-invalid @enderror" 
                                   placeholder="Contoh: Peta Layout IPAL & Blok Land Application Sei Intan" value="{{ old('nama_peta') }}" required>
                            @error('nama_peta')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-3">
                            <label class="form-simoli-label">Tahun Pembuatan Peta</label>
                            <input type="text" name="tahun_peta" class="form-control form-simoli-control @error('tahun_peta') is-invalid @enderror" 
                                   placeholder="Contoh: 2020" value="{{ old('tahun_peta', date('Y')) }}">
                            @error('tahun_peta')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        {{-- Upload File Peta --}}
                        <div class="col-md-12">
                            <label class="form-simoli-label">Unggah Berkas Peta (PDF / Gambar / Zip / GeoJSON) <span class="text-danger">*</span></label>
                            <div class="p-3 border rounded-3 text-center bg-light">
                                <i class="feather-upload-cloud text-success" style="font-size:32px;margin-bottom:6px;display:block;"></i>
                                <input type="file" name="file_peta" class="form-control form-simoli-control @error('file_peta') is-invalid @enderror" accept=".pdf,.jpg,.jpeg,.png,.webp,.svg,.zip,.geojson,.json" required>
                                <small class="text-muted d-block mt-2" style="font-size:11.5px;">
                                    Format: <strong>PDF, Gambar (JPG, PNG, WEBP), ZIP Shapefile, GeoJSON</strong> (Maksimal 25 MB).
                                </small>
                            </div>
                            @error('file_peta')<div class="text-danger mt-1" style="font-size:12px;">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-12">
                            <label class="form-simoli-label">Deskripsi / Keterangan Peta</label>
                            <textarea name="keterangan" rows="3" class="form-control form-simoli-control" placeholder="Tuliskan keterangan detail mengenai peta ini...">{{ old('keterangan') }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            {{-- TOMBOL SUBMIT --}}
            <div class="d-flex justify-content-end gap-2 mb-5">
                <a href="{{ route('pemetaan-la.index') }}" class="btn btn-light border px-4">Batal</a>
                <button type="submit" class="btn-ptpn btn-ptpn-primary px-4">
                    <i class="feather-save me-1"></i> Simpan Arsip Peta
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
