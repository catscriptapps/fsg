<?php
// /resources/views/components/site/portrait.php
//
// A consultant's portrait: their photo when one is set ($person['photo'],
// a path under public/ — or, for the lead consultant, the photo in the
// 'team.lead' spot chosen in the admin under Site Photos), otherwise an
// ink-and-gold monogram.

use Src\Content\Icons;
use Src\Content\Photos;
use Src\Content\Team;

$initials = implode('', array_map(fn($w) => mb_substr($w, 0, 1), array_slice(explode(' ', $person['name']), 0, 2)));
$portraitSrc = !empty($person['photo'])
    ? $assetBase . $person['photo']
    : (($person['slug'] ?? '') === Team::lead()['slug'] ? (Photos::get('team.lead')['src'] ?? null) : null);
?>
<div class="relative aspect-[4/5] w-full overflow-hidden rounded-[1.75rem] bg-ink-950 shadow-2xl shadow-ink-950/25">
    <?php if ($portraitSrc): ?>
        <img src="<?= e($portraitSrc) ?>" alt="<?= e($person['name'] . ', ' . $person['role']) ?>" class="h-full w-full object-cover" loading="lazy">
    <?php else: ?>
        <div class="ink-surface absolute inset-0"></div>
        <div aria-hidden="true" class="absolute inset-x-0 bottom-0 opacity-20"><?= Icons::waveform('w-full') ?></div>
        <div class="absolute inset-0 flex items-center justify-center">
            <div class="flex h-44 w-44 items-center justify-center rounded-full border border-gold-400/50 shadow-[0_0_80px_-10px_rgba(202,156,106,0.5)]">
                <span class="gold-text font-serif text-7xl font-medium tracking-wide" role="img" aria-label="<?= e($person['name']) ?>"><?= e($initials) ?></span>
            </div>
        </div>
    <?php endif; ?>
    <div aria-hidden="true" class="pointer-events-none absolute inset-3 rounded-[1.4rem] border border-gold-300/20"></div>
</div>
