@extends('layouts.simoli')

@section('title', 'Edit Arsip SK Perizinan LA')
@section('page-title', 'Edit Metadata & Berkas SK Land Application')
@section('page-description', 'Perbarui Informasi Legalitas atau Unggah Ulang Berkas Surat Keputusan (SK)')

@section('breadcrumb')
    <li><a href="{{ route('perizinan-la.index') }}">Arsip Perizinan LA</a></li>
    <li class="separator">/</li>
    <li>Edit Arsip SK</li>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <form action="{{ route('perizinan-la.update', $perizinan->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            
            <div class="simoli-card mb-4">
                <div class="simoli-card-header">
                    <h3 class="simoli-card-title">
                        <i class="feather-edit text-success"></i>
                        <span>Informasi &amp; Berkas Dokumen SK</span>
                    </h3>
                </div>
                <div class="simoli-card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-simoli-label">Pabrik Kelapa Sawit (PKS) <span class="text-danger">*</span></label>
                            @if(Auth::user()->isAdmin())
                                <select name="id_pks" class="form-select form-simoli-control @error('id_pks') is-invalid @enderror" required>
                                    <option value="">-- Pilih PKS --</option>
                                    @foreach($daftarPks as $pks)
                                    <option value="{{ $pks->id_pks }}" {{ old('id_pks', $perizinan->id_pks) == $pks->id_pks ? 'selected' : '' }}>
                                        {{ $pks->nama }} ({{ $pks->akro ?? $pks->kode }})
                                    </option>
                                    @endforeach
                                </select>
                            @else
                                <input type="hidden" name="id_pks" value="{{ $perizinan->id_pks }}">
                                <input type="text" class="form-control form-simoli-control" value="{{ $perizinan->pks ? $perizinan->pks->nama . ' (' . ($perizinan->pks->akro ?? $perizinan->pks->kode) . ')' : 'PKS Unit' }}" disabled readonly style="background:#f0fdf4;color:#15803d;font-weight:700;">
                            @endif
                            @error('id_pks')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-simoli-label">Nomor SK Perizinan <span class="text-danger">*</span></label>
                            <input type="text" name="nomor_sk" class="form-control form-simoli-control @error('nomor_sk') is-invalid @enderror" 
                                   value="{{ old('nomor_sk', $perizinan->nomor_sk) }}" required>
                            @error('nomor_sk')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-12">
                            <label class="form-simoli-label">Perihal / Judul Surat Keputusan <span class="text-danger">*</span></label>
                            <input type="text" name="tentang" class="form-control form-simoli-control @error('tentang') is-invalid @enderror" 
                                   value="{{ old('tentang', $perizinan->tentang) }}" required>
                            @error('tentang')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-simoli-label">Instansi Penerbit Izin <span class="text-danger">*</span></label>
                            <input type="text" name="instansi_penerbit" class="form-control form-simoli-control @error('instansi_penerbit') is-invalid @enderror" 
                                   value="{{ old('instansi_penerbit', $perizinan->instansi_penerbit) }}" required>
                            @error('instansi_penerbit')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-simoli-label">Batas Maksimal Debit Pengaliran (Sesuai SK) <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="number" step="0.01" name="debit_maksimal_harian" class="form-control form-simoli-control @error('debit_maksimal_harian') is-invalid @enderror" 
                                       placeholder="Contoh: 400 atau 750" value="{{ old('debit_maksimal_harian', $perizinan->debit_maksimal_harian ?? 400) }}" required style="font-weight:700;">
                                <span class="input-group-text bg-light text-success fw-bold">m³ / hari</span>
                            </div>
                            <small class="text-muted" style="font-size:11px;">Batas kuota debit maksimal yang diperbolehkan dialirkan per hari sesuai SK legalitas.</small>
                            @error('debit_maksimal_harian')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-simoli-label">Tanggal Penetapan / Terbit <span class="text-danger">*</span></label>
                            <input type="date" name="tanggal_terbit" class="form-control form-simoli-control @error('tanggal_terbit') is-invalid @enderror" 
                                   value="{{ old('tanggal_terbit', $perizinan->tanggal_terbit?->format('Y-m-d')) }}" required>
                            @error('tanggal_terbit')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-simoli-label">Tanggal Berakhir (Masa Berlaku)</label>
                            <input type="date" name="tanggal_berakhir" class="form-control form-simoli-control @error('tanggal_berakhir') is-invalid @enderror" 
                                   value="{{ old('tanggal_berakhir', $perizinan->tanggal_berakhir?->format('Y-m-d')) }}">
                            @error('tanggal_berakhir')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-12">
                            <label class="form-simoli-label">Status Izin</label>
                            <select name="status_izin" class="form-select form-simoli-control">
                                <option value="Aktif" {{ old('status_izin', $perizinan->status_izin) == 'Aktif' ? 'selected' : '' }}>Aktif</option>
                                <option value="Proses Perpanjangan" {{ old('status_izin', $perizinan->status_izin) == 'Proses Perpanjangan' ? 'selected' : '' }}>Proses Perpanjangan</option>
                                <option value="Kedaluwarsa" {{ old('status_izin', $perizinan->status_izin) == 'Kedaluwarsa' ? 'selected' : '' }}>Kedaluwarsa</option>
                            </select>
                        </div>

                        {{-- File SK --}}
                        <div class="col-md-12">
                            <label class="form-simoli-label">Berkas Salinan Dokumen SK</label>
                            @if($perizinan->file_sk)
                            <div class="d-flex align-items-center justify-content-between p-3 border rounded-3 bg-light mb-2">
                                <div class="d-flex align-items-center gap-2">
                                    <i class="feather-file-text text-success" style="font-size:24px;"></i>
                                    <div>
                                        <div style="font-size:13px;font-weight:700;color:#1f2937;">{{ $perizinan->file_sk }}</div>
                                        <div style="font-size:11px;color:#6b7280;">Berkas yang saat ini tersimpan di sistem</div>
                                    </div>
                                </div>
                                <a href="{{ asset('uploads/perizinan_la/' . $perizinan->file_sk) }}" target="_blank" class="btn btn-sm btn-outline-success">
                                    <i class="feather-eye"></i> Lihat Berkas
                                </a>
                            </div>
                            @endif

                            <div class="p-3 border rounded-3 text-center bg-light">
                                <input type="file" name="file_sk" class="form-control form-simoli-control @error('file_sk') is-invalid @enderror" accept=".pdf,.jpg,.jpeg,.png">
                                <small class="text-muted d-block mt-2" style="font-size:11.5px;">
                                    Kosongkan jika tidak ingin mengubah berkas yang sudah ada. Format: <strong>PDF, JPG, PNG</strong> (Maks 20 MB).
                                </small>
                            </div>
                            @error('file_sk')<div class="text-danger mt-1" style="font-size:12px;">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-12">
                            <label class="form-simoli-label">Catatan / Keterangan Tambahan</label>
                            <textarea name="keterangan" rows="3" class="form-control form-simoli-control" placeholder="Tuliskan catatan arsip jika ada...">{{ old('keterangan', $perizinan->keterangan) }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            {{-- TOMBOL SUBMIT --}}
            <div class="d-flex justify-content-end gap-2 mb-5">
                <a href="{{ route('perizinan-la.show', $perizinan->id) }}" class="btn btn-light border px-4">Batal</a>
                <button type="submit" class="btn-ptpn btn-ptpn-primary px-4">
                    <i class="feather-save me-1"></i> Perbarui Arsip SK
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
