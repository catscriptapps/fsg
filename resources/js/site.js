// /resources/js/site.js
//
// Public website (layouts/site.php): CatScript SPA partial navigation
// (utils/spa-router.js — every a[data-partial] swaps #main-content), Alpine
// for the nav dropdowns and mobile drawer, the header's solid-on-scroll and
// active-link state, reveal-on-scroll, and the inquiry forms
// (components/site/inquiry-form.php → api/contact). Everything page-level is
// re-initialised on each 'partial-load'.

import Alpine from 'alpinejs';
import collapse from '@alpinejs/collapse';
import { escapeHtml } from './utils/escape-html.js';
import { bindPartialLinks } from './utils/spa-router.js';
import { setupSpinner } from './ui/spinner.js';
import { setupScrollToTop } from './ui/scroll-to-top.js';
import { LoginModal } from './modals/login-modal.js';

Alpine.plugin(collapse);
window.Alpine = Alpine;
Alpine.start();

window.__siteReady = true;

/** The current page's site path ('home', 'about', 'services/…'), without the app's base path */
function currentPath() {
  const base = new URL(window.APP_CONFIG?.baseUrl || '/', window.location.origin).pathname;
  const path = window.location.pathname.startsWith(base) ? window.location.pathname.slice(base.length) : window.location.pathname;
  return path.replace(/^\/+|\/+$/g, '') || 'home';
}

/** Re-applies the header's active highlight (hooks rendered by partials/site-header.php) */
function setActiveNav() {
  const path = currentPath();
  const swap = (el, on) => {
    const add = (on ? el.dataset.on : el.dataset.off) || '';
    const remove = (on ? el.dataset.off : el.dataset.on) || '';
    if (remove) el.classList.remove(...remove.split(' '));
    if (add) el.classList.add(...add.split(' '));
    if (el.tagName === 'A') {
      if (on) el.setAttribute('aria-current', 'page');
      else el.removeAttribute('aria-current');
    }
  };
  document.querySelectorAll('[data-nav-match]').forEach((el) => {
    swap(el, el.dataset.navMatch.split(' ').some((p) => path === p || path.startsWith(`${p}/`)));
  });
  document.querySelectorAll('[data-nav-exact]').forEach((el) => swap(el, el.dataset.navExact === path));
}

/** Keeps the meta description, canonical and Open Graph tags in step with partial loads */
function updateMeta(summary) {
  const set = (selector, attr, value) => document.querySelector(selector)?.setAttribute(attr, value);
  const canonical = `${window.location.origin}${window.location.pathname === '/home' ? '/' : window.location.pathname}`;
  set('link[rel="canonical"]', 'href', canonical);
  set('meta[property="og:url"]', 'content', canonical);
  set('meta[property="og:title"]', 'content', document.title);
  if (summary) {
    set('meta[name="description"]', 'content', summary);
    set('meta[property="og:description"]', 'content', summary);
  }
}

function initHeader() {
  const header = document.querySelector('[data-site-header]');
  if (!header) return;
  const update = () => header.classList.toggle('is-solid', window.scrollY > 24);
  update();
  window.addEventListener('scroll', update, { passive: true });
}

const revealObserver = 'IntersectionObserver' in window
  ? new IntersectionObserver((entries) => {
    entries.forEach((entry) => {
      if (!entry.isIntersecting) return;
      entry.target.classList.add('is-visible');
      revealObserver.unobserve(entry.target);
    });
  }, { rootMargin: '0px 0px -8% 0px', threshold: 0.08 })
  : null;

/** Watches every not-yet-revealed [data-reveal] (call again after a partial load) */
function initReveal() {
  document.querySelectorAll('[data-reveal]:not(.is-visible)').forEach((el) => {
    if (revealObserver) revealObserver.observe(el);
    else el.classList.add('is-visible');
  });
}

function initInquiryForms() {
  document.querySelectorAll('[data-inquiry-form]').forEach((form) => {
    // Bind each form once (partial loads bring in new ones)
    if (form.dataset.ready) return;
    form.dataset.ready = 'true';
    const loadedAt = Date.now(); // for the server's time trap

    const wrap = form.closest('[data-inquiry]');
    const done = wrap.querySelector('[data-inquiry-done]');
    const errorsBox = form.querySelector('[data-errors]');
    const button = form.querySelector('button[type="submit"]');
    const label = button.querySelector('[data-label]');
    const field = (name) => form.elements.namedItem(name);

    const mark = (el, bad) => el.setAttribute('aria-invalid', String(bad));
    const showErrors = (list) => {
      errorsBox.innerHTML = list
        .map((m) => `<p class="rounded-xl border border-red-200 bg-red-50 px-4 py-2.5 text-sm text-red-800">${escapeHtml(m)}</p>`)
        .join('');
    };

    const validate = () => {
      const errors = [];
      const name = field('full_name');
      const email = field('email');
      const message = field('message');
      const consent = field('consent');

      mark(name, !name.value.trim());
      if (!name.value.trim()) errors.push('Please enter your name.');
      const emailBad = !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email.value.trim());
      mark(email, emailBad);
      if (emailBad) errors.push('Please enter a valid email address so we can reply.');
      mark(message, !message.value.trim());
      if (!message.value.trim()) errors.push('Please tell us a little about your project or need.');
      if (!consent.checked) errors.push('Please confirm you consent to us using your information to respond.');
      return errors;
    };

    form.addEventListener('input', (e) => {
      if (e.target.getAttribute('aria-invalid') === 'true' && e.target.value.trim()) mark(e.target, false);
    });

    form.addEventListener('submit', async (e) => {
      e.preventDefault();
      errorsBox.innerHTML = '';
      const errors = validate();
      if (errors.length) {
        showErrors(errors);
        form.querySelector('[aria-invalid="true"]')?.focus();
        return;
      }

      const data = Object.fromEntries(new FormData(form).entries());
      data.consent = field('consent').checked;
      data.elapsed = Date.now() - loadedAt;

      button.disabled = true;
      label.textContent = 'Sending…';
      try {
        const res = await fetch(`${window.APP_CONFIG?.baseUrl || '/'}api/contact`, {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify(data),
        });
        const result = await res.json().catch(() => ({ success: false }));
        if (!result.success) {
          showErrors(result.messages || ['Sorry — your inquiry couldn’t be sent. Please try again, or email or call us directly.']);
          return;
        }
        done.querySelector('[data-done-text]').textContent = result.messages?.[0] || '';
        form.hidden = true;
        done.hidden = false;
        done.focus();
        done.scrollIntoView({ block: 'center', behavior: 'smooth' });
      } catch {
        showErrors(['Sorry — your inquiry couldn’t be sent. Check your connection and try again, or email or call us directly.']);
      } finally {
        button.disabled = false;
        label.textContent = 'Send inquiry';
      }
    });
  });
}

/**
 * Showcase slideshows (components/site/slideshow.php). The active tab's fill
 * animation (--slide-ms) is the timer: when it ends, the next slide shows.
 * Pausing (button, hover, keyboard focus, hidden tab) pauses that animation.
 */
function initSlideshows() {
  document.querySelectorAll('[data-slideshow]').forEach((root) => {
    if (root.dataset.ready) return;
    root.dataset.ready = 'true';

    const imgs = [...root.querySelectorAll('[data-slide-img]')];
    const captions = [...root.querySelectorAll('[data-slide]')];
    const tabs = [...root.querySelectorAll('[data-slide-tab]')];
    const num = root.querySelector('[data-slide-num]');
    const toggle = root.querySelector('[data-slide-toggle]');
    const reduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    let index = 0;
    let userPaused = reduced; // no autoplay for reduced-motion visitors
    let holds = 0;            // hover / focus / hidden-tab holds

    const show = (next) => {
      index = (next + imgs.length) % imgs.length;
      imgs.forEach((el, i) => el.classList.toggle('is-active', i === index));
      captions.forEach((el, i) => {
        const on = i === index;
        el.classList.toggle('is-active', on);
        el.toggleAttribute('inert', !on);
        if (on) el.removeAttribute('aria-hidden'); else el.setAttribute('aria-hidden', 'true');
      });
      tabs.forEach((el, i) => {
        el.setAttribute('aria-selected', String(i === index));
        el.classList.toggle('is-done', i < index);
      });
      if (num) num.textContent = String(index + 1).padStart(2, '0');
    };

    const sync = () => {
      const paused = userPaused || holds > 0;
      root.classList.toggle('is-paused', paused);
      toggle.querySelector('[data-icon-pause]').classList.toggle('hidden', userPaused);
      toggle.querySelector('[data-icon-play]').classList.toggle('hidden', !userPaused);
      toggle.querySelector('[data-toggle-label]').textContent = userPaused ? 'Play' : 'Pause';
      toggle.setAttribute('aria-label', userPaused ? 'Play slideshow' : 'Pause slideshow');
    };
    const hold = (on) => { holds = Math.max(0, holds + (on ? 1 : -1)); sync(); };

    // The active fill finishing = time for the next slide
    root.addEventListener('animationend', (e) => {
      if (e.animationName === 'slideFill' && !userPaused) show(index + 1);
    });
    root.querySelector('[data-slide-prev]').addEventListener('click', () => show(index - 1));
    root.querySelector('[data-slide-next]').addEventListener('click', () => show(index + 1));
    tabs.forEach((tab, i) => tab.addEventListener('click', () => show(i)));
    toggle.addEventListener('click', () => { userPaused = !userPaused; sync(); });

    root.addEventListener('mouseenter', () => hold(true));
    root.addEventListener('mouseleave', () => hold(false));
    root.addEventListener('focusin', () => hold(true));
    root.addEventListener('focusout', (e) => { if (!root.contains(e.relatedTarget)) hold(false); });
    root.addEventListener('keydown', (e) => {
      if (e.key === 'ArrowLeft') { e.preventDefault(); show(index - 1); }
      if (e.key === 'ArrowRight') { e.preventDefault(); show(index + 1); }
    });

    // Swipe
    let startX = null;
    root.addEventListener('pointerdown', (e) => { if (e.pointerType !== 'mouse') startX = e.clientX; });
    root.addEventListener('pointerup', (e) => {
      if (startX === null) return;
      const dx = e.clientX - startX;
      startX = null;
      if (Math.abs(dx) > 50) show(index + (dx < 0 ? 1 : -1));
    });

    // Don't run while the browser tab is hidden; stop for good once a partial load removes it
    const onVisibility = () => {
      if (!document.body.contains(root)) { document.removeEventListener('visibilitychange', onVisibility); return; }
      hold(document.hidden);
    };
    document.addEventListener('visibilitychange', onVisibility);

    show(0);
    sync();
  });
}

/**
 * Sign-in: every a[data-login-button] (header, mobile menu, footer) opens the
 * CatScript sign-in modal in place. The modal lives outside #main-content, so
 * one instance survives partial loads; on success it reloads this page.
 * ?login in the URL opens it straight away (as app.js does).
 */
function initSignIn() {
  new LoginModal('a[data-login-button]');
  const params = new URLSearchParams(window.location.search);
  if (!params.has('login')) return;
  params.delete('login');
  const query = params.toString();
  history.replaceState(history.state, '', window.location.pathname + (query ? `?${query}` : '') + window.location.hash);
  document.querySelector('a[data-login-button]')?.click();
}

/**
 * Live Feed (pages/live-feed.php): the post list with its live updates, the
 * post window, and — for signed-in staff — the composer (utils/live-feed/*).
 * Loaded only when the feed is open, so other pages don't carry the uploaders.
 */
let feed = null;
async function initLiveFeed() {
  if (!document.getElementById('live-feed-page')) return;
  if (!feed) {
    const [posts, viewPost, compose] = await Promise.all([
      import('./utils/live-feed/posts.js'),
      import('./utils/live-feed/view-post.js'),
      import('./utils/live-feed/compose.js'),
    ]);
    feed = { ...posts, ...viewPost, ...compose };
    document.addEventListener('click', (e) => {
      const trigger = e.target.closest('#live-feed-page [data-compose]');
      if (trigger) feed.openComposer(trigger.dataset.compose);
    });
  }
  feed.initPosts();
  feed.initViewPost();
}

document.addEventListener('DOMContentLoaded', () => {
  setupSpinner();
  setupScrollToTop();
  bindPartialLinks();
  initSignIn();
  initHeader();
  initReveal();
  initInquiryForms();
  initSlideshows();
  initLiveFeed();
});

// Router: title/summary of the page being loaded (fired before the swap)
let pendingSummary = '';
window.addEventListener('spa-navigation', (e) => {
  pendingSummary = e.detail?.summary || '';
  feed?.closeViewPost(); // never leave the feed's post window (and its scroll lock) behind
});

// Router: the new page is in #main-content
document.body.addEventListener('partial-load', () => {
  setActiveNav();
  updateMeta(pendingSummary);
  initReveal();
  initInquiryForms();
  initSlideshows();
  initLiveFeed();
});
