<?php
// /resources/views/pages/privacy.php
//
// Privacy policy (PIPEDA-oriented draft). Have it reviewed by a qualified
// professional before launch, and update it whenever forms, analytics or
// third-party tools change.

declare(strict_types=1);

use Src\Content\Site;

$updated = 'October 13, 2026';
$hasAnalytics = trim((string) ($_ENV['ANALYTICS_PLAUSIBLE_DOMAIN'] ?? '')) !== '';
$hero = [
    'eyebrow' => 'Privacy',
    'title' => 'Privacy <em>Policy.</em>',
    'lead' => 'How we collect, use and protect the personal information you share with us through this website.',
    'crumbs' => [['Privacy Policy', null]],
];
include __DIR__ . '/../components/site/page-hero.php';
?>
<section class="bg-ivory py-20 lg:py-28">
    <div class="site-container">
        <article class="prose-site mx-auto max-w-3xl rounded-[1.75rem] bg-white p-8 shadow-sm sm:p-14">
            <p class="text-sm text-ink-500">Last updated: <?= e($updated) ?></p>

            <p><?= e(Site::LEGAL_NAME) ?> (“<?= e(Site::BRAND) ?>”, “we”, “us”) respects your privacy. This policy explains how we handle personal information collected through <?= e(Site::WEBSITE_LABEL) ?> (the “website”), consistent with Canada’s <em>Personal Information Protection and Electronic Documents Act</em> (PIPEDA) and other applicable Canadian privacy laws.</p>

            <h2>Information we collect</h2>
            <h3>Information you give us</h3>
            <p>When you submit our contact or consultation form, we collect the information you provide: your name, email address, and optionally your organization, phone number, the service you are interested in, and your message. We also record that you consented to us using this information to respond to you, and when.</p>
            <p>Please do not include sensitive personal information — such as health, financial or disability-related details — in the form. If your inquiry involves sensitive matters, we will discuss the details with you directly.</p>
            <h3>Information collected automatically</h3>
            <ul>
                <li><strong>Server logs.</strong> Like most websites, our hosting provider records technical information such as IP address, browser type and the pages requested, for security and troubleshooting.</li>
                <li><strong>Essential cookie.</strong> When you submit a form, we set a single session cookie used to protect the form from spam and abuse. It is not used for advertising or tracking.</li>
                <?php if ($hasAnalytics): ?>
                    <li><strong>Analytics.</strong> We use privacy-friendly, cookieless analytics to understand how the website is used in aggregate (for example, which pages are visited most). It does not use cookies, does not collect personal information and does not track you across other websites.</li>
                <?php else: ?>
                    <li><strong>Analytics.</strong> If we introduce website analytics, we will use privacy-friendly tools that report on website use in aggregate, and we will update this policy before doing so.</li>
                <?php endif; ?>
            </ul>

            <h2>How we use your information</h2>
            <ul>
                <li>To respond to your inquiry and, if you request it, to arrange a consultation.</li>
                <li>To provide consulting services you engage us for.</li>
                <li>To keep a record of our communications with you.</li>
                <li>To protect the website and our forms against spam, fraud and misuse.</li>
                <li>To meet our legal and regulatory obligations.</li>
            </ul>
            <p>We do not sell, rent or trade your personal information, and we do not add you to marketing mailing lists without your express consent. Any commercial electronic messages we send will comply with Canada’s Anti-Spam Legislation (CASL).</p>

            <h2>Consent</h2>
            <p>By submitting our form and checking the consent box, you consent to us using your information as described in this policy. You may withdraw your consent at any time by contacting us, subject to legal or contractual restrictions; withdrawing consent may limit our ability to respond to you or provide services.</p>

            <h2>Sharing your information</h2>
            <p>We share personal information only when necessary:</p>
            <ul>
                <li><strong>Service providers</strong> who help us operate the website and email (such as hosting and email providers), who are required to protect it and use it only for the services they provide to us. Some providers may store information outside your province or outside Canada, where it may be subject to the laws of that jurisdiction.</li>
                <li><strong>Partner consultants</strong> — only with your consent, where your engagement requires their expertise.</li>
                <li><strong>When required by law</strong>, for example in response to a valid legal request.</li>
            </ul>

            <h2>Retention</h2>
            <p>We keep inquiry information only as long as needed to respond to you and for our reasonable business records, after which it is securely deleted or anonymized.</p>

            <h2>Security</h2>
            <p>We protect personal information with safeguards appropriate to its sensitivity, including encrypted connections (HTTPS), access controls and limiting access to those who need it. No method of transmission or storage is completely secure, but we work to protect your information.</p>

            <h2>Your rights</h2>
            <p>You may ask to access the personal information we hold about you, ask us to correct it, or withdraw your consent. To make a request, contact us using the details below. If you are not satisfied with our response, you may contact the <a href="https://www.priv.gc.ca/" target="_blank" rel="noopener">Office of the Privacy Commissioner of Canada</a>.</p>

            <h2>Links to other websites</h2>
            <p>Our website may link to other websites, including those of partner and affiliated organizations. We are not responsible for their privacy practices — please review their policies.</p>

            <h2>Changes to this policy</h2>
            <p>We may update this policy from time to time. The date at the top shows when it was last revised.</p>

            <h2>Contact us</h2>
            <p>Questions or requests about privacy can be sent to:</p>
            <p><strong><?= e(Site::LEGAL_NAME) ?></strong><br>Attn: Privacy — <?= e(Site::LEAD) ?><br>Email: <a href="mailto:<?= e(Site::EMAIL) ?>"><?= e(Site::EMAIL) ?></a><br>Phone: <a href="tel:<?= Site::PHONE_E164 ?>"><?= e(Site::PHONE) ?></a></p>
        </article>
    </div>
</section>
