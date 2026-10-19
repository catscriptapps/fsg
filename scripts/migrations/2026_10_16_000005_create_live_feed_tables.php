<?php
// /scripts/migrations/2026_10_16_000005_create_live_feed_tables.php
//
// Live Feed (public page /live-feed — Src\Controller\LiveFeedController):
//
//   posts           a member's update: text, and/or one photo or video
//                   (media_url is a bare file name in public/images/uploads/posts/)
//   post_comments   comments on a post
//   post_likes      one like per member per post
//
// Additive and idempotent: each table is created only if it's missing.
// Comments and likes go with their post (ON DELETE CASCADE).

declare(strict_types=1);

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Schema\Blueprint;

return function (): array {
    $schema = Capsule::schema();
    $log = [];

    $tables = [
        'posts' => function (Blueprint $t) {
            $t->increments('post_id');
            $t->unsignedInteger('orig_user_id')->index();
            $t->text('content')->nullable();
            $t->string('media_url')->nullable();
            $t->enum('media_type', ['image', 'video', 'none'])->default('none');
            $t->timestamps();
            $t->index('created_at');
        },

        'post_comments' => function (Blueprint $t) {
            $t->increments('comment_id');
            $t->unsignedInteger('post_id')->index();
            $t->unsignedInteger('orig_user_id')->index();
            $t->text('comment_text');
            $t->timestamps();
            $t->foreign('post_id')->references('post_id')->on('posts')->onDelete('cascade');
        },

        'post_likes' => function (Blueprint $t) {
            $t->increments('like_id');
            $t->unsignedInteger('post_id')->index();
            $t->unsignedInteger('orig_user_id');
            $t->timestamps();
            $t->unique(['post_id', 'orig_user_id']);
            $t->foreign('post_id')->references('post_id')->on('posts')->onDelete('cascade');
        },
    ];

    foreach ($tables as $name => $definition) {
        if ($schema->hasTable($name)) {
            $log[] = "{$name} already exists — skipped";
            continue;
        }
        $schema->create($name, $definition);
        $log[] = "created {$name}";
    }

    return $log;
};
