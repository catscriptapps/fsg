// /resources/js/utils/live-feed/posts.js
//
// The Live Feed's list of posts (#live-feed-posts): "Load older posts",
// likes, the ⋯ menu and deleting — and keeping it live (for guests too —
// reading is public; the like / delete buttons only exist for signed-in staff):
//
//   - every POLL_MS (and on coming back to the tab) it fetches the first
//     page; the server's `sig` (posts + like / comment counts) says whether
//     anything really changed, so it never redraws just because a
//     "5 minutes ago" ticked over;
//   - likes and comments update in place;
//   - new posts appear on their own only when that can't pull the page out
//     from under you (near the top, nothing open, no older pages loaded) —
//     otherwise a "↑ N new posts" button offers them;
//   - it skips a round while you're busy (typing, a window or menu open, a
//     video playing).

import { showToast } from '../../ui/toast.js';
import { confirmDialog } from '../../ui/confirm.js';

const POLL_MS = 30 * 1000;
const NEAR_TOP_PX = 240;

const base = () => window.APP_CONFIG?.baseUrl || '/';
const page = () => document.getElementById('live-feed-page');
const list = () => document.getElementById('live-feed-posts');

let timer = null;
let lastCheck = 0;
let request = 0;
let pending = null; // { html, sig } waiting behind the "new posts" button
let olderLoaded = false;

// ------------------------------------------------------------
// Fetching
// ------------------------------------------------------------

async function fetchPage(n = 1) {
  const res = await fetch(`${base()}api/live-feed?page=${n}`, { cache: 'no-store' });
  const json = await res.json();
  if (!json.success) throw new Error(json.message || 'Could not load the feed');
  return json;
}

const ids = (root) => [...root.querySelectorAll(':scope > [data-post-id]')].map((el) => el.dataset.postId);

const emptyState = () => page()?.dataset.empty || '';

/** Replace the list with page 1 (and reset paging). */
function apply(json) {
  const l = list();
  const p = page();
  if (!l || !p) return;
  l.innerHTML = json.html || emptyState();
  p.dataset.sig = json.sig;
  p.dataset.nextPage = json.next_page;
  page().querySelector('[data-load-more]')?.classList.toggle('hidden', !json.has_more);
  olderLoaded = false;
  pending = null;
  hidePill();
}

/** Like / comment counts from a fetched page onto the cards already shown. */
function patchCounts(fetched) {
  fetched.querySelectorAll(':scope > [data-post-id]').forEach((card) => {
    const mine = list()?.querySelector(`:scope > [data-post-id="${CSS.escape(card.dataset.postId)}"]`);
    if (!mine) return;
    for (const sel of ['.like-count', '.comment-count']) {
      const a = mine.querySelector(sel);
      const b = card.querySelector(sel);
      if (a && b && a.textContent !== b.textContent) a.textContent = b.textContent;
    }
  });
}

// ------------------------------------------------------------
// Live updates
// ------------------------------------------------------------

function busy() {
  const l = list();
  return document.body.style.overflow === 'hidden'
    || !document.getElementById('live-post-modal')?.classList.contains('hidden')
    || !!document.getElementById('compose-post-modal')?.offsetParent
    || !!document.activeElement?.matches?.('input, textarea, select, [contenteditable="true"]')
    || !!l?.querySelector('.post-options-menu:not(.hidden)')
    || [...(l?.querySelectorAll('video') || [])].some((v) => !v.paused && !v.ended);
}

const nearTop = () => (list()?.getBoundingClientRect().top ?? 0) > -NEAR_TOP_PX;

async function check() {
  const p = page();
  if (!p) { stop(); return; }
  if (document.visibilityState !== 'visible') return;
  lastCheck = Date.now();
  const seq = ++request;
  let json;
  try { json = await fetchPage(); } catch { return; } // offline for a moment
  if (seq !== request || !document.body.contains(p) || json.sig === p.dataset.sig) return;

  const tmp = document.createElement('div');
  tmp.innerHTML = json.html;
  const shown = new Set(ids(list()));
  const fresh = ids(tmp).filter((id) => !shown.has(id)).length;

  if (!fresh) {
    // Likes / comments / deletions only
    if (!olderLoaded && !busy()) apply(json); else patchCounts(tmp);
    if (olderLoaded || busy()) p.dataset.sig = json.sig;
    return;
  }
  if (!olderLoaded && !busy() && nearTop()) { apply(json); return; }
  patchCounts(tmp);
  pending = json;
  showPill(fresh);
}

function showPill(count) {
  let pill = document.getElementById('live-feed-new-posts');
  if (!pill) {
    pill = document.createElement('button');
    pill.type = 'button';
    pill.id = 'live-feed-new-posts';
    pill.className = 'fixed left-1/2 top-28 z-40 -translate-x-1/2 inline-flex items-center gap-2 rounded-full bg-ink-950 px-5 py-2.5 text-sm font-semibold text-gold-200 ring-1 ring-gold-300/30 shadow-xl transition-all hover:scale-105';
    pill.addEventListener('click', () => {
      if (pending) apply(pending);
      list()?.scrollIntoView({ behavior: 'smooth', block: 'start' });
    });
    document.body.appendChild(pill);
  }
  pill.innerHTML = `<svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 10l7-7m0 0l7 7m-7-7v18" /></svg> ${count} new ${count === 1 ? 'post' : 'posts'}`;
}

function hidePill() {
  document.getElementById('live-feed-new-posts')?.remove();
}

function stop() {
  clearInterval(timer);
  timer = null;
  hidePill();
}

/** Re-fetch the first page now. */
export async function refreshFeed() {
  const p = page();
  const l = list();
  if (!p || !l) return;
  const seq = ++request;
  l.style.transition = 'opacity 150ms ease';
  l.style.opacity = '0.5';
  try {
    const json = await fetchPage();
    if (seq === request && document.body.contains(l)) apply(json);
  } catch {
    showToast('Could not refresh the feed — check your connection.', 'error');
  } finally {
    l.style.opacity = '';
  }
}

/** A post just shared: on top of the list. */
export function addPostCard(html) {
  const l = list();
  if (!l || !html) return;
  l.querySelector('[data-empty-feed]')?.remove();
  l.insertAdjacentHTML('afterbegin', html);
  bumpMyPosts(1);
}

function bumpMyPosts(delta) {
  const el = page()?.querySelector('[data-my-posts]');
  if (el) el.textContent = Math.max(0, (parseInt(el.textContent, 10) || 0) + delta);
}

// ------------------------------------------------------------
// Clicks
// ------------------------------------------------------------

async function loadOlder(btn) {
  const p = page();
  btn.disabled = true;
  btn.textContent = 'Loading…';
  try {
    const json = await fetchPage(parseInt(p.dataset.nextPage, 10) || 2);
    const tmp = document.createElement('div');
    tmp.innerHTML = json.html;
    const shown = new Set(ids(list()));
    [...tmp.children].forEach((card) => { if (!shown.has(card.dataset.postId)) list().appendChild(card); });
    p.dataset.nextPage = json.next_page;
    btn.classList.toggle('hidden', !json.has_more);
    olderLoaded = true;
  } catch {
    showToast('Could not load older posts.', 'error');
  } finally {
    btn.disabled = false;
    btn.textContent = 'Load older posts';
  }
}

async function toggleLike(btn) {
  const count = btn.querySelector('.like-count');
  const icon = btn.querySelector('svg');
  const was = btn.getAttribute('aria-pressed') === 'true';
  const before = parseInt(count.textContent, 10) || 0;
  const show = (liked, n) => {
    btn.setAttribute('aria-pressed', String(liked));
    btn.classList.toggle('text-rose-600', liked);
    btn.classList.toggle('text-ink-500', !liked);
    icon.classList.toggle('fill-current', liked);
    icon.classList.toggle('fill-none', !liked);
    count.textContent = n;
  };

  show(!was, Math.max(0, before + (was ? -1 : 1))); // optimistic
  try {
    const res = await fetch(`${base()}api/live-feed`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ action: 'toggle-like', post_id: btn.dataset.id }),
    });
    const json = await res.json();
    if (!json.success) throw new Error(json.message);
    show(json.action === 'liked', json.like_count);
  } catch (err) {
    show(was, before);
    showToast(err.message || 'Could not update the like.', 'error');
  }
}

async function deletePost(btn) {
  const own = btn.textContent.includes('Delete post');
  const ok = await confirmDialog(own ? 'Delete this post? Its comments and likes go too.' : 'Remove this member’s post? Its comments and likes go too.', own ? 'Delete' : 'Remove', 'Cancel', 'bg-red-600 hover:bg-red-700');
  if (!ok) return;
  const postId = btn.dataset.postId;
  try {
    const res = await fetch(`${base()}api/live-feed`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ action: 'delete-post', post_id: postId }),
    });
    const json = await res.json();
    if (!json.success) throw new Error(json.message);
    const card = list()?.querySelector(`[data-post-id="${CSS.escape(postId)}"]`);
    if (card) {
      card.style.transition = 'opacity .25s ease, transform .25s ease';
      card.style.opacity = '0';
      card.style.transform = 'scale(.98)';
      setTimeout(() => { card.remove(); if (list() && !list().children.length) list().innerHTML = emptyState(); }, 250);
    }
    if (own) bumpMyPosts(-1);
    showToast(own ? 'Post deleted' : 'Post removed', 'success');
  } catch (err) {
    showToast(err.message || 'Could not delete the post.', 'error');
  }
}

function onClick(e) {
  if (!page()?.contains(e.target)) return;

  const more = e.target.closest('[data-load-more]');
  if (more) return loadOlder(more);

  const like = e.target.closest('.like-toggle-btn');
  if (like) return toggleLike(like);

  const del = e.target.closest('.delete-post-btn');
  if (del) { del.closest('.post-options-menu')?.classList.add('hidden'); return deletePost(del); }

  const menuBtn = e.target.closest('.post-options-btn');
  const menu = menuBtn?.closest('.post-options-container')?.querySelector('.post-options-menu');
  page().querySelectorAll('.post-options-menu:not(.hidden)').forEach((m) => { if (m !== menu) m.classList.add('hidden'); });
  menu?.classList.toggle('hidden');
}

// ------------------------------------------------------------
// Setup
// ------------------------------------------------------------

let bound = false;

export function initPosts() {
  stop();
  olderLoaded = false;
  pending = null;
  timer = setInterval(check, POLL_MS);

  if (!bound) {
    bound = true;
    document.addEventListener('click', onClick);
    document.addEventListener('visibilitychange', () => {
      if (document.visibilityState === 'visible' && timer && Date.now() - lastCheck >= POLL_MS) check();
    });
  }
}
