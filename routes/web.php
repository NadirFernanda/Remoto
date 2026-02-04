<?php
require_once __DIR__.'/auth.php';

use Illuminate\Support\Facades\Route;
use App\Http\Middleware\Role;

Route::aliasMiddleware('role', Role::class);

use App\Livewire\NotificationPanel;
use App\Livewire\Chat\ServiceChat;
use App\Livewire\Freelancer\Wallet as FreelancerWallet;
use App\Livewire\Freelancer\ServiceDelivery;
use App\Livewire\Freelancer\ServiceReview;
use App\Livewire\Freelancer\Dashboard as FreelancerDashboard;
use App\Livewire\Freelancer\AffiliatePanel;
use App\Livewire\Client\Dashboard;
use App\Livewire\Freelancer\SponsorshipPanel;

use App\Livewire\Client\OrderHistory;
use App\Livewire\Client\ReviewPanel;
use App\Livewire\Client\Profile;
use App\Livewire\Client\Settings;
use App\Livewire\Client\ServiceCancel;
use App\Http\Controllers\PublicProjectsController;
use App\Livewire\Admin\Dashboard as AdminDashboard;

Route::middleware(['auth'])->group(function () {
	Route::get('/cliente/pedido/{service}/cancelar', ServiceCancel::class)->name('client.service.cancel');
	Route::get('/cliente/financeiro/export-csv', [\App\Livewire\Client\FinanceHistory::class, 'exportCsv'])->name('client.finance.exportCsv');
});

Route::middleware(['auth'])->group(function () {
	Route::get('/cliente/dashboard', \App\Livewire\Client\Dashboard::class)->name('client.dashboard');
});
Route::middleware(['auth'])->group(function () {
	Route::get('/notificacoes', NotificationPanel::class)->name('notifications');
	Route::get('/cliente/perfil', Profile::class)->name('client.profile');
	Route::get('/cliente/configuracoes', Settings::class)->name('client.settings');
});
Route::middleware(['auth'])->group(function () {
	Route::get('/servico/{service}/chat', ServiceChat::class)->name('service.chat');
});

Route::middleware(['auth', 'role:freelancer'])->group(function () {
	Route::get('/freelancer/carteira', FreelancerWallet::class)->name('freelancer.wallet');
	Route::get('/freelancer/entrega/{service}', ServiceDelivery::class)->name('freelancer.service.delivery');
	Route::get('/freelancer/servico/{service}', ServiceReview::class)->name('freelancer.service.review');
	Route::get('/freelancer/dashboard', FreelancerDashboard::class)->name('freelancer.dashboard');
	Route::get('/freelancer/afiliados', AffiliatePanel::class)->name('freelancer.affiliate');
	Route::get('/freelancer/patrocinio', SponsorshipPanel::class)->name('freelancer.sponsorship');
	Route::get('/freelancer/projetos-disponiveis', \App\Livewire\Freelancer\AvailableProjects::class)->name('freelancer.available-projects');
	Route::view('/freelancer/configuracoes', 'livewire.freelancer.settings')->name('freelancer.settings');
});

Route::middleware(['auth', 'role:admin'])->group(function () {
	Route::get('/admin/dashboard', AdminDashboard::class)->name('admin.dashboard');
});

// Removed duplicate middleware for client profile and settings

use App\Livewire\Client\Briefing;
use App\Livewire\Client\ServiceValue;
use App\Livewire\Client\PaymentEscrow;

Route::get('/', function () {
	return view('welcome');
})->name('home');


Route::middleware(['auth'])->group(function () {
	Route::get('/briefing', function () {
		return view('client-briefing');
	})->name('client.briefing');
	Route::get('/valor', function () {
		return view('client-value');
	})->name('client.value');
	Route::get('/pagamento', function () {
		return view('client-payment');
	})->name('client.payment');
	Route::get('/cliente/pedidos', function () {
		return view('client-orders');
	})->name('client.orders');
});

// Rota fake para simular redirecionamento e retorno do PayPal
use Illuminate\Support\Facades\Route as RouteFacade;

RouteFacade::get('/pagamento/paypal', function () {
	// Exibe tela de redirecionamento fake
	$sessionId = session()->getId();
	// Recupera briefing e pagamento da query string e salva na sessão para o retorno
	$briefing = request()->query('briefing');
	$pagamento = request()->query('pagamento');
	if ($briefing) {
		session(['briefing' => json_decode($briefing, true)]);
	}
	if ($pagamento) {
		session(['pagamento' => json_decode($pagamento, true)]);
	}
	return view('paypal-redirect-layout', [
		'session_id' => $sessionId
	]);
})->name('client.paypal');

// Rota que simula o retorno do PayPal e cria o pedido

RouteFacade::get('/pagamento/paypal/retorno', function () {
	$sessionId = session()->getId();
	$user = auth()->user();
	$briefing = session('briefing', null);
	$pagamento = session('pagamento', null);
	if (!$user || !$briefing || !$pagamento) {
		$debug = [];
		if (!$user) $debug[] = 'Usuário não autenticado';
		if (!$briefing) $debug[] = 'Briefing ausente na sessão';
		if (!$pagamento) $debug[] = 'Dados de pagamento ausentes na sessão';
		$debug[] = 'Session ID: ' . $sessionId;
		return response()->view('paypal-redirect', [
			'debug' => $debug,
			'session_id' => $sessionId
		]);
	}
	$briefing_processado = (new App\Livewire\Client\PaymentEscrow)->processBriefing($briefing);
	$service = App\Models\Service::create([
		'cliente_id' => $user->id,
		'titulo' => $briefing_processado['business_type'] ?? 'Pedido sem título',
		'briefing' => json_encode($briefing_processado),
		'valor' => $pagamento['valor'],
		'taxa' => $pagamento['taxa'],
		'valor_liquido' => $pagamento['valor_liquido'],
		'status' => 'published',
	]);
	if ($service) {
		return redirect()->route('client.orders')->with('success', 'Pagamento via PayPal realizado e pedido publicado com sucesso!');
	} else {
		return redirect()->route('client.payment')->with('error', 'Erro ao criar o pedido. Tente novamente.');
	}
})->name('client.paypal.return');

Route::get('/projetos', [PublicProjectsController::class, 'index'])->name('public.projects');

use App\Http\Controllers\Client\ServiceTitleController;

Route::middleware(['auth'])->post('/cliente/pedido/{service}/cancelar/edit-title', [ServiceTitleController::class, 'update']);
use Illuminate\Foundation\Auth\EmailVerificationRequest;

// Rotas de verificação de e-mail Laravel
Route::get('/email/verify', function () {
	return view('auth.verify-email');
})->middleware('auth')->name('verification.notice');

Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
	$request->fulfill();
	return redirect('/home');
})->middleware(['auth', 'signed'])->name('verification.verify');

Route::post('/email/verification-notification', function () {
	request()->user()->sendEmailVerificationNotification();
	return back()->with('message', 'Verification link sent!');
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');