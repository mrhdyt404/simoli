@extends('layouts.simoli')

@section('title', 'Input Data Pengaliran')
@section('page-title', 'Data Pengaliran')

@section('breadcrumb')
    <li class="breadcrumb-item">Input Data</li>
    <li class="breadcrumb-item active">Pengaliran</li>
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

        .volume-bar {
            height: 6px;
            border-radius: 3px;
            background: #e9ecef;
            overflow: hidden;
        }

        .volume-bar-fill {
            height: 100%;
            border-radius: 3px;
            transition: width 0.3s ease;
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

        /* Samakan tinggi semua input filter */
        .filter-card .form-control,
        .filter-card .form-select {
            height: 42px;
            font-size: 13px;
        }

        /* Select2 fix */
        .filter-card .select2-container--default .select2-selection--single {
            height: 42px;
            padding: 6px 10px;
            border: 1px solid #e5e7eb;
        }

        .filter-card .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 28px;
        }

        .filter-card .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 40px;
        }

        /* tombol */
        .filter-card .btn {
            height: 42px;
            font-size: 13px;
        }

        select[name="tahun"] {
            font-size: 12px;
            padding-left: 8px;
            padding-right: 8px;
        }
    </style>
    <style>
        .custom-paging>nav>div.d-flex {
            gap: 25px !important;
        }

        .custom-paging .page-link {
            padding: 0.25rem 0.5rem !important;
            font-size: 0.85rem !important;
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
            <a href="{{ route('pengaliran.create') }}" class="btn btn-primary">
                <i class="feather-plus me-2"></i>
                <span>Tambah Data</span>
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
        <div class="col-xxl col-md-4 col-6">
            <div class="card stat-card shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <p class="text-muted fs-12 fw-medium mb-1">Total Data</p>
                            <h3 class="fw-bold mb-0">{{ number_format($totalRecords) }}</h3>
                            <span class="fs-11 text-muted">record pengaliran</span>
                        </div>
                        <div class="stat-icon bg-soft-primary text-primary">
                            <i class="feather-database"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xxl col-md-4 col-6">
            <div class="card stat-card shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <p class="text-muted fs-12 fw-medium mb-1">Vol. Dihasilkan</p>
                            <h3 class="fw-bold mb-0">{{ number_format($totalVolDihasilkan) }}</h3>
                            <span class="fs-11 text-muted">m&sup3; total</span>
                        </div>
                        <div class="stat-icon bg-soft-info text-info">
                            <i class="feather-trending-up"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xxl col-md-4 col-6">
            <div class="card stat-card shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <p class="text-muted fs-12 fw-medium mb-1">Vol. Dialirkan</p>
                            <h3 class="fw-bold mb-0">{{ number_format($totalVolDialirkan) }}</h3>
                            <span class="fs-11 text-muted">m&sup3; total</span>
                        </div>
                        <div class="stat-icon bg-soft-success text-success">
                            <i class="feather-droplet"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xxl col-md-6 col-6">
            <div class="card stat-card shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <p class="text-muted fs-12 fw-medium mb-1">Total Flat Bed</p>
                            <h3 class="fw-bold mb-0">{{ number_format($totalFlatBed) }}</h3>
                            <span class="fs-11 text-muted">flat bed</span>
                        </div>
                        <div class="stat-icon bg-soft-warning text-warning">
                            <i class="feather-layers"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xxl col-md-6 col-6">
            <div class="card stat-card shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <p class="text-muted fs-12 fw-medium mb-1">Luas Area</p>
                            <h3 class="fw-bold mb-0">{{ number_format($totalLuasArea, 1) }}</h3>
                            <span class="fs-11 text-muted">Ha total</span>
                        </div>
                        <div class="stat-icon bg-soft-danger text-danger">
                            <i class="feather-map"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Filter Card --}}
    <div class="card filter-card mb-4 shadow-sm">
        <div class="card-body py-3">
            <form action="{{ route('pengaliran.index') }}" method="GET">
                <div class="row g-3 align-items-end">

                    @if(Auth::user()->isAdmin())
                        <div class="col-lg-3 col-md-6">
                            <label class="form-label fs-12 fw-semibold text-muted mb-1">
                                <i class="feather-home me-1"></i> Unit PKS
                            </label>
                            <select name="id_pks" class="form-select" data-select2-selector="status">
                                <option value="">Semua PKS</option>
                                @foreach($pksList as $pks)
                                    <option value="{{ $pks->ID }}" {{ request('id_pks') == $pks->ID ? 'selected' : '' }}>
                                        {{ $pks->nama }} ({{ $pks->akro }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    @endif

                    <div class="col-lg-2 col-md-3">
                        <label class="form-label fs-12 fw-semibold text-muted mb-1">
                            <i class="feather-calendar me-1"></i> Bulan
                        </label>
                        <select name="bulan" class="form-select">
                            <option value="">Semua</option>
                            @foreach(['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'] as $i => $bln)
                                <option value="{{ $i + 1 }}" {{ request('bulan') == ($i + 1) ? 'selected' : '' }}>
                                    {{ $bln }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-lg-1 col-md-3">
                        <label class="form-label fs-12 fw-semibold text-muted mb-1">
                            <i class="feather-hash me-1"></i> Tahun
                        </label>
                        <select name="tahun" class="form-select form-select-sm">
                            <option value="">Semua</option>
                            @for($y = date('Y'); $y >= 2020; $y--)
                                <option value="{{ $y }}" {{ request('tahun') == $y ? 'selected' : '' }}>
                                    {{ $y }}
                                </option>
                            @endfor
                        </select>
                    </div>

                    <div class="col-lg-2 col-md-3">
                        <label class="form-label fs-12 fw-semibold text-muted mb-1">
                            Dari Tanggal
                        </label>
                        <input type="date" name="dari_tanggal" class="form-control" value="{{ request('dari_tanggal') }}">
                    </div>

                    <div class="col-lg-2 col-md-3">
                        <label class="form-label fs-12 fw-semibold text-muted mb-1">
                            Sampai Tanggal
                        </label>
                        <input type="date" name="sampai_tanggal" class="form-control"
                            value="{{ request('sampai_tanggal') }}">
                    </div>

                    <div class="col-lg-1 col-md-12">
                        <label class="form-label d-block mb-1">&nbsp;</label>
                        <div class="d-flex align-items-end gap-2">
                            <button type="submit" class="btn btn-primary btn-sm px-3">
                                <i class="feather-filter"></i>
                            </button>

                            <a href="{{ route('pengaliran.index') }}" class="btn btn-light btn-sm px-3">
                                <i class="feather-refresh-cw"></i>
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
                        <i class="feather-list me-2 text-primary"></i>
                        Daftar Data Pengaliran
                    </h6>
                    <small class="text-muted">Menampilkan {{ $pengaliran->firstItem() ?? 0 }} -
                        {{ $pengaliran->lastItem() ?? 0 }} dari {{ $pengaliran->total() }} data</small>
                </div>
                <div class="d-flex gap-2">
                    <span class="badge bg-soft-primary text-primary fs-12 px-3 py-2 ms-3">
                        <i class="feather-database me-1"></i> {{ $pengaliran->total() }} Total
                    </span>
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            @if($pengaliran->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th class="ps-4">#</th>
                                <th>Tanggal</th>
                                <th>PKS</th>
                                <th>Jam</th>
                                <th>Volume</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($pengaliran as $index => $item)
                                <tr class="table-row-click"
                                    onclick="window.location='{{ route('pengaliran.show', $item->id_pengaliran) }}'"
                                    style="cursor:pointer">

                                    <td class="ps-4 fw-medium text-muted">
                                        {{ $pengaliran->firstItem() + $index }}
                                    </td>

                                    <td>
                                        <div class="fw-semibold">
                                            {{ $item->tanggal ? $item->tanggal->format('d M Y') : '-' }}
                                        </div>
                                        <small class="text-muted">
                                            {{ $item->blok ?? '-' }}
                                        </small>
                                    </td>

                                    <td>
                                        @if($item->pks)
                                            <span class="badge bg-soft-primary text-primary">
                                                {{ $item->pks->AKRO }}
                                            </span>
                                        @else
                                            <span class="badge bg-soft-secondary text-secondary">
                                                N/A
                                            </span>
                                        @endif
                                    </td>

                                    <td>
                                        <small>
                                            <i class="feather-clock me-1"></i>
                                            {{ $item->jam_mulai ? substr($item->jam_mulai, 0, 5) : '-' }}
                                            -
                                            {{ $item->jam_selesai ? substr($item->jam_selesai, 0, 5) : '-' }}
                                        </small>
                                    </td>

                                    <td>
                                        <div class="fw-bold text-success">
                                            {{ number_format($item->vol_limbah_dialirkan) }}
                                            <span class="fs-11 text-muted">m³</span>
                                        </div>

                                        <small class="text-muted">
                                            Flat: {{ $item->flat_bed ?? 0 }}
                                        </small>
                                    </td>

                                    <td class="text-center" onclick="event.stopPropagation();">
                                        <a href="{{ route('pengaliran.show', $item->id_pengaliran) }}"
                                            class="btn btn-sm btn-soft-info">
                                            <i class="feather-eye"></i>
                                        </a>

                                        <a href="{{ route('pengaliran.edit', $item->id_pengaliran) }}"
                                            class="btn btn-sm btn-soft-primary">
                                            <i class="feather-edit-2"></i>
                                        </a>
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
                            Halaman {{ $pengaliran->currentPage() }} dari {{ $pengaliran->lastPage() }}
                        </small>
                        <div class="custom-paging">
                            {{ $pengaliran->links('pagination::bootstrap-5') }}
                        </div>
                    </div>
                </div>
            @else
                <div class="empty-state">
                    <i class="feather-inbox d-block"></i>
                    <h5 class="text-muted">Belum Ada Data</h5>
                    <p class="text-muted mb-3">Data pengaliran belum tersedia atau tidak sesuai filter yang dipilih.</p>
                    <a href="{{ route('pengaliran.create') }}" class="btn btn-primary">
                        <i class="feather-plus me-2"></i> Tambah Data Pertama
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
        document.querySelectorAll('.btn-delete').forEach(function (btn) {
            btn.addEventListener('click', function (e) {
                e.preventDefault();
                const form = this.closest('form');
                Swal.fire({
                    title: 'Hapus Data?',
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