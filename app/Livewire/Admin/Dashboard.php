<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\User;
use App\Models\Service;
use App\Models\Dispute;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Cache;

class Dashboard extends Component
{
    public array $stats = [];
    public array $funnel = [];
    public array $revenueByDay = [];
    public $recentLogs;
    public $recentUsers;
    public int $period = 30;

    public function mount(): void
    {
        abort_if(auth()->user()?->role !== 'admin', 403);
        $this->loadStats();
    }

    public function updatedPeriod(): void
    {
        $this->loadStats();
    }

    private function loadStats(): void
    {
        $period = $this->period;
        $cacheKey = "admin_dashboard_stats_{$period}";

        // Cache por 3 minutos — evita 20+ queries DB em cada mudança de período
        $cached = Cache::remember($cacheKey, 180, function () use ($period) {
            $since = now()->subDays($period);

            $totalServices  = Service::count() ?: 1;
            $completedCount = Service::where('status', 'completed')->count();

            // Funnel: registered → posted → hired → completed
            $registered = User::where('role', 'cliente')->count();
            $posted     = User::where('role', 'cliente')->has('servicesAsClient')->count();
            $hired      = User::where('role', 'cliente')
                ->whereHas('servicesAsClient', fn($q) => $q->whereNotNull('freelancer_id'))
                ->count();
            $completed  = User::where('role', 'cliente')
                ->whereHas('servicesAsClient', fn($q) => $q->where('status', 'completed'))
                ->count();

            $funnel = compact('registered', 'posted', 'hired', 'completed');

            $revenueByDay = Service::whereIn('status', ['completed', 'delivered'])
                ->where('updated_at', '>=', $since)
                ->selectRaw('DATE(updated_at) as day, SUM(taxa) as total')
                ->groupBy('day')
                ->orderBy('day')
                ->pluck('total', 'day')
                ->toArray();

            $stats = [
                'gmv_total'          => Service::whereIn('status', ['completed', 'delivered'])->sum('valor'),
                'gmv_period'         => Service::whereIn('status', ['completed', 'delivered'])->where('updated_at', '>=', $since)->sum('valor'),
                'projects_total'     => Service::count(),
                'projects_active'    => Service::where('status', 'in_progress')->count(),
                'projects_published' => Service::where('status', 'published')->count(),
                'projects_delivered' => Service::where('status', 'delivered')->count(),
                'projects_completed' => $completedCount,
                'projects_cancelled' => Service::where('status', 'cancelled')->count(),
                'projects_period'    => Service::where('created_at', '>=', $since)->count(),
                'conversion_rate'    => round($completedCount / $totalServices * 100, 1),
                'revenue_total'      => Service::whereIn('status', ['completed', 'delivered'])->sum('taxa'),
                'revenue_period'     => Service::whereIn('status', ['completed', 'delivered'])->where('updated_at', '>=', $since)->sum('taxa'),
                'users_total'        => User::count(),
                'users_new_period'   => User::where('created_at', '>=', $since)->count(),
                'users_clients'      => User::where('role', 'cliente')->count(),
                'users_freelancers'  => User::where('role', 'freelancer')->count(),
                'kyc_pending'        => User::where('kyc_status', 'pending')->where('role', '!=', 'admin')->count(),
                'users_suspended'    => User::where('is_suspended', true)->count(),
                'disputes_open'      => Dispute::where('status', 'aberta')->count(),
                'disputes_mediation' => Dispute::where('status', 'em_mediacao')->count(),
                'moderacao_pendente' => Service::where('status', 'em_moderacao')->count(),
            ];

            $recentLogs  = AuditLog::with('user')->orderByDesc('created_at')->take(8)->get();
            $recentUsers = User::orderByDesc('created_at')->take(10)->get();

            return compact('funnel', 'revenueByDay', 'stats', 'recentLogs', 'recentUsers');
        });

        $this->funnel       = $cached['funnel'];
        $this->revenueByDay = $cached['revenueByDay'];
        $this->stats        = $cached['stats'];
        $this->recentLogs   = $cached['recentLogs'];
        $this->recentUsers  = $cached['recentUsers'];
    }

    public function render()
    {
        return view('livewire.admin.dashboard')
            ->layout('layouts.dashboard', [
                'dashboardTitle' => 'Painel do Administrador'
            ]);
    }
}

