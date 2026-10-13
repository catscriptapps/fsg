<?php
// /resources/views/layouts/site.php

declare(strict_types=1);

/**
 * Public website layout — top navigation, page, footer. Used for every
 * page listed in Src\Content\Site::pages() (plus services/{slug} and the
 * 404), chosen in public/index.php. The admin area keeps layouts/app.php.
 *
 * Expects: $pageFile, $sitePath, $siteMeta [title, description], $baseUrl,
 * $assetBase, $isLoggedIn.
 */

use Src\Content\Site;

[$metaTitle, $metaDescription] = $siteMeta;
$fullTitle = $metaTitle . ' | ' . Site::BRAND; // same format the SPA router sets on partial loads
$canonical = Site::origin() . '/' . ($sitePath === 'home' ? '' : $sitePath);
$ogImage = Site::origin() . '/images/logo/og-image.jpg';
$is404 = http_response_code() === 404;

// Render the page before the layout (the header needs nothing from it, but
// output buffering keeps any page-level header() calls working)
ob_start();
include $pageFile;
$pageHtml = ob_get_clean();

$jsonLd = [
    '@context' => 'https://schema.org',
    '@type' => 'ProfessionalService',
    'name' => Site::BRAND,
    'legalName' => Site::LEGAL_NAME,
    'url' => Site::WEBSITE,
    'email' => Site::EMAIL,
    'telephone' => Site::PHONE_E164,
    'logo' => Site::origin() . '/images/logo/icon-512.png',
    'image' => $ogImage,
    'slogan' => Site::POSITIONING,
    'description' => Site::pages()['home'][1],
    'founder' => ['@type' => 'Person', 'name' => Site::LEAD, 'jobTitle' => 'Lead Consultant'],
];
$analyticsDomain = trim((string) ($_ENV['ANALYTICS_PLAUSIBLE_DOMAIN'] ?? ''));
?>
<!DOCTYPE html>
<html lang="en-CA" class="scroll-smooth">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script>
        // Reveal-on-scroll hides content until site.js runs; if it never does, show everything
        document.documentElement.classList.add('js');
        setTimeout(function () { if (!window.__siteReady) document.documentElement.classList.remove('js'); }, 2500);
    </script>
    <title><?= e($fullTitle) ?></title>
    <meta name="description" content="<?= e($metaDescription) ?>">
    <?php if ($is404): ?>
        <meta name="robots" content="noindex">
    <?php else: ?>
        <link rel="canonical" href="<?= e($canonical) ?>">
    <?php endif; ?>

    <meta property="og:type" content="website">
    <meta property="og:site_name" content="<?= e(Site::BRAND) ?>">
    <meta property="og:title" content="<?= e($fullTitle) ?>">
    <meta property="og:description" content="<?= e($metaDescription) ?>">
    <meta property="og:url" content="<?= e($canonical) ?>">
    <meta property="og:image" content="<?= e($ogImage) ?>">
    <meta property="og:locale" content="en_CA">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="theme-color" content="#0b0a08">

    <link rel="icon" href="<?= $assetBase ?>images/logo/favicon.ico" sizes="48x48">
    <link rel="icon" type="image/png" sizes="32x32" href="<?= $assetBase ?>images/logo/favicon-32.png">
    <link rel="apple-touch-icon" href="<?= $assetBase ?>images/logo/apple-touch-icon.png">
    <link rel="manifest" href="<?= $assetBase ?>site.webmanifest">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,500;0,600;1,500;1,600&family=Manrope:wght@400;500;600;700&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="<?= $assetBase ?>assets/css/app.min.css?v=<?= assetVersion('assets/css/app.min.css') ?>">

    <script>
        // layout: 'site' tells the SPA router (and the server) which shell is loaded
        window.APP_CONFIG = { baseUrl: <?= json_encode($baseUrl) ?>, assetBase: <?= json_encode($assetBase) ?>, appName: <?= json_encode(Site::BRAND) ?>, layout: 'site' };
    </script>
    <script type="application/ld+json"><?= json_encode($jsonLd, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) ?></script>
    <?php if ($analyticsDomain !== ''): ?>
        <!-- Privacy-friendly, cookieless analytics (see /privacy) -->
        <script defer data-domain="<?= e($analyticsDomain) ?>" src="https://plausible.io/js/script.js"></script>
    <?php endif; ?>
</head>

<body class="bg-ivory font-sans text-ink-900 antialiased selection:bg-gold-300/60 selection:text-ink-950">
    <a href="#main" class="sr-only focus:not-sr-only focus:fixed focus:left-4 focus:top-4 focus:z-[100] focus:rounded-full focus:bg-gold-300 focus:px-5 focus:py-2.5 focus:text-sm focus:font-semibold focus:text-ink-950">Skip to main content</a>

    <?php include __DIR__ . '/../partials/site-header.php'; ?>

    <main id="main">
        <!-- Swapped by the SPA router (resources/js/utils/spa-router.js) -->
        <div id="main-content">
            <?= $pageHtml ?>
        </div>
    </main>

    <?php include __DIR__ . '/../partials/site-footer.php'; ?>

    <?php include __DIR__ . '/../components/scroll-top.php'; ?>

    <script type="module" src="<?= $assetBase ?>assets/js/site.min.js?v=<?= assetVersion('assets/js/site.min.js') ?>"></script>
</body>

</html>
