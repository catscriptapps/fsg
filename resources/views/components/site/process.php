<?php
// /resources/views/components/site/process.php
//
// The five-step consulting process on a dark band (home + About).

use Src\Content\Company;
use Src\Content\Icons;

$processId = $processId ?? 'approach';
?>
<section id="<?= e($processId) ?>" class="ink-surface relative scroll-mt-24 overflow-hidden py-24 lg:py-32">
    <div class="site-container relative">
        <div class="mx-auto max-w-2xl text-center">
            <p class="eyebrow eyebrow-light justify-center" data-reveal>Our approach</p>
            <h2 class="display display-light mt-5 text-4xl sm:text-5xl" data-reveal>Five steps from <em>listening</em> to lasting results.</h2>
            <p class="mt-6 text-lg leading-relaxed text-ink-300" data-reveal>A structured, collaborative process — so you always know where we are, what comes next and why.</p>
        </div>

        <ol class="relative mt-20 grid gap-10 md:grid-cols-5 md:gap-6">
            <!-- Connecting line (desktop) -->
            <div aria-hidden="true" class="absolute left-[10%] right-[10%] top-8 hidden h-px bg-gradient-to-r from-gold-500/10 via-gold-400/50 to-gold-500/10 md:block"></div>
            <?php foreach (Company::PROCESS as $i => [$icon, $step, $text]): ?>
                <li class="relative flex gap-5 md:flex-col md:items-center md:gap-0 md:text-center" data-reveal style="--reveal-delay:<?= $i * 110 ?>ms">
                    <div class="relative flex h-16 w-16 flex-shrink-0 items-center justify-center rounded-full border border-gold-300/40 bg-ink-950 text-gold-300 shadow-[0_0_0_6px_rgba(11,10,8,1),0_0_40px_-6px_rgba(202,156,106,0.45)]">
                        <?= Icons::svg($icon, 'h-6 w-6') ?>
                        <span class="absolute -right-1 -top-1 flex h-6 w-6 items-center justify-center rounded-full bg-gold-400 text-[0.7rem] font-bold text-ink-950"><?= $i + 1 ?></span>
                    </div>
                    <div class="md:mt-7">
                        <h3 class="font-serif text-2xl font-medium text-ivory"><?= e($step) ?></h3>
                        <p class="mt-2 text-sm leading-relaxed text-ink-300"><?= e($text) ?></p>
                    </div>
                </li>
            <?php endforeach; ?>
        </ol>
    </div>
</section>
