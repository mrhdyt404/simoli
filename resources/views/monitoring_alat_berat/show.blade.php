@extends('layouts.simoli')

@section('title', 'Detail Log Monitoring Alat Berat')
@section('page-title', 'Detail Log Monitoring Alat Berat')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('monitoring-alat-berat.index') }}">Monitoring Alat Berat</a></li>
<li class="breadcrumb-item active">Detail Log</li>
@endsection

@section('page-actions')
<div class="d-flex align-items-center gap-2">
    @if(Auth::user()->isUnit())
    <a href="{{ route('monitoring-alat-berat.edit', $log->id) }}" class="btn btn-warning">
        <i class="feather-edit-2 me-1"></i> Edit Data
    </a>
    @endif
    <a href="{{ route('monitoring-alat-berat.index') }}" class="btn btn-light">
        <i class="feather-arrow-left me-1"></i> Kembali
    </a>
</div>
@endsection

@section('content')
<div class="row">
            <div class="col-lg-8 col-12">
                <div class="card stretch stretch-full">
                    <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <h5 class="card-title mb-0">Rincian Operasional Limbah</h5>
                        <span class="badge bg-soft-primary text-primary fs-13">{{ $log->pks ? $log->pks->nama : 'PKS N/A' }}</span>
                    </div>
                    <div class="card-body">
                        <div class="row g-3 g-md-4 mb-4">
                            <div class="col-12 col-sm-6">
                                <div class="p-3 border rounded bg-light h-100">
                                    <small class="text-muted d-block text-uppercase fw-bold mb-1">Tanggal Operasional</small>
                                    <div class="fs-15 fs-sm-16 fw-bold text-dark"><i class="feather-calendar me-2 text-primary"></i>{{ \Carbon\Carbon::parse($log->tanggal)->translatedFormat('l, d F Y') }}</div>
                                </div>
                            </div>
                            <div class="col-12 col-sm-6">
                                <div class="p-3 border rounded bg-light h-100">
                                    <small class="text-muted d-block text-uppercase fw-bold mb-1">Operator Alat Berat</small>
                                    <div class="fs-15 fs-sm-16 fw-bold text-dark"><i class="feather-user me-2 text-primary"></i>{{ $log->operator }}</div>
                                </div>
                            </div>
                            <div class="col-12 col-sm-6">
                                <div class="p-3 border rounded bg-light h-100">
                                    <small class="text-muted d-block text-uppercase fw-bold mb-1">Unit Alat Berat</small>
                                    <div class="fs-15 fs-sm-16 fw-bold text-primary">{{ $log->alatBerat ? $log->alatBerat->kode_alat : '-' }} - {{ $log->alatBerat ? $log->alatBerat->nama_alat : '-' }}</div>
                                    <small class="text-muted">Jenis: {{ $log->alatBerat ? $log->alatBerat->jenis_alat : '-' }}</small>
                                </div>
                            </div>
                            <div class="col-12 col-sm-6">
                                <div class="p-3 border rounded bg-light h-100">
                                    <small class="text-muted d-block text-uppercase fw-bold mb-1">Kondisi Alat Saat Kerja</small>
                                    <div>
                                        @if($log->kondisi_alat == 'Normal')
                                            <span class="badge bg-success fs-13"><i class="feather-check-circle me-1"></i> Normal / Baik</span>
                                        @elseif($log->kondisi_alat == 'Perlu Perbaikan')
                                            <span class="badge bg-warning text-dark fs-13"><i class="feather-alert-triangle me-1"></i> Perlu Perbaikan</span>
                                        @else
                                            <span class="badge bg-danger fs-13"><i class="feather-x-circle me-1"></i> Breakdown / Rusak</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="border-top pt-4 mb-4">
                            <h6 class="fw-bold mb-3">Kegiatan & Jam Kerja (Hour Meter)</h6>
                            <div class="table-responsive">
                                <table class="table table-bordered mb-0">
                                    <tbody>
                                        <tr>
                                            <th style="min-width: 140px; width: 35%;" class="bg-light">Jenis Kegiatan Limbah</th>
                                            <td class="fw-bold text-dark">{{ $log->kegiatan }}</td>
                                        </tr>
                                        <tr>
                                            <th class="bg-light">Lokasi / Kolam / Blok</th>
                                            <td>{{ $log->lokasi_blok ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <th class="bg-light">Flat Bed Dikerjakan</th>
                                            <td><span class="badge bg-soft-info text-info fs-13">{{ $log->flat_bed ?? 0 }} Bed</span></td>
                                        </tr>
                                        <tr>
                                            <th class="bg-light">Long Bed Dikerjakan</th>
                                            <td><span class="badge bg-soft-info text-info fs-13">{{ $log->long_bed ?? 0 }} Bed</span></td>
                                        </tr>
                                        <tr>
                                            <th class="bg-light">Total Bed Dikerjakan</th>
                                            <td><span class="badge bg-soft-primary text-primary fs-13">{{ $log->jumlah_bed }} Bed</span></td>
                                        </tr>
                                        @php
                                            $latAwal = $log->latitude_awal ?? $log->latitude;
                                            $longAwal = $log->longitude_awal ?? $log->longitude;
                                            $latAkhir = $log->latitude_akhir;
                                            $longAkhir = $log->longitude_akhir;
                                        @endphp
                                        <tr>
                                            <th class="bg-light">Titik Koordinat GPS Awal</th>
                                            <td>
                                                @if($latAwal && $longAwal)
                                                    <div class="d-flex flex-wrap align-items-center gap-2">
                                                        <span class="badge bg-soft-primary text-primary fs-13 text-break"><i class="feather-navigation me-1"></i>{{ $latAwal }}, {{ $longAwal }}</span>
                                                        <a href="{{ $log->google_maps_url_awal }}" target="_blank" class="btn btn-xs btn-outline-primary text-nowrap">
                                                            <i class="feather-external-link me-1"></i> Peta GPS Awal
                                                        </a>
                                                    </div>
                                                @else
                                                    <span class="text-muted fs-13">Koordinat Awal tidak direkam</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="bg-light">Titik Koordinat GPS Akhir</th>
                                            <td>
                                                @if($latAkhir && $longAkhir)
                                                    <div class="d-flex flex-wrap align-items-center gap-2">
                                                        <span class="badge bg-soft-success text-success fs-13 text-break"><i class="feather-navigation me-1"></i>{{ $latAkhir }}, {{ $longAkhir }}</span>
                                                        <a href="{{ $log->google_maps_url_akhir }}" target="_blank" class="btn btn-xs btn-outline-success text-nowrap">
                                                            <i class="feather-external-link me-1"></i> Peta GPS Akhir
                                                        </a>
                                                    </div>
                                                @else
                                                    <span class="text-muted fs-13">Koordinat Akhir tidak direkam</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="bg-light">Hour Meter (HM) Awal</th>
                                            <td>{{ $log->hm_awal_formatted }}</td>
                                        </tr>
                                        <tr>
                                            <th class="bg-light">Hour Meter (HM) Akhir</th>
                                            <td>{{ $log->hm_akhir_formatted }}</td>
                                        </tr>
                                        <tr>
                                            <th class="bg-light">Total Jam Kerja</th>
                                            <td><span class="badge bg-primary fs-14">{{ $log->total_hm_formatted }} Jam</span></td>
                                        </tr>
                                        <tr>
                                            <th class="bg-light">Konsumsi BBM Solar</th>
                                            <td><span class="badge bg-warning text-dark fs-14">{{ number_format($log->bbm_liter, 1) }} Liter</span></td>
                                        </tr>
                                        <tr>
                                            <th class="bg-light">Catatan Lapangan</th>
                                            <td>{{ $log->catatan ?? '-' }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="border-top pt-4">
                            <h6 class="fw-bold mb-3"><i class="feather-image me-2 text-primary"></i>Foto Laporan Sebelum & Sesudah Jam Kerja</h6>
                            
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="card border mb-0">
                                        <div class="card-header bg-soft-info py-2">
                                            <span class="fw-bold fs-13 text-info"><i class="feather-sunrise me-1"></i>Foto Sebelum Jam Kerja (Awal Shift)</span>
                                        </div>
                                        <div class="card-body text-center p-3">
                                            @if($log->foto_sebelum_url)
                                                <a href="{{ $log->foto_sebelum_url }}" target="_blank">
                                                    <img src="{{ $log->foto_sebelum_url }}" alt="Foto Sebelum Kerja" class="img-fluid rounded border shadow-sm" style="max-height: 250px; object-fit: cover;">
                                                </a>
                                            @else
                                                <div class="py-4 text-muted fs-13">
                                                    <i class="feather-image fs-3 d-block mb-1 opacity-50"></i>
                                                    Tidak ada foto sebelum kerja
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="card border mb-0">
                                        <div class="card-header bg-soft-success py-2">
                                            <span class="fw-bold fs-13 text-success"><i class="feather-sunset me-1"></i>Foto Sesudah Jam Kerja (Akhir Shift)</span>
                                        </div>
                                        <div class="card-body text-center p-3">
                                            @if($log->foto_sesudah_url)
                                                <a href="{{ $log->foto_sesudah_url }}" target="_blank">
                                                    <img src="{{ $log->foto_sesudah_url }}" alt="Foto Sesudah Kerja" class="img-fluid rounded border shadow-sm" style="max-height: 250px; object-fit: cover;">
                                                </a>
                                            @else
                                                <div class="py-4 text-muted fs-13">
                                                    <i class="feather-image fs-3 d-block mb-1 opacity-50"></i>
                                                    Tidak ada foto sesudah kerja
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>

                            @if($log->foto && $log->foto !== $log->foto_sebelum && $log->foto !== $log->foto_sesudah)
                                <div class="mt-4 pt-3 border-top">
                                    <h6 class="fw-bold mb-2 text-muted fs-13">Dokumentasi Tambahan (Lampiran Utama)</h6>
                                    <a href="{{ asset('gallery/' . $log->foto) }}" target="_blank">
                                        <img src="{{ asset('gallery/' . $log->foto) }}" alt="Dokumentasi Operasional" class="img-fluid rounded border shadow-sm" style="max-height: 200px;">
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
@endsection
