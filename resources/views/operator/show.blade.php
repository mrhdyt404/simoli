@extends('layouts.operator')

@section('title', 'Detail Laporan Kerja - SIMOLII Operator')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-3">
    <div>
        <h5 class="fw-bold m-0 text-dark fs-17"><i class="feather-file-text me-1.5 text-primary"></i>Detail Laporan Kerja</h5>
        <div class="text-muted fs-12">{{ \Carbon\Carbon::parse($log->tanggal)->translatedFormat('l, d F Y') }}</div>
    </div>
    <a href="{{ route('operator.index') }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3 fs-12 fw-semibold">
        <i class="feather-arrow-left me-1"></i>Kembali
    </a>
</div>

<!-- Main Detail Card -->
<div class="op-card mb-3">
    <div class="op-card-header bg-primary text-white d-flex align-items-center justify-content-between py-2.5 px-3">
        <span class="fw-bold fs-14"><i class="feather-truck me-2"></i>{{ $log->alatBerat ? $log->alatBerat->nama_alat : 'Alat Berat' }}</span>
        <span class="badge bg-white text-primary fw-bold fs-12 px-2.5 py-1">{{ $log->alatBerat ? $log->alatBerat->kode_alat : '-' }}</span>
    </div>
    <div class="op-card-body">
        <div class="row g-2 mb-3">
            <div class="col-6">
                <div class="p-2.5 bg-light rounded-3 border">
                    <span class="text-muted fs-11 d-block fw-semibold text-uppercase">TANGGAL KERJA</span>
                    <span class="fw-bold fs-13 text-dark">{{ \Carbon\Carbon::parse($log->tanggal)->translatedFormat('d F Y') }}</span>
                </div>
            </div>
            <div class="col-6">
                <div class="p-2.5 bg-light rounded-3 border">
                    <span class="text-muted fs-11 d-block fw-semibold text-uppercase">NAMA OPERATOR</span>
                    <span class="fw-bold fs-13 text-dark">{{ $log->operator }}</span>
                </div>
            </div>
        </div>

        <div class="mb-3 p-2.5 bg-light rounded-3 border">
            <span class="text-muted fs-11 d-block fw-semibold text-uppercase">KEGIATAN / PEKERJAAN</span>
            <div class="fw-bold text-primary fs-14">{{ $log->kegiatan }}</div>
            @if($log->lokasi_blok)
                <div class="text-muted fs-12 mt-1"><i class="feather-map-pin me-1 text-danger"></i>Lokasi: <strong class="text-dark">{{ $log->lokasi_blok }}</strong></div>
            @endif
        </div>

        <!-- Bed Breakdown Grid -->
        <div class="row g-2 mb-3">
            <div class="col-4">
                <div class="p-2 text-center bg-soft-primary rounded-3 border border-primary-subtle">
                    <span class="text-muted fs-11 d-block">Flat Bed</span>
                    <span class="fw-bolder fs-14 text-primary">{{ $log->flat_bed }}</span>
                </div>
            </div>
            <div class="col-4">
                <div class="p-2 text-center bg-soft-primary rounded-3 border border-primary-subtle">
                    <span class="text-muted fs-11 d-block">Long Bed</span>
                    <span class="fw-bolder fs-14 text-primary">{{ $log->long_bed }}</span>
                </div>
            </div>
            <div class="col-4">
                <div class="p-2 text-center bg-primary text-white rounded-3">
                    <span class="opacity-80 fs-11 d-block">Total Bed</span>
                    <span class="fw-bolder fs-14">{{ $log->jumlah_bed }}</span>
                </div>
            </div>
        </div>

        <!-- HM Breakdown Grid -->
        <div class="row g-2 mb-3">
            <div class="col-4">
                <div class="p-2 text-center bg-light rounded-3 border">
                    <span class="text-muted fs-11 d-block">Jam Awal</span>
                    <span class="fw-bold fs-13 text-dark">{{ $log->hm_awal_formatted }}</span>
                </div>
            </div>
            <div class="col-4">
                <div class="p-2 text-center bg-light rounded-3 border">
                    <span class="text-muted fs-11 d-block">Jam Akhir</span>
                    <span class="fw-bold fs-13 text-dark">{{ $log->hm_akhir_formatted }}</span>
                </div>
            </div>
            <div class="col-4">
                <div class="p-2 text-center bg-success text-white rounded-3">
                    <span class="opacity-80 fs-11 d-block">Total HM</span>
                    <span class="fw-bolder fs-13">{{ \App\Models\MonitoringAlatBerat::formatHm($log->total_hm, true) }}</span>
                </div>
            </div>
        </div>

        <div class="row g-2 mb-3">
            <div class="col-6">
                <div class="p-2.5 bg-light rounded-3 border">
                    <span class="text-muted fs-11 d-block fw-semibold text-uppercase">BBM SOLAR</span>
                    <span class="fw-bold fs-13 text-warning">{{ number_format($log->bbm_liter, 1) }} Liter</span>
                </div>
            </div>
            <div class="col-6">
                <div class="p-2.5 bg-light rounded-3 border">
                    <span class="text-muted fs-11 d-block fw-semibold text-uppercase">KONDISI ALAT</span>
                    @if($log->kondisi_alat == 'Normal')
                        <span class="badge bg-success badge-status-op">Normal / Baik</span>
                    @elseif($log->kondisi_alat == 'Perlu Perbaikan')
                        <span class="badge bg-warning text-dark badge-status-op">Perlu Perbaikan</span>
                    @else
                        <span class="badge bg-danger badge-status-op">Breakdown</span>
                    @endif
                </div>
            </div>
        </div>

        @if($log->catatan)
            <div class="mb-3 p-2.5 bg-light rounded-3 border">
                <span class="text-muted fs-11 d-block fw-semibold text-uppercase">CATATAN / KENDALA</span>
                <span class="fs-13 text-dark">{{ $log->catatan }}</span>
            </div>
        @endif

        @php
            $latAwal = $log->latitude_awal ?? $log->latitude;
            $longAwal = $log->longitude_awal ?? $log->longitude;
            $latAkhir = $log->latitude_akhir;
            $longAkhir = $log->longitude_akhir;
        @endphp

        @if($latAwal || $latAkhir)
            <div class="mb-3 p-3 bg-light rounded-3 border">
                <span class="text-muted fs-11 d-block fw-bold text-uppercase mb-2"><i class="feather-map-pin me-1 text-primary"></i>Lokasi Koordinat GPS Kerja</span>
                <div class="row g-2">
                    @if($latAwal && $longAwal)
                        <div class="col-12 col-sm-6">
                            <div class="p-2.5 bg-white rounded-3 border shadow-sm">
                                <span class="fs-11 text-muted d-block fw-semibold text-primary"><i class="feather-navigation me-1"></i>GPS Awal</span>
                                <span class="fs-12 text-dark fw-bold d-block my-1">{{ $latAwal }}, {{ $longAwal }}</span>
                                <a href="https://maps.google.com/?q={{ $latAwal }},{{ $longAwal }}" target="_blank" class="btn btn-xs btn-outline-primary rounded-pill w-100 mt-1 fs-11">
                                    <i class="feather-map me-1"></i>Buka Peta GPS Awal
                                </a>
                            </div>
                        </div>
                    @endif
                    @if($latAkhir && $longAkhir)
                        <div class="col-12 col-sm-6">
                            <div class="p-2.5 bg-white rounded-3 border shadow-sm">
                                <span class="fs-11 text-muted d-block fw-semibold text-success"><i class="feather-navigation me-1"></i>GPS Akhir</span>
                                <span class="fs-12 text-dark fw-bold d-block my-1">{{ $latAkhir }}, {{ $longAkhir }}</span>
                                <a href="https://maps.google.com/?q={{ $latAkhir }},{{ $longAkhir }}" target="_blank" class="btn btn-xs btn-outline-success rounded-pill w-100 mt-1 fs-11">
                                    <i class="feather-map me-1"></i>Buka Peta GPS Akhir
                                </a>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        @endif
    </div>
</div>

<!-- Documentation Photos Card -->
<div class="op-card mb-4">
    <div class="op-card-header">
        <span><i class="feather-camera me-2 text-primary"></i>Foto Dokumentasi</span>
    </div>
    <div class="op-card-body">
        <div class="row g-3">
            <div class="col-6">
                <div class="text-center mb-1.5 fw-semibold fs-12 text-muted">Sebelum Kerja</div>
                @if($log->foto_sebelum_url)
                    <a href="{{ $log->foto_sebelum_url }}" target="_blank">
                        <img src="{{ $log->foto_sebelum_url }}" class="img-fluid rounded-3 border shadow-sm" style="max-height: 180px; width: 100%; object-fit: cover;" alt="Foto Sebelum">
                    </a>
                @else
                    <div class="p-4 bg-light text-muted text-center rounded-3 border fs-12">Tidak ada foto</div>
                @endif
            </div>
            <div class="col-6">
                <div class="text-center mb-1.5 fw-semibold fs-12 text-muted">Sesudah Kerja</div>
                @if($log->foto_sesudah_url)
                    <a href="{{ $log->foto_sesudah_url }}" target="_blank">
                        <img src="{{ $log->foto_sesudah_url }}" class="img-fluid rounded-3 border shadow-sm" style="max-height: 180px; width: 100%; object-fit: cover;" alt="Foto Sesudah">
                    </a>
                @else
                    <div class="p-4 bg-light text-muted text-center rounded-3 border fs-12">Tidak ada foto</div>
                @endif
            </div>
        </div>
    </div>
</div>

@if($log->isCompleted())
    <div class="alert alert-success border-0 shadow-sm d-flex align-items-center mb-3 p-3 rounded-3" style="background: #F0FDF4; border-left: 4px solid #10B981 !important;">
        <i class="feather-check-circle fs-3 me-3 text-success flex-shrink-0"></i>
        <div>
            <strong class="d-block text-dark fs-13">Laporan Pekerjaan Selesai & Terkunci</strong>
            <span class="fs-12 text-muted">Seluruh data pekerjaan shift ini telah diselesaikan dan dikunci untuk mencegah rekayasa.</span>
        </div>
    </div>
    <div class="mb-4">
        <a href="{{ route('operator.index') }}" class="btn btn-secondary w-100 py-3 rounded-3 fw-bold border-0 fs-14">
            <i class="feather-arrow-left me-2"></i>Kembali ke Beranda
        </a>
    </div>
@else
    <!-- Action Buttons -->
    <div class="d-flex gap-2 mb-4">
        <a href="{{ route('operator.edit', $log->id) }}" class="btn btn-warning text-dark flex-fill py-3 rounded-3 fw-bold shadow-sm border-0 fs-14 d-inline-flex align-items-center justify-content-center gap-1">
            <i class="feather-edit-2 me-1"></i>Selesaikan Shift
        </a>
        <a href="{{ route('operator.index') }}" class="btn btn-secondary flex-fill py-3 rounded-3 fw-bold border-0 fs-14 d-inline-flex align-items-center justify-content-center gap-1">
            <i class="feather-arrow-left me-1"></i>Kembali
        </a>
    </div>
@endif
@endsection
