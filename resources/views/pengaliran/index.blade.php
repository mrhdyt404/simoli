@extends('layouts.simoli')

@section('title', 'Data Pengaliran Land Aplikasi')
@section('page-title', 'Data Pengaliran Land Aplikasi')
@section('page-description', 'Rekap Harian Pengaliran Limbah ke Land Aplikasi seluruh PKS')

@section('breadcrumb')
    <li>Input Data</li>
    <li class="separator">/</li>
    <li>Pengaliran</li>
@endsection

@section('page-actions')
    <div class="d-flex align-items-center gap-2 flex-wrap">
        <a href="{{ route('report-pengaliran') }}" class="btn-ptpn btn-ptpn-outline" style="font-weight:700;">
            <i class="feather-printer" style="font-size:14px;"></i>
            <span>Export / Cetak PDF</span>
        </a>
        <a href="{{ route('pengaliran.create') }}" class="btn-ptpn btn-ptpn-primary">
            <i class="feather-plus" style="font-size:15px;"></i>
            <span>Tambah Data</span>
        </a>
    </div>
@endsection

@section('styles')
<link rel="stylesheet" type="text/css" href="{{ asset('duraluxadmin/assets/vendors/css/select2.min.css') }}" />
<link rel="stylesheet" type="text/css" href="{{ asset('duraluxadmin/assets/vendors/css/select2-theme.min.css') }}" />
<style>
    /* ================================================================
       PENGALIRAN INDEX — PTPN GREEN THEME
       ================================================================ */
    @keyframes fadeUpCard {
        from { opacity:0; transform:translateY(14px); }
        to   { opacity:1; transform:translateY(0); }
    }

    /* === KPI SUMMARY CARDS === */
    .pg-kpi {
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
    .pg-kpi::after {
        content:'';position:absolute;top:0;left:0;right:0;height:3px;border-radius:18px 18px 0 0;
    }
    .pg-kpi-green::after  { background: linear-gradient(90deg,#16a34a,#4ade80); }
    .pg-kpi-blue::after   { background: linear-gradient(90deg,#1d4ed8,#60a5fa); }
    .pg-kpi-gold::after   { background: linear-gradient(90deg,#d97706,#fbbf24); }
    .pg-kpi-red::after    { background: linear-gradient(90deg,#dc2626,#f87171); }
    .pg-kpi-teal::after   { background: linear-gradient(90deg,#0d9488,#2dd4bf); }
    .pg-kpi:hover { transform:translateY(-4px); box-shadow:0 12px 28px rgba(22,163,74,.12); }

    .pg-kpi-label { font-size:10.5px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.5px;margin-bottom:5px; }
    .pg-kpi-value { font-family:'Outfit',sans-serif;font-size:clamp(20px,2vw+10px,26px);font-weight:900;line-height:1.1;letter-spacing:-.5px;margin-bottom:6px; }
    .pg-kpi-sub   { font-size:11px;color:#6b7280; }
    .pg-kpi-icon  { width:44px;height:44px;border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:18px;flex-shrink:0; }
    .kpi-icon-g  { background:#f0fdf4;color:#16a34a;border:1px solid #bbf7d0; }
    .kpi-icon-b  { background:#eff6ff;color:#1d4ed8;border:1px solid #bfdbfe; }
    .kpi-icon-a  { background:#fffbeb;color:#b45309;border:1px solid #fde68a; }
    .kpi-icon-r  { background:#fef2f2;color:#dc2626;border:1px solid #fca5a5; }
    .kpi-icon-t  { background:#f0fdfa;color:#0d9488;border:1px solid #99f6e4; }

    /* === FILTER CARD === */
    .pg-filter {
        background: #ffffff;
        border-radius: 16px;
        border: 1px solid rgba(22,163,74,.15);
        box-shadow: 0 2px 10px rgba(22,163,74,.05);
        padding: 18px 20px;
        margin-bottom: 20px;
    }

    .pg-filter .form-control,
    .pg-filter .form-select {
        border-radius: 12px;
        border: 1.5px solid #e5e7eb;
        font-size: 13px;
        padding: 9px 14px;
        transition: all .2s ease;
        background: #f9fafb;
    }

    .pg-filter .form-control:focus,
    .pg-filter .form-select:focus {
        border-color: rgba(22,163,74,.5);
        background: #ffffff;
        box-shadow: 0 0 0 3px rgba(34,197,94,.08);
    }

    .pg-filter label { font-size:11.5px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.4px;margin-bottom:5px; }

    /* Select2 style match */
    .pg-filter .select2-container--default .select2-selection--single {
        height: 42px; border-radius: 12px; border: 1.5px solid #e5e7eb;
        background: #f9fafb; padding: 6px 12px;
    }
    .pg-filter .select2-container--default .select2-selection--single .select2-selection__rendered { line-height: 28px; font-size: 13px; }
    .pg-filter .select2-container--default .select2-selection--single .select2-selection__arrow { height: 40px; }

    /* === DATA TABLE CARD === */
    .pg-table-card {
        background: #ffffff;
        border-radius: 18px;
        border: 1px solid rgba(22,163,74,.1);
        box-shadow: 0 2px 12px rgba(22,163,74,.06);
        overflow: hidden;
    }

    .pg-table-header {
        padding: 16px 20px;
        border-bottom: 1px solid rgba(22,163,74,.08);
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 10px;
    }

    .pg-table-title {
        font-family: 'Outfit', sans-serif;
        font-size: 15px;
        font-weight: 800;
        color: #14532d;
        display: flex;
        align-items: center;
        gap: 8px;
        margin: 0;
    }

    .pg-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
        font-size: clamp(11.5px, .7vw+9px, 13px);
    }

    .pg-table thead th {
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

    .pg-table tbody td {
        padding: 12px 16px;
        border-bottom: 1px solid rgba(22,163,74,.07);
        vertical-align: middle;
    }

    .pg-table tbody tr { cursor: pointer; transition: background .15s ease; }
    .pg-table tbody tr:hover td { background: rgba(22,163,74,.03); }
    .pg-table tbody tr:last-child td { border-bottom: none; }

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

    /* Blok badge */
    .blok-chip {
        display: inline-block;
        padding: 3px 10px;
        border-radius: 7px;
        background: #f0fdf4;
        border: 1px solid #bbf7d0;
        font-size: 11.5px;
        font-weight: 800;
        color: #166534;
    }

    /* Bak badge */
    .bak-chip {
        display: inline-block;
        padding: 3px 10px;
        border-radius: 7px;
        background: #eff6ff;
        border: 1px solid #bfdbfe;
        font-size: 11px;
        font-weight: 700;
        color: #1d4ed8;
    }

    /* Vol bar */
    .vol-bar { height: 5px; border-radius: 5px; background: rgba(22,163,74,.1); overflow: hidden; margin-top: 4px; }
    .vol-bar-fill { height: 100%; border-radius: 5px; background: linear-gradient(90deg,#16a34a,#4ade80); transition: width 1s ease; }

    /* Action btns */
    .tbl-action { display:inline-flex;align-items:center;justify-content:center;width:32px;height:32px;border-radius:9px;border:none;cursor:pointer;transition:all .2s ease;font-size:14px; }
    .tbl-action-view  { background:#f0fdf4;color:#16a34a;border:1px solid #bbf7d0; }
    .tbl-action-edit  { background:#eff6ff;color:#1d4ed8;border:1px solid #bfdbfe; }
    .tbl-action-view:hover { background:#dcfce7;transform:scale(1.1); }
    .tbl-action-edit:hover { background:#dbeafe;transform:scale(1.1); }

    /* Empty state */
    .empty-pg {
        padding: 60px 24px;
        text-align: center;
    }
    .empty-pg-icon { font-size: 52px; color: #d1d5db; margin-bottom: 14px; }

    /* Pagination */
    .pg-pagination { display:flex;align-items:center;justify-content:space-between;padding:14px 20px;border-top:1px solid rgba(22,163,74,.08);flex-wrap:wrap;gap:10px; }

    /* Dark mode */
    html.app-skin-dark .pg-kpi,
    html.app-skin-dark .pg-filter,
    html.app-skin-dark .pg-table-card { background:#0a2317 !important; border-color:rgba(34,197,94,.15) !important; }
    html.app-skin-dark .pg-table-title,
    html.app-skin-dark .pg-kpi-value { color:#d1fae5 !important; }
    html.app-skin-dark .pg-table thead th { background:#021a0b !important; }
    html.app-skin-dark .pg-table tbody td { border-color:rgba(34,197,94,.07) !important; color:#d1fae5; }
    html.app-skin-dark .pg-table tbody tr:hover td { background:rgba(34,197,94,.04) !important; }
    html.app-skin-dark .pg-filter .form-control,
    html.app-skin-dark .pg-filter .form-select { background:#0e3b26;border-color:rgba(34,197,94,.2);color:#d1fae5; }
    html.app-skin-dark .blok-chip { background:#0e3b26;border-color:rgba(34,197,94,.2);color:#86efac; }
    html.app-skin-dark .bak-chip  { background:#0a2317;border-color:rgba(37,99,235,.2);color:#93c5fd; }
</style>
@endsection

@section('content')
<article class="pengaliran-index">

    {{-- ================================================================
         1. KPI SUMMARY CARDS
         ================================================================ --}}
    <section aria-label="Ringkasan Data Pengaliran" class="mb-4">
        <div class="row g-3">
            <div class="col-xxl col-xl-4 col-md-4 col-6" style="animation-delay:.03s">
                <div class="pg-kpi pg-kpi-green">
                    <div class="d-flex align-items-start justify-content-between mb-3">
                        <div>
                            <div class="pg-kpi-label">Total Data</div>
                            <div class="pg-kpi-value" style="color:#16a34a;">{{ number_format($totalRecords) }}</div>
                        </div>
                        <div class="pg-kpi-icon kpi-icon-g"><i class="feather-database"></i></div>
                    </div>
                    <div class="pg-kpi-sub">record pengaliran</div>
                </div>
            </div>
            <div class="col-xxl col-xl-4 col-md-4 col-6" style="animation-delay:.06s">
                <div class="pg-kpi pg-kpi-blue">
                    <div class="d-flex align-items-start justify-content-between mb-3">
                        <div>
                            <div class="pg-kpi-label">Vol. Dihasilkan</div>
                            <div class="pg-kpi-value" style="color:#1d4ed8;">{{ number_format($totalVolDihasilkan) }}</div>
                        </div>
                        <div class="pg-kpi-icon kpi-icon-b"><i class="feather-trending-up"></i></div>
                    </div>
                    <div class="pg-kpi-sub">m³ total</div>
                </div>
            </div>
            <div class="col-xxl col-xl-4 col-md-4 col-6" style="animation-delay:.09s">
                <div class="pg-kpi pg-kpi-green">
                    <div class="d-flex align-items-start justify-content-between mb-3">
                        <div>
                            <div class="pg-kpi-label">Vol. Dialirkan</div>
                            <div class="pg-kpi-value" style="color:#059669;">{{ number_format($totalVolDialirkan) }}</div>
                        </div>
                        <div class="pg-kpi-icon kpi-icon-g"><i class="feather-droplet"></i></div>
                    </div>
                    <div class="pg-kpi-sub">m³ total</div>
                </div>
            </div>
            <div class="col-xxl col-xl-6 col-md-6 col-6" style="animation-delay:.12s">
                <div class="pg-kpi pg-kpi-gold">
                    <div class="d-flex align-items-start justify-content-between mb-3">
                        <div>
                            <div class="pg-kpi-label">Total Flat Bed</div>
                            <div class="pg-kpi-value" style="color:#b45309;">{{ number_format($totalFlatBed) }}</div>
                        </div>
                        <div class="pg-kpi-icon kpi-icon-a"><i class="feather-layers"></i></div>
                    </div>
                    <div class="pg-kpi-sub">flat bed</div>
                </div>
            </div>
            <div class="col-xxl col-xl-6 col-md-6 col-6" style="animation-delay:.15s">
                <div class="pg-kpi pg-kpi-teal">
                    <div class="d-flex align-items-start justify-content-between mb-3">
                        <div>
                            <div class="pg-kpi-label">Luas Area</div>
                            <div class="pg-kpi-value" style="color:#0d9488;">{{ number_format($totalLuasArea, 1) }}</div>
                        </div>
                        <div class="pg-kpi-icon kpi-icon-t"><i class="feather-map"></i></div>
                    </div>
                    <div class="pg-kpi-sub">Ha total</div>
                </div>
            </div>
        </div>
    </section>

    {{-- ================================================================
         2. FILTER BAR
         ================================================================ --}}
    <div class="pg-filter no-print">
        <form action="{{ route('pengaliran.index') }}" method="GET">
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

                <div class="col-lg-2 col-md-3">
                    <label>&nbsp;</label>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn-ptpn btn-ptpn-primary flex-grow-1" style="padding:9px 14px;">
                            <i class="feather-filter" style="font-size:14px;"></i>
                            <span class="d-none d-sm-inline">Filter</span>
                        </button>
                        <a href="{{ route('pengaliran.index') }}" class="btn-ptpn btn-ptpn-outline" style="padding:9px 12px;" title="Reset">
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
    <div class="pg-table-card">
        <div class="pg-table-header">
            <h3 class="pg-table-title">
                <i class="feather-list" style="color:#16a34a;font-size:18px;"></i>
                Daftar Data Pengaliran
            </h3>
            <div class="d-flex align-items-center gap-3">
                <small style="color:#6b7280;font-size:11.5px;">
                    Menampilkan {{ $pengaliran->firstItem() ?? 0 }}–{{ $pengaliran->lastItem() ?? 0 }}
                    dari <strong>{{ $pengaliran->total() }}</strong> data
                </small>
                <span class="mod-pill mod-pill-ok" style="font-size:10.5px;">
                    <i class="feather-database" style="font-size:11px;"></i>
                    {{ $pengaliran->total() }} Total
                </span>
            </div>
        </div>

        @if($pengaliran->count() > 0)
        <div class="table-responsive" style="-webkit-overflow-scrolling: touch;">
            <table class="pg-table" role="table" aria-label="Daftar Data Pengaliran">
                <thead>
                    <tr>
                        <th style="width:44px;text-align:center;">#</th>
                        <th style="min-width:120px;">Tanggal &amp; Jam</th>
                        @if(Auth::user()->isAdmin())
                        <th style="width:80px;text-align:center;">PKS</th>
                        @endif
                        <th style="min-width:110px;">Block Pengaliran</th>
                        <th style="min-width:130px;">Bak Distribusi</th>
                        <th style="width:100px;text-align:center;">Bed Dialirkan</th>
                        <th style="min-width:130px;">Vol. Dialirkan</th>
                        <th style="min-width:180px;">Keterangan</th>
                        <th style="width:90px;text-align:center;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($pengaliran as $index => $item)
                    <tr onclick="window.location='{{ route('pengaliran.show', $item->id_pengaliran) }}'">
                        <td style="text-align:center;font-weight:700;color:#9ca3af;font-size:12px;">
                            {{ $pengaliran->firstItem() + $index }}
                        </td>

                        <td>
                            <div style="font-weight:700;color:#14532d;font-size:13px;">
                                {{ $item->tanggal ? $item->tanggal->format('d M Y') : '-' }}
                            </div>
                            <small style="color:#6b7280;font-size:11px;">
                                <i class="feather-clock" style="font-size:10px;"></i>
                                {{ $item->jam_mulai ? substr($item->jam_mulai,0,5) : '-' }} — {{ $item->jam_selesai ? substr($item->jam_selesai,0,5) : '-' }}
                            </small>
                        </td>

                        @if(Auth::user()->isAdmin())
                        <td style="text-align:center;">
                            @if($item->pks)
                                <span class="pks-badge">{{ $item->pks->akro ?? $item->pks->AKRO }}</span>
                            @else
                                <span style="color:#9ca3af;font-size:11px;">N/A</span>
                            @endif
                        </td>
                        @endif

                        <td>
                            <span class="blok-chip">{{ $item->blok ?? '-' }}</span>
                        </td>

                        <td>
                            <span class="bak-chip">{{ $item->no_bak ?? '-' }}</span>
                        </td>

                        <td style="text-align:center;">
                            <span style="font-family:'Outfit',sans-serif;font-size:16px;font-weight:900;color:#16a34a;">
                                {{ number_format($item->flat_bed ?? 0, 0, ',', '.') }}
                            </span>
                            <span style="font-size:11px;color:#6b7280;font-weight:600;"> Bed</span>
                        </td>

                        <td>
                            @php 
                                $maxVol = $totalVolDialirkan > 0 ? $totalVolDialirkan : 1; 
                                $pct = min(100, ($item->vol_limbah_dialirkan / $maxVol) * 100); 
                                $debitSk = $item->pks && $item->pks->perizinanLa ? (float)$item->pks->perizinanLa->debit_maksimal_harian : null;
                                $isOver = $debitSk && ($item->vol_limbah_dialirkan > $debitSk);
                                $isUnder = $debitSk && ($item->vol_limbah_dialirkan > 0 && $item->vol_limbah_dialirkan < (0.4 * $debitSk));
                            @endphp
                            <div style="font-weight:800;font-size:13px;color:#1f2937;">
                                {{ number_format($item->vol_limbah_dialirkan, 0, ',', '.') }}
                                <span style="font-size:11px;font-weight:600;color:#6b7280;">m³</span>
                            </div>
                            <div class="vol-bar"><div class="vol-bar-fill" style="width:{{ $pct }}%"></div></div>
                            @if($isOver)
                                <span class="badge bg-danger" style="font-size:9.5px;padding:2px 6px;border-radius:4px;margin-top:3px;display:inline-block;" title="Volume dialirkan melebihi kuota debit SK Izin LA ({{ number_format($debitSk) }} m³/hari)">
                                    ⚠️ Overflow (+{{ number_format($item->vol_limbah_dialirkan - $debitSk) }} m³)
                                </span>
                            @elseif($isUnder)
                                <span class="badge bg-warning text-dark" style="font-size:9.5px;padding:2px 6px;border-radius:4px;margin-top:3px;display:inline-block;" title="Volume dialirkan di bawah 40% dari kuota SK Izin LA ({{ number_format($debitSk) }} m³/hari)">
                                    ⚠️ Underflow
                                </span>
                            @endif
                        </td>

                        <td>
                            <small style="color:#6b7280;font-size:11.5px;display:block;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;max-width:200px;"
                                   title="{{ $item->keterangan }}">
                                {{ $item->keterangan ?? 'Pengaliran Limbah lancar' }}
                            </small>
                        </td>

                        <td style="text-align:center;" onclick="event.stopPropagation();">
                            <div class="d-flex gap-1 justify-content-center">
                                <a href="{{ route('pengaliran.show', $item->id_pengaliran) }}"
                                   class="tbl-action tbl-action-view" title="Lihat Detail">
                                    <i class="feather-eye"></i>
                                </a>
                                @if(Auth::user()->isAdmin() || Auth::user()->id_pks == $item->id_pks)
                                <a href="{{ route('pengaliran.edit', $item->id_pengaliran) }}"
                                   class="tbl-action tbl-action-edit" title="Edit Data">
                                    <i class="feather-edit-2"></i>
                                </a>
                                <form action="{{ route('pengaliran.destroy', $item->id_pengaliran) }}" method="POST" class="d-inline form-delete">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="tbl-action tbl-action-delete btn-delete" title="Hapus Data">
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
        <div class="pg-pagination">
            <small style="color:#6b7280;font-size:11.5px;">
                Halaman <strong>{{ $pengaliran->currentPage() }}</strong> dari <strong>{{ $pengaliran->lastPage() }}</strong>
            </small>
            <div>{{ $pengaliran->links('pagination::bootstrap-5') }}</div>
        </div>

        @else
        <div class="empty-pg">
            <div class="empty-pg-icon"><i class="feather-inbox"></i></div>
            <h5 style="color:#374151;font-family:'Outfit',sans-serif;font-weight:800;margin-bottom:8px;">Belum Ada Data</h5>
            <p style="color:#6b7280;font-size:13.5px;margin-bottom:20px;">Data pengaliran belum tersedia atau tidak sesuai filter yang dipilih.</p>
            <a href="{{ route('pengaliran.create') }}" class="btn-ptpn btn-ptpn-primary" style="display:inline-flex;">
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