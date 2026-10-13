<?php
// /src/Content/Photos.php

declare(strict_types=1);

namespace Src\Content;

/**
 * Photography for the public site. The files live in public/images/home/
 * (numbered, as Src\Utils\CuratedPhotos expects). Pages ask for a photo by
 * role — Photos::get('meeting') — so swapping a picture means changing one
 * file name here. A missing file is simply skipped.
 */
class Photos
{
    public const DIR = 'images/home/';

    /** role => [file, alt text] */
    private const ROLES = [
        'network'  => ['1.jpg', 'Hands joined over a glowing map of global connections'],
        'meeting'  => ['2.jpg', 'A consultant leading a strategy discussion with a team around a meeting table'],
        'advising' => ['3.jpg', 'Two colleagues reviewing a plan together on a laptop'],
        'consult'  => ['4.jpg', 'Two professionals reviewing documents on a tablet during a consultation'],
        'teamwork' => ['5.jpg', 'A diverse team working together over documents at a shared table'],
    ];

    /**
     * Home page slideshow, in order: role + caption. Edit freely.
     * link is a site path (opened as an SPA partial).
     */
    public const SLIDES = [
        ['role' => 'meeting', 'eyebrow' => 'Strategy', 'title' => 'A clear plan, <em>built with you.</em>', 'text' => 'We turn goals and challenges into a structured strategy — priorities, resources, timelines and the next right step.', 'cta' => 'Our approach', 'link' => 'about#approach'],
        ['role' => 'advising', 'eyebrow' => 'Funding', 'title' => 'Funding, made <em>attainable.</em>', 'text' => 'From finding the right opportunities to a ready-to-submit application, we help you make your strongest case.', 'cta' => 'Funding & resources', 'link' => 'funding-resources'],
        ['role' => 'consult', 'eyebrow' => 'Listening', 'title' => 'It starts with <em>listening.</em>', 'text' => 'Every engagement begins by understanding where you are, where you want to go and what is standing in the way.', 'cta' => 'Book a consultation', 'link' => 'contact?type=consultation'],
        ['role' => 'teamwork', 'eyebrow' => 'Community', 'title' => 'Stronger organizations, <em>stronger communities.</em>', 'text' => 'We help nonprofits and community agencies build programs, capacity and sustainable funding.', 'cta' => 'Nonprofit support', 'link' => 'services/nonprofit-development'],
        ['role' => 'network', 'eyebrow' => 'Partners', 'title' => 'The right expertise, <em>connected.</em>', 'text' => 'We bring together partners, funders and specialists across disciplines — so complex challenges get the expertise they need.', 'cta' => 'Partner with us', 'link' => 'projects'],
    ];

    /** @return array{src: string, alt: string}|null */
    public static function get(string $role): ?array
    {
        if (!isset(self::ROLES[$role])) {
            return null;
        }
        [$file, $alt] = self::ROLES[$role];
        if (!is_file(__DIR__ . '/../../public/' . self::DIR . $file)) {
            return null;
        }
        return ['src' => getAssetBase() . self::DIR . $file, 'alt' => $alt];
    }

    /**
     * A gold-framed photo (.photo-frame in app.css) for inner pages, or ''
     * if the file is missing. $class sets the size, e.g. 'aspect-[4/3]'.
     */
    public static function frame(string $role, string $class = 'aspect-[4/3]', bool $reveal = true): string
    {
        $photo = self::get($role);
        if (!$photo) {
            return '';
        }
        return '<figure class="photo-frame ' . e($class) . '"' . ($reveal ? ' data-reveal' : '') . '>'
            . '<img src="' . e($photo['src']) . '" alt="' . e($photo['alt']) . '" loading="lazy" width="1400" height="934">'
            . '</figure>';
    }

    /** Slides whose photo exists, with 'src' and 'alt' added */
    public static function slides(): array
    {
        $out = [];
        foreach (self::SLIDES as $slide) {
            if ($photo = self::get($slide['role'])) {
                $out[] = $slide + $photo;
            }
        }
        return $out;
    }
}
