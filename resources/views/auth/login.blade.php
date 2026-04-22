<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login – SIMAKSI</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('plus-admin-free/src') }}/assets/vendors/mdi/css/materialdesignicons.min.css">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            display: flex;
            background: #0D1B2A;
            overflow: hidden;
        }

        /* ── LEFT PANEL ─────────────────────────────── */
        .left-panel {
            flex: 1;
            background: linear-gradient(145deg, #0D1B2A 0%, #1565C0 60%, #00ACC1 100%);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 48px;
            position: relative;
            overflow: hidden;
        }
        .left-panel::before {
            content: '';
            position: absolute;
            width: 500px; height: 500px;
            border-radius: 50%;
            background: rgba(255,255,255,.04);
            top: -120px; left: -120px;
        }
        .left-panel::after {
            content: '';
            position: absolute;
            width: 350px; height: 350px;
            border-radius: 50%;
            background: rgba(0,172,193,.12);
            bottom: -80px; right: -80px;
        }
        .left-content { position: relative; z-index: 1; text-align: center; }
        .brand-logo {
            width: 72px; height: 72px; border-radius: 18px;
            background: rgba(255,255,255,.15);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255,255,255,.25);
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 24px;
        }
        .brand-logo i { font-size: 36px; color: #fff; }
        .left-title {
            font-size: 32px; font-weight: 800; color: #fff;
            line-height: 1.2; margin-bottom: 12px;
        }
        .left-subtitle {
            font-size: 14px; color: rgba(255,255,255,.6);
            line-height: 1.6; max-width: 320px;
        }
        .features {
            margin-top: 48px;
            display: flex; flex-direction: column; gap: 16px;
            text-align: left;
        }
        .feature-item {
            display: flex; align-items: center; gap: 14px;
            background: rgba(255,255,255,.07);
            border: 1px solid rgba(255,255,255,.1);
            border-radius: 12px; padding: 14px 18px;
            backdrop-filter: blur(6px);
        }
        .feature-icon {
            width: 38px; height: 38px; border-radius: 10px;
            background: rgba(255,255,255,.12);
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
        }
        .feature-icon i { color: #fff; font-size: 18px; }
        .feature-text strong { font-size: 13.5px; color: #fff; display: block; font-weight: 600; }
        .feature-text span   { font-size: 12px; color: rgba(255,255,255,.5); }

        /* ── RIGHT PANEL (FORM) ─────────────────────── */
        .right-panel {
            width: 460px;
            background: #fff;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 48px 44px;
            position: relative;
        }
        .form-header { width: 100%; margin-bottom: 32px; }
        .form-header h2 {
            font-size: 26px; font-weight: 800;
            color: #1a2332; margin-bottom: 6px;
        }
        .form-header p { font-size: 13.5px; color: #6b7a90; }

        /* Alert */
        .alert-error {
            background: #FFEBEE; border: 1px solid #FFCDD2;
            border-radius: 10px; padding: 12px 16px;
            display: flex; align-items: flex-start; gap: 10px;
            margin-bottom: 20px; font-size: 13px; color: #C62828;
            width: 100%;
        }
        .alert-success {
            background: #E8F5E9; border: 1px solid #C8E6C9;
            border-radius: 10px; padding: 12px 16px;
            display: flex; align-items: flex-start; gap: 10px;
            margin-bottom: 20px; font-size: 13px; color: #2E7D32;
            width: 100%;
        }

        /* Form Fields */
        .form-group { margin-bottom: 18px; width: 100%; }
        .form-label {
            font-size: 13px; font-weight: 600; color: #1a2332;
            display: block; margin-bottom: 6px;
        }
        .input-wrapper { position: relative; }
        .input-icon {
            position: absolute; left: 13px; top: 50%; transform: translateY(-50%);
            color: #9ca3af; font-size: 18px; pointer-events: none;
        }
        .form-input {
            width: 100%;
            border: 1.5px solid #e4eaf2;
            border-radius: 10px;
            padding: 11px 13px 11px 40px;
            font-size: 13.5px;
            font-family: 'Inter', sans-serif;
            outline: none;
            color: #1a2332;
            background: #f9fbff;
            transition: all .2s;
        }
        .form-input:focus {
            border-color: #1976D2;
            background: #fff;
            box-shadow: 0 0 0 3px rgba(25,118,210,.12);
        }
        .form-input.is-error { border-color: #C62828; }
        .toggle-password {
            position: absolute; right: 13px; top: 50%; transform: translateY(-50%);
            background: none; border: none; cursor: pointer;
            color: #9ca3af; font-size: 18px;
            display: flex; align-items: center;
        }
        .toggle-password:hover { color: #1976D2; }
        .field-error { font-size: 11.5px; color: #C62828; margin-top: 5px; display: flex; align-items: center; gap: 4px; }

        /* Remember + Forgot */
        .form-options {
            display: flex; align-items: center; justify-content: space-between;
            margin-bottom: 22px; width: 100%;
        }
        .checkbox-label {
            display: flex; align-items: center; gap: 7px;
            font-size: 13px; color: #4b5563; cursor: pointer;
        }
        .checkbox-label input[type="checkbox"] {
            width: 16px; height: 16px; accent-color: #1565C0;
            cursor: pointer;
        }

        /* Submit Button */
        .btn-login {
            width: 100%;
            background: linear-gradient(135deg, #1565C0, #1976D2);
            color: #fff; border: none; border-radius: 10px;
            padding: 13px; font-size: 14.5px; font-weight: 700;
            font-family: 'Inter', sans-serif;
            cursor: pointer; transition: all .2s;
            display: flex; align-items: center; justify-content: center; gap: 8px;
            letter-spacing: .3px;
        }
        .btn-login:hover {
            background: linear-gradient(135deg, #0D47A1, #1565C0);
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(21,101,192,.35);
        }
        .btn-login:active { transform: translateY(0); }
        .btn-login .spinner {
            width: 18px; height: 18px; border: 2.5px solid rgba(255,255,255,.4);
            border-top-color: #fff; border-radius: 50%;
            animation: spin .7s linear infinite; display: none;
        }
        @keyframes spin { to { transform: rotate(360deg); } }
        .btn-login.loading .spinner { display: block; }
        .btn-login.loading span { display: none; }

        /* Footer */
        .login-footer {
            margin-top: 28px; text-align: center;
            font-size: 12px; color: #9ca3af; width: 100%;
        }

        /* Animated blobs */
        .blob {
            position: absolute;
            border-radius: 50%;
            filter: blur(60px);
            opacity: 0.15;
            animation: floatBlob 8s ease-in-out infinite;
        }
        .blob-1 { width:300px; height:300px; background:#00ACC1; top:-80px; right:-80px; animation-delay:0s; }
        .blob-2 { width:200px; height:200px; background:#1976D2; bottom:-60px; left:-60px; animation-delay:3s; }
        @keyframes floatBlob {
            0%,100% { transform: scale(1) translate(0,0); }
            50%      { transform: scale(1.08) translate(10px, -10px); }
        }

        @media (max-width: 768px) {
            .left-panel { display: none; }
            .right-panel { width: 100%; padding: 32px 24px; }
        }
    </style>
</head>
<body>

    {{-- LEFT PANEL --}}
    <div class="left-panel">
        <div class="left-content">
            <div class="brand-logo">
                <i class="mdi mdi-school"></i>
            </div>
            <div class="left-title">Selamat Datang di<br>SIMAKSI</div>
            <div class="left-subtitle">
                Sistem Informasi Akademik & Absensi berbasis digital untuk sekolah modern.
            </div>

            <div class="features">
                <div class="feature-item">
                    <div class="feature-icon"><i class="mdi mdi-account-check-outline"></i></div>
                    <div class="feature-text">
                        <strong>Absensi Digital</strong>
                        <span>Rekam kehadiran via QR Code real-time</span>
                    </div>
                </div>
                <div class="feature-item">
                    <div class="feature-icon"><i class="mdi mdi-chart-bar"></i></div>
                    <div class="feature-text">
                        <strong>Rekap & Laporan</strong>
                        <span>Export data ke Excel dengan mudah</span>
                    </div>
                </div>
                <div class="feature-item">
                    <div class="feature-icon"><i class="mdi mdi-star-circle-outline"></i></div>
                    <div class="feature-text">
                        <strong>Sistem Poin</strong>
                        <span>Gamifikasi kehadiran siswa</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- RIGHT PANEL --}}
    <div class="right-panel">
        <div class="blob blob-1"></div>
        <div class="blob blob-2"></div>

        <div class="form-header">
            <h2>Masuk ke Panel Admin</h2>
            <p>Masukkan email dan password akun Anda</p>
        </div>

        {{-- Error dari session --}}
        @if(session('error'))
            <div class="alert-error">
                <i class="mdi mdi-alert-circle-outline" style="font-size:18px;flex-shrink:0;"></i>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        {{-- Error email (credential salah) --}}
        @if($errors->has('email') && !$errors->has('password'))
            <div class="alert-error">
                <i class="mdi mdi-alert-circle-outline" style="font-size:18px;flex-shrink:0;"></i>
                <span>{{ $errors->first('email') }}</span>
            </div>
        @endif

        <form method="POST" action="/login" id="loginForm" style="width:100%;">
            @csrf

            {{-- Email --}}
            <div class="form-group">
                <label class="form-label" for="email">Email</label>
                <div class="input-wrapper">
                    <i class="mdi mdi-email-outline input-icon"></i>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        class="form-input {{ $errors->has('email') ? 'is-error' : '' }}"
                        placeholder="admin@sekolah.sch.id"
                        value="{{ old('email') }}"
                        autocomplete="email"
                        autofocus
                    >
                </div>
                @error('email')
                    <div class="field-error">
                        <i class="mdi mdi-alert-circle-outline"></i>
                        {{ $message }}
                    </div>
                @enderror
            </div>

            {{-- Password --}}
            <div class="form-group">
                <label class="form-label" for="password">Password</label>
                <div class="input-wrapper">
                    <i class="mdi mdi-lock-outline input-icon"></i>
                    <input
                        type="password"
                        id="password"
                        name="password"
                        class="form-input {{ $errors->has('password') ? 'is-error' : '' }}"
                        placeholder="••••••••"
                        autocomplete="current-password"
                    >
                    <button type="button" class="toggle-password" onclick="togglePass()" id="toggleBtn">
                        <i class="mdi mdi-eye-outline" id="eyeIcon"></i>
                    </button>
                </div>
                @error('password')
                    <div class="field-error">
                        <i class="mdi mdi-alert-circle-outline"></i>
                        {{ $message }}
                    </div>
                @enderror
            </div>

            {{-- Remember --}}
            <div class="form-options">
                <label class="checkbox-label">
                    <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}>
                    Ingat saya
                </label>
            </div>

            {{-- Submit --}}
            <button type="submit" class="btn-login" id="loginBtn">
                <div class="spinner"></div>
                <span><i class="mdi mdi-login me-1"></i>Masuk ke Dashboard</span>
            </button>
        </form>

        <div class="login-footer">
            © {{ date('Y') }} SIMAKSI — Sistem Informasi Akademik & Absensi
        </div>
    </div>

    <script>
        function togglePass() {
            const input = document.getElementById('password')
            const icon  = document.getElementById('eyeIcon')
            const isPass = input.type === 'password'
            input.type = isPass ? 'text' : 'password'
            icon.className = isPass ? 'mdi mdi-eye-off-outline' : 'mdi mdi-eye-outline'
        }

        document.getElementById('loginForm').addEventListener('submit', function () {
            const btn = document.getElementById('loginBtn')
            btn.classList.add('loading')
            btn.disabled = true
        })
    </script>
</body>
</html>
