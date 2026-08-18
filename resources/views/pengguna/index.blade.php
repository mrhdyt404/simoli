@extends('layouts.simoli')

@section('title', 'Data Pengguna')
@section('page-title', 'Data Pengguna')

@section('breadcrumb')
    <li class="breadcrumb-item">Input Data</li>
    <li class="breadcrumb-item active">Data Pengguna</li>
@endsection


@section('styles')
    <style>
        .user-stat-card {
            border: none;
            border-radius: 14px;
            box-shadow: 0 6px 20px rgba(15, 23, 42, 0.06);
        }

        .user-stat-card .card-body {
            padding: 18px;
        }

        .user-table thead th {
            background: #f8fafc;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #64748b;
            border-bottom: 0;
        }

        .user-table td {
            vertical-align: middle;
        }

        .user-badge {
            font-size: 11px;
            font-weight: 700;
            padding: 5px 10px;
            border-radius: 999px;
        }

        .user-action-btn {
            width: 34px;
            height: 34px;
            padding: 0;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 10px;
        }
    </style>
@endsection

@section('page-actions')
    <div class="page-header-right-items">
        <div class="d-flex d-md-none">
            <a href="javascript:void(0)" class="page-header-right-close-toggle">
                <i class="feather-arrow-left me-2"></i><span>Back</span>
            </a>
        </div>
        <div class="d-flex align-items-center gap-2 page-header-right-items-wrapper">
            <a href="{{ route('pengguna.create') }}" class="btn btn-primary">
                <i class="feather-plus me-2"></i>Tambah Data
            </a>
        </div>
    </div>
    <div class="d-md-none d-flex align-items-center">
        <a href="javascript:void(0)" class="page-header-right-open-toggle"><i class="feather-align-right fs-20"></i></a>
    </div>
@endsection

@section('content')
    <div class="row g-3 mb-4">
        <div class="col-12 col-md-4">
            <div class="card user-stat-card">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted fs-12 mb-1">Total Pengguna</div>
                        <h3 class="mb-0 fw-bold">{{ $totalUsers }}</h3>
                    </div>
                    <div class="stat-icon bg-primary-soft text-primary"><i class="feather-users"></i></div>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-4">
            <div class="card user-stat-card">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted fs-12 mb-1">Admin</div>
                        <h3 class="mb-0 fw-bold">{{ $totalAdmin }}</h3>
                    </div>
                    <div class="stat-icon bg-warning-soft text-warning"><i class="feather-shield"></i></div>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-4">
            <div class="card user-stat-card">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted fs-12 mb-1">Unit</div>
                        <h3 class="mb-0 fw-bold">{{ $totalUnit }}</h3>
                    </div>
                    <div class="stat-icon bg-success-soft text-success"><i class="feather-home"></i></div>
                </div>
            </div>
        </div>
    </div>

    <div class="card stretch stretch-full mb-4">
        <div class="card-body">
            <form action="{{ route('pengguna.index') }}" method="GET" class="row g-3 align-items-end">
                <div class="col-lg-5">
                    <label class="form-label fw-semibold">Cari</label>
                    <input type="text" name="q" class="form-control" value="{{ request('q') }}"
                        placeholder="Nama, username, kode, atau AKRO">
                </div>
                <div class="col-lg-3">
                    <label class="form-label fw-semibold">Level Akses</label>
                    <select name="level_akses" class="form-select">
                        <option value="">Semua</option>
                        <option value="admin" {{ request('level_akses') === 'admin' ? 'selected' : '' }}>Admin</option>
                        <option value="unit" {{ request('level_akses') === 'unit' ? 'selected' : '' }}>Unit</option>
                    </select>
                </div>
                <div class="col-lg-4 d-flex gap-2">
                    <button type="submit" class="btn btn-primary flex-grow-1"><i
                            class="feather-search me-1"></i>Filter</button>
                    <a href="{{ route('pengguna.index') }}" class="btn btn-light-brand"><i
                            class="feather-refresh-cw"></i></a>
                </div>
            </form>
        </div>
    </div>

    <div class="card stretch stretch-full">
        <div class="card-header d-flex align-items-center justify-content-between">
            <h5 class="card-title mb-0">Daftar Pengguna</h5>
            <span class="badge bg-soft-primary text-primary">{{ $users->total() }} Data</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover user-table mb-0">
                    <thead>
                        <tr>
                            <th>Kode</th>
                            <th>Unit PKS</th>
                            <th>Username</th>
                            <th>Level Akses</th>
                            <th class="text-center pe-4" style="width:130px">
                                Aksi
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $item)
                            <tr>

                                <td class="fw-semibold">
                                    {{ $item->pks->kode ?? '-' }}
                                </td>

                                <td>
                                    <div class="fw-semibold">
                                        {{ $item->pks->nama ?? '-' }}
                                    </div>
                                    <small class="text-muted">
                                        {{ $item->pks->akro ?? '-' }}
                                    </small>
                                </td>

                                <td>
                                    {{ $item->username }}
                                </td>

                                <td>
                                    @if($item->level_akses == 'admin')
                                        <span class="badge bg-soft-warning text-warning user-badge">
                                            Admin
                                        </span>
                                    @else
                                        <span class="badge bg-soft-success text-success user-badge">
                                            Unit
                                        </span>
                                    @endif
                                </td>

                                <td class="text-center pe-4">
                                    <div class="d-inline-flex gap-1">

                                        <a href="{{ route('pengguna.edit', $item->ID) }}"
                                            class="btn btn-soft-primary user-action-btn" title="Edit">
                                            <i class="feather-edit-2"></i>
                                        </a>

                                        <form action="{{ route('pengguna.destroy', $item->ID) }}" method="POST"
                                            class="delete-form">

                                            @csrf
                                            @method('DELETE')

                                            <button type="button" class="btn btn-soft-danger user-action-btn btn-delete"
                                                title="Hapus">
                                                <i class="feather-trash-2"></i>
                                            </button>

                                        </form>

                                    </div>
                                </td>

                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-5 text-muted">
                                    Belum ada data pengguna.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer bg-white">
            {{ $users->links() }}
        </div>
    </div>
    @section('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {

                document.querySelectorAll('.btn-delete').forEach(function (button) {

                    button.addEventListener('click', function () {

                        const form = this.closest('.delete-form');

                        Swal.fire({
                            title: 'Hapus Data Pengguna?',
                            text: 'Data pengguna yang dihapus tidak dapat dikembalikan.',
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#dc3545',
                            cancelButtonColor: '#6c757d',
                            confirmButtonText: '<i class="feather-trash-2 me-1"></i> Ya, Hapus',
                            cancelButtonText: 'Batal',
                            reverseButtons: true,
                            focusCancel: true
                        }).then((result) => {

                            if (result.isConfirmed) {
                                form.submit();
                            }

                        });

                    });

                });

            });
        </script>
    @endsection
@endsection