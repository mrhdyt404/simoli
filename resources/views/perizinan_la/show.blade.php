@extends('layouts.simoli')

@section('title', 'Detail Perizinan LA — ' . ($perizinan->pks->nama ?? 'PKS'))
@section('page-title', 'Detail Legalitas SK Izin Land Application')
@section('page-description', 'Keputusan Izin Pemanfaatan Air Limbah Pabrik Kelapa Sawit Pada Tanah (Land Application)')

@section('breadcrumb')
    <li><a href="{{ route('perizinan-la.index') }}">Perizinan LA</a></li>
    <li class="separator">/</li>
    <li>Detail {{ $perizinan->pks->nama ?? 'PKS' }}</li>
@endsection

@section('page-actions')
    <div class="d-flex gap-2">
        <a href="{{ route('pemetaan-la.index', ['id_pks' => $perizinan->id_pks]) }}" class="btn-ptpn btn-ptpn-outline">
            <i class="feather-map" style="font-size:15px;"></i>
            <span>Buka di Peta Spasial GIS</span>
        </a>
        @if(Auth::user()->isAdmin())
        <a href="{{ route('perizinan-la.edit', $perizinan->id) }}" class="btn-ptpn btn-ptpn-primary">
            <i class="feather-edit-2" style="font-size:15px;"></i>
            <span>Edit Data Izin</span>
        </a>
        @endif
    </div>
@endsection

@section('styles')
<style>
    .sk-hero-header {
        background: linear-gradient(135deg, #032b16 0%, #0d4a2b 50%, #166534 100%);
        border-radius: 20px;
        color: #ffffff;
        padding: 28px 32px;
        position: relative;
        overflow: hidden;
        margin-bottom: 24px;
        box-shadow: 0 10px 30px rgba(5, 46, 22, 0.25);
    }
    .sk-hero-header::after {
        content: '';
        position: absolute;
        bottom: -50px; right: -50px;
        width: 220px; height: 220px;
        background: radial-gradient(circle, rgba(74, 222, 128, 0.2) 0%, transparent 70%);
        border-radius: 50%;
    }
    .sk-number {
        font-family: 'Outfit', sans-serif;
        font-size: clamp(20px, 2vw + 12px, 28px);
        font-weight: 800;
        letter-spacing: -0.5px;
        color: #86efac;
    }
    .param-box {
        background: #f8fafc;
        border-radius: 14px;
        border: 1px solid #e2e8f0;
        padding: 16px;
        height: 100%;
        transition: all .2s;
    }
    .param-box:hover {
        background: #ffffff;
        border-color: #86efac;
        box-shadow: 0 4px 14px rgba(22, 163, 74, 0.08);
    }
    .param-label { font-size: 11px; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: .5px; margin-bottom: 6px; }
    .param-value { font-family: 'Outfit', sans-serif; font-size: 22px; font-weight: 800; color: #0f172a; margin-bottom: 2px; }
    .param-sub { font-size: 11.5px; color: #64748b; }
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

{{-- 1. HERO HEADER SK --}}
<div class="sk-hero-header">
    <div class="row align-items-center g-3">
        <div class="col-lg-8">
            <div class="d-flex align-items-center gap-2 mb-2">
                <span class="badge bg-success-subtle text-success-emphasis border border-success-subtle px-3 py-1 font-12 fw-bold">
                    PKS {{ $perizinan->pks->nama ?? '-' }} ({{ $perizinan->pks->kode ?? '' }})
                </span>
                @if($perizinan->badge_class === 'success')
                <span class="badge bg-success text-white px-3 py-1 font-12 fw-bold"><i class="feather-check-circle me-1"></i> {{ $perizinan->status_label }}</span>
                @elseif($perizinan->badge_class === 'warning')
                <span class="badge bg-warning text-dark px-3 py-1 font-12 fw-bold"><i class="feather-clock me-1"></i> {{ $perizinan->status_label }}</span>
                @else
                <span class="badge bg-danger text-white px-3 py-1 font-12 fw-bold"><i class="feather-alert-triangle me-1"></i> {{ $perizinan->status_label }}</span>
                @endif
            </div>
            <div class="sk-number mb-1">SK No: {{ $perizinan->nomor_sk }}</div>
            <p class="mb-2 text-white-50 font-13" style="max-width: 680px;">
                {{ $perizinan->tentang }}
            </p>
            <div class="d-flex flex-wrap gap-3 font-12 text-white-50 mt-3 pt-2 border-top border-success border-opacity-25">
                <div><i class="feather-briefcase text-success me-1"></i> Penerbit: <strong class="text-white">{{ $perizinan->instansi_penerbit }}</strong></div>
                <div><i class="feather-calendar text-success me-1"></i> Ditetapkan: <strong class="text-white">{{ $perizinan->tanggal_terbit ? $perizinan->tanggal_terbit->format('d F Y') : '-' }}</strong></div>
                <div><i class="feather-clock text-success me-1"></i> Masa Berlaku: <strong class="text-white">{{ $perizinan->tanggal_berakhir ? $perizinan->tanggal_berakhir->format('d F Y') : '5 Tahun' }}</strong></div>
            </div>
        </div>

        <div class="col-lg-4 text-lg-end">
            <div class="d-inline-flex flex-column gap-2 text-start">
                @if($perizinan->file_sk)
                <a href="{{ asset('uploads/perizinan_la/' . $perizinan->file_sk) }}" target="_blank" class="btn btn-sm btn-light fw-bold text-danger px-3 py-2 shadow-sm">
                    <i class="feather-download me-1"></i> Unduh Salinan Dokumen SK
                </a>
                @endif
                @if($perizinan->file_peta)
                <a href="{{ asset('uploads/perizinan_la/' . $perizinan->file_peta) }}" target="_blank" class="btn btn-sm btn-outline-light fw-bold px-3 py-2">
                    <i class="feather-image me-1"></i> Unduh Lampiran Peta LA
                </a>
                @endif
                <a href="{{ route('pemetaan-la.peta-digital', $perizinan->id_pks) }}" target="_blank" class="btn btn-sm btn-success fw-bold px-3 py-2">
                    <i class="feather-printer me-1"></i> Cetak Peta Kartografi GIS
                </a>
            </div>
        </div>
    </div>
</div>

{{-- 2. BAKU MUTU & KUOTA DEBIT --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="param-box">
            <div class="param-label">Debit Maksimal Harian</div>
            <div class="param-value text-success">{{ number_format($perizinan->debit_maksimal_harian, 0, ',', '.') }} <span class="fs-6 font-normal text-muted">m³/hari</span></div>
            <div class="param-sub">Batas kuota pengaliran ke tanah</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="param-box">
            <div class="param-label">BOD Maksimal</div>
            <div class="param-value text-primary">{{ number_format($perizinan->bod_maksimal, 0, ',', '.') }} <span class="fs-6 font-normal text-muted">mg/L</span></div>
            <div class="param-sub">Kadar Biological Oxygen Demand</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="param-box">
            <div class="param-label">Rentang Derajat pH</div>
            <div class="param-value text-warning">{{ $perizinan->ph_min }} – {{ $perizinan->ph_max }}</div>
            <div class="param-sub">Tingkat keasaman air limbah</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="param-box">
            <div class="param-label">Total Luas Areal Izin</div>
            <div class="param-value text-dark">{{ number_format($perizinan->luas_areal_izin, 2, ',', '.') }} <span class="fs-6 font-normal text-muted">Ha</span></div>
            <div class="param-sub">Areal Land Application berizin</div>
        </div>
    </div>
</div>

<div class="row g-4">
    
    {{-- 3. TITIK PENAATAN & SUMUR PANTAU --}}
    <div class="col-lg-7">
        <div class="simoli-card mb-4">
            <div class="simoli-card-header">
                <h3 class="simoli-card-title">
                    <i class="feather-crosshair text-danger"></i>
                    <span>Titik Penaatan Effluent Outlet IPAL (Kolam Anaerob)</span>
                </h3>
            </div>
            <div class="simoli-card-body">
                <div class="p-3 bg-light rounded-3 border mb-3">
                    <div class="row align-items-center">
                        <div class="col-md-7">
                            <h6 class="fw-bold text-dark mb-1">{{ $perizinan->nama_titik_penaatan ?? 'IPAL Kolam Anaerob Pond IV' }}</h6>
                            <div class="text-muted font-12 mb-2">Saluran Distribusi: <strong>{{ $perizinan->saluran_distribusi ?? 'Pipa PVC 6 inchi' }}</strong></div>
                            <span class="badge bg-danger-subtle text-danger border border-danger-subtle font-12">
                                <i class="feather-radio me-1"></i>{{ $perizinan->koordinat_penaatan_text ?? ($perizinan->lat_titik_penaatan ? $perizinan->lat_titik_penaatan.','.$perizinan->long_titik_penaatan : 'N/A') }}
                            </span>
                        </div>
                        <div class="col-md-5 text-md-end mt-2 mt-md-0">
                            @if($perizinan->lat_titik_penaatan && $perizinan->long_titik_penaatan)
                            <a href="https://maps.google.com/?q={{ $perizinan->lat_titik_penaatan }},{{ $perizinan->long_titik_penaatan }}" target="_blank" class="btn btn-sm btn-outline-danger">
                                <i class="feather-external-link me-1"></i> Buka Google Maps
                            </a>
                            @endif
                        </div>
                    </div>
                </div>

                <h6 class="fw-bold text-dark mt-4 mb-2 font-13">
                    <i class="feather-droplet text-primary me-1"></i> Titik Pantau Air Tanah (Monitoring Wells)
                </h6>
                <div class="table-responsive">
                    <table class="table table-sm table-simoli table-bordered align-middle font-12">
                        <thead class="table-light">
                            <tr>
                                <th>No</th>
                                <th>Nama Titik Pantau</th>
                                <th>Jenis Sumur</th>
                                <th>Lokasi Blok</th>
                                <th>Koordinat</th>
                                <th>Frekuensi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($perizinan->sumurPantau as $idx => $sp)
                            <tr>
                                <td class="text-center">{{ $idx + 1 }}</td>
                                <td class="fw-bold text-dark">{{ $sp->nama_sumur }}</td>
                                <td>
                                    <span class="badge bg-light text-primary border">{{ $sp->jenis_sumur }}</span>
                                </td>
                                <td>{{ $sp->lokasi_blok ?? '-' }}</td>
                                <td>
                                    <small class="text-muted font-11">{{ $sp->koordinat_text ?? ($sp->latitude ? $sp->latitude.','.$sp->longitude : '-') }}</small>
                                </td>
                                <td><span class="badge bg-light text-secondary border">{{ $sp->frekuensi_pantau }}</span></td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center py-3 text-muted">Belum ada data titik sumur pantau.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="alert alert-info border-info-subtle font-12 mt-3 mb-0">
                    <i class="feather-info me-1"></i> <strong>Parameter Pengujian Air Tanah:</strong> BOD, DO, pH, NO3, NH3-N, Cd, Cu, Pb, Zn, Cl, SO4-2 (diuji oleh laboratorium terakreditasi minimal 6 bulan sekali).
                </div>
            </div>
        </div>
    </div>

    {{-- 4. DAFTAR BLOK TERPETAKAN DI LAHAN PERKEBUNAN --}}
    <div class="col-lg-5">
        <div class="simoli-card mb-4">
            <div class="simoli-card-header">
                <h3 class="simoli-card-title">
                    <i class="feather-grid text-success"></i>
                    <span>Daftar Blok Land Application Terizin ({{ $perizinan->pks->petaBlokLa->count() }} Blok)</span>
                </h3>
            </div>
            <div class="simoli-card-body p-0">
                <div class="table-responsive" style="max-height: 420px; overflow-y: auto;">
                    <table class="table table-sm table-hover align-middle mb-0 font-12">
                        <thead class="table-light sticky-top">
                            <tr>
                                <th>Blok</th>
                                <th>Afdeling</th>
                                <th>Luas (Ha)</th>
                                <th>Flatbed</th>
                                <th>Parit (m)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($perizinan->pks->petaBlokLa as $pb)
                            <tr>
                                <td class="fw-bold text-success">Blok {{ $pb->nama_blok }}</td>
                                <td><span class="badge bg-light text-dark border">{{ $pb->afdeling }}</span></td>
                                <td>{{ $pb->luas_ha }} Ha</td>
                                <td>{{ $pb->jumlah_flat_bed }} Bed</td>
                                <td>{{ $pb->panjang_parit_meter ? number_format($pb->panjang_parit_meter, 0, ',', '.') . ' m' : '-' }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">Belum ada data blok LA terpetakan.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- RIWAYAT PENGALIRAN TERAKHIR KE PKS INI --}}
        <div class="simoli-card">
            <div class="simoli-card-header">
                <h3 class="simoli-card-title">
                    <i class="feather-activity text-primary"></i>
                    <span>Pengaliran Terakhir (Realisasi vs Izin)</span>
                </h3>
            </div>
            <div class="simoli-card-body p-0">
                <div class="table-responsive">
                    <table class="table table-sm table-hover align-middle mb-0 font-12">
                        <thead class="table-light">
                            <tr>
                                <th>Tgl</th>
                                <th>Blok</th>
                                <th>Vol Dialirkan</th>
                                <th>Status Kuota</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentPengaliran as $rp)
                            <tr>
                                <td>{{ $rp->tanggal ? $rp->tanggal->format('d/m/y') : '-' }}</td>
                                <td class="fw-semibold">Blok {{ $rp->blok ?? '-' }}</td>
                                <td><strong>{{ number_format($rp->vol_limbah_dialirkan, 0, ',', '.') }}</strong> m³</td>
                                <td>
                                    @if($rp->vol_limbah_dialirkan > $perizinan->debit_maksimal_harian)
                                    <span class="badge bg-danger">Over ({{ number_format($rp->vol_limbah_dialirkan - $perizinan->debit_maksimal_harian, 0, ',', '.') }} m³)</span>
                                    @else
                                    <span class="badge bg-success">Sesuai Kuota</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center py-3 text-muted">Belum ada riwayat pengaliran.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>

</div>

@endsection
