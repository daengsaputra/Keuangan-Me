<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class AuthController extends Controller
{
    private const ACCOUNTS = [
        'daeng' => [
            'name' => 'Daeng',
            'email' => 'daeng@keuangan.local',
            'password' => '12345',
        ],
        'naufal' => [
            'name' => 'Naufal',
            'email' => 'naufal@keuangan.local',
            'password' => '54321',
        ],
    ];

    public function showLogin(): View
    {
        $this->ensureDefaultUsers();
        $captcha = $this->makeCaptcha();

        return view('auth.login', compact('captcha'));
    }

    public function login(Request $request): RedirectResponse
    {
        $this->ensureDefaultUsers();

        $credentials = $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
            'captcha' => ['required', 'integer'],
        ]);

        if ((int) $credentials['captcha'] !== (int) $request->session()->get('login_captcha_answer')) {
            $this->makeCaptcha();

            return back()
                ->withErrors(['captcha' => 'Jawaban captcha belum sesuai.'])
                ->onlyInput('username');
        }

        $account = self::ACCOUNTS[strtolower($credentials['username'])] ?? null;

        if ($account && Auth::attempt(['email' => $account['email'], 'password' => $credentials['password']], true)) {
            $request->session()->regenerate();

            return to_route('dashboard')->with('success', 'Berhasil masuk sebagai '.$account['name'].'.');
        }

        $this->makeCaptcha();

        return back()
            ->withErrors(['username' => 'Username atau password tidak sesuai.'])
            ->onlyInput('username');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return to_route('login')->with('success', 'Anda sudah keluar.');
    }

    public function showResetPassword(): View
    {
        $this->ensureDefaultUsers();

        return view('auth.reset-password');
    }

    public function resetPassword(Request $request): RedirectResponse
    {
        $this->ensureDefaultUsers();

        $validated = $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string', 'min:5', 'confirmed'],
        ]);

        $account = self::ACCOUNTS[strtolower($validated['username'])] ?? null;

        if (! $account) {
            return back()
                ->withErrors(['username' => 'Akun hanya tersedia untuk daeng atau naufal.'])
                ->onlyInput('username');
        }

        User::where('email', $account['email'])->update([
            'password' => Hash::make($validated['password']),
        ]);

        return to_route('login')->with('success', 'Password '.$account['name'].' berhasil direset.');
    }

    private function ensureDefaultUsers(): void
    {
        foreach (self::ACCOUNTS as $account) {
            User::firstOrCreate(
                ['email' => $account['email']],
                [
                    'name' => $account['name'],
                    'password' => Hash::make($account['password']),
                ],
            );
        }
    }

    private function makeCaptcha(): string
    {
        $left = random_int(2, 9);
        $right = random_int(1, 9);

        session(['login_captcha_answer' => $left + $right]);

        return $left.' + '.$right;
    }
}
