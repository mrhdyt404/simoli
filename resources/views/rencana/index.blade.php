@extends('layouts.simoli')

@section('title', 'Rencana Pengaliran & Pemeliharaan')
@section('page-title', 'Rencana Pengaliran & Pemeliharaan')

@section('breadcrumb')
<li class="breadcrumb-item">Input Data</li>
<li class="breadcrumb-item active">Rencana</li>
@endsection

@section('styles')
<link rel="stylesheet" type="text/css" href="{{ asset('duraluxadmin/assets/vendors/css/dataTables.bs5.min.css') }}" />
<link rel="stylesheet" type="text/css" href="{{ asset('duraluxadmin/assets/vendors/css/select2.min.css') }}" />
<link rel="stylesheet" type="text/css" href="{{ asset('duraluxadmin/assets/vendors/css/select2-theme.min.css') }}" />
<link rel="stylesheet" type="text/css" href="{{ asset('duraluxadmin/assets/vendors/css/sweetalert2.min.css') }}" />
<style>
    .stat-card {
        border: none;
        border-radius: 12px;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .stat-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
    }
    .stat-icon {
        width: 52px;
        height: 52px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 22px;
    }
    .filter-card {
        border: 1px solid #e9ecef;
        border-radius: 12px;
        background: linear-gradient(135deg, #f8f9ff 0%, #ffffff 100%);
    }
    .table-card {
        border: none;
        border-radius: 12px;
        overflow: hidden;
    }
    .badge-pks {
        font-size: 11px;
        padding: 4px 10px;
        border-radius: 6px;
        font-weight: 600;
    }
    .badge-year {
        font-size: 11px;
        padding: 4px 12px;
        border-radius: 20px;
        font-weight: 700;
    }
    .btn-action {
        width: 32px;
        height: 32px;
        padding: 0;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
        font-size: 14px;
    }
    .table thead th {
        background: #f0f3ff;
        border: none;
        font-weight: 600;
        font-size: 12px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #5b6b8a;
        white-space: nowrap;
    }
    .table tbody td {
        vertical-align: middle;
        font-size: 13px;
        border-bottom: 1px solid #f0f3f5;
    }
    .table tbody tr:hover {
        background-color: #f8f9ff;
    }
    .bed-bar {
        height: 8px;
        border-radius: 4px;
        background: #e9ecef;
        overflow: hidden;
        min-width: 80px;
    }
    .bed-bar-fill {
        height: 100%;
        border-radius: 4px;
        transition: width 0.4s ease;
    }
    .empty-state {
        padding: 60px 20px;
        text-align: center;
    }
    .empty-state i {
        font-size: 64px;
        color: #d1d5db;
        margin-bottom: 16px;
    }
</style>
@endsection

@section('page-actions')
<div class="page-header-right-items">
    <div class="d-flex d-md-none">
        <a href="javascript:void(0)" class="page-header-right-close-toggle">
            <i class="feather-arrow-left me-2"></i>
            <span>Back</span>
        </a>
    </div>
    <div class="d-flex align-items-center gap-2 page-header-right-items-wrapper">
        <a href="{{ route('rencana.create') }}" class="btn btn-primary">
            <i class="feather-plus me-2"></i>
            <span>Tambah Rencana</span>
        </a>
    </div>
</div>
<div class="d-md-none d-flex align-items-center">
    <a href="javascript:void(0)" class="page-header-right-open-toggle">
        <i class="feather-align-right fs-20"></i>
    </a>
</div>
@endsection

@section('content')
{{-- Summary Cards --}}
<div class="row mb-4">
    <div class="col-xxl-3 col-md-6">
        <div class="card stat-card shadow-sm">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <p class="text-muted fs-12 fw-medium mb-1">Total Rencana</p>
                        <h3 class="fw-bold mb-0">{{ number_format($totalRecords) }}</h3>
                        <span class="fs-11 text-muted">record rencana</span>
                    </div>
                    <div class="stat-icon bg-soft-primary text-primary">
                        <i class="feather-clipboard"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xxl-3 col-md-6">
        <div class="card stat-card shadow-sm">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <p class="text-muted fs-12 fw-medium mb-1">Total Flat Bed</p>
                        <h3 class="fw-bold mb-0">{{ number_format($totalFlatBed) }}</h3>
                        <span class="fs-11 text-muted">flat bed direncanakan</span>
                    </div>
                    <div class="stat-icon bg-soft-success text-success">
                        <i class="feather-layers"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xxl-3 col-md-6">
        <div class="card stat-card shadow-sm">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <p class="text-muted fs-12 fw-medium mb-1">Total Long Bed</p>
                        <h3 class="fw-bold mb-0">{{ number_format($totalLongBed) }}</h3>
                        <span class="fs-11 text-muted">long bed direncanakan</span>
                    </div>
                    <div class="stat-icon bg-soft-warning text-warning">
                        <i class="feather-maximize-2"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xxl-3 col-md-6">
        <div class="card stat-card shadow-sm">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <p class="text-muted fs-12 fw-medium mb-1">Unit PKS</p>
                        <h3 class="fw-bold mb-0">{{ number_format($totalPks) }}</h3>
                        <span class="fs-11 text-muted">PKS terdaftar</span>
                    </div>
                    <div class="stat-icon bg-soft-info text-info">
                        <i class="feather-home"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Filter Card --}}
<div class="card filter-card mb-4 shadow-sm">
    <div class="card-body py-3">
        <form action="{{ route('rencana.index') }}" method="GET">
            <div class="row g-3 align-items-end">
                @if(Auth::user()->isAdmin())
                <div class="col-md-4">
                    <label class="form-label fs-12 fw-semibold text-muted">
                        <i class="feather-home me-1"></i> Unit PKS
                    </label>
                    <select name="id_pks" class="form-control" data-select2-selector="status">
                        <option value="">Semua PKS</option>
                        @foreach($pksList as $pks)
                        <option value="{{ $pks->ID }}" {{ request('id_pks') == $pks->ID ? 'selected' : '' }}>
                            {{ $pks->nama }} ({{ $pks->akro }})
                        </option>
                        @endforeach
                    </select>
                </div>
                @endif
                <div class="col-md-3">
                    <label class="form-label fs-12 fw-semibold text-muted">
                        <i class="feather-calendar me-1"></i> Tahun
                    </label>
                    <select name="tahun" class="form-select">
                        <option value="">Semua Tahun</option>
                        @foreach($years as $y)
                        <option value="{{ $y }}" {{ request('tahun') == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endforeach
                        @if(!$years->contains(date('Y')))
                        <option value="{{ date('Y') }}" {{ request('tahun') == date('Y') ? 'selected' : '' }}>{{ date('Y') }}</option>
                        @endif
                    </select>
                </div>
                <div class="col-md-3">
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary" title="Filter">
                            <i class="feather-filter me-1"></i> Filter
                        </button>
                        <a href="{{ route('rencana.index') }}" class="btn btn-outline-secondary" title="Reset">
                            <i class="feather-refresh-cw me-1"></i> Reset
                        </a>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- Data Table --}}
<div class="card table-card shadow-sm">
    <div class="card-header border-0 py-3">
        <div class="d-flex align-items-center justify-content-between">
            <div>
                <h6 class="mb-0 fw-bold">
                    <i class="feather-clipboard me-2 text-primary"></i>
                    Daftar Rencana Pengaliran & Pemeliharaan
                </h6>
                <small class="text-muted">Menampilkan {{ $rencana->firstItem() ?? 0 }} - {{ $rencana->lastItem() ?? 0 }} dari {{ $rencana->total() }} data</small>
            </div>
            <div class="d-flex gap-2">
                <span class="badge bg-soft-primary text-primary fs-12 px-3 py-2 ms-3">
                    <i class="feather-database me-1"></i> {{ $rencana->total() }} Total
                </span>
            </div>
        </div>
    </div>
    <div class="card-body p-0">
        @if($rencana->count() > 0)
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th class="ps-4">#</th>
                        <th>Tahun</th>
                        <th>PKS</th>
                        <th>Flat Bed</th>
                        <th>Long Bed</th>
                        <th>Total Bed</th>
                        <th>Visualisasi</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $maxBed = max($rencana->max('flat_bed'), $rencana->max('long_bed'), 1);
                    @endphp
                    @foreach($rencana as $index => $item)
                    <tr>
                        <td class="ps-4 fw-medium text-muted">{{ $rencana->firstItem() + $index }}</td>
                        <td>
                            <span class="badge badge-year bg-soft-primary text-primary">{{ $item->tahun }}</span>
                        </td>
                        <td>
                            @if($item->pks)
                            <div>
                                <span class="badge badge-pks bg-soft-primary text-primary">{{ $item->pks->akro }}</span>
                                <div class="fs-11 text-muted mt-1">{{ $item->pks->nama }}</div>
                            </div>
                            @else
                            <span class="badge badge-pks bg-soft-secondary text-secondary">N/A</span>
                            @endif
                        </td>
                        <td>
                            <span class="fw-bold text-success">{{ number_format($item->flat_bed) }}</span>
                        </td>
                        <td>
                            <span class="fw-bold text-warning">{{ number_format($item->long_bed) }}</span>
                        </td>
                        <td>
                            <span class="fw-bold">{{ number_format($item->flat_bed + $item->long_bed) }}</span>
                        </td>
                        <td style="min-width: 180px;">
                            @php
                                $fbPct = min(100, ($item->flat_bed / max(1, $maxBed)) * 100);
                                $lbPct = min(100, ($item->long_bed / max(1, $maxBed)) * 100);
                            @endphp
                            <div class="d-flex align-items-center gap-2 mb-1">
                                <small class="text-muted" style="width: 20px;">FB</small>
                                <div class="bed-bar flex-grow-1">
                                    <div class="bed-bar-fill" style="width: {{ $fbPct }}%; background: linear-gradient(90deg, #10b981, #34d399);"></div>
                                </div>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <small class="text-muted" style="width: 20px;">LB</small>
                                <div class="bed-bar flex-grow-1">
                                    <div class="bed-bar-fill" style="width: {{ $lbPct }}%; background: linear-gradient(90deg, #f59e0b, #fbbf24);"></div>
                                </div>
                            </div>
                        </td>
                        <td class="text-center">
                            <div class="d-flex gap-1 justify-content-center">
                                <a href="{{ route('rencana.edit', $item->id) }}" class="btn btn-sm btn-action btn-soft-primary" title="Edit">
                                    <i class="feather-edit-2"></i>
                                </a>
                                @if(Auth::user()->isAdmin())
                                <form action="{{ route('rencana.destroy', $item->id) }}" method="POST" class="d-inline form-delete">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-action btn-soft-danger btn-delete" title="Hapus">
                                        <i class="feather-trash-2"></i>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div class="card-footer border-0 py-3">
            <div class="d-flex align-items-center justify-content-between">
                <small class="text-muted">
                    Halaman {{ $rencana->currentPage() }} dari {{ $rencana->lastPage() }}
                </small>
                {{ $rencana->links('pagination::bootstrap-5') }}
            </div>
        </div>
        @else
        <div class="empty-state">
            <i class="feather-inbox d-block"></i>
            <h5 class="text-muted">Belum Ada Data Rencana</h5>
            <p class="text-muted mb-3">Data rencana pengaliran & pemeliharaan belum tersedia.</p>
            <a href="{{ route('rencana.create') }}" class="btn btn-primary">
                <i class="feather-plus me-2"></i> Tambah Rencana Pertama
            </a>
        </div>
        @endif
    </div>
</div>
@endsection

@section('scripts')
<script src="{{ asset('duraluxadmin/assets/vendors/js/select2.min.js') }}"></script>
<script src="{{ asset('duraluxadmin/assets/vendors/js/select2-active.min.js') }}"></script>
<script src="{{ asset('duraluxadmin/assets/vendors/js/sweetalert2.min.js') }}"></script>
<script>
    // Delete confirmation with SweetAlert2
    document.querySelectorAll('.btn-delete').forEach(function(btn) {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const form = this.closest('form');
            Swal.fire({
                title: 'Hapus Data Rencana?',
                text: 'Data yang dihapus tidak dapat dikembalikan!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal',
                reverseButtons: true
            }).then((result) => {
                if (result.value) {
                    form.submit();
                }
            });
        });
    });
</script>
@endsection
