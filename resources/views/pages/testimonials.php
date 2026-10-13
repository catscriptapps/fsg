<?php
// /resources/views/pages/testimonials.php

declare(strict_types=1);

use Src\Content\Testimonials;

$testimonials = Testimonials::all();
$hero = [
    'eyebrow' => 'Testimonials',
    'title' => 'In our clients’ <em>words.</em>',
    'lead' => 'The best measure of our work is what it means for the people and organizations we support.',
    'photo' => 'teamwork',
    'crumbs' => [['Testimonials', null]],
];
include __DIR__ . '/../components/site/page-hero.php';
?>

<section class="bg-ivory py-24 lg:py-32">
    <div class="site-container">
        <?php if ($testimonials): ?>
            <div class="mx-auto max-w-4xl space-y-8">
                <?php foreach ($testimonials as $i => $t): ?>
                    <figure class="card-lux grid gap-8 !p-10 sm:grid-cols-[auto,1fr] sm:!p-14" data-reveal>
                        <span aria-hidden="true" class="font-serif text-[7rem] leading-[0.7] text-gold-400">“</span>
                        <div>
                            <blockquote class="font-serif text-3xl leading-snug text-ink-900 sm:text-4xl"><?= e($t['quote']) ?></blockquote>
                            <figcaption class="mt-8 flex items-center gap-4">
                                <span class="h-px w-10 bg-gold-500"></span>
                                <span class="font-semibold text-ink-950"><?= e($t['name']) ?></span>
                                <span class="text-xs uppercase tracking-widest text-gold-700"><?= e($t['topic']) ?></span>
                            </figcaption>
                        </div>
                    </figure>
                <?php endforeach; ?>
            </div>
            <p class="mx-auto mt-12 max-w-2xl text-center text-sm text-ink-500" data-reveal>Testimonials are shared with our clients’ permission. To protect their privacy, we use first names only.</p>
        <?php else: ?>
            <p class="text-center text-ink-600">Client testimonials will appear here soon.</p>
        <?php endif; ?>
    </div>
</section>

<?php $cta = ['title' => 'Let’s write your <em>success story.</em>']; include __DIR__ . '/../components/site/cta-band.php'; ?>
