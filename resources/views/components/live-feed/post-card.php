<?php
// /resources/views/components/live-feed/post-card.php
//
// One Live Feed post (rendered by LiveFeedController::renderPostCard()).
// The class names / data-* hooks are what utils/live-feed/*.js use: like
// (.like-toggle-btn), comments (.view-comments-btn, .post-media-trigger),
// the ⋯ menu (.post-options-*), delete (.delete-post-btn).
//
// @var array $data   see renderPostCard()
// @var string $assetBase

declare(strict_types=1);

$e = fn($v) => htmlspecialchars((string) $v, ENT_QUOTES, 'UTF-8');
$postId = $data['encoded_id'] ?? '';
$author = $data['author'] ?? 'Member';
$mediaUrl = $data['media_url'] ?? null;
$mediaType = $data['media_type'] ?? 'none';
$isLiked = !empty($data['is_liked']);
$mediaSrc = $mediaUrl ? $assetBase . \Src\Controller\LiveFeedController::MEDIA_DIR . rawurlencode(basename($mediaUrl)) : '';
$avatarSrc = !empty($data['author_avatar']) ? $assetBase . 'images/uploads/avatars/' . ltrim($data['author_avatar'], '/') : null;

// Text: escaped, line breaks kept, and web addresses made clickable
$content = nl2br($e($data['content'] ?? ''));
$content = preg_replace(
    '~\bhttps?://[^\s<]+[^\s<.,;:!?)\]\'"]~i',
    '<a href="$0" target="_blank" rel="noopener noreferrer nofollow" class="font-medium text-primary-600 dark:text-primary-400 hover:underline break-all">$0</a>',
    $content
);
?>
<article class="live-post group/post bg-white dark:bg-gray-900 border border-gray-200/80 dark:border-gray-800 rounded-2xl shadow-sm transition-shadow hover:shadow-md" data-post-id="<?= $e($postId) ?>">

    <header class="flex items-center justify-between gap-3 px-4 sm:px-5 pt-4">
        <div class="flex items-center gap-3 min-w-0">
            <?php if ($avatarSrc): ?>
                <img src="<?= $e($avatarSrc) ?>" alt="" class="post-author-avatar h-10 w-10 flex-shrink-0 rounded-full object-cover ring-2 ring-white dark:ring-gray-900 shadow-sm">
            <?php else: ?>
                <span class="post-author-avatar h-10 w-10 flex-shrink-0 rounded-full bg-gradient-to-br from-primary-400 to-primary-600 flex items-center justify-center text-white font-bold shadow-sm"><?= $e(strtoupper(mb_substr($author, 0, 1))) ?></span>
            <?php endif; ?>
            <div class="min-w-0">
                <h3 class="post-author-name text-sm font-bold text-gray-900 dark:text-white truncate"><?= $e($author) ?></h3>
                <time class="post-time-ago block text-xs text-gray-500 dark:text-gray-400" datetime="<?= $e($data['posted_at'] ?? '') ?>" title="<?= $e($data['posted_label'] ?? '') ?>"><?= $e($data['time_ago'] ?? '') ?></time>
            </div>
        </div>

        <?php if (!empty($data['can_delete'])): ?>
            <div class="relative post-options-container">
                <button type="button" class="post-options-btn p-1.5 rounded-lg text-gray-400 hover:text-gray-700 hover:bg-gray-100 dark:hover:text-gray-200 dark:hover:bg-gray-800 transition-colors" aria-label="Post options" aria-haspopup="true">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M6 10a2 2 0 11-4 0 2 2 0 014 0zM12 10a2 2 0 11-4 0 2 2 0 014 0zM16 12a2 2 0 100-4 2 2 0 000 4z" /></svg>
                </button>
                <div class="post-options-menu hidden absolute right-0 mt-1 w-52 rounded-xl bg-white dark:bg-gray-800 shadow-lg ring-1 ring-black/5 dark:ring-white/10 z-20 py-1">
                    <button type="button" class="delete-post-btn w-full flex items-center gap-2 px-4 py-2 text-sm text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors" data-post-id="<?= $e($postId) ?>">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                        <span><?= !empty($data['is_own']) ? 'Delete post' : 'Remove post (admin)' ?></span>
                    </button>
                </div>
            </div>
        <?php endif; ?>
    </header>

    <?php if (($data['content'] ?? '') !== ''): ?>
        <div class="post-content px-4 sm:px-5 pt-3 text-[15px] leading-relaxed text-gray-800 dark:text-gray-200 break-words"><?= $content ?></div>
    <?php endif; ?>

    <?php if ($mediaSrc): ?>
        <div class="mt-3 bg-gray-100 dark:bg-gray-950 border-y border-gray-100 dark:border-gray-800 flex justify-center overflow-hidden">
            <?php if ($mediaType === 'video'): ?>
                <video controls preload="metadata" playsinline class="post-main-media max-h-[32rem] w-full bg-black" src="<?= $e($mediaSrc) ?>"></video>
            <?php else: ?>
                <img src="<?= $e($mediaSrc) ?>" alt="Photo shared by <?= $e($author) ?>" loading="lazy"
                    class="post-main-media post-media-trigger max-h-[32rem] w-full object-contain cursor-zoom-in">
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <footer class="flex items-center gap-1 px-2 sm:px-3 py-2 <?= $mediaSrc ? '' : 'mt-2 border-t border-gray-100 dark:border-gray-800' ?>">
        <button type="button" data-id="<?= $e($postId) ?>" aria-pressed="<?= $isLiked ? 'true' : 'false' ?>"
            class="like-toggle-btn inline-flex items-center gap-2 rounded-xl px-3 py-2 text-sm font-semibold transition-all active:scale-95 hover:bg-gray-50 dark:hover:bg-gray-800 <?= $isLiked ? 'text-rose-600 dark:text-rose-400' : 'text-gray-500 dark:text-gray-400' ?>">
            <svg class="w-5 h-5 <?= $isLiked ? 'fill-current' : 'fill-none' ?>" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" /></svg>
            <span class="like-count"><?= (int) ($data['like_count'] ?? 0) ?></span>
            <span class="sr-only">likes</span>
        </button>
        <button type="button" data-id="<?= $e($postId) ?>"
            class="view-comments-btn inline-flex items-center gap-2 rounded-xl px-3 py-2 text-sm font-semibold text-gray-500 dark:text-gray-400 hover:bg-gray-50 hover:text-secondary-700 dark:hover:bg-gray-800 dark:hover:text-white transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" /></svg>
            <span class="comment-count"><?= (int) ($data['comment_count'] ?? 0) ?></span>
            <span class="hidden sm:inline">Comment</span>
        </button>
    </footer>
</article>
