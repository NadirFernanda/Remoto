<?php

namespace App\Modules\Social\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\Social\CreatePostRequest;
use App\Models\SocialPost;
use App\Modules\Social\Services\PostService;
use Illuminate\Http\RedirectResponse;

class PostController extends Controller
{
    public function __construct(private readonly PostService $postService) {}

    /**
     * Cria um novo post via HTTP form (complementa o fluxo Livewire).
     */
    public function store(CreatePostRequest $request): RedirectResponse
    {
        $data = array_merge($request->validated(), [
            'type'   => $request->input('post_type'),
            'photos' => $request->file('photos', []),
            'video'  => $request->file('video'),
            'audio'  => $request->file('audio'),
        ]);

        $this->postService->create($request->user(), $data);

        return redirect()->route('social.feed')
            ->with('success', 'Publicação criada com sucesso!');
    }

    /**
     * Remove um post via HTTP (complementa o fluxo Livewire).
     */
    public function destroy(SocialPost $post): RedirectResponse
    {
        $this->authorize('delete', $post);
        $this->postService->delete($post);

        return back()->with('success', 'Publicação removida.');
    }
}
