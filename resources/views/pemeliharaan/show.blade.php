@extends('layouts.simoli')

@section('title', 'Detail Pemeliharaan')
@section('page-title', 'Detail Pemeliharaan')

@section('breadcrumb')
<li class="breadcrumb-item">
    <a href="{{ route('pemeliharaan.index') }}">Pemeliharaan</a>
</li>
<li class="breadcrumb-item active">Detail</li>
@endsection

@section('page-actions')
    <div class="page-header-right-items">
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('pemeliharaan.index') }}" class="btn btn-light">
                <i class="feather-arrow-left me-2"></i>
                Kembali
            </a>

            <a href="{{ route('pemeliharaan.edit', $data) }}" class="btn btn-primary">
                <i class="feather-edit-2 me-2"></i>
                Edit
            </a>
                @if(Auth::user()->isAdmin())
                <form action="{{ route('pemeliharaan.destroy', $data) }}" method="POST" class="d-inline form-delete">
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
    html.app-skin-dark .detail-card {
        background-color: #1f2937;
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
    .badge-mekanis {
        background-color: #dbeafe;
        color: #0369a1;
    }
    html.app-skin-dark .badge-mekanis {
        background-color: #0c4a6e;
        color: #38bdf8;
    }
    .badge-manual {
        background-color: #fef08a;
        color: #92400e;
    }
    html.app-skin-dark .badge-manual {
        background-color: #542507;
        color: #fbbf24;
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
    .photo-card {
        overflow: hidden;
    }
    .photo-card img {
        width: 100%;
        height: auto;
        border-radius: 8px;
    }
    .card-header {
        background-color: #f8f9fa;
    }
    html.app-skin-dark .card-header {
        background-color: #111827;
        border-bottom-color: #374151;
    }
    .card-title {
        color: #1f2937;
    }
    html.app-skin-dark .card-title {
        color: #f3f4f6;
    }
    .text-muted {
        color: #6b7280 !important;
    }
    html.app-skin-dark .text-muted {
        color: #9ca3af !important;
    }
    .photo-header {
        background-color: #f8f9fa;
    }
    html.app-skin-dark .photo-header {
        background-color: #111827;
    }
    .photo-header h6 {
        color: #4338ca;
    }
    html.app-skin-dark .photo-header h6 {
        color: #818cf8;
    }
</style>
@endsection

@section('content')

<div class="row">

    {{-- Info Utama --}}
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
                            <div class="detail-value">
                                {{ $data->tanggal ? $data->tanggal->format('d F Y') : '-' }}
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="detail-item">
                            <span class="detail-label">PKS</span>
                            <div class="detail-value">
                                <span class="info-badge">{{ $data->pks->AKRO ?? '-' }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="detail-item">
                            <span class="detail-label">Jenis Pemeliharaan</span>
                            <div class="detail-value">
                                @if($data->jenis_pemeliharaan == 1)
                                    <span class="badge badge-mekanis fw-semibold">Mekanis</span>
                                @else
                                    <span class="badge badge-manual fw-semibold">Manual</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="detail-item">
                            <span class="detail-label">Jumlah HK</span>
                            <div class="detail-value">{{ $data->jumlah_hk ?? '-' }}</div>
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
                </div>
            </div>
        </div>

        {{-- Data Area --}}
        <div class="card detail-card shadow-sm mb-4">
            <div class="card-body p-4">
                <div class="detail-section-title">
                    <i class="feather-layers"></i> Area Pemeliharaan
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="detail-item">
                            <span class="detail-label">Flat Bed</span>
                            <div class="detail-value" style="color: #2563eb;">{{ number_format($data->flat_bed) }}</div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="detail-item">
                            <span class="detail-label">Long Bed</span>
                            <div class="detail-value" style="color: #059669;">{{ number_format($data->long_bed) }}</div>
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

    {{-- Sidebar: Foto + Summary --}}
    <div class="col-lg-4">
        {{-- Summary Card --}}
        <div class="card detail-card shadow-sm bg-gradient-primary text-white mb-4" style="background: linear-gradient(135deg, #4338ca 0%, #6366f1 100%);">
            <div class="card-body p-4">
                <div class="d-flex align-items-center mb-3">
                    <div class="avatar avatar-lg rounded-circle bg-white bg-opacity-25" style="font-size: 24px;">
                        <i class="feather-wrench"></i>
                    </div>
                    <div class="ms-3">
                        <h6 class="mb-0 fw-bold">Ringkasan</h6>
                    </div>
                </div>

                <hr class="bg-white bg-opacity-25">

                <div class="mb-3">
                    <small class="text-white text-opacity-75">Jenis</small>
                    <h5 class="mb-0 fw-bold">
                        @if($data->jenis_pemeliharaan == 1)
                            <span class="badge bg-info">Mekanis</span>
                        @else
                            <span class="badge bg-warning">Manual</span>
                        @endif
                    </h5>
                </div>

                <div class="mb-3">
                    <small class="text-white text-opacity-75">Total Area</small>
                    <h5 class="mb-0 fw-bold">{{ number_format($data->flat_bed + $data->long_bed) }} Unit</h5>
                </div>

                <div>
                    <small class="text-white text-opacity-75">Jumlah HK</small>
                    <h5 class="mb-0 fw-bold">{{ $data->jumlah_hk ?? '-' }} Orang</h5>
                </div>
            </div>
        </div>

        {{-- Foto Sebelum --}}
        <div class="card detail-card shadow-sm mb-4">
            <div class="card-header border-bottom photo-header">
                <h6 class="mb-0 fw-bold">
                    <i class="feather-image me-2"></i>
                    Foto Sebelum
                </h6>
            </div>

            <div class="card-body p-3 photo-card">
                @if($data->sebelum)
                    <img src="{{ asset('gallery/'.$data->sebelum) }}"
                         class="img-fluid rounded shadow-sm" alt="Foto Sebelum">
                @else
                    <div class="text-center text-muted py-5">
                        <i class="feather-image" style="font-size: 48px; opacity: 0.3;"></i>
                        <p class="mt-2">Tidak ada foto</p>
                    </div>
                @endif
            </div>
        </div>

        {{-- Foto Sesudah --}}
        <div class="card detail-card shadow-sm">
            <div class="card-header border-bottom photo-header">
                <h6 class="mb-0 fw-bold">
                    <i class="feather-image me-2"></i>
                    Foto Sesudah
                </h6>
            </div>

            <div class="card-body p-3 photo-card">
                @if($data->sesudah)
                    <img src="{{ asset('gallery/'.$data->sesudah) }}"
                         class="img-fluid rounded shadow-sm" alt="Foto Sesudah">
                @else
                    <div class="text-center text-muted py-5">
                        <i class="feather-image" style="font-size: 48px; opacity: 0.3;"></i>
                        <p class="mt-2">Tidak ada foto</p>
                    </div>
                @endif
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
                    text: 'Yakin ingin menghapus data pemeliharaan ini? Aksi ini tidak dapat dibatalkan.',
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