<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    public function create(): View
    {
        return view('auth.login');
    }

    public function store(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $throttleKey = Str::transliterate(Str::lower($credentials['email'].'|'.$request->ip()));

        if (RateLimiter::tooManyAttempts($throttleKey, 3)) {
            return back()->withErrors([
                'email' => 'Tiga percobaan masuk gagal. Gunakan pemulihan kata sandi atau coba lagi setelah '.RateLimiter::availableIn($throttleKey).' detik.',
            ])->onlyInput('email');
        }

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            RateLimiter::hit($throttleKey, 900);

            $message = RateLimiter::tooManyAttempts($throttleKey, 3)
                ? 'Tiga percobaan masuk gagal. Gunakan pemulihan kata sandi melalui email.'
                : 'Email atau password tidak sesuai.';

            return back()->withErrors(['email' => $message])->onlyInput('email');
        }

        RateLimiter::clear($throttleKey);
        $request->session()->regenerate();

        if ($request->user()?->isAdmin()) {
            return redirect()->route('admin.dashboard');
        }

        if ($request->user()?->role === 'anggota') {
            return redirect()->route('member.dashboard');
        }

        if ($request->user()?->role === 'pengurus') {
            return redirect()->route('pengurus.dashboard');
        }

        return redirect()->intended(route('home'));
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }
}
