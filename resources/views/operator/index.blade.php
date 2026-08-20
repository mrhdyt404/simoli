@extends('layouts.operator')

@section('title', 'Beranda Operator Lapangan - SIMOLII')

@section('content')
<!-- Welcome Profile Card -->
<div class="op-card p-3 mb-3 border-0 text-white shadow-sm" style="background: linear-gradient(135deg, #0F52BA 0%, #1E3C72 100%);">
    <div class="d-flex align-items-center justify-content-between">
        <div>
            @if(Auth::user()->isMandor())
                <span class="badge bg-warning text-dark fw-bold mb-1.5 px-2.5 py-1" style="font-size: 0.68rem; letter-spacing: 0.3px;">
                    <i class="feather-shield me-1"></i>MANDOR LAPANGAN
                </span>
            @else
                <span class="badge bg-white text-primary fw-bold mb-1.5 px-2.5 py-1" style="font-size: 0.68rem; letter-spacing: 0.3px;">
                    <i class="feather-user me-1"></i>OPERATOR LAPANGAN
                </span>
            @endif
            <h5 class="fw-bolder m-0 mb-1 text-white fs-18">{{ Auth::user()->username }}</h5>
            <div class="opacity-85 fs-12 d-flex align-items-center gap-1 text-white">
                <i class="feather-map-pin"></i>
                <span>PKS {{ Auth::user()->pks ? Auth::user()->pks->nama : 'Unit' }}</span>
            </div>
        </div>
        <div class="text-end">
            <div class="fs-11 opacity-85 text-uppercase fw-semibold">{{ \Carbon\Carbon::now('Asia/Jakarta')->translatedFormat('l') }}</div>
            <div class="fw-bold fs-14 text-white">{{ \Carbon\Carbon::now('Asia/Jakarta')->translatedFormat('d M Y') }}</div>
        </div>
    </div>
</div>

<!-- Offline Outbox Queue Card (Rendered via IndexedDB JS) -->
<div id="simoli-offline-queue-card" class="op-card mb-3 border-warning" style="display: none; background: #FFFDF5;">
    <div class="op-card-header bg-warning-subtle text-dark d-flex justify-content-between align-items-center py-2 px-3">
        <span class="fw-bold fs-13"><i class="feather-hard-drive me-1.5 text-warning"></i>Antrean Laporan di HP (Offline)</span>
        <button type="button" class="btn btn-xs btn-warning text-dark fw-bold rounded-pill px-2.5 py-1 fs-11" onclick="SimoliSync.pushPendingQueue(true)">
            <i class="feather-upload-cloud me-1"></i>Sinkronkan Sekarang
        </button>
    </div>
    <div class="op-card-body p-2" id="simoli-offline-queue-list">
        <!-- Injected via JavaScript -->
    </div>
</div>

<!-- Primary Action Hero Button -->
<div class="mb-3">
    <a href="{{ route('operator.create') }}" class="btn btn-op-primary py-3 shadow">
        <i class="feather-plus-circle fs-5"></i>
        <span>INPUT LAPORAN KERJA BARU</span>
    </a>
</div>

<!-- Summary Stats Grid (2 columns on mobile, 4 columns on tablet & PC) -->
<div class="row g-2 mb-3">
    <div class="col-6 col-md-3">
        <div class="op-card p-3 h-100 text-center mb-0 border-0 shadow-sm" style="background: #F0FDF4; border-top: 3px solid #10B981 !important;">
            <div class="text-muted fs-11 fw-semibold text-uppercase">Laporan (7 Hari)</div>
            <div class="fs-3 fw-bolder text-success my-1">{{ $stats['total_laporan_today'] ?? 0 }}</div>
            <div class="fs-11 text-muted">Laporan Terdaftar</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="op-card p-3 h-100 text-center mb-0 border-0 shadow-sm" style="background: #EFF6FF; border-top: 3px solid #0F52BA !important;">
            <div class="text-muted fs-11 fw-semibold text-uppercase">Total HM Kerja</div>
            <div class="fs-4 fw-bolder text-primary my-1">{{ \App\Models\MonitoringAlatBerat::formatHm($stats['total_hm_today'] ?? 0, true) }}</div>
            <div class="fs-11 text-muted">Durasi Jam Kerja</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="op-card p-3 h-100 text-center mb-0 border-0 shadow-sm" style="background: #F0F9FF; border-top: 3px solid #0284C7 !important;">
            <div class="text-muted fs-11 fw-semibold text-uppercase">Aplikasi Bed</div>
            <div class="fs-3 fw-bolder text-info my-1">{{ $stats['total_bed_today'] ?? 0 }}</div>
            <div class="fs-11 text-muted">Flat & Long Bed</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="op-card p-3 h-100 text-center mb-0 border-0 shadow-sm" style="background: #FFFBEB; border-top: 3px solid #F59E0B !important;">
            <div class="text-muted fs-11 fw-semibold text-uppercase">Konsumsi BBM</div>
            <div class="fs-3 fw-bolder text-warning my-1">{{ number_format($stats['total_bbm_today'] ?? 0, 1) }}</div>
            <div class="fs-11 text-muted">Liter Solar</div>
        </div>
    </div>
</div>

<!-- Equipment Readiness Card -->
<div class="op-card mb-3">
    <div class="op-card-header d-flex justify-content-between align-items-center">
        <span><i class="feather-truck me-2 text-primary"></i>Unit Alat Berat PKS</span>
        @if(Auth::user()->canManageAlatBerat())
            <a href="{{ route('operator.alat-berat.index') }}" class="btn btn-sm btn-outline-primary rounded-pill px-2.5 py-0.5 fs-11 fw-bold">
                <i class="feather-settings me-1"></i>Kelola Unit
            </a>
        @else
            <span class="badge bg-light text-muted border fs-11">
                <i class="feather-info me-1"></i>Status Kesiapan
            </span>
        @endif
    </div>
    <div class="op-card-body p-2">
        <div class="row g-2">
            @forelse($alatBeratList as $ab)
                <div class="col-12 col-md-6">
                    <div class="d-flex align-items-center justify-content-between p-2.5 rounded-3 bg-light border border-light-subtle h-100">
                        <div>
                            <div class="fw-bold text-dark fs-13">
                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle me-1">{{ $ab->kode_alat }}</span>
                                <span>{{ $ab->nama_alat }}</span>
                            </div>
                            <div class="text-muted fs-11 mt-0.5">{{ $ab->jenis_alat }} {{ $ab->merk_tipe ? '• ' . $ab->merk_tipe : '' }}</div>
                        </div>
                        <div class="ms-2 flex-shrink-0">
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
                </div>
            @empty
                <div class="col-12 text-center p-3 text-muted fs-12">Belum ada unit alat berat terdaftar.</div>
            @endforelse
        </div>
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
                <div class="d-flex align-items-center justify-content-between mb-1.5">
                    <span class="fw-bold text-primary fs-12 d-flex align-items-center">
                        <i class="feather-calendar me-1"></i>{{ \Carbon\Carbon::parse($item->tanggal)->translatedFormat('d M Y') }}
                    </span>
                    <span class="badge bg-soft-info text-info fs-11 fw-bold border border-info-subtle">
                        <i class="feather-clock me-1"></i>HM: {{ \App\Http\Controllers\OperatorMonitoringController::formatHmDisplay($item->total_hm, $item->hm_awal, $item->hm_akhir) }}
                    </span>
                </div>
                
                <div class="fw-bold text-dark fs-14 mb-1">
                    <span class="badge bg-secondary bg-opacity-10 text-dark border me-1">[{{ $item->alatBerat ? $item->alatBerat->kode_alat : '-' }}]</span>
                    <span>{{ $item->alatBerat ? $item->alatBerat->nama_alat : 'Alat Berat' }}</span>
                </div>

                <div class="text-muted fs-12 mb-2">
                    <span class="d-inline-flex align-items-center me-2"><i class="feather-user me-1"></i>{{ $item->operator }}</span>
                    <span class="d-inline-flex align-items-center me-2"><i class="feather-activity me-1 text-primary"></i>{{ $item->kegiatan }}</span>
                </div>

                <div class="d-flex align-items-center justify-content-between pt-1 flex-wrap gap-2">
                    <div class="d-flex gap-1 flex-wrap">
                        @if($item->lokasi_blok)
                            <span class="badge bg-soft-success text-success fs-11 border border-success-subtle">
                                <i class="feather-map-pin me-1"></i>Blok: {{ $item->lokasi_blok }}
                            </span>
                        @endif
                        @if($item->no_bak)
                            <span class="badge bg-soft-info text-info fs-11 border border-info-subtle">
                                <i class="feather-layers me-1"></i>Bak: {{ $item->no_bak }}
                            </span>
                        @endif
                        @if($item->flat_bed > 0 || $item->jumlah_bed > 0)
                            <span class="badge bg-soft-primary text-primary fs-11 border border-primary-subtle fw-bold">
                                Bed di alirkan: {{ number_format($item->flat_bed ?: $item->jumlah_bed, 0, ',', '.') }} Bed
                            </span>
                        @endif
                        @if($item->bbm_liter > 0)
                            <span class="badge bg-soft-warning text-warning fs-11">BBM: {{ $item->bbm_liter }} L</span>
                        @endif
                    </div>
                    <div class="d-flex gap-2 align-items-center ms-auto">
                        <a href="{{ route('operator.show', $item->id) }}" class="btn btn-sm btn-outline-primary px-2.5 py-1 fs-12 fw-semibold rounded-pill" title="Detail Laporan">
                            <i class="feather-eye me-1"></i>Detail
                        </a>
                        @if(!$item->isCompleted())
                            <a href="{{ route('operator.edit', $item->id) }}" class="btn btn-sm btn-warning text-dark px-3 py-1 fs-12 fw-bold shadow-sm rounded-pill border-0" title="Edit & Selesaikan Pekerjaan">
                                <i class="feather-edit-2 me-1"></i>Selesaikan Shift
                            </a>
                        @else
                            <span class="badge bg-soft-success text-success px-2.5 py-1.5 fs-11 fw-bold rounded-pill border border-success-subtle">
                                <i class="feather-check-circle me-1"></i>Selesai
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="text-center p-4 text-muted">
                <i class="feather-inbox fs-2 d-block mb-2 text-muted opacity-50"></i>
                <div class="fs-13 fw-semibold">Belum ada laporan kerja</div>
                <div class="fs-11 text-muted">Silakan klik tombol Input Laporan Kerja Baru di atas untuk memulai.</div>
            </div>
        @endforelse
    </div>
    @if($logs->hasPages())
        <div class="p-2.5 border-top bg-light">
            {{ $logs->links() }}
        </div>
    @endif
</div>
@endsection

@section('scripts')
<script>
    async function renderOfflineQueue() {
        const queueCard = document.getElementById('simoli-offline-queue-card');
        const queueList = document.getElementById('simoli-offline-queue-list');
        if (!queueCard || !queueList) return;

        const items = await SimoliDB.getPendingQueue();
        if (!items || items.length === 0) {
            queueCard.style.display = 'none';
            queueList.innerHTML = '';
            return;
        }

        const masterAlat = await SimoliDB.getMasterAlatBerat();
        const alatMap = {};
        if (masterAlat) {
            masterAlat.forEach(a => { alatMap[a.id] = a; });
        }

        queueCard.style.display = 'block';
        let html = '';

        items.forEach((item, index) => {
            const timeStr = item.client_created_at ? new Date(item.client_created_at).toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' }) : '-';
            const alat = alatMap[item.alat_berat_id];
            const alatName = alat ? `[${alat.kode_alat}] ${alat.nama_alat}` : 'Unit Alat Berat';

            html += `
                <div class="d-flex align-items-center justify-content-between p-2.5 mb-2 bg-white rounded-3 border border-warning shadow-sm">
                    <div class="pe-2">
                        <div class="fw-bold fs-13 text-dark mb-0.5">
                            <span class="badge bg-warning text-dark me-1 fs-11">Offline di HP</span>
                            ${alatName}
                        </div>
                        <div class="text-muted fs-11">
                            <i class="feather-activity me-1"></i>${item.kegiatan || 'Laporan Kerja'}
                            ${item.lokasi_blok ? `| <i class="feather-map-pin me-1"></i>${item.lokasi_blok}` : ''}
                        </div>
                        <div class="text-muted fs-11 mt-0.5">
                            <i class="feather-clock me-1"></i>${timeStr} | Operator: ${item.operator || '-'} | HM: ${item.hm_awal || '-'} s/d ${item.hm_akhir || '-'}
                        </div>
                    </div>
                    <span class="badge bg-secondary rounded-pill fs-11 px-2 py-1 flex-shrink-0">
                        <i class="feather-clock me-1"></i>Menunggu Sinyal
                    </span>
                </div>
            `;
        });

        queueList.innerHTML = html;
        if (window.feather) feather.replace();
    }

    document.addEventListener('DOMContentLoaded', () => {
        renderOfflineQueue();

        // Re-render whenever sync status updates
        window.addEventListener('online', () => {
            setTimeout(renderOfflineQueue, 1500);
        });
        window.addEventListener('offline', renderOfflineQueue);
        setInterval(renderOfflineQueue, 8000);
    });
</script>
@endsection
