<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

// Rota GET para exibir o formulário de login
Route::get('/login', function () {
    return view('auth.login');
})->name('login');

// Rota POST para processar o login
Route::post('/login', function (Request $request) {
    $credentials = $request->only('email', 'password');
    if (Auth::attempt($credentials)) {
        $request->session()->regenerate();
        $user = Auth::user();
        if ($user->role === 'cliente') {
            return redirect()->intended('/cliente/dashboard');
        } elseif ($user->role === 'freelancer') {
            return redirect()->intended('/freelancer/dashboard');
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