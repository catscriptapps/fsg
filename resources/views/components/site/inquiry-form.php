<?php
// /resources/views/components/site/inquiry-form.php
//
// The inquiry / consultation form (home + contact page). site.js validates
// and posts it to api/contact (MessagesController::submitContact); the
// message lands in the admin Messages inbox and admins are emailed.
// Preselects from ?type=consultation|inquiry|partnership and ?service={slug}.

use Src\Content\Icons;
use Src\Content\Services;
use Src\Content\Site;

$formId = $formId ?? 'inquiry';
$types = [
    'consultation' => 'Request a consultation',
    'inquiry' => 'General inquiry',
    'partnership' => 'Partner with us',
];
$type = $_GET['type'] ?? 'consultation';
$type = isset($types[$type]) ? $type : 'consultation';
$service = (string) ($_GET['service'] ?? '');
if ($service !== 'general' && !Services::find($service)) {
    $service = '';
}
?>
<div data-inquiry class="relative">
    <form id="<?= e($formId) ?>" data-inquiry-form novalidate class="space-y-6">
        <!-- Spam protection: a field people never see (bots fill it) -->
        <div aria-hidden="true" class="absolute -left-[10000px] h-px w-px overflow-hidden">
            <label>Leave this empty <input type="text" name="website" tabindex="-1" autocomplete="off"></label>
        </div>

        <fieldset>
            <legend class="field-label">How can we help?</legend>
            <div class="grid gap-2 sm:grid-cols-3">
                <?php foreach ($types as $value => $label): ?>
                    <label class="group relative flex cursor-pointer items-center gap-3 rounded-xl border border-ink-900/15 bg-white px-4 py-3 text-sm font-medium text-ink-700 transition has-[:checked]:border-gold-600 has-[:checked]:bg-gold-50 has-[:checked]:text-ink-950 has-[:focus-visible]:ring-4 has-[:focus-visible]:ring-gold-500/20">
                        <input type="radio" name="inquiry_type" value="<?= $value ?>" class="peer sr-only" <?= $value === $type ? 'checked' : '' ?>>
                        <span class="flex h-4 w-4 flex-shrink-0 items-center justify-center rounded-full border border-ink-300 peer-checked:border-gold-700 peer-checked:bg-gold-700 peer-checked:shadow-[inset_0_0_0_3px_white]"></span>
                        <?= e($label) ?>
                    </label>
                <?php endforeach; ?>
            </div>
        </fieldset>

        <div class="grid gap-5 sm:grid-cols-2">
            <div>
                <label for="<?= $formId ?>-name" class="field-label">Full name <span class="text-gold-700">*</span></label>
                <input id="<?= $formId ?>-name" name="full_name" type="text" autocomplete="name" maxlength="120" required class="field">
            </div>
            <div>
                <label for="<?= $formId ?>-email" class="field-label">Email <span class="text-gold-700">*</span></label>
                <input id="<?= $formId ?>-email" name="email" type="email" autocomplete="email" maxlength="255" required class="field">
            </div>
            <div>
                <label for="<?= $formId ?>-org" class="field-label">Organization <span class="font-normal normal-case tracking-normal text-ink-400">(optional)</span></label>
                <input id="<?= $formId ?>-org" name="organization" type="text" autocomplete="organization" maxlength="160" class="field">
            </div>
            <div>
                <label for="<?= $formId ?>-phone" class="field-label">Phone <span class="font-normal normal-case tracking-normal text-ink-400">(optional)</span></label>
                <input id="<?= $formId ?>-phone" name="phone" type="tel" autocomplete="tel" maxlength="40" class="field">
            </div>
        </div>

        <div>
            <label for="<?= $formId ?>-service" class="field-label">Service of interest</label>
            <div class="relative">
                <select id="<?= $formId ?>-service" name="service" class="field appearance-none pr-12">
                    <option value="" <?= $service === '' ? 'selected' : '' ?>>Select a service…</option>
                    <?php foreach (Services::all() as $s): ?>
                        <option value="<?= e($s['slug']) ?>" <?= $service === $s['slug'] ? 'selected' : '' ?>><?= e($s['title']) ?></option>
                    <?php endforeach; ?>
                    <option value="general" <?= $service === 'general' ? 'selected' : '' ?>>Not sure / general inquiry</option>
                </select>
                <span class="pointer-events-none absolute inset-y-0 right-4 flex items-center text-gold-700"><?= Icons::svg('chevron', 'h-4 w-4') ?></span>
            </div>
        </div>

        <div>
            <label for="<?= $formId ?>-message" class="field-label">Tell us about your project or need <span class="text-gold-700">*</span></label>
            <textarea id="<?= $formId ?>-message" name="message" rows="5" maxlength="5000" required class="field resize-y" placeholder="Where are you now, where do you want to go, and what’s standing in the way?"></textarea>
        </div>

        <label class="flex items-start gap-3 text-sm leading-relaxed text-ink-600">
            <input type="checkbox" name="consent" value="1" required class="mt-1 h-4 w-4 flex-shrink-0 rounded border-ink-300 text-gold-700 accent-[#8e6a45] focus:ring-gold-500">
            <span>I consent to Frequency Strategy Group using the information I’ve provided to respond to my inquiry, as described in the <a data-partial href="<?= site_url('privacy') ?>" class="font-semibold text-gold-800 underline decoration-gold-500/40 underline-offset-4">Privacy Policy</a>. <span class="text-gold-700">*</span></span>
        </label>

        <div data-errors class="space-y-2" aria-live="polite"></div>

        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <p class="flex items-center gap-2 text-xs text-ink-500"><?= Icons::svg('lock', 'h-4 w-4 text-gold-700') ?> Your information is kept confidential.</p>
            <button type="submit" class="btn-ink">
                <span data-label>Send inquiry</span> <?= Icons::svg('arrow', 'h-4 w-4') ?>
            </button>
        </div>
    </form>

    <!-- Confirmation (shown by site.js after a successful send) -->
    <div data-inquiry-done hidden tabindex="-1" class="rounded-2xl border border-gold-500/30 bg-gradient-to-br from-gold-50 to-white px-8 py-12 text-center focus:outline-none">
        <span class="mx-auto flex h-16 w-16 items-center justify-center rounded-full border border-gold-500/40 bg-white text-gold-700 shadow-sm"><?= Icons::svg('check', 'h-7 w-7') ?></span>
        <h3 class="display mt-6 text-3xl">Thank you — we’ve received your inquiry.</h3>
        <p data-done-text class="mx-auto mt-3 max-w-md text-ink-600"></p>
        <p class="mx-auto mt-3 max-w-md text-sm text-ink-500">We aim to respond <?= e(Site::RESPONSE_TIME) ?>. If your matter is time-sensitive, please call <a href="tel:<?= Site::PHONE_E164 ?>" class="font-semibold text-gold-800"><?= e(Site::PHONE) ?></a>.</p>
    </div>
</div>
