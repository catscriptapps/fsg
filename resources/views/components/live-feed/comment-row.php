<?php
// /resources/views/components/live-feed/comment-row.php
//
// One comment in the Live Feed's post window (LiveFeedController).
// @var array $data {id, author, author_avatar, comment_text, time_ago, can_delete}
// @var string $assetBase

declare(strict_types=1);

$e = fn($v) => htmlspecialchars((string) $v, ENT_QUOTES, 'UTF-8');
$author = $data['author'] ?? 'Member';
$avatarSrc = !empty($data['author_avatar']) ? $assetBase . 'images/uploads/avatars/' . ltrim($data['author_avatar'], '/') : null;
?>
<div class="group flex items-start gap-3" data-comment-id="<?= (int) ($data['id'] ?? 0) ?>">
    <?php if ($avatarSrc): ?>
        <img src="<?= $e($avatarSrc) ?>" alt="" class="h-8 w-8 flex-shrink-0 rounded-full object-cover">
    <?php else: ?>
        <span class="h-8 w-8 flex-shrink-0 rounded-full bg-gradient-to-br from-primary-400 to-primary-600 flex items-center justify-center text-white text-xs font-bold"><?= $e(strtoupper(mb_substr($author, 0, 1))) ?></span>
    <?php endif; ?>
    <div class="min-w-0 flex-1">
        <div class="inline-block max-w-full rounded-2xl rounded-tl-md bg-gray-100 dark:bg-gray-800 px-3.5 py-2">
            <p class="text-xs font-bold text-gray-900 dark:text-white"><?= $e($author) ?></p>
            <p class="text-sm text-gray-700 dark:text-gray-300 break-words"><?= nl2br($e($data['comment_text'] ?? '')) ?></p>
        </div>
        <div class="mt-1 flex items-center gap-3 px-1">
            <span class="text-[11px] text-gray-400"><?= $e($data['time_ago'] ?? '') ?></span>
            <?php if (!empty($data['can_delete'])): ?>
                <button type="button" data-delete-comment="<?= (int) ($data['id'] ?? 0) ?>"
                    class="text-[11px] font-semibold text-gray-400 hover:text-red-500 sm:opacity-0 sm:group-hover:opacity-100 focus:opacity-100 transition-opacity">Delete</button>
            <?php endif; ?>
        </div>
    </div>
</div>
