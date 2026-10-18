// /resources/js/pages/live-feed-page.js
//
// Live Feed (views/pages/live-feed.php) — retrofitted from the CatScript
// Apps Social Feed. The pieces live in utils/live-feed/:
//   posts.js      the list, Everyone / Following, older posts, likes,
//                 deleting, and the 30-second live updates
//   compose.js    the "New post" window (text + one photo or video)
//   view-post.js  the post window with its comments
//   people.js     people search, suggestions, follow / unfollow
// Called by app.js on a full load and after each SPA navigation here.

import { initPosts } from '../utils/live-feed/posts.js';
import { initViewPost } from '../utils/live-feed/view-post.js';
import { initPeople } from '../utils/live-feed/people.js';
import { openComposer } from '../utils/live-feed/compose.js';

let bound = false;

export function init() {
  if (!document.getElementById('live-feed-page')) return;

  initPosts();
  initViewPost();
  initPeople();

  if (!bound) {
    bound = true;
    document.addEventListener('click', (e) => {
      const trigger = e.target.closest('#live-feed-page [data-compose]');
      if (trigger) openComposer(trigger.dataset.compose);
    });
  }
}
