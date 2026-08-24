@extends('layouts.simoli')

@section('title', 'Tambah Data Pemeliharaan')
@section('page-title', 'Tambah Data Pemeliharaan')
@section('page-description', 'Input Data Pemeliharaan Kolam & Bed Land Aplikasi')

@section('breadcrumb')
    <li><a href="{{ route('pemeliharaan.index') }}" style="color:inherit;text-decoration:none;">Pemeliharaan</a></li>
    <li class="separator">/</li>
    <li>Tambah Data</li>
@endsection

@section('styles')
<link rel="stylesheet" type="text/css" href="{{ asset('duraluxadmin/assets/vendors/css/select2.min.css') }}" />
<link rel="stylesheet" type="text/css" href="{{ asset('duraluxadmin/assets/vendors/css/select2-theme.min.css') }}" />
<style>
    /* ================================================================
       PEMELIHARAAN CREATE FORM — PTPN GREEN THEME
       ================================================================ */
    @keyframes fadeUpCard {
        from { opacity:0; transform:translateY(12px); }
        to   { opacity:1; transform:translateY(0); }
    }

    .form-card {
        background: #ffffff;
        border-radius: 18px;
        border: 1px solid rgba(22,163,74,.1);
        box-shadow: 0 2px 12px rgba(22,163,74,.06);
        padding: 24px;
        margin-bottom: 20px;
        animation: fadeUpCard .4s ease-out;
    }

    .form-section-title {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 11.5px;
        font-weight: 800;
        color: #16a34a;
        text-transform: uppercase;
        letter-spacing: .6px;
        margin-bottom: 18px;
        padding-bottom: 10px;
        border-bottom: 2px solid rgba(22,163,74,.1);
    }

    .form-card .form-control,
    .form-card .form-select {
        border-radius: 12px;
        border: 1.5px solid #e5e7eb;
        font-size: 13.5px;
        padding: 10px 14px;
        transition: all .2s ease;
        background: #f9fafb;
    }

    .form-card .form-control:focus,
    .form-card .form-select:focus {
        border-color: rgba(22,163,74,.5);
        background: #ffffff;
        box-shadow: 0 0 0 3px rgba(34,197,94,.08);
    }

    .form-card label.form-label {
        font-size: 12px;
        font-weight: 700;
        color: #374151;
        margin-bottom: 6px;
    }

    .form-card .input-group .form-control { border-radius: 12px 0 0 12px; }
    .form-card .input-group-text {
        border-radius: 0 12px 12px 0;
        background: #f0fdf4;
        border: 1.5px solid rgba(22,163,74,.2);
        border-left: none;
        color: #16a34a;
        font-weight: 700;
        font-size: 12.5px;
    }

    .photo-upload-box {
        border: 2px dashed rgba(22,163,74,.25);
        border-radius: 14px;
        padding: 20px;
        text-align: center;
        background: #f9fafb;
        cursor: pointer;
        transition: all .2s ease;
    }

    .photo-upload-box:hover {
        border-color: rgba(22,163,74,.5);
        background: #f0fdf4;
    }

    .photo-preview-img {
        max-width: 100%;
        max-height: 180px;
        border-radius: 12px;
        border: 2px solid rgba(22,163,74,.2);
        margin-top: 10px;
        display: none;
    }

    .pks-display {
        padding: 12px 16px;
        border-radius: 12px;
        background: #f0fdf4;
        border: 1.5px solid rgba(22,163,74,.2);
        display: flex;
        align-items: center;
        gap: 10px;
        font-weight: 700;
        font-size: 13.5px;
        color: #14532d;
    }

    /* Dark mode */
    html.app-skin-dark .form-card {
        background: #0a2317 !important;
        border-color: rgba(34,197,94,.15) !important;
    }
    html.app-skin-dark .form-label { color: #d1fae5 !important; }
    html.app-skin-dark .form-card .form-control,
    html.app-skin-dark .form-card .form-select { background:#0e3b26;border-color:rgba(34,197,94,.2);color:#d1fae5; }
    html.app-skin-dark .form-card .input-group-text { background:#052e16;color:#86efac;border-color:rgba(34,197,94,.2); }
    html.app-skin-dark .pks-display { background:#0e3b26;border-color:rgba(34,197,94,.2);color:#86efac; }
</style>
@endsection

@section('content')
<form action="{{ route('pemeliharaan.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <div class="row g-4">
        {{-- Left Column --}}
        <div class="col-lg-8">
            {{-- Informasi Umum --}}
            <div class="form-card">
                <div class="form-section-title">
                    <i class="feather-info"></i> Informasi Umum
                </div>
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Tanggal <span class="text-danger">*</span></label>
                        <input type="date" name="tanggal" class="form-control @error('tanggal') is-invalid @enderror" value="{{ old('tanggal', date('Y-m-d')) }}" required>
                        @error('tanggal') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Jenis Pemeliharaan</label>
                        <select name="jenis_pemeliharaan" class="form-select @error('jenis_pemeliharaan') is-invalid @enderror">
                            <option value="">— Pilih Jenis —</option>
                            <option value="1" {{ old('jenis_pemeliharaan') == '1' ? 'selected' : '' }}>Mekanis</option>
                            <option value="2" {{ old('jenis_pemeliharaan') == '2' ? 'selected' : '' }}>Manual</option>
                        </select>
                        @error('jenis_pemeliharaan') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Jumlah HK <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="text" name="jumlah_hk" class="form-control @error('jumlah_hk') is-invalid @enderror" value="{{ old('jumlah_hk', '1') }}" placeholder="Contoh: 2" required>
                            <span class="input-group-text">Orang</span>
                        </div>
                        @error('jumlah_hk') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>
            </div>

            {{-- Detail Lokasi --}}
            <div class="form-card">
                <div class="form-section-title">
                    <i class="feather-map-pin"></i> Detail Lokasi
                </div>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">No. Bak <span class="text-danger">*</span></label>
                        <input type="text" name="no_bak" class="form-control @error('no_bak') is-invalid @enderror" value="{{ old('no_bak') }}" placeholder="Contoh: 10" required>
                        @error('no_bak') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Blok <span class="text-danger">*</span></label>
                        <input type="text" name="blok" class="form-control @error('blok') is-invalid @enderror" value="{{ old('blok') }}" placeholder="Contoh: 22L" required>
                        @error('blok') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>
            </div>

            {{-- Data Bed --}}
            <div class="form-card">
                <div class="form-section-title">
                    <i class="feather-layers"></i> Data Bed
                </div>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label" style="color:#1d4ed8;">Flat Bed <span class="text-danger">*</span></label>
                        <input type="number" name="flat_bed" class="form-control @error('flat_bed') is-invalid @enderror" value="{{ old('flat_bed', 0) }}" min="0" step="any" required>
                        @error('flat_bed') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" style="color:#b45309;">Long Bed <span class="text-danger">*</span></label>
                        <input type="number" name="long_bed" class="form-control @error('long_bed') is-invalid @enderror" value="{{ old('long_bed', 0) }}" min="0" step="any" required>
                        @error('long_bed') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>
            </div>

            {{-- Foto Dokumentasi --}}
            <div class="form-card">
                <div class="form-section-title">
                    <i class="feather-camera"></i> Foto Dokumentasi
                </div>
                <div class="row g-4">
                    <div class="col-md-6">
                        <label class="form-label d-flex align-items-center">
                            <span class="mod-pill mod-pill-err me-2" style="font-size:10px;">Sebelum</span> Foto Sebelum
                        </label>
                        <div class="photo-upload-box" onclick="document.getElementById('foto_sebelum').click()">
                            <i class="feather-upload-cloud d-block mb-2" style="font-size: 28px; color: #16a34a;"></i>
                            <small class="text-muted d-block" id="label_sebelum">Klik untuk upload foto sebelum</small>
                            <input type="file" name="sebelum" id="foto_sebelum" class="d-none @error('sebelum') is-invalid @enderror" accept="image/*" onchange="previewImage(this, 'preview_sebelum', 'label_sebelum')">
                        </div>
                        <img id="preview_sebelum" class="photo-preview-img" alt="Preview Sebelum">
                        @error('sebelum') <div class="text-danger fs-12 mt-1">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label d-flex align-items-center">
                            <span class="mod-pill mod-pill-ok me-2" style="font-size:10px;">Sesudah</span> Foto Sesudah
                        </label>
                        <div class="photo-upload-box" onclick="document.getElementById('foto_sesudah').click()">
                            <i class="feather-upload-cloud d-block mb-2" style="font-size: 28px; color: #16a34a;"></i>
                            <small class="text-muted d-block" id="label_sesudah">Klik untuk upload foto sesudah</small>
                            <input type="file" name="sesudah" id="foto_sesudah" class="d-none @error('sesudah') is-invalid @enderror" accept="image/*" onchange="previewImage(this, 'preview_sesudah', 'label_sesudah')">
                        </div>
                        <img id="preview_sesudah" class="photo-preview-img" alt="Preview Sesudah">
                        @error('sesudah') <div class="text-danger fs-12 mt-1">{{ $message }}</div> @enderror
                    </div>
                </div>
            </div>
        </div>

        {{-- Right Column --}}
        <div class="col-lg-4">
            {{-- Unit PKS --}}
            <div class="form-card">
                <div class="form-section-title">
                    <i class="feather-home"></i> Unit PKS
                </div>
                @if($user->isAdmin())
                <select name="id_pks" class="form-control @error('id_pks') is-invalid @enderror" data-select2-selector="status" required>
                    <option value="">— Pilih PKS —</option>
                    @foreach($pksList as $pks)
                    <option value="{{ $pks->id_pks }}" {{ old('id_pks') == $pks->id_pks ? 'selected' : '' }}>
                        {{ $pks->nama }} ({{ $pks->akro }})
                    </option>
                    @endforeach
                </select>
                @error('id_pks') <div class="invalid-feedback">{{ $message }}</div> @enderror
                @else
                <input type="hidden" name="id_pks" value="{{ $user->id_pks }}">
                <div class="pks-display">
                    <i class="feather-map-pin" style="color:#16a34a;font-size:18px;"></i>
                    <div>
                        <div style="font-size:14px;font-weight:800;">{{ $user->NAMA }}</div>
                        <div style="font-size:11px;color:#6b7280;">{{ $user->AKRO }}</div>
                    </div>
                </div>
                @endif
            </div>

            {{-- Keterangan --}}
            <div class="form-card">
                <div class="form-section-title">
                    <i class="feather-message-square"></i> Keterangan
                </div>
                <textarea name="keterangan" rows="5" class="form-control @error('keterangan') is-invalid @enderror" placeholder="Tambahkan catatan pemeliharaan...">{{ old('keterangan') }}</textarea>
                @error('keterangan') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            {{-- Action buttons --}}
            <div class="d-grid gap-3">
                <button type="submit" class="btn-ptpn btn-ptpn-primary" style="padding:14px;font-size:15px;font-weight:800;border-radius:14px;">
                    <i class="feather-save" style="font-size:16px;"></i>
                    Simpan Data Pemeliharaan
                </button>
                <a href="{{ route('pemeliharaan.index') }}" class="btn-ptpn btn-ptpn-outline" style="padding:12px;font-size:14px;border-radius:14px;text-align:center;justify-content:center;">
                    <i class="feather-arrow-left" style="font-size:15px;"></i>
                    Kembali
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
    function previewImage(input, previewId, labelId) {
        const preview = document.getElementById(previewId);
        const label = document.getElementById(labelId);
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.src = e.target.result;
                preview.style.display = 'block';
            };
            reader.readAsDataURL(input.files[0]);
            label.textContent = '✓ ' + input.files[0].name;
            label.style.color = '#16a34a';
            label.style.fontWeight = '700';
        }
    }
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof $ !== 'undefined' && $.fn.select2) {
            $('[data-select2-selector]').select2({ width: '100%' });
        }
    });
</script>
@endsection
