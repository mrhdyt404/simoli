@extends('layouts.simoli')

@section('title', 'Edit Rencana')
@section('page-title', 'Edit Rencana Pengaliran & Pemeliharaan')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('rencana.index') }}">Rencana</a></li>
<li class="breadcrumb-item active">Edit Data</li>
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
    .bed-info {
        background: linear-gradient(135deg, #f0fdf4 0%, #ecfdf5 100%);
        border: 1px solid #bbf7d0;
        border-radius: 10px;
        padding: 16px;
    }
    .bed-info.long {
        background: linear-gradient(135deg, #fffbeb 0%, #fef3c7 100%);
        border-color: #fde68a;
    }
</style>
@endsection

@section('content')
<form action="{{ route('rencana.update', $rencana->id) }}" method="POST">
    @csrf
    @method('PUT')
    <div class="row">
        {{-- Left Column --}}
        <div class="col-lg-8">
            <div class="card form-card shadow-sm mb-4">
                <div class="card-body p-4">
                    <div class="form-section-title">
                        <i class="feather-calendar"></i> Periode Rencana
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Tahun <span class="text-danger">*</span></label>
                            <select name="tahun" class="form-select form-select-lg @error('tahun') is-invalid @enderror" required>
                                @for($y = date('Y') + 1; $y >= 2020; $y--)
                                <option value="{{ $y }}" {{ old('tahun', $rencana->tahun) == $y ? 'selected' : '' }}>{{ $y }}</option>
                                @endfor
                            </select>
                            @error('tahun') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <hr class="my-4">

                    <div class="form-section-title">
                        <i class="feather-layers"></i> Target Bed
                    </div>
                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="bed-info">
                                <label class="form-label fw-semibold">
                                    <i class="feather-grid me-1 text-success"></i> Flat Bed <span class="text-danger">*</span>
                                </label>
                                <input type="number" name="flat_bed" class="form-control form-control-lg @error('flat_bed') is-invalid @enderror" value="{{ old('flat_bed', $rencana->flat_bed) }}" min="0" step="any" required>
                                <small class="text-muted mt-1 d-block">Jumlah flat bed yang direncanakan</small>
                                @error('flat_bed') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="bed-info long">
                                <label class="form-label fw-semibold">
                                    <i class="feather-maximize-2 me-1 text-warning"></i> Long Bed <span class="text-danger">*</span>
                                </label>
                                <input type="number" name="long_bed" class="form-control form-control-lg @error('long_bed') is-invalid @enderror" value="{{ old('long_bed', $rencana->long_bed) }}" min="0" step="any" required>
                                <small class="text-muted mt-1 d-block">Jumlah long bed yang direncanakan</small>
                                @error('long_bed') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
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
                        <option value="{{ $pks->ID }}" {{ old('id_pks', $rencana->id_pks) == $pks->ID ? 'selected' : '' }}>
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
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div class="stat-icon bg-soft-info text-info" style="width:44px;height:44px;border-radius:10px;display:flex;align-items:center;justify-content:center;">
                            <i class="feather-file-text" style="font-size:18px;"></i>
                        </div>
                        <div>
                            <h6 class="mb-0 fw-bold">Data Saat Ini</h6>
                            <small class="text-muted">Rencana #{{ $rencana->id }}</small>
                        </div>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted fs-13">PKS</span>
                        <span class="fw-semibold fs-13">{{ $rencana->pks->AKRO ?? 'N/A' }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted fs-13">Tahun</span>
                        <span class="fw-semibold fs-13">{{ $rencana->tahun }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted fs-13">Flat Bed</span>
                        <span class="fw-bold text-success fs-13">{{ number_format($rencana->flat_bed) }}</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="text-muted fs-13">Long Bed</span>
                        <span class="fw-bold text-warning fs-13">{{ number_format($rencana->long_bed) }}</span>
                    </div>
                </div>
            </div>

            <div class="d-grid gap-2">
                <button type="submit" class="btn btn-primary btn-lg">
                    <i class="feather-save me-2"></i> Perbarui Rencana
                </button>
                <a href="{{ route('rencana.index') }}" class="btn btn-outline-secondary btn-lg">
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
