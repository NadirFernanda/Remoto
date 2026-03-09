<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Str;

// Rota GET para exibir o formulário de login
Route::get('/login', function () {
    if (Auth::check()) {
        $role = Auth::user()->role;
        if ($role === 'freelancer') return redirect('/freelancer/dashboard');
        if ($role === 'admin')      return redirect('/admin/dashboard');
        return redirect('/cliente/dashboard');
    }
    return view('auth.login');
})->name('login');

// Rota POST para processar o login
Route::post('/login', function (Request $request) {
    $credentials = $request->only('email', 'password');
    if (Auth::attempt($credentials)) {
        $request->session()->regenerate();
        $user = Auth::user();
        // Marca o e-mail como verificado ao fazer login com senha correta
        if (!$user->hasVerifiedEmail()) {
            $user->email_verified_at = now();
            $user->save();
        }
        if ($user->role === 'cliente') {
            return redirect()->intended('/cliente/dashboard');
        } elseif ($user->role === 'freelancer') {
            return redirect()->intended('/freelancer/dashboard');
        } elseif ($user->role === 'admin') {
            return redirect()->intended('/admin/dashboard');
        } else {
            return redirect()->intended('/');
        }
    }
    return back()->withErrors([
        'email' => 'Credenciais inválidas.',
    ]);
});

// Rota para logout
Route::post('/logout', function (Request $request) {
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return redirect('/login');
})->name('logout');

// ---- Recuperação de senha ----
Route::get('/esqueci-senha', function () {
    return view('auth.forgot-password');
})->name('password.request');

Route::post('/esqueci-senha', function (Request $request) {
    $request->validate(['email' => 'required|email']);
    $status = Password::sendResetLink($request->only('email'));
    return $status === Password::RESET_LINK_SENT
        ? back()->with('status', __($status))
        : back()->withErrors(['email' => __($status)]);
})->name('password.email');

Route::get('/password/reset/{token}', function (string $token) {
    return view('auth.reset-password', ['token' => $token, 'email' => request('email')]);
})->name('password.reset');

Route::post('/password/reset', function (Request $request) {
    $request->validate([
        'token'    => 'required',
        'email'    => 'required|email',
        'password' => 'required|min:8|confirmed',
    ]);
    $status = Password::reset(
        $request->only('email', 'password', 'password_confirmation', 'token'),
        function ($user, $password) {
            $user->forceFill(['password' => Hash::make($password)])
                 ->setRememberToken(Str::random(60));
            $user->save();
            event(new PasswordReset($user));
        }
    );
    return $status === Password::PASSWORD_RESET
        ? redirect('/login')->with('status', __($status))
        : back()->withErrors(['email' => __($status)]);
})->name('password.update');

// Cadastro freelancer centralizado no RegisterController
use App\Http\Controllers\Auth\RegisterController;

Route::get('/register', function () {
    if (Auth::check()) {
        $role = Auth::user()->role;
        if ($role === 'freelancer') return redirect('/freelancer/dashboard');
        if ($role === 'admin')      return redirect('/admin/dashboard');
        return redirect('/cliente/dashboard');
    }
    return app(\App\Http\Controllers\Auth\RegisterController::class)->showFreelancerForm();
})->name('register');
Route::post('/register', [RegisterController::class, 'register']);