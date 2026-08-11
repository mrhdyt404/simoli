@extends('layouts.operator')

@section('title', 'Edit Laporan Kerja - SIMOLII Operator')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-3">
    <h5 class="fw-bold m-0"><i class="feather-edit-2 me-2 text-warning"></i>Edit Laporan Kerja</h5>
    <a href="{{ route('operator.show', $log->id) }}" class="btn btn-sm btn-outline-secondary rounded-pill">
        <i class="feather-arrow-left me-1"></i>Batal
    </a>
</div>

<form action="{{ route('operator.update', $log->id) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')

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
                        <option value="{{ $ab->id }}" {{ old('alat_berat_id', $log->alat_berat_id) == $ab->id ? 'selected' : '' }}>
                            [{{ $ab->kode_alat }}] {{ $ab->nama_alat }} ({{ $ab->jenis_alat }}){{ $ab->status !== 'Operational' ? ' - [' . $ab->status . ']' : '' }}
                        </option>
                    @endforeach
                </select>
                @error('alat_berat_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

                <div class="col-6 mb-3">
                    <label class="form-label">Tanggal Laporan <span class="text-danger">*</span></label>
                    <input type="date" class="form-control bg-light @error('tanggal') is-invalid @enderror" value="{{ old('tanggal', is_string($log->tanggal) ? $log->tanggal : $log->tanggal->format('Y-m-d')) }}" readonly>
                    <input type="hidden" name="tanggal" value="{{ old('tanggal', is_string($log->tanggal) ? $log->tanggal : $log->tanggal->format('Y-m-d')) }}">
                    <small class="text-danger fs-11 mt-1 d-block"><i class="feather-lock me-1"></i>Tanggal tidak dapat diubah.</small>
                    @error('tanggal') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-6 mb-3">
                    <label class="form-label">Operator <span class="text-danger">*</span></label>
                    <input type="text" name="operator" class="form-control @error('operator') is-invalid @enderror" value="{{ old('operator', $log->operator) }}" required>
                    @error('operator') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Jenis Kegiatan / Pekerjaan <span class="text-danger">*</span></label>
                <input type="text" name="kegiatan" class="form-control @error('kegiatan') is-invalid @enderror" value="{{ old('kegiatan', $log->kegiatan) }}" required>
                @error('kegiatan') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="mb-0">
                <label class="form-label">Lokasi / Blok Pekerjaan</label>
                <input type="text" name="lokasi_blok" class="form-control @error('lokasi_blok') is-invalid @enderror" value="{{ old('lokasi_blok', $log->lokasi_blok) }}">
                @error('lokasi_blok') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
        </div>
    </div>

    <!-- Card 2: Hasil Aplikasi Bed -->
    <div class="op-card mb-3">
        <div class="op-card-header">
            <span><i class="feather-grid me-2 text-primary"></i>2. Hasil Aplikasi Bed</span>
        </div>
        <div class="op-card-body">
            <div class="row">
                <div class="col-6 mb-3">
                    <label class="form-label">Flat Bed</label>
                    <input type="number" name="flat_bed" id="flat_bed" class="form-control @error('flat_bed') is-invalid @enderror" value="{{ old('flat_bed', $log->flat_bed) }}" min="0" oninput="calcBedTotal()">
                    @error('flat_bed') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-6 mb-3">
                    <label class="form-label">Long Bed</label>
                    <input type="number" name="long_bed" id="long_bed" class="form-control @error('long_bed') is-invalid @enderror" value="{{ old('long_bed', $log->long_bed) }}" min="0" oninput="calcBedTotal()">
                    @error('long_bed') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>

            <div class="p-2 bg-light rounded-3 text-center border">
                <span class="text-muted fs-12 fw-semibold">Total Bed Dikerjakan:</span>
                <span id="total_bed_display" class="fw-bold fs-5 text-primary ms-2">{{ $log->jumlah_bed }}</span> Bed
                <input type="hidden" name="jumlah_bed" id="jumlah_bed" value="{{ old('jumlah_bed', $log->jumlah_bed) }}">
            </div>
        </div>
    </div>

    <!-- Card 3: Hour Meter (HM) & Jam Kerja -->
    <div class="op-card mb-3">
        <div class="op-card-header">
            <span><i class="feather-clock me-2 text-primary"></i>3. Hour Meter (HM) & Jam Kerja</span>
        </div>
        <div class="op-card-body">
            <div class="row">
                <div class="col-6 mb-3">
                    <label class="form-label">HM / Jam Awal Kerja</label>
                    @php 
                        $isAwalLocked = !empty($log->hm_awal) || !empty($log->foto_sebelum) || !empty(old('hm_awal'));
                    @endphp
                    <input type="text" name="hm_awal" id="hm_awal" 
                        class="form-control @if($isAwalLocked) bg-light @endif @error('hm_awal') is-invalid @enderror" 
                        value="{{ old('hm_awal', $log->hm_awal_formatted != '-' ? $log->hm_awal_formatted : '') }}" 
                        placeholder="HH:MM (e.g. 08:00)" 
                        @if($isAwalLocked) readonly @endif oninput="hitungHm()" onblur="lockIfFilled(this, 'msg_hm_awal', 'HM Awal')">
                    <small id="msg_hm_awal" class="fs-11 d-block mt-1 @if($isAwalLocked) text-danger @else text-muted @endif">
                        @if($isAwalLocked)
                            <i class="feather-lock me-1"></i>HM Awal terkunci (foto/data terisi) untuk mencegah rekayasa.
                        @else
                            <i class="feather-info text-primary me-1"></i>Otomatis terisi & terkunci saat Foto Sebelum di-upload.
                        @endif
                    </small>
                    @error('hm_awal') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-6 mb-3">
                    <label class="form-label">HM / Jam Akhir Kerja</label>
                    @php 
                        $isAkhirLocked = !empty($log->hm_akhir) || !empty($log->foto_sesudah) || !empty(old('hm_akhir'));
                    @endphp
                    <input type="text" name="hm_akhir" id="hm_akhir" 
                        class="form-control @if($isAkhirLocked) bg-light @endif @error('hm_akhir') is-invalid @enderror" 
                        value="{{ old('hm_akhir', $log->hm_akhir_formatted != '-' ? $log->hm_akhir_formatted : '') }}" 
                        placeholder="HH:MM (e.g. 15:30)" 
                        @if($isAkhirLocked) readonly @endif oninput="hitungHm()" onblur="lockIfFilled(this, 'msg_hm_akhir', 'HM Akhir')">
                    <small id="msg_hm_akhir" class="fs-11 d-block mt-1 @if($isAkhirLocked) text-danger @else text-muted @endif">
                        @if($isAkhirLocked)
                            <i class="feather-lock me-1"></i>HM Akhir terkunci (foto/data terisi) untuk mencegah rekayasa.
                        @else
                            <i class="feather-info text-primary me-1"></i>Otomatis terisi & terkunci saat Foto Sesudah di-upload.
                        @endif
                    </small>
                    @error('hm_akhir') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>

            <div class="p-2 bg-light rounded-3 text-center border">
                <span class="text-muted fs-12 fw-semibold">Total Jam Kerja (HM):</span>
                <span id="total_hm_display" class="fw-bold fs-5 text-success ms-2">{{ \App\Models\MonitoringAlatBerat::formatHm($log->total_hm, true) }}</span>
            </div>
        </div>
    </div>

    <!-- Card 4: BBM & Kondisi Alat -->
    <div class="op-card mb-3">
        <div class="op-card-header">
            <span><i class="feather-check-square me-2 text-primary"></i>4. Konsumsi BBM & Kondisi Alat</span>
        </div>
        <div class="op-card-body">
            <div class="mb-3">
                <label class="form-label">Pengisian BBM (Liter)</label>
                <input type="number" step="0.1" name="bbm_liter" class="form-control @error('bbm_liter') is-invalid @enderror" value="{{ old('bbm_liter', $log->bbm_liter) }}" min="0">
                @error('bbm_liter') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="mb-3">
                <label class="form-label">Kondisi Alat Berat <span class="text-danger">*</span></label>
                <select name="kondisi_alat" class="form-select @error('kondisi_alat') is-invalid @enderror" required>
                    <option value="Normal" {{ old('kondisi_alat', $log->kondisi_alat) == 'Normal' ? 'selected' : '' }}>Normal / Baik</option>
                    <option value="Perlu Perbaikan" {{ old('kondisi_alat', $log->kondisi_alat) == 'Perlu Perbaikan' ? 'selected' : '' }}>Perlu Perbaikan (Maintenance)</option>
                    <option value="Breakdown" {{ old('kondisi_alat', $log->kondisi_alat) == 'Breakdown' ? 'selected' : '' }}>Breakdown (Rusak)</option>
                </select>
                @error('kondisi_alat') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="mb-0">
                <label class="form-label">Catatan / Kendala Operasional</label>
                <textarea name="catatan" class="form-control" rows="2">{{ old('catatan', $log->catatan) }}</textarea>
            </div>
        </div>
    </div>

    <!-- Card 5: Dokumentasi Foto -->
    <div class="op-card mb-3">
        <div class="op-card-header">
            <span><i class="feather-camera me-2 text-primary"></i>5. Ganti Foto Dokumentasi</span>
        </div>
        <div class="op-card-body">
            <div class="row g-3">
                <div class="col-6">
                    <label class="form-label text-center d-block">Sebelum Kerja</label>
                    <div class="photo-preview-box" onclick="document.getElementById('foto_sebelum').click()">
                        @if($log->foto_sebelum && file_exists(public_path('uploads/monitoring_alat_berat/' . $log->foto_sebelum)))
                            <img id="img_sebelum_preview" src="{{ asset('uploads/monitoring_alat_berat/' . $log->foto_sebelum) }}">
                            <div id="preview_sebelum_placeholder" style="display: none;"></div>
                        @else
                            <div id="preview_sebelum_placeholder" class="text-center p-2">
                                <i class="feather-camera fs-3 text-muted d-block mb-1"></i>
                                <span class="fs-12 text-muted fw-semibold">Pilih Foto</span>
                            </div>
                            <img id="img_sebelum_preview" src="" style="display: none;">
                        @endif
                        <span id="ts_sebelum_tag" class="timestamp-tag" style="display: none;"></span>
                    </div>
                    <input type="file" name="foto_sebelum" id="foto_sebelum" accept="image/*" style="display: none;" onchange="handlePhotoSelect(this, 'img_sebelum_preview', 'preview_sebelum_placeholder', 'ts_sebelum_tag')">
                </div>

                <div class="col-6">
                    <label class="form-label text-center d-block">Sesudah Kerja</label>
                    <div class="photo-preview-box" onclick="document.getElementById('foto_sesudah').click()">
                        @if($log->foto_sesudah && file_exists(public_path('uploads/monitoring_alat_berat/' . $log->foto_sesudah)))
                            <img id="img_sesudah_preview" src="{{ asset('uploads/monitoring_alat_berat/' . $log->foto_sesudah) }}">
                            <div id="preview_sesudah_placeholder" style="display: none;"></div>
                        @else
                            <div id="preview_sesudah_placeholder" class="text-center p-2">
                                <i class="feather-camera fs-3 text-muted d-block mb-1"></i>
                                <span class="fs-12 text-muted fw-semibold">Pilih Foto</span>
                            </div>
                            <img id="img_sesudah_preview" src="" style="display: none;">
                        @endif
                        <span id="ts_sesudah_tag" class="timestamp-tag" style="display: none;"></span>
                    </div>
                    <input type="file" name="foto_sesudah" id="foto_sesudah" accept="image/*" style="display: none;" onchange="handlePhotoSelect(this, 'img_sesudah_preview', 'preview_sesudah_placeholder', 'ts_sesudah_tag')">
                </div>
            </div>
        </div>
    </div>

    <!-- Submit Button -->
    <div class="mb-4">
        <button type="submit" class="btn btn-warning btn-op-primary text-dark shadow-lg py-3">
            <i class="feather-save me-2"></i>SIMPAN PERUBAHAN LAPORAN
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

    function lockInput(inputElem, msgId, labelName) {
        if (!inputElem) return;
        inputElem.readOnly = true;
        inputElem.classList.add('bg-light');
        const msgElem = document.getElementById(msgId);
        if (msgElem) {
            msgElem.className = 'fs-11 d-block mt-1 text-danger';
            msgElem.innerHTML = `<i class="feather-lock me-1"></i>${labelName || 'HM'} terkunci untuk mencegah rekayasa.`;
        }
    }

    function lockIfFilled(inputElem, msgId, labelName) {
        if (inputElem && inputElem.value.trim() !== '') {
            lockInput(inputElem, msgId, labelName);
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
                    lockInput(hmAwal, 'msg_hm_awal', 'HM Awal');
                    hitungHm();
                } else if (input.id === 'foto_sesudah') {
                    const hmAkhir = document.getElementById('hm_akhir');
                    hmAkhir.value = timeHHMM;
                    lockInput(hmAkhir, 'msg_hm_akhir', 'HM Akhir');
                    hitungHm();
                }
            }

            reader.readAsDataURL(file);
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        calcBedTotal();
        hitungHm();
        const hmAwal = document.getElementById('hm_awal');
        const hmAkhir = document.getElementById('hm_akhir');
        if (hmAwal && hmAwal.value.trim() !== '') {
            lockInput(hmAwal, 'msg_hm_awal', 'HM Awal');
        }
        if (hmAkhir && hmAkhir.value.trim() !== '') {
            lockInput(hmAkhir, 'msg_hm_akhir', 'HM Akhir');
        }
    });
</script>
@endsection
