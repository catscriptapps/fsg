<?php
// /resources/views/pages/funding-resources.php
//
// Funding & Resources: the funding service in brief, a funding-readiness
// guide, and a home for future guides / insights articles.

declare(strict_types=1);

use Src\Content\Icons;
use Src\Content\Services;

$funding = Services::find('funding-strategy-grant-development');

$hero = [
    'eyebrow' => 'Funding & resources',
    'title' => 'Get ready for the <em>right funding.</em>',
    'lead' => 'Funding is more attainable when you know where to look and what funders expect. We help you find appropriate opportunities and prepare applications that clearly show your need, plan and impact.',
    'crumbs' => [['Funding & Resources', null]],
    'actions' => '<a data-partial href="' . site_url('contact?type=consultation&service=' . $funding['slug']) . '" class="btn-gold">Explore Funding Support</a>'
        . '<a href="#readiness" class="btn-outline-light">Funding-readiness guide</a>',
];
include __DIR__ . '/../components/site/page-hero.php';

// General educational guidance — not tailored advice.
$readiness = [
    ['Know your eligibility', 'Funders set rules about who can apply — for example registered charities, nonprofits, businesses of a certain size or stage, or organizations in a particular region or sector. Confirm your legal status and that you meet each funder’s criteria before you invest time in an application.'],
    ['Define the need', 'Be able to explain the problem you are addressing, who is affected and why it matters now. Evidence such as community data, needs assessments or client feedback makes a case far stronger.'],
    ['Describe a clear project', 'Set out what you will do, who will do it, the timeline and the results you expect. Funders look for well-defined activities that link directly to their priorities.'],
    ['Build a realistic budget', 'Prepare an itemized budget that matches your activities, shows other sources of support, and explains how each cost is justified.'],
    ['Plan how you’ll measure results', 'Identify the outcomes you will track and how you will report them. Measurable objectives show funders their investment will be accountable.'],
    ['Gather your documents early', 'Common requirements include incorporation or registration documents, financial statements, board or ownership information, and letters of support from partners. Collecting them in advance avoids last-minute pressure.'],
    ['Show capacity and partnerships', 'Demonstrate that your team and partners have the experience and resources to deliver — and, where relevant, how the work will be sustained after the funding ends.'],
    ['Respect deadlines and instructions', 'Read guidelines carefully, answer every question asked, follow formatting rules and leave time for review. Incomplete or late applications are often not considered.'],
];
$sources = [
    ['institution', 'Government', 'Federal, provincial and municipal programs for businesses, nonprofits and community initiatives.'],
    ['compassion', 'Foundations', 'Private and community foundations that fund work aligned with their mission and priorities.'],
    ['business', 'Corporate', 'Corporate giving, sponsorship and community investment programs.'],
    ['community', 'Community', 'Local funds, service clubs and community partnerships.'],
];
?>

<!-- How we help -->
<section class="bg-ivory py-24 lg:py-32">
    <div class="site-container grid gap-14 lg:grid-cols-12 lg:gap-20">
        <div class="lg:col-span-5">
            <p class="eyebrow" data-reveal>Funding strategy & grant development</p>
            <h2 class="display mt-5 text-4xl sm:text-5xl" data-reveal>From research to a <em>ready-to-submit</em> application.</h2>
            <p class="lead mt-6" data-reveal><?= e($funding['intro'][0]) ?></p>
            <a data-partial href="<?= site_url('services/' . $funding['slug']) ?>" class="link-arrow mt-8" data-reveal>Full service details <?= Icons::svg('arrow') ?></a>
        </div>
        <div class="lg:col-span-7">
            <ul class="grid gap-3 sm:grid-cols-2" data-reveal>
                <?php foreach ($funding['offerings'] as $o): ?>
                    <li class="flex gap-3 rounded-xl border border-ink-900/[0.06] bg-white px-5 py-4 text-sm leading-relaxed text-ink-800"><?= Icons::svg('check', 'mt-0.5 h-4 w-4 flex-shrink-0 text-gold-600') ?><?= e($o) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>

    <div class="site-container mt-16">
        <h3 class="text-xs font-semibold uppercase tracking-brand text-gold-700" data-reveal>Funding sources we help you explore</h3>
        <div class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <?php foreach ($sources as $i => [$icon, $title, $text]): ?>
                <div class="card-lux !p-6" data-reveal style="--reveal-delay:<?= $i * 80 ?>ms">
                    <span class="text-gold-700"><?= Icons::svg($icon) ?></span>
                    <h4 class="mt-4 font-semibold text-ink-950"><?= e($title) ?></h4>
                    <p class="mt-2 text-sm leading-relaxed text-ink-600"><?= e($text) ?></p>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Funding readiness guide -->
<section id="readiness" class="scroll-mt-24 bg-white py-24 lg:py-32">
    <div class="site-container grid gap-14 lg:grid-cols-12 lg:gap-20">
        <div class="lg:col-span-4">
            <div class="lg:sticky lg:top-32">
                <p class="eyebrow" data-reveal>Guide</p>
                <h2 class="display mt-5 text-4xl sm:text-5xl" data-reveal>Are you <em>funding-ready?</em></h2>
                <p class="lead mt-6" data-reveal>Eight things funders commonly look for. Use this as a starting checklist before you apply.</p>
                <a data-partial href="<?= site_url('contact?type=consultation&service=' . $funding['slug']) ?>" class="btn-ink mt-8" data-reveal>Request a readiness assessment</a>
                <p class="mt-6 text-xs leading-relaxed text-ink-500" data-reveal>General information only. Every funder has its own requirements — always check the official guidelines for the program you’re applying to.</p>
            </div>
        </div>
        <ol class="space-y-4 lg:col-span-8">
            <?php foreach ($readiness as $i => [$title, $text]): ?>
                <li class="flex gap-6 rounded-2xl border border-ink-900/[0.07] bg-ivory px-7 py-7" data-reveal>
                    <span class="gold-text-deep font-serif text-4xl font-medium leading-none"><?= str_pad((string) ($i + 1), 2, '0', STR_PAD_LEFT) ?></span>
                    <div>
                        <h3 class="text-lg font-semibold text-ink-950"><?= e($title) ?></h3>
                        <p class="mt-2 leading-relaxed text-ink-600"><?= e($text) ?></p>
                    </div>
                </li>
            <?php endforeach; ?>
        </ol>
    </div>
</section>

<!-- Funding note -->
<section class="bg-ivory-deep py-16">
    <div class="site-container">
        <div class="flex flex-col gap-5 rounded-2xl border border-gold-500/30 bg-white px-8 py-7 sm:flex-row sm:items-start" data-reveal role="note">
            <span class="icon-tile"><?= Icons::svg('info') ?></span>
            <div>
                <h2 class="text-lg font-semibold text-ink-950">A note on funding outcomes</h2>
                <p class="mt-2 leading-relaxed text-ink-600"><?= e($funding['note']) ?> We will always give you an honest view of your fit for an opportunity.</p>
            </div>
        </div>
    </div>
</section>

<!-- Guides & insights (future) -->
<section id="insights" class="scroll-mt-24 bg-ivory py-24 lg:py-32">
    <div class="site-container">
        <div class="flex flex-col gap-6 lg:flex-row lg:items-end lg:justify-between">
            <div class="max-w-2xl">
                <p class="eyebrow" data-reveal>Guides & insights</p>
                <h2 class="display mt-5 text-4xl sm:text-5xl" data-reveal>Resources <em>coming soon.</em></h2>
                <p class="lead mt-6" data-reveal>We’re preparing downloadable guides and articles on the topics our clients ask about most.</p>
            </div>
        </div>
        <div class="mt-12 grid gap-5 sm:grid-cols-2 lg:grid-cols-5">
            <?php foreach (['Funding readiness', 'Organizational growth', 'Community development', 'Healthcare innovation', 'Strategic planning'] as $i => $topic): ?>
                <div class="rounded-2xl border border-dashed border-gold-500/40 bg-white/60 p-6" data-reveal style="--reveal-delay:<?= $i * 70 ?>ms">
                    <span class="text-gold-700"><?= Icons::svg('document') ?></span>
                    <p class="mt-4 font-semibold text-ink-900"><?= e($topic) ?></p>
                    <p class="mt-1 text-xs uppercase tracking-widest text-ink-400">Coming soon</p>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<?php $cta = ['title' => 'Let’s find the funding that <em>fits.</em>', 'text' => 'Tell us about your organization and project. We’ll help you understand your options and what it will take to apply with confidence.', 'primary' => ['Explore Funding Support', 'contact?type=consultation&service=' . $funding['slug']], 'secondary' => ['Read the FAQs', 'faqs']]; include __DIR__ . '/../components/site/cta-band.php'; ?>
