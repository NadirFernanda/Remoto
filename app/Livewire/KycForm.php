<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\KycSubmission;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class KycForm extends Component
{
    use WithFileUploads;

    public string $documentType = 'bi';
    public $documentFront;
    public $documentBack;
    public $selfie;
    public string $successMessage = '';

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
            'documentBack'  => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:10240',
            'selfie'        => 'nullable|file|mimes:jpg,jpeg,png|max:10240',
        ];
    }

    public function submit(): void
    {
        $this->validate();

        $user = Auth::user();

        // Only allow new submission if no pending/approved one exists
        if ($this->existing && in_array($this->existing->status, ['pending', 'approved'])) {
            $this->successMessage = '';
            session()->flash('error', 'Já tem uma submissão em análise ou aprovada.');
            return;
        }

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
        $user->update(['kyc_status' => 'pending']);

        $this->existing = $submission;
        $this->reset(['documentFront', 'documentBack', 'selfie']);
        $this->successMessage = 'Documentos enviados com sucesso! A equipa irá analisar em breve.';
    }

    public function render()
    {
        return view('livewire.kyc-form')
            ->layout('layouts.dashboard', ['dashboardTitle' => 'Verificação de Identidade (KYC)']);
    }
}
