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
    <style>
.login-body{background:#070707;position:relative;overflow:hidden}.login-body:after{content:"";position:fixed;inset:0;pointer-events:none;background-image:linear-gradient(rgba(255,255,255,.025) 1px,transparent 1px),linear-gradient(90deg,rgba(255,255,255,.025) 1px,transparent 1px);background-size:42px 42px;mask-image:linear-gradient(to right,#000,transparent 70%)}.login-shell{position:relative;z-index:1;border:1px solid rgba(245,168,23,.2);box-shadow:0 35px 90px rgba(0,0,0,.55)}.login-visual{background:radial-gradient(circle at 28% 20%,rgba(245,168,23,.2),transparent 27%),linear-gradient(145deg,#080808,#17120a);overflow:hidden}.login-visual:before{content:"";position:absolute;inset:0;background:linear-gradient(115deg,transparent 35%,rgba(245,168,23,.08) 50%,transparent 65%);transform:translateX(-100%);animation:loginSweep 6s ease-in-out infinite}.login-energy-ring{position:absolute;right:-130px;bottom:-135px;width:390px;height:390px;border:1px solid rgba(245,168,23,.34);border-radius:50%;box-shadow:0 0 70px rgba(245,168,23,.08);animation:ringRotate 13s linear infinite}.login-energy-ring:before,.login-energy-ring:after{content:"";position:absolute;border-radius:inherit;border:1px dashed rgba(255,255,255,.12)}.login-energy-ring:before{inset:36px}.login-energy-ring:after{inset:82px;border-color:rgba(245,168,23,.22)}.login-spark{position:absolute;width:6px;height:6px;border-radius:50%;background:#f5a817;box-shadow:0 0 18px #f5a817;animation:sparkFloat 4s ease-in-out infinite}.login-spark.one{left:15%;top:28%}.login-spark.two{right:18%;top:19%;animation-delay:-1.2s}.login-spark.three{left:48%;bottom:18%;animation-delay:-2.1s}.login-visual-copy p{font-family:'Inter',sans-serif;font-size:clamp(43px,5vw,72px)!important;line-height:.9!important;letter-spacing:-.055em}.login-visual-copy p strong{background:linear-gradient(90deg,#f5a817,#ffd56d);-webkit-background-clip:text;background-clip:text;color:transparent}.login-visual-subtitle{max-width:440px;margin:17px 0 0!important;color:rgba(255,255,255,.67)!important;font-size:12px!important;line-height:1.65!important;letter-spacing:0!important;text-transform:none!important}.login-platform-stats{display:grid;grid-template-columns:repeat(3,1fr);gap:9px;margin-top:22px;max-width:480px}.login-platform-stats div{padding:12px;border:1px solid rgba(255,255,255,.11);border-radius:10px;background:rgba(255,255,255,.055);backdrop-filter:blur(8px)}.login-platform-stats b,.login-platform-stats small{display:block}.login-platform-stats b{color:#fff;font-size:13px}.login-platform-stats small{margin-top:3px;color:rgba(255,255,255,.53);font-size:8px;text-transform:uppercase;letter-spacing:.07em}.login-card{background:linear-gradient(150deg,#fff,#f7f8fa)}.login-card-inner{position:relative}.login-welcome-icon{width:52px;height:52px;border-radius:14px;display:grid;place-items:center;margin-bottom:17px;background:#111;color:#f5a817;font-size:19px;box-shadow:0 10px 24px rgba(0,0,0,.16)}.login-card-head h1{font-size:36px!important;letter-spacing:-.04em}.login-form label:not(.check-row){padding:4px;border-radius:10px;transition:.2s}.login-form label:not(.check-row):focus-within{background:#fff8e8}.login-form>button[type=submit]{position:relative;overflow:hidden;box-shadow:0 12px 24px rgba(245,168,23,.25)}.login-form>button[type=submit]:after{content:"";position:absolute;top:0;bottom:0;width:45px;background:rgba(255,255,255,.32);transform:skewX(-20deg) translateX(-120px);animation:buttonShine 4.5s ease-in-out infinite}.login-trust{display:flex;justify-content:center;gap:16px;margin-top:16px;color:#7c8491;font-size:9px;font-weight:700}.login-trust span{display:flex;align-items:center;gap:5px}.login-trust i{color:#aa7300}.login-footer{padding-top:20px;border-top:1px solid #eceff3}@keyframes loginSweep{0%,35%{transform:translateX(-100%)}65%,100%{transform:translateX(100%)}}@keyframes ringRotate{to{transform:rotate(360deg)}}@keyframes sparkFloat{50%{transform:translateY(-17px) scale(1.35);opacity:.45}}@keyframes buttonShine{0%,60%{transform:skewX(-20deg) translateX(-120px)}85%,100%{transform:skewX(-20deg) translateX(520px)}}@media(max-width:900px){.login-body{overflow:auto}.login-shell{min-height:100dvh;border:0}}@media(max-width:620px){.login-platform-stats,.login-visual-subtitle{display:none}.login-card-head h1{font-size:30px!important}.login-trust{gap:9px;flex-wrap:wrap}}
    </style>
</head>
<body class="login-body">
    <main class="login-shell">
        <section class="login-visual" aria-label="Arete Performance">
<div class="login-visual-bg"></div>
            <div class="login-energy-ring" aria-hidden="true"></div>
            <i class="login-spark one" aria-hidden="true"></i>
            <i class="login-spark two" aria-hidden="true"></i>
            <i class="login-spark three" aria-hidden="true"></i>
            <div class="login-banner-orb login-banner-orb-one"></div>
            <div class="login-banner-orb login-banner-orb-two"></div>

            <div class="login-visual-logo">
                <img src="{{ url($siteSettings['header_logo'] ?? 'frontend/assets/images/logo/logo-transperent.png') }}" alt="Arete Performance">
            </div>

            <div class="login-visual-copy">
                <small>Admin Console</small>
                <p>Performance<br><strong>Dashboard</strong></p>                <span></span>
                <p class="login-visual-subtitle">Your command centre for orders, customers, inventory and real-time store performance.</p>
                <div class="login-platform-stats" aria-label="Admin platform areas"><div><b>Live</b><small>Analytics</small></div><div><b>Secure</b><small>Access</small></div><div><b>Central</b><small>Control</small></div></div>
                <div class="login-banner-pills" aria-label="Platform highlights">
                    <b><i class="fa-solid fa-chart-line"></i> Analytics</b>
                    <b><i class="fa-solid fa-shield-halved"></i> Secure</b>
                </div>
            </div>
        </section>

        <section class="login-card">
            <div class="login-card-inner">
                <div class="login-card-head">
                    <span class="login-welcome-icon"><i class="fa-solid fa-fingerprint"></i></span>
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

<button type="submit">Sign in <i class="fa-solid fa-arrow-right-to-bracket"></i></button>
                    <div class="login-trust"><span><i class="fa-solid fa-lock"></i> Encrypted session</span><span><i class="fa-solid fa-shield-halved"></i> Protected admin access</span></div>
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
