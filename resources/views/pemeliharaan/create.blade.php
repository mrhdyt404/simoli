@extends('layouts.simoli')

@section('title', 'Tambah Data Pemeliharaan')
@section('page-title', 'Tambah Data Pemeliharaan')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('pemeliharaan.index') }}">Pemeliharaan</a></li>
<li class="breadcrumb-item active">Tambah Data</li>
@endsection

@section('styles')
<link rel="stylesheet" type="text/css" href="{{ asset('duraluxadmin/assets/vendors/css/select2.min.css') }}" />
<link rel="stylesheet" type="text/css" href="{{ asset('duraluxadmin/assets/vendors/css/select2-theme.min.css') }}" />
<style>
    .form-card {
        border: none;
        border-radius: 12px;
    }
    .form-section-title {
        font-size: 14px;
        font-weight: 700;
        color: #4338ca;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 16px;
        padding-bottom: 8px;
        border-bottom: 2px solid #e0e7ff;
    }
    .form-section-title i {
        margin-right: 8px;
    }
    .photo-preview {
        max-width: 100%;
        max-height: 200px;
        border-radius: 8px;
        border: 2px dashed #e0e7ff;
        padding: 4px;
        margin-top: 8px;
        display: none;
    }
    .photo-upload-area {
        border: 2px dashed #d1d5db;
        border-radius: 12px;
        padding: 20px;
        text-align: center;
        transition: border-color 0.3s ease, background-color 0.3s ease;
        cursor: pointer;
    }
    .photo-upload-area:hover {
        border-color: #6366f1;
        background-color: #f5f3ff;
    }
</style>
@endsection

@section('content')
<form action="{{ route('pemeliharaan.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <div class="row">
        {{-- Left Column --}}
        <div class="col-lg-8">
            <div class="card form-card shadow-sm mb-4">
                <div class="card-body p-4">
                    <div class="form-section-title">
                        <i class="feather-info"></i> Informasi Umum
                    </div>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Tanggal <span class="text-danger">*</span></label>
                            <input type="date" name="tanggal" class="form-control @error('tanggal') is-invalid @enderror" value="{{ old('tanggal', date('Y-m-d')) }}" required>
                            @error('tanggal') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Jenis Pemeliharaan</label>
                            <select name="jenis_pemeliharaan" class="form-select @error('jenis_pemeliharaan') is-invalid @enderror">
                                <option value="">-- Pilih Jenis --</option>
                                <option value="1" {{ old('jenis_pemeliharaan') == '1' ? 'selected' : '' }}>Mekanis</option>
                                <option value="2" {{ old('jenis_pemeliharaan') == '2' ? 'selected' : '' }}>Manual</option>
                            </select>
                            @error('jenis_pemeliharaan') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Jumlah HK <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="text" name="jumlah_hk" class="form-control @error('jumlah_hk') is-invalid @enderror" value="{{ old('jumlah_hk', '1') }}" placeholder="Contoh: 2" required>
                                <span class="input-group-text">Orang</span>
                            </div>
                            @error('jumlah_hk') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <hr class="my-4">

                    <div class="form-section-title">
                        <i class="feather-map-pin"></i> Detail Lokasi
                    </div>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">No. Bak <span class="text-danger">*</span></label>
                            <input type="text" name="no_bak" class="form-control @error('no_bak') is-invalid @enderror" value="{{ old('no_bak') }}" placeholder="Contoh: 10" required>
                            @error('no_bak') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Blok <span class="text-danger">*</span></label>
                            <input type="text" name="blok" class="form-control @error('blok') is-invalid @enderror" value="{{ old('blok') }}" placeholder="Contoh: 22L" required>
                            @error('blok') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <hr class="my-4">

                    <div class="form-section-title">
                        <i class="feather-layers"></i> Data Bed
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Flat Bed <span class="text-danger">*</span></label>
                            <input type="number" name="flat_bed" class="form-control @error('flat_bed') is-invalid @enderror" value="{{ old('flat_bed', 0) }}" min="0" step="any" required>
                            @error('flat_bed') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Long Bed <span class="text-danger">*</span></label>
                            <input type="number" name="long_bed" class="form-control @error('long_bed') is-invalid @enderror" value="{{ old('long_bed', 0) }}" min="0" step="any" required>
                            @error('long_bed') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <hr class="my-4">

                    <div class="form-section-title">
                        <i class="feather-camera"></i> Foto Dokumentasi
                    </div>
                    <div class="row g-4">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold d-flex align-items-center">
                                <span class="badge bg-soft-danger text-danger me-2">Sebelum</span> Foto Sebelum
                            </label>
                            <div class="photo-upload-area" onclick="document.getElementById('foto_sebelum').click()">
                                <i class="feather-upload-cloud d-block text-muted mb-2" style="font-size: 28px;"></i>
                                <small class="text-muted">Klik untuk upload foto sebelum</small>
                                <input type="file" name="sebelum" id="foto_sebelum" class="d-none @error('sebelum') is-invalid @enderror" accept="image/*" onchange="previewImage(this, 'preview_sebelum')">
                            </div>
                            <img id="preview_sebelum" class="photo-preview" alt="Preview Sebelum">
                            @error('sebelum') <div class="text-danger fs-12 mt-1">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold d-flex align-items-center">
                                <span class="badge bg-soft-success text-success me-2">Sesudah</span> Foto Sesudah
                            </label>
                            <div class="photo-upload-area" onclick="document.getElementById('foto_sesudah').click()">
                                <i class="feather-upload-cloud d-block text-muted mb-2" style="font-size: 28px;"></i>
                                <small class="text-muted">Klik untuk upload foto sesudah</small>
                                <input type="file" name="sesudah" id="foto_sesudah" class="d-none @error('sesudah') is-invalid @enderror" accept="image/*" onchange="previewImage(this, 'preview_sesudah')">
                            </div>
                            <img id="preview_sesudah" class="photo-preview" alt="Preview Sesudah">
                            @error('sesudah') <div class="text-danger fs-12 mt-1">{{ $message }}</div> @enderror
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Right Column --}}
        <div class="col-lg-4">
            <div class="card form-card shadow-sm mb-4">
                <div class="card-body p-4">
                    <div class="form-section-title">
                        <i class="feather-home"></i> Unit PKS
                    </div>
                    @if($user->isAdmin())
                    <select name="id_pks" class="form-control @error('id_pks') is-invalid @enderror" data-select2-selector="status" required>
                        <option value="">-- Pilih PKS --</option>
                        @foreach($pksList as $pks)
                        <option value="{{ $pks->ID }}" {{ old('id_pks') == $pks->ID ? 'selected' : '' }}>
                            {{ $pks->NAMA }} ({{ $pks->AKRO }})
                        </option>
                        @endforeach
                    </select>
                    @error('id_pks') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    @else
                    <input type="hidden" name="id_pks" value="{{ $user->ID }}">
                    <div class="form-control bg-light">{{ $user->NAMA }} ({{ $user->AKRO }})</div>
                    @endif
                </div>
            </div>

            <div class="card form-card shadow-sm mb-4">
                <div class="card-body p-4">
                    <div class="form-section-title">
                        <i class="feather-message-square"></i> Keterangan
                    </div>
                    <textarea name="keterangan" rows="5" class="form-control @error('keterangan') is-invalid @enderror" placeholder="Tambahkan catatan pemeliharaan...">{{ old('keterangan') }}</textarea>
                    @error('keterangan') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>

            <div class="d-grid gap-2">
                <button type="submit" class="btn btn-primary btn-lg">
                    <i class="feather-save me-2"></i> Simpan Data
                </button>
                <a href="{{ route('pemeliharaan.index') }}" class="btn btn-outline-secondary btn-lg">
                    <i class="feather-arrow-left me-2"></i> Kembali
                </a>
            </div>
        </div>
    </div>
</form>
@endsection

@section('scripts')
<script src="{{ asset('duraluxadmin/assets/vendors/js/select2.min.js') }}"></script>
<script src="{{ asset('duraluxadmin/assets/vendors/js/select2-active.min.js') }}"></script>
<script>
    function previewImage(input, previewId) {
        const preview = document.getElementById(previewId);
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.src = e.target.result;
                preview.style.display = 'block';
            };
            reader.readAsDataURL(input.files[0]);
            // Update upload area text
            input.closest('.photo-upload-area').querySelector('small').textContent = input.files[0].name;
        }
    }
</script>
@endsection
