<?php

namespace App\Livewire\Client;

use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\Attributes\On;
use App\Models\Proposal;
use App\Models\Service;
use App\Models\Notification;
use Auth;

class SendProposal extends Component
{
    use WithFileUploads;

    public $show        = false;
    public $recipient_id;
    public $title       = '';
    public $message     = '';
    public $value;
    public $attachments = [];
    public bool $sent     = false;
    public $service_id   = null;

    protected function rules(): array
    {
        return [
            'recipient_id' => 'required|exists:users,id',
            'title'        => 'required|string|max:120',
            'message'      => 'required|string|max:5000',
            'value'        => 'nullable|numeric|min:0',
            'attachments'  => 'nullable|array|max:5',
            'attachments.*'=> 'file|max:5120|mimes:pdf,doc,docx,xlsx,png,jpg,jpeg,zip',
        ];
    }

    protected $messages = [
        'title.required'   => 'Indique o título do projeto.',
        'message.required' => 'Escreva uma mensagem para o freelancer.',
    ];

    #[On('openProposal')]
    public function openProposal($recipientId)
    {
        $this->reset(['title', 'message', 'value', 'attachments']);
        $this->recipient_id = $recipientId;
        $this->show = true;
    }

    public function close()
    {
        $this->dispatch('proposalModalClosed');
        $this->reset(['show', 'sent', 'service_id', 'recipient_id', 'title', 'message', 'value', 'attachments']);
    }

    public function send()
    {
        $user = Auth::user();

        if (!$user) {
            $this->close();
            return redirect()->route('login');
        }

        $this->validate();

        // Guard: não pode convidar a si mesmo
        if ($user->id == $this->recipient_id) {
            $this->addError('message', 'Não pode enviar uma proposta para si mesmo.');
            return;
        }

        // Guard: já existe proposta pendente para este freelancer
        $exists = Proposal::where('sender_id', $user->id)
            ->where('recipient_id', $this->recipient_id)
            ->where('status', 'pending')
            ->exists();

        if ($exists) {
            $this->addError('message', 'Já tem uma proposta pendente com este freelancer.');
            return;
        }

        $fee = $this->value ? round($this->value * 0.10, 2) : 0;
        $net = $this->value ? round($this->value - $fee, 2) : 0;

        // Criar o Service em modo negociação (canal de chat)
        $service = Service::create([
            'cliente_id'    => $user->id,
            'freelancer_id' => $this->recipient_id,
            'titulo'        => $this->title,
            'briefing'      => $this->message,
            'service_type'  => 'direct_invite',
            'valor'         => $this->value ?? 0,
            'taxa'          => $fee,
            'valor_liquido' => $net,
            'status'        => 'negotiating',
        ]);

        $proposal = Proposal::create([
            'sender_id'    => $user->id,
            'recipient_id' => $this->recipient_id,
            'title'        => $this->title,
            'message'      => $this->message,
            'value'        => $this->value,
            'fee'          => $fee,
            'net'          => $net,
            'type'         => 'direct_invite',
            'status'       => 'pending',
            'service_id'   => $service->id,
        ]);

        // Guardar anexos
        $stored = [];
        foreach ($this->attachments ?? [] as $file) {
            if (!$file) continue;
            $path = $file->storePubliclyAs(
                'proposals/' . $proposal->id,
                uniqid() . '_' . preg_replace('/[^A-Za-z0-9_.-]/', '_', $file->getClientOriginalName()),
                'public'
            );
            $stored[] = $path;
        }
        if (!empty($stored)) {
            $proposal->attachments = $stored;
            $proposal->save();
        }

        // Notificar o freelancer
        Notification::create([
            'user_id' => $this->recipient_id,
            'type'    => 'proposal_received',
            'title'   => 'Nova proposta de cliente',
            'message' => $user->name . ' enviou-lhe uma proposta: "' . $this->title . '"',
        ]);

        $this->service_id = $service->id;
        $this->sent       = true;
        $this->dispatch('proposalSent');
    }

    public function render()
    {
        return view('livewire.client.send-proposal');
    }
}
