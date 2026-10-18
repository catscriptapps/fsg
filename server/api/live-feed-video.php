<?php
// /server/api/live-feed-video.php
//
// One video for a Live Feed post, received in chunks from
// modals/video-upload-modal.js (video_chunk + chunk_index / total_chunks /
// file_uuid) and stitched by VideoUploadService into
// public/images/uploads/posts/. Answers "uploading" until the last chunk,
// then the saved file name.

declare(strict_types=1);

use Src\Controller\LiveFeedController;
use Src\Service\AuthService;
use Src\Service\VideoUploadService;

header('Content-Type: application/json; charset=utf-8');

if (!AuthService::userId()) {
    json_response(['success' => false, 'message' => 'Please sign in to upload videos.'], 401);
}
if (!AuthService::hasAccess('Live Feed')) {
    json_response(['success' => false, 'message' => "You don't have access to the Live Feed."], 403);
}
if (empty($_FILES['video_chunk']['tmp_name'])) {
    json_response(['success' => false, 'message' => 'No video was received.'], 400);
}

try {
    $result = (new VideoUploadService(LiveFeedController::mediaPath()))->handleChunk(
        $_FILES['video_chunk'],
        (string) ($_POST['file_uuid'] ?? ''),
        (int) ($_POST['chunk_index'] ?? 0),
        (int) ($_POST['total_chunks'] ?? 1)
    );
} catch (\Throwable $e) {
    json_response(['success' => false, 'message' => $e->getMessage()], 422);
}

if ($result['status'] !== 'completed') {
    json_response(['success' => true, 'status' => 'uploading']);
}

$url = getAssetBase() . LiveFeedController::MEDIA_DIR . $result['fileName'];
json_response([
    'success'  => true,
    'filename' => $result['fileName'],
    'url'      => $url,
    'files'    => [['url' => $url, 'filename' => $result['fileName']]],
]);
