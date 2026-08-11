@extends('layouts.simoli')

@section('title', 'Tambah Log Monitoring Alat Berat')
@section('page-title', 'Tambah Log Monitoring Alat Berat')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('monitoring-alat-berat.index') }}">Monitoring Alat Berat</a></li>
<li class="breadcrumb-item active">Tambah Log</li>
@endsection

@section('page-actions')
<a href="{{ route('monitoring-alat-berat.index') }}" class="btn btn-light">
    <i class="feather-arrow-left me-1"></i> Kembali
</a>
@endsection

@section('content')
<div class="row">
            <div class="col-lg-9 col-12">
                <div class="card stretch stretch-full">
                    <div class="card-header">
                        <h5 class="card-title">Form Input Operasional Alat Berat</h5>
                    </div>
                    <div class="card-body">
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form action="{{ route('monitoring-alat-berat.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf

                            <div class="row">
                                @if($user->isAdmin())
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">PKS Unit <span class="text-danger">*</span></label>
                                        <select name="id_pks" id="id_pks_select" class="form-select @error('id_pks') is-invalid @enderror" required>
                                            <option value="">-- Pilih PKS --</option>
                                            @foreach($pksList as $pks)
                                                <option value="{{ $pks->id_pks }}" {{ old('id_pks') == $pks->id_pks ? 'selected' : '' }}>
                                                    {{ $pks->nama }} ({{ $pks->akro }})
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('id_pks') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                @endif

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Pilih Alat Berat <span class="text-danger">*</span></label>
                                    <select name="alat_berat_id" class="form-select @error('alat_berat_id') is-invalid @enderror" required>
                                        <option value="">-- Pilih Unit Alat Berat --</option>
                                        @foreach($alatBeratList as $ab)
                                            <option value="{{ $ab->id }}" {{ old('alat_berat_id') == $ab->id ? 'selected' : '' }}>
                                                [{{ $ab->kode_alat }}] {{ $ab->nama_alat }} ({{ $ab->jenis_alat }}){{ $ab->status === 'Standby' ? ' - [Standby / Cadangan]' : '' }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('alat_berat_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Tanggal Operasional <span class="text-danger">*</span></label>
                                    <input type="date" name="tanggal" class="form-control @error('tanggal') is-invalid @enderror" value="{{ old('tanggal', date('Y-m-d')) }}" required>
                                    @error('tanggal') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Nama Operator / Penanggung Jawab <span class="text-danger">*</span></label>
                                    <input type="text" name="operator" class="form-control @error('operator') is-invalid @enderror" placeholder="Nama Operator Alat Berat" value="{{ old('operator') }}" required>
                                    @error('operator') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-5 mb-3">
                                    <label class="form-label">Jenis Kegiatan Pengolahan Limbah <span class="text-danger">*</span></label>
                                    <select name="kegiatan" class="form-select @error('kegiatan') is-invalid @enderror" required>
                                        <option value="">-- Pilih Jenis Kegiatan --</option>
                                        <option value="Pembersihan & Pengerukan Kolam Limbah" {{ old('kegiatan') == 'Pembersihan & Pengerukan Kolam Limbah' ? 'selected' : '' }}>Pembersihan & Pengerukan Kolam Limbah</option>
                                        <option value="Aplikasi Lahan (Land Application)" {{ old('kegiatan') == 'Aplikasi Lahan (Land Application)' ? 'selected' : '' }}>Aplikasi Lahan (Land Application)</option>
                                        <option value="Pengadukan Kolam Limbah / Anaerob" {{ old('kegiatan') == 'Pengadukan Kolam Limbah / Anaerob' ? 'selected' : '' }}>Pengadukan Kolam Limbah / Anaerob</option>
                                        <option value="Transportasi & Loading Sludge / Solid" {{ old('kegiatan') == 'Transportasi & Loading Sludge / Solid' ? 'selected' : '' }}>Transportasi & Loading Sludge / Solid</option>
                                        <option value="Perbaikan Pematang / Tanggul Kolam" {{ old('kegiatan') == 'Perbaikan Pematang / Tanggul Kolam' ? 'selected' : '' }}>Perbaikan Pematang / Tanggul Kolam</option>
                                        <option value="Pemeliharaan Routine Alat Berat" {{ old('kegiatan') == 'Pemeliharaan Routine Alat Berat' ? 'selected' : '' }}>Pemeliharaan Routine Alat Berat</option>
                                        <option value="Kegiatan Pengolahan Lainnya" {{ old('kegiatan') == 'Kegiatan Pengolahan Lainnya' ? 'selected' : '' }}>Kegiatan Pengolahan Lainnya</option>
                                    </select>
                                    @error('kegiatan') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Lokasi / Kolam / Blok Kerja</label>
                                    <input type="text" name="lokasi_blok" class="form-control" placeholder="Contoh: Kolam 3 / Blok C18" value="{{ old('lokasi_blok') }}">
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Jumlah Flat Bed Dikerjakan</label>
                                    <div class="input-group">
                                        <input type="number" name="flat_bed" min="0" class="form-control @error('flat_bed') is-invalid @enderror" placeholder="0" value="{{ old('flat_bed', 0) }}">
                                        <span class="input-group-text">Flat Bed</span>
                                    </div>
                                    @error('flat_bed') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Jumlah Long Bed Dikerjakan</label>
                                    <div class="input-group">
                                        <input type="number" name="long_bed" min="0" class="form-control @error('long_bed') is-invalid @enderror" placeholder="0" value="{{ old('long_bed', 0) }}">
                                        <span class="input-group-text">Long Bed</span>
                                    </div>
                                    @error('long_bed') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>

                            <hr class="my-4">
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <h6 class="fw-bold text-primary mb-0"><i class="feather-map-pin me-2"></i>Titik Koordinat Lokasi Operasional (GPS)</h6>
                                <button type="button" id="btn-get-location" class="btn btn-sm btn-primary">
                                    <i class="feather-crosshair me-1"></i> Ambil Lokasi Saat Ini (GPS)
                                </button>
                            </div>
                            <div id="location-status" class="small text-muted mb-2" style="display: none;"></div>
                            <div class="row mb-3">
                                <div class="col-md-6 mb-3 mb-md-0">
                                    <label class="form-label">Latitude (Garintang)</label>
                                    <input type="text" name="latitude" id="latitude" class="form-control @error('latitude') is-invalid @enderror" placeholder="-0.12345678" value="{{ old('latitude') }}">
                                    @error('latitude') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Longitude (Garis Bujur)</label>
                                    <input type="text" name="longitude" id="longitude" class="form-control @error('longitude') is-invalid @enderror" placeholder="101.12345678" value="{{ old('longitude') }}">
                                    @error('longitude') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>

                            <hr class="my-4">
                            <h6 class="fw-bold text-primary mb-3"><i class="feather-clock me-2"></i>Catatan Jam Kerja (Hour Meter / HM) & BBM</h6>

                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Hour Meter (HM) Awal</label>
                                    <input type="text" name="hm_awal" id="hm_awal" class="form-control @error('hm_awal') is-invalid @enderror" placeholder="08:00 (Otomatis dari Foto Sebelum)" value="{{ old('hm_awal') }}">
                                    <small class="text-muted fs-11">Otomatis terisi dari foto sebelum kerja atau ketik manual (HH:MM)</small>
                                    @error('hm_awal') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Hour Meter (HM) Akhir</label>
                                    <input type="text" name="hm_akhir" id="hm_akhir" class="form-control @error('hm_akhir') is-invalid @enderror" placeholder="16:30 (Otomatis dari Foto Sesudah)" value="{{ old('hm_akhir') }}">
                                    <small class="text-muted fs-11">Otomatis terisi dari foto sesudah kerja atau ketik manual (HH:MM)</small>
                                    @error('hm_akhir') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Total Jam Kerja (Otomatis)</label>
                                    <input type="text" id="total_hm_display" class="form-control bg-light fw-bold text-primary" value="00:00 Jam" readonly>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Konsumsi BBM (Liter Solar)</label>
                                    <div class="input-group">
                                        <input type="number" step="0.1" name="bbm_liter" class="form-control" placeholder="0" value="{{ old('bbm_liter', 0) }}">
                                        <span class="input-group-text">Liter</span>
                                    </div>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Kondisi Alat Saat Pengolahan <span class="text-danger">*</span></label>
                                    <select name="kondisi_alat" class="form-select @error('kondisi_alat') is-invalid @enderror" required>
                                        <option value="Normal" {{ old('kondisi_alat') == 'Normal' ? 'selected' : '' }}>Normal / Baik</option>
                                        <option value="Perlu Perbaikan" {{ old('kondisi_alat') == 'Perlu Perbaikan' ? 'selected' : '' }}>Ada Kendala / Perlu Perbaikan</option>
                                        <option value="Breakdown" {{ old('kondisi_alat') == 'Breakdown' ? 'selected' : '' }}>Breakdown / Rusak</option>
                                    </select>
                                    @error('kondisi_alat') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>

                            <hr class="my-4">
                            <h6 class="fw-bold text-primary mb-3"><i class="feather-image me-2"></i>Foto Laporan Sebelum & Sesudah Jam Kerja</h6>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-semibold">Foto Sebelum Jam Kerja (Awal Shift)</label>
                                    <input type="file" name="foto_sebelum" id="foto_sebelum" class="form-control @error('foto_sebelum') is-invalid @enderror" accept="image/*">
                                    <small class="text-muted fs-11 d-block mt-1"><i class="feather-clock me-1 text-primary"></i>Jam Awal Kerja (HM Awal) otomatis diisi saat foto ini dipilih/diupload.</small>
                                    @error('foto_sebelum') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-semibold">Foto Sesudah Jam Kerja (Akhir Shift)</label>
                                    <input type="file" name="foto_sesudah" id="foto_sesudah" class="form-control @error('foto_sesudah') is-invalid @enderror" accept="image/*">
                                    <small class="text-muted fs-11 d-block mt-1"><i class="feather-clock me-1 text-primary"></i>Jam Selesai Kerja (HM Akhir) otomatis diisi saat foto ini dipilih/diupload.</small>
                                    @error('foto_sesudah') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Catatan Operasional / Kendala Lapangan</label>
                                <textarea name="catatan" class="form-control" rows="3" placeholder="Catatan pekerjaan atau kendala yang dihadapi saat pengolahan limbah...">{{ old('catatan') }}</textarea>
                            </div>

                            <div class="d-flex justify-content-between mt-4">
                                <a href="{{ route('monitoring-alat-berat.index') }}" class="btn btn-light">Batal</a>
                                <button type="submit" class="btn btn-primary"><i class="feather-save me-2"></i>Simpan Log Monitoring</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const hmAwalInput = document.getElementById('hm_awal');
        const hmAkhirInput = document.getElementById('hm_akhir');
        const totalHmDisplay = document.getElementById('total_hm_display');
        const btnGetLocation = document.getElementById('btn-get-location');
        const latInput = document.getElementById('latitude');
        const lngInput = document.getElementById('longitude');
        const locStatus = document.getElementById('location-status');
        const fotoSebelumInput = document.getElementById('foto_sebelum');
        const fotoSesudahInput = document.getElementById('foto_sesudah');

        function formatTimeToHHMM(dateObj) {
            const h = dateObj.getHours();
            const m = dateObj.getMinutes();
            const hStr = h < 10 ? '0' + h : h;
            const mStr = m < 10 ? '0' + m : m;
            return `${hStr}:${mStr}`;
        }

        if (fotoSebelumInput) {
            fotoSebelumInput.addEventListener('change', function(e) {
                if (e.target.files && e.target.files[0]) {
                    const now = new Date();
                    hmAwalInput.value = formatTimeToHHMM(now);
                    hmAwalInput.readOnly = true;
                    hmAwalInput.classList.add('bg-light');
                    hitungHm();
                }
            });
        }

        if (fotoSesudahInput) {
            fotoSesudahInput.addEventListener('change', function(e) {
                if (e.target.files && e.target.files[0]) {
                    const now = new Date();
                    hmAkhirInput.value = formatTimeToHHMM(now);
                    hmAkhirInput.readOnly = true;
                    hmAkhirInput.classList.add('bg-light');
                    hitungHm();
                }
            });
        }

        if (btnGetLocation) {
            btnGetLocation.addEventListener('click', function() {
                if (!navigator.geolocation) {
                    alert('Geolocation tidak didukung oleh browser Anda.');
                    return;
                }
                btnGetLocation.disabled = true;
                btnGetLocation.innerHTML = '<i class="feather-loader spinner-border spinner-border-sm me-1"></i> Mengambil Lokasi...';
                locStatus.style.display = 'block';
                locStatus.className = 'small text-info mb-2';
                locStatus.innerText = 'Mengakses GPS perangkat...';

                navigator.geolocation.getCurrentPosition(
                    function(position) {
                        latInput.value = position.coords.latitude.toFixed(8);
                        lngInput.value = position.coords.longitude.toFixed(8);
                        btnGetLocation.disabled = false;
                        btnGetLocation.innerHTML = '<i class="feather-check me-1"></i> Lokasi Berhasil Diambil';
                        locStatus.className = 'small text-success mb-2';
                        locStatus.innerText = `Koordinat didapatkan (Akurasi: ±${Math.round(position.coords.accuracy)}m)`;
                    },
                    function(error) {
                        btnGetLocation.disabled = false;
                        btnGetLocation.innerHTML = '<i class="feather-crosshair me-1"></i> Coba Lagi';
                        locStatus.className = 'small text-danger mb-2';
                        let msg = 'Gagal mengambil lokasi.';
                        if (error.code === error.PERMISSION_DENIED) {
                            msg = 'Izin lokasi ditolak oleh pengguna.';
                        } else if (error.code === error.POSITION_UNAVAILABLE) {
                            msg = 'Informasi lokasi tidak tersedia.';
                        } else if (error.code === error.TIMEOUT) {
                            msg = 'Waktu permintaan lokasi habis.';
                        }
                        locStatus.innerText = msg;
                    },
                    { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 }
                );
            });
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
            if (!hmAwalInput.value || !hmAkhirInput.value) {
                totalHmDisplay.value = '00:00 Jam';
                return;
            }
            const awalMins = parseTimeToMinutes(hmAwalInput.value);
            const akhirMins = parseTimeToMinutes(hmAkhirInput.value);
            let diff = akhirMins - awalMins;
            if (diff < 0 && awalMins > 0 && akhirMins > 0 && (hmAwalInput.value.includes(':') || hmAwalInput.value.includes('.'))) {
                diff += 24 * 60;
            }
            const finalDiff = Math.max(0, diff);
            totalHmDisplay.value = minutesToTime(finalDiff);
        }

        hmAwalInput.addEventListener('input', hitungHm);
        hmAkhirInput.addEventListener('input', hitungHm);
        hitungHm();
    });
</script>
@endsection
