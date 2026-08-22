<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\KycSubmission;
use App\Events\KycSubmissionCreated;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class KycForm extends Component
{
    use WithFileUploads;

    public string $documentType = 'bi';
    public $documentFront;
    public $documentBack;
    public $selfie;
    public string $successMessage = '';
    public string $errorMessage = '';
    public bool $showResubmitForm = false;

    public ?KycSubmission $existing = null;

    public function mount(): void
    {
        $this->existing = KycSubmission::where('user_id', Auth::id())
            ->latest()
            ->first();
    }

    protected function rules(): array
    {
        return [
            'documentType'  => 'required|in:bi,passport,driving_license',
            'documentFront' => 'required|file|mimes:jpg,jpeg,png,pdf|max:10240',
            'documentBack'  => 'required|file|mimes:jpg,jpeg,png,pdf|max:10240',
            'selfie'        => 'nullable|file|mimes:jpg,jpeg,png|max:10240',
        ];
    }

    public function requestResubmit(): void
    {
        $this->showResubmitForm = true;
        $this->successMessage   = '';
        $this->errorMessage     = '';
        $this->reset(['documentFront', 'documentBack', 'selfie']);
        $this->documentType = $this->existing?->document_type ?? 'bi';
    }

    public function submit(): void
    {
        $this->errorMessage = '';

        // Se o upload assíncrono de um ficheiro falhou (ex.: rede instável)
        // a propriedade fica vazia mesmo depois de o utilizador "escolher" o
        // ficheiro no ecrã — a regra 'required' abaixo apanha isso, mas sem
        // esta mensagem explícita a única pista era um texto pequeno por
        // baixo do campo, fácil de não notar.
        if (!$this->documentFront || !$this->documentBack) {
            $this->errorMessage = 'A frente e o verso do documento são obrigatórios. Se já escolheu os ficheiros e o campo continua vazio, o carregamento falhou — tente novamente com uma ligação mais estável.';
        }

        $this->validate();

        $user = Auth::user();

        // Block re-submission if there is already a pending/approved one,
        // UNLESS the user explicitly requested a resubmission
        if (!$this->showResubmitForm && $this->existing && in_array($this->existing->status, ['pending', 'approved'])) {
            session()->flash('error', 'Já tem uma submissão em análise ou aprovada.');
            return;
        }

        $isResubmission = (bool) $this->existing;

        try {
            $frontPath = $this->documentFront->store('kyc/' . $user->id, 'private');
            $backPath  = $this->documentBack  ? $this->documentBack->store('kyc/' . $user->id, 'private')  : null;
            $selfiePath = $this->selfie       ? $this->selfie->store('kyc/' . $user->id, 'private')        : null;

            $submission = KycSubmission::create([
                'user_id'             => $user->id,
                'document_type'       => $this->documentType,
                'document_front_path' => $frontPath,
                'document_back_path'  => $backPath,
                'selfie_path'         => $selfiePath,
                'status'              => 'pending',
            ]);

            // Update kyc_status on user
            $user->kyc_status = 'pending';
            $user->save();
        } catch (\Throwable $e) {
            Log::error('Falha ao gravar submissão KYC', [
                'user_id' => $user->id,
                'error'   => $e->getMessage(),
            ]);
            $this->errorMessage = 'Não foi possível enviar os documentos devido a um erro no servidor. Tente novamente — se o problema persistir, contacte o suporte.';
            return;
        }

        KycSubmissionCreated::dispatch($submission, $user, $isResubmission);

        $this->existing = $submission;
        $this->showResubmitForm = false;
        $this->reset(['documentFront', 'documentBack', 'selfie']);
        $this->successMessage = 'Documentos enviados com sucesso! A equipa irá analisar em breve.';
    }

    public function render()
    {
        return view('livewire.kyc-form')
            ->layout('layouts.dashboard', ['dashboardTitle' => '']);
    }
}
