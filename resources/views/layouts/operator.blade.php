<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>@yield('title', 'Operator Lapangan - SIMOLII')</title>
    
    <!-- Google Fonts & Icons -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/feather-icons/dist/feather.css">
    
    <style>
        :root {
            --op-primary: #0F52BA;
            --op-primary-dark: #0A367C;
            --op-secondary: #00A86B;
            --op-bg: #F4F6FB;
            --op-card-bg: #FFFFFF;
            --op-text-dark: #1E293B;
            --op-text-muted: #64748B;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--op-bg);
            color: var(--op-text-dark);
            padding-bottom: 85px; /* space for bottom nav */
            -webkit-tap-highlight-color: transparent;
        }

        /* Top App Bar */
        .operator-topbar {
            background: linear-gradient(135deg, #0F52BA 0%, #1E3C72 100%);
            color: #FFFFFF;
            padding: 14px 18px;
            box-shadow: 0 4px 20px rgba(15, 82, 186, 0.25);
            position: sticky;
            top: 0;
            z-index: 1030;
        }

        .operator-brand {
            font-weight: 800;
            font-size: 1.15rem;
            letter-spacing: 0.5px;
            color: #FFFFFF;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .unit-badge {
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(8px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 20px;
            padding: 3px 10px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        /* Mobile Bottom Nav */
        .operator-bottom-nav {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background: #FFFFFF;
            box-shadow: 0 -4px 20px rgba(0, 0, 0, 0.08);
            display: flex;
            justify-content: space-around;
            align-items: center;
            height: 68px;
            z-index: 1040;
            border-top: 1px solid #E2E8F0;
        }

        .nav-item-link {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            color: var(--op-text-muted);
            font-size: 0.72rem;
            font-weight: 600;
            width: 33.33%;
            height: 100%;
            transition: all 0.2s ease;
        }

        .nav-item-link i {
            font-size: 1.25rem;
            margin-bottom: 2px;
        }

        .nav-item-link.active {
            color: var(--op-primary);
            font-weight: 700;
        }

        .nav-fab-btn {
            background: linear-gradient(135deg, #00A86B 0%, #00875A 100%);
            color: white !important;
            width: 52px;
            height: 52px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 6px 16px rgba(0, 168, 107, 0.4);
            margin-top: -24px;
            border: 3px solid #FFFFFF;
        }

        .nav-fab-btn i {
            font-size: 1.5rem;
            margin: 0;
        }

        /* Operator Cards */
        .op-card {
            background: var(--op-card-bg);
            border-radius: 16px;
            border: 1px solid #E2E8F0;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.03);
            margin-bottom: 16px;
            overflow: hidden;
        }

        .op-card-header {
            padding: 14px 16px;
            border-bottom: 1px solid #F1F5F9;
            background: #FAFAFC;
            font-weight: 700;
            font-size: 0.92rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .op-card-body {
            padding: 16px;
        }

        /* Custom Touch Inputs */
        .form-label {
            font-weight: 600;
            font-size: 0.85rem;
            color: #334155;
            margin-bottom: 6px;
        }

        .form-control, .form-select {
            border-radius: 12px;
            border: 1.5px solid #CBD5E1;
            padding: 12px 14px;
            font-size: 0.95rem;
            font-weight: 500;
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--op-primary);
            box-shadow: 0 0 0 4px rgba(15, 82, 186, 0.12);
        }

        .btn-op-primary {
            background: linear-gradient(135deg, #0F52BA 0%, #1E3C72 100%);
            color: #FFFFFF;
            border: none;
            border-radius: 14px;
            padding: 14px 20px;
            font-weight: 700;
            font-size: 1rem;
            box-shadow: 0 4px 14px rgba(15, 82, 186, 0.3);
            width: 100%;
        }

        .btn-op-primary:active {
            transform: scale(0.98);
        }

        /* Status Badge */
        .badge-status-op {
            border-radius: 20px;
            padding: 4px 10px;
            font-size: 0.72rem;
            font-weight: 700;
        }

        .photo-preview-box {
            width: 100%;
            height: 160px;
            border: 2px dashed #CBD5E1;
            border-radius: 14px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            background: #F8FAFC;
            cursor: pointer;
            overflow: hidden;
            position: relative;
        }

        .photo-preview-box img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .timestamp-tag {
            position: absolute;
            bottom: 6px;
            right: 6px;
            background: rgba(15, 23, 42, 0.75);
            color: #FFFFFF;
            font-size: 0.68rem;
            padding: 3px 8px;
            border-radius: 6px;
            font-weight: 600;
        }

        @media (min-width: 768px) {
            .operator-container {
                max-width: 720px;
                margin: 0 auto;
            }
        }
    </style>

    @yield('styles')
</head>
<body>

    <!-- Top App Bar -->
    <header class="operator-topbar">
        <div class="d-flex align-items-center justify-content-between">
            <a href="{{ route('operator.index') }}" class="operator-brand">
                <i class="feather-truck"></i> SIMOLII OPERATOR
            </a>
            <div class="d-flex align-items-center gap-2">
                <span class="unit-badge">
                    <i class="feather-map-pin me-1"></i>{{ Auth::user()->pks ? Auth::user()->pks->akro : 'UNIT' }}
                </span>
                <form action="{{ route('logout') }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-outline-light rounded-circle px-2 py-1" title="Keluar">
                        <i class="feather-log-out"></i>
                    </button>
                </form>
            </div>
        </div>
    </header>

    <!-- Main Container -->
    <main class="operator-container px-3 pt-3">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show rounded-3 shadow-sm border-0 mb-3" role="alert">
                <i class="feather-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show rounded-3 shadow-sm border-0 mb-3" role="alert">
                <i class="feather-alert-triangle me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @yield('content')
    </main>

    <!-- Bottom Navigation for Mobile -->
    <nav class="operator-bottom-nav">
        <a href="{{ route('operator.index') }}" class="nav-item-link {{ Route::is('operator.index') ? 'active' : '' }}">
            <i class="feather-home"></i>
            <span>Beranda</span>
        </a>
        <a href="{{ route('operator.create') }}" class="nav-item-link {{ Route::is('operator.create') ? 'active' : '' }}">
            <i class="feather-file-plus"></i>
            <span>Input Laporan</span>
        </a>
        <a href="{{ route('operator.alat-berat.index') }}" class="nav-item-link {{ Route::is('operator.alat-berat.*') ? 'active' : '' }}">
            <i class="feather-truck"></i>
            <span>Kelola Alat</span>
        </a>
    </nav>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/feather-icons/dist/feather.min.js"></script>
    <script>
        feather.replace();
    </script>
    @yield('scripts')
</body>
</html>
