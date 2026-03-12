<?php

namespace App\Livewire\Creator;

use Livewire\Component;
use App\Models\CreatorSubscription;
use App\Models\CreatorProfile;
use App\Models\Infoproduto;
use App\Models\InfoprodutoCompra;
use Illuminate\Support\Facades\Auth;

class Dashboard extends Component
{
    public function render()
    {
        $user = Auth::user();

        if (!$user->has_creator_profile) {
            return redirect()->route('creator.activate');
        }

        $creatorProfile = $user->creatorProfile;

        // Active subscribers
        $activeSubscriptions = CreatorSubscription::where('creator_id', $user->id)
            ->active()
            ->with('subscriber')
            ->latest()
            ->get();

        $totalSubscribers   = $activeSubscriptions->count();
        $monthlyEarnings    = $activeSubscriptions->sum('net_amount');

        $allTimeEarnings    = CreatorSubscription::where('creator_id', $user->id)
            ->sum('net_amount');

        // Infoprodutos
        $infoprodutos = Infoproduto::where('freelancer_id', $user->id)
            ->withCount('compras')
            ->latest()
            ->take(5)
            ->get();

        $infoprodutosEarnings = InfoprodutoCompra::whereHas(
            'infoproduto', fn($q) => $q->where('freelancer_id', $user->id)
        )->sum('valor_freelancer');

        return view('livewire.creator.dashboard', compact(
            'user',
            'creatorProfile',
            'activeSubscriptions',
            'totalSubscribers',
            'monthlyEarnings',
            'allTimeEarnings',
            'infoprodutos',
            'infoprodutosEarnings'
        ))->layout('layouts.dashboard', ['dashboardTitle' => 'Painel do Criador']);
    }
}
