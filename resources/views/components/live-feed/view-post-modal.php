<?php
// /resources/views/components/live-feed/view-post-modal.php
//
// The Live Feed's post window — the post (copied from its card) with its
// comments and a comment box. Driven by utils/live-feed/view-post.js.

declare(strict_types=1);
?>
<div id="live-post-modal" class="fixed inset-0 z-[60] hidden" role="dialog" aria-modal="true" aria-labelledby="live-post-author">
    <div data-close-post class="absolute inset-0 bg-gray-950/60 backdrop-blur-sm"></div>

    <div class="relative flex h-full items-end sm:items-center justify-center sm:p-6 pointer-events-none">
        <div class="pointer-events-auto flex w-full max-h-[92vh] sm:max-w-2xl flex-col overflow-hidden rounded-t-3xl sm:rounded-3xl bg-white dark:bg-gray-900 shadow-2xl ring-1 ring-black/5 dark:ring-white/10">

            <div class="flex items-center justify-between gap-3 border-b border-gray-100 dark:border-gray-800 px-5 py-4">
                <div class="flex items-center gap-3 min-w-0">
                    <span id="live-post-avatar" class="flex h-10 w-10 flex-shrink-0 overflow-hidden rounded-full"></span>
                    <div class="min-w-0">
                        <h2 id="live-post-author" class="text-sm font-bold text-gray-900 dark:text-white truncate"></h2>
                        <p id="live-post-time" class="text-xs text-gray-500 dark:text-gray-400"></p>
                    </div>
                </div>
                <button type="button" data-close-post aria-label="Close" class="rounded-xl p-2 text-gray-400 hover:bg-gray-100 hover:text-gray-700 dark:hover:bg-gray-800 dark:hover:text-gray-200 transition-colors">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </div>

            <div class="flex-1 overflow-y-auto custom-scrollbar">
                <div id="live-post-content" class="px-5 pt-4 text-[15px] leading-relaxed text-gray-800 dark:text-gray-200 break-words empty:hidden"></div>
                <div id="live-post-media" class="mt-4 hidden bg-black flex justify-center"></div>

                <div class="flex items-center gap-4 px-5 pt-4 pb-3 text-xs font-semibold text-gray-500 dark:text-gray-400">
                    <span><span id="live-post-likes">0</span> likes</span>
                    <span><span id="live-post-comments-count">0</span> comments</span>
                </div>

                <div id="live-post-comments" class="space-y-4 border-t border-gray-100 dark:border-gray-800 px-5 py-4"></div>
            </div>

            <div class="border-t border-gray-100 dark:border-gray-800 px-4 py-3">
                <div class="flex items-end gap-2">
                    <div class="relative flex-1">
                        <textarea id="live-post-comment-input" rows="1" maxlength="2000" placeholder="Write a comment…" aria-label="Write a comment"
                            class="block max-h-32 w-full resize-none rounded-2xl border-0 bg-gray-100 dark:bg-gray-800 py-2.5 pl-4 pr-11 text-sm text-gray-900 dark:text-white placeholder-gray-400 focus:ring-2 focus:ring-primary-500/40 outline-none"></textarea>
                        <button type="button" id="live-post-emoji" aria-label="Add an emoji" class="absolute right-2.5 bottom-2 rounded-lg p-1 text-gray-400 hover:text-primary-500 transition-colors">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        </button>
                    </div>
                    <button type="button" id="live-post-send" aria-label="Post comment"
                        class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-full bg-primary-600 text-white shadow-sm hover:bg-primary-500 active:scale-95 transition-all disabled:opacity-50">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 12L3.269 3.126A59.768 59.768 0 0121.485 12 59.77 59.77 0 013.27 20.876L5.999 12zm0 0h7.5" /></svg>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
