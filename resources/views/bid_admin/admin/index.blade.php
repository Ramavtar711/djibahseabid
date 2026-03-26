<!DOCTYPE html>
<html lang="en">

<head>
    <title>Djibah SeaBid Admin Login</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta charset="UTF-8">
    <link rel="shortcut icon" href="{{ asset('admin/assets/img/favicon.png') }}" type="image/png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Jost:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link type="text/css" rel="stylesheet" href="{{ asset('admin/assets/css/bootstrap.min.css') }}">
    <style>
        :root {
            --brand-blue: #4f7ff5;
            --brand-deep: #2f3f9d;
            --brand-text: #32344b;
            --muted-text: #6f748a;
            --field-bg: #f4f5f9;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            height: 100vh;
            font-family: 'Jost', sans-serif;
            background:
                radial-gradient(circle at 20% 80%, rgba(100, 140, 255, 0.22), transparent 20%),
                radial-gradient(circle at 80% 20%, rgba(79, 127, 245, 0.18), transparent 18%),
                linear-gradient(135deg, #edf2fb 0%, #f7f9fc 50%, #eef3ff 100%);
            color: var(--brand-text);
            overflow: hidden;
        }

        body::before,
        body::after {
            content: "";
            position: fixed;
            border-radius: 999px;
            pointer-events: none;
            z-index: 0;
        }

        body::before {
            top: -140px;
            right: -120px;
            width: 360px;
            height: 360px;
            border: 1px solid rgba(79, 127, 245, 0.35);
        }

        body::after {
            right: 70px;
            bottom: 60px;
            width: 120px;
            height: 120px;
            background-image: radial-gradient(rgba(79, 127, 245, 0.3) 1.2px, transparent 1.2px);
            background-size: 10px 10px;
            opacity: 0.55;
        }

        .admin-login-shell {
            position: relative;
            z-index: 1;
            height: 100vh;
            padding: 8px;
        }

        .admin-login-frame {
            height: calc(100vh - 16px);
            border-radius: 22px;
            overflow: hidden;
            background: rgba(255, 255, 255, 0.72);
            box-shadow: 0 30px 80px rgba(27, 41, 84, 0.14);
            backdrop-filter: blur(8px);
        }

        .visual-panel {
            position: relative;
            height: calc(100vh - 16px);
            padding: 44px 48px;
            display: flex;
            align-items: flex-start;
            background:
                linear-gradient(180deg, rgba(11, 22, 72, 0.7) 0%, rgba(5, 22, 62, 0.55) 36%, rgba(7, 24, 70, 0.7) 100%),
                url('{{ asset('admin/assets/img/login.png') }}') center center / cover no-repeat;
        }

        .visual-panel::before {
            content: "";
            position: absolute;
            inset: auto auto -40px -40px;
            width: 420px;
            height: 220px;
            background: radial-gradient(circle at center, rgba(98, 140, 255, 0.45), rgba(98, 140, 255, 0) 70%);
            filter: blur(10px);
        }

        .visual-panel::after {
            content: "";
            position: absolute;
            left: -10%;
            right: -10%;
            bottom: -70px;
            height: 220px;
            background:
                radial-gradient(circle at 18% 40%, rgba(117, 167, 255, 0.95) 0, rgba(117, 167, 255, 0.18) 18%, transparent 34%),
                linear-gradient(180deg, rgba(31, 58, 162, 0.02) 0%, rgba(30, 87, 255, 0.45) 100%);
            clip-path: ellipse(58% 80% at 42% 100%);
            opacity: 0.9;
        }

        .visual-copy {
            position: relative;
            z-index: 2;
            max-width: 470px;
            color: #fff;
            padding-top: 84px;
        }

        .brand-headline {
            margin: 0 0 12px;
            font-size: 3.25rem;
            font-weight: 700;
            line-height: 0.95;
            letter-spacing: -0.03em;
        }

        .brand-headline span {
            color: #68a3ff;
        }

        .hero-tagline {
            display: flex;
            align-items: center;
            gap: 14px;
            margin-bottom: 22px;
            font-size: 1.08rem;
            font-weight: 500;
        }

        .hero-tagline::before,
        .hero-description::before {
            content: "";
            display: inline-block;
            width: 3px;
            border-radius: 99px;
            background: linear-gradient(180deg, #92bfff, #4f7ff5);
        }

        .hero-tagline::before {
            height: 34px;
        }

        .hero-description {
            display: flex;
            gap: 14px;
            margin: 0;
            max-width: 360px;
            font-size: 1.02rem;
            line-height: 1.55;
            color: rgba(255, 255, 255, 0.92);
        }

        .hero-description::before {
            flex: 0 0 3px;
            min-height: 92px;
        }

        .auth-panel {
            position: relative;
            height: calc(100vh - 16px);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 32px 20px;
        }

        .auth-panel::before {
            content: "";
            position: absolute;
            left: 9%;
            bottom: 6%;
            width: 280px;
            height: 280px;
            border-radius: 999px;
            background: radial-gradient(circle, rgba(94, 145, 255, 0.26), rgba(94, 145, 255, 0));
            filter: blur(10px);
        }

        .auth-card {
            position: relative;
            z-index: 2;
            width: min(100%, 420px);
            padding: 36px 30px 24px;
            border-radius: 22px;
            background: rgba(255, 255, 255, 0.92);
            box-shadow: 0 24px 70px rgba(35, 52, 104, 0.14);
        }

        .auth-brand {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 26px;
        }

        .auth-brand img {
            width: 34px;
            height: 34px;
            object-fit: contain;
        }

        .auth-brand-title {
            margin: 0;
            font-size: 1.02rem;
            font-weight: 800;
            color: var(--brand-text);
        }

        .auth-brand-title span {
            color: var(--brand-blue);
        }

        .auth-title {
            margin: 0 0 22px;
            font-size: 1.95rem;
            font-weight: 700;
            color: var(--brand-text);
        }

        .form-field {
            position: relative;
            margin-bottom: 14px;
        }

        .form-field svg {
            position: absolute;
            top: 50%;
            left: 16px;
            width: 20px;
            height: 20px;
            transform: translateY(-50%);
            stroke: #7f859c;
        }

        .form-field .toggle-password {
            left: auto;
            right: 16px;
            cursor: pointer;
        }

        .form-control {
            height: 54px;
            padding: 0 52px;
            border: 1px solid transparent;
            border-radius: 14px;
            background: var(--field-bg);
            color: var(--brand-text);
            font-size: 1rem;
            box-shadow: none;
        }

        .form-control:focus {
            border-color: rgba(79, 127, 245, 0.38);
            box-shadow: 0 0 0 4px rgba(79, 127, 245, 0.09);
            background: #fff;
        }

        .form-select {
            width: 100%;
            height: 54px;
            border: 1px solid transparent;
            border-radius: 14px;
            background: var(--field-bg);
            color: var(--brand-text);
            font-size: 1rem;
            padding: 0 52px 0 16px;
            appearance: none;
        }

        .form-field.select-field {
            padding-right: 0;
        }

        .form-field.select-field::after {
            content: '';
            position: absolute;
            top: 50%;
            right: 20px;
            width: 10px;
            height: 10px;
            border-right: 2px solid #7f859c;
            border-bottom: 2px solid #7f859c;
            transform: translateY(-50%) rotate(45deg);
            pointer-events: none;
            border-radius: 2px;
        }

        .field-error {
            display: block;
            color: #cf2e2e;
            font-size: 0.85rem;
            margin-top: 6px;
        }

        .error-block {
            margin-bottom: 16px;
            padding: 12px 16px;
            border-radius: 12px;
            background: rgba(255, 93, 88, 0.15);
            color: #9b1c1c;
            font-size: 0.95rem;
        }

        .error-block ul {
            margin: 8px 0 0;
            padding-left: 18px;
        }

        .options-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            margin: 8px 0 18px;
            color: var(--muted-text);
            font-size: 0.97rem;
        }

        .options-row .form-check {
            margin: 0;
        }

        .options-row .form-check-input {
            margin-top: 0.18rem;
            border-color: rgba(111, 116, 138, 0.4);
        }

        .options-row a {
            color: var(--brand-deep);
            font-weight: 600;
            text-decoration: none;
        }

        .signin-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            width: 100%;
            height: 54px;
            border: 0;
            border-radius: 12px;
            background: linear-gradient(90deg, #66b1f8 0%, #4d72df 45%, #2f3f9d 100%);
            color: #fff;
            font-size: 1.05rem;
            font-weight: 700;
            letter-spacing: 0.02em;
            box-shadow: 0 16px 32px rgba(79, 127, 245, 0.28);
        }

        .signin-btn:hover {
            filter: brightness(1.03);
        }

        .secure-note {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            margin-top: 20px;
            color: var(--muted-text);
            font-size: 0.9rem;
            text-align: center;
        }

        .secure-note::before,
        .secure-note::after {
            content: "";
            flex: 1;
            height: 1px;
            background: linear-gradient(90deg, rgba(181, 188, 214, 0), rgba(181, 188, 214, 0.9));
        }

        .secure-note::after {
            background: linear-gradient(90deg, rgba(181, 188, 214, 0.9), rgba(181, 188, 214, 0));
        }

        .secure-note svg {
            width: 16px;
            height: 16px;
            stroke: #4d72df;
        }

        @media (max-width: 991.98px) {
            .admin-login-shell {
                height: auto;
                min-height: 100vh;
                padding: 12px;
                overflow-y: auto;
            }

            .admin-login-frame {
                height: auto;
            }

            .visual-panel,
            .auth-panel {
                height: auto;
            }

            .visual-panel {
                padding: 36px 24px 180px;
            }

            .visual-copy {
                padding-top: 32px;
            }

            .brand-headline {
                font-size: 2.6rem;
            }

            .auth-panel {
                padding: 28px 16px 36px;
            }

            .auth-card {
                padding: 32px 20px 24px;
            }
        }
    </style>
</head>

<body>
    <main class="admin-login-shell">
        <div class="admin-login-frame">
            <div class="row g-0">
                <div class="col-lg-6">
                    <section class="visual-panel">
                        <div class="visual-copy">
                            <h1 class="brand-headline">Djibah <span>SeaBid</span></h1>
                            <div class="hero-tagline">Real-Time Seafood Auction Platform</div>
                            <p class="hero-description">
                                <span>
                                    Connecting <strong>Fishermen</strong>, <strong>Suppliers</strong>,
                                    <strong>Quality Control</strong> and <strong>Buyers</strong> in one digital marketplace.
                                </span>
                            </p>
                        </div>
                    </section>
                </div>
                <div class="col-lg-6">
                    <section class="auth-panel">
                        <div class="auth-card">
                            <div class="auth-brand">
                                <img src="{{ asset('admin/assets/img/logo-small.png') }}" alt="Djibah SeaBid">
                                <h4 class="auth-brand-title">Djibah <span>SeaBid</span></h4>
                            </div>

                            <h2 class="auth-title">Welcome Back</h2>

                            <form action="{{ route('admin.login.store') }}" method="post">
                                @csrf

                                @if ($errors->any())
                                    <div class="error-block">
                                        <strong>Unable to sign in.</strong>
                                        <ul>
                                            @foreach ($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif

                                <div class="form-field select-field">
                                    <select name="login_type" class="form-select">
                                        <option value="" {{ old('login_type') ? '' : 'selected' }}>Select login type</option>
                                        <option value="admin" {{ old('login_type') === 'admin' ? 'selected' : '' }}>Admin</option>
                                        <option value="qc" {{ old('login_type') === 'qc' ? 'selected' : '' }}>QC</option>
                                    </select>
                                </div>
                                @error('login_type')
                                    <span class="field-error">{{ $message }}</span>
                                @enderror

                                <div class="form-field">
                                    <svg fill="none" viewBox="0 0 24 24" stroke-width="1.8" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 7.5v9A2.25 2.25 0 0 1 19.5 18.75h-15A2.25 2.25 0 0 1 2.25 16.5v-9m19.5 0A2.25 2.25 0 0 0 19.5 5.25h-15A2.25 2.25 0 0 0 2.25 7.5m19.5 0-8.69 5.216a2.25 2.25 0 0 1-2.32 0L2.25 7.5" />
                                    </svg>
                                    <input name="email" type="email" class="form-control" placeholder="Email Address" value="{{ old('email') }}">
                                </div>
                                <div class="form-field">
                                    <svg fill="none" viewBox="0 0 24 24" stroke-width="1.8" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 0h10.5A2.25 2.25 0 0 1 19.5 12.75v6A2.25 2.25 0 0 1 17.25 21h-10.5A2.25 2.25 0 0 1 4.5 18.75v-6A2.25 2.25 0 0 1 6.75 10.5Z" />
                                    </svg>
                                    <input id="adminPassword" name="password" type="password" class="form-control" placeholder="Password" autocomplete="off">
                                    <svg id="toggleAdminPassword" class="toggle-password" fill="none" viewBox="0 0 24 24" stroke-width="1.8" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12s3.75-6.75 9.75-6.75S21.75 12 21.75 12s-3.75 6.75-9.75 6.75S2.25 12 2.25 12Z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 15.75A3.75 3.75 0 1 0 12 8.25a3.75 3.75 0 0 0 0 7.5Z" />
                                    </svg>
                                </div>

                                <div class="options-row">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" value="" id="rememberMe">
                                        <label class="form-check-label" for="rememberMe">Remember me</label>
                                    </div>
                                    <a href="#">Forgot Password?</a>
                                </div>

                                <button type="submit" class="signin-btn">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6.75a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.5 20.118a7.5 7.5 0 0 1 15 0" />
                                    </svg>
                                    <span>SIGN IN</span>
                                </button>
                            </form>

                            <div class="secure-note">
                                <svg fill="none" viewBox="0 0 24 24" stroke-width="1.8" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 0h10.5A2.25 2.25 0 0 1 19.5 12.75v6A2.25 2.25 0 0 1 17.25 21h-10.5A2.25 2.25 0 0 1 4.5 18.75v-6A2.25 2.25 0 0 1 6.75 10.5Z" />
                                </svg>
                                <span>Secure access for <strong>Admin</strong>, <strong>Sellers</strong>, <strong>Buyers</strong> &amp; <strong>QC</strong></span>
                            </div>
                        </div>
                    </section>
                </div>
            </div>
        </div>
    </main>

    <script>
        const togglePasswordButton = document.getElementById('toggleAdminPassword');
        const adminPasswordField = document.getElementById('adminPassword');

        if (togglePasswordButton && adminPasswordField) {
            togglePasswordButton.addEventListener('click', function () {
                const nextType = adminPasswordField.getAttribute('type') === 'password' ? 'text' : 'password';
                adminPasswordField.setAttribute('type', nextType);
            });
        }
    </script>
</body>

</html>
