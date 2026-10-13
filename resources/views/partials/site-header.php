<?php
// /resources/views/partials/site-header.php
//
// Public site header: fixed, transparent over each page's dark hero and
// solid once scrolled (site.js toggles .is-solid). Dropdowns and the mobile
// drawer are Alpine; every link is a real <a href> so it all works without JS.

use Src\Content\Icons;
use Src\Content\Site;

$isActive = function (array $item) use ($sitePath): bool {
    if ($sitePath === $item['path'] || str_starts_with($sitePath, $item['path'] . '/')) {
        return true;
    }
    foreach ($item['children'] ?? [] as $c) {
        if ($sitePath === $c['path']) {
            return true;
        }
    }
    return false;
};
$nav = Site::nav();

// Hooks for site.js setActiveNav(): pages load as SPA partials, so the
// header's highlight is re-applied in the browser after each navigation.
// data-nav-match = active on these paths or below them; data-nav-exact = on
// exactly this path. data-on / data-off = the classes for each state.
$navHook = function (array $item, string $on, string $off): string {
    $paths = array_merge([$item['path']], array_column($item['children'] ?? [], 'path'));
    return 'data-nav-match="' . e(implode(' ', array_unique($paths))) . '" data-on="' . $on . '" data-off="' . $off . '"';
};
$navExact = fn(string $path, string $on, string $off): string => 'data-nav-exact="' . e($path) . '" data-on="' . $on . '" data-off="' . $off . '"';
?>
<header data-site-header class="site-header fixed inset-x-0 top-0 z-50 border-b border-transparent" x-data="{ menu: false }" @keydown.escape.window="menu = false" @spa-navigation.window="menu = false" x-effect="document.documentElement.classList.toggle('overflow-hidden', menu); if (menu) $nextTick(() => $refs.closeMenu.focus())">
    <div class="site-container flex h-20 items-center justify-between gap-6 lg:h-24">
        <a href="<?= site_url() ?>" class="flex-shrink-0" aria-label="<?= e(Site::BRAND) ?> — home">
            <img src="<?= $assetBase ?>images/logo/logo-gold.webp" alt="<?= e(Site::BRAND) ?>" width="176" height="54" class="h-11 w-auto lg:h-[3.25rem]">
        </a>

        <!-- Desktop navigation -->
        <nav aria-label="Main" class="hidden xl:block">
            <ul class="flex items-center gap-0.5 2xl:gap-1">
                <?php foreach ($nav as $item): $active = $isActive($item); ?>
                    <?php if (!empty($item['children'])): $isServices = $item['path'] === 'services'; ?>
                        <li class="relative" x-data="{ open: false }" @mouseenter="open = true" @mouseleave="open = false" @focusout="if (!$el.contains($event.relatedTarget)) open = false">
                            <a data-partial href="<?= site_url($item['path']) ?>" <?= $navHook($item, 'text-gold-300', 'text-ink-100') ?> class="inline-flex items-center gap-1.5 whitespace-nowrap rounded-full px-3 py-2 text-sm font-medium transition-colors hover:text-gold-300 <?= $active ? 'text-gold-300' : 'text-ink-100' ?>" <?= $active ? 'aria-current="page"' : '' ?>
                                @click="open = false" :aria-expanded="open.toString()" @keydown.arrow-down.prevent="open = true; $nextTick(() => $refs.panel.querySelector('a').focus())">
                                <?= e($item['label']) ?>
                                <?= Icons::svg('chevron', 'h-3.5 w-3.5 opacity-70 transition-transform duration-300') ?>
                            </a>
                            <div x-ref="panel" x-show="open" x-cloak
                                x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-1" x-transition:enter-end="opacity-100 translate-y-0"
                                x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                                class="absolute left-1/2 top-full -translate-x-1/2 pt-3 <?= $isServices ? 'w-[40rem]' : 'w-80' ?>">
                                <div class="overflow-hidden rounded-2xl border border-gold-300/15 bg-ink-950/95 p-3 shadow-2xl shadow-black/40 backdrop-blur-xl">
                                    <ul class="<?= $isServices ? 'grid grid-cols-2 gap-1' : 'space-y-1' ?>">
                                        <?php foreach ($item['children'] as $child): ?>
                                            <li>
                                                <a data-partial href="<?= site_url($child['path']) ?>" @click="open = false" <?= $navExact($child['path'], 'bg-white/[0.05]', '') ?> class="block rounded-xl px-4 py-3 transition-colors hover:bg-white/[0.06] focus:bg-white/[0.06] focus:outline-none <?= $sitePath === $child['path'] ? 'bg-white/[0.05]' : '' ?>" <?= $sitePath === $child['path'] ? 'aria-current="page"' : '' ?>>
                                                    <span <?= $navExact($child['path'], 'text-gold-300', 'text-ivory') ?> class="block text-sm font-semibold <?= $sitePath === $child['path'] ? 'text-gold-300' : 'text-ivory' ?>"><?= e($child['label']) ?></span>
                                                    <?php if (!empty($child['note'])): ?><span class="mt-0.5 block text-xs text-ink-300"><?= e($child['note']) ?></span><?php endif; ?>
                                                </a>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                    <?php if ($isServices): ?>
                                        <div class="mt-2 flex items-center justify-between rounded-xl bg-gold-300/[0.07] px-4 py-3">
                                            <span class="text-xs text-ink-200">Not sure which service fits? We’ll help you find the right path.</span>
                                            <a data-partial href="<?= site_url('services') ?>" @click="open = false" class="inline-flex items-center gap-1.5 text-xs font-semibold text-gold-300 hover:text-gold-200">All services <?= Icons::svg('arrow', 'h-3.5 w-3.5') ?></a>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </li>
                    <?php else: ?>
                        <li>
                            <a data-partial href="<?= site_url($item['path']) ?>" <?= $navHook($item, 'text-gold-300', 'text-ink-100') ?> class="inline-flex whitespace-nowrap rounded-full px-3 py-2 text-sm font-medium transition-colors hover:text-gold-300 <?= $active ? 'text-gold-300' : 'text-ink-100' ?>" <?= $active ? 'aria-current="page"' : '' ?>><?= e($item['label']) ?></a>
                        </li>
                    <?php endif; ?>
                <?php endforeach; ?>
            </ul>
        </nav>

        <div class="flex items-center gap-3">
            <?php if ($isLoggedIn ?? false): ?>
                <a data-partial href="<?= site_url('dashboard') ?>" title="Dashboard" class="hidden h-11 items-center gap-2 whitespace-nowrap rounded-full border border-gold-300/30 px-3.5 text-sm font-medium text-ink-100 transition-colors hover:border-gold-300 hover:text-gold-300 lg:inline-flex"><?= Icons::svg('user', 'h-4 w-4') ?><span class="sr-only 2xl:not-sr-only 2xl:whitespace-nowrap">Dashboard</span></a>
            <?php else: ?>
                <!-- Opens the sign-in modal in place (LoginModal, via site.js); the href is the no-JS fallback -->
                <a href="<?= site_url('dashboard?login') ?>" data-login-button rel="nofollow" title="Sign in" class="hidden h-11 items-center gap-2 whitespace-nowrap rounded-full border border-gold-300/30 px-3.5 text-sm font-medium text-ink-100 transition-colors hover:border-gold-300 hover:text-gold-300 lg:inline-flex"><?= Icons::svg('user', 'h-4 w-4') ?><span class="sr-only 2xl:not-sr-only 2xl:whitespace-nowrap">Sign in</span></a>
            <?php endif; ?>
            <a data-partial href="<?= site_url('contact?type=consultation') ?>" class="btn-gold hidden whitespace-nowrap !px-6 !py-3 sm:inline-flex">
                <?= Icons::svg('calendar', 'h-4 w-4') ?> Book a Consultation
            </a>
            <button type="button" class="inline-flex h-11 w-11 items-center justify-center rounded-full border border-gold-300/30 text-gold-200 transition hover:border-gold-300 xl:hidden"
                @click="menu = true" aria-label="Open menu" aria-controls="mobile-menu" :aria-expanded="menu.toString()">
                <?= Icons::svg('menu', 'h-5 w-5') ?>
            </button>
        </div>
    </div>

    <!-- Mobile drawer -->
    <div id="mobile-menu" x-show="menu" x-cloak class="fixed inset-0 z-50 xl:hidden" role="dialog" aria-modal="true" aria-label="Menu">
        <div x-show="menu" x-transition.opacity.duration.300ms class="absolute inset-0 bg-black/60 backdrop-blur-sm" @click="menu = false"></div>
        <div x-show="menu"
            x-transition:enter="transition ease-out duration-300" x-transition:enter-start="translate-x-full" x-transition:enter-end="translate-x-0"
            x-transition:leave="transition ease-in duration-200" x-transition:leave-start="translate-x-0" x-transition:leave-end="translate-x-full"
            class="ink-surface absolute inset-y-0 right-0 flex w-full max-w-sm flex-col overflow-y-auto border-l border-gold-300/10 shadow-2xl">
            <div class="flex h-20 items-center justify-between px-6">
                <img src="<?= $assetBase ?>images/logo/logo-gold.webp" alt="" class="h-10 w-auto">
                <button type="button" class="inline-flex h-11 w-11 items-center justify-center rounded-full border border-gold-300/30 text-gold-200" @click="menu = false" x-ref="closeMenu" aria-label="Close menu">
                    <?= Icons::svg('close', 'h-5 w-5') ?>
                </button>
            </div>
            <nav aria-label="Mobile" class="flex-1 px-4 pb-6">
                <ul class="divide-y divide-white/[0.06]">
                    <?php foreach ($nav as $item): ?>
                        <li x-data="{ sub: <?= $isActive($item) && !empty($item['children']) ? 'true' : 'false' ?> }">
                            <?php if (!empty($item['children'])): ?>
                                <button type="button" class="flex w-full items-center justify-between px-2 py-4 text-left text-lg font-medium text-ivory" @click="sub = !sub" :aria-expanded="sub.toString()">
                                    <?= e($item['label']) ?>
                                    <span class="transition-transform duration-300" :class="sub && 'rotate-180'"><?= Icons::svg('chevron', 'h-4 w-4 text-gold-300') ?></span>
                                </button>
                                <ul x-show="sub" x-collapse class="space-y-0.5 pb-3 pl-2">
                                    <?php if ($item['path'] === 'services'): ?>
                                        <li><a data-partial href="<?= site_url('services') ?>" class="block rounded-lg px-3 py-2 text-sm font-semibold text-gold-300">All services</a></li>
                                    <?php endif; ?>
                                    <?php foreach ($item['children'] as $child): ?>
                                        <li><a data-partial href="<?= site_url($child['path']) ?>" <?= $navExact($child['path'], 'text-gold-300', 'text-ink-200') ?> class="block rounded-lg px-3 py-2 text-sm hover:text-ivory <?= $sitePath === $child['path'] ? 'text-gold-300' : 'text-ink-200' ?>"><?= e($child['label']) ?></a></li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php else: ?>
                                <a data-partial href="<?= site_url($item['path']) ?>" <?= $navHook($item, 'text-gold-300', 'text-ivory') ?> class="block px-2 py-4 text-lg font-medium <?= $isActive($item) ? 'text-gold-300' : 'text-ivory' ?>"><?= e($item['label']) ?></a>
                            <?php endif; ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </nav>
            <div class="space-y-4 border-t border-white/[0.06] px-6 py-6">
                <a data-partial href="<?= site_url('contact?type=consultation') ?>" class="btn-gold w-full"><?= Icons::svg('calendar', 'h-4 w-4') ?> Book a Consultation</a>
                <?php if ($isLoggedIn ?? false): ?>
                    <a data-partial href="<?= site_url('dashboard') ?>" class="btn-outline-light w-full"><?= Icons::svg('user', 'h-4 w-4') ?> Dashboard</a>
                <?php else: ?>
                    <a href="<?= site_url('dashboard?login') ?>" data-login-button rel="nofollow" @click="menu = false" class="btn-outline-light w-full"><?= Icons::svg('user', 'h-4 w-4') ?> Sign in</a>
                <?php endif; ?>
                <a href="tel:<?= Site::PHONE_E164 ?>" class="flex items-center gap-3 text-sm text-ink-200"><?= Icons::svg('phone', 'h-4 w-4 text-gold-300') ?><?= e(Site::PHONE) ?></a>
                <a href="mailto:<?= e(Site::EMAIL) ?>" class="flex items-center gap-3 break-all text-sm text-ink-200"><?= Icons::svg('mail', 'h-4 w-4 flex-shrink-0 text-gold-300') ?><?= e(Site::EMAIL) ?></a>
            </div>
        </div>
    </div>
</header>
