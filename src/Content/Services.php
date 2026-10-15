<?php
// /src/Content/Services.php

declare(strict_types=1);

namespace Src\Content;

/**
 * The consulting service areas. Each entry gets a card on /services and
 * the home page, its own page at /services/{slug}, an option in the
 * contact form's "Service of interest" list, and a sitemap entry.
 *
 * To add a service, copy an entry and change the slug (lower-case, hyphens)
 * and copy. 'icon' is a key from Src\Content\Icons. Each service page's hero photo is
 * chosen in the admin (Site Photos — spot service.{slug}).
 */
class Services
{
    public static function all(): array
    {
        return [
            [
                'slug' => 'funding-strategy-grant-development',
                'title' => 'Funding Strategy & Grant Development',
                'seo_title' => 'Grant Writing & Funding Strategy Consulting',
                'icon' => 'funding',
                'short' => 'Identify the right funding opportunities and prepare strong, strategic applications.',
                'summary' => 'Grant research, eligibility assessment, proposal writing, budgets and funding-readiness support for businesses, nonprofits, community organizations and entrepreneurs.',
                'intro' => [
                    'We help businesses, nonprofits, community organizations and entrepreneurs identify potential funding opportunities and prepare strategic funding applications.',
                    'From the first eligibility check to the final review of an application, we help you understand what funders are looking for and present your project clearly and credibly. Where specialized expertise is required, we collaborate with partner consultants.',
                ],
                'audience' => ['Nonprofits and charitable initiatives', 'Small and medium-sized businesses', 'Community organizations', 'Entrepreneurs and start-ups'],
                'offerings' => [
                    'Grant opportunity identification and funding research',
                    'Grant eligibility assessment and funding strategy',
                    'Grant application planning and development',
                    'Proposal writing and application review',
                    'Organizational needs assessments and project descriptions',
                    'Budget development and funding justification',
                    'Letters of support and partnership development',
                    'Funding-readiness assessments',
                    'Identifying government, foundation, corporate and community funding sources',
                    'Strategic support for capital projects, program development and organizational growth',
                ],
                'deliverables' => [
                    'A funding-readiness assessment with clear next steps',
                    'A shortlist of funding opportunities that fit your eligibility and goals',
                    'An application plan with timelines and responsibilities',
                    'Proposal narrative, budget and supporting materials, ready for submission',
                ],
                'benefits' => [
                    'Spend your time on the opportunities you are most likely to qualify for',
                    'Present your project with a clear need, plan, budget and outcomes',
                    'Build materials you can adapt for future applications',
                ],
                'note' => 'We help clients identify and pursue appropriate funding opportunities. Funding awards are subject to eligibility, funder requirements and each funder’s final decision, and cannot be guaranteed.',
                'cta' => 'Explore Funding Support',
            ],
            [
                'slug' => 'business-consulting-strategic-planning',
                'title' => 'Business Consulting & Strategic Planning',
                'seo_title' => 'Business Consulting & Strategic Planning',
                'icon' => 'strategy',
                'short' => 'Turn an idea into an actionable plan — and a plan into sustainable growth.',
                'summary' => 'Business strategy, growth planning, business model development, strategic and operational planning for entrepreneurs, small businesses and established organizations.',
                'intro' => [
                    'We support entrepreneurs, small businesses and established organizations that need help turning an idea into an actionable plan.',
                    'Our aim is to help you move from having an idea to having a clear plan for how to execute it — with priorities, resources, partners and practical next steps.',
                ],
                'audience' => ['Entrepreneurs and start-ups', 'Small and medium-sized businesses', 'Established organizations planning growth or change'],
                'offerings' => [
                    'Business strategy and growth planning',
                    'Business model development',
                    'Strategic and operational planning',
                    'Organizational needs assessments',
                    'Business development and expansion strategies',
                    'Program and service development',
                    'Partnership and stakeholder engagement',
                    'Resource identification and strategic implementation planning',
                    'Support for organizations seeking to improve operations and build sustainable services',
                ],
                'deliverables' => [
                    'A clear picture of where you are, where you want to go and what is in the way',
                    'A strategic or growth plan with priorities, milestones and resources',
                    'An implementation roadmap your team can act on',
                ],
                'benefits' => [
                    'Make decisions with a structured plan rather than guesswork',
                    'Focus limited time and money on the actions that matter most',
                    'Build services and operations that can be sustained',
                ],
                'cta' => 'Discuss Your Business',
            ],
            [
                'slug' => 'nonprofit-development',
                'title' => 'Nonprofit Development & Community Organization Support',
                'seo_title' => 'Nonprofit Consulting & Community Organization Support',
                'icon' => 'community',
                'short' => 'Strengthen capacity, design programs and pursue funding for community impact.',
                'summary' => 'Nonprofit program development, funding strategy, community needs assessments, partnership development and sustainable service delivery models.',
                'intro' => [
                    'We work with nonprofits and community-focused organizations to strengthen their capacity, develop programs and pursue funding opportunities.',
                    'Our approach is particularly relevant to organizations serving marginalized populations and communities facing barriers to services and resources.',
                ],
                'audience' => ['Nonprofit organizations and charitable initiatives', 'Community and social-service agencies', 'Organizations serving marginalized populations'],
                'offerings' => [
                    'Nonprofit program development',
                    'Funding and grant strategy',
                    'Organizational planning',
                    'Community needs assessments',
                    'Partnership development',
                    'Program design and implementation planning',
                    'Resource mobilization',
                    'Strategic support for community initiatives',
                    'Developing sustainable models for service delivery',
                ],
                'deliverables' => [
                    'A community needs assessment grounded in the people you serve',
                    'Program designs with goals, activities and measurable outcomes',
                    'A funding and partnership strategy to sustain the work',
                ],
                'benefits' => [
                    'Programs designed around real community needs',
                    'Stronger, better-documented cases for funders and partners',
                    'A more sustainable organization, not just a single project',
                ],
                'cta' => 'Discuss Your Initiative',
            ],
            [
                'slug' => 'healthcare-consulting',
                'title' => 'Healthcare & Community Health Consulting',
                'seo_title' => 'Healthcare, Clinic Development & Community Health Consulting',
                'icon' => 'health',
                'short' => 'Clinical insight and strategic planning for healthcare programs, clinics and community health.',
                'summary' => 'Healthcare program development, clinic planning, community health initiatives, operations and workflow planning, quality improvement and program evaluation.',
                'intro' => [
                    'Healthcare is an important area of our expertise. We support healthcare organizations, clinics, community agencies and entrepreneurs who are developing or improving healthcare-related services.',
                    'Our approach combines clinical knowledge, strategic planning, operational thinking and an understanding of community needs.',
                ],
                'audience' => ['Healthcare organizations and clinic developers', 'Community health and social-service agencies', 'Healthcare entrepreneurs'],
                'offerings' => [
                    'Healthcare program development',
                    'Clinic planning and service development',
                    'Community health initiatives',
                    'Healthcare operations and workflow planning',
                    'Service navigation and access improvement',
                    'Quality improvement and performance measurement',
                    'Risk assessment and policy development',
                    'Partnership development',
                    'Community engagement',
                    'Program evaluation and implementation planning',
                    'Support for innovative, integrated models of care',
                ],
                'deliverables' => [
                    'Program or clinic service plans shaped by clinical and community insight',
                    'Workflow, policy and quality-improvement recommendations',
                    'Evaluation frameworks with clear performance measures',
                ],
                'benefits' => [
                    'Services designed with both clinical realities and community needs in mind',
                    'Smoother operations and better access for the people you serve',
                    'Evidence to show what is working — and what to improve',
                ],
                'note' => 'Our healthcare consulting supports planning, operations and program development. It does not include medical diagnosis, treatment or other clinical care.',
                'cta' => 'Discuss Your Health Project',
            ],
            [
                'slug' => 'disability-case-management-consulting',
                'title' => 'Disability Case Management Consulting',
                'seo_title' => 'Disability Case Management Consulting',
                'icon' => 'case',
                'short' => 'Clinically informed guidance for navigating disability-related processes.',
                'summary' => 'Disability case management consultation, return-to-work and accommodation planning, resource navigation, case coordination and workplace accommodation strategy.',
                'intro' => [
                    'We provide disability case management consulting informed by clinical experience and an understanding of the challenges that individuals and organizations face when navigating disability-related processes.',
                    'We help clients understand their situation, organize the information they need and plan practical next steps — working alongside, not in place of, legal, medical and other regulated professionals.',
                ],
                'audience' => ['Individuals navigating disability-related processes', 'Employers and workplaces', 'Organizations developing disability management processes'],
                'offerings' => [
                    'Disability case management consultation',
                    'Return-to-work and accommodation planning',
                    'Disability-related resource navigation',
                    'Case coordination and documentation support',
                    'Workplace accommodation strategy',
                    'Guidance on navigating complex disability-related situations',
                    'Support for organizations developing disability management processes',
                ],
                'deliverables' => [
                    'A clear overview of the situation, options and next steps',
                    'Organized documentation and a coordination plan',
                    'Accommodation or return-to-work planning recommendations',
                ],
                'benefits' => [
                    'Less confusion in complex, stressful situations',
                    'Better-coordinated communication between the people involved',
                    'Workplace processes that are clearer and more consistent',
                ],
                'note' => 'This is a consulting and navigation service. It is not legal representation, medical diagnosis or treatment, or a substitute for advice from a lawyer, physician or other regulated professional.',
                'cta' => 'Request a Consultation',
            ],
            [
                'slug' => 'sports-career-management',
                'title' => 'Sports Career Management',
                'seo_title' => 'Sports Career Management & Athlete Career Planning',
                'icon' => 'sports',
                'short' => 'Career planning, positioning and opportunity strategy for athletes.',
                'summary' => 'Athlete career planning, career development and transition planning, opportunity identification, partnerships, brand positioning and business development.',
                'intro' => [
                    'We support athletes and sports-related clients with career planning, professional development and strategic opportunities.',
                    'The objective is to help athletes make informed decisions about their professional opportunities and long-term career development — during their playing careers and beyond.',
                ],
                'audience' => ['Athletes and sports professionals', 'Sports-related organizations and clients'],
                'offerings' => [
                    'Athlete career planning',
                    'Career development and transition planning',
                    'Strategic opportunity identification',
                    'Partnership and networking support',
                    'Brand and professional positioning',
                    'Business development and project planning',
                    'Coordination with relevant professional service providers',
                ],
                'deliverables' => [
                    'A career plan covering short- and long-term goals',
                    'A strategy for opportunities, partnerships and positioning',
                    'Coordinated introductions to relevant professional service providers',
                ],
                'benefits' => [
                    'Make career decisions with a long-term view',
                    'Prepare for transitions before they happen',
                    'Build a professional brand and network with purpose',
                ],
                'cta' => 'Discuss Your Career',
            ],
            [
                'slug' => 'shelter-social-service-projects',
                'title' => 'Shelter & Social-Service Project Development',
                'seo_title' => 'Shelter & Social-Service Project Development Consulting',
                'icon' => 'shelter',
                'short' => 'Planning, funding and partnership support for shelters and social-service initiatives.',
                'summary' => 'Consulting and project development support for organizations exploring shelters and social-service initiatives: feasibility, funding, programs and partnerships.',
                'intro' => [
                    'We assist organizations and community partners that are exploring the development of shelters and other social-service initiatives.',
                    'We provide consulting and project development support — helping partners plan, fund and organize their projects. The organizations we advise remain the operators of their own projects.',
                ],
                'audience' => ['Organizations developing shelters and community programs', 'Community partners and institutions exploring collaborative projects'],
                'offerings' => [
                    'Project planning and feasibility support',
                    'Funding strategy',
                    'Program development',
                    'Partnership and stakeholder coordination',
                    'Resource identification',
                    'Operational planning',
                    'Community needs assessment',
                    'Implementation planning',
                ],
                'deliverables' => [
                    'Feasibility and community-need findings to guide decisions',
                    'A project and funding plan with key partners identified',
                    'Operational and implementation plans for launch',
                ],
                'benefits' => [
                    'Test the idea before committing major resources',
                    'Bring funders, partners and stakeholders along from the start',
                    'Launch with a realistic operating plan',
                ],
                'cta' => 'Discuss Your Project',
            ],
            [
                'slug' => 'specialized-consulting',
                'title' => 'Specialized Consulting & Strategic Partnerships',
                'seo_title' => 'Specialized Consulting & Strategic Partnerships',
                'icon' => 'network',
                'short' => 'Not sure where you fit? We bring together the right expertise for complex challenges.',
                'summary' => 'Multidisciplinary consulting that coordinates expertise across business, healthcare, disability management, funding and community development for challenges that span more than one field.',
                'intro' => [
                    'Some client needs do not fit neatly into a single service category. Our multidisciplinary approach allows us to work with partner consultants to address specialized challenges.',
                    'We can coordinate expertise across business, healthcare, disability management, funding, community development and related fields. If you are unsure which service you need, start here — we will listen first and help you find the right path.',
                ],
                'audience' => ['Leaders and organizations with complex, cross-disciplinary challenges', 'Community partners and institutions exploring collaborative projects', 'Anyone unsure which service fits'],
                'offerings' => [
                    'An initial conversation to understand your situation',
                    'Coordinating expertise across several disciplines',
                    'Bringing in partner consultants where specialized knowledge is needed',
                    'Strategic partnership development',
                    'Project scoping for challenges that span more than one field',
                ],
                'deliverables' => [
                    'A clear definition of the problem and the expertise it needs',
                    'A coordinated team and plan built around your situation',
                ],
                'benefits' => [
                    'One point of contact for a multidisciplinary challenge',
                    'The right people at the table from the beginning',
                ],
                'cta' => 'Make a General Inquiry',
            ],
        ];
    }

    public static function find(string $slug): ?array
    {
        foreach (self::all() as $s) {
            if ($s['slug'] === $slug) {
                return $s;
            }
        }
        return null;
    }
}
