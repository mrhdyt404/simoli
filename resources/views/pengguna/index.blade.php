@extends('layouts.simoli')

@section('title', 'Data Pengguna SIMOLI')
@section('page-title', 'Manajemen Data Pengguna')
@section('page-description', 'Kelola Akun Akses Pengguna, PKS Unit, Mandor, dan Operator Lapangan')

@section('breadcrumb')
    <li>Input Data</li>
    <li class="separator">/</li>
    <li>Data Pengguna</li>
@endsection

@section('page-actions')
    <a href="{{ route('pengguna.create') }}" class="btn-ptpn btn-ptpn-primary">
        <i class="feather-user-plus" style="font-size:15px;"></i>
        <span>Tambah Pengguna Baru</span>
    </a>
@endsection

@section('styles')
<style>
    /* ================================================================
       DATA PENGGUNA INDEX — PTPN GREEN THEME
       ================================================================ */
    @keyframes fadeUpCard {
        from { opacity:0; transform:translateY(14px); }
        to   { opacity:1; transform:translateY(0); }
    }

    /* === KPI SUMMARY CARDS === */
    .usr-kpi {
        background: #ffffff;
        border-radius: 18px;
        border: 1px solid rgba(22,163,74,.1);
        box-shadow: 0 2px 12px rgba(22,163,74,.06);
        padding: 16px 18px;
        height: 100%;
        transition: all .25s ease;
        position: relative;
        overflow: hidden;
        animation: fadeUpCard .4s ease-out both;
    }

    .usr-kpi::after {
        content:'';position:absolute;top:0;left:0;right:0;height:3px;border-radius:18px 18px 0 0;
    }
    .usr-kpi-green::after  { background: linear-gradient(90deg,#16a34a,#4ade80); }
    .usr-kpi-amber::after  { background: linear-gradient(90deg,#d97706,#fbbf24); }
    .usr-kpi-teal::after   { background: linear-gradient(90deg,#0d9488,#2dd4bf); }
    .usr-kpi-purple::after { background: linear-gradient(90deg,#7c3aed,#a78bfa); }
    .usr-kpi-blue::after   { background: linear-gradient(90deg,#1d4ed8,#60a5fa); }

    .usr-kpi:hover { transform:translateY(-3px); box-shadow:0 10px 24px rgba(22,163,74,.12); }

    .usr-kpi-label { font-size:10.5px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.5px;margin-bottom:4px; }
    .usr-kpi-value { font-family:'Outfit',sans-serif;font-size:clamp(18px,1.8vw+8px,24px);font-weight:900;line-height:1.1;letter-spacing:-.5px; }
    .usr-kpi-icon  { width:42px;height:42px;border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:18px;flex-shrink:0; }

    .kpi-icon-g { background:#f0fdf4;color:#16a34a;border:1px solid #bbf7d0; }
    .kpi-icon-a { background:#fffbeb;color:#b45309;border:1px solid #fde68a; }
    .kpi-icon-t { background:#f0fdfa;color:#0d9488;border:1px solid #99f6e4; }
    .kpi-icon-p { background:#f5f3ff;color:#7c3aed;border:1px solid #ddd6fe; }
    .kpi-icon-b { background:#eff6ff;color:#1d4ed8;border:1px solid #bfdbfe; }

    /* === FILTER CARD === */
    .usr-filter {
        background: #ffffff;
        border-radius: 16px;
        border: 1px solid rgba(22,163,74,.15);
        box-shadow: 0 2px 10px rgba(22,163,74,.05);
        padding: 18px 20px;
        margin-bottom: 20px;
    }

    .usr-filter .form-control,
    .usr-filter .form-select {
        border-radius: 12px;
        border: 1.5px solid #e5e7eb;
        font-size: 13px;
        padding: 9px 14px;
        transition: all .2s ease;
        background: #f9fafb;
    }

    .usr-filter .form-control:focus,
    .usr-filter .form-select:focus {
        border-color: rgba(22,163,74,.5);
        background: #ffffff;
        box-shadow: 0 0 0 3px rgba(34,197,94,.08);
    }

    .usr-filter label { font-size:11.5px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.4px;margin-bottom:5px; }

    /* === DATA TABLE CARD === */
    .usr-table-card {
        background: #ffffff;
        border-radius: 18px;
        border: 1px solid rgba(22,163,74,.1);
        box-shadow: 0 2px 12px rgba(22,163,74,.06);
        overflow: hidden;
    }

    .usr-table-header {
        padding: 16px 20px;
        border-bottom: 1px solid rgba(22,163,74,.08);
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 10px;
    }

    .usr-table-title {
        font-family: 'Outfit', sans-serif;
        font-size: 15px;
        font-weight: 800;
        color: #14532d;
        display: flex;
        align-items: center;
        gap: 8px;
        margin: 0;
    }

    .usr-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
        font-size: clamp(11.5px, .65vw+8.5px, 13px);
    }

    .usr-table thead th {
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

    .usr-table tbody td {
        padding: 12px 16px;
        border-bottom: 1px solid rgba(22,163,74,.07);
        vertical-align: middle;
    }

    .usr-table tbody tr { transition: background .15s ease; }
    .usr-table tbody tr:hover td { background: rgba(22,163,74,.03); }
    .usr-table tbody tr:last-child td { border-bottom: none; }

    /* Action btns */
    .tbl-action { display:inline-flex;align-items:center;justify-content:center;width:32px;height:32px;border-radius:9px;border:none;cursor:pointer;transition:all .2s ease;font-size:14px; }
    .tbl-action-edit   { background:#eff6ff;color:#1d4ed8;border:1px solid #bfdbfe; }
    .tbl-action-delete { background:#fef2f2;color:#dc2626;border:1px solid #fca5a5; }
    .tbl-action-edit:hover   { background:#dbeafe;transform:scale(1.1); }
    .tbl-action-delete:hover { background:#fee2e2;transform:scale(1.1); }

    /* Empty state */
    .empty-usr {
        padding: 60px 24px;
        text-align: center;
    }
    .empty-usr-icon { font-size: 52px; color: #d1d5db; margin-bottom: 14px; }

    /* Pagination */
    .usr-pagination { display:flex;align-items:center;justify-content:space-between;padding:14px 20px;border-top:1px solid rgba(22,163,74,.08);flex-wrap:wrap;gap:10px; }

    /* Dark mode */
    html.app-skin-dark .usr-kpi,
    html.app-skin-dark .usr-filter,
    html.app-skin-dark .usr-table-card { background:#0a2317 !important; border-color:rgba(34,197,94,.18) !important; }
    html.app-skin-dark .usr-table-title { color:#d1fae5 !important; }
    html.app-skin-dark .usr-kpi-label { color:#9ca3af !important; }
    html.app-skin-dark .usr-filter label { color:#86efac !important; }
    html.app-skin-dark .usr-table thead th { background:#021a0b !important; }
    html.app-skin-dark .usr-table tbody td { border-color:rgba(34,197,94,.08) !important; color:#d1fae5; }
    html.app-skin-dark .usr-table tbody tr:hover td { background:rgba(34,197,94,.05) !important; }
    html.app-skin-dark .usr-filter .form-control,
    html.app-skin-dark .usr-filter .form-select {
        background:#0e3b26 !important;
        border-color:rgba(34,197,94,.25) !important;
        color:#d1fae5 !important;
        color-scheme: dark !important;
    }
    html.app-skin-dark .usr-filter .form-select option {
        background:#0a2317 !important;
        color:#d1fae5 !important;
    }
    html.app-skin-dark .tbl-action-edit { background:rgba(59,130,246,.15) !important; color:#93c5fd !important; border-color:rgba(59,130,246,.3) !important; }
    html.app-skin-dark .tbl-action-delete { background:rgba(239,68,68,.15) !important; color:#fca5a5 !important; border-color:rgba(239,68,68,.3) !important; }
    html.app-skin-dark .kpi-icon-g { background:rgba(34,197,94,.15) !important; color:#4ade80 !important; border-color:rgba(34,197,94,.3) !important; }
    html.app-skin-dark .kpi-icon-a { background:rgba(245,158,11,.15) !important; color:#fbbf24 !important; border-color:rgba(245,158,11,.3) !important; }
    html.app-skin-dark .kpi-icon-t { background:rgba(20,184,166,.15) !important; color:#2dd4bf !important; border-color:rgba(20,184,166,.3) !important; }
    html.app-skin-dark .kpi-icon-p { background:rgba(124,58,237,.15) !important; color:#c084fc !important; border-color:rgba(124,58,237,.3) !important; }
    html.app-skin-dark .kpi-icon-b { background:rgba(59,130,246,.15) !important; color:#60a5fa !important; border-color:rgba(59,130,246,.3) !important; }
</style>
@endsection

@section('content')
<article class="pengguna-index">

    {{-- ================================================================
         1. KPI SUMMARY CARDS
         ================================================================ --}}
    <section aria-label="Ringkasan Statistik Akun Pengguna" class="mb-4">
        <div class="row g-3">
            <div class="col-xl-24 col-lg-4 col-sm-6" style="animation-delay:.03s">
                <div class="usr-kpi usr-kpi-green">
                    <div class="d-flex align-items-start justify-content-between">
                        <div>
                            <div class="usr-kpi-label">Total Pengguna</div>
                            <div class="usr-kpi-value" style="color:#16a34a;">{{ $totalUsers }}</div>
                        </div>
                        <div class="usr-kpi-icon kpi-icon-g"><i class="feather-users"></i></div>
                    </div>
                </div>
            </div>

            <div class="col-xl-24 col-lg-4 col-sm-6" style="animation-delay:.06s">
                <div class="usr-kpi usr-kpi-amber">
                    <div class="d-flex align-items-start justify-content-between">
                        <div>
                            <div class="usr-kpi-label">Admin SIMOLI</div>
                            <div class="usr-kpi-value" style="color:#b45309;">{{ $totalAdmin }}</div>
                        </div>
                        <div class="usr-kpi-icon kpi-icon-a"><i class="feather-shield"></i></div>
                    </div>
                </div>
            </div>

            <div class="col-xl-24 col-lg-4 col-sm-6" style="animation-delay:.09s">
                <div class="usr-kpi usr-kpi-teal">
                    <div class="d-flex align-items-start justify-content-between">
                        <div>
                            <div class="usr-kpi-label">Akun Unit PKS</div>
                            <div class="usr-kpi-value" style="color:#0d9488;">{{ $totalUnit ?? 0 }}</div>
                        </div>
                        <div class="usr-kpi-icon kpi-icon-t"><i class="feather-home"></i></div>
                    </div>
                </div>
            </div>

            <div class="col-xl-24 col-lg-4 col-sm-6" style="animation-delay:.12s">
                <div class="usr-kpi usr-kpi-purple">
                    <div class="d-flex align-items-start justify-content-between">
                        <div>
                            <div class="usr-kpi-label">Mandor Lapangan</div>
                            <div class="usr-kpi-value" style="color:#7c3aed;">{{ $totalMandor ?? 0 }}</div>
                        </div>
                        <div class="usr-kpi-icon kpi-icon-p"><i class="feather-award"></i></div>
                    </div>
                </div>
            </div>

            <div class="col-xl-24 col-lg-4 col-sm-6" style="animation-delay:.15s">
                <div class="usr-kpi usr-kpi-blue">
                    <div class="d-flex align-items-start justify-content-between">
                        <div>
                            <div class="usr-kpi-label">Operator Lapangan</div>
                            <div class="usr-kpi-value" style="color:#1d4ed8;">{{ $totalOperator ?? 0 }}</div>
                        </div>
                        <div class="usr-kpi-icon kpi-icon-b"><i class="feather-user-check"></i></div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ================================================================
         2. FILTER BAR
         ================================================================ --}}
    <div class="usr-filter no-print">
        <form action="{{ route('pengguna.index') }}" method="GET">
            <div class="row g-3 align-items-end">
                <div class="col-12 col-md-5">
                    <label><i class="feather-search me-1" style="color:#16a34a;"></i> Pencarian Kata Kunci</label>
                    <input type="text" name="q" class="form-control" value="{{ request('q') }}" placeholder="Cari nama, username, kode, atau AKRO...">
                </div>

                <div class="col-12 col-md-4">
                    <label><i class="feather-layers me-1" style="color:#16a34a;"></i> Level Akses Pengguna</label>
                    <select name="level_akses" class="form-select">
                        <option value="">Semua Level Akses</option>
                        <option value="admin" {{ request('level_akses') === 'admin' ? 'selected' : '' }}>Admin SIMOLI</option>
                        <option value="unit" {{ request('level_akses') === 'unit' ? 'selected' : '' }}>Unit PKS</option>
                        <option value="mandor" {{ request('level_akses') === 'mandor' ? 'selected' : '' }}>Mandor Lapangan</option>
                        <option value="operator" {{ request('level_akses') === 'operator' ? 'selected' : '' }}>Operator Lapangan</option>
                    </select>
                </div>

                <div class="col-12 col-md-3 d-flex gap-2">
                    <button type="submit" class="btn-ptpn btn-ptpn-primary flex-grow-1" style="padding:9px 16px;">
                        <i class="feather-search" style="font-size:14px;"></i> Filter Data
                    </button>
                    <a href="{{ route('pengguna.index') }}" class="btn-ptpn btn-ptpn-outline" style="padding:9px 14px;" title="Reset Filter">
                        <i class="feather-refresh-cw" style="font-size:14px;"></i>
                    </a>
                </div>
            </div>
        </form>
    </div>

    {{-- ================================================================
         3. DATA TABLE
         ================================================================ --}}
    <div class="usr-table-card">
        <div class="usr-table-header">
            <h3 class="usr-table-title">
                <i class="feather-users" style="color:#16a34a;font-size:18px;"></i>
                Daftar Akun Pengguna SIMOLI
            </h3>
            <div class="d-flex align-items-center gap-3">
                <small style="color:#6b7280;font-size:11.5px;">
                    Menampilkan {{ $users->firstItem() ?? 0 }}–{{ $users->lastItem() ?? 0 }}
                    dari <strong>{{ $users->total() }}</strong> pengguna
                </small>
                <span class="mod-pill mod-pill-ok" style="font-size:10.5px;">
                    <i class="feather-database" style="font-size:11px;"></i>
                    Total {{ $users->total() }} Akun
                </span>
            </div>
        </div>

        @if($users->count() > 0)
        <div class="table-responsive" style="-webkit-overflow-scrolling: touch;">
            <table class="usr-table" role="table" aria-label="Daftar Pengguna">
                <thead>
                    <tr>
                        <th style="width:50px;text-align:center;">#</th>
                        <th style="min-width:100px;">Kode PKS</th>
                        <th style="min-width:180px;">Nama Unit PKS</th>
                        <th style="min-width:140px;">Username</th>
                        <th style="width:140px;text-align:center;">Level Akses</th>
                        <th style="width:100px;text-align:center;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $index => $item)
                    <tr>
                        <td style="text-align:center;font-weight:700;color:#9ca3af;font-size:12px;">
                            {{ $users->firstItem() + $index }}
                        </td>

                        <td>
                            <code style="font-weight:800;color:#16a34a;background:rgba(22,163,74,.08);padding:4px 8px;border-radius:6px;">{{ $item->pks->KODE ?? $item->pks->kode ?? '-' }}</code>
                        </td>

                        <td>
                            <div style="font-weight:700;color:#14532d;font-size:13px;">{{ $item->pks->NAMA ?? $item->pks->nama ?? '-' }}</div>
                            <small style="color:#6b7280;font-size:11px;">AKRO: {{ $item->pks->AKRO ?? $item->pks->akro ?? '-' }}</small>
                        </td>

                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div style="width:32px;height:32px;border-radius:50%;background:linear-gradient(135deg,#052e16,#166534);color:#86efac;display:flex;align-items:center;justify-content:center;font-weight:800;font-size:13px;flex-shrink:0;">
                                    {{ strtoupper(substr($item->username, 0, 1)) }}
                                </div>
                                <span style="font-weight:700;color:#1f2937;">{{ $item->username }}</span>
                            </div>
                        </td>

                        <td style="text-align:center;">
                            @if($item->level_akses == 'admin')
                                <span class="mod-pill mod-pill-warn" style="font-size:11px;font-weight:800;">
                                    <i class="feather-shield me-1"></i> Admin
                                </span>
                            @elseif($item->level_akses == 'mandor')
                                <span class="mod-pill" style="font-size:11px;font-weight:800;background:#f5f3ff;color:#7c3aed;border:1px solid #ddd6fe;">
                                    <i class="feather-award me-1"></i> Mandor
                                </span>
                            @elseif($item->level_akses == 'operator')
                                <span class="mod-pill mod-pill-info" style="font-size:11px;font-weight:800;">
                                    <i class="feather-user-check me-1"></i> Operator
                                </span>
                            @else
                                <span class="mod-pill mod-pill-ok" style="font-size:11px;font-weight:800;">
                                    <i class="feather-home me-1"></i> Unit PKS
                                </span>
                            @endif
                        </td>

                        <td style="text-align:center;">
                            <div class="d-flex gap-1 justify-content-center">
                                <a href="{{ route('pengguna.edit', $item->ID) }}" class="tbl-action tbl-action-edit" title="Edit Pengguna">
                                    <i class="feather-edit-2"></i>
                                </a>
                                <form action="{{ route('pengguna.destroy', $item->ID) }}" method="POST" class="d-inline form-delete">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" class="tbl-action tbl-action-delete btn-delete" title="Hapus Pengguna">
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
        <div class="usr-pagination">
            <small style="color:#6b7280;font-size:11.5px;">
                Halaman <strong>{{ $users->currentPage() }}</strong> dari <strong>{{ $users->lastPage() }}</strong>
            </small>
            <div>{{ $users->links() }}</div>
        </div>

        @else
        <div class="empty-usr">
            <div class="empty-usr-icon"><i class="feather-users"></i></div>
            <h5 style="color:#374151;font-family:'Outfit',sans-serif;font-weight:800;margin-bottom:8px;">Belum Ada Data Pengguna</h5>
            <p style="color:#6b7280;font-size:13.5px;margin-bottom:20px;">Data akun pengguna belum ditemukan.</p>
            <a href="{{ route('pengguna.create') }}" class="btn-ptpn btn-ptpn-primary" style="display:inline-flex;">
                <i class="feather-user-plus" style="font-size:15px;"></i>
                Tambah Pengguna Pertama
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
                title: 'Hapus Data Pengguna?',
                text: 'Data pengguna yang dihapus tidak dapat dikembalikan!',
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