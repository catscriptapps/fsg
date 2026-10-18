<?php
// /server/api/live-feed-photo.php
//
// One photo for a Live Feed post (the composer's "Photo" button, through the
// shared upload modal). Resized by ImageUploadService into
// public/images/uploads/posts/; answers with the file name the post saves.

declare(strict_types=1);

use Src\Controller\LiveFeedController;
use Src\Service\AuthService;
use Src\Service\ImageUploadService;

header('Content-Type: application/json; charset=utf-8');

if (!AuthService::userId()) {
    json_response(['success' => false, 'message' => 'Please sign in to upload photos.'], 401);
}
if (!AuthService::hasAccess('Live Feed')) {
    json_response(['success' => false, 'message' => "You don't have access to the Live Feed."], 403);
}
if (empty($_FILES['images']) || empty($_FILES['images']['tmp_name'][0])) {
    json_response(['success' => false, 'message' => 'No photo was received.'], 400);
}

$dir = LiveFeedController::mediaPath();
if (!is_dir($dir)) {
    mkdir($dir, 0775, true);
}

// Exactly one file
$single = [];
foreach (['name', 'type', 'tmp_name', 'error', 'size'] as $key) {
    $single[$key] = [$_FILES['images'][$key][0]];
}

$prefix = getAssetBase() . LiveFeedController::MEDIA_DIR;
$uploaded = (new ImageUploadService(realpath($dir) . '/', 2000, 88))->upload($single, function (array $files) use ($prefix) {
    foreach ($files as $i => $file) {
        $files[$i]['fileUrl'] = $prefix . $file['fileName'];
    }
    return $files;
});

if (empty($uploaded) || (isset($uploaded['success']) && $uploaded['success'] === false) || empty($uploaded[0]['fileName'])) {
    json_response(['success' => false, 'message' => 'The photo could not be saved.'], 500);
}

json_response([
    'success'  => true,
    'filename' => $uploaded[0]['fileName'],
    'url'      => $uploaded[0]['fileUrl'],
    'files'    => [['url' => $uploaded[0]['fileUrl'], 'filename' => $uploaded[0]['fileName']]],
]);
