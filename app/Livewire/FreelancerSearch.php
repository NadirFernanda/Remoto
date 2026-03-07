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
    public function updatingsort()      { $this->resetPage(); }
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
                $q->whereHas('reviewsReceived', function ($r) {
                    $r->havingRaw('AVG(rating) >= ?', [$this->minRating]);
                });
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
        } else {
            $freelancers->latest();
        }

        $allSkills = FreelancerProfile::query()
            ->whereNotNull('skills')
            ->pluck('skills')
            ->filter()
            ->flatten()
            ->unique()
            ->sort()
            ->values();

        $allLanguages = FreelancerProfile::query()
            ->whereNotNull('languages')
            ->pluck('languages')
            ->filter()
            ->flatten()
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
