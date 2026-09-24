<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <meta name="theme-color" content="#0F52BA">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="SIMOLI">
    <link rel="manifest" href="/manifest.webmanifest">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('logo/Icon%20SIMOLI.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('logo/Icon%20SIMOLI.png') }}">
    <title>@yield('title', 'Operator Lapangan - SIMOLI')</title>
    
    <!-- Google Fonts & Icon Fonts (Verified Active 200 OK) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Poppins:wght@500;600;700&display=swap" rel="stylesheet">
    
    <!-- Bootstrap 5.3.3 CSS (CDN) -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    
    <!-- Icon Fonts: Feather (Local), Bootstrap Icons (CDN), FontAwesome 6 (CDN) -->
    <link rel="stylesheet" type="text/css" href="{{ asset('duraluxadmin/assets/vendors/css/feather.min.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <style>
        :root {
            --op-primary: #0F52BA;
            --op-primary-dark: #0A367C;
            --op-primary-light: #EEF4FF;
            --op-secondary: #059669;
            --op-success: #10B981;
            --op-warning: #F59E0B;
            --op-danger: #EF4444;
            --op-bg: #F8FAFC;
            --op-card-bg: #FFFFFF;
            --op-text-dark: #0F172A;
            --op-text-muted: #64748B;
            --op-border: #E2E8F0;
            --op-radius: 16px;
        }

        /* Base styles */
        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            background-color: var(--op-bg);
            color: var(--op-text-dark);
            min-height: 100vh;
            padding-bottom: calc(85px + env(safe-area-inset-bottom, 0px));
            -webkit-tap-highlight-color: transparent;
            -webkit-font-smoothing: antialiased;
        }

        /* SVG / Pagination Icon Safeguards */
        .pagination svg,
        .page-item svg,
        nav[aria-label="Pagination Navigation"] svg,
        nav[role="navigation"] svg {
            max-width: 16px !important;
            max-height: 16px !important;
            width: 16px !important;
            height: 16px !important;
            display: inline-block;
        }

        /* App Top Bar */
        .operator-topbar {
            background: linear-gradient(135deg, #0F52BA 0%, #1E3C72 100%);
            color: #FFFFFF;
            padding: 12px 16px;
            box-shadow: 0 4px 20px rgba(15, 82, 186, 0.2);
            position: sticky;
            top: 0;
            z-index: 1030;
        }

        .operator-topbar-inner {
            max-width: 1080px;
            margin: 0 auto;
            display: flex;
            align-items: center;
            justify-content: space-between;
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
            transition: opacity 0.2s;
        }

        .operator-brand:hover {
            color: #FFFFFF;
            opacity: 0.9;
        }

        .unit-badge {
            background: rgba(255, 255, 255, 0.18);
            backdrop-filter: blur(8px);
            border: 1px solid rgba(255, 255, 255, 0.28);
            border-radius: 20px;
            padding: 4px 10px;
            font-size: 0.72rem;
            font-weight: 700;
            color: #FFFFFF;
        }

        .sync-btn {
            background: rgba(255, 255, 255, 0.15);
            border: 1px solid rgba(255, 255, 255, 0.25);
            color: #FFFFFF;
            border-radius: 20px;
            padding: 5px 12px;
            font-size: 0.75rem;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            cursor: pointer;
            transition: all 0.2s ease;
            text-decoration: none;
        }

        .sync-btn:hover, .sync-btn:active {
            background: rgba(255, 255, 255, 0.3);
            color: #FFFFFF;
        }

        .sync-btn.syncing i {
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            100% { transform: rotate(360deg); }
        }

        /* Container Layout Responsiveness */
        .operator-container {
            width: 100%;
            max-width: 100%;
            padding-left: 14px;
            padding-right: 14px;
            margin: 0 auto;
        }

        @media (min-width: 576px) {
            .operator-container {
                max-width: 540px;
                padding-left: 16px;
                padding-right: 16px;
            }
        }

        @media (min-width: 768px) {
            .operator-container {
                max-width: 768px;
                padding-top: 8px;
            }
        }

        @media (min-width: 992px) {
            .operator-container {
                max-width: 960px;
            }
        }

        @media (min-width: 1200px) {
            .operator-container {
                max-width: 1080px;
            }
        }

        /* Modern Clean Cards */
        .op-card {
            background: var(--op-card-bg);
            border-radius: var(--op-radius);
            border: 1px solid var(--op-border);
            box-shadow: 0 2px 10px rgba(15, 23, 42, 0.04);
            margin-bottom: 16px;
            overflow: hidden;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .op-card-header {
            padding: 12px 16px;
            border-bottom: 1px solid #F1F5F9;
            background: #FAFAFC;
            font-weight: 700;
            font-size: 0.92rem;
            color: var(--op-text-dark);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .op-card-body {
            padding: 16px;
        }

        @media (min-width: 768px) {
            .op-card-body {
                padding: 20px;
            }
        }

        /* Clean Step Header for Forms */
        .step-badge {
            width: 24px;
            height: 24px;
            border-radius: 50%;
            background: var(--op-primary);
            color: white;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 0.75rem;
            font-weight: 800;
            margin-right: 8px;
        }

        /* Form Inputs - Clean & Touch Friendly */
        .form-label {
            font-weight: 600;
            font-size: 0.84rem;
            color: #334155;
            margin-bottom: 6px;
            display: block;
        }

        .form-control, .form-select {
            border-radius: 12px;
            border: 1.5px solid #CBD5E1;
            padding: 10px 14px;
            font-size: 0.92rem;
            font-weight: 500;
            color: #1E293B;
            background-color: #FFFFFF;
            transition: all 0.2s ease;
            min-height: 44px;
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--op-primary);
            box-shadow: 0 0 0 4px rgba(15, 82, 186, 0.12);
            outline: none;
        }

        .form-control[readonly], .form-control:disabled {
            background-color: #F8FAFC;
            border-color: #E2E8F0;
            color: #475569;
        }

        /* Primary Action Buttons */
        .btn-op-primary {
            background: linear-gradient(135deg, #0F52BA 0%, #1E3C72 100%);
            color: #FFFFFF;
            border: none;
            border-radius: 14px;
            padding: 13px 20px;
            font-weight: 700;
            font-size: 0.96rem;
            letter-spacing: 0.3px;
            box-shadow: 0 4px 14px rgba(15, 82, 186, 0.25);
            width: 100%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: all 0.2s ease;
            cursor: pointer;
            text-decoration: none;
        }

        .btn-op-primary:hover {
            color: #FFFFFF;
            opacity: 0.95;
            box-shadow: 0 6px 18px rgba(15, 82, 186, 0.35);
        }

        .btn-op-primary:active {
            transform: scale(0.98);
        }

        /* Status Badges */
        .badge-status-op {
            border-radius: 20px;
            padding: 5px 11px;
            font-size: 0.72rem;
            font-weight: 700;
            letter-spacing: 0.2px;
        }

        /* Photo Upload & Preview Box */
        .photo-preview-box {
            width: 100%;
            height: 160px;
            border: 2px dashed #94A3B8;
            border-radius: 14px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            background: #F8FAFC;
            cursor: pointer;
            overflow: hidden;
            position: relative;
            transition: all 0.2s ease;
        }

        .photo-preview-box:hover, .photo-preview-box:active {
            border-color: var(--op-primary);
            background: var(--op-primary-light);
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
            background: rgba(15, 23, 42, 0.8);
            color: #FFFFFF;
            font-size: 0.68rem;
            padding: 3px 8px;
            border-radius: 6px;
            font-weight: 600;
            backdrop-filter: blur(4px);
        }

        /* Modern Bottom Navigation (Mobile & Desktop Adaptive Dock) */
        .operator-bottom-nav-wrapper {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            z-index: 1040;
            padding: 0;
            pointer-events: none;
        }

        .operator-bottom-nav {
            background: #FFFFFF;
            box-shadow: 0 -4px 20px rgba(15, 23, 42, 0.08);
            display: flex;
            justify-content: space-around;
            align-items: center;
            height: 64px;
            border-top: 1px solid #E2E8F0;
            width: 100%;
            pointer-events: auto;
            padding-bottom: env(safe-area-inset-bottom, 0px);
        }

        @media (min-width: 768px) {
            .operator-bottom-nav-wrapper {
                bottom: 16px;
                display: flex;
                justify-content: center;
                padding: 0 16px;
            }

            .operator-bottom-nav {
                max-width: 580px;
                border-radius: 24px;
                border: 1px solid #CBD5E1;
                box-shadow: 0 10px 30px rgba(15, 23, 42, 0.15);
                height: 60px;
                padding-bottom: 0;
            }
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
            flex: 1;
            height: 100%;
            transition: all 0.2s ease;
            position: relative;
        }

        .nav-item-link i {
            font-size: 1.25rem;
            margin-bottom: 2px;
            transition: transform 0.2s ease;
        }

        .nav-item-link.active {
            color: var(--op-primary);
            font-weight: 700;
        }

        .nav-item-link.active i {
            transform: translateY(-2px);
        }

        .nav-item-link:hover {
            color: var(--op-primary);
        }

        /* Toast Container */
        #simoli-toast-container {
            position: fixed;
            top: 72px;
            left: 50%;
            transform: translateX(-50%);
            z-index: 1060;
            width: 92%;
            max-width: 480px;
            pointer-events: none;
        }

        .simoli-toast {
            pointer-events: auto;
            border-radius: 12px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);
            margin-bottom: 8px;
            animation: slideDown 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        }

        @keyframes slideDown {
            from { opacity: 0; transform: translateY(-12px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Helpers */
        .fs-11 { font-size: 0.72rem !important; }
        .fs-12 { font-size: 0.78rem !important; }
        .fs-13 { font-size: 0.84rem !important; }
        .fs-14 { font-size: 0.90rem !important; }
        .fs-15 { font-size: 0.96rem !important; }

        .bg-soft-primary { background-color: #EFF6FF !important; color: #1D4ED8 !important; }
        .bg-soft-success { background-color: #ECFDF5 !important; color: #047857 !important; }
        .bg-soft-warning { background-color: #FFFBEB !important; color: #B45309 !important; }
        .bg-soft-danger { background-color: #FEF2F2 !important; color: #B91C1C !important; }
        .bg-soft-info { background-color: #F0F9FF !important; color: #0369A1 !important; }
    </style>

    @yield('styles')
</head>
<body>

    <!-- Top App Bar -->
    <header class="operator-topbar">
        <div class="operator-topbar-inner">
            <a href="{{ route('operator.index') }}" class="operator-brand">
                <i class="feather-truck me-1"></i>
                <span>
                    @if(Auth::check() && method_exists(Auth::user(), 'isMandor') && Auth::user()->isMandor())
                        SIMOLI MANDOR
                    @else
                        SIMOLI OPERATOR
                    @endif
                </span>
            </a>
            
            <div class="d-flex align-items-center gap-2">
                <!-- Network Status Indicator -->
                <span id="simoli-network-indicator" class="badge bg-success d-inline-flex align-items-center gap-1.5 px-2.5 py-1.5 shadow-sm rounded-pill" style="font-size: 0.72rem;">
                    <span class="spinner-grow spinner-grow-sm" style="width: 6px; height: 6px;" role="status"></span>
                    <span>Online</span>
                </span>

                <!-- Notification Status & Test Button -->
                <button type="button" id="simoli-notif-toggle-btn" class="btn btn-sm btn-outline-light rounded-pill px-2.5 py-1 fs-11 fw-semibold d-inline-flex align-items-center gap-1" onclick="SimoliNotify.testNotification()" title="Status & Uji Notifikasi PWA">
                    <i class="feather-bell"></i>
                    <span class="d-none d-sm-inline ms-1">Notifikasi</span>
                </button>

                <!-- Sync Trigger Button -->
                <button type="button" id="simoli-sync-btn" class="sync-btn" onclick="SimoliSync.pushPendingQueue(true)" title="Sinkronkan Data Offline">
                    <i class="feather-refresh-cw"></i>
                    <span id="simoli-queue-badge" class="badge bg-danger rounded-pill px-1.5 py-0.5" style="display: none; font-size: 0.65rem;">
                        <span id="simoli-queue-count">0</span>
                    </span>
                </button>

                @if(Auth::check())
                    <form action="{{ route('logout') }}" method="POST" class="d-inline ms-1" onsubmit="return confirm('Apakah Anda yakin ingin keluar dari aplikasi?');">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-outline-light rounded-circle p-0" title="Keluar Akun" style="width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center;">
                            <i class="feather-log-out" style="font-size: 14px;"></i>
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </header>

    <!-- Toast Notification Container -->
    <div id="simoli-toast-container"></div>

    <!-- Main Container -->
    <main class="operator-container pt-3">
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

    <!-- Bottom Navigation for Mobile & Desktop Dock -->
    <div class="operator-bottom-nav-wrapper">
        <nav class="operator-bottom-nav">
            <a href="{{ route('operator.index') }}" class="nav-item-link {{ Route::is('operator.index') ? 'active' : '' }}">
                <i class="feather-home"></i>
                <span>Beranda</span>
            </a>
            <a href="{{ route('operator.create') }}" class="nav-item-link {{ Route::is('operator.create') ? 'active' : '' }}">
                <i class="feather-file-plus"></i>
                <span>Input Laporan</span>
            </a>
            @if(Auth::check() && Auth::user()->canManageAlatBerat())
                <a href="{{ route('operator.alat-berat.index') }}" class="nav-item-link {{ Route::is('operator.alat-berat.*') ? 'active' : '' }}">
                    <i class="feather-settings"></i>
                    <span>Kelola Alat</span>
                </a>
            @endif
        </nav>
    </div>

    <!-- Scripts (Bootstrap 5.3.3 + Feather Icons) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/feather-icons/dist/feather.min.js"></script>
    
    <!-- PWA Offline-First Scripts -->
    <script src="/js/simoli-offline-db.js"></script>
    <script src="/js/simoli-sync-manager.js"></script>
    <script src="/js/simoli-notifications.js"></script>

    <script>
        if (window.feather) {
            feather.replace();
        }

        // Toast Helper
        window.showToastNotification = function(message, type = 'info') {
            const container = document.getElementById('simoli-toast-container');
            if (!container) return;

            const bgClass = type === 'success' ? 'bg-success text-white' :
                           type === 'danger' ? 'bg-danger text-white' :
                           type === 'warning' ? 'bg-warning text-dark' : 'bg-primary text-white';

            const toastEl = document.createElement('div');
            toastEl.className = `alert ${bgClass} simoli-toast d-flex align-items-center justify-content-between p-3 border-0`;
            toastEl.innerHTML = `
                <div class="d-flex align-items-center gap-2">
                    <i class="feather-${type === 'success' ? 'check-circle' : type === 'warning' ? 'alert-triangle' : 'info'}"></i>
                    <span class="fs-13 fw-semibold">${message}</span>
                </div>
                <button type="button" class="btn-close ${type === 'warning' ? '' : 'btn-close-white'} ms-2" onclick="this.parentElement.remove()"></button>
            `;
            container.appendChild(toastEl);
            if (window.feather) feather.replace();

            setTimeout(() => {
                toastEl.style.transition = 'opacity 0.5s ease';
                toastEl.style.opacity = '0';
                setTimeout(() => toastEl.remove(), 500);
            }, 4500);
        };

        // Cache Session Info into IndexedDB for Offline Sync
        @if(Auth::check())
            SimoliDB.init().then(() => {
                SimoliDB.setConfig('user_id', '{{ Auth::user()->ID }}');
                SimoliDB.setConfig('username', '{{ Auth::user()->username }}');
                SimoliDB.setConfig('user_pks_id', '{{ Auth::user()->id_pks }}');
                SimoliDB.setConfig('auth_token', '{{ base64_encode(Auth::user()->ID . ":" . Auth::user()->username) }}');
            });
        @endif

        // Register Service Worker for Offline Smartphone PWA
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('/sw.js')
                    .then((reg) => console.log('[PWA] Service Worker registered in scope:', reg.scope))
                    .catch((err) => console.warn('[PWA] Service Worker registration failed:', err));
            });
        }

        // Trigger PWA notification on flash success
        @if(session('success'))
            document.addEventListener('DOMContentLoaded', () => {
                if (window.SimoliNotify) {
                    SimoliNotify.send({
                        title: '📝 Berhasil Disimpan',
                        body: '{{ addslashes(session('success')) }}',
                        type: 'success',
                        tag: 'simoli-flash-' + Date.now(),
                        url: '{{ route('operator.index') }}'
                    });
                }
            });
        @endif
    </script>
    @yield('scripts')
</body>
</html>
