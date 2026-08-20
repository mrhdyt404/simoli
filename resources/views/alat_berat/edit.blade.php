@extends('layouts.simoli')

@section('title', 'Edit Data Alat Berat')
@section('page-title', 'Edit Data Alat Berat')
@section('page-description', 'Perbarui Informasi Master Alat Berat — ' . $alatBerat->kode_alat)

@section('breadcrumb')
    <li><a href="{{ route('alat-berat.index') }}" style="color:inherit;text-decoration:none;">Master Alat Berat</a></li>
    <li class="separator">/</li>
    <li>Edit</li>
@endsection

@section('styles')
<style>
    /* ================================================================
       ALAT BERAT EDIT FORM — PTPN GREEN THEME
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

    .edit-warning {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 12px 16px;
        border-radius: 12px;
        background: #fffbeb;
        border: 1.5px solid #fde68a;
        margin-bottom: 20px;
        font-size: 13px;
        font-weight: 600;
        color: #92400e;
    }

    /* Dark mode */
    html.app-skin-dark .form-card {
        background: #0a2317 !important;
        border-color: rgba(34,197,94,.15) !important;
    }
    html.app-skin-dark .form-label { color: #d1fae5 !important; }
    html.app-skin-dark .form-card .form-control,
    html.app-skin-dark .form-card .form-select { background:#0e3b26;border-color:rgba(34,197,94,.2);color:#d1fae5; }
</style>
@endsection

@section('content')

{{-- Warning Banner --}}
<div class="edit-warning">
    <i class="feather-edit-2" style="font-size:18px;flex-shrink:0;color:#d97706;"></i>
    <span>Anda sedang mengedit master data alat berat <strong>{{ $alatBerat->kode_alat }} - {{ $alatBerat->nama_alat }}</strong>.</span>
</div>

<form action="{{ route('alat-berat.update', $alatBerat->id) }}" method="POST">
    @csrf
    @method('PUT')
    <div class="row g-4">
        {{-- Left Column --}}
        <div class="col-lg-8">
            <div class="form-card">
                <div class="form-section-title">
                    <i class="feather-truck"></i> Informasi Alat Berat
                </div>

                @if ($errors->any())
                    <div class="alert alert-danger mb-4" style="border-radius:12px;">
                        <ul class="mb-0 fs-13">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if($user->isAdmin())
                    <div class="mb-3">
                        <label class="form-label">PKS Unit <span class="text-danger">*</span></label>
                        <select name="id_pks" class="form-select @error('id_pks') is-invalid @enderror" required>
                            <option value="">— Pilih PKS Unit —</option>
                            @foreach($pksList as $pks)
                                <option value="{{ $pks->id_pks }}" {{ old('id_pks', $alatBerat->id_pks) == $pks->id_pks ? 'selected' : '' }}>
                                    {{ $pks->nama }} ({{ $pks->akro }})
                                </option>
                            @endforeach
                        </select>
                        @error('id_pks') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                @endif

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Kode Alat Berat <span class="text-danger">*</span></label>
                        <input type="text" name="kode_alat" class="form-control @error('kode_alat') is-invalid @enderror" value="{{ old('kode_alat', $alatBerat->kode_alat) }}" required>
                        @error('kode_alat') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
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

                <div class="my-3">
                    <label class="form-label">Nama / Deskripsi Alat Berat <span class="text-danger">*</span></label>
                    <input type="text" name="nama_alat" class="form-control @error('nama_alat') is-invalid @enderror" value="{{ old('nama_alat', $alatBerat->nama_alat) }}" required>
                    @error('nama_alat') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Merk / Tipe</label>
                        <input type="text" name="merk_tipe" class="form-control @error('merk_tipe') is-invalid @enderror" value="{{ old('merk_tipe', $alatBerat->merk_tipe) }}">
                        @error('merk_tipe') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
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

                <div>
                    <label class="form-label">Keterangan / Catatan Tambahan</label>
                    <textarea name="keterangan" class="form-control" rows="3">{{ old('keterangan', $alatBerat->keterangan) }}</textarea>
                </div>
            </div>
        </div>

        {{-- Right Column --}}
        <div class="col-lg-4">
            {{-- Current info --}}
            <div class="form-card">
                <div class="d-flex align-items-center gap-3 mb-3">
                    <div style="width:40px;height:40px;border-radius:10px;display:flex;align-items:center;justify-content:center;background:#eff6ff;color:#1d4ed8;border:1px solid #bfdbfe;">
                        <i class="feather-truck" style="font-size:18px;"></i>
                    </div>
                    <div>
                        <h6 class="mb-0 fw-bold" style="font-family:'Outfit',sans-serif;color:#14532d;">Unit Saat Ini</h6>
                        <small style="color:#6b7280;font-size:11.5px;">{{ $alatBerat->kode_alat }}</small>
                    </div>
                </div>
                <div class="d-flex justify-content-between py-2 border-bottom">
                    <span style="color:#6b7280;font-size:12.5px;">PKS</span>
                    <span class="mod-pill mod-pill-ok" style="font-size:10.5px;">{{ $alatBerat->pks->akro ?? '-' }}</span>
                </div>
                <div class="d-flex justify-content-between py-2 border-bottom">
                    <span style="color:#6b7280;font-size:12.5px;">Jenis Alat</span>
                    <span style="font-weight:700;font-size:12.5px;color:#14532d;">{{ $alatBerat->jenis_alat }}</span>
                </div>
                <div class="d-flex justify-content-between pt-2">
                    <span style="color:#6b7280;font-size:12.5px;">Status</span>
                    <span style="font-weight:700;font-size:12.5px;color:#16a34a;">{{ $alatBerat->status }}</span>
                </div>
            </div>

            {{-- Action buttons --}}
            <div class="d-grid gap-3">
                <button type="submit" class="btn-ptpn btn-ptpn-primary" style="padding:14px;font-size:15px;font-weight:800;border-radius:14px;">
                    <i class="feather-save" style="font-size:16px;"></i>
                    Perbarui Data Alat Berat
                </button>
                <a href="{{ route('alat-berat.index') }}" class="btn-ptpn btn-ptpn-outline" style="padding:12px;font-size:14px;border-radius:14px;text-align:center;justify-content:center;">
                    <i class="feather-arrow-left" style="font-size:15px;"></i>
                    Kembali
                </a>
            </div>
        </div>
    </div>
</form>
@endsection
