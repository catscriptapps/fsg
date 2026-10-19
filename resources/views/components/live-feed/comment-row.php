<?php
// /resources/views/components/live-feed/comment-row.php
//
// One comment in the Live Feed's post window (LiveFeedController), in the
// public site's look.
// @var array $data {id, author, author_avatar, comment_text, time_ago, can_delete}
// @var string $assetBase

declare(strict_types=1);

$author = $data['author'] ?? 'Frequency Strategy Group';
$avatarSrc = !empty($data['author_avatar']) ? $assetBase . 'images/uploads/avatars/' . ltrim($data['author_avatar'], '/') : null;
?>
<div class="group flex items-start gap-3" data-comment-id="<?= (int) ($data['id'] ?? 0) ?>">
    <?php if ($avatarSrc): ?>
        <img src="<?= e($avatarSrc) ?>" alt="" class="h-9 w-9 flex-shrink-0 rounded-full object-cover">
    <?php else: ?>
        <span class="flex h-9 w-9 flex-shrink-0 items-center justify-center rounded-full bg-gradient-to-br from-gold-300 to-gold-600 font-serif text-sm text-ink-950"><?= e(strtoupper(mb_substr($author, 0, 1))) ?></span>
    <?php endif; ?>
    <div class="min-w-0 flex-1">
        <div class="inline-block max-w-full rounded-2xl rounded-tl-md bg-ivory-deep px-4 py-2.5">
            <p class="text-xs font-semibold text-ink-950"><?= e($author) ?></p>
            <p class="break-words text-sm leading-relaxed text-ink-700"><?= nl2br(e($data['comment_text'] ?? '')) ?></p>
        </div>
        <div class="mt-1 flex items-center gap-3 px-1">
            <span class="text-[11px] text-ink-400"><?= e($data['time_ago'] ?? '') ?></span>
            <?php if (!empty($data['can_delete'])): ?>
                <button type="button" data-delete-comment="<?= (int) ($data['id'] ?? 0) ?>"
                    class="text-[11px] font-semibold text-ink-400 transition-opacity hover:text-red-600 focus:opacity-100 sm:opacity-0 sm:group-hover:opacity-100">Delete</button>
            <?php endif; ?>
        </div>
    </div>
</div>
