@extends('layouts.simoli')

@section('title', 'Arsip Dokumen Perizinan Land Application')
@section('page-title', 'Arsip Dokumen Perizinan Land Application')
@section('page-description', 'Pusat Penyimpanan & Pengarsipan Berkas Surat Keputusan (SK) Izin Land Application PKS')

@section('breadcrumb')
    <li>Arsip & Legalitas</li>
    <li class="separator">/</li>
    <li>Arsip Perizinan LA</li>
@endsection

@section('page-actions')
    <div class="d-flex gap-2 flex-wrap">
        @if(Auth::user()->isAdmin())
        <div class="dropdown">
            <button class="btn-ptpn btn-ptpn-outline dropdown-toggle d-inline-flex align-items-center gap-1.5" type="button" data-bs-toggle="dropdown" aria-expanded="false" style="border-color:#dc2626;color:#dc2626;">
                <i class="feather-lock text-danger" style="font-size:15px;"></i>
                <span>Kunci / Buka Semua</span>
            </button>
            <ul class="dropdown-menu dropdown-menu-end shadow border-0" style="border-radius:12px;font-size:13px;min-width:260px;">
                <li><h6 class="dropdown-header text-uppercase fw-bold text-muted" style="font-size:11px;">Kontrol Dokumen SK Izin LA</h6></li>
                <li>
                    <form action="{{ route('perizinan-la.bulk-lock') }}" method="POST" class="m-0 p-0" onsubmit="return confirm('Apakah Anda yakin ingin MENGUNCI SEMUA data dokumen SK perizinan untuk SEMUA UNIT? Unit PKS tidak akan dapat mengedit arsip SK.');">
                        @csrf
                        <input type="hidden" name="action" value="lock">
                        <input type="hidden" name="id_pks" value="all">
                        <button type="submit" class="dropdown-item d-flex align-items-center gap-2 py-2 text-danger fw-semibold">
                            <i class="feather-lock text-danger"></i>
                            <span>Kunci Semua SK Izin (Semua Unit)</span>
                        </button>
                    </form>
                </li>
                <li>
                    <form action="{{ route('perizinan-la.bulk-lock') }}" method="POST" class="m-0 p-0" onsubmit="return confirm('Apakah Anda yakin ingin MEMBUKA KUNCI SEMUA data dokumen SK perizinan untuk SEMUA UNIT?');">
                        @csrf
                        <input type="hidden" name="action" value="unlock">
                        <input type="hidden" name="id_pks" value="all">
                        <button type="submit" class="dropdown-item d-flex align-items-center gap-2 py-2 text-success fw-semibold">
                            <i class="feather-unlock text-success"></i>
                            <span>Buka Kunci Semua SK Izin</span>
                        </button>
                    </form>
                </li>
                <li><hr class="dropdown-divider"></li>
                <li><h6 class="dropdown-header text-uppercase fw-bold text-muted" style="font-size:11px;">Kontrol Global (Peta &amp; SK Izin)</h6></li>
                <li>
                    <form action="{{ route('pemetaan-la.bulk-lock-all') }}" method="POST" class="m-0 p-0" onsubmit="return confirm('PERINGATAN: Apakah Anda yakin ingin MENGUNCI SEMUA data Arsip Peta DAN Dokumen SK Izin untuk SEMUA UNIT sekaligus?');">
                        @csrf
                        <input type="hidden" name="action" value="lock">
                        <button type="submit" class="dropdown-item d-flex align-items-center gap-2 py-2 text-danger fw-bold">
                            <i class="feather-shield text-danger"></i>
                            <span>Kunci Semua Peta &amp; Izin (Global)</span>
                        </button>
                    </form>
                </li>
                <li>
                    <form action="{{ route('pemetaan-la.bulk-lock-all') }}" method="POST" class="m-0 p-0" onsubmit="return confirm('Apakah Anda yakin ingin MEMBUKA KUNCI SEMUA data Arsip Peta DAN Dokumen SK Izin untuk SEMUA UNIT?');">
                        @csrf
                        <input type="hidden" name="action" value="unlock">
                        <button type="submit" class="dropdown-item d-flex align-items-center gap-2 py-2 text-success fw-bold">
                            <i class="feather-check-circle text-success"></i>
                            <span>Buka Semua Peta &amp; Izin (Global)</span>
                        </button>
                    </form>
                </li>
            </ul>
        </div>
        @endif

        <a href="{{ route('pemetaan-la.index') }}" class="btn-ptpn btn-ptpn-outline">
            <i class="feather-map" style="font-size:15px;"></i>
            <span>Arsip Peta LA</span>
        </a>
        <a href="{{ route('perizinan-la.create') }}" class="btn-ptpn btn-ptpn-primary">
            <i class="feather-upload-cloud" style="font-size:15px;"></i>
            <span>Unggah Arsip SK</span>
        </a>
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

    .doc-card {
        background: #ffffff;
        border-radius: 16px;
        border: 1px solid rgba(22,163,74,.15);
        box-shadow: 0 2px 12px rgba(22,163,74,.05);
        transition: all .25s ease;
        padding: 20px;
        display: flex;
        flex-direction: column;
        height: 100%;
    }
    .doc-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 10px 25px rgba(22,163,74,.12);
        border-color: rgba(22,163,74,.35);
    }
    .doc-icon {
        width: 54px;
        height: 54px;
        border-radius: 16px;
        background: #f0fdf4;
        border: 1.5px solid #bbf7d0;
        color: #16a34a;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 26px;
        flex-shrink: 0;
    }
    .badge-status-aktif { background: #dcfce7; color: #15803d; border: 1px solid #86efac; }
    .badge-status-perhatian { background: #fef3c7; color: #b45309; border: 1px solid #fde68a; }
    .badge-status-kedaluwarsa { background: #fee2e2; color: #b91c1c; border: 1px solid #fca5a5; }

    html.app-skin-dark .prz-kpi,
    html.app-skin-dark .doc-card {
        background: #0a2317 !important;
        border-color: rgba(34,197,94,.18) !important;
    }
    html.app-skin-dark .doc-icon {
        background: #052e16 !important;
        border-color: rgba(34,197,94,.3) !important;
        color: #86efac !important;
    }
</style>
@endsection

@section('content')

{{-- 1. KPI Ringkasan Dokumen --}}
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="prz-kpi prz-kpi-green">
            <div class="prz-kpi-label">Total Arsip SK</div>
            <div class="prz-kpi-val text-success">{{ $totalArsip }}</div>
            <div class="prz-kpi-sub">Berkas SK izin terdaftar</div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="prz-kpi prz-kpi-blue">
            <div class="prz-kpi-label">Izin Aktif</div>
            <div class="prz-kpi-val text-primary">{{ $arsipAktif }}</div>
            <div class="prz-kpi-sub">Masa berlaku masih valid</div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="prz-kpi prz-kpi-gold">
            <div class="prz-kpi-label">Proses Perpanjangan</div>
            <div class="prz-kpi-val text-warning">{{ $arsipPerpanjangan }}</div>
            <div class="prz-kpi-sub">&le; 60 hari sebelum expired</div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="prz-kpi prz-kpi-red">
            <div class="prz-kpi-label">Kedaluwarsa</div>
            <div class="prz-kpi-val text-danger">{{ $arsipKedaluwarsa }}</div>
            <div class="prz-kpi-sub">Perlu pembaruan SK</div>
        </div>
    </div>
</div>

{{-- 2. Filter & Pencarian Arsip --}}
<div class="simoli-card mb-4">
    <div class="simoli-card-body p-3">
        <form method="GET" action="{{ route('perizinan-la.index') }}" class="row g-2 align-items-center">
            <div class="col-md-4">
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0"><i class="feather-search text-muted"></i></span>
                    <input type="text" name="search" class="form-control border-start-0" placeholder="Cari Nomor SK, Judul, atau Instansi..." value="{{ request('search') }}">
                </div>
            </div>
            
            @if(Auth::user()->isAdmin())
            <div class="col-md-3">
                <select name="id_pks" class="form-select">
                    <option value="">-- Semua Unit PKS --</option>
                    @foreach($daftarPks as $pks)
                        <option value="{{ $pks->id_pks }}" {{ request('id_pks') == $pks->id_pks ? 'selected' : '' }}>
                            {{ $pks->nama }} ({{ $pks->akro ?? $pks->kode }})
                        </option>
                    @endforeach
                </select>
            </div>
            @endif

            <div class="col-md-3">
                <select name="status" class="form-select">
                    <option value="">-- Semua Status --</option>
                    <option value="Aktif" {{ request('status') == 'Aktif' ? 'selected' : '' }}>Aktif</option>
                    <option value="Proses Perpanjangan" {{ request('status') == 'Proses Perpanjangan' ? 'selected' : '' }}>Proses Perpanjangan</option>
                    <option value="Kedaluwarsa" {{ request('status') == 'Kedaluwarsa' ? 'selected' : '' }}>Kedaluwarsa</option>
                </select>
            </div>

            <div class="col-md-2 d-flex gap-2">
                <button type="submit" class="btn-ptpn btn-ptpn-primary w-100 justify-content-center">
                    <i class="feather-filter"></i> Filter
                </button>
                @if(request()->hasAny(['search', 'id_pks', 'status']))
                    <a href="{{ route('perizinan-la.index') }}" class="btn btn-light border px-3" title="Reset Filter">
                        <i class="feather-refresh-cw"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>
</div>

{{-- 3. Daftar Berkas Arsip SK --}}
@if($perizinanList->count() > 0)
<div class="row g-4 mb-4">
    @foreach($perizinanList as $item)
    <div class="col-md-6 col-xl-4">
        <div class="doc-card">
            <div class="d-flex align-items-start justify-content-between gap-3 mb-3">
                <div class="doc-icon">
                    <i class="feather-file-text"></i>
                </div>
                <div class="text-end">
                    @if($item->is_locked)
                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-0.5 rounded-pill mb-1 d-inline-block" style="font-size:10px;font-weight:700;">
                            <i class="feather-lock"></i> Terkunci
                        </span>
                    @endif
                    @php
                        $badgeClass = 'badge-status-aktif';
                        if ($item->status_label === 'Kedaluwarsa') {
                            $badgeClass = 'badge-status-kedaluwarsa';
                        } elseif (str_contains($item->status_label, 'Perpanjang') || str_contains($item->status_label, 'Perhatian')) {
                            $badgeClass = 'badge-status-perhatian';
                        }
                    @endphp
                    <span class="badge {{ $badgeClass }} px-2 py-1 rounded-pill" style="font-size:11px;font-weight:700;">
                        {{ $item->status_label }}
                    </span>
                    <div style="font-size:11px;color:#6b7280;margin-top:3px;">
                        @if($item->sisa_hari !== null)
                            @if($item->sisa_hari > 0)
                                Sisa {{ $item->sisa_hari }} hari
                            @else
                                Lewat {{ abs($item->sisa_hari) }} hari
                            @endif
                        @else
                            -
                        @endif
                    </div>
                </div>
            </div>

            <div class="mb-2">
                <span class="badge bg-light text-dark border mb-1" style="font-size:11px;font-weight:700;">
                    <i class="feather-home text-success"></i> {{ $item->pks ? $item->pks->nama : 'Unit PKS' }}
                </span>
                <h5 style="font-family:'Outfit',sans-serif;font-weight:800;font-size:15px;color:#1f2937;margin-bottom:4px;line-height:1.3;">
                    {{ $item->nomor_sk }}
                </h5>
                <p style="font-size:12px;color:#4b5563;margin-bottom:12px;line-height:1.4;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;">
                    {{ $item->tentang }}
                </p>
            </div>

            <div class="bg-light rounded-3 p-2 mb-3" style="font-size:11.5px;color:#374151;">
                <div class="d-flex justify-content-between mb-1">
                    <span class="text-muted">Penerbit:</span>
                    <strong class="text-truncate" style="max-width:180px;">{{ $item->instansi_penerbit }}</strong>
                </div>
                <div class="d-flex justify-content-between mb-1">
                    <span class="text-muted">Batas Debit Izin:</span>
                    <strong class="text-success"><i class="feather-droplet"></i> {{ number_format($item->debit_maksimal_harian ?? 0, 0, ',', '.') }} m³/hari</strong>
                </div>
                <div class="d-flex justify-content-between mb-1">
                    <span class="text-muted">Tgl Terbit:</span>
                    <strong>{{ $item->tanggal_terbit ? $item->tanggal_terbit->format('d/m/Y') : '-' }}</strong>
                </div>
                <div class="d-flex justify-content-between">
                    <span class="text-muted">Masa Berlaku:</span>
                    <strong>{{ $item->tanggal_berakhir ? $item->tanggal_berakhir->format('d/m/Y') : '5 Tahun' }}</strong>
                </div>
            </div>

            <div class="mt-auto pt-2.5 border-top d-flex align-items-center justify-content-between gap-2 flex-wrap">
                <div class="d-flex align-items-center gap-2 flex-nowrap">
                    @if($item->file_sk)
                        <a href="{{ asset('uploads/perizinan_la/' . $item->file_sk) }}" target="_blank" class="btn btn-sm btn-outline-success d-inline-flex align-items-center gap-1.5 px-3 py-1.5 fw-bold" style="border-radius:9px;font-size:12.5px;">
                            <i class="feather-eye" style="font-size:14.5px;"></i> Buka PDF
                        </a>
                        <a href="{{ asset('uploads/perizinan_la/' . $item->file_sk) }}" download class="btn btn-sm btn-light border text-muted d-inline-flex align-items-center justify-content-center px-2.5 py-1.5" style="border-radius:9px;font-size:12.5px;" title="Unduh Berkas">
                            <i class="feather-download" style="font-size:14.5px;"></i>
                        </a>
                    @else
                        <span class="text-muted" style="font-size:11.5px;"><em>Belum ada file</em></span>
                    @endif
                </div>

                <div class="d-flex align-items-center p-2 gap-1 flex-nowrap">
                    <a href="{{ route('perizinan-la.show', $item->id) }}" class="btn btn-sm btn-light border text-secondary d-inline-flex align-items-center justify-content-center px-2.5 py-1.5" title="Detail Arsip" style="border-radius:9px;">
                        <i class="feather-info" style="font-size:14.5px;"></i>
                    </a>

                    {{-- Edit: Admin atau Unit jika tidak terkunci --}}
                    @if(Auth::user()->isAdmin() || (Auth::user()->id_pks == $item->id_pks && !$item->is_locked))
                    <a href="{{ route('perizinan-la.edit', $item->id) }}" class="btn btn-sm btn-light border text-warning d-inline-flex align-items-center justify-content-center px-2.5 py-1.5" title="Edit Metadata" style="border-radius:9px;">
                        <i class="feather-edit-2" style="font-size:14.5px;"></i>
                    </a>
                    @elseif(Auth::user()->id_pks == $item->id_pks && $item->is_locked)
                    <button type="button" class="btn btn-sm btn-light border text-muted opacity-50 d-inline-flex align-items-center justify-content-center px-2.5 py-1.5" title="Data dikunci oleh Admin (Tidak dapat diedit)" style="border-radius:9px;" disabled>
                        <i class="feather-lock text-danger" style="font-size:14.5px;"></i>
                    </button>
                    @endif

                    {{-- Admin Only Actions: Lock/Unlock & Delete --}}
                    @if(Auth::user()->isAdmin())
                    <form action="{{ route('perizinan-la.toggle-lock', $item->id) }}" method="POST" class="d-inline m-0 p-0">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-light border {{ $item->is_locked ? 'text-danger' : 'text-secondary' }} d-inline-flex align-items-center justify-content-center px-2.5 py-1.5" title="{{ $item->is_locked ? 'Buka Kunci Arsip (Unlock)' : 'Kunci Arsip (Lock)' }}" style="border-radius:9px;">
                            <i class="{{ $item->is_locked ? 'feather-lock text-danger' : 'feather-unlock' }}" style="font-size:14.5px;"></i>
                        </button>
                    </form>
                    <form action="{{ route('perizinan-la.destroy', $item->id) }}" method="POST" class="d-inline m-0 p-0" onsubmit="return confirm('Apakah Anda yakin ingin menghapus arsip SK ini?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-light border text-danger d-inline-flex align-items-center justify-content-center px-2.5 py-1.5" title="Hapus Arsip" style="border-radius:9px;">
                            <i class="feather-trash-2" style="font-size:14.5px;"></i>
                        </button>
                    </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @endforeach
</div>

<div class="d-flex justify-content-center">
    {{ $perizinanList->links('pagination::bootstrap-5') }}
</div>
@else
<div class="simoli-card text-center py-5">
    <div style="font-size:48px;color:#86efac;margin-bottom:12px;">
        <i class="feather-inbox"></i>
    </div>
    <h5 style="font-family:'Outfit',sans-serif;font-weight:800;color:#374151;">Belum Ada Arsip Dokumen SK</h5>
    <p style="font-size:13px;color:#6b7280;max-width:400px;margin:0 auto 20px auto;">
        Belum ada berkas Surat Keputusan (SK) Land Application yang diunggah. Silakan klik tombol di bawah untuk mengunggah berkas pertama.
    </p>
    <a href="{{ route('perizinan-la.create') }}" class="btn-ptpn btn-ptpn-primary" style="display:inline-flex;">
        <i class="feather-upload-cloud"></i> Unggah Arsip SK Sekarang
    </a>
</div>
@endif

@endsection
