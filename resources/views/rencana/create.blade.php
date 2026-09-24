@extends('layouts.simoli')

@section('title', 'Tambah Rencana')
@section('page-title', 'Tambah Rencana Pengaliran & Pemeliharaan')
@section('page-description', 'Input Target Tahunan Rencana Flat Bed & Long Bed')

@section('breadcrumb')
    <li><a href="{{ route('rencana.index') }}" style="color:inherit;text-decoration:none;">Rencana</a></li>
    <li class="separator">/</li>
    <li>Tambah Data</li>
@endsection

@section('styles')
<link rel="stylesheet" type="text/css" href="{{ asset('duraluxadmin/assets/vendors/css/select2.min.css') }}" />
<link rel="stylesheet" type="text/css" href="{{ asset('duraluxadmin/assets/vendors/css/select2-theme.min.css') }}" />
<style>
    /* ================================================================
       RENCANA CREATE FORM — PTPN GREEN THEME
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

    .bed-info {
        background: #eff6ff;
        border: 1.5px solid #bfdbfe;
        border-radius: 14px;
        padding: 18px;
    }

    .bed-info.long {
        background: #fffbeb;
        border-color: #fde68a;
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
    html.app-skin-dark .pks-display { background:#0e3b26;border-color:rgba(34,197,94,.2);color:#86efac; }
    html.app-skin-dark .bed-info { background:#0e3b26;border-color:rgba(37,99,235,.2); }
    html.app-skin-dark .bed-info.long { background:#1c1000;border-color:rgba(217,119,6,.2); }
</style>
@endsection

@section('content')
<form action="{{ route('rencana.store') }}" method="POST">
    @csrf
    <div class="row g-4">
        {{-- Left Column --}}
        <div class="col-lg-8">
            <div class="form-card">
                <div class="form-section-title">
                    <i class="feather-calendar"></i> Periode Rencana
                </div>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Tahun Periode <span class="text-danger">*</span></label>
                        <select name="tahun" class="form-select @error('tahun') is-invalid @enderror" required>
                            @for($y = date('Y') + 1; $y >= 2020; $y--)
                            <option value="{{ $y }}" {{ old('tahun', date('Y')) == $y ? 'selected' : '' }}>{{ $y }}</option>
                            @endfor
                        </select>
                        @error('tahun') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>

                <hr class="my-4" style="border-color:rgba(22,163,74,.1);">

                <div class="form-section-title">
                    <i class="feather-layers"></i> Target Bed
                </div>
                <div class="row g-4">
                    <div class="col-md-6">
                        <div class="bed-info">
                            <label class="form-label d-flex align-items-center" style="color:#1d4ed8;">
                                <i class="feather-grid me-2"></i> Flat Bed <span class="text-danger">*</span>
                            </label>
                            <input type="number" name="flat_bed" class="form-control form-control-lg @error('flat_bed') is-invalid @enderror" value="{{ old('flat_bed', 0) }}" min="0" step="any" style="color:#1d4ed8;font-weight:800;" required>
                            <small class="text-muted mt-1 d-block" style="font-size:11px;">Jumlah flat bed yang direncanakan</small>
                            @error('flat_bed') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="bed-info long">
                            <label class="form-label d-flex align-items-center" style="color:#b45309;">
                                <i class="feather-maximize-2 me-2"></i> Long Bed <span class="text-danger">*</span>
                            </label>
                            <input type="number" name="long_bed" class="form-control form-control-lg @error('long_bed') is-invalid @enderror" value="{{ old('long_bed', 0) }}" min="0" step="any" style="color:#b45309;font-weight:800;" required>
                            <small class="text-muted mt-1 d-block" style="font-size:11px;">Jumlah long bed yang direncanakan</small>
                            @error('long_bed') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
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
                        <div style="font-size:14px;font-weight:800;">{{ $user->pks ? $user->pks->nama : 'PKS' }}</div>
                        <div style="font-size:11px;color:#6b7280;">{{ $user->pks ? $user->pks->akro : '' }}</div>
                    </div>
                </div>
                @endif
            </div>

            {{-- Guide Card --}}
            <div class="form-card">
                <div class="d-flex align-items-center gap-3 mb-3">
                    <div style="width:40px;height:40px;border-radius:10px;display:flex;align-items:center;justify-content:center;background:#f0fdf4;color:#16a34a;border:1px solid #bbf7d0;">
                        <i class="feather-info" style="font-size:18px;"></i>
                    </div>
                    <div>
                        <h6 class="mb-0 fw-bold" style="font-family:'Outfit',sans-serif;color:#14532d;">Petunjuk Pengisian</h6>
                        <small style="color:#6b7280;font-size:11.5px;">Panduan penyusunan rencana</small>
                    </div>
                </div>
                <ul class="list-unstyled mb-0" style="font-size:12.5px;color:#4b5563;">
                    <li class="mb-2 d-flex align-items-start gap-2">
                        <i class="feather-check-circle text-success mt-1" style="font-size:13px;flex-shrink:0;"></i>
                        <span>Pilih unit PKS yang akan buat rencana</span>
                    </li>
                    <li class="mb-2 d-flex align-items-start gap-2">
                        <i class="feather-check-circle text-success mt-1" style="font-size:13px;flex-shrink:0;"></i>
                        <span>Tentukan tahun periode target</span>
                    </li>
                    <li class="mb-2 d-flex align-items-start gap-2">
                        <i class="feather-check-circle text-success mt-1" style="font-size:13px;flex-shrink:0;"></i>
                        <span>Masukkan target flat bed &amp; long bed</span>
                    </li>
                    <li class="d-flex align-items-start gap-2">
                        <i class="feather-check-circle text-success mt-1" style="font-size:13px;flex-shrink:0;"></i>
                        <span>Isi 0 jika tidak ada target bed</span>
                    </li>
                </ul>
            </div>

            {{-- Action buttons --}}
            <div class="d-grid gap-3">
                <button type="submit" class="btn-ptpn btn-ptpn-primary" style="padding:14px;font-size:15px;font-weight:800;border-radius:14px;">
                    <i class="feather-save" style="font-size:16px;"></i>
                    Simpan Rencana Target
                </button>
                <a href="{{ route('rencana.index') }}" class="btn-ptpn btn-ptpn-outline" style="padding:12px;font-size:14px;border-radius:14px;text-align:center;justify-content:center;">
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
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof $ !== 'undefined' && $.fn.select2) {
            $('[data-select2-selector]').select2({ width: '100%' });
        }
    });
</script>
@endsection
