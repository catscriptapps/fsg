<?php
// /resources/views/components/live-feed/view-post-modal.php
//
// The Live Feed's post window — the post (copied from its card) with its
// comments. Signed-in staff get the comment box; guests read the comments
// and see a sign-in note instead. Driven by utils/live-feed/view-post.js.
//
// @var bool $canPost (pages/live-feed.php)

declare(strict_types=1);

use Src\Content\Icons;
?>
<div id="live-post-modal" class="fixed inset-0 z-[60] hidden" role="dialog" aria-modal="true" aria-labelledby="live-post-author">
    <div data-close-post class="absolute inset-0 bg-ink-950/70 backdrop-blur-sm"></div>

    <div class="pointer-events-none relative flex h-full items-end justify-center sm:items-center sm:p-6">
        <div class="pointer-events-auto flex max-h-[92vh] w-full flex-col overflow-hidden rounded-t-3xl bg-white shadow-2xl ring-1 ring-ink-900/10 sm:max-w-2xl sm:rounded-3xl">

            <div class="flex items-center justify-between gap-3 border-b border-ink-900/[0.06] px-6 py-4">
                <div class="flex min-w-0 items-center gap-3">
                    <span id="live-post-avatar" class="flex h-11 w-11 flex-shrink-0 overflow-hidden rounded-full"></span>
                    <div class="min-w-0">
                        <h2 id="live-post-author" class="truncate font-semibold text-ink-950"></h2>
                        <p id="live-post-time" class="text-xs text-ink-500"></p>
                    </div>
                </div>
                <button type="button" data-close-post aria-label="Close" class="rounded-xl p-2 text-ink-400 transition-colors hover:bg-ivory hover:text-ink-800">
                    <?= Icons::svg('close', 'h-5 w-5') ?>
                </button>
            </div>

            <div class="custom-scrollbar flex-1 overflow-y-auto">
                <div id="live-post-content" class="break-words px-6 pt-5 text-base leading-relaxed text-ink-800 empty:hidden"></div>
                <div id="live-post-media" class="mt-4 flex hidden justify-center bg-ink-950"></div>

                <div class="flex items-center gap-5 px-6 pb-3 pt-4 text-xs font-semibold uppercase tracking-widest text-ink-500">
                    <span class="inline-flex items-center gap-1.5"><?= Icons::svg('heart', 'h-4 w-4 text-rose-500') ?><span id="live-post-likes">0</span><span class="sr-only"> likes</span></span>
                    <span class="inline-flex items-center gap-1.5"><?= Icons::svg('comment', 'h-4 w-4 text-gold-700') ?><span id="live-post-comments-count">0</span><span class="sr-only"> comments</span></span>
                </div>

                <div id="live-post-comments" class="space-y-4 border-t border-ink-900/[0.06] px-6 py-5"></div>
            </div>

            <div class="border-t border-ink-900/[0.06] px-4 py-3">
                <?php if (!empty($canPost)): ?>
                    <div class="flex items-end gap-2">
                        <div class="relative flex-1">
                            <textarea id="live-post-comment-input" rows="1" maxlength="2000" placeholder="Write a comment…" aria-label="Write a comment"
                                class="block max-h-32 w-full resize-none rounded-2xl border-0 bg-ivory-deep py-3 pl-4 pr-11 text-sm text-ink-950 outline-none placeholder:text-ink-400 focus:ring-2 focus:ring-gold-500/40"></textarea>
                            <button type="button" id="live-post-emoji" aria-label="Add an emoji" class="absolute bottom-2.5 right-2.5 rounded-lg p-1 text-ink-400 transition-colors hover:text-gold-700">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                            </button>
                        </div>
                        <button type="button" id="live-post-send" aria-label="Post comment"
                            class="flex h-11 w-11 flex-shrink-0 items-center justify-center rounded-full bg-ink-950 text-gold-200 shadow-sm transition-all hover:bg-ink-800 active:scale-95 disabled:opacity-50">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M6 12L3.269 3.126A59.768 59.768 0 0121.485 12 59.77 59.77 0 013.27 20.876L5.999 12zm0 0h7.5" /></svg>
                        </button>
                    </div>
                <?php else: ?>
                    <p class="px-2 py-1.5 text-center text-sm text-ink-500">Comments are from our team. Team member? <a href="<?= site_url('dashboard?login') ?>" data-login-button rel="nofollow" class="font-semibold text-gold-800 underline decoration-gold-500/40 underline-offset-4">Sign in</a> to join in.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
