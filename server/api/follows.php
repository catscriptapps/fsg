<?php
// /server/api/follows.php
//
// The Live Feed's follow graph (Src\Controller\FollowsController):
//   GET  ?action=get-stats                      following / followers counts
//   GET  ?action=get-list&type=following|followers
//   GET  ?action=search&q=…                     people search
//   GET                                         people to follow
//   POST {following_id}                         follow / unfollow

declare(strict_types=1);

use Src\Controller\FollowsController;
use Src\Service\AuthService;

header('Content-Type: application/json; charset=utf-8');

$userId = AuthService::userId();
if (!$userId) {
    json_response(['success' => false, 'message' => 'Please sign in.'], 401);
}
if (!AuthService::hasAccess('Live Feed')) {
    json_response(['success' => false, 'message' => "You don't have access to the Live Feed."], 403);
}

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$input = array_merge($_GET, $_POST, json_decode(file_get_contents('php://input'), true) ?: []);
$action = (string) ($input['action'] ?? '');

if ($method === 'GET') {
    if ($action === 'get-stats') {
        json_response(['success' => true] + FollowsController::stats($userId));
    }

    $rows = match ($action) {
        'get-list' => FollowsController::list($userId, ($input['type'] ?? '') === 'followers' ? 'followers' : 'following'),
        'search'   => FollowsController::search((string) ($input['q'] ?? ''), $userId),
        default    => FollowsController::suggestions($userId),
    };
    json_response(['success' => true, 'users' => $rows, 'html' => FollowsController::renderRows($rows)]);
}

if ($method === 'POST') {
    $result = FollowsController::toggleFollow($userId, (int) ($input['following_id'] ?? 0));
    json_response($result + FollowsController::stats($userId), $result['success'] ? 200 : 422);
}

json_response(['success' => false, 'message' => 'Method not allowed'], 405);
