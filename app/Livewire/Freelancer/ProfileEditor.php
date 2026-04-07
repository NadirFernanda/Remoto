<?php

namespace App\Livewire\Freelancer;

use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Str;
use App\Models\FreelancerProfile;
use App\Models\WorkExperience;
use App\Models\Education;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProfileEditor extends Component
{
    use WithFileUploads;

    public string $successMessage = '';
    public string $photoMessage = '';

    public $headline;
    public $profilePhoto;
    public $currentProfilePhoto;
    public $coverPhoto;
    public $currentCoverPhoto;
    public $summary;
    public $hourly_rate;
    public $currency = 'USD';
    public $availability_status = 'available';
    public $skills = '';
    public $languages = '';
    // user fields
    public $name;
    public $email;
    public $phone;
    public $location;
    // metrics
    public $metrics_completed_projects;
    public $metrics_rating;
    public $metrics_total_earnings;
    public $kyc_status;

    // ── Histórico profissional ──────────────────────────────
    public array $experiences = [];   // lista carregada do DB
    public array $expForm = [         // formulário do item em edição/criação
        'id'         => null,
        'titulo'     => '',
        'empresa'    => '',
        'cidade'     => '',
        'pais'       => '',
        'mes_inicio' => '',
        'ano_inicio' => '',
        'mes_fim'    => '',
        'ano_fim'    => '',
        'atual'      => false,
        'descricao'  => '',
    ];
    public bool $showExpForm = false;

    // ── Educação ────────────────────────────────────────────
    public array $educations = [];    // lista carregada do DB
    public array $eduForm = [         // formulário do item em edição/criação
        'id'          => null,
        'escola'      => '',
        'grau'        => '',
        'area_estudo' => '',
        'ano_inicio'  => '',
        'ano_fim'     => '',
        'atual'       => false,
        'descricao'   => '',
    ];
    public bool $showEduForm = false;

    public function mount()
    {
        $user = Auth::user();
        $this->currentProfilePhoto = $user->profile_photo;
        $this->currentCoverPhoto   = $user->cover_photo;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->phone = $user->phone;
        $this->location = $user->location;
        $profile = $user->freelancerProfile;
        if ($profile) {
            $this->headline = $profile->headline;
            $this->summary = $profile->summary;
            $this->hourly_rate = $profile->hourly_rate;
            $this->currency = $profile->currency;
            $this->availability_status = $profile->availability_status;
            $this->skills = is_array($profile->skills) ? implode(',', $profile->skills) : $profile->skills;
            $this->languages = is_array($profile->languages) ? implode(',', $profile->languages) : $profile->languages;
            $metrics = is_array($profile->metrics) ? $profile->metrics : (json_decode($profile->metrics, true) ?? []);
            $this->metrics_completed_projects = $metrics['completed_projects'] ?? null;
            $this->metrics_rating = $metrics['rating'] ?? null;
            $this->metrics_total_earnings = $metrics['total_earnings'] ?? null;
            $this->kyc_status = $profile->kyc_status ?? 'pending';
        }

        // Carrega experiências e educações
        $this->loadExperiences();
        $this->loadEducations();
    }

    // savePhoto removido: upload e salvamento agora são automáticos

    public function updatedProfilePhoto()
    {
        $this->validate(['profilePhoto' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:8192']);
        if (!$this->profilePhoto) return;

        $user = User::find(Auth::id());
        if ($user->profile_photo) {
            Storage::disk('public')->delete($user->profile_photo);
        }
        $ext = $this->profilePhoto->getClientOriginalExtension();
        $path = $this->profilePhoto->storeAs('avatars', Str::uuid() . '.' . $ext, 'public');
        $user->profile_photo = $path;
        $user->save();
        $this->currentProfilePhoto = $path;
        $this->profilePhoto = null;
        $this->photoMessage = 'Foto de perfil atualizada!';
    }

    public function updatedCoverPhoto()
    {
        $this->validate(['coverPhoto' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:8192']);
        if (!$this->coverPhoto) return;

        $user = User::find(Auth::id());
        if ($user->cover_photo) {
            Storage::disk('public')->delete($user->cover_photo);
        }
        $ext  = $this->coverPhoto->getClientOriginalExtension();
        $path = $this->coverPhoto->storeAs('covers', Str::uuid() . '.' . $ext, 'public');
        $user->cover_photo = $path;
        $user->save();
        $this->currentCoverPhoto = $path;
        $this->coverPhoto = null;
        $this->photoMessage = 'Foto de capa atualizada!';
    }

    protected function rules()
    {
        return [
            'profilePhoto' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:8192',
            'coverPhoto'   => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:8192',
            'name' => ['required', 'string', 'max:120', 'not_regex:/<[^>]*>/'],
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:50',
            'location' => 'nullable|string|max:255',
            'headline' => 'nullable|string|max:120',
            'summary' => 'nullable|string|max:5000',
            'hourly_rate' => 'nullable|numeric|min:0',
            'currency' => 'nullable|string|max:8',
            'availability_status' => 'nullable|string|in:available,unavailable,busy',
            'skills' => 'nullable|string|max:1000',
            'languages' => 'nullable|string|max:500',
            'metrics_completed_projects' => 'nullable|integer|min:0',
            'metrics_rating' => 'nullable|numeric|min:0|max:5',
            'metrics_total_earnings' => 'nullable|numeric|min:0',
        ];
    }

    public function saveProfile()
    {
        $this->validate();
        $user = User::find(Auth::id());
        // update user fields
        $user->update([
            'name' => strip_tags($this->name),
            'email' => $this->email,
            'phone' => $this->phone,
            'location' => $this->location,
        ]);

        $profile = FreelancerProfile::updateOrCreate(
            ['user_id' => $user->id],
            [
                'headline' => $this->headline,
                'summary' => $this->summary,
                'hourly_rate' => $this->hourly_rate,
                'currency' => $this->currency,
                'availability_status' => $this->availability_status,
                'skills' => $this->skills ? array_map('trim', explode(',', $this->skills)) : null,
                'languages' => $this->languages ? array_map('trim', explode(',', $this->languages)) : null,
                'metrics' => [
                    'completed_projects' => $this->metrics_completed_projects ?? 0,
                    'rating' => $this->metrics_rating ?? null,
                    'total_earnings' => $this->metrics_total_earnings ?? 0,
                ],
            ]
        );

        $this->successMessage = 'Perfil salvo com sucesso!';
    }

    // ── Histórico profissional ──────────────────────────────

    protected function loadExperiences(): void
    {
        $this->experiences = WorkExperience::where('user_id', Auth::id())
            ->orderByDesc('ano_inicio')
            ->get()
            ->toArray();
    }

    public function openExpForm(?int $id = null): void
    {
        if ($id) {
            $exp = WorkExperience::where('id', $id)->where('user_id', Auth::id())->firstOrFail();
            $this->expForm = $exp->toArray();
        } else {
            $this->expForm = [
                'id' => null, 'titulo' => '', 'empresa' => '', 'cidade' => '',
                'pais' => '', 'mes_inicio' => '', 'ano_inicio' => '',
                'mes_fim' => '', 'ano_fim' => '', 'atual' => false, 'descricao' => '',
            ];
        }
        $this->showExpForm = true;
    }

    public function closeExpForm(): void
    {
        $this->showExpForm = false;
        $this->resetErrorBag('expForm.*');
    }

    public function saveExperience(): void
    {
        $this->validate([
            'expForm.titulo'     => 'required|string|max:120',
            'expForm.empresa'    => 'required|string|max:120',
            'expForm.cidade'     => 'nullable|string|max:100',
            'expForm.pais'       => 'nullable|string|max:100',
            'expForm.mes_inicio' => 'nullable|integer|min:1|max:12',
            'expForm.ano_inicio' => 'nullable|integer|min:1950|max:2100',
            'expForm.mes_fim'    => 'nullable|integer|min:1|max:12',
            'expForm.ano_fim'    => 'nullable|integer|min:1950|max:2100',
            'expForm.atual'      => 'boolean',
            'expForm.descricao'  => 'nullable|string|max:2000',
        ], [
            'expForm.titulo.required'  => 'O título/cargo é obrigatório.',
            'expForm.empresa.required' => 'O nome da empresa é obrigatório.',
        ]);

        $data = [
            'user_id'    => Auth::id(),
            'titulo'     => strip_tags($this->expForm['titulo']),
            'empresa'    => strip_tags($this->expForm['empresa']),
            'cidade'     => $this->expForm['cidade'] ? strip_tags($this->expForm['cidade']) : null,
            'pais'       => $this->expForm['pais'] ? strip_tags($this->expForm['pais']) : null,
            'mes_inicio' => $this->expForm['mes_inicio'] ?: null,
            'ano_inicio' => $this->expForm['ano_inicio'] ?: null,
            'mes_fim'    => $this->expForm['atual'] ? null : ($this->expForm['mes_fim'] ?: null),
            'ano_fim'    => $this->expForm['atual'] ? null : ($this->expForm['ano_fim'] ?: null),
            'atual'      => (bool) $this->expForm['atual'],
            'descricao'  => $this->expForm['descricao'] ? strip_tags($this->expForm['descricao']) : null,
        ];

        if ($this->expForm['id']) {
            WorkExperience::where('id', $this->expForm['id'])->where('user_id', Auth::id())->update($data);
        } else {
            WorkExperience::create($data);
        }

        $this->loadExperiences();
        $this->showExpForm = false;
        $this->successMessage = 'Experiência guardada!';
    }

    public function deleteExperience(int $id): void
    {
        WorkExperience::where('id', $id)->where('user_id', Auth::id())->delete();
        $this->loadExperiences();
    }

    // ── Educação ────────────────────────────────────────────

    protected function loadEducations(): void
    {
        $this->educations = Education::where('user_id', Auth::id())
            ->orderByDesc('ano_inicio')
            ->get()
            ->toArray();
    }

    public function openEduForm(?int $id = null): void
    {
        if ($id) {
            $edu = Education::where('id', $id)->where('user_id', Auth::id())->firstOrFail();
            $this->eduForm = $edu->toArray();
        } else {
            $this->eduForm = [
                'id' => null, 'escola' => '', 'grau' => '', 'area_estudo' => '',
                'ano_inicio' => '', 'ano_fim' => '', 'atual' => false, 'descricao' => '',
            ];
        }
        $this->showEduForm = true;
    }

    public function closeEduForm(): void
    {
        $this->showEduForm = false;
        $this->resetErrorBag('eduForm.*');
    }

    public function saveEducation(): void
    {
        $this->validate([
            'eduForm.escola'      => 'required|string|max:150',
            'eduForm.grau'        => 'nullable|string|max:100',
            'eduForm.area_estudo' => 'nullable|string|max:150',
            'eduForm.ano_inicio'  => 'nullable|integer|min:1950|max:2100',
            'eduForm.ano_fim'     => 'nullable|integer|min:1950|max:2100',
            'eduForm.atual'       => 'boolean',
            'eduForm.descricao'   => 'nullable|string|max:2000',
        ], [
            'eduForm.escola.required' => 'O nome da escola/instituição é obrigatório.',
        ]);

        $data = [
            'user_id'     => Auth::id(),
            'escola'      => strip_tags($this->eduForm['escola']),
            'grau'        => $this->eduForm['grau'] ? strip_tags($this->eduForm['grau']) : null,
            'area_estudo' => $this->eduForm['area_estudo'] ? strip_tags($this->eduForm['area_estudo']) : null,
            'ano_inicio'  => $this->eduForm['ano_inicio'] ?: null,
            'ano_fim'     => $this->eduForm['atual'] ? null : ($this->eduForm['ano_fim'] ?: null),
            'atual'       => (bool) $this->eduForm['atual'],
            'descricao'   => $this->eduForm['descricao'] ? strip_tags($this->eduForm['descricao']) : null,
        ];

        if ($this->eduForm['id']) {
            Education::where('id', $this->eduForm['id'])->where('user_id', Auth::id())->update($data);
        } else {
            Education::create($data);
        }

        $this->loadEducations();
        $this->showEduForm = false;
        $this->successMessage = 'Educação guardada!';
    }

    public function deleteEducation(int $id): void
    {
        Education::where('id', $id)->where('user_id', Auth::id())->delete();
        $this->loadEducations();
    }

    public function render()
    {
        return view('livewire.freelancer.profile-editor')
            ->layout('layouts.dashboard', ['dashboardTitle' => 'Editar Perfil']);
    }
}
