<?php

namespace App\Livewire\Client;

use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\Attributes\On;
use App\Models\Proposal;
use Auth;

class SendProposal extends Component
{
    use WithFileUploads;
    public $show = false;
    public $recipient_id;
    public $message;
    public $value;
    public $attachments = [];

    protected function rules()
    {
        return [
            'recipient_id' => 'required|exists:users,id',
            'message' => 'nullable|string|max:5000',
            'value' => 'nullable|numeric|min:0',
            'attachments' => 'nullable|array|max:5',
            'attachments.*' => 'file|max:5120|mimes:pdf,doc,docx,xlsx,png,jpg,jpeg,zip',
        ];
    }

    #[On('openProposal')]
    public function openProposal($recipientId)
    {
        $this->recipient_id = $recipientId;
        $this->show = true;
    }

    public function close()
    {
        $this->reset(['show', 'recipient_id', 'message', 'value']);
    }

    public function send()
    {
        $this->validate();
        $user = Auth::user();
        if (!$user) {
            session()->flash('error', 'Precisa estar autenticado para enviar proposta.');
            return;
        }

        $fee = $this->value ? round($this->value * 0.10, 2) : 0;
        $net = $this->value ? round($this->value - $fee, 2) : 0;

        $proposal = Proposal::create([
            'sender_id' => $user->id,
            'recipient_id' => $this->recipient_id,
            'message' => $this->message,
            'value' => $this->value,
            'fee' => $fee,
            'net' => $net,
            'type' => 'direct_invite',
            'status' => 'pending',
        ]);

        // Handle attachments
        $stored = [];
        if (!empty($this->attachments)) {
            foreach ($this->attachments as $file) {
                if (!$file) continue;
                $path = $file->storePubliclyAs('proposals/'.$proposal->id, uniqid().'_'.preg_replace('/[^A-Za-z0-9_.-]/', '_', $file->getClientOriginalName()), 'public');
                $stored[] = $path;
            }
        }

        if (!empty($stored)) {
            $proposal->attachments = $stored;
            $proposal->save();
        }

        session()->flash('success', 'Proposta enviada com sucesso.');
        $this->close();
        $this->dispatch('proposalSent');
    }

    public function render()
    {
        return view('livewire.client.send-proposal');
    }
}
