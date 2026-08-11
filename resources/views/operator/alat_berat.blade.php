@extends('layouts.operator')

@section('title', 'Kelola Alat Berat & Status - SIMOLII Operator')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-3">
    <div>
        <h5 class="fw-bold m-0"><i class="feather-truck me-2 text-primary"></i>Kelola Unit Alat Berat</h5>
        <small class="text-muted fs-12">PKS {{ Auth::user()->pks ? Auth::user()->pks->nama : 'Unit' }}</small>
    </div>
    <button type="button" class="btn btn-primary btn-sm rounded-pill px-3 shadow-sm fw-bold" data-bs-toggle="modal" data-bs-target="#modalAddAlatBerat">
        <i class="feather-plus me-1"></i>Tambah Unit
    </button>
</div>

<!-- Search & Filter Card -->
<div class="op-card mb-3 p-3">
    <form action="{{ route('operator.alat-berat.index') }}" method="GET" class="row g-2">
        <div class="col-7">
            <input type="text" name="search" class="form-control form-control-sm" placeholder="Cari Kode / Nama..." value="{{ request('search') }}">
        </div>
        <div class="col-5">
            <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                <option value="">Semua Status</option>
                <option value="Operational" {{ request('status') == 'Operational' ? 'selected' : '' }}>Ready</option>
                <option value="Standby" {{ request('status') == 'Standby' ? 'selected' : '' }}>Standby</option>
                <option value="Maintenance" {{ request('status') == 'Maintenance' ? 'selected' : '' }}>Perbaikan</option>
                <option value="Breakdown" {{ request('status') == 'Breakdown' ? 'selected' : '' }}>Rusak</option>
                <option value="Rolling" {{ request('status') == 'Rolling' ? 'selected' : '' }}>Rolling</option>
            </select>
        </div>
    </form>
</div>

<!-- Equipment List Cards -->
@forelse($alatBeratList as $ab)
    <div class="op-card mb-3">
        <div class="op-card-header d-flex justify-content-between align-items-center bg-light">
            <div>
                <code class="fw-bold text-primary fs-14 me-1">[{{ $ab->kode_alat }}]</code>
                <span class="fw-bold text-dark fs-14">{{ $ab->nama_alat }}</span>
            </div>
            <!-- Quick Status Change Dropdown -->
            <div class="dropdown">
                <button class="btn btn-sm dropdown-toggle py-0 px-2 fw-bold 
                    @if($ab->status == 'Operational') btn-success
                    @elseif($ab->status == 'Standby') btn-secondary
                    @elseif($ab->status == 'Maintenance') btn-warning text-dark
                    @elseif($ab->status == 'Breakdown') btn-danger
                    @elseif($ab->status == 'Rolling') btn-info text-dark @endif" 
                    type="button" data-bs-toggle="dropdown" style="font-size: 0.75rem;">
                    {{ $ab->status }}
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow border-0">
                    <li><h6 class="dropdown-header">Ubah Status Unit</h6></li>
                    <li>
                        <form action="{{ route('operator.alat-berat.update-status', $ab->id) }}" method="POST">
                            @csrf @method('PATCH')
                            <input type="hidden" name="status" value="Operational">
                            <button type="submit" class="dropdown-item text-success"><i class="feather-check-circle me-2"></i>Ready / Operational</button>
                        </form>
                    </li>
                    <li>
                        <form action="{{ route('operator.alat-berat.update-status', $ab->id) }}" method="POST">
                            @csrf @method('PATCH')
                            <input type="hidden" name="status" value="Standby">
                            <button type="submit" class="dropdown-item text-secondary"><i class="feather-pause-circle me-2"></i>Standby / Cadangan</button>
                        </form>
                    </li>
                    <li>
                        <form action="{{ route('operator.alat-berat.update-status', $ab->id) }}" method="POST">
                            @csrf @method('PATCH')
                            <input type="hidden" name="status" value="Maintenance">
                            <button type="submit" class="dropdown-item text-warning"><i class="feather-tool me-2"></i>Maintenance / Perbaikan</button>
                        </form>
                    </li>
                    <li>
                        <form action="{{ route('operator.alat-berat.update-status', $ab->id) }}" method="POST">
                            @csrf @method('PATCH')
                            <input type="hidden" name="status" value="Breakdown">
                            <button type="submit" class="dropdown-item text-danger"><i class="feather-alert-triangle me-2"></i>Breakdown / Rusak</button>
                        </form>
                    </li>
                    <li>
                        <form action="{{ route('operator.alat-berat.update-status', $ab->id) }}" method="POST">
                            @csrf @method('PATCH')
                            <input type="hidden" name="status" value="Rolling">
                            <button type="submit" class="dropdown-item text-info"><i class="feather-repeat me-2"></i>Rolling / Dipinjam Kebun Lain</button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>

        <div class="op-card-body p-3">
            <div class="row g-2 mb-2 text-muted fs-12">
                <div class="col-6"><i class="feather-info me-1"></i>Jenis: <strong class="text-dark">{{ $ab->jenis_alat }}</strong></div>
                <div class="col-6"><i class="feather-cpu me-1"></i>Merk/Tipe: <strong class="text-dark">{{ $ab->merk_tipe ?? '-' }}</strong></div>
                <div class="col-6"><i class="feather-calendar me-1"></i>Tahun: <strong class="text-dark">{{ $ab->tahun_pengadaan ?? '-' }}</strong></div>
            </div>

            @if($ab->keterangan)
                <div class="p-2 bg-light rounded border text-muted fs-12 mb-2">
                    {{ $ab->keterangan }}
                </div>
            @endif

            <div class="d-flex justify-content-end gap-2 pt-2 border-top">
                <button type="button" class="btn btn-sm btn-outline-warning px-3" data-bs-toggle="modal" data-bs-target="#modalEditAlatBerat{{ $ab->id }}">
                    <i class="feather-edit-2 me-1"></i>Edit
                </button>
                <form action="{{ route('operator.alat-berat.destroy', $ab->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus unit alat berat ini?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-outline-danger px-3">
                        <i class="feather-trash-2 me-1"></i>Hapus
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Modal for Unit -->
    <div class="modal fade" id="modalEditAlatBerat{{ $ab->id }}" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-4 border-0">
                <div class="modal-header bg-warning text-dark">
                    <h6 class="modal-title fw-bold"><i class="feather-edit-2 me-2"></i>Edit Unit Alat Berat</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('operator.alat-berat.update', $ab->id) }}" method="POST">
                    @csrf @method('PUT')
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Kode Alat <span class="text-danger">*</span></label>
                            <input type="text" name="kode_alat" class="form-control" value="{{ old('kode_alat', $ab->kode_alat) }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Nama Alat Berat <span class="text-danger">*</span></label>
                            <input type="text" name="nama_alat" class="form-control" value="{{ old('nama_alat', $ab->nama_alat) }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Jenis Alat <span class="text-danger">*</span></label>
                            <select name="jenis_alat" class="form-select" required>
                                <option value="Excavator" {{ old('jenis_alat', $ab->jenis_alat) == 'Excavator' ? 'selected' : '' }}>Excavator</option>
                                <option value="Wheel Loader" {{ old('jenis_alat', $ab->jenis_alat) == 'Wheel Loader' ? 'selected' : '' }}>Wheel Loader</option>
                                <option value="Bulldozer" {{ old('jenis_alat', $ab->jenis_alat) == 'Bulldozer' ? 'selected' : '' }}>Bulldozer</option>
                                <option value="Dump Truck" {{ old('jenis_alat', $ab->jenis_alat) == 'Dump Truck' ? 'selected' : '' }}>Dump Truck</option>
                                <option value="Compactor" {{ old('jenis_alat', $ab->jenis_alat) == 'Compactor' ? 'selected' : '' }}>Compactor</option>
                                <option value="Lainnya" {{ old('jenis_alat', $ab->jenis_alat) == 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
                            </select>
                        </div>
                        <div class="row">
                            <div class="col-6 mb-3">
                                <label class="form-label">Merk / Tipe</label>
                                <input type="text" name="merk_tipe" class="form-control" value="{{ old('merk_tipe', $ab->merk_tipe) }}">
                            </div>
                            <div class="col-6 mb-3">
                                <label class="form-label">Tahun</label>
                                <input type="number" name="tahun_pengadaan" class="form-control" value="{{ old('tahun_pengadaan', $ab->tahun_pengadaan) }}">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Status Operasional <span class="text-danger">*</span></label>
                            <select name="status" class="form-select" required>
                                <option value="Operational" {{ old('status', $ab->status) == 'Operational' ? 'selected' : '' }}>Ready / Operational</option>
                                <option value="Standby" {{ old('status', $ab->status) == 'Standby' ? 'selected' : '' }}>Standby / Cadangan</option>
                                <option value="Maintenance" {{ old('status', $ab->status) == 'Maintenance' ? 'selected' : '' }}>Maintenance / Dalam Perbaikan</option>
                                <option value="Breakdown" {{ old('status', $ab->status) == 'Breakdown' ? 'selected' : '' }}>Breakdown / Rusak</option>
                                <option value="Rolling" {{ old('status', $ab->status) == 'Rolling' ? 'selected' : '' }}>Rolling / Dipinjam Kebun Lain</option>
                            </select>
                        </div>
                        <div class="mb-0">
                            <label class="form-label">Keterangan</label>
                            <textarea name="keterangan" class="form-control" rows="2">{{ old('keterangan', $ab->keterangan) }}</textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-warning fw-bold">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@empty
    <div class="text-center p-4 bg-white rounded-3 border text-muted">
        <i class="feather-truck fs-2 d-block mb-2 text-muted"></i>
        Belum ada data unit alat berat. Tekan tombol Tambah Unit untuk memasukkan unit baru.
    </div>
@endforelse

<!-- Modal Add Alat Berat -->
<div class="modal fade" id="modalAddAlatBerat" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0">
            <div class="modal-header bg-primary text-white">
                <h6 class="modal-title fw-bold"><i class="feather-plus-circle me-2"></i>Tambah Unit Alat Berat Baru</h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('operator.alat-berat.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Kode Alat <span class="text-danger">*</span></label>
                        <input type="text" name="kode_alat" class="form-control" placeholder="Contoh: EX-03" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nama Alat Berat <span class="text-danger">*</span></label>
                        <input type="text" name="nama_alat" class="form-control" placeholder="Contoh: Excavator Kobelco SK200" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Jenis Alat <span class="text-danger">*</span></label>
                        <select name="jenis_alat" class="form-select" required>
                            <option value="Excavator">Excavator</option>
                            <option value="Wheel Loader">Wheel Loader</option>
                            <option value="Bulldozer">Bulldozer</option>
                            <option value="Dump Truck">Dump Truck</option>
                            <option value="Compactor">Compactor</option>
                            <option value="Lainnya">Lainnya</option>
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-6 mb-3">
                            <label class="form-label">Merk / Tipe</label>
                            <input type="text" name="merk_tipe" class="form-control" placeholder="Contoh: Kobelco SK200-10">
                        </div>
                        <div class="col-6 mb-3">
                            <label class="form-label">Tahun</label>
                            <input type="number" name="tahun_pengadaan" class="form-control" placeholder="Contoh: 2023" min="1900" max="{{ date('Y') }}">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Status Operasional Awal <span class="text-danger">*</span></label>
                        <select name="status" class="form-select" required>
                            <option value="Operational">Ready / Operational</option>
                            <option value="Standby">Standby / Cadangan</option>
                            <option value="Maintenance">Maintenance / Dalam Perbaikan</option>
                            <option value="Breakdown">Breakdown / Rusak</option>
                            <option value="Rolling">Rolling / Dipinjam Kebun Lain</option>
                        </select>
                    </div>
                    <div class="mb-0">
                        <label class="form-label">Keterangan</label>
                        <textarea name="keterangan" class="form-control" rows="2" placeholder="Catatan kondisi atau peruntukan unit..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary fw-bold">Tambah Alat Berat</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
