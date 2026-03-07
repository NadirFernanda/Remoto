<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Models\Dispute;
use App\Models\DisputeMessage;
use App\Models\Service;

class DisputeCenter extends Component
{
    public Service $service;

    // Open dispute
    public string $reason = '';
    public string $description = '';

    // Message
    public string $newMessage = '';

    public $dispute = null;

    public function mount(Service $service)
    {
        $this->service = $service;
        $user = Auth::user();

        // Must be client or freelancer of this service
        if ($user->id !== $service->cliente_id && $user->id !== $service->freelancer_id) {
            abort(403);
        }

        $this->dispute = Dispute::where('service_id', $service->id)->latest()->first();
    }

    public function openDispute()
    {
        $this->validate([
            'reason'      => 'required|in:atraso,qualidade,nao_pagamento,outro',
            'description' => 'required|string|min:20|max:2000',
        ], [
            'reason.required'      => 'Selecione o motivo da disputa.',
            'description.required' => 'Descreva o problema detalhadamente.',
            'description.min'      => 'Descreva com pelo menos 20 caracteres.',
        ]);

        if ($this->dispute) {
            session()->flash('error', 'Já existe uma disputa aberta para este projeto.');
            return;
        }

        $this->dispute = Dispute::create([
            'service_id'  => $this->service->id,
            'opened_by'   => Auth::id(),
            'reason'      => $this->reason,
            'description' => $this->description,
            'status'      => 'aberta',
        ]);

        // First message = description
        DisputeMessage::create([
            'dispute_id' => $this->dispute->id,
            'user_id'    => Auth::id(),
            'message'    => $this->description,
        ]);

        $this->reason = '';
        $this->description = '';
        session()->flash('success', 'Disputa aberta. A equipa será notificada em breve.');
    }

    public function sendMessage()
    {
        $this->validate([
            'newMessage' => 'required|string|max:2000',
        ]);

        if (!$this->dispute || in_array($this->dispute->status, ['resolvida', 'encerrada'])) {
            session()->flash('error', 'Esta disputa já está encerrada.');
            return;
        }

        DisputeMessage::create([
            'dispute_id' => $this->dispute->id,
            'user_id'    => Auth::id(),
            'message'    => trim($this->newMessage),
        ]);

        $this->newMessage = '';
        $this->dispute->refresh();
    }

    public function render()
    {
        $messages = $this->dispute
            ? $this->dispute->messages()->with('user')->orderBy('created_at')->get()
            : collect();

        return view('livewire.dispute-center', compact('messages'));
    }
}
