@extends('layouts.operator')

@section('title', 'Input Laporan Kerja - SIMOLII Operator')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-3">
    <h5 class="fw-bold m-0"><i class="feather-plus-circle me-2 text-primary"></i>Input Laporan Kerja Alat Berat</h5>
    <a href="{{ route('operator.index') }}" class="btn btn-sm btn-outline-secondary rounded-pill">
        <i class="feather-arrow-left me-1"></i>Kembali
    </a>
</div>

<form action="{{ route('operator.store') }}" method="POST" enctype="multipart/form-data" id="formOperatorReport">
    @csrf

    <!-- Card 1: Unit & Detail Pekerjaan -->
    <div class="op-card mb-3">
        <div class="op-card-header">
            <span><i class="feather-truck me-2 text-primary"></i>1. Informasi Unit & Pekerjaan</span>
        </div>
        <div class="op-card-body">
            <div class="mb-3">
                <label class="form-label">Unit Alat Berat <span class="text-danger">*</span></label>
                <select name="alat_berat_id" class="form-select @error('alat_berat_id') is-invalid @enderror" required>
                    <option value="">-- Pilih Unit Alat Berat --</option>
                    @foreach($alatBeratList as $ab)
                        <option value="{{ $ab->id }}" {{ old('alat_berat_id') == $ab->id ? 'selected' : '' }}>
                            [{{ $ab->kode_alat }}] {{ $ab->nama_alat }} ({{ $ab->jenis_alat }}){{ $ab->status === 'Standby' ? ' - [Standby/Cadangan]' : '' }}
                        </option>
                    @endforeach
                </select>
                @error('alat_berat_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="row">
                <div class="col-6 mb-3">
                    <label class="form-label">Tanggal <span class="text-danger">*</span></label>
                    <input type="date" name="tanggal" class="form-control @error('tanggal') is-invalid @enderror" value="{{ old('tanggal', date('Y-m-d')) }}" required>
                    @error('tanggal') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-6 mb-3">
                    <label class="form-label">Operator <span class="text-danger">*</span></label>
                    <input type="text" name="operator" class="form-control @error('operator') is-invalid @enderror" value="{{ old('operator', Auth::user()->username) }}" required placeholder="Nama Operator">
                    @error('operator') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Jenis Kegiatan / Pekerjaan <span class="text-danger">*</span></label>
                <input type="text" name="kegiatan" class="form-control @error('kegiatan') is-invalid @enderror" value="{{ old('kegiatan') }}" required placeholder="Contoh: Pengerukan kolam anaerob / Aplikasi Lahan">
                @error('kegiatan') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="mb-0">
                <label class="form-label">Lokasi / Blok Pekerjaan</label>
                <input type="text" name="lokasi_blok" class="form-control @error('lokasi_blok') is-invalid @enderror" value="{{ old('lokasi_blok') }}" placeholder="Contoh: Kolam 2 Anaerob / Blok C18">
                @error('lokasi_blok') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
        </div>
    </div>

    <!-- Card 2: Lokasi GPS Kerja (Koordinat Awal & Akhir Kerja) - POSISI DI ATAS -->
    <div class="op-card mb-3">
        <div class="op-card-header">
            <span><i class="feather-map-pin me-2 text-primary"></i>2. Lokasi GPS Kerja (Koordinat Awal & Akhir)</span>
        </div>
        <div class="op-card-body">
            <div class="row g-3">
                <!-- GPS Awal -->
                <div class="col-md-6 border-end-md pb-2">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <label class="form-label fw-bold mb-0 text-primary fs-12"><i class="feather-navigation me-1"></i>Koordinat Awal Kerja</label>
                        <button type="button" class="btn btn-xs btn-outline-primary rounded-pill px-2.5 py-0.5 fs-11" onclick="getGpsAwal()">
                            <i class="feather-crosshair me-1"></i>GPS Awal
                        </button>
                    </div>
                    <div class="row g-2">
                        <div class="col-6">
                            <input type="text" name="latitude_awal" id="latitude_awal" class="form-control form-control-sm bg-light" readonly placeholder="Lat Awal" value="{{ old('latitude_awal') }}">
                            <input type="hidden" name="latitude" id="latitude" value="{{ old('latitude') }}">
                        </div>
                        <div class="col-6">
                            <input type="text" name="longitude_awal" id="longitude_awal" class="form-control form-control-sm bg-light" readonly placeholder="Long Awal" value="{{ old('longitude_awal') }}">
                            <input type="hidden" name="longitude" id="longitude" value="{{ old('longitude') }}">
                        </div>
                    </div>
                    <small id="gps_awal_status" class="fs-11 text-muted mt-1 d-block">Terisi otomatis saat Foto Sebelum dipilih/diambil.</small>
                </div>

                <!-- GPS Akhir -->
                <div class="col-md-6 pt-2 pt-md-0">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <label class="form-label fw-bold mb-0 text-success fs-12"><i class="feather-navigation me-1"></i>Koordinat Akhir Kerja</label>
                        <button type="button" class="btn btn-xs btn-outline-success rounded-pill px-2.5 py-0.5 fs-11" onclick="getGpsAkhir()">
                            <i class="feather-crosshair me-1"></i>GPS Akhir
                        </button>
                    </div>
                    <div class="row g-2">
                        <div class="col-6">
                            <input type="text" name="latitude_akhir" id="latitude_akhir" class="form-control form-control-sm bg-light" readonly placeholder="Lat Akhir" value="{{ old('latitude_akhir') }}">
                        </div>
                        <div class="col-6">
                            <input type="text" name="longitude_akhir" id="longitude_akhir" class="form-control form-control-sm bg-light" readonly placeholder="Long Akhir" value="{{ old('longitude_akhir') }}">
                        </div>
                    </div>
                    <small id="gps_akhir_status" class="fs-11 text-muted mt-1 d-block">Terisi otomatis saat Foto Sesudah dipilih/diambil.</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Card 3: Foto Dokumentasi Kerja - GAMBAR DI ATAS JAM -->
    <div class="op-card mb-3">
        <div class="op-card-header">
            <span><i class="feather-camera me-2 text-primary"></i>3. Foto Dokumentasi Kerja</span>
        </div>
        <div class="op-card-body">
            <div class="row g-3">
                <div class="col-6">
                    <label class="form-label text-center d-block">Foto Sebelum Kerja <span class="text-danger">*</span></label>
                    <div class="photo-preview-box" onclick="document.getElementById('foto_sebelum').click()" style="cursor: pointer;">
                        <div id="preview_sebelum_placeholder" class="text-center p-2">
                            <i class="feather-camera fs-3 text-muted d-block mb-1"></i>
                            <span class="fs-12 text-muted fw-semibold">Pilih / Ambil Foto</span>
                        </div>
                        <img id="img_sebelum_preview" src="" style="display: none;">
                        <span id="ts_sebelum_tag" class="timestamp-tag" style="display: none;"></span>
                    </div>
                    <input type="file" name="foto_sebelum" id="foto_sebelum" accept="image/*" style="display: none;" onchange="handlePhotoSelect(this, 'img_sebelum_preview', 'preview_sebelum_placeholder', 'ts_sebelum_tag')">
                    @error('foto_sebelum') <small class="text-danger d-block mt-1 fs-11">{{ $message }}</small> @enderror
                </div>

                <div class="col-6">
                    <label class="form-label text-center d-block">Foto Sesudah Kerja</label>
                    <div class="photo-preview-box" onclick="document.getElementById('foto_sesudah').click()" style="cursor: pointer;">
                        <div id="preview_sesudah_placeholder" class="text-center p-2">
                            <i class="feather-camera fs-3 text-muted d-block mb-1"></i>
                            <span class="fs-12 text-muted fw-semibold">Pilih / Ambil Foto</span>
                        </div>
                        <img id="img_sesudah_preview" src="" style="display: none;">
                        <span id="ts_sesudah_tag" class="timestamp-tag" style="display: none;"></span>
                    </div>
                    <input type="file" name="foto_sesudah" id="foto_sesudah" accept="image/*" style="display: none;" onchange="handlePhotoSelect(this, 'img_sesudah_preview', 'preview_sesudah_placeholder', 'ts_sesudah_tag')">
                    @error('foto_sesudah') <small class="text-danger d-block mt-1 fs-11">{{ $message }}</small> @enderror
                </div>
            </div>
        </div>
    </div>

    <!-- Card 4: Hour Meter (HM) & Jam Kerja - JAM TIDAK BOLEH INPUT MANUAL -->
    <div class="op-card mb-3">
        <div class="op-card-header">
            <span><i class="feather-clock me-2 text-primary"></i>4. Hour Meter (HM) & Jam Kerja</span>
        </div>
        <div class="op-card-body">
            <div class="row">
                <div class="col-6 mb-3">
                    <label class="form-label">HM / Jam Awal Kerja <span class="text-danger">*</span></label>
                    <input type="text" name="hm_awal" id="hm_awal" 
                        class="form-control bg-light @error('hm_awal') is-invalid @enderror" 
                        value="{{ old('hm_awal') }}" placeholder="HH:MM (Otomatis dari Foto)" 
                        readonly oninput="hitungHm()">
                    <small id="msg_hm_awal" class="fs-11 d-block mt-1 text-danger">
                        <i class="feather-lock me-1"></i>Jam Awal terisi otomatis dari foto & tidak dapat diisi manual.
                    </small>
                    @error('hm_awal') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-6 mb-3">
                    <label class="form-label">HM / Jam Akhir Kerja</label>
                    <input type="text" name="hm_akhir" id="hm_akhir" 
                        class="form-control bg-light @error('hm_akhir') is-invalid @enderror" 
                        value="{{ old('hm_akhir') }}" placeholder="HH:MM (Otomatis dari Foto)" 
                        readonly oninput="hitungHm()">
                    <small id="msg_hm_akhir" class="fs-11 d-block mt-1 text-danger">
                        <i class="feather-lock me-1"></i>Jam Akhir terisi otomatis dari foto & tidak dapat diisi manual.
                    </small>
                    @error('hm_akhir') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>

            <div class="p-2 bg-light rounded-3 text-center border">
                <span class="text-muted fs-12 fw-semibold">Total Jam Kerja (HM):</span>
                <span id="total_hm_display" class="fw-bold fs-5 text-success ms-2">00:00 Jam</span>
            </div>
        </div>
    </div>

    <!-- Card 5: Hasil Aplikasi Bed -->
    <div class="op-card mb-3">
        <div class="op-card-header">
            <span><i class="feather-grid me-2 text-primary"></i>5. Hasil Aplikasi Bed (Aplikasi Lahan)</span>
        </div>
        <div class="op-card-body">
            <div class="row">
                <div class="col-6 mb-3">
                    <label class="form-label">Flat Bed (Jumlah)</label>
                    <input type="number" name="flat_bed" id="flat_bed" class="form-control @error('flat_bed') is-invalid @enderror" value="{{ old('flat_bed', 0) }}" min="0" oninput="calcBedTotal()">
                    @error('flat_bed') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-6 mb-3">
                    <label class="form-label">Long Bed (Jumlah)</label>
                    <input type="number" name="long_bed" id="long_bed" class="form-control @error('long_bed') is-invalid @enderror" value="{{ old('long_bed', 0) }}" min="0" oninput="calcBedTotal()">
                    @error('long_bed') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>

            <div class="p-2 bg-light rounded-3 text-center border">
                <span class="text-muted fs-12 fw-semibold">Total Bed Dikerjakan:</span>
                <span id="total_bed_display" class="fw-bold fs-5 text-primary ms-2">0</span> Bed
                <input type="hidden" name="jumlah_bed" id="jumlah_bed" value="{{ old('jumlah_bed', 0) }}">
            </div>
        </div>
    </div>

    <!-- Card 6: BBM & Kondisi Alat -->
    <div class="op-card mb-3">
        <div class="op-card-header">
            <span><i class="feather-check-square me-2 text-primary"></i>6. Konsumsi BBM & Kondisi Alat</span>
        </div>
        <div class="op-card-body">
            <div class="mb-3">
                <label class="form-label">Pengisian BBM (Liter)</label>
                <input type="number" step="0.1" name="bbm_liter" class="form-control @error('bbm_liter') is-invalid @enderror" value="{{ old('bbm_liter', 0) }}" placeholder="Contoh: 75.0" min="0">
                @error('bbm_liter') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="mb-3">
                <label class="form-label">Kondisi Alat Berat <span class="text-danger">*</span></label>
                <select name="kondisi_alat" class="form-select @error('kondisi_alat') is-invalid @enderror" required>
                    <option value="Normal" {{ old('kondisi_alat') == 'Normal' ? 'selected' : '' }}>Normal / Baik</option>
                    <option value="Perlu Perbaikan" {{ old('kondisi_alat') == 'Perlu Perbaikan' ? 'selected' : '' }}>Perlu Perbaikan (Maintenance)</option>
                    <option value="Breakdown" {{ old('kondisi_alat') == 'Breakdown' ? 'selected' : '' }}>Breakdown (Rusak)</option>
                </select>
                @error('kondisi_alat') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="mb-0">
                <label class="form-label">Catatan / Kendala Operasional</label>
                <textarea name="catatan" class="form-control" rows="2" placeholder="Catatan kondisi alat atau kendala di lapangan...">{{ old('catatan') }}</textarea>
            </div>
        </div>
    </div>

    <!-- Submit Button -->
    <div class="mb-4">
        <button type="submit" class="btn btn-op-primary shadow-lg py-3">
            <i class="feather-save me-2"></i>SIMPAN LAPORAN KERJA
        </button>
    </div>
</form>
@endsection

@section('scripts')
<script>
    function calcBedTotal() {
        const flat = parseInt(document.getElementById('flat_bed').value) || 0;
        const long = parseInt(document.getElementById('long_bed').value) || 0;
        const total = flat + long;
        document.getElementById('total_bed_display').innerText = total;
        document.getElementById('jumlah_bed').value = total;
    }

    function formatTimeToHHMM(dateObj) {
        const h = dateObj.getHours();
        const m = dateObj.getMinutes();
        const hStr = h < 10 ? '0' + h : h;
        const mStr = m < 10 ? '0' + m : m;
        return `${hStr}:${mStr}`;
    }

    function parseTimeToMinutes(val) {
        if (!val) return 0;
        let str = String(val).trim().replace(',', '.');
        if (str.includes(':')) {
            const parts = str.split(':');
            const h = parseInt(parts[0], 10) || 0;
            const m = parseInt(parts[1], 10) || 0;
            return (h * 60) + m;
        }
        if (str.includes('.')) {
            const parts = str.split('.');
            const p0 = parseInt(parts[0], 10) || 0;
            const p1Str = parts[1] || '0';
            const p1 = parseInt(p1Str, 10) || 0;
            if (p0 >= 0 && p0 <= 23 && p1 < 60 && p1Str.length <= 2 && !isNaN(p0) && !isNaN(p1)) {
                const m = parseInt(p1Str.padEnd(2, '0'), 10) || 0;
                return (p0 * 60) + m;
            }
            const num = parseFloat(str) || 0;
            return Math.round(num * 60);
        }
        const num = parseFloat(str) || 0;
        return Math.round(num * 60);
    }

    function minutesToTime(totalMins) {
        if (totalMins <= 0 || isNaN(totalMins)) return '00:00 Jam';
        const h = Math.floor(totalMins / 60);
        const m = totalMins % 60;
        const hStr = h < 10 ? '0' + h : h;
        const mStr = m < 10 ? '0' + m : m;
        return `${hStr}:${mStr} Jam`;
    }

    function hitungHm() {
        const hmAwalInput = document.getElementById('hm_awal');
        const hmAkhirInput = document.getElementById('hm_akhir');
        const totalHmDisplay = document.getElementById('total_hm_display');

        if (!hmAwalInput.value || !hmAkhirInput.value) {
            if (hmAwalInput.value && !hmAkhirInput.value) {
                totalHmDisplay.innerText = 'Proses / Berjalan';
            } else {
                totalHmDisplay.innerText = '00:00 Jam';
            }
            return;
        }
        const awalMins = parseTimeToMinutes(hmAwalInput.value);
        const akhirMins = parseTimeToMinutes(hmAkhirInput.value);
        let diff = akhirMins - awalMins;
        if (diff < 0 && awalMins > 0 && akhirMins > 0 && (hmAwalInput.value.includes(':') || hmAwalInput.value.includes('.'))) {
            diff += 24 * 60;
        }
        const finalDiff = Math.max(0, diff);
        totalHmDisplay.innerText = minutesToTime(finalDiff);
    }

    function getGpsAwal() {
        const statusElem = document.getElementById('gps_awal_status');
        if (statusElem) statusElem.innerText = 'Tunggu sebentar, merekam lokasi GPS Awal...';

        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(
                function(position) {
                    const lat = position.coords.latitude.toFixed(8);
                    const long = position.coords.longitude.toFixed(8);
                    document.getElementById('latitude_awal').value = lat;
                    document.getElementById('longitude_awal').value = long;
                    document.getElementById('latitude').value = lat;
                    document.getElementById('longitude').value = long;
                    if (statusElem) statusElem.innerHTML = `<span class="text-success"><i class="feather-check-circle me-1"></i>GPS Awal berhasil direkam! (${lat}, ${long})</span>`;
                },
                function(error) {
                    if (statusElem) statusElem.innerHTML = `<span class="text-danger"><i class="feather-alert-triangle me-1"></i>Gagal mengambil GPS Awal: ${error.message}</span>`;
                },
                { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 }
            );
        } else {
            if (statusElem) statusElem.innerText = 'Browser Anda tidak mendukung geolokasi GPS.';
        }
    }

    function getGpsAkhir() {
        const statusElem = document.getElementById('gps_akhir_status');
        if (statusElem) statusElem.innerText = 'Tunggu sebentar, merekam lokasi GPS Akhir...';

        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(
                function(position) {
                    const lat = position.coords.latitude.toFixed(8);
                    const long = position.coords.longitude.toFixed(8);
                    document.getElementById('latitude_akhir').value = lat;
                    document.getElementById('longitude_akhir').value = long;
                    if (statusElem) statusElem.innerHTML = `<span class="text-success"><i class="feather-check-circle me-1"></i>GPS Akhir berhasil direkam! (${lat}, ${long})</span>`;
                },
                function(error) {
                    if (statusElem) statusElem.innerHTML = `<span class="text-danger"><i class="feather-alert-triangle me-1"></i>Gagal mengambil GPS Akhir: ${error.message}</span>`;
                },
                { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 }
            );
        } else {
            if (statusElem) statusElem.innerText = 'Browser Anda tidak mendukung geolokasi GPS.';
        }
    }

    function handlePhotoSelect(input, imgId, placeholderId, tsTagId) {
        if (input.files && input.files[0]) {
            const file = input.files[0];
            const reader = new FileReader();

            reader.onload = function(e) {
                const img = document.getElementById(imgId);
                const placeholder = document.getElementById(placeholderId);
                const tsTag = document.getElementById(tsTagId);

                img.src = e.target.result;
                img.style.display = 'block';
                if (placeholder) placeholder.style.display = 'none';

                const now = new Date();
                const timeString = now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
                const timeHHMM = formatTimeToHHMM(now);

                tsTag.innerText = timeString;
                tsTag.style.display = 'block';

                if (input.id === 'foto_sebelum') {
                    const hmAwal = document.getElementById('hm_awal');
                    hmAwal.value = timeHHMM;
                    hitungHm();
                    getGpsAwal();
                } else if (input.id === 'foto_sesudah') {
                    const hmAkhir = document.getElementById('hm_akhir');
                    hmAkhir.value = timeHHMM;
                    hitungHm();
                    getGpsAkhir();
                }
            }

            reader.readAsDataURL(file);
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        calcBedTotal();
        hitungHm();
    });
</script>
@endsection
