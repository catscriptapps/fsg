<?php
// /scripts/migrations/2026_10_13_000004_create_site_photos.php
//
// Site photos (admin: /site-photos — Src\Controller\SitePhotosController):
//
//   site_photos        the photo library — file (path under public/), alt
//                      text, size, whether it plays in the home slideshow,
//                      its slide caption, and its position (display_order —
//                      the slideshow order)
//   site_photo_slots   which library photo fills each fixed spot on the
//                      public site (Src\Content\Photos::slots())
//
// Additive and idempotent: tables are created only if missing; the library
// is seeded only while it's empty, and only missing spots are filled — so an
// admin's choices are never overwritten by a later DB update.
//
// Seeding COPIES the shipped photos (public/images/home/) into the library's
// own folder (images/uploads/site/), so every library photo is a file the
// admin page owns — deleting a photo always deletes its file, and the
// shipped originals stay as the fallback.

declare(strict_types=1);

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Schema\Blueprint;
use Src\Content\Photos;

return function (): array {
    $schema = Capsule::schema();
    $log = [];

    if ($schema->hasTable('site_photos')) {
        $log[] = 'site_photos already exists — skipped';
    } else {
        $schema->create('site_photos', function (Blueprint $t) {
            $t->increments('id');
            $t->string('file');                        // relative to public/, e.g. images/uploads/site/abc.jpg
            $t->string('alt')->nullable();
            $t->unsignedInteger('width')->nullable();
            $t->unsignedInteger('height')->nullable();
            $t->boolean('in_slideshow')->default(true);
            $t->integer('display_order')->default(0);
            $t->string('slide_eyebrow', 40)->nullable();
            $t->string('slide_title', 120)->nullable(); // *words* in asterisks show in gold italics
            $t->string('slide_text', 300)->nullable();
            $t->string('slide_cta', 40)->nullable();
            $t->string('slide_link', 200)->nullable();  // a site path, e.g. contact?type=consultation
            $t->unsignedInteger('orig_user_id')->nullable();
            $t->timestamps();
        });
        $log[] = 'created site_photos';
    }

    if ($schema->hasTable('site_photo_slots')) {
        $log[] = 'site_photo_slots already exists — skipped';
    } else {
        $schema->create('site_photo_slots', function (Blueprint $t) {
            $t->string('slot', 80)->primary();
            $t->unsignedInteger('photo_id')->nullable()->index();
            $t->timestamps();
        });
        $log[] = 'created site_photo_slots';
    }

    $now = date('Y-m-d H:i:s');
    $author = Capsule::table('users')->where('id', 1)->exists() ? 1 : null;
    $public = __DIR__ . '/../../public/';

    // ---- Library: the shipped photos, slideshow ones first in slide order
    $idByRole = [];
    if (Capsule::table('site_photos')->count() === 0) {
        $dir = $public . Photos::UPLOAD_DIR;
        if (!is_dir($dir)) {
            mkdir($dir, 0775, true);
        }
        $captions = [];
        foreach (Photos::DEFAULT_SLIDES as $s) {
            $captions[$s['role']] = $s;
        }
        $roles = array_unique(array_merge(array_column(Photos::DEFAULT_SLIDES, 'role'), array_keys(Photos::FALLBACK)));
        $order = 0;
        foreach ($roles as $role) {
            [$src, $alt] = Photos::FALLBACK[$role];
            if (!is_file($public . $src)) {
                continue;
            }
            $file = Photos::UPLOAD_DIR . uniqid('seed-') . '-' . $role . '.jpg';
            if (!copy($public . $src, $public . $file)) {
                continue;
            }
            $size = @getimagesize($public . $file) ?: [null, null];
            $c = $captions[$role] ?? null;
            $idByRole[$role] = Capsule::table('site_photos')->insertGetId([
                'file' => $file,
                'alt' => $alt,
                'width' => $size[0],
                'height' => $size[1],
                'in_slideshow' => $c !== null,
                'display_order' => ++$order,
                'slide_eyebrow' => $c['eyebrow'] ?? null,
                'slide_title' => $c['title'] ?? null,
                'slide_text' => $c['text'] ?? null,
                'slide_cta' => $c['cta'] ?? null,
                'slide_link' => $c['link'] ?? null,
                'orig_user_id' => $author,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
        $log[] = 'added ' . count($idByRole) . ' photos to the site photo library';
    } else {
        $log[] = 'site photo library already has photos — skipped';
        // A spot added later can still get its default: the seeded copies are named …-{role}.jpg
        foreach (array_keys(Photos::FALLBACK) as $role) {
            $id = Capsule::table('site_photos')->where('file', 'like', '%-' . $role . '.jpg')->orderBy('id')->value('id');
            if ($id) {
                $idByRole[$role] = (int) $id;
            }
        }
    }

    // ---- Spots: today's placements (only spots not set yet)
    $existing = array_flip(Capsule::table('site_photo_slots')->pluck('slot')->all());
    $filled = 0;
    foreach (Photos::slots() as $slot => [, , , $role]) {
        if (isset($existing[$slot])) {
            continue;
        }
        Capsule::table('site_photo_slots')->insert([
            'slot' => $slot,
            'photo_id' => $role ? ($idByRole[$role] ?? null) : null,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        $filled++;
    }
    $log[] = $filled ? "set {$filled} site photo spots" : 'site photo spots already set — skipped';

    return $log;
};
