<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="x-ua-compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <title>SIMOLI || @yield('title', 'Dashboard')</title>
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('logo/Icon%20SIMOLI.png') }}" />
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('logo/Icon%20SIMOLI.png') }}" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="{{ asset('duraluxadmin/assets/css/bootstrap.min.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ asset('duraluxadmin/assets/vendors/css/vendors.min.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ asset('duraluxadmin/assets/vendors/css/daterangepicker.min.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ asset('duraluxadmin/assets/css/theme.min.css') }}" />
    @yield('styles')
    <style>
        /* ===== Safeguard for Pagination SVG Arrows & Icons ===== */
        .pagination svg,
        .page-item svg,
        nav[aria-label="Pagination Navigation"] svg,
        nav[role="navigation"] svg {
            max-width: 16px !important;
            max-height: 16px !important;
            width: 16px !important;
            height: 16px !important;
        }

        /* ===== Dark Mode Overrides for Custom SIMOLI Styles ===== */
        html.app-skin-dark .filter-card {
            border-color: #1b2436 !important;
            background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%) !important;
        }
        html.app-skin-dark .stat-card {
            border-color: #1b2436 !important;
        }
        html.app-skin-dark .table thead th {
            background: #1e293b !important;
            color: #94a3b8 !important;
        }
        html.app-skin-dark .table tbody td {
            border-color: #1b2436 !important;
        }
        html.app-skin-dark .table tbody tr:hover {
            background-color: rgba(99,102,241,0.08) !important;
        }
        html.app-skin-dark .card-header.bg-white,
        html.app-skin-dark .card-footer.bg-white {
            background: transparent !important;
        }
        html.app-skin-dark .volume-bar,
        html.app-skin-dark .bed-bar {
            background: #334155 !important;
        }
        html.app-skin-dark .photo-thumb {
            border-color: #334155 !important;
        }
        html.app-skin-dark .photo-thumb:hover {
            border-color: #818cf8 !important;
        }
        /* Dashboard admin */
        html.app-skin-dark .activity-timeline::before {
            background: #334155 !important;
        }
        html.app-skin-dark .activity-dot {
            border-color: #0f172a !important;
            box-shadow: 0 0 0 2px #334155 !important;
        }
        html.app-skin-dark .filter-form .form-control {
            border-color: #334155 !important;
        }
        /* Dashboard unit */
        html.app-skin-dark .status-card.success {
            background: linear-gradient(135deg, #064e3b, #065f46) !important;
        }
        html.app-skin-dark .status-card.success .status-icon {
            background: rgba(16,185,129,0.2) !important;
        }
        html.app-skin-dark .status-card.danger {
            background: linear-gradient(135deg, #7f1d1d, #991b1b) !important;
        }
        html.app-skin-dark .status-card.danger .status-icon {
            background: rgba(239,68,68,0.2) !important;
        }
        html.app-skin-dark .quick-action {
            border-color: #334155 !important;
            background: transparent !important;
        }
        html.app-skin-dark .quick-action:hover {
            background: rgba(99,102,241,0.12) !important;
            border-color: #6366f1 !important;
        }
        html.app-skin-dark .quick-action span {
            color: #cbd5e1 !important;
        }
        html.app-skin-dark .month-progress {
            background: #1e293b !important;
        }
        /* Disable Duralux's filter:invert(1) on our custom SVG logo */
        html.app-skin-dark .nxl-navigation .m-header .logo-lg,
        html.app-navigation-dark .nxl-navigation .m-header .logo-lg,
        html.app-skin-dark .nxl-navigation .m-header .logo-sm,
        html.app-navigation-dark .nxl-navigation .m-header .logo-sm {
            filter: none !important;
        }
        /* Logo SVG text colors — covers dark skin AND dark sidebar navigation */
        html.app-skin-dark .logo-title,
        html.app-navigation-dark .logo-title {
            fill: #e0e7ff !important;
        }
        html.app-skin-dark .logo-subtitle,
        html.app-navigation-dark .logo-subtitle {
            fill: #94a3b8 !important;
        }
        html.app-skin-dark .logo-accent,
        html.app-navigation-dark .logo-accent {
            stroke: #818cf8 !important;
            opacity: 0.5;
        }
        /* Empty state */
        html.app-skin-dark .empty-state i {
            color: #475569 !important;
        }

        /* ===== Pagination (Bootstrap 5 - Laravel) ===== */
        html.app-skin-dark .page-link {
            background-color: #121a2d !important;
            border-color: #1b2436 !important;
            color: #b1b4c0 !important;
        }
        html.app-skin-dark .page-link:hover {
            background-color: #1c2438 !important;
            border-color: #334155 !important;
            color: #fff !important;
        }
        html.app-skin-dark .page-item.active .page-link {
            background-color: #4f46e5 !important;
            border-color: #4f46e5 !important;
            color: #fff !important;
        }
        html.app-skin-dark .page-item.disabled .page-link {
            background-color: #0f172a !important;
            border-color: #1b2436 !important;
            color: #475569 !important;
            opacity: 0.6;
        }

        /* ===== Form Select Arrow (invisible on dark bg) ===== */
        html.app-skin-dark .form-select {
            background-color: #1e293b !important;
            color: #e2e8f0 !important;
            border-color: #334155 !important;
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%23b1b4c0' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='m2 5 6 6 6-6'/%3e%3c/svg%3e") !important;
        }
        html.app-skin-dark .form-select:focus {
            background-color: #1e293b !important;
            color: #e2e8f0 !important;
            border-color: #6366f1 !important;
            box-shadow: 0 0 0 0.25rem rgba(99, 102, 241, 0.25) !important;
        }
        html.app-skin-dark .form-select option {
            background-color: #0f172a !important;
            color: #e2e8f0 !important;
        }
        html.app-skin-dark .form-select option:checked {
            background: linear-gradient(#6366f1, #6366f1) !important;
            background-color: #6366f1 !important;
        }

        /* ===== Select2 Dark Mode ===== */
        html.app-skin-dark .select2-container--default .select2-selection--single {
            background-color: #1e293b !important;
            border-color: #334155 !important;
            color: #e2e8f0 !important;
        }
        html.app-skin-dark .select2-container--default .select2-selection--single .select2-selection__rendered {
            color: #e2e8f0 !important;
        }
        html.app-skin-dark .select2-container--default.select2-container--open .select2-selection--single {
            border-color: #6366f1 !important;
        }
        html.app-skin-dark .select2-container--default .select2-selection--single .select2-selection__arrow {
            color: #cbd5e1 !important;
        }
        html.app-skin-dark .select2-dropdown {
            background-color: #1e293b !important;
            border-color: #334155 !important;
        }
        html.app-skin-dark .select2-dropdown .select2-results__option {
            color: #e2e8f0 !important;
        }
        html.app-skin-dark .select2-dropdown .select2-results__option--highlighted {
            background-color: #6366f1 !important;
            color: #ffffff !important;
        }
        html.app-skin-dark .select2-dropdown .select2-results__option[aria-selected=true] {
            background-color: #4f46e5 !important;
            color: #ffffff !important;
        }

        /* ===== Form Section Title (create/edit forms) ===== */
        html.app-skin-dark .form-section-title {
            color: #818cf8 !important;
            border-bottom-color: #312e81 !important;
        }

        /* ===== Bed Info Boxes (rencana create/edit) ===== */
        html.app-skin-dark .bed-info {
            background: linear-gradient(135deg, #064e3b 0%, #065f46 100%) !important;
            border-color: #065f46 !important;
        }
        html.app-skin-dark .bed-info.long {
            background: linear-gradient(135deg, #422006 0%, #451a03 100%) !important;
            border-color: #92400e !important;
        }

        /* ===== Photo Upload Area (pemeliharaan create/edit) ===== */
        html.app-skin-dark .photo-upload-area {
            border-color: #334155 !important;
        }
        html.app-skin-dark .photo-upload-area:hover {
            border-color: #6366f1 !important;
            background-color: rgba(99,102,241,0.08) !important;
        }

        /* ===== Current Photo & Preview Border (edit forms) ===== */
        html.app-skin-dark .current-photo,
        html.app-skin-dark .photo-preview {
            border-color: #334155 !important;
        }

        /* ===== bg-light in dark mode ===== */
        html.app-skin-dark .bg-light {
            background-color: #1c2438 !important;
        }

        /* ===== Card Footer ===== */
        html.app-skin-dark .card-footer {
            background-color: transparent !important;
        }

        /* ===== Badge text-dark on warning badge ===== */
        html.app-skin-dark .badge.bg-warning.text-dark {
            color: #422006 !important;
        }

        /* ===== Brand Logo Sizing ===== */
        .nxl-navigation .m-header .b-brand {
            display: flex;
            align-items: center;
        }
        .nxl-navigation .m-header .logo-lg {
            width: auto;
            max-width: 420px;
            height: 112px;
            object-fit: contain;
        }
        .nxl-navigation .m-header .logo-sm {
            width: auto;
            max-width: 140px;
            height: 140px;
            object-fit: contain;
        }
        .nxl-navigation.nxl-navigation-mini .m-header .logo-lg {
            display: none;
        }
        .nxl-navigation.nxl-navigation-mini .m-header .logo-sm {
            display: inline-block;
        }
        @media (max-width: 575.98px) {
            .nxl-navigation .m-header .logo-lg {
                max-width: 340px;
                height: 92px;
            }
            .nxl-navigation .m-header .logo-sm {
                max-width: 110px;
                height: 110px;
            }
        }

        /* ===== Sidebar Toggle Position Inside Sidebar Header ===== */
        .nxl-navigation .m-header {
            display: flex !important;
            align-items: center !important;
            justify-content: space-between !important;
            padding: 0 16px 0 20px !important;
        }

        .nxl-navigation .m-header .b-brand {
            display: flex !important;
            align-items: center !important;
            flex-grow: 1;
            overflow: hidden;
        }

        .nxl-navigation .m-header .nxl-navigation-toggle {
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            flex-shrink: 0;
            margin-left: 8px;
        }

        .nxl-navigation .m-header .nxl-navigation-toggle a {
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            width: 36px;
            height: 36px;
            border-radius: 8px;
            color: #64748b;
            font-size: 20px;
            transition: all 0.2s ease;
            text-decoration: none;
        }

        .nxl-navigation .m-header .nxl-navigation-toggle a:hover {
            color: #1e293b;
            background-color: rgba(0, 0, 0, 0.06);
        }

        /* Dark mode styling for sidebar toggle button */
        html.app-skin-dark .nxl-navigation .m-header .nxl-navigation-toggle a,
        html.app-navigation-dark .nxl-navigation .m-header .nxl-navigation-toggle a {
            color: #94a3b8 !important;
        }

        html.app-skin-dark .nxl-navigation .m-header .nxl-navigation-toggle a:hover,
        html.app-navigation-dark .nxl-navigation .m-header .nxl-navigation-toggle a:hover {
            color: #ffffff !important;
            background-color: rgba(255, 255, 255, 0.12) !important;
        }

        /* Minimenu (collapsed sidebar) adjustments */
        html.minimenu .nxl-navigation .m-header {
            padding: 0 8px !important;
            justify-content: center !important;
        }

        html.minimenu .nxl-navigation .m-header .b-brand {
            display: none !important;
        }

        html.minimenu .nxl-navigation .m-header .nxl-navigation-toggle {
            margin-left: 0 !important;
        }

        /* ===== Global Print Mode Overrides for Dark Mode ===== */
        @media print {
            html,
            body,
            html.app-skin-dark,
            html.app-skin-dark body,
            html.app-skin-dark .nxl-container,
            html.app-skin-dark .nxl-content,
            html.app-skin-dark .main-content,
            html.app-skin-dark .print-only,
            html.app-skin-dark .card,
            html.app-skin-dark .bg-white,
            html.app-skin-dark div {
                background: #ffffff !important;
                background-color: #ffffff !important;
                color: #000000 !important;
            }
            html.app-skin-dark * {
                box-shadow: none !important;
                text-shadow: none !important;
            }
            html.app-skin-dark .print-only,
            html.app-skin-dark .print-only * {
                color: #000000 !important;
            }
        }
    </style>
</head>

<body>
    <!--! [Start] Navigation Menu -->
    <nav class="nxl-navigation">
        <div class="navbar-wrapper">
            <div class="m-header">
                <a href="{{ url('/dashboard') }}" class="b-brand">
                    <img src="{{ asset('logo/Logo%20SIMOLI.png') }}" alt="SIMOLI" class="logo logo-lg" />
                    <img src="{{ asset('logo/Icon%20SIMOLI.png') }}" alt="SIMOLI" class="logo logo-sm" />
                </a>
                <div class="nxl-navigation-toggle">
                    <a href="javascript:void(0);" id="menu-mini-button" title="Sembunyikan Sidebar">
                        <i class="feather-align-left"></i>
                    </a>
                    <a href="javascript:void(0);" id="menu-expend-button" style="display: none" title="Tampilkan Sidebar">
                        <i class="feather-arrow-right"></i>
                    </a>
                </div>
            </div>
            <div class="navbar-content">
                <ul class="nxl-navbar">
                    <li class="nxl-item nxl-caption">
                        <label>Navigation</label>
                    </li>
                    {{-- Dashboard --}}
                    <li class="nxl-item {{ request()->is('dashboard') ? 'active' : '' }}">
                        <a href="{{ url('/dashboard') }}" class="nxl-link">
                            <span class="nxl-micon"><i class="feather-airplay"></i></span>
                            <span class="nxl-mtext">Dashboard</span>
                        </a>
                    </li>
                    @if(Auth::user()->isAdmin())
                    {{-- Monitoring & Report (Admin) --}}
                    <li class="nxl-item nxl-hasmenu {{ request()->is('report-pengaliran*') || request()->is('report-pemeliharaan*') || request()->is('report-rencana*') || request()->is('report-alat-berat*') ? 'active' : '' }}">
                        <a href="javascript:void(0);" class="nxl-link">
                            <span class="nxl-micon"><i class="feather-bar-chart-2"></i></span>
                            <span class="nxl-mtext">Monitoring</span>
                            <span class="nxl-arrow"><i class="feather-chevron-right"></i></span>
                        </a>
                        <ul class="nxl-submenu">
                            <li class="nxl-item {{ request()->is('report-pengaliran*') ? 'active' : '' }}">
                                <a class="nxl-link" href="{{ route('report-pengaliran') }}">Laporan Pengaliran</a>
                            </li>
                            <li class="nxl-item {{ request()->is('report-pemeliharaan*') ? 'active' : '' }}">
                                <a class="nxl-link" href="{{ route('report-pemeliharaan') }}">Laporan Pemeliharaan</a>
                            </li>
                            <li class="nxl-item {{ request()->is('report-rencana*') ? 'active' : '' }}">
                                <a class="nxl-link" href="{{ route('report-rencana') }}">Laporan Rencana</a>
                            </li>
                            <li class="nxl-item {{ request()->is('report-alat-berat*') ? 'active' : '' }}">
                                <a class="nxl-link" href="{{ route('report-alat-berat') }}">Laporan Alat Berat</a>
                            </li>
                            <li class="nxl-item {{ request()->is('monitoring-alat-berat*') ? 'active' : '' }}">
                                <a class="nxl-link" href="{{ route('monitoring-alat-berat.index') }}">Log Monitoring Alat Berat</a>
                            </li>
                        </ul>
                    </li>
                    <li class="nxl-item nxl-hasmenu {{ request()->is('pengguna*') || request()->is('alat-berat*') ? 'active' : '' }}">
                        <a href="javascript:void(0);" class="nxl-link">
                            <span class="nxl-micon"><i class="feather-edit-3"></i></span>
                            <span class="nxl-mtext">Input Data</span>
                            <span class="nxl-arrow"><i class="feather-chevron-right"></i></span>
                        </a>
                        <ul class="nxl-submenu">
                            <li class="nxl-item {{ request()->is('alat-berat*') ? 'active' : '' }}">
                                <a class="nxl-link" href="{{ route('alat-berat.index') }}">Master Alat Berat</a>
                            </li>
                            <li class="nxl-item {{ request()->is('pengguna*') ? 'active' : '' }}">
                                <a class="nxl-link" href="{{ route('pengguna.index') }}">Data Pengguna</a>
                            </li>
                        </ul>
                    </li>
                    @else
                    {{-- Input Data (Unit) --}}
                    <li class="nxl-item nxl-hasmenu {{ request()->is('pengaliran*') || request()->is('pemeliharaan*') || request()->is('rencana*') || request()->is('alat-berat*') || request()->is('monitoring-alat-berat*') ? 'active' : '' }}">
                        <a href="javascript:void(0);" class="nxl-link">
                            <span class="nxl-micon"><i class="feather-edit-3"></i></span>
                            <span class="nxl-mtext">Input Data</span>
                            <span class="nxl-arrow"><i class="feather-chevron-right"></i></span>
                        </a>
                        <ul class="nxl-submenu">
                            <li class="nxl-item {{ request()->is('pengaliran*') ? 'active' : '' }}">
                                <a class="nxl-link" href="{{ route('pengaliran.index') }}">Pengaliran</a>
                            </li>
                            <li class="nxl-item {{ request()->is('pemeliharaan*') ? 'active' : '' }}">
                                <a class="nxl-link" href="{{ route('pemeliharaan.index') }}">Pemeliharaan</a>
                            </li>
                            <li class="nxl-item {{ request()->is('rencana*') ? 'active' : '' }}">
                                <a class="nxl-link" href="{{ route('rencana.index') }}">Rencana Pengaliran & Pemeliharaan</a>
                            </li>
                            <li class="nxl-item {{ request()->is('alat-berat*') ? 'active' : '' }}">
                                <a class="nxl-link" href="{{ route('alat-berat.index') }}">Data Master Alat Berat</a>
                            </li>
                            <li class="nxl-item {{ request()->is('monitoring-alat-berat*') ? 'active' : '' }}">
                                <a class="nxl-link" href="{{ route('monitoring-alat-berat.index') }}">Monitoring Alat Berat</a>
                            </li>
                        </ul>
                    </li>
                    @endif
                </ul>
            </div>
        </div>
    </nav>
    <!--! [End] Navigation Menu -->

    <!--! [Start] Header -->
    <header class="nxl-header">
        <div class="header-wrapper">
            <div class="header-left d-flex align-items-center gap-4">
                <a href="javascript:void(0);" class="nxl-head-mobile-toggler" id="mobile-collapse">
                    <div class="hamburger hamburger--arrowturn">
                        <div class="hamburger-box">
                            <div class="hamburger-inner"></div>
                        </div>
                    </div>
                </a>
                <div class="nxl-lavel-mega-menu-toggle d-flex d-lg-none">
                    <a href="javascript:void(0);" id="nxl-lavel-mega-menu-open">
                        <i class="feather-align-left"></i>
                    </a>
                </div>
                <div class="nxl-drp-link nxl-lavel-mega-menu">
                    <div class="nxl-lavel-mega-menu-toggle d-flex d-lg-none">
                        <a href="javascript:void(0)" id="nxl-lavel-mega-menu-hide">
                            <i class="feather-arrow-left me-2"></i>
                            <span>Back</span>
                        </a>
                    </div>
                </div>
            </div>
            <div class="header-right ms-auto">
                <div class="d-flex align-items-center">
                    <div class="nxl-h-item dark-light-theme">
                        <a href="javascript:void(0);" class="nxl-head-link me-0 dark-button">
                            <i class="feather-moon"></i>
                        </a>
                        <a href="javascript:void(0);" class="nxl-head-link me-0 light-button" style="display: none">
                            <i class="feather-sun"></i>
                        </a>
                    </div>
                    <div class="dropdown nxl-h-item">
                        <a href="javascript:void(0);" data-bs-toggle="dropdown" role="button" data-bs-auto-close="outside">
                            <img src="{{ asset('duraluxadmin/assets/images/avatar/1.png') }}" alt="user-image" class="img-fluid user-avtar me-0" />
                        </a>
                        <div class="dropdown-menu dropdown-menu-end nxl-h-dropdown nxl-user-dropdown">
                            <div class="dropdown-header">
                                <div class="d-flex align-items-center">
                                    <img src="{{ asset('duraluxadmin/assets/images/avatar/1.png') }}" alt="user-image" class="img-fluid user-avtar" />
                                    <div>
                                        <h6 class="mb-0">{{ Auth::user()->pks->nama ?? 'User' }}</h6>
                                        <span class="fs-12 fw-medium text-muted">{{ ucfirst(Auth::user()->pks->level_akses ?? '') }} - {{ Auth::user()->pks->akro ?? '' }}</span>
                                    </div>
                                </div>
                            </div>
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="dropdown-item">
                                    <i class="feather-log-out"></i>
                                    <span>Logout</span>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>
    <!--! [End] Header -->

    <!--! [Start] Main Content -->
    <main class="nxl-container">
        <div class="nxl-content">
            <!-- Page Header -->
            <div class="page-header">
                <div class="page-header-left d-flex align-items-center">
                    <div class="page-header-title">
                        <h5 class="m-b-10">@yield('page-title', 'Dashboard')</h5>
                    </div>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ url('/dashboard') }}">Dashboard</a></li>
                        @yield('breadcrumb')
                    </ul>
                </div>
                <div class="page-header-right ms-auto">
                    @yield('page-actions')
                </div>
            </div>

            <!-- Main Content -->
            <div class="main-content">
                @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="feather-check-circle me-2"></i>
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                @endif

                @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="feather-alert-circle me-2"></i>
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                @endif

                @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="feather-alert-triangle me-2"></i>
                    <strong>Terdapat kesalahan:</strong>
                    <ul class="mb-0 mt-1">
                        @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                @endif

                @yield('content')
            </div>
        </div>
    </main>
    <!--! [End] Main Content -->

    <!--! Footer Scripts -->
    <script src="{{ asset('duraluxadmin/assets/vendors/js/vendors.min.js') }}"></script>
    <script src="{{ asset('duraluxadmin/assets/js/common-init.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    @yield('scripts')
    <script src="{{ asset('duraluxadmin/assets/js/theme-customizer-init.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</body>

</html>
