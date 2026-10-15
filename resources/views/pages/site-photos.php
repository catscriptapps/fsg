<?php
// /resources/views/pages/site-photos.php
//
// Admin: Site Photos — every photo on the public website. Two parts, both
// drawn by resources/js/pages/site-photos-page.js from #site-photos-data:
//
//   1. The photo library = the home slideshow, in order. Add photos (shared
//      upload modal), reorder with each tile's ⇄ button (utils/pic-reorder.js
//      — pick one up, then click the ⇄ on the photo whose place it takes),
//      select many / select all → delete, delete all, and a full-screen
//      editor per photo (slide caption, description, slideshow on/off, move
//      first / earlier / later / last, download, delete).
//   2. Photos around the website — the photo in each fixed spot
//      (Src\Content\Photos::slots()), chosen from the library or uploaded.
//
// Deleting a photo deletes its file on the server too. Writes go to
// server/api/site-photos.php (Src\Controller\SitePhotosController).

declare(strict_types=1);

use Src\Content\Photos;
use Src\Content\Services;
use Src\Content\Site;
use Src\Controller\SitePhotosController;
use Src\Service\AuthService;

/** @var string $baseUrl */

if (!AuthService::isAdmin()) {
    include __DIR__ . '/access-denied.php';
    return;
}

$ready = Photos::installed();
$data = ['ready' => $ready] + ($ready ? SitePhotosController::state() : ['photos' => [], 'slots' => []]);
// Suggestions for a slide's button link (any page on the site works)
$data['links'] = array_merge(
    array_map(fn($p, $m) => ['path' => $p === 'home' ? '' : $p, 'label' => $m[0]], array_keys(Site::pages()), Site::pages()),
    array_map(fn($s) => ['path' => 'services/' . $s['slug'], 'label' => $s['title']], Services::all()),
    [['path' => 'contact?type=consultation', 'label' => 'Book a consultation'], ['path' => 'contact?type=partnership', 'label' => 'Partner with us'], ['path' => 'about#approach', 'label' => 'Our approach (About)']]
);
$breadcrumbs = [['label' => 'Admin', 'href' => $baseUrl . 'admin'], ['label' => 'Site Photos']];
$btnGhost = 'inline-flex flex-shrink-0 items-center gap-2 rounded-xl border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 px-4 py-2.5 text-sm font-semibold text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors';
?>
<div id="site-photos-page" class="space-y-6">

    <?php include __DIR__ . '/../components/breadcrumbs.php'; ?>

    <!-- Hero: live preview of the slideshow + numbers (drawn by the page script) -->
    <section class="relative overflow-hidden rounded-3xl bg-secondary-950 text-white shadow-xl shadow-secondary-900/10">
        <div id="sp-hero-slides" class="absolute inset-0" aria-hidden="true"></div>
        <div aria-hidden="true" class="absolute inset-0 bg-gradient-to-r from-secondary-950/95 via-secondary-950/80 to-secondary-950/40"></div>
        <div id="sp-hero" class="relative p-6 sm:px-10 sm:py-8"></div>
    </section>

    <?php if (!$ready): ?>
        <div class="rounded-2xl border border-amber-200 dark:border-amber-900/60 bg-amber-50 dark:bg-amber-950/30 px-5 py-4 text-sm text-amber-800 dark:text-amber-200">
            <strong>One step first:</strong> Site Photos needs a database update. Run the DB update from the header (it only adds what’s missing), then reload this page. Until then the website keeps showing its built-in photos.
        </div>
    <?php endif; ?>

    <!-- Library header -->
    <div class="flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
        <div class="min-w-0 flex-1">
            <h2 class="text-xl font-bold text-gray-900 dark:text-white">Photo library &amp; home slideshow</h2>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">The order here is the slideshow’s order. To move a photo, click its ⇄ button, then the ⇄ on the photo whose place it should take. Click a photo to edit its slide caption.</p>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <button type="button" id="sp-select-btn" class="<?= $btnGhost ?> hidden">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                Select
            </button>
            <button type="button" id="sp-delete-all-btn" class="<?= $btnGhost ?> hidden !text-red-600 hover:!bg-red-50 dark:hover:!bg-red-950/40">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                Delete all
            </button>
            <button type="button" id="sp-upload-btn" <?= $ready ? '' : 'disabled' ?>
                class="inline-flex flex-shrink-0 items-center gap-2 rounded-xl bg-primary-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition-colors hover:bg-primary-700 disabled:cursor-not-allowed disabled:opacity-50">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" /></svg>
                Add photos
            </button>
        </div>
    </div>

    <!-- Library grid -->
    <section class="rounded-2xl border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 shadow-sm">
        <div id="sp-grid" class="min-h-[12rem] p-3 sm:p-5" aria-live="polite"></div>
    </section>

    <!-- Spots around the website -->
    <section class="rounded-2xl border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 p-5 shadow-sm sm:p-6" aria-labelledby="sp-slots-heading">
        <h2 id="sp-slots-heading" class="text-xl font-bold text-gray-900 dark:text-white">Photos around the website</h2>
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">The photo in each spot on the public pages — choose one from the library, or upload a new one straight into the spot. An empty spot simply shows no photo.</p>
        <div id="sp-slots" class="mt-6 space-y-7"></div>
    </section>

    <!-- Selection bar (select mode) -->
    <div id="sp-selectbar" class="fixed bottom-6 left-1/2 z-40 hidden -translate-x-1/2 flex items-center gap-2 rounded-2xl bg-secondary-900 px-3 py-2 text-white shadow-2xl shadow-black/30 ring-1 ring-white/10">
        <span class="whitespace-nowrap px-2 text-sm font-semibold" data-sel-count>0 selected</span>
        <button type="button" data-sel="all" class="rounded-lg px-3 py-1.5 text-xs font-semibold transition-colors hover:bg-white/10">Select all</button>
        <button type="button" data-sel="delete" class="rounded-lg bg-red-500/20 px-3 py-1.5 text-xs font-semibold text-red-200 transition-colors hover:bg-red-500/30 disabled:opacity-40" disabled>Delete</button>
        <button type="button" data-sel="cancel" class="rounded-lg px-3 py-1.5 text-xs font-semibold transition-colors hover:bg-white/10">Done</button>
    </div>

    <script type="application/json" id="site-photos-data"><?= json_encode($data, JSON_HEX_TAG | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?></script>
</div>
