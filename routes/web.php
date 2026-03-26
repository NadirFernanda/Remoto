<?php

/**
 * routes/web.php — Core & Shared Routes
 *
 * Feature-specific routes live in their module:
 *   app/Modules/Marketplace/routes.php   — freelancers, projects, reviews, disputes
 *   app/Modules/Social/routes.php        — feed, posts, creator profiles
 *   app/Modules/Messaging/routes.php     — chat, file uploads
 *   app/Modules/Payments/routes.php      — escrow, wallet, financial exports
 *   app/Modules/Admin/routes.php         — admin dashboards, contracts, settings
 *   app/Modules/Loja/routes.php          — infoproducts store
 *
 * Module providers are registered in bootstrap/providers.php.
 */

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\OtpVerificationController;

// ─── Homepage ─────────────────────────────────────────────────────────────────
Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }
    return view('welcome');
})->name('home');

// ─── Dashboard (role-based redirect) ─────────────────────────────────────────
Route::get('/dashboard', function () {
    $user = auth()->user();
    return match ($user->activeRole()) {
        'freelancer' => redirect()->route('freelancer.dashboard'),
        'admin'      => redirect()->route('admin.dashboard'),
        'creator'    => redirect()->route('creator.dashboard'),
        default      => redirect()->route('client.dashboard'),
    };
})->middleware('auth')->name('dashboard');

// ─── Legal / Privacy ──────────────────────────────────────────────────────────
Route::get('/privacidade', fn() => view('legal.privacy-policy'))->name('legal.privacy');
Route::get('/termos',      fn() => view('legal.terms-of-use'))->name('legal.terms');
Route::get('/cookies',     fn() => view('legal.cookies'))->name('legal.cookies');

// ─── Institutional Pages ──────────────────────────────────────────────────────
Route::prefix('sobre')->name('sobre.')->group(function () {
    Route::get('/sobre-nos',     fn() => view('sobre.sobre-nos'))->name('sobre-nos');
    Route::get('/como-funciona', fn() => view('sobre.como-funciona'))->name('como-funciona');
    Route::get('/seguranca',     fn() => view('sobre.seguranca'))->name('seguranca');
    Route::get('/investidores',  fn() => view('sobre.investidores'))->name('investidores');
    Route::get('/mapa-do-site',  fn() => view('sobre.mapa-do-site'))->name('mapa-do-site');
    Route::get('/historias',     fn() => view('sobre.historias'))->name('historias');
    Route::get('/noticias',      fn() => view('sobre.noticias'))->name('noticias');
    Route::get('/equipe',        fn() => view('sobre.equipe'))->name('equipe');
    Route::get('/premios',       fn() => view('sobre.premios'))->name('premios');
    Route::get('/comunicados',   fn() => view('sobre.comunicados'))->name('comunicados');
    Route::get('/carreiras',     fn() => view('sobre.carreiras'))->name('carreiras');
});

// ─── Suporte / Contacto ───────────────────────────────────────────────────────
Route::get('/suporte', fn() => view('suporte.index'))->name('suporte');
Route::post('/suporte', function (\Illuminate\Http\Request $request) {
    $data = $request->validate([
        'nome'     => ['required', 'string', 'max:100'],
        'email'    => ['required', 'email', 'max:150'],
        'assunto'  => ['required', 'string', 'max:100'],
        'mensagem' => ['required', 'string', 'max:2000'],
    ]);

    \Illuminate\Support\Facades\Mail::raw(
        "Nova mensagem de suporte\n\n" .
        "Nome: {$data['nome']}\n" .
        "Email: {$data['email']}\n" .
        "Assunto: {$data['assunto']}\n\n" .
        "Mensagem:\n{$data['mensagem']}",
        function ($message) use ($data) {
            $message->to('contacto@24horas.ao')
                    ->replyTo($data['email'], $data['nome'])
                    ->subject("[Suporte] {$data['assunto']} — {$data['nome']}");
        }
    );

    return redirect()->route('suporte')->with('success', 'Mensagem enviada com sucesso! Respondemos em até 24 horas.');
})->middleware('throttle:5,1')->name('suporte.enviar');

// ─── OTP Verification ─────────────────────────────────────────────────────────
Route::middleware('auth')->group(function () {
    Route::get('/otp', [OtpVerificationController::class, 'showOtpForm'])->name('otp.form');
    Route::post('/otp/send', [OtpVerificationController::class, 'sendOtp'])->name('otp.send');
    Route::post('/otp/verify', [OtpVerificationController::class, 'verifyOtp'])->name('otp.verify');
});

// ─── Email Verification ───────────────────────────────────────────────────────
Route::post('/email/verification-notification', function () {
    auth()->user()->sendEmailVerificationNotification();
    return back()->with('message', 'Link de verificação enviado!');
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');

// ─── Core Authenticated Routes ────────────────────────────────────────────────
Route::middleware(['auth', 'verified'])->group(function () {

    // Role switching (cliente ↔ freelancer)
    Route::post('/switch-role', function () {
        $user = auth()->user();
        if (!$user->canSwitchRole()) {
            abort(403);
        }
        $newRole  = $user->switchableRole();
        session(['active_role' => $newRole]);
        $dashboard = $newRole === 'freelancer' ? '/freelancer/dashboard' : '/cliente/dashboard';
        return redirect($dashboard);
    })->name('switch.role');

    // Global notifications
    Route::get('/notificacoes', \App\Livewire\NotificationPanel::class)->name('notifications');

    // KYC
    Route::get('/kyc', \App\Livewire\KycForm::class)->name('kyc.submit');

});

// ─── Freelancer Core ──────────────────────────────────────────────────────────
Route::middleware(['auth', 'verified', 'role:freelancer'])->group(function () {
    Route::get('/freelancer/dashboard',          \App\Livewire\Freelancer\Dashboard::class)->name('freelancer.dashboard');
    Route::get('/freelancer/notificacoes',       \App\Livewire\Freelancer\NotificationsPage::class)->name('freelancer.notifications');
    Route::get('/freelancer/configuracoes',      \App\Livewire\Freelancer\Settings::class)->name('freelancer.settings');
    Route::get('/freelancer/perfil/editar',      \App\Livewire\Freelancer\ProfileEditor::class)->name('freelancer.profile.edit');
    Route::get('/freelancer/portfolio',          \App\Livewire\Freelancer\PortfolioManager::class)->name('freelancer.portfolio');
    Route::get('/freelancer/onboarding',         \App\Livewire\Freelancer\Onboarding::class)->name('freelancer.onboarding');
    Route::get('/freelancer/financeiro',         \App\Livewire\Freelancer\FinancialPanel::class)->name('freelancer.financial');
    Route::get('/freelancer/carteira',           \App\Livewire\Freelancer\Wallet::class)->name('freelancer.wallet');
    Route::get('/freelancer/carteira/historico', \App\Livewire\Freelancer\WalletHistory::class)->name('freelancer.wallet.history');
});

// ─── Client Core ─────────────────────────────────────────────────────────────
Route::middleware(['auth', 'verified', 'role:cliente'])->group(function () {
    Route::get('/cliente/dashboard',     \App\Livewire\Client\Dashboard::class)->name('client.dashboard');
    Route::get('/cliente/perfil',        \App\Livewire\Client\Profile::class)->name('client.profile');
    Route::get('/cliente/perfil/editar', \App\Livewire\Client\Profile::class)->name('client.profile.edit');
    Route::get('/cliente/configuracoes', \App\Livewire\Client\Settings::class)->name('client.settings');
});
