<?php
// /src/Content/Projects.php

declare(strict_types=1);

namespace Src\Content;

/**
 * Projects, case studies and affiliated initiatives for /projects.
 *
 * Never publish confidential client information. Only add a project once
 * the client / partner has authorized it.
 */
class Projects
{
    /**
     * Approved projects and case studies. Fields: title, category, summary,
     * outcomes (list), partners (list of acknowledgements), image (path under
     * public/images/projects/ or null).
     */
    public static function all(): array
    {
        return [];
    }

    /**
     * Initiatives in our broader organizational ecosystem. These are separate
     * organizations — shown by name only, with the disclaimer on the page,
     * until each one's description is confirmed. Add 'summary' and 'url' when
     * approved; set 'show' => false to hide one.
     */
    public static function affiliates(): array
    {
        $all = [
            ['name' => 'Alchemy Health', 'summary' => null, 'url' => null, 'show' => true],
            ['name' => 'Alchemy Pharmacy', 'summary' => null, 'url' => null, 'show' => true],
            ['name' => 'Alchemists Medical', 'summary' => null, 'url' => null, 'show' => true],
            ['name' => 'Always Alchemy', 'summary' => null, 'url' => null, 'show' => true],
            ['name' => 'Elevated Education', 'summary' => null, 'url' => null, 'show' => true],
        ];
        return array_values(array_filter($all, fn($a) => $a['show']));
    }
}
