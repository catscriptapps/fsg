<?php
// /src/Content/Company.php

declare(strict_types=1);

namespace Src\Content;

/**
 * Who we are: philosophy, mission, vision, values, process, audiences and
 * "why work with us". Used across the home and About pages.
 */
class Company
{
    /** The meaning of the name: [word, promise, text] */
    public const PHILOSOPHY = [
        ['Frequency', 'We listen.', 'Frequency relates to sound, wavelengths, communication and awareness — being receptive to what is happening around us. Effective consulting begins with listening. We take the time to understand our clients, their circumstances, their goals and the challenges they are trying to overcome. We do not offer generic solutions before we understand the problem.'],
        ['Strategy', 'We develop the plan.', 'Once we understand your needs, we develop a practical, methodical strategy. We identify resources, opportunities, partnerships, funding options and actionable steps that turn your vision into reality.'],
        ['Group', 'We bring the right expertise together.', 'We work with a group of consultants and partners from different professional disciplines, connecting you with the knowledge, expertise and resources needed for challenges that require more than one area of specialization.'],
    ];

    public const PHILOSOPHY_SUMMARY = 'Listen carefully, understand the problem, develop the strategy, connect the right people, and work toward measurable results.';

    public const MISSION = [
        'Frequency Strategy Group transforms strategic vision into execution by bringing together clinical expertise, community insight, business strategy and innovative solutions to achieve meaningful, measurable results.',
        'We connect people, organizations, expertise and resources to help solve problems, overcome barriers, secure opportunities and build sustainable solutions.',
    ];

    public const VISION = 'To create a more equitable future where organizations, communities and leaders have the strategy, expertise and resources to turn ambitious ideas into meaningful, measurable impact.';

    /** [icon, value, description] */
    public const VALUES = [
        ['equity', 'Equity & Access', 'We work to reduce barriers and improve access to resources, opportunities, funding and professional expertise.'],
        ['expertise', 'Expertise & Evidence', 'We draw on professional knowledge, practical experience, research and evidence-informed approaches to develop effective solutions.'],
        ['listen', 'Listening & Understanding', 'We begin by listening to our clients and understanding their individual circumstances before recommending a course of action.'],
        ['network', 'Collaboration & Partnership', 'Complex problems are best addressed by bringing the right people and disciplines together.'],
        ['innovation', 'Innovation & Transformation', 'We look for creative, forward-thinking solutions that help clients grow, adapt and succeed.'],
        ['accountability', 'Execution & Accountability', 'We focus on turning plans into action, establishing clear objectives and working toward tangible outcomes.'],
        ['compassion', 'Compassion & Respect', 'We treat our clients and partners with dignity, professionalism, empathy and respect.'],
        ['impact', 'Community Impact', 'Stronger organizations, accessible services and sustainable businesses contribute to stronger communities.'],
    ];

    /** The five-step consulting process: [icon, step, text] */
    public const PROCESS = [
        ['listen', 'Listen', 'We meet with you to understand your goals, circumstances, challenges and priorities.'],
        ['assess', 'Assess', 'We review your needs, existing resources, barriers, opportunities and desired outcomes.'],
        ['strategize', 'Strategize', 'We develop a tailored, structured plan with recommended actions, resources, timelines and priorities.'],
        ['connect', 'Connect & Execute', 'We help you connect with relevant expertise, potential funding sources, partners and implementation resources.'],
        ['evaluate', 'Evaluate & Advance', 'We review progress against agreed objectives, identify next steps and help you build toward sustainable results.'],
    ];

    /** Who we serve: [icon, audience] */
    public const AUDIENCES = [
        ['business', 'Small and medium-sized businesses'],
        ['idea', 'Entrepreneurs and start-ups'],
        ['compassion', 'Nonprofit organizations and charitable initiatives'],
        ['health', 'Healthcare organizations and clinic developers'],
        ['community', 'Community health and social-service agencies'],
        ['equity', 'Organizations serving marginalized populations'],
        ['sports', 'Athletes and sports professionals'],
        ['shelter', 'Organizations developing shelters and community programs'],
        ['strategy', 'Leaders seeking funding, planning or specialized consulting'],
        ['institution', 'Community partners and institutions exploring collaboration'],
    ];

    /** Why work with us: [icon, title, text] */
    public const WHY_US = [
        ['network', 'Multidisciplinary expertise', 'Strategy, clinical and healthcare knowledge, community development, funding and business development — under one roof, with partner consultants where specialized skills are needed.'],
        ['listen', 'Personalized solutions', 'No one-size-fits-all packages. Every engagement starts by listening, and every plan is built around your circumstances.'],
        ['connect', 'Collaborative partnerships', 'We connect you with the people, partners, funders and resources that can move your work forward.'],
        ['accountability', 'Focus on practical outcomes', 'We connect strategy with implementation — clear objectives, actionable steps and progress you can measure.'],
    ];
}
