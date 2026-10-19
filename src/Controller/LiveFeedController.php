<?php
// /src/Controller/LiveFeedController.php

declare(strict_types=1);

namespace Src\Controller;

use App\Models\Post;
use App\Models\PostComment;
use App\Models\PostLike;
use App\Traits\RecentActivityLogger;
use App\Utils\IdEncoder;
use Illuminate\Pagination\LengthAwarePaginator;
use Src\Service\AuthService;

/**
 * Live Feed — the team's public updates: text / photo / video posts, likes
 * and comments. Retrofitted from the CatScript Apps Social Feed:
 *
 *  - public: anyone, signed in or not, can read the posts and comments
 *    (viewer id 0 = a guest); signed-in staff and admins post, like and
 *    comment (server/api/live-feed.php guards every write);
 *  - pages of PER_PAGE posts ("Load older posts"), and a signature of the
 *    first page so the page's 30-second live check only redraws on real
 *    changes (not just "5 minutes ago" ticking over);
 *  - authors can delete their own posts and comments; admins can remove
 *    anyone's (moderation);
 *  - deleteAllFor() clears a member's posts, comments and likes when their
 *    account is deleted.
 */
class LiveFeedController
{
    use RecentActivityLogger;

    public const PER_PAGE = 15;

    /** Where post photos and videos live, under public/. */
    public const MEDIA_DIR = 'images/uploads/posts/';

    private const MAX_POST = 5000;
    private const MAX_COMMENT = 2000;

    // ============================================================
    // Reads
    // ============================================================

    public static function feed(int $page = 1): LengthAwarePaginator
    {
        return Post::query()->with(['user', 'likes', 'comments.user'])->orderByDesc('created_at')->orderByDesc('post_id')
            ->paginate(self::PER_PAGE, ['*'], 'page', max(1, $page));
    }

    /**
     * One page of the feed as card HTML, plus what the page needs to page
     * further and to notice changes (sig: posts, likes and comments on it).
     * $viewerId 0 = a guest (read-only cards).
     * @return array{html: string, has_more: bool, next_page: int, sig: string, total: int}
     */
    public static function page(int $viewerId, int $page): array
    {
        $posts = self::feed($page);

        $html = '';
        $sig = [];
        foreach ($posts as $post) {
            $html .= self::renderPostCard($post, $viewerId);
            $sig[] = $post->post_id . ':' . $post->likes->count() . ':' . $post->comments->count();
        }

        return [
            'html'      => $html,
            'has_more'  => $posts->hasMorePages(),
            'next_page' => $posts->currentPage() + 1,
            'sig'       => md5(implode(',', $sig)),
            'total'     => $posts->total(),
        ];
    }

    /** Posts this member has shared (Dashboard / sidebar). */
    public static function postCount(int $userId): int
    {
        return Post::where('orig_user_id', $userId)->count();
    }

    /** Comments + counts for one post (the view-post window). */
    public static function postDetails(string $encodedPostId, int $viewerId): array
    {
        $postId = self::decodeId($encodedPostId);
        $post = $postId ? Post::with(['likes', 'comments.user'])->find($postId) : null;

        if (!$post) {
            return ['success' => false, 'message' => 'That post no longer exists.'];
        }

        $commentsHtml = '';
        foreach ($post->comments as $comment) {
            $commentsHtml .= self::renderCommentHtml($comment, $viewerId);
        }

        return [
            'success'        => true,
            'likes'          => $post->likes->count(),
            'comments_count' => $post->comments->count(),
            'html'           => $commentsHtml,
        ];
    }

    // ============================================================
    // Writes
    // ============================================================

    /**
     * @param array $input content, media_url (a file name from an upload), media_type
     * @return array{success: bool, errors: string[], post: ?Post}
     */
    public static function create(array $input, int $userId): array
    {
        $content = trim((string) ($input['content'] ?? ''));
        $media = basename(trim((string) ($input['media_url'] ?? '')));
        $mediaType = (string) ($input['media_type'] ?? 'none');

        if (mb_strlen($content) > self::MAX_POST) {
            return ['success' => false, 'errors' => ['Posts can be up to ' . number_format(self::MAX_POST) . ' characters.'], 'post' => null];
        }
        // Only a file our own uploaders put in the posts folder can be attached
        if ($media !== '' && ($media[0] === '.' || !is_file(self::mediaPath($media)))) {
            return ['success' => false, 'errors' => ['That photo or video upload was not found — please attach it again.'], 'post' => null];
        }
        if ($content === '' && $media === '') {
            return ['success' => false, 'errors' => ['Write something or attach a photo or video.'], 'post' => null];
        }
        if (!in_array($mediaType, ['image', 'video'], true)) {
            $mediaType = 'image';
        }

        $post = Post::create([
            'orig_user_id' => $userId,
            'content'      => $content !== '' ? $content : null,
            'media_url'    => $media !== '' ? $media : null,
            'media_type'   => $media !== '' ? $mediaType : 'none',
        ]);
        $post->load(['user', 'likes', 'comments.user']);
        static::logActivity('Shared a post on the Live Feed', 'Live Feed', (int) $post->post_id, $userId);

        return ['success' => true, 'errors' => [], 'post' => $post];
    }

    /** @return array{success: bool, action?: string, like_count?: int, message?: string} */
    public static function toggleLike(string $encodedPostId, int $userId): array
    {
        $postId = self::decodeId($encodedPostId);
        if (!$postId || !Post::where('post_id', $postId)->exists()) {
            return ['success' => false, 'message' => 'That post no longer exists.'];
        }

        $existing = PostLike::where('post_id', $postId)->where('orig_user_id', $userId)->first();
        if ($existing) {
            $existing->delete();
            $action = 'unliked';
        } else {
            PostLike::firstOrCreate(['post_id' => $postId, 'orig_user_id' => $userId]);
            $action = 'liked';
        }

        return ['success' => true, 'action' => $action, 'like_count' => PostLike::where('post_id', $postId)->count()];
    }

    /** @param array $input post_id (encoded), content */
    public static function addComment(array $input, int $userId): array
    {
        $postId = self::decodeId((string) ($input['post_id'] ?? ''));
        $text = trim((string) ($input['content'] ?? ''));

        if (!$postId || !Post::where('post_id', $postId)->exists()) {
            return ['success' => false, 'message' => 'That post no longer exists.'];
        }
        if ($text === '') {
            return ['success' => false, 'message' => 'Write a comment first.'];
        }
        if (mb_strlen($text) > self::MAX_COMMENT) {
            return ['success' => false, 'message' => 'Comments can be up to ' . number_format(self::MAX_COMMENT) . ' characters.'];
        }

        $comment = PostComment::create(['post_id' => $postId, 'orig_user_id' => $userId, 'comment_text' => $text]);
        $comment->load('user');

        return ['success' => true, 'commentHtml' => self::renderCommentHtml($comment, $userId)];
    }

    /** The comment's author, or an admin. */
    public static function deleteComment(int $commentId, int $userId): array
    {
        $comment = PostComment::find($commentId);
        if (!$comment) {
            return ['success' => false, 'message' => 'That comment no longer exists.'];
        }
        if ((int) $comment->orig_user_id !== $userId && !AuthService::isAdmin()) {
            return ['success' => false, 'message' => 'You can only delete your own comments.'];
        }

        $comment->delete();
        return ['success' => true];
    }

    /** The post's author, or an admin. Also deletes its photo / video file. */
    public static function deletePost(string $encodedPostId, int $userId): array
    {
        $postId = self::decodeId($encodedPostId);
        $post = $postId ? Post::find($postId) : null;
        if (!$post) {
            return ['success' => false, 'message' => 'That post no longer exists.'];
        }

        $own = (int) $post->orig_user_id === $userId;
        if (!$own && !AuthService::isAdmin()) {
            return ['success' => false, 'message' => 'You can only delete your own posts.'];
        }

        self::deleteMedia($post);
        $post->delete(); // comments and likes go with it (ON DELETE CASCADE)
        static::logActivity($own ? 'Deleted a Live Feed post' : 'Removed a member’s Live Feed post', 'Live Feed', $postId, $userId);

        return ['success' => true];
    }

    /** A member's account is being deleted: remove everything of theirs here. */
    public static function deleteAllFor(int $userId): void
    {
        foreach (Post::where('orig_user_id', $userId)->get() as $post) {
            self::deleteMedia($post);
            $post->delete();
        }
        PostComment::where('orig_user_id', $userId)->delete();
        PostLike::where('orig_user_id', $userId)->delete();
    }

    // ============================================================
    // Rendering
    // ============================================================

    public static function renderPostCard(Post $post, int $viewerId): string
    {
        $author = $post->user;
        $isOwn = $viewerId > 0 && (int) $post->orig_user_id === $viewerId;

        $data = [
            'encoded_id'    => IdEncoder::encode((int) $post->post_id),
            'author'        => $author->full_name ?? 'Former member',
            'author_avatar' => $author->avatar_url ?? null,
            'content'       => $post->content ?? '',
            'posted_at'     => $post->created_at?->toIso8601String() ?? '',
            'posted_label'  => $post->created_at?->format('M j, Y g:i a') ?? '',
            'time_ago'      => $post->created_at?->diffForHumans() ?? '',
            'like_count'    => $post->likes->count(),
            'comment_count' => $post->comments->count(),
            'is_liked'      => $viewerId > 0 && $post->likes->contains(fn($like) => (int) $like->orig_user_id === $viewerId),
            'can_interact'  => $viewerId > 0, // guests see the counts, not the buttons
            'media_url'     => $post->media_url,
            'media_type'    => $post->media_type,
            'can_delete'    => $isOwn || ($viewerId > 0 && AuthService::isAdmin()),
            'is_own'        => $isOwn,
        ];

        $assetBase = getAssetBase();
        ob_start();
        include __DIR__ . '/../../resources/views/components/live-feed/post-card.php';
        return ob_get_clean() ?: '';
    }

    private static function renderCommentHtml(PostComment $comment, int $viewerId): string
    {
        $author = $comment->user;
        $data = [
            'id'            => (int) $comment->comment_id,
            'author'        => $author->full_name ?? 'Former member',
            'author_avatar' => $author->avatar_url ?? null,
            'comment_text'  => $comment->comment_text,
            'time_ago'      => $comment->created_at?->diffForHumans() ?? '',
            'can_delete'    => $viewerId > 0 && ((int) $comment->orig_user_id === $viewerId || AuthService::isAdmin()),
        ];

        $assetBase = getAssetBase();
        ob_start();
        include __DIR__ . '/../../resources/views/components/live-feed/comment-row.php';
        return ob_get_clean() ?: '';
    }

    // ============================================================
    // Helpers
    // ============================================================

    public static function mediaPath(string $fileName = ''): string
    {
        return __DIR__ . '/../../public/' . self::MEDIA_DIR . $fileName;
    }

    private static function deleteMedia(Post $post): void
    {
        if (!empty($post->media_url)) {
            $path = self::mediaPath(basename($post->media_url));
            if (is_file($path)) {
                @unlink($path);
            }
        }
    }

    /** Post ids are obfuscated in the page (IdEncoder); comment ids aren't. */
    private static function decodeId(string $raw): ?int
    {
        if ($raw === '') {
            return null;
        }
        return ctype_digit($raw) ? (int) $raw : IdEncoder::decode($raw);
    }
}
