// /resources/js/utils/live-feed/view-post.js
//
// The post window (#live-post-modal): opened from a post's comment button
// or its photo. The post itself is copied from its card (instant); the
// comments are fetched. Comments can be added (Enter sends, Shift+Enter is
// a new line) and deleted by their author or an admin; the card's comment
// count follows along. Escape or the backdrop closes it.

import { showToast } from '../../ui/toast.js';
import { confirmDialog } from '../../ui/confirm.js';
import { initEmojiPicker } from './emoji-picker.js';

const base = () => window.APP_CONFIG?.baseUrl || '/';
const $ = (id) => document.getElementById(id);

let activePostId = null;
let bound = false;

function open(card) {
  const modal = $('live-post-modal');
  if (!card || !modal) return;
  activePostId = card.dataset.postId;

  $('live-post-avatar').innerHTML = card.querySelector('.post-author-avatar')?.outerHTML || '';
  $('live-post-author').textContent = card.querySelector('.post-author-name')?.textContent.trim() || 'Member';
  const time = card.querySelector('.post-time-ago');
  $('live-post-time').textContent = time ? `${time.textContent.trim()} · ${time.title}` : '';
  $('live-post-content').innerHTML = card.querySelector('.post-content')?.innerHTML || '';

  const media = card.querySelector('.post-main-media');
  const box = $('live-post-media');
  box.classList.toggle('hidden', !media);
  box.innerHTML = '';
  if (media) {
    const copy = media.cloneNode(true);
    copy.classList.remove('post-media-trigger', 'cursor-zoom-in', 'max-h-[32rem]');
    copy.classList.add('max-h-[60vh]');
    box.appendChild(copy);
  }

  $('live-post-likes').textContent = card.querySelector('.like-count')?.textContent || '0';
  $('live-post-comments-count').textContent = card.querySelector('.comment-count')?.textContent || '0';
  $('live-post-comments').innerHTML = '<div class="flex justify-center py-6"><div class="h-5 w-5 animate-spin rounded-full border-2 border-primary-500 border-t-transparent"></div></div>';

  modal.classList.remove('hidden');
  document.body.style.overflow = 'hidden';
  loadComments(activePostId);
  setTimeout(() => $('live-post-comment-input')?.focus(), 50);
}

function close() {
  const modal = $('live-post-modal');
  if (!modal || modal.classList.contains('hidden')) return;
  modal.querySelectorAll('video').forEach((v) => v.pause());
  modal.classList.add('hidden');
  document.body.style.overflow = '';
  activePostId = null;
}

const noComments = '<p data-no-comments class="py-4 text-center text-sm text-gray-400">No comments yet — start the conversation.</p>';

async function loadComments(postId) {
  try {
    const res = await fetch(`${base()}api/live-feed?action=get-details&post_id=${encodeURIComponent(postId)}`, { cache: 'no-store' });
    const json = await res.json();
    if (postId !== activePostId) return;
    if (!json.success) throw new Error(json.message);
    $('live-post-comments').innerHTML = json.html || noComments;
    $('live-post-likes').textContent = json.likes;
    $('live-post-comments-count').textContent = json.comments_count;
    setCardCount(postId, json.comments_count);
  } catch (err) {
    if (postId === activePostId) $('live-post-comments').innerHTML = `<p class="py-4 text-center text-sm text-gray-400">${err.message || 'Couldn’t load the comments.'}</p>`;
  }
}

function setCardCount(postId, n) {
  const el = document.querySelector(`#live-feed-posts [data-post-id="${CSS.escape(postId)}"] .comment-count`);
  if (el) el.textContent = Math.max(0, n);
}

function bumpCount(delta) {
  const el = $('live-post-comments-count');
  const n = Math.max(0, (parseInt(el.textContent, 10) || 0) + delta);
  el.textContent = n;
  setCardCount(activePostId, n);
}

async function send() {
  const input = $('live-post-comment-input');
  const btn = $('live-post-send');
  const text = input.value.trim();
  if (!text || !activePostId) return;

  btn.disabled = true;
  try {
    const res = await fetch(`${base()}api/live-feed`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ action: 'add-comment', post_id: activePostId, content: text }),
    });
    const json = await res.json();
    if (!json.success) throw new Error(json.message);
    const listEl = $('live-post-comments');
    listEl.querySelector('[data-no-comments]')?.remove();
    listEl.insertAdjacentHTML('beforeend', json.commentHtml);
    listEl.lastElementChild?.scrollIntoView({ block: 'nearest', behavior: 'smooth' });
    input.value = '';
    autosize(input);
    bumpCount(1);
  } catch (err) {
    showToast(err.message || 'Could not post the comment.', 'error');
  } finally {
    btn.disabled = false;
  }
}

async function deleteComment(btn) {
  if (!await confirmDialog('Delete this comment?', 'Delete', 'Cancel', 'bg-red-600 hover:bg-red-700')) return;
  try {
    const res = await fetch(`${base()}api/live-feed`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ action: 'delete-comment', comment_id: btn.dataset.deleteComment }),
    });
    const json = await res.json();
    if (!json.success) throw new Error(json.message);
    btn.closest('[data-comment-id]')?.remove();
    if (!$('live-post-comments').children.length) $('live-post-comments').innerHTML = noComments;
    bumpCount(-1);
  } catch (err) {
    showToast(err.message || 'Could not delete the comment.', 'error');
  }
}

function autosize(el) {
  el.style.height = 'auto';
  el.style.height = `${Math.min(el.scrollHeight, 128)}px`;
}

export function initViewPost() {
  // The window is part of the page, so its own controls are wired each visit
  const input = $('live-post-comment-input');
  if (input && !input.dataset.bound) {
    input.dataset.bound = '1';
    initEmojiPicker($('live-post-emoji'), input);
    input.addEventListener('input', () => autosize(input));
    input.addEventListener('keydown', (e) => {
      if (e.key === 'Enter' && !e.shiftKey) { e.preventDefault(); send(); }
    });
    $('live-post-send').addEventListener('click', send);
  }

  if (bound) return;
  bound = true;
  document.addEventListener('click', (e) => {
    const trigger = e.target.closest('#live-feed-posts .view-comments-btn, #live-feed-posts .post-media-trigger');
    if (trigger) return open(trigger.closest('[data-post-id]'));
    if (e.target.closest('#live-post-modal [data-close-post]')) return close();
    const del = e.target.closest('#live-post-comments [data-delete-comment]');
    if (del) deleteComment(del);
  });
  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape' && !document.querySelector('.emoji-picker-popover')) close();
  });
}

/** Leaving the page with the window open shouldn't leave scrolling locked. */
export function closeViewPost() {
  close();
}
