@extends('layouts.simoli')

@section('title', 'Master Data Alat Berat')
@section('page-title', 'Master Data Alat Berat')
@section('page-description', 'Manajemen Master Unit & Status Operasional Alat Berat Pengolahan Limbah')

@section('breadcrumb')
    <li>Input Data</li>
    <li class="separator">/</li>
    <li>Master Alat Berat</li>
@endsection

@section('page-actions')
    <a href="{{ route('alat-berat.create') }}" class="btn-ptpn btn-ptpn-primary">
        <i class="feather-plus" style="font-size:15px;"></i>
        <span>Tambah Alat Berat</span>
    </a>
@endsection

@section('styles')
<link rel="stylesheet" type="text/css" href="{{ asset('duraluxadmin/assets/vendors/css/select2.min.css') }}" />
<link rel="stylesheet" type="text/css" href="{{ asset('duraluxadmin/assets/vendors/css/select2-theme.min.css') }}" />
<style>
    /* ================================================================
       MASTER ALAT BERAT INDEX — PTPN GREEN THEME
       ================================================================ */
    @keyframes fadeUpCard {
        from { opacity:0; transform:translateY(14px); }
        to   { opacity:1; transform:translateY(0); }
    }

    /* === FILTER CARD === */
    .ab-filter {
        background: #ffffff;
        border-radius: 16px;
        border: 1px solid rgba(22,163,74,.15);
        box-shadow: 0 2px 10px rgba(22,163,74,.05);
        padding: 18px 20px;
        margin-bottom: 20px;
    }

    .ab-filter .form-control,
    .ab-filter .form-select {
        border-radius: 12px;
        border: 1.5px solid #e5e7eb;
        font-size: 13px;
        padding: 9px 14px;
        transition: all .2s ease;
        background: #f9fafb;
    }

    .ab-filter .form-control:focus,
    .ab-filter .form-select:focus {
        border-color: rgba(22,163,74,.5);
        background: #ffffff;
        box-shadow: 0 0 0 3px rgba(34,197,94,.08);
    }

    .ab-filter label { font-size:11.5px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.4px;margin-bottom:5px; }

    /* === DATA TABLE CARD === */
    .ab-table-card {
        background: #ffffff;
        border-radius: 18px;
        border: 1px solid rgba(22,163,74,.1);
        box-shadow: 0 2px 12px rgba(22,163,74,.06);
        overflow: hidden;
    }

    .ab-table-header {
        padding: 16px 20px;
        border-bottom: 1px solid rgba(22,163,74,.08);
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 10px;
    }

    .ab-table-title {
        font-family: 'Outfit', sans-serif;
        font-size: 15px;
        font-weight: 800;
        color: #14532d;
        display: flex;
        align-items: center;
        gap: 8px;
        margin: 0;
    }

    .ab-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
        font-size: clamp(11.5px, .65vw+8.5px, 13px);
    }

    .ab-table thead th {
        background: #052e16;
        color: #86efac;
        font-size: 10px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .6px;
        padding: 12px 16px;
        border: none;
        white-space: nowrap;
    }

    .ab-table tbody td {
        padding: 12px 16px;
        border-bottom: 1px solid rgba(22,163,74,.07);
        vertical-align: middle;
    }

    .ab-table tbody tr { transition: background .15s ease; }
    .ab-table tbody tr:hover td { background: rgba(22,163,74,.03); }
    .ab-table tbody tr:last-child td { border-bottom: none; }

    /* Action btns */
    .tbl-action { display:inline-flex;align-items:center;justify-content:center;width:32px;height:32px;border-radius:9px;border:none;cursor:pointer;transition:all .2s ease;font-size:14px; }
    .tbl-action-edit   { background:#eff6ff;color:#1d4ed8;border:1px solid #bfdbfe; }
    .tbl-action-delete { background:#fef2f2;color:#dc2626;border:1px solid #fca5a5; }
    .tbl-action-edit:hover   { background:#dbeafe;transform:scale(1.1); }
    .tbl-action-delete:hover { background:#fee2e2;transform:scale(1.1); }

    /* Empty state */
    .empty-ab {
        padding: 60px 24px;
        text-align: center;
    }
    .empty-ab-icon { font-size: 52px; color: #d1d5db; margin-bottom: 14px; }

    /* Pagination */
    .ab-pagination { display:flex;align-items:center;justify-content:space-between;padding:14px 20px;border-top:1px solid rgba(22,163,74,.08);flex-wrap:wrap;gap:10px; }

    /* Dark mode */
    html.app-skin-dark .ab-filter,
    html.app-skin-dark .ab-table-card { background:#0a2317 !important; border-color:rgba(34,197,94,.18) !important; }
    html.app-skin-dark .ab-table-title { color:#d1fae5 !important; }
    html.app-skin-dark .ab-filter label { color:#86efac !important; }
    html.app-skin-dark .ab-table thead th { background:#021a0b !important; }
    html.app-skin-dark .ab-table tbody td { border-color:rgba(34,197,94,.08) !important; color:#d1fae5; }
    html.app-skin-dark .ab-table tbody tr:hover td { background:rgba(34,197,94,.05) !important; }
    html.app-skin-dark .ab-name { color:#86efac !important; }
    html.app-skin-dark .ab-merk { color:#e2f5ea !important; }
    html.app-skin-dark .ab-year { color:#9ca3af !important; }
    html.app-skin-dark .ab-desc { color:#9ca3af !important; }
    html.app-skin-dark .ab-filter .form-control,
    html.app-skin-dark .ab-filter .form-select {
        background:#0e3b26 !important;
        border-color:rgba(34,197,94,.25) !important;
        color:#d1fae5 !important;
        color-scheme: dark !important;
    }
    html.app-skin-dark .ab-filter .form-select option {
        background:#0a2317 !important;
        color:#d1fae5 !important;
    }
</style>
@endsection

@section('content')
<article class="alat-berat-index">

    {{-- ================================================================
         1. FILTER BAR
         ================================================================ --}}
    <div class="ab-filter no-print">
        <form method="GET" action="{{ route('alat-berat.index') }}">
            <div class="row g-3 align-items-end">
                @if(Auth::user()->isAdmin())
                <div class="col-12 col-sm-6 col-md-3">
                    <label><i class="feather-home me-1" style="color:#16a34a;"></i> Unit PKS</label>
                    <select name="id_pks" class="form-select">
                        <option value="">Semua PKS</option>
                        @foreach($pksList as $pks)
                            <option value="{{ $pks->id_pks }}" {{ request('id_pks') == $pks->id_pks ? 'selected' : '' }}>
                                {{ $pks->nama }} ({{ $pks->akro }})
                            </option>
                        @endforeach
                    </select>
                </div>
                @endif

                <div class="col-12 col-sm-6 col-md-3">
                    <label><i class="feather-truck me-1" style="color:#16a34a;"></i> Jenis Alat</label>
                    <select name="jenis_alat" class="form-select">
                        <option value="">Semua Jenis</option>
                        <option value="Excavator" {{ request('jenis_alat') == 'Excavator' ? 'selected' : '' }}>Excavator</option>
                        <option value="Wheel Loader" {{ request('jenis_alat') == 'Wheel Loader' ? 'selected' : '' }}>Wheel Loader</option>
                        <option value="Bulldozer" {{ request('jenis_alat') == 'Bulldozer' ? 'selected' : '' }}>Bulldozer</option>
                        <option value="Dump Truck" {{ request('jenis_alat') == 'Dump Truck' ? 'selected' : '' }}>Dump Truck</option>
                        <option value="Compactor" {{ request('jenis_alat') == 'Compactor' ? 'selected' : '' }}>Compactor</option>
                        <option value="Lainnya" {{ request('jenis_alat') == 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
                    </select>
                </div>

                <div class="col-12 col-sm-6 col-md-3">
                    <label><i class="feather-activity me-1" style="color:#16a34a;"></i> Status Operasional</label>
                    <select name="status" class="form-select">
                        <option value="">Semua Status</option>
                        <option value="Operational" {{ request('status') == 'Operational' ? 'selected' : '' }}>Ready / Operational</option>
                        <option value="Maintenance" {{ request('status') == 'Maintenance' ? 'selected' : '' }}>Maintenance / Perbaikan</option>
                        <option value="Breakdown" {{ request('status') == 'Breakdown' ? 'selected' : '' }}>Breakdown / Rusak</option>
                        <option value="Standby" {{ request('status') == 'Standby' ? 'selected' : '' }}>Standby</option>
                        <option value="Rolling" {{ request('status') == 'Rolling' ? 'selected' : '' }}>Rolling / Dipinjam Kebun Lain</option>
                    </select>
                </div>

                <div class="col-12 col-sm-6 col-md-3">
                    <label><i class="feather-calendar me-1" style="color:#16a34a;"></i> Dari Tanggal</label>
                    <input type="date" name="dari_tanggal" class="form-control" value="{{ request('dari_tanggal') }}">
                </div>

                <div class="col-12 col-sm-6 col-md-3">
                    <label><i class="feather-calendar me-1" style="color:#16a34a;"></i> Sampai Tanggal</label>
                    <input type="date" name="sampai_tanggal" class="form-control" value="{{ request('sampai_tanggal') }}">
                </div>

                <div class="col-12 col-sm-6 col-md-3">
                    <label><i class="feather-search me-1" style="color:#16a34a;"></i> Pencarian</label>
                    <input type="text" name="search" class="form-control" placeholder="Kode / Nama / Merk..." value="{{ request('search') }}">
                </div>

                <div class="col-12 col-sm-6 col-md-3">
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn-ptpn btn-ptpn-primary flex-grow-1" style="padding:9px 14px;">
                            <i class="feather-filter" style="font-size:14px;"></i> Filter
                        </button>
                        <a href="{{ route('alat-berat.index') }}" class="btn-ptpn btn-ptpn-outline" style="padding:9px 14px;" title="Reset Filter">
                            <i class="feather-refresh-cw" style="font-size:14px;"></i>
                        </a>
                    </div>
                </div>
            </div>
        </form>
    </div>

    {{-- ================================================================
         2. DATA TABLE
         ================================================================ --}}
    <div class="ab-table-card">
        <div class="ab-table-header">
            <h3 class="ab-table-title">
                <i class="feather-truck" style="color:#16a34a;font-size:18px;"></i>
                Daftar Alat Berat Pengolahan Limbah
            </h3>
            <div class="d-flex align-items-center gap-3">
                <small style="color:#6b7280;font-size:11.5px;">
                    Menampilkan {{ $alatBerat->firstItem() ?? 0 }}–{{ $alatBerat->lastItem() ?? 0 }}
                    dari <strong>{{ $alatBerat->total() }}</strong> unit
                </small>
                <span class="mod-pill mod-pill-ok" style="font-size:10.5px;">
                    <i class="feather-database" style="font-size:11px;"></i>
                    Total {{ $alatBerat->total() }} Unit
                </span>
            </div>
        </div>

        @if($alatBerat->count() > 0)
        <div class="table-responsive" style="-webkit-overflow-scrolling: touch;">
            <table class="ab-table" role="table" aria-label="Daftar Master Alat Berat">
                <thead>
                    <tr>
                        <th style="width:40px;text-align:center;">#</th>
                        <th style="width:70px;text-align:center;">PKS</th>
                        <th style="min-width:110px;">Kode Alat</th>
                        <th style="min-width:160px;">Nama Alat Berat</th>
                        <th style="min-width:120px;">Jenis Alat</th>
                        <th style="min-width:130px;">Merk / Tipe</th>
                        <th style="width:80px;text-align:center;">Tahun</th>
                        <th style="width:130px;text-align:center;">Status</th>
                        <th style="min-width:150px;">Keterangan</th>
                        <th style="width:90px;text-align:center;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($alatBerat as $index => $item)
                    <tr>
                        <td style="text-align:center;font-weight:700;color:#9ca3af;font-size:12px;">
                            {{ $alatBerat->firstItem() + $index }}
                        </td>

                        <td style="text-align:center;">
                            <span class="mod-pill mod-pill-ok" style="font-size:10.5px;">
                                {{ $item->pks ? $item->pks->akro : '-' }}
                            </span>
                        </td>

                        <td>
                            <code style="font-weight:800;color:#16a34a;background:rgba(22,163,74,.08);padding:4px 8px;border-radius:6px;">{{ $item->kode_alat }}</code>
                        </td>

                        <td>
                            <span class="ab-name" style="font-weight:700;color:#14532d;font-size:13px;">{{ $item->nama_alat }}</span>
                        </td>

                        <td>
                            <span class="mod-pill mod-pill-info" style="font-size:10.5px;">{{ $item->jenis_alat }}</span>
                        </td>

                        <td>
                            <span class="ab-merk" style="font-weight:600;color:#374151;">{{ $item->merk_tipe ?? '-' }}</span>
                        </td>

                        <td style="text-align:center;">
                            <span class="ab-year" style="font-weight:700;color:#6b7280;">{{ $item->tahun_pengadaan ?? '-' }}</span>
                        </td>

                        <td style="text-align:center;">
                            @if($item->status == 'Operational')
                                <span class="mod-pill mod-pill-ok" style="font-size:10.5px;">Ready / Operational</span>
                            @elseif($item->status == 'Maintenance')
                                <span class="mod-pill mod-pill-warn" style="font-size:10.5px;">Maintenance</span>
                            @elseif($item->status == 'Breakdown')
                                <span class="mod-pill mod-pill-err" style="font-size:10.5px;">Breakdown</span>
                            @elseif($item->status == 'Rolling')
                                <span class="mod-pill mod-pill-info" style="font-size:10.5px;">Rolling</span>
                            @else
                                <span class="mod-pill mod-pill-standby" style="font-size:10.5px;">Standby</span>
                            @endif
                        </td>

                        <td>
                            <small class="ab-desc" style="color:#6b7280;">{{ $item->keterangan ?? '-' }}</small>
                        </td>

                        <td style="text-align:center;">
                            <div class="d-flex gap-1 justify-content-center">
                                <a href="{{ route('alat-berat.edit', $item->id) }}" class="tbl-action tbl-action-edit" title="Edit Alat Berat">
                                    <i class="feather-edit-2"></i>
                                </a>
                                <form action="{{ route('alat-berat.destroy', $item->id) }}" method="POST" class="d-inline form-delete">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="tbl-action tbl-action-delete btn-delete" title="Hapus Alat Berat">
                                        <i class="feather-trash-2"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div class="ab-pagination">
            <small style="color:#6b7280;font-size:11.5px;">
                Halaman <strong>{{ $alatBerat->currentPage() }}</strong> dari <strong>{{ $alatBerat->lastPage() }}</strong>
            </small>
            <div>{{ $alatBerat->links() }}</div>
        </div>

        @else
        <div class="empty-ab">
            <div class="empty-ab-icon"><i class="feather-inbox"></i></div>
            <h5 style="color:#374151;font-family:'Outfit',sans-serif;font-weight:800;margin-bottom:8px;">Belum Ada Master Alat Berat</h5>
            <p style="color:#6b7280;font-size:13.5px;margin-bottom:20px;">Data master alat berat belum tersedia.</p>
            <a href="{{ route('alat-berat.create') }}" class="btn-ptpn btn-ptpn-primary" style="display:inline-flex;">
                <i class="feather-plus" style="font-size:15px;"></i>
                Tambah Alat Berat Pertama
            </a>
        </div>
        @endif
    </div>

</article>
@endsection

@section('scripts')
<script src="{{ asset('duraluxadmin/assets/vendors/js/sweetalert2.min.js') }}"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.btn-delete').forEach(function(btn) {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const form = this.closest('form');
            Swal.fire({
                title: 'Hapus Master Alat Berat?',
                text: 'Data yang dihapus tidak dapat dikembalikan!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc2626',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal',
                reverseButtons: true,
                borderRadius: '16px'
            }).then((result) => {
                if (result.value) {
                    form.submit();
                }
            });
        });
    });
});
</script>
@endsection
