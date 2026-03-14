<?php

namespace App\Events;

use App\Models\Message;
use App\Models\Service;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageSent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public int $serviceId;
    public int $messageId;
    public int $senderId;
    public string $senderName;
    public string $content;
    public ?string $anexo;
    public string $createdAt;

    public function __construct(Service $service, Message $message)
    {
        $this->serviceId  = $service->id;
        $this->messageId  = $message->id;
        $this->senderId   = $message->user_id;
        $this->senderName = $message->user->name ?? '';
        $this->content    = $message->conteudo ?? '';
        $this->anexo      = $message->anexo;
        $this->createdAt  = $message->created_at->toISOString();
    }

    public function broadcastOn(): PrivateChannel
    {
        return new PrivateChannel('chat.' . $this->serviceId);
    }

    public function broadcastAs(): string
    {
        return 'MessageSent';
    }
}
