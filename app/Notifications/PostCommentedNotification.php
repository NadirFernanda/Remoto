<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Models\SocialPost;
use App\Models\User;

class PostCommentedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public SocialPost $post,
        public User $commenter,
        public string $commentPreview
    ) {}

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toDatabase($notifiable): array
    {
        return [
            'post_id'         => $this->post->id,
            'commenter_id'    => $this->commenter->id,
            'commenter_name'  => $this->commenter->name,
            'comment_preview' => $this->commentPreview,
            'type'            => 'post_commented',
            'message'         => $this->commenter->name . ' comentou na sua publicação: "' . $this->commentPreview . '"',
            'url'             => url('/social') . '?post=' . $this->post->id,
        ];
    }
}
