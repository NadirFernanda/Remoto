<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\User;
use App\Models\FreelancerProfile;

class FreelancerSearch extends Component
{
    use WithPagination;

    public string $query = '';
    public string $skill = '';
    public string $language = '';
    public string $availability = '';
    public string $sort = 'relevancia';
    public int $minRate = 0;
    public int $maxRate = 999999;
    public float $minRating = 0;

    protected $queryString = [
        'query'       => ['except' => ''],
        'skill'       => ['except' => ''],
        'language'    => ['except' => ''],
        'availability'=> ['except' => ''],
        'sort'        => ['except' => 'relevancia'],
        'minRate'     => ['except' => 0],
        'maxRate'     => ['except' => 999999],
        'minRating'   => ['except' => 0],
    ];
    public function updatingQuery()     { $this->resetPage(); }
    public function updatingSkill()     { $this->resetPage(); }
    public function updatingLanguage()  { $this->resetPage(); }
    public function updatingAvailability() { $this->resetPage(); }
    public function updatingSort()      { $this->resetPage(); }
    public function updatingMinRate()   { $this->resetPage(); }
    public function updatingMaxRate()   { $this->resetPage(); }
    public function updatingMinRating() { $this->resetPage(); }

    public function render()
    {
        $freelancers = User::query()
            ->whereIn('role', ['freelancer'])
            ->whereHas('freelancerProfile')
            ->with(['freelancerProfile', 'reviewsReceived'])
            ->when($this->query, function ($q) {
                $q->where(function ($inner) {
                    $inner->where('name', 'ilike', '%' . $this->query . '%')
                          ->orWhereHas('freelancerProfile', function ($fp) {
                              $fp->where('headline', 'ilike', '%' . $this->query . '%')
                                 ->orWhere('summary', 'ilike', '%' . $this->query . '%');
                          });
                });
            })
            ->when($this->skill, function ($q) {
                $q->whereHas('freelancerProfile', function ($fp) {
                    $fp->whereJsonContains('skills', $this->skill);
                });
            })
            ->when($this->language, function ($q) {
                $q->whereHas('freelancerProfile', function ($fp) {
                    $fp->whereJsonContains('languages', $this->language);
                });
            })
            ->when($this->availability, function ($q) {
                $q->whereHas('freelancerProfile', function ($fp) {
                    $fp->where('availability_status', $this->availability);
                });
            })
            ->when($this->minRate > 0, function ($q) {
                $q->whereHas('freelancerProfile', function ($fp) {
                    $fp->where('hourly_rate', '>=', $this->minRate);
                });
            })
            ->when($this->maxRate < 999999, function ($q) {
                $q->whereHas('freelancerProfile', function ($fp) {
                    $fp->where('hourly_rate', '<=', $this->maxRate);
                });
            })
            ->when($this->minRating > 0, function ($q) {
                $q->whereRaw(
                    '(SELECT COALESCE(AVG(rating), 0) FROM reviews WHERE target_id = users.id) >= ?',
                    [(float) $this->minRating]
                );
            });

        // Sorting
        if ($this->sort === 'preco_asc') {
            $freelancers->join('freelancer_profiles', 'users.id', '=', 'freelancer_profiles.user_id')
                        ->orderBy('freelancer_profiles.hourly_rate', 'asc')
                        ->select('users.*');
        } elseif ($this->sort === 'preco_desc') {
            $freelancers->join('freelancer_profiles', 'users.id', '=', 'freelancer_profiles.user_id')
                        ->orderBy('freelancer_profiles.hourly_rate', 'desc')
                        ->select('users.*');
        } elseif ($this->sort === 'popularidade') {
            $freelancers->orderByRaw(
                '(SELECT COALESCE(AVG(rating), 0) FROM reviews WHERE target_id = users.id) DESC'
            );
        } else {
            $freelancers->latest();
        }

        $baseSkills = collect([
            'HTML', 'CSS', 'JavaScript', 'TypeScript', 'React', 'Vue.js', 'Angular',
            'Node.js', 'PHP', 'Laravel', 'Python', 'Django', 'Java', 'C#', '.NET',
            'Ruby', 'Go', 'Swift', 'Kotlin', 'Flutter', 'React Native',
            'WordPress', 'Shopify', 'WooCommerce', 'Magento',
            'MySQL', 'PostgreSQL', 'MongoDB', 'Firebase', 'Redis',
            'AWS', 'Azure', 'Google Cloud', 'Docker', 'Linux',
            'Git', 'REST API', 'GraphQL',
            'UI/UX Design', 'Figma', 'Adobe Photoshop', 'Adobe Illustrator',
            'Premiere Pro', 'After Effects', 'Edição de Vídeo',
            'SEO', 'Google Ads', 'Facebook Ads', 'Marketing Digital',
            'Copywriting', 'Redação', 'Tradução',
            'Gestão de Redes Sociais', 'Community Manager',
            'Consultoria', 'Contabilidade', 'Finanças',
            'Excel', 'Power BI', 'Data Analysis',
        ]);

        $allSkills = FreelancerProfile::query()
            ->whereNotNull('skills')
            ->pluck('skills')
            ->filter()
            ->flatten()
            ->merge($baseSkills)
            ->unique()
            ->sort()
            ->values();

        $baseLanguages = collect([
            'Português', 'Inglês', 'Francês', 'Espanhol', 'Árabe',
            'Mandarim', 'Russo', 'Alemão', 'Italiano', 'Japonês',
            'Coreano', 'Hindi', 'Bengali', 'Suaíli', 'Hauçá',
            'Iorubá', 'Zulu', 'Amárico', 'Somali', 'Lingala',
        ]);

        $allLanguages = FreelancerProfile::query()
            ->whereNotNull('languages')
            ->pluck('languages')
            ->filter()
            ->flatten()
            ->merge($baseLanguages)
            ->unique()
            ->sort()
            ->values();

        return view('livewire.freelancer-search', [
            'freelancers' => $freelancers->paginate(12),
            'allSkills'   => $allSkills,
            'allLanguages'=> $allLanguages,
        ]);
    }
}
