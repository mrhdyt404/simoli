@extends('layouts.simoli')

@section('title', 'Rencana Pengaliran & Pemeliharaan')
@section('page-title', 'Rencana Pengaliran & Pemeliharaan')
@section('page-description', 'Target Tahunan Pengaliran Limbah & Pemeliharaan Bed Land Aplikasi')

@section('breadcrumb')
    <li>Input Data</li>
    <li class="separator">/</li>
    <li>Rencana</li>
@endsection

@section('page-actions')
    <a href="{{ route('rencana.create') }}" class="btn-ptpn btn-ptpn-primary">
        <i class="feather-plus" style="font-size:15px;"></i>
        <span>Tambah Rencana</span>
    </a>
@endsection

@section('styles')
<link rel="stylesheet" type="text/css" href="{{ asset('duraluxadmin/assets/vendors/css/select2.min.css') }}" />
<link rel="stylesheet" type="text/css" href="{{ asset('duraluxadmin/assets/vendors/css/select2-theme.min.css') }}" />
<style>
    /* ================================================================
       RENCANA INDEX — PTPN GREEN THEME
       ================================================================ */
    @keyframes fadeUpCard {
        from { opacity:0; transform:translateY(14px); }
        to   { opacity:1; transform:translateY(0); }
    }

    /* === KPI SUMMARY CARDS === */
    .rc-kpi {
        background: #ffffff;
        border-radius: 18px;
        border: 1px solid rgba(22,163,74,.1);
        box-shadow: 0 2px 12px rgba(22,163,74,.06);
        padding: 18px 20px;
        height: 100%;
        transition: all .25s ease;
        position: relative;
        overflow: hidden;
        animation: fadeUpCard .4s ease-out both;
    }
    .rc-kpi::after {
        content:'';position:absolute;top:0;left:0;right:0;height:3px;border-radius:18px 18px 0 0;
    }
    .rc-kpi-green::after { background: linear-gradient(90deg,#16a34a,#4ade80); }
    .rc-kpi-blue::after  { background: linear-gradient(90deg,#1d4ed8,#60a5fa); }
    .rc-kpi-amber::after { background: linear-gradient(90deg,#d97706,#fbbf24); }
    .rc-kpi-teal::after  { background: linear-gradient(90deg,#0d9488,#2dd4bf); }
    
    .rc-kpi:hover { transform:translateY(-4px); box-shadow:0 12px 28px rgba(22,163,74,.12); }

    .rc-kpi-label { font-size:10.5px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.5px;margin-bottom:5px; }
    .rc-kpi-value { font-family:'Outfit',sans-serif;font-size:clamp(20px,2vw+10px,26px);font-weight:900;line-height:1.1;letter-spacing:-.5px;margin-bottom:6px; }
    .rc-kpi-sub   { font-size:11px;color:#6b7280; }
    .rc-kpi-icon  { width:44px;height:44px;border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:18px;flex-shrink:0; }
    
    .kpi-icon-g { background:#f0fdf4;color:#16a34a;border:1px solid #bbf7d0; }
    .kpi-icon-b { background:#eff6ff;color:#1d4ed8;border:1px solid #bfdbfe; }
    .kpi-icon-a { background:#fffbeb;color:#b45309;border:1px solid #fde68a; }
    .kpi-icon-t { background:#f0fdfa;color:#0d9488;border:1px solid #99f6e4; }

    /* === FILTER CARD === */
    .rc-filter {
        background: #ffffff;
        border-radius: 16px;
        border: 1px solid rgba(22,163,74,.15);
        box-shadow: 0 2px 10px rgba(22,163,74,.05);
        padding: 18px 20px;
        margin-bottom: 20px;
    }

    .rc-filter .form-control,
    .rc-filter .form-select {
        border-radius: 12px;
        border: 1.5px solid #e5e7eb;
        font-size: 13px;
        padding: 9px 14px;
        transition: all .2s ease;
        background: #f9fafb;
    }

    .rc-filter .form-control:focus,
    .rc-filter .form-select:focus {
        border-color: rgba(22,163,74,.5);
        background: #ffffff;
        box-shadow: 0 0 0 3px rgba(34,197,94,.08);
    }

    .rc-filter label { font-size:11.5px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.4px;margin-bottom:5px; }

    .rc-filter .select2-container--default .select2-selection--single {
        height: 42px; border-radius: 12px; border: 1.5px solid #e5e7eb;
        background: #f9fafb; padding: 6px 12px;
    }
    .rc-filter .select2-container--default .select2-selection--single .select2-selection__rendered { line-height: 28px; font-size: 13px; }
    .rc-filter .select2-container--default .select2-selection--single .select2-selection__arrow { height: 40px; }

    /* === DATA TABLE CARD === */
    .rc-table-card {
        background: #ffffff;
        border-radius: 18px;
        border: 1px solid rgba(22,163,74,.1);
        box-shadow: 0 2px 12px rgba(22,163,74,.06);
        overflow: hidden;
    }

    .rc-table-header {
        padding: 16px 20px;
        border-bottom: 1px solid rgba(22,163,74,.08);
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 10px;
    }

    .rc-table-title {
        font-family: 'Outfit', sans-serif;
        font-size: 15px;
        font-weight: 800;
        color: #14532d;
        display: flex;
        align-items: center;
        gap: 8px;
        margin: 0;
    }

    .rc-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
        font-size: clamp(11.5px, .7vw+9px, 13px);
    }

    .rc-table thead th {
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

    .rc-table tbody td {
        padding: 12px 16px;
        border-bottom: 1px solid rgba(22,163,74,.07);
        vertical-align: middle;
    }

    .rc-table tbody tr { transition: background .15s ease; }
    .rc-table tbody tr:hover td { background: rgba(22,163,74,.03); }
    .rc-table tbody tr:last-child td { border-bottom: none; }

    /* PKS Badge */
    .pks-badge {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 4px 10px;
        border-radius: 8px;
        font-size: 11px;
        font-weight: 800;
        background: linear-gradient(135deg,#052e16,#166534);
        color: #86efac;
    }

    /* Bed Bars */
    .bed-bar {
        height: 6px;
        border-radius: 6px;
        background: rgba(22,163,74,.1);
        overflow: hidden;
        min-width: 80px;
    }
    .bed-bar-fill-fb { height: 100%; border-radius: 6px; background: linear-gradient(90deg,#1d4ed8,#60a5fa); transition: width .4s ease; }
    .bed-bar-fill-lb { height: 100%; border-radius: 6px; background: linear-gradient(90deg,#d97706,#fbbf24); transition: width .4s ease; }

    /* Action btns */
    .tbl-action { display:inline-flex;align-items:center;justify-content:center;width:32px;height:32px;border-radius:9px;border:none;cursor:pointer;transition:all .2s ease;font-size:14px; }
    .tbl-action-edit   { background:#eff6ff;color:#1d4ed8;border:1px solid #bfdbfe; }
    .tbl-action-delete { background:#fef2f2;color:#dc2626;border:1px solid #fca5a5; }
    .tbl-action-edit:hover   { background:#dbeafe;transform:scale(1.1); }
    .tbl-action-delete:hover { background:#fee2e2;transform:scale(1.1); }

    /* Empty state */
    .empty-rc {
        padding: 60px 24px;
        text-align: center;
    }
    .empty-rc-icon { font-size: 52px; color: #d1d5db; margin-bottom: 14px; }

    /* Pagination */
    .rc-pagination { display:flex;align-items:center;justify-content:space-between;padding:14px 20px;border-top:1px solid rgba(22,163,74,.08);flex-wrap:wrap;gap:10px; }

    /* Dark mode */
    html.app-skin-dark .rc-kpi,
    html.app-skin-dark .rc-filter,
    html.app-skin-dark .rc-table-card { background:#0a2317 !important; border-color:rgba(34,197,94,.15) !important; }
    html.app-skin-dark .rc-table-title,
    html.app-skin-dark .rc-kpi-value { color:#d1fae5 !important; }
    html.app-skin-dark .rc-table thead th { background:#021a0b !important; }
    html.app-skin-dark .rc-table tbody td { border-color:rgba(34,197,94,.07) !important; color:#d1fae5; }
    html.app-skin-dark .rc-table tbody tr:hover td { background:rgba(34,197,94,.04) !important; }
    html.app-skin-dark .rc-filter .form-control,
    html.app-skin-dark .rc-filter .form-select { background:#0e3b26;border-color:rgba(34,197,94,.2);color:#d1fae5; }
</style>
@endsection

@section('content')
<article class="rencana-index">

    {{-- ================================================================
         1. KPI SUMMARY CARDS
         ================================================================ --}}
    <section aria-label="Ringkasan Data Rencana" class="mb-4">
        <div class="row g-3">
            <div class="col-xxl-3 col-md-6" style="animation-delay:.03s">
                <div class="rc-kpi rc-kpi-green">
                    <div class="d-flex align-items-start justify-content-between mb-2">
                        <div>
                            <div class="rc-kpi-label">Total Rencana</div>
                            <div class="rc-kpi-value" style="color:#16a34a;">{{ number_format($totalRecords) }}</div>
                        </div>
                        <div class="rc-kpi-icon kpi-icon-g"><i class="feather-clipboard"></i></div>
                    </div>
                    <div class="rc-kpi-sub">record rencana</div>
                </div>
            </div>

            <div class="col-xxl-3 col-md-6" style="animation-delay:.06s">
                <div class="rc-kpi rc-kpi-blue">
                    <div class="d-flex align-items-start justify-content-between mb-2">
                        <div>
                            <div class="rc-kpi-label">Total Flat Bed</div>
                            <div class="rc-kpi-value" style="color:#1d4ed8;">{{ number_format($totalFlatBed) }}</div>
                        </div>
                        <div class="rc-kpi-icon kpi-icon-b"><i class="feather-layers"></i></div>
                    </div>
                    <div class="rc-kpi-sub">flat bed direncanakan</div>
                </div>
            </div>

            <div class="col-xxl-3 col-md-6" style="animation-delay:.09s">
                <div class="rc-kpi rc-kpi-amber">
                    <div class="d-flex align-items-start justify-content-between mb-2">
                        <div>
                            <div class="rc-kpi-label">Total Long Bed</div>
                            <div class="rc-kpi-value" style="color:#b45309;">{{ number_format($totalLongBed) }}</div>
                        </div>
                        <div class="rc-kpi-icon kpi-icon-a"><i class="feather-maximize-2"></i></div>
                    </div>
                    <div class="rc-kpi-sub">long bed direncanakan</div>
                </div>
            </div>

            <div class="col-xxl-3 col-md-6" style="animation-delay:.12s">
                <div class="rc-kpi rc-kpi-teal">
                    <div class="d-flex align-items-start justify-content-between mb-2">
                        <div>
                            <div class="rc-kpi-label">Unit PKS</div>
                            <div class="rc-kpi-value" style="color:#0d9488;">{{ number_format($totalPks) }}</div>
                        </div>
                        <div class="rc-kpi-icon kpi-icon-t"><i class="feather-home"></i></div>
                    </div>
                    <div class="rc-kpi-sub">PKS terdaftar</div>
                </div>
            </div>
        </div>
    </section>

    {{-- ================================================================
         2. FILTER BAR
         ================================================================ --}}
    <div class="rc-filter no-print">
        <form action="{{ route('rencana.index') }}" method="GET">
            <div class="row g-3 align-items-end">
                @if(Auth::user()->isAdmin())
                <div class="col-md-4">
                    <label><i class="feather-home me-1" style="color:#16a34a;"></i> Unit PKS</label>
                    <select name="id_pks" class="form-select" data-select2-selector="status">
                        <option value="">Semua PKS</option>
                        @foreach($pksList as $pks)
                        <option value="{{ $pks->id_pks }}" {{ request('id_pks') == $pks->id_pks ? 'selected' : '' }}>
                            {{ $pks->nama }} ({{ $pks->akro }})
                        </option>
                        @endforeach
                    </select>
                </div>
                @endif

                <div class="col-md-3">
                    <label><i class="feather-calendar me-1" style="color:#16a34a;"></i> Tahun</label>
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
                    <label>&nbsp;</label>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn-ptpn btn-ptpn-primary flex-grow-1" style="padding:9px 14px;">
                            <i class="feather-filter" style="font-size:14px;"></i>
                            <span class="d-none d-sm-inline">Filter</span>
                        </button>
                        <a href="{{ route('rencana.index') }}" class="btn-ptpn btn-ptpn-outline" style="padding:9px 12px;" title="Reset">
                            <i class="feather-refresh-cw" style="font-size:14px;"></i>
                        </a>
                    </div>
                </div>
            </div>
        </form>
    </div>

    {{-- ================================================================
         3. DATA TABLE
         ================================================================ --}}
    <div class="rc-table-card">
        <div class="rc-table-header">
            <h3 class="rc-table-title">
                <i class="feather-clipboard" style="color:#16a34a;font-size:18px;"></i>
                Daftar Rencana Pengaliran &amp; Pemeliharaan
            </h3>
            <div class="d-flex align-items-center gap-3">
                <small style="color:#6b7280;font-size:11.5px;">
                    Menampilkan {{ $rencana->firstItem() ?? 0 }}–{{ $rencana->lastItem() ?? 0 }}
                    dari <strong>{{ $rencana->total() }}</strong> data
                </small>
                <span class="mod-pill mod-pill-ok" style="font-size:10.5px;">
                    <i class="feather-database" style="font-size:11px;"></i>
                    {{ $rencana->total() }} Total
                </span>
            </div>
        </div>

        @if($rencana->count() > 0)
        <div class="table-responsive" style="-webkit-overflow-scrolling: touch;">
            <table class="rc-table" role="table" aria-label="Daftar Rencana Target">
                <thead>
                    <tr>
                        <th style="width:44px;text-align:center;">#</th>
                        <th style="width:100px;">Tahun</th>
                        <th style="min-width:140px;">PKS</th>
                        <th style="width:120px;text-align:right;">Flat Bed</th>
                        <th style="width:120px;text-align:right;">Long Bed</th>
                        <th style="width:130px;text-align:right;">Total Bed</th>
                        <th style="min-width:180px;">Visualisasi</th>
                        <th style="width:90px;text-align:center;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $maxBed = max($rencana->max('flat_bed'), $rencana->max('long_bed'), 1);
                    @endphp
                    @foreach($rencana as $index => $item)
                    <tr>
                        <td style="text-align:center;font-weight:700;color:#9ca3af;font-size:12px;">
                            {{ $rencana->firstItem() + $index }}
                        </td>

                        <td>
                            <span class="mod-pill mod-pill-ok" style="font-size:11.5px;">{{ $item->tahun }}</span>
                        </td>

                        <td>
                            @if($item->pks)
                            <div>
                                <span class="pks-badge">{{ $item->pks->akro }}</span>
                                <div style="font-size:11px;color:#6b7280;margin-top:2px;">{{ $item->pks->nama }}</div>
                            </div>
                            @else
                            <span style="color:#9ca3af;font-size:11px;">N/A</span>
                            @endif
                        </td>

                        <td style="text-align:right;font-weight:800;color:#1d4ed8;">
                            {{ number_format($item->flat_bed) }}
                        </td>

                        <td style="text-align:right;font-weight:800;color:#b45309;">
                            {{ number_format($item->long_bed) }}
                        </td>

                        <td style="text-align:right;font-weight:900;color:#14532d;">
                            {{ number_format($item->flat_bed + $item->long_bed) }}
                        </td>

                        <td>
                            @php
                                $fbPct = min(100, ($item->flat_bed / max(1, $maxBed)) * 100);
                                $lbPct = min(100, ($item->long_bed / max(1, $maxBed)) * 100);
                            @endphp
                            <div class="d-flex align-items-center gap-2 mb-1">
                                <small style="width:20px;color:#6b7280;font-weight:700;font-size:10px;">FB</small>
                                <div class="bed-bar flex-grow-1"><div class="bed-bar-fill-fb" style="width:{{ $fbPct }}%;"></div></div>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <small style="width:20px;color:#6b7280;font-weight:700;font-size:10px;">LB</small>
                                <div class="bed-bar flex-grow-1"><div class="bed-bar-fill-lb" style="width:{{ $lbPct }}%;"></div></div>
                            </div>
                        </td>

                        <td style="text-align:center;">
                            <div class="d-flex gap-1 justify-content-center">
                                <a href="{{ route('rencana.edit', $item->id) }}" class="tbl-action tbl-action-edit" title="Edit Rencana">
                                    <i class="feather-edit-2"></i>
                                </a>
                                @if(Auth::user()->isAdmin())
                                <form action="{{ route('rencana.destroy', $item->id) }}" method="POST" class="d-inline form-delete">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="tbl-action tbl-action-delete btn-delete" title="Hapus Rencana">
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
        <div class="rc-pagination">
            <small style="color:#6b7280;font-size:11.5px;">
                Halaman <strong>{{ $rencana->currentPage() }}</strong> dari <strong>{{ $rencana->lastPage() }}</strong>
            </small>
            <div>{{ $rencana->links('pagination::bootstrap-5') }}</div>
        </div>

        @else
        <div class="empty-rc">
            <div class="empty-rc-icon"><i class="feather-inbox"></i></div>
            <h5 style="color:#374151;font-family:'Outfit',sans-serif;font-weight:800;margin-bottom:8px;">Belum Ada Data Rencana</h5>
            <p style="color:#6b7280;font-size:13.5px;margin-bottom:20px;">Data rencana pengaliran &amp; pemeliharaan belum tersedia.</p>
            <a href="{{ route('rencana.create') }}" class="btn-ptpn btn-ptpn-primary" style="display:inline-flex;">
                <i class="feather-plus" style="font-size:15px;"></i>
                Tambah Rencana Pertama
            </a>
        </div>
        @endif
    </div>

</article>
@endsection

@section('scripts')
<script src="{{ asset('duraluxadmin/assets/vendors/js/select2.min.js') }}"></script>
<script src="{{ asset('duraluxadmin/assets/vendors/js/select2-active.min.js') }}"></script>
<script src="{{ asset('duraluxadmin/assets/vendors/js/sweetalert2.min.js') }}"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    if (typeof $ !== 'undefined' && $.fn.select2) {
        $('[data-select2-selector]').select2({ width: '100%' });
    }

    document.querySelectorAll('.btn-delete').forEach(function(btn) {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const form = this.closest('form');
            Swal.fire({
                title: 'Hapus Data Rencana?',
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
