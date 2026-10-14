<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Login - Majix</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --color-primary: #f5a817;
            --color-primary-light: #ffc14d;
            --color-black: #050505;
            --color-text: #121212;
            --color-muted: #707070;
            --color-border: #dedede;
            --font-body: "Inter", sans-serif;
            --radius: 8px;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-width: 320px;
            color: var(--color-text);
            font-family: var(--font-body);
            background: #fff;
        }

        a {
            color: inherit;
            text-decoration: none;
        }

        button,
        input {
            font: inherit;
        }

        .login-page {
            display: grid;
            min-height: 100vh;
            grid-template-columns: 43.5% 56.5%;
        }

        .brand-panel {
            position: relative;
            display: flex;
            min-height: 100vh;
            flex-direction: column;
            justify-content: center;
            overflow: hidden;
            padding: 82px 72px 68px;
            color: #fff;
            background: #050505 url("{{ url('backend/assets/imgs/login/sign-in-bg.webp') }}") center / cover no-repeat;
        }

        .brand-panel::after {
            content: "";
            position: absolute;
            inset: 0;
            background: linear-gradient(90deg, rgba(0, 0, 0, .02), rgba(0, 0, 0, .18));
            pointer-events: none;
        }

        .brand-content,
        .brand-footer {
            position: relative;
            z-index: 1;
        }

        .brand-logo img,
        .form-logo img {
            display: block;
            object-fit: contain;
        }

        .brand-logo img {
            width: 196px;
            height: 76px;
            margin-bottom: 56px;
        }

        .brand-kicker {
            display: block;
            margin-bottom: 12px;
            color: #ffb617;
            font-size: 15px;
            font-weight: 800;
            letter-spacing: 11px;
            text-transform: uppercase;
        }

        .brand-title {
            max-width: 540px;
            margin: 0;
            font-size: 54px;
            font-weight: 900;
            line-height: 1.13;
            letter-spacing: -1.5px;
        }

        .brand-title span {
            color: #ffb617;
            text-shadow: none;
        }

        .brand-text {
            max-width: 470px;
            margin: 18px 0 34px;
            color: rgba(255, 255, 255, .74);
            font-size: 20px;
            line-height: 1.45;
        }

        .feature-row {
            display: grid;
            max-width: 530px;
            grid-template-columns: repeat(4, 1fr);
            gap: 26px;
        }

        .feature {
            display: grid;
            justify-items: center;
            gap: 10px;
            text-align: center;
        }

        .feature i {
            display: grid;
            width: 56px;
            height: 56px;
            place-items: center;
            border: 1.7px solid #ffb617;
            border-radius: 50%;
            color: #ffb617;
            font-size: 23px;
        }

        .feature span {
            color: #fff;
            font-size: 14px;
            font-weight: 700;
            line-height: 1.15;
        }

        .brand-footer {
            margin-top: auto;
            padding-top: 64px;
        }

        .brand-footer::before {
            content: "";
            display: block;
            width: 45px;
            height: 3px;
            margin-bottom: 18px;
            background: #ffb617;
        }

        .brand-footer p {
            max-width: 360px;
            margin: 0;
            color: rgba(255, 255, 255, .72);
            font-size: 16px;
            line-height: 1.45;
        }

        .form-panel {
            display: grid;
            min-height: 100vh;
            place-items: center;
            padding: 54px;
            background: #fff url("{{ url('backend/assets/imgs/login/form-bg.webp') }}") center / cover no-repeat;
        }

        .login-card {
            width: min(600px, 100%);
            padding: 54px 46px 38px;
            border: 1px solid rgba(197, 201, 211, .72);
            border-radius: 22px;
            background: rgba(255, 255, 255, .82);
            box-shadow: 0 28px 74px rgba(28, 31, 39, .08);
            backdrop-filter: blur(8px);
        }

        .form-logo {
            display: flex;
            justify-content: center;
            margin-bottom: 22px;
        }

        .form-logo img {
            width: 154px;
            height: 58px;
        }

        .login-heading {
            margin: 0;
            color: #070707;
            text-align: center;
            font-size: 38px;
            font-weight: 900;
            line-height: 1.12;
            letter-spacing: -1.2px;
        }

        .login-subtitle {
            margin: 12px 0 34px;
            color: var(--color-muted);
            text-align: center;
            font-size: 19px;
            line-height: 1.4;
        }

        .login-form {
            display: grid;
            gap: 22px;
        }

        .field {
            display: grid;
            gap: 8px;
        }

        .field span {
            color: #080808;
            font-size: 15px;
            font-weight: 800;
        }

        .input-wrap {
            position: relative;
        }

        .input-wrap > i {
            position: absolute;
            top: 50%;
            left: 17px;
            color: #1f2329;
            font-size: 19px;
            transform: translateY(-50%);
        }

        .input-wrap input {
            width: 100%;
            height: 54px;
            padding: 0 50px 0 60px;
            border: 1px solid #cfd3dc;
            border-radius: 9px;
            outline: 0;
            color: #111;
            background: rgba(255, 255, 255, .62);
            font-size: 16px;
            transition: border-color .18s ease, box-shadow .18s ease, background .18s ease;
        }

        .input-wrap input::placeholder {
            color: #7b7f8b;
        }

        .input-wrap input:focus {
            border-color: var(--color-primary);
            background: #fff;
            box-shadow: 0 0 0 4px rgba(255, 182, 23, .16);
        }

        .input-wrap input:disabled {
            color: #5d6470;
            background: #f1f3f6;
            cursor: not-allowed;
        }

        .password-toggle {
            position: absolute;
            top: 50%;
            right: 12px;
            display: grid;
            width: 34px;
            height: 34px;
            place-items: center;
            border: 0;
            color: #111;
            background: transparent;
            font-size: 18px;
            cursor: pointer;
            transform: translateY(-50%);
        }

        .login-error {
            margin: -8px 0 0;
            padding: 12px 14px;
            border: 1px solid rgba(196, 41, 41, .25);
            border-radius: 9px;
            color: #b42318;
            background: #fff1f1;
            font-size: 13px;
            font-weight: 700;
        }

        .login-status {
            margin: -8px 0 0;
            padding: 12px 14px;
            border: 1px solid rgba(34, 139, 78, .22);
            border-radius: 9px;
            color: #146c3a;
            background: #edfff4;
            font-size: 13px;
            font-weight: 700;
            line-height: 1.45;
        }

        .form-options {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 14px;
            margin-top: -2px;
        }

        .check-row {
            display: inline-flex;
            align-items: center;
            gap: 12px;
            color: #111;
            font-size: 15px;
            line-height: 1;
        }

        .check-row input {
            width: 24px;
            height: 24px;
            margin: 0;
            accent-color: var(--color-primary);
        }

        .forgot-link {
            color: #d18400;
            font-size: 15px;
            font-weight: 600;
        }

        .submit-btn {
            display: inline-flex;
            min-height: 48px;
            padding: 15px 31px;
            align-items: center;
            justify-content: center;
            gap: 0;
            border: 0;
            border-radius: 5px;
            color: var(--color-black);
            background: linear-gradient(135deg, var(--color-primary-light), var(--color-primary));
            box-shadow: 0 10px 24px rgba(245, 168, 23, .18);
            font-size: .78rem;
            font-weight: 800;
            letter-spacing: 0;
            text-transform: uppercase;
            cursor: pointer;
            transition: transform .25s, box-shadow .25s, filter .25s, background .25s;
        }

        .submit-btn.is-loading {
            pointer-events: none;
            opacity: .86;
        }

        .submit-btn .submit-spinner {
            display: none;
            margin-left: 10px;
        }

        .submit-btn.is-loading .submit-spinner {
            display: inline-block;
        }

        .submit-btn.is-loading .submit-icon {
            display: none;
        }

        .submit-btn i {
            margin-left: 10px;
        }

        .submit-btn:hover {
            color: var(--color-black);
            background: var(--color-primary-light);
            transform: translateY(-2px);
            box-shadow: 0 16px 34px rgba(245, 168, 23, .28);
            filter: saturate(1.08);
        }

        .login-status {
            max-height: 120px;
            overflow: hidden;
            transition: opacity .35s ease, transform .35s ease, max-height .35s ease, margin .35s ease, padding .35s ease;
        }

        .login-status.is-hiding {
            max-height: 0;
            margin-top: 0;
            margin-bottom: 0;
            padding-top: 0;
            padding-bottom: 0;
            opacity: 0;
            transform: translateY(-14px);
        }

        .divider {
            display: grid;
            grid-template-columns: 1fr auto 1fr;
            align-items: center;
            gap: 20px;
            margin: 12px 0 4px;
            color: #999da8;
            font-size: 12px;
            font-weight: 800;
            letter-spacing: 6px;
            text-transform: uppercase;
        }

        .divider::before,
        .divider::after {
            content: "";
            height: 1px;
            background: #d7d9e1;
        }

        .social-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 18px;
        }

        .social-btn {
            display: inline-flex;
            height: 54px;
            align-items: center;
            justify-content: center;
            gap: 14px;
            border: 1px solid #d9dce4;
            border-radius: var(--radius);
            color: #111;
            background: rgba(255, 255, 255, .72);
            font-size: 14px;
            font-weight: 700;
        }

        .social-btn i {
            font-size: 23px;
        }

        .social-btn .google {
            color: #4285f4;
            font-weight: 900;
        }

        .signup-copy {
            margin: 24px 0 0;
            color: #7a7e89;
            text-align: center;
            font-size: 16px;
        }

        .signup-copy a {
            color: #d18400;
            font-weight: 800;
        }

        @media (max-width: 1180px) {
            .brand-panel {
                padding: 64px 44px 52px;
            }

            .brand-title {
                font-size: 46px;
            }

            .form-panel {
                padding: 34px;
            }
        }

        @media (max-width: 940px) {
            .login-page {
                grid-template-columns: 1fr;
            }

            .brand-panel {
                min-height: auto;
                padding: 42px 28px 36px;
            }

            .brand-logo img {
                width: 160px;
                height: 62px;
                margin-bottom: 34px;
            }

            .brand-title {
                font-size: 40px;
            }

            .brand-text {
                font-size: 17px;
            }

            .brand-footer {
                padding-top: 38px;
            }

            .form-panel {
                min-height: auto;
                padding: 36px 18px;
            }
        }

        @media (max-width: 560px) {
            .brand-panel {
                padding: 32px 20px;
            }

            .brand-kicker {
                font-size: 12px;
                letter-spacing: 7px;
            }

            .brand-title {
                font-size: 34px;
                letter-spacing: -.8px;
            }

            .feature-row {
                grid-template-columns: repeat(2, 1fr);
                gap: 18px;
            }

            .login-card {
                padding: 34px 22px 28px;
                border-radius: 18px;
            }

            .login-heading {
                font-size: 31px;
            }

            .login-subtitle {
                margin-bottom: 26px;
                font-size: 16px;
            }

            .form-options,
            .social-row {
                grid-template-columns: 1fr;
                flex-direction: column;
                align-items: flex-start;
            }
        }
    </style>
</head>
<body>
    <main class="login-page">
        <section class="brand-panel" aria-label="Majix">
            <div class="brand-content">
                <a class="brand-logo" href="{{ route('frontend.index') }}" aria-label="Majix home">
                    <img src="{{ url($siteSettings['header_logo'] ?? 'frontend/assets/images/logo/logo-transperent.png') }}" alt="Majix">
                </a>

                <span class="brand-kicker">Welcome Back</span>
                <h1 class="brand-title">Sign In to<br>Your <span>Account</span></h1>
                <p class="brand-text">Access your account to manage products, track orders and control your dashboard.</p>

                <div class="feature-row" aria-label="Account features">
                    <div class="feature">
                        <i class="fa-solid fa-cart-shopping"></i>
                        <span>Shop<br>Products</span>
                    </div>
                    <div class="feature">
                        <i class="fa-solid fa-truck-fast"></i>
                        <span>Track<br>Orders</span>
                    </div>
                    <div class="feature">
                        <i class="fa-regular fa-heart"></i>
                        <span>Save<br>Favorites</span>
                    </div>
                    <div class="feature">
                        <i class="fa-regular fa-user"></i>
                        <span>Manage<br>Account</span>
                    </div>
                </div>
            </div>

            <div class="brand-footer">
                <p>Premium performance solutions to help you reach your full potential.</p>
            </div>
        </section>

        <section class="form-panel">
            <div class="login-card">

                @php($authMode = $authMode ?? 'login')

                <h2 class="login-heading">
                    @if($authMode === 'forgot')
                        Forgot Password
                    @elseif($authMode === 'reset')
                        Reset Password
                    @else
                        Welcome Back
                    @endif
                </h2>
                <p class="login-subtitle">
                    @if($authMode === 'forgot')
                        Enter your email address to receive a reset link
                    @elseif($authMode === 'reset')
                        Create a new password for your account
                    @else
                        Sign in to your account to continue
                    @endif
                </p>

                @if (session('status'))
                    <p class="login-status">{{ session('status') }}</p>
                @endif

                <form action="{{ $authMode === 'forgot' ? route('password.email') : ($authMode === 'reset' ? route('password.update') : route('login.store')) }}" method="POST" class="login-form">
                    @csrf

                    @if($authMode === 'reset')
                        <input type="hidden" name="token" value="{{ $resetToken }}">
                    @endif

                    <label class="field">
                        <span>Email Address</span>
                        <div class="input-wrap">
                            <i class="fa-regular fa-envelope"></i>
                            <input type="email" name="email" value="{{ old('email', $resetEmail ?? '') }}" placeholder="Enter your email address" required autofocus @disabled($authMode === 'reset')>
                            @if($authMode === 'reset')
                                <input type="hidden" name="email" value="{{ old('email', $resetEmail ?? '') }}">
                            @endif
                        </div>
                    </label>

                    @if($authMode !== 'forgot')
                        <label class="field">
                            <span>{{ $authMode === 'reset' ? 'New Password' : 'Password' }}</span>
                            <div class="input-wrap">
                                <i class="fa-solid fa-lock"></i>
                                <input id="adminPassword" type="password" name="password" placeholder="{{ $authMode === 'reset' ? 'Enter new password' : 'Enter your password' }}" required>
                                <button class="password-toggle" type="button" aria-label="Show password" aria-controls="adminPassword">
                                    <i class="fa-regular fa-eye-slash"></i>
                                </button>
                            </div>
                        </label>
                    @endif

                    @if($authMode === 'reset')
                        <label class="field">
                            <span>Confirm Password</span>
                            <div class="input-wrap">
                                <i class="fa-solid fa-lock"></i>
                                <input type="password" name="password_confirmation" placeholder="Confirm new password" required>
                            </div>
                        </label>
                    @endif

                    @error('email')
                        <p class="login-error">{{ $message }}</p>
                    @enderror
                    @error('password')
                        <p class="login-error">{{ $message }}</p>
                    @enderror

                    @if($authMode === 'login')
                        <div class="form-options">
                            <label class="check-row">
                                <input type="checkbox" name="remember" value="1" checked>
                                <span>Remember me</span>
                            </label>
                            <a class="forgot-link" href="{{ route('password.request') }}">Forgot Password?</a>
                        </div>
                    @else
                        <div class="form-options">
                            <a class="forgot-link" href="{{ route('login') }}">Back to Sign In</a>
                        </div>
                    @endif

                    <button class="submit-btn" type="submit" data-loading-text="Please wait">
                        <span class="submit-label">
                            @if($authMode === 'forgot')
                                Send Reset Link
                            @elseif($authMode === 'reset')
                                Update Password
                            @else
                                Sign In
                            @endif
                        </span>
                        <i class="fa-solid fa-arrow-right submit-icon"></i>
                        <i class="fa-solid fa-spinner fa-spin submit-spinner" aria-hidden="true"></i>
                    </button>
                </form>
            </div>
        </section>
    </main>

    <script>
        document.querySelectorAll('.login-form').forEach((form) => {
            form.addEventListener('submit', () => {
                const button = form.querySelector('.submit-btn[type="submit"]');
                const label = button?.querySelector('.submit-label');

                if (!button || !label || button.classList.contains('is-loading')) {
                    return;
                }

                button.dataset.originalText = label.textContent.trim();
                label.textContent = button.dataset.loadingText || 'Please wait';
                button.classList.add('is-loading');
                button.setAttribute('aria-busy', 'true');
            });
        });

        document.querySelectorAll('.login-status').forEach((message) => {
            window.setTimeout(() => {
                message.classList.add('is-hiding');
            }, 10000);
        });

        document.querySelector('.password-toggle')?.addEventListener('click', function () {
            const input = document.querySelector('#adminPassword');
            const icon = this.querySelector('i');

            if (!input || !icon) {
                return;
            }

            const isPassword = input.type === 'password';
            input.type = isPassword ? 'text' : 'password';
            icon.classList.toggle('fa-eye', isPassword);
            icon.classList.toggle('fa-eye-slash', !isPassword);
            this.setAttribute('aria-label', isPassword ? 'Hide password' : 'Show password');
        });
    </script>
</body>
</html>
