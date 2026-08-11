@extends('layouts.operator')

@section('title', 'Detail Laporan Kerja - SIMOLII Operator')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-3">
    <h5 class="fw-bold m-0"><i class="feather-file-text me-2 text-primary"></i>Detail Laporan Kerja</h5>
    <a href="{{ route('operator.index') }}" class="btn btn-sm btn-outline-secondary rounded-pill">
        <i class="feather-arrow-left me-1"></i>Kembali
    </a>
</div>

<!-- Main Detail Card -->
<div class="op-card mb-3">
    <div class="op-card-header bg-primary text-white d-flex align-items-center justify-content-between">
        <span><i class="feather-truck me-2"></i>{{ $log->alatBerat ? $log->alatBerat->nama_alat : 'Alat Berat' }}</span>
        <code class="text-white bg-dark px-2 py-1 rounded fs-12">{{ $log->alatBerat ? $log->alatBerat->kode_alat : '-' }}</code>
    </div>
    <div class="op-card-body">
        <div class="row g-2 mb-3">
            <div class="col-6">
                <div class="p-2 bg-light rounded border">
                    <span class="text-muted fs-11 d-block">TANGGAL KERJA</span>
                    <span class="fw-bold fs-13 text-dark">{{ \Carbon\Carbon::parse($log->tanggal)->translatedFormat('d F Y') }}</span>
                </div>
            </div>
            <div class="col-6">
                <div class="p-2 bg-light rounded border">
                    <span class="text-muted fs-11 d-block">OPERATOR</span>
                    <span class="fw-bold fs-13 text-dark">{{ $log->operator }}</span>
                </div>
            </div>
        </div>

        <div class="mb-3">
            <span class="text-muted fs-11 d-block fw-semibold">KEGIATAN / PEKERJAAN</span>
            <div class="fw-bold text-primary fs-14">{{ $log->kegiatan }}</div>
        </div>

        <div class="mb-3">
            <span class="text-muted fs-11 d-block fw-semibold">LOKASI / BLOK</span>
            <div class="fw-semibold text-dark fs-13">{{ $log->lokasi_blok ?? '-' }}</div>
        </div>

        <!-- Bed Breakdown Grid -->
        <div class="row g-2 mb-3">
            <div class="col-4">
                <div class="p-2 text-center bg-soft-primary rounded border border-primary-subtle">
                    <span class="text-muted fs-11 d-block">Flat Bed</span>
                    <span class="fw-bold fs-14 text-primary">{{ $log->flat_bed }}</span>
                </div>
            </div>
            <div class="col-4">
                <div class="p-2 text-center bg-soft-primary rounded border border-primary-subtle">
                    <span class="text-muted fs-11 d-block">Long Bed</span>
                    <span class="fw-bold fs-14 text-primary">{{ $log->long_bed }}</span>
                </div>
            </div>
            <div class="col-4">
                <div class="p-2 text-center bg-primary text-white rounded">
                    <span class="opacity-75 fs-11 d-block">Total Bed</span>
                    <span class="fw-bold fs-14">{{ $log->jumlah_bed }}</span>
                </div>
            </div>
        </div>

        <!-- HM Breakdown Grid -->
        <div class="row g-2 mb-3">
            <div class="col-4">
                <div class="p-2 text-center bg-light rounded border">
                    <span class="text-muted fs-11 d-block">Jam Awal</span>
                    <span class="fw-bold fs-13 text-dark">{{ $log->hm_awal_formatted }}</span>
                </div>
            </div>
            <div class="col-4">
                <div class="p-2 text-center bg-light rounded border">
                    <span class="text-muted fs-11 d-block">Jam Akhir</span>
                    <span class="fw-bold fs-13 text-dark">{{ $log->hm_akhir_formatted }}</span>
                </div>
            </div>
            <div class="col-4">
                <div class="p-2 text-center bg-success text-white rounded">
                    <span class="opacity-75 fs-11 d-block">Total HM</span>
                    <span class="fw-bold fs-13">{{ \App\Models\MonitoringAlatBerat::formatHm($log->total_hm, true) }}</span>
                </div>
            </div>
        </div>

        <div class="row g-2 mb-3">
            <div class="col-6">
                <div class="p-2 bg-light rounded border">
                    <span class="text-muted fs-11 d-block">BBM SOLAR</span>
                    <span class="fw-bold fs-13 text-warning">{{ number_format($log->bbm_liter, 1) }} Liter</span>
                </div>
            </div>
            <div class="col-6">
                <div class="p-2 bg-light rounded border">
                    <span class="text-muted fs-11 d-block">KONDISI ALAT</span>
                    @if($log->kondisi_alat == 'Normal')
                        <span class="badge bg-success">Normal / Baik</span>
                    @elseif($log->kondisi_alat == 'Perlu Perbaikan')
                        <span class="badge bg-warning text-dark">Perlu Perbaikan</span>
                    @else
                        <span class="badge bg-danger">Breakdown</span>
                    @endif
                </div>
            </div>
        </div>

        @if($log->catatan)
            <div class="mb-3 p-2 bg-light rounded border">
                <span class="text-muted fs-11 d-block fw-semibold">CATATAN / KENDALA</span>
                <span class="fs-13 text-dark">{{ $log->catatan }}</span>
            </div>
        @endif

        @if($log->latitude && $log->longitude)
            <div class="mb-3 p-2 bg-light rounded border d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted fs-11 d-block fw-semibold">LOKASI KOORDINAT GPS</span>
                    <span class="fs-12 text-dark fw-bold">{{ $log->latitude }}, {{ $log->longitude }}</span>
                </div>
                <a href="https://maps.google.com/?q={{ $log->latitude }},{{ $log->longitude }}" target="_blank" class="btn btn-sm btn-outline-primary rounded-pill">
                    <i class="feather-map me-1"></i>Buka Peta
                </a>
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
        <div class="row g-2">
            <div class="col-6">
                <div class="text-center mb-1 fw-semibold fs-12 text-muted">Sebelum Kerja</div>
                @if($log->foto_sebelum_url)
                    <a href="{{ $log->foto_sebelum_url }}" target="_blank">
                        <img src="{{ $log->foto_sebelum_url }}" class="img-fluid rounded-3 border shadow-sm" style="max-height: 160px; width: 100%; object-fit: cover;">
                    </a>
                @else
                    <div class="p-4 bg-light text-muted text-center rounded border fs-12">Tidak ada foto</div>
                @endif
            </div>
            <div class="col-6">
                <div class="text-center mb-1 fw-semibold fs-12 text-muted">Sesudah Kerja</div>
                @if($log->foto_sesudah_url)
                    <a href="{{ $log->foto_sesudah_url }}" target="_blank">
                        <img src="{{ $log->foto_sesudah_url }}" class="img-fluid rounded-3 border shadow-sm" style="max-height: 160px; width: 100%; object-fit: cover;">
                    </a>
                @else
                    <div class="p-4 bg-light text-muted text-center rounded border fs-12">Tidak ada foto</div>
                @endif
            </div>
        </div>
    </div>
</div>

@if($log->isCompleted())
    <div class="alert alert-success border-0 shadow-sm d-flex align-items-center mb-3 p-3 rounded-3">
        <i class="feather-check-circle fs-3 me-3 text-success"></i>
        <div>
            <strong class="d-block text-dark fs-14">Laporan Pekerjaan Selesai & Terkunci</strong>
            <span class="fs-12 text-muted">Seluruh data pekerjaan shift ini telah diselesaikan dan dikunci untuk mencegah rekayasa.</span>
        </div>
    </div>
    <div class="mb-4">
        <a href="{{ route('operator.index') }}" class="btn btn-secondary w-100 py-2.5 rounded-3 fw-bold border-0 fs-14">
            <i class="feather-arrow-left me-2"></i>Kembali ke Beranda
        </a>
    </div>
@else
    <!-- Action Buttons -->
    <div class="d-flex gap-2 mb-4">
        <a href="{{ route('operator.edit', $log->id) }}" class="btn btn-warning text-dark flex-fill py-2.5 rounded-3 fw-bold shadow-sm border-0 fs-14">
            <i class="feather-edit-2 me-2"></i>Selesaikan Shift
        </a>
        <a href="{{ route('operator.index') }}" class="btn btn-secondary flex-fill py-2.5 rounded-3 fw-bold border-0 fs-14">
            <i class="feather-arrow-left me-2"></i>Kembali
        </a>
    </div>
@endif
@endsection
