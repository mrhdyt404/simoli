@extends('layouts.simoli')

@section('title', 'Perizinan Land Application')
@section('page-title', 'Perizinan Land Application Tiap PKS')
@section('page-description', 'Legalitas Izin Pemanfaatan Air Limbah (BOD, pH, Kuota Debit Harian, Titik Penaatan & Sumur Pantau)')

@section('breadcrumb')
    <li>Master & Regulasi</li>
    <li class="separator">/</li>
    <li>Perizinan LA</li>
@endsection

@section('page-actions')
    <div class="d-flex gap-2">
        <a href="{{ route('pemetaan-la.index') }}" class="btn-ptpn btn-ptpn-outline">
            <i class="feather-map-pin" style="font-size:15px;"></i>
            <span>Peta Spasial GIS LA</span>
        </a>
        @if(Auth::user()->isAdmin())
        <a href="{{ route('perizinan-la.create') }}" class="btn-ptpn btn-ptpn-primary">
            <i class="feather-plus" style="font-size:15px;"></i>
            <span>Tambah Izin PKS</span>
        </a>
        @endif
    </div>
@endsection

@section('styles')
<style>
    .prz-kpi {
        background: #ffffff;
        border-radius: 16px;
        border: 1px solid rgba(22,163,74,.12);
        box-shadow: 0 2px 10px rgba(22,163,74,.05);
        padding: 18px 20px;
        height: 100%;
        transition: all .25s ease;
        position: relative;
        overflow: hidden;
    }
    .prz-kpi::after {
        content: '';
        position: absolute;
        top: 0; left: 0; right: 0; height: 3.5px;
    }
    .prz-kpi-green::after  { background: linear-gradient(90deg, #16a34a, #4ade80); }
    .prz-kpi-blue::after   { background: linear-gradient(90deg, #1d4ed8, #60a5fa); }
    .prz-kpi-gold::after   { background: linear-gradient(90deg, #d97706, #fbbf24); }
    .prz-kpi-red::after    { background: linear-gradient(90deg, #dc2626, #f87171); }
    .prz-kpi:hover { transform: translateY(-3px); box-shadow: 0 8px 24px rgba(22,163,74,.12); }

    .prz-kpi-label { font-size: 11px; font-weight: 700; color: #6b7280; text-transform: uppercase; letter-spacing: .5px; margin-bottom: 4px; }
    .prz-kpi-val   { font-family: 'Outfit', sans-serif; font-size: 24px; font-weight: 800; line-height: 1.1; margin-bottom: 4px; }
    .prz-kpi-sub   { font-size: 11.5px; color: #6b7280; }

    /* Alert Banner Kepatuhan */
    .alert-compliance {
        background: linear-gradient(135deg, #fffbeb 0%, #fef3c7 100%);
        border: 1.5px solid #fde68a;
        border-left: 5px solid #d97706;
        border-radius: 14px;
        padding: 14px 18px;
        margin-bottom: 20px;
    }

    .badge-permit-aktif {
        background: rgba(34, 197, 94, 0.15);
        color: #15803d;
        border: 1px solid #86efac;
        padding: 4px 10px;
        border-radius: 50px;
        font-size: 11.5px;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }
    .badge-permit-warn {
        background: rgba(245, 158, 11, 0.15);
        color: #b45309;
        border: 1px solid #fde68a;
        padding: 4px 10px;
        border-radius: 50px;
        font-size: 11.5px;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }
    .badge-permit-danger {
        background: rgba(239, 68, 68, 0.15);
        color: #b91c1c;
        border: 1px solid #fca5a5;
        padding: 4px 10px;
        border-radius: 50px;
        font-size: 11.5px;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }

    .tbl-action-btn {
        width: 32px; height: 32px;
        border-radius: 8px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 14px;
        transition: all .2s;
    }
</style>
@endsection

@section('content')

@if(session('success'))
<div class="alert-simoli alert-simoli-success alert-dismissible fade show" role="alert">
    <i class="feather-check-circle fs-5 text-success"></i>
    <div class="flex-grow-1">
        <strong>Sukses!</strong> {{ session('success') }}
    </div>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

{{-- 1. KPI SUMMARY CARDS --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-lg-3">
        <div class="prz-kpi prz-kpi-green">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="prz-kpi-label">PKS Memiliki Izin</div>
                    <div class="prz-kpi-val text-success">{{ $totalIzin }} <span class="fs-6 text-muted font-normal">/ {{ $totalPks }} Unit</span></div>
                    <div class="prz-kpi-sub">Kelola Regulasi DLH</div>
                </div>
                <div class="icon-pill icon-pill-green"><i class="feather-shield"></i></div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="prz-kpi prz-kpi-blue">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="prz-kpi-label">Status Izin Aktif</div>
                    <div class="prz-kpi-val text-primary">{{ $izinAktif }} <span class="fs-6 text-muted font-normal">PKS</span></div>
                    <div class="prz-kpi-sub">Memenuhi Standar PP</div>
                </div>
                <div class="icon-pill icon-pill-blue"><i class="feather-check-square"></i></div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="prz-kpi prz-kpi-gold">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="prz-kpi-label">Proses Perpanjangan</div>
                    <div class="prz-kpi-val text-warning">{{ $izinPerpanjangan }} <span class="fs-6 text-muted font-normal">PKS</span></div>
                    <div class="prz-kpi-sub">Pengajuan Ulang / SLO</div>
                </div>
                <div class="icon-pill icon-pill-gold"><i class="feather-refresh-cw"></i></div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="prz-kpi prz-kpi-red">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="prz-kpi-label">Izin Kedaluwarsa</div>
                    <div class="prz-kpi-val text-danger">{{ $izinKedaluwarsa }} <span class="fs-6 text-muted font-normal">PKS</span></div>
                    <div class="prz-kpi-sub">Perlu Tindak Lanjut Segera</div>
                </div>
                <div class="icon-pill icon-pill-rose"><i class="feather-alert-triangle"></i></div>
            </div>
        </div>
    </div>
</div>

{{-- 2. COMPLIANCE NOTIFICATION (BILA ADA OVER DEBIT PENGALIRAN) --}}
@if($recentOverDebits->count() > 0)
<div class="alert-compliance">
    <div class="d-flex align-items-start gap-3">
        <i class="feather-alert-octagon text-warning fs-3 mt-1"></i>
        <div class="flex-grow-1">
            <h6 class="fw-bold text-dark mb-1">Peringatan Kepatuhan Debit Pengaliran (Over Permit Discharge)</h6>
            <p class="mb-2 text-muted small">
                Terdeteksi <strong>{{ $recentOverDebits->count() }}</strong> catatan pengaliran dalam 30 hari terakhir yang debit pengalirannya melebihi batas kuota harian dalam SK Perizinan:
            </p>
            <div class="table-responsive bg-white rounded-3 p-2 border">
                <table class="table table-sm table-borderless align-middle mb-0 font-12">
                    <thead>
                        <tr class="text-muted border-bottom">
                            <th>Tanggal</th>
                            <th>PKS</th>
                            <th>Blok / No. Bak</th>
                            <th>Debit Realisasi</th>
                            <th>Batas Izin SK</th>
                            <th>Selisih (Over)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentOverDebits->take(3) as $od)
                        <tr>
                            <td class="fw-bold">{{ $od->tanggal ? $od->tanggal->format('d/m/Y') : '-' }}</td>
                            <td><span class="badge bg-light text-dark border">{{ $od->pks->nama ?? '-' }}</span></td>
                            <td>Blok {{ $od->blok ?? '-' }} (Bak {{ $od->no_bak ?? '-' }})</td>
                            <td class="text-danger fw-bold">{{ number_format($od->vol_limbah_dialirkan, 0, ',', '.') }} m³/hari</td>
                            <td>{{ number_format($od->debit_izin, 0, ',', '.') }} m³/hari</td>
                            <td><span class="badge bg-danger">+{{ number_format($od->kelebihan, 0, ',', '.') }} m³</span></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endif

{{-- 3. FILTER DAN TABEL PERIZINAN --}}
<div class="simoli-card">
    <div class="simoli-card-header">
        <h3 class="simoli-card-title">
            <i class="feather-file-text text-success"></i>
            <span>Daftar Dokumen & Ketentuan SK Izin Land Application</span>
        </h3>
    </div>
    <div class="simoli-card-body">
        
        {{-- Search & Filter Bar --}}
        <form method="GET" action="{{ route('perizinan-la.index') }}" class="row g-2 mb-4">
            @if(Auth::user()->isAdmin())
            <div class="col-md-3">
                <select name="id_pks" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="">-- Semua PKS --</option>
                    @foreach($daftarPks as $pks)
                    <option value="{{ $pks->id_pks }}" {{ request('id_pks') == $pks->id_pks ? 'selected' : '' }}>
                        {{ $pks->nama }} ({{ $pks->kode }})
                    </option>
                    @endforeach
                </select>
            </div>
            @endif
            <div class="col-md-3">
                <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="">-- Semua Status Izin --</option>
                    <option value="Aktif" {{ request('status') == 'Aktif' ? 'selected' : '' }}>Aktif</option>
                    <option value="Proses Perpanjangan" {{ request('status') == 'Proses Perpanjangan' ? 'selected' : '' }}>Proses Perpanjangan</option>
                    <option value="Kedaluwarsa" {{ request('status') == 'Kedaluwarsa' ? 'selected' : '' }}>Kedaluwarsa</option>
                </select>
            </div>
            <div class="col-md-4">
                <div class="input-group input-group-sm">
                    <input type="text" name="search" class="form-control" placeholder="Cari No. SK / Instansi..." value="{{ request('search') }}">
                    <button class="btn btn-ptpn-primary" type="submit"><i class="feather-search"></i></button>
                </div>
            </div>
            <div class="col-md-2 text-end">
                @if(request()->hasAny(['id_pks', 'status', 'search']))
                <a href="{{ route('perizinan-la.index') }}" class="btn btn-sm btn-light border text-muted">
                    <i class="feather-x"></i> Reset
                </a>
                @endif
            </div>
        </form>

        {{-- Table --}}
        <div class="table-responsive">
            <table class="table table-simoli table-hover align-middle">
                <thead>
                    <tr>
                        <th width="4%">No</th>
                        <th width="15%">PKS & Lokasi</th>
                        <th width="20%">Nomor SK & Penerbit</th>
                        <th width="18%">Baku Mutu & Kuota Izin</th>
                        <th width="15%">Titik Penaatan & Sumur</th>
                        <th width="14%">Masa Berlaku</th>
                        <th width="14%" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($perizinanList as $index => $item)
                    <tr>
                        <td>{{ $perizinanList->firstItem() + $index }}</td>
                        <td>
                            <div class="fw-bold text-dark">{{ $item->pks->nama ?? '-' }}</div>
                            <small class="text-muted d-block">{{ $item->pks->kode ?? '' }} &bull; {{ $item->luas_areal_izin }} Ha</small>
                            <span class="badge bg-light text-success border mt-1 font-11">
                                <i class="feather-map-pin me-1"></i>{{ $item->pks->petaBlokLa ? $item->pks->petaBlokLa->count() : 0 }} Blok Terpetakan
                            </span>
                        </td>
                        <td>
                            <div class="fw-bold text-primary">{{ $item->nomor_sk }}</div>
                            <div class="small text-muted mb-1">{{ $item->instansi_penerbit }}</div>
                            @if($item->file_sk)
                            <a href="{{ asset('uploads/perizinan_la/' . $item->file_sk) }}" target="_blank" class="badge bg-danger-subtle text-danger border border-danger-subtle text-decoration-none">
                                <i class="feather-file me-1"></i>Download SK (PDF)
                            </a>
                            @endif
                        </td>
                        <td>
                            <div class="font-12">
                                <div class="d-flex justify-content-between mb-1">
                                    <span class="text-muted">Debit Maks:</span>
                                    <strong class="text-success">{{ number_format($item->debit_maksimal_harian, 0, ',', '.') }} m³/hari</strong>
                                </div>
                                <div class="d-flex justify-content-between mb-1">
                                    <span class="text-muted">BOD Maks:</span>
                                    <strong>{{ number_format($item->bod_maksimal, 0, ',', '.') }} mg/L</strong>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <span class="text-muted">Rentang pH:</span>
                                    <span>{{ $item->ph_min }} – {{ $item->ph_max }}</span>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="font-12 mb-1">
                                <i class="feather-radio text-danger me-1"></i>
                                <span class="fw-semibold">{{ $item->nama_titik_penaatan ?? 'IPAL Outlet' }}</span>
                            </div>
                            <small class="text-muted d-block">{{ $item->koordinat_penaatan_text ?? ($item->lat_titik_penaatan ? $item->lat_titik_penaatan.','.$item->long_titik_penaatan : '-') }}</small>
                            <div class="mt-1">
                                <span class="badge bg-info-subtle text-primary border border-info-subtle font-11">
                                    <i class="feather-droplet me-1"></i>{{ $item->sumurPantau->count() }} Titik Sumur Pantau
                                </span>
                            </div>
                        </td>
                        <td>
                            <div class="mb-1">
                                @if($item->badge_class === 'success')
                                <span class="badge-permit-aktif"><i class="feather-check-circle"></i> {{ $item->status_label }}</span>
                                @elseif($item->badge_class === 'warning')
                                <span class="badge-permit-warn"><i class="feather-clock"></i> {{ $item->status_label }}</span>
                                @else
                                <span class="badge-permit-danger"><i class="feather-alert-triangle"></i> {{ $item->status_label }}</span>
                                @endif
                            </div>
                            <small class="text-muted d-block">
                                Exp: {{ $item->tanggal_berakhir ? $item->tanggal_berakhir->format('d M Y') : 'Seterusnya' }}
                            </small>
                            @if($item->sisa_hari !== null && $item->sisa_hari >= 0)
                            <small class="text-secondary font-11">Sisa {{ $item->sisa_hari }} hari lagi</small>
                            @endif
                        </td>
                        <td class="text-center">
                            <div class="d-flex justify-content-center gap-1">
                                <a href="{{ route('perizinan-la.show', $item->id) }}" class="btn btn-sm btn-outline-success tbl-action-btn" title="Lihat Detail & Legalitas">
                                    <i class="feather-eye"></i>
                                </a>
                                <a href="{{ route('pemetaan-la.index', ['id_pks' => $item->id_pks]) }}" class="btn btn-sm btn-outline-primary tbl-action-btn" title="Buka di Peta Spasial GIS">
                                    <i class="feather-map"></i>
                                </a>
                                @if(Auth::user()->isAdmin())
                                <a href="{{ route('perizinan-la.edit', $item->id) }}" class="btn btn-sm btn-outline-warning tbl-action-btn" title="Edit Data Izin">
                                    <i class="feather-edit-2"></i>
                                </a>
                                <form action="{{ route('perizinan-la.destroy', $item->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data perizinan ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger tbl-action-btn" title="Hapus">
                                        <i class="feather-trash-2"></i>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">
                            <i class="feather-inbox fs-1 d-block mb-2 text-secondary"></i>
                            Belum ada data perizinan Land Application.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-between align-items-center mt-3">
            <small class="text-muted">Menampilkan {{ $perizinanList->count() }} dari {{ $perizinanList->total() }} data perizinan PKS</small>
            {{ $perizinanList->links() }}
        </div>

    </div>
</div>

@endsection
