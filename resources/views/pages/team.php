<?php
// /resources/views/pages/team.php
//
// Our Team: the lead consultant's profile, then additional consultants and
// partners from Src\Content\Team::members() (an "announced soon" panel
// until there are any).

declare(strict_types=1);

use Src\Content\Icons;
use Src\Content\Site;
use Src\Content\Team;

$lead = Team::lead();
$members = Team::members();

$hero = [
    'eyebrow' => 'Our team',
    'title' => 'The right expertise, <em>brought together.</em>',
    'lead' => 'Our lead consultant works alongside a growing network of consultants and professional partners from different disciplines — so every client gets the expertise their situation calls for.',
    'crumbs' => [['Our Team', null]],
];
include __DIR__ . '/../components/site/page-hero.php';
?>

<!-- Lead consultant -->
<section id="<?= e($lead['slug']) ?>" class="scroll-mt-24 bg-ivory py-24 lg:py-32">
    <div class="site-container grid gap-14 lg:grid-cols-12 lg:gap-20">
        <div class="lg:col-span-5">
            <div class="lg:sticky lg:top-32">
                <div class="mx-auto max-w-sm lg:max-w-none" data-reveal>
                    <?php $person = $lead; include __DIR__ . '/../components/site/portrait.php'; ?>
                </div>
                <div class="mt-8 rounded-2xl border border-ink-900/[0.07] bg-white p-7 shadow-sm" data-reveal>
                    <p class="text-xs font-semibold uppercase tracking-brand text-gold-700">Work with Sheena</p>
                    <div class="mt-5 space-y-3 text-sm">
                        <a href="tel:<?= Site::PHONE_E164 ?>" class="flex items-center gap-3 text-ink-800 hover:text-gold-800"><?= Icons::svg('phone', 'h-4 w-4 text-gold-700') ?><?= e(Site::PHONE) ?></a>
                        <a href="mailto:<?= e(Site::EMAIL) ?>" class="flex items-center gap-3 break-all text-ink-800 hover:text-gold-800"><?= Icons::svg('mail', 'h-4 w-4 flex-shrink-0 text-gold-700') ?><?= e(Site::EMAIL) ?></a>
                    </div>
                    <a data-partial href="<?= site_url('contact?type=consultation') ?>" class="btn-ink mt-6 w-full"><?= Icons::svg('calendar', 'h-4 w-4') ?> Book a Consultation</a>
                </div>
            </div>
        </div>

        <div class="lg:col-span-7">
            <p class="eyebrow" data-reveal><?= e($lead['role']) ?></p>
            <h2 class="display mt-5 text-5xl sm:text-6xl" data-reveal><?= e($lead['name']) ?></h2>
            <p class="mt-3 text-sm font-semibold uppercase tracking-[0.2em] text-gold-700" data-reveal><?= e($lead['credentials']) ?> · <?= e($lead['specialization']) ?></p>
            <p class="mt-8 font-serif text-3xl italic leading-snug text-ink-800" data-reveal><?= e($lead['headline']) ?></p>
            <div class="hairline my-10"></div>
            <div class="space-y-6 text-lg leading-relaxed text-ink-600" data-reveal>
                <?php foreach ($lead['bio'] as $p): ?><p><?= e($p) ?></p><?php endforeach; ?>
            </div>

            <h3 class="mt-14 text-xs font-semibold uppercase tracking-brand text-gold-700" data-reveal>Areas of expertise</h3>
            <ul class="mt-6 grid gap-3 sm:grid-cols-2" data-reveal>
                <?php foreach ($lead['expertise'] as $x): ?>
                    <li class="flex items-center gap-3 rounded-xl border border-ink-900/[0.06] bg-white px-5 py-4 text-ink-800"><?= Icons::svg('check', 'h-4 w-4 flex-shrink-0 text-gold-600') ?><?= e($x) ?></li>
                <?php endforeach; ?>
            </ul>

            <?php if ($lead['links']): ?>
                <ul class="mt-8 flex flex-wrap gap-3">
                    <?php foreach ($lead['links'] as $label => $href): ?>
                        <li><a href="<?= e($href) ?>" target="_blank" rel="noopener" class="btn-outline !px-5 !py-2.5"><?= e($label) ?></a></li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- Consultants & partners -->
<section id="network" class="scroll-mt-24 bg-white py-24 lg:py-32">
    <div class="site-container">
        <div class="mx-auto max-w-2xl text-center">
            <p class="eyebrow justify-center" data-reveal>Consultants & professional partners</p>
            <h2 class="display mt-5 text-4xl sm:text-5xl" data-reveal>A multidisciplinary <em>network.</em></h2>
            <p class="lead mt-6" data-reveal>We collaborate with consultants and partners across business, healthcare, disability management, funding, community development and related fields.</p>
        </div>

        <?php if ($members): ?>
            <div class="mt-16 grid gap-8 sm:grid-cols-2 lg:grid-cols-3">
                <?php foreach ($members as $i => $m): ?>
                    <article id="<?= e($m['slug']) ?>" class="card-lux scroll-mt-24 !p-0 overflow-hidden" data-reveal style="--reveal-delay:<?= ($i % 3) * 90 ?>ms">
                        <?php $person = $m; include __DIR__ . '/../components/site/portrait.php'; ?>
                        <div class="p-7">
                            <h3 class="font-serif text-2xl font-medium text-ink-950"><?= e($m['name']) ?><?php if (!empty($m['credentials'])): ?><span class="ml-2 font-sans text-xs font-semibold tracking-widest text-gold-700"><?= e($m['credentials']) ?></span><?php endif; ?></h3>
                            <p class="mt-1 text-sm font-semibold text-gold-800"><?= e($m['role'] ?? '') ?><?= !empty($m['specialization']) ? ' · ' . e($m['specialization']) : '' ?></p>
                            <?php foreach ($m['bio'] ?? [] as $p): ?><p class="mt-4 text-sm leading-relaxed text-ink-600"><?= e($p) ?></p><?php endforeach; ?>
                            <?php if (!empty($m['links'])): ?>
                                <ul class="mt-5 flex flex-wrap gap-4 text-sm">
                                    <?php foreach ($m['links'] as $label => $href): ?><li><a href="<?= e($href) ?>" target="_blank" rel="noopener" class="link-arrow"><?= e($label) ?></a></li><?php endforeach; ?>
                                </ul>
                            <?php endif; ?>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="mx-auto mt-16 max-w-3xl rounded-[1.75rem] border border-dashed border-gold-500/40 bg-ivory px-8 py-14 text-center" data-reveal>
                <span class="mx-auto flex h-14 w-14 items-center justify-center rounded-full border border-gold-500/40 text-gold-700"><?= Icons::svg('community') ?></span>
                <h3 class="display mt-6 text-3xl">Additional consultants and partners <em>announced soon.</em></h3>
                <p class="mx-auto mt-4 max-w-xl text-ink-600">Profiles of our consultants and professional partners will be added here as they become available.</p>
                <a data-partial href="<?= site_url('contact?type=partnership') ?>" class="btn-outline mt-8">Interested in partnering with us?</a>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php $cta = ['title' => 'Let’s talk about what <em>you need.</em>', 'secondary' => ['Partner With Us', 'contact?type=partnership']]; include __DIR__ . '/../components/site/cta-band.php'; ?>
