<?php
// /server/api/site-photos.php
//
// Admin only — the /site-photos page (Src\Controller\SitePhotosController).
// GET                                          -> state
// POST multipart images[]                      -> add photos to the library (and slideshow)
// POST multipart images[] ?slot={key}          -> add one photo and put it in that spot
// POST JSON {action: 'update', id, alt?, in_slideshow?, slide_eyebrow?, slide_title?, slide_text?, slide_cta?, slide_link?}
//           {action: 'reorder', ids: [...]}
//           {action: 'delete', ids: [...] | ['*']}   (['*'] = delete all)
//           {action: 'assign', slot, photo_id|null}
// Every success answers with the fresh state (photos + slots).

declare(strict_types=1);

use Src\Content\Photos;
use Src\Controller\SitePhotosController;
use Src\Service\AuthService;

header('Content-Type: application/json; charset=UTF-8');

if (!AuthService::isAdmin()) {
    json_response(['success' => false, 'messages' => ['Only an administrator can manage site photos.']], 403);
}
if (!Photos::installed()) {
    json_response(['success' => false, 'messages' => ['Site photos aren’t set up yet — run the DB update (header button) first.']], 409);
}

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

try {
    if ($method === 'GET') {
        json_response(['success' => true] + SitePhotosController::state());
    }
    if ($method !== 'POST') {
        json_response(['success' => false, 'messages' => ['Method not supported.']], 405);
    }

    // Uploads (the shared upload modal posts images[])
    if (!empty($_FILES['images']['tmp_name'][0])) {
        $slot = (string) ($_GET['slot'] ?? '');
        if ($slot !== '' && !isset(Photos::slots()[$slot])) {
            json_response(['success' => false, 'messages' => ['Unknown photo spot.']], 422);
        }
        $result = SitePhotosController::upload($_FILES['images'], AuthService::userId(), $slot === '');
        if ($result['success'] && $slot !== '') {
            SitePhotosController::assign($slot, $result['files'][0]['id']);
            $result['messages'] = ['Photo added to that spot.'];
        }
        Photos::flush();
        json_response($result + ($result['success'] ? SitePhotosController::state() : []), $result['success'] ? 200 : 422);
    }

    $data = json_decode(file_get_contents('php://input') ?: '', true);
    if (!is_array($data)) {
        json_response(['success' => false, 'messages' => ['No image or action received.']], 400);
    }

    $result = match ((string) ($data['action'] ?? '')) {
        'update'  => SitePhotosController::update((string) ($data['id'] ?? ''), array_intersect_key($data, array_flip(['alt', 'in_slideshow', 'slide_eyebrow', 'slide_title', 'slide_text', 'slide_cta', 'slide_link']))),
        'reorder' => SitePhotosController::reorder((array) ($data['ids'] ?? [])),
        'delete'  => SitePhotosController::delete(array_map('strval', (array) ($data['ids'] ?? []))),
        'assign'  => SitePhotosController::assign((string) ($data['slot'] ?? ''), isset($data['photo_id']) ? (string) $data['photo_id'] : null),
        default   => ['success' => false, 'messages' => ['Unknown action.']],
    };

    Photos::flush();
    json_response($result + ($result['success'] ? SitePhotosController::state() : []), $result['success'] ? 200 : 422);
} catch (\Throwable $e) {
    error_log('[api site-photos] ' . $e->getMessage());
    json_response(['success' => false, 'messages' => ['Something went wrong saving the photos.']], 500);
}
