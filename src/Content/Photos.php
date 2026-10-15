<?php
// /src/Content/Photos.php

declare(strict_types=1);

namespace Src\Content;

use Illuminate\Database\Capsule\Manager as Capsule;

/**
 * Every photo on the public site, as an admin has arranged it on
 * /site-photos (Src\Controller\SitePhotosController):
 *
 *   - the photo library (site_photos) — its order is the home slideshow's
 *     order, and each photo can carry a slide caption;
 *   - the photo in each fixed spot around the site (site_photo_slots, keyed
 *     by SLOTS below).
 *
 * Pages only read through here: Photos::get('about.hero'), ::frame(),
 * ::slides(). Until the site-photos migration has run, everything falls back
 * to the shipped photos in public/images/home/ (FALLBACK), so the site never
 * breaks.
 */
class Photos
{
    /** Where the library's files live (relative to public/). */
    public const UPLOAD_DIR = 'images/uploads/site/';

    /** Shipped photos: role => [file under public/, alt text]. Seed + fallback only. */
    public const FALLBACK = [
        'network'  => ['images/home/1.jpg', 'Hands joined over a glowing map of global connections'],
        'meeting'  => ['images/home/2.jpg', 'A consultant leading a strategy discussion with a team around a meeting table'],
        'advising' => ['images/home/3.jpg', 'Two colleagues reviewing a plan together on a laptop'],
        'consult'  => ['images/home/4.jpg', 'Two professionals reviewing documents on a tablet during a consultation'],
        'teamwork' => ['images/home/5.jpg', 'A diverse team working together over documents at a shared table'],
    ];

    /** The original slideshow captions (seed + fallback), in order. */
    public const DEFAULT_SLIDES = [
        ['role' => 'meeting', 'eyebrow' => 'Strategy', 'title' => 'A clear plan, *built with you.*', 'text' => 'We turn goals and challenges into a structured strategy — priorities, resources, timelines and the next right step.', 'cta' => 'Our approach', 'link' => 'about#approach'],
        ['role' => 'advising', 'eyebrow' => 'Funding', 'title' => 'Funding, made *attainable.*', 'text' => 'From finding the right opportunities to a ready-to-submit application, we help you make your strongest case.', 'cta' => 'Funding & resources', 'link' => 'funding-resources'],
        ['role' => 'consult', 'eyebrow' => 'Listening', 'title' => 'It starts with *listening.*', 'text' => 'Every engagement begins by understanding where you are, where you want to go and what is standing in the way.', 'cta' => 'Book a consultation', 'link' => 'contact?type=consultation'],
        ['role' => 'teamwork', 'eyebrow' => 'Community', 'title' => 'Stronger organizations, *stronger communities.*', 'text' => 'We help nonprofits and community agencies build programs, capacity and sustainable funding.', 'cta' => 'Nonprofit support', 'link' => 'services/nonprofit-development'],
        ['role' => 'network', 'eyebrow' => 'Partners', 'title' => 'The right expertise, *connected.*', 'text' => 'We bring together partners, funders and specialists across disciplines — so complex challenges get the expertise they need.', 'cta' => 'Partner with us', 'link' => 'projects'],
    ];

    /**
     * Every spot on the site that shows a photo: key => [page, label, hint,
     * default role (FALLBACK) or null]. Shown in this order on /site-photos.
     */
    public static function slots(): array
    {
        $slots = [
            'home.hero'         => ['Home', 'Hero background', 'Faintly behind the home page headline, toned to the dark gold of the hero. Warm, uncluttered photos work best.', 'network'],
            'about.hero'        => ['About', 'Hero background', 'Behind the About page headline, under a dark tint.', 'teamwork'],
            'about.overview'    => ['About', 'Overview photo', 'Beside “Who we are”, with the pull quote overlapping it.', 'meeting'],
            'services.hero'     => ['Services', 'Hero background', 'Behind the Services page headline, under a dark tint.', 'meeting'],
        ];
        $serviceDefaults = [
            'funding-strategy-grant-development' => 'advising', 'business-consulting-strategic-planning' => 'meeting',
            'nonprofit-development' => 'teamwork', 'healthcare-consulting' => 'consult',
            'disability-case-management-consulting' => 'consult', 'sports-career-management' => 'meeting',
            'shelter-social-service-projects' => 'teamwork', 'specialized-consulting' => 'network',
        ];
        foreach (Services::all() as $s) {
            $slots['service.' . $s['slug']] = ['Service pages', $s['title'], 'Behind this service page’s headline, under a dark tint.', $serviceDefaults[$s['slug']] ?? 'meeting'];
        }
        return $slots + [
            'team.hero'         => ['Our Team', 'Hero background', 'Behind the Our Team headline, under a dark tint.', 'consult'],
            'team.lead'         => ['Our Team', 'Lead consultant portrait', 'Sheena’s photo on Our Team and the home page (a portrait, 4:5, works best). Empty shows her monogram.', null],
            'team.network'      => ['Our Team', 'Network banner', 'The wide photo above “Additional consultants and partners”.', 'teamwork'],
            'funding.hero'      => ['Funding & Resources', 'Hero background', 'Behind the Funding & Resources headline, under a dark tint.', 'advising'],
            'funding.intro'     => ['Funding & Resources', 'Intro photo', 'Under “From research to a ready-to-submit application”.', 'meeting'],
            'projects.hero'     => ['Projects & Partnerships', 'Hero background', 'Behind the Projects headline, under a dark tint.', 'network'],
            'projects.partner'  => ['Projects & Partnerships', 'Partner with us photo', 'In the dark “Partner with us” section.', 'teamwork'],
            'testimonials.hero' => ['Testimonials', 'Hero background', 'Behind the Testimonials headline, under a dark tint.', 'teamwork'],
            'contact.hero'      => ['Contact', 'Hero background', 'Behind the Contact headline, under a dark tint.', 'consult'],
        ];
    }

    /** @var array<string, ?array{src: string, alt: string}>|null per-request cache */
    private static ?array $slotCache = null;

    public static function installed(): bool
    {
        static $ok = null;
        if ($ok === null) {
            try {
                $ok = Capsule::schema()->hasTable('site_photos') && Capsule::schema()->hasTable('site_photo_slots');
            } catch (\Throwable $e) {
                $ok = false;
            }
        }
        return $ok;
    }

    /** Public URL for a file stored relative to public/ (cache-busted by its mtime). */
    public static function url(string $file): string
    {
        $full = self::path($file);
        $parts = array_map('rawurlencode', explode('/', ltrim($file, '/')));
        return getAssetBase() . implode('/', $parts) . (is_file($full) ? '?v=' . filemtime($full) : '');
    }

    /** Absolute disk path of a file stored relative to public/. */
    public static function path(string $file): string
    {
        return __DIR__ . '/../../public/' . ltrim($file, '/');
    }

    /**
     * The photo in one spot, or null (empty spot / missing file).
     * @return array{src: string, alt: string}|null
     */
    public static function get(string $slot): ?array
    {
        if (self::$slotCache === null) {
            self::$slotCache = [];
            if (self::installed()) {
                $rows = Capsule::table('site_photo_slots as s')
                    ->leftJoin('site_photos as p', 'p.id', '=', 's.photo_id')
                    ->get(['s.slot', 'p.file', 'p.alt']);
                foreach ($rows as $r) {
                    self::$slotCache[$r->slot] = $r->file && is_file(self::path($r->file))
                        ? ['src' => self::url($r->file), 'alt' => (string) ($r->alt ?? '')]
                        : null;
                }
            }
        }
        if (array_key_exists($slot, self::$slotCache)) {
            return self::$slotCache[$slot];
        }
        // Not set up yet: the shipped default for this spot
        $role = self::slots()[$slot][3] ?? null;
        if ($role && isset(self::FALLBACK[$role]) && is_file(self::path(self::FALLBACK[$role][0]))) {
            return ['src' => self::url(self::FALLBACK[$role][0]), 'alt' => self::FALLBACK[$role][1]];
        }
        return null;
    }

    /**
     * A gold-framed photo (.photo-frame in app.css) for a spot, or '' when
     * the spot is empty. $class sets the size, e.g. 'aspect-[4/3]'.
     */
    public static function frame(string $slot, string $class = 'aspect-[4/3]', bool $reveal = true): string
    {
        $photo = self::get($slot);
        if (!$photo) {
            return '';
        }
        return '<figure class="photo-frame ' . e($class) . '"' . ($reveal ? ' data-reveal' : '') . '>'
            . '<img src="' . e($photo['src']) . '" alt="' . e($photo['alt']) . '" loading="lazy">'
            . '</figure>';
    }

    /**
     * The home slideshow, in order: each slide has src, alt, eyebrow, title
     * (HTML, from titleHtml()), text, cta, link.
     */
    public static function slides(): array
    {
        if (self::installed()) {
            $rows = Capsule::table('site_photos')->where('in_slideshow', true)
                ->orderBy('display_order')->orderBy('id')->get();
            $out = [];
            foreach ($rows as $p) {
                if (!is_file(self::path($p->file))) {
                    continue;
                }
                $out[] = [
                    'src' => self::url($p->file),
                    'alt' => (string) ($p->alt ?? ''),
                    'eyebrow' => (string) ($p->slide_eyebrow ?: Site::BRAND),
                    'title' => self::titleHtml((string) ($p->slide_title ?: 'Equitable strategy. *Expert execution.*')),
                    'text' => (string) ($p->slide_text ?? ''),
                    'cta' => (string) ($p->slide_cta ?? ''),
                    'link' => (string) ($p->slide_link ?? ''),
                ];
            }
            return $out;
        }
        $out = [];
        foreach (self::DEFAULT_SLIDES as $s) {
            [$file, $alt] = self::FALLBACK[$s['role']];
            if (is_file(self::path($file))) {
                $out[] = ['src' => self::url($file), 'alt' => $alt, 'title' => self::titleHtml($s['title'])] + $s;
            }
        }
        return $out;
    }

    /** Slide title: escaped, with *words in asterisks* in gold italics. */
    public static function titleHtml(string $title): string
    {
        return preg_replace('/\*(.+?)\*/', '<em>$1</em>', e($title));
    }

    /** Forget the per-request cache (after an admin change). */
    public static function flush(): void
    {
        self::$slotCache = null;
    }
}
