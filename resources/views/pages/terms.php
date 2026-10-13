<?php
// /resources/views/pages/terms.php
//
// Terms of use + professional disclaimers (draft). Have it reviewed by a
// qualified professional before launch.

declare(strict_types=1);

use Src\Content\Site;

$updated = 'October 13, 2026';
$hero = [
    'eyebrow' => 'Legal',
    'title' => 'Terms of Use & <em>Disclaimers.</em>',
    'lead' => 'The terms that apply to your use of this website, and important information about the nature of our consulting services.',
    'crumbs' => [['Terms of Use & Disclaimers', null]],
];
include __DIR__ . '/../components/site/page-hero.php';
?>
<section class="bg-ivory py-20 lg:py-28">
    <div class="site-container">
        <article class="prose-site mx-auto max-w-3xl rounded-[1.75rem] bg-white p-8 shadow-sm sm:p-14">
            <p class="text-sm text-ink-500">Last updated: <?= e($updated) ?></p>
            <p>This website is operated by <?= e(Site::LEGAL_NAME) ?> (“<?= e(Site::BRAND) ?>”, “we”, “us”). By using it, you agree to these terms. If you do not agree, please do not use the website.</p>

            <h2>General information only</h2>
            <p>The content on this website is provided for general information. It is not legal, medical, financial, tax, accounting or other professional advice, and it should not be relied on as a substitute for advice tailored to your circumstances. Using this website or contacting us through it does not create a consulting or other professional relationship; an engagement begins only when we both agree to its terms.</p>

            <h2 id="disclaimers">Consulting disclaimers</h2>
            <h3>Funding outcomes</h3>
            <p>We help clients identify and pursue appropriate funding opportunities. Funding awards are subject to eligibility, each funder’s requirements and priorities, available budgets and the funder’s final decision. We do not and cannot guarantee that any application will be funded.</p>
            <h3>Regulated professional services</h3>
            <p>Our services are consulting, planning, coordination and navigation services. Unless expressly agreed in writing by an appropriately licensed professional, they are not:</p>
            <ul>
                <li>legal advice or legal representation;</li>
                <li>medical diagnosis, treatment or other clinical care;</li>
                <li>financial, investment, tax or accounting advice; or</li>
                <li>any other service that may only be provided by a regulated professional.</li>
            </ul>
            <p>Our disability case management consulting provides guidance, coordination and resource navigation; it does not replace advice from a lawyer, physician or other regulated professional. Where your situation requires such advice, we will encourage you to obtain it.</p>
            <h3>Results</h3>
            <p>Every client’s circumstances are different. Testimonials reflect individual experiences and are not a guarantee of similar results.</p>
            <h3>Affiliated initiatives and partners</h3>
            <p>References to partner or affiliated organizations do not mean that their services are provided by <?= e(Site::BRAND) ?>. Each organization is responsible for its own services.</p>

            <h2>Use of the website</h2>
            <p>You agree not to misuse the website — for example, by attempting to gain unauthorized access, interfering with its operation, or submitting false, unlawful or harmful content through its forms.</p>

            <h2>Intellectual property</h2>
            <p>The website’s content, including text, graphics, the <?= e(Site::BRAND) ?> name and logo, is owned by or licensed to <?= e(Site::LEGAL_NAME) ?> and may not be reproduced without our permission, except for personal, non-commercial use.</p>

            <h2>Links to other websites</h2>
            <p>Links to third-party websites are provided for convenience. We do not control and are not responsible for their content or practices.</p>

            <h2>Limitation of liability</h2>
            <p>The website is provided “as is”. To the extent permitted by law, we are not liable for any loss or damage arising from your use of, or reliance on, the website or its content. We do our best to keep information accurate and current but do not warrant that it is complete or error-free.</p>

            <h2>Accessibility</h2>
            <p>We want our website to be usable by everyone and aim to meet the Web Content Accessibility Guidelines (WCAG) 2.1, Level AA. If you have difficulty accessing any content, please <a data-partial href="<?= site_url('contact') ?>">contact us</a> and we will provide the information in another way.</p>

            <h2>Governing law</h2>
            <p>These terms are governed by the laws of the Province of Ontario and the federal laws of Canada that apply there.</p>

            <h2>Changes</h2>
            <p>We may update these terms from time to time. The date at the top shows when they were last revised.</p>

            <h2>Contact</h2>
            <p>Questions about these terms: <a href="mailto:<?= e(Site::EMAIL) ?>"><?= e(Site::EMAIL) ?></a> or <a href="tel:<?= Site::PHONE_E164 ?>"><?= e(Site::PHONE) ?></a>. See also our <a data-partial href="<?= site_url('privacy') ?>">Privacy Policy</a>.</p>
        </article>
    </div>
</section>
