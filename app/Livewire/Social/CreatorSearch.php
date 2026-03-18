<?php

namespace App\Livewire\Social;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\User;
use App\Models\CreatorProfile;
use App\Models\CreatorSubscription;
use Illuminate\Support\Facades\Auth;

class CreatorSearch extends Component
{
    use WithPagination;

    public string $query    = '';
    public string $category = '';
    public string $sort     = 'populares';

    protected $paginationTheme = 'tailwind';

    protected $queryString = [
        'query'    => ['except' => ''],
        'category' => ['except' => ''],
        'sort'     => ['except' => 'populares'],
    ];

    public function updatingQuery()    { $this->resetPage(); }
    public function updatingCategory() { $this->resetPage(); }
    public function updatingSort()     { $this->resetPage(); }

    public function render()
    {
        $user = Auth::user();

        $creators = User::query()
            ->where(function ($q) {
                $q->where('role', 'creator')
                  ->orWhere('has_creator_profile', true);
            })
            ->with(['creatorProfile'])
            ->when($this->query, function ($q) {
                $q->where(function ($inner) {
                    $inner->where('name', 'like', '%' . $this->query . '%')
                          ->orWhereHas('creatorProfile', function ($cp) {
                              $cp->where('bio', 'like', '%' . $this->query . '%');
                          });
                });
            })
            ->when($this->category, function ($q) {
                $q->whereHas('creatorProfile', function ($cp) {
                    $cp->where('category', $this->category);
                });
            });

        if ($this->sort === 'populares') {
            $creators->orderByRaw(
                '(SELECT COALESCE(total_subscribers, 0) FROM creator_profiles WHERE creator_profiles.user_id = users.id) DESC'
            );
        } elseif ($this->sort === 'preco_asc') {
            $creators->orderByRaw(
                '(SELECT COALESCE(subscription_price, 0) FROM creator_profiles WHERE creator_profiles.user_id = users.id) ASC'
            );
        } elseif ($this->sort === 'preco_desc') {
            $creators->orderByRaw(
                '(SELECT COALESCE(subscription_price, 0) FROM creator_profiles WHERE creator_profiles.user_id = users.id) DESC'
            );
        } elseif ($this->sort === 'novos') {
            $creators->latest();
        }

        $creators = $creators->paginate(12);

        $subscribedCreatorIds = [];
        if ($user) {
            try {
                $subscribedCreatorIds = CreatorSubscription::where('subscriber_id', $user->id)
                    ->where('status', 'active')
                    ->where('expires_at', '>', now())
                    ->pluck('creator_id')
                    ->toArray();
            } catch (\Throwable $e) {
                //
            }
        }

        $categories = CreatorProfile::categories();

        return view('livewire.social.creator-search', compact('creators', 'subscribedCreatorIds', 'categories'))
            ->layout('layouts.dashboard', ['title' => 'Buscar Criadores de Conteúdo', 'dashboardTitle' => 'Buscar Criadores de Conteúdo']);
    }
}
