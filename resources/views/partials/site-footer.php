<?php
// /resources/views/partials/site-footer.php

use Src\Content\Icons;
use Src\Content\Services;
use Src\Content\Site;

$explore = [
    ['About Us', 'about'], ['Our Team', 'team'], ['Funding & Resources', 'funding-resources'],
    ['Projects & Partnerships', 'projects'], ['Live Feed', 'live-feed'], ['Testimonials', 'testimonials'], ['FAQs', 'faqs'], ['Contact', 'contact'],
];
?>
<footer class="ink-surface relative overflow-hidden text-ink-200">
    <div aria-hidden="true" class="pointer-events-none absolute -right-24 bottom-0 w-[36rem] opacity-[0.06]"><?= Icons::waveform('w-full', false) ?></div>

    <div class="site-container relative pb-10 pt-20">
        <div class="grid gap-12 lg:grid-cols-12">
            <div class="lg:col-span-4">
                <img src="<?= $assetBase ?>images/logo/logo-gold.webp" alt="<?= e(Site::BRAND) ?>" width="220" height="68" class="h-14 w-auto" loading="lazy">
                <p class="mt-6 max-w-sm text-sm leading-relaxed text-ink-300">A Black-owned, women-led, multidisciplinary consulting company helping organizations and individuals turn complex challenges into practical, measurable progress.</p>
                <p class="mt-6 font-serif text-xl italic text-gold-300"><?= e(implode(' ', Site::BRAND_STATEMENT)) ?></p>
            </div>

            <nav aria-label="Explore" class="lg:col-span-2">
                <h2 class="text-xs font-semibold uppercase tracking-brand text-gold-300">Explore</h2>
                <ul class="mt-5 space-y-3 text-sm">
                    <?php foreach ($explore as [$label, $p]): ?>
                        <li><a data-partial href="<?= site_url($p) ?>" class="transition-colors hover:text-gold-300"><?= e($label) ?></a></li>
                    <?php endforeach; ?>
                </ul>
            </nav>

            <nav aria-label="Services" class="lg:col-span-3">
                <h2 class="text-xs font-semibold uppercase tracking-brand text-gold-300">Services</h2>
                <ul class="mt-5 space-y-3 text-sm">
                    <?php foreach (Services::all() as $s): ?>
                        <li><a data-partial href="<?= site_url('services/' . $s['slug']) ?>" class="transition-colors hover:text-gold-300"><?= e($s['title']) ?></a></li>
                    <?php endforeach; ?>
                </ul>
            </nav>

            <div class="lg:col-span-3">
                <h2 class="text-xs font-semibold uppercase tracking-brand text-gold-300">Get in touch</h2>
                <ul class="mt-5 space-y-4 text-sm">
                    <li class="flex gap-3"><?= Icons::svg('user', 'mt-0.5 h-4 w-4 flex-shrink-0 text-gold-400') ?><span><?= e(Site::LEAD) ?><span class="block text-xs text-ink-400">Lead Consultant</span></span></li>
                    <li><a href="tel:<?= Site::PHONE_E164 ?>" class="flex gap-3 transition-colors hover:text-gold-300"><?= Icons::svg('phone', 'mt-0.5 h-4 w-4 flex-shrink-0 text-gold-400') ?><?= e(Site::PHONE) ?></a></li>
                    <li><a href="mailto:<?= e(Site::EMAIL) ?>" class="flex gap-3 break-all transition-colors hover:text-gold-300"><?= Icons::svg('mail', 'mt-0.5 h-4 w-4 flex-shrink-0 text-gold-400') ?><?= e(Site::EMAIL) ?></a></li>
                    <li><a href="<?= e(Site::WEBSITE) ?>" class="flex gap-3 transition-colors hover:text-gold-300"><?= Icons::svg('globe', 'mt-0.5 h-4 w-4 flex-shrink-0 text-gold-400') ?><?= e(Site::WEBSITE_LABEL) ?></a></li>
                </ul>
                <a data-partial href="<?= site_url('contact?type=consultation') ?>" class="btn-gold mt-7 !px-6 !py-3">Book a Consultation</a>
                <?php if (Site::SOCIALS): ?>
                    <ul class="mt-7 flex gap-3">
                        <?php foreach (Site::SOCIALS as $network => $href): ?>
                            <li><a href="<?= e($href) ?>" rel="noopener" target="_blank" class="inline-flex h-10 items-center rounded-full border border-gold-300/25 px-4 text-xs font-semibold uppercase tracking-widest text-gold-200 hover:border-gold-300"><?= e(ucfirst($network)) ?></a></li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
        </div>

        <div class="hairline mt-16 opacity-40"></div>

        <div class="mt-8 flex flex-col gap-4 text-xs text-ink-400 md:flex-row md:items-center md:justify-between">
            <p>&copy; <?= date('Y') ?> <?= e(Site::LEGAL_NAME) ?>. All rights reserved.</p>
            <ul class="flex flex-wrap gap-x-6 gap-y-2">
                <li><a data-partial href="<?= site_url('privacy') ?>" class="hover:text-gold-300">Privacy Policy</a></li>
                <li><a data-partial href="<?= site_url('terms') ?>" class="hover:text-gold-300">Terms of Use & Disclaimers</a></li>
                <?php if ($isLoggedIn ?? false): ?>
                    <li><a data-partial href="<?= site_url('dashboard') ?>" class="hover:text-gold-300">Dashboard</a></li>
                <?php else: ?>
                    <li><a href="<?= site_url('dashboard?login') ?>" data-login-button rel="nofollow" class="hover:text-gold-300">Team sign-in</a></li>
                <?php endif; ?>
            </ul>
        </div>
        <p class="mt-6 max-w-4xl text-[0.75rem] leading-relaxed text-ink-500">Information on this website is general in nature and is not legal, medical, financial or other regulated professional advice. Funding decisions rest with each funder and cannot be guaranteed. See our <a data-partial href="<?= site_url('terms') ?>" class="underline decoration-ink-600 underline-offset-2 hover:text-gold-300">disclaimers</a>.</p>
    </div>
</footer>
