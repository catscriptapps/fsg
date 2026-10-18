<?php
// /server/api/live-feed.php
//
// The Live Feed (Src\Controller\LiveFeedController), JSON in / out:
//   GET  ?scope=everyone|following&page=N   one page of post cards (+ sig)
//   GET  ?action=get-details&post_id=…      a post's comments
//   POST {content, media_url, media_type}   share a post
//   POST {action: toggle-like | add-comment | delete-comment | delete-post, …}

declare(strict_types=1);

use Src\Controller\LiveFeedController;
use Src\Service\AuthService;

header('Content-Type: application/json; charset=utf-8');

$userId = AuthService::userId();
if (!$userId) {
    json_response(['success' => false, 'message' => 'Please sign in to use the Live Feed.'], 401);
}
if (!AuthService::hasAccess('Live Feed')) {
    json_response(['success' => false, 'message' => "You don't have access to the Live Feed."], 403);
}

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$input = array_merge($_GET, $_POST, json_decode(file_get_contents('php://input'), true) ?: []);
$action = (string) ($input['action'] ?? '');

if ($method === 'GET') {
    if ($action === 'get-details') {
        $result = LiveFeedController::postDetails((string) ($input['post_id'] ?? ''), $userId);
        json_response($result, $result['success'] ? 200 : 404);
    }

    json_response(['success' => true] + LiveFeedController::page($userId, (string) ($input['scope'] ?? 'everyone'), (int) ($input['page'] ?? 1)));
}

if ($method === 'POST') {
    switch ($action) {
        case 'toggle-like':
            $result = LiveFeedController::toggleLike((string) ($input['post_id'] ?? ''), $userId);
            json_response($result, $result['success'] ? 200 : 404);

        case 'add-comment':
            $result = LiveFeedController::addComment($input, $userId);
            json_response($result, $result['success'] ? 200 : 422);

        case 'delete-comment':
            $result = LiveFeedController::deleteComment((int) ($input['comment_id'] ?? 0), $userId);
            json_response($result, $result['success'] ? 200 : 403);

        case 'delete-post':
            $result = LiveFeedController::deletePost((string) ($input['post_id'] ?? ''), $userId);
            json_response($result, $result['success'] ? 200 : 403);

        case '':
        case 'create':
            $result = LiveFeedController::create($input, $userId);
            if (!$result['success']) {
                json_response(['success' => false, 'errors' => $result['errors']], 422);
            }
            json_response(['success' => true, 'html' => LiveFeedController::renderPostCard($result['post'], $userId)]);
    }
    json_response(['success' => false, 'message' => 'Unknown action.'], 400);
}

json_response(['success' => false, 'message' => 'Method not allowed'], 405);
