@extends('layouts.simoli')

@section('title', 'Data Pemeliharaan Land Aplikasi')
@section('page-title', 'Data Pemeliharaan Land Aplikasi')
@section('page-description', 'Pencatatan dan Monitoring Pemeliharaan Kolam & Bed Land Aplikasi')

@section('breadcrumb')
    <li>Input Data</li>
    <li class="separator">/</li>
    <li>Pemeliharaan</li>
@endsection

@section('page-actions')
    <a href="{{ route('pemeliharaan.create') }}" class="btn-ptpn btn-ptpn-primary">
        <i class="feather-plus" style="font-size:15px;"></i>
        <span>Tambah Data</span>
    </a>
@endsection

@section('styles')
<link rel="stylesheet" type="text/css" href="{{ asset('duraluxadmin/assets/vendors/css/select2.min.css') }}" />
<link rel="stylesheet" type="text/css" href="{{ asset('duraluxadmin/assets/vendors/css/select2-theme.min.css') }}" />
<style>
    /* ================================================================
       PEMELIHARAAN INDEX — PTPN GREEN THEME
       ================================================================ */
    @keyframes fadeUpCard {
        from { opacity:0; transform:translateY(14px); }
        to   { opacity:1; transform:translateY(0); }
    }

    /* === KPI SUMMARY CARDS === */
    .pm-kpi {
        background: #ffffff;
        border-radius: 18px;
        border: 1px solid rgba(22,163,74,.1);
        box-shadow: 0 2px 12px rgba(22,163,74,.06);
        padding: 18px 18px;
        height: 100%;
        transition: all .25s ease;
        position: relative;
        overflow: hidden;
        animation: fadeUpCard .4s ease-out both;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }
    .pm-kpi::after {
        content:'';position:absolute;top:0;left:0;right:0;height:3px;border-radius:18px 18px 0 0;
    }
    .pm-kpi-green::after  { background: linear-gradient(90deg,#16a34a,#4ade80); }
    .pm-kpi-blue::after   { background: linear-gradient(90deg,#1d4ed8,#60a5fa); }
    .pm-kpi-amber::after  { background: linear-gradient(90deg,#d97706,#fbbf24); }
    .pm-kpi-teal::after   { background: linear-gradient(90deg,#0d9488,#2dd4bf); }
    .pm-kpi-indigo::after { background: linear-gradient(90deg,#4f46e5,#818cf8); }
    .pm-kpi-purple::after { background: linear-gradient(90deg,#9333ea,#c084fc); }
    
    .pm-kpi:hover { transform:translateY(-4px); box-shadow:0 12px 28px rgba(22,163,74,.12); }

    .pm-kpi-label { font-size:10.5px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.5px;margin-bottom:4px; }
    .pm-kpi-value { font-family:'Outfit',sans-serif;font-size:clamp(18px,1.8vw+8px,24px);font-weight:900;line-height:1.1;letter-spacing:-.5px;margin-bottom:4px; }
    .pm-kpi-sub   { font-size:11px;color:#6b7280; }
    .pm-kpi-icon  { width:42px;height:42px;border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:18px;flex-shrink:0; }
    
    .kpi-icon-g  { background:#f0fdf4;color:#16a34a;border:1px solid #bbf7d0; }
    .kpi-icon-b  { background:#eff6ff;color:#1d4ed8;border:1px solid #bfdbfe; }
    .kpi-icon-a  { background:#fffbeb;color:#b45309;border:1px solid #fde68a; }
    .kpi-icon-t  { background:#f0fdfa;color:#0d9488;border:1px solid #99f6e4; }
    .kpi-icon-i  { background:#eef2ff;color:#4f46e5;border:1px solid #c7d2fe; }
    .kpi-icon-p  { background:#faf5ff;color:#9333ea;border:1px solid #e9d5ff; }

    /* === FILTER CARD === */
    .pm-filter {
        background: #ffffff;
        border-radius: 16px;
        border: 1px solid rgba(22,163,74,.15);
        box-shadow: 0 2px 10px rgba(22,163,74,.05);
        padding: 18px 20px;
        margin-bottom: 20px;
    }

    .pm-filter .form-control,
    .pm-filter .form-select {
        border-radius: 12px;
        border: 1.5px solid #e5e7eb;
        font-size: 13px;
        padding: 9px 14px;
        transition: all .2s ease;
        background: #f9fafb;
    }

    .pm-filter .form-control:focus,
    .pm-filter .form-select:focus {
        border-color: rgba(22,163,74,.5);
        background: #ffffff;
        box-shadow: 0 0 0 3px rgba(34,197,94,.08);
    }

    .pm-filter label { font-size:11.5px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.4px;margin-bottom:5px; }

    .pm-filter .select2-container--default .select2-selection--single {
        height: 42px; border-radius: 12px; border: 1.5px solid #e5e7eb;
        background: #f9fafb; padding: 6px 12px;
    }
    .pm-filter .select2-container--default .select2-selection--single .select2-selection__rendered { line-height: 28px; font-size: 13px; }
    .pm-filter .select2-container--default .select2-selection--single .select2-selection__arrow { height: 40px; }

    /* === DATA TABLE CARD === */
    .pm-table-card {
        background: #ffffff;
        border-radius: 18px;
        border: 1px solid rgba(22,163,74,.1);
        box-shadow: 0 2px 12px rgba(22,163,74,.06);
        overflow: hidden;
    }

    .pm-table-header {
        padding: 16px 20px;
        border-bottom: 1px solid rgba(22,163,74,.08);
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 10px;
    }

    .pm-table-title {
        font-family: 'Outfit', sans-serif;
        font-size: 15px;
        font-weight: 800;
        color: #14532d;
        display: flex;
        align-items: center;
        gap: 8px;
        margin: 0;
    }

    .pm-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
        font-size: clamp(11.5px, .7vw+9px, 13px);
    }

    .pm-table thead th {
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

    .pm-table tbody td {
        padding: 12px 16px;
        border-bottom: 1px solid rgba(22,163,74,.07);
        vertical-align: middle;
    }

    .pm-table tbody tr { cursor: pointer; transition: background .15s ease; }
    .pm-table tbody tr:hover td { background: rgba(22,163,74,.03); }
    .pm-table tbody tr:last-child td { border-bottom: none; }

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

    /* Jenis Chips */
    .jenis-chip-mekanis {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 4px 10px;
        border-radius: 8px;
        font-size: 11px;
        font-weight: 800;
        background: #eff6ff;
        border: 1px solid #bfdbfe;
        color: #1d4ed8;
    }

    .jenis-chip-manual {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 4px 10px;
        border-radius: 8px;
        font-size: 11px;
        font-weight: 800;
        background: #fffbeb;
        border: 1px solid #fde68a;
        color: #b45309;
    }

    /* Action btns */
    .tbl-action { display:inline-flex;align-items:center;justify-content:center;width:32px;height:32px;border-radius:9px;border:none;cursor:pointer;transition:all .2s ease;font-size:14px; }
    .tbl-action-view { background:#f0fdf4;color:#16a34a;border:1px solid #bbf7d0; }
    .tbl-action-view:hover { background:#dcfce7;transform:scale(1.1); }

    /* Empty state */
    .empty-pm {
        padding: 60px 24px;
        text-align: center;
    }
    .empty-pm-icon { font-size: 52px; color: #d1d5db; margin-bottom: 14px; }

    /* Pagination */
    .pm-pagination { display:flex;align-items:center;justify-content:space-between;padding:14px 20px;border-top:1px solid rgba(22,163,74,.08);flex-wrap:wrap;gap:10px; }

    /* Dark mode */
    html.app-skin-dark .pm-kpi,
    html.app-skin-dark .pm-filter,
    html.app-skin-dark .pm-table-card { background:#0a2317 !important; border-color:rgba(34,197,94,.15) !important; }
    html.app-skin-dark .pm-table-title,
    html.app-skin-dark .pm-kpi-value { color:#d1fae5 !important; }
    html.app-skin-dark .pm-table thead th { background:#021a0b !important; }
    html.app-skin-dark .pm-table tbody td { border-color:rgba(34,197,94,.07) !important; color:#d1fae5; }
    html.app-skin-dark .pm-table tbody tr:hover td { background:rgba(34,197,94,.04) !important; }
    html.app-skin-dark .pm-filter .form-control,
    html.app-skin-dark .pm-filter .form-select { background:#0e3b26;border-color:rgba(34,197,94,.2);color:#d1fae5; }
    html.app-skin-dark .jenis-chip-mekanis { background:#0e3b26;border-color:rgba(37,99,235,.2);color:#93c5fd; }
    html.app-skin-dark .jenis-chip-manual  { background:#1c1000;border-color:rgba(217,119,6,.2);color:#fde68a; }
</style>
@endsection

@section('content')
<article class="pemeliharaan-index">

    {{-- ================================================================
         1. KPI SUMMARY CARDS
         ================================================================ --}}
    <section aria-label="Ringkasan Data Pemeliharaan" class="mb-4">
        <div class="row g-3">
            <div class="col-xxl-2 col-xl-4 col-md-4 col-6" style="animation-delay:.03s">
                <div class="pm-kpi pm-kpi-green">
                    <div class="d-flex align-items-start justify-content-between mb-2">
                        <div>
                            <div class="pm-kpi-label">Total Data</div>
                            <div class="pm-kpi-value" style="color:#16a34a;">{{ number_format($totalRecords) }}</div>
                        </div>
                        <div class="pm-kpi-icon kpi-icon-g"><i class="feather-database"></i></div>
                    </div>
                    <div class="pm-kpi-sub">record pemeliharaan</div>
                </div>
            </div>

            <div class="col-xxl-2 col-xl-4 col-md-4 col-6" style="animation-delay:.06s">
                <div class="pm-kpi pm-kpi-blue">
                    <div class="d-flex align-items-start justify-content-between mb-2">
                        <div>
                            <div class="pm-kpi-label">Total Flat Bed</div>
                            <div class="pm-kpi-value" style="color:#1d4ed8;">{{ number_format($totalFlatBed) }}</div>
                        </div>
                        <div class="pm-kpi-icon kpi-icon-b"><i class="feather-layers"></i></div>
                    </div>
                    <div class="pm-kpi-sub">flat bed</div>
                </div>
            </div>

            <div class="col-xxl-2 col-xl-4 col-md-4 col-6" style="animation-delay:.09s">
                <div class="pm-kpi pm-kpi-amber">
                    <div class="d-flex align-items-start justify-content-between mb-2">
                        <div>
                            <div class="pm-kpi-label">Total Long Bed</div>
                            <div class="pm-kpi-value" style="color:#b45309;">{{ number_format($totalLongBed) }}</div>
                        </div>
                        <div class="pm-kpi-icon kpi-icon-a"><i class="feather-maximize-2"></i></div>
                    </div>
                    <div class="pm-kpi-sub">long bed</div>
                </div>
            </div>

            <div class="col-xxl-2 col-xl-4 col-md-4 col-6" style="animation-delay:.12s">
                <div class="pm-kpi pm-kpi-teal">
                    <div class="d-flex align-items-start justify-content-between mb-2">
                        <div>
                            <div class="pm-kpi-label">Total HK</div>
                            <div class="pm-kpi-value" style="color:#0d9488;">{{ number_format($totalHK) }}</div>
                        </div>
                        <div class="pm-kpi-icon kpi-icon-t"><i class="feather-users"></i></div>
                    </div>
                    <div class="pm-kpi-sub">hari kerja</div>
                </div>
            </div>

            <div class="col-xxl-2 col-xl-4 col-md-4 col-6" style="animation-delay:.15s">
                <div class="pm-kpi pm-kpi-indigo">
                    <div class="d-flex align-items-start justify-content-between mb-2">
                        <div>
                            <div class="pm-kpi-label">Mekanis</div>
                            <div class="pm-kpi-value" style="color:#4f46e5;">{{ number_format($totalMekanis) }}</div>
                        </div>
                        <div class="pm-kpi-icon kpi-icon-i"><i class="feather-settings"></i></div>
                    </div>
                    <div class="pm-kpi-sub">record mekanis</div>
                </div>
            </div>

            <div class="col-xxl-2 col-xl-4 col-md-4 col-6" style="animation-delay:.18s">
                <div class="pm-kpi pm-kpi-purple">
                    <div class="d-flex align-items-start justify-content-between mb-2">
                        <div>
                            <div class="pm-kpi-label">Manual</div>
                            <div class="pm-kpi-value" style="color:#9333ea;">{{ number_format($totalManual) }}</div>
                        </div>
                        <div class="pm-kpi-icon kpi-icon-p"><i class="feather-user"></i></div>
                    </div>
                    <div class="pm-kpi-sub">record manual</div>
                </div>
            </div>
        </div>
    </section>

    {{-- ================================================================
         2. FILTER BAR
         ================================================================ --}}
    <div class="pm-filter no-print">
        <form action="{{ route('pemeliharaan.index') }}" method="GET">
            <div class="row g-3 align-items-end">
                @if(Auth::user()->isAdmin())
                <div class="col-lg-3 col-md-6">
                    <label>
                        <i class="feather-home me-1" style="color:#16a34a;"></i> Unit PKS
                    </label>
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

                <div class="col-lg-2 col-md-3 col-6">
                    <label><i class="feather-tool me-1" style="color:#16a34a;"></i> Jenis</label>
                    <select name="jenis" class="form-select">
                        <option value="">Semua Jenis</option>
                        <option value="1" {{ request('jenis') == '1' ? 'selected' : '' }}>Mekanis</option>
                        <option value="2" {{ request('jenis') == '2' ? 'selected' : '' }}>Manual</option>
                    </select>
                </div>

                <div class="col-lg-2 col-md-3 col-6">
                    <label><i class="feather-calendar me-1" style="color:#16a34a;"></i> Bulan</label>
                    <select name="bulan" class="form-select">
                        <option value="">Semua Bulan</option>
                        @foreach(['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'] as $i => $bln)
                            <option value="{{ $i+1 }}" {{ request('bulan') == ($i+1) ? 'selected' : '' }}>{{ $bln }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-lg-1 col-md-2 col-6">
                    <label><i class="feather-hash me-1" style="color:#16a34a;"></i> Tahun</label>
                    <select name="tahun" class="form-select">
                        <option value="">Semua</option>
                        @for($y = date('Y'); $y >= 2020; $y--)
                            <option value="{{ $y }}" {{ request('tahun') == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endfor
                    </select>
                </div>

                <div class="col-lg-2 col-md-3 col-6">
                    <label>Dari Tanggal</label>
                    <input type="date" name="dari_tanggal" class="form-control" value="{{ request('dari_tanggal') }}">
                </div>

                <div class="col-lg-2 col-md-3 col-6">
                    <label>Sampai Tanggal</label>
                    <input type="date" name="sampai_tanggal" class="form-control" value="{{ request('sampai_tanggal') }}">
                </div>

                <div class="col-lg-auto">
                    <label>&nbsp;</label>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn-ptpn btn-ptpn-primary" style="padding:9px 16px;">
                            <i class="feather-filter" style="font-size:14px;"></i>
                            <span class="d-none d-sm-inline">Filter</span>
                        </button>
                        <a href="{{ route('pemeliharaan.index') }}" class="btn-ptpn btn-ptpn-outline" style="padding:9px 12px;" title="Reset">
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
    <div class="pm-table-card">
        <div class="pm-table-header">
            <h3 class="pm-table-title">
                <i class="feather-tool" style="color:#16a34a;font-size:18px;"></i>
                Daftar Data Pemeliharaan
            </h3>
            <div class="d-flex align-items-center gap-3">
                <small style="color:#6b7280;font-size:11.5px;">
                    Menampilkan {{ $pemeliharaan->firstItem() ?? 0 }}–{{ $pemeliharaan->lastItem() ?? 0 }}
                    dari <strong>{{ $pemeliharaan->total() }}</strong> data
                </small>
                <span class="mod-pill mod-pill-ok" style="font-size:10.5px;">
                    <i class="feather-database" style="font-size:11px;"></i>
                    {{ $pemeliharaan->total() }} Total
                </span>
            </div>
        </div>

        @if($pemeliharaan->count() > 0)
        <div class="table-responsive" style="-webkit-overflow-scrolling: touch;">
            <table class="pm-table" role="table" aria-label="Daftar Data Pemeliharaan">
                <thead>
                    <tr>
                        <th style="width:44px;text-align:center;">#</th>
                        <th style="min-width:120px;">Tanggal</th>
                        @if(Auth::user()->isAdmin())
                        <th style="width:80px;text-align:center;">PKS</th>
                        @endif
                        <th style="min-width:110px;">Jenis</th>
                        <th style="min-width:100px;">No. Bak</th>
                        <th style="min-width:100px;">Blok</th>
                        <th style="width:100px;text-align:right;">Flat Bed</th>
                        <th style="width:100px;text-align:right;">Long Bed</th>
                        <th style="width:90px;text-align:right;">HK</th>
                        <th style="width:80px;text-align:center;">Detail</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($pemeliharaan as $index => $item)
                    <tr onclick="window.location='{{ route('pemeliharaan.show', $item->id) }}'">
                        <td style="text-align:center;font-weight:700;color:#9ca3af;font-size:12px;">
                            {{ $pemeliharaan->firstItem() + $index }}
                        </td>

                        <td>
                            <div style="font-weight:700;color:#14532d;font-size:13px;">
                                {{ $item->tanggal ? $item->tanggal->format('d M Y') : '-' }}
                            </div>
                        </td>

                        @if(Auth::user()->isAdmin())
                        <td style="text-align:center;">
                            @if($item->pks)
                                <span class="pks-badge">{{ $item->pks->AKRO }}</span>
                            @else
                                <span style="color:#9ca3af;font-size:11px;">N/A</span>
                            @endif
                        </td>
                        @endif

                        <td>
                            @if($item->jenis_pemeliharaan == '1')
                                <span class="jenis-chip-mekanis">
                                    <i class="feather-settings"></i> Mekanis
                                </span>
                            @elseif($item->jenis_pemeliharaan == '2')
                                <span class="jenis-chip-manual">
                                    <i class="feather-user"></i> Manual
                                </span>
                            @else
                                <span style="color:#9ca3af;">-</span>
                            @endif
                        </td>

                        <td>
                            <span style="font-weight:700;color:#1d4ed8;">{{ $item->no_bak ?? '-' }}</span>
                        </td>

                        <td>
                            <span style="font-weight:700;color:#14532d;">{{ $item->blok ?? '-' }}</span>
                        </td>

                        <td style="text-align:right;font-weight:700;color:#16a34a;">
                            {{ number_format($item->flat_bed ?? 0) }}
                        </td>

                        <td style="text-align:right;font-weight:700;color:#b45309;">
                            {{ number_format($item->long_bed ?? 0) }}
                        </td>

                        <td style="text-align:right;font-weight:700;color:#0d9488;">
                            {{ $item->jumlah_hk ?? '-' }}
                        </td>

                        <td style="text-align:center;" onclick="event.stopPropagation();">
                            <a href="{{ route('pemeliharaan.show', $item->id) }}"
                               class="tbl-action tbl-action-view" title="Lihat Detail">
                                <i class="feather-eye"></i>
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div class="pm-pagination">
            <small style="color:#6b7280;font-size:11.5px;">
                Halaman <strong>{{ $pemeliharaan->currentPage() }}</strong> dari <strong>{{ $pemeliharaan->lastPage() }}</strong>
            </small>
            <div>{{ $pemeliharaan->links('pagination::bootstrap-5') }}</div>
        </div>

        @else
        <div class="empty-pm">
            <div class="empty-pm-icon"><i class="feather-inbox"></i></div>
            <h5 style="color:#374151;font-family:'Outfit',sans-serif;font-weight:800;margin-bottom:8px;">Belum Ada Data</h5>
            <p style="color:#6b7280;font-size:13.5px;margin-bottom:20px;">Data pemeliharaan belum tersedia atau tidak sesuai filter yang dipilih.</p>
            <a href="{{ route('pemeliharaan.create') }}" class="btn-ptpn btn-ptpn-primary" style="display:inline-flex;">
                <i class="feather-plus" style="font-size:15px;"></i>
                Tambah Data Pertama
            </a>
        </div>
        @endif
    </div>

</article>
@endsection

@section('scripts')
<script src="{{ asset('duraluxadmin/assets/vendors/js/select2.min.js') }}"></script>
<script src="{{ asset('duraluxadmin/assets/vendors/js/select2-active.min.js') }}"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    if (typeof $ !== 'undefined' && $.fn.select2) {
        $('[data-select2-selector]').select2({ width: '100%' });
    }
});
</script>
@endsection