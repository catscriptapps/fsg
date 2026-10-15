<?php
// /resources/views/pages/contact.php
//
// Contact Us: contact details, how inquiries are handled, and the inquiry /
// consultation form (components/site/inquiry-form.php).

declare(strict_types=1);

use Src\Content\Icons;
use Src\Content\Site;

$hero = [
    'eyebrow' => 'Contact us',
    'title' => 'Let’s start with a <em>conversation.</em>',
    'lead' => 'Book a consultation, discuss a project, explore funding support or ask a question. Tell us a little about your situation and we’ll be in touch.',
    'photo' => 'contact.hero', // photo spot — chosen in the admin under Site Photos
    'crumbs' => [['Contact', null]],
];
include __DIR__ . '/../components/site/page-hero.php';
?>

<section class="bg-ivory py-24 lg:py-28">
    <div class="site-container grid gap-12 lg:grid-cols-12 lg:gap-16">
        <div class="lg:col-span-8">
            <div class="card-lux !p-7 sm:!p-10" data-reveal>
                <h2 class="display text-3xl sm:text-4xl">Send us an <em>inquiry.</em></h2>
                <p class="mb-8 mt-3 text-ink-600">Fields marked <span class="text-gold-700">*</span> are required.</p>
                <?php $formId = 'contact-inquiry'; include __DIR__ . '/../components/site/inquiry-form.php'; ?>
            </div>
        </div>

        <aside class="space-y-6 lg:col-span-4">
            <div class="ink-surface rounded-2xl p-8" data-reveal>
                <p class="eyebrow eyebrow-light">Reach us directly</p>
                <p class="mt-5 font-serif text-2xl text-ivory"><?= e(Site::LEAD) ?></p>
                <p class="text-sm text-ink-400">Lead Consultant</p>
                <ul class="mt-6 space-y-4 text-sm">
                    <li><a href="tel:<?= Site::PHONE_E164 ?>" class="flex items-center gap-3 text-ink-100 hover:text-gold-300"><?= Icons::svg('phone', 'h-5 w-5 text-gold-300') ?><?= e(Site::PHONE) ?></a></li>
                    <li><a href="mailto:<?= e(Site::EMAIL) ?>" class="flex items-start gap-3 break-all text-ink-100 hover:text-gold-300"><?= Icons::svg('mail', 'h-5 w-5 flex-shrink-0 text-gold-300') ?><?= e(Site::EMAIL) ?></a></li>
                    <li><a href="<?= e(Site::WEBSITE) ?>" class="flex items-center gap-3 text-ink-100 hover:text-gold-300"><?= Icons::svg('globe', 'h-5 w-5 text-gold-300') ?><?= e(Site::WEBSITE_LABEL) ?></a></li>
                </ul>
                <p class="mt-6 border-t border-white/[0.08] pt-5 text-xs leading-relaxed text-ink-400">Meetings are arranged by appointment.</p>
            </div>

            <div class="rounded-2xl border border-ink-900/[0.07] bg-white p-8 shadow-sm" data-reveal>
                <h2 class="text-xs font-semibold uppercase tracking-brand text-gold-700">How we handle inquiries</h2>
                <ol class="mt-6 space-y-5">
                    <?php foreach ([
                        ['We read every message', 'Your inquiry goes directly to our team — not to a mailing list or a third party.'],
                        ['We reply personally', 'We aim to respond ' . Site::RESPONSE_TIME . ' by email or phone, to arrange a time to talk.'],
                        ['We listen first', 'Your initial conversation is about understanding your goals and whether we’re the right fit.'],
                    ] as $i => [$t, $d]): ?>
                        <li class="flex gap-4">
                            <span class="flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-full border border-gold-500/40 font-serif text-base text-gold-800"><?= $i + 1 ?></span>
                            <div><p class="font-semibold text-ink-950"><?= e($t) ?></p><p class="mt-1 text-sm leading-relaxed text-ink-600"><?= e($d) ?></p></div>
                        </li>
                    <?php endforeach; ?>
                </ol>
                <p class="mt-6 border-t border-ink-900/[0.07] pt-5 text-xs leading-relaxed text-ink-500">We use your information only to respond to your inquiry. Please don’t include sensitive health or financial details in the form — we’ll discuss specifics with you directly. See our <a data-partial href="<?= site_url('privacy') ?>" class="font-semibold text-gold-800 underline underline-offset-2">Privacy Policy</a>.</p>
            </div>

            <a data-partial href="<?= site_url('faqs') ?>" class="card-lux group flex items-center gap-4 !p-6" data-reveal>
                <span class="icon-tile h-11 w-11"><?= Icons::svg('info', 'h-5 w-5') ?></span>
                <span class="flex-1"><span class="block font-semibold text-ink-950">Have a quick question?</span><span class="block text-sm text-ink-600">The FAQs may already have the answer.</span></span>
                <?= Icons::svg('arrow', 'h-4 w-4 text-gold-700 transition-transform group-hover:translate-x-1') ?>
            </a>
        </aside>
    </div>
</section>
