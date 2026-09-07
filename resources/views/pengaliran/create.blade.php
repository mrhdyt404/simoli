@extends('layouts.simoli')

@section('title', 'Tambah Data Pengaliran')
@section('page-title', 'Tambah Data Pengaliran Land Aplikasi')
@section('page-description', 'Input data harian pengaliran limbah ke Land Aplikasi')

@section('breadcrumb')
    <li><a href="{{ route('pengaliran.index') }}" style="color:inherit;text-decoration:none;">Pengaliran</a></li>
    <li class="separator">/</li>
    <li>Tambah Data</li>
@endsection

@section('styles')
<link rel="stylesheet" type="text/css" href="{{ asset('duraluxadmin/assets/vendors/css/select2.min.css') }}" />
<link rel="stylesheet" type="text/css" href="{{ asset('duraluxadmin/assets/vendors/css/select2-theme.min.css') }}" />
<style>
    /* ================================================================
       PENGALIRAN CREATE/EDIT FORM — PTPN GREEN THEME
       ================================================================ */

    /* === PROGRESS PREVIEW WIDGET === */
    .progress-preview {
        border-radius: 18px;
        overflow: hidden;
        border: 1px solid rgba(22,163,74,.2);
        box-shadow: 0 4px 20px rgba(22,163,74,.08);
        margin-bottom: 24px;
        animation: fadeUpCard .4s ease-out;
    }

    @keyframes fadeUpCard {
        from { opacity:0; transform:translateY(12px); }
        to   { opacity:1; transform:translateY(0); }
    }

    .progress-preview-header {
        padding: 14px 20px;
        background: linear-gradient(90deg, #052e16 0%, #14532d 50%, #166534 100%);
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .progress-preview-title {
        font-family: 'Outfit', sans-serif;
        font-size: 13.5px;
        font-weight: 800;
        color: #86efac;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .progress-preview-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 4px 12px;
        border-radius: 50px;
        background: rgba(255,255,255,.15);
        color: #ffffff;
        font-size: 12px;
        font-weight: 800;
        border: 1px solid rgba(255,255,255,.2);
    }

    .progress-preview-body { background: #ffffff; padding: 16px; }

    .progress-tile {
        border-radius: 14px;
        padding: 14px 16px;
        height: 100%;
    }
    .progress-tile-week   { background: #f0fdf4; border: 1px solid #bbf7d0; }
    .progress-tile-month  { background: #fffbeb; border: 1px solid #fde68a; }

    .progress-tile-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 10px;
    }

    .progress-tile-label {
        font-size: 10.5px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: .5px;
    }
    .progress-tile-label-week  { color: #15803d; }
    .progress-tile-label-month { color: #b45309; }

    .progress-tile-val {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 3px 10px;
        border-radius: 50px;
        font-size: 11.5px;
        font-weight: 800;
        color: #ffffff;
    }
    .val-week  { background: #16a34a; }
    .val-month { background: #d97706; }

    .progress-tile-row {
        display: flex;
        justify-content: space-between;
        gap: 8px;
        font-size: 12px;
    }
    .progress-tile-key { font-weight: 700; color: #6b7280; }
    .progress-tile-data { font-weight: 700; color: #1f2937; text-align: right; }

    /* === FORM CARD === */
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

    .form-section-title i { font-size: 16px; }

    /* Form controls */
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

    /* PKS display (non-admin) */
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

    /* Quick keterangan chips */
    .keterangan-chip {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 5px 12px;
        border-radius: 50px;
        font-size: 11.5px;
        font-weight: 700;
        cursor: pointer;
        transition: all .2s ease;
        border: 1.5px solid rgba(22,163,74,.2);
        background: #f0fdf4;
        color: #166534;
    }
    .keterangan-chip:hover { background: #dcfce7; border-color: rgba(22,163,74,.4); transform: translateY(-1px); }

    /* Photo upload area */
    .photo-upload-area {
        border: 2px dashed rgba(22,163,74,.25);
        border-radius: 14px;
        padding: 20px;
        text-align: center;
        background: #f9fafb;
        cursor: pointer;
        transition: all .2s ease;
    }

    .photo-upload-area:hover {
        border-color: rgba(22,163,74,.5);
        background: #f0fdf4;
    }

    .photo-upload-area input[type="file"] {
        position: absolute;
        inset: 0;
        opacity: 0;
        cursor: pointer;
    }

    .photo-upload-wrap {
        position: relative;
    }

    /* Numeric input highlight */
    input[type="number"].form-control { font-weight: 800; font-size: 15px; }

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
    html.app-skin-dark .keterangan-chip { background:#0e3b26;border-color:rgba(34,197,94,.2);color:#86efac; }
    html.app-skin-dark .progress-preview-body { background:#0a2317; }
    html.app-skin-dark .progress-tile-week  { background:#0e3b26;border-color:rgba(34,197,94,.15); }
    html.app-skin-dark .progress-tile-month { background:#1c1000;border-color:rgba(217,119,6,.2); }
    html.app-skin-dark .progress-tile-data  { color:#d1fae5; }
</style>
@endsection

@section('content')

{{-- ================================================================
     PROGRESS WIDGET — Format PENGALIRAN LAND APLIKASI OF THE MONTH
     ================================================================ --}}
@if(isset($pksProgress))
<div class="progress-preview">
    <div class="progress-preview-header">
        <div class="progress-preview-title">
            <i class="feather-activity"></i>
            Rekap Pengaliran LA SIMOLI — PKS {{ $pksProgress->akro }} ({{ $pksProgress->nama }})
        </div>
        <div class="progress-preview-badge">
            <i class="feather-layers" style="font-size:13px;"></i>
            Total Bed: {{ number_format($pksProgress->total_bed, 0, ',', '.') }}
        </div>
    </div>
    <div class="progress-preview-body">
        <div class="row g-3">
            <div class="col-md-6">
                <div class="progress-tile progress-tile-week">
                    <div class="progress-tile-header">
                        <span class="progress-tile-label progress-tile-label-week">
                            <i class="feather-calendar" style="font-size:13px;margin-right:4px;"></i>Progress Minggu Ini
                        </span>
                        <span class="progress-tile-val val-week">
                            <i class="feather-layers" style="font-size:12px;"></i>
                            {{ number_format($pksProgress->minggu_ini->bed_dialirkan, 0, ',', '.') }} Bed
                        </span>
                    </div>
                    <div class="progress-tile-row">
                        <div>
                            <div class="progress-tile-key">Block Pengaliran</div>
                            <div class="progress-tile-data">{{ $pksProgress->minggu_ini->blok }}</div>
                        </div>
                        <div style="text-align:right;">
                            <div class="progress-tile-key">Bak Distribusi</div>
                            <div class="progress-tile-data">{{ $pksProgress->minggu_ini->bak }}</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="progress-tile progress-tile-month">
                    <div class="progress-tile-header">
                        <span class="progress-tile-label progress-tile-label-month">
                            <i class="feather-clock" style="font-size:13px;margin-right:4px;"></i>Progress S.d Bulan Ini
                        </span>
                        <span class="progress-tile-val val-month">
                            <i class="feather-layers" style="font-size:12px;"></i>
                            {{ number_format($pksProgress->sd_bulan_ini->bed_dialirkan, 0, ',', '.') }} Bed
                        </span>
                    </div>
                    <div class="progress-tile-row">
                        <div>
                            <div class="progress-tile-key">Block Pengaliran</div>
                            <div class="progress-tile-data">{{ $pksProgress->sd_bulan_ini->blok }}</div>
                        </div>
                        <div style="text-align:right;">
                            <div class="progress-tile-key">Bak Distribusi</div>
                            <div class="progress-tile-data">{{ $pksProgress->sd_bulan_ini->bak }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endif

<form action="{{ route('pengaliran.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <div class="row g-4">

        {{-- ============ LEFT COLUMN ============ --}}
        <div class="col-lg-8">

            {{-- Waktu Operasional --}}
            <div class="form-card">
                <div class="form-section-title">
                    <i class="feather-clock"></i>
                    Informasi Waktu Operasional
                </div>
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Tanggal <span class="text-danger">*</span></label>
                        <input type="date" name="tanggal"
                            class="form-control @error('tanggal') is-invalid @enderror"
                            value="{{ old('tanggal', date('Y-m-d')) }}" required>
                        @error('tanggal') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Jam Mulai <span class="text-danger">*</span></label>
                        <input type="time" name="jam_mulai"
                            class="form-control @error('jam_mulai') is-invalid @enderror"
                            value="{{ old('jam_mulai', '07:00') }}" required>
                        @error('jam_mulai') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Jam Selesai <span class="text-danger">*</span></label>
                        <input type="time" name="jam_selesai"
                            class="form-control @error('jam_selesai') is-invalid @enderror"
                            value="{{ old('jam_selesai', '18:00') }}" required>
                        @error('jam_selesai') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>
            </div>

            {{-- Lokasi Pengaliran --}}
            <div class="form-card">
                <div class="form-section-title">
                    <i class="feather-map-pin"></i>
                    Detail Lokasi Pengaliran Land Aplikasi
                </div>
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Block Pengaliran <span class="text-danger">*</span></label>
                        <input type="text" name="blok"
                            class="form-control @error('blok') is-invalid @enderror"
                            value="{{ old('blok') }}" placeholder="Contoh: F4, L25, 22K" required>
                        <small class="text-muted" style="font-size:11px;">Blok area pengaliran lahan</small>
                        @error('blok') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Bak Distribusi <span class="text-danger">*</span></label>
                        <input type="text" name="no_bak"
                            class="form-control @error('no_bak') is-invalid @enderror"
                            value="{{ old('no_bak') }}" placeholder="Contoh: 11, 12 atau 5, 6" required>
                        <small class="text-muted" style="font-size:11px;">Nomor bak distribusi</small>
                        @error('no_bak') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Rotasi</label>
                        <input type="text" name="rotasi"
                            class="form-control @error('rotasi') is-invalid @enderror"
                            value="{{ old('rotasi') }}" placeholder="Contoh: 7 Hari">
                        <small class="text-muted" style="font-size:11px;">Siklus rotasi pengaliran</small>
                        @error('rotasi') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>
            </div>

            {{-- Data Bed & Volume --}}
            <div class="form-card">
                <div class="form-section-title">
                    <i class="feather-droplet"></i>
                    Data Bed &amp; Volume Limbah
                </div>
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Vol. Dihasilkan <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="number" name="vol_limbah_dihasilkan"
                                class="form-control @error('vol_limbah_dihasilkan') is-invalid @enderror"
                                value="{{ old('vol_limbah_dihasilkan', 0) }}" min="0" step="1" required>
                            <span class="input-group-text">m³</span>
                        </div>
                        <small class="text-muted" style="font-size:11px;">Limbah dari proses PKS</small>
                        @error('vol_limbah_dihasilkan') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-3">
                        <label class="form-label" style="color:#059669;">
                            Vol. Dialirkan <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <input type="number" name="vol_limbah_dialirkan"
                                class="form-control @error('vol_limbah_dialirkan') is-invalid @enderror"
                                style="color:#059669;"
                                value="{{ old('vol_limbah_dialirkan', 0) }}" min="0" step="1" required>
                            <span class="input-group-text">m³</span>
                        </div>
                        <small class="text-muted" style="font-size:11px;">Debit limbah ke LA</small>
                        @error('vol_limbah_dialirkan') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-3">
                        <label class="form-label" style="color:#16a34a;">
                            Bed di alirkan <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <input type="number" name="flat_bed"
                                class="form-control @error('flat_bed') is-invalid @enderror"
                                style="color:#16a34a;"
                                value="{{ old('flat_bed', 0) }}" min="0" step="1" required>
                            <span class="input-group-text">Bed</span>
                        </div>
                        <small class="text-muted" style="font-size:11px;">Jumlah flat bed dialirkan</small>
                        @error('flat_bed') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Luas Area <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="number" name="luas_area"
                                class="form-control @error('luas_area') is-invalid @enderror"
                                value="{{ old('luas_area', 0) }}" min="0" step="1" required>
                            <span class="input-group-text">Ha</span>
                        </div>
                        <small class="text-muted" style="font-size:11px;">Luasan area pengaliran</small>
                        @error('luas_area') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>
            </div>
        </div>

        {{-- ============ RIGHT COLUMN ============ --}}
        <div class="col-lg-4">

            {{-- Unit PKS --}}
            <div class="form-card">
                <div class="form-section-title">
                    <i class="feather-home"></i>
                    Unit PKS
                </div>
                @if($user->isAdmin())
                    <select name="id_pks" class="form-control @error('id_pks') is-invalid @enderror"
                        data-select2-selector="status" required>
                        <option value="">— Pilih PKS —</option>
                        @foreach($pksList as $pks)
                            <option value="{{ $pks->id_pks }}" {{ old('id_pks', $user->id_pks) == $pks->id_pks ? 'selected' : '' }}>
                                {{ $pks->nama }} ({{ $pks->akro }})
                            </option>
                        @endforeach
                    </select>
                    @error('id_pks') <div class="invalid-feedback">{{ $message }}</div> @enderror
                @else
                    <input type="hidden" name="id_pks" value="{{ $user->id_pks }}">
                    <div class="pks-display">
                        <i class="feather-map-pin" style="color:#16a34a;font-size:18px;flex-shrink:0;"></i>
                        <div>
                            <div style="font-size:14px;font-weight:800;">{{ $user->pks ? $user->pks->nama : 'PKS' }}</div>
                            <div style="font-size:11px;font-weight:600;color:#6b7280;">{{ $user->pks ? $user->pks->akro : '' }}</div>
                        </div>
                    </div>
                @endif
            </div>

            {{-- Keterangan --}}
            <div class="form-card">
                <div class="form-section-title">
                    <i class="feather-message-square"></i>
                    Keterangan Status Pengaliran
                </div>

                <div style="margin-bottom:12px;">
                    <div style="font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.4px;margin-bottom:8px;">
                        Pilihan Cepat:
                    </div>
                    <div class="d-flex flex-wrap gap-2">
                        <button type="button" class="keterangan-chip"
                            onclick="setKeterangan('Pengaliran Limbah lancar')">
                            🌿 Lancar
                        </button>
                        <button type="button" class="keterangan-chip"
                            onclick="setKeterangan('Pengaliran Limbah dari kolam IPAL ke LA berjalan lancar')">
                            💧 IPAL ke LA Lancar
                        </button>
                    </div>
                </div>

                <textarea name="keterangan" id="keterangan_field" rows="4"
                    class="form-control @error('keterangan') is-invalid @enderror"
                    placeholder="Tuliskan keterangan status pengaliran...">{{ old('keterangan', 'Pengaliran Limbah lancar') }}</textarea>
                @error('keterangan') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            {{-- Foto Dokumentasi --}}
            <div class="form-card">
                <div class="form-section-title">
                    <i class="feather-camera"></i>
                    Foto Dokumentasi
                </div>
                <div class="photo-upload-wrap">
                    <label class="photo-upload-area d-block" for="foto_field">
                        <div style="font-size:32px;color:#86efac;margin-bottom:8px;">
                            <i class="feather-image"></i>
                        </div>
                        <div style="font-size:13px;font-weight:700;color:#374151;margin-bottom:4px;" id="fileLabel">
                            Klik untuk pilih foto
                        </div>
                        <div style="font-size:11px;color:#6b7280;">JPG, JPEG, PNG (maks 2MB)</div>
                        <input type="file" name="foto" id="foto_field"
                            class="@error('foto') is-invalid @enderror"
                            accept="image/*" style="opacity:0;position:absolute;inset:0;cursor:pointer;"
                            onchange="updateFileLabel(this)">
                    </label>
                </div>
                @error('foto') <div class="text-danger mt-1" style="font-size:12px;">{{ $message }}</div> @enderror
            </div>

            {{-- Submit --}}
            <div class="d-grid gap-3">
                <button type="submit" class="btn-ptpn btn-ptpn-primary" style="padding:14px;font-size:15px;font-weight:800;border-radius:14px;">
                    <i class="feather-save" style="font-size:16px;"></i>
                    Simpan Data Pengaliran
                </button>
                <a href="{{ route('pengaliran.index') }}" class="btn-ptpn btn-ptpn-outline" style="padding:12px;font-size:14px;border-radius:14px;text-align:center;justify-content:center;">
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
    function setKeterangan(text) {
        document.getElementById('keterangan_field').value = text;
    }
    function updateFileLabel(input) {
        const label = document.getElementById('fileLabel');
        if (input.files && input.files.length > 0) {
            label.textContent = '✓ ' + input.files[0].name;
            label.style.color = '#16a34a';
        } else {
            label.textContent = 'Klik untuk pilih foto';
            label.style.color = '';
        }
    }
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof $ !== 'undefined' && $.fn.select2) {
            $('[data-select2-selector]').select2({ width: '100%' });
        }
    });
</script>
@endsection