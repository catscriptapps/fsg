<?php
// /src/Controller/SitePhotosController.php

declare(strict_types=1);

namespace Src\Controller;

use App\Traits\RecentActivityLogger;
use App\Utils\IdEncoder;
use Illuminate\Database\Capsule\Manager as Capsule;
use Src\Content\Photos;
use Src\Service\ImageUploadService;

/**
 * Admin management of the public site's photos (/site-photos page,
 * server/api/site-photos.php): the photo library — which is also the home
 * slideshow, in order — and the photo in each spot around the site
 * (Src\Content\Photos::slots()). Every write answers with the fresh state().
 *
 * Deleting a photo deletes its file from the server too, and empties any
 * spot that was using it.
 */
class SitePhotosController
{
    use RecentActivityLogger;

    private const MAX_ALT = 255;
    /** Slide caption fields => max length (matching the columns) */
    private const SLIDE_FIELDS = ['slide_eyebrow' => 40, 'slide_title' => 120, 'slide_text' => 300, 'slide_cta' => 40, 'slide_link' => 200];

    /** Everything the admin page needs. */
    public static function state(): array
    {
        $photos = Capsule::table('site_photos as p')
            ->leftJoin('users as u', 'u.id', '=', 'p.orig_user_id')
            ->orderBy('p.display_order')->orderBy('p.id')
            ->get(['p.*', 'u.full_name as added_by']);
        $slotRows = Capsule::table('site_photo_slots')->pluck('photo_id', 'slot')->all();
        $slotDefs = Photos::slots();

        $usedIn = [];
        foreach ($slotRows as $slot => $photoId) {
            if ($photoId && isset($slotDefs[$slot])) {
                [$page, $label] = $slotDefs[$slot];
                $usedIn[(int) $photoId][] = "{$page} · {$label}";
            }
        }

        $out = [];
        $slide = 0;
        foreach ($photos as $i => $p) {
            $path = Photos::path($p->file);
            $missing = !is_file($path);
            $out[] = [
                'id' => IdEncoder::encode((int) $p->id),
                'position' => $i + 1,
                'slide' => $p->in_slideshow ? ++$slide : null,
                'url' => Photos::url($p->file),
                'file' => basename($p->file),
                'missing' => $missing,
                'width' => $p->width ? (int) $p->width : null,
                'height' => $p->height ? (int) $p->height : null,
                'bytes' => $missing ? null : filesize($path),
                'alt' => (string) ($p->alt ?? ''),
                'in_slideshow' => (bool) $p->in_slideshow,
                'slide_eyebrow' => (string) ($p->slide_eyebrow ?? ''),
                'slide_title' => (string) ($p->slide_title ?? ''),
                'slide_text' => (string) ($p->slide_text ?? ''),
                'slide_cta' => (string) ($p->slide_cta ?? ''),
                'slide_link' => (string) ($p->slide_link ?? ''),
                'added' => $p->created_at ? substr((string) $p->created_at, 0, 10) : null,
                'added_by' => $p->added_by ? (string) $p->added_by : null,
                'used_in' => $usedIn[(int) $p->id] ?? [],
            ];
        }

        $slots = [];
        foreach ($slotDefs as $key => [$page, $label, $hint]) {
            $id = $slotRows[$key] ?? null;
            $slots[] = ['key' => $key, 'page' => $page, 'label' => $label, 'hint' => $hint, 'photo_id' => $id ? IdEncoder::encode((int) $id) : null];
        }

        return ['photos' => $out, 'slots' => $slots];
    }

    /**
     * Save uploaded images ($_FILES['images'], from the shared upload modal)
     * to the end of the library. $inSlideshow = false for a photo uploaded
     * straight into a spot. Returns the new photos' ids (encoded) and URLs.
     */
    public static function upload(array $files, ?int $userId, bool $inSlideshow = true): array
    {
        $dir = Photos::path(Photos::UPLOAD_DIR);
        if (!is_dir($dir)) {
            mkdir($dir, 0775, true);
        }
        $uploaded = (new ImageUploadService(realpath($dir) . '/', 2400, 86))->upload($files, fn(array $f) => $f);
        if (empty($uploaded) || (isset($uploaded['success']) && $uploaded['success'] === false)) {
            return ['success' => false, 'messages' => [$uploaded['message'] ?? 'Those pictures could not be saved.']];
        }

        $order = (int) Capsule::table('site_photos')->max('display_order');
        $now = date('Y-m-d H:i:s');
        $created = [];
        $rejected = 0;
        foreach ($uploaded as $u) {
            $file = Photos::UPLOAD_DIR . basename((string) $u['fileName']);
            $path = Photos::path($file);
            // The upload service trusts the browser's file type — check it really is a picture
            $size = @getimagesize($path);
            if (!$size || !in_array($size[2] ?? 0, [IMAGETYPE_JPEG, IMAGETYPE_PNG, IMAGETYPE_WEBP, IMAGETYPE_GIF], true)) {
                @unlink($path);
                $rejected++;
                continue;
            }
            $id = Capsule::table('site_photos')->insertGetId([
                'file' => $file, 'alt' => null, 'width' => $size[0], 'height' => $size[1],
                'in_slideshow' => $inSlideshow, 'display_order' => ++$order,
                'orig_user_id' => $userId, 'created_at' => $now, 'updated_at' => $now,
            ]);
            $created[] = ['id' => IdEncoder::encode((int) $id), 'url' => Photos::url($file)];
        }

        if (!$created) {
            return ['success' => false, 'messages' => ['Those files weren’t pictures we could use.']];
        }
        $n = count($created);
        self::logActivity('Added ' . $n . ' site ' . ($n === 1 ? 'photo' : 'photos'), 'Site');
        $msg = $n === 1 ? 'Photo added.' : "{$n} photos added.";
        if ($rejected) {
            $msg .= " {$rejected} " . ($rejected === 1 ? 'file wasn’t a picture and was' : 'files weren’t pictures and were') . ' skipped.';
        }
        return ['success' => true, 'messages' => [$msg], 'files' => $created];
    }

    /** Alt text, slideshow on/off and/or the slide caption for one photo. */
    public static function update(string $encodedId, array $data): array
    {
        $id = self::photoId($encodedId);
        if (!$id) {
            return ['success' => false, 'messages' => ['That photo no longer exists.']];
        }
        $changes = [];
        if (array_key_exists('alt', $data)) {
            $alt = trim(strip_tags((string) $data['alt']));
            if (mb_strlen($alt) > self::MAX_ALT) {
                return ['success' => false, 'messages' => ['Please keep the description under ' . self::MAX_ALT . ' characters.']];
            }
            $changes['alt'] = $alt === '' ? null : $alt;
        }
        if (array_key_exists('in_slideshow', $data)) {
            $changes['in_slideshow'] = (bool) $data['in_slideshow'];
        }
        foreach (self::SLIDE_FIELDS as $field => $max) {
            if (!array_key_exists($field, $data)) {
                continue;
            }
            $value = trim(preg_replace('/\s+/', ' ', strip_tags((string) $data[$field])));
            if (mb_strlen($value) > $max) {
                return ['success' => false, 'messages' => ["Please keep that under {$max} characters."]];
            }
            if ($field === 'slide_link' && $value !== '') {
                // A page on this site: “contact?type=consultation”, “services/…”, “about#approach”
                $value = ltrim($value, '/');
                if (!preg_match('#^[a-z0-9\-/]*(\?[a-z0-9\-=&_]*)?(\#[a-z0-9\-_]*)?$#i', $value)) {
                    return ['success' => false, 'messages' => ['The button link must be a page on this site, e.g. contact or services/healthcare-consulting.']];
                }
            }
            $changes[$field] = $value === '' ? null : $value;
        }
        if (!$changes) {
            return ['success' => false, 'messages' => ['Nothing to change.']];
        }
        Capsule::table('site_photos')->where('id', $id)->update($changes + ['updated_at' => date('Y-m-d H:i:s')]);

        if (array_key_exists('in_slideshow', $changes) && count($changes) === 1) {
            self::logActivity($changes['in_slideshow'] ? 'Added a photo to the home slideshow' : 'Took a photo out of the home slideshow', 'Site', $id);
            return ['success' => true, 'messages' => [$changes['in_slideshow'] ? 'Added to the slideshow.' : 'Taken out of the slideshow.']];
        }
        self::logActivity('Updated a site photo', 'Site', $id);
        return ['success' => true, 'messages' => ['Saved.']];
    }

    /** New order for the library (= the slideshow order): every photo id, first to last. */
    public static function reorder(array $encodedIds): array
    {
        $ids = array_values(array_unique(array_filter(array_map(fn($e) => self::photoId((string) $e), $encodedIds))));
        if (!$ids) {
            return ['success' => false, 'messages' => ['Nothing to reorder.']];
        }
        Capsule::connection()->transaction(function () use ($ids) {
            $order = 0;
            foreach ($ids as $id) {
                Capsule::table('site_photos')->where('id', $id)->update(['display_order' => ++$order]);
            }
            // Anything not in the list keeps its relative place, after these
            $rest = Capsule::table('site_photos')->whereNotIn('id', $ids)->orderBy('display_order')->pluck('id');
            foreach ($rest as $id) {
                Capsule::table('site_photos')->where('id', $id)->update(['display_order' => ++$order]);
            }
        });
        self::logActivity('Rearranged the home slideshow', 'Site');
        return ['success' => true, 'messages' => ['New order saved.']];
    }

    /**
     * Delete photos — the library rows, their files on the server, and any
     * spot using them. $encodedIds = ['*'] deletes every photo.
     */
    public static function delete(array $encodedIds): array
    {
        $all = $encodedIds === ['*'];
        $ids = $all
            ? Capsule::table('site_photos')->pluck('id')->map(fn($i) => (int) $i)->all()
            : array_values(array_unique(array_filter(array_map(fn($e) => self::photoId((string) $e), $encodedIds))));
        if (!$ids) {
            return ['success' => false, 'messages' => [$all ? 'The library is already empty.' : 'Those photos no longer exist.']];
        }

        $files = Capsule::table('site_photos')->whereIn('id', $ids)->pluck('file')->all();
        Capsule::connection()->transaction(function () use ($ids) {
            Capsule::table('site_photo_slots')->whereIn('photo_id', $ids)->update(['photo_id' => null, 'updated_at' => date('Y-m-d H:i:s')]);
            Capsule::table('site_photos')->whereIn('id', $ids)->delete();
        });

        // The files go too — but only ever from the library's own folder
        $removed = 0;
        $dir = realpath(Photos::path(Photos::UPLOAD_DIR));
        foreach ($files as $file) {
            $path = realpath(Photos::path($file));
            if ($dir && $path && str_starts_with($path, $dir . DIRECTORY_SEPARATOR) && is_file($path) && @unlink($path)) {
                $removed++;
            }
        }

        $n = count($ids);
        self::logActivity($all ? "Deleted all {$n} site photos" : 'Deleted ' . $n . ' site ' . ($n === 1 ? 'photo' : 'photos'), 'Site');
        return ['success' => true, 'messages' => [$all
            ? "All {$n} photos deleted."
            : ($n === 1 ? 'Photo deleted.' : "{$n} photos deleted.")], 'files_removed' => $removed];
    }

    /** Put a library photo in a spot (or empty the spot with null). */
    public static function assign(string $slot, ?string $encodedPhotoId): array
    {
        $defs = Photos::slots();
        if (!isset($defs[$slot])) {
            return ['success' => false, 'messages' => ['Unknown photo spot.']];
        }
        $photoId = null;
        if ($encodedPhotoId !== null && $encodedPhotoId !== '') {
            $photoId = self::photoId($encodedPhotoId);
            if (!$photoId) {
                return ['success' => false, 'messages' => ['That photo no longer exists.']];
            }
        }
        $now = date('Y-m-d H:i:s');
        Capsule::table('site_photo_slots')->where('slot', $slot)->exists()
            ? Capsule::table('site_photo_slots')->where('slot', $slot)->update(['photo_id' => $photoId, 'updated_at' => $now])
            : Capsule::table('site_photo_slots')->insert(['slot' => $slot, 'photo_id' => $photoId, 'created_at' => $now, 'updated_at' => $now]);

        [$page, $label] = $defs[$slot];
        self::logActivity(($photoId ? 'Changed' : 'Removed') . " the photo: {$page} · {$label}", 'Site');
        return ['success' => true, 'messages' => [$photoId ? 'Photo updated.' : 'Photo removed from that spot.']];
    }

    /** Decoded id of an existing photo, or null. */
    private static function photoId(string $encodedId): ?int
    {
        $id = IdEncoder::decode($encodedId);
        return $id && Capsule::table('site_photos')->where('id', $id)->exists() ? (int) $id : null;
    }
}
