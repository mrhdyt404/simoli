@extends('layouts.simoli')

@section('title', 'Tambah Data Pengaliran')
@section('page-title', 'Tambah Data Pengaliran')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('pengaliran.index') }}">Pengaliran</a></li>
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
    </style>
@endsection

@section('content')
    <form action="{{ route('pengaliran.store') }}" method="POST" enctype="multipart/form-data">
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
                                <input type="date" name="tanggal"
                                    class="form-control @error('tanggal') is-invalid @enderror"
                                    value="{{ old('tanggal', date('Y-m-d')) }}" required>
                                @error('tanggal') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Jam Mulai <span class="text-danger">*</span></label>
                                <input type="time" name="jam_mulai"
                                    class="form-control @error('jam_mulai') is-invalid @enderror"
                                    value="{{ old('jam_mulai', '07:00') }}" required>
                                @error('jam_mulai') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Jam Selesai <span class="text-danger">*</span></label>
                                <input type="time" name="jam_selesai"
                                    class="form-control @error('jam_selesai') is-invalid @enderror"
                                    value="{{ old('jam_selesai', '18:00') }}" required>
                                @error('jam_selesai') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <hr class="my-4">

                        <div class="form-section-title">
                            <i class="feather-map-pin"></i> Detail Lokasi
                        </div>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">No. Bak <span class="text-danger">*</span></label>
                                <input type="text" name="no_bak" class="form-control @error('no_bak') is-invalid @enderror"
                                    value="{{ old('no_bak') }}" placeholder="Contoh: 5-6" required>
                                @error('no_bak') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Blok <span class="text-danger">*</span></label>
                                <input type="text" name="blok" class="form-control @error('blok') is-invalid @enderror"
                                    value="{{ old('blok') }}" placeholder="Contoh: C6, D6A" required>
                                @error('blok') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Rotasi</label>
                                <input type="text" name="rotasi" class="form-control @error('rotasi') is-invalid @enderror"
                                    value="{{ old('rotasi') }}" placeholder="Contoh: 7 Hari">
                                @error('rotasi') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <hr class="my-4">

                        <div class="form-section-title">
                            <i class="feather-droplet"></i> Data Volume
                        </div>
                        <div class="row g-3">

                            <!-- Flat Bed -->
                            <div class="col-md-3">
                                <label class="form-label fw-semibold">
                                    Flat Bed <span class="text-danger">*</span>
                                </label>
                                <input type="number" name="flat_bed"
                                    class="form-control @error('flat_bed') is-invalid @enderror"
                                    value="{{ old('flat_bed', 0) }}" min="0" step="1" required>
                                @error('flat_bed')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Volume Limbah Dihasilkan -->
                            <div class="col-md-3">
                                <label class="form-label fw-semibold">
                                    Vol. Dihasilkan <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <input type="number" name="vol_limbah_dihasilkan"
                                        class="form-control @error('vol_limbah_dihasilkan') is-invalid @enderror"
                                        value="{{ old('vol_limbah_dihasilkan', 0) }}" min="0" step="1" required>
                                    <span class="input-group-text">m&sup3;</span>
                                </div>
                                @error('vol_limbah_dihasilkan')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Volume Limbah Dialirkan -->
                            <div class="col-md-3">
                                <label class="form-label fw-semibold">
                                    Vol. Dialirkan <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <input type="number" name="vol_limbah_dialirkan"
                                        class="form-control @error('vol_limbah_dialirkan') is-invalid @enderror"
                                        value="{{ old('vol_limbah_dialirkan', 0) }}" min="0" step="1" required>
                                    <span class="input-group-text">m&sup3;</span>
                                </div>
                                @error('vol_limbah_dialirkan')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Luas Area -->
                            <div class="col-md-3">
                                <label class="form-label fw-semibold">
                                    Luas Area <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <input type="number" name="luas_area"
                                        class="form-control @error('luas_area') is-invalid @enderror"
                                        value="{{ old('luas_area', 0) }}" min="0" step="1" required>
                                    <span class="input-group-text">Ha</span>
                                </div>
                                @error('luas_area')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
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
                            <select name="id_pks" class="form-control @error('id_pks') is-invalid @enderror"
                                data-select2-selector="status" required>
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
                        <textarea name="keterangan" rows="4" class="form-control @error('keterangan') is-invalid @enderror"
                            placeholder="Tambahkan catatan...">{{ old('keterangan') }}</textarea>
                        @error('keterangan') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="card form-card shadow-sm mb-4">
                    <div class="card-body p-4">
                        <div class="form-section-title">
                            <i class="feather-camera"></i> Foto Dokumentasi
                        </div>
                        <input type="file" name="foto" class="form-control @error('foto') is-invalid @enderror"
                            accept="image/*">
                        <small class="text-muted mt-1 d-block">Format: JPG, JPEG, PNG (max 2MB)</small>
                        @error('foto') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="feather-save me-2"></i> Simpan Data
                    </button>
                    <a href="{{ route('pengaliran.index') }}" class="btn btn-outline-secondary btn-lg">
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
@endsection