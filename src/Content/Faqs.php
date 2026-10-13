<?php
// /src/Content/Faqs.php

declare(strict_types=1);

namespace Src\Content;

/**
 * Public FAQs (/faqs). Answers are plain text; separate paragraphs with a
 * blank line. [label](path) makes an internal link.
 */
class Faqs
{
    /** @return array<int, array{0: string, 1: string}> [question, answer] */
    public static function all(): array
    {
        return [
            ['What types of organizations do you work with?', "We work with small and medium-sized businesses, entrepreneurs and start-ups, nonprofit organizations and charitable initiatives, healthcare organizations and clinic developers, community health and social-service agencies, athletes and sports professionals, and organizations developing shelters and community programs.\n\nWe support both for-profit and nonprofit clients, as well as individuals and leaders seeking funding, strategic planning or specialized consulting."],
            ['Can you help a nonprofit find funding?', "Yes. We help nonprofits identify potential government, foundation, corporate and community funding sources, assess eligibility, and plan, write and review applications — including needs assessments, project descriptions, budgets and letters of support.\n\nSee [Funding Strategy & Grant Development](services/funding-strategy-grant-development) for details."],
            ['Do you work with for-profit businesses?', "Absolutely. We help entrepreneurs, start-ups and established businesses with business strategy, growth planning, business model development, operational planning and funding strategy. See [Business Consulting & Strategic Planning](services/business-consulting-strategic-planning)."],
            ['Can you help develop a new program or clinic?', "Yes. Program and clinic development is a core area of our expertise. We support clinic planning and service development, healthcare and community program design, operations and workflow planning, and implementation and evaluation planning — combining clinical knowledge with strategic and operational thinking. See [Healthcare & Community Health Consulting](services/healthcare-consulting)."],
            ['Do you provide disability case management consulting?', "Yes. We provide disability case management consulting informed by clinical experience, including return-to-work and accommodation planning, resource navigation, and case coordination and documentation support.\n\nThis is a consulting and navigation service. It is not legal representation, medical diagnosis or treatment, and it does not replace advice from a lawyer, physician or other regulated professional."],
            ['How does the consulting process work?', "Our process has five steps: we **Listen** to understand your goals and challenges, **Assess** your needs, resources and opportunities, **Strategize** a tailored plan, help you **Connect & Execute** with the right expertise, funding and partners, and **Evaluate & Advance** by reviewing progress and planning next steps.\n\nRead more on our [About page](about#approach)."],
            ['How do we arrange an initial consultation?', "Use the [contact form](contact) and choose “Request a consultation”, or call us at 289-489-4798 or email Sheena.L.Wright@FrequencyStrategyGroup.com. Tell us a little about your situation and we will reply to arrange a time to talk."],
            ['Do you guarantee grant funding?', "No. No consultant can honestly guarantee funding. Funding decisions rest entirely with each funder and depend on eligibility, the funder’s requirements and priorities, available budgets and competition.\n\nWhat we do is help you find appropriate opportunities, understand what funders are looking for, and prepare the strongest, clearest application possible."],
            ['What if I’m not sure which service I need?', "That is very common — many challenges span more than one area. Choose “Not sure / general inquiry” on the [contact form](contact) and tell us what you are working on. We will listen first and help you find the right path, bringing in partner consultants where needed."],
        ];
    }
}
