<?php
// /resources/views/components/site/cta-band.php
//
// Closing call to action. Optional $cta: title (may contain <em>), text,
// primary [label, path], secondary [label, path].

use Src\Content\Icons;

$cta = ($cta ?? []) + [
    'title' => 'Let’s turn your challenge into a <em>clear plan.</em>',
    'text' => 'Tell us about your project, funding need, business challenge or consulting requirement. We’ll listen first — then help you find the way forward.',
    'primary' => ['Book a Consultation', 'contact?type=consultation'],
    'secondary' => ['Discuss Your Project', 'contact'],
];
?>
<section class="relative overflow-hidden bg-ivory py-24 lg:py-32">
    <div class="site-container">
        <div class="ink-surface relative overflow-hidden rounded-[2rem] px-8 py-16 text-center shadow-2xl shadow-ink-950/20 sm:px-16 lg:py-20" data-reveal>
            <div aria-hidden="true" class="pointer-events-none absolute inset-x-0 -bottom-28 mx-auto w-[42rem] max-w-full opacity-[0.10]"><?= Icons::waveform('w-full') ?></div>
            <div aria-hidden="true" class="pointer-events-none absolute inset-3 rounded-[1.6rem] border border-gold-300/15"></div>
            <div class="relative mx-auto max-w-2xl">
                <p class="eyebrow eyebrow-light justify-center">Start the conversation</p>
                <h2 class="display display-light mt-5 text-4xl sm:text-5xl"><?= $cta['title'] ?></h2>
                <p class="mt-6 text-lg leading-relaxed text-ink-200"><?= e($cta['text']) ?></p>
                <div class="mt-10 flex flex-wrap justify-center gap-4">
                    <a data-partial href="<?= site_url($cta['primary'][1]) ?>" class="btn-gold"><?= e($cta['primary'][0]) ?> <?= Icons::svg('arrow', 'h-4 w-4') ?></a>
                    <?php if (!empty($cta['secondary'])): ?>
                        <a data-partial href="<?= site_url($cta['secondary'][1]) ?>" class="btn-outline-light"><?= e($cta['secondary'][0]) ?></a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>
<?php unset($cta); ?>
