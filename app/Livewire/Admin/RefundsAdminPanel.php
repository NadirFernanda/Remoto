<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Refund;

class RefundsAdminPanel extends Component
{
    public $status = '';
    public $search = '';

    public function approve($id)
    {
        $refund = Refund::find($id);
        if ($refund) {
            $refund->status = 'aprovado';
            $refund->save();
        }
    }

    public function reject($id)
    {
        $refund = Refund::find($id);
        if ($refund) {
            $refund->status = 'rejeitado';
            $refund->save();
        }
    }

    public function render()
    {
        $refunds = Refund::query()
            ->when($this->status, fn($q) => $q->where('status', $this->status))
            ->when($this->search, fn($q) => $q->where('reason', 'like', '%'.$this->search.'%'))
            ->orderByDesc('created_at')
            ->paginate(15);
        return view('livewire.admin.refunds-admin-panel', compact('refunds'));
    }
}
