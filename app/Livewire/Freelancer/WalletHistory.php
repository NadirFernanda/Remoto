<?php
namespace App\Livewire\Freelancer;

use Livewire\Component;
use App\Models\WalletLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

class WalletHistory extends Component
{
    public $tipo = '';
    public $logs = [];

    public function mount()
    {
        $this->loadLogs();
    }

    public function updatedTipo()
    {
        $this->loadLogs();
    }

    public function loadLogs()
    {
        $user = Auth::user();
        if (! Schema::hasTable('wallet_logs')) {
            $this->logs = collect();
            return;
        }

        $query = WalletLog::where('user_id', $user->id);
        if ($this->tipo) {
            $query->where('tipo', $this->tipo);
        }
        $this->logs = $query->orderByDesc('created_at')->take(50)->get();
    }

    public function render()
    {
        if (! Schema::hasTable('wallet_logs')) {
            $tipos = collect();
        } else {
            $tipos = WalletLog::where('user_id', Auth::id())->select('tipo')->distinct()->pluck('tipo');
        }
        return view('livewire.freelancer.wallet-history', [
            'logs' => $this->logs,
            'tipos' => $tipos,
            'tipo' => $this->tipo,
        ]);
    }
}
