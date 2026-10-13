<?php
// /resources/views/pages/home.php
//
// Home: hero, who we are, philosophy, services, who we help, approach, why
// us, testimonials, lead consultant, call to action and contact.

declare(strict_types=1);

use Src\Content\Company;
use Src\Content\Icons;
use Src\Content\Services;
use Src\Content\Site;
use Src\Content\Team;
use Src\Content\Testimonials;

$lead = Team::lead();
$testimonials = Testimonials::all();
?>

<!-- 1. Hero -->
<section class="ink-surface relative overflow-hidden">
    <div aria-hidden="true" class="pointer-events-none absolute inset-0 bg-[radial-gradient(circle_at_70%_45%,rgba(224,193,144,0.10),transparent_45%)]"></div>
    <div class="site-container relative grid min-h-[100svh] items-center gap-12 pb-24 pt-32 lg:grid-cols-12 lg:pb-20 lg:pt-28">
        <div class="lg:col-span-7">
            <p class="eyebrow eyebrow-light" data-reveal>Black-owned · Women-led · Multidisciplinary</p>
            <h1 class="display display-light mt-6 text-5xl leading-[1.02] sm:text-6xl lg:text-[5.25rem]" data-reveal style="--reveal-delay:80ms">
                Turn ambitious ideas into <em>measurable impact.</em>
            </h1>
            <p class="mt-8 max-w-xl text-lg leading-relaxed text-ink-200" data-reveal style="--reveal-delay:160ms">
                We help businesses, nonprofits, healthcare organizations and entrepreneurs secure funding, build the right strategy and turn plans into results — bringing clinical, community and business expertise together around your goals.
            </p>
            <div class="mt-10 flex flex-wrap gap-4" data-reveal style="--reveal-delay:240ms">
                <a data-partial href="<?= site_url('contact?type=consultation') ?>" class="btn-gold"><?= Icons::svg('calendar', 'h-4 w-4') ?> Book a Consultation</a>
                <a data-partial href="<?= site_url('services') ?>" class="btn-outline-light">Explore Our Services <?= Icons::svg('arrow', 'h-4 w-4') ?></a>
            </div>
        </div>

        <div class="relative hidden lg:col-span-5 lg:block" data-reveal style="--reveal-delay:200ms">
            <div class="relative mx-auto aspect-square w-full max-w-md">
                <div class="absolute inset-0 rounded-full border border-gold-400/50 shadow-[0_0_120px_-20px_rgba(202,156,106,0.55),inset_0_0_80px_-30px_rgba(202,156,106,0.4)]"></div>
                <div class="absolute inset-6 rounded-full border border-gold-300/10"></div>
                <div class="absolute inset-[18%] flex items-center"><?= Icons::waveform('w-full') ?></div>
            </div>
        </div>
    </div>

    <!-- Brand statement -->
    <div class="relative border-t border-gold-300/10">
        <div class="site-container">
            <ul class="grid grid-cols-2 divide-gold-300/10 md:grid-cols-4 md:divide-x">
                <?php foreach (Site::BRAND_STATEMENT as $i => $phrase): ?>
                    <li class="py-6 text-center font-serif text-xl italic text-gold-200 md:py-8 md:text-2xl" data-reveal style="--reveal-delay:<?= $i * 90 ?>ms"><?= e($phrase) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
</section>

<!-- 2. Who we are -->
<section class="bg-ivory py-24 lg:py-32">
    <div class="site-container grid gap-14 lg:grid-cols-12 lg:gap-20">
        <div class="lg:col-span-5">
            <p class="eyebrow" data-reveal>Who we are</p>
            <h2 class="display mt-5 text-4xl sm:text-5xl" data-reveal>Every organization has the potential to <em>shine.</em></h2>
        </div>
        <div class="space-y-6 lg:col-span-7" data-reveal>
            <p class="lead">Frequency Strategy Group is a multidisciplinary consulting company that helps businesses, nonprofits, healthcare organizations, entrepreneurs and individuals navigate challenges and pursue meaningful opportunities.</p>
            <p class="text-ink-600">Our work begins with listening. We take the time to understand where you are, where you want to go and what is standing in your way — then develop tailored strategies that connect you with the right resources, expertise, partnerships and funding opportunities. We don’t simply provide advice: we connect strategy with implementation.</p>
            <a data-partial href="<?= site_url('about') ?>" class="link-arrow">More about us <?= Icons::svg('arrow') ?></a>
        </div>
    </div>

    <div class="site-container mt-20">
        <dl class="grid gap-px overflow-hidden rounded-2xl border border-ink-900/[0.07] bg-ink-900/[0.07] grid-cols-2 lg:grid-cols-4" data-reveal>
            <?php foreach ([
                ['27+', 'Years of clinical & leadership experience behind our lead consultant'],
                [(string) count(Services::all()), 'Service areas, from funding strategy to healthcare consulting'],
                ['5', 'Clear steps, from listening to evaluating results'],
                ['1', 'Point of contact, with a network of specialist partners'],
            ] as [$figure, $label]): ?>
                <div class="bg-white px-5 py-7 sm:px-8 sm:py-9">
                    <dt class="sr-only"><?= e($label) ?></dt>
                    <dd class="gold-text-deep font-serif text-6xl font-medium leading-none"><?= e($figure) ?></dd>
                    <dd class="mt-4 text-sm leading-relaxed text-ink-600"><?= e($label) ?></dd>
                </div>
            <?php endforeach; ?>
        </dl>
    </div>
</section>

<!-- Showcase slideshow (Src\Content\Photos::SLIDES) -->
<?php include __DIR__ . '/../components/site/slideshow.php'; ?>

<!-- 3. Our philosophy -->
<section class="relative overflow-hidden bg-white py-24 lg:py-32">
    <div class="site-container">
        <div class="mx-auto max-w-2xl text-center">
            <p class="eyebrow justify-center" data-reveal>Our philosophy</p>
            <h2 class="display mt-5 text-4xl sm:text-5xl" data-reveal>What our name <em>means.</em></h2>
        </div>
        <div class="mt-16 grid gap-6 lg:grid-cols-3">
            <?php foreach (Company::PHILOSOPHY as $i => [$word, $promise, $text]): ?>
                <article class="card-lux overflow-hidden !p-9" data-reveal style="--reveal-delay:<?= $i * 120 ?>ms">
                    <span aria-hidden="true" class="pointer-events-none absolute -right-4 -top-10 select-none font-serif text-[11rem] font-medium leading-none text-gold-100"><?= $word[0] ?></span>
                    <div class="relative">
                        <p class="text-xs font-semibold uppercase tracking-brand text-gold-700"><?= e($word) ?></p>
                        <h3 class="display mt-4 text-3xl"><?= e($promise) ?></h3>
                        <div class="mt-5 h-px w-12 bg-gold-500"></div>
                        <p class="mt-5 text-[0.95rem] leading-relaxed text-ink-600"><?= e($text) ?></p>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
        <p class="mx-auto mt-14 max-w-3xl text-center font-serif text-2xl italic leading-snug text-ink-800 sm:text-3xl" data-reveal>“<?= e(Company::PHILOSOPHY_SUMMARY) ?>”</p>
    </div>
</section>

<!-- 4. Services -->
<section class="bg-ivory py-24 lg:py-32">
    <div class="site-container">
        <div class="flex flex-col gap-6 lg:flex-row lg:items-end lg:justify-between">
            <div class="max-w-2xl">
                <p class="eyebrow" data-reveal>Our services</p>
                <h2 class="display mt-5 text-4xl sm:text-5xl" data-reveal>Expertise across the challenges that <em>matter most.</em></h2>
            </div>
            <a data-partial href="<?= site_url('services') ?>" class="link-arrow flex-shrink-0" data-reveal>View all services <?= Icons::svg('arrow') ?></a>
        </div>
        <div class="mt-14 grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
            <?php foreach (Services::all() as $i => $s): ?>
                <a data-partial href="<?= site_url('services/' . $s['slug']) ?>" class="card-lux group flex flex-col" data-reveal style="--reveal-delay:<?= ($i % 4) * 80 ?>ms">
                    <span class="icon-tile"><?= Icons::svg($s['icon']) ?></span>
                    <h3 class="mt-6 text-lg font-semibold leading-snug text-ink-950"><?= e($s['title']) ?></h3>
                    <p class="mt-3 flex-1 text-sm leading-relaxed text-ink-600"><?= e($s['short']) ?></p>
                    <span class="link-arrow mt-6">Learn more <?= Icons::svg('arrow') ?></span>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- 5. Who we help -->
<section class="bg-ivory-deep py-24 lg:py-32">
    <div class="site-container grid gap-14 lg:grid-cols-12 lg:gap-20">
        <div class="lg:col-span-5">
            <p class="eyebrow" data-reveal>Who we help</p>
            <h2 class="display mt-5 text-4xl sm:text-5xl" data-reveal>For-profit, nonprofit and <em>everyone between.</em></h2>
            <p class="lead mt-6" data-reveal>Whether you are launching a business, growing a nonprofit, developing a clinic or planning your next career move, we meet you where you are.</p>
            <a data-partial href="<?= site_url('contact?service=general&type=inquiry') ?>" class="btn-outline mt-9" data-reveal>Not sure where you fit? Ask us</a>
        </div>
        <ul class="grid gap-3 sm:grid-cols-2 lg:col-span-7">
            <?php foreach (Company::AUDIENCES as $i => [$icon, $label]): ?>
                <li class="flex items-center gap-4 rounded-xl border border-ink-900/[0.06] bg-white/70 px-5 py-4" data-reveal style="--reveal-delay:<?= ($i % 2) * 80 ?>ms">
                    <span class="text-gold-700"><?= Icons::svg($icon, 'h-6 w-6') ?></span>
                    <span class="text-[0.95rem] font-medium text-ink-800"><?= e($label) ?></span>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
</section>

<!-- 6. Our approach -->
<?php include __DIR__ . '/../components/site/process.php'; ?>

<!-- 7. Why work with us -->
<section class="bg-white py-24 lg:py-32">
    <div class="site-container">
        <div class="mx-auto max-w-2xl text-center">
            <p class="eyebrow justify-center" data-reveal>Why work with us</p>
            <h2 class="display mt-5 text-4xl sm:text-5xl" data-reveal>Strategy connected to <em>implementation.</em></h2>
        </div>
        <div class="mt-16 grid gap-x-12 gap-y-14 md:grid-cols-2">
            <?php foreach (Company::WHY_US as $i => [$icon, $title, $text]): ?>
                <div class="flex gap-6" data-reveal style="--reveal-delay:<?= ($i % 2) * 100 ?>ms">
                    <span class="icon-tile h-14 w-14"><?= Icons::svg($icon) ?></span>
                    <div>
                        <h3 class="font-serif text-2xl font-medium text-ink-950"><?= e($title) ?></h3>
                        <p class="mt-3 leading-relaxed text-ink-600"><?= e($text) ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- 8. Testimonials -->
<?php if ($testimonials): ?>
<section class="relative overflow-hidden bg-ivory py-24 lg:py-32">
    <div class="site-container">
        <div class="flex flex-col gap-6 lg:flex-row lg:items-end lg:justify-between">
            <div class="max-w-2xl">
                <p class="eyebrow" data-reveal>Client testimonials</p>
                <h2 class="display mt-5 text-4xl sm:text-5xl" data-reveal>In our clients’ <em>words.</em></h2>
            </div>
            <a data-partial href="<?= site_url('testimonials') ?>" class="link-arrow flex-shrink-0" data-reveal>All testimonials <?= Icons::svg('arrow') ?></a>
        </div>
        <div class="mt-14 grid gap-6 lg:grid-cols-3">
            <?php foreach ($testimonials as $i => $t): ?>
                <figure class="card-lux flex flex-col !p-9" data-reveal style="--reveal-delay:<?= $i * 120 ?>ms">
                    <span aria-hidden="true" class="font-serif text-7xl leading-[0.6] text-gold-400">“</span>
                    <blockquote class="mt-4 flex-1 font-serif text-2xl leading-snug text-ink-900"><?= e($t['quote']) ?></blockquote>
                    <figcaption class="mt-8 flex items-center gap-4 border-t border-ink-900/[0.07] pt-6">
                        <span class="flex h-11 w-11 items-center justify-center rounded-full bg-ink-950 font-serif text-lg text-gold-300"><?= e($t['name'][0]) ?></span>
                        <span><span class="block font-semibold text-ink-950"><?= e($t['name']) ?></span><span class="block text-xs uppercase tracking-widest text-gold-700"><?= e($t['topic']) ?></span></span>
                    </figcaption>
                </figure>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- 9. Meet the lead consultant -->
<section class="bg-white py-24 lg:py-32">
    <div class="site-container grid items-center gap-14 lg:grid-cols-12 lg:gap-20">
        <div class="mx-auto w-full max-w-sm lg:col-span-5 lg:max-w-none" data-reveal>
            <?php $person = $lead; include __DIR__ . '/../components/site/portrait.php'; ?>
        </div>
        <div class="lg:col-span-7">
            <p class="eyebrow" data-reveal>Meet our lead consultant</p>
            <h2 class="display mt-5 text-4xl sm:text-5xl" data-reveal><?= e($lead['name']) ?><span class="ml-3 align-middle font-sans text-base font-semibold tracking-widest text-gold-700"><?= e($lead['credentials']) ?></span></h2>
            <p class="mt-4 font-serif text-2xl italic text-ink-700" data-reveal><?= e($lead['headline']) ?></p>
            <p class="mt-6 leading-relaxed text-ink-600" data-reveal><?= e($lead['bio'][1]) ?></p>
            <ul class="mt-8 flex flex-wrap gap-2" data-reveal>
                <?php foreach (array_slice($lead['expertise'], 0, 5) as $x): ?>
                    <li class="rounded-full border border-gold-500/30 bg-gold-50 px-4 py-1.5 text-xs font-semibold text-gold-800"><?= e($x) ?></li>
                <?php endforeach; ?>
            </ul>
            <div class="mt-10 flex flex-wrap gap-4" data-reveal>
                <a data-partial href="<?= site_url('team') ?>" class="btn-ink">Read Sheena’s profile <?= Icons::svg('arrow', 'h-4 w-4') ?></a>
                <a data-partial href="<?= site_url('contact?type=consultation') ?>" class="btn-outline">Book a Consultation</a>
            </div>
        </div>
    </div>
</section>

<!-- 10. Call to action -->
<?php $cta = ['title' => 'Have a project, funding need or business challenge? <em>Let’s talk.</em>']; include __DIR__ . '/../components/site/cta-band.php'; ?>

<!-- 11. Contact -->
<section id="contact" class="scroll-mt-24 bg-ivory pb-24 lg:pb-32">
    <div class="site-container grid gap-12 lg:grid-cols-12 lg:gap-16">
        <div class="lg:col-span-4">
            <p class="eyebrow" data-reveal>Contact</p>
            <h2 class="display mt-5 text-4xl sm:text-5xl" data-reveal>Start with a <em>conversation.</em></h2>
            <p class="lead mt-6" data-reveal>Reach out directly, or send us a note and we’ll reply to arrange a time to talk.</p>
            <ul class="mt-10 space-y-6" data-reveal>
                <li class="flex gap-4"><span class="icon-tile h-11 w-11"><?= Icons::svg('phone', 'h-5 w-5') ?></span><span><span class="block text-xs font-semibold uppercase tracking-widest text-ink-500">Phone</span><a href="tel:<?= Site::PHONE_E164 ?>" class="text-lg font-semibold text-ink-950 hover:text-gold-800"><?= e(Site::PHONE) ?></a></span></li>
                <li class="flex gap-4"><span class="icon-tile h-11 w-11"><?= Icons::svg('mail', 'h-5 w-5') ?></span><span class="min-w-0"><span class="block text-xs font-semibold uppercase tracking-widest text-ink-500">Email</span><a href="mailto:<?= e(Site::EMAIL) ?>" class="break-all font-semibold text-ink-950 hover:text-gold-800"><?= e(Site::EMAIL) ?></a></span></li>
                <li class="flex gap-4"><span class="icon-tile h-11 w-11"><?= Icons::svg('globe', 'h-5 w-5') ?></span><span><span class="block text-xs font-semibold uppercase tracking-widest text-ink-500">Website</span><a href="<?= e(Site::WEBSITE) ?>" class="font-semibold text-ink-950 hover:text-gold-800"><?= e(Site::WEBSITE_LABEL) ?></a></span></li>
            </ul>
        </div>
        <div class="lg:col-span-8">
            <div class="card-lux !p-7 sm:!p-10" data-reveal>
                <?php $formId = 'home-inquiry'; include __DIR__ . '/../components/site/inquiry-form.php'; ?>
            </div>
        </div>
    </div>
</section>
