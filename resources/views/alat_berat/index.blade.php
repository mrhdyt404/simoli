@extends('layouts.simoli')

@section('title', 'Master Data Alat Berat')
@section('page-title', 'Master Data Alat Berat')

@section('breadcrumb')
<li class="breadcrumb-item">Input Data</li>
<li class="breadcrumb-item active">Master Alat Berat</li>
@endsection

@section('page-actions')
<a href="{{ route('alat-berat.create') }}" class="btn btn-primary">
    <i class="feather-plus me-2"></i>Tambah Alat Berat
</a>
@endsection

@section('content')
<div class="row">
            <div class="col-12">
                <div class="card stretch stretch-full">
                    <div class="card-header">
                        <h5 class="card-title">Filter & Pencarian</h5>
                    </div>
                    <div class="card-body">
                        <form method="GET" action="{{ route('alat-berat.index') }}" class="row g-3">
                            @if(Auth::user()->isAdmin())
                                <div class="col-12 col-sm-6 col-md-3">
                                    <label class="form-label">PKS Unit</label>
                                    <select name="id_pks" class="form-select">
                                        <option value="">-- Semua PKS --</option>
                                        @foreach($pksList as $pks)
                                            <option value="{{ $pks->id_pks }}" {{ request('id_pks') == $pks->id_pks ? 'selected' : '' }}>
                                                {{ $pks->nama }} ({{ $pks->akro }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            @endif
                            <div class="col-12 col-sm-6 col-md-3">
                                <label class="form-label">Jenis Alat</label>
                                <select name="jenis_alat" class="form-select">
                                    <option value="">-- Semua Jenis --</option>
                                    <option value="Excavator" {{ request('jenis_alat') == 'Excavator' ? 'selected' : '' }}>Excavator</option>
                                    <option value="Wheel Loader" {{ request('jenis_alat') == 'Wheel Loader' ? 'selected' : '' }}>Wheel Loader</option>
                                    <option value="Bulldozer" {{ request('jenis_alat') == 'Bulldozer' ? 'selected' : '' }}>Bulldozer</option>
                                    <option value="Dump Truck" {{ request('jenis_alat') == 'Dump Truck' ? 'selected' : '' }}>Dump Truck</option>
                                    <option value="Compactor" {{ request('jenis_alat') == 'Compactor' ? 'selected' : '' }}>Compactor</option>
                                    <option value="Lainnya" {{ request('jenis_alat') == 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
                                </select>
                            </div>
                            <div class="col-12 col-sm-6 col-md-3">
                                <label class="form-label">Status Operasional</label>
                                <select name="status" class="form-select">
                                    <option value="">-- Semua Status --</option>
                                    <option value="Operational" {{ request('status') == 'Operational' ? 'selected' : '' }}>Ready / Operational</option>
                                    <option value="Maintenance" {{ request('status') == 'Maintenance' ? 'selected' : '' }}>Maintenance / Perbaikan</option>
                                    <option value="Breakdown" {{ request('status') == 'Breakdown' ? 'selected' : '' }}>Breakdown / Rusak</option>
                                    <option value="Standby" {{ request('status') == 'Standby' ? 'selected' : '' }}>Standby</option>
                                    <option value="Rolling" {{ request('status') == 'Rolling' ? 'selected' : '' }}>Rolling / Dipinjam Kebun Lain</option>
                                </select>
                            </div>
                            <div class="col-12 col-sm-6 col-md-3">
                                <label class="form-label">Kata Kunci</label>
                                <div class="input-group">
                                    <input type="text" name="search" class="form-control" placeholder="Kode / Nama / Merk..." value="{{ request('search') }}">
                                    <button type="submit" class="btn btn-primary"><i class="feather-search"></i></button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-12">
                <div class="card stretch stretch-full">
                    <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <h5 class="card-title m-0">Daftar Alat Berat Pengolahan Limbah</h5>
                        <span class="badge bg-soft-primary text-primary fs-12">Total: {{ $alatBerat->total() }} Unit</span>
                    </div>
                    <div class="card-body custom-table-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover table-striped mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th style="min-width: 50px;">No</th>
                                        <th style="min-width: 80px;">PKS</th>
                                        <th style="min-width: 100px;">Kode Alat</th>
                                        <th style="min-width: 150px;">Nama Alat Berat</th>
                                        <th style="min-width: 120px;">Jenis Alat</th>
                                        <th style="min-width: 120px;">Merk / Tipe</th>
                                        <th style="min-width: 80px;">Tahun</th>
                                        <th style="min-width: 130px;">Status</th>
                                        <th style="min-width: 150px;">Keterangan</th>
                                        <th class="text-end" style="min-width: 100px;">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($alatBerat as $index => $item)
                                        <tr>
                                            <td>{{ $alatBerat->firstItem() + $index }}</td>
                                            <td><span class="fw-bold text-dark">{{ $item->pks ? $item->pks->akro : '-' }}</span></td>
                                            <td><code class="fw-bold text-primary">{{ $item->kode_alat }}</code></td>
                                            <td class="fw-semibold">{{ $item->nama_alat }}</td>
                                            <td><span class="badge bg-soft-info text-info">{{ $item->jenis_alat }}</span></td>
                                            <td>{{ $item->merk_tipe ?? '-' }}</td>
                                            <td>{{ $item->tahun_pengadaan ?? '-' }}</td>
                                            <td>
                                                @if($item->status == 'Operational')
                                                    <span class="badge bg-success">Ready / Operational</span>
                                                @elseif($item->status == 'Maintenance')
                                                    <span class="badge bg-warning text-dark">Maintenance</span>
                                                @elseif($item->status == 'Breakdown')
                                                    <span class="badge bg-danger">Breakdown</span>
                                                @elseif($item->status == 'Rolling')
                                                    <span class="badge bg-info text-dark">Rolling / Dipinjam Kebun Lain</span>
                                                @else
                                                    <span class="badge bg-secondary">Standby</span>
                                                @endif
                                            </td>
                                            <td class="text-truncate" style="max-width: 200px;">{{ $item->keterangan ?? '-' }}</td>
                                            <td class="text-end">
                                                <div class="btn-group btn-group-sm">
                                                    <a href="{{ route('alat-berat.edit', $item->id) }}" class="btn btn-outline-warning" title="Edit">
                                                        <i class="feather-edit-2"></i>
                                                    </a>
                                                    <form action="{{ route('alat-berat.destroy', $item->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data alat berat ini?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-outline-danger" title="Hapus">
                                                            <i class="feather-trash-2"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="10" class="text-center py-5 text-muted">
                                                <i class="feather-inbox fs-3 d-block mb-2"></i>
                                                Belum ada data master alat berat.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer d-flex flex-column flex-sm-row justify-content-between align-items-center gap-2">
                        <div>
                            Menampilkan {{ $alatBerat->firstItem() ?? 0 }} - {{ $alatBerat->lastItem() ?? 0 }} dari {{ $alatBerat->total() }} data
                        </div>
                        <div>
                            {{ $alatBerat->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
@endsection
