@extends('layouts.simoli')

@section('title', 'Edit Log Monitoring Alat Berat')
@section('page-title', 'Edit Log Monitoring Alat Berat')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('monitoring-alat-berat.index') }}">Monitoring Alat Berat</a></li>
<li class="breadcrumb-item active">Edit Log</li>
@endsection

@section('page-actions')
<a href="{{ route('monitoring-alat-berat.index') }}" class="btn btn-light">
    <i class="feather-arrow-left me-1"></i> Kembali
</a>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-10 col-12">
        <div class="card stretch stretch-full">
            <div class="card-header">
                <h5 class="card-title"><i class="feather-edit-2 me-2 text-warning"></i>Edit Log Operasional Alat Berat</h5>
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

                <form action="{{ route('monitoring-alat-berat.update', $log->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <!-- 1. Informasi Unit & Pekerjaan -->
                    <h6 class="fw-bold text-primary mb-3"><i class="feather-truck me-2"></i>1. Informasi Unit & Pekerjaan</h6>
                    <div class="row">
                        @if($user->isAdmin())
                            <div class="col-md-6 mb-3">
                                <label class="form-label">PKS Unit <span class="text-danger">*</span></label>
                                <select name="id_pks" class="form-select @error('id_pks') is-invalid @enderror" required>
                                    <option value="">-- Pilih PKS --</option>
                                    @foreach($pksList as $pks)
                                        <option value="{{ $pks->id_pks }}" {{ old('id_pks', $log->id_pks) == $pks->id_pks ? 'selected' : '' }}>
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
                                    <option value="{{ $ab->id }}" {{ old('alat_berat_id', $log->alat_berat_id) == $ab->id ? 'selected' : '' }}>
                                        [{{ $ab->kode_alat }}] {{ $ab->nama_alat }} ({{ $ab->jenis_alat }}){{ $ab->status !== 'Operational' ? ' - [' . $ab->status . ']' : '' }}
                                    </option>
                                @endforeach
                            </select>
                            @error('alat_berat_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Tanggal Operasional <span class="text-danger">*</span></label>
                            <input type="date" class="form-control bg-light @error('tanggal') is-invalid @enderror" value="{{ old('tanggal', is_string($log->tanggal) ? $log->tanggal : $log->tanggal->format('Y-m-d')) }}" readonly>
                            <input type="hidden" name="tanggal" value="{{ old('tanggal', is_string($log->tanggal) ? $log->tanggal : $log->tanggal->format('Y-m-d')) }}">
                            <small class="text-danger fs-11 mt-1 d-block"><i class="feather-lock me-1"></i>Tanggal operasional terkunci & tidak dapat diubah setelah dibuat.</small>
                            @error('tanggal') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nama Operator <span class="text-danger">*</span></label>
                            <input type="text" name="operator" class="form-control @error('operator') is-invalid @enderror" value="{{ old('operator', $log->operator) }}" required>
                            @error('operator') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Jenis Kegiatan Pengolahan Limbah <span class="text-danger">*</span></label>
                            <select name="kegiatan" class="form-select @error('kegiatan') is-invalid @enderror" required>
                                <option value="Pembersihan & Pengerukan Kolam Limbah" {{ old('kegiatan', $log->kegiatan) == 'Pembersihan & Pengerukan Kolam Limbah' ? 'selected' : '' }}>Pembersihan & Pengerukan Kolam Limbah</option>
                                <option value="Aplikasi Lahan (Land Application)" {{ old('kegiatan', $log->kegiatan) == 'Aplikasi Lahan (Land Application)' ? 'selected' : '' }}>Aplikasi Lahan (Land Application)</option>
                                <option value="Pengadukan Kolam Limbah / Anaerob" {{ old('kegiatan', $log->kegiatan) == 'Pengadukan Kolam Limbah / Anaerob' ? 'selected' : '' }}>Pengadukan Kolam Limbah / Anaerob</option>
                                <option value="Transportasi & Loading Sludge / Solid" {{ old('kegiatan', $log->kegiatan) == 'Transportasi & Loading Sludge / Solid' ? 'selected' : '' }}>Transportasi & Loading Sludge / Solid</option>
                                <option value="Perbaikan Pematang / Tanggul Kolam" {{ old('kegiatan', $log->kegiatan) == 'Perbaikan Pematang / Tanggul Kolam' ? 'selected' : '' }}>Perbaikan Pematang / Tanggul Kolam</option>
                                <option value="Pemeliharaan Routine Alat Berat" {{ old('kegiatan', $log->kegiatan) == 'Pemeliharaan Routine Alat Berat' ? 'selected' : '' }}>Pemeliharaan Routine Alat Berat</option>
                                <option value="Kegiatan Pengolahan Lainnya" {{ old('kegiatan', $log->kegiatan) == 'Kegiatan Pengolahan Lainnya' ? 'selected' : '' }}>Kegiatan Pengolahan Lainnya</option>
                            </select>
                            @error('kegiatan') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Lokasi / Kolam / Blok Kerja</label>
                            <input type="text" name="lokasi_blok" class="form-control" value="{{ old('lokasi_blok', $log->lokasi_blok) }}">
                        </div>
                    </div>

                    <hr class="my-4">
                    <!-- 2. Titik Koordinat GPS Kerja (Koordinat Awal & Akhir) - POSISI DI ATAS -->
                    <h6 class="fw-bold text-primary mb-3"><i class="feather-map-pin me-2"></i>2. Titik Koordinat Lokasi Kerja (GPS Awal & Akhir)</h6>
                    
                    <div class="row g-3 mb-3">
                        <!-- GPS Awal -->
                        <div class="col-md-6 border-end-md">
                            <div class="p-3 bg-light rounded border">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <label class="form-label fw-bold mb-0 text-primary fs-12"><i class="feather-navigation me-1"></i>Koordinat Awal Kerja</label>
                                    <button type="button" class="btn btn-xs btn-outline-primary rounded-pill px-2 py-1 fs-11" onclick="getGpsAwal()">
                                        <i class="feather-crosshair me-1"></i>Ambil GPS Awal
                                    </button>
                                </div>
                                <div class="row g-2">
                                    <div class="col-6">
                                        <input type="text" name="latitude_awal" id="latitude_awal" class="form-control form-control-sm bg-white" readonly placeholder="Lat Awal" value="{{ old('latitude_awal', $log->latitude_awal ?? $log->latitude) }}">
                                        <input type="hidden" name="latitude" id="latitude" value="{{ old('latitude', $log->latitude) }}">
                                    </div>
                                    <div class="col-6">
                                        <input type="text" name="longitude_awal" id="longitude_awal" class="form-control form-control-sm bg-white" readonly placeholder="Long Awal" value="{{ old('longitude_awal', $log->longitude_awal ?? $log->longitude) }}">
                                        <input type="hidden" name="longitude" id="longitude" value="{{ old('longitude', $log->longitude) }}">
                                    </div>
                                </div>
                                <small id="gps_awal_status" class="fs-11 text-muted mt-1 d-block">
                                    @if($log->latitude_awal || $log->latitude)
                                        <span class="text-success"><i class="feather-check-circle me-1"></i>GPS Awal terekam</span>
                                    @else
                                        Klik tombol untuk mengambil GPS Awal.
                                    @endif
                                </small>
                            </div>
                        </div>

                        <!-- GPS Akhir -->
                        <div class="col-md-6">
                            <div class="p-3 bg-light rounded border">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <label class="form-label fw-bold mb-0 text-success fs-12"><i class="feather-navigation me-1"></i>Koordinat Akhir Kerja</label>
                                    <button type="button" class="btn btn-xs btn-outline-success rounded-pill px-2 py-1 fs-11" onclick="getGpsAkhir()">
                                        <i class="feather-crosshair me-1"></i>Ambil GPS Akhir
                                    </button>
                                </div>
                                <div class="row g-2">
                                    <div class="col-6">
                                        <input type="text" name="latitude_akhir" id="latitude_akhir" class="form-control form-control-sm bg-white" readonly placeholder="Lat Akhir" value="{{ old('latitude_akhir', $log->latitude_akhir) }}">
                                    </div>
                                    <div class="col-6">
                                        <input type="text" name="longitude_akhir" id="longitude_akhir" class="form-control form-control-sm bg-white" readonly placeholder="Long Akhir" value="{{ old('longitude_akhir', $log->longitude_akhir) }}">
                                    </div>
                                </div>
                                <small id="gps_akhir_status" class="fs-11 text-muted mt-1 d-block">
                                    @if($log->latitude_akhir)
                                        <span class="text-success"><i class="feather-check-circle me-1"></i>GPS Akhir terekam</span>
                                    @else
                                        Terisi otomatis saat Foto Sesudah diupload.
                                    @endif
                                </small>
                            </div>
                        </div>
                    </div>

                    <hr class="my-4">
                    <!-- 3. Foto Dokumentasi Kerja - GAMBAR DI ATAS JAM -->
                    <h6 class="fw-bold text-primary mb-3"><i class="feather-image me-2"></i>3. Foto Dokumentasi Sebelum & Sesudah Jam Kerja</h6>

                    <div class="row mb-3">
                        <!-- Foto Sebelum -->
                        <div class="col-md-6 mb-3 mb-md-0">
                            <label class="form-label fw-semibold">Foto Sebelum Jam Kerja (Awal Shift)</label>
                            @php
                                $isFotoSebelumLocked = !empty($log->foto_sebelum);
                            @endphp

                            @if($isFotoSebelumLocked)
                                <div class="p-2 bg-light rounded border text-center mb-2">
                                    <span class="badge bg-danger mb-2"><i class="feather-lock me-1"></i>Foto Terkunci</span>
                                    <img src="{{ asset('gallery/' . $log->foto_sebelum) }}" alt="Foto Sebelum Kerja" class="img-thumbnail d-block mx-auto" style="max-height: 140px;">
                                    <small class="text-danger fs-11 d-block mt-1 fw-semibold">Foto Sebelum terkunci untuk mencegah rekayasa data.</small>
                                </div>
                            @else
                                <input type="file" name="foto_sebelum" id="foto_sebelum" class="form-control @error('foto_sebelum') is-invalid @enderror" accept="image/*">
                                <small class="text-muted fs-11 d-block mt-1"><i class="feather-clock me-1 text-primary"></i>Jam Awal & GPS Awal otomatis terekam saat foto ini dipilih.</small>
                                @error('foto_sebelum') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            @endif
                        </div>

                        <!-- Foto Sesudah -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Foto Sesudah Jam Kerja (Akhir Shift)</label>
                            <input type="file" name="foto_sesudah" id="foto_sesudah" class="form-control @error('foto_sesudah') is-invalid @enderror" accept="image/*">
                            <small class="text-muted fs-11 d-block mt-1"><i class="feather-clock me-1 text-primary"></i>Jam Selesai & GPS Akhir otomatis terekam saat foto ini dipilih.</small>
                            @error('foto_sesudah') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            @if($log->foto_sesudah)
                                <div class="mt-2 text-center">
                                    <small class="text-muted d-block mb-1">Foto sesudah kerja saat ini:</small>
                                    <img src="{{ asset('gallery/' . $log->foto_sesudah) }}" alt="Foto Sesudah Kerja" class="img-thumbnail" style="max-height: 120px;">
                                </div>
                            @endif
                        </div>
                    </div>

                    <hr class="my-4">
                    <!-- 4. Hour Meter (HM) & Jam Kerja - JAM TIDAK BOLEH INPUT MANUAL -->
                    <h6 class="fw-bold text-primary mb-3"><i class="feather-clock me-2"></i>4. Catatan Jam Kerja (Hour Meter / HM)</h6>

                    <div class="row mb-3">
                        <div class="col-md-4 mb-3 mb-md-0">
                            <label class="form-label">Hour Meter (HM) Awal</label>
                            <input type="text" name="hm_awal" id="hm_awal" class="form-control bg-light @error('hm_awal') is-invalid @enderror" placeholder="HH:MM (Otomatis dari Foto Sebelum)" value="{{ old('hm_awal', $log->hm_awal_formatted != '-' ? $log->hm_awal_formatted : '') }}" readonly>
                            <small class="text-danger fs-11 d-block mt-1"><i class="feather-lock me-1"></i>HM Awal terkunci (terisi dari foto sebelum/data awal) untuk mencegah rekayasa.</small>
                            @error('hm_awal') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-4 mb-3 mb-md-0">
                            <label class="form-label">Hour Meter (HM) Akhir</label>
                            <input type="text" name="hm_akhir" id="hm_akhir" class="form-control bg-light @error('hm_akhir') is-invalid @enderror" placeholder="HH:MM (Otomatis dari Foto Sesudah)" value="{{ old('hm_akhir', $log->hm_akhir_formatted != '-' ? $log->hm_akhir_formatted : '') }}" readonly>
                            <small class="text-danger fs-11 d-block mt-1"><i class="feather-lock me-1"></i>HM Akhir terkunci (terisi dari foto sesudah/data akhir) untuk mencegah rekayasa.</small>
                            @error('hm_akhir') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Total Jam Kerja (Otomatis)</label>
                            <input type="text" id="total_hm_display" class="form-control bg-light fw-bold text-primary" value="{{ \App\Models\MonitoringAlatBerat::formatHm($log->total_hm, true) }}" readonly>
                        </div>
                    </div>

                    <hr class="my-4">
                    <!-- 5. Hasil Aplikasi Bed -->
                    <h6 class="fw-bold text-primary mb-3"><i class="feather-grid me-2"></i>5. Hasil Aplikasi Bed (Aplikasi Lahan)</h6>
                    
                    <div class="row mb-3">
                        <div class="col-md-4 mb-3 mb-md-0">
                            <label class="form-label">Jumlah Flat Bed Dikerjakan</label>
                            <div class="input-group">
                                <input type="number" name="flat_bed" id="flat_bed" min="0" class="form-control @error('flat_bed') is-invalid @enderror" value="{{ old('flat_bed', $log->flat_bed ?? 0) }}" oninput="calcBedTotal()">
                                <span class="input-group-text">Flat Bed</span>
                            </div>
                            @error('flat_bed') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-4 mb-3 mb-md-0">
                            <label class="form-label">Jumlah Long Bed Dikerjakan</label>
                            <div class="input-group">
                                <input type="number" name="long_bed" id="long_bed" min="0" class="form-control @error('long_bed') is-invalid @enderror" value="{{ old('long_bed', $log->long_bed ?? 0) }}" oninput="calcBedTotal()">
                                <span class="input-group-text">Long Bed</span>
                            </div>
                            @error('long_bed') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Total Bed Dikerjakan</label>
                            <div class="p-2 bg-light rounded border text-center">
                                <span id="total_bed_display" class="fw-bold fs-5 text-primary">{{ $log->jumlah_bed }}</span> Bed
                                <input type="hidden" name="jumlah_bed" id="jumlah_bed" value="{{ old('jumlah_bed', $log->jumlah_bed) }}">
                            </div>
                        </div>
                    </div>

                    <hr class="my-4">
                    <!-- 6. BBM & Kondisi Alat -->
                    <h6 class="fw-bold text-primary mb-3"><i class="feather-check-square me-2"></i>6. Konsumsi BBM & Kondisi Alat</h6>

                    <div class="row mb-3">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Konsumsi BBM (Liter Solar)</label>
                            <div class="input-group">
                                <input type="number" step="0.1" name="bbm_liter" class="form-control" value="{{ old('bbm_liter', $log->bbm_liter) }}">
                                <span class="input-group-text">Liter</span>
                            </div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Kondisi Alat Saat Pengolahan <span class="text-danger">*</span></label>
                            <select name="kondisi_alat" class="form-select @error('kondisi_alat') is-invalid @enderror" required>
                                <option value="Normal" {{ old('kondisi_alat', $log->kondisi_alat) == 'Normal' ? 'selected' : '' }}>Normal / Baik</option>
                                <option value="Perlu Perbaikan" {{ old('kondisi_alat', $log->kondisi_alat) == 'Perlu Perbaikan' ? 'selected' : '' }}>Ada Kendala / Perlu Perbaikan</option>
                                <option value="Breakdown" {{ old('kondisi_alat', $log->kondisi_alat) == 'Breakdown' ? 'selected' : '' }}>Breakdown / Rusak</option>
                            </select>
                            @error('kondisi_alat') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Catatan Operasional / Kendala Lapangan</label>
                        <textarea name="catatan" class="form-control" rows="3">{{ old('catatan', $log->catatan) }}</textarea>
                    </div>

                    <div class="d-flex justify-content-between mt-4">
                        <a href="{{ route('monitoring-alat-berat.index') }}" class="btn btn-light">Batal</a>
                        <button type="submit" class="btn btn-primary"><i class="feather-save me-2"></i>Perbarui Log Monitoring</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

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
                totalHmDisplay.value = 'Proses / Berjalan';
            } else {
                totalHmDisplay.value = '00:00 Jam';
            }
            return;
        }
        const awalMins = parseTimeToMinutes(hmAwalInput.value);
        const akhirMins = parseTimeToMinutes(hmAkhirInput.value);
        let diff = akhirMins - awalMins;
        if (diff < 0 && awalMins > 0 && akhirMins > 0) {
            diff += 24 * 60;
        }
        const finalDiff = Math.max(0, diff);
        totalHmDisplay.value = minutesToTime(finalDiff);
    }

    function getGpsAwal() {
        const statusElem = document.getElementById('gps_awal_status');
        if (statusElem) statusElem.innerText = 'Merekam lokasi GPS Awal...';

        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(
                function(position) {
                    const lat = position.coords.latitude.toFixed(8);
                    const long = position.coords.longitude.toFixed(8);
                    document.getElementById('latitude_awal').value = lat;
                    document.getElementById('longitude_awal').value = long;
                    document.getElementById('latitude').value = lat;
                    document.getElementById('longitude').value = long;
                    if (statusElem) statusElem.innerHTML = `<span class="text-success"><i class="feather-check-circle me-1"></i>GPS Awal terekam (${lat}, ${long})</span>`;
                },
                function(error) {
                    if (statusElem) statusElem.innerHTML = `<span class="text-danger"><i class="feather-alert-triangle me-1"></i>Gagal GPS: ${error.message}</span>`;
                },
                { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 }
            );
        }
    }

    function getGpsAkhir() {
        const statusElem = document.getElementById('gps_akhir_status');
        if (statusElem) statusElem.innerText = 'Merekam lokasi GPS Akhir...';

        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(
                function(position) {
                    const lat = position.coords.latitude.toFixed(8);
                    const long = position.coords.longitude.toFixed(8);
                    document.getElementById('latitude_akhir').value = lat;
                    document.getElementById('longitude_akhir').value = long;
                    if (statusElem) statusElem.innerHTML = `<span class="text-success"><i class="feather-check-circle me-1"></i>GPS Akhir terekam (${lat}, ${long})</span>`;
                },
                function(error) {
                    if (statusElem) statusElem.innerHTML = `<span class="text-danger"><i class="feather-alert-triangle me-1"></i>Gagal GPS: ${error.message}</span>`;
                },
                { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 }
            );
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        const fotoSebelumInput = document.getElementById('foto_sebelum');
        const fotoSesudahInput = document.getElementById('foto_sesudah');
        const hmAwalInput = document.getElementById('hm_awal');
        const hmAkhirInput = document.getElementById('hm_akhir');

        if (fotoSebelumInput) {
            fotoSebelumInput.addEventListener('change', function(e) {
                if (e.target.files && e.target.files[0]) {
                    const now = new Date();
                    hmAwalInput.value = formatTimeToHHMM(now);
                    hitungHm();
                    getGpsAwal();
                }
            });
        }

        if (fotoSesudahInput) {
            fotoSesudahInput.addEventListener('change', function(e) {
                if (e.target.files && e.target.files[0]) {
                    const now = new Date();
                    hmAkhirInput.value = formatTimeToHHMM(now);
                    hitungHm();
                    getGpsAkhir();
                }
            });
        }

        calcBedTotal();
        hitungHm();
    });
</script>
@endsection
