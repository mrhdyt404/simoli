<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>SIMOLI || Login</title>
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('logo/Icon%20SIMOLI.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('logo/Icon%20SIMOLI.png') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('duraluxadmin/assets/css/bootstrap.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('duraluxadmin/assets/vendors/css/vendors.min.css') }}">
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

<body>
    <main class="auth-minimal-wrapper">
        <div class="auth-minimal-inner">
            <div class="minimal-card-wrapper">
                <div class="card mb-4 mt-5 mx-4 mx-sm-0 position-relative">
                    <div
                        class="wd-80 bg-white p-2 rounded-circle shadow-lg position-absolute translate-middle top-0 start-50">
                        <img src="{{ asset('logo/Icon%20SIMOLI.png') }}" alt="SIMOLI" width="96" height="96"
                            class="img-fluid" />
                    </div>
                    <div class="card-body p-sm-5">
                        <h2 class="fs-20 fw-bolder mb-4">SIMOLI Login</h2>
                        <h4 class="fs-13 fw-bold mb-2">Sistem Monitoring Limbah</h4>
                        <p class="fs-12 fw-medium text-muted">Silahkan login untuk mengakses <strong>SIMOLI</strong> -
                            PT. Perkebunan Nusantara IV Regional 3</p>

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
</body>

</html>