<?php

namespace App\Modules\Social\Services;

use App\Models\SocialPost;
use App\Models\SocialPostMedia;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class PostService
{
    /**
     * Cria um post com os ficheiros de media associados.
     *
     * @param  array{
     *   content: ?string,
     *   type: string,
     *   visibility: string,
     *   link_url: ?string,
     *   link_title: ?string,
     *   link_description: ?string,
     *   link_image: ?string,
     *   repost_id: ?int,
     *   photos: UploadedFile[],
     *   video: ?UploadedFile,
     *   audio: ?UploadedFile,
     * } $data
     */
    public function create(User $author, array $data): SocialPost
    {
        $post = SocialPost::create([
            'user_id'          => $author->id,
            'content'          => $data['content'] ?? null,
            'type'             => $data['type'],
            'visibility'       => $data['visibility'] ?? 'public',
            'status'           => 'active',
            'link_url'         => $data['type'] === 'link' ? ($data['link_url']         ?? null) : null,
            'link_title'       => $data['type'] === 'link' ? ($data['link_title']       ?? null) : null,
            'link_description' => $data['type'] === 'link' ? ($data['link_description'] ?? null) : null,
            'link_image'       => $data['type'] === 'link' ? ($data['link_image']       ?? null) : null,
            'repost_id'        => $data['type'] === 'repost' ? ($data['repost_id']      ?? null) : null,
        ]);

        if ($data['type'] === 'image' && !empty($data['photos'])) {
            foreach ($data['photos'] as $i => $photo) {
                /** @var UploadedFile $photo */
                $path = $photo->store('social/images', 'public');
                SocialPostMedia::create([
                    'post_id'       => $post->id,
                    'type'          => 'image',
                    'path'          => $path,
                    'original_name' => $photo->getClientOriginalName(),
                    'mime_type'     => $photo->getMimeType(),
                    'file_size'     => $photo->getSize(),
                    'order'         => $i,
                ]);
            }
        } elseif ($data['type'] === 'video' && !empty($data['video'])) {
            /** @var UploadedFile $video */
            $video = $data['video'];
            $path  = $video->store('social/videos', 'public');
            SocialPostMedia::create([
                'post_id'       => $post->id,
                'type'          => 'video',
                'path'          => $path,
                'original_name' => $video->getClientOriginalName(),
                'mime_type'     => $video->getMimeType(),
                'file_size'     => $video->getSize(),
            ]);
        } elseif ($data['type'] === 'audio' && !empty($data['audio'])) {
            /** @var UploadedFile $audio */
            $audio = $data['audio'];
            $path  = $audio->store('social/audio', 'public');
            SocialPostMedia::create([
                'post_id'       => $post->id,
                'type'          => 'audio',
                'path'          => $path,
                'original_name' => $audio->getClientOriginalName(),
                'mime_type'     => $audio->getMimeType(),
                'file_size'     => $audio->getSize(),
            ]);
        }

        return $post;
    }

    /**
     * Actualiza o conteúdo textual de um post.
     */
    public function update(SocialPost $post, string $content): SocialPost
    {
        $post->update(['content' => $content]);
        return $post->refresh();
    }

    /**
     * Remove o post e todos os ficheiros de media associados.
     */
    public function delete(SocialPost $post): void
    {
        foreach ($post->media as $media) {
            Storage::disk('public')->delete($media->path);
        }
        // Compatibilidade com posts antigos que usam SocialPostImage
        foreach ($post->images as $img) {
            Storage::disk('public')->delete($img->path);
        }
        $post->delete();
    }
}
