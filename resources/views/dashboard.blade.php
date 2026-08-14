<!DOCTYPE html>
<html lang="zxx">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="x-ua-compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="description" content="" />
    <meta name="keyword" content="" />
    <meta name="author" content="flexilecode" />
    <!--! The above 6 meta tags *must* come first in the head; any other head content must come *after* these tags !-->
    <!--! BEGIN: Apps Title-->
    <title>SIMOLI || Dashboard</title>
    <!--! END:  Apps Title-->
    <!--! BEGIN: Favicon-->
    <link rel="shortcut icon" type="image/png" href="{{ asset('logo/Icon%20SIMOLI.png') }}" />
    <link rel="stylesheet" type="text/css" href="{{ asset('duraluxadmin/assets/css/bootstrap.min.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ asset('duraluxadmin/assets/vendors/css/vendors.min.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ asset('duraluxadmin/assets/vendors/css/daterangepicker.min.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ asset('duraluxadmin/assets/css/theme.min.css') }}" />
    <style>
        .nxl-navigation .m-header .b-brand {
            display: flex;
            align-items: center;
        }
        .nxl-navigation .m-header .logo-lg {
            width: auto;
            max-width: 360px;
            height: 96px;
            object-fit: contain;
        }
        .nxl-navigation .m-header .logo-sm {
            width: auto;
            max-width: 140px;
            height: 140px;
            object-fit: contain;
        }
    </style>
    <!--! END: Custom CSS-->
    <!--! HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries !-->
    <!--! WARNING: Respond.js doesn"t work if you view the page via file: !-->
    <!--[if lt IE 9]>
			<script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
			<script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
		<![endif]-->
</head>

<body>
    <!--! ================================================================ !-->
    <!--! [Start] Navigation Manu !-->
    <!--! ================================================================ !-->
    <nav class="nxl-navigation">
        <div class="navbar-wrapper">
            <div class="m-header">
                <a href="index.html" class="b-brand">
                    <img src="{{ asset('logo/Logo%20SIMOLI.png') }}" alt="SIMOLI" class="logo logo-lg" />
                    <img src="{{ asset('logo/Icon%20SIMOLI.png') }}" alt="SIMOLI" class="logo logo-sm" />
                </a>
            </div>
            <div class="navbar-content">
                <ul class="nxl-navbar">
                    <li class="nxl-item nxl-caption">
                        <label>Navigation</label>
                    </li>
                    {{-- Dashboard --}}
                    <li class="nxl-item active">
                        <a href="{{ url('/dashboard') }}" class="nxl-link">
                            <span class="nxl-micon"><i class="feather-airplay"></i></span>
                            <span class="nxl-mtext">Dashboard</span>
                        </a>
                    </li>
                    {{-- Input Data --}}
                    <li class="nxl-item nxl-hasmenu">
                        <a href="javascript:void(0);" class="nxl-link">
                            <span class="nxl-micon"><i class="feather-edit-3"></i></span>
                            <span class="nxl-mtext">Input Data</span>
                            <span class="nxl-arrow"><i class="feather-chevron-right"></i></span>
                        </a>
                        <ul class="nxl-submenu">
                            <li class="nxl-item"><a class="nxl-link" href="{{ route('pengaliran.index') }}">Pengaliran</a></li>
                            <li class="nxl-item"><a class="nxl-link" href="{{ route('pemeliharaan.index') }}">Pemeliharaan</a></li>
                            <li class="nxl-item"><a class="nxl-link" href="{{ route('rencana.index') }}">Rencana Pengaliran & Pemeliharaan</a></li>
                        </ul>
                    </li>
                    {{-- Report --}}
                    <li class="nxl-item nxl-hasmenu">
                        <a href="javascript:void(0);" class="nxl-link">
                            <span class="nxl-micon"><i class="feather-bar-chart-2"></i></span>
                            <span class="nxl-mtext">Report</span>
                            <span class="nxl-arrow"><i class="feather-chevron-right"></i></span>
                        </a>
                        <ul class="nxl-submenu">
                            <li class="nxl-item"><a class="nxl-link" href="{{ route('laporan-harian') }}">Laporan Harian</a></li>
                            @if(Auth::user()->isAdmin())
                            <li class="nxl-item"><a class="nxl-link" href="{{ route('report-pengaliran') }}">Report Pengaliran</a></li>
                            <li class="nxl-item"><a class="nxl-link" href="{{ route('report-pemeliharaan') }}">Report Pemeliharaan</a></li>
                            @endif
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <!--! ================================================================ !-->
    <!--! [End]  Navigation Manu !-->
    <!--! ================================================================ !-->
    <!--! ================================================================ !-->
    <!--! [Start] Header !-->
    <!--! ================================================================ !-->
    <header class="nxl-header">
        <div class="header-wrapper">
            <!--! [Start] Header Left !-->
            <div class="header-left d-flex align-items-center gap-4">
                <!--! [Start] nxl-head-mobile-toggler !-->
                <a href="javascript:void(0);" class="nxl-head-mobile-toggler" id="mobile-collapse">
                    <div class="hamburger hamburger--arrowturn">
                        <div class="hamburger-box">
                            <div class="hamburger-inner"></div>
                        </div>
                    </div>
                </a>
                <!--! [Start] nxl-head-mobile-toggler !-->
                <!--! [Start] nxl-navigation-toggle !-->
                <div class="nxl-navigation-toggle">
                    <a href="javascript:void(0);" id="menu-mini-button">
                        <i class="feather-align-left"></i>
                    </a>
                    <a href="javascript:void(0);" id="menu-expend-button" style="display: none">
                        <i class="feather-arrow-right"></i>
                    </a>
                </div>
                <!--! [End] nxl-navigation-toggle !-->
                <!--! [Start] nxl-lavel-mega-menu-toggle !-->
                <div class="nxl-lavel-mega-menu-toggle d-flex d-lg-none">
                    <a href="javascript:void(0);" id="nxl-lavel-mega-menu-open">
                        <i class="feather-align-left"></i>
                    </a>
                </div>
                <!--! [End] nxl-lavel-mega-menu-toggle !-->
                <!--! [Start] nxl-lavel-mega-menu !-->
                <div class="nxl-drp-link nxl-lavel-mega-menu">
                    <div class="nxl-lavel-mega-menu-toggle d-flex d-lg-none">
                        <a href="javascript:void(0)" id="nxl-lavel-mega-menu-hide">
                            <i class="feather-arrow-left me-2"></i>
                            <span>Back</span>
                        </a>
                    </div>
                </div>
                <!--! [End] nxl-lavel-mega-menu !-->
            </div>
            <!--! [End] Header Left !-->
            <!--! [Start] Header Right !-->
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
                                        <h6 class="text-dark mb-0">{{ Auth::user()->pks->nama ?? 'User' }}</h6>
                                        <span class="fs-12 fw-medium text-muted">{{ ucfirst(Auth::user()->pks->level_akses ?? '') }} - {{ Auth::user()->pks->akro ?? '' }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="dropdown-divider"></div>
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
            <!--! [End] Header Right !-->
        </div>
    </header>
    <!--! ================================================================ !-->
    <!--! [End] Header !-->
    <!--! ================================================================ !-->
    <!--! ================================================================ !-->
    <!--! [Start] Main Content !-->
    <!--! ================================================================ !-->
    <main class="nxl-container">
        <div class="nxl-content">
            <!-- [ page-header ] start -->
            <div class="page-header">
                <div class="page-header-left d-flex align-items-center">
                    <div class="page-header-title">
                        <h5 class="m-b-10">Monitoring Harian</h5>
                    </div>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ url('/dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item">Dashboard</li>
                    </ul>
                </div>
                <div class="page-header-right ms-auto">
                    <div class="page-header-right-items">
                        <div class="d-flex d-md-none">
                            <a href="javascript:void(0)" class="page-header-right-close-toggle">
                                <i class="feather-arrow-left me-2"></i>
                                <span>Back</span>
                            </a>
                        </div>
                        <div class="d-flex align-items-center gap-2 page-header-right-items-wrapper">
                            <form action="{{ route('dashboard') }}" method="GET" class="d-flex align-items-center gap-2">
                                <input type="date" name="tanggal" class="form-control" value="{{ $tanggal }}" style="width: 180px">
                                <button type="submit" class="btn btn-primary btn-md">
                                    <i class="feather-search me-1"></i> Tampilkan
                                </button>
                                <a href="{{ route('dashboard') }}" class="btn btn-md btn-light-brand">
                                    <i class="feather-refresh-cw me-1"></i> Hari Ini
                                </a>
                            </form>
                        </div>
                    </div>
                    <div class="d-md-none d-flex align-items-center">
                        <a href="javascript:void(0)" class="page-header-right-open-toggle">
                            <i class="feather-align-right fs-20"></i>
                        </a>
                    </div>
                </div>
            </div>
            <!-- [ page-header ] end -->
            <!-- [ Main Content ] start -->
            @php
                $pctPengaliran = $totalPks > 0 ? round(($sudahPengaliran / $totalPks) * 100) : 0;
                $pctPemeliharaan = $totalPks > 0 ? round(($sudahPemeliharaan / $totalPks) * 100) : 0;
                $pctLengkap = $totalPks > 0 ? round(($sudahLengkap / $totalPks) * 100) : 0;
                $belumKirim = $totalPks - $sudahLengkap;
                $pctBelum = $totalPks > 0 ? round(($belumKirim / $totalPks) * 100) : 0;
            @endphp
            <div class="main-content">
                <div class="row">
                    <!-- [Total PKS] start -->
                    <div class="col-xxl-3 col-md-6">
                        <div class="card stretch stretch-full">
                            <div class="card-body">
                                <div class="d-flex align-items-start justify-content-between mb-4">
                                    <div class="d-flex gap-4 align-items-center">
                                        <div class="avatar-text avatar-lg bg-gray-200">
                                            <i class="feather-home"></i>
                                        </div>
                                        <div>
                                            <div class="fs-4 fw-bold text-dark"><span class="counter">{{ $totalPks }}</span></div>
                                            <h3 class="fs-13 fw-semibold text-truncate-1-line">Total Unit PKS</h3>
                                        </div>
                                    </div>
                                </div>
                                <div class="pt-4">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <span class="fs-12 fw-medium text-muted">Terdaftar</span>
                                        <div class="w-100 text-end">
                                            <span class="fs-12 text-dark">{{ $totalPks }} Unit</span>
                                        </div>
                                    </div>
                                    <div class="progress mt-2 ht-3">
                                        <div class="progress-bar bg-primary" role="progressbar" style="width: 100%"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- [Total PKS] end -->
                    <!-- [Pengaliran] start -->
                    <div class="col-xxl-3 col-md-6">
                        <div class="card stretch stretch-full">
                            <div class="card-body">
                                <div class="d-flex align-items-start justify-content-between mb-4">
                                    <div class="d-flex gap-4 align-items-center">
                                        <div class="avatar-text avatar-lg bg-gray-200">
                                            <i class="feather-droplet"></i>
                                        </div>
                                        <div>
                                            <div class="fs-4 fw-bold text-dark"><span class="counter">{{ $sudahPengaliran }}</span>/<span class="counter">{{ $totalPks }}</span></div>
                                            <h3 class="fs-13 fw-semibold text-truncate-1-line">Sudah Kirim Pengaliran</h3>
                                        </div>
                                    </div>
                                </div>
                                <div class="pt-4">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <span class="fs-12 fw-medium text-muted">Progress</span>
                                        <div class="w-100 text-end">
                                            <span class="fs-12 text-dark">{{ $sudahPengaliran }} Terkirim</span>
                                            <span class="fs-11 text-muted">({{ $pctPengaliran }}%)</span>
                                        </div>
                                    </div>
                                    <div class="progress mt-2 ht-3">
                                        <div class="progress-bar bg-success" role="progressbar" style="width: {{ $pctPengaliran }}%"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- [Pengaliran] end -->
                    <!-- [Pemeliharaan] start -->
                    <div class="col-xxl-3 col-md-6">
                        <div class="card stretch stretch-full">
                            <div class="card-body">
                                <div class="d-flex align-items-start justify-content-between mb-4">
                                    <div class="d-flex gap-4 align-items-center">
                                        <div class="avatar-text avatar-lg bg-gray-200">
                                            <i class="feather-tool"></i>
                                        </div>
                                        <div>
                                            <div class="fs-4 fw-bold text-dark"><span class="counter">{{ $sudahPemeliharaan }}</span>/<span class="counter">{{ $totalPks }}</span></div>
                                            <h3 class="fs-13 fw-semibold text-truncate-1-line">Sudah Kirim Pemeliharaan</h3>
                                        </div>
                                    </div>
                                </div>
                                <div class="pt-4">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <span class="fs-12 fw-medium text-muted">Progress</span>
                                        <div class="w-100 text-end">
                                            <span class="fs-12 text-dark">{{ $sudahPemeliharaan }} Terkirim</span>
                                            <span class="fs-11 text-muted">({{ $pctPemeliharaan }}%)</span>
                                        </div>
                                    </div>
                                    <div class="progress mt-2 ht-3">
                                        <div class="progress-bar bg-warning" role="progressbar" style="width: {{ $pctPemeliharaan }}%"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- [Pemeliharaan] end -->
                    <!-- [Belum Lengkap] start -->
                    <div class="col-xxl-3 col-md-6">
                        <div class="card stretch stretch-full">
                            <div class="card-body">
                                <div class="d-flex align-items-start justify-content-between mb-4">
                                    <div class="d-flex gap-4 align-items-center">
                                        <div class="avatar-text avatar-lg bg-gray-200">
                                            <i class="feather-alert-circle"></i>
                                        </div>
                                        <div>
                                            <div class="fs-4 fw-bold text-dark"><span class="counter">{{ $belumKirim }}</span>/<span class="counter">{{ $totalPks }}</span></div>
                                            <h3 class="fs-13 fw-semibold text-truncate-1-line">Belum Lengkap</h3>
                                        </div>
                                    </div>
                                </div>
                                <div class="pt-4">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <span class="fs-12 fw-medium text-muted">Sisa</span>
                                        <div class="w-100 text-end">
                                            <span class="fs-12 text-dark">{{ $belumKirim }} Belum</span>
                                            <span class="fs-11 text-muted">({{ $pctBelum }}%)</span>
                                        </div>
                                    </div>
                                    <div class="progress mt-2 ht-3">
                                        <div class="progress-bar bg-danger" role="progressbar" style="width: {{ $pctBelum }}%"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- [Belum Lengkap] end -->
                    <!-- [Tabel Monitoring] start -->
                    <div class="col-xxl-8">
                        <div class="card stretch stretch-full">
                            <div class="card-header">
                                <h5 class="card-title">Status Monitoring PKS</h5>
                                <span class="badge bg-soft-primary text-primary fs-11">{{ \Carbon\Carbon::parse($tanggal)->translatedFormat('d F Y') }}</span>
                            </div>
                            <div class="card-body custom-card-action p-0">
                                <div class="table-responsive">
                                    <table class="table table-hover mb-0">
                                        <thead>
                                            <tr class="border-b">
                                                <th scope="col" class="ps-4">No</th>
                                                <th scope="col">Unit PKS</th>
                                                <th scope="col" class="text-center">Pengaliran</th>
                                                <th scope="col" class="text-center">Pemeliharaan</th>
                                                <th scope="col" class="text-center">Keterangan</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($monitoringData as $i => $item)
                                            <tr>
                                                <td class="ps-4">{{ $i + 1 }}</td>
                                                <td>
                                                    <div class="d-flex align-items-center gap-3">
                                                        <div class="avatar-text avatar-md bg-gray-200 rounded-2">
                                                            {{ strtoupper(substr($item['pks'], 0, 2)) }}
                                                        </div>
                                                        <span class="fw-semibold text-truncate-1-line">{{ $item['pks'] }}</span>
                                                    </div>
                                                </td>
                                                <td class="text-center">
                                                    @if($item['pengaliran'])
                                                        <span class="badge bg-soft-success text-success">Sudah</span>
                                                    @else
                                                        <span class="badge bg-soft-danger text-danger">Belum</span>
                                                    @endif
                                                </td>
                                                <td class="text-center">
                                                    @if($item['pemeliharaan'])
                                                        <span class="badge bg-soft-success text-success">Sudah</span>
                                                    @else
                                                        <span class="badge bg-soft-danger text-danger">Belum</span>
                                                    @endif
                                                </td>
                                                <td class="text-center">
                                                    @if($item['pengaliran'] && $item['pemeliharaan'])
                                                        <span class="badge bg-soft-success text-success"><i class="feather-check-circle me-1"></i>Lengkap</span>
                                                    @elseif($item['pengaliran'] || $item['pemeliharaan'])
                                                        <span class="badge bg-soft-warning text-warning"><i class="feather-clock me-1"></i>Sebagian</span>
                                                    @else
                                                        <span class="badge bg-soft-danger text-danger"><i class="feather-x-circle me-1"></i>Belum Ada</span>
                                                    @endif
                                                </td>
                                            </tr>
                                            @empty
                                            <tr>
                                                <td colspan="5" class="text-center text-muted py-4">Tidak ada data PKS ditemukan</td>
                                            </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- [Tabel Monitoring] end -->
                    <!-- [Ringkasan] start -->
                    <div class="col-xxl-4">
                        <div class="card stretch stretch-full">
                            <div class="card-header">
                                <h5 class="card-title">Ringkasan</h5>
                            </div>
                            <div class="card-body">
                                <div class="p-3 border border-dashed rounded-3 mb-3">
                                    <div class="d-flex align-items-center justify-content-between mb-2">
                                        <span class="fs-12 fw-medium text-muted">Pengaliran Terkirim</span>
                                        <span class="fs-12 fw-bold text-success">{{ $sudahPengaliran }}/{{ $totalPks }}</span>
                                    </div>
                                    <div class="progress ht-3">
                                        <div class="progress-bar bg-success" role="progressbar" style="width: {{ $pctPengaliran }}%"></div>
                                    </div>
                                </div>
                                <div class="p-3 border border-dashed rounded-3 mb-3">
                                    <div class="d-flex align-items-center justify-content-between mb-2">
                                        <span class="fs-12 fw-medium text-muted">Pemeliharaan Terkirim</span>
                                        <span class="fs-12 fw-bold text-warning">{{ $sudahPemeliharaan }}/{{ $totalPks }}</span>
                                    </div>
                                    <div class="progress ht-3">
                                        <div class="progress-bar bg-warning" role="progressbar" style="width: {{ $pctPemeliharaan }}%"></div>
                                    </div>
                                </div>
                                <div class="p-3 border border-dashed rounded-3 mb-3">
                                    <div class="d-flex align-items-center justify-content-between mb-2">
                                        <span class="fs-12 fw-medium text-muted">Laporan Lengkap</span>
                                        <span class="fs-12 fw-bold text-primary">{{ $sudahLengkap }}/{{ $totalPks }}</span>
                                    </div>
                                    <div class="progress ht-3">
                                        <div class="progress-bar bg-primary" role="progressbar" style="width: {{ $pctLengkap }}%"></div>
                                    </div>
                                </div>
                                <div class="p-3 border border-dashed rounded-3">
                                    <div class="d-flex align-items-center justify-content-between mb-2">
                                        <span class="fs-12 fw-medium text-muted">Belum Lengkap</span>
                                        <span class="fs-12 fw-bold text-danger">{{ $belumKirim }}/{{ $totalPks }}</span>
                                    </div>
                                    <div class="progress ht-3">
                                        <div class="progress-bar bg-danger" role="progressbar" style="width: {{ $pctBelum }}%"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer">
                                <div class="d-flex align-items-center">
                                    <i class="feather-calendar me-2 text-muted"></i>
                                    <span class="fs-11 text-muted">Data tanggal: {{ \Carbon\Carbon::parse($tanggal)->translatedFormat('d F Y') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- [Ringkasan] end -->
                </div>
            </div>
            <!-- [ Main Content ] end -->
        </div>
        <!-- [ Footer ] start -->
        <footer class="footer">
            <p class="fs-11 text-muted fw-medium text-uppercase mb-0 copyright">
                <span>Copyright Â©</span>
                <script>
                    document.write(new Date().getFullYear());
                </script>
            </p>
            <div class="d-flex align-items-center gap-4">
                <a href="javascript:void(0);" class="fs-11 fw-semibold text-uppercase">Help</a>
                <a href="javascript:void(0);" class="fs-11 fw-semibold text-uppercase">Terms</a>
                <a href="javascript:void(0);" class="fs-11 fw-semibold text-uppercase">Privacy</a>
            </div>
        </footer>
        <!-- [ Footer ] end -->
    </main>
    <!--! ================================================================ !-->
    <!--! [End] Main Content !-->
    <!--! ================================================================ !-->
    <!--! ================================================================ !-->
    <!--! BEGIN: Theme Customizer !-->
    <!--! ================================================================ !-->
    <div class="theme-customizer">
        <div class="customizer-handle">
            <a href="javascript:void(0);" class="cutomizer-open-trigger bg-primary">
                <i class="feather-settings"></i>
            </a>
        </div>
        <div class="customizer-sidebar-wrapper">
            <div class="customizer-sidebar-header px-4 ht-80 border-bottom d-flex align-items-center justify-content-between">
                <h5 class="mb-0">Theme Settings</h5>
                <a href="javascript:void(0);" class="cutomizer-close-trigger d-flex">
                    <i class="feather-x"></i>
                </a>
            </div>
            <div class="customizer-sidebar-body position-relative p-4" data-scrollbar-target="#psScrollbarInit">
                <!--! BEGIN: [Navigation] !-->
                <div class="position-relative px-3 pb-3 pt-4 mt-3 mb-5 border border-gray-2 theme-options-set">
                    <label class="py-1 px-2 fs-8 fw-bold text-uppercase text-muted text-spacing-2 bg-white border border-gray-2 position-absolute rounded-2 options-label" style="top: -12px">Navigation</label>
                    <div class="row g-2 theme-options-items app-navigation" id="appNavigationList">
                        <div class="col-6 text-center single-option">
                            <input type="radio" class="btn-check" id="app-navigation-light" name="app-navigation" value="1" data-app-navigation="app-navigation-light" checked />
                            <label class="py-2 fs-9 fw-bold text-dark text-uppercase text-spacing-1 border border-gray-2 w-100 h-100 c-pointer position-relative options-label" for="app-navigation-light">Light</label>
                        </div>
                        <div class="col-6 text-center single-option">
                            <input type="radio" class="btn-check" id="app-navigation-dark" name="app-navigation" value="2" data-app-navigation="app-navigation-dark" />
                            <label class="py-2 fs-9 fw-bold text-dark text-uppercase text-spacing-1 border border-gray-2 w-100 h-100 c-pointer position-relative options-label" for="app-navigation-dark">Dark</label>
                        </div>
                    </div>
                </div>
                <!--! END: [Navigation] !-->
                <!--! BEGIN: [Header] !-->
                <div class="position-relative px-3 pb-3 pt-4 mt-3 mb-5 border border-gray-2 theme-options-set mt-5">
                    <label class="py-1 px-2 fs-8 fw-bold text-uppercase text-muted text-spacing-2 bg-white border border-gray-2 position-absolute rounded-2 options-label" style="top: -12px">Header</label>
                    <div class="row g-2 theme-options-items app-header" id="appHeaderList">
                        <div class="col-6 text-center single-option">
                            <input type="radio" class="btn-check" id="app-header-light" name="app-header" value="1" data-app-header="app-header-light" checked />
                            <label class="py-2 fs-9 fw-bold text-dark text-uppercase text-spacing-1 border border-gray-2 w-100 h-100 c-pointer position-relative options-label" for="app-header-light">Light</label>
                        </div>
                        <div class="col-6 text-center single-option">
                            <input type="radio" class="btn-check" id="app-header-dark" name="app-header" value="2" data-app-header="app-header-dark" />
                            <label class="py-2 fs-9 fw-bold text-dark text-uppercase text-spacing-1 border border-gray-2 w-100 h-100 c-pointer position-relative options-label" for="app-header-dark">Dark</label>
                        </div>
                    </div>
                </div>
                <!--! END: [Header] !-->
                <!--! BEGIN: [Skins] !-->
                <div class="position-relative px-3 pb-3 pt-4 mt-3 mb-5 border border-gray-2 theme-options-set">
                    <label class="py-1 px-2 fs-8 fw-bold text-uppercase text-muted text-spacing-2 bg-white border border-gray-2 position-absolute rounded-2 options-label" style="top: -12px">Skins</label>
                    <div class="row g-2 theme-options-items app-skin" id="appSkinList">
                        <div class="col-6 text-center position-relative single-option light-button active">
                            <input type="radio" class="btn-check" id="app-skin-light" name="app-skin" value="1" data-app-skin="app-skin-light" />
                            <label class="py-2 fs-9 fw-bold text-dark text-uppercase text-spacing-1 border border-gray-2 w-100 h-100 c-pointer position-relative options-label" for="app-skin-light">Light</label>
                        </div>
                        <div class="col-6 text-center position-relative single-option dark-button">
                            <input type="radio" class="btn-check" id="app-skin-dark" name="app-skin" value="2" data-app-skin="app-skin-dark" />
                            <label class="py-2 fs-9 fw-bold text-dark text-uppercase text-spacing-1 border border-gray-2 w-100 h-100 c-pointer position-relative options-label" for="app-skin-dark">Dark</label>
                        </div>
                    </div>
                </div>
                <!--! END: [Skins] !-->
                <!--! BEGIN: [Typography] !-->
                <div class="position-relative px-3 pb-3 pt-4 mt-3 mb-0 border border-gray-2 theme-options-set">
                    <label class="py-1 px-2 fs-8 fw-bold text-uppercase text-muted text-spacing-2 bg-white border border-gray-2 position-absolute rounded-2 options-label" style="top: -12px">Typography</label>
                    <div class="row g-2 theme-options-items font-family" id="fontFamilyList">
                        <div class="col-6 text-center single-option">
                            <input type="radio" class="btn-check" id="app-font-family-lato" name="font-family" value="1" data-font-family="app-font-family-lato" />
                            <label class="py-2 fs-9 fw-bold text-dark text-uppercase text-spacing-1 border border-gray-2 w-100 h-100 c-pointer position-relative options-label" for="app-font-family-lato">Lato</label>
                        </div>
                        <div class="col-6 text-center single-option">
                            <input type="radio" class="btn-check" id="app-font-family-rubik" name="font-family" value="2" data-font-family="app-font-family-rubik" />
                            <label class="py-2 fs-9 fw-bold text-dark text-uppercase text-spacing-1 border border-gray-2 w-100 h-100 c-pointer position-relative options-label" for="app-font-family-rubik">Rubik</label>
                        </div>
                        <div class="col-6 text-center single-option">
                            <input type="radio" class="btn-check" id="app-font-family-inter" name="font-family" value="3" data-font-family="app-font-family-inter" checked />
                            <label class="py-2 fs-9 fw-bold text-dark text-uppercase text-spacing-1 border border-gray-2 w-100 h-100 c-pointer position-relative options-label" for="app-font-family-inter">Inter</label>
                        </div>
                        <div class="col-6 text-center single-option">
                            <input type="radio" class="btn-check" id="app-font-family-cinzel" name="font-family" value="4" data-font-family="app-font-family-cinzel" />
                            <label class="py-2 fs-9 fw-bold text-dark text-uppercase text-spacing-1 border border-gray-2 w-100 h-100 c-pointer position-relative options-label" for="app-font-family-cinzel">Cinzel</label>
                        </div>
                        <div class="col-6 text-center single-option">
                            <input type="radio" class="btn-check" id="app-font-family-nunito" name="font-family" value="6" data-font-family="app-font-family-nunito" />
                            <label class="py-2 fs-9 fw-bold text-dark text-uppercase text-spacing-1 border border-gray-2 w-100 h-100 c-pointer position-relative options-label" for="app-font-family-nunito">Nunito</label>
                        </div>
                        <div class="col-6 text-center single-option">
                            <input type="radio" class="btn-check" id="app-font-family-roboto" name="font-family" value="7" data-font-family="app-font-family-roboto" />
                            <label class="py-2 fs-9 fw-bold text-dark text-uppercase text-spacing-1 border border-gray-2 w-100 h-100 c-pointer position-relative options-label" for="app-font-family-roboto">Roboto</label>
                        </div>
                        <div class="col-6 text-center single-option">
                            <input type="radio" class="btn-check" id="app-font-family-ubuntu" name="font-family" value="8" data-font-family="app-font-family-ubuntu" />
                            <label class="py-2 fs-9 fw-bold text-dark text-uppercase text-spacing-1 border border-gray-2 w-100 h-100 c-pointer position-relative options-label" for="app-font-family-ubuntu">Ubuntu</label>
                        </div>
                        <div class="col-6 text-center single-option">
                            <input type="radio" class="btn-check" id="app-font-family-poppins" name="font-family" value="9" data-font-family="app-font-family-poppins" />
                            <label class="py-2 fs-9 fw-bold text-dark text-uppercase text-spacing-1 border border-gray-2 w-100 h-100 c-pointer position-relative options-label" for="app-font-family-poppins">Poppins</label>
                        </div>
                        <div class="col-6 text-center single-option">
                            <input type="radio" class="btn-check" id="app-font-family-raleway" name="font-family" value="10" data-font-family="app-font-family-raleway" />
                            <label class="py-2 fs-9 fw-bold text-dark text-uppercase text-spacing-1 border border-gray-2 w-100 h-100 c-pointer position-relative options-label" for="app-font-family-raleway">Raleway</label>
                        </div>
                        <div class="col-6 text-center single-option">
                            <input type="radio" class="btn-check" id="app-font-family-system-ui" name="font-family" value="11" data-font-family="app-font-family-system-ui" />
                            <label class="py-2 fs-9 fw-bold text-dark text-uppercase text-spacing-1 border border-gray-2 w-100 h-100 c-pointer position-relative options-label" for="app-font-family-system-ui">System UI</label>
                        </div>
                        <div class="col-6 text-center single-option">
                            <input type="radio" class="btn-check" id="app-font-family-noto-sans" name="font-family" value="12" data-font-family="app-font-family-noto-sans" />
                            <label class="py-2 fs-9 fw-bold text-dark text-uppercase text-spacing-1 border border-gray-2 w-100 h-100 c-pointer position-relative options-label" for="app-font-family-noto-sans">Noto Sans</label>
                        </div>
                        <div class="col-6 text-center single-option">
                            <input type="radio" class="btn-check" id="app-font-family-fira-sans" name="font-family" value="13" data-font-family="app-font-family-fira-sans" />
                            <label class="py-2 fs-9 fw-bold text-dark text-uppercase text-spacing-1 border border-gray-2 w-100 h-100 c-pointer position-relative options-label" for="app-font-family-fira-sans">Fira Sans</label>
                        </div>
                        <div class="col-6 text-center single-option">
                            <input type="radio" class="btn-check" id="app-font-family-work-sans" name="font-family" value="14" data-font-family="app-font-family-work-sans" />
                            <label class="py-2 fs-9 fw-bold text-dark text-uppercase text-spacing-1 border border-gray-2 w-100 h-100 c-pointer position-relative options-label" for="app-font-family-work-sans">Work Sans</label>
                        </div>
                        <div class="col-6 text-center single-option">
                            <input type="radio" class="btn-check" id="app-font-family-open-sans" name="font-family" value="15" data-font-family="app-font-family-open-sans" />
                            <label class="py-2 fs-9 fw-bold text-dark text-uppercase text-spacing-1 border border-gray-2 w-100 h-100 c-pointer position-relative options-label" for="app-font-family-open-sans">Open Sans</label>
                        </div>
                        <div class="col-6 text-center single-option">
                            <input type="radio" class="btn-check" id="app-font-family-maven-pro" name="font-family" value="16" data-font-family="app-font-family-maven-pro" />
                            <label class="py-2 fs-9 fw-bold text-dark text-uppercase text-spacing-1 border border-gray-2 w-100 h-100 c-pointer position-relative options-label" for="app-font-family-maven-pro">Maven Pro</label>
                        </div>
                        <div class="col-6 text-center single-option">
                            <input type="radio" class="btn-check" id="app-font-family-quicksand" name="font-family" value="17" data-font-family="app-font-family-quicksand" />
                            <label class="py-2 fs-9 fw-bold text-dark text-uppercase text-spacing-1 border border-gray-2 w-100 h-100 c-pointer position-relative options-label" for="app-font-family-quicksand">Quicksand</label>
                        </div>
                        <div class="col-6 text-center single-option">
                            <input type="radio" class="btn-check" id="app-font-family-montserrat" name="font-family" value="18" data-font-family="app-font-family-montserrat" />
                            <label class="py-2 fs-9 fw-bold text-dark text-uppercase text-spacing-1 border border-gray-2 w-100 h-100 c-pointer position-relative options-label" for="app-font-family-montserrat">Montserrat</label>
                        </div>
                        <div class="col-6 text-center single-option">
                            <input type="radio" class="btn-check" id="app-font-family-josefin-sans" name="font-family" value="19" data-font-family="app-font-family-josefin-sans" />
                            <label class="py-2 fs-9 fw-bold text-dark text-uppercase text-spacing-1 border border-gray-2 w-100 h-100 c-pointer position-relative options-label" for="app-font-family-josefin-sans">Josefin Sans</label>
                        </div>
                        <div class="col-6 text-center single-option">
                            <input type="radio" class="btn-check" id="app-font-family-ibm-plex-sans" name="font-family" value="20" data-font-family="app-font-family-ibm-plex-sans" />
                            <label class="py-2 fs-9 fw-bold text-dark text-uppercase text-spacing-1 border border-gray-2 w-100 h-100 c-pointer position-relative options-label" for="app-font-family-ibm-plex-sans">IBM Plex Sans</label>
                        </div>
                        <div class="col-6 text-center single-option">
                            <input type="radio" class="btn-check" id="app-font-family-source-sans-pro" name="font-family" value="5" data-font-family="app-font-family-source-sans-pro" />
                            <label class="py-2 fs-9 fw-bold text-dark text-uppercase text-spacing-1 border border-gray-2 w-100 h-100 c-pointer position-relative options-label" for="app-font-family-source-sans-pro">Source Sans Pro</label>
                        </div>
                        <div class="col-6 text-center single-option">
                            <input type="radio" class="btn-check" id="app-font-family-montserrat-alt" name="font-family" value="21" data-font-family="app-font-family-montserrat-alt" />
                            <label class="py-2 fs-9 fw-bold text-dark text-uppercase text-spacing-1 border border-gray-2 w-100 h-100 c-pointer position-relative options-label" for="app-font-family-montserrat-alt">Montserrat Alt</label>
                        </div>
                        <div class="col-6 text-center single-option">
                            <input type="radio" class="btn-check" id="app-font-family-roboto-slab" name="font-family" value="22" data-font-family="app-font-family-roboto-slab" />
                            <label class="py-2 fs-9 fw-bold text-dark text-uppercase text-spacing-1 border border-gray-2 w-100 h-100 c-pointer position-relative options-label" for="app-font-family-roboto-slab">Roboto Slab</label>
                        </div>
                    </div>
                </div>
                <!--! END: [Typography] !-->
            </div>
            <div class="customizer-sidebar-footer px-4 ht-60 border-top d-flex align-items-center gap-2">
                <div class="flex-fill w-50">
                    <a href="javascript:void(0);" class="btn btn-danger" data-style="reset-all-common-style">Reset</a>
                </div>
                <div class="flex-fill w-50">
                    <a href="javascript:void(0);" class="btn btn-primary">Download</a>
                </div>
            </div>
        </div>
    </div>
    <!--! ================================================================ !-->
    <!--! [End] Theme Customizer !-->
    <!--! ================================================================ !-->
    <!--! ================================================================ !-->
    <!--! Footer Script !-->
    <!--! ================================================================ !-->
    <!--! BEGIN: Vendors JS !-->
    <script src="{{ asset('duraluxadmin/assets/vendors/js/daterangepicker.min.js') }}"></script>
    <script src="{{ asset('duraluxadmin/assets/vendors/js/vendors.min.js') }}"></script>
    <script src="{{ asset('duraluxadmin/assets/vendors/js/apexcharts.min.js') }}"></script>
    <script src="{{ asset('duraluxadmin/assets/vendors/js/circle-progress.min.js') }}"></script>
    <script src="{{ asset('duraluxadmin/assets/js/common-init.min.js') }}"></script>
    <script src="{{ asset('duraluxadmin/assets/js/dashboard-init.min.js') }}"></script>
    <script src="{{ asset('duraluxadmin/assets/js/theme-customizer-init.min.js') }}"></script>
    <!--! END: Theme Customizer !-->
</body>

</html>
