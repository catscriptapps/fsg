<?php
// /resources/views/pages/services/detail.php
//
// One service area: /services/{slug}. The slug arrives in
// $GLOBALS['encodedId'] (resolvePageRoute in server/helpers.php); unknown
// slugs 404 before the layout renders (resolveDynamicPageMeta).

declare(strict_types=1);

use Src\Content\Icons;
use Src\Content\Services;

$s = Services::find((string) ($GLOBALS['encodedId'] ?? ''));
$others = array_values(array_filter(Services::all(), fn($o) => $o['slug'] !== $s['slug']));

$hero = [
    'eyebrow' => 'Services',
    'title' => e($s['title']),
    'lead' => $s['short'],
    'photo' => 'service.' . $s['slug'],
    'crumbs' => [['Services', 'services'], [$s['title'], null]],
    'actions' => '<a data-partial href="' . site_url('contact?type=consultation&service=' . $s['slug']) . '" class="btn-gold">' . e($s['cta']) . ' ' . Icons::svg('arrow', 'h-4 w-4') . '</a>'
        . '<a href="#included" class="btn-outline-light">What’s included</a>',
];
include __DIR__ . '/../../components/site/page-hero.php';
?>

<section class="bg-ivory py-24 lg:py-28">
    <div class="site-container grid gap-14 lg:grid-cols-12 lg:gap-20">
        <div class="space-y-6 lg:col-span-7" data-reveal>
            <span class="icon-tile h-14 w-14"><?= Icons::svg($s['icon'], 'h-7 w-7') ?></span>
            <?php foreach ($s['intro'] as $i => $p): ?>
                <p class="<?= $i === 0 ? 'lead !text-xl text-ink-800' : 'leading-relaxed text-ink-600' ?>"><?= e($p) ?></p>
            <?php endforeach; ?>
        </div>
        <aside class="lg:col-span-5" data-reveal>
            <div class="rounded-2xl border border-ink-900/[0.07] bg-white p-8 shadow-sm">
                <p class="text-xs font-semibold uppercase tracking-brand text-gold-700">Who it’s for</p>
                <ul class="mt-5 space-y-3">
                    <?php foreach ($s['audience'] as $a): ?>
                        <li class="flex gap-3 text-ink-800"><?= Icons::svg('check', 'mt-1 h-4 w-4 flex-shrink-0 text-gold-600') ?><?= e($a) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </aside>
    </div>
</section>

<section id="included" class="scroll-mt-24 bg-white py-24 lg:py-28">
    <div class="site-container">
        <p class="eyebrow" data-reveal>What’s included</p>
        <h2 class="display mt-5 max-w-2xl text-4xl sm:text-5xl" data-reveal>How we can <em>help.</em></h2>
        <ul class="mt-12 grid gap-3 sm:grid-cols-2 lg:grid-cols-3" data-reveal>
            <?php foreach ($s['offerings'] as $o): ?>
                <li class="flex gap-4 rounded-xl border border-ink-900/[0.07] bg-ivory px-6 py-5 text-ink-800"><?= Icons::svg('check', 'mt-0.5 h-5 w-5 flex-shrink-0 text-gold-600') ?><?= e($o) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
</section>

<section class="bg-ivory-deep py-24 lg:py-28">
    <div class="site-container grid gap-6 lg:grid-cols-2">
        <div class="card-lux !p-10" data-reveal>
            <p class="eyebrow">Typical deliverables</p>
            <h2 class="display mt-4 text-3xl">What you <em>receive.</em></h2>
            <ul class="mt-8 space-y-5">
                <?php foreach ($s['deliverables'] as $i => $d): ?>
                    <li class="flex gap-5">
                        <span class="font-serif text-2xl leading-none text-gold-600"><?= str_pad((string) ($i + 1), 2, '0', STR_PAD_LEFT) ?></span>
                        <span class="leading-relaxed text-ink-700"><?= e($d) ?></span>
                    </li>
                <?php endforeach; ?>
            </ul>
            <p class="mt-8 text-xs text-ink-500">Every engagement is scoped to your needs — your deliverables are agreed with you at the outset.</p>
        </div>
        <div class="ink-surface relative overflow-hidden rounded-2xl p-10" data-reveal style="--reveal-delay:120ms">
            <p class="eyebrow eyebrow-light">Client benefits</p>
            <h2 class="display display-light mt-4 text-3xl">Why it <em>matters.</em></h2>
            <ul class="mt-8 space-y-5">
                <?php foreach ($s['benefits'] as $b): ?>
                    <li class="flex gap-4 leading-relaxed text-ink-200"><span class="mt-2.5 h-px w-6 flex-shrink-0 bg-gold-400"></span><?= e($b) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>

    <?php if (!empty($s['note'])): ?>
        <div class="site-container mt-6">
            <div class="flex gap-4 rounded-2xl border border-gold-500/30 bg-gold-50 px-7 py-5 text-sm leading-relaxed text-ink-700" data-reveal role="note">
                <?= Icons::svg('info', 'mt-0.5 h-5 w-5 flex-shrink-0 text-gold-700') ?>
                <p><span class="font-semibold text-ink-900">Please note:</span> <?= e($s['note']) ?></p>
            </div>
        </div>
    <?php endif; ?>
</section>

<?php $cta = [
    'title' => 'Let’s talk about <em>your needs.</em>',
    'text' => 'Every engagement starts with listening. Tell us where you are and what you want to achieve, and we’ll outline how we can help.',
    'primary' => [$s['cta'], 'contact?type=consultation&service=' . $s['slug']],
    'secondary' => ['Contact Our Team', 'contact?type=inquiry&service=' . $s['slug']],
]; include __DIR__ . '/../../components/site/cta-band.php'; ?>

<section class="bg-ivory pb-24 lg:pb-32">
    <div class="site-container">
        <h2 class="text-xs font-semibold uppercase tracking-brand text-gold-700">Other services</h2>
        <div class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <?php foreach (array_slice($others, 0, 4) as $o): ?>
                <a data-partial href="<?= site_url('services/' . $o['slug']) ?>" class="card-lux group flex items-start gap-4 !p-6">
                    <span class="text-gold-700"><?= Icons::svg($o['icon']) ?></span>
                    <span class="text-sm font-semibold leading-snug text-ink-900 group-hover:text-gold-800"><?= e($o['title']) ?></span>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>
