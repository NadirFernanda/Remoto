<?php

namespace App\Livewire\Social;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\SocialPost;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class MyPosts extends Component
{
    use WithPagination;

    public string $filter = 'all'; // all | active | archived
    public ?int $confirmDeleteId = null;

    protected $paginationTheme = 'tailwind';

    public function updatingFilter(): void
    {
        $this->resetPage();
    }

    public function deletePost(int $id): void
    {
        $post = SocialPost::where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        // Delete associated media files
        foreach ($post->media as $media) {
            if ($media->path) {
                Storage::disk('public')->delete($media->path);
            }
        }
        $post->media()->delete();
        $post->likes()->delete();
        $post->comments()->delete();
        $post->delete();

        $this->confirmDeleteId = null;
        session()->flash('success', 'Publicação eliminada com sucesso.');
    }

    public function toggleStatus(int $id): void
    {
        $post = SocialPost::where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $post->status = $post->status === 'active' ? 'archived' : 'active';
        $post->save();
    }

    public function render()
    {
        $query = SocialPost::where('user_id', Auth::id())
            ->with(['media', 'likes', 'comments'])
            ->latest();

        if ($this->filter === 'active') {
            $query->where('status', 'active');
        } elseif ($this->filter === 'archived') {
            $query->where('status', 'archived');
        }

        return view('livewire.social.my-posts', [
            'posts' => $query->paginate(15),
        ])->layout('layouts.dashboard', [
            'title' => 'Minhas Publicações',
        ]);
    }
}
