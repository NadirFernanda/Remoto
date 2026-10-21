<?php

namespace Tests\Feature;

use App\Livewire\Social\Feed;
use App\Models\SocialComment;
use App\Models\SocialPost;
use App\Models\User;
use App\Notifications\PostCommentedNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class SocialFeedCommentsTest extends TestCase
{
    use RefreshDatabase;

    public function test_comment_drafts_are_isolated_and_submitted_to_their_own_posts(): void
    {
        $user = User::factory()->create();
        $firstPost = SocialPost::create(['user_id' => $user->id, 'content' => 'First post']);
        $secondPost = SocialPost::create(['user_id' => $user->id, 'content' => 'Second post']);
        $firstText = 'Frase longa: '.str_repeat('texto 123, pontuação! ', 40).' á à â ã é ê í ó ô õ ú ç 😊';
        $secondText = 'Texto independente';

        $this->actingAs($user);

        $component = Livewire::test(Feed::class)
            ->set("commentText.{$firstPost->id}", $firstText)
            ->set("commentText.{$secondPost->id}", $secondText)
            ->assertSet("commentText.{$firstPost->id}", $firstText)
            ->assertSet("commentText.{$secondPost->id}", $secondText)
            ->call('submitComment', $firstPost->id, $firstText)
            ->assertHasNoErrors()
            ->assertSet("commentText.{$secondPost->id}", $secondText);

        $this->assertDatabaseHas('social_comments', [
            'post_id' => $firstPost->id,
            'user_id' => $user->id,
            'content' => $firstText,
        ]);
        $this->assertDatabaseMissing('social_comments', [
            'post_id' => $secondPost->id,
            'content' => $firstText,
        ]);
        $this->assertDatabaseCount('social_comments', 1);

        $component->call('submitComment', $secondPost->id, $secondText);

        $this->assertDatabaseHas('social_comments', [
            'post_id' => $secondPost->id,
            'user_id' => $user->id,
            'content' => $secondText,
        ]);
        $this->assertDatabaseCount('social_comments', 2);
        $this->assertSame(2, SocialComment::count());
    }

    public function test_comment_fields_have_unique_bindings_and_disable_during_submission(): void
    {
        $user = User::factory()->create();
        $firstPost = SocialPost::create(['user_id' => $user->id, 'content' => 'First post']);
        $secondPost = SocialPost::create(['user_id' => $user->id, 'content' => 'Second post']);

        $this->actingAs($user);

        Livewire::test(Feed::class)
            ->assertSeeHtml('x-model="draft"')
            ->assertSeeHtml('comment-draft-cleared.window')
            ->assertSeeHtml('x-on:click="$wire.submitComment('.$firstPost->id.', draft)"')
            ->assertSeeHtml('$wire.submitComment('.$firstPost->id.', draft)')
            ->assertSeeHtml('$wire.submitComment('.$secondPost->id.', draft)')
            ->assertSeeHtml('x-on:keydown.enter.prevent')
            ->assertSeeHtml('wire:loading.attr="disabled"');
    }

    public function test_empty_comment_is_not_submitted(): void
    {
        $user = User::factory()->create();
        $post = SocialPost::create(['user_id' => $user->id, 'content' => 'A post']);

        $this->actingAs($user);

        Livewire::test(Feed::class)
            ->set("commentText.{$post->id}", '')
            ->call('submitComment', $post->id, '')
            ->assertHasErrors(["commentText.{$post->id}" => 'required']);

        $this->assertDatabaseCount('social_comments', 0);
    }

    public function test_commenting_on_another_users_post_still_sends_a_notification(): void
    {
        $author = User::factory()->create();
        $commenter = User::factory()->create();
        $post = SocialPost::create(['user_id' => $author->id, 'content' => 'A post']);

        $this->actingAs($commenter);

        Livewire::test(Feed::class)
            ->call('submitComment', $post->id, 'Comentário para notificação')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('social_comments', [
            'post_id' => $post->id,
            'user_id' => $commenter->id,
            'content' => 'Comentário para notificação',
        ]);
        $this->assertDatabaseHas('notifications', [
            'notifiable_type' => User::class,
            'notifiable_id' => $author->id,
            'type' => PostCommentedNotification::class,
        ]);
    }
}
