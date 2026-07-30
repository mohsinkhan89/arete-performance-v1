<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Login - Arete Performance</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" rel="stylesheet">
    <link href="{{ url('backend/assets/css/style.css') }}" rel="stylesheet">
</head>
<body class="login-body">
    <main class="login-shell">
        <section class="login-visual" aria-label="Arete Performance">
            <div class="login-visual-bg"></div>
            <div class="login-banner-orb login-banner-orb-one"></div>
            <div class="login-banner-orb login-banner-orb-two"></div>

            <div class="login-visual-logo">
                <img src="{{ url('frontend/assets/images/logo/logo-transperent.png') }}" alt="Arete Performance">
            </div>

            <div class="login-visual-copy">
                <small>Admin Control Center</small>
                <p>Manage premium performance<br><strong>with confidence.</strong></p>
                <span></span>
                <div class="login-banner-pills" aria-label="Platform highlights">
                    <b><i class="fa-solid fa-chart-line"></i> Live Analytics</b>
                    <b><i class="fa-solid fa-boxes-stacked"></i> Product Control</b>
                    <b><i class="fa-solid fa-shield-halved"></i> Secure Access</b>
                </div>
            </div>
        </section>

        <section class="login-card">
            <div class="login-card-inner">
                <div class="login-card-head">
                    <span class="login-kicker">Arete Admin</span>
                    <h1>Welcome back</h1>
                    <p>Please sign in to your account and continue managing your store.</p>
                </div>

                <form action="{{ route('login.store') }}" method="POST" class="login-form">
                    @csrf
                    <label>
                        <span>Email address</span>
                        <i class="fa-regular fa-envelope"></i>
                        <input type="email" name="email" value="{{ old('email') }}" placeholder="Enter your email" required autofocus>
                    </label>

                    <label>
                        <span>Password</span>
                        <i class="fa-solid fa-lock"></i>
                        <input id="adminPassword" type="password" name="password" placeholder="Enter your password" required>
                        <button class="password-toggle" type="button" aria-label="Show password" aria-controls="adminPassword">
                            <i class="fa-regular fa-eye"></i>
                        </button>
                    </label>

                    @error('email')
                        <p class="login-error">{{ $message }}</p>
                    @enderror

                    <div class="login-options">
                        <label class="check-row">
                            <input type="checkbox" name="remember" value="1" checked>
                            <span>Remember me</span>
                        </label>
                        <a href="#">Forgot password?</a>
                    </div>

                    <button type="submit">Sign in</button>
                </form>

                <p class="login-footer">&copy; {{ date('Y') }} Arete Performance. All rights reserved.</p>
            </div>
        </section>
    </main>

    <script>
        document.querySelector('.password-toggle')?.addEventListener('click', function () {
            const input = document.querySelector('#adminPassword');
            const icon = this.querySelector('i');

            if (!input || !icon) {
                return;
            }

            const isPassword = input.type === 'password';
            input.type = isPassword ? 'text' : 'password';
            icon.classList.toggle('fa-eye', !isPassword);
            icon.classList.toggle('fa-eye-slash', isPassword);
            this.setAttribute('aria-label', isPassword ? 'Hide password' : 'Show password');
        });
    </script>
</body>
</html>
