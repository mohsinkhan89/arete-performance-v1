<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="robots" content="noindex, nofollow">
  <title>@yield('code') | Arete Performance</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Barlow+Condensed:wght@600;700;800&family=Montserrat:wght@400;600;700&display=swap" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" rel="stylesheet">
  <link href="{{ url('frontend/assets/css/style.css') }}" rel="stylesheet">
</head>
<body class="error-page">
  <main class="error-screen">
    <a class="error-brand" href="{{ route('frontend.index') }}" aria-label="Arete Performance home">
      <img src="{{ url('frontend/assets/images/logo/logo-transperent.png') }}" alt="Arete Performance">
    </a>

    <section class="error-card" aria-labelledby="error-title">
      <p class="error-eyebrow">@yield('eyebrow')</p>
      <div class="error-code" aria-hidden="true">@yield('code')</div>
      <h1 id="error-title">@yield('heading')</h1>
      <p class="error-message">@yield('message')</p>

      <div class="error-countdown" aria-live="polite">
        Redirecting to home in <strong data-error-countdown>10</strong> seconds
      </div>

      <a class="btn btn-gold" href="{{ route('frontend.index') }}">
        Back to Home <i class="fa-solid fa-arrow-right"></i>
      </a>
    </section>
  </main>

  <script>
    (() => {
      const output = document.querySelector('[data-error-countdown]');
      let seconds = 10;
      const timer = window.setInterval(() => {
        seconds -= 1;
        output.textContent = seconds;
        if (seconds <= 0) {
          window.clearInterval(timer);
          window.location.replace(@json(route('frontend.index')));
        }
      }, 1000);
    })();
  </script>
</body>
</html>
