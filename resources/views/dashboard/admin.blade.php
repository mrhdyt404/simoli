@extends('layouts.simoli')

@section('title', 'Dashboard Admin')

@section('styles')
    <style>
        .welcome-card {
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
            border: none;
            border-radius: 16px;
            overflow: hidden;
            position: relative;
        }

        .welcome-card::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -20%;
            width: 300px;
            height: 300px;
            background: rgba(255, 255, 255, 0.08);
            border-radius: 50%;
        }

        .welcome-card::after {
            content: '';
            position: absolute;
            bottom: -30%;
            right: 10%;
            width: 200px;
            height: 200px;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 50%;
        }

        .stat-icon {
            width: 52px;
            height: 52px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
        }

        .stat-icon.bg-primary-soft {
            background: rgba(79, 70, 229, 0.12);
            color: #4f46e5;
        }

        .stat-icon.bg-success-soft {
            background: rgba(16, 185, 129, 0.12);
            color: #10b981;
        }

        .stat-icon.bg-warning-soft {
            background: rgba(245, 158, 11, 0.12);
            color: #f59e0b;
        }

        .stat-icon.bg-danger-soft {
            background: rgba(239, 68, 68, 0.12);
            color: #ef4444;
        }

        .monitoring-badge {
            font-size: 12px;
            padding: 5px 12px;
            border-radius: 20px;
            font-weight: 600;
        }

        .activity-timeline {
            position: relative;
            padding-left: 28px;
        }

        .activity-timeline::before {
            content: '';
            position: absolute;
            left: 8px;
            top: 0;
            bottom: 0;
            width: 2px;
            background: #e5e7eb;
        }

        .activity-dot {
            width: 18px;
            height: 18px;
            border-radius: 50%;
            position: absolute;
            left: 0;
            border: 3px solid #fff;
            box-shadow: 0 0 0 2px #e5e7eb;
        }

        .activity-dot.success {
            background: #10b981;
            box-shadow: 0 0 0 2px #10b981;
        }

        .activity-dot.warning {
            background: #f59e0b;
            box-shadow: 0 0 0 2px #f59e0b;
        }

        .filter-form .form-control {
            border-radius: 10px;
            border: 1px solid #e0e3e8;
        }

        .filter-form .btn {
            border-radius: 10px;
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

        .pks-rank-table th {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-weight: 700;
        }

        .pks-rank-table td {
            font-size: 12px;
            white-space: nowrap;
        }

        .theme-adaptive-text {
            color: #111827;
        }

        .theme-adaptive-subtext {
            color: #374151;
        }

        .app-skin-dark .theme-adaptive-text,
        .app-skin-dark .theme-adaptive-subtext {
            color: #ffffff;
        }

        .total-row-adaptive {
            background: #f0f3ff;
            color: #111827;
        }

        .app-skin-dark .total-row-adaptive {
            background: #1f2937;
            color: #f9fafb;
        }

        .quick-report-card {
            border: 1px solid #eef2ff;
            border-radius: 16px;
            transition: transform 0.2s ease, box-shadow 0.2s ease, border-color 0.2s ease;
            overflow: hidden;
        }

        .quick-report-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 28px rgba(15, 23, 42, 0.08);
            border-color: #c7d2fe;
        }

        .quick-report-card .report-icon {
            width: 52px;
            height: 52px;
            border-radius: 14px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            flex-shrink: 0;
        }

        .report-meta {
            font-size: 11px;
            line-height: 1.5;
            color: #6b7280;
            margin-bottom: 0;
        }

        .summary-note-list {
            list-style: none;
            padding: 0;
            margin: 0;
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 12px;
        }

        .summary-note-list li {
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            gap: 6px;
            padding: 14px 16px;
            border: 1px solid #e5e7eb;
            border-radius: 14px;
            background: #f8fafc;
            min-height: 84px;
        }

        .summary-note-list .label {
            font-size: 12px;
            color: #6b7280;
        }

        .summary-note-list .value {
            font-size: 15px;
            font-weight: 800;
            color: #111827;
        }

        @media (max-width: 575.98px) {
            .summary-note-list {
                grid-template-columns: 1fr;
            }
        }

        .report-hub-card {
            border: 1px solid #e5e7eb;
            border-radius: 18px;
            background: #ffffff;
            transition: .3s;
        }

        .report-hub-copy h5 {
            font-size: 20px;
            font-weight: 700;
            color: #1f2937;
            margin-bottom: 6px;
        }

        .report-hub-copy p {
            font-size: 14px;
            color: #6b7280;
            margin-bottom: 0;
            line-height: 1.7;
        }

        .report-filter-chip {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 14px;
            border-radius: 50px;
            background: #eef2ff;
            color: #4338ca;
            font-size: 12px;
            font-weight: 600;
        }

        .report-filter-chip.warning {
            background: #fff7ed;
            color: #ea580c;
        }

        .report-filter-chip.info {
            background: #ecfeff;
            color: #0891b2;
        }

        .report-filter-chip.success {
            background: #ecfdf5;
            color: #047857;
        }

        .report-filter-chip.warning {
            background: #fffbeb;
            color: #b45309;
        }

        .report-filter-chip.info {
            background: #ecfeff;
            color: #0e7490;
        }

        .status-alert {
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 700;
        }

        .status-alert.safe {
            background: #dcfce7;
            color: #15803d;
        }

        .status-alert.warning {
            background: #fef3c7;
            color: #b45309;
        }

        .status-alert.danger {
            background: #fee2e2;
            color: #dc2626;
        }


        .monitor-icon {
            width: 38px;
            height: 38px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
        }

        .report-hint {
            font-size: 11px;
            color: #6b7280;
            margin-top: 10px;
            margin-bottom: 0;
        }

        /* =========================
                                                                                                   DARK MODE
                                                                                                ========================= */

        .app-skin-dark .report-hub-card {
            background: #1b2431;
            border-color: #344054;
        }

        .app-skin-dark .report-hub-copy h5 {
            color: #ffffff;
        }

        .app-skin-dark .report-hub-copy p {
            color: #cbd5e1;
        }

        .app-skin-dark .report-filter-chip {
            background: #293445;
            color: #e2e8f0;
        }

        .app-skin-dark .report-filter-chip.warning {
            background: #473519;
            color: #fbbf24;
        }

        .app-skin-dark .report-filter-chip.info {
            background: #123847;
            color: #67e8f9;
        }

        .app-skin-dark .report-filter-chip.success {
            background: #163d34;
            color: #6ee7b7;
        }

        .app-skin-dark .btn-light-brand {
            background: #293445;
            color: #ffffff;
            border: 1px solid #3d4d63;
        }

        .app-skin-dark .btn-light-brand:hover {
            background: #2563eb;
            border-color: #2563eb;
            color: #fff;
        }
    </style>
@endsection

@section('page-title', 'Dashboard')

@section('breadcrumb')
    <li class="breadcrumb-item">Admin</li>
@endsection

@section('page-actions')
    <div class="page-header-right-items">
        <div class="d-flex d-md-none">
            <a href="javascript:void(0)" class="page-header-right-close-toggle">
                <i class="feather-arrow-left me-2"></i><span>Back</span>
            </a>
        </div>
        <div class="d-flex align-items-center gap-2 page-header-right-items-wrapper">
            <form action="{{ route('dashboard') }}" method="GET" class="d-flex align-items-center gap-2 filter-form">
                <input type="date" name="tanggal" class="form-control" value="{{ $tanggal }}" style="width: 170px">
                <button type="submit" class="btn btn-primary"><i class="feather-search me-1"></i>Filter</button>
                <a href="{{ route('dashboard') }}" class="btn btn-light-brand"><i class="feather-refresh-cw"></i></a>
            </form>
        </div>
    </div>
    <div class="d-md-none d-flex align-items-center">
        <a href="javascript:void(0)" class="page-header-right-open-toggle"><i class="feather-align-right fs-20"></i></a>
    </div>
@endsection

@php
    $selectedDate = \Carbon\Carbon::parse($tanggal);
    $pctPengaliran = $totalPks > 0 ? round(($sudahPengaliran / $totalPks) * 100) : 0;
    $pctPemeliharaan = $totalPks > 0 ? round(($sudahPemeliharaan / $totalPks) * 100) : 0;
    $pctLengkap = $totalPks > 0 ? round(($sudahLengkap / $totalPks) * 100) : 0;
    $belumKirim = $totalPks - $sudahLengkap;
    $pctBelum = $totalPks > 0 ? round(($belumKirim / $totalPks) * 100) : 0;
    $bulanLabel = $selectedDate->translatedFormat('F Y');
    $filterBulan = $selectedDate->format('m');
    $filterTahun = $selectedDate->format('Y');
@endphp

@php
    $rencanaMap = collect($monitoringRencana)->mapWithKeys(function ($item) {
        return [
            $item['pks']->ID => $item['rencana']
        ];
    });
@endphp

@section('content')
    <div class="dashboard-admin">
        <div class="row g-4">
            {{-- ================= MONITORING SUMMARY ================= --}}
            <div class="col-12">
                <div class="card border-0 shadow-sm" style="border-radius:18px;">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center flex-wrap mb-4">
                            <div>
                                <h4 class="fw-bold mb-1">
                                    Halo, {{ Auth::user()->pks->nama }}
                                </h4>
                                <p class="text-muted mb-0 fs-13">
                                    Monitoring seluruh PKS
                                    <strong>
                                        {{ \Carbon\Carbon::parse($tanggal)->locale('id')->translatedFormat('l, d F Y') }}
                                    </strong>
                                </p>
                            </div>
                            <div>
                                @if($sudahLengkap == $totalPks)
                                    <span class="badge bg-success px-3 py-2">
                                        <i class="feather-check-circle me-1"></i>
                                        Semua PKS Lengkap
                                    </span>
                                @else
                                    <span class="badge bg-warning px-3 py-2">
                                        <i class="feather-alert-circle me-1"></i>
                                        Ada Monitoring Belum Selesai
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="row g-3">

                            {{-- TOTAL --}}
                            <div class="col-md-3 col-6">
                                <div class="p-3 rounded-3 bg-light text-center">
                                    <i class="feather-home fs-22 text-primary"></i>
                                    <h3 class="fw-bold mb-0 text-primary">
                                        {{ $totalPks }}
                                    </h3>
                                    <small class="text-muted">
                                        Total PKS
                                    </small>
                                </div>
                            </div>

                            {{-- LENGKAP --}}
                            <div class="col-md-3 col-6">
                                <div class="p-3 rounded-3 bg-soft-success text-center">
                                    <i class="feather-check-circle fs-22 text-success"></i>
                                    <h3 class="fw-bold mb-0 text-success">
                                        {{ $sudahLengkap }}
                                    </h3>
                                    <small class="text-muted">
                                        PKS Lengkap
                                    </small>
                                </div>
                            </div>

                            {{-- SEBAGIAN --}}
                            <div class="col-md-3 col-6">
                                <div class="p-3 rounded-3 bg-soft-warning text-center">
                                    <i class="feather-clock fs-22 text-warning"></i>
                                    <h3 class="fw-bold mb-0 text-warning">
                                        {{ $sebagian }}
                                    </h3>
                                    <small class="text-muted">
                                        Sebagian
                                    </small>
                                </div>
                            </div>

                            {{-- BELUM --}}
                            <div class="col-md-3 col-6">
                                <div class="p-3 rounded-3 bg-soft-danger text-center">
                                    <i class="feather-x-circle fs-22 text-danger"></i>
                                    <h3 class="fw-bold mb-0 text-danger">
                                        {{ $belumAda }}
                                    </h3>
                                    <small class="text-muted">
                                        Belum Ada Data
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ================= REPORT HUB ================= --}}
            <div class="col-12">
                <div class="card report-hub-card">
                    <div class="card-body p-4">

                        <div class="row align-items-center g-4">

                            <div class="col-lg-7">
                                <div class="report-hub-copy">
                                    <h5>
                                        <i class="feather-book-open text-success me-2"></i>
                                        Seluruh Laporan
                                    </h5>

                                    <p>
                                        Pusat akses seluruh laporan monitoring SIMOLI.
                                        Pilih jenis laporan yang ingin ditampilkan sesuai
                                        periode monitoring.
                                    </p>

                                    <div class="d-flex flex-wrap gap-2 mt-3">
                                        <span class="report-filter-chip">
                                            <i class="feather-home"></i> PKS
                                        </span>

                                        <span class="report-filter-chip warning">
                                            <i class="feather-calendar"></i> Bulan
                                        </span>

                                        <span class="report-filter-chip info">
                                            <i class="feather-flag"></i> Tahun
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-5">
                                <div class="row g-2">

                                    <div class="col-12">
                                        <a href="{{ route('report-pengaliran', ['bulan' => $filterBulan, 'tahun' => $filterTahun]) }}"
                                            class="btn btn-light-brand w-100 py-2">
                                            <i class="feather-droplet me-2"></i>
                                            Laporan Pengaliran
                                        </a>
                                    </div>

                                    <div class="col-12">
                                        <a href="{{ route('report-pemeliharaan', ['bulan' => $filterBulan, 'tahun' => $filterTahun]) }}"
                                            class="btn btn-light-brand w-100 py-2">
                                            <i class="feather-tool me-2"></i>
                                            Laporan Pemeliharaan
                                        </a>
                                    </div>

                                    <div class="col-12">
                                        <a href="{{ route('report-rencana', ['tahun' => $filterTahun]) }}"
                                            class="btn btn-light-brand w-100 py-2">
                                            <i class="feather-clipboard me-2"></i>
                                            Laporan Rencana
                                        </a>
                                    </div>

                                </div>
                            </div>

                        </div>

                    </div>
                </div>
            </div>

            {{-- ================= MONITORING PKS ================= --}}
            <div class="col-12">
                <div class="card stretch stretch-full">

                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">
                            <i class="feather-monitor text-primary me-2"></i>
                            Monitoring Seluruh PKS
                        </h5>

                        <span class="badge bg-soft-primary text-primary">
                            {{ $selectedDate->translatedFormat('d F Y') }}
                        </span>
                    </div>


                    <div class="card-body p-0">
                        <div class="table-responsive">

                            <table class="table table-hover mb-0">

                                <thead>
                                    <tr>
                                        <th class="ps-4">PKS</th>
                                        <th>Monitoring</th>
                                        <th class="text-center">Kelengkapan</th>
                                        <th class="text-center pe-4">Status</th>
                                    </tr>
                                </thead>


                                <tbody>

                                    @foreach($monitoringData as $item)

                                        @php
                                            $rencanaAda = $rencanaMap[$item['pks']->ID] ?? false;

                                            $jumlahSelesai =
                                                ($item['pengaliran'] ? 1 : 0) +
                                                ($item['pemeliharaan'] ? 1 : 0) +
                                                ($rencanaAda ? 1 : 0);


                                            if ($jumlahSelesai == 3) {
                                                $status = 'Aman';
                                                $statusClass = 'safe';
                                                $icon = 'check-circle';

                                            } elseif ($jumlahSelesai > 0) {
                                                $status = 'Sebagian';
                                                $statusClass = 'warning';
                                                $icon = 'alert-circle';

                                            } else {
                                                $status = 'Belum Input';
                                                $statusClass = 'danger';
                                                $icon = 'x-circle';
                                            }
                                        @endphp


                                        <tr>

                                            {{-- PKS --}}
                                            <td class="ps-4">

                                                <div class="d-flex align-items-center gap-3">

                                                    <div class="
                                                            monitor-icon
                                                            @if($statusClass == 'safe')
                                                                bg-soft-success text-success
                                                            @elseif($statusClass == 'warning')
                                                                bg-soft-warning text-warning
                                                            @else
                                                                bg-soft-danger text-danger
                                                            @endif
                                                        ">
                                                        {{ strtoupper(substr($item['pks']->akro, 0, 2)) }}
                                                    </div>


                                                    <div>
                                                        <div class="fw-semibold">
                                                            {{ $item['pks']->nama }}
                                                        </div>

                                                        <small class="text-muted">
                                                            {{ $item['pks']->akro }}
                                                        </small>
                                                    </div>

                                                </div>

                                            </td>



                                            {{-- Monitoring --}}
                                            <td>

                                                <div class="d-flex flex-wrap gap-2">

                                                    <span
                                                        class="badge {{ $item['pengaliran'] ? 'bg-soft-success text-success' : 'bg-soft-danger text-danger' }}">
                                                        <i class="feather-{{ $item['pengaliran'] ? 'check' : 'x' }} me-1"></i>
                                                        Pengaliran
                                                    </span>


                                                    <span
                                                        class="badge {{ $item['pemeliharaan'] ? 'bg-soft-success text-success' : 'bg-soft-danger text-danger' }}">
                                                        <i class="feather-{{ $item['pemeliharaan'] ? 'check' : 'x' }} me-1"></i>
                                                        Pemeliharaan
                                                    </span>


                                                    <span
                                                        class="badge {{ $rencanaAda ? 'bg-soft-success text-success' : 'bg-soft-danger text-danger' }}">
                                                        <i class="feather-{{ $rencanaAda ? 'check' : 'x' }} me-1"></i>
                                                        Rencana
                                                    </span>

                                                </div>

                                            </td>



                                            {{-- Kelengkapan --}}
                                            <td class="text-center">

                                                <div class="fw-bold fs-16">
                                                    {{ $jumlahSelesai }}/3
                                                </div>


                                                <div class="progress mt-2" style="height:6px;width:80px;margin:auto">

                                                    <div class="progress-bar 
                                                            @if($jumlahSelesai == 3)
                                                                bg-success
                                                            @elseif($jumlahSelesai > 0)
                                                                bg-warning
                                                            @else
                                                                bg-danger
                                                            @endif" style="width: {{ ($jumlahSelesai / 3) * 100 }}%">
                                                    </div>

                                                </div>

                                            </td>



                                            {{-- Status --}}
                                            <td class="text-center pe-4">

                                                <span class="status-alert {{ $statusClass }}">
                                                    <i class="feather-{{ $icon }} me-1"></i>
                                                    {{ $status }}
                                                </span>


                                                <div class="fs-11 text-muted mt-1">

                                                    @if($statusClass == 'safe')
                                                        Semua monitoring lengkap

                                                    @elseif($statusClass == 'warning')
                                                        Ada monitoring belum selesai

                                                    @else
                                                        Belum ada data monitoring

                                                    @endif

                                                </div>

                                            </td>

                                        </tr>

                                    @endforeach

                                </tbody>

                            </table>

                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="{{ asset('duraluxadmin/assets/vendors/js/apexcharts.min.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var isDark = document.documentElement.classList.contains('app-skin-dark');
            var gridColor = isDark ? '#1e293b' : '#f1f1f1';
            var labelColor = isDark ? '#94a3b8' : '#9ca3af';
            var bgCard = isDark ? '#0f172a' : '#fff';

            // Donut Chart
            new ApexCharts(document.querySelector("#statusDonutChart"), {
                series: [{{ $sudahLengkap }}, {{ $sebagian }}, {{ $belumAda }}],
                chart: { type: 'donut', height: 220 },
                labels: ['Lengkap', 'Sebagian', 'Belum Ada'],
                colors: ['#10b981', '#f59e0b', '#ef4444'],
                plotOptions: { pie: { donut: { size: '70%', labels: { show: true, name: { show: true, fontSize: '13px' }, value: { show: true, fontSize: '20px', fontWeight: 700 }, total: { show: true, label: 'Total PKS', fontSize: '12px', formatter: function (w) { return {{ $totalPks }}; } } } } } },
                legend: { position: 'bottom', fontSize: '12px', fontFamily: 'inherit', labels: { colors: labelColor } },
                dataLabels: { enabled: false },
                stroke: { width: 2, colors: [bgCard] },
                responsive: [{ breakpoint: 480, options: { chart: { height: 200 } } }]
            }).render();

            // Trend Chart
            new ApexCharts(document.querySelector("#trendChart"), {
                series: [{ name: 'Pengaliran', data: {!! json_encode(array_column($trendData, 'pengaliran')) !!} }, { name: 'Pemeliharaan', data: {!! json_encode(array_column($trendData, 'pemeliharaan')) !!} }],
                chart: { type: 'area', height: 300, toolbar: { show: false }, fontFamily: 'inherit' },
                colors: ['#10b981', '#f59e0b'],
                fill: { type: 'gradient', gradient: { shadeIntensity: 1, opacityFrom: 0.4, opacityTo: 0.05, stops: [0, 90, 100] } },
                stroke: { curve: 'smooth', width: 2.5 },
                xaxis: { categories: {!! json_encode(array_column($trendData, 'label')) !!}, labels: { style: { fontSize: '11px', colors: labelColor } }, axisBorder: { show: false }, axisTicks: { show: false } },
                yaxis: { max: {{ $totalPks + 1 }}, labels: { style: { fontSize: '11px', colors: labelColor }, formatter: function (val) { return Math.round(val); } } },
                grid: { borderColor: gridColor, strokeDashArray: 4 },
                legend: { fontSize: '12px', fontFamily: 'inherit', labels: { colors: labelColor } },
                dataLabels: { enabled: false },
                tooltip: { y: { formatter: function (val) { return val + ' PKS'; } } },
                markers: { size: 4, strokeWidth: 0 }
            }).render();

            // Volume Dialirkan Per PKS
            var pksLabels = {!! json_encode(collect($statsPerPks)->pluck('pks.AKRO')->values()) !!};
            var volData = {!! json_encode(collect($statsPerPks)->pluck('vol_dialirkan')->values()) !!};

            new ApexCharts(document.querySelector("#volPerPksChart"), {
                series: [{ name: 'Vol. Dialirkan (m³)', data: volData }],
                chart: { type: 'bar', height: 320, toolbar: { show: false }, fontFamily: 'inherit' },
                colors: ['#10b981'],
                plotOptions: { bar: { borderRadius: 4, columnWidth: '55%', dataLabels: { position: 'top' } } },
                dataLabels: { enabled: true, formatter: function (val) { return val > 0 ? val.toLocaleString() : ''; }, offsetY: -20, style: { fontSize: '10px', colors: [labelColor] } },
                xaxis: { categories: pksLabels, labels: { style: { fontSize: '10px', colors: labelColor }, rotate: -45, rotateAlways: pksLabels.length > 6 } },
                yaxis: { labels: { style: { fontSize: '10px', colors: labelColor }, formatter: function (val) { return val.toLocaleString(); } } },
                grid: { borderColor: gridColor, strokeDashArray: 4 },
                tooltip: { y: { formatter: function (val) { return val.toLocaleString() + ' m³'; } } }
            }).render();

            // Pemeliharaan Per PKS - Stacked Bar
            new ApexCharts(document.querySelector("#maintPerPksChart"), {
                series: [
                    { name: 'Flat Bed', data: {!! json_encode(collect($statsPerPks)->pluck('flat_bed_m')->values()) !!} },
                    { name: 'Long Bed', data: {!! json_encode(collect($statsPerPks)->pluck('long_bed')->values()) !!} }
                ],
                chart: { type: 'bar', height: 320, stacked: true, toolbar: { show: false }, fontFamily: 'inherit' },
                colors: ['#f59e0b', '#6366f1'],
                plotOptions: { bar: { borderRadius: 4, columnWidth: '55%' } },
                xaxis: { categories: pksLabels, labels: { style: { fontSize: '10px', colors: labelColor }, rotate: -45, rotateAlways: pksLabels.length > 6 } },
                yaxis: { labels: { style: { fontSize: '10px', colors: labelColor }, formatter: function (val) { return Math.round(val); } } },
                grid: { borderColor: gridColor, strokeDashArray: 4 },
                legend: { fontSize: '12px', fontFamily: 'inherit', labels: { colors: labelColor } },
                dataLabels: { enabled: false },
                tooltip: { y: { formatter: function (val) { return Math.round(val); } } }
            }).render();
        });
    </script>
@endsection