<!DOCTYPE html>
<html lang="id" class="simoli-app">
<head>
    <meta charset="utf-8" />
    <meta http-equiv="x-ua-compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <meta name="theme-color" content="#16a34a">
    <meta name="description" content="SIMOLI — Sistem Informasi Monitoring Limbah & Land Aplikasi PTPN IV">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="SIMOLI">
    <link rel="manifest" href="{{ asset('manifest.webmanifest') }}">
    <title>SIMOLI — @yield('title', 'Dashboard') | PTPN IV</title>
    <link rel="icon" type="image/png" href="{{ asset('logo/Icon%20SIMOLI.png') }}" />
    <link rel="apple-touch-icon" href="{{ asset('logo/Icon%20SIMOLI.png') }}" />

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&family=Plus+Jakarta+Sans:ital,wght@0,400;0,500;0,600;0,700;0,800;1,400&display=swap" rel="stylesheet">

    <!-- Bootstrap & Admin Theme -->
    <link rel="stylesheet" href="{{ asset('duraluxadmin/assets/css/bootstrap.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('duraluxadmin/assets/vendors/css/vendors.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('duraluxadmin/assets/vendors/css/feather.min.css') }}" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('duraluxadmin/assets/vendors/css/daterangepicker.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('duraluxadmin/assets/css/theme.min.css') }}" />

    @yield('styles')

    <style>
        /* ================================================================
           SIMOLI DESIGN SYSTEM — HIJAU PTPN GLOSSY GRADIENT
           Version 3.0 | PTPN IV Land Application Monitoring System
           ================================================================ */

        /* === DESIGN TOKENS === */
        :root {
            /* PTPN Green Palette */
            --ptpn-50:  #f0fdf4;
            --ptpn-100: #dcfce7;
            --ptpn-200: #bbf7d0;
            --ptpn-300: #86efac;
            --ptpn-400: #4ade80;
            --ptpn-500: #22c55e;
            --ptpn-600: #16a34a;
            --ptpn-700: #15803d;
            --ptpn-800: #166534;
            --ptpn-900: #14532d;
            --ptpn-950: #052e16;

            /* Gold Accent */
            --ptpn-gold:    #d4a017;
            --ptpn-gold-lt: #fef3c7;

            /* Sidebar gradient stops */
            --sidebar-from:  #030d07;
            --sidebar-mid:   #0a2317;
            --sidebar-to:    #0e3b26;

            /* Typography */
            --font-main:    'Plus Jakarta Sans', sans-serif;
            --font-heading: 'Outfit', sans-serif;

            /* Layout */
            --sidebar-width:     264px;
            --sidebar-mini:      72px;
            --header-height:     68px;
            --content-radius:    20px;
            --card-radius:       16px;
            --pill-radius:       50px;

            /* Shadows */
            --shadow-sm:  0 1px 4px rgba(0,0,0,.06);
            --shadow-md:  0 4px 16px rgba(0,0,0,.08);
            --shadow-lg:  0 10px 32px rgba(0,0,0,.10);
            --shadow-glow-green: 0 4px 20px rgba(22,163,74,.30);

            /* Transitions */
            --ease-spring: cubic-bezier(0.34, 1.56, 0.64, 1);
            --ease-smooth: cubic-bezier(0.16, 1, 0.3, 1);
            --dur-fast: 200ms;
            --dur-normal: 300ms;
            --dur-slow: 500ms;
        }

        /* === RESET & BASE === */
        *, *::before, *::after { box-sizing: border-box; }

        html, body {
            font-family: var(--font-main);
            background: var(--ptpn-50);
            color: #1a2e22;
            -webkit-font-smoothing: antialiased;
            overflow-x: hidden;
        }

        h1, h2, h3, h4, h5, h6 { font-family: var(--font-heading); }

        /* === KEYFRAME ANIMATIONS === */
        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(14px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to   { opacity: 1; }
        }

        @keyframes slideInLeft {
            from { opacity: 0; transform: translateX(-20px); }
            to   { opacity: 1; transform: translateX(0); }
        }

        @keyframes pulseGreen {
            0%   { box-shadow: 0 0 0 0 rgba(34, 197, 94, 0.6); }
            70%  { box-shadow: 0 0 0 8px rgba(34, 197, 94, 0); }
            100% { box-shadow: 0 0 0 0 rgba(34, 197, 94, 0); }
        }

        @keyframes shimmerGreen {
            0%   { background-position: -200% center; }
            100% { background-position: 200% center; }
        }

        @keyframes rotateSlow {
            from { transform: rotate(0deg); }
            to   { transform: rotate(360deg); }
        }

        /* === SIDEBAR / NAV === */
        .simoli-sidebar {
            position: fixed;
            top: 0; left: 0;
            height: 100vh;
            width: var(--sidebar-width);
            background: linear-gradient(180deg,
                var(--sidebar-from) 0%,
                var(--sidebar-mid)  40%,
                var(--sidebar-to)   100%);
            border-right: 1px solid rgba(34, 197, 94, 0.12);
            box-shadow: 4px 0 32px rgba(0, 0, 0, 0.35);
            display: flex;
            flex-direction: column;
            z-index: 1040;
            transition: width var(--dur-normal) var(--ease-smooth),
                        transform var(--dur-normal) var(--ease-smooth);
            overflow: hidden;
        }

        /* Glossy top-edge shine */
        .simoli-sidebar::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 1px;
            background: linear-gradient(90deg,
                transparent 0%, rgba(34, 197, 94, 0.5) 50%, transparent 100%);
        }

        /* Decorative radial glow in sidebar */
        .simoli-sidebar::after {
            content: '';
            position: absolute;
            bottom: -80px; right: -80px;
            width: 280px; height: 280px;
            border-radius: 50%;
            background: radial-gradient(circle,
                rgba(22, 163, 74, 0.15) 0%,
                transparent 70%);
            pointer-events: none;
        }

        /* --- Sidebar Brand Header --- */
        .sidebar-brand {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 18px;
            height: var(--header-height);
            border-bottom: 1px solid rgba(255,255,255,0.06);
            flex-shrink: 0;
            gap: 10px;
        }

        .sidebar-brand-logo {
            display: flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
            overflow: hidden;
            flex: 1;
            min-width: 0;
        }

        .sidebar-brand-logo img {
            height: 44px;
            width: auto;
            object-fit: contain;
            filter: drop-shadow(0 2px 8px rgba(34, 197, 94, 0.35));
            flex-shrink: 0;
        }

        .sidebar-brand-logo img.logo-sm {
            height: 38px;
        }

        .sidebar-logo-text {
            display: flex;
            flex-direction: column;
            white-space: nowrap;
            overflow: hidden;
        }

        .sidebar-logo-text .logo-name {
            font-family: var(--font-heading);
            font-size: 18px;
            font-weight: 900;
            color: #ffffff;
            letter-spacing: -0.5px;
            line-height: 1.1;
        }

        .sidebar-logo-text .logo-sub {
            font-size: 9.5px;
            font-weight: 600;
            color: var(--ptpn-400);
            text-transform: uppercase;
            letter-spacing: 0.8px;
        }

        /* --- Sidebar Toggle Button --- */
        .sidebar-toggle-btn {
            width: 34px; height: 34px;
            border-radius: 10px;
            background: rgba(255,255,255,0.07);
            border: 1px solid rgba(255,255,255,0.13);
            color: var(--ptpn-400);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all var(--dur-fast) ease;
            flex-shrink: 0;
        }

        .sidebar-toggle-btn:hover {
            background: rgba(34, 197, 94, 0.2);
            border-color: rgba(34, 197, 94, 0.4);
            color: #ffffff;
            box-shadow: 0 0 12px rgba(34, 197, 94, 0.3);
        }

        /* --- Nav Scrollable Body --- */
        .sidebar-nav {
            flex: 1;
            overflow-y: auto;
            overflow-x: hidden;
            padding: 12px 0 24px;
            scrollbar-width: thin;
            scrollbar-color: rgba(34, 197, 94, 0.2) transparent;
        }

        .sidebar-nav::-webkit-scrollbar { width: 4px; }
        .sidebar-nav::-webkit-scrollbar-track { background: transparent; }
        .sidebar-nav::-webkit-scrollbar-thumb {
            background: rgba(34, 197, 94, 0.2);
            border-radius: 4px;
        }

        /* --- Nav Section Labels --- */
        .nav-section-label {
            padding: 18px 22px 6px;
            font-size: 9.5px;
            font-weight: 800;
            color: rgba(134, 239, 172, 0.5);
            text-transform: uppercase;
            letter-spacing: 1.2px;
            white-space: nowrap;
            overflow: hidden;
        }

        /* --- Nav Items --- */
        .nav-item-simoli { list-style: none; padding: 0; margin: 0; }

        .nav-link-simoli {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 16px;
            margin: 3px 10px;
            border-radius: 12px;
            color: rgba(209, 250, 229, 0.75);
            font-size: 13.5px;
            font-weight: 600;
            text-decoration: none;
            transition: all var(--dur-fast) var(--ease-smooth);
            white-space: nowrap;
            overflow: hidden;
            position: relative;
        }

        .nav-link-simoli:hover {
            color: #ffffff;
            background: rgba(34, 197, 94, 0.12);
            transform: translateX(4px);
        }

        .nav-link-simoli.active {
            background: linear-gradient(135deg, #16a34a 0%, #22c55e 60%, #4ade80 100%);
            color: #ffffff;
            box-shadow: var(--shadow-glow-green), inset 0 1px 0 rgba(255,255,255,0.2);
            font-weight: 700;
            transform: translateX(0);
        }

        .nav-link-simoli.active::before {
            content: '';
            position: absolute;
            left: -10px; top: 50%;
            transform: translateY(-50%);
            width: 4px; height: 60%;
            background: var(--ptpn-400);
            border-radius: 0 4px 4px 0;
        }

        /* Nav Icon Pill */
        .nav-icon-pill {
            width: 34px; height: 34px;
            border-radius: 10px;
            background: rgba(255,255,255,0.07);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
            color: var(--ptpn-300);
            flex-shrink: 0;
            transition: all var(--dur-fast) ease;
        }

        .nav-link-simoli.active .nav-icon-pill {
            background: rgba(255,255,255,0.2);
            color: #ffffff;
        }

        .nav-link-simoli:hover .nav-icon-pill {
            background: rgba(34, 197, 94, 0.15);
            color: var(--ptpn-300);
        }

        /* Submenu arrow */
        .nav-arrow {
            margin-left: auto;
            font-size: 13px;
            color: rgba(134, 239, 172, 0.4);
            transition: transform var(--dur-fast) ease;
            flex-shrink: 0;
        }

        .nxl-hasmenu.active > .nav-link-simoli .nav-arrow,
        .nxl-hasmenu > .nav-link-simoli[aria-expanded="true"] .nav-arrow {
            transform: rotate(90deg);
            color: var(--ptpn-400);
        }

        /* Submenu */
        .nav-submenu {
            list-style: none;
            padding: 4px 0 8px;
            margin: 0 12px 4px 12px;
            background: rgba(0,0,0,0.18);
            border-radius: 12px;
            border-left: 2px solid rgba(34, 197, 94, 0.25);
            overflow: hidden;
        }

        .nav-submenu .nav-link-simoli {
            font-size: 12.5px;
            padding: 8px 14px 8px 20px;
            margin: 2px 8px;
            color: rgba(187, 247, 208, 0.65);
        }

        .nav-submenu .nav-link-simoli:hover {
            color: #ffffff;
            background: rgba(34, 197, 94, 0.1);
        }

        .nav-submenu .nav-link-simoli.active {
            color: var(--ptpn-300);
            background: rgba(34, 197, 94, 0.15);
            box-shadow: none;
            font-weight: 700;
        }

        /* --- Sidebar Footer / User Strip --- */
        .sidebar-footer {
            padding: 14px;
            border-top: 1px solid rgba(255,255,255,0.06);
            flex-shrink: 0;
        }

        .sidebar-user-strip {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 12px;
            border-radius: 12px;
            background: rgba(255,255,255,0.05);
            border: 1px solid rgba(34, 197, 94, 0.12);
            cursor: pointer;
            transition: all var(--dur-fast) ease;
            overflow: hidden;
        }

        .sidebar-user-strip:hover {
            background: rgba(34, 197, 94, 0.12);
            border-color: rgba(34, 197, 94, 0.3);
        }

        .sidebar-user-avatar {
            width: 36px; height: 36px;
            border-radius: 50%;
            background: linear-gradient(135deg, #16a34a 0%, #4ade80 100%);
            color: #ffffff;
            font-weight: 800;
            font-size: 13px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            box-shadow: 0 2px 8px rgba(22,163,74,0.35);
        }

        .sidebar-user-info .user-name {
            font-size: 12.5px;
            font-weight: 700;
            color: #e2f5ea;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 130px;
        }

        .sidebar-user-info .user-role {
            font-size: 10px;
            color: var(--ptpn-400);
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* ================================================================
           HEADER / TOPBAR
           ================================================================ */
        .simoli-header {
            position: fixed;
            top: 0;
            left: var(--sidebar-width);
            right: 0;
            height: var(--header-height);
            background: rgba(255, 255, 255, 0.92);
            backdrop-filter: blur(20px) saturate(1.8);
            -webkit-backdrop-filter: blur(20px) saturate(1.8);
            border-bottom: 1px solid rgba(22, 163, 74, 0.12);
            box-shadow: 0 2px 20px rgba(22, 163, 74, 0.06);
            display: flex;
            align-items: center;
            padding: 0 24px;
            gap: 16px;
            z-index: 1030;
            transition: left var(--dur-normal) var(--ease-smooth);
        }

        /* Top green accent strip */
        .simoli-header::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 2px;
            background: linear-gradient(90deg,
                var(--ptpn-600) 0%, var(--ptpn-400) 50%, var(--ptpn-700) 100%);
        }

        /* --- Header Mobile Menu Toggle --- */
        .header-mobile-toggle {
            display: none;
            width: 38px; height: 38px;
            border-radius: 10px;
            background: rgba(22, 163, 74, 0.08);
            border: 1px solid rgba(22, 163, 74, 0.2);
            color: var(--ptpn-700);
            align-items: center;
            justify-content: center;
            cursor: pointer;
            flex-shrink: 0;
            transition: all var(--dur-fast) ease;
        }

        .header-mobile-toggle:hover {
            background: var(--ptpn-600);
            color: #fff;
        }

        /* --- Header Brand Breadcrumb --- */
        .header-context {
            display: flex;
            flex-direction: column;
            min-width: 0;
        }

        .header-page-label {
            font-family: var(--font-heading);
            font-size: clamp(14px, 1.2vw + 9px, 18px);
            font-weight: 800;
            color: #1a2e22;
            line-height: 1.2;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .header-breadcrumb {
            display: flex;
            align-items: center;
            gap: 4px;
            font-size: 11px;
            color: #6b7280;
            margin: 0;
            padding: 0;
            list-style: none;
            flex-wrap: wrap;
        }

        .header-breadcrumb a {
            color: var(--ptpn-700);
            font-weight: 600;
            text-decoration: none;
        }

        .header-breadcrumb a:hover { color: var(--ptpn-600); text-decoration: underline; }

        .header-breadcrumb .separator { color: #d1d5db; }

        /* --- Header Right Actions --- */
        .header-actions {
            margin-left: auto;
            display: flex;
            align-items: center;
            gap: 10px;
            flex-shrink: 0;
        }

        /* Header Icon Button */
        .hdr-icon-btn {
            width: 40px; height: 40px;
            border-radius: 12px;
            background: rgba(22, 163, 74, 0.06);
            border: 1px solid rgba(22, 163, 74, 0.15);
            color: var(--ptpn-700);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 17px;
            text-decoration: none;
            cursor: pointer;
            transition: all var(--dur-fast) ease;
        }

        .hdr-icon-btn:hover {
            background: var(--ptpn-600);
            border-color: var(--ptpn-600);
            color: #ffffff;
            transform: translateY(-2px);
            box-shadow: var(--shadow-glow-green);
        }

        /* Header User Avatar Dropdown Trigger */
        .hdr-user-trigger {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 5px 12px 5px 5px;
            border-radius: var(--pill-radius);
            background: rgba(22, 163, 74, 0.06);
            border: 1px solid rgba(22, 163, 74, 0.18);
            cursor: pointer;
            transition: all var(--dur-fast) ease;
        }

        .hdr-user-trigger:hover {
            background: rgba(22, 163, 74, 0.12);
            border-color: rgba(22, 163, 74, 0.4);
        }

        .hdr-user-avatar {
            width: 34px; height: 34px;
            border-radius: 50%;
            background: linear-gradient(135deg, #16a34a 0%, #4ade80 100%);
            color: #fff;
            font-weight: 800;
            font-size: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            box-shadow: 0 2px 8px rgba(22,163,74,.35);
        }

        .hdr-user-name {
            font-size: 12.5px;
            font-weight: 700;
            color: #1a2e22;
            white-space: nowrap;
        }

        .hdr-user-role {
            font-size: 10px;
            color: var(--ptpn-700);
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.4px;
        }

        /* User Dropdown */
        .user-dropdown-menu {
            border-radius: 18px !important;
            border: 1px solid rgba(22, 163, 74, 0.15) !important;
            box-shadow: 0 20px 50px rgba(15, 23, 42, 0.14) !important;
            padding: 14px !important;
            min-width: 240px !important;
            background: #ffffff !important;
        }

        .user-dropdown-header {
            padding-bottom: 12px;
            margin-bottom: 10px;
            border-bottom: 1px solid var(--ptpn-100);
        }

        .btn-logout-green {
            background: linear-gradient(135deg, #dcfce7 0%, #bbf7d0 100%);
            color: var(--ptpn-800);
            font-weight: 700;
            border-radius: 10px;
            padding: 9px 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            border: 1px solid var(--ptpn-200);
            width: 100%;
            cursor: pointer;
            transition: all var(--dur-fast) ease;
            font-size: 13px;
        }

        .btn-logout-green:hover {
            background: linear-gradient(135deg, #16a34a 0%, #22c55e 100%);
            color: #fff;
            border-color: var(--ptpn-600);
            box-shadow: var(--shadow-glow-green);
        }

        /* ================================================================
           MAIN CONTENT SHELL
           ================================================================ */
        .simoli-main {
            margin-left: var(--sidebar-width);
            padding-top: var(--header-height);
            min-height: 100vh;
            transition: margin-left var(--dur-normal) var(--ease-smooth);
        }

        .simoli-content {
            padding: clamp(16px, 2vw + 10px, 28px);
            animation: fadeUp 0.35s var(--ease-smooth);
        }

        /* ================================================================
           PAGE HEADER STRIP
           ================================================================ */
        .page-hero-strip {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 12px;
            margin-bottom: clamp(16px, 2vw + 10px, 24px);
        }

        .page-hero-strip h1 {
            font-family: var(--font-heading);
            font-size: clamp(18px, 1.5vw + 11px, 24px);
            font-weight: 800;
            color: var(--ptpn-950);
            margin: 0;
            letter-spacing: -0.5px;
        }

        .page-hero-strip p {
            font-size: 12.5px;
            color: #6b7280;
            margin: 2px 0 0;
        }

        /* ================================================================
           SHARED CARD SYSTEM
           ================================================================ */
        .simoli-card {
            background: #ffffff;
            border-radius: var(--card-radius);
            border: 1px solid rgba(22, 163, 74, 0.1);
            box-shadow: var(--shadow-sm);
            transition: box-shadow var(--dur-fast) ease, transform var(--dur-fast) ease;
        }

        .simoli-card:hover {
            box-shadow: var(--shadow-md);
        }

        .simoli-card-header {
            padding: 18px 20px 14px;
            border-bottom: 1px solid var(--ptpn-50);
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
        }

        .simoli-card-title {
            font-family: var(--font-heading);
            font-size: 14.5px;
            font-weight: 700;
            color: var(--ptpn-950);
            display: flex;
            align-items: center;
            gap: 8px;
            margin: 0;
        }

        .simoli-card-body { padding: 20px; }

        /* === Green Icon Pill === */
        .icon-pill {
            width: 42px; height: 42px;
            border-radius: 12px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            flex-shrink: 0;
        }

        .icon-pill-green  { background: linear-gradient(135deg, var(--ptpn-100), var(--ptpn-50)); color: var(--ptpn-700); border: 1px solid var(--ptpn-200); }
        .icon-pill-gold   { background: linear-gradient(135deg, #fef3c7, #fffbeb); color: #b45309; border: 1px solid #fde68a; }
        .icon-pill-blue   { background: linear-gradient(135deg, #dbeafe, #eff6ff); color: #1d4ed8; border: 1px solid #bfdbfe; }
        .icon-pill-rose   { background: linear-gradient(135deg, #ffe4e6, #fff1f2); color: #be123c; border: 1px solid #fecdd3; }
        .icon-pill-purple { background: linear-gradient(135deg, #f3e8ff, #faf5ff); color: #7c3aed; border: 1px solid #e9d5ff; }
        .icon-pill-teal   { background: linear-gradient(135deg, #ccfbf1, #f0fdfa); color: #0f766e; border: 1px solid #99f6e4; }

        /* === Status Pills / Badges === */
        .status-pill {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 4px 10px;
            border-radius: var(--pill-radius);
            font-size: 11px;
            font-weight: 700;
            white-space: nowrap;
        }

        .status-pill-success { background: var(--ptpn-100); color: var(--ptpn-800); border: 1px solid var(--ptpn-200); }
        .status-pill-warning { background: #fef3c7; color: #92400e; border: 1px solid #fde68a; }
        .status-pill-danger  { background: #fee2e2; color: #991b1b; border: 1px solid #fca5a5; }
        .status-pill-info    { background: #dbeafe; color: #1e40af; border: 1px solid #bfdbfe; }
        .status-pill-muted   { background: #f1f5f9; color: #475569; border: 1px solid #e2e8f0; }

        /* === Buttons === */
        .btn-ptpn {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            padding: 9px 18px;
            border-radius: 12px;
            font-size: 13px;
            font-weight: 700;
            cursor: pointer;
            text-decoration: none;
            transition: all var(--dur-fast) var(--ease-spring);
            border: none;
            white-space: nowrap;
        }

        .btn-ptpn-primary {
            background: linear-gradient(135deg, var(--ptpn-700) 0%, var(--ptpn-500) 100%);
            color: #ffffff;
            box-shadow: 0 4px 14px rgba(22, 163, 74, 0.3);
        }

        .btn-ptpn-primary:hover {
            background: linear-gradient(135deg, var(--ptpn-600) 0%, var(--ptpn-400) 100%);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(22, 163, 74, 0.4);
            color: #ffffff;
        }

        .btn-ptpn-outline {
            background: transparent;
            border: 1.5px solid rgba(22, 163, 74, 0.3);
            color: var(--ptpn-700);
        }

        .btn-ptpn-outline:hover {
            background: var(--ptpn-50);
            border-color: var(--ptpn-600);
            color: var(--ptpn-800);
        }

        /* === Tables === */
        .table-simoli {
            border-collapse: separate;
            border-spacing: 0;
            width: 100%;
            font-size: clamp(11.5px, 0.7vw + 8px, 13.5px);
        }

        .table-simoli thead th {
            background: var(--ptpn-950);
            color: var(--ptpn-300);
            font-size: 10.5px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.7px;
            padding: 13px 16px;
            border: none;
            white-space: nowrap;
        }

        .table-simoli thead th:first-child { border-radius: 12px 0 0 0; }
        .table-simoli thead th:last-child  { border-radius: 0 12px 0 0; }

        .table-simoli tbody td {
            padding: 13px 16px;
            border-bottom: 1px solid var(--ptpn-50);
            vertical-align: middle;
            color: #374151;
        }

        .table-simoli tbody tr:hover td {
            background: rgba(22, 163, 74, 0.03);
        }

        .table-simoli tbody tr:last-child td { border-bottom: none; }

        /* === Forms === */
        .form-simoli-label {
            font-size: 12.5px;
            font-weight: 700;
            color: var(--ptpn-900);
            margin-bottom: 6px;
            display: block;
        }

        .form-simoli-control {
            border-radius: 12px;
            border: 1.5px solid rgba(22, 163, 74, 0.2);
            padding: 10px 14px;
            font-size: 13.5px;
            color: #1a2e22;
            transition: border-color var(--dur-fast) ease, box-shadow var(--dur-fast) ease;
            width: 100%;
        }

        .form-simoli-control:focus {
            border-color: var(--ptpn-500);
            box-shadow: 0 0 0 3px rgba(34, 197, 94, 0.12);
            outline: none;
        }

        /* === Alert Toasts === */
        .alert-simoli {
            border-radius: 14px;
            padding: 14px 18px;
            display: flex;
            align-items: flex-start;
            gap: 12px;
            margin-bottom: 16px;
            border: 1px solid transparent;
            animation: fadeUp 0.3s var(--ease-smooth);
        }

        .alert-simoli-success {
            background: linear-gradient(135deg, var(--ptpn-50), #fff);
            border-color: var(--ptpn-200);
            color: var(--ptpn-900);
            border-left: 4px solid var(--ptpn-500);
        }

        .alert-simoli-danger {
            background: linear-gradient(135deg, #fef2f2, #fff);
            border-color: #fca5a5;
            color: #991b1b;
            border-left: 4px solid #ef4444;
        }

        /* ================================================================
           MINIMIZED / COLLAPSED SIDEBAR STATE
           ================================================================ */
        .simoli-app.sidebar-mini .simoli-sidebar {
            width: var(--sidebar-mini);
        }

        .simoli-app.sidebar-mini .simoli-header {
            left: var(--sidebar-mini);
        }

        .simoli-app.sidebar-mini .simoli-main {
            margin-left: var(--sidebar-mini);
        }

        .simoli-app.sidebar-mini .sidebar-logo-text,
        .simoli-app.sidebar-mini .sidebar-nav .nav-label-text,
        .simoli-app.sidebar-mini .nav-section-label,
        .simoli-app.sidebar-mini .sidebar-user-info,
        .simoli-app.sidebar-mini .hdr-user-name,
        .simoli-app.sidebar-mini .hdr-user-role {
            display: none !important;
        }

        .simoli-app.sidebar-mini .sidebar-brand-logo img.logo-lg {
            display: none;
        }

        .simoli-app.sidebar-mini .sidebar-brand-logo img.logo-sm {
            display: block;
        }

        .simoli-app.sidebar-mini .sidebar-brand {
            justify-content: center;
            padding: 0 10px;
        }

        .simoli-app.sidebar-mini .sidebar-brand-logo {
            flex: 0 0 auto;
        }

        .simoli-app.sidebar-mini .nav-link-simoli {
            justify-content: center;
            margin: 3px 6px;
            padding: 10px;
        }

        .simoli-app.sidebar-mini .nav-link-simoli .nav-arrow { display: none; }
        .simoli-app.sidebar-mini .nav-submenu { display: none !important; }
        .simoli-app.sidebar-mini .sidebar-user-strip { justify-content: center; padding: 10px; }

        /* ================================================================
           MOBILE OVERLAY
           ================================================================ */
        .sidebar-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(5, 46, 22, 0.55);
            z-index: 1035;
            backdrop-filter: blur(2px);
        }

        /* ================================================================
           RESPONSIVE BREAKPOINTS
           ================================================================ */
        @media (max-width: 1199.98px) {
            :root { --sidebar-width: 240px; }
        }

        @media (max-width: 991.98px) {
            .simoli-sidebar {
                transform: translateX(calc(-1 * var(--sidebar-width)));
            }

            .simoli-app.mobile-open .simoli-sidebar {
                transform: translateX(0);
            }

            .simoli-app.mobile-open .sidebar-overlay {
                display: block;
            }

            .simoli-header {
                left: 0 !important;
            }

            .simoli-main {
                margin-left: 0 !important;
            }

            .header-mobile-toggle { display: inline-flex; }

            .hdr-user-name, .hdr-user-role { display: none; }

            .hdr-user-trigger {
                padding: 5px;
                border-radius: 50%;
            }
        }

        @media (max-width: 575.98px) {
            .simoli-content { padding: 14px 12px; }
            .header-breadcrumb { display: none; }
        }

        /* ================================================================
           DARK MODE SUPPORT
           ================================================================ */
        html.app-skin-dark body {
            background: #030d07;
            color: #e2f5ea;
        }

        html.app-skin-dark .simoli-header {
            background: rgba(5, 46, 22, 0.92) !important;
            border-bottom-color: rgba(34, 197, 94, 0.15) !important;
        }

        html.app-skin-dark .header-page-label,
        html.app-skin-dark .hdr-user-name { color: #e2f5ea; }

        html.app-skin-dark .simoli-card,
        html.app-skin-dark .user-dropdown-menu {
            background: #0a2317 !important;
            border-color: rgba(34, 197, 94, 0.15) !important;
        }

        html.app-skin-dark .simoli-card-header {
            border-bottom-color: rgba(34, 197, 94, 0.1) !important;
        }

        html.app-skin-dark .simoli-card-title,
        html.app-skin-dark .page-hero-strip h1 { color: #d1fae5; }

        html.app-skin-dark .table-simoli tbody td {
            color: #d1fae5;
            border-bottom-color: rgba(34, 197, 94, 0.1);
        }

        html.app-skin-dark .table-simoli tbody tr:hover td {
            background: rgba(34, 197, 94, 0.05);
        }

        html.app-skin-dark .hdr-icon-btn {
            background: rgba(34, 197, 94, 0.08);
            border-color: rgba(34, 197, 94, 0.2);
            color: var(--ptpn-400);
        }

        html.app-skin-dark .hdr-user-trigger {
            background: rgba(34, 197, 94, 0.08);
            border-color: rgba(34, 197, 94, 0.2);
        }

        /* Pagination protection */
        .pagination svg, .page-item svg {
            max-width: 16px !important; max-height: 16px !important;
            width: 16px !important; height: 16px !important;
        }

        /* Print override */
        @media print {
            .simoli-sidebar, .simoli-header { display: none !important; }
            .simoli-main { margin-left: 0 !important; padding-top: 0 !important; }
        }
    </style>
</head>

<body>
    {{-- ============================================================
         SIDEBAR MOBILE OVERLAY
         ============================================================ --}}
    <div class="sidebar-overlay" id="sidebarOverlay" onclick="closeMobileSidebar()"></div>

    {{-- ============================================================
         MAIN NAVIGATION SIDEBAR
         ============================================================ --}}
    <nav class="simoli-sidebar" id="simoliSidebar" role="navigation" aria-label="Menu Utama SIMOLI">

        {{-- Brand Header --}}
        <div class="sidebar-brand">
            <a href="{{ url('/dashboard') }}" class="sidebar-brand-logo" title="SIMOLI Dashboard">
                <img src="{{ asset('logo/Logo%20SIMOLI.png') }}" alt="SIMOLI" class="logo-lg" />
                <img src="{{ asset('logo/Icon%20SIMOLI.png') }}" alt="SIMOLI" class="logo-sm" style="display: none;" />
                <div class="sidebar-logo-text">
                    <span class="logo-name">SIMOLI</span>
                    <span class="logo-sub">PTPN IV &bull; Land Aplikasi</span>
                </div>
            </a>
            <button type="button" class="sidebar-toggle-btn" id="sidebarToggleBtn"
                    title="Toggle Sidebar" aria-label="Toggle Sidebar" aria-expanded="true">
                <i class="feather-sidebar toggle-icon" id="sidebarToggleIcon"></i>
            </button>
        </div>

        {{-- Navigation Links --}}
        <div class="sidebar-nav" role="list">
            <ul class="nav-item-simoli" role="listitem">
                {{-- Section: Beranda --}}
                <li class="nav-section-label">Beranda</li>

                {{-- Dashboard --}}
                <li>
                    <a href="{{ url('/dashboard') }}"
                       class="nav-link-simoli {{ request()->is('dashboard') ? 'active' : '' }}"
                       role="menuitem">
                        <span class="nav-icon-pill"><i class="feather-grid"></i></span>
                        <span class="nav-label-text">Dashboard Eksekutif</span>
                    </a>
                </li>

                @if(Auth::user()->isAdmin())
                {{-- Section: Monitoring (Admin) --}}
                <li class="nav-section-label">Laporan & Monitoring</li>

                <li class="nxl-hasmenu {{ request()->is('report-pengaliran*','report-pemeliharaan*','report-rencana*','report-alat-berat*','monitoring-alat-berat*') ? 'active' : '' }}">
                    <a href="javascript:void(0);" class="nav-link-simoli
                        {{ request()->is('report-pengaliran*','report-pemeliharaan*','report-rencana*','report-alat-berat*','monitoring-alat-berat*') ? 'active' : '' }}"
                       data-bs-toggle="collapse" data-bs-target="#submenu-laporan">
                        <span class="nav-icon-pill"><i class="feather-bar-chart-2"></i></span>
                        <span class="nav-label-text">Laporan SIMOLI</span>
                        <i class="feather-chevron-right nav-arrow"></i>
                    </a>
                    <ul class="nav-submenu collapse {{ request()->is('report-pengaliran*','report-pemeliharaan*','report-rencana*','report-alat-berat*','monitoring-alat-berat*') ? 'show' : '' }}"
                        id="submenu-laporan">
                        <li>
                            <a class="nav-link-simoli {{ request()->is('report-pengaliran*') ? 'active' : '' }}"
                               href="{{ route('report-pengaliran') }}">
                                <span class="nav-icon-pill" style="width:26px;height:26px;font-size:13px;"><i class="feather-droplet"></i></span>
                                <span class="nav-label-text">Pengaliran LA</span>
                            </a>
                        </li>
                        <li>
                            <a class="nav-link-simoli {{ request()->is('report-pemeliharaan*') ? 'active' : '' }}"
                               href="{{ route('report-pemeliharaan') }}">
                                <span class="nav-icon-pill" style="width:26px;height:26px;font-size:13px;"><i class="feather-tool"></i></span>
                                <span class="nav-label-text">Pemeliharaan</span>
                            </a>
                        </li>
                        <li>
                            <a class="nav-link-simoli {{ request()->is('report-rencana*') ? 'active' : '' }}"
                               href="{{ route('report-rencana') }}">
                                <span class="nav-icon-pill" style="width:26px;height:26px;font-size:13px;"><i class="feather-clipboard"></i></span>
                                <span class="nav-label-text">Rencana Tahunan</span>
                            </a>
                        </li>
                        <li>
                            <a class="nav-link-simoli {{ request()->is('report-alat-berat*') ? 'active' : '' }}"
                               href="{{ route('report-alat-berat') }}">
                                <span class="nav-icon-pill" style="width:26px;height:26px;font-size:13px;"><i class="feather-truck"></i></span>
                                <span class="nav-label-text">Laporan Alat Berat</span>
                            </a>
                        </li>
                        <li>
                            <a class="nav-link-simoli {{ request()->is('monitoring-alat-berat*') ? 'active' : '' }}"
                               href="{{ route('monitoring-alat-berat.index') }}">
                                <span class="nav-icon-pill" style="width:26px;height:26px;font-size:13px;"><i class="feather-activity"></i></span>
                                <span class="nav-label-text">Log Monitoring</span>
                            </a>
                        </li>
                    </ul>
                </li>

                {{-- Section: Master Data --}}
                <li class="nav-section-label">Kelola Data</li>

                <li class="nxl-hasmenu {{ request()->is('pengguna*','alat-berat*') ? 'active' : '' }}">
                    <a href="javascript:void(0);" class="nav-link-simoli
                        {{ request()->is('pengguna*','alat-berat*') ? 'active' : '' }}"
                       data-bs-toggle="collapse" data-bs-target="#submenu-master">
                        <span class="nav-icon-pill"><i class="feather-database"></i></span>
                        <span class="nav-label-text">Master Data</span>
                        <i class="feather-chevron-right nav-arrow"></i>
                    </a>
                    <ul class="nav-submenu collapse {{ request()->is('pengguna*','alat-berat*') ? 'show' : '' }}"
                        id="submenu-master">
                        <li>
                            <a class="nav-link-simoli {{ request()->is('alat-berat*') ? 'active' : '' }}"
                               href="{{ route('alat-berat.index') }}">
                                <span class="nav-icon-pill" style="width:26px;height:26px;font-size:13px;"><i class="feather-truck"></i></span>
                                <span class="nav-label-text">Master Alat Berat</span>
                            </a>
                        </li>
                        <li>
                            <a class="nav-link-simoli {{ request()->is('pengguna*') ? 'active' : '' }}"
                               href="{{ route('pengguna.index') }}">
                                <span class="nav-icon-pill" style="width:26px;height:26px;font-size:13px;"><i class="feather-users"></i></span>
                                <span class="nav-label-text">Data Pengguna</span>
                            </a>
                        </li>
                    </ul>
                </li>

                @else
                {{-- Section: Unit Operasional --}}
                <li class="nav-section-label">Operasional Unit</li>

                <li class="nxl-hasmenu {{ request()->is('pengaliran*','pemeliharaan*','rencana*','alat-berat*','monitoring-alat-berat*') ? 'active' : '' }}">
                    <a href="javascript:void(0);" class="nav-link-simoli
                        {{ request()->is('pengaliran*','pemeliharaan*','rencana*','alat-berat*','monitoring-alat-berat*') ? 'active' : '' }}"
                       data-bs-toggle="collapse" data-bs-target="#submenu-input">
                        <span class="nav-icon-pill"><i class="feather-edit-3"></i></span>
                        <span class="nav-label-text">Input Laporan</span>
                        <i class="feather-chevron-right nav-arrow"></i>
                    </a>
                    <ul class="nav-submenu collapse {{ request()->is('pengaliran*','pemeliharaan*','rencana*','alat-berat*','monitoring-alat-berat*') ? 'show' : '' }}"
                        id="submenu-input">
                        <li>
                            <a class="nav-link-simoli {{ request()->is('pengaliran*') ? 'active' : '' }}"
                               href="{{ route('pengaliran.index') }}">
                                <span class="nav-icon-pill" style="width:26px;height:26px;font-size:13px;"><i class="feather-droplet"></i></span>
                                <span class="nav-label-text">Pengaliran LA</span>
                            </a>
                        </li>
                        <li>
                            <a class="nav-link-simoli {{ request()->is('pemeliharaan*') ? 'active' : '' }}"
                               href="{{ route('pemeliharaan.index') }}">
                                <span class="nav-icon-pill" style="width:26px;height:26px;font-size:13px;"><i class="feather-tool"></i></span>
                                <span class="nav-label-text">Pemeliharaan Bed</span>
                            </a>
                        </li>
                        <li>
                            <a class="nav-link-simoli {{ request()->is('rencana*') ? 'active' : '' }}"
                               href="{{ route('rencana.index') }}">
                                <span class="nav-icon-pill" style="width:26px;height:26px;font-size:13px;"><i class="feather-clipboard"></i></span>
                                <span class="nav-label-text">Rencana Tahunan</span>
                            </a>
                        </li>
                        <li>
                            <a class="nav-link-simoli {{ request()->is('alat-berat*') ? 'active' : '' }}"
                               href="{{ route('alat-berat.index') }}">
                                <span class="nav-icon-pill" style="width:26px;height:26px;font-size:13px;"><i class="feather-truck"></i></span>
                                <span class="nav-label-text">Data Alat Berat</span>
                            </a>
                        </li>
                        <li>
                            <a class="nav-link-simoli {{ request()->is('monitoring-alat-berat*') ? 'active' : '' }}"
                               href="{{ route('monitoring-alat-berat.index') }}">
                                <span class="nav-icon-pill" style="width:26px;height:26px;font-size:13px;"><i class="feather-activity"></i></span>
                                <span class="nav-label-text">Monitoring Alat Berat</span>
                            </a>
                        </li>
                    </ul>
                </li>
                @endif
            </ul>
        </div>

        {{-- Sidebar Footer: User Info --}}
        <div class="sidebar-footer">
            <div class="sidebar-user-strip">
                <div class="sidebar-user-avatar">
                    {{ strtoupper(substr(Auth::user()->username ?? 'U', 0, 2)) }}
                </div>
                <div class="sidebar-user-info">
                    <div class="user-name">{{ Auth::user()->pks->nama ?? Auth::user()->username }}</div>
                    <div class="user-role">{{ Auth::user()->isAdmin() ? 'Administrator' : 'Unit PKS' }}</div>
                </div>
            </div>
        </div>
    </nav>

    {{-- ============================================================
         STICKY HEADER / TOPBAR
         ============================================================ --}}
    <header class="simoli-header" role="banner" id="simoliHeader">
        {{-- Left: Mobile Toggle + Breadcrumb --}}
        <div class="d-flex align-items-center gap-3 min-w-0 flex-1">
            {{-- Mobile hamburger --}}
            <button type="button" class="header-mobile-toggle" id="mobileMenuToggle"
                    aria-label="Buka Menu" onclick="toggleMobileSidebar()">
                <i class="feather-menu"></i>
            </button>

            {{-- Page context --}}
            <div class="header-context min-w-0">
                <h6 class="header-page-label">@yield('page-title', 'Dashboard')</h6>
                <nav aria-label="breadcrumb">
                    <ol class="header-breadcrumb">
                        <li><a href="{{ url('/dashboard') }}"><i class="feather-home" style="font-size:11px;"></i></a></li>
                        <li class="separator">/</li>
                        @yield('breadcrumb')
                    </ol>
                </nav>
            </div>
        </div>

        {{-- Right Actions --}}
        <div class="header-actions">
            {{-- Page-specific actions slot --}}
            @yield('page-actions')

            {{-- Quick Report Link --}}
            <!-- <a href="{{ route('report-pengaliran') }}"
               class="hdr-icon-btn d-none d-lg-inline-flex"
               title="Laporan Pengaliran LA">
                <i class="feather-file-text" style="font-size:16px;"></i>
            </a> -->

            {{-- Dark/Light Toggle --}}
            <div class="dark-light-theme">
                <button type="button" class="hdr-icon-btn dark-button" title="Mode Gelap">
                    <i class="feather-moon" style="font-size:16px;"></i>
                </button>
                <button type="button" class="hdr-icon-btn light-button" style="display:none;" title="Mode Terang">
                    <i class="feather-sun" style="font-size:16px;"></i>
                </button>
            </div>

            {{-- User Dropdown --}}
            <div class="dropdown">
                <div class="hdr-user-trigger" data-bs-toggle="dropdown" role="button"
                     data-bs-auto-close="outside" aria-expanded="false">
                    <div class="hdr-user-avatar">
                        {{ strtoupper(substr(Auth::user()->username ?? 'U', 0, 2)) }}
                    </div>
                    <div class="d-none d-md-block">
                        <div class="hdr-user-name">{{ Auth::user()->pks->nama ?? Auth::user()->username }}</div>
                        <div class="hdr-user-role">{{ Auth::user()->isAdmin() ? 'Administrator' : 'Unit PKS' }}</div>
                    </div>
                    <i class="feather-chevron-down text-muted" style="font-size:13px;"></i>
                </div>

                <div class="dropdown-menu dropdown-menu-end user-dropdown-menu">
                    <div class="user-dropdown-header d-flex align-items-center gap-3">
                        <div class="hdr-user-avatar" style="width:44px;height:44px;font-size:16px;flex-shrink:0;">
                            {{ strtoupper(substr(Auth::user()->username ?? 'U', 0, 2)) }}
                        </div>
                        <div>
                            <div style="font-size:14px;font-weight:800;color:#1a2e22;">
                                {{ Auth::user()->pks->nama ?? Auth::user()->username }}
                            </div>
                            <span class="status-pill status-pill-success" style="font-size:9px;padding:2px 8px;">
                                {{ Auth::user()->isAdmin() ? 'Administrator Regional' : ('PKS ' . (Auth::user()->pks->akro ?? '')) }}
                            </span>
                        </div>
                    </div>

                    <a href="{{ url('/dashboard') }}" class="dropdown-item rounded-2 py-2" style="font-size:12.5px;font-weight:600;">
                        <i class="feather-grid me-2 text-success"></i> Dashboard Utama
                    </a>
                    @if(Auth::user()->isAdmin())
                    <a href="{{ route('pengguna.index') }}" class="dropdown-item rounded-2 py-2" style="font-size:12.5px;font-weight:600;">
                        <i class="feather-users me-2 text-info"></i> Manajemen Pengguna
                    </a>
                    @endif

                    <div class="mt-2 pt-2" style="border-top:1px solid var(--ptpn-100);">
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="btn-logout-green">
                                <i class="feather-log-out"></i>
                                <span>Keluar dari SIMOLI</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </header>

    {{-- ============================================================
         MAIN CONTENT AREA
         ============================================================ --}}
    <main class="simoli-main" id="simoliMain" role="main">
        <div class="simoli-content">
            {{-- Page Hero Strip --}}
            <section class="page-hero-strip" aria-label="Page Header">
                <div>
                    <h1>@yield('page-title', 'Dashboard')</h1>
                    <p>@yield('page-description', 'Sistem Informasi Monitoring Limbah & Land Aplikasi — PTPN IV')</p>
                </div>
                <div class="d-flex align-items-center flex-wrap gap-2">
                    @yield('page-actions-hero')
                </div>
            </section>

            {{-- Session Alerts --}}
            @if(session('success'))
            <div class="alert-simoli alert-simoli-success" role="alert">
                <i class="feather-check-circle" style="font-size:20px;color:var(--ptpn-600);flex-shrink:0;"></i>
                <div>
                    <strong style="font-size:13px;font-weight:800;">Berhasil!</strong>
                    <p class="mb-0 mt-1" style="font-size:12.5px;">{{ session('success') }}</p>
                </div>
                <button onclick="this.parentElement.remove()" style="background:none;border:none;margin-left:auto;cursor:pointer;color:var(--ptpn-700);font-size:18px;padding:0;flex-shrink:0;">×</button>
            </div>
            @endif

            @if(session('error'))
            <div class="alert-simoli alert-simoli-danger" role="alert">
                <i class="feather-alert-circle" style="font-size:20px;color:#ef4444;flex-shrink:0;"></i>
                <div>
                    <strong style="font-size:13px;font-weight:800;">Terjadi Kesalahan</strong>
                    <p class="mb-0 mt-1" style="font-size:12.5px;">{{ session('error') }}</p>
                </div>
                <button onclick="this.parentElement.remove()" style="background:none;border:none;margin-left:auto;cursor:pointer;color:#ef4444;font-size:18px;padding:0;flex-shrink:0;">×</button>
            </div>
            @endif

            @if($errors->any())
            <div class="alert-simoli alert-simoli-danger" role="alert">
                <i class="feather-alert-triangle" style="font-size:20px;color:#ef4444;flex-shrink:0;"></i>
                <div style="flex:1;">
                    <strong style="font-size:13px;font-weight:800;">Terdapat Kesalahan Validasi:</strong>
                    <ul class="mb-0 mt-1 ps-3" style="font-size:12.5px;">
                        @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                <button onclick="this.parentElement.remove()" style="background:none;border:none;margin-left:auto;cursor:pointer;color:#ef4444;font-size:18px;padding:0;flex-shrink:0;align-self:flex-start;">×</button>
            </div>
            @endif

            {{-- Main Page Content --}}
            @yield('content')
        </div>
    </main>

    {{-- ============================================================
         SCRIPTS
         ============================================================ --}}
    <script src="{{ asset('duraluxadmin/assets/vendors/js/vendors.min.js') }}"></script>
    <script src="{{ asset('duraluxadmin/assets/js/common-init.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    @yield('scripts')
    <script src="{{ asset('duraluxadmin/assets/js/theme-customizer-init.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- SIMOLI Sidebar Controller -->
    <script>
    (function() {
        var MINI_KEY = 'simoli_sidebar_mini';
        var html = document.documentElement;
        var toggleIcon = document.getElementById('sidebarToggleIcon');

        function setSidebarState(mini) {
            if (mini) {
                html.classList.add('simoli-app', 'sidebar-mini');
                if (toggleIcon) toggleIcon.className = 'feather-menu toggle-icon';
            } else {
                html.classList.remove('sidebar-mini');
                if (toggleIcon) toggleIcon.className = 'feather-sidebar toggle-icon';
            }
        }

        // Restore saved state
        var saved = localStorage.getItem(MINI_KEY);
        setSidebarState(saved === '1');

        window.toggleSidebar = function() {
            var isMini = html.classList.contains('sidebar-mini');
            var next = !isMini;
            setSidebarState(next);
            localStorage.setItem(MINI_KEY, next ? '1' : '0');
            setTimeout(function() { window.dispatchEvent(new Event('resize')); }, 350);
        };

        window.toggleMobileSidebar = function() {
            html.classList.toggle('mobile-open');
        };

        window.closeMobileSidebar = function() {
            html.classList.remove('mobile-open');
        };

        document.getElementById('sidebarToggleBtn')?.addEventListener('click', toggleSidebar);
    })();
    </script>

    <!-- PWA -->
    <script src="{{ asset('js/simoli-offline-db.js') }}"></script>
    <script src="{{ asset('js/simoli-sync-manager.js') }}"></script>
    <script>
        @if(Auth::check())
        SimoliDB.init().then(function() {
            SimoliDB.setConfig('user_id', '{{ Auth::user()->id ?? Auth::user()->ID }}');
            SimoliDB.setConfig('username', '{{ Auth::user()->username }}');
            SimoliDB.setConfig('user_pks_id', '{{ Auth::user()->id_pks }}');
        });
        @endif
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', function() {
                navigator.serviceWorker.register('/sw.js')
                    .catch(function(e) { console.warn('[PWA]', e); });
            });
        }
    </script>
</body>
</html>
