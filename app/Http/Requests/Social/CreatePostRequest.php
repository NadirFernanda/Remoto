<?php

namespace App\Http\Requests\Social;

use Illuminate\Foundation\Http\FormRequest;

class CreatePostRequest extends FormRequest
{
    public function authorize(): bool
    {
        return in_array($this->user()?->role, ['freelancer', 'creator']);
    }

    public function rules(): array
    {
        $base = [
            'post_type'  => 'required|in:text,image,video,audio,link,repost',
            'visibility' => 'required|in:public,followers',
            'content'    => 'nullable|string|max:3000',
        ];

        return match ($this->input('post_type')) {
            'image' => array_merge($base, [
                'photos'   => 'required|array|min:1|max:5',
                'photos.*' => 'image|max:8192',
            ]),
            'video' => array_merge($base, [
                'video' => 'required|file|mimetypes:video/mp4,video/webm,video/quicktime,video/x-msvideo|max:204800',
            ]),
            'audio' => array_merge($base, [
                'audio' => 'required|file|mimetypes:audio/mpeg,audio/mp4,audio/ogg,audio/wav,audio/x-wav|max:51200',
            ]),
            'link' => array_merge($base, [
                'link_url' => 'required|url|max:2000',
            ]),
            'repost' => array_merge($base, [
                'repost_id' => 'required|integer|exists:social_posts,id',
                'content'   => 'nullable|string|max:1000',
            ]),
            default => array_merge($base, [
                'content' => 'required|string|min:5|max:3000',
            ]),
        };
    }

    public function messages(): array
    {
        return [
            'content.required'   => 'Escreva algo para publicar.',
            'content.min'        => 'A publicação deve ter pelo menos 5 caracteres.',
            'photos.required'    => 'Selecione pelo menos uma imagem.',
            'photos.max'         => 'Máximo de 5 imagens por publicação.',
            'photos.*.image'     => 'Apenas imagens são permitidas (JPG, PNG, GIF, WebP).',
            'photos.*.max'       => 'Cada imagem não pode ultrapassar 8 MB.',
            'video.required'     => 'Selecione um vídeo para publicar.',
            'video.mimetypes'    => 'Formato não suportado. Use MP4, WebM ou MOV.',
            'video.max'          => 'O vídeo não pode ultrapassar 200 MB.',
            'audio.required'     => 'Selecione um ficheiro de áudio.',
            'audio.mimetypes'    => 'Formato não suportado. Use MP3, M4A, OGG ou WAV.',
            'audio.max'          => 'O áudio não pode ultrapassar 50 MB.',
            'link_url.required'  => 'Introduza um URL válido.',
            'link_url.url'       => 'O URL não é válido.',
            'repost_id.required' => 'Publicação de origem inválida.',
            'repost_id.exists'   => 'A publicação selecionada não existe.',
        ];
    }
}
