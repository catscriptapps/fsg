<?php
// /resources/views/pages/services.php
//
// Services overview: every service area (Src\Content\Services) with a
// summary, what's included and a link to its own page.

declare(strict_types=1);

use Src\Content\Icons;
use Src\Content\Services;

$services = Services::all();
$hero = [
    'eyebrow' => 'Our services',
    'title' => 'Consulting built around <em>your goals.</em>',
    'lead' => 'From funding strategy and business planning to healthcare, disability case management and community projects — multidisciplinary expertise, coordinated around what you need.',
    'crumbs' => [['Services', null]],
    'actions' => '<a data-partial href="' . site_url('contact?type=consultation') . '" class="btn-gold">Request Consulting Services</a>'
        . '<a href="#all-services" class="btn-outline-light">Browse the services</a>',
];
include __DIR__ . '/../components/site/page-hero.php';
?>

<section id="all-services" class="scroll-mt-24 bg-ivory py-24 lg:py-32">
    <div class="site-container space-y-6">
        <?php foreach ($services as $i => $s): ?>
            <article class="card-lux is-link grid gap-8 !p-8 lg:grid-cols-12 lg:gap-12 lg:!p-12" data-reveal>
                <div class="lg:col-span-5">
                    <div class="flex items-center gap-4">
                        <span class="icon-tile"><?= Icons::svg($s['icon']) ?></span>
                        <span class="font-serif text-2xl text-gold-600"><?= str_pad((string) ($i + 1), 2, '0', STR_PAD_LEFT) ?></span>
                    </div>
                    <h2 class="display mt-6 text-3xl sm:text-4xl"><a data-partial href="<?= site_url('services/' . $s['slug']) ?>" class="hover:text-gold-800"><?= e($s['title']) ?></a></h2>
                    <p class="mt-4 leading-relaxed text-ink-600"><?= e($s['short']) ?></p>
                    <div class="mt-8 flex flex-wrap gap-3">
                        <a data-partial href="<?= site_url('services/' . $s['slug']) ?>" class="btn-ink !px-6 !py-3">Learn more <span class="sr-only">about <?= e($s['title']) ?></span> <?= Icons::svg('arrow', 'h-4 w-4') ?></a>
                        <a data-partial href="<?= site_url('contact?service=' . $s['slug']) ?>" class="btn-outline !px-6 !py-3"><?= e($s['cta']) ?></a>
                    </div>
                </div>
                <div class="lg:col-span-7">
                    <p class="text-xs font-semibold uppercase tracking-brand text-gold-700">What’s included</p>
                    <ul class="mt-5 grid gap-x-8 gap-y-3 sm:grid-cols-2">
                        <?php foreach (array_slice($s['offerings'], 0, 8) as $o): ?>
                            <li class="flex gap-3 text-sm leading-relaxed text-ink-700"><?= Icons::svg('check', 'mt-0.5 h-4 w-4 flex-shrink-0 text-gold-600') ?><?= e($o) ?></li>
                        <?php endforeach; ?>
                    </ul>
                    <?php if (count($s['offerings']) > 8): ?>
                        <p class="mt-4 text-sm text-ink-500">+ <?= count($s['offerings']) - 8 ?> more on the <a data-partial href="<?= site_url('services/' . $s['slug']) ?>" class="font-semibold text-gold-800 underline decoration-gold-500/40 underline-offset-4">service page</a></p>
                    <?php endif; ?>
                </div>
            </article>
        <?php endforeach; ?>
    </div>
</section>

<?php $cta = ['title' => 'Not sure which service <em>you need?</em>', 'text' => 'Many challenges span more than one area. Tell us what you’re working on and we’ll help you find the right path — bringing in partner consultants where needed.', 'primary' => ['Make a General Inquiry', 'contact?service=general&type=inquiry'], 'secondary' => ['Book a Consultation', 'contact?type=consultation']]; include __DIR__ . '/../components/site/cta-band.php'; ?>
