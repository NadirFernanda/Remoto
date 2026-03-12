<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Extend social_posts with new columns
        Schema::table('social_posts', function (Blueprint $table) {
            $table->enum('type', ['text', 'image', 'video', 'audio', 'link', 'repost'])
                  ->default('text')->after('content');
            $table->string('link_url', 2000)->nullable()->after('type');
            $table->string('link_title')->nullable()->after('link_url');
            $table->text('link_description')->nullable()->after('link_title');
            $table->string('link_image')->nullable()->after('link_description');
            $table->unsignedBigInteger('repost_id')->nullable()->after('link_image');
            $table->foreign('repost_id')->references('id')->on('social_posts')->nullOnDelete();
            $table->enum('visibility', ['public', 'followers'])->default('public')->after('repost_id');
            $table->unsignedBigInteger('views_count')->default(0)->after('visibility');
        });

        // 2. Create unified media table (images, videos, audio, documents)
        Schema::create('social_post_media', function (Blueprint $table) {
            $table->id();
            $table->foreignId('post_id')->constrained('social_posts')->cascadeOnDelete();
            $table->enum('type', ['image', 'video', 'audio', 'document']);
            $table->string('path');
            $table->string('thumbnail_path')->nullable(); // video poster frame
            $table->string('original_name')->nullable();
            $table->string('mime_type', 100)->nullable();
            $table->unsignedBigInteger('file_size')->nullable(); // bytes
            $table->unsignedSmallInteger('duration')->nullable(); // seconds (video/audio)
            $table->unsignedTinyInteger('order')->default(0);
            $table->timestamps();
            $table->index(['post_id', 'type']);
        });

        // 3. Migrate existing social_post_images → social_post_media
        if (Schema::hasTable('social_post_images')) {
            $now = now()->toDateTimeString();
            DB::table('social_post_images')->orderBy('id')->chunk(200, function ($images) use ($now) {
                $rows = [];
                foreach ($images as $img) {
                    $rows[] = [
                        'post_id'    => $img->post_id,
                        'type'       => 'image',
                        'path'       => $img->path,
                        'order'      => $img->order,
                        'created_at' => $img->created_at ?? $now,
                        'updated_at' => $img->updated_at ?? $now,
                    ];
                }
                if ($rows) {
                    DB::table('social_post_media')->insertOrIgnore($rows);
                }
            });

            // Mark posts that had images as type='image'
            $postIds = DB::table('social_post_images')->distinct()->pluck('post_id')->toArray();
            if (!empty($postIds)) {
                DB::table('social_posts')
                    ->whereIn('id', $postIds)
                    ->where('type', 'text')
                    ->update(['type' => 'image']);
            }
        }
    }

    public function down(): void
    {
        Schema::table('social_posts', function (Blueprint $table) {
            $table->dropForeign(['repost_id']);
            $table->dropColumn([
                'type', 'link_url', 'link_title', 'link_description',
                'link_image', 'repost_id', 'visibility', 'views_count',
            ]);
        });
        Schema::dropIfExists('social_post_media');
    }
};
