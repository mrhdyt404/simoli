@extends('layouts.operator')

@section('title', 'Input Laporan Kerja - SIMOLII Operator')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-3">
    <div>
        <h5 class="fw-bold m-0 text-dark fs-17">
            <i class="feather-plus-circle me-1.5 text-primary"></i>Input Laporan Kerja Alat Berat
        </h5>
        <div class="text-muted fs-12">Isi data awal shift sebelum memulai pekerjaan operasional</div>
    </div>
    <a href="{{ route('operator.index') }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3 fs-12 fw-semibold">
        <i class="feather-arrow-left me-1"></i>Kembali
    </a>
</div>

<form action="{{ route('operator.store') }}" method="POST" enctype="multipart/form-data" id="formOperatorReport">
    @csrf

    <!-- Card 1: Unit & Waktu Kerja -->
    <div class="op-card mb-3">
        <div class="op-card-header">
            <span class="d-flex align-items-center">
                <span class="step-badge">1</span>
                <span>Unit Alat & Informasi Pekerjaan</span>
            </span>
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

            <div class="row g-2 mb-3">
                <div class="col-6">
                    <label class="form-label">Tanggal <span class="text-danger">*</span></label>
                    <input type="date" name="tanggal" class="form-control @error('tanggal') is-invalid @enderror" value="{{ old('tanggal', date('Y-m-d')) }}" required>
                    @error('tanggal') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-6">
                    <label class="form-label">Nama Operator <span class="text-danger">*</span></label>
                    <input type="text" name="operator" class="form-control @error('operator') is-invalid @enderror" value="{{ old('operator', Auth::user()->username) }}" required placeholder="Nama Operator">
                    @error('operator') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Jenis Kegiatan / Pekerjaan <span class="text-danger">*</span></label>
                <input type="text" name="kegiatan" class="form-control @error('kegiatan') is-invalid @enderror" value="{{ old('kegiatan') }}" required placeholder="Contoh: Pengerukan kolam / Normalisasi Bed / Aplikasi Lahan">
                @error('kegiatan') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="mb-0">
                <label class="form-label">Lokasi / Blok Pekerjaan</label>
                <input type="text" name="lokasi_blok" id="lokasi_blok" class="form-control @error('lokasi_blok') is-invalid @enderror" value="{{ old('lokasi_blok') }}" placeholder="Contoh: Kolam 2 Anaerob / Blok C18">
                <small class="text-muted fs-11">Wajib diisi saat menyelesaikan shift kerja.</small>
                @error('lokasi_blok') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
        </div>
    </div>

    <!-- Card 2: Foto Dokumentasi Kerja (Sebelum & Sesudah) & Jam Kerja -->
    <div class="op-card mb-3">
        <div class="op-card-header">
            <span class="d-flex align-items-center">
                <span class="step-badge">2</span>
                <span>Foto Dokumentasi & Jam Kerja Otomatis</span>
            </span>
        </div>
        <div class="op-card-body">
            <div class="row g-3 mb-3">
                <!-- Foto Sebelum -->
                <div class="col-12 col-sm-6">
                    <label class="form-label text-dark fw-bold mb-1">
                        Foto Sebelum Kerja (Awal Shift) <span class="text-danger">*</span>
                    </label>
                    <div class="photo-preview-box mb-2" onclick="document.getElementById('foto_sebelum').click()">
                        <div id="preview_sebelum_placeholder" class="text-center p-3">
                            <i class="feather-camera fs-2 text-primary d-block mb-1"></i>
                            <span class="fs-13 text-dark fw-bold d-block">Ambil Foto Sebelum</span>
                            <small class="text-muted fs-11">Ketuk untuk buka kamera / galeri</small>
                        </div>
                        <img id="img_sebelum_preview" src="" style="display: none;" alt="Preview Foto Sebelum">
                        <span id="ts_sebelum_tag" class="timestamp-tag" style="display: none;"></span>
                    </div>
                    <input type="file" name="foto_sebelum" id="foto_sebelum" accept="image/*" capture="environment" style="display: none;" onchange="handlePhotoSelect(this, 'img_sebelum_preview', 'preview_sebelum_placeholder', 'ts_sebelum_tag')">
                    
                    <div class="p-2 bg-light rounded-3 border">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="fs-11 fw-bold text-primary"><i class="feather-navigation me-1"></i>GPS Awal:</span>
                            <button type="button" class="btn btn-xs btn-outline-primary rounded-pill px-2 py-0.5 fs-11" onclick="getGpsAwal()">
                                <i class="feather-crosshair me-1"></i>Ulang GPS
                            </button>
                        </div>
                        <div class="row g-1">
                            <div class="col-6">
                                <input type="text" name="latitude_awal" id="latitude_awal" class="form-control form-control-sm bg-white" readonly placeholder="Lat Awal" value="{{ old('latitude_awal') }}">
                                <input type="hidden" name="latitude" id="latitude" value="{{ old('latitude') }}">
                            </div>
                            <div class="col-6">
                                <input type="text" name="longitude_awal" id="longitude_awal" class="form-control form-control-sm bg-white" readonly placeholder="Long Awal" value="{{ old('longitude_awal') }}">
                                <input type="hidden" name="longitude" id="longitude" value="{{ old('longitude') }}">
                            </div>
                        </div>
                        <small id="gps_awal_status" class="fs-11 text-muted mt-1 d-block">Otomatis tersimpan saat foto diambil.</small>
                    </div>
                    @error('foto_sebelum') <small class="text-danger d-block mt-1 fs-11">{{ $message }}</small> @enderror
                </div>

                <!-- Foto Sesudah -->
                <div class="col-12 col-sm-6">
                    <label class="form-label text-dark fw-bold mb-1">
                        Foto Sesudah Kerja (Akhir Shift)
                        <span class="badge bg-secondary-subtle text-secondary fs-11 fw-normal ms-1">Bisa diisi nanti</span>
                    </label>
                    <div class="photo-preview-box mb-2" onclick="document.getElementById('foto_sesudah').click()">
                        <div id="preview_sesudah_placeholder" class="text-center p-3">
                            <i class="feather-camera fs-2 text-success d-block mb-1"></i>
                            <span class="fs-13 text-dark fw-bold d-block">Ambil Foto Sesudah</span>
                            <small class="text-muted fs-11">Diisi saat selesai shift kerja</small>
                        </div>
                        <img id="img_sesudah_preview" src="" style="display: none;" alt="Preview Foto Sesudah">
                        <span id="ts_sesudah_tag" class="timestamp-tag" style="display: none;"></span>
                    </div>
                    <input type="file" name="foto_sesudah" id="foto_sesudah" accept="image/*" capture="environment" style="display: none;" onchange="handlePhotoSelect(this, 'img_sesudah_preview', 'preview_sesudah_placeholder', 'ts_sesudah_tag')">
                    
                    <div class="p-2 bg-light rounded-3 border">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="fs-11 fw-bold text-success"><i class="feather-navigation me-1"></i>GPS Akhir:</span>
                            <button type="button" class="btn btn-xs btn-outline-success rounded-pill px-2 py-0.5 fs-11" onclick="getGpsAkhir()">
                                <i class="feather-crosshair me-1"></i>Ulang GPS
                            </button>
                        </div>
                        <div class="row g-1">
                            <div class="col-6">
                                <input type="text" name="latitude_akhir" id="latitude_akhir" class="form-control form-control-sm bg-white" readonly placeholder="Lat Akhir" value="{{ old('latitude_akhir') }}">
                            </div>
                            <div class="col-6">
                                <input type="text" name="longitude_akhir" id="longitude_akhir" class="form-control form-control-sm bg-white" readonly placeholder="Long Akhir" value="{{ old('longitude_akhir') }}">
                            </div>
                        </div>
                        <small id="gps_akhir_status" class="fs-11 text-muted mt-1 d-block">Otomatis tersimpan saat foto diambil.</small>
                    </div>
                    @error('foto_sesudah') <small class="text-danger d-block mt-1 fs-11">{{ $message }}</small> @enderror
                </div>
            </div>

            {{-- Hour Meter & Jam Kerja --}}
            <div class="row g-2">
                <div class="col-6 mb-2">
                    <label class="form-label">Jam Awal Kerja <span class="text-danger">*</span></label>
                    <input type="text" name="hm_awal" id="hm_awal" 
                        class="form-control bg-light @error('hm_awal') is-invalid @enderror" 
                        value="{{ old('hm_awal') }}" placeholder="HH:MM (Dari Foto)" 
                        readonly oninput="hitungHm()">
                    <small id="msg_hm_awal" class="fs-11 d-block mt-1 text-muted">
                        <i class="feather-lock me-1 text-primary"></i>Otomatis dari foto sebelum.
                    </small>
                    @error('hm_awal') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-6 mb-2">
                    <label class="form-label">Jam Akhir Kerja</label>
                    <input type="text" name="hm_akhir" id="hm_akhir" 
                        class="form-control bg-light @error('hm_akhir') is-invalid @enderror" 
                        value="{{ old('hm_akhir') }}" placeholder="HH:MM (Dari Foto)" 
                        readonly oninput="hitungHm()">
                    <small id="msg_hm_akhir" class="fs-11 d-block mt-1 text-muted">
                        <i class="feather-lock me-1 text-success"></i>Otomatis dari foto sesudah.
                    </small>
                    @error('hm_akhir') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>

            <div class="p-3 bg-soft-success rounded-3 text-center border border-success-subtle mt-2">
                <div class="text-muted fs-12 fw-semibold">Total Durasi Jam Kerja (HM):</div>
                <div id="total_hm_display" class="fw-bolder fs-4 text-success my-1">00:00 Jam</div>
                <small class="text-muted fs-11">Dihitung otomatis dari selisih Jam Awal & Jam Akhir.</small>
            </div>
        </div>
    </div>

    <!-- Card 3: Hasil Aplikasi Bed (Jika Pekerjaan Aplikasi Lahan) -->
    <div class="op-card mb-3">
        <div class="op-card-header">
            <span class="d-flex align-items-center">
                <span class="step-badge">3</span>
                <span>Hasil Aplikasi Bed (Opsional)</span>
            </span>
        </div>
        <div class="op-card-body">
            <div class="row g-2 mb-2">
                <div class="col-6">
                    <label class="form-label">Flat Bed</label>
                    <input type="number" name="flat_bed" id="flat_bed" class="form-control @error('flat_bed') is-invalid @enderror" value="{{ old('flat_bed', 0) }}" min="0" oninput="calcBedTotal()">
                    @error('flat_bed') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-6">
                    <label class="form-label">Long Bed</label>
                    <input type="number" name="long_bed" id="long_bed" class="form-control @error('long_bed') is-invalid @enderror" value="{{ old('long_bed', 0) }}" min="0" oninput="calcBedTotal()">
                    @error('long_bed') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>

            <div class="p-2.5 bg-soft-primary rounded-3 text-center border border-primary-subtle">
                <span class="text-muted fs-12 fw-semibold">Total Bed Dikerjakan:</span>
                <span id="total_bed_display" class="fw-bold fs-5 text-primary ms-1">0</span> Bed
                <input type="hidden" name="jumlah_bed" id="jumlah_bed" value="{{ old('jumlah_bed', 0) }}">
            </div>
        </div>
    </div>

    <!-- Card 4: BBM & Kondisi Alat -->
    <div class="op-card mb-3">
        <div class="op-card-header">
            <span class="d-flex align-items-center">
                <span class="step-badge">4</span>
                <span>Konsumsi BBM & Kondisi Alat</span>
            </span>
        </div>
        <div class="op-card-body">
            <div class="mb-3">
                <label class="form-label">Pengisian BBM Solar (Liter)</label>
                <input type="number" step="0.1" name="bbm_liter" class="form-control @error('bbm_liter') is-invalid @enderror" value="{{ old('bbm_liter', 0) }}" placeholder="Contoh: 75.0" min="0">
                @error('bbm_liter') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="mb-3">
                <label class="form-label">Kondisi Alat Berat <span class="text-danger">*</span></label>
                <select name="kondisi_alat" class="form-select @error('kondisi_alat') is-invalid @enderror" required>
                    <option value="Normal" {{ old('kondisi_alat') == 'Normal' ? 'selected' : '' }}>Normal / Baik (Siap Kerja)</option>
                    <option value="Perlu Perbaikan" {{ old('kondisi_alat') == 'Perlu Perbaikan' ? 'selected' : '' }}>Perlu Perbaikan (Maintenance)</option>
                    <option value="Breakdown" {{ old('kondisi_alat') == 'Breakdown' ? 'selected' : '' }}>Breakdown (Rusak)</option>
                </select>
                @error('kondisi_alat') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="mb-0">
                <label class="form-label">Catatan Lapangan / Kendala Operasional</label>
                <textarea name="catatan" class="form-control" rows="2" placeholder="Tuliskan catatan kondisi lapangan, kendala teknis, atau komponen yang diperbaiki...">{{ old('catatan') }}</textarea>
            </div>
        </div>
    </div>

    <!-- Submit Button -->
    <div class="mb-4">
        <button type="submit" class="btn btn-op-primary shadow-lg py-3">
            <i class="feather-check-circle me-2 fs-5"></i>
            <span>SIMPAN LAPORAN KERJA ALAT BERAT</span>
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

    // --- Smart 3-Tier Offline Satellite GPS Engine ---
    function captureSmartLocation(onSuccess, statusElem) {
        if (!navigator.geolocation) {
            if (statusElem) statusElem.innerHTML = '<span class="text-danger"><i class="feather-alert-triangle me-1"></i>Browser ini tidak mendukung GPS.</span>';
            return;
        }

        if (statusElem) {
            statusElem.innerHTML = '<span class="text-primary"><span class="spinner-border spinner-border-sm me-1" style="width: 10px; height: 10px;"></span>Mengunci sinyal satelit GPS... Harap di area terbuka.</span>';
        }

        function handleSuccess(pos, source) {
            const lat = pos.coords.latitude.toFixed(8);
            const long = pos.coords.longitude.toFixed(8);
            const accuracy = Math.round(pos.coords.accuracy || 0);
            onSuccess(lat, long, accuracy);
            if (statusElem) {
                statusElem.innerHTML = `<span class="text-success fw-semibold"><i class="feather-check-circle me-1"></i>GPS Terkunci (${source})! Akurasi: ±${accuracy}m</span>`;
            }
        }

        function handleFinalError(err) {
            let msg = 'Gagal mengunci sinyal GPS.';
            if (err.code === 1) {
                msg = 'Izin lokasi (GPS) ditolak. Aktifkan izin lokasi pada browser/HP.';
            } else if (err.code === 2) {
                msg = 'Sinyal satelit belum terdeteksi. Pastikan tombol Lokasi/GPS di HP aktif.';
            } else if (err.code === 3) {
                msg = 'Waktu pencarian satelit habis (Timeout). Pastikan berada di bawah langit terbuka.';
            }
            if (statusElem) {
                statusElem.innerHTML = `<span class="text-danger fs-11"><i class="feather-alert-triangle me-1"></i>${msg}</span>`;
            }
        }

        navigator.geolocation.getCurrentPosition(
            (pos) => handleSuccess(pos, 'Cache GPS'),
            (err1) => {
                navigator.geolocation.getCurrentPosition(
                    (pos) => handleSuccess(pos, 'Satelit HP'),
                    (err2) => {
                        navigator.geolocation.getCurrentPosition(
                            (pos) => handleSuccess(pos, 'Perkiraan Perangkat'),
                            (err3) => handleFinalError(err3),
                            { enableHighAccuracy: false, timeout: 20000, maximumAge: 600000 }
                        );
                    },
                    { enableHighAccuracy: true, timeout: 25000, maximumAge: 120000 }
                );
            },
            { enableHighAccuracy: false, timeout: 4000, maximumAge: 300000 }
        );
    }

    function getGpsAwal() {
        const statusElem = document.getElementById('gps_awal_status');
        captureSmartLocation(function(lat, long, accuracy) {
            document.getElementById('latitude_awal').value = lat;
            document.getElementById('longitude_awal').value = long;
            document.getElementById('latitude').value = lat;
            document.getElementById('longitude').value = long;
        }, statusElem);
    }

    function getGpsAkhir() {
        const statusElem = document.getElementById('gps_akhir_status');
        captureSmartLocation(function(lat, long, accuracy) {
            document.getElementById('latitude_akhir').value = lat;
            document.getElementById('longitude_akhir').value = long;
        }, statusElem);
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

        // 1. Fallback: populate alat berat from IndexedDB if offline or empty
        const selectAlat = document.querySelector('select[name="alat_berat_id"]');
        if (selectAlat && selectAlat.options.length <= 1) {
            SimoliDB.getMasterAlatBerat().then((list) => {
                if (list && list.length > 0) {
                    list.forEach(ab => {
                        const opt = document.createElement('option');
                        opt.value = ab.id;
                        opt.textContent = `[${ab.kode_alat}] ${ab.nama_alat} (${ab.jenis_alat || 'Unit'})`;
                        selectAlat.appendChild(opt);
                    });
                }
            });
        }

        // 2. Intercept Form Submission for Offline Reliability
        const form = document.getElementById('formOperatorReport');
        if (form) {
            form.addEventListener('submit', async function(e) {
                e.preventDefault();
                
                const submitBtn = form.querySelector('button[type="submit"]');
                const originalBtnHtml = submitBtn ? submitBtn.innerHTML : 'Simpan Laporan Kerja';
                if (submitBtn) {
                    submitBtn.disabled = true;
                    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Menyimpan ke Memori HP...';
                }

                try {
                    const report = await SimoliSync.saveReportFromForm(form);
                    
                    if (navigator.onLine) {
                        if (window.showToastNotification) {
                            showToastNotification('✓ Laporan berhasil disimpan & disinkronkan!', 'success');
                        }
                    } else {
                        if (window.showToastNotification) {
                            showToastNotification('✓ Laporan berhasil disimpan di HP (Mode Offline Lapangan). Otomatis disinkronkan saat ada sinyal.', 'warning');
                        }
                    }
                    
                    setTimeout(() => {
                        window.location.href = "{{ route('operator.index') }}";
                    }, 1000);
                } catch (err) {
                    console.error('[SIMOLI] Offline save error:', err);
                    alert('Gagal menyimpan laporan: ' + err.message);
                    if (submitBtn) {
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = originalBtnHtml;
                    }
                }
            });
        }
    });
</script>
@endsection
