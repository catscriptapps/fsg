<?php
// /src/Controller/FollowsController.php

declare(strict_types=1);

namespace Src\Controller;

use App\Models\Follow;
use App\Models\User;
use Illuminate\Support\Collection;

/**
 * The follow graph behind the Live Feed's "Following" view: counts, the
 * following / followers lists, "people to follow" suggestions, people
 * search, and follow / unfollow. Retrofitted from the CatScript Apps Social
 * Feed (SocialRelationsController). Only active accounts are listed.
 */
class FollowsController
{
    public static function stats(int $userId): array
    {
        return [
            'following' => Follow::where('follower_id', $userId)->count(),
            'followers' => Follow::where('following_id', $userId)->count(),
        ];
    }

    /** @return array<int, array> rows for renderRows() */
    public static function list(int $userId, string $type): array
    {
        $ids = $type === 'followers'
            ? Follow::where('following_id', $userId)->pluck('follower_id')
            : Follow::where('follower_id', $userId)->pluck('following_id');

        return self::toRows(User::current()->whereIn('id', $ids)->orderBy('full_name')->get(), $userId);
    }

    /** A few active members the viewer doesn't follow yet. */
    public static function suggestions(int $userId, int $limit = 6): array
    {
        $exclude = Follow::where('follower_id', $userId)->pluck('following_id')->push($userId)->all();
        $users = User::current()->whereNotIn('id', $exclude)->inRandomOrder()->limit($limit)->get();

        return self::toRows($users, $userId);
    }

    public static function search(string $query, int $userId, int $limit = 10): array
    {
        $query = trim($query);
        if ($query === '') {
            return [];
        }

        $users = User::current()->where('id', '!=', $userId)
            ->where('full_name', 'like', '%' . addcslashes($query, '%_\\') . '%')
            ->orderBy('full_name')->limit($limit)->get();

        return self::toRows($users, $userId);
    }

    /** @return array{success: bool, status?: string, message?: string} */
    public static function toggleFollow(int $followerId, int $targetId): array
    {
        if ($followerId === $targetId) {
            return ['success' => false, 'message' => "You can't follow yourself."];
        }
        if (!User::current()->where('id', $targetId)->exists()) {
            return ['success' => false, 'message' => 'That member was not found.'];
        }

        $existing = Follow::where('follower_id', $followerId)->where('following_id', $targetId)->first();
        if ($existing) {
            $existing->delete();
            return ['success' => true, 'status' => 'unfollowed'];
        }

        Follow::firstOrCreate(['follower_id' => $followerId, 'following_id' => $targetId]);
        return ['success' => true, 'status' => 'followed'];
    }

    /** One HTML string of person rows (components/live-feed/person-row.php). */
    public static function renderRows(array $rows): string
    {
        $assetBase = getAssetBase();
        $html = '';
        foreach ($rows as $data) {
            ob_start();
            include __DIR__ . '/../../resources/views/components/live-feed/person-row.php';
            $html .= ob_get_clean() ?: '';
        }
        return $html;
    }

    private static function toRows(Collection $users, int $viewerId): array
    {
        $following = Follow::where('follower_id', $viewerId)->pluck('following_id')->all();
        $followers = Follow::where('following_id', $viewerId)->pluck('follower_id')->all();

        return $users->map(fn(User $u) => [
            'id'           => (int) $u->id,
            'name'         => $u->full_name,
            'avatar'       => $u->avatar_url,
            'is_following' => in_array((int) $u->id, $following, true),
            'follows_you'  => in_array((int) $u->id, $followers, true),
        ])->values()->all();
    }
}
