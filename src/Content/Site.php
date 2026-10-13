<?php
// /src/Content/Site.php

declare(strict_types=1);

namespace Src\Content;

/**
 * Company details, navigation and per-page SEO for the public site
 * (layouts/site.php). Edit the values here — no rebuild needed unless you
 * introduce new Tailwind classes in a view.
 */
class Site
{
    public const BRAND = 'Frequency Strategy Group';
    public const LEGAL_NAME = 'Frequency Strategy Group, Incorporated';
    public const LEAD = 'Sheena Wright';
    public const EMAIL = 'Sheena.L.Wright@FrequencyStrategyGroup.com';
    public const PHONE = '289-489-4798';
    public const PHONE_E164 = '+12894894798';
    public const WEBSITE = 'https://FrequencyStrategyGroup.com';
    public const WEBSITE_LABEL = 'FrequencyStrategyGroup.com';

    public const BRAND_STATEMENT = ['We listen.', 'We strategize.', 'We connect.', 'We deliver.'];
    public const POSITIONING = 'Equitable strategy. Expert execution. Measurable impact.';

    /** How quickly inquiries are answered — shown on the contact form and page. Confirm before launch. */
    public const RESPONSE_TIME = 'within two business days';

    /**
     * Social profiles — leave empty until each account is confirmed; the
     * footer only shows the ones filled in. Keys: linkedin, instagram,
     * facebook, x, youtube.
     * @var array<string, string>
     */
    public const SOCIALS = [
        // 'linkedin' => 'https://www.linkedin.com/company/...',
    ];

    /** Main navigation. A 'children' key renders a dropdown. */
    public static function nav(): array
    {
        $services = array_map(fn($s) => ['label' => $s['title'], 'path' => 'services/' . $s['slug']], Services::all());

        return [
            ['label' => 'About', 'path' => 'about', 'children' => [
                ['label' => 'About Us', 'path' => 'about', 'note' => 'Who we are and what drives us'],
                ['label' => 'Our Team', 'path' => 'team', 'note' => 'Our lead consultant and network'],
                ['label' => 'Testimonials', 'path' => 'testimonials', 'note' => 'In our clients’ words'],
                ['label' => 'FAQs', 'path' => 'faqs', 'note' => 'Answers to common questions'],
            ]],
            ['label' => 'Services', 'path' => 'services', 'children' => $services],
            ['label' => 'Funding & Resources', 'path' => 'funding-resources'],
            ['label' => 'Projects & Partnerships', 'path' => 'projects'],
            ['label' => 'Contact', 'path' => 'contact'],
        ];
    }

    /**
     * Every public page: path => [title, meta description]. A path listed
     * here renders in the public site layout (see public/index.php) and is
     * included in sitemap.xml.
     * @return array<string, array{0: string, 1: string}>
     */
    public static function pages(): array
    {
        return [
            'home' => [
                'Business, Funding & Healthcare Consulting',
                'Frequency Strategy Group is a Black-owned, women-led multidisciplinary consulting company helping businesses, nonprofits and healthcare organizations with strategy, funding and grant development, and execution.',
            ],
            'about' => [
                'About Us — Mission, Vision & Values',
                'Learn who we are, what our name means and how we work: a multidisciplinary consulting group that listens first, builds the strategy and connects clients with the right expertise.',
            ],
            'services' => [
                'Consulting Services',
                'Funding strategy and grant development, business consulting, nonprofit development, healthcare consulting, disability case management consulting, sports career management and more.',
            ],
            'team' => [
                'Our Team — Lead Consultant Sheena Wright',
                'Meet Sheena Wright, BScN, RN, Lead Consultant at Frequency Strategy Group, and our growing network of consultants and professional partners.',
            ],
            'funding-resources' => [
                'Funding & Grant Resources',
                'Funding strategy, grant development and funding-readiness guidance for businesses, nonprofits and community organizations.',
            ],
            'projects' => [
                'Projects & Partnerships',
                'Collaborations, partnerships and project work from Frequency Strategy Group — and how to partner with us.',
            ],
            'testimonials' => [
                'Client Testimonials',
                'What clients say about working with Frequency Strategy Group.',
            ],
            'faqs' => [
                'Frequently Asked Questions',
                'Answers about who we work with, nonprofit funding, program and clinic development, disability case management consulting and our consulting process.',
            ],
            'contact' => [
                'Contact Us — Book a Consultation',
                'Contact Frequency Strategy Group to book a consultation or discuss a project, funding need, business challenge or consulting requirement.',
            ],
            'privacy' => [
                'Privacy Policy',
                'How Frequency Strategy Group collects, uses and protects personal information submitted through this website.',
            ],
            'terms' => [
                'Terms of Use & Disclaimers',
                'Terms of use for this website, and disclaimers about consulting services, funding outcomes and regulated professional services.',
            ],
        ];
    }

    /** Title + description for a path (services/{slug} included), or null if it isn't a site page. */
    public static function meta(string $path): ?array
    {
        $path = trim($path, '/') ?: 'home';
        if (isset(self::pages()[$path])) {
            return self::pages()[$path];
        }
        if (preg_match('#^services/([a-z0-9-]+)$#', $path, $m) && ($s = Services::find($m[1]))) {
            return [$s['seo_title'] ?? $s['title'], $s['summary']];
        }
        return null;
    }

    /** All URLs for sitemap.xml */
    public static function sitemapPaths(): array
    {
        $paths = array_map(fn($p) => $p === 'home' ? '' : $p, array_keys(self::pages()));
        foreach (Services::all() as $s) {
            $paths[] = 'services/' . $s['slug'];
        }
        return $paths;
    }

    /** Public, canonical origin (APP_URL in .env) without a trailing slash */
    public static function origin(): string
    {
        return rtrim((string) ($_ENV['APP_URL'] ?? self::WEBSITE), '/');
    }
}
