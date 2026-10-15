<?php
// /resources/views/pages/projects.php
//
// Projects & Partnerships: approved projects / case studies
// (Src\Content\Projects::all()), how we partner, and the broader ecosystem
// of affiliated initiatives — clearly separated from our own services.

declare(strict_types=1);

use Src\Content\Icons;
use Src\Content\Photos;
use Src\Content\Projects;
use Src\Content\Site;

$projects = Projects::all();
$affiliates = Projects::affiliates();

$hero = [
    'eyebrow' => 'Projects & partnerships',
    'title' => 'Stronger together: <em>collaboration in action.</em>',
    'lead' => 'Complex challenges are best addressed by bringing the right people and disciplines together. Here we highlight approved projects, collaborations and the partnerships behind them.',
    'photo' => 'projects.hero', // photo spot — chosen in the admin under Site Photos
    'crumbs' => [['Projects & Partnerships', null]],
    'actions' => '<a data-partial href="' . site_url('contact?type=partnership') . '" class="btn-gold">Partner With Us</a>',
];
include __DIR__ . '/../components/site/page-hero.php';
?>

<!-- Projects & case studies -->
<section class="bg-ivory py-24 lg:py-32">
    <div class="site-container">
        <div class="max-w-2xl">
            <p class="eyebrow" data-reveal>Projects & case studies</p>
            <h2 class="display mt-5 text-4xl sm:text-5xl" data-reveal>Work we’re <em>proud of.</em></h2>
        </div>

        <?php if ($projects): ?>
            <div class="mt-14 grid gap-6 lg:grid-cols-2">
                <?php foreach ($projects as $p): ?>
                    <article class="card-lux overflow-hidden !p-0" data-reveal>
                        <?php if (!empty($p['image'])): ?>
                            <img src="<?= $assetBase . e($p['image']) ?>" alt="<?= e($p['image_alt'] ?? $p['title']) ?>" class="aspect-[16/9] w-full object-cover" loading="lazy">
                        <?php endif; ?>
                        <div class="p-8">
                            <?php if (!empty($p['category'])): ?><p class="text-xs font-semibold uppercase tracking-brand text-gold-700"><?= e($p['category']) ?></p><?php endif; ?>
                            <h3 class="display mt-3 text-3xl"><?= e($p['title']) ?></h3>
                            <p class="mt-4 leading-relaxed text-ink-600"><?= e($p['summary']) ?></p>
                            <?php if (!empty($p['outcomes'])): ?>
                                <ul class="mt-6 space-y-2">
                                    <?php foreach ($p['outcomes'] as $o): ?><li class="flex gap-3 text-sm text-ink-800"><?= Icons::svg('check', 'mt-0.5 h-4 w-4 flex-shrink-0 text-gold-600') ?><?= e($o) ?></li><?php endforeach; ?>
                                </ul>
                            <?php endif; ?>
                            <?php if (!empty($p['partners'])): ?>
                                <p class="mt-6 border-t border-ink-900/[0.07] pt-5 text-xs text-ink-500">In partnership with <?= e(human_list($p['partners'])) ?></p>
                            <?php endif; ?>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="mt-14 grid gap-6 lg:grid-cols-3">
                <?php foreach ([
                    ['strategy', 'Case studies', 'Approved client stories describing the challenge, our approach and the outcomes achieved.'],
                    ['connect', 'Collaborations', 'Joint initiatives with community partners, institutions and professional organizations.'],
                    ['impact', 'Community impact', 'Projects that strengthen organizations, improve access to services and support communities.'],
                ] as $i => [$icon, $title, $text]): ?>
                    <div class="rounded-2xl border border-dashed border-gold-500/40 bg-white/60 p-8" data-reveal style="--reveal-delay:<?= $i * 90 ?>ms">
                        <span class="icon-tile"><?= Icons::svg($icon) ?></span>
                        <h3 class="mt-6 text-lg font-semibold text-ink-950"><?= e($title) ?></h3>
                        <p class="mt-2 text-sm leading-relaxed text-ink-600"><?= e($text) ?></p>
                        <p class="mt-5 text-xs uppercase tracking-widest text-ink-400">To be published with partner approval</p>
                    </div>
                <?php endforeach; ?>
            </div>
            <p class="mt-8 flex items-center gap-3 text-sm text-ink-500" data-reveal><?= Icons::svg('lock', 'h-4 w-4 text-gold-700') ?> We only publish project details with our clients’ and partners’ authorization, and never share confidential client information.</p>
        <?php endif; ?>
    </div>
</section>

<!-- Partner with us -->
<section class="ink-surface py-24 lg:py-32">
    <div class="site-container grid gap-14 lg:grid-cols-12 lg:gap-20">
        <div class="lg:col-span-5">
            <p class="eyebrow eyebrow-light" data-reveal>Partner with us</p>
            <h2 class="display display-light mt-5 text-4xl sm:text-5xl" data-reveal>Let’s build something <em>meaningful.</em></h2>
            <p class="mt-6 text-lg leading-relaxed text-ink-300" data-reveal>We welcome conversations with consultants, community organizations, institutions and funders who share our commitment to equitable access and measurable impact.</p>
            <a data-partial href="<?= site_url('contact?type=partnership') ?>" class="btn-gold mt-10" data-reveal>Start a partnership conversation</a>
            <div class="mt-12"><?= Photos::frame('projects.partner', 'aspect-[4/3]') ?></div>
        </div>
        <ul class="grid content-center gap-4 sm:grid-cols-2 lg:col-span-7">
            <?php foreach ([
                ['expertise', 'Consultants & specialists', 'Professionals who can bring specialized expertise to multidisciplinary engagements.'],
                ['community', 'Community organizations', 'Agencies and nonprofits working to improve access to services and resources.'],
                ['institution', 'Institutions', 'Health, education and public-sector partners exploring collaborative projects.'],
                ['funding', 'Funders & sponsors', 'Organizations investing in community health, social impact and economic development.'],
            ] as $i => [$icon, $title, $text]): ?>
                <li class="rounded-2xl border border-gold-300/15 bg-white/[0.03] p-7" data-reveal style="--reveal-delay:<?= $i * 80 ?>ms">
                    <span class="icon-tile icon-tile-dark"><?= Icons::svg($icon) ?></span>
                    <h3 class="mt-5 font-semibold text-ivory"><?= e($title) ?></h3>
                    <p class="mt-2 text-sm leading-relaxed text-ink-300"><?= e($text) ?></p>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
</section>

<!-- Broader ecosystem -->
<?php if ($affiliates): ?>
<section id="ecosystem" class="scroll-mt-24 bg-white py-24 lg:py-32">
    <div class="site-container">
        <div class="mx-auto max-w-3xl text-center">
            <p class="eyebrow justify-center" data-reveal>Our broader ecosystem</p>
            <h2 class="display mt-5 text-4xl sm:text-5xl" data-reveal>Connected <em>initiatives.</em></h2>
            <p class="lead mt-6" data-reveal>Our longer-term work supports projects and partnerships that connect healthcare, community services, education and other resources — including initiatives associated with:</p>
        </div>
        <ul class="mx-auto mt-12 flex max-w-4xl flex-wrap justify-center gap-4">
            <?php foreach ($affiliates as $i => $a): ?>
                <li class="rounded-2xl border border-ink-900/[0.08] bg-ivory px-7 py-5 text-center" data-reveal style="--reveal-delay:<?= $i * 70 ?>ms">
                    <?php if ($a['url']): ?>
                        <a href="<?= e($a['url']) ?>" target="_blank" rel="noopener" class="font-serif text-2xl font-medium text-ink-950 hover:text-gold-800"><?= e($a['name']) ?></a>
                    <?php else: ?>
                        <span class="font-serif text-2xl font-medium text-ink-950"><?= e($a['name']) ?></span>
                    <?php endif; ?>
                    <?php if ($a['summary']): ?><p class="mt-1 max-w-xs text-sm text-ink-600"><?= e($a['summary']) ?></p><?php endif; ?>
                </li>
            <?php endforeach; ?>
        </ul>
        <p class="mx-auto mt-10 max-w-3xl text-center text-sm leading-relaxed text-ink-500" data-reveal role="note"><?= e(Site::BRAND) ?> is an independent consulting company. The initiatives above are separate organizations; their services are provided by each organization and are not services of <?= e(Site::BRAND) ?>.</p>
    </div>
</section>
<?php endif; ?>

<?php $cta = ['title' => 'Have a project in <em>mind?</em>', 'primary' => ['Discuss Your Project', 'contact?type=inquiry'], 'secondary' => ['Partner With Us', 'contact?type=partnership']]; include __DIR__ . '/../components/site/cta-band.php'; ?>
