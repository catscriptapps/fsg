<?php
// /resources/views/components/site/page-hero.php
//
// Dark hero for inner pages. Set $hero before including:
//   eyebrow, title (may contain <em>), lead, crumbs [[label, path|null]...], actions (HTML),
//   photo (a Src\Content\Photos role — shown behind the hero, tinted with ink)

use Src\Content\Icons;
use Src\Content\Photos;

$crumbs = $hero['crumbs'] ?? [];
$heroPhoto = !empty($hero['photo']) ? Photos::get($hero['photo']) : null;
?>
<section class="ink-surface relative overflow-hidden pb-20 pt-36 lg:pb-28 lg:pt-44">
    <?php if ($heroPhoto): ?>
        <div aria-hidden="true" class="absolute inset-0">
            <img src="<?= e($heroPhoto['src']) ?>" alt="" class="hero-photo h-full w-full object-cover object-center" fetchpriority="high">
            <div class="absolute inset-0 bg-gradient-to-r from-ink-950 via-ink-950/85 to-ink-950/45"></div>
            <div class="absolute inset-0 bg-gradient-to-t from-ink-950/80 via-transparent to-ink-950/60"></div>
        </div>
    <?php else: ?>
        <div aria-hidden="true" class="pointer-events-none absolute -right-10 top-1/2 hidden w-[30rem] -translate-y-1/2 opacity-30 lg:block"><?= Icons::waveform('w-full') ?></div>
    <?php endif; ?>
    <div aria-hidden="true" class="pointer-events-none absolute inset-x-0 bottom-0 h-px bg-gradient-to-r from-transparent via-gold-500/40 to-transparent"></div>

    <div class="site-container relative">
        <?php if ($crumbs): ?>
            <nav aria-label="Breadcrumb" class="mb-8">
                <ol class="flex flex-wrap items-center gap-2 text-xs font-medium uppercase tracking-widest text-ink-400">
                    <li><a data-partial href="<?= site_url() ?>" class="hover:text-gold-300">Home</a></li>
                    <?php foreach ($crumbs as [$label, $p]): ?>
                        <li aria-hidden="true" class="text-gold-700">/</li>
                        <li><?php if ($p): ?><a data-partial href="<?= site_url($p) ?>" class="hover:text-gold-300"><?= e($label) ?></a><?php else: ?><span class="text-gold-300" aria-current="page"><?= e($label) ?></span><?php endif; ?></li>
                    <?php endforeach; ?>
                </ol>
            </nav>
        <?php endif; ?>

        <div class="max-w-3xl">
            <?php if (!empty($hero['eyebrow'])): ?><p class="eyebrow eyebrow-light" data-reveal><?= e($hero['eyebrow']) ?></p><?php endif; ?>
            <h1 class="display display-light mt-5 text-5xl leading-[1.05] sm:text-6xl lg:text-7xl" data-reveal style="--reveal-delay:80ms"><?= $hero['title'] ?></h1>
            <?php if (!empty($hero['lead'])): ?>
                <p class="mt-7 max-w-2xl text-lg leading-relaxed text-ink-200" data-reveal style="--reveal-delay:160ms"><?= e($hero['lead']) ?></p>
            <?php endif; ?>
            <?php if (!empty($hero['actions'])): ?>
                <div class="mt-10 flex flex-wrap gap-4" data-reveal style="--reveal-delay:240ms"><?= $hero['actions'] ?></div>
            <?php endif; ?>
        </div>
    </div>
</section>
