@extends('layouts.simoli')

@section('title', 'Input Data Pemeliharaan')
@section('page-title', 'Data Pemeliharaan')

@section('breadcrumb')
    <li class="breadcrumb-item">Input Data</li>
    <li class="breadcrumb-item active">Pemeliharaan</li>
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

        .badge-jenis {
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

        .photo-thumb {
            width: 42px;
            height: 42px;
            object-fit: cover;
            border-radius: 8px;
            border: 2px solid #e9ecef;
            cursor: pointer;
            transition: transform 0.2s ease, border-color 0.2s ease;
        }

        .photo-thumb:hover {
            transform: scale(1.15);
            border-color: #6366f1;
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

        .filter-card .form-control,
        .filter-card .form-select,
        .filter-card .select2-container .select2-selection--single {
            height: 42px;
        }

        .filter-card .filter-btn {
            height: 42px;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0 12px;
        }

        /* kecilkan teks dalam filter */
        .filter-card .form-control,
        .filter-card .form-select {
            font-size: 12px;
        }

        /* kecilkan label juga */
        .filter-card .form-label {
            font-size: 11px;
            margin-bottom: 4px;
        }

        /* rapatkan jarak atas dan tombol */
        .filter-card .row {
            row-gap: 8px !important;
        }

        /* tombol filter reset */
        .filter-card .filter-btn {
            height: 40px;
            padding: 0 10px;
        }

        /* khusus kolom tombol biar naik dikit */
        .filter-card .btn-wrapper {
            margin-top: -2px;
        }

        .table-row-click:hover {
            background-color: #f8f9ff;
            transition: 0.2s;
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
            <a href="{{ route('pemeliharaan.create') }}" class="btn btn-primary">
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
        <div class="col-xxl-2 col-md-4 col-6">
            <div class="card stat-card shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <p class="text-muted fs-12 fw-medium mb-1">Total Data</p>
                            <h3 class="fw-bold mb-0">{{ number_format($totalRecords) }}</h3>
                            <span class="fs-11 text-muted">record pemeliharaan</span>
                        </div>
                        <div class="stat-icon bg-soft-primary text-primary">
                            <i class="feather-database"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xxl-2 col-md-4 col-6">
            <div class="card stat-card shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <p class="text-muted fs-12 fw-medium mb-1">Total Flat Bed</p>
                            <h3 class="fw-bold mb-0">{{ number_format($totalFlatBed) }}</h3>
                            <span class="fs-11 text-muted">flat bed</span>
                        </div>
                        <div class="stat-icon bg-soft-success text-success">
                            <i class="feather-layers"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xxl-2 col-md-4 col-6">
            <div class="card stat-card shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <p class="text-muted fs-12 fw-medium mb-1">Total Long Bed</p>
                            <h3 class="fw-bold mb-0">{{ number_format($totalLongBed) }}</h3>
                            <span class="fs-11 text-muted">long bed</span>
                        </div>
                        <div class="stat-icon bg-soft-warning text-warning">
                            <i class="feather-maximize-2"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xxl-2 col-md-4 col-6">
            <div class="card stat-card shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <p class="text-muted fs-12 fw-medium mb-1">Total HK</p>
                            <h3 class="fw-bold mb-0">{{ number_format($totalHK) }}</h3>
                            <span class="fs-11 text-muted">hari kerja</span>
                        </div>
                        <div class="stat-icon bg-soft-info text-info">
                            <i class="feather-users"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xxl-2 col-md-4 col-6">
            <div class="card stat-card shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <p class="text-muted fs-12 fw-medium mb-1">Mekanis</p>
                            <h3 class="fw-bold mb-0">{{ number_format($totalMekanis) }}</h3>
                            <span class="fs-11 text-muted">record</span>
                        </div>
                        <div class="stat-icon" style="background: rgba(99,102,241,0.12); color: #6366f1;">
                            <i class="feather-settings"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xxl-2 col-md-4 col-6">
            <div class="card stat-card shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <p class="text-muted fs-12 fw-medium mb-1">Manual</p>
                            <h3 class="fw-bold mb-0">{{ number_format($totalManual) }}</h3>
                            <span class="fs-11 text-muted">record</span>
                        </div>
                        <div class="stat-icon" style="background: rgba(20,184,166,0.12); color: #14b8a6;">
                            <i class="feather-user"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Filter Card --}}
    <div class="card filter-card mb-4 shadow-sm">
        <div class="card-body py-3">
            <form action="{{ route('pemeliharaan.index') }}" method="GET">
                <div class="row g-3 align-items-end">

                    @if(Auth::user()->isAdmin())
                        <div class="col-lg-3 col-md-6">
                            <label class="form-label fs-12 fw-semibold text-muted">
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
                        <label class="form-label fs-12 fw-semibold text-muted">
                            <i class="feather-tool me-1"></i> Jenis
                        </label>
                        <select name="jenis" class="form-select">
                            <option value="">Semua</option>
                            <option value="1" {{ request('jenis') == '1' ? 'selected' : '' }}>Mekanis</option>
                            <option value="2" {{ request('jenis') == '2' ? 'selected' : '' }}>Manual</option>
                        </select>
                    </div>

                    <div class="col-lg-2 col-md-3">
                        <label class="form-label fs-12 fw-semibold text-muted">
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

                    <div class="col-lg-1 col-md-2">
                        <label class="form-label fs-12 fw-semibold text-muted">
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
                        <label class="form-label fs-12 fw-semibold text-muted">
                            Dari Tanggal
                        </label>
                        <input type="date" name="dari_tanggal" class="form-control" value="{{ request('dari_tanggal') }}">
                    </div>

                    <div class="col-lg-2 col-md-3">
                        <label class="form-label fs-12 fw-semibold text-muted">
                            Sampai Tanggal
                        </label>
                        <input type="date" name="sampai_tanggal" class="form-control"
                            value="{{ request('sampai_tanggal') }}">
                    </div>

                    <div class="col-lg-auto btn-wrapper">
                        <label class="form-label d-block">&nbsp;</label>
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary btn-sm">
                                <i class="feather-filter"></i>
                            </button>

                            <a href="{{ route('pemeliharaan.index') }}" class="btn btn-light btn-sm">
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
                        <i class="feather-tool me-2 text-primary"></i>
                        Daftar Data Pemeliharaan
                    </h6>
                    <small class="text-muted">Menampilkan {{ $pemeliharaan->firstItem() ?? 0 }} -
                        {{ $pemeliharaan->lastItem() ?? 0 }} dari {{ $pemeliharaan->total() }} data</small>
                </div>
                <div class="d-flex gap-2">
                    <span class="badge bg-soft-primary text-primary fs-12 px-3 py-2 ms-3">
                        <i class="feather-database me-1"></i> {{ $pemeliharaan->total() }} Total
                    </span>
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            @if($pemeliharaan->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th class="ps-4">#</th>
                                <th>Tanggal</th>
                                <th>PKS</th>
                                <th>Jenis</th>
                                <th class="text-center">Detail</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($pemeliharaan as $index => $item)
                                <tr class="table-row-click" onclick="window.location='{{ route('pemeliharaan.show', $item->id) }}'"
                                    style="cursor:pointer">

                                    <td class="ps-4 fw-medium text-muted">
                                        {{ $pemeliharaan->firstItem() + $index }}
                                    </td>

                                    <td>
                                        <div class="fw-semibold">
                                            {{ $item->tanggal ? $item->tanggal->format('d M Y') : '-' }}
                                        </div>
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
                                        @if($item->jenis_pemeliharaan == '1')
                                            <span class="badge bg-soft-info text-info">
                                                <i class="feather-settings me-1"></i> Mekanis
                                            </span>
                                        @elseif($item->jenis_pemeliharaan == '2')
                                            <span class="badge bg-soft-warning text-warning">
                                                <i class="feather-user me-1"></i> Manual
                                            </span>
                                        @else
                                            -
                                        @endif
                                    </td>

                                    <td class="text-center">
                                        <a href="{{ route('pemeliharaan.show', $item->id) }}" class="btn btn-sm btn-soft-info">
                                            <i class="feather-eye"></i>
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
                            Halaman {{ $pemeliharaan->currentPage() }} dari {{ $pemeliharaan->lastPage() }}
                        </small>

                        <!-- Pembungkus khusus untuk memanipulasi elemen internal Laravel -->
                        <div class="custom-paging">
                            {{ $pemeliharaan->links('pagination::bootstrap-5') }}
                        </div>

                    </div>
                </div>
            @else
                <div class="empty-state">
                    <i class="feather-inbox d-block"></i>
                    <h5 class="text-muted">Belum Ada Data</h5>
                    <p class="text-muted mb-3">Data pemeliharaan belum tersedia atau tidak sesuai filter yang dipilih.</p>
                    <a href="{{ route('pemeliharaan.create') }}" class="btn btn-primary">
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

        // Photo preview with SweetAlert2
        function showPhoto(url, title) {
            Swal.fire({
                title: title,
                imageUrl: url,
                imageAlt: title,
                imageWidth: 500,
                showConfirmButton: false,
                showCloseButton: true,
                customClass: {
                    image: 'rounded-3'
                }
            });
        }
    </script>
@endsection