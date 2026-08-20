@extends('layouts.simoli')

@section('title', 'Edit Data Pengguna')
@section('page-title', 'Edit Data Pengguna')
@section('page-description', 'Perbarui Informasi Akun Pengguna — ' . $user->username)

@section('breadcrumb')
    <li><a href="{{ route('pengguna.index') }}" style="color:inherit;text-decoration:none;">Data Pengguna</a></li>
    <li class="separator">/</li>
    <li>Edit Data</li>
@endsection

@section('styles')
<style>
    /* ================================================================
       PENGGUNA EDIT FORM — PTPN GREEN THEME
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
    <span>Anda sedang mengedit data akun pengguna <strong>{{ $user->username }}</strong> ({{ strtoupper($user->level_akses) }}).</span>
</div>

<form action="{{ route('pengguna.update', $user->ID) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')
    <div class="row g-4">
        {{-- Left Column --}}
        <div class="col-lg-8">
            <div class="form-card">
                <div class="form-section-title">
                    <i class="feather-user-check"></i> Informasi Akun Pengguna
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

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Unit PKS <span class="text-danger">*</span></label>
                        <select name="id_pks" class="form-select @error('id_pks') is-invalid @enderror" required>
                            @foreach($pks as $item)
                                <option value="{{ $item->id_pks }}" {{ old('id_pks', $user->id_pks) == $item->id_pks ? 'selected' : '' }}>
                                    {{ $item->nama }} ({{ $item->akro ?? $item->AKRO ?? '-' }})
                                </option>
                            @endforeach
                        </select>
                        @error('id_pks') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Username Akses <span class="text-danger">*</span></label>
                        <input type="text" name="username" class="form-control @error('username') is-invalid @enderror" value="{{ old('username', $user->username) }}" required>
                        @error('username') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Password Login <span class="text-danger">*</span></label>
                        <input type="text" name="password" class="form-control @error('password') is-invalid @enderror" value="{{ old('password', $user->password) }}" required>
                        @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Level Akses System <span class="text-danger">*</span></label>
                        <select name="level_akses" class="form-select @error('level_akses') is-invalid @enderror" required>
                            <option value="admin" {{ old('level_akses', $user->level_akses) == 'admin' ? 'selected' : '' }}>Admin SIMOLI</option>
                            <option value="unit" {{ old('level_akses', $user->level_akses) == 'unit' ? 'selected' : '' }}>Unit PKS</option>
                            <option value="mandor" {{ old('level_akses', $user->level_akses) == 'mandor' ? 'selected' : '' }}>Mandor Lapangan</option>
                            <option value="operator" {{ old('level_akses', $user->level_akses) == 'operator' ? 'selected' : '' }}>Operator Lapangan</option>
                        </select>
                        @error('level_akses') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>
            </div>
        </div>

        {{-- Right Column --}}
        <div class="col-lg-4">
            {{-- Current Account Summary Widget --}}
            <div class="form-card">
                <div class="d-flex align-items-center gap-3 mb-3">
                    <div style="width:40px;height:40px;border-radius:10px;display:flex;align-items:center;justify-content:center;background:#eff6ff;color:#1d4ed8;border:1px solid #bfdbfe;">
                        <i class="feather-user" style="font-size:18px;"></i>
                    </div>
                    <div>
                        <h6 class="mb-0 fw-bold" style="font-family:'Outfit',sans-serif;color:#14532d;">Akun Terdaftar</h6>
                        <small style="color:#6b7280;font-size:11.5px;">{{ $user->username }}</small>
                    </div>
                </div>
                <div class="d-flex justify-content-between py-2 border-bottom">
                    <span style="color:#6b7280;font-size:12.5px;">Unit PKS</span>
                    <span class="mod-pill mod-pill-ok" style="font-size:10.5px;">{{ $user->pks->AKRO ?? $user->pks->akro ?? '-' }}</span>
                </div>
                <div class="d-flex justify-content-between pt-2">
                    <span style="color:#6b7280;font-size:12.5px;">Level Akses</span>
                    <span style="font-weight:800;font-size:12.5px;color:#16a34a;">{{ strtoupper($user->level_akses) }}</span>
                </div>
            </div>

            {{-- Action buttons --}}
            <div class="d-grid gap-3">
                <button type="submit" class="btn-ptpn btn-ptpn-primary" style="padding:14px;font-size:15px;font-weight:800;border-radius:14px;">
                    <i class="feather-save" style="font-size:16px;"></i>
                    Perbarui Data Pengguna
                </button>
                <a href="{{ route('pengguna.index') }}" class="btn-ptpn btn-ptpn-outline" style="padding:12px;font-size:14px;border-radius:14px;text-align:center;justify-content:center;">
                    <i class="feather-arrow-left" style="font-size:15px;"></i>
                    Kembali
                </a>
            </div>
        </div>
    </div>
</form>
@endsection