<?php

namespace App\Modules\Messaging\Services;

use App\Models\ChatRead;
use App\Models\Message;
use App\Models\Service;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;

class ChatService
{
    /**
     * Send a message in a service chat thread.
     *
     * @throws \InvalidArgumentException when both content and file are empty
     */
    public function send(Service $service, User $sender, string $content, ?UploadedFile $file = null): Message
    {
        $content = trim($content);

        if ($content === '' && $file === null) {
            throw new \InvalidArgumentException('A mensagem não pode estar vazia.');
        }

        $anexoFilename = null;
        $anexoOriginal = null;

        if ($file !== null) {
            $original      = $file->getClientOriginalName();
            $safe          = preg_replace('/[^a-zA-Z0-9._-]/', '_', $original);
            $filename      = time() . '_' . $safe;
            $file->storeAs('anexos', $filename, 'public');
            $anexoFilename = $filename;
            $anexoOriginal = $original ?: $filename;
        }

        $message = $service->messages()->create([
            'user_id'             => $sender->id,
            'conteudo'            => $content,
            'anexo'               => $anexoFilename,
            'nome_original_anexo' => $anexoOriginal,
        ]);

        ChatRead::markRead($service->id, $sender->id);

        return $message;
    }

    /**
     * Retrieve all messages for a service chat, ordered chronologically.
     */
    public function getMessages(Service $service): Collection
    {
        return $service->messages()
            ->with('user')
            ->orderBy('created_at')
            ->get();
    }

    /**
     * Mark the chat as read for the given user.
     */
    public function markRead(Service $service, User $user): void
    {
        ChatRead::markRead($service->id, $user->id);
    }
}
