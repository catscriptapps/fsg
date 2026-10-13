<?php
// /resources/views/pages/faqs.php
//
// Public FAQs from Src\Content\Faqs, as an accordion (native <details>, so
// it works without JS). Answers support blank-line paragraphs, **bold** and
// [label](path) internal links.

declare(strict_types=1);

use Src\Content\Faqs;
use Src\Content\Icons;

$faqs = Faqs::all();

$format = function (string $answer): string {
    $html = '';
    foreach (preg_split("/\n\s*\n/", trim($answer)) as $para) {
        $p = e($para);
        $p = preg_replace('/\*\*(.+?)\*\*/', '<strong class="font-semibold text-ink-900">$1</strong>', $p);
        $p = preg_replace_callback('/\[([^\]]+)\]\(([a-z0-9\-\/#?=&;]+)\)/i', fn($m) => '<a data-partial href="' . site_url(html_entity_decode($m[2])) . '">' . $m[1] . '</a>', $p);
        $html .= '<p>' . nl2br($p) . '</p>';
    }
    return $html;
};

$hero = [
    'eyebrow' => 'FAQs',
    'title' => 'Frequently asked <em>questions.</em>',
    'lead' => 'Answers to the questions we hear most. If yours isn’t here, we’re happy to talk it through.',
    'crumbs' => [['FAQs', null]],
];
include __DIR__ . '/../components/site/page-hero.php';

// FAQPage structured data for search engines
$faqLd = ['@context' => 'https://schema.org', '@type' => 'FAQPage', 'mainEntity' => array_map(fn($f) => [
    '@type' => 'Question', 'name' => $f[0],
    'acceptedAnswer' => ['@type' => 'Answer', 'text' => trim(strip_tags(preg_replace('/\[([^\]]+)\]\([^)]+\)/', '$1', str_replace('**', '', $f[1]))))],
], $faqs)];
?>
<script type="application/ld+json"><?= json_encode($faqLd, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) ?></script>

<section class="bg-ivory py-24 lg:py-32">
    <div class="site-container grid gap-14 lg:grid-cols-12 lg:gap-20">
        <div class="space-y-4 lg:col-span-8">
            <?php foreach ($faqs as $i => [$q, $a]): ?>
                <details class="group rounded-2xl border border-ink-900/[0.07] bg-white shadow-sm transition-colors open:border-gold-500/40" data-reveal <?= $i === 0 ? 'open' : '' ?>>
                    <summary class="flex cursor-pointer list-none items-center justify-between gap-6 px-7 py-6 text-left [&::-webkit-details-marker]:hidden">
                        <h2 class="text-lg font-semibold text-ink-950"><?= e($q) ?></h2>
                        <span class="flex h-9 w-9 flex-shrink-0 items-center justify-center rounded-full border border-gold-500/40 text-gold-700 transition-transform duration-300 group-open:rotate-45"><?= Icons::svg('plus', 'h-4 w-4') ?></span>
                    </summary>
                    <div class="prose-site px-7 pb-7 !space-y-4 text-ink-600"><?= $format($a) ?></div>
                </details>
            <?php endforeach; ?>
        </div>
        <aside class="lg:col-span-4">
            <div class="ink-surface rounded-2xl p-8 lg:sticky lg:top-32" data-reveal>
                <p class="eyebrow eyebrow-light">Still have questions?</p>
                <p class="mt-5 font-serif text-3xl leading-snug text-ivory">We’d be glad to <em class="text-gold-300">talk it through.</em></p>
                <a data-partial href="<?= site_url('contact?type=inquiry') ?>" class="btn-gold mt-8 w-full">Contact Our Team</a>
                <a data-partial href="<?= site_url('contact?type=consultation') ?>" class="btn-outline-light mt-3 w-full">Book a Consultation</a>
            </div>
        </aside>
    </div>
</section>
