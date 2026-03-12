<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FreelancerListingController;
use App\Http\Controllers\FreelancerProfileController;
use App\Http\Controllers\PublicProjectsController;
use App\Http\Controllers\Auth\OtpVerificationController;
use App\Http\Controllers\Client\FinanceHistoryExportController;
use App\Http\Controllers\Client\ServiceTitleController;
use App\Http\Controllers\ChatFileUploadController;

Route::middleware('auth')->get('/cliente/pagamentos', \App\Livewire\Client\FinanceHistory::class)->name('client.payments');

// ─── Módulo Social ────────────────────────────────────────────────────────────
// Public: anyone can browse feed and creator profiles
Route::get('/social', \App\Livewire\Social\Feed::class)->name('social.feed');
Route::get('/social/criador/{user}', \App\Livewire\Social\CreatorProfile::class)->name('social.creator');

// Authenticated: create post (freelancer only), bookmarks, stories
Route::middleware('auth')->group(function () {
    Route::get('/social/publicar', \App\Livewire\Social\CreatePost::class)->name('social.create');
    Route::get('/social/guardados', function () {
        return redirect('/social?bookmarkedOnly=1');
    })->name('social.bookmarks');
});

// ─── Loja de Infoprodutos (public) ───────────────────────────────────────────
Route::get('/loja', \App\Livewire\Loja\Vitrine::class)->name('loja.index');
Route::get('/loja/{produto:slug}', \App\Livewire\Loja\ProdutoDetalhe::class)->name('loja.show');

// Homepage
Route::get('/', function () {
    return view('welcome');
})->name('home');

// Dashboard redirect based on role
Route::get('/dashboard', function () {
    $user = auth()->user();
    return match ($user->activeRole()) {
        'freelancer' => redirect()->route('freelancer.dashboard'),
        'admin'      => redirect()->route('admin.dashboard'),
        'creator'    => redirect()->route('creator.dashboard'),
        default      => redirect()->route('client.dashboard'),
    };
})->middleware('auth')->name('dashboard');

// ─── Creator / Seguidor Module ────────────────────────────────────────────────
Route::middleware('auth')->prefix('creator')->name('creator.')->group(function () {
    Route::get('/dashboard', \App\Livewire\Creator\Dashboard::class)->name('dashboard');
    Route::get('/activar/{profile?}', \App\Livewire\Creator\ActivateProfile::class)->name('activate');
});
// Public creator profile (already handled by social.creator route)

// --- Institutional / Sobre pages ---
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

// --- Public routes ---
Route::get('/freelancers', [FreelancerListingController::class, 'index'])->name('freelancers.index');
Route::get('/freelancers/buscar', \App\Livewire\FreelancerSearch::class)->name('freelancers.search');
Route::get('/freelancers/{user}', [FreelancerProfileController::class, 'show'])->name('freelancer.show');
Route::get('/projetos', [PublicProjectsController::class, 'index'])->name('public.projects');
Route::get('/projetos/{service}', [PublicProjectsController::class, 'show'])->name('public.project.show');
Route::post('/projetos/{service}/candidatar', function (\App\Models\Service $service) {
    $user = auth()->user();
    if (!$user || $user->id === $service->cliente_id) {
        return back()->with('error', 'Você não pode aceitar este projeto.');
    }
    $candidate = $service->candidates()->where('freelancer_id', $user->id)->first();
    if (!$candidate) {
        $service->candidates()->create([
            'freelancer_id' => $user->id,
            'status' => 'pending',
        ]);
    }
    return redirect()->route('freelancer.dashboard')->with('success', 'Candidatura registrada! Aguarde o cliente responder.');
})->middleware('auth')->name('service.candidatar');

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

// --- Chat file upload ---
Route::post('/chat/upload-file', [ChatFileUploadController::class, 'upload'])
    ->middleware('auth')
    ->name('chat.upload.file');

// --- Authenticated routes ---
Route::middleware('auth')->group(function () {

    // Role switching (cliente <-> freelancer)
    Route::post('/switch-role', function () {
        $user = auth()->user();
        if (!$user->canSwitchRole()) {
            abort(403);
        }
        $newRole = $user->switchableRole();
        session(['active_role' => $newRole]);
        $dashboard = $newRole === 'freelancer' ? '/freelancer/dashboard' : '/cliente/dashboard';
        return redirect($dashboard);
    })->name('switch.role');

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
    Route::get('/freelancer/propostas', \App\Livewire\Freelancer\Proposals::class)->name('freelancer.proposals');
    Route::get('/freelancer/notificacoes', \App\Livewire\Freelancer\NotificationsPage::class)->name('freelancer.notifications');
    Route::get('/freelancer/projetos-disponiveis', \App\Livewire\Freelancer\AvailableProjects::class)->name('freelancer.available-projects');
    Route::get('/freelancer/servico/{service}/review', \App\Livewire\Freelancer\ServiceReview::class)->name('freelancer.service.review');
    Route::get('/freelancer/servico/{service}/entrega', \App\Livewire\Freelancer\ServiceDelivery::class)->name('freelancer.service.delivery');
    Route::get('/freelancer/configuracoes', \App\Livewire\Client\Settings::class)->name('freelancer.settings');
    Route::get('/freelancer/perfil/editar', \App\Livewire\Freelancer\ProfileEditor::class)->name('freelancer.profile.edit');
    Route::get('/freelancer/portfolio', \App\Livewire\Freelancer\PortfolioManager::class)->name('freelancer.portfolio');
    Route::get('/freelancer/financeiro', \App\Livewire\Freelancer\FinancialPanel::class)->name('freelancer.financial');
    Route::get('/freelancer/loja', \App\Livewire\Freelancer\Loja::class)->name('freelancer.loja');
    Route::get('/kyc', \App\Livewire\KycForm::class)->name('kyc.submit');

    // --- Client routes ---
    Route::get('/cliente/dashboard', \App\Livewire\Client\Dashboard::class)->name('client.dashboard');
    Route::get('/cliente/projetos', \App\Livewire\Client\ProjectManager::class)->name('client.projects');
    Route::get('/cliente/projetos/matching/{service}', \App\Livewire\Client\FreelancerMatching::class)->name('client.matching');
    Route::get('/cliente/perfil', \App\Livewire\Client\Profile::class)->name('client.profile');
    Route::get('/cliente/briefing', \App\Livewire\Client\Briefing::class)->name('client.briefing');
    Route::get('/cliente/pedidos', \App\Livewire\Client\OrderHistory::class)->name('client.orders');
    Route::get('/cliente/servico/{service}/cancelar', \App\Livewire\Client\ServiceCancel::class)->name('client.service.cancel');
    Route::get('/cliente/servico/{service}/valor', \App\Livewire\Client\ServiceValue::class)->name('client.value');
    Route::get('/cliente/servico/{service}/pagamento', \App\Livewire\Client\PaymentEscrow::class)->name('client.payment');
    Route::get('/cliente/financeiro/export', [FinanceHistoryExportController::class, 'exportCsv'])->name('client.finance.exportCsv');
    Route::put('/cliente/servico/{service}/titulo', [ServiceTitleController::class, 'update'])->name('client.service.title.update');

    // --- Reviews ---
    Route::get('/servico/{service}/avaliar', \App\Livewire\LeaveReview::class)->name('service.review.leave');
    Route::get('/avaliacoes', \App\Livewire\Client\ReviewPanel::class)->name('reviews.panel');

    // --- Disputes ---
    Route::get('/servico/{service}/disputa', \App\Livewire\DisputeCenter::class)->name('service.dispute');

    // --- Chat ---
    Route::get('/chat/servico/{service}', \App\Livewire\Chat\ServiceChat::class)->name('service.chat');

    // --- Admin ---
    Route::get('/admin/dashboard', \App\Livewire\Admin\Dashboard::class)->name('admin.dashboard');
    Route::get('/admin/users', \App\Livewire\Admin\Users::class)->name('admin.users');
    Route::get('/admin/services', \App\Livewire\Admin\Services::class)->name('admin.services');
    Route::get('/admin/disputas', \App\Livewire\Admin\DisputeAdmin::class)->name('admin.disputes');
    Route::get('/admin/auditoria', \App\Livewire\Admin\AuditLogs::class)->name('admin.audit');
    Route::get('/admin/loja', \App\Livewire\Admin\LojaAdmin::class)->name('admin.loja');
    Route::get('/admin/social', \App\Livewire\Admin\SocialModeration::class)->name('admin.social.moderation');
    
        // Painel admin de reembolsos
        Route::middleware(['auth', 'can:admin'])->group(function () {
            Route::get('/admin/reembolsos', \App\Livewire\Admin\RefundsAdminPanel::class)->name('admin.refunds');
        });

    // Admin — Gestão Financeira
    Route::get('/admin/financeiro', \App\Livewire\Admin\Financial::class)->name('admin.financial')->middleware('admin.module:financeiro');
    Route::get('/admin/comissoes', \App\Livewire\Admin\Commissions::class)->name('admin.commissions')->middleware('admin.module:financeiro');
    Route::get('/admin/saques', \App\Livewire\Admin\Payouts::class)->name('admin.payouts')->middleware('admin.module:financeiro');

    // Admin — Suporte
    Route::get('/admin/notificacoes-massa', \App\Livewire\Admin\MassNotifications::class)->name('admin.notifications.mass')->middleware('admin.module:suporte');

    // Admin — Configurações (master only)
    Route::get('/admin/configuracoes', \App\Livewire\Admin\Settings::class)->name('admin.settings')->middleware('admin.module:settings');
    Route::get('/admin/categorias', \App\Livewire\Admin\Categories::class)->name('admin.categories')->middleware('admin.module:settings');
    Route::get('/admin/taxas', \App\Livewire\Admin\Fees::class)->name('admin.fees')->middleware('admin.module:settings');

    // Histórico de transações
   Route::get('/transacoes', [\App\Http\Controllers\TransactionHistoryController::class, 'index'])->name('transactions.history')->middleware('auth');
   // Reembolso de serviço
  Route::post('/cliente/servico/{service}/reembolso', [\App\Http\Controllers\ServiceRefundController::class, 'requestRefund'])->name('client.service.refund');
  // Liberação de pagamento (escrow)
  Route::post('/cliente/servico/{service}/liberar-pagamento', [\App\Http\Controllers\ServiceEscrowController::class, 'releasePayment'])->name('client.service.release_payment');
});

// Página de projetos do freelancer
Route::get('/freelancer/projetos', \App\Livewire\Freelancer\ProjectManager::class)->name('freelancer.projects');
Route::middleware('auth')->group(function () {
    Route::get('/cliente/reembolsos', \App\Livewire\Client\RefundsPanel::class)->name('client.refunds');
});
// --- Refunds ---
Route::middleware('auth')->group(function () {
    Route::get('/cliente/reembolso', \App\Livewire\Client\RefundRequest::class)->name('client.refund.request');
});
Route::middleware('auth')->get('/cliente/perfil/editar', \App\Livewire\Client\Profile::class)->name('client.profile.edit');
Route::middleware('auth')->get('/cliente/relatorios', \App\Livewire\Client\OrderHistory::class)->name('client.reports');