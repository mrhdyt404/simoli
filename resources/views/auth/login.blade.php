<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#16a34a">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-title" content="SIMOLI">
    <link rel="manifest" href="{{ asset('manifest.webmanifest') }}">
    <title>SIMOLI — Login | PTPN IV</title>
    <link rel="icon" href="{{ asset('logo/Icon%20SIMOLI.png') }}" />

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;600;700;800;900&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Icons -->
    <link rel="stylesheet" href="{{ asset('duraluxadmin/assets/vendors/css/feather.min.css') }}">
    <link rel="stylesheet" href="{{ asset('duraluxadmin/assets/css/bootstrap.min.css') }}">

    <style>
        /* ================================================================
           SIMOLI LOGIN — HIJAU PTPN FULL REDESIGN
           ================================================================ */
        :root {
            --ptpn-600: #16a34a;
            --ptpn-700: #15803d;
            --ptpn-800: #166534;
            --ptpn-950: #052e16;
            --ptpn-300: #86efac;
            --ptpn-100: #dcfce7;
            --ptpn-50:  #f0fdf4;
            --ptpn-gold: #d4a017;
        }

        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        html, body {
            height: 100%;
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: #030d07;
            overflow: hidden;
        }

        /* === ANIMATED BACKGROUND === */
        .login-bg {
            position: fixed;
            inset: 0;
            background: linear-gradient(135deg, #030d07 0%, #0a2317 40%, #0e3b26 70%, #14532d 100%);
            z-index: 0;
        }

        /* Animated orbs */
        .login-bg::before {
            content: '';
            position: absolute;
            width: 600px; height: 600px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(22, 163, 74, 0.18) 0%, transparent 70%);
            top: -150px; right: -150px;
            animation: float1 8s ease-in-out infinite;
        }

        .login-bg::after {
            content: '';
            position: absolute;
            width: 400px; height: 400px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(74, 222, 128, 0.12) 0%, transparent 70%);
            bottom: -100px; left: -100px;
            animation: float2 10s ease-in-out infinite;
        }

        @keyframes float1 {
            0%, 100% { transform: translate(0, 0) scale(1); }
            50%       { transform: translate(-30px, 40px) scale(1.1); }
        }

        @keyframes float2 {
            0%, 100% { transform: translate(0, 0) scale(1); }
            50%       { transform: translate(40px, -30px) scale(1.08); }
        }

        /* Dot grid overlay */
        .login-dots {
            position: fixed;
            inset: 0;
            z-index: 1;
            background-image: radial-gradient(rgba(34, 197, 94, 0.08) 1px, transparent 1px);
            background-size: 32px 32px;
            pointer-events: none;
        }

        /* === LAYOUT WRAPPER === */
        .login-wrapper {
            position: relative;
            z-index: 2;
            min-height: 100vh;
            display: flex;
            align-items: stretch;
        }

        /* === LEFT PANEL — Branding === */
        .login-left {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: clamp(32px, 6vw, 80px);
            position: relative;
        }

        .login-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 6px 14px;
            border-radius: 50px;
            background: rgba(34, 197, 94, 0.12);
            border: 1px solid rgba(34, 197, 94, 0.3);
            color: var(--ptpn-300);
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            margin-bottom: 28px;
        }

        .login-badge-dot {
            width: 7px; height: 7px;
            border-radius: 50%;
            background: var(--ptpn-300);
            animation: pulseDot 2s ease-in-out infinite;
        }

        @keyframes pulseDot {
            0%, 100% { opacity: 1; transform: scale(1); }
            50%       { opacity: 0.5; transform: scale(0.8); }
        }

        .login-headline {
            font-family: 'Outfit', sans-serif;
            font-size: clamp(28px, 4vw + 14px, 56px);
            font-weight: 900;
            color: #ffffff;
            line-height: 1.1;
            letter-spacing: -1px;
            margin-bottom: 20px;
        }

        .login-headline .highlight {
            background: linear-gradient(135deg, #4ade80 0%, #86efac 50%, #d4a017 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .login-desc {
            font-size: clamp(13px, 1vw + 10px, 16px);
            color: rgba(187, 247, 208, 0.7);
            line-height: 1.7;
            max-width: 440px;
            margin-bottom: 40px;
        }

        /* Feature bullets */
        .login-features {
            display: flex;
            flex-direction: column;
            gap: 14px;
        }

        .login-feature-item {
            display: flex;
            align-items: center;
            gap: 12px;
            color: rgba(209, 250, 229, 0.8);
            font-size: 13.5px;
            font-weight: 500;
        }

        .login-feature-icon {
            width: 36px; height: 36px;
            border-radius: 10px;
            background: rgba(34, 197, 94, 0.15);
            border: 1px solid rgba(34, 197, 94, 0.25);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
            color: var(--ptpn-300);
            flex-shrink: 0;
        }

        /* PTPN Brand Footer on Left */
        .login-brand-footer {
            margin-top: 60px;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .login-brand-footer img {
            height: 48px;
            width: auto;
            object-fit: contain;
            filter: brightness(1.1) drop-shadow(0 2px 8px rgba(34, 197, 94, 0.3));
        }

        .login-brand-name {
            font-family: 'Outfit', sans-serif;
            font-size: 22px;
            font-weight: 900;
            color: #ffffff;
            letter-spacing: -0.5px;
        }

        .login-brand-sub {
            font-size: 11px;
            color: var(--ptpn-300);
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.8px;
        }

        /* === RIGHT PANEL — Form Card === */
        .login-right {
            width: clamp(380px, 42vw, 520px);
            flex-shrink: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: clamp(24px, 3vw, 48px);
        }

        .login-card {
            width: 100%;
            background: rgba(255, 255, 255, 0.97);
            backdrop-filter: blur(20px) saturate(1.5);
            -webkit-backdrop-filter: blur(20px) saturate(1.5);
            border-radius: 24px;
            box-shadow:
                0 30px 80px rgba(0, 0, 0, 0.3),
                inset 0 1px 0 rgba(255, 255, 255, 0.5);
            border: 1px solid rgba(255, 255, 255, 0.5);
            padding: clamp(28px, 3vw + 14px, 44px);
            position: relative;
            overflow: hidden;
            animation: slideCard 0.5s cubic-bezier(0.34, 1.56, 0.64, 1);
        }

        @keyframes slideCard {
            from { opacity: 0; transform: translateX(30px) scale(0.96); }
            to   { opacity: 1; transform: translateX(0) scale(1); }
        }

        /* Top green accent line on card */
        .login-card::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 3px;
            background: linear-gradient(90deg, var(--ptpn-700) 0%, var(--ptpn-300) 50%, var(--ptpn-700) 100%);
        }

        .login-card-header {
            text-align: center;
            margin-bottom: 32px;
        }

        .login-card-header img {
            height: 60px;
            width: auto;
            margin-bottom: 16px;
            object-fit: contain;
            filter: drop-shadow(0 2px 8px rgba(22, 163, 74, 0.2));
        }

        .login-card-title {
            font-family: 'Outfit', sans-serif;
            font-size: 22px;
            font-weight: 900;
            color: var(--ptpn-950);
            letter-spacing: -0.5px;
            margin-bottom: 6px;
        }

        .login-card-sub {
            font-size: 12.5px;
            color: #6b7280;
            font-weight: 500;
        }

        /* Error Alert */
        .login-alert {
            background: #fef2f2;
            border: 1px solid #fca5a5;
            border-left: 4px solid #ef4444;
            border-radius: 12px;
            padding: 12px 16px;
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 20px;
            animation: alertIn 0.3s ease;
        }

        @keyframes alertIn {
            from { opacity: 0; transform: translateY(-8px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        .login-alert i { color: #ef4444; font-size: 18px; flex-shrink: 0; }
        .login-alert-text { font-size: 12.5px; color: #991b1b; font-weight: 600; }
        .login-alert-close {
            margin-left: auto; background: none; border: none;
            cursor: pointer; color: #ef4444; font-size: 18px; padding: 0; flex-shrink: 0;
        }

        /* Success Alert */
        .login-success {
            background: var(--ptpn-50);
            border: 1px solid var(--ptpn-100);
            border-left: 4px solid var(--ptpn-600);
            border-radius: 12px;
            padding: 12px 16px;
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 20px;
        }

        .login-success i { color: var(--ptpn-600); font-size: 18px; }
        .login-success-text { font-size: 12.5px; color: var(--ptpn-800); font-weight: 600; }

        /* Form Groups */
        .login-form-group { margin-bottom: 20px; }

        .login-label {
            display: block;
            font-size: 12.5px;
            font-weight: 700;
            color: var(--ptpn-900);
            margin-bottom: 7px;
        }

        .login-input-wrap { position: relative; }

        .login-input-icon {
            position: absolute;
            left: 14px; top: 50%;
            transform: translateY(-50%);
            font-size: 17px;
            color: #9ca3af;
            pointer-events: none;
            transition: color 0.2s;
        }

        .login-input {
            width: 100%;
            padding: 12px 14px 12px 44px;
            border-radius: 14px;
            border: 1.5px solid #e5e7eb;
            font-size: 14px;
            font-family: 'Plus Jakarta Sans', sans-serif;
            color: #1a2e22;
            background: #f9fafb;
            transition: all 0.2s ease;
            outline: none;
        }

        .login-input:focus {
            border-color: var(--ptpn-500);
            background: #ffffff;
            box-shadow: 0 0 0 3px rgba(34, 197, 94, 0.1);
        }

        .login-input:focus + .login-input-icon-focus,
        .login-input-wrap:focus-within .login-input-icon {
            color: var(--ptpn-600);
        }

        .login-error-text {
            font-size: 11.5px;
            color: #ef4444;
            font-weight: 600;
            margin-top: 5px;
        }

        /* Submit Button */
        .login-submit-btn {
            width: 100%;
            padding: 14px;
            border-radius: 14px;
            border: none;
            background: linear-gradient(135deg, var(--ptpn-800) 0%, var(--ptpn-600) 50%, #22c55e 100%);
            color: #ffffff;
            font-family: 'Outfit', sans-serif;
            font-size: 16px;
            font-weight: 800;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: all 0.25s cubic-bezier(0.34, 1.56, 0.64, 1);
            box-shadow: 0 6px 20px rgba(22, 163, 74, 0.35);
            margin-top: 8px;
            letter-spacing: 0.2px;
        }

        .login-submit-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 28px rgba(22, 163, 74, 0.45);
        }

        .login-submit-btn:active { transform: translateY(0); }

        /* Footer Link */
        .login-footer-note {
            text-align: center;
            margin-top: 24px;
            font-size: 11px;
            color: #9ca3af;
        }

        .login-footer-note strong { color: var(--ptpn-700); }

        /* === MOBILE — only show form card centered === */
        @media (max-width: 767.98px) {
            html, body { overflow: auto; }
            .login-left { display: none; }
            .login-right {
                width: 100%;
                min-height: 100vh;
                padding: 20px 16px;
                align-items: flex-start;
                padding-top: 40px;
            }
            .login-card { border-radius: 20px; }
        }

        @media (max-width: 991.98px) and (min-width: 768px) {
            .login-left { padding: 32px; }
            .login-headline { font-size: clamp(26px, 3vw + 10px, 38px); }
            .login-features { display: none; }
        }
    </style>
</head>
<body>

<div class="login-bg"></div>
<div class="login-dots"></div>

<div class="login-wrapper">
    <!-- ======= LEFT PANEL: Branding ======= -->
    <section class="login-left" aria-label="SIMOLI Branding">
        <div class="login-badge">
            <span class="login-badge-dot"></span>
            Sistem Online & Real-Time
        </div>

        <h1 class="login-headline">
            Monitoring<br>
            <span class="highlight">Land Aplikasi</span><br>
            PTPN IV
        </h1>

        <p class="login-desc">
            Platform terintegrasi pengawasan pengaliran limbah, pemeliharaan kolam IPAL,
            dan operasional alat berat seluruh PKS PTPN IV secara real-time.
        </p>

        <div class="login-features">
            <div class="login-feature-item">
                <div class="login-feature-icon"><i class="feather-droplet"></i></div>
                <span>Monitoring Pengaliran Land Aplikasi 12 PKS</span>
            </div>
            <div class="login-feature-item">
                <div class="login-feature-icon"><i class="feather-tool"></i></div>
                <span>Pemeliharaan Kolam IPAL & Bed secara Digital</span>
            </div>
            <div class="login-feature-item">
                <div class="login-feature-icon"><i class="feather-truck"></i></div>
                <span>Log Operasional Alat Berat dengan GPS & Foto</span>
            </div>
            <div class="login-feature-item">
                <div class="login-feature-icon"><i class="feather-bar-chart-2"></i></div>
                <span>Laporan Eksekutif Format Resmi PTPN</span>
            </div>
        </div>

        <div class="login-brand-footer">
            <img src="{{ asset('logo/Logo%20SIMOLI.png') }}" alt="SIMOLI PTPN">
        </div>
    </section>

    <!-- ======= RIGHT PANEL: Login Form ======= -->
    <aside class="login-right" aria-label="Form Login">
        <div class="login-card">
            <div class="login-card-header">
                <img src="{{ asset('logo/Icon%20SIMOLI.png') }}" alt="SIMOLI Icon">
                <h2 class="login-card-title">Selamat Datang</h2>
                <p class="login-card-sub">Masukkan kredensial Anda untuk melanjutkan</p>
            </div>

            {{-- Success Message --}}
            @if(session('success'))
            <div class="login-success">
                <i class="feather-check-circle"></i>
                <span class="login-success-text">{{ session('success') }}</span>
            </div>
            @endif

            {{-- Error Message --}}
            @if($errors->has('login'))
            <div class="login-alert" id="loginAlert">
                <i class="feather-alert-circle"></i>
                <div class="login-alert-text">
                    <div style="font-weight:800;margin-bottom:2px;">Login Gagal</div>
                    {{ $errors->first('login') }}
                </div>
                <button class="login-alert-close" onclick="document.getElementById('loginAlert').remove()" aria-label="Tutup">×</button>
            </div>
            @endif

            <form action="{{ route('login') }}" method="POST" novalidate>
                @csrf

                {{-- Username --}}
                <div class="login-form-group">
                    <label class="login-label" for="username">Username</label>
                    <div class="login-input-wrap">
                        <input
                            type="text"
                            id="username"
                            name="username"
                            class="login-input @error('username') is-invalid @enderror"
                            placeholder="Masukkan username Anda"
                            value="{{ old('username') }}"
                            autocomplete="username"
                            autofocus
                            required
                        >
                        <i class="feather-user login-input-icon"></i>
                    </div>
                    @error('username')
                    <p class="login-error-text">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Password --}}
                <div class="login-form-group">
                    <label class="login-label" for="password">Password</label>
                    <div class="login-input-wrap">
                        <input
                            type="password"
                            id="password"
                            name="password"
                            class="login-input @error('password') is-invalid @enderror"
                            placeholder="Masukkan password Anda"
                            autocomplete="current-password"
                            required
                        >
                        <i class="feather-lock login-input-icon"></i>
                    </div>
                    @error('password')
                    <p class="login-error-text">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Submit --}}
                <button type="submit" class="login-submit-btn">
                    <i class="feather-log-in"></i>
                    Masuk ke SIMOLI
                </button>
            </form>

            <p class="login-footer-note">
                <strong>SIMOLI</strong> &copy; {{ date('Y') }} &bull; PTPN IV &bull; Divisi Land Aplikasi
            </p>
        </div>
    </aside>
</div>

<!-- PWA Service Worker Registration -->
<script>
    if ('serviceWorker' in navigator) {
        window.addEventListener('load', function() {
            navigator.serviceWorker.register('/sw.js').catch(function() {});
        });
    }
</script>
</body>
</html>