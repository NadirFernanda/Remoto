<?php

namespace App\Modules\Messaging\Controllers;

use App\Models\Service;
use App\Modules\Messaging\Services\ChatService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;

class ChatSendController extends Controller
{
    public function send(Request $request, Service $service)
    {
        abort_unless(Auth::check(), 401);

        $user = Auth::user();

        // Authorization: must be owner, freelancer or candidate
        $isOwner      = $user->id === $service->cliente_id;
        $isFreelancer = $user->id === $service->freelancer_id;
        $isCandidate  = $service->candidates()
            ->where('freelancer_id', $user->id)
            ->whereIn('status', ['pending', 'proposal_sent', 'invited', 'chosen'])
            ->exists();

        abort_unless($isOwner || $isFreelancer || $isCandidate, 403);

        // Chat must be unblocked
        $blocked = !in_array($service->status, [
            'published', 'negotiating', 'accepted', 'in_progress', 'delivered',
        ]);
        abort_if($blocked, 422, 'Chat bloqueado.');

        // Rate limit
        $key = 'chat-message:' . $user->id;
        if (RateLimiter::tooManyAttempts($key, 30)) {
            return response()->json(['error' => 'Enviou muitas mensagens. Aguarde um momento.'], 429);
        }
        RateLimiter::hit($key, 60);

        $request->validate([
            'mensagem'           => 'nullable|string|max:5000',
            'attachment_path'    => 'nullable|string|max:255',
            'attachment_original'=> 'nullable|string|max:500',
        ]);

        $mensagem    = trim($request->input('mensagem', ''));
        $attachPath  = trim($request->input('attachment_path', ''));
        $attachOrig  = trim($request->input('attachment_original', ''));

        if ($mensagem === '' && $attachPath === '') {
            return response()->json(['error' => 'A mensagem não pode estar vazia.'], 422);
        }

        $msg = app(ChatService::class)->send(
            $service,
            $user,
            $mensagem,
            null,
            $attachPath ?: null,
            $attachOrig ?: null
        );

        $ext      = $attachPath ? strtolower(pathinfo($attachPath, PATHINFO_EXTENSION)) : null;
        $isImage  = in_array($ext, ['jpg','jpeg','png','gif','bmp','webp']);
        $isAudio  = in_array($ext, ['mp3','wav','ogg','m4a','aac']);

        return response()->json([
            'id'               => $msg->id,
            'user_id'          => $user->id,
            'name'             => $user->name,
            'avatar'           => $user->avatarUrl(),
            'conteudo'         => $msg->conteudo,
            'anexo'            => $attachPath,
            'nome_original'    => $attachOrig ?: $attachPath,
            'is_image'         => $isImage,
            'is_audio'         => $isAudio,
            'ext'              => strtoupper($ext ?? ''),
            'time'             => $msg->created_at->format('H:i'),
        ]);
    }
}
