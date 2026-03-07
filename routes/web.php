<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FreelancerListingController;
use App\Http\Controllers\FreelancerProfileController;
use App\Http\Controllers\PublicProjectsController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Auth\OtpVerificationController;
use App\Http\Controllers\Client\FinanceHistoryExportController;
use App\Http\Controllers\Client\ServiceTitleController;

// Homepage
Route::get('/', function () {
    return auth()->check() ? redirect('/dashboard') : view('welcome');
});

// Dashboard redirect based on role
Route::get('/dashboard', function () {
    $user = auth()->user();
    return match ($user->role) {
        'freelancer' => redirect()->route('freelancer.dashboard'),
        'admin' => redirect()->route('admin.dashboard'),
        default => redirect()->route('client.dashboard'),
    };
})->middleware('auth')->name('dashboard');

// --- Public routes ---
Route::get('/freelancers', [FreelancerListingController::class, 'index'])->name('freelancers.index');
Route::get('/freelancers/{user}', [FreelancerProfileController::class, 'show'])->name('freelancer.show');
Route::get('/projetos', [PublicProjectsController::class, 'index'])->name('public.projects');
Route::get('/projetos/{service}', [PublicProjectsController::class, 'show'])->name('public.project.show');

// --- OTP verification ---
Route::middleware('auth')->group(function () {
    Route::get('/otp', [OtpVerificationController::class, 'showOtpForm'])->name('otp.form');
    Route::post('/otp/send', [OtpVerificationController::class, 'sendOtp'])->name('otp.send');
    Route::post('/otp/verify', [OtpVerificationController::class, 'verifyOtp'])->name('otp.verify');
});

// --- Email verification ---
Route::post('/email/verification-notification', function () {
    auth()->user()->sendEmailVerificationNotification();
    return back()->with('message', 'Link de verificação enviado!');
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');

// --- Authenticated routes ---
Route::middleware('auth')->group(function () {

    // Profile
    Route::get('/perfil', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::post('/perfil', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/perfil/password', [ProfileController::class, 'update'])->name('profile.password');

    // Affiliate
    Route::post('/affiliate/generate', function () {
        $user = auth()->user();
        if (!$user->affiliate) {
            \App\Models\Affiliate::create([
                'user_id' => $user->id,
                'code' => strtoupper(\Illuminate\Support\Str::random(8)),
            ]);
        }
        return back()->with('success', 'Link de afiliado gerado!');
    })->name('affiliate.generate');

    // Notifications (generic)
    Route::get('/notificacoes', \App\Livewire\NotificationPanel::class)->name('notifications');

    // --- Freelancer routes ---
    Route::get('/freelancer/dashboard', \App\Livewire\Freelancer\Dashboard::class)->name('freelancer.dashboard');
    Route::get('/freelancer/notificacoes', \App\Livewire\Freelancer\NotificationsPage::class)->name('freelancer.notifications');
    Route::get('/freelancer/projetos-disponiveis', \App\Livewire\Freelancer\AvailableProjects::class)->name('freelancer.available-projects');
    Route::get('/freelancer/servico/{service}/review', \App\Livewire\Freelancer\ServiceReview::class)->name('freelancer.service.review');
    Route::get('/freelancer/servico/{service}/entrega', \App\Livewire\Freelancer\ServiceDelivery::class)->name('freelancer.service.delivery');
    Route::get('/freelancer/configuracoes', \App\Livewire\Client\Settings::class)->name('freelancer.settings');

    // --- Client routes ---
    Route::get('/cliente/dashboard', \App\Livewire\Client\Dashboard::class)->name('client.dashboard');
    Route::get('/cliente/perfil', \App\Livewire\Client\Profile::class)->name('client.profile');
    Route::get('/cliente/briefing', \App\Livewire\Client\Briefing::class)->name('client.briefing');
    Route::get('/cliente/pedidos', \App\Livewire\Client\OrderHistory::class)->name('client.orders');
    Route::get('/cliente/servico/{service}/cancelar', \App\Livewire\Client\ServiceCancel::class)->name('client.service.cancel');
    Route::get('/cliente/servico/{service}/valor', \App\Livewire\Client\ServiceValue::class)->name('client.value');
    Route::get('/cliente/servico/{service}/pagamento', \App\Livewire\Client\PaymentEscrow::class)->name('client.payment');
    Route::get('/cliente/financeiro/export', [FinanceHistoryExportController::class, 'exportCsv'])->name('client.finance.exportCsv');
    Route::put('/cliente/servico/{service}/titulo', [ServiceTitleController::class, 'update'])->name('client.service.title.update');

    // --- Chat ---
    Route::get('/chat/servico/{service}', \App\Livewire\Chat\ServiceChat::class)->name('service.chat');

    // --- Admin ---
    Route::get('/admin/dashboard', \App\Livewire\Admin\Dashboard::class)->name('admin.dashboard');
});
