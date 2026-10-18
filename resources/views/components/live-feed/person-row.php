<?php
// /resources/views/components/live-feed/person-row.php
//
// One member in the Live Feed's "People to follow" list, people search or
// following / followers window (FollowsController::renderRows()).
// @var array $data {id, name, avatar, is_following, follows_you}
// @var string $assetBase

declare(strict_types=1);

$e = fn($v) => htmlspecialchars((string) $v, ENT_QUOTES, 'UTF-8');
$name = $data['name'] ?? 'Member';
$following = !empty($data['is_following']);
$avatarSrc = !empty($data['avatar']) ? $assetBase . 'images/uploads/avatars/' . ltrim($data['avatar'], '/') : null;
?>
<div class="flex items-center justify-between gap-3 rounded-xl p-2.5 hover:bg-gray-50 dark:hover:bg-gray-800/60 transition-colors" data-user-row="<?= (int) ($data['id'] ?? 0) ?>">
    <div class="flex items-center gap-3 min-w-0">
        <?php if ($avatarSrc): ?>
            <img src="<?= $e($avatarSrc) ?>" alt="" class="h-9 w-9 flex-shrink-0 rounded-full object-cover">
        <?php else: ?>
            <span class="h-9 w-9 flex-shrink-0 rounded-full bg-gradient-to-br from-secondary-500 to-secondary-700 flex items-center justify-center text-white text-sm font-bold"><?= $e(strtoupper(mb_substr($name, 0, 1))) ?></span>
        <?php endif; ?>
        <div class="min-w-0">
            <p class="text-sm font-semibold text-gray-900 dark:text-white truncate"><?= $e($name) ?></p>
            <?php if (!empty($data['follows_you'])): ?>
                <p class="text-[11px] font-medium text-gray-500 dark:text-gray-400">Follows you</p>
            <?php endif; ?>
        </div>
    </div>
    <button type="button" data-follow-toggle="<?= (int) ($data['id'] ?? 0) ?>" data-following="<?= $following ? '1' : '0' ?>" aria-pressed="<?= $following ? 'true' : 'false' ?>"
        class="follow-toggle-btn flex-shrink-0 rounded-full px-3.5 py-1.5 text-xs font-bold transition-all active:scale-95
            <?= $following
                ? 'bg-gray-100 text-gray-600 hover:bg-red-50 hover:text-red-600 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-red-950/40 dark:hover:text-red-400'
                : 'bg-secondary-900 text-white hover:bg-secondary-700 dark:bg-white dark:text-gray-900 dark:hover:bg-gray-200' ?>">
        <?= $following ? 'Following' : 'Follow' ?>
    </button>
</div>
