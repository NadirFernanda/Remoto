<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password as PasswordRule;
use App\Models\User;

// Rota GET para exibir o formulário de login
Route::get('/login', function () {
    if (Auth::check()) {
        $user = Auth::user();
        if ($user->role === 'admin') {
            return $user->two_factor_confirmed_at === null
                ? redirect()->route('2fa.setup')
                : redirect()->route('2fa.challenge');
        }
        if ($user->role === 'freelancer') return redirect('/freelancer/dashboard');
        return redirect('/cliente/dashboard');
    }
    return view('auth.login');
})->name('login');

// Rota POST para processar o login (throttle: 5 tentativas por minuto por IP)
Route::post('/login', function (Request $request) {
    $request->validate([
        'email'    => 'required|email',
        'password' => 'required|string',
    ], [
        'email.required'    => 'O e-mail é obrigatório.',
        'email.email'       => 'Introduza um endereço de e-mail válido.',
        'password.required' => 'A senha é obrigatória.',
    ]);

    // Verifica se o e-mail existe na base de dados
    $user = \App\Models\User::where('email', $request->email)->first();

    if (!$user) {
        return back()->withErrors([
            'email' => 'Este e-mail não está registado na plataforma.',
        ])->withInput($request->only('email'));
    }

    // E-mail existe — verifica a senha
    if (!\Illuminate\Support\Facades\Hash::check($request->password, $user->password)) {
        return back()->withErrors([
            'password' => 'A senha introduzida está incorrecta.',
        ])->withInput($request->only('email'));
    }

    // Credenciais correctas — autenticar
    // (a caixa "lembrar-me" nunca fazia nada — Auth::login() era sempre
    // chamado sem o 2º parâmetro; encontrado em auditoria de segurança)
    Auth::login($user, $request->boolean('remember'));
    $request->session()->regenerate();

    // Aviso de segurança sobre negociar dentro da plataforma — mostrado uma
    // vez a cada login (dados de flash, desaparecem sozinhos na navegação
    // seguinte), apenas para clientes/freelancers/criadores.
    if (in_array($user->role, ['cliente', 'freelancer', 'creator'])) {
        $request->session()->flash('show_login_notice', true);
    }

    // Marca o e-mail como verificado ao fazer login com senha correta
    if (!$user->hasVerifiedEmail()) {
        $user->email_verified_at = now();
        $user->save();
    }

    if ($user->role === 'admin') {
        return $user->two_factor_confirmed_at === null
            ? redirect()->route('2fa.setup')
            : redirect()->route('2fa.challenge');
    }

    if ($user->role === 'cliente') {
        return redirect()->intended('/cliente/dashboard');
    } elseif ($user->role === 'freelancer') {
        return redirect()->intended('/freelancer/dashboard');
    } elseif ($user->role === 'creator') {
        return redirect()->intended('/creator/dashboard');
    } else {
        return redirect()->intended('/');
    }
})->middleware('throttle:5,1');

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
    // Contas de admin exigem uma senha mais forte (mínimo 10, maiúscula +
    // minúscula + número + símbolo) — o resto dos utilizadores mantém o
    // mínimo de 8 já usado no registo, para não travar o fluxo normal.
    $isAdmin = User::where('email', $request->input('email'))->where('role', 'admin')->exists();

    $request->validate([
        'token'    => 'required',
        'email'    => 'required|email',
        'password' => [
            'required',
            'confirmed',
            $isAdmin
                ? PasswordRule::min(10)->mixedCase()->numbers()->symbols()
                : PasswordRule::min(8),
        ],
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

// ─── Email Verification ──────────────────────────────────────────────────────
// Necessário para o middleware 'verified' funcionar correctamente.
// O login web já faz auto-verificação, mas utilizadores criados via API
// ou seeders podem não ter email_verified_at preenchido.
Route::middleware('auth')->group(function () {
    Route::get('/email/verify', function () {
        return redirect()->route('dashboard');
    })->name('verification.notice');

    Route::get('/email/verify/{id}/{hash}', function (\Illuminate\Foundation\Auth\EmailVerificationRequest $request) {
        $request->fulfill();
        return redirect()->route('dashboard')->with('success', 'Email verificado com sucesso!');
    })->middleware('signed')->name('verification.verify');
});

Route::get('/register', \App\Livewire\Auth\RegisterWizard::class)->name('register');