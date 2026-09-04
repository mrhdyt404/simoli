@extends('layouts.simoli')

@section('title', 'Edit Data Pengaliran')
@section('page-title', 'Edit Data Pengaliran Land Aplikasi')
@section('page-description', 'Perbarui data harian pengaliran limbah ke Land Aplikasi')

@section('breadcrumb')
    <li><a href="{{ route('pengaliran.index') }}" style="color:inherit;text-decoration:none;">Pengaliran</a></li>
    <li class="separator">/</li>
    <li>Edit Data</li>
@endsection

@section('styles')
<link rel="stylesheet" type="text/css" href="{{ asset('duraluxadmin/assets/vendors/css/select2.min.css') }}" />
<link rel="stylesheet" type="text/css" href="{{ asset('duraluxadmin/assets/vendors/css/select2-theme.min.css') }}" />
<style>
    @keyframes fadeUpCard {
        from { opacity:0; transform:translateY(12px); }
        to   { opacity:1; transform:translateY(0); }
    }

    .progress-preview { border-radius:18px;overflow:hidden;border:1px solid rgba(22,163,74,.2);box-shadow:0 4px 20px rgba(22,163,74,.08);margin-bottom:24px;animation:fadeUpCard .4s ease-out; }
    .progress-preview-header { padding:14px 20px;background:linear-gradient(90deg,#052e16,#14532d,#166534);display:flex;align-items:center;justify-content:space-between; }
    .progress-preview-title { font-family:'Outfit',sans-serif;font-size:13.5px;font-weight:800;color:#86efac;display:flex;align-items:center;gap:8px; }
    .progress-preview-badge { display:inline-flex;align-items:center;gap:6px;padding:4px 12px;border-radius:50px;background:rgba(255,255,255,.15);color:#fff;font-size:12px;font-weight:800;border:1px solid rgba(255,255,255,.2); }
    .progress-preview-body { background:#ffffff;padding:16px; }
    .progress-tile { border-radius:14px;padding:14px 16px;height:100%; }
    .progress-tile-week  { background:#f0fdf4;border:1px solid #bbf7d0; }
    .progress-tile-month { background:#fffbeb;border:1px solid #fde68a; }
    .progress-tile-header { display:flex;align-items:center;justify-content:space-between;margin-bottom:10px; }
    .progress-tile-label { font-size:10.5px;font-weight:800;text-transform:uppercase;letter-spacing:.5px; }
    .progress-tile-label-week  { color:#15803d; }
    .progress-tile-label-month { color:#b45309; }
    .progress-tile-val { display:inline-flex;align-items:center;gap:5px;padding:3px 10px;border-radius:50px;font-size:11.5px;font-weight:800;color:#fff; }
    .val-week  { background:#16a34a; }
    .val-month { background:#d97706; }
    .progress-tile-row { display:flex;justify-content:space-between;gap:8px;font-size:12px; }
    .progress-tile-key  { font-weight:700;color:#6b7280; }
    .progress-tile-data { font-weight:700;color:#1f2937;text-align:right; }

    .form-card { background:#ffffff;border-radius:18px;border:1px solid rgba(22,163,74,.1);box-shadow:0 2px 12px rgba(22,163,74,.06);padding:24px;margin-bottom:20px;animation:fadeUpCard .4s ease-out; }
    .form-section-title { display:flex;align-items:center;gap:8px;font-size:11.5px;font-weight:800;color:#16a34a;text-transform:uppercase;letter-spacing:.6px;margin-bottom:18px;padding-bottom:10px;border-bottom:2px solid rgba(22,163,74,.1); }
    .form-card .form-control, .form-card .form-select { border-radius:12px;border:1.5px solid #e5e7eb;font-size:13.5px;padding:10px 14px;transition:all .2s ease;background:#f9fafb; }
    .form-card .form-control:focus, .form-card .form-select:focus { border-color:rgba(22,163,74,.5);background:#fff;box-shadow:0 0 0 3px rgba(34,197,94,.08); }
    .form-card label.form-label { font-size:12px;font-weight:700;color:#374151;margin-bottom:6px; }
    .form-card .input-group .form-control { border-radius:12px 0 0 12px; }
    .form-card .input-group-text { border-radius:0 12px 12px 0;background:#f0fdf4;border:1.5px solid rgba(22,163,74,.2);border-left:none;color:#16a34a;font-weight:700;font-size:12.5px; }
    .pks-display { padding:12px 16px;border-radius:12px;background:#f0fdf4;border:1.5px solid rgba(22,163,74,.2);display:flex;align-items:center;gap:10px;font-weight:700;font-size:13.5px;color:#14532d; }
    .keterangan-chip { display:inline-flex;align-items:center;gap:5px;padding:5px 12px;border-radius:50px;font-size:11.5px;font-weight:700;cursor:pointer;transition:all .2s ease;border:1.5px solid rgba(22,163,74,.2);background:#f0fdf4;color:#166534; }
    .keterangan-chip:hover { background:#dcfce7;border-color:rgba(22,163,74,.4);transform:translateY(-1px); }

    /* Edit-warning banner */
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

    /* === PERMIT COMPLIANCE STYLES === */
    .permit-status-card {
        border-radius: 14px;
        padding: 14px 18px;
        margin-top: 14px;
        transition: all .3s cubic-bezier(0.4, 0, 0.2, 1);
        display: none;
    }
    .permit-status-card.active { display: block; animation: fadeUpCard .3s ease-out; }
    .permit-card-sesuai {
        background: #f0fdf4;
        border: 1.5px solid #86efac;
        color: #14532d;
    }
    .permit-card-luar {
        background: #fffbeb;
        border: 1.5px solid #f59e0b;
        color: #78350f;
    }
    .permit-card-title {
        font-size: 13px;
        font-weight: 800;
        display: flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 4px;
    }
    .permit-card-desc {
        font-size: 11.5px;
        line-height: 1.45;
    }

    /* Blok Quick Chips */
    .blok-chips-container {
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
        margin-top: 8px;
        margin-bottom: 6px;
        max-height: 120px;
        overflow-y: auto;
        padding: 2px;
    }
    .blok-chip {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 4px 10px;
        border-radius: 8px;
        font-size: 11px;
        font-weight: 700;
        cursor: pointer;
        border: 1px solid #bbf7d0;
        background: #ffffff;
        color: #15803d;
        transition: all .15s ease;
    }
    .blok-chip:hover {
        background: #16a34a;
        color: #ffffff;
        border-color: #16a34a;
        transform: translateY(-1px);
    }
    .blok-chip-bak {
        font-size: 9.5px;
        opacity: .85;
        font-weight: 600;
    }

    /* Alasan Luar Izin Animated Box */
    .alasan-luar-izin-box {
        background: #fff7ed;
        border: 1.5px dashed #f97316;
        border-radius: 14px;
        padding: 16px 18px;
        margin-top: 14px;
        animation: fadeUpCard .3s ease-out;
        display: none;
    }
    .alasan-luar-izin-box.show { display: block; }
    .alasan-luar-izin-box label {
        color: #9a3412 !important;
        font-weight: 800 !important;
    }

    html.app-skin-dark .permit-card-sesuai { background: #062b16; border-color: #16a34a; color: #86efac; }
    html.app-skin-dark .permit-card-luar   { background: #261704; border-color: #d97706; color: #fde68a; }
    html.app-skin-dark .blok-chip { background: #0e3b26; border-color: rgba(34,197,94,.3); color: #86efac; }
    html.app-skin-dark .blok-chip:hover { background: #16a34a; color: #fff; }
    html.app-skin-dark .alasan-luar-izin-box { background: #261405; border-color: #ea580c; }
    html.app-skin-dark .alasan-luar-izin-box label { color: #fdba74 !important; }

    html.app-skin-dark .form-card { background:#0a2317 !important;border-color:rgba(34,197,94,.15) !important; }
    html.app-skin-dark .form-label { color:#d1fae5 !important; }
    html.app-skin-dark .form-card .form-control, html.app-skin-dark .form-card .form-select { background:#0e3b26;border-color:rgba(34,197,94,.2);color:#d1fae5; }
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

{{-- Edit Warning --}}
<div class="edit-warning">
    <i class="feather-edit-2" style="font-size:18px;flex-shrink:0;color:#d97706;"></i>
    <span>Anda sedang mengedit data pengaliran. Perubahan akan langsung tersimpan saat menekan <strong>"Perbarui"</strong>.</span>
</div>

{{-- Progress Widget --}}
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
                            {{ number_format($pksProgress->minggu_ini->bed_dialirkan, 0, ',', '.') }} Bed
                        </span>
                    </div>
                    <div class="progress-tile-row">
                        <div><div class="progress-tile-key">Block Pengaliran</div><div class="progress-tile-data">{{ $pksProgress->minggu_ini->blok }}</div></div>
                        <div style="text-align:right;"><div class="progress-tile-key">Bak Distribusi</div><div class="progress-tile-data">{{ $pksProgress->minggu_ini->bak }}</div></div>
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
                            {{ number_format($pksProgress->sd_bulan_ini->bed_dialirkan, 0, ',', '.') }} Bed
                        </span>
                    </div>
                    <div class="progress-tile-row">
                        <div><div class="progress-tile-key">Block Pengaliran</div><div class="progress-tile-data">{{ $pksProgress->sd_bulan_ini->blok }}</div></div>
                        <div style="text-align:right;"><div class="progress-tile-key">Bak Distribusi</div><div class="progress-tile-data">{{ $pksProgress->sd_bulan_ini->bak }}</div></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endif

<form action="{{ route('pengaliran.update', $pengaliran->id_pengaliran) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')
    <div class="row g-4">

        {{-- LEFT COLUMN --}}
        <div class="col-lg-8">

            <div class="form-card">
                <div class="form-section-title"><i class="feather-clock"></i> Informasi Waktu Operasional</div>
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Tanggal <span class="text-danger">*</span></label>
                        <input type="date" name="tanggal" class="form-control @error('tanggal') is-invalid @enderror"
                            value="{{ old('tanggal', $pengaliran->tanggal?->format('Y-m-d')) }}" required>
                        @error('tanggal') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Jam Mulai <span class="text-danger">*</span></label>
                        <input type="time" name="jam_mulai" class="form-control @error('jam_mulai') is-invalid @enderror"
                            value="{{ old('jam_mulai', $pengaliran->jam_mulai) }}" required>
                        @error('jam_mulai') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Jam Selesai <span class="text-danger">*</span></label>
                        <input type="time" name="jam_selesai" class="form-control @error('jam_selesai') is-invalid @enderror"
                            value="{{ old('jam_selesai', $pengaliran->jam_selesai) }}" required>
                        @error('jam_selesai') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>
            </div>

            <div class="form-card">
                <div class="form-section-title"><i class="feather-map-pin"></i> Detail Lokasi Pengaliran Land Aplikasi</div>
                <div class="row g-3">
                    <div class="col-md-5">
                        <label class="form-label">Block Pengaliran <span class="text-danger">*</span></label>
                        <input type="text" name="blok" id="blok_input" class="form-control @error('blok') is-invalid @enderror"
                            value="{{ old('blok', $pengaliran->blok) }}" placeholder="Contoh: E25, L25, F26" required
                            autocomplete="off">
                        <small class="text-muted" style="font-size:11px;">Blok area pengaliran lahan</small>
                        @error('blok') <div class="invalid-feedback">{{ $message }}</div> @enderror

                        {{-- Quick Chips Blok Berizin --}}
                        <div class="mt-2">
                            <div class="d-flex align-items-center justify-content-between">
                                <span style="font-size:10.5px;font-weight:700;color:#6b7280;text-transform:uppercase;">Blok Berizin PKS Ini:</span>
                                <span id="sk_info_badge" class="badge bg-light text-muted" style="font-size:10px;"></span>
                            </div>
                            <div class="blok-chips-container" id="blok_chips_container">
                                <span class="text-muted" style="font-size:11px;font-style:italic;">Memuat data blok berizin...</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Bak Distribusi <span class="text-danger">*</span></label>
                        <input type="text" name="no_bak" id="no_bak_input" class="form-control @error('no_bak') is-invalid @enderror"
                            value="{{ old('no_bak', $pengaliran->no_bak) }}" placeholder="Contoh: 1, 2 atau 5-6" required
                            autocomplete="off">
                        <small class="text-muted" style="font-size:11px;">Nomor bak distribusi limbah</small>
                        @error('no_bak') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Rotasi</label>
                        <input type="text" name="rotasi" class="form-control @error('rotasi') is-invalid @enderror"
                            value="{{ old('rotasi', $pengaliran->rotasi) }}" placeholder="Contoh: 7 Hari">
                        <small class="text-muted" style="font-size:11px;">Siklus rotasi pengaliran</small>
                        @error('rotasi') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>

                {{-- Live Permit Status Card --}}
                <div id="permit_status_card" class="permit-status-card">
                    <div class="permit-card-title">
                        <span id="permit_status_icon"></span>
                        <span id="permit_status_title"></span>
                    </div>
                    <div id="permit_status_desc" class="permit-card-desc"></div>
                </div>

                {{-- Alasan Pengaliran di Luar Izin --}}
                <div id="alasan_luar_izin_box" class="alasan-luar-izin-box @if(old('alasan_tidak_sesuai_izin', $pengaliran->alasan_tidak_sesuai_izin) || $errors->has('alasan_tidak_sesuai_izin') || $pengaliran->isDiLuarIzin()) show @endif">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <i class="feather-alert-triangle" style="font-size:16px;color:#ea580c;"></i>
                        <label class="form-label mb-0" for="alasan_field">
                            Alasan Pengaliran di Luar Izin <span class="text-danger">*</span>
                        </label>
                    </div>
                    <p style="font-size:11.5px;color:#c2410c;margin-bottom:8px;">
                        Karena pengaliran dilakukan di luar surat izin Land Application resmi PKS ini, Anda <strong>wajib</strong> menyertakan alasan operasional/teknis:
                    </p>
                    <textarea name="alasan_tidak_sesuai_izin" id="alasan_field" rows="3"
                        class="form-control @error('alasan_tidak_sesuai_izin') is-invalid @enderror"
                        placeholder="Tuliskan alasan pengaliran dilakukan di luar izin...">{{ old('alasan_tidak_sesuai_izin', $pengaliran->alasan_tidak_sesuai_izin) }}</textarea>
                    @error('alasan_tidak_sesuai_izin')
                        <div class="invalid-feedback d-block mt-1 fw-bold">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="form-card">
                <div class="form-section-title"><i class="feather-droplet"></i> Data Bed &amp; Volume Limbah</div>
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label" style="color:#16a34a;">Bed di alirkan <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="number" name="flat_bed" style="color:#16a34a;"
                                class="form-control @error('flat_bed') is-invalid @enderror"
                                value="{{ old('flat_bed', $pengaliran->flat_bed) }}" min="0" step="1" required>
                            <span class="input-group-text">Bed</span>
                        </div>
                        @error('flat_bed') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-3">
                        <label class="form-label" style="color:#059669;">Vol. Dialirkan <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="number" name="vol_limbah_dialirkan" style="color:#059669;"
                                class="form-control @error('vol_limbah_dialirkan') is-invalid @enderror"
                                value="{{ old('vol_limbah_dialirkan', $pengaliran->vol_limbah_dialirkan) }}" min="0" step="1" required>
                            <span class="input-group-text">m³</span>
                        </div>
                        @error('vol_limbah_dialirkan') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Vol. Dihasilkan <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="number" name="vol_limbah_dihasilkan"
                                class="form-control @error('vol_limbah_dihasilkan') is-invalid @enderror"
                                value="{{ old('vol_limbah_dihasilkan', $pengaliran->vol_limbah_dihasilkan) }}" min="0" step="1" required>
                            <span class="input-group-text">m³</span>
                        </div>
                        @error('vol_limbah_dihasilkan') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Luas Area <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="number" name="luas_area"
                                class="form-control @error('luas_area') is-invalid @enderror"
                                value="{{ old('luas_area', $pengaliran->luas_area) }}" min="0" step="1" required>
                            <span class="input-group-text">Ha</span>
                        </div>
                        @error('luas_area') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>
            </div>
        </div>

        {{-- RIGHT COLUMN --}}
        <div class="col-lg-4">

            <div class="form-card">
                <div class="form-section-title"><i class="feather-home"></i> Unit PKS</div>
                @if($user->isAdmin())
                    <select name="id_pks" id="pks_select" class="form-control @error('id_pks') is-invalid @enderror" data-select2-selector="status" required>
                        <option value="">— Pilih PKS —</option>
                        @foreach($pksList as $pks)
                        <option value="{{ $pks->id_pks }}" {{ old('id_pks', $pengaliran->id_pks) == $pks->id_pks ? 'selected' : '' }}>
                            {{ $pks->nama }} ({{ $pks->akro }})
                        </option>
                        @endforeach
                    </select>
                    @error('id_pks') <div class="invalid-feedback">{{ $message }}</div> @enderror
                @else
                    <input type="hidden" name="id_pks" id="pks_select" value="{{ $user->id_pks }}">
                    <div class="pks-display">
                        <i class="feather-map-pin" style="color:#16a34a;font-size:18px;flex-shrink:0;"></i>
                        <div>
                            <div style="font-size:14px;font-weight:800;">{{ $user->pks ? $user->pks->nama : 'PKS' }}</div>
                            <div style="font-size:11px;font-weight:600;color:#6b7280;">{{ $user->pks ? $user->pks->akro : '' }}</div>
                        </div>
                    </div>
                @endif
            </div>

            <div class="form-card">
                <div class="form-section-title"><i class="feather-message-square"></i> Keterangan Status Pengaliran</div>
                <div style="margin-bottom:12px;">
                    <div style="font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.4px;margin-bottom:8px;">Pilihan Cepat:</div>
                    <div class="d-flex flex-wrap gap-2">
                        <button type="button" class="keterangan-chip" onclick="setKeterangan('Pengaliran Limbah lancar')">🌿 Lancar</button>
                        <button type="button" class="keterangan-chip" onclick="setKeterangan('Pengaliran Limbah dari kolam IPAL ke LA berjalan lancar')">💧 IPAL ke LA Lancar</button>
                    </div>
                </div>
                <textarea name="keterangan" id="keterangan_field" rows="4"
                    class="form-control @error('keterangan') is-invalid @enderror"
                    placeholder="Tuliskan keterangan status pengaliran...">{{ old('keterangan', $pengaliran->keterangan ?? 'Pengaliran Limbah lancar') }}</textarea>
                @error('keterangan') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="form-card">
                <div class="form-section-title"><i class="feather-camera"></i> Foto Dokumentasi</div>
                @if($pengaliran->foto)
                <div style="margin-bottom:12px;">
                    <img src="{{ asset('gallery/' . $pengaliran->foto) }}" alt="Foto Saat Ini" class="current-photo">
                    <small style="display:block;color:#6b7280;font-size:11px;">Foto saat ini</small>
                </div>
                @endif
                <input type="file" name="foto" class="form-control @error('foto') is-invalid @enderror" accept="image/*">
                <small style="display:block;margin-top:6px;color:#6b7280;font-size:11px;">Kosongkan jika tidak ingin mengubah foto</small>
                @error('foto') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="d-grid gap-3">
                <button type="submit" class="btn-ptpn btn-ptpn-primary" style="padding:14px;font-size:15px;font-weight:800;border-radius:14px;">
                    <i class="feather-save" style="font-size:16px;"></i>
                    Perbarui Data Pengaliran
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
    const PKS_BLOK_MAP = @json($pksBlokMap ?? []);

    function setKeterangan(text) {
        document.getElementById('keterangan_field').value = text;
    }

    function getSelectedPksId() {
        const pksEl = document.getElementById('pks_select');
        return pksEl ? pksEl.value : null;
    }

    function renderBlokChips(idPks) {
        const container = document.getElementById('blok_chips_container');
        const skBadge = document.getElementById('sk_info_badge');
        if (!container) return;

        const pksData = PKS_BLOK_MAP[idPks];
        if (!pksData || !pksData.bloks || pksData.bloks.length === 0) {
            container.innerHTML = '<span class="text-muted" style="font-size:11px;font-style:italic;">Belum ada master peta blok LA terdaftar untuk PKS ini.</span>';
            if (skBadge) skBadge.textContent = 'Belum Ada Master Blok';
            return;
        }

        if (skBadge) {
            skBadge.textContent = pksData.nomor_sk ? ('SK: ' + pksData.nomor_sk) : 'Master Blok Terdaftar';
        }

        let html = '';
        pksData.bloks.forEach(b => {
            const bakText = (b.no_bak_awal && b.no_bak_akhir)
                ? (b.no_bak_awal === b.no_bak_akhir ? `(Bak ${b.no_bak_awal})` : `(Bak ${b.no_bak_awal}-${b.no_bak_akhir})`)
                : '';
            html += `
                <button type="button" class="blok-chip" onclick="selectBlokChip('${b.nama_blok}', '${b.no_bak_awal || ''}', '${b.no_bak_akhir || ''}')">
                    <span>${b.nama_blok}</span>
                    ${bakText ? `<span class="blok-chip-bak">${bakText}</span>` : ''}
                </button>
            `;
        });
        container.innerHTML = html;
    }

    function selectBlokChip(namaBlok, bakAwal, bakAkhir) {
        const blokInput = document.getElementById('blok_input');
        const bakInput = document.getElementById('no_bak_input');
        if (blokInput) {
            blokInput.value = namaBlok;
        }
        if (bakInput && bakAwal) {
            if (bakAwal === bakAkhir || !bakAkhir) {
                bakInput.value = bakAwal;
            } else {
                bakInput.value = `${bakAwal}, ${bakAkhir}`;
            }
        }
        checkPermitCompliance();
    }

    function checkPermitCompliance() {
        const idPks = getSelectedPksId();
        const blokInput = document.getElementById('blok_input');
        const bakInput = document.getElementById('no_bak_input');
        const statusCard = document.getElementById('permit_status_card');
        const statusIcon = document.getElementById('permit_status_icon');
        const statusTitle = document.getElementById('permit_status_title');
        const statusDesc = document.getElementById('permit_status_desc');
        const alasanBox = document.getElementById('alasan_luar_izin_box');
        const alasanField = document.getElementById('alasan_field');

        if (!blokInput || !statusCard) return;

        const rawBlok = blokInput.value.trim();
        const rawBak = bakInput ? bakInput.value.trim() : '';

        if (!rawBlok) {
            statusCard.className = 'permit-status-card';
            if (alasanBox && !alasanField.value.trim()) {
                alasanBox.classList.remove('show');
            }
            return;
        }

        const pksData = PKS_BLOK_MAP[idPks];
        if (!pksData || !pksData.bloks || pksData.bloks.length === 0) {
            statusCard.className = 'permit-status-card permit-card-sesuai active';
            statusIcon.innerHTML = '<i class="feather-info" style="color:#16a34a;"></i>';
            statusTitle.textContent = 'PKS Belum Memiliki Master Peta Blok';
            statusDesc.textContent = 'Data pengaliran akan disimpan langsung ke sistem.';
            if (alasanBox) alasanBox.classList.remove('show');
            return;
        }

        // Parse input tokens
        const rawTokens = rawBlok.split(/[\/,\s;&+]+|(?:\bdan\b)/i);
        const cleanTokens = [];
        rawTokens.forEach(t => {
            const clean = t.replace(/^(BLOK|BLOCK|AFD\.?|AFDELING)\s*/i, '').replace(/[^A-Za-z0-9]/g, '').toUpperCase();
            if (clean && !cleanTokens.includes(clean)) cleanTokens.push(clean);
        });

        const bakNumbers = (rawBak.match(/\d+/g) || []).map(Number);

        const matchedBloks = [];
        const unmatchedBloks = [];

        cleanTokens.forEach(token => {
            const found = pksData.bloks.find(b => b.clean_blok === token);
            if (found) {
                matchedBloks.push(found);
            } else {
                unmatchedBloks.push(token);
            }
        });

        let isCompliant = true;
        let reasonMsg = '';

        if (unmatchedBloks.length > 0) {
            isCompliant = false;
            reasonMsg = `Blok [${unmatchedBloks.join(', ')}] tidak tercantum dalam Surat Keputusan Izin LA resmi PKS ini.`;
        } else if (cleanTokens.length === 0) {
            isCompliant = false;
            reasonMsg = 'Format blok pengaliran tidak dikenali.';
        } else if (bakNumbers.length > 0) {
            const invalidBaks = [];
            bakNumbers.forEach(bakNo => {
                const validInAny = matchedBloks.some(b => {
                    const min = b.no_bak_awal !== null ? Number(b.no_bak_awal) : null;
                    const max = b.no_bak_akhir !== null ? Number(b.no_bak_akhir) : null;
                    if (min !== null && max !== null) {
                        return bakNo >= Math.min(min, max) && bakNo <= Math.max(min, max);
                    } else if (min !== null) {
                        return bakNo === min;
                    }
                    return true;
                });
                if (!validInAny) invalidBaks.push(bakNo);
            });

            if (invalidBaks.length > 0) {
                isCompliant = false;
                reasonMsg = `Bak No. [${invalidBaks.join(', ')}] berada di luar rentang bak resmi untuk blok (${cleanTokens.join(', ')}).`;
            }
        }

        if (isCompliant) {
            statusCard.className = 'permit-status-card permit-card-sesuai active';
            statusIcon.innerHTML = '<i class="feather-check-circle" style="color:#16a34a;font-size:16px;"></i>';
            statusTitle.textContent = '✅ Sesuai Izin Land Application';
            statusDesc.textContent = `Blok ${cleanTokens.join(', ')} terdaftar resmi pada Surat Izin LA (${pksData.nomor_sk || 'Terverifikasi'}).`;
            if (alasanBox) {
                alasanBox.classList.remove('show');
            }
        } else {
            statusCard.className = 'permit-status-card permit-card-luar active';
            statusIcon.innerHTML = '<i class="feather-alert-triangle" style="color:#f59e0b;font-size:16px;"></i>';
            statusTitle.textContent = '⚠️ DI LUAR IZIN LAND APLIKASI TERDETEKSI';
            statusDesc.textContent = `${reasonMsg} Sistem mewajibkan Anda mengisi alasan pengaliran di bawah ini.`;
            if (alasanBox) {
                alasanBox.classList.add('show');
            }
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        if (typeof $ !== 'undefined' && $.fn.select2) {
            $('[data-select2-selector]').select2({ width: '100%' });
            $('#pks_select').on('change', function() {
                renderBlokChips(this.value);
                checkPermitCompliance();
            });
        }

        const initialPks = getSelectedPksId();
        if (initialPks) {
            renderBlokChips(initialPks);
        }

        const blokInput = document.getElementById('blok_input');
        const bakInput = document.getElementById('no_bak_input');

        if (blokInput) {
            blokInput.addEventListener('input', checkPermitCompliance);
        }
        if (bakInput) {
            bakInput.addEventListener('input', checkPermitCompliance);
        }

        // Jalankan pengecekan awal saat edit page dimuat
        if (blokInput && blokInput.value.trim()) {
            checkPermitCompliance();
        }
    });
</script>
@endsection
