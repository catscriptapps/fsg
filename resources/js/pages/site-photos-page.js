// /resources/js/pages/site-photos-page.js
//
// Admin: Site Photos (resources/views/pages/site-photos.php). Everything
// lives in memory (seeded from #site-photos-data) and every write to
// api/site-photos answers with the fresh state, which is re-rendered.
//
//  - Library grid = the home slideshow, in order. Reorder with each tile's
//    ⇄ button: click one to pick that photo up (thick gold ring — click it
//    again, or Esc, to put it back), then click another photo's ⇄ to move
//    the picked photo into that photo's place; the photos in between shift
//    over by one (utils/pic-reorder.js moveToSlot). Saved straight away.
//  - Select mode: tick tiles (or Select all), then Delete. "Delete all"
//    empties the library. Deleting removes the files from the server too.
//  - Add photos: the shared uploader (modals/upload-modal.js — HEIC support,
//    resized in the browser), appended to the end of the slideshow.
//  - Editor: a full-screen viewer per photo with Original / As a slide /
//    As a page hero previews, the slide caption, description (alt text),
//    slideshow on/off, move first / earlier / later / last, download, delete.
//    ← / → / Esc, swipe on phones.
//  - Spots: the photo in each fixed spot on the public pages — choose from
//    the library (Modal factory picker), upload straight into it, or remove.

import { createUploadHandler } from '../modals/upload-modal.js';
import { moveToSlot } from '../utils/pic-reorder.js';
import { Modal } from '../factories/modal-factory.js';
import { showToast } from '../ui/toast.js';
import { confirmDialog } from '../ui/confirm.js';
import { escapeHtml } from '../utils/escape-html.js';

const base = () => window.APP_CONFIG?.baseUrl || '/';
const api = () => `${base()}api/site-photos`;
// Shown large (slideshow, heroes): keep them sharp, unlike the uploader's small avatar defaults
const UPLOAD_QUALITY = { maxDim: 2400, q: 0.86, targetKB: 900 };
const LOW_RES = 1200; // narrower than this looks soft in a large slot
const HERO_SLIDE_MS = 6000;

let data = { ready: true, photos: [], slots: [], links: [] };
let selecting = false;
let selected = new Set();
let picked = null;     // id of the photo picked up to move
let viewer = null;     // the <body>-level editor element
let current = -1;      // index in data.photos shown in the editor
let view = 'slide';    // original | slide | hero
let heroTimer = null;
let picker = null;

const esc = (v) => escapeHtml(String(v ?? ''));
const plural = (n, one, many = `${one}s`) => `${n} ${n === 1 ? one : many}`;
const shortDate = (s) => (s ? new Date(`${s}T00:00:00`).toLocaleDateString('en-CA', { month: 'short', day: 'numeric', year: 'numeric' }) : '');
const mb = (b) => (b ? (b >= 1048576 ? `${(b / 1048576).toFixed(1)} MB` : `${Math.round(b / 1024)} KB`) : '');
const svg = (path, cls = 'h-4 w-4') => `<svg class="${cls}" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="${path}" /></svg>`;
const ICON = {
  edit: 'M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931z',
  trash: 'M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16',
  left: 'M15 19l-7-7 7-7', right: 'M9 5l7 7-7 7', x: 'M6 18L18 6M6 6l12 12',
  first: 'M11 19l-7-7 7-7m8 14l-7-7 7-7', last: 'M13 5l7 7-7 7M5 5l7 7-7 7',
  download: 'M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4',
  upload: 'M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12',
  reorder: 'M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4',
  check: 'M5 13l4 4L19 7',
  photo: 'M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z',
};
/** *words* in asterisks → gold italics, like the public slideshow (Photos::titleHtml) */
const titleHtml = (t) => esc(t).replace(/\*(.+?)\*/g, '<em class="italic text-primary-300">$1</em>');

/** Quality hints for a photo shown large. */
function hints(p) {
  const out = [];
  if (p.missing) out.push(['missing', 'File missing on the server']);
  if (p.width && p.width < LOW_RES) out.push(['low', `Low resolution (${p.width}px wide) — may look soft when shown large`]);
  if (p.width && p.height && p.height > p.width) out.push(['portrait', 'Portrait — wide spots crop most of it away']);
  if (p.in_slideshow && !p.slide_title) out.push(['caption', 'No slide caption — the slideshow shows a default headline']);
  return out;
}

// ------------------------------------------------------------
// Init
// ------------------------------------------------------------

export function init() {
  const page = document.getElementById('site-photos-page');
  if (!page || page.dataset.ready) return;
  page.dataset.ready = 'true';

  try { data = { ...data, ...JSON.parse(document.getElementById('site-photos-data')?.textContent || '{}') }; } catch { /* keep defaults */ }

  page.addEventListener('click', onClick);
  document.getElementById('sp-selectbar')?.addEventListener('click', onSelectBar);
  document.addEventListener('keydown', onPageKey);
  render();
}

// ------------------------------------------------------------
// Server
// ------------------------------------------------------------

async function post(body) {
  const res = await fetch(api(), { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify(body) });
  const json = await res.json().catch(() => ({ success: false, messages: ['Unexpected server response.'] }));
  if (!json.success) throw new Error(json.messages?.[0] || 'Something went wrong.');
  return json;
}

async function reload() {
  const res = await fetch(api(), { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
  const json = await res.json().catch(() => ({}));
  if (json.success) adopt(json);
}

function adopt(json) {
  if (json.photos) data.photos = json.photos;
  if (json.slots) data.slots = json.slots;
  if (picked && !data.photos.some((p) => p.id === picked)) picked = null;
  selected = new Set([...selected].filter((id) => data.photos.some((p) => p.id === id)));
  render();
  if (viewer && !viewer.classList.contains('hidden')) {
    if (!data.photos.length) closeViewer();
    else { current = Math.min(Math.max(current, 0), data.photos.length - 1); paintViewer(); }
  }
}

/** POST an action, adopt the fresh state, toast the outcome. */
async function run(body, okToast = true) {
  try {
    const json = await post(body);
    adopt(json);
    if (okToast) showToast(json.messages?.[0] || 'Saved.', 'success');
    return json;
  } catch (err) {
    showToast(err.message, 'error');
    await reload(); // undo any optimistic change
    return null;
  }
}

// ------------------------------------------------------------
// Render
// ------------------------------------------------------------

function render() { renderHero(); renderGrid(); renderSelectBar(); renderSlots(); }

function renderHero() {
  const el = document.getElementById('sp-hero');
  if (!el) return;
  const photos = data.photos;
  const inShow = photos.filter((p) => p.in_slideshow);
  const filled = data.slots.filter((s) => s.photo_id).length;
  const flagged = photos.filter((p) => hints(p).some(([k]) => k !== 'caption'));
  const tile = (value, label, note, accent = 'text-white') => `
    <div class="min-w-0 rounded-2xl bg-white/10 px-4 py-3.5 ring-1 ring-white/15 backdrop-blur-md">
      <span class="block truncate text-2xl font-bold leading-none sm:text-3xl ${accent}">${value}</span>
      <span class="mt-1.5 block text-[11px] font-semibold uppercase tracking-wider text-primary-200">${label}</span>
      ${note ? `<span class="mt-0.5 block truncate text-[11px] text-white/60">${note}</span>` : ''}
    </div>`;
  el.innerHTML = `
    <div class="grid gap-6 lg:grid-cols-5 lg:items-end">
      <div class="min-w-0 lg:col-span-3">
        <span class="inline-flex items-center gap-2 rounded-full bg-white/10 px-3 py-1 text-xs font-semibold ring-1 ring-white/15">${svg(ICON.photo, 'h-3.5 w-3.5')} Admin · Site Photos</span>
        <h1 class="mt-3 text-3xl font-bold tracking-tight drop-shadow-[0_2px_12px_rgba(0,0,0,0.35)] sm:text-4xl">${inShow.length ? `${plural(inShow.length, 'photo')} in the home slideshow` : 'The home slideshow is empty'}</h1>
        <p class="mt-2 max-w-xl text-base text-white/80">${photos.length ? 'You’re looking at it now. Changes here show on the public website straight away.' : 'Add photos to build the home slideshow and fill the photo spots around the website.'}</p>
        <div class="mt-5 grid grid-cols-2 gap-3 sm:grid-cols-4">
          ${tile(photos.length, 'In the library', photos.length ? plural(photos.length - inShow.length, 'not in slideshow', 'not in slideshow') : 'add some')}
          ${tile(inShow.length, 'Slides', inShow.length ? `${Math.ceil((inShow.length * 7) / 60) || 1} min loop` : '—')}
          ${tile(`${filled}/${data.slots.length}`, 'Spots filled', filled < data.slots.length ? `${data.slots.length - filled} empty` : 'all set')}
          ${tile(flagged.length, 'Worth a look', flagged.length ? 'soft or portrait photos' : 'all look good', flagged.length ? 'text-amber-300' : 'text-emerald-300')}
        </div>
      </div>
      <div class="rounded-2xl bg-white/10 p-4 ring-1 ring-white/15 backdrop-blur-md lg:col-span-2">
        <p class="mb-2 text-xs font-bold uppercase tracking-wider text-primary-200">Where they appear</p>
        <ul class="space-y-1.5 text-sm text-white/85">
          <li class="flex gap-2"><span>🏠</span><span>The home page slideshow — the library, in this order</span></li>
          <li class="flex gap-2"><span>🖼️</span><span>${plural(data.slots.length, 'photo spot')} on the About, Services, Team, Funding, Projects, Testimonials and Contact pages</span></li>
          <li class="flex gap-2"><span>💡</span><span>Wide landscape photos (1600px+) look best</span></li>
        </ul>
      </div>
    </div>`;
  startHeroSlides(inShow);
}

/** The hero's background quietly plays the slideshow. */
function startHeroSlides(slides) {
  const box = document.getElementById('sp-hero-slides');
  clearInterval(heroTimer);
  if (!box) return;
  const usable = slides.filter((p) => !p.missing);
  box.innerHTML = usable.map((p, i) => `<img src="${esc(p.url)}" alt="" class="absolute inset-0 h-full w-full object-cover transition-opacity duration-1000 ${i === 0 ? 'opacity-100' : 'opacity-0'}">`).join('');
  if (usable.length < 2 || window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;
  let i = 0;
  heroTimer = setInterval(() => {
    const imgs = box.querySelectorAll('img');
    if (!document.body.contains(box) || !imgs.length) { clearInterval(heroTimer); return; }
    imgs[i % imgs.length].classList.replace('opacity-100', 'opacity-0');
    i = (i + 1) % imgs.length;
    imgs[i].classList.replace('opacity-0', 'opacity-100');
  }, HERO_SLIDE_MS);
}

function tileHtml(p) {
  const on = selected.has(p.id);
  const isPicked = picked === p.id;
  const flags = hints(p).filter(([k]) => k !== 'caption');
  const reorderBtn = isPicked
    ? `<button type="button" data-reorder="${esc(p.id)}" title="Put it back (don’t move)" aria-label="Put it back" aria-pressed="true" class="flex h-9 w-9 items-center justify-center rounded-xl bg-primary-500 text-white shadow-lg ring-2 ring-white">${svg(ICON.reorder)}</button>`
    : `<button type="button" data-reorder="${esc(p.id)}" title="${picked ? `Move the picked photo to position ${p.position}` : 'Move this photo'}" aria-label="${picked ? `Move the picked photo to position ${p.position}` : 'Move this photo'}" aria-pressed="false"
        class="flex h-9 w-9 items-center justify-center rounded-xl shadow ${picked ? 'animate-pulse bg-primary-500/90 text-white ring-2 ring-white/80' : 'bg-white/90 text-gray-700 hover:text-primary-700'}">${svg(ICON.reorder)}</button>`;
  return `
    <div data-tile data-id="${esc(p.id)}" class="group relative cursor-pointer select-none overflow-hidden rounded-2xl bg-gray-100 shadow-sm transition-shadow dark:bg-gray-800 ${isPicked
      ? 'shadow-lg shadow-primary-500/30 ring-[6px] ring-primary-500 ring-offset-2 ring-offset-white dark:ring-offset-gray-900'
      : `ring-2 ${on ? 'ring-primary-500' : 'ring-transparent'}`}">
      <div class="aspect-[4/3]">
        ${p.missing
          ? `<div class="flex h-full w-full flex-col items-center justify-center gap-1 text-xs text-gray-400">${svg(ICON.x, 'h-6 w-6')}File missing</div>`
          : `<img src="${esc(p.url)}" alt="${esc(p.alt || `Photo ${p.position}`)}" loading="lazy" draggable="false" class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105 ${p.in_slideshow ? '' : 'opacity-70 grayscale-[35%]'}">`}
      </div>
      <div aria-hidden="true" class="pointer-events-none absolute inset-0 bg-gradient-to-t from-black/75 via-black/0 to-black/30"></div>
      <span class="absolute left-2 top-2 flex h-7 min-w-[1.75rem] items-center justify-center rounded-lg bg-black/55 px-1.5 text-xs font-black text-white backdrop-blur">${p.position}</span>
      <span class="absolute left-11 top-2 rounded-lg px-1.5 py-1 text-[10px] font-bold ${p.slide ? 'bg-primary-500/90 text-secondary-950' : 'bg-white/85 text-gray-600'}">${p.slide ? `Slide ${p.slide}` : 'Library only'}</span>
      ${flags.length ? `<span title="${esc(flags.map((f) => f[1]).join(' · '))}" class="absolute left-2 top-11 rounded-lg bg-amber-400/90 px-1.5 py-1 text-[10px] font-bold text-amber-950">${{ portrait: 'Portrait', low: 'Low-res', missing: 'Missing' }[flags[0][0]]}</span>` : ''}
      ${selecting
        ? `<span class="absolute right-2 top-2 flex h-7 w-7 items-center justify-center rounded-full shadow ${on ? 'bg-primary-500 text-white' : 'bg-white/80 text-transparent ring-2 ring-white'}">${svg(ICON.check)}</span>`
        : `${picked ? '' : `<span class="absolute right-2 top-2 flex gap-1 opacity-100 transition-opacity sm:opacity-0 sm:group-hover:opacity-100 sm:group-focus-within:opacity-100">
            <button type="button" data-edit="${esc(p.id)}" title="Edit" aria-label="Edit photo ${p.position}" class="flex h-8 w-8 items-center justify-center rounded-lg bg-white/90 text-gray-700 shadow hover:text-primary-700">${svg(ICON.edit)}</button>
            <button type="button" data-delete="${esc(p.id)}" title="Delete" aria-label="Delete photo ${p.position}" class="flex h-8 w-8 items-center justify-center rounded-lg bg-white/90 text-gray-700 shadow hover:text-red-600">${svg(ICON.trash)}</button>
          </span>`}
          <span class="absolute bottom-2 right-2">${reorderBtn}</span>
          ${isPicked ? '<span class="absolute right-2 top-2 rounded-lg bg-primary-500 px-2 py-1 text-[10px] font-bold text-white shadow">Moving…</span>' : ''}`}
      <div class="absolute bottom-3 left-3 right-14 min-w-0 text-white drop-shadow">
        ${p.in_slideshow && p.slide_title ? `<p class="truncate text-xs font-semibold">${esc(p.slide_title.replace(/\*/g, ''))}</p>` : ''}
        ${p.used_in.length ? `<p class="truncate text-[10px] text-white/75">Also in ${plural(p.used_in.length, 'spot')}</p>` : ''}
      </div>
    </div>`;
}

function renderGrid() {
  const el = document.getElementById('sp-grid');
  if (!el) return;
  const has = data.photos.length > 0;
  document.getElementById('sp-select-btn')?.classList.toggle('hidden', !has || selecting);
  document.getElementById('sp-delete-all-btn')?.classList.toggle('hidden', !has || selecting);
  if (!has) {
    el.innerHTML = `
      <div class="px-6 py-16 text-center">
        <span class="mb-4 inline-flex h-14 w-14 items-center justify-center rounded-2xl bg-primary-50 text-primary-600 dark:bg-primary-950/40">${svg(ICON.photo, 'h-7 w-7')}</span>
        <p class="text-base font-semibold text-gray-800 dark:text-gray-100">No photos yet</p>
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Add a few wide, bright photos — they’ll start playing in the home slideshow straight away.</p>
        ${data.ready ? '<button type="button" data-act="upload" class="mt-5 inline-flex items-center gap-2 rounded-xl bg-primary-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-primary-700">Add photos</button>' : ''}
      </div>`;
    return;
  }
  const p = data.photos.find((x) => x.id === picked);
  el.innerHTML = `
    ${p ? `
      <div class="mb-4 flex flex-wrap items-center gap-3 rounded-xl border border-primary-200 bg-primary-50 px-4 py-2.5 text-sm text-primary-900 dark:border-primary-900/60 dark:bg-primary-950/40 dark:text-primary-200">
        <span>Moving photo <strong>${p.position}</strong> — click the ${svg(ICON.reorder, 'inline h-4 w-4 align-text-bottom')} on the photo whose place it should take.</span>
        <button type="button" data-unpick class="ml-auto rounded-lg px-3 py-1 text-xs font-semibold hover:bg-primary-100 dark:hover:bg-primary-900/50">Cancel</button>
      </div>` : ''}
    <div class="grid grid-cols-2 gap-3 sm:gap-4 md:grid-cols-3 xl:grid-cols-4">${data.photos.map(tileHtml).join('')}</div>`;
}

function renderSelectBar() {
  const bar = document.getElementById('sp-selectbar');
  if (!bar) return;
  bar.classList.toggle('hidden', !selecting);
  bar.querySelector('[data-sel-count]').textContent = `${selected.size} selected`;
  bar.querySelector('[data-sel="delete"]').disabled = !selected.size;
  bar.querySelector('[data-sel="delete"]').textContent = selected.size > 1 ? `Delete ${selected.size}` : 'Delete';
  bar.querySelector('[data-sel="all"]').textContent = selected.size === data.photos.length ? 'Select none' : 'Select all';
}

function renderSlots() {
  const el = document.getElementById('sp-slots');
  if (!el) return;
  if (!data.ready) { el.innerHTML = ''; return; }
  const byId = Object.fromEntries(data.photos.map((p) => [p.id, p]));
  const pages = [...new Set(data.slots.map((s) => s.page))];
  el.innerHTML = pages.map((page) => `
    <div>
      <h3 class="mb-3 text-xs font-bold uppercase tracking-[0.16em] text-gray-500 dark:text-gray-400">${esc(page)}</h3>
      <div class="grid gap-3 md:grid-cols-2 xl:grid-cols-3">
        ${data.slots.filter((s) => s.page === page).map((s) => {
          const photo = s.photo_id ? byId[s.photo_id] : null;
          return `
          <div class="flex items-center gap-3 rounded-2xl border border-gray-200 p-3 dark:border-gray-800">
            ${photo
              ? `<button type="button" data-pick="${esc(s.key)}" class="flex-shrink-0" title="Change"><img src="${esc(photo.url)}" alt="" class="h-16 w-20 rounded-xl object-cover"></button>`
              : `<span class="flex h-16 w-20 flex-shrink-0 items-center justify-center rounded-xl border-2 border-dashed border-gray-200 text-gray-300 dark:border-gray-700 dark:text-gray-600">${svg(ICON.photo, 'h-6 w-6')}</span>`}
            <div class="min-w-0 flex-1">
              <p class="text-sm font-semibold text-gray-900 dark:text-white">${esc(s.label)}</p>
              <p class="text-xs text-gray-500 dark:text-gray-400">${esc(s.hint)}</p>
              <div class="mt-1.5 flex flex-wrap gap-x-3 gap-y-1">
                <button type="button" data-pick="${esc(s.key)}" class="text-xs font-semibold text-primary-700 hover:underline dark:text-primary-300">${photo ? 'Change' : 'Choose a photo'}</button>
                <button type="button" data-slot-upload="${esc(s.key)}" class="text-xs font-semibold text-primary-700 hover:underline dark:text-primary-300">Upload</button>
                ${photo ? `<button type="button" data-clear-slot="${esc(s.key)}" class="text-xs font-semibold text-gray-500 hover:text-red-600">Remove</button>` : ''}
              </div>
            </div>
          </div>`;
        }).join('')}
      </div>
    </div>`).join('');
}

// ------------------------------------------------------------
// Clicks
// ------------------------------------------------------------

function onClick(e) {
  if (e.target.closest('#sp-upload-btn, [data-act="upload"]')) { openUpload(); return; }
  if (e.target.closest('#sp-select-btn')) { selecting = true; selected = new Set(); picked = null; renderGrid(); renderSelectBar(); return; }
  if (e.target.closest('#sp-delete-all-btn')) { removePhotos(data.photos.map((p) => p.id), true); return; }

  const ro = e.target.closest('[data-reorder]');
  if (ro) { onReorder(ro.dataset.reorder); return; }
  if (e.target.closest('[data-unpick]')) { picked = null; renderGrid(); return; }
  const ed = e.target.closest('[data-edit]');
  if (ed) { openViewer(data.photos.findIndex((p) => p.id === ed.dataset.edit)); return; }
  const del = e.target.closest('[data-delete]');
  if (del) { removePhotos([del.dataset.delete]); return; }

  const pick = e.target.closest('[data-pick]');
  if (pick) { openPicker(pick.dataset.pick); return; }
  const up = e.target.closest('[data-slot-upload]');
  if (up) { openUpload(up.dataset.slotUpload); return; }
  const clear = e.target.closest('[data-clear-slot]');
  if (clear) { clearSlot(clear.dataset.clearSlot); return; }

  const tile = e.target.closest('[data-tile]');
  if (!tile) return;
  const id = tile.dataset.id;
  if (picked !== null && !selecting) return; // while moving, only the ⇄ buttons act
  if (selecting) {
    if (selected.has(id)) selected.delete(id); else selected.add(id);
    renderGrid(); renderSelectBar();
    return;
  }
  openViewer(data.photos.findIndex((p) => p.id === id));
}

function onSelectBar(e) {
  const act = e.target.closest('[data-sel]')?.dataset.sel;
  if (act === 'all') {
    selected = selected.size === data.photos.length ? new Set() : new Set(data.photos.map((p) => p.id));
  } else if (act === 'cancel') {
    selecting = false; selected = new Set();
  } else if (act === 'delete') {
    removePhotos([...selected]);
    return;
  }
  renderGrid(); renderSelectBar();
}

/** Esc puts a picked-up photo back (the editor handles its own keys). */
function onPageKey(e) {
  if (!document.getElementById('site-photos-page')) { document.removeEventListener('keydown', onPageKey); return; }
  if (e.key === 'Escape' && picked !== null && (!viewer || viewer.classList.contains('hidden'))) { picked = null; renderGrid(); }
}

// ------------------------------------------------------------
// Reorder (utils/pic-reorder.js: pick up → drop into another photo's place)
// ------------------------------------------------------------

async function onReorder(id) {
  if (!data.ready) return;
  if (picked === null) { picked = id; renderGrid(); return; }   // pick up
  if (picked === id) { picked = null; renderGrid(); return; }   // put it back
  const ids = moveToSlot(data.photos.map((x) => x.id), picked, id); // forwards or backwards
  picked = null;
  await saveOrder(ids);
}

async function saveOrder(ids) {
  // Optimistic: show the new order (and numbers) straight away
  const byId = new Map(data.photos.map((p) => [p.id, p]));
  data.photos = ids.map((id, i) => ({ ...byId.get(id), position: i + 1 }));
  renderGrid();
  await run({ action: 'reorder', ids });
}

// ------------------------------------------------------------
// Upload / delete
// ------------------------------------------------------------

/** Shared upload modal — into the library (slideshow), or straight into one spot. */
function openUpload(slotKey = null) {
  if (!data.ready) return;
  const url = slotKey ? `${api()}?slot=${encodeURIComponent(slotKey)}` : api();
  createUploadHandler(url, 'site-photos', async (files) => {
    if (!files?.length) return;
    await reload();
    showToast(slotKey ? 'Photo added to that spot.' : (files.length === 1 ? 'Photo added to the slideshow.' : `${files.length} photos added to the slideshow.`), 'success');
  }, 4, true, { quality: UPLOAD_QUALITY, single: !!slotKey, maxFiles: slotKey ? 1 : 40 });
}

async function removePhotos(ids, all = false) {
  if (!ids.length) return;
  const n = ids.length;
  const spots = data.photos.filter((p) => ids.includes(p.id)).reduce((k, p) => k + p.used_in.length, 0);
  const msg = all
    ? `Delete <strong>all ${plural(n, 'photo')}</strong> from the website? Their files are removed from the server and can’t be recovered.<br><br>The home slideshow will be empty${spots ? `, and ${plural(spots, 'photo spot')} will show no photo` : ''}.`
    : `Delete ${n === 1 ? 'this photo' : `these <strong>${n}</strong> photos`}? ${n === 1 ? 'Its file is' : 'Their files are'} removed from the server and can’t be recovered.${spots ? `<br><br>${plural(spots, 'photo spot')} using ${n === 1 ? 'it' : 'them'} will show no photo.` : ''}`;
  if (!(await confirmDialog(msg, all ? `Delete all ${n}` : (n === 1 ? 'Delete' : `Delete ${n}`), 'Cancel', 'bg-red-600 hover:bg-red-700'))) return;
  const json = await run({ action: 'delete', ids: all ? ['*'] : ids });
  if (json && selecting) { selecting = data.photos.length > 0 && selecting; selected = new Set(); renderGrid(); renderSelectBar(); }
}

// ------------------------------------------------------------
// Spots
// ------------------------------------------------------------

async function clearSlot(key) {
  const slot = data.slots.find((s) => s.key === key);
  const ok = await confirmDialog(`Remove the photo from “${esc(slot?.page)} · ${esc(slot?.label)}”? That spot will show no photo. (The photo stays in the library.)`, 'Remove', 'Cancel', 'bg-red-600 hover:bg-red-700');
  if (ok) run({ action: 'assign', slot: key, photo_id: null });
}

function openPicker(key) {
  const slot = data.slots.find((s) => s.key === key);
  if (!slot) return;
  picker?.destroy?.();
  document.getElementById('site-photo-picker')?.remove();
  document.getElementById('site-photo-picker-overlay')?.remove();
  picker = new Modal({
    id: 'site-photo-picker',
    title: `${slot.page} · ${slot.label}`,
    size: 'xl',
    showFooter: false,
    content: `
      <div class="space-y-4">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
          <p class="text-sm text-gray-500 dark:text-gray-400">${esc(slot.hint)} Pick a photo, or upload a new one.</p>
          <button type="button" data-picker-upload class="inline-flex flex-shrink-0 items-center gap-2 rounded-xl border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-200 dark:hover:bg-gray-800">${svg(ICON.upload)} Upload a new photo</button>
        </div>
        <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-4">
          ${data.photos.filter((p) => !p.missing).map((p) => {
            const on = p.id === slot.photo_id;
            return `
            <button type="button" data-choose="${esc(p.id)}" aria-pressed="${on}" title="${esc(p.alt || `Photo ${p.position}`)}"
              class="group relative overflow-hidden rounded-xl ring-2 transition ${on ? 'ring-primary-600' : 'ring-transparent hover:ring-primary-300'}">
              <img src="${esc(p.url)}" alt="${esc(p.alt)}" loading="lazy" class="aspect-[4/3] w-full object-cover">
              ${on ? `<span class="absolute right-2 top-2 flex h-6 w-6 items-center justify-center rounded-full bg-primary-600 text-white shadow">${svg(ICON.check, 'h-3.5 w-3.5')}</span>` : ''}
            </button>`;
          }).join('') || '<p class="col-span-full text-sm text-gray-500">The library is empty — upload a photo.</p>'}
        </div>
      </div>`,
  });
  picker.open();

  document.getElementById('site-photo-picker')?.addEventListener('click', async (e) => {
    const choose = e.target.closest('[data-choose]');
    if (choose) {
      picker.close();
      if (choose.dataset.choose !== slot.photo_id) await run({ action: 'assign', slot: key, photo_id: choose.dataset.choose });
      return;
    }
    if (e.target.closest('[data-picker-upload]')) {
      picker.close();
      openUpload(key);
    }
  });
}

// ------------------------------------------------------------
// Editor — full-screen viewer with the slide caption
// ------------------------------------------------------------

function ensureViewer() {
  if (viewer && document.body.contains(viewer)) return viewer;
  viewer = document.createElement('div');
  viewer.id = 'sp-viewer';
  viewer.className = 'hidden fixed inset-0 flex flex-col bg-gray-950/95 text-white backdrop-blur-sm';
  viewer.style.zIndex = '2147483646';
  viewer.setAttribute('role', 'dialog');
  viewer.setAttribute('aria-modal', 'true');
  viewer.setAttribute('aria-label', 'Edit site photo');
  document.body.appendChild(viewer);

  viewer.addEventListener('click', onViewerClick);
  viewer.addEventListener('input', onViewerInput);
  viewer.addEventListener('submit', onViewerSave);
  document.addEventListener('keydown', onViewerKey);
  let x0 = null;
  viewer.addEventListener('touchstart', (e) => { x0 = e.touches[0].clientX; }, { passive: true });
  viewer.addEventListener('touchend', (e) => {
    if (x0 === null) return;
    const dx = e.changedTouches[0].clientX - x0;
    x0 = null;
    if (Math.abs(dx) > 50 && !e.target.closest('input, textarea, form')) step(dx < 0 ? 1 : -1);
  });
  return viewer;
}

function openViewer(index) {
  if (index < 0) return;
  current = index;
  view = data.photos[index]?.in_slideshow ? 'slide' : 'original';
  ensureViewer().classList.remove('hidden');
  document.documentElement.classList.add('overflow-hidden');
  paintViewer();
}

function closeViewer() {
  viewer?.classList.add('hidden');
  document.documentElement.classList.remove('overflow-hidden');
}

async function step(d) {
  if (!data.photos.length) return;
  if (isDirty() && !(await confirmDialog('You have unsaved changes to this photo. Leave without saving?', 'Leave', 'Stay'))) return;
  current = (current + d + data.photos.length) % data.photos.length;
  paintViewer();
}

/** The caption form's current values (or the saved ones before it's drawn). */
function formValues() {
  const f = viewer?.querySelector('form[data-editor]');
  const p = data.photos[current];
  if (!f) return p;
  const v = (n) => f.elements.namedItem(n)?.value ?? '';
  return { ...p, alt: v('alt'), slide_eyebrow: v('slide_eyebrow'), slide_title: v('slide_title'), slide_text: v('slide_text'), slide_cta: v('slide_cta'), slide_link: v('slide_link'), in_slideshow: f.elements.namedItem('in_slideshow')?.checked ?? p.in_slideshow };
}

function isDirty() {
  const p = data.photos[current];
  const f = viewer?.querySelector('form[data-editor]');
  if (!p || !f || viewer.classList.contains('hidden')) return false;
  const v = formValues();
  return ['alt', 'slide_eyebrow', 'slide_title', 'slide_text', 'slide_cta', 'slide_link', 'in_slideshow'].some((k) => String(v[k]).trim() !== String(p[k]).trim());
}

function stageHtml(p) {
  if (p.missing) return '<div class="text-sm text-white/60">This photo’s file is missing on the server — delete it and add it again.</div>';
  const v = formValues();
  if (view === 'slide') {
    // As on the home page: photo panel + dark caption panel
    return `
      <div class="w-full max-w-5xl">
        <div class="grid overflow-hidden rounded-[1.5rem] bg-[#0b0a08] shadow-2xl lg:grid-cols-12">
          <div class="relative aspect-[16/10] lg:col-span-7 lg:aspect-auto lg:min-h-[22rem]">
            <img src="${esc(p.url)}" alt="" class="absolute inset-0 h-full w-full object-cover">
            <span class="absolute left-4 top-4 rounded-full bg-black/60 px-3 py-1 font-serif text-sm text-white/90 ring-1 ring-primary-300/20">${String(p.slide || '–').padStart(2, '0')} / ${String(data.photos.filter((x) => x.in_slideshow).length).padStart(2, '0')}</span>
          </div>
          <div class="flex flex-col justify-center p-6 sm:p-8 lg:col-span-5" data-slide-preview>
            ${slideCaptionHtml(v)}
          </div>
        </div>
        <p class="mt-3 text-center text-xs text-white/50">${v.in_slideshow ? 'How this photo plays in the home slideshow — the caption updates as you type.' : 'This photo isn’t in the slideshow — tick “Play in the home slideshow” below to add it.'}</p>
      </div>`;
  }
  if (view === 'hero') {
    // As a page hero background: wide, cropped, under the ink tint
    return `
      <div class="w-full max-w-6xl">
        <div class="relative aspect-[16/6] overflow-hidden rounded-3xl bg-[#0b0a08] shadow-2xl">
          <img src="${esc(p.url)}" alt="" class="absolute inset-0 h-full w-full object-cover" style="filter:saturate(.75) contrast(1.05)">
          <div class="absolute inset-0 bg-gradient-to-r from-[#0b0a08] via-[#0b0a08]/85 to-[#0b0a08]/45"></div>
          <div class="relative p-6 sm:p-12">
            <p class="text-[11px] font-semibold uppercase tracking-[0.28em] text-primary-300">Our services</p>
            <p class="mt-3 font-serif text-3xl sm:text-5xl">Consulting built around <em class="italic text-primary-300">your goals.</em></p>
            <p class="mt-3 max-w-lg text-sm text-white/70">How this photo sits behind a page headline — the edges are cropped to fill the space.</p>
          </div>
        </div>
      </div>`;
  }
  return `<img src="${esc(p.url)}" alt="${esc(p.alt)}" class="max-h-full max-w-full select-none rounded-lg object-contain shadow-2xl" draggable="false">`;
}

function slideCaptionHtml(v) {
  return `
    <p class="flex items-center gap-3 text-[11px] font-semibold uppercase tracking-[0.28em] text-primary-300"><span class="h-px w-6 bg-current"></span>${esc(v.slide_eyebrow || 'Frequency Strategy Group')}</p>
    <p class="mt-4 font-serif text-3xl leading-tight sm:text-4xl">${titleHtml(v.slide_title || 'Equitable strategy. *Expert execution.*')}</p>
    ${v.slide_text ? `<p class="mt-4 text-sm leading-relaxed text-white/75">${esc(v.slide_text)}</p>` : ''}
    ${v.slide_cta && v.slide_link ? `<p class="mt-5 text-sm font-semibold text-primary-300">${esc(v.slide_cta)} →</p>` : ''}`;
}

function paintViewer() {
  const p = data.photos[current];
  if (!viewer || !p) return;
  const n = data.photos.length;
  const btn = 'inline-flex items-center gap-1.5 rounded-xl px-3 py-2 text-sm font-semibold transition-colors';
  const ghost = `${btn} bg-white/10 hover:bg-white/20`;
  const viewBtn = (key, label) => `<button type="button" data-view="${key}" aria-pressed="${view === key}" class="rounded-lg px-3 py-1.5 text-xs font-semibold transition-colors ${view === key ? 'bg-white text-gray-900' : 'text-white/70 hover:text-white'}">${label}</button>`;
  const field = 'w-full rounded-xl border-0 bg-white/10 px-3.5 py-2 text-sm text-white placeholder-white/40 outline-none focus:ring-2 focus:ring-primary-400';
  const label = 'mb-1 block text-[11px] font-semibold uppercase tracking-wider text-white/50';
  const flags = hints(p);

  viewer.innerHTML = `
    <div class="flex items-center gap-3 border-b border-white/10 px-4 py-3 sm:px-6">
      <span class="rounded-lg bg-white/10 px-2.5 py-1 text-sm font-bold">${current + 1} <span class="font-semibold text-white/50">of ${n}</span></span>
      <div class="hidden rounded-xl bg-white/10 p-1 sm:inline-flex" role="group" aria-label="Preview as">
        ${viewBtn('original', 'Original')}${viewBtn('slide', 'As a slide')}${viewBtn('hero', 'As a page hero')}
      </div>
      <button type="button" data-v="close" class="ml-auto flex h-10 w-10 items-center justify-center rounded-xl bg-white/10 hover:bg-white/20" aria-label="Close (Esc)">${svg(ICON.x, 'h-5 w-5')}</button>
    </div>

    <div class="relative flex min-h-0 flex-1 items-center justify-center p-4 sm:p-8" data-stage>
      ${stageHtml(p)}
      ${n > 1 ? `
        <button type="button" data-v="prev" class="absolute left-2 top-1/2 flex h-12 w-12 -translate-y-1/2 items-center justify-center rounded-full bg-white/10 hover:bg-white/25 sm:left-5" aria-label="Previous (←)">${svg(ICON.left, 'h-6 w-6')}</button>
        <button type="button" data-v="next" class="absolute right-2 top-1/2 flex h-12 w-12 -translate-y-1/2 items-center justify-center rounded-full bg-white/10 hover:bg-white/25 sm:right-5" aria-label="Next (→)">${svg(ICON.right, 'h-6 w-6')}</button>` : ''}
    </div>

    <form data-editor class="max-h-[46vh] space-y-4 overflow-y-auto border-t border-white/10 px-4 py-4 sm:px-6">
      <div class="flex w-max rounded-xl bg-white/10 p-1 sm:hidden" role="group" aria-label="Preview as">${viewBtn('original', 'Original')}${viewBtn('slide', 'Slide')}${viewBtn('hero', 'Hero')}</div>
      <div class="flex flex-wrap items-center gap-x-6 gap-y-2">
        <label class="inline-flex cursor-pointer items-center gap-2.5 text-sm font-semibold">
          <input type="checkbox" name="in_slideshow" ${p.in_slideshow ? 'checked' : ''} class="h-4 w-4 rounded border-white/30 bg-white/10 text-primary-500 focus:ring-primary-400">
          Play in the home slideshow
        </label>
        ${p.used_in.length ? `<p class="text-xs text-white/60">Also shown in: ${p.used_in.map(esc).join(' · ')}</p>` : '<p class="text-xs text-white/40">Not used in any other spot.</p>'}
      </div>
      <fieldset class="grid gap-3 md:grid-cols-12" ${p.in_slideshow ? '' : 'disabled'} data-caption-fields>
        <legend class="sr-only">Slide caption</legend>
        <div class="md:col-span-3"><label class="${label}" for="sp-eyebrow">Small heading</label>
          <input id="sp-eyebrow" name="slide_eyebrow" maxlength="40" value="${esc(p.slide_eyebrow)}" placeholder="e.g. Funding" class="${field}"></div>
        <div class="md:col-span-9"><label class="${label}" for="sp-title">Headline — put *words in asterisks* for gold italics</label>
          <input id="sp-title" name="slide_title" maxlength="120" value="${esc(p.slide_title)}" placeholder="e.g. Funding, made *attainable.*" class="${field}"></div>
        <div class="md:col-span-12"><label class="${label}" for="sp-text">Text</label>
          <textarea id="sp-text" name="slide_text" maxlength="300" rows="2" placeholder="One or two sentences" class="${field} resize-y">${esc(p.slide_text)}</textarea></div>
        <div class="md:col-span-4"><label class="${label}" for="sp-cta">Button label</label>
          <input id="sp-cta" name="slide_cta" maxlength="40" value="${esc(p.slide_cta)}" placeholder="e.g. Book a consultation" class="${field}"></div>
        <div class="md:col-span-8"><label class="${label}" for="sp-link">Button link — a page on the site</label>
          <input id="sp-link" name="slide_link" maxlength="200" list="sp-links" value="${esc(p.slide_link)}" placeholder="e.g. contact?type=consultation" class="${field}">
          <datalist id="sp-links">${(data.links || []).map((l) => `<option value="${esc(l.path)}">${esc(l.label)}</option>`).join('')}</datalist></div>
      </fieldset>
      <div><label class="${label}" for="sp-alt">Description — for screen readers and search engines</label>
        <input id="sp-alt" name="alt" maxlength="255" value="${esc(p.alt)}" placeholder="What’s in the photo, e.g. Two colleagues reviewing a plan on a laptop" class="${field}"></div>

      <div class="flex flex-col gap-3 lg:flex-row lg:items-center">
        <button type="submit" class="${btn} bg-primary-500 text-secondary-950 hover:bg-primary-400 disabled:opacity-40" data-save disabled>${svg(ICON.check)} Save changes</button>
        <div class="flex flex-wrap items-center gap-1.5 lg:ml-auto">
          <button type="button" data-v="first" ${current === 0 ? 'disabled' : ''} class="${ghost} disabled:opacity-30" title="Make it the first photo">${svg(ICON.first)} First</button>
          <button type="button" data-v="earlier" ${current === 0 ? 'disabled' : ''} class="${ghost} disabled:opacity-30" title="Move one place earlier">${svg(ICON.left)} Earlier</button>
          <button type="button" data-v="later" ${current === n - 1 ? 'disabled' : ''} class="${ghost} disabled:opacity-30" title="Move one place later">Later ${svg(ICON.right)}</button>
          <button type="button" data-v="last" ${current === n - 1 ? 'disabled' : ''} class="${ghost} disabled:opacity-30" title="Make it the last photo">Last ${svg(ICON.last)}</button>
          ${p.missing ? '' : `<a href="${esc(p.url)}" download="${esc(p.file)}" class="${ghost}" title="Download">${svg(ICON.download)}<span class="sr-only">Download</span></a>`}
          <button type="button" data-v="delete" class="${btn} bg-red-500/20 text-red-200 hover:bg-red-500/30">${svg(ICON.trash)} Delete</button>
        </div>
      </div>
      <p class="text-xs text-white/50">
        ${[p.width && p.height ? `${p.width} × ${p.height}px` : '', mb(p.bytes), p.added ? `added ${shortDate(p.added)}${p.added_by ? ` by ${esc(p.added_by)}` : ''}` : '', esc(p.file)].filter(Boolean).join(' · ')}
        ${flags.length ? `<span class="ml-2 font-semibold text-amber-300">${flags.map((f) => esc(f[1])).join(' · ')}</span>` : ''}
      </p>
    </form>`;
}

/** Live preview + Save button state as the caption is typed. */
function onViewerInput(e) {
  if (!e.target.closest('form[data-editor]')) return;
  if (e.target.name === 'in_slideshow') {
    viewer.querySelector('[data-caption-fields]').disabled = !e.target.checked;
    if (view === 'slide') viewer.querySelector('[data-stage]').firstElementChild.outerHTML = stageHtml(data.photos[current]);
  }
  const preview = viewer.querySelector('[data-slide-preview]');
  if (preview) preview.innerHTML = slideCaptionHtml(formValues());
  viewer.querySelector('[data-save]').disabled = !isDirty();
}

async function onViewerSave(e) {
  e.preventDefault();
  if (!isDirty()) return;
  const v = formValues();
  const p = data.photos[current];
  const body = { action: 'update', id: p.id, in_slideshow: v.in_slideshow, alt: v.alt.trim() };
  // Only send the caption while it can be edited (the fieldset is disabled otherwise)
  if (v.in_slideshow) Object.assign(body, { slide_eyebrow: v.slide_eyebrow, slide_title: v.slide_title, slide_text: v.slide_text, slide_cta: v.slide_cta, slide_link: v.slide_link });
  await run(body);
}

async function move(to) {
  if (isDirty() && !(await confirmDialog('Save your changes to this photo first? Moving it now discards them.', 'Discard and move', 'Stay'))) return;
  const ids = data.photos.map((p) => p.id);
  const id = ids[current];
  ids.splice(current, 1);
  const target = { first: 0, last: ids.length, earlier: current - 1, later: current + 1 }[to];
  ids.splice(target, 0, id);
  current = target;
  await saveOrder(ids);
}

async function onViewerClick(e) {
  const v = e.target.closest('[data-view]');
  if (v) {
    view = v.dataset.view;
    viewer.querySelector('[data-stage]').firstElementChild.outerHTML = stageHtml(data.photos[current]);
    viewer.querySelectorAll('[data-view]').forEach((b) => {
      const on = b.dataset.view === view;
      b.setAttribute('aria-pressed', String(on));
      b.classList.toggle('bg-white', on); b.classList.toggle('text-gray-900', on);
      b.classList.toggle('text-white/70', !on);
    });
    return;
  }
  const a = e.target.closest('[data-v]')?.dataset.v;
  if (!a) {
    if (e.target === viewer.querySelector('[data-stage]')) requestClose(); // the dark area around the photo
    return;
  }
  if (a === 'close') requestClose();
  else if (a === 'prev') step(-1);
  else if (a === 'next') step(1);
  else if (a === 'delete') removePhotos([data.photos[current].id]);
  else move(a);
}

async function requestClose() {
  if (isDirty() && !(await confirmDialog('You have unsaved changes to this photo. Close without saving?', 'Close', 'Stay'))) return;
  closeViewer();
}

function onViewerKey(e) {
  if (!viewer || viewer.classList.contains('hidden')) return;
  if (e.target.matches?.('input, textarea')) { if (e.key === 'Escape') e.target.blur(); return; }
  if (document.querySelector('.fixed.inset-0 #confirm-proceed')) return; // a confirm dialog is up
  if (e.key === 'Escape') requestClose();
  else if (e.key === 'ArrowLeft') step(-1);
  else if (e.key === 'ArrowRight') step(1);
}
