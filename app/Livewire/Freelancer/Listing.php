<?php

namespace App\Livewire\Freelancer;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\User;

class Listing extends Component
{
    use WithPagination;

    public $search = '';
    public $skill = '';

    protected $updatesQueryString = ['search', 'skill'];

    protected $listeners = ['refreshListing' => '$refresh'];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $query = User::where('role', 'freelancer')
            ->with(['freelancerProfile', 'portfolios', 'reviewsReceived'])
            ->when($this->search, function ($q) {
                $q->where(function ($sub) {
                    $sub->where('name', 'ilike', "%{$this->search}%")
                        ->orWhereHas('freelancerProfile', function ($p) {
                            $p->where('headline', 'ilike', "%{$this->search}%")
                              ->orWhere('summary', 'ilike', "%{$this->search}%");
                        });
                });
            })
            ->when($this->skill, function ($q) {
                $q->whereHas('freelancerProfile', function ($p) {
                    $p->whereRaw("skills::text ILIKE ?", ["%" . $this->skill . "%"]);
                });
            });

        $freelancers = $query->orderBy('created_at', 'desc')->paginate(12);

        return view('livewire.freelancer.listing', [
            'freelancers' => $freelancers,
        ]);
    }

    // Server-side helper to open proposal modal via emitting event
    public function openProposal($id)
    {
        $this->dispatch('openProposal', $id);
    }
}
