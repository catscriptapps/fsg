<?php
// /scripts/migrations/2026_10_16_000005_create_live_feed_tables.php
//
// Live Feed (members: /live-feed — Src\Controller\LiveFeedController and
// FollowsController):
//
//   posts           a member's update: text, and/or one photo or video
//                   (media_url is a bare file name in public/images/uploads/posts/)
//   post_comments   comments on a post
//   post_likes      one like per member per post
//   follows         who follows whom (the "Following" view of the feed)
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

        'follows' => function (Blueprint $t) {
            $t->increments('follow_id');
            $t->unsignedInteger('follower_id');   // the person doing the following
            $t->unsignedInteger('following_id')->index(); // the person being followed
            $t->timestamps();
            $t->unique(['follower_id', 'following_id']);
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
