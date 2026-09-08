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

            {{-- 1. Unit PKS & Waktu Operasional --}}
            <div class="form-card">
                <div class="form-section-title">
                    <i class="feather-clock"></i>
                    Unit PKS &amp; Waktu Operasional
                </div>
                <div class="row g-3">
                    @if($user->isAdmin())
                    <div class="col-12">
                        <label class="form-label">Unit PKS <span class="text-danger">*</span></label>
                        <select name="id_pks" id="pks_selector" class="form-control @error('id_pks') is-invalid @enderror"
                            data-select2-selector="status" required>
                            <option value="">— Pilih PKS —</option>
                            @foreach($pksList as $pks)
                                <option value="{{ $pks->id_pks }}" {{ old('id_pks', $user->id_pks) == $pks->id_pks ? 'selected' : '' }}>
                                    {{ $pks->nama }} ({{ $pks->akro }})
                                </option>
                            @endforeach
                        </select>
                        @error('id_pks') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    @else
                    <input type="hidden" name="id_pks" id="pks_selector" value="{{ $user->id_pks }}">
                    <div class="col-12">
                        <div class="pks-display">
                            <i class="feather-map-pin" style="color:#16a34a;font-size:18px;flex-shrink:0;"></i>
                            <div>
                                <div style="font-size:14px;font-weight:800;">{{ $user->pks ? $user->pks->nama : 'PKS' }}</div>
                                <div style="font-size:11px;font-weight:600;color:#6b7280;">Unit Pabrik Kelapa Sawit ({{ $user->pks ? $user->pks->akro : '' }})</div>
                            </div>
                        </div>
                    </div>
                    @endif

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

            {{-- 2. Lokasi Pengaliran --}}
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

            {{-- 3. Data Bed & Volume Limbah --}}
            <div class="form-card">
                <div class="form-section-title">
                    <i class="feather-droplet"></i>
                    Data Bed &amp; Volume Limbah
                </div>
                <div class="row g-3">
                    {{-- 1. Bed di alirkan --}}
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

                    {{-- 2. Vol. Dialirkan --}}
                    <div class="col-md-3">
                        <label class="form-label" style="color:#059669;">
                            Vol. Dialirkan <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <input type="number" name="vol_limbah_dialirkan" id="vol_dialirkan_input"
                                class="form-control @error('vol_limbah_dialirkan') is-invalid @enderror"
                                style="color:#059669;"
                                value="{{ old('vol_limbah_dialirkan', 0) }}" min="0" step="1" required>
                            <span class="input-group-text">m³</span>
                        </div>
                        <small id="vol_dialirkan_hint" class="text-muted" style="font-size:11px;">Debit limbah ke LA</small>
                        @error('vol_limbah_dialirkan') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    {{-- 3. Vol. Dihasilkan --}}
                    <div class="col-md-3">
                        <label class="form-label">Vol. Dihasilkan <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="number" name="vol_limbah_dihasilkan" id="vol_dihasilkan_input"
                                class="form-control @error('vol_limbah_dihasilkan') is-invalid @enderror"
                                value="{{ old('vol_limbah_dihasilkan', 0) }}" min="0" step="1" required>
                            <span class="input-group-text">m³</span>
                        </div>
                        <small class="text-muted" style="font-size:11px;">Limbah dari proses PKS</small>
                        @error('vol_limbah_dihasilkan') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    {{-- 4. Luas Area --}}
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

                {{-- INFO KUOTA MAKSIMAL SK IZIN LA --}}
                <div id="sk_quota_info_banner" class="mt-3 p-2.5 px-3 rounded-3 d-flex align-items-center justify-content-between flex-wrap gap-2" style="background: #f0fdf4; border: 1.5px solid #bbf7d0; display: none; transition: all .3s ease;">
                    <div class="d-flex align-items-center gap-2.5">
                        <div id="sk_quota_icon_wrap" style="width: 32px; height: 32px; border-radius: 9px; background: #dcfce7; color: #16a34a; display: flex; align-items: center; justify-content: center; font-size: 16px; flex-shrink: 0;">
                            <i id="sk_quota_icon" class="feather-shield"></i>
                        </div>
                        <div>
                            <div class="d-flex align-items-center gap-1.5 flex-wrap">
                                <span style="font-size: 12px; font-weight: 800; color: #14532d;">Kuota Maksimal Harian Berdasarkan SK Izin LA:</span>
                                <span id="sk_quota_val_text" style="font-size: 13.5px; font-weight: 900; color: #15803d;">-</span>
                            </div>
                            <small id="sk_quota_subtext" class="text-muted" style="font-size: 11px;">Debit pengaliran per hari tidak boleh melampaui batas yang tertera pada arsip izin LA.</small>
                        </div>
                    </div>
                    <div>
                        <span id="sk_quota_status_pill" class="badge bg-success text-white px-3 py-1.5 rounded-pill" style="font-size: 11px; font-weight: 700;">
                            Batas Aman
                        </span>
                    </div>
                </div>

                {{-- REAL-TIME VOLUME DETECTION BANNER & COMPLIANCE WIDGET --}}
                <div id="volume_detection_card" style="margin-top:16px;display:none;border-radius:14px;padding:16px;transition:all .3s ease;">
                    <div class="d-flex align-items-start gap-3">
                        <div id="detection_icon_wrap" style="width:42px;height:42px;border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:20px;flex-shrink:0;">
                            <i id="detection_icon" class="feather-info"></i>
                        </div>
                        <div class="flex-grow-1">
                            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-1">
                                <h6 id="detection_title" style="margin:0;font-size:13.5px;font-weight:800;">Analisis Pengaliran</h6>
                                <span id="detection_ratio_badge" style="font-size:11px;font-weight:800;padding:3px 10px;border-radius:50px;">Rasio: 0%</span>
                            </div>
                            <p id="detection_desc" style="margin:0 0 10px 0;font-size:12.5px;line-height:1.4;"></p>
                            
                            {{-- Chips Rekomendasi Alasan/Justifikasi --}}
                            <div id="detection_suggestions" style="display:none;">
                                <div style="font-size:11px;font-weight:800;margin-bottom:6px;text-transform:uppercase;letter-spacing:.3px;">
                                    ⚡ Rekomendasi Keterangan/Alasan (Klik untuk mengisi):
                                </div>
                                <div id="suggestion_chips_container" class="d-flex flex-wrap gap-2"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ============ RIGHT COLUMN ============ --}}
        <div class="col-lg-4">

            {{-- Keterangan / Alasan Deviasi --}}
            <div class="form-card" id="keterangan_card">
                <div class="form-section-title d-flex justify-content-between align-items-center">
                    <div>
                        <i class="feather-message-square"></i>
                        <span id="keterangan_card_title">Keterangan &amp; Alasan</span>
                    </div>
                    <span id="keterangan_required_badge" class="badge bg-danger" style="display:none;font-size:10px;">Wajib Diisi</span>
                </div>

                <div style="margin-bottom:12px;">
                    <div style="font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.4px;margin-bottom:8px;">
                        Pilihan Cepat Standar:
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
    const perizinanMap = @json($perizinanMap ?? []);

    function setKeterangan(text) {
        const field = document.getElementById('keterangan_field');
        field.value = text;
        field.focus();
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

    // REAL-TIME VOLUME DETECTION LOGIC
    function checkVolumeDetection() {
        const volDialirkanInput = document.getElementById('vol_dialirkan_input');
        const volDihasilkanInput = document.getElementById('vol_dihasilkan_input');
        const pksSelector = document.getElementById('pks_selector');
        const skLimitBadge = document.getElementById('sk_limit_badge');
        
        if (!volDialirkanInput || !volDihasilkanInput) return;

        const volDialirkan = parseFloat(volDialirkanInput.value) || 0;
        const volDihasilkan = parseFloat(volDihasilkanInput.value) || 0;
        const idPks = pksSelector ? pksSelector.value : null;
        
        const card = document.getElementById('volume_detection_card');
        const iconWrap = document.getElementById('detection_icon_wrap');
        const icon = document.getElementById('detection_icon');
        const title = document.getElementById('detection_title');
        const badge = document.getElementById('detection_ratio_badge');
        const desc = document.getElementById('detection_desc');
        const suggestionsBox = document.getElementById('detection_suggestions');
        const chipsContainer = document.getElementById('suggestion_chips_container');
        const ketField = document.getElementById('keterangan_field');
        const ketReqBadge = document.getElementById('keterangan_required_badge');
        const ketCard = document.getElementById('keterangan_card');

        // Update SK limit banner display (di luar form input)
        const skQuotaBanner = document.getElementById('sk_quota_info_banner');
        const skQuotaValText = document.getElementById('sk_quota_val_text');
        const skQuotaStatusPill = document.getElementById('sk_quota_status_pill');
        const skQuotaIconWrap = document.getElementById('sk_quota_icon_wrap');
        const skQuotaSubtext = document.getElementById('sk_quota_subtext');

        let debitIzin = null;
        if (idPks && perizinanMap[idPks] && perizinanMap[idPks].debit_maksimal_harian) {
            debitIzin = parseFloat(perizinanMap[idPks].debit_maksimal_harian);
        }

        if (skQuotaBanner) {
            if (debitIzin && debitIzin > 0) {
                skQuotaBanner.style.display = 'flex';
                skQuotaValText.textContent = `${debitIzin.toLocaleString()} m³/hari`;
                
                if (volDialirkan > debitIzin) {
                    const overVal = volDialirkan - debitIzin;
                    skQuotaBanner.style.background = '#fef2f2';
                    skQuotaBanner.style.borderColor = '#fca5a5';
                    if (skQuotaIconWrap) {
                        skQuotaIconWrap.style.background = '#fee2e2';
                        skQuotaIconWrap.style.color = '#dc2626';
                    }
                    if (skQuotaStatusPill) {
                        skQuotaStatusPill.className = 'badge bg-danger text-white px-3 py-1.5 rounded-pill';
                        skQuotaStatusPill.textContent = `⛔ Melebihi Kuota (+${overVal.toLocaleString()} m³)`;
                    }
                    if (skQuotaSubtext) {
                        skQuotaSubtext.textContent = 'Peringatan: Debit yang dialirkan hari ini melebihi ambang batas izin SK!';
                        skQuotaSubtext.className = 'text-danger fw-bold';
                    }
                } else {
                    skQuotaBanner.style.background = '#f0fdf4';
                    skQuotaBanner.style.borderColor = '#bbf7d0';
                    if (skQuotaIconWrap) {
                        skQuotaIconWrap.style.background = '#dcfce7';
                        skQuotaIconWrap.style.color = '#16a34a';
                    }
                    if (skQuotaStatusPill) {
                        skQuotaStatusPill.className = 'badge bg-success text-white px-3 py-1.5 rounded-pill';
                        skQuotaStatusPill.textContent = `✅ Batas Aman (${volDialirkan.toLocaleString()} / ${debitIzin.toLocaleString()} m³)`;
                    }
                    if (skQuotaSubtext) {
                        skQuotaSubtext.textContent = 'Debit pengaliran per hari berada dalam kuota aman yang tertera pada arsip izin LA.';
                        skQuotaSubtext.className = 'text-muted';
                    }
                }
            } else {
                skQuotaBanner.style.display = 'none';
            }
        }

        // Jika input masih 0 atau kosong, sembunyikan card deteksi
        if (volDialirkan === 0) {
            card.style.display = 'none';
            ketReqBadge.style.display = 'none';
            ketField.required = false;
            volDialirkanInput.style.borderColor = '';
            return;
        }

        card.style.display = 'block';

        const ratioSk = debitIzin > 0 ? ((volDialirkan / debitIzin) * 100).toFixed(1) : 0;
        const selisihSk = debitIzin > 0 ? (volDialirkan - debitIzin) : 0;

        // KASUS 1: OVERFLOW (Vol Dialirkan > Debit Kuota SK)
        if (debitIzin && volDialirkan > debitIzin) {
            card.style.background = '#fef2f2';
            card.style.border = '2px solid #ef4444';
            iconWrap.style.background = '#fee2e2';
            iconWrap.style.color = '#dc2626';
            icon.className = 'feather-alert-triangle';
            title.textContent = '⚠️ PERINGATAN: Volume Dialirkan Melebihi Kuota SK Izin LA (Overflow)!';
            title.style.color = '#991b1b';

            badge.style.background = '#dc2626';
            badge.style.color = '#ffffff';
            badge.textContent = `⚠️ Overflow: ${ratioSk}% (+${selisihSk.toLocaleString()} m³ | Maks SK: ${debitIzin.toLocaleString()} m³/hari)`;

            desc.style.color = '#7f1d1d';
            desc.innerHTML = `Volume yang dialirkan (<strong>${volDialirkan.toLocaleString()} m³</strong>) melampaui batas kuota debit maksimal harian Surat Izin Land Application (<strong>${debitIzin.toLocaleString()} m³/hari</strong>). <strong>Wajib memberikan justifikasi teknis/alasan kondisi darurat pengaliran.</strong>`;

            // Rekomendasi alasan over-quota
            suggestionsBox.style.display = 'block';
            chipsContainer.innerHTML = `
                <button type="button" class="keterangan-chip" style="background:#fee2e2;color:#991b1b;border-color:#fca5a5;" onclick="setKeterangan('Pengaliran darurat akibat normalisasi tanggul kolam IPAL pasca curah hujan ekstrem')">
                    🚨 Operasional Darurat IPAL
                </button>
                <button type="button" class="keterangan-chip" style="background:#fee2e2;color:#991b1b;border-color:#fca5a5;" onclick="setKeterangan('Pengaliran sisa cadangan limbah kolam anaerobik hari sebelumnya yang tertunda')">
                    💧 Sisa Cadangan Tertunda
                </button>
                <button type="button" class="keterangan-chip" style="background:#fee2e2;color:#991b1b;border-color:#fca5a5;" onclick="setKeterangan('Akumulasi debit limbah olah puncak pasca libur operasional')">
                    ⏱️ Akumulasi Debit Puncak
                </button>
            `;

            ketReqBadge.style.display = 'inline-block';
            ketReqBadge.textContent = 'Wajib Diisi (Justifikasi Kuota SK)';
            ketField.required = true;
            ketCard.style.borderColor = '#fca5a5';
            volDialirkanInput.style.borderColor = '#ef4444';
        }
        // KASUS 2: UNDERFLOW (Vol Dialirkan < 40% dari Debit Kuota SK)
        else if (debitIzin && volDialirkan < (0.4 * debitIzin)) {
            card.style.background = '#fffbeb';
            card.style.border = '1.5px solid #fde68a';
            iconWrap.style.background = '#fef3c7';
            iconWrap.style.color = '#d97706';
            icon.className = 'feather-alert-circle';
            title.textContent = '⚠️ PERHATIAN: Volume Dialirkan Terlalu Sedikit (Underflow / Kendala)';
            title.style.color = '#92400e';

            badge.style.background = '#d97706';
            badge.style.color = '#ffffff';
            badge.textContent = `⚠️ Underflow: ${ratioSk}% (Hanya ${volDialirkan.toLocaleString()} dari Kuota SK ${debitIzin.toLocaleString()} m³) `;

            desc.style.color = '#78350f';
            desc.innerHTML = `Volume yang dialirkan (<strong>${volDialirkan.toLocaleString()} m³</strong>) berada jauh di bawah batas normal kuota SK Izin LA (<strong>${debitIzin.toLocaleString()} m³/hari</strong>). <strong>Wajib memberikan keterangan kendala operasional pengaliran.</strong>`;

            // Rekomendasi kendala
            suggestionsBox.style.display = 'block';
            chipsContainer.innerHTML = `
                <button type="button" class="keterangan-chip" style="background:#fef3c7;color:#92400e;border-color:#fde68a;" onclick="setKeterangan('Pompa distribusi / pipa saluran Land Application sedang maintenance/perbaikan')">
                    🔧 Maintenance Pompa/Pipa
                </button>
                <button type="button" class="keterangan-chip" style="background:#fef3c7;color:#92400e;border-color:#fde68a;" onclick="setKeterangan('Limbah ditampung di kolam retensi IPAL untuk stabilisasi waktu tinggal')">
                    ⏳ Penampungan/Retensi IPAL
                </button>
                <button type="button" class="keterangan-chip" style="background:#fef3c7;color:#92400e;border-color:#fde68a;" onclick="setKeterangan('Lahan LA jenuh air akibat curah hujan tinggi (menghindari run-off)')">
                    🌧️ Lahan LA Jenuh Air (Hujan)
                </button>
                <button type="button" class="keterangan-chip" style="background:#fef3c7;color:#92400e;border-color:#fde68a;" onclick="setKeterangan('Pabrik stop olah / kendala pasokan TBS')">
                    🛑 Stop Olah Pabrik
                </button>
            `;

            ketReqBadge.style.display = 'inline-block';
            ketReqBadge.textContent = 'Wajib Diisi (Keterangan Kendala)';
            ketField.required = true;
            ketCard.style.borderColor = '#fde68a';
            volDialirkanInput.style.borderColor = '#d97706';
        }
        // KASUS 3: NORMAL (40% s/d 100% Kuota SK)
        else {
            card.style.background = '#f0fdf4';
            card.style.border = '1.5px solid #bbf7d0';
            iconWrap.style.background = '#dcfce7';
            iconWrap.style.color = '#16a34a';
            icon.className = 'feather-check-circle';
            title.textContent = '✅ Status Pengaliran Normal & Sesuai Kuota SK Izin LA';
            title.style.color = '#14532d';

            badge.style.background = '#16a34a';
            badge.style.color = '#ffffff';
            badge.textContent = debitIzin ? `✅ Normal: ${ratioSk}% (${volDialirkan.toLocaleString()} / ${debitIzin.toLocaleString()} m³)` : `✅ Normal: ${volDialirkan.toLocaleString()} m³`;

            desc.style.color = '#166534';
            desc.innerHTML = `Volume dialirkan (<strong>${volDialirkan.toLocaleString()} m³</strong>) berada dalam batas kuota aman sesuai Surat Izin Land Application${debitIzin ? ' (' + debitIzin.toLocaleString() + ' m³/hari)' : ''}.`;

            suggestionsBox.style.display = 'none';
            ketReqBadge.style.display = 'none';
            ketField.required = false;
            ketCard.style.borderColor = 'rgba(22,163,74,.1)';
            volDialirkanInput.style.borderColor = '';
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        if (typeof $ !== 'undefined' && $.fn.select2) {
            $('[data-select2-selector]').select2({ width: '100%' });
            // Re-check when Select2 changes
            $('[data-select2-selector]').on('select2:select', function (e) {
                checkVolumeDetection();
            });
        }

        const volDialirkanInput = document.getElementById('vol_dialirkan_input');
        const volDihasilkanInput = document.getElementById('vol_dihasilkan_input');
        const pksSelector = document.getElementById('pks_selector');

        if (volDialirkanInput && volDihasilkanInput) {
            volDialirkanInput.addEventListener('input', checkVolumeDetection);
            volDihasilkanInput.addEventListener('input', checkVolumeDetection);
            if (pksSelector) {
                pksSelector.addEventListener('change', checkVolumeDetection);
            }
            // Trigger check on initial load
            checkVolumeDetection();
        }
    });
</script>
@endsection