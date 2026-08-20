@extends('layouts.simoli')

@section('title', 'Edit Log Monitoring Alat Berat')
@section('page-title', 'Edit Log Monitoring Alat Berat')
@section('page-description', 'Perbarui Log Operasional & Jam Kerja (HM) Alat Berat')

@section('breadcrumb')
    <li><a href="{{ route('monitoring-alat-berat.index') }}" style="color:inherit;text-decoration:none;">Monitoring Alat Berat</a></li>
    <li class="separator">/</li>
    <li>Edit Log</li>
@endsection

@section('styles')
<style>
    /* ================================================================
       MONITORING ALAT BERAT EDIT — PTPN GREEN THEME
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

    .gps-card {
        padding: 16px;
        border-radius: 14px;
        background: #f0fdf4;
        border: 1.5px solid rgba(22,163,74,.2);
    }

    .gps-card-akhir {
        padding: 16px;
        border-radius: 14px;
        background: #eff6ff;
        border: 1.5px solid #bfdbfe;
    }

    .photo-upload-box {
        border: 2px dashed rgba(22,163,74,.25);
        border-radius: 14px;
        padding: 20px;
        text-align: center;
        background: #f9fafb;
        cursor: pointer;
        transition: all .2s ease;
    }

    .photo-upload-box:hover {
        border-color: rgba(22,163,74,.5);
        background: #f0fdf4;
    }

    .photo-preview-img {
        max-width: 100%;
        max-height: 180px;
        border-radius: 12px;
        border: 2px solid rgba(22,163,74,.2);
        margin-top: 10px;
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
    html.app-skin-dark .gps-card { background:#0e3b26;border-color:rgba(34,197,94,.2); }
    html.app-skin-dark .gps-card-akhir { background:#0a2317;border-color:rgba(37,99,235,.2); }
</style>
@endsection

@section('content')

{{-- Warning Banner --}}
<div class="edit-warning">
    <i class="feather-edit-2" style="font-size:18px;flex-shrink:0;color:#d97706;"></i>
    <span>Anda sedang mengedit log monitoring alat berat tanggal <strong>{{ \Carbon\Carbon::parse($monitoring->tanggal)->format('d/m/Y') }}</strong>.</span>
</div>

<form action="{{ route('monitoring-alat-berat.update', $monitoring->id) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')
    <div class="row g-4">
        {{-- Left Column --}}
        <div class="col-lg-8">
            {{-- 1. Informasi Unit & Pekerjaan --}}
            <div class="form-card">
                <div class="form-section-title">
                    <i class="feather-truck"></i> 1. Informasi Unit &amp; Pekerjaan
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
                    @if($user->isAdmin())
                        <div class="col-md-6">
                            <label class="form-label">PKS Unit <span class="text-danger">*</span></label>
                            <select name="id_pks" id="id_pks_select" class="form-select @error('id_pks') is-invalid @enderror" required>
                                <option value="">— Pilih PKS Unit —</option>
                                @foreach($pksList as $pks)
                                    <option value="{{ $pks->id_pks }}" {{ old('id_pks', $monitoring->id_pks) == $pks->id_pks ? 'selected' : '' }}>
                                        {{ $pks->nama }} ({{ $pks->akro }})
                                    </option>
                                @endforeach
                            </select>
                            @error('id_pks') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    @endif

                    <div class="col-md-6">
                        <label class="form-label">Pilih Alat Berat <span class="text-danger">*</span></label>
                        <select name="alat_berat_id" class="form-select @error('alat_berat_id') is-invalid @enderror" required>
                            <option value="">— Pilih Unit Alat Berat —</option>
                            @foreach($alatBeratList as $ab)
                                <option value="{{ $ab->id }}" {{ old('alat_berat_id', $monitoring->alat_berat_id) == $ab->id ? 'selected' : '' }}>
                                    [{{ $ab->kode_alat }}] {{ $ab->nama_alat }} ({{ $ab->jenis_alat }})
                                </option>
                            @endforeach
                        </select>
                        @error('alat_berat_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Tanggal Operasional <span class="text-danger">*</span></label>
                        <input type="date" name="tanggal" class="form-control @error('tanggal') is-invalid @enderror" value="{{ old('tanggal', $monitoring->tanggal) }}" required>
                        @error('tanggal') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Nama Operator / Penanggung Jawab <span class="text-danger">*</span></label>
                        <input type="text" name="operator" class="form-control @error('operator') is-invalid @enderror" value="{{ old('operator', $monitoring->operator) }}" required>
                        @error('operator') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Jenis Kegiatan Pengolahan <span class="text-danger">*</span></label>
                        <select name="kegiatan" class="form-select @error('kegiatan') is-invalid @enderror" required>
                            <option value="">— Pilih Jenis Kegiatan —</option>
                            <option value="Pembersihan & Pengerukan Kolam Limbah" {{ old('kegiatan', $monitoring->kegiatan) == 'Pembersihan & Pengerukan Kolam Limbah' ? 'selected' : '' }}>Pembersihan &amp; Pengerukan Kolam Limbah</option>
                            <option value="Aplikasi Lahan (Land Application)" {{ old('kegiatan', $monitoring->kegiatan) == 'Aplikasi Lahan (Land Application)' ? 'selected' : '' }}>Aplikasi Lahan (Land Application)</option>
                            <option value="Pengadukan Kolam Limbah / Anaerob" {{ old('kegiatan', $monitoring->kegiatan) == 'Pengadukan Kolam Limbah / Anaerob' ? 'selected' : '' }}>Pengadukan Kolam Limbah / Anaerob</option>
                            <option value="Transportasi & Loading Sludge / Solid" {{ old('kegiatan', $monitoring->kegiatan) == 'Transportasi & Loading Sludge / Solid' ? 'selected' : '' }}>Transportasi &amp; Loading Sludge / Solid</option>
                            <option value="Perbaikan Pematang / Tanggul Kolam" {{ old('kegiatan', $monitoring->kegiatan) == 'Perbaikan Pematang / Tanggul Kolam' ? 'selected' : '' }}>Perbaikan Pematang / Tanggul Kolam</option>
                            <option value="Pemeliharaan Routine Alat Berat" {{ old('kegiatan', $monitoring->kegiatan) == 'Pemeliharaan Routine Alat Berat' ? 'selected' : '' }}>Pemeliharaan Routine Alat Berat</option>
                            <option value="Kegiatan Pengolahan Lainnya" {{ old('kegiatan', $monitoring->kegiatan) == 'Kegiatan Pengolahan Lainnya' ? 'selected' : '' }}>Kegiatan Pengolahan Lainnya</option>
                        </select>
                        @error('kegiatan') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Lokasi / Kolam / Blok Kerja</label>
                        <input type="text" name="lokasi_blok" class="form-control" value="{{ old('lokasi_blok', $monitoring->lokasi_blok) }}">
                    </div>
                </div>
            </div>

            {{-- 2. GPS Koordinat --}}
            <div class="form-card">
                <div class="form-section-title">
                    <i class="feather-map-pin"></i> 2. Titik Koordinat GPS Kerja
                </div>
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="gps-card">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <label class="form-label fw-bold mb-0 text-success" style="font-size:11.5px;">
                                    <i class="feather-navigation me-1"></i> Koordinat Awal Kerja
                                </label>
                                <button type="button" class="btn-ptpn btn-ptpn-primary" style="padding:4px 10px;font-size:11px;" onclick="getGpsAwal()">
                                    <i class="feather-crosshair me-1"></i> Update GPS
                                </button>
                            </div>
                            <div class="row g-2">
                                <div class="col-6">
                                    <input type="text" name="latitude_awal" id="latitude_awal" class="form-control form-control-sm" readonly placeholder="Lat Awal" value="{{ old('latitude_awal', $monitoring->latitude_awal ?? $monitoring->latitude) }}">
                                    <input type="hidden" name="latitude" id="latitude" value="{{ old('latitude', $monitoring->latitude) }}">
                                </div>
                                <div class="col-6">
                                    <input type="text" name="longitude_awal" id="longitude_awal" class="form-control form-control-sm" readonly placeholder="Long Awal" value="{{ old('longitude_awal', $monitoring->longitude_awal ?? $monitoring->longitude) }}">
                                    <input type="hidden" name="longitude" id="longitude" value="{{ old('longitude', $monitoring->longitude) }}">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="gps-card-akhir">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <label class="form-label fw-bold mb-0" style="font-size:11.5px;color:#1d4ed8;">
                                    <i class="feather-navigation me-1"></i> Koordinat Akhir Kerja
                                </label>
                                <button type="button" class="btn-ptpn btn-ptpn-outline" style="padding:4px 10px;font-size:11px;" onclick="getGpsAkhir()">
                                    <i class="feather-crosshair me-1"></i> Update GPS
                                </button>
                            </div>
                            <div class="row g-2">
                                <div class="col-6">
                                    <input type="text" name="latitude_akhir" id="latitude_akhir" class="form-control form-control-sm" readonly placeholder="Lat Akhir" value="{{ old('latitude_akhir', $monitoring->latitude_akhir) }}">
                                </div>
                                <div class="col-6">
                                    <input type="text" name="longitude_akhir" id="longitude_akhir" class="form-control form-control-sm" readonly placeholder="Long Akhir" value="{{ old('longitude_akhir', $monitoring->longitude_akhir) }}">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- 3. Jam Kerja HM & BBM --}}
            <div class="form-card">
                <div class="form-section-title">
                    <i class="feather-clock"></i> 3. Jam Kerja (HM) &amp; Consumsi BBM
                </div>
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">HM Awal <span class="text-danger">*</span></label>
                        <input type="number" name="hm_awal" class="form-control @error('hm_awal') is-invalid @enderror" value="{{ old('hm_awal', $monitoring->hm_awal) }}" min="0" step="any" required>
                        @error('hm_awal') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">HM Akhir <span class="text-danger">*</span></label>
                        <input type="number" name="hm_akhir" class="form-control @error('hm_akhir') is-invalid @enderror" value="{{ old('hm_akhir', $monitoring->hm_akhir) }}" min="0" step="any" required>
                        @error('hm_akhir') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Konsumsi BBM (Liter) <span class="text-danger">*</span></label>
                        <input type="number" name="bbm_liter" class="form-control @error('bbm_liter') is-invalid @enderror" value="{{ old('bbm_liter', $monitoring->bbm_liter) }}" min="0" step="any" required>
                        @error('bbm_liter') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="row g-3 mt-2">
                    <div class="col-md-6">
                        <label class="form-label">Jumlah Flat Bed Dikerjakan</label>
                        <input type="number" name="flat_bed" class="form-control" value="{{ old('flat_bed', $monitoring->flat_bed ?? 0) }}" min="0">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Jumlah Long Bed Dikerjakan</label>
                        <input type="number" name="long_bed" class="form-control" value="{{ old('long_bed', $monitoring->long_bed ?? 0) }}" min="0">
                    </div>
                </div>
            </div>

            {{-- 4. Foto Dokumentasi --}}
            <div class="form-card">
                <div class="form-section-title">
                    <i class="feather-camera"></i> 4. Foto Dokumentasi Kerja
                </div>
                <div class="row g-4">
                    <div class="col-md-6">
                        <label class="form-label d-flex align-items-center">
                            <span class="mod-pill mod-pill-err me-2" style="font-size:10px;">Sebelum</span> Foto Sebelum
                        </label>
                        @if($monitoring->foto_sebelum_url)
                            <div class="mb-2 text-center">
                                <img src="{{ $monitoring->foto_sebelum_url }}" alt="Foto Sebelum" class="photo-preview-img d-block mx-auto mb-1">
                                <small class="text-muted">Foto saat ini</small>
                            </div>
                        @endif
                        <div class="photo-upload-box" onclick="document.getElementById('foto_sebelum').click()">
                            <i class="feather-upload-cloud d-block mb-2" style="font-size: 28px; color: #16a34a;"></i>
                            <small class="text-muted d-block" id="label_sebelum">{{ $monitoring->foto_sebelum ? 'Upload baru untuk mengganti' : 'Klik untuk upload foto sebelum' }}</small>
                            <input type="file" name="foto_sebelum" id="foto_sebelum" class="d-none" accept="image/*" onchange="previewImage(this, 'preview_sebelum', 'label_sebelum')">
                        </div>
                        <img id="preview_sebelum" class="photo-preview-img" style="display:none;" alt="Preview Sebelum">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label d-flex align-items-center">
                            <span class="mod-pill mod-pill-ok me-2" style="font-size:10px;">Sesudah</span> Foto Sesudah
                        </label>
                        @if($monitoring->foto_sesudah_url)
                            <div class="mb-2 text-center">
                                <img src="{{ $monitoring->foto_sesudah_url }}" alt="Foto Sesudah" class="photo-preview-img d-block mx-auto mb-1">
                                <small class="text-muted">Foto saat ini</small>
                            </div>
                        @endif
                        <div class="photo-upload-box" onclick="document.getElementById('foto_sesudah').click()">
                            <i class="feather-upload-cloud d-block mb-2" style="font-size: 28px; color: #16a34a;"></i>
                            <small class="text-muted d-block" id="label_sesudah">{{ $monitoring->foto_sesudah ? 'Upload baru untuk mengganti' : 'Klik untuk upload foto sesudah' }}</small>
                            <input type="file" name="foto_sesudah" id="foto_sesudah" class="d-none" accept="image/*" onchange="previewImage(this, 'preview_sesudah', 'label_sesudah')">
                        </div>
                        <img id="preview_sesudah" class="photo-preview-img" style="display:none;" alt="Preview Sesudah">
                    </div>
                </div>
            </div>
        </div>

        {{-- Right Column --}}
        <div class="col-lg-4">
            <div class="form-card">
                <div class="form-section-title">
                    <i class="feather-activity"></i> Kondisi &amp; Catatan
                </div>
                <div class="mb-3">
                    <label class="form-label">Kondisi Alat Berat <span class="text-danger">*</span></label>
                    <select name="kondisi_alat" class="form-select @error('kondisi_alat') is-invalid @enderror" required>
                        <option value="Normal" {{ old('kondisi_alat', $monitoring->kondisi_alat) == 'Normal' ? 'selected' : '' }}>Normal / Siap Pakai</option>
                        <option value="Perlu Perbaikan" {{ old('kondisi_alat', $monitoring->kondisi_alat) == 'Perlu Perbaikan' ? 'selected' : '' }}>Perlu Perbaikan Minor</option>
                        <option value="Breakdown" {{ old('kondisi_alat', $monitoring->kondisi_alat) == 'Breakdown' ? 'selected' : '' }}>Breakdown / Rusak Berat</option>
                    </select>
                    @error('kondisi_alat') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div>
                    <label class="form-label">Catatan Operasional</label>
                    <textarea name="catatan" rows="4" class="form-control" placeholder="Tambahkan catatan khusus...">{{ old('catatan', $monitoring->catatan) }}</textarea>
                </div>
            </div>

            {{-- Action buttons --}}
            <div class="d-grid gap-3">
                <button type="submit" class="btn-ptpn btn-ptpn-primary" style="padding:14px;font-size:15px;font-weight:800;border-radius:14px;">
                    <i class="feather-save" style="font-size:16px;"></i>
                    Perbarui Log Monitoring
                </button>
                <a href="{{ route('monitoring-alat-berat.index') }}" class="btn-ptpn btn-ptpn-outline" style="padding:12px;font-size:14px;border-radius:14px;text-align:center;justify-content:center;">
                    <i class="feather-arrow-left" style="font-size:15px;"></i>
                    Kembali
                </a>
            </div>
        </div>
    </div>
</form>
@endsection

@section('scripts')
<script>
    function previewImage(input, previewId, labelId) {
        const preview = document.getElementById(previewId);
        const label = document.getElementById(labelId);
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.src = e.target.result;
                preview.style.display = 'block';
            };
            reader.readAsDataURL(input.files[0]);
            label.textContent = '✓ ' + input.files[0].name;
            label.style.color = '#16a34a';
            label.style.fontWeight = '700';
        }
    }

    function getGpsAwal() {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(function(pos) {
                document.getElementById('latitude_awal').value = pos.coords.latitude;
                document.getElementById('longitude_awal').value = pos.coords.longitude;
                document.getElementById('latitude').value = pos.coords.latitude;
                document.getElementById('longitude').value = pos.coords.longitude;
            });
        }
    }

    function getGpsAkhir() {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(function(pos) {
                document.getElementById('latitude_akhir').value = pos.coords.latitude;
                document.getElementById('longitude_akhir').value = pos.coords.longitude;
            });
        }
    }
</script>
@endsection
