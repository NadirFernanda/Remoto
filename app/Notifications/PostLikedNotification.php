<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Models\SocialPost;
use App\Models\User;

class PostLikedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public SocialPost $post,
        public User $liker
    ) {}

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toDatabase($notifiable): array
    {
        return [
            'post_id'    => $this->post->id,
            'liker_id'   => $this->liker->id,
            'liker_name' => $this->liker->name,
            'type'       => 'post_liked',
            'message'    => $this->liker->name . ' gostou da sua publicação.',
            'url'        => url('/social') . '?post=' . $this->post->id,
        ];
    }
}
