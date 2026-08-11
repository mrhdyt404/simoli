@extends('layouts.simoli')

@section('title', 'Edit Data Alat Berat')
@section('page-title', 'Edit Data Alat Berat')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('alat-berat.index') }}">Master Alat Berat</a></li>
<li class="breadcrumb-item active">Edit</li>
@endsection

@section('page-actions')
<a href="{{ route('alat-berat.index') }}" class="btn btn-light">
    <i class="feather-arrow-left me-1"></i> Kembali
</a>
@endsection

@section('content')
<div class="row">
            <div class="col-lg-8 col-12">
                <div class="card stretch stretch-full">
                    <div class="card-header">
                        <h5 class="card-title">Edit Master Alat Berat - {{ $alatBerat->kode_alat }}</h5>
                    </div>
                    <div class="card-body">
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form action="{{ route('alat-berat.update', $alatBerat->id) }}" method="POST">
                            @csrf
                            @method('PUT')

                            @if($user->isAdmin())
                                <div class="mb-3">
                                    <label class="form-label">PKS Unit <span class="text-danger">*</span></label>
                                    <select name="id_pks" class="form-select @error('id_pks') is-invalid @enderror" required>
                                        <option value="">-- Pilih PKS Unit --</option>
                                        @foreach($pksList as $pks)
                                            <option value="{{ $pks->id_pks }}" {{ old('id_pks', $alatBerat->id_pks) == $pks->id_pks ? 'selected' : '' }}>
                                                {{ $pks->nama }} ({{ $pks->akro }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('id_pks') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            @endif

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Kode Alat Berat <span class="text-danger">*</span></label>
                                    <input type="text" name="kode_alat" class="form-control @error('kode_alat') is-invalid @enderror" value="{{ old('kode_alat', $alatBerat->kode_alat) }}" required>
                                    @error('kode_alat') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Jenis Alat Berat <span class="text-danger">*</span></label>
                                    <select name="jenis_alat" class="form-select @error('jenis_alat') is-invalid @enderror" required>
                                        <option value="Excavator" {{ old('jenis_alat', $alatBerat->jenis_alat) == 'Excavator' ? 'selected' : '' }}>Excavator</option>
                                        <option value="Wheel Loader" {{ old('jenis_alat', $alatBerat->jenis_alat) == 'Wheel Loader' ? 'selected' : '' }}>Wheel Loader</option>
                                        <option value="Bulldozer" {{ old('jenis_alat', $alatBerat->jenis_alat) == 'Bulldozer' ? 'selected' : '' }}>Bulldozer</option>
                                        <option value="Dump Truck" {{ old('jenis_alat', $alatBerat->jenis_alat) == 'Dump Truck' ? 'selected' : '' }}>Dump Truck</option>
                                        <option value="Compactor" {{ old('jenis_alat', $alatBerat->jenis_alat) == 'Compactor' ? 'selected' : '' }}>Compactor</option>
                                        <option value="Lainnya" {{ old('jenis_alat', $alatBerat->jenis_alat) == 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
                                    </select>
                                    @error('jenis_alat') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Nama / Deskripsi Alat Berat <span class="text-danger">*</span></label>
                                <input type="text" name="nama_alat" class="form-control @error('nama_alat') is-invalid @enderror" value="{{ old('nama_alat', $alatBerat->nama_alat) }}" required>
                                @error('nama_alat') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Merk / Tipe</label>
                                    <input type="text" name="merk_tipe" class="form-control @error('merk_tipe') is-invalid @enderror" value="{{ old('merk_tipe', $alatBerat->merk_tipe) }}">
                                    @error('merk_tipe') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Tahun Pengadaan</label>
                                    <input type="number" name="tahun_pengadaan" class="form-control @error('tahun_pengadaan') is-invalid @enderror" min="1900" max="{{ date('Y') }}" value="{{ old('tahun_pengadaan', $alatBerat->tahun_pengadaan) }}">
                                    @error('tahun_pengadaan') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Status Operasional <span class="text-danger">*</span></label>
                                <select name="status" class="form-select @error('status') is-invalid @enderror" required>
                                    <option value="Operational" {{ old('status', $alatBerat->status) == 'Operational' ? 'selected' : '' }}>Ready / Operational</option>
                                    <option value="Maintenance" {{ old('status', $alatBerat->status) == 'Maintenance' ? 'selected' : '' }}>Maintenance / Dalam Perbaikan</option>
                                    <option value="Breakdown" {{ old('status', $alatBerat->status) == 'Breakdown' ? 'selected' : '' }}>Breakdown / Rusak</option>
                                    <option value="Standby" {{ old('status', $alatBerat->status) == 'Standby' ? 'selected' : '' }}>Standby / Cadangan</option>
                                    <option value="Rolling" {{ old('status', $alatBerat->status) == 'Rolling' ? 'selected' : '' }}>Rolling / Dipinjam Kebun Lain</option>
                                </select>
                                @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Keterangan / Catatan Tambahan</label>
                                <textarea name="keterangan" class="form-control" rows="3">{{ old('keterangan', $alatBerat->keterangan) }}</textarea>
                            </div>

                            <div class="d-flex justify-content-between mt-4">
                                <a href="{{ route('alat-berat.index') }}" class="btn btn-light">Batal</a>
                                <button type="submit" class="btn btn-primary"><i class="feather-save me-2"></i>Perbarui Data</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
@endsection
