<?php
// /src/Content/Testimonials.php

declare(strict_types=1);

namespace Src\Content;

/**
 * Client testimonials. First names only — no surnames, photos, company
 * names or other identifying details without the client's written
 * authorization. Set 'approved' => false to hide one until its client has
 * confirmed permission to publish.
 */
class Testimonials
{
    public static function all(): array
    {
        $all = [
            ['name' => 'Meg', 'topic' => 'Funding Support', 'quote' => 'Frequency Strategy Group has got me all the funding that I needed. Now my business is thriving.', 'approved' => true],
            ['name' => 'Craig', 'topic' => 'Client Support', 'quote' => 'Sheena and her team were there for me in my time of need.', 'approved' => true],
            ['name' => 'Kevin', 'topic' => 'Business Growth', 'quote' => 'Sheena and her team are helping me scale my business. They’re great to work with.', 'approved' => true],
        ];
        return array_values(array_filter($all, fn($t) => $t['approved']));
    }
}
