@extends('layouts.operator')

@section('title', 'Beranda Operator Lapangan - SIMOLII')

@section('content')
<!-- Welcome Profile Card -->
<div class="op-card p-3 mb-3 border-0 text-white" style="background: linear-gradient(135deg, #0F52BA 0%, #1E3C72 100%);">
    <div class="d-flex align-items-center justify-content-between">
        <div>
            @if(Auth::user()->isMandor())
                <span class="badge bg-warning text-dark fw-bold mb-1" style="font-size: 0.7rem;">
                    <i class="feather-shield me-1"></i>MANDOR LAPANGAN
                </span>
            @else
                <span class="badge bg-white text-primary fw-bold mb-1" style="font-size: 0.7rem;">
                    <i class="feather-user me-1"></i>OPERATOR LAPANGAN
                </span>
            @endif
            <h5 class="fw-bold m-0 mb-1 text-white">{{ Auth::user()->username }}</h5>
            <small class="opacity-75" style="font-size: 0.78rem;">
                <i class="feather-map-pin me-1"></i>PKS {{ Auth::user()->pks ? Auth::user()->pks->nama : 'Unit' }}
            </small>
        </div>
        <div class="text-end">
            <div class="fs-12 opacity-75">{{ \Carbon\Carbon::now('Asia/Jakarta')->translatedFormat('l') }}</div>
            <div class="fw-bold fs-14">{{ \Carbon\Carbon::now('Asia/Jakarta')->translatedFormat('d M Y') }}</div>
        </div>
    </div>
</div>

<!-- Primary Action Button -->
<div class="mb-3">
    <a href="{{ route('operator.create') }}" class="btn btn-op-primary d-flex align-items-center justify-content-center gap-2 py-3 shadow-lg">
        <i class="feather-plus-circle fs-5"></i>
        <span>INPUT LAPORAN KERJA BARU</span>
    </a>
</div>

<!-- Today's Stats Grid -->
<div class="row g-2 mb-3">
    <div class="col-6">
        <div class="op-card p-3 h-100 text-center mb-0">
            <div class="text-muted fs-12 fw-semibold">Laporan Kerja (7 Hari)</div>
            <div class="fs-3 fw-bold text-primary my-1">{{ $stats['total_laporan_today'] ?? 0 }}</div>
            <div class="fs-11 text-muted">Laporan Terdaftar</div>
        </div>
    </div>
    <div class="col-6">
        <div class="op-card p-3 h-100 text-center mb-0">
            <div class="text-muted fs-12 fw-semibold">Total HM Kerja</div>
            <div class="fs-4 fw-bold text-success my-1">{{ \App\Models\MonitoringAlatBerat::formatHm($stats['total_hm_today'] ?? 0, true) }}</div>
            <div class="fs-11 text-muted">Durasi Jam Kerja</div>
        </div>
    </div>
    <div class="col-6">
        <div class="op-card p-3 h-100 text-center mb-0">
            <div class="text-muted fs-12 fw-semibold">Aplikasi Bed</div>
            <div class="fs-3 fw-bold text-info my-1">{{ $stats['total_bed_today'] ?? 0 }}</div>
            <div class="fs-11 text-muted">Flat & Long Bed</div>
        </div>
    </div>
    <div class="col-6">
        <div class="op-card p-3 h-100 text-center mb-0">
            <div class="text-muted fs-12 fw-semibold">Konsumsi BBM</div>
            <div class="fs-3 fw-bold text-warning my-1">{{ number_format($stats['total_bbm_today'] ?? 0, 1) }}</div>
            <div class="fs-11 text-muted">Liter Solar</div>
        </div>
    </div>
</div>

<!-- Equipment Readiness Card -->
<div class="op-card mb-3">
    <div class="op-card-header d-flex justify-content-between align-items-center">
        <span><i class="feather-truck me-2 text-primary"></i>Unit Alat Berat PKS</span>
        @if(Auth::user()->canManageAlatBerat())
            <a href="{{ route('operator.alat-berat.index') }}" class="btn btn-sm btn-outline-primary rounded-pill px-2 py-1 fs-11 fw-bold">
                <i class="feather-settings me-1"></i>Kelola & Status
            </a>
        @else
            <span class="badge bg-light text-muted border fs-11">
                <i class="feather-info me-1"></i>Status Readiness
            </span>
        @endif
    </div>
    <div class="op-card-body p-2">
        @forelse($alatBeratList as $ab)
            <div class="d-flex align-items-center justify-content-between p-2 border-bottom last-border-0">
                <div>
                    <div class="fw-bold text-dark fs-14">
                        <code>[{{ $ab->kode_alat }}]</code> {{ $ab->nama_alat }}
                    </div>
                    <div class="text-muted fs-12">{{ $ab->jenis_alat }} {{ $ab->merk_tipe ? '• ' . $ab->merk_tipe : '' }}</div>
                </div>
                <div>
                    @if($ab->status == 'Operational')
                        <span class="badge bg-success badge-status-op">Ready</span>
                    @elseif($ab->status == 'Standby')
                        <span class="badge bg-secondary badge-status-op">Standby</span>
                    @elseif($ab->status == 'Maintenance')
                        <span class="badge bg-warning text-dark badge-status-op">Perbaikan</span>
                    @elseif($ab->status == 'Breakdown')
                        <span class="badge bg-danger badge-status-op">Rusak</span>
                    @elseif($ab->status == 'Rolling')
                        <span class="badge bg-info text-dark badge-status-op">Rolling</span>
                    @endif
                </div>
            </div>
        @empty
            <div class="text-center p-3 text-muted">Belum ada unit alat berat terdaftar.</div>
        @endforelse
    </div>
</div>

<!-- Recent Work Logs -->
<div class="op-card mb-3">
    <div class="op-card-header">
        <span><i class="feather-clock me-2 text-primary"></i>Riwayat Laporan Kerja Terbaru</span>
    </div>
    <div class="op-card-body p-0">
        @forelse($logs as $item)
            <div class="p-3 border-bottom position-relative">
                <div class="d-flex align-items-center justify-content-between mb-1">
                    <span class="fw-bold text-primary fs-13">
                        <i class="feather-calendar me-1"></i>{{ \Carbon\Carbon::parse($item->tanggal)->translatedFormat('d M Y') }}
                    </span>
                    <span class="badge bg-soft-info text-info fs-11 fw-bold border border-info-subtle">
                        <i class="feather-clock me-1"></i>HM: {{ \App\Http\Controllers\OperatorMonitoringController::formatHmDisplay($item->total_hm, $item->hm_awal, $item->hm_akhir) }}
                    </span>
                </div>
                
                <div class="fw-bold text-dark fs-14 mb-1">
                    <code>[{{ $item->alatBerat ? $item->alatBerat->kode_alat : '-' }}]</code> 
                    {{ $item->alatBerat ? $item->alatBerat->nama_alat : 'Alat Berat' }}
                </div>

                <div class="text-muted fs-12 mb-2">
                    <i class="feather-user me-1"></i>{{ $item->operator }} | 
                    <i class="feather-activity me-1"></i>{{ $item->kegiatan }}
                    @if($item->lokasi_blok) | <i class="feather-map-pin me-1"></i>{{ $item->lokasi_blok }} @endif
                </div>

                <div class="d-flex align-items-center justify-content-between pt-1">
                    <div class="d-flex gap-1 flex-wrap">
                        @if($item->flat_bed > 0 || $item->long_bed > 0)
                            <span class="badge bg-soft-primary text-primary fs-11">
                                Bed: {{ $item->flat_bed }} FB / {{ $item->long_bed }} LB (Total: {{ $item->jumlah_bed }})
                            </span>
                        @endif
                        @if($item->bbm_liter > 0)
                            <span class="badge bg-soft-warning text-warning fs-11">BBM: {{ $item->bbm_liter }} L</span>
                        @endif
                    </div>
                    <div class="d-flex gap-2 align-items-center">
                        <a href="{{ route('operator.show', $item->id) }}" class="btn btn-sm btn-outline-primary px-2.5 py-1 fs-12 fw-semibold rounded-2" title="Detail Laporan">
                            <i class="feather-eye me-1"></i>Detail
                        </a>
                        @if(!$item->isCompleted())
                            <a href="{{ route('operator.edit', $item->id) }}" class="btn btn-sm btn-warning text-dark px-2.5 py-1 fs-12 fw-bold shadow-sm rounded-2 border-0" title="Edit & Selesaikan Pekerjaan">
                                <i class="feather-edit-2 me-1"></i>Selesaikan Shift
                            </a>
                        @else
                            <span class="badge bg-soft-success text-success px-2 py-1 fs-11 fw-bold border border-success-subtle">
                                <i class="feather-check-circle me-1"></i>Selesai
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="text-center p-4 text-muted">
                <i class="feather-inbox fs-2 d-block mb-2 text-muted"></i>
                Belum ada laporan kerja alat berat yang dimasukkan.
            </div>
        @endforelse
    </div>
    @if($logs->hasPages())
        <div class="p-2 border-top">
            {{ $logs->links() }}
        </div>
    @endif
</div>
@endsection
