<?php
// /resources/views/pages/about.php
//
// About Us: overview, what the name means, mission / vision / values and
// the consulting approach.

declare(strict_types=1);

use Src\Content\Company;
use Src\Content\Icons;
use Src\Content\Site;

$hero = [
    'eyebrow' => 'About us',
    'title' => 'We listen first. Then we build the <em>way forward.</em>',
    'lead' => 'A Black-owned, women-led consulting company bringing professionals from different disciplines together to help organizations and individuals solve complex problems and turn ideas into achievable outcomes.',
    'crumbs' => [['About Us', null]],
];
include __DIR__ . '/../components/site/page-hero.php';
?>

<!-- Overview -->
<section class="bg-ivory py-24 lg:py-32">
    <div class="site-container grid gap-14 lg:grid-cols-12 lg:gap-20">
        <div class="lg:col-span-5">
            <p class="eyebrow" data-reveal>Who we are</p>
            <h2 class="display mt-5 text-4xl sm:text-5xl" data-reveal>Strategy, expertise and resources — <em>connected.</em></h2>
            <div class="mt-10 rounded-2xl border-l-2 border-gold-500 bg-white px-7 py-6 shadow-sm" data-reveal>
                <p class="font-serif text-2xl italic leading-snug text-ink-900">“We are not simply providing advice. We work to connect strategy with implementation.”</p>
            </div>
        </div>
        <div class="space-y-6 text-ink-600 lg:col-span-7" data-reveal>
            <p class="lead">At Frequency Strategy Group, we believe every organization has the potential to shine when it has the right strategy, expertise, partnerships and resources.</p>
            <p>We are a multidisciplinary consulting company that helps businesses, nonprofits, healthcare organizations, entrepreneurs and individuals navigate challenges and pursue meaningful opportunities. We bring together strategic planning, clinical and healthcare expertise, community development, funding strategy, disability case management, business development and specialized consulting.</p>
            <p>Our work begins with listening. We take the time to understand where our clients are, where they want to go and what is standing in their way. From there, we develop tailored strategies that connect them with the right resources, expertise, partnerships and funding opportunities.</p>
            <p>Our services span funding and grant development, business strategy, healthcare and community development consulting, disability case management, sports career management, organizational planning and specialized project development. Rather than taking a one-size-fits-all approach, we collaborate with a network of consultants and professional partners to build solutions around each client’s specific needs.</p>
            <p>We are committed to helping clients move beyond identifying problems — to developing practical solutions, building capacity and creating lasting impact.</p>
        </div>
    </div>
</section>

<!-- The name -->
<section id="philosophy" class="scroll-mt-24 bg-white py-24 lg:py-32">
    <div class="site-container">
        <div class="mx-auto max-w-2xl text-center">
            <p class="eyebrow justify-center" data-reveal>Our philosophy</p>
            <h2 class="display mt-5 text-4xl sm:text-5xl" data-reveal>The meaning behind <em>our name.</em></h2>
        </div>
        <div class="mt-16 divide-y divide-ink-900/[0.08] border-y border-ink-900/[0.08]">
            <?php foreach (Company::PHILOSOPHY as $i => [$word, $promise, $text]): ?>
                <div class="grid gap-6 py-12 lg:grid-cols-12 lg:gap-12" data-reveal>
                    <div class="lg:col-span-4">
                        <p class="gold-text-deep font-serif text-6xl font-medium sm:text-7xl"><?= e($word) ?></p>
                    </div>
                    <div class="lg:col-span-8">
                        <h3 class="font-serif text-3xl font-medium text-ink-950"><?= e($promise) ?></h3>
                        <p class="mt-4 text-lg leading-relaxed text-ink-600"><?= e($text) ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <div class="mx-auto mt-16 max-w-3xl text-center" data-reveal>
            <p class="font-serif text-3xl italic leading-snug text-ink-900">“<?= e(Company::PHILOSOPHY_SUMMARY) ?>”</p>
            <p class="mt-8 text-xs font-semibold uppercase tracking-brand text-gold-700"><?= e(implode(' · ', array_map(fn($p) => rtrim($p, '.'), Site::BRAND_STATEMENT))) ?></p>
        </div>
    </div>
</section>

<!-- Mission & vision -->
<section id="mission" class="ink-surface scroll-mt-24 py-24 lg:py-32">
    <div class="site-container grid gap-8 lg:grid-cols-2">
        <article class="rounded-[1.75rem] border border-gold-300/15 bg-white/[0.03] p-10 lg:p-12" data-reveal>
            <p class="eyebrow eyebrow-light">Our mission</p>
            <p class="mt-6 font-serif text-3xl leading-snug text-ivory"><?= e(Company::MISSION[0]) ?></p>
            <p class="mt-6 leading-relaxed text-ink-300"><?= e(Company::MISSION[1]) ?></p>
        </article>
        <article class="rounded-[1.75rem] border border-gold-300/15 bg-white/[0.03] p-10 lg:p-12" data-reveal style="--reveal-delay:120ms">
            <p class="eyebrow eyebrow-light">Our vision</p>
            <p class="mt-6 font-serif text-3xl leading-snug text-ivory"><?= e(Company::VISION) ?></p>
            <p class="mt-10 font-serif text-xl italic text-gold-300"><?= e(Site::POSITIONING) ?></p>
        </article>
    </div>
</section>

<!-- Values -->
<section id="values" class="scroll-mt-24 bg-ivory py-24 lg:py-32">
    <div class="site-container">
        <div class="max-w-2xl">
            <p class="eyebrow" data-reveal>Core values</p>
            <h2 class="display mt-5 text-4xl sm:text-5xl" data-reveal>The principles behind <em>every engagement.</em></h2>
        </div>
        <div class="mt-14 grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
            <?php foreach (Company::VALUES as $i => [$icon, $value, $text]): ?>
                <div class="card-lux" data-reveal style="--reveal-delay:<?= ($i % 4) * 80 ?>ms">
                    <span class="icon-tile"><?= Icons::svg($icon) ?></span>
                    <h3 class="mt-6 text-lg font-semibold text-ink-950"><?= e($value) ?></h3>
                    <p class="mt-3 text-sm leading-relaxed text-ink-600"><?= e($text) ?></p>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<?php include __DIR__ . '/../components/site/process.php'; ?>

<?php $cta = ['title' => 'Ready to start with a <em>conversation?</em>', 'secondary' => ['Meet Our Team', 'team']]; include __DIR__ . '/../components/site/cta-band.php'; ?>
