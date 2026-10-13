<?php
// /resources/views/components/site/not-found.php
//
// The 404 page in the public site layout (public/index.php sends every
// not-found route here).

use Src\Content\Icons;
use Src\Content\Services;
?>
<section class="ink-surface relative flex min-h-[80vh] items-center overflow-hidden pb-24 pt-36">
    <div aria-hidden="true" class="pointer-events-none absolute inset-x-0 bottom-10 mx-auto w-[40rem] max-w-full opacity-20"><?= Icons::waveform('w-full') ?></div>
    <div class="site-container relative text-center">
        <p class="eyebrow eyebrow-light justify-center">Error 404</p>
        <h1 class="display display-light mt-6 text-5xl sm:text-6xl">We couldn’t find <em>that page.</em></h1>
        <p class="mx-auto mt-6 max-w-xl text-lg text-ink-300">It may have moved, or the link may be out of date. Here are some places to start instead.</p>
        <div class="mt-10 flex flex-wrap justify-center gap-4">
            <a data-partial href="<?= site_url() ?>" class="btn-gold">Back to home</a>
            <a data-partial href="<?= site_url('services') ?>" class="btn-outline-light">Explore our services</a>
            <a data-partial href="<?= site_url('contact') ?>" class="btn-outline-light">Contact us</a>
        </div>
        <ul class="mx-auto mt-14 flex max-w-3xl flex-wrap justify-center gap-x-6 gap-y-3 text-sm text-ink-400">
            <?php foreach (Services::all() as $s): ?>
                <li><a data-partial href="<?= site_url('services/' . $s['slug']) ?>" class="hover:text-gold-300"><?= e($s['title']) ?></a></li>
            <?php endforeach; ?>
        </ul>
    </div>
</section>
