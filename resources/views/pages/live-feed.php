<?php
// /resources/views/pages/live-feed.php
//
// Live Feed — the team's public news feed, in the public website layout.
// Everyone (guests included) reads the posts, photos, videos, likes and
// comments; signed-in staff and admins also post, like and comment (the API
// guards every write — server/api/live-feed.php). The first page is
// rendered here; site.js runs utils/live-feed/* (paging, 30-second live
// updates, the composer and the post window).

declare(strict_types=1);

use Src\Content\Icons;
use Src\Controller\LiveFeedController;
use Src\Service\AuthService;

$me = AuthService::isLoggedIn() ? AuthService::currentUser() : null;
$meId = $me ? (int) $me->id : 0;
$canPost = $me && AuthService::hasAccess('Live Feed');
$first = LiveFeedController::page($meId, 1);

$meAvatar = $me && !empty($me->avatar_url) ? $assetBase . 'images/uploads/avatars/' . $me->avatar_url : null;
$avatar = fn(string $size) => $meAvatar
    ? '<img src="' . e($meAvatar) . '" alt="" class="' . $size . ' flex-shrink-0 rounded-full object-cover">'
    : '<span class="' . $size . ' flex flex-shrink-0 items-center justify-center rounded-full bg-gradient-to-br from-gold-300 to-gold-600 font-serif text-ink-950">' . e(strtoupper(mb_substr($me->full_name ?? 'F', 0, 1))) . '</span>';

$emptyState = '<div data-empty-feed class="rounded-2xl border border-dashed border-gold-500/40 bg-white/60 px-6 py-14 text-center">'
    . '<span class="mx-auto flex h-14 w-14 items-center justify-center rounded-full border border-gold-500/40 text-gold-700">' . Icons::svg('listen') . '</span>'
    . '<h3 class="display mt-5 text-2xl">News is on its <em>way.</em></h3>'
    . '<p class="mx-auto mt-2 max-w-sm text-sm text-ink-600">' . ($canPost ? 'Be the first to share an update, a photo or a video.' : 'Updates, photos and videos from our team will appear here.') . '</p></div>';

$hero = [
    'eyebrow' => 'Live Feed',
    'title' => 'News from <em>the team.</em>',
    'lead' => 'Updates, photos and videos from Frequency Strategy Group — our work, our partners and our community, as it happens.',
    'photo' => 'live-feed.hero', // photo spot — chosen in the admin under Site Photos
    'crumbs' => [['Live Feed', null]],
    'actions' => '<span class="inline-flex items-center gap-2 rounded-full border border-emerald-300/30 bg-emerald-400/10 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-emerald-200" title="New posts, likes and comments appear on their own">'
        . '<span class="relative flex h-2 w-2"><span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-emerald-400 opacity-75"></span><span class="relative inline-flex h-2 w-2 rounded-full bg-emerald-400"></span></span>Live — updates on its own</span>',
];
include __DIR__ . '/../components/site/page-hero.php';
?>
<section class="bg-ivory py-16 lg:py-24">
    <div id="live-feed-page" class="site-container grid grid-cols-1 gap-8 lg:grid-cols-12 lg:gap-12"
        data-sig="<?= e($first['sig']) ?>" data-next-page="<?= (int) $first['next_page'] ?>" data-empty="<?= e($emptyState) ?>">

        <div class="min-w-0 space-y-5 lg:col-span-8">
            <?php if ($canPost): ?>
                <!-- Composer shortcut (staff and admins) -->
                <div class="rounded-2xl border border-ink-900/[0.07] bg-white p-4 shadow-sm">
                    <div class="flex items-center gap-3">
                        <?= $avatar('h-11 w-11 text-lg') ?>
                        <button type="button" data-compose="text"
                            class="flex-1 rounded-full bg-ivory-deep px-5 py-3 text-left text-sm text-ink-500 transition-colors hover:bg-gold-100/70">
                            Share an update, <?= e($me->first_name ?: 'there') ?>…
                        </button>
                    </div>
                    <div class="mt-3 flex items-center gap-1 border-t border-ink-900/[0.06] pt-3">
                        <button type="button" data-compose="photo" class="inline-flex flex-1 items-center justify-center gap-2 rounded-xl px-4 py-2 text-sm font-semibold text-ink-700 transition-colors hover:bg-ivory sm:flex-none">
                            <?= Icons::svg('photo', 'h-5 w-5 text-gold-700') ?> Photo
                        </button>
                        <button type="button" data-compose="video" class="inline-flex flex-1 items-center justify-center gap-2 rounded-xl px-4 py-2 text-sm font-semibold text-ink-700 transition-colors hover:bg-ivory sm:flex-none">
                            <?= Icons::svg('video', 'h-5 w-5 text-gold-700') ?> Video
                        </button>
                        <button type="button" data-compose="text" class="btn-ink ml-auto hidden !px-5 !py-2.5 sm:inline-flex">
                            <?= Icons::svg('plus', 'h-4 w-4') ?> New post
                        </button>
                    </div>
                    <p class="mt-3 flex items-center gap-2 px-1 text-xs text-ink-500"><?= Icons::svg('globe', 'h-3.5 w-3.5 text-gold-700') ?> Posts are public — anyone visiting the website can see them.</p>
                </div>
            <?php endif; ?>

            <!-- The feed -->
            <div id="live-feed-posts" class="space-y-5" aria-live="polite">
                <?= $first['html'] !== '' ? $first['html'] : $emptyState ?>
            </div>
            <div class="flex justify-center">
                <button type="button" data-load-more class="<?= $first['has_more'] ? '' : 'hidden' ?> btn-outline disabled:opacity-60">Load older posts</button>
            </div>
        </div>

        <!-- Side panel -->
        <aside class="space-y-5 self-start lg:sticky lg:top-28 lg:col-span-4">
            <?php if ($me): ?>
                <div class="rounded-2xl border border-ink-900/[0.07] bg-white p-6 shadow-sm">
                    <div class="flex items-center gap-3">
                        <?= $avatar('h-12 w-12 text-xl') ?>
                        <div class="min-w-0">
                            <p class="text-xs font-semibold uppercase tracking-widest text-gold-700">Signed in as</p>
                            <p class="truncate font-semibold text-ink-950"><?= e($me->full_name) ?></p>
                        </div>
                    </div>
                    <p class="mt-4 text-sm leading-relaxed text-ink-600"><?= $canPost ? 'You can post, like and comment. Remember everything here is public — never share confidential client information.' : 'You can read the feed. Ask an administrator if you need to post.' ?></p>
                    <div class="mt-4 rounded-xl bg-ivory px-4 py-3 text-center">
                        <span data-my-posts class="block font-serif text-3xl text-ink-950"><?= LiveFeedController::postCount($meId) ?></span>
                        <span class="text-xs font-semibold uppercase tracking-widest text-ink-500">Your posts</span>
                    </div>
                </div>
            <?php endif; ?>

            <div class="ink-surface relative overflow-hidden rounded-2xl p-7">
                <div aria-hidden="true" class="pointer-events-none absolute -bottom-6 right-0 w-56 opacity-[0.12]"><?= Icons::waveform('w-full', false) ?></div>
                <p class="eyebrow eyebrow-light">About this feed</p>
                <p class="mt-4 font-serif text-2xl leading-snug text-ivory">What we’re working on, <em class="text-gold-300">as it happens.</em></p>
                <p class="mt-3 text-sm leading-relaxed text-ink-300">News, photos and videos from our team, partners and community work. New posts, likes and comments appear here on their own.</p>
                <?php if (!$me): ?>
                    <a data-partial href="<?= site_url('contact?type=consultation') ?>" class="btn-gold mt-6 w-full !py-3">Book a Consultation</a>
                    <p class="mt-4 text-center text-xs text-ink-400">Team member? <a href="<?= site_url('dashboard?login') ?>" data-login-button rel="nofollow" class="font-semibold text-gold-300 hover:text-gold-200">Sign in</a> to post, like and comment.</p>
                <?php endif; ?>
            </div>
        </aside>

        <?php include __DIR__ . '/../components/live-feed/view-post-modal.php'; ?>
    </div>
</section>
