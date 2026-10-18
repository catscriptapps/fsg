// /resources/js/utils/live-feed/compose.js
//
// The Live Feed composer: a window with text and one optional photo or
// video. Photos go through the shared upload modal (compressed in the
// browser, api/live-feed-photo); videos through the chunked video modal
// (api/live-feed-video). Posting returns the new card, which is put at the
// top of the feed straight away.

import { Modal } from '../../factories/modal-factory.js';
import { uploadModal, createUploadHandler } from '../../modals/upload-modal.js';
import { videoUploadModal, createVideoUploadHandler } from '../../modals/video-upload-modal.js';
import { showToast } from '../../ui/toast.js';
import { escapeHtml } from '../escape-html.js';
import { initEmojiPicker } from './emoji-picker.js';
import { addPostCard } from './posts.js';

const base = () => window.APP_CONFIG?.baseUrl || '/';
const MAX_POST = 5000;

let modal = null;
let media = null; // { url, type: 'image' | 'video' }

function build() {
  if (modal && document.getElementById('compose-post-modal')) return modal;

  modal = new Modal({
    id: 'compose-post-modal',
    title: 'New post',
    size: 'md',
    showFooter: true,
    content: `
      <form id="compose-post-form" class="space-y-4" novalidate>
        <div class="relative">
          <textarea id="compose-post-content" rows="5" maxlength="${MAX_POST}" placeholder="What would you like to share?"
            class="block w-full resize-none rounded-2xl border-0 bg-gray-100 dark:bg-gray-800 py-3 pl-4 pr-12 text-[15px] text-gray-900 dark:text-white placeholder-gray-400 focus:ring-2 focus:ring-primary-500/40 outline-none"></textarea>
          <button type="button" id="compose-emoji-btn" aria-label="Add an emoji" class="absolute right-3 top-3 rounded-lg p-1 text-gray-400 hover:text-primary-500 transition-colors">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
          </button>
          <span id="compose-post-count" class="absolute right-4 bottom-2.5 text-[11px] text-gray-400"></span>
        </div>

        <div id="compose-media-preview" class="hidden relative overflow-hidden rounded-2xl bg-black ring-1 ring-black/5"></div>

        <div class="flex items-center gap-2">
          <button type="button" id="compose-add-photo" class="inline-flex items-center gap-2 rounded-xl px-3.5 py-2 text-sm font-semibold text-gray-600 dark:text-gray-300 bg-gray-50 hover:bg-gray-100 dark:bg-gray-800 dark:hover:bg-gray-700 transition-colors">
            <svg class="h-5 w-5 text-emerald-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
            Photo
          </button>
          <button type="button" id="compose-add-video" class="inline-flex items-center gap-2 rounded-xl px-3.5 py-2 text-sm font-semibold text-gray-600 dark:text-gray-300 bg-gray-50 hover:bg-gray-100 dark:bg-gray-800 dark:hover:bg-gray-700 transition-colors">
            <svg class="h-5 w-5 text-rose-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" /></svg>
            Video
          </button>
          <span class="ml-auto text-xs text-gray-400">One photo or video per post</span>
        </div>

        <div id="compose-post-message" aria-live="polite"></div>
      </form>`,
    footerButtons: [
      { id: 'compose-post-submit', text: 'Post', classes: 'px-6 py-2.5 text-sm font-bold rounded-xl bg-primary-600 hover:bg-primary-500 text-white shadow-sm shadow-primary-600/30 transition-colors disabled:opacity-60' },
    ],
  });

  const textarea = document.getElementById('compose-post-content');
  const counter = document.getElementById('compose-post-count');
  initEmojiPicker(document.getElementById('compose-emoji-btn'), textarea);
  textarea.addEventListener('input', () => {
    const left = MAX_POST - textarea.value.length;
    counter.textContent = left < 300 ? String(left) : '';
  });

  document.getElementById('compose-add-photo').addEventListener('click', pickPhoto);
  document.getElementById('compose-add-video').addEventListener('click', pickVideo);
  document.getElementById('compose-post-form').addEventListener('submit', (e) => e.preventDefault());
  document.getElementById('compose-post-submit').addEventListener('click', submit);
  textarea.addEventListener('keydown', (e) => {
    if (e.key === 'Enter' && (e.ctrlKey || e.metaKey)) { e.preventDefault(); submit(); }
  });

  return modal;
}

function pickPhoto() {
  uploadModal.open();
  setTimeout(() => {
    createUploadHandler(`${base()}api/live-feed-photo`, 'live-feed-photo', (files) => {
      if (files?.[0]?.url) { media = { url: files[0].url, type: 'image' }; renderPreview(); }
    }, 1, true, { single: true, maxFiles: 1 });
  }, 50);
}

function pickVideo() {
  videoUploadModal.open();
  setTimeout(() => {
    createVideoUploadHandler(`${base()}api/live-feed-video`, (files) => {
      if (files?.[0]?.url) { media = { url: files[0].url, type: 'video' }; renderPreview(); }
    });
  }, 50);
}

function renderPreview() {
  const box = document.getElementById('compose-media-preview');
  if (!box) return;
  if (!media) { box.classList.add('hidden'); box.innerHTML = ''; return; }

  box.classList.remove('hidden');
  const src = escapeHtml(media.url);
  box.innerHTML = media.type === 'image'
    ? `<img src="${src}" alt="" class="max-h-72 w-full object-contain">`
    : `<video src="${src}" class="max-h-72 w-full" controls playsinline></video>`;

  const remove = document.createElement('button');
  remove.type = 'button';
  remove.setAttribute('aria-label', 'Remove');
  remove.className = 'absolute top-2 right-2 flex h-8 w-8 items-center justify-center rounded-full bg-black/60 text-white hover:bg-black/80 backdrop-blur-sm';
  remove.innerHTML = '<svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>';
  remove.addEventListener('click', () => { media = null; renderPreview(); });
  box.appendChild(remove);
}

async function submit() {
  const textarea = document.getElementById('compose-post-content');
  const message = document.getElementById('compose-post-message');
  const btn = document.getElementById('compose-post-submit');
  const content = textarea.value.trim();

  if (content === '' && !media) {
    message.innerHTML = '<p class="text-sm text-red-600 dark:text-red-400">Write something, or add a photo or video.</p>';
    return;
  }

  btn.disabled = true;
  btn.textContent = 'Posting…';
  message.innerHTML = '';

  try {
    const res = await fetch(`${base()}api/live-feed`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ content, media_url: media?.url || '', media_type: media?.type || 'none' }),
    });
    const json = await res.json();
    if (!json.success) {
      message.innerHTML = `<p class="text-sm text-red-600 dark:text-red-400">${escapeHtml((json.errors || [json.message || 'Please try again.']).join(' '))}</p>`;
      return;
    }
    addPostCard(json.html);
    textarea.value = '';
    media = null;
    renderPreview();
    modal.close();
    showToast('Posted to the Live Feed', 'success');
  } catch {
    message.innerHTML = '<p class="text-sm text-red-600 dark:text-red-400">Could not reach the server — please try again.</p>';
  } finally {
    btn.disabled = false;
    btn.textContent = 'Post';
  }
}

/** Opens the composer; `start` = 'photo' | 'video' jumps straight to picking one. */
export function openComposer(start = 'text') {
  build().open();
  setTimeout(() => {
    if (start === 'photo') pickPhoto();
    else if (start === 'video') pickVideo();
    else document.getElementById('compose-post-content')?.focus();
  }, 150);
}
