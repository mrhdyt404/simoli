@extends('layouts.simoli')
@section('title', 'Dashboard')
@php
    $namaBulan = [
        1 => 'Januari',
        2 => 'Februari',
        3 => 'Maret',
        4 => 'April',
        5 => 'Mei',
        6 => 'Juni',
        7 => 'Juli',
        8 => 'Agustus',
        9 => 'September',
        10 => 'Oktober',
        11 => 'November',
        12 => 'Desember',
    ];
@endphp

@section('styles')
    <style>
        .status-card {
            border-radius: 16px;
            border: none;
            transition: transform 0.2s;
        }

        .status-card:hover {
            transform: translateY(-2px);
        }

        .status-card.success {
            background: linear-gradient(135deg, #ecfdf5 0%, #d1fae5 100%);
            border-left: 4px solid #10b981;
        }

        .status-card.danger {
            background: linear-gradient(135deg, #fef2f2 0%, #fecaca 100%);
            border-left: 4px solid #ef4444;
        }

        .status-icon {
            width: 56px;
            height: 56px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
        }

        .quick-action {
            border-radius: 12px;
            padding: 16px;
            text-align: center;
            transition: all 0.2s;
            border: 2px dashed #e5e7eb;
            text-decoration: none;
            display: block;
        }

        .quick-action:hover {
            border-color: #4f46e5;
            background: #f5f3ff;
            transform: translateY(-2px);
        }

        .quick-action i {
            font-size: 28px;
            color: #4f46e5;
            display: block;
            margin-bottom: 8px;
        }

        .quick-action span {
            font-size: 12px;
            font-weight: 600;
            color: #374151;
        }

        .month-progress {
            background: #f9fafb;
            border-radius: 12px;
            padding: 16px;
        }

        .section-divider {
            font-size: 16px;
            font-weight: 700;
            margin-bottom: 0;
            margin-top: 8px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .section-divider i {
            font-size: 20px;
        }

        .stat-mini {
            text-align: center;
            padding: 16px 8px;
            border-radius: 12px;
            transition: transform 0.2s;
        }

        .bg-soft-success {
            background: #ecfdf5;
        }

        .bg-soft-warning {
            background: #fffbeb;
        }

        .bg-soft-primary {
            background: #eff6ff;
        }

        .bg-soft-info {
            background: #ecfeff;
        }

        .bg-soft-danger {
            background: #fef2f2;
        }

        .stat-mini:hover {
            transform: translateY(-2px);
        }

        .stat-mini .stat-mini-value {
            font-size: 22px;
            font-weight: 800;
            line-height: 1.2;
        }

        .stat-mini .stat-mini-label {
            font-size: 11px;
            font-weight: 600;
            color: #6b7280;
            margin-top: 4px;
        }

        .stat-mini .stat-mini-icon {
            font-size: 28px;
            margin-bottom: 6px;
        }

        .timeline .avatar-text {
            width: 40px;
            height: 40px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .progress.ht-8 {
            height: 8px;
            border-radius: 10px;
        }
    </style>
@endsection

@section('page-title', 'Dashboard')

@section('breadcrumb')
    <li class="breadcrumb-item">{{ Auth::user()->pks->akro }}</li>
@endsection

@section('content')
    @php
        $isComplete = $hasPengaliran && $hasPemeliharaan;
        $pctMonthPengaliran = $daysInMonth > 0 ? round(($monthlyPengaliran / $daysInMonth) * 100) : 0;
        $pctMonthPemeliharaan = $daysInMonth > 0 ? round(($monthlyPemeliharaan / $daysInMonth) * 100) : 0;
    @endphp

    <div class="row">
        {{-- Monitoring Hari Ini --}}
        <div class="col-12 mb-4">
            <div class="card border-0 shadow-sm" style="border-radius:16px;">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center flex-wrap mb-3">
                        <div>
                            <h3 class="fw-bold mb-1">
                                Halo, {{ Auth::user()->pks->nama }}
                            </h3>
                            <span class="text-muted">
                                {{ \Carbon\Carbon::parse($tanggal)->locale('id')->translatedFormat('l, d F Y') }}
                            </span>
                        </div>
                        <div>
                            <span class="badge bg-{{ $simoliColor }} fs-13 px-3 py-2">
                                <i class="feather-{{ $simoliIcon }} me-1"></i>
                                {{ $simoliStatus }}
                            </span>
                        </div>
                    </div>

                    {{-- STATUS SIMOLI COMPACT --}}
                    <div class="alert alert-{{ $simoliColor }} d-flex align-items-center py-2 px-3 mb-4">
                        <i class="feather-{{ $simoliIcon }} me-2 fs-18"></i>
                        <span class="fs-13">
                            {{ $simoliMessage }}
                        </span>
                    </div>

                    <div class="row">
                        {{-- Pengaliran --}}
                        <div class="col-md-4">
                            <div class="border rounded p-3 h-100">
                                <div class="d-flex justify-content-between">
                                    <strong>Pengaliran</strong>
                                    @if($hasPengaliran)
                                        <i class="feather-check-circle text-success"></i>
                                    @else
                                        <i class="feather-alert-circle text-danger"></i>
                                    @endif
                                </div>
                                <small class="text-muted">
                                    @if($hasPengaliran)
                                        Sudah diinput
                                    @else
                                        Belum diinput
                                    @endif
                                </small>
                                <div class="mt-2">
                                    @if(!$hasPengaliran)
                                        <a href="{{ route('pengaliran.create') }}" class="btn btn-sm btn-danger">
                                            Input Sekarang
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>

                        {{-- Pemeliharaan --}}
                        <div class="col-md-4">
                            <div class="border rounded p-3 h-100">
                                <div class="d-flex justify-content-between">
                                    <strong>Pemeliharaan</strong>
                                    @if($hasPemeliharaan)
                                        <i class="feather-check-circle text-success"></i>
                                    @else
                                        <i class="feather-alert-circle text-danger"></i>
                                    @endif
                                </div>
                                <small class="text-muted">
                                    @if($hasPemeliharaan)
                                        Sudah diinput
                                    @else
                                        Belum diinput
                                    @endif
                                </small>
                                <div class="mt-2">
                                    @if(!$hasPemeliharaan)
                                        <a href="{{ route('pemeliharaan.create') }}" class="btn btn-sm btn-danger">
                                            Input Sekarang
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>

                        {{-- Rencana --}}
                        <div class="col-md-4">
                            <div class="border rounded p-3 h-100">
                                <div class="d-flex justify-content-between">
                                    <strong>Rencana</strong>
                                    <i class="feather-calendar text-primary"></i>
                                </div>
                                <small class="text-muted">
                                    Lihat atau kelola rencana tahunan.
                                </small>
                                <div class="mt-2">
                                    <a href="{{ route('rencana.index') }}" class="btn btn-sm btn-primary">
                                        Lihat Rencana
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <hr class="my-4">
                    <h6 class="fw-bold mb-3">
                        <i class="feather-alert-triangle text-warning me-2"></i>
                        Yang Harus Dilakukan Hari Ini
                    </h6>
                    <ul class="list-group list-group-flush">
                        @if($simoliStatus == 'NORMAL')
                            <li class="list-group-item px-0 text-success">
                                <i class="feather-check-circle me-2"></i>
                                Seluruh pekerjaan monitoring hari ini telah selesai.
                            </li>
                        @elseif($simoliStatus == 'PERHATIAN')
                            @if(!$hasPengaliran)
                                <li class="list-group-item px-0 text-warning">
                                    <i class="feather-alert-circle me-2"></i>
                                    Lengkapi data Pengaliran hari ini.
                                </li>
                            @endif
                            @if(!$hasPemeliharaan)
                                <li class="list-group-item px-0 text-warning">
                                    <i class="feather-alert-circle me-2"></i>
                                    Lengkapi data Pemeliharaan hari ini.
                                </li>
                            @endif
                        @else
                            <li class="list-group-item px-0 text-danger">
                                <i class="feather-x-circle me-2"></i>
                                Belum ada data monitoring hari ini.
                            </li>
                        @endif
                    </ul>
                </div>
            </div>
        </div>

        <!-- Grafik Monitoring Bulanan -->
        <div class="row mt-4">

            <div class="col-lg-12">

                <div class="card stretch stretch-full">

                    <div class="card-header d-flex justify-content-between align-items-center">

                        <div>
                            <h5 class="card-title mb-1">
                                <i class="feather-droplet text-success me-2"></i>
                                Grafik Total Volume Pengaliran
                            </h5>

                            <div id="filterInfo" class="mt-1">
                                <span class="badge bg-soft-primary text-primary">
                                    {{ $namaBulan[(int) $bulan] }}
                                </span>

                                <span class="badge bg-soft-success text-success">
                                    {{ $jenis == 'blok' ? 'Blok' : 'Flat Bed' }}
                                </span>

                                <span class="badge bg-soft-secondary text-secondary">
                                    {{ $nilai }}
                                </span>
                            </div>

                        </div>

                        <form id="filterGrafik" class="d-flex gap-2">

                            {{-- Bulan --}}
                            <select name="bulan" id="bulan" class="form-select form-select-sm">

                                @for($i = 1; $i <= 12; $i++)
                                    <option value="{{ $i }}" {{ $bulan == $i ? 'selected' : '' }}>
                                        {{ \Carbon\Carbon::create()->month($i)->translatedFormat('F') }}
                                    </option>
                                @endfor

                            </select>

                            {{-- Jenis Filter --}}
                            <select name="jenis" id="jenis" class="form-select form-select-sm">

                                <option value="blok" {{ $jenis == 'blok' ? 'selected' : '' }}>
                                    Blok
                                </option>

                                <option value="flat_bed" {{ $jenis == 'flat_bed' ? 'selected' : '' }}>
                                    Nomor Bak
                                </option>

                            </select>

                            {{-- Nilai --}}
                            <select name="nilai" id="nilai" class="form-select form-select-sm">

                                <option value="">
                                    Semua {{ $jenis == 'blok' ? 'Blok' : 'Nomor Bak' }}
                                </option>

                                @foreach($pilihan as $item)

                                    <option value="{{ $item }}" {{ $nilai == $item ? 'selected' : '' }}>

                                        {{ $item }}

                                    </option>

                                @endforeach

                            </select>

                            <button class="btn btn-primary btn-sm">
                                <i class="feather-filter"></i>
                            </button>

                        </form>

                    </div>

                    <div class="card-body">

                        <div style="height:420px">

                            <canvas id="volumeChart"></canvas>

                        </div>

                    </div>

                </div>

            </div>

        </div>

        {{-- ============ RINGKASAN MONITORING ============ --}}
        <div class="col-12 mt-4">
            <h5 class="section-divider">
                <i class="feather-activity text-primary"></i>
                Ringkasan Monitoring
                {{ \Carbon\Carbon::parse($tanggal)->translatedFormat('F Y') }}
            </h5> <br>
        </div>

        {{-- CARD PENGALIRAN --}}
        <div class="col-md-6">
            <div class="card stretch stretch-full">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="feather-droplet text-success me-2"></i>
                        Statistik Pengaliran
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-6">
                            <div class="stat-mini bg-soft-success">
                                <div class="stat-mini-icon text-success">
                                    <i class="feather-database"></i>
                                </div>
                                <div class="stat-mini-value text-success">
                                    {{ number_format($statPengaliran['total_records']) }}
                                </div>
                                <div class="stat-mini-label">
                                    Total Record
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="stat-mini bg-soft-primary">
                                <div class="stat-mini-icon text-primary">
                                    <i class="feather-trending-up"></i>
                                </div>
                                <div class="stat-mini-value text-primary">
                                    {{ number_format($statPengaliran['vol_dihasilkan']) }}
                                </div>
                                <div class="stat-mini-label">
                                    Volume Dihasilkan (m³)
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="stat-mini bg-soft-info">
                                <div class="stat-mini-icon text-info">
                                    <i class="feather-droplet"></i>
                                </div>
                                <div class="stat-mini-value text-info">
                                    {{ number_format($statPengaliran['vol_dialirkan']) }}
                                </div>
                                <div class="stat-mini-label">
                                    Volume Dialirkan (m³)
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="stat-mini bg-soft-warning">
                                <div class="stat-mini-icon text-warning">
                                    <i class="feather-layers"></i>
                                </div>
                                <div class="stat-mini-value text-warning">
                                    {{ number_format($statPengaliran['total_flat_bed']) }}
                                </div>
                                <div class="stat-mini-label">
                                    Total Flat Bed
                                </div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="stat-mini bg-soft-danger">
                                <div class="stat-mini-icon text-danger">
                                    <i class="feather-map"></i>
                                </div>
                                <div class="stat-mini-value text-danger">
                                    {{ number_format($statPengaliran['total_luas_area'], 1) }}
                                </div>
                                <div class="stat-mini-label">
                                    Luas Area (Ha)
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- CARD PEMELIHARAAN --}}
        <div class="col-md-6">
            <div class="card stretch stretch-full">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="feather-tool text-warning me-2"></i>
                        Statistik Pemeliharaan
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        @php
                            $maintenanceStats = [
                                [
                                    'icon' => 'database',
                                    'value' => $statPemeliharaan['total_records'],
                                    'label' => 'Total Record',
                                    'color' => 'warning'
                                ],
                                [
                                    'icon' => 'layers',
                                    'value' => $statPemeliharaan['total_flat_bed'],
                                    'label' => 'Flat Bed',
                                    'color' => 'success'
                                ],
                                [
                                    'icon' => 'maximize-2',
                                    'value' => $statPemeliharaan['total_long_bed'],
                                    'label' => 'Long Bed',
                                    'color' => 'primary'
                                ],
                                [
                                    'icon' => 'users',
                                    'value' => $statPemeliharaan['total_hk'],
                                    'label' => 'Total HK',
                                    'color' => 'info'
                                ],
                                [
                                    'icon' => 'settings',
                                    'value' => $statPemeliharaan['total_mekanis'],
                                    'label' => 'Mekanis',
                                    'color' => 'primary'
                                ],
                                [
                                    'icon' => 'user',
                                    'value' => $statPemeliharaan['total_manual'],
                                    'label' => 'Manual',
                                    'color' => 'success'
                                ]
                            ];
                        @endphp
                        @foreach($maintenanceStats as $stat)
                            <div class="col-6">
                                <div class="stat-mini bg-soft-{{ $stat['color'] }}">
                                    <div class="stat-mini-icon text-{{ $stat['color'] }}">
                                        <i class="feather-{{ $stat['icon'] }}"></i>
                                    </div>
                                    <div class="stat-mini-value text-{{ $stat['color'] }}">
                                        {{ number_format($stat['value']) }}
                                    </div>
                                    <div class="stat-mini-label">
                                        {{ $stat['label'] }}
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        {{-- MONITORING BULAN BERJALAN --}}
        <div class="col-md-6">
            <div class="card stretch stretch-full">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="feather-calendar text-primary me-2"></i>
                        Monitoring Bulan Berjalan
                    </h5>
                </div>
                <div class="card-body">
                    <div class="month-progress mb-4">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="fw-semibold">
                                <i class="feather-droplet text-success me-2"></i>
                                Pengaliran
                            </span>
                            <span class="fw-bold">
                                {{ $pctMonthPengaliran }}%
                            </span>
                        </div>
                        <div class="progress ht-8">
                            <div class="progress-bar bg-success" style="width:{{ $pctMonthPengaliran }}%">
                            </div>
                        </div>
                        <small class="text-muted">
                            {{ $monthlyPengaliran }} dari {{ $daysInMonth }} hari
                        </small>
                    </div>
                    <div class="month-progress">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="fw-semibold">
                                <i class="feather-tool text-warning me-2"></i>
                                Pemeliharaan
                            </span>
                            <span class="fw-bold">
                                {{ $pctMonthPemeliharaan }}%
                            </span>
                        </div>
                        <div class="progress ht-8">
                            <div class="progress-bar bg-warning" style="width:{{ $pctMonthPemeliharaan }}%">
                            </div>
                        </div>
                        <small class="text-muted">
                            {{ $monthlyPemeliharaan }} dari {{ $daysInMonth }} hari
                        </small>
                    </div>
                    <hr>
                    @if($pctMonthPengaliran >= 80 && $pctMonthPemeliharaan >= 80)
                        <div class="alert alert-success mb-0">
                            <i class="feather-check-circle me-2"></i>
                            Monitoring bulan ini berjalan baik.
                        </div>
                    @else
                        <div class="alert alert-warning mb-0">
                            <i class="feather-alert-circle me-2"></i>
                            Monitoring bulan ini masih perlu perhatian.
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- AKTIVITAS TERAKHIR --}}
        <div class="col-md-6">
            <div class="card stretch stretch-full">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="feather-clock text-primary me-2"></i>
                        Aktivitas Terakhir
                    </h5>
                </div>

                <div class="card-body">
                    <div class="timeline">
                        @forelse($recentPengaliran->take(3) as $item)
                            <div class="d-flex mb-4">
                                <div class="me-3">
                                    <div class="avatar-text avatar-md bg-soft-success">
                                        <i class="feather-droplet text-success"></i>
                                    </div>
                                </div>
                                <div>
                                    <h6 class="mb-1">
                                        Pengaliran {{ $item->blok }}
                                    </h6>
                                    <small class="text-muted">
                                        {{ $item->tanggal->format('d M Y') }}-{{ number_format($item->vol_limbah_dialirkan ?? 0) }}
                                        m³
                                    </small>
                                </div>
                            </div>
                        @empty
                            <p class="text-muted">
                                Belum ada aktivitas pengaliran
                            </p>
                        @endforelse
                        @forelse($recentPemeliharaan->take(3) as $item)
                            <div class="d-flex mb-4">
                                <div class="me-3">
                                    <div class="avatar-text avatar-md bg-soft-warning">
                                        <i class="feather-tool text-warning"></i>
                                    </div>
                                </div>
                                <div>
                                    <h6 class="mb-1">
                                        Pemeliharaan {{ $item->blok }}
                                    </h6>
                                    <small class="text-muted">
                                        {{ $item->tanggal->format('d M Y') }}
                                        {{ $item->jenis_label }}
                                    </small>
                                </div>
                            </div>
                        @empty
                            <p class="text-muted">
                                Belum ada aktivitas pemeliharaan
                            </p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
@endsection

    @section('scripts')

        <script>
            const volumeCtx = document.getElementById('volumeChart');

            const volumeChart = new Chart(volumeCtx, {

                type: 'line',

                data: {

                    labels: @json($labelHari),

                    datasets: [{

                        label: 'Volume Limbah Dialirkan (m³)',

                        data: @json($volumeGrafik),

                        borderColor: '#2563eb',

                        backgroundColor: 'rgba(37,99,235,.15)',

                        fill: true,

                        tension: .4,

                        borderWidth: 3,

                        pointRadius: 4,

                        pointHoverRadius: 7

                    }]

                },

                options: {

                    responsive: true,

                    maintainAspectRatio: false,

                    interaction: {
                        mode: 'index',
                        intersect: false
                    },

                    plugins: {

                        legend: {
                            display: true
                        }

                    },

                    scales: {

                        x: {

                            title: {
                                display: true,
                                text: 'Tanggal'
                            }

                        },

                        y: {

                            beginAtZero: true,

                            title: {
                                display: true,
                                text: 'Volume (m³)'
                            }

                        }

                    }

                }

            });

            function loadGrafik() {

                fetch(
                    "{{ route('dashboard.grafik-volume') }}?bulan=" +
                    document.getElementById('bulan').value +
                    "&jenis=" +
                    document.getElementById('jenis').value +
                    "&nilai=" +
                    document.getElementById('nilai').value,
                    {
                        headers: {
                            "X-Requested-With": "XMLHttpRequest"
                        }
                    }
                )

                    .then(res => res.json())

                    .then(data => {

                        volumeChart.data.labels = data.labelHari;
                        volumeChart.data.datasets[0].data = data.volumeGrafik;
                        volumeChart.update();

                        const bulanText =
                            document.getElementById('bulan').options[
                                document.getElementById('bulan').selectedIndex
                            ].text;

                        const jenisText =
                            document.getElementById('jenis').value == 'blok'
                                ? 'Blok'
                                : 'Flat Bed';

                        const nilaiText =
                            document.getElementById('nilai').value;

                        document.getElementById('filterInfo').innerHTML =
                            `
            <span class="badge bg-soft-primary text-primary">
                ${bulanText}
            </span>

            <span class="badge bg-soft-success text-success">
                ${jenisText}
            </span>

            <span class="badge bg-soft-secondary text-secondary">
                ${nilaiText}
            </span>
            `;

                        let select = document.getElementById('nilai');

                        select.innerHTML = "";

                        data.pilihan.forEach(function (item) {

                            select.innerHTML +=
                                `<option value="${item}">${item}</option>`;

                        });

                    });

            }

            document.getElementById('bulan').addEventListener('change', loadGrafik);

            document.getElementById('jenis').addEventListener('change', loadGrafik);

            document.getElementById('nilai').addEventListener('change', loadGrafik);
        </script>

    @endsection