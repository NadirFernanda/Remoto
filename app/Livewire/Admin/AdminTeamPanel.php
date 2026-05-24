<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\User;
use App\Models\AuditLog;
use App\Models\SupportTicket;
use App\Models\SupportTicketReply;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AdminTeamPanel extends Component
{
    public string $periodo = 'hoje'; // hoje | semana | mes
    public ?int $selectedAdminId = null;

    public function mount(): void
    {
        abort_if(auth()->user()?->role !== 'admin', 403);
    }

    public function selectAdmin(int $id): void
    {
        $this->selectedAdminId = ($this->selectedAdminId === $id) ? null : $id;
    }

    public function render()
    {
        $desde = match ($this->periodo) {
            'semana' => now()->startOfWeek(),
            'mes'    => now()->startOfMonth(),
            default  => now()->startOfDay(),
        };

        // Todos os admins activos
        $admins = User::where('role', 'admin')
            ->where('status', 'active')
            ->orderByRaw("CASE WHEN last_seen_at >= ? THEN 0 WHEN last_seen_at >= ? THEN 1 ELSE 2 END",
                [now()->subMinutes(5), now()->subMinutes(30)])
            ->orderBy('name')
            ->get();

        // Acções de cada admin no período (via audit_logs)
        $accoesIds = $admins->pluck('id');
        $accoesCount = AuditLog::whereIn('user_id', $accoesIds)
            ->where('created_at', '>=', $desde)
            ->select('user_id', DB::raw('count(*) as total'))
            ->groupBy('user_id')
            ->pluck('total', 'user_id');

        // Tickets de suporte respondidos por cada admin no período
        $ticketsRespondidos = SupportTicketReply::whereIn('user_id', $accoesIds)
            ->where('is_admin_reply', true)
            ->where('created_at', '>=', $desde)
            ->select('user_id', DB::raw('count(*) as total'))
            ->groupBy('user_id')
            ->pluck('total', 'user_id');

        // Últimas acções por admin (para o painel de detalhe)
        $ultimasAccoes = [];
        if ($this->selectedAdminId) {
            $ultimasAccoes = AuditLog::where('user_id', $this->selectedAdminId)
                ->latest()
                ->limit(15)
                ->get();
        }

        // Totais globais
        $overview = [
            'online'  => $admins->filter(fn($a) => $a->isOnline())->count(),
            'idle'    => $admins->filter(fn($a) => $a->isIdle())->count(),
            'offline' => $admins->filter(fn($a) => !$a->isOnline() && !$a->isIdle())->count(),
            'total_accoes' => $accoesCount->sum(),
            'total_tickets' => $ticketsRespondidos->sum(),
            'tickets_abertos' => SupportTicket::where('status', 'aberto')->count(),
        ];

        return view('livewire.admin.admin-team-panel', compact(
            'admins', 'accoesCount', 'ticketsRespondidos', 'ultimasAccoes', 'overview'
        ))->layout('layouts.dashboard', ['dashboardTitle' => 'Painel de Equipa RH']);
    }
}
