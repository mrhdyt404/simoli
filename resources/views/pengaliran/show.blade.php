@extends('layouts.simoli')

@section('title', 'Detail Pengaliran')
@section('page-title', 'Detail Pengaliran')

@section('breadcrumb')
    <li class="breadcrumb-item">Input Data</li>
    <li class="breadcrumb-item">
        <a href="{{ route('pengaliran.index') }}">Pengaliran</a>
    </li>
    <li class="breadcrumb-item active">Detail</li>
@endsection

@section('page-actions')
    <div class="page-header-right-items">
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('pengaliran.index') }}" class="btn btn-light">
                <i class="feather-arrow-left me-2"></i>
                Kembali
            </a>

            <a href="{{ route('pengaliran.edit', $data->id_pengaliran) }}" class="btn btn-primary">
                <i class="feather-edit-2 me-2"></i>
                Edit
            </a>
                @if(Auth::user()->isAdmin())
                <form action="{{ route('pengaliran.destroy', $data->id_pengaliran) }}" method="POST" class="d-inline form-delete">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-outline-danger">
                        <i class="feather-trash-2 me-2"></i>
                        Hapus
                    </button>
                </form>
                @endif
        </div>
    </div>
@endsection

@section('styles')
<style>
    .detail-card {
        border: none;
        border-radius: 12px;
    }
    .detail-section-title {
        font-size: 14px;
        font-weight: 700;
        color: #4338ca;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 20px;
        padding-bottom: 12px;
        border-bottom: 2px solid #e0e7ff;
    }
    html.app-skin-dark .detail-section-title {
        color: #818cf8;
        border-bottom-color: #312e81;
    }
    .detail-section-title i {
        margin-right: 8px;
    }
    .detail-item {
        padding: 12px 0;
        border-bottom: 1px solid #f3f4f6;
    }
    html.app-skin-dark .detail-item {
        border-bottom-color: #374151;
    }
    .detail-item:last-child {
        border-bottom: none;
    }
    .detail-label {
        font-size: 12px;
        font-weight: 600;
        color: #6b7280;
        text-transform: uppercase;
        letter-spacing: 0.3px;
        margin-bottom: 6px;
        display: block;
    }
    html.app-skin-dark .detail-label {
        color: #9ca3af;
    }
    .detail-value {
        font-size: 15px;
        font-weight: 500;
        color: #1f2937;
    }
    html.app-skin-dark .detail-value {
        color: #f3f4f6;
    }
    .info-badge {
        display: inline-block;
        background-color: #f0f4ff;
        color: #4338ca;
        padding: 6px 12px;
        border-radius: 6px;
        font-weight: 600;
        font-size: 13px;
    }
    html.app-skin-dark .info-badge {
        background-color: #312e81;
        color: #818cf8;
    }
    .value-info {
        color: #0891b2;
        font-weight: 600;
    }
    html.app-skin-dark .value-info {
        color: #22d3ee;
    }
    .value-success {
        color: #059669;
        font-weight: 600;
    }
    html.app-skin-dark .value-success {
        color: #10b981;
    }
    .value-primary {
        color: #2563eb;
        font-weight: 600;
    }
    html.app-skin-dark .value-primary {
        color: #60a5fa;
    }
    .keterangan-box {
        background: linear-gradient(135deg, #f0f4ff 0%, #faf5ff 100%);
        padding: 16px;
        border-left: 4px solid #4338ca;
        border-radius: 8px;
        font-size: 14px;
        line-height: 1.6;
        color: #374151;
    }
    html.app-skin-dark .keterangan-box {
        background: linear-gradient(135deg, #312e81 0%, #2e1065 100%);
        color: #e5e7eb;
        border-left-color: #818cf8;
    }
</style>
@endsection

@section('content')

    <div class="row">
        <div class="col-lg-8">
            {{-- Informasi Umum --}}
            <div class="card detail-card shadow-sm mb-4">
                <div class="card-body p-4">
                    <div class="detail-section-title">
                        <i class="feather-info"></i> Informasi Umum
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="detail-item">
                                <span class="detail-label">Tanggal</span>
                                <div class="detail-value">{{ $data->tanggal }}</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="detail-item">
                                <span class="detail-label">PKS</span>
                                <div class="detail-value">
                                    <span class="info-badge">{{ $data->pks->AKRO }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="detail-item">
                                <span class="detail-label">Jam Mulai</span>
                                <div class="detail-value">{{ $data->jam_mulai }}</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="detail-item">
                                <span class="detail-label">Jam Selesai</span>
                                <div class="detail-value">{{ $data->jam_selesai }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Detail Lokasi --}}
            <div class="card detail-card shadow-sm mb-4">
                <div class="card-body p-4">
                    <div class="detail-section-title">
                        <i class="feather-map-pin"></i> Detail Lokasi
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="detail-item">
                                <span class="detail-label">No. Bak</span>
                                <div class="detail-value">{{ $data->no_bak ?? '-' }}</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="detail-item">
                                <span class="detail-label">Blok</span>
                                <div class="detail-value">{{ $data->blok ?? '-' }}</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="detail-item">
                                <span class="detail-label">Rotasi</span>
                                <div class="detail-value">{{ $data->rotasi ?? '-' }}</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="detail-item">
                                <span class="detail-label">Luas Area</span>
                                <div class="detail-value">{{ $data->luas_area ?? '-' }} Ha</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Data Volume --}}
            <div class="card detail-card shadow-sm mb-4">
                <div class="card-body p-4">
                    <div class="detail-section-title">
                        <i class="feather-droplet"></i> Data Volume
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="detail-item">
                                <span class="detail-label">Flat Bed</span>
                                <div class="detail-value">{{ number_format($data->flat_bed) }}</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="detail-item">
                                <span class="detail-label">Vol. Dihasilkan</span>
                                <div class="detail-value value-info">{{ number_format($data->vol_limbah_dihasilkan) }} m³</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="detail-item">
                                <span class="detail-label">Vol. Dialirkan</span>
                                <div class="detail-value value-success">{{ number_format($data->vol_limbah_dialirkan) }} m³</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Keterangan --}}
            @if($data->keterangan)
                <div class="card detail-card shadow-sm">
                    <div class="card-body p-4">
                        <div class="detail-section-title">
                            <i class="feather-message-square"></i> Keterangan
                        </div>
                        <div class="keterangan-box">
                            {{ $data->keterangan }}
                        </div>
                    </div>
                </div>
            @endif
        </div>

        {{-- Summary Card --}}
        <div class="col-lg-4">
            <div class="card detail-card shadow-sm bg-gradient-primary text-white" style="background: linear-gradient(135deg, #4338ca 0%, #6366f1 100%);">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center mb-3">
                        <div class="avatar avatar-lg rounded-circle bg-white bg-opacity-25" style="font-size: 24px;">
                            <i class="feather-gauge"></i>
                        </div>
                        <div class="ms-3">
                            <h6 class="mb-0 fw-bold">Ringkasan Data</h6>
                        </div>
                    </div>

                    <hr class="bg-white bg-opacity-25">

                    <div class="mb-3">
                        <small class="text-white text-opacity-75">Volume Dialirkan</small>
                        <h5 class="mb-0 fw-bold">{{ number_format($data->vol_limbah_dialirkan) }} m³</h5>
                    </div>

                    <div class="mb-3">
                        <small class="text-white text-opacity-75">Volume Dihasilkan</small>
                        <h5 class="mb-0 fw-bold">{{ number_format($data->vol_limbah_dihasilkan) }} m³</h5>
                    </div>

                    <div class="mb-3">
                        <small class="text-white text-opacity-75">Efisiensi Pengaliran</small>
                        <h5 class="mb-0 fw-bold">
                            @php
                                $efisiensi = $data->vol_limbah_dihasilkan > 0 ? round(($data->vol_limbah_dialirkan / $data->vol_limbah_dihasilkan) * 100) : 0;
                            @endphp
                            {{ $efisiensi }}%
                        </h5>
                    </div>

                    <div>
                        <small class="text-white text-opacity-75">Status</small>
                        <h5 class="mb-0 fw-bold">
                            @if($data->vol_limbah_dialirkan > 0)
                                <span class="badge bg-success">✓ Aktif</span>
                            @else
                                <span class="badge bg-warning">◐ Pending</span>
                            @endif
                        </h5>
                    </div>
                </div>
            </div>

            <div class="card detail-card shadow-sm mt-4">
                <div class="card-body p-4">
                    <h6 class="fw-bold mb-3" style="color: #4338ca;">
                        <i class="feather-calendar me-2"></i>
                        Informasi Waktu
                    </h6>
                    <div class="detail-item">
                        <span class="detail-label">Tanggal Pengaliran</span>
                        <div class="detail-value">{{ \Carbon\Carbon::parse($data->tanggal)->locale('id')->isoFormat('dddd, D MMMM Y') }}</div>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Durasi Operasional</span>
                        <div class="detail-value">{{ \Carbon\Carbon::parse($data->jam_mulai)->diff(\Carbon\Carbon::parse($data->jam_selesai))->format('%H jam %I menit') }}</div>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Jam Pengaliran</span>
                        <div class="detail-value">{{ $data->jam_mulai }} s/d {{ $data->jam_selesai }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('scripts')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.12/dist/sweetalert2.min.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.12/dist/sweetalert2.all.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.form-delete').forEach(function(form){
            form.addEventListener('submit', function(e){
                e.preventDefault();
                Swal.fire({
                    title: 'Konfirmasi Hapus',
                    text: 'Yakin ingin menghapus data pengaliran ini? Aksi ini tidak dapat dibatalkan.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: 'Ya, Hapus',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });
    });
</script>
@endsection