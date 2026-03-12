<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('social_stories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->enum('type', ['image', 'video'])->default('image');
            $table->string('media_path');
            $table->string('caption', 300)->nullable();
            $table->unsignedInteger('views_count')->default(0);
            $table->timestamp('expires_at');
            $table->timestamps();
            $table->index(['user_id', 'expires_at']);
        });

        Schema::create('social_story_views', function (Blueprint $table) {
            $table->id();
            $table->foreignId('story_id')->constrained('social_stories')->cascadeOnDelete();
            $table->foreignId('viewer_id')->constrained('users')->cascadeOnDelete();
            $table->timestamp('viewed_at')->useCurrent();
            $table->unique(['story_id', 'viewer_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('social_story_views');
        Schema::dropIfExists('social_stories');
    }
};
