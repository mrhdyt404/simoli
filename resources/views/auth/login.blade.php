<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#0F52BA">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="SIMOLI">
    <link rel="manifest" href="{{ asset('manifest.webmanifest') }}">
    <title>SIMOLI || Login</title>
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('logo/Icon%20SIMOLI.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('logo/Icon%20SIMOLI.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="{{ asset('duraluxadmin/assets/css/bootstrap.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('duraluxadmin/assets/vendors/css/vendors.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('duraluxadmin/assets/vendors/css/feather.min.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" type="text/css" href="{{ asset('duraluxadmin/assets/css/theme.min.css') }}">
    <style>
        .login-alert {
            display: flex;
            align-items: center;
            gap: 16px;
            padding: 16px 18px;
            border-radius: 14px;
            background: #FEF2F2;
            border: 1px solid #FECACA;
            margin-bottom: 24px;
            animation: fadeDown .3s ease;
        }
        .login-alert-icon {
            width: 46px;
            height: 46px;
            border-radius: 50%;
            background: #FEE2E2;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #DC2626;
            font-size: 22px;
            flex-shrink: 0;
        }
        .login-alert-content {
            flex: 1;
        }
        .login-alert-content h6 {
            margin: 0;
            font-size: 15px;
            font-weight: 700;
            color: #991B1B;
        }
        .login-alert-content p {
            margin: 2px 0 0;
            color: #7F1D1D;
            font-size: 13px;
        }
        .login-alert-close {
            width: 36px;
            height: 36px;
            border: none;
            border-radius: 50%;
            background: transparent;
            color: #7F1D1D;
            transition: .25s;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-alert-close:hover {
            background: #FCA5A5;
            color: white;
        }
        @keyframes fadeDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>

<body class="bg-light">
    <main class="auth-minimal-wrapper">
        <div class="auth-minimal-inner">
            <div class="minimal-card-wrapper">
                <div class="card mb-4 mt-5 mx-4 mx-sm-0 position-relative">
                    <div class="card-body p-sm-5">
                        <div class="text-center mb-4">
                            <img src="{{ asset('logo/Logo%20SIMOLI.png') }}" alt="Logo SIMOLI"
                                style="max-height: 85px; width: auto; object-fit: contain;">
                            <h4 class="fs-20 fw-bolder mt-3 mb-1">Sistem Informasi Monitoring Limbah & Oli</h4>
                            <p class="fs-12 fw-medium text-muted">Silahkan masukkan username dan password Anda</p>
                        </div>

                        @if(session('success'))
                            <div class="alert alert-success d-flex align-items-center mb-4" role="alert">
                                <i class="feather-check-circle me-2"></i>
                                <div>{{ session('success') }}</div>
                            </div>
                        @endif

                        @if ($errors->has('login'))
                            <div class="login-alert">

                                <div class="login-alert-icon">
                                    <i class="feather-alert-circle"></i>
                                </div>

                                <div class="login-alert-content">
                                    <h6>Login Gagal</h6>
                                    <p>{{ $errors->first('login') }}</p>
                                </div>

                                <button class="login-alert-close" onclick="this.parentElement.remove()">
                                    <i class="feather-x"></i>
                                </button>

                            </div>
                        @endif

                        <form action="{{ route('login') }}" method="POST" class="w-100 mt-4 pt-2">
                            @csrf
                            <div class="mb-4">
                                <label class="form-label fw-semibold">Username</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="feather-user"></i></span>
                                    <input type="text" name="username" class="form-control"
                                        placeholder="Masukkan username" value="{{ old('username') }}" required
                                        autofocus>
                                </div>
                                @error('username')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Password</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="feather-lock"></i></span>
                                    <input type="password" name="password" class="form-control"
                                        placeholder="Masukkan password" required>
                                </div>
                                @error('password')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            <!-- <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input" id="remember" name="remember">
                                        <label class="custom-control-label c-pointer" for="remember">Remember Me</label>
                                    </div>
                                </div>
                            </div> -->
                            <div class="mt-5">
                                <button type="submit" class="btn btn-lg btn-primary w-100">
                                    <i class="feather-log-in me-2"></i>Sign In
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script src="{{ asset('duraluxadmin/assets/vendors/js/vendors.min.js') }}"></script>
    <script src="{{ asset('duraluxadmin/assets/js/common-init.min.js') }}"></script>
    <script src="{{ asset('duraluxadmin/assets/js/theme-customizer-init.min.js') }}"></script>
    
    <!-- PWA Offline-First Scripts -->
    <script src="{{ asset('js/simoli-offline-db.js') }}"></script>
    <script src="{{ asset('js/simoli-sync-manager.js') }}"></script>
    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('/sw.js')
                    .then((reg) => console.log('[PWA] Service Worker registered in scope:', reg.scope))
                    .catch((err) => console.warn('[PWA] Service Worker registration failed:', err));
            });
        }
    </script>
</body>

</html>