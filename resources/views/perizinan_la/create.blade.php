@extends('layouts.simoli')

@section('title', 'Tambah Perizinan Land Application')
@section('page-title', 'Form Pendaftaran SK Izin Land Application')
@section('page-description', 'Input Data Legalitas, Baku Mutu & Titik Pantau Pemanfaatan Air Limbah ke Tanah')

@section('breadcrumb')
    <li><a href="{{ route('perizinan-la.index') }}">Perizinan LA</a></li>
    <li class="separator">/</li>
    <li>Tambah Izin</li>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-10">
        <form action="{{ route('perizinan-la.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            {{-- 1. IDENTITAS LEGALITAS SK --}}
            <div class="simoli-card mb-4">
                <div class="simoli-card-header">
                    <h3 class="simoli-card-title">
                        <i class="feather-file-text text-success"></i>
                        <span>1. Identitas Legalitas Surat Keputusan (SK)</span>
                    </h3>
                </div>
                <div class="simoli-card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-simoli-label">Pabrik Kelapa Sawit (PKS) <span class="text-danger">*</span></label>
                            <select name="id_pks" class="form-select form-simoli-control @error('id_pks') is-invalid @enderror" required>
                                <option value="">-- Pilih PKS --</option>
                                @foreach($daftarPks as $pks)
                                <option value="{{ $pks->id_pks }}" {{ old('id_pks') == $pks->id_pks ? 'selected' : '' }}>
                                    {{ $pks->nama }} ({{ $pks->kode }})
                                </option>
                                @endforeach
                            </select>
                            @error('id_pks')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-simoli-label">Nomor SK Perizinan <span class="text-danger">*</span></label>
                            <input type="text" name="nomor_sk" class="form-control form-simoli-control @error('nomor_sk') is-invalid @enderror" 
                                   placeholder="Contoh: 41/DLH/2017 atau 503/DPM-PTSP.PEL/LA/2017/09" value="{{ old('nomor_sk') }}" required>
                            @error('nomor_sk')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-12">
                            <label class="form-simoli-label">Tentang / Judul Keputusan</label>
                            <input type="text" name="tentang" class="form-control form-simoli-control" 
                                   placeholder="Izin Pemanfaatan Air Limbah Pabrik Kelapa Sawit Pada Tanah (Land Application)" 
                                   value="{{ old('tentang', 'Izin Pemanfaatan Air Limbah Pabrik Kelapa Sawit Pada Tanah di Perkebunan Kelapa Sawit') }}">
                        </div>

                        <div class="col-md-6">
                            <label class="form-simoli-label">Instansi Penerbit Izin <span class="text-danger">*</span></label>
                            <input type="text" name="instansi_penerbit" class="form-control form-simoli-control @error('instansi_penerbit') is-invalid @enderror" 
                                   placeholder="Contoh: Dinas Lingkungan Hidup Kabupaten Rokan Hilir" value="{{ old('instansi_penerbit') }}" required>
                            @error('instansi_penerbit')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-3">
                            <label class="form-simoli-label">Tanggal Penetapan / Terbit <span class="text-danger">*</span></label>
                            <input type="date" name="tanggal_terbit" class="form-control form-simoli-control @error('tanggal_terbit') is-invalid @enderror" 
                                   value="{{ old('tanggal_terbit') }}" required>
                            @error('tanggal_terbit')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-3">
                            <label class="form-simoli-label">Tanggal Berakhir (Masa Berlaku)</label>
                            <input type="date" name="tanggal_berakhir" class="form-control form-simoli-control @error('tanggal_berakhir') is-invalid @enderror" 
                                   value="{{ old('tanggal_berakhir') }}">
                            @error('tanggal_berakhir')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-4">
                            <label class="form-simoli-label">Status Izin</label>
                            <select name="status_izin" class="form-select form-simoli-control">
                                <option value="Aktif" {{ old('status_izin') == 'Aktif' ? 'selected' : '' }}>Aktif</option>
                                <option value="Proses Perpanjangan" {{ old('status_izin') == 'Proses Perpanjangan' ? 'selected' : '' }}>Proses Perpanjangan</option>
                                <option value="Kedaluwarsa" {{ old('status_izin') == 'Kedaluwarsa' ? 'selected' : '' }}>Kedaluwarsa</option>
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-simoli-label">Upload Berkas SK (PDF/Gambar)</label>
                            <input type="file" name="file_sk" class="form-control form-simoli-control @error('file_sk') is-invalid @enderror" accept=".pdf,.jpg,.jpeg,.png">
                            <small class="text-muted">Maks. 10 MB (Format: PDF, JPG, PNG)</small>
                            @error('file_sk')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-4">
                            <label class="form-simoli-label">Upload Lampiran Peta LA (Gambar/PDF)</label>
                            <input type="file" name="file_peta" class="form-control form-simoli-control @error('file_peta') is-invalid @enderror" accept=".pdf,.jpg,.jpeg,.png">
                            <small class="text-muted">Maks. 10 MB (Peta Kartografi)</small>
                            @error('file_peta')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>
            </div>

            {{-- 2. BAKU MUTU & BATASAN TEKNIS --}}
            <div class="simoli-card mb-4">
                <div class="simoli-card-header">
                    <h3 class="simoli-card-title">
                        <i class="feather-sliders text-success"></i>
                        <span>2. Standar Baku Mutu & Batasan Teknis Pengaliran</span>
                    </h3>
                </div>
                <div class="simoli-card-body">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-simoli-label">Kadar BOD Maksimal (mg/L / ppm) <span class="text-danger">*</span></label>
                            <input type="number" name="bod_maksimal" class="form-control form-simoli-control" value="{{ old('bod_maksimal', 5000) }}" required>
                            <small class="text-muted">Baku mutu standar: 5.000 mg/L</small>
                        </div>

                        <div class="col-md-3">
                            <label class="form-simoli-label">Rentang pH (Min - Max) <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="number" step="0.1" name="ph_min" class="form-control form-simoli-control" placeholder="Min" value="{{ old('ph_min', 6.0) }}" required>
                                <span class="input-group-text">s/d</span>
                                <input type="number" step="0.1" name="ph_max" class="form-control form-simoli-control" placeholder="Max" value="{{ old('ph_max', 9.0) }}" required>
                            </div>
                            <small class="text-muted">Standar pH: 6.0 – 9.0</small>
                        </div>

                        <div class="col-md-3">
                            <label class="form-simoli-label">Debit Maksimal Harian (m³/hari) <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" name="debit_maksimal_harian" class="form-control form-simoli-control" placeholder="Contoh: 400" value="{{ old('debit_maksimal_harian', 400.00) }}" required>
                            <small class="text-muted">Kuota izin buang/hari</small>
                        </div>

                        <div class="col-md-3">
                            <label class="form-simoli-label">Luas Areal Izin (Ha) <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" name="luas_areal_izin" class="form-control form-simoli-control" placeholder="Contoh: 250.00" value="{{ old('luas_areal_izin', 250.00) }}" required>
                            <small class="text-muted">Total luas blok berizin</small>
                        </div>

                        <div class="col-md-6">
                            <label class="form-simoli-label">Spesifikasi Saluran / Pipa Distribusi</label>
                            <input type="text" name="saluran_distribusi" class="form-control form-simoli-control" placeholder="Contoh: Pipa PVC ukuran 6 inchi kedap air" value="{{ old('saluran_distribusi', 'Pipa PVC ukuran 6 inchi') }}">
                        </div>

                        <div class="col-md-6">
                            <label class="form-simoli-label">Nama Titik Penaatan Effluent (Outlet IPAL)</label>
                            <input type="text" name="nama_titik_penaatan" class="form-control form-simoli-control" placeholder="IPAL Kolam Anaerob Pond IV" value="{{ old('nama_titik_penaatan', 'IPAL Kolam Anaerob Pond IV') }}">
                        </div>

                        <div class="col-md-4">
                            <label class="form-simoli-label">Koordinat Penaatan (Format Teks)</label>
                            <input type="text" name="koordinat_penaatan_text" class="form-control form-simoli-control" placeholder='N 01°44\' 33.09" E 100°30\' 36.04"' value="{{ old('koordinat_penaatan_text') }}">
                        </div>

                        <div class="col-md-4">
                            <label class="form-simoli-label">Latitude Titik Penaatan (Desimal)</label>
                            <input type="number" step="0.00000001" name="lat_titik_penaatan" id="lat_titik_penaatan" class="form-control form-simoli-control" placeholder="Contoh: 1.74252500" value="{{ old('lat_titik_penaatan') }}">
                        </div>

                        <div class="col-md-4">
                            <label class="form-simoli-label">Longitude Titik Penaatan (Desimal)</label>
                            <input type="number" step="0.00000001" name="long_titik_penaatan" id="long_titik_penaatan" class="form-control form-simoli-control" placeholder="Contoh: 100.51001100" value="{{ old('long_titik_penaatan') }}">
                        </div>
                    </div>
                </div>
            </div>

            {{-- 3. TITIK SUMUR PANTAU AIR TANAH --}}
            <div class="simoli-card mb-4">
                <div class="simoli-card-header">
                    <h3 class="simoli-card-title">
                        <i class="feather-droplet text-success"></i>
                        <span>3. Titik Koordinat Sumur Pantau Air Tanah (Sesuai Lampiran SK)</span>
                    </h3>
                    <button type="button" class="btn btn-sm btn-outline-success" onclick="tambahBarisSumur()">
                        <i class="feather-plus"></i> Tambah Titik Sumur
                    </button>
                </div>
                <div class="simoli-card-body">
                    <div id="container-sumur-pantau">
                        {{-- Row Default 1: Lahan Aplikasi --}}
                        <div class="row g-2 p-3 bg-light rounded-3 border mb-3 sumur-row">
                            <div class="col-md-3">
                                <label class="form-simoli-label font-11">Nama Titik Sumur</label>
                                <input type="text" name="sumur_nama[]" class="form-control form-control-sm" value="Sumur Pantau Lahan Aplikasi" placeholder="Nama Sumur Pantau">
                            </div>
                            <div class="col-md-3">
                                <label class="form-simoli-label font-11">Jenis Sumur</label>
                                <select name="sumur_jenis[]" class="form-select form-control-sm">
                                    <option value="Sumur Pantau Aplikasi">Sumur Pantau Aplikasi</option>
                                    <option value="Sumur Pantau Kontrol">Sumur Pantau Kontrol</option>
                                    <option value="Sumur Pantau Pemukiman">Sumur Pantau Pemukiman</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-simoli-label font-11">Lokasi Blok</label>
                                <input type="text" name="sumur_blok[]" class="form-control form-control-sm" placeholder="Contoh: AFD III Blok C 27">
                            </div>
                            <div class="col-md-2">
                                <label class="form-simoli-label font-11">Latitude</label>
                                <input type="number" step="0.00000001" name="sumur_lat[]" class="form-control form-control-sm" placeholder="1.74956400">
                            </div>
                            <div class="col-md-2">
                                <label class="form-simoli-label font-11">Longitude</label>
                                <input type="number" step="0.00000001" name="sumur_long[]" class="form-control form-control-sm" placeholder="100.52151400">
                            </div>
                        </div>

                        {{-- Row Default 2: Lahan Kontrol --}}
                        <div class="row g-2 p-3 bg-light rounded-3 border mb-3 sumur-row">
                            <div class="col-md-3">
                                <label class="form-simoli-label font-11">Nama Titik Sumur</label>
                                <input type="text" name="sumur_nama[]" class="form-control form-control-sm" value="Sumur Pantau Lahan Kontrol" placeholder="Nama Sumur Pantau">
                            </div>
                            <div class="col-md-3">
                                <label class="form-simoli-label font-11">Jenis Sumur</label>
                                <select name="sumur_jenis[]" class="form-select form-control-sm">
                                    <option value="Sumur Pantau Aplikasi">Sumur Pantau Aplikasi</option>
                                    <option value="Sumur Pantau Kontrol" selected>Sumur Pantau Kontrol</option>
                                    <option value="Sumur Pantau Pemukiman">Sumur Pantau Pemukiman</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-simoli-label font-11">Lokasi Blok</label>
                                <input type="text" name="sumur_blok[]" class="form-control form-control-sm" placeholder="Contoh: AFD II Blok G.25">
                            </div>
                            <div class="col-md-2">
                                <label class="form-simoli-label font-11">Latitude</label>
                                <input type="number" step="0.00000001" name="sumur_lat[]" class="form-control form-control-sm" placeholder="1.72977800">
                            </div>
                            <div class="col-md-2">
                                <label class="form-simoli-label font-11">Longitude</label>
                                <input type="number" step="0.00000001" name="sumur_long[]" class="form-control form-control-sm" placeholder="100.51175000">
                            </div>
                        </div>

                        {{-- Row Default 3: Pemukiman --}}
                        <div class="row g-2 p-3 bg-light rounded-3 border mb-3 sumur-row">
                            <div class="col-md-3">
                                <label class="form-simoli-label font-11">Nama Titik Sumur</label>
                                <input type="text" name="sumur_nama[]" class="form-control form-control-sm" value="Sumur Pantau Perumahan Karyawan" placeholder="Nama Sumur Pantau">
                            </div>
                            <div class="col-md-3">
                                <label class="form-simoli-label font-11">Jenis Sumur</label>
                                <select name="sumur_jenis[]" class="form-select form-control-sm">
                                    <option value="Sumur Pantau Aplikasi">Sumur Pantau Aplikasi</option>
                                    <option value="Sumur Pantau Kontrol">Sumur Pantau Kontrol</option>
                                    <option value="Sumur Pantau Pemukiman" selected>Sumur Pantau Pemukiman</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-simoli-label font-11">Lokasi Blok</label>
                                <input type="text" name="sumur_blok[]" class="form-control form-control-sm" placeholder="Contoh: AFD III Blok D.27">
                            </div>
                            <div class="col-md-2">
                                <label class="form-simoli-label font-11">Latitude</label>
                                <input type="number" step="0.00000001" name="sumur_lat[]" class="form-control form-control-sm" placeholder="1.74405600">
                            </div>
                            <div class="col-md-2">
                                <label class="form-simoli-label font-11">Longitude</label>
                                <input type="number" step="0.00000001" name="sumur_long[]" class="form-control form-control-sm" placeholder="100.52147800">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- 4. KETERANGAN & CATATAN --}}
            <div class="simoli-card mb-4">
                <div class="simoli-card-header">
                    <h3 class="simoli-card-title">
                        <i class="feather-info text-success"></i>
                        <span>4. Catatan Tambahan & Keterangan Regulasi</span>
                    </h3>
                </div>
                <div class="simoli-card-body">
                    <textarea name="keterangan" rows="3" class="form-control form-simoli-control" placeholder="Tambahkan catatan mengenai persetujuan teknis, SLO, atau lampiran dinas..."></textarea>
                </div>
            </div>

            {{-- ACTIONS --}}
            <div class="d-flex justify-content-end gap-2 mb-5">
                <a href="{{ route('perizinan-la.index') }}" class="btn btn-light border px-4">Batal</a>
                <button type="submit" class="btn-ptpn btn-ptpn-primary px-5">
                    <i class="feather-save me-1"></i> Simpan Data Izin
                </button>
            </div>

        </form>
    </div>
</div>

<script>
function tambahBarisSumur() {
    const container = document.getElementById('container-sumur-pantau');
    const newRow = document.createElement('div');
    newRow.className = 'row g-2 p-3 bg-light rounded-3 border mb-3 sumur-row';
    newRow.innerHTML = `
        <div class="col-md-3">
            <label class="form-simoli-label font-11">Nama Titik Sumur</label>
            <input type="text" name="sumur_nama[]" class="form-control form-control-sm" placeholder="Nama Titik Sumur Pantau">
        </div>
        <div class="col-md-3">
            <label class="form-simoli-label font-11">Jenis Sumur</label>
            <select name="sumur_jenis[]" class="form-select form-control-sm">
                <option value="Sumur Pantau Aplikasi">Sumur Pantau Aplikasi</option>
                <option value="Sumur Pantau Kontrol">Sumur Pantau Kontrol</option>
                <option value="Sumur Pantau Pemukiman">Sumur Pantau Pemukiman</option>
                <option value="Lainnya">Lainnya</option>
            </select>
        </div>
        <div class="col-md-2">
            <label class="form-simoli-label font-11">Lokasi Blok</label>
            <input type="text" name="sumur_blok[]" class="form-control form-control-sm" placeholder="Contoh: AFD I Blok B1">
        </div>
        <div class="col-md-2">
            <label class="form-simoli-label font-11">Latitude</label>
            <input type="number" step="0.00000001" name="sumur_lat[]" class="form-control form-control-sm" placeholder="Lat Desimal">
        </div>
        <div class="col-md-1">
            <label class="form-simoli-label font-11">Longitude</label>
            <input type="number" step="0.00000001" name="sumur_long[]" class="form-control form-control-sm" placeholder="Long Desimal">
        </div>
        <div class="col-md-1 d-flex align-items-end">
            <button type="button" class="btn btn-sm btn-outline-danger w-100" onclick="this.closest('.sumur-row').remove()">
                <i class="feather-trash-2"></i>
            </button>
        </div>
    `;
    container.appendChild(newRow);
}
</script>
@endsection
