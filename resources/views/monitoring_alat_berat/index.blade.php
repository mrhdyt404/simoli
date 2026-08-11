@extends('layouts.simoli')

@section('title', 'Monitoring Alat Berat')
@section('page-title', 'Monitoring Alat Berat')

@section('breadcrumb')
<li class="breadcrumb-item">Input Data</li>
<li class="breadcrumb-item active">Monitoring Alat Berat</li>
@endsection

@section('page-actions')
@if(Auth::user()->isUnit())
<a href="{{ route('monitoring-alat-berat.create') }}" class="btn btn-primary">
    <i class="feather-plus me-2"></i>Tambah Log Monitoring
</a>
@endif
@endsection

@section('content')
<!-- Summary Metric Cards -->
<div class="row">
            <div class="col-xl-3 col-sm-6 col-12 mb-3 mb-xl-4">
                <div class="card stretch stretch-full border-start border-4 border-primary h-100 mb-0">
                    <div class="card-body p-3 p-sm-4">
                        <div class="d-flex align-items-center justify-content-between gap-2">
                            <div class="overflow-hidden me-2">
                                <span class="text-muted fw-semibold d-block text-truncate fs-13">Total Jam Kerja (HM)</span>
                                <h3 class="mt-2 mb-0 fw-bold text-primary text-nowrap fs-20">{{ \App\Models\MonitoringAlatBerat::formatHm($totalHm, true) }}</h3>
                            </div>
                            <div class="avatar-text avatar-lg bg-soft-primary text-primary flex-shrink-0">
                                <i class="feather-clock fs-2"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6 col-12 mb-3 mb-xl-4">
                <div class="card stretch stretch-full border-start border-4 border-warning h-100 mb-0">
                    <div class="card-body p-3 p-sm-4">
                        <div class="d-flex align-items-center justify-content-between gap-2">
                            <div class="overflow-hidden me-2">
                                <span class="text-muted fw-semibold d-block text-truncate fs-13">Total Konsumsi BBM</span>
                                <h3 class="mt-2 mb-0 fw-bold text-warning text-nowrap fs-20">{{ number_format($totalBbm, 2) }} <span class="fs-13 text-muted">Liter</span></h3>
                            </div>
                            <div class="avatar-text avatar-lg bg-soft-warning text-warning flex-shrink-0">
                                <i class="feather-droplet fs-2"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6 col-12 mb-3 mb-xl-4">
                <div class="card stretch stretch-full border-start border-4 border-info h-100 mb-0">
                    <div class="card-body p-3 p-sm-4">
                        <div class="d-flex align-items-center justify-content-between gap-2">
                            <div class="overflow-hidden me-2">
                                <span class="text-muted fw-semibold d-block text-truncate fs-13">Total Bed Dikerjakan</span>
                                <h3 class="mt-2 mb-0 fw-bold text-info text-nowrap fs-20">{{ number_format($totalBed ?? 0) }} <span class="fs-13 text-muted">Bed</span></h3>
                                <small class="text-muted fs-11">Flat: {{ number_format($totalFlatBed ?? 0) }} | Long: {{ number_format($totalLongBed ?? 0) }}</small>
                            </div>
                            <div class="avatar-text avatar-lg bg-soft-info text-info flex-shrink-0">
                                <i class="feather-grid fs-2"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6 col-12 mb-3 mb-xl-4">
                <div class="card stretch stretch-full border-start border-4 border-success h-100 mb-0">
                    <div class="card-body p-3 p-sm-4">
                        <div class="d-flex align-items-center justify-content-between gap-2">
                            <div class="overflow-hidden me-2">
                                <span class="text-muted fw-semibold d-block text-truncate fs-13">Total Kegiatan Operasional</span>
                                <h3 class="mt-2 mb-0 fw-bold text-success text-nowrap fs-20">{{ $totalKegiatan }} <span class="fs-13 text-muted">Log</span></h3>
                            </div>
                            <div class="avatar-text avatar-lg bg-soft-success text-success flex-shrink-0">
                                <i class="feather-activity fs-2"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter Card -->
        <div class="row">
            <div class="col-12">
                <div class="card stretch stretch-full">
                    <div class="card-header">
                        <h5 class="card-title">Filter Log Monitoring</h5>
                    </div>
                    <div class="card-body">
                        <form method="GET" action="{{ route('monitoring-alat-berat.index') }}" class="row g-3">
                            @if(Auth::user()->isAdmin())
                                <div class="col-md-3">
                                    <label class="form-label">PKS Unit</label>
                                    <select name="id_pks" class="form-select">
                                        <option value="">-- Semua PKS --</option>
                                        @foreach($pksList as $pks)
                                            <option value="{{ $pks->id_pks }}" {{ request('id_pks') == $pks->id_pks ? 'selected' : '' }}>
                                                {{ $pks->nama }} ({{ $pks->akro }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            @endif

                            <div class="col-md-3">
                                <label class="form-label">Alat Berat</label>
                                <select name="alat_berat_id" class="form-select">
                                    <option value="">-- Semua Alat Berat --</option>
                                    @foreach($alatBeratList as $ab)
                                        <option value="{{ $ab->id }}" {{ request('alat_berat_id') == $ab->id ? 'selected' : '' }}>
                                            {{ $ab->kode_alat }} - {{ $ab->nama_alat }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-2">
                                <label class="form-label">Bulan</label>
                                <select name="bulan" class="form-select">
                                    <option value="">-- Semua Bulan --</option>
                                    @for($m=1; $m<=12; $m++)
                                        <option value="{{ sprintf('%02d', $m) }}" {{ request('bulan') == sprintf('%02d', $m) ? 'selected' : '' }}>
                                            {{ date('F', mktime(0, 0, 0, $m, 1)) }}
                                        </option>
                                    @endfor
                                </select>
                            </div>

                            <div class="col-md-2">
                                <label class="form-label">Tahun</label>
                                <select name="tahun" class="form-select">
                                    <option value="">-- Semua Tahun --</option>
                                    @for($y=date('Y'); $y>=2024; $y--)
                                        <option value="{{ $y }}" {{ request('tahun') == $y ? 'selected' : '' }}>{{ $y }}</option>
                                    @endfor
                                </select>
                            </div>

                            <div class="col-md-2">
                                <label class="form-label">Kondisi Alat</label>
                                <select name="kondisi_alat" class="form-select">
                                    <option value="">-- Semua Kondisi --</option>
                                    <option value="Normal" {{ request('kondisi_alat') == 'Normal' ? 'selected' : '' }}>Normal</option>
                                    <option value="Perlu Perbaikan" {{ request('kondisi_alat') == 'Perlu Perbaikan' ? 'selected' : '' }}>Perlu Perbaikan</option>
                                    <option value="Breakdown" {{ request('kondisi_alat') == 'Breakdown' ? 'selected' : '' }}>Breakdown</option>
                                </select>
                            </div>

                            <div class="col-12 d-flex justify-content-end gap-2">
                                <a href="{{ route('monitoring-alat-berat.index') }}" class="btn btn-light">Reset</a>
                                <button type="submit" class="btn btn-primary"><i class="feather-filter me-1"></i> Terapkan Filter</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Table Log -->
            <div class="col-12">
                <div class="card stretch stretch-full">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title m-0">Riwayat Operasional Alat Berat</h5>
                    </div>
                    <div class="card-body custom-table-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover table-striped mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>No</th>
                                        <th>Tanggal</th>
                                        <th>PKS</th>
                                        <th>Alat Berat</th>
                                        <th>Operator</th>
                                        <th>Kegiatan Pengolahan</th>
                                        <th>Flat / Long Bed</th>
                                        <th>HM (Awal - Akhir)</th>
                                        <th>Total HM</th>
                                        <th>BBM (L)</th>
                                        <th>Kondisi</th>
                                        <th>Foto</th>
                                        <th class="text-end">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($logs as $index => $log)
                                        <tr>
                                            <td>{{ $logs->firstItem() + $index }}</td>
                                            <td><span class="fw-bold">{{ \Carbon\Carbon::parse($log->tanggal)->format('d/m/Y') }}</span></td>
                                            <td><span class="badge bg-soft-secondary text-secondary">{{ $log->pks ? $log->pks->akro : '-' }}</span></td>
                                            <td>
                                                <div class="fw-bold text-primary">{{ $log->alatBerat ? $log->alatBerat->kode_alat : '-' }}</div>
                                                <small class="text-muted">{{ $log->alatBerat ? $log->alatBerat->nama_alat : '-' }}</small>
                                            </td>
                                            <td>{{ $log->operator }}</td>
                                            <td>
                                                <span class="fw-semibold text-dark">{{ $log->kegiatan }}</span>
                                                @if($log->lokasi_blok)
                                                    <div class="small text-muted"><i class="feather-map-pin me-1"></i>{{ $log->lokasi_blok }}</div>
                                                @endif
                                                @if($log->latitude && $log->longitude)
                                                    <div class="mt-1">
                                                        <a href="{{ $log->google_maps_url }}" target="_blank" class="badge bg-soft-info text-info text-decoration-none" title="Buka Koordinat di Maps">
                                                            <i class="feather-crosshair me-1"></i>{{ number_format($log->latitude, 4) }}, {{ number_format($log->longitude, 4) }}
                                                        </a>
                                                    </div>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge bg-soft-info text-info fs-12" title="Flat: {{ $log->flat_bed ?? 0 }} / Long: {{ $log->long_bed ?? 0 }}">
                                                    F: {{ $log->flat_bed ?? 0 }} | L: {{ $log->long_bed ?? 0 }} Bed
                                                </span>
                                            </td>
                                            <td>{{ $log->hm_awal_formatted }} - {{ $log->hm_akhir_formatted }}</td>
                                            <td><span class="badge bg-soft-primary text-primary fs-12">{{ $log->total_hm_formatted }} Jam</span></td>
                                            <td>{{ number_format($log->bbm_liter, 0) }} L</td>
                                            <td>
                                                @if($log->kondisi_alat == 'Normal')
                                                    <span class="badge bg-success">Normal</span>
                                                @elseif($log->kondisi_alat == 'Perlu Perbaikan')
                                                    <span class="badge bg-warning text-dark">Perlu Perbaikan</span>
                                                @else
                                                    <span class="badge bg-danger">Breakdown</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="d-flex gap-1">
                                                    @if($log->foto_sebelum_url)
                                                        <a href="{{ $log->foto_sebelum_url }}" target="_blank" title="Foto Sebelum Kerja">
                                                            <img src="{{ $log->foto_sebelum_url }}" alt="Sebelum" class="rounded border" style="width: 38px; height: 38px; object-fit: cover;">
                                                        </a>
                                                    @endif
                                                    @if($log->foto_sesudah_url)
                                                        <a href="{{ $log->foto_sesudah_url }}" target="_blank" title="Foto Sesudah Kerja">
                                                            <img src="{{ $log->foto_sesudah_url }}" alt="Sesudah" class="rounded border" style="width: 38px; height: 38px; object-fit: cover;">
                                                        </a>
                                                    @endif
                                                    @if(!$log->foto_sebelum && !$log->foto_sesudah && $log->foto_url)
                                                        <a href="{{ $log->foto_url }}" target="_blank" title="Foto Laporan">
                                                            <img src="{{ $log->foto_url }}" alt="Foto" class="rounded border" style="width: 38px; height: 38px; object-fit: cover;">
                                                        </a>
                                                    @endif
                                                    @if(!$log->foto_sebelum && !$log->foto_sesudah && !$log->foto)
                                                        <span class="text-muted fs-11">-</span>
                                                    @endif
                                                </div>
                                            </td>
                                            <td class="text-end">
                                                <div class="btn-group btn-group-sm">
                                                    <a href="{{ route('monitoring-alat-berat.show', $log->id) }}" class="btn btn-outline-info" title="Detail">
                                                        <i class="feather-eye"></i>
                                                    </a>
                                                    @if(Auth::user()->isUnit())
                                                        <a href="{{ route('monitoring-alat-berat.edit', $log->id) }}" class="btn btn-outline-warning" title="Edit">
                                                            <i class="feather-edit-2"></i>
                                                        </a>
                                                        <form action="{{ route('monitoring-alat-berat.destroy', $log->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus log monitoring ini?')">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-outline-danger" title="Hapus">
                                                                <i class="feather-trash-2"></i>
                                                            </button>
                                                        </form>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="13" class="text-center py-5 text-muted">
                                                <i class="feather-inbox fs-3 d-block mb-2"></i>
                                                Belum ada data log monitoring alat berat.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer d-flex justify-content-between align-items-center">
                        <div>
                            Menampilkan {{ $logs->firstItem() ?? 0 }} - {{ $logs->lastItem() ?? 0 }} dari {{ $logs->total() }} data
                        </div>
                        <div>
                            {{ $logs->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
@endsection
