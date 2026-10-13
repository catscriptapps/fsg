<?php
// /src/Content/Team.php

declare(strict_types=1);

namespace Src\Content;

/**
 * Consultants and professional partners for /team (and the lead
 * consultant feature on the home page).
 *
 * Profile fields: slug, name, credentials, role, photo (path under
 * public/images/team/, or null for the monogram placeholder), specialization,
 * bio (paragraphs), expertise (list), links ([label => url]).
 *
 * Only add people — and credentials — that have been confirmed.
 */
class Team
{
    public static function lead(): array
    {
        return [
            'slug' => 'sheena-wright',
            'name' => 'Sheena Wright',
            'credentials' => 'BScN, RN',
            'role' => 'Lead Consultant',
            'photo' => null, // e.g. 'images/team/sheena-wright.jpg'
            'specialization' => 'Healthcare & community development',
            'headline' => 'More than 27 years of combined clinical and leadership experience.',
            'bio' => [
                'Sheena Wright is a healthcare and community development professional with more than 27 years of combined clinical and leadership experience. Her background includes clinical practice, healthcare leadership, program development, strategic planning, community partnerships, quality improvement and operational development.',
                'She brings a multidisciplinary perspective to consulting, combining clinical expertise with strategic thinking and an understanding of how organizations can translate their goals into practical solutions.',
                'Her professional experience includes healthcare program development, community-based service delivery, clinic development, organizational planning and collaboration across healthcare and social-service settings.',
                'As Lead Consultant, Sheena works with clients to understand their needs, develop tailored strategies, and coordinate the expertise and resources needed to move projects forward.',
            ],
            'expertise' => [
                'Healthcare program development',
                'Clinic development',
                'Strategic & organizational planning',
                'Community partnerships',
                'Community-based service delivery',
                'Quality improvement',
                'Operational development',
                'Healthcare leadership',
            ],
            'links' => [],
        ];
    }

    /**
     * Additional consultants and partners — announced soon. Add profiles
     * here as they are confirmed (same fields as lead()).
     */
    public static function members(): array
    {
        return [];
    }
}
