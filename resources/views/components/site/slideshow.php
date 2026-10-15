<?php
// /resources/views/components/site/slideshow.php
//
// Home page showcase: a crossfading, slowly-zooming photo panel beside a
// dark caption panel, with prev/next, pause/play and gold progress tabs.
// Slides come from Src\Content\Photos::slides() — the photo library, in the
// order and with the captions set in the admin (Site Photos). site.js (initSlideshows)
// drives it: each tab's fill animation sets the pace, and its end advances
// to the next slide. Works as a static first slide without JS.

use Src\Content\Icons;
use Src\Content\Photos;

$slides = Photos::slides();
if (!$slides) {
    return;
}
$count = count($slides);
?>
<section class="bg-ivory pb-24 lg:pb-32" aria-labelledby="showcase-title">
    <div class="site-container">
        <h2 id="showcase-title" class="sr-only">How we help</h2>
        <div data-slideshow class="slideshow relative grid overflow-hidden rounded-[2rem] bg-ink-950 shadow-2xl shadow-ink-950/25 lg:grid-cols-12"
            role="region" aria-roledescription="carousel" aria-label="How we help" style="--slide-ms: 7000ms">

            <!-- Photos -->
            <div class="relative aspect-[4/3] overflow-hidden sm:aspect-[16/10] lg:col-span-7 lg:aspect-auto lg:min-h-[36rem]">
                <?php foreach ($slides as $i => $s): ?>
                    <img src="<?= e($s['src']) ?>" alt="<?= e($s['alt']) ?>" data-slide-img
                        class="slide-img absolute inset-0 h-full w-full object-cover <?= $i === 0 ? 'is-active' : '' ?>"
                        <?= $i === 0 ? 'fetchpriority="high"' : 'loading="lazy"' ?> width="1400" height="934">
                <?php endforeach; ?>
                <div aria-hidden="true" class="pointer-events-none absolute inset-0 bg-gradient-to-t from-ink-950/50 via-transparent to-transparent lg:bg-gradient-to-r lg:from-transparent lg:from-80% lg:via-transparent lg:to-ink-950/50"></div>
                <div aria-hidden="true" class="pointer-events-none absolute inset-4 rounded-[1.4rem] border border-gold-300/20 lg:inset-5"></div>
                <!-- Counter -->
                <p class="absolute left-7 top-7 rounded-full bg-ink-950/60 px-4 py-1.5 font-serif text-lg text-ivory/90 ring-1 ring-gold-300/20 backdrop-blur-md lg:left-9 lg:top-9" aria-hidden="true">
                    <span data-slide-num class="text-2xl text-gold-300">01</span><span class="mx-2 text-gold-500/60">/</span><?= str_pad((string) $count, 2, '0', STR_PAD_LEFT) ?>
                </p>
            </div>

            <!-- Captions -->
            <div class="ink-surface relative flex flex-col justify-between gap-10 p-8 sm:p-12 lg:col-span-5 lg:p-14">
                <div aria-hidden="true" class="pointer-events-none absolute -bottom-10 right-0 w-72 opacity-[0.08]"><?= Icons::waveform('w-full', false) ?></div>

                <div class="relative grid">
                    <?php foreach ($slides as $i => $s): ?>
                        <div data-slide class="slide-caption [grid-area:1/1] <?= $i === 0 ? 'is-active' : '' ?>"
                            role="group" aria-roledescription="slide" aria-label="<?= $i + 1 ?> of <?= $count ?>" <?= $i === 0 ? '' : 'aria-hidden="true" inert' ?>>
                            <p class="eyebrow eyebrow-light"><?= e($s['eyebrow']) ?></p>
                            <h3 class="display display-light mt-6 text-4xl leading-[1.08] sm:text-5xl"><?= $s['title'] ?></h3>
                            <?php if ($s['text'] !== ''): ?><p class="mt-6 text-lg leading-relaxed text-ink-200"><?= e($s['text']) ?></p><?php endif; ?>
                            <?php if ($s['cta'] !== '' && $s['link'] !== ''): ?><a data-partial href="<?= site_url($s['link']) ?>" class="link-arrow mt-8 !text-gold-300 hover:!text-gold-200"><?= e($s['cta']) ?> <?= Icons::svg('arrow') ?></a><?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="relative">
                    <!-- Progress tabs (also jump to a slide) -->
                    <div class="grid gap-2" style="grid-template-columns: repeat(<?= $count ?>, minmax(0, 1fr))" role="tablist" aria-label="Choose a slide">
                        <?php foreach ($slides as $i => $s): ?>
                            <button type="button" data-slide-tab role="tab" aria-selected="<?= $i === 0 ? 'true' : 'false' ?>" aria-label="Slide <?= $i + 1 ?>: <?= e($s['eyebrow']) ?>"
                                class="group pt-3 text-left focus:outline-none">
                                <span class="relative block h-0.5 overflow-hidden rounded-full bg-white/15">
                                    <span data-slide-fill class="slide-fill absolute inset-y-0 left-0 w-0 bg-gradient-to-r from-gold-300 to-gold-500"></span>
                                </span>
                                <span class="mt-3 hidden truncate text-[0.66rem] font-semibold uppercase tracking-[0.06em] text-ink-400 transition-colors group-hover:text-gold-200 group-focus-visible:text-gold-200 group-aria-selected:text-gold-300 sm:block"><?= e($s['eyebrow']) ?></span>
                            </button>
                        <?php endforeach; ?>
                    </div>
                    <div class="mt-8 flex items-center gap-3">
                        <button type="button" data-slide-prev class="inline-flex h-12 w-12 items-center justify-center rounded-full border border-gold-300/30 text-gold-200 transition hover:border-gold-300 hover:bg-gold-300/10" aria-label="Previous slide">
                            <?= Icons::svg('arrow', 'h-4 w-4 rotate-180') ?>
                        </button>
                        <button type="button" data-slide-next class="inline-flex h-12 w-12 items-center justify-center rounded-full border border-gold-300/30 text-gold-200 transition hover:border-gold-300 hover:bg-gold-300/10" aria-label="Next slide">
                            <?= Icons::svg('arrow', 'h-4 w-4') ?>
                        </button>
                        <button type="button" data-slide-toggle class="ml-auto inline-flex h-12 items-center gap-2 rounded-full px-4 text-xs font-semibold uppercase tracking-widest text-ink-300 transition hover:text-gold-200" aria-label="Pause slideshow">
                            <svg data-icon-pause class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><rect x="6" y="5" width="4" height="14" rx="1"/><rect x="14" y="5" width="4" height="14" rx="1"/></svg>
                            <svg data-icon-play class="hidden h-4 w-4" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M8 5.5v13a1 1 0 001.5.86l10.5-6.5a1 1 0 000-1.72L9.5 4.64A1 1 0 008 5.5z"/></svg>
                            <span data-toggle-label>Pause</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
