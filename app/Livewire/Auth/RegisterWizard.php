<?php

namespace App\Livewire\Auth;

use App\Events\ClientRegistered;
use App\Events\FreelancerRegistered;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\CreatorProfile;
use App\Models\KycSubmission;
use App\Models\User;
use App\Services\AffiliateService;
use App\Services\PlatformStatsService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithFileUploads;

class RegisterWizard extends Component
{
    use WithFileUploads;

    public int $step = 1;

    // ── Passo 1: dados da conta ─────────────────────────────────────────
    public string $role = 'freelancer';
    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';

    // ── Passo 2: verificação de identidade (KYC) ────────────────────────
    public string $documentType = 'bi';
    public $documentFront;
    public $documentBack;
    public $selfie;

    public function mount(): void
    {
        if (!Auth::check()) {
            return;
        }

        $role = Auth::user()->role;
        if ($role === 'freelancer') {
            $this->redirect('/freelancer/dashboard');
        } elseif ($role === 'admin') {
            $this->redirect('/admin/dashboard');
        } else {
            $this->redirect('/cliente/dashboard');
        }
    }

    public function nextStep(): void
    {
        $this->validate((new RegisterRequest())->rules(), (new RegisterRequest())->messages());
        $this->step = 2;
    }

    public function back(): void
    {
        $this->step = 1;
    }

    protected function step2Rules(): array
    {
        return [
            'documentType'  => 'required|in:bi,passport,driving_license',
            'documentFront' => 'required|file|mimes:jpg,jpeg,png,pdf|max:10240',
            'documentBack'  => 'required|file|mimes:jpg,jpeg,png,pdf|max:10240',
            'selfie'        => 'nullable|file|mimes:jpg,jpeg,png|max:10240',
        ];
    }

    public function submit(): void
    {
        $this->validate($this->step2Rules());

        $user = DB::transaction(function () {
            // Código de afiliado único
            do {
                $affiliateCode = strtoupper(Str::random(8));
            } while (User::where('affiliate_code', $affiliateCode)->exists());

            $user = User::create([
                'name'           => strip_tags($this->name),
                'email'          => $this->email,
                'password'       => bcrypt($this->password),
                'affiliate_code' => $affiliateCode,
            ]);

            // role atribuído explicitamente — não está em $fillable (OWASP A03)
            $user->role = $this->role === 'freelancer' ? 'freelancer' : 'cliente';
            $user->save();

            // Seed multi-profile flags: freelancer tem automaticamente acesso ao módulo de criador
            $profileFlags = match ($user->role) {
                'freelancer' => ['has_freelancer_profile' => true, 'has_creator_profile' => true],
                'cliente'    => ['has_cliente_profile' => true],
                default      => [],
            };
            if ($profileFlags) {
                $user->update($profileFlags);
            }

            if ($user->role === 'freelancer') {
                CreatorProfile::firstOrCreate(['user_id' => $user->id]);
            }

            // Processa referral: lê da URL (?ref=) ou do cookie de 30 dias
            $ref = request()->query('ref') ?: request()->cookie('affiliate_ref');
            if ($ref) {
                (new AffiliateService())->recordReferral($user, strtoupper(trim($ref)), request());
            }

            // Documentos de identidade
            $frontPath  = $this->documentFront->store('kyc/' . $user->id, 'private');
            $backPath   = $this->documentBack->store('kyc/' . $user->id, 'private');
            $selfiePath = $this->selfie ? $this->selfie->store('kyc/' . $user->id, 'private') : null;

            KycSubmission::create([
                'user_id'             => $user->id,
                'document_type'       => $this->documentType,
                'document_front_path' => $frontPath,
                'document_back_path'  => $backPath,
                'selfie_path'         => $selfiePath,
                'status'              => 'pending',
            ]);

            $user->kyc_status = 'pending';
            $user->save();

            return $user;
        });

        if ($user->role === 'freelancer') {
            event(new FreelancerRegistered($user));
        } else {
            event(new ClientRegistered($user));
        }

        Auth::login($user);
        session()->regenerate();

        session()->flash('status', 'Conta criada com sucesso! Os seus documentos de identidade estão em análise — já pode começar a usar a plataforma.');

        $this->redirect($user->role === 'freelancer' ? '/freelancer/dashboard' : '/cliente/dashboard');
    }

    public function render()
    {
        return view('livewire.auth.register-wizard', PlatformStatsService::get())
            ->layout('layouts.main');
    }
}
