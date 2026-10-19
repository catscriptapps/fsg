<?php
// /resources/views/components/live-feed/post-card.php
//
// One Live Feed post (rendered by LiveFeedController::renderPostCard()), in
// the public site's look. The class names / data-* hooks are what
// utils/live-feed/*.js use: like (.like-toggle-btn), comments
// (.view-comments-btn, .post-media-trigger), the ⋯ menu (.post-options-*),
// delete (.delete-post-btn), counts (.like-count, .comment-count).
// Guests (can_interact false) see the counts, not the like button; they can
// still open a post to read its comments.
//
// @var array $data   see renderPostCard()
// @var string $assetBase

declare(strict_types=1);

use Src\Content\Icons;

$postId = $data['encoded_id'] ?? '';
$author = $data['author'] ?? 'Frequency Strategy Group';
$mediaUrl = $data['media_url'] ?? null;
$mediaType = $data['media_type'] ?? 'none';
$isLiked = !empty($data['is_liked']);
$canInteract = !empty($data['can_interact']);
$likes = (int) ($data['like_count'] ?? 0);
$comments = (int) ($data['comment_count'] ?? 0);
$mediaSrc = $mediaUrl ? $assetBase . \Src\Controller\LiveFeedController::MEDIA_DIR . rawurlencode(basename($mediaUrl)) : '';
$avatarSrc = !empty($data['author_avatar']) ? $assetBase . 'images/uploads/avatars/' . ltrim($data['author_avatar'], '/') : null;

// Text: escaped, line breaks kept, and web addresses made clickable
$content = nl2br(e($data['content'] ?? ''));
$content = preg_replace(
    '~\bhttps?://[^\s<]+[^\s<.,;:!?)\]\'"]~i',
    '<a href="$0" target="_blank" rel="noopener noreferrer nofollow" class="break-all font-semibold text-gold-800 underline decoration-gold-500/40 underline-offset-4 hover:decoration-gold-700">$0</a>',
    $content
);
?>
<article class="live-post group/post rounded-2xl border border-ink-900/[0.07] bg-white shadow-[0_1px_2px_rgba(23,21,18,0.04),0_12px_32px_-14px_rgba(23,21,18,0.12)]" data-post-id="<?= e($postId) ?>">

    <header class="flex items-center justify-between gap-3 px-5 pt-5 sm:px-6">
        <div class="flex min-w-0 items-center gap-3">
            <?php if ($avatarSrc): ?>
                <img src="<?= e($avatarSrc) ?>" alt="" class="post-author-avatar h-11 w-11 flex-shrink-0 rounded-full object-cover ring-2 ring-gold-200">
            <?php else: ?>
                <span class="post-author-avatar flex h-11 w-11 flex-shrink-0 items-center justify-center rounded-full bg-gradient-to-br from-gold-300 to-gold-600 font-serif text-lg text-ink-950"><?= e(strtoupper(mb_substr($author, 0, 1))) ?></span>
            <?php endif; ?>
            <div class="min-w-0">
                <h3 class="post-author-name truncate font-semibold text-ink-950"><?= e($author) ?></h3>
                <p class="flex items-center gap-1.5 text-xs text-ink-500">
                    <span class="font-semibold uppercase tracking-widest text-gold-700">Team</span>
                    <span aria-hidden="true">·</span>
                    <time class="post-time-ago" datetime="<?= e($data['posted_at'] ?? '') ?>" title="<?= e($data['posted_label'] ?? '') ?>"><?= e($data['time_ago'] ?? '') ?></time>
                </p>
            </div>
        </div>

        <?php if (!empty($data['can_delete'])): ?>
            <div class="post-options-container relative">
                <button type="button" class="post-options-btn rounded-lg p-1.5 text-ink-400 transition-colors hover:bg-ivory hover:text-ink-800" aria-label="Post options" aria-haspopup="true">
                    <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true"><path d="M6 10a2 2 0 11-4 0 2 2 0 014 0zM12 10a2 2 0 11-4 0 2 2 0 014 0zM16 12a2 2 0 100-4 2 2 0 000 4z" /></svg>
                </button>
                <div class="post-options-menu absolute right-0 z-20 mt-1 hidden w-52 rounded-xl bg-white py-1 shadow-lg ring-1 ring-ink-900/10">
                    <button type="button" class="delete-post-btn flex w-full items-center gap-2 px-4 py-2 text-sm text-red-600 transition-colors hover:bg-red-50" data-post-id="<?= e($postId) ?>">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                        <span><?= !empty($data['is_own']) ? 'Delete post' : 'Remove post (admin)' ?></span>
                    </button>
                </div>
            </div>
        <?php endif; ?>
    </header>

    <?php if (($data['content'] ?? '') !== ''): ?>
        <div class="post-content break-words px-5 pt-4 text-base leading-relaxed text-ink-800 sm:px-6"><?= $content ?></div>
    <?php endif; ?>

    <?php if ($mediaSrc): ?>
        <div class="mt-4 flex justify-center overflow-hidden border-y border-ink-900/[0.06] bg-ink-950">
            <?php if ($mediaType === 'video'): ?>
                <video controls preload="metadata" playsinline class="post-main-media max-h-[34rem] w-full bg-black" src="<?= e($mediaSrc) ?>"></video>
            <?php else: ?>
                <img src="<?= e($mediaSrc) ?>" alt="Photo shared by <?= e($author) ?>" loading="lazy"
                    class="post-main-media post-media-trigger max-h-[34rem] w-full cursor-zoom-in object-contain">
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <footer class="flex items-center gap-1 px-3 py-2.5 sm:px-4 <?= $mediaSrc ? '' : 'mt-3 border-t border-ink-900/[0.06]' ?>">
        <?php if ($canInteract): ?>
            <button type="button" data-id="<?= e($postId) ?>" aria-pressed="<?= $isLiked ? 'true' : 'false' ?>"
                class="like-toggle-btn inline-flex items-center gap-2 rounded-xl px-3 py-2 text-sm font-semibold transition-all hover:bg-ivory active:scale-95 <?= $isLiked ? 'text-rose-600' : 'text-ink-500' ?>">
                <svg class="h-5 w-5 <?= $isLiked ? 'fill-current' : 'fill-none' ?>" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" /></svg>
                <span class="like-count"><?= $likes ?></span>
                <span class="sr-only">likes</span>
            </button>
        <?php else: ?>
            <span class="inline-flex items-center gap-2 px-3 py-2 text-sm font-semibold text-ink-500" title="Likes from our team">
                <?= Icons::svg('heart', 'h-5 w-5 text-rose-500') ?>
                <span class="like-count"><?= $likes ?></span>
                <span class="sr-only">likes</span>
            </span>
        <?php endif; ?>
        <button type="button" data-id="<?= e($postId) ?>"
            class="view-comments-btn inline-flex items-center gap-2 rounded-xl px-3 py-2 text-sm font-semibold text-ink-500 transition-colors hover:bg-ivory hover:text-gold-800">
            <?= Icons::svg('comment', 'h-5 w-5') ?>
            <span class="comment-count"><?= $comments ?></span>
            <span class="hidden sm:inline"><?= $canInteract ? 'Comment' : ($comments === 1 ? 'comment' : 'comments') ?></span>
        </button>
    </footer>
</article>
