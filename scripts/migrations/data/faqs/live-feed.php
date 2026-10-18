<?php
// /scripts/migrations/data/faqs/live-feed.php
//
// Help-centre questions for the Live Feed (topic key "live-feed" in
// FaqsController::CATEGORIES). Same format as core.php; seeded by
// 2026_10_08_000003_seed_faqs.php on the next DB update.

return [

    ['live-feed', 'What is the Live Feed?', <<<'TXT'
The [Live Feed](/live-feed) is where members share updates with each other — a few words, a photo or a video. Everyone signed in can read it, like posts and join the conversation in the comments.

It's live: new posts, likes and comments show up on their own while you have it open, without reloading.
TXT],

    ['live-feed', 'What’s the difference between Everyone and Following?', <<<'TXT'
- **Everyone** — every member's posts, newest first.
- **Following** — just your own posts and those of the people you follow.

Switch between them at the top of the [Live Feed](/live-feed). Your choice is in the page address, so a bookmark to the Following view opens straight to it.
TXT],

    ['live-feed', 'How do I share a post?', <<<'TXT'
1. Click **New post** (or “Share an update…”) at the top of the [Live Feed](/live-feed).
2. Write your update — the smiley adds an emoji.
3. Optionally add **one photo or one video**.
4. Click **Post** (or press Ctrl+Enter).

Web addresses in your post become clickable links.
TXT],

    ['live-feed', 'What photos and videos can I add?', <<<'TXT'
- **Photos** — JPG, PNG, WEBP or iPhone HEIC; they're resized in your browser before uploading, so even big phone photos go up quickly.
- **Videos** — MP4, MOV, WEBM or OGG, up to **200 MB**. They upload in small pieces with a progress bar — keep the page open until it says the video is ready.

One photo or one video per post.
TXT],

    ['live-feed', 'How do I like or comment on a post?', <<<'TXT'
Tap the **heart** to like a post (tap again to undo). Tap **Comment** — or a post's photo — to open the post with its comments; type in the box at the bottom and press **Enter** to send (Shift+Enter starts a new line).
TXT],

    ['live-feed', 'How do I follow people?', <<<'TXT'
Use **People to follow** on the right of the [Live Feed](/live-feed), or search for someone by name in **Find people**, and click **Follow**. Their posts then appear in your **Following** view.

Click **Following** or **Followers** on your card to see those lists — you can follow back or unfollow from there too.
TXT],

    ['live-feed', 'Can I delete a post or comment?', <<<'TXT'
Yes — your own. Open the **⋯** menu on your post and choose **Delete post** (its comments and likes go with it), or hover over your comment and click **Delete**.

Admins can also remove anyone's post or comment, to keep the feed tidy.
TXT],

    ['live-feed', 'Who can see what I post?', <<<'TXT'
Every signed-in member. The Live Feed is never shown to visitors who aren't signed in, and it isn't public on the website.
TXT],
];
