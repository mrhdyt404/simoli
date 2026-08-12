@extends('layouts.simoli')

@section('title', 'Tambah Data Pengguna')
@section('page-title', 'Tambah Data Pengguna')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('pengguna.index') }}">Data Pengguna</a></li>
    <li class="breadcrumb-item active">Tambah Data</li>
@endsection

@section('styles')
    <style>
        .form-card {
            border: none;
            border-radius: 14px;
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
    <form action="{{ route('pengguna.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="row g-4">
            <div class="col-lg-8">
                <div class="card form-card shadow-sm">
                    <div class="card-body p-4">
                        <div class="form-section-title"><i class="feather-user-plus"></i> Informasi Pengguna</div>
                        <div class="row g-3">

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">
                                    PKS <span class="text-danger">*</span>
                                </label>

                                <select name="id_pks" class="form-select @error('id_pks') is-invalid @enderror" required>

                                    <option value="">-- Pilih PKS --</option>

                                    @foreach($pks as $item)
                                        <option value="{{ $item->id_pks }}" {{ old('id_pks') == $item->id_pks ? 'selected' : '' }}>
                                            {{ $item->nama }}
                                        </option>
                                    @endforeach

                                </select>

                                @error('id_pks')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>


                            <div class="col-md-6">
                                <label class="form-label fw-semibold">
                                    Username <span class="text-danger">*</span>
                                </label>

                                <input type="text" name="username"
                                    class="form-control @error('username') is-invalid @enderror"
                                    value="{{ old('username') }}" required>

                                @error('username')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>


                            <div class="col-md-6">
                                <label class="form-label fw-semibold">
                                    Password <span class="text-danger">*</span>
                                </label>

                                <input type="text" name="password"
                                    class="form-control @error('password') is-invalid @enderror"
                                    value="{{ old('password') }}" required>

                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>


                            <div class="col-md-6">
                                <label class="form-label fw-semibold">
                                    Level Akses <span class="text-danger">*</span>
                                </label>

                                <select name="level_akses" class="form-select @error('level_akses') is-invalid @enderror"
                                    required>

                                    <option value="">Pilih Level Akses</option>
                                    <option value="admin" {{ old('level_akses') == 'admin' ? 'selected' : '' }}>
                                        Admin SIMOLII
                                    </option>

                                    <option value="unit" {{ old('level_akses') == 'unit' ? 'selected' : '' }}>
                                        Unit PKS
                                    </option>

                                    <option value="mandor" {{ old('level_akses') == 'mandor' ? 'selected' : '' }}>
                                        Mandor Lapangan (Akses Laporan & Kelola/Status Alat)
                                    </option>

                                    <option value="operator" {{ old('level_akses') == 'operator' ? 'selected' : '' }}>
                                        Operator Lapangan (Khusus Laporan Kerja)
                                    </option>

                                </select>

                                @error('level_akses')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary btn-lg"><i class="feather-save me-2"></i>Simpan
                        Data</button>
                    <a href="{{ route('pengguna.index') }}" class="btn btn-outline-secondary btn-lg"><i
                            class="feather-arrow-left me-2"></i>Kembali</a>
                </div>
            </div>
        </div>
    </form>
@endsection