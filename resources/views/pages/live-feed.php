<?php
// /resources/views/pages/live-feed.php
//
// Live Feed — members share updates, photos and videos, like and comment.
// Two views: Everyone (every member's posts) and Following (yours + people
// you follow). The first page is rendered here; pages/live-feed-page.js
// pages further, checks for new activity every 30 seconds, and runs the
// composer, post window and follow tools (utils/live-feed/*).
// Retrofitted from the CatScript Apps Social Feed.
//
// @var string $assetBase
// @var string $baseUrl

declare(strict_types=1);

use Src\Controller\FollowsController;
use Src\Controller\LiveFeedController;
use Src\Service\AuthService;

if (!AuthService::hasAccess('Live Feed')) {
    include __DIR__ . '/access-denied.php';
    return;
}

$e = fn($v) => htmlspecialchars((string) $v, ENT_QUOTES, 'UTF-8');
$me = AuthService::currentUser();
$meId = (int) $me->id;
$scope = ($_GET['view'] ?? '') === 'following' ? 'following' : 'everyone';
$first = LiveFeedController::page($meId, $scope, 1);
$stats = FollowsController::stats($meId);
$myPosts = LiveFeedController::postCount($meId);
$meAvatar = !empty($me->avatar_url) ? $assetBase . 'images/uploads/avatars/' . $me->avatar_url : null;
$meInitial = strtoupper(mb_substr($me->full_name ?: 'U', 0, 1));

$avatar = fn(string $size) => $meAvatar
    ? '<img src="' . $e($meAvatar) . '" alt="" class="' . $size . ' flex-shrink-0 rounded-full object-cover">'
    : '<span class="' . $size . ' flex-shrink-0 rounded-full bg-gradient-to-br from-primary-400 to-primary-600 flex items-center justify-center text-white font-bold">' . $e($meInitial) . '</span>';

$emptyState = static function (string $scope): string {
    return $scope === 'following'
        ? '<div data-empty-feed class="rounded-2xl border border-dashed border-gray-300 dark:border-gray-700 bg-white/60 dark:bg-gray-900/60 px-6 py-12 text-center"><p class="text-3xl mb-2">👋</p><h3 class="text-sm font-bold text-gray-900 dark:text-white">Nothing here yet</h3><p class="mx-auto mt-1 max-w-sm text-sm text-gray-500 dark:text-gray-400">Follow people from “People to follow” and their posts will show up here, alongside your own.</p></div>'
        : '<div data-empty-feed class="rounded-2xl border border-dashed border-gray-300 dark:border-gray-700 bg-white/60 dark:bg-gray-900/60 px-6 py-12 text-center"><p class="text-3xl mb-2">✨</p><h3 class="text-sm font-bold text-gray-900 dark:text-white">The feed is quiet</h3><p class="mx-auto mt-1 max-w-sm text-sm text-gray-500 dark:text-gray-400">Be the first to share an update, a photo or a video with the team.</p></div>';
};
?>
<div id="live-feed-page" class="grid grid-cols-1 lg:grid-cols-12 gap-6"
    data-scope="<?= $e($scope) ?>" data-sig="<?= $e($first['sig']) ?>" data-next-page="<?= (int) $first['next_page'] ?>"
    data-empty-everyone="<?= $e($emptyState('everyone')) ?>" data-empty-following="<?= $e($emptyState('following')) ?>">

    <div class="lg:col-span-8 space-y-5 min-w-0">

        <!-- Title, live indicator, view switch -->
        <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4">
            <div>
                <div class="flex items-center gap-3">
                    <h1 class="text-2xl sm:text-3xl font-bold tracking-tight text-gray-900 dark:text-white">Live Feed</h1>
                    <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 dark:bg-emerald-950/40 px-2.5 py-1 text-[11px] font-bold uppercase tracking-wider text-emerald-700 dark:text-emerald-300" title="New posts, likes and comments appear on their own">
                        <span class="relative flex h-2 w-2"><span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-emerald-400 opacity-75"></span><span class="relative inline-flex h-2 w-2 rounded-full bg-emerald-500"></span></span>
                        Live
                    </span>
                </div>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Updates, photos and videos from across the team.</p>
            </div>
            <div role="tablist" aria-label="Which posts" class="inline-flex self-start sm:self-auto rounded-xl bg-gray-100 dark:bg-gray-800 p-1">
                <?php foreach (['everyone' => 'Everyone', 'following' => 'Following'] as $key => $label): ?>
                    <button type="button" role="tab" data-feed-scope="<?= $key ?>" aria-selected="<?= $scope === $key ? 'true' : 'false' ?>"
                        class="rounded-lg px-4 py-1.5 text-sm font-semibold text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white transition-all
                            aria-selected:bg-white aria-selected:text-gray-900 aria-selected:shadow-sm dark:aria-selected:bg-gray-900 dark:aria-selected:text-white"><?= $label ?></button>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Composer shortcut -->
        <div class="rounded-2xl bg-white dark:bg-gray-900 border border-gray-200/80 dark:border-gray-800 shadow-sm p-4">
            <div class="flex items-center gap-3">
                <?= $avatar('h-10 w-10') ?>
                <button type="button" data-compose="text"
                    class="flex-1 rounded-full bg-gray-100 dark:bg-gray-800 px-4 py-2.5 text-left text-sm text-gray-500 dark:text-gray-400 hover:bg-gray-200/70 dark:hover:bg-gray-700/70 transition-colors">
                    Share an update, <?= $e($me->first_name ?: 'there') ?>…
                </button>
            </div>
            <div class="mt-3 flex items-center gap-1 border-t border-gray-100 dark:border-gray-800 pt-3">
                <button type="button" data-compose="photo" class="flex-1 sm:flex-none inline-flex items-center justify-center gap-2 rounded-xl px-4 py-2 text-sm font-semibold text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
                    <svg class="h-5 w-5 text-emerald-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                    Photo
                </button>
                <button type="button" data-compose="video" class="flex-1 sm:flex-none inline-flex items-center justify-center gap-2 rounded-xl px-4 py-2 text-sm font-semibold text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
                    <svg class="h-5 w-5 text-rose-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" /></svg>
                    Video
                </button>
                <button type="button" data-compose="text" class="ml-auto hidden sm:inline-flex items-center gap-2 rounded-xl bg-primary-600 hover:bg-primary-500 px-4 py-2 text-sm font-bold text-white shadow-sm shadow-primary-600/30 transition-colors">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" /></svg>
                    New post
                </button>
            </div>
        </div>

        <!-- The feed -->
        <div id="live-feed-posts" class="space-y-4" aria-live="polite">
            <?= $first['html'] !== '' ? $first['html'] : $emptyState($scope) ?>
        </div>
        <div class="flex justify-center">
            <button type="button" data-load-more class="<?= $first['has_more'] ? '' : 'hidden' ?> rounded-xl bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 px-5 py-2.5 text-sm font-semibold text-gray-700 dark:text-gray-200 shadow-sm hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors disabled:opacity-60">
                Load older posts
            </button>
        </div>
    </div>

    <!-- Sidebar: you, people search, people to follow -->
    <aside class="lg:col-span-4 space-y-5 lg:sticky lg:top-24 self-start">
        <div class="rounded-2xl bg-white dark:bg-gray-900 border border-gray-200/80 dark:border-gray-800 shadow-sm overflow-hidden">
            <div class="h-14 bg-gradient-to-br from-secondary-700 via-secondary-800 to-secondary-950 relative">
                <div aria-hidden="true" class="absolute -top-8 right-6 h-24 w-24 rounded-full bg-primary-500/30 blur-2xl"></div>
            </div>
            <div class="px-5 pb-5">
                <div class="relative -mt-7 flex items-end gap-3">
                    <span class="rounded-full ring-4 ring-white dark:ring-gray-900"><?= $avatar('h-14 w-14 text-xl') ?></span>
                    <div class="min-w-0 pb-1">
                        <p class="text-sm font-bold text-gray-900 dark:text-white truncate"><?= $e($me->full_name) ?></p>
                        <a href="<?= $baseUrl ?>profile" data-partial class="text-xs font-medium text-gray-500 hover:text-primary-600 dark:text-gray-400">View profile</a>
                    </div>
                </div>
                <div class="mt-4 grid grid-cols-3 divide-x divide-gray-100 dark:divide-gray-800 rounded-xl bg-gray-50 dark:bg-gray-800/60 text-center">
                    <div class="py-2.5"><span data-my-posts class="block text-lg font-bold text-gray-900 dark:text-white"><?= $myPosts ?></span><span class="text-[11px] font-medium text-gray-500 dark:text-gray-400">Posts</span></div>
                    <button type="button" data-follow-list="following" class="py-2.5 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors"><span data-following-count class="block text-lg font-bold text-gray-900 dark:text-white"><?= $stats['following'] ?></span><span class="text-[11px] font-medium text-gray-500 dark:text-gray-400">Following</span></button>
                    <button type="button" data-follow-list="followers" class="py-2.5 rounded-r-xl hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors"><span data-followers-count class="block text-lg font-bold text-gray-900 dark:text-white"><?= $stats['followers'] ?></span><span class="text-[11px] font-medium text-gray-500 dark:text-gray-400">Followers</span></button>
                </div>
            </div>
        </div>

        <div class="rounded-2xl bg-white dark:bg-gray-900 border border-gray-200/80 dark:border-gray-800 shadow-sm p-4">
            <div class="relative">
                <svg class="pointer-events-none absolute left-3.5 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                <input type="search" data-people-search placeholder="Find people" autocomplete="off" aria-label="Find people"
                    class="block w-full rounded-xl border-0 bg-gray-100 dark:bg-gray-800 py-2.5 pl-10 pr-3 text-sm text-gray-900 dark:text-white placeholder-gray-400 focus:ring-2 focus:ring-primary-500/40 outline-none">
            </div>
            <h2 data-people-heading class="mt-4 mb-1 px-1 text-[11px] font-bold uppercase tracking-[0.15em] text-gray-400">People to follow</h2>
            <div data-people-list class="max-h-[22rem] overflow-y-auto custom-scrollbar -mx-1 space-y-0.5">
                <div class="space-y-3 p-2 animate-pulse">
                    <?php for ($i = 0; $i < 3; $i++): ?>
                        <div class="flex items-center gap-3"><div class="h-9 w-9 rounded-full bg-gray-100 dark:bg-gray-800"></div><div class="h-2.5 flex-1 rounded bg-gray-100 dark:bg-gray-800"></div></div>
                    <?php endfor; ?>
                </div>
            </div>
        </div>
    </aside>

    <?php include __DIR__ . '/../components/live-feed/view-post-modal.php'; ?>
</div>
