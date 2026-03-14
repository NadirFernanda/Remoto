<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Marketplace\Controllers\FreelancerListingController;
use App\Modules\Marketplace\Controllers\FreelancerProfileController;
use App\Modules\Marketplace\Controllers\PublicProjectsController;
use App\Modules\Marketplace\Controllers\ServiceTitleController;

// ─── Marketplace Module Routes ────────────────────────────────────────────────

// Public freelancer & project listing
Route::middleware('web')->group(function () {
    Route::get('/freelancers', [FreelancerListingController::class, 'index'])->name('freelancers.index');
    Route::get('/freelancers/buscar', \App\Livewire\FreelancerSearch::class)->name('freelancers.search');
    Route::get('/freelancers/{user}', [FreelancerProfileController::class, 'show'])->name('freelancer.show');
    Route::get('/projetos', [PublicProjectsController::class, 'index'])->name('public.projects');
    Route::get('/projetos/{service}', [PublicProjectsController::class, 'show'])->name('public.project.show');
});

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
})->middleware(['web', 'auth', 'throttle:proposals'])->name('service.candidatar');

// Authenticated marketplace routes
Route::middleware(['web', 'auth'])->group(function () {
    // Freelancer marketplace
    Route::get('/freelancer/projetos', \App\Livewire\Freelancer\ProjectManager::class)->name('freelancer.projects');
    Route::get('/freelancer/projetos-disponiveis', \App\Livewire\Freelancer\AvailableProjects::class)->name('freelancer.available-projects');
    Route::get('/freelancer/servico/{service}/review', \App\Livewire\Freelancer\ServiceReview::class)->name('freelancer.service.review');
    Route::get('/freelancer/servico/{service}/entrega', \App\Livewire\Freelancer\ServiceDelivery::class)->name('freelancer.service.delivery');
    Route::get('/freelancer/propostas', \App\Livewire\Freelancer\Proposals::class)->name('freelancer.proposals');

    // Client marketplace
    Route::get('/cliente/projetos', \App\Livewire\Client\ProjectManager::class)->name('client.projects');
    Route::get('/cliente/projetos/matching/{service}', \App\Livewire\Client\FreelancerMatching::class)->name('client.matching');
    Route::get('/cliente/briefing', \App\Livewire\Client\Briefing::class)->name('client.briefing');
    Route::get('/cliente/pedidos', \App\Livewire\Client\OrderHistory::class)->name('client.orders');
    Route::get('/cliente/servico/{service}/cancelar', \App\Livewire\Client\ServiceCancel::class)->name('client.service.cancel');
    Route::put('/cliente/servico/{service}/titulo', [ServiceTitleController::class, 'update'])->name('client.service.title.update');

    // Reviews & Disputes (cross-cutting but tied to marketplace services)
    Route::get('/servico/{service}/avaliar', \App\Livewire\LeaveReview::class)->name('service.review.leave');
    Route::get('/avaliacoes', \App\Livewire\Client\ReviewPanel::class)->name('reviews.panel');
    Route::get('/servico/{service}/disputa', \App\Livewire\DisputeCenter::class)->name('service.dispute');
});
