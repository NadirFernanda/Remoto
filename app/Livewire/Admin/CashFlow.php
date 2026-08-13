<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\CashFlowClosing;
use App\Services\CashFlowClosingService;
use App\Services\CashFlowService;
use Carbon\Carbon;

class CashFlow extends Component
{
    public string $period     = 'month';
    public string $dateStart  = '';
    public string $dateEnd    = '';

    public function mount(): void
    {
        abort_if(auth()->user()?->role !== 'admin', 403);
    }

    public function updatedPeriod(): void
    {
        $this->dateStart = '';
        $this->dateEnd   = '';
    }

    private function startDate(): Carbon
    {
        if ($this->dateStart) {
            return Carbon::parse($this->dateStart)->startOfDay();
        }

        return match ($this->period) {
            'week'  => Carbon::now()->startOfWeek(),
            'year'  => Carbon::now()->startOfYear(),
            default => Carbon::now()->startOfMonth(),
        };
    }

    private function endDate(): Carbon
    {
        return $this->dateEnd
            ? Carbon::parse($this->dateEnd)->endOfDay()
            : Carbon::now()->endOfDay();
    }

    /** Fecha manualmente o dia de hoje (além do fecho automático agendado às 23:59). */
    public function fecharHoje(): void
    {
        $admin = auth()->user();
        app(CashFlowClosingService::class)->closeDay(Carbon::today(), $admin->name ?? 'admin');
        session()->flash('cashflow_success', 'Fecho de hoje registado com sucesso.');
    }

    public function render()
    {
        $start = $this->startDate();
        $end   = $this->endDate();

        $resultado = (new CashFlowService())->calculate($start, $end);

        $fechos = CashFlowClosing::orderByDesc('data')->take(30)->get();

        return view('livewire.admin.cash-flow', array_merge($resultado, [
            'fechos' => $fechos,
        ]))->layout('layouts.dashboard', ['dashboardTitle' => 'Fluxo de Caixa']);
    }
}
