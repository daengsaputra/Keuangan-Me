<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Reset Password - Keuangan Me</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="auth-page">
    <main class="auth-shell">
        <section class="auth-card">
            <div class="auth-heading">
                <span class="brand-mark">K</span>
                <p class="eyebrow">RESET PASSWORD</p>
                <h1>Buat password baru</h1>
            </div>

            <form method="POST" action="{{ route('password.update') }}">
                @csrf

                <label class="field full">
                    <span>Username</span>
                    <select name="username" required autofocus>
                        <option value="">Pilih akun</option>
                        <option value="daeng" {{ old('username') === 'daeng' ? 'selected' : '' }}>daeng</option>
                        <option value="naufal" {{ old('username') === 'naufal' ? 'selected' : '' }}>naufal</option>
                    </select>
                    @error('username')<small>{{ $message }}</small>@enderror
                </label>

                <label class="field full">
                    <span>Password baru</span>
                    <input type="password" name="password" placeholder="Minimal 5 karakter" required>
                    @error('password')<small>{{ $message }}</small>@enderror
                </label>

                <label class="field full">
                    <span>Ulangi password baru</span>
                    <input type="password" name="password_confirmation" placeholder="Ketik ulang password" required>
                </label>

                <div class="form-actions auth-actions">
                    <a href="{{ route('login') }}" class="secondary-button">Kembali</a>
                    <button class="primary-button">Reset Password</button>
                </div>
            </form>
        </section>
    </main>
</body>
</html>
