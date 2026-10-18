// /resources/js/utils/live-feed/people.js
//
// The Live Feed sidebar's people tools (api/follows): "People to follow"
// suggestions, the people search (same list, swapped for results while
// there's a query), follow / unfollow (anywhere on the page or in the
// following / followers window), and those two windows, opened from the
// counts on your card. Following someone refreshes the feed when it's on
// the Following view, so their posts show up at once.

import { Modal } from '../../factories/modal-factory.js';
import { showToast } from '../../ui/toast.js';
import { refreshFeed } from './posts.js';

const base = () => window.APP_CONFIG?.baseUrl || '/';
const page = () => document.getElementById('live-feed-page');

let searchTimer = null;
let searchSeq = 0;
let bound = false;

const message = (text) => `<p class="py-6 text-center text-sm text-gray-400">${text}</p>`;

async function getRows(query) {
  const res = await fetch(`${base()}api/follows?${query}`, { cache: 'no-store' });
  const json = await res.json();
  if (!json.success) throw new Error(json.message);
  return json;
}

async function showSuggestions() {
  const listEl = page()?.querySelector('[data-people-list]');
  if (!listEl) return;
  page().querySelector('[data-people-heading]').textContent = 'People to follow';
  try {
    const json = await getRows('');
    listEl.innerHTML = json.users.length ? json.html : message('You’re following everyone already 🎉');
  } catch {
    listEl.innerHTML = message('Couldn’t load suggestions.');
  }
}

function onSearch(input) {
  clearTimeout(searchTimer);
  const q = input.value.trim();
  if (!q) { searchSeq++; showSuggestions(); return; }
  searchTimer = setTimeout(async () => {
    const seq = ++searchSeq;
    const listEl = page()?.querySelector('[data-people-list]');
    if (!listEl) return;
    page().querySelector('[data-people-heading]').textContent = 'Results';
    try {
      const json = await getRows(`action=search&q=${encodeURIComponent(q)}`);
      if (seq === searchSeq) listEl.innerHTML = json.users.length ? json.html : message('No one by that name.');
    } catch {
      if (seq === searchSeq) listEl.innerHTML = message('Search failed — try again.');
    }
  }, 250);
}

function paintButton(btn, following) {
  btn.dataset.following = following ? '1' : '0';
  btn.setAttribute('aria-pressed', String(following));
  btn.textContent = following ? 'Following' : 'Follow';
  const on = 'bg-gray-100 text-gray-600 hover:bg-red-50 hover:text-red-600 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-red-950/40 dark:hover:text-red-400';
  const off = 'bg-secondary-900 text-white hover:bg-secondary-700 dark:bg-white dark:text-gray-900 dark:hover:bg-gray-200';
  btn.classList.remove(...(following ? off : on).split(' '));
  btn.classList.add(...(following ? on : off).split(' '));
}

async function toggleFollow(btn) {
  const id = btn.dataset.followToggle;
  btn.disabled = true;
  try {
    const res = await fetch(`${base()}api/follows`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ following_id: id }),
    });
    const json = await res.json();
    if (!json.success) throw new Error(json.message);
    const following = json.status === 'followed';
    // Every button for this person (sidebar list and an open window)
    document.querySelectorAll(`[data-follow-toggle="${CSS.escape(id)}"]`).forEach((b) => paintButton(b, following));
    const p = page();
    if (p) {
      p.querySelector('[data-following-count]').textContent = json.following;
      p.querySelector('[data-followers-count]').textContent = json.followers;
      if (p.dataset.scope === 'following') refreshFeed();
    }
  } catch (err) {
    showToast(err.message || 'Could not update that follow.', 'error');
  } finally {
    btn.disabled = false;
  }
}

async function openList(type) {
  const title = type === 'followers' ? 'Followers' : 'Following';
  const modal = new Modal({
    id: 'follow-list-modal',
    title,
    size: 'md',
    showFooter: false,
    content: '<div id="follow-list-body" class="min-h-[8rem] space-y-0.5"><div class="flex justify-center py-10"><div class="h-6 w-6 animate-spin rounded-full border-2 border-primary-500 border-t-transparent"></div></div></div>',
  });
  modal.open();
  const body = document.getElementById('follow-list-body');
  try {
    const json = await getRows(`action=get-list&type=${type}`);
    body.innerHTML = json.users.length ? json.html : message(type === 'followers' ? 'No followers yet.' : 'You’re not following anyone yet.');
  } catch {
    body.innerHTML = message('Couldn’t load this list.');
  }
}

export function initPeople() {
  showSuggestions();
  const input = page()?.querySelector('[data-people-search]');
  if (input) input.addEventListener('input', () => onSearch(input));

  if (bound) return;
  bound = true;
  document.addEventListener('click', (e) => {
    const follow = e.target.closest('[data-follow-toggle]');
    if (follow) return toggleFollow(follow);
    const listBtn = e.target.closest('#live-feed-page [data-follow-list]');
    if (listBtn) openList(listBtn.dataset.followList);
  });
}
