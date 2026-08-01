<!doctype html>
<html lang="id" data-theme="light">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Login - Lanyard Bekasi CMS</title>
    <link rel="icon" href="/favicon.ico" sizes="any" />
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png" />
    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png" />
    <script>
      (function () {
        var t = localStorage.getItem("stisla-theme");
        if (t === "dark" || t === "light") document.documentElement.dataset.theme = t;
      })();
    </script>
    <link rel="preload" href="/font-assets/inter-latin" as="font" type="font/woff2" crossorigin />
    <link rel="stylesheet" href="/css/inter.css?v={{ filemtime(public_path('css/inter.css')) }}" />
    <link rel="stylesheet" href="/vendor/meridian/assets/css/style.css" />
    <style>
      .auth-shell {
        min-height: 100dvh;
        display: grid;
        place-items: center;
        padding: clamp(1.25rem, 4vw, 3rem);
        background:
          radial-gradient(circle at 12% 8%, color-mix(in srgb, var(--color-primary) 10%, transparent), transparent 28rem),
          var(--color-background);
      }

      .auth-panel {
        width: min(100%, 1120px);
        display: grid;
        grid-template-columns: 1fr;
        overflow: hidden;
        border: 1px solid var(--color-border);
        border-radius: 1.75rem;
        background: var(--color-card);
        box-shadow: 0 24px 80px rgb(15 23 42 / .10);
      }

      .auth-brand {
        display: none;
        min-height: 620px;
        padding: 3.25rem;
        color: white;
        background:
          linear-gradient(160deg, rgb(37 99 235 / .92), rgb(30 64 175 / .94)),
          var(--color-primary);
      }

      .auth-brand__top {
        display: inline-flex;
        align-items: center;
        gap: .875rem;
        color: white;
        font-weight: 800;
        text-decoration: none;
      }

      .auth-brand__logo {
        width: 9.5rem;
        height: auto;
        object-fit: contain;
        filter: drop-shadow(0 8px 18px rgb(15 23 42 / .18));
      }

      .auth-brand__eyebrow,
      .auth-form__eyebrow {
        margin: 0;
        color: color-mix(in srgb, var(--color-primary) 80%, var(--color-foreground));
        font-size: .75rem;
        font-weight: 800;
        letter-spacing: .04em;
        line-height: 1.4;
        text-transform: uppercase;
      }

      .auth-brand__eyebrow {
        color: rgb(255 255 255 / .72);
      }

      .auth-brand__title {
        max-width: 11ch;
        margin: 1.25rem 0 0;
        font-size: clamp(2.75rem, 4vw, 4.5rem);
        font-weight: 800;
        line-height: .95;
        letter-spacing: 0;
      }

      .auth-brand__copy {
        max-width: 34rem;
        margin: 1.5rem 0 0;
        color: rgb(255 255 255 / .82);
        font-size: 1.05rem;
        line-height: 1.7;
      }

      .auth-form-wrap {
        display: grid;
        place-items: center;
        min-height: 620px;
        padding: clamp(1.5rem, 5vw, 4rem);
      }

      .auth-form-card {
        width: min(100%, 27rem);
        padding: clamp(1.25rem, 3vw, 2rem);
        border: 1px solid var(--color-border);
        border-radius: 1.25rem;
        background: var(--color-card);
        box-shadow: 0 16px 48px rgb(15 23 42 / .08);
      }

      .auth-form__title {
        margin: .75rem 0 0;
        color: var(--color-foreground);
        font-size: clamp(1.75rem, 3vw, 2.25rem);
        font-weight: 800;
        line-height: 1.1;
        letter-spacing: 0;
      }

      .auth-form__copy {
        margin: .75rem 0 0;
        color: var(--color-muted-foreground);
        line-height: 1.65;
      }

      .auth-form {
        display: grid;
        gap: 1.1rem;
        margin-top: 1.75rem;
      }

      .auth-field {
        display: grid;
        gap: .5rem;
      }

      .auth-label {
        color: var(--color-foreground);
        font-size: .875rem;
        font-weight: 700;
      }

      .auth-input {
        width: 100%;
        min-height: 3.25rem;
        padding: .875rem 1rem;
        border: 1px solid var(--color-border);
        border-radius: .875rem;
        background: var(--color-card);
        color: var(--color-foreground);
        font: inherit;
        outline: none;
        transition: border-color .18s ease, box-shadow .18s ease;
      }

      .auth-input:focus {
        border-color: var(--color-primary);
        box-shadow: 0 0 0 4px color-mix(in srgb, var(--color-primary) 14%, transparent);
      }

      .auth-options {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        flex-wrap: wrap;
      }

      .auth-check {
        display: inline-flex;
        align-items: center;
        gap: .55rem;
        color: var(--color-muted-foreground);
        font-size: .9rem;
      }

      .auth-link {
        color: var(--color-primary);
        font-size: .9rem;
        font-weight: 700;
        text-decoration: none;
      }

      .auth-actions {
        display: grid;
        gap: .75rem;
        margin-top: .35rem;
      }

      .auth-turnstile {
        min-height: 65px;
        display: flex;
        align-items: center;
        overflow: hidden;
      }

      .auth-submit {
        width: 100%;
        min-height: 3.25rem;
        justify-content: center;
        border-radius: .875rem;
        font-weight: 800;
      }

      .alert {
        border-radius: var(--radius-lg);
        padding: .875rem 1rem;
        font-weight: 600;
      }

      .alert--success {
        background: color-mix(in srgb, #16a34a 10%, transparent);
        color: #166534;
      }

      .alert--danger {
        background: color-mix(in srgb, #dc2626 10%, transparent);
        color: #991b1b;
      }

      @media (min-width: 64rem) {
        .auth-panel {
          grid-template-columns: 1fr .9fr;
        }

        .auth-brand {
          display: flex;
          flex-direction: column;
          justify-content: space-between;
        }
      }

      @media (max-width: 63.999rem) {
        .auth-panel {
          width: min(100%, 34rem);
        }

        .auth-form-wrap {
          min-height: auto;
        }
      }
    </style>
  </head>
  <body>
    <main class="auth-shell">
      <div class="auth-panel">
        <section class="auth-brand">
          <a class="auth-brand__top" href="{{ route('dashboard') }}">
            <img class="auth-brand__logo" src="/uploads/lanyard-bekasi-logo.webp" alt="Lanyard Bekasi" />
          </a>

          <div>
            <p class="auth-brand__eyebrow">Dashboard Internal</p>
            <h1 class="auth-brand__title">Kelola CMS tanpa ribet.</h1>
            <p class="auth-brand__copy">Atur konten, produk, media, inquiry, dan pengaturan website Lanyard Bekasi dari satu panel yang rapi.</p>
          </div>
        </section>

        <section class="auth-form-wrap">
          <div class="auth-form-card">
            <p class="auth-form__eyebrow">Login Dashboard</p>
            <h1 class="auth-form__title">Masuk ke Kawruh CMS</h1>
            <p class="auth-form__copy">Gunakan akun admin Lanyard Bekasi untuk mengelola website.</p>

            @if($status)
              <div class="alert alert--success" style="margin-top: 1.25rem;">{{ $status }}</div>
            @endif

            @if($errors->any())
              <div class="alert alert--danger" style="margin-top: 1.25rem;">{{ $errors->first() }}</div>
            @endif

            <form class="auth-form" method="post" action="{{ route('login') }}">
              @csrf

              <div class="auth-field">
                <label class="auth-label" for="email">Email</label>
                <input id="email" class="auth-input" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" placeholder="admin@tes.com" />
              </div>

              <div class="auth-field">
                <label class="auth-label" for="password">Password</label>
                <input id="password" class="auth-input" type="password" name="password" required autocomplete="current-password" placeholder="admin12345" />
              </div>

              <div class="auth-options">
                <label class="auth-check">
                  <input class="checkbox" type="checkbox" name="remember" value="1" />
                  <span>Ingat saya</span>
                </label>

                @if($canResetPassword)
                  <a class="auth-link" href="{{ route('password.request') }}">Lupa password?</a>
                @endif
              </div>

              @if($turnstileEnabled)
                <div class="auth-turnstile">
                  <div class="cf-turnstile" data-sitekey="{{ $turnstileSiteKey }}" data-theme="light" data-language="id"></div>
                </div>
              @endif

              <div class="auth-actions">
                <button class="button button--primary auth-submit" type="submit">Masuk Dashboard</button>
              </div>
            </form>
          </div>
        </section>
      </div>
    </main>

    <script src="/vendor/meridian/assets/js/theme.js"></script>
    @if($turnstileEnabled)
      <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
    @endif
  </body>
</html>
