<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - Keuangan Me</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="auth-page">
    <main class="auth-shell">
        <section class="auth-card">
            <div class="auth-heading">
                <span class="brand-mark">K</span>
                <p class="eyebrow">LOGIN DASHBOARD</p>
                <h1>Masuk ke Keuangan Me</h1>
            </div>

            @if (session('success'))
                <div class="alert success">{{ session('success') }}</div>
            @endif

            <form method="POST" action="{{ route('login.store') }}">
                @csrf

                <label class="field full">
                    <span>Username</span>
                    <input name="username" value="{{ old('username') }}" placeholder="daeng atau naufal" required autofocus>
                    @error('username')<small>{{ $message }}</small>@enderror
                </label>

                <label class="field full">
                    <span>Password</span>
                    <input type="password" name="password" placeholder="Masukkan password" required>
                    @error('password')<small>{{ $message }}</small>@enderror
                </label>

                <label class="field full captcha-field">
                    <span>Captcha</span>
                    <div class="captcha-row">
                        <strong>{{ $captcha }}</strong>
                        <input type="number" name="captcha" placeholder="Jawaban" required>
                    </div>
                    @error('captcha')<small>{{ $message }}</small>@enderror
                </label>

                <button class="primary-button auth-submit">Masuk</button>
            </form>

            <div class="auth-help">
                <a href="{{ route('password.reset') }}">Reset password</a>
                <span>daeng: 12345 | naufal: 54321</span>
            </div>
        </section>
    </main>
</body>
</html>
