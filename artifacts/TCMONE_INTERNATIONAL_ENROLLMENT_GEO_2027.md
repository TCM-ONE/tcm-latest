# TCM One international enrollment and AI search

November 27, 2026 · Local staging

## Enrollment and release scope

Paid course pages now link to a private, noindex international enrollment form. Visitors can review the course and INR charge, provide different countries of residence and billing, choose a display currency, and view scheduled cohort starts in an IANA time zone. The form accepts international calling codes. When a cohort is unavailable or transfer checkout is disabled, visitors can send their preferred timing to admissions without submitting a payment.

Where the bank account and migration are configured, the server issues a session-bound quote for an available cohort. The state is bound to the course and the email used to sign in. It retains the INR charge and FX estimate until the session expires, up to 24 hours. The proof submission is pending until an administrator verifies the bank transfer. Approval records the payment and course access, and a signed-in student can view the status and download the existing receipt after approval. Cohort preference and actual assignment are separate.

The administrator sees the countries, time zone, original and applicable INR fees, markup, display currency, rate, source, and cohort preference on the payment review page. The manual payments list can filter by recorded residence and self-reported source once the migration is applied. Google Search in that filter denotes a declaration and cannot establish organic attribution. Existing leads retain the discovery source for cohort requests and proof submissions. The approval email uses existing SMTP when configured. The approval was tested with email disabled, so mail delivery remains unverified.

| Files | Change |
|---|---|
| app.php, InternationalEnrollmentController.php, InternationalEnrollmentQuote.php | Route, CSRF, validated form and server-side quote |
| RegionalPricing.php, international enrollment template and JS | Shared international markup and optional bounded USD rounding, mobile form and timezone conversion |
| Student/PaymentController.php, PaymentSubmission.php, payment status/history/submit templates, admin payment detail | Quote validation, persisted snapshot, duplicate approval protection, status and confirmation email |
| database/migrations/2026_11_27_add_international_enrollment_details.sql | Optional JSON column on existing payment submissions. Apply separately to a backed-up staging database. |
| tcm-editorial.js and international-enrollment.js | Consent-gated form start, cohort selection and form submit events. Event payloads exclude personal form fields. |

The bank transfer rail requires manual reconciliation. Self-service card capture, international installment plans, cohort reservations and assignment, payment and class reminder emails, and server-side payment analytics remain unfinished. Before enabling hosted international PayU, verify that the merchant account can accept the required cards and settlement currency, then test callbacks, authenticated webhooks, exact amount matching, retries, refunds, receipt delivery, and production credentials isolation. The existing India PayU and EMI code paths were retained. [PayU hosted international checkout](https://docs.payu.in/docs/payu-hosted-checkout-integration-dynamic-currency-conversion)

## GEO and AEO findings

OpenSEO project a3071e7c-4e06-41c9-b7ea-9341d9da7314 has no AI tracker or collected observations. The Prompt Research request for “online data analytics courses” requires a paid plan. The earlier U.S. keyword batch also failed for insufficient credits. No competitor visibility rates, prompts observed in AI products, citation counts, keyword metrics, or city demand were available. No paid tracking or subscriptions were activated. [OpenSEO project](https://app.openseo.so/p/a3071e7c-4e06-41c9-b7ea-9341d9da7314)

The sampled provider and directory pages consistently make course hours, delivery format, curriculum and fees easy to compare. Noble Desktop distinguishes its instructor-led and self-paced options. General Assembly documents instructional hours and outcomes. Course Report exposes duration and price in its analytics directory. These are comparisons of source pages available in public search. Their frequency in AI answers remains unknown. [Noble Desktop](https://www.nobledesktop.com/classes/data-analysis-basic-course) · [General Assembly](https://ga-public-downloads.s3.us-east-1.amazonaws.com/regulatory/US-Catalog.pdf) · [Course Report](https://www.coursereport.com/subjects/data-analytics)

### Priority actions

1. Keep cohort starts, fee and charge currency, learning mode, prerequisites, and practice examples visible in the course HTML. Add named instructors only when a real public profile can be matched to a course or cohort.
2. Continue the topic-guide structure. Each guide answers which course to choose, illustrates a small practice project, provides relevant links, and sends readers to the live catalog for pricing.
3. Approach Course Report about eligibility for a school listing or a useful guest article when course facts, cohort schedules and review permissions are confirmed. Class Central’s listings skew toward self-paced courses, so confirm eligibility before preparing a submission. Ask genuine learners for voluntary feedback and respect applicable community rules. No outreach was sent. [Course Report listing contacts](https://www.coursereport.com/connect) · [Class Central course formats](https://www.classcentral.com/help/intro-to-online-learning)

## U.S. prompt and keyword assignments

The following are proposed English prompts for the United States, location 2840. They are hypotheses for collection.

| Priority | Proposed natural-language prompts | Existing or staged destination |
|---|---|---|
| 1 | Which live online data analytics classes work for beginners? Where can I learn Excel, SQL, Python and Power BI together? Which analytics courses offer feedback on a practical project? | /online-coding-courses-usa/data-analysis |
| 1 | Which affordable AI courses teach Python and machine learning live? What should I know before taking a practical ML course? How do a tool-use course and a model-building course differ? | /online-coding-courses-usa/ai-machine-learning |
| 1 | Where can adult beginners learn Python in live online classes? Which Python courses teach automation? Which course prepares me to work with data or ML? | /online-coding-courses-usa/python |
| 2 | Where can I learn full stack development through live classes? Should I choose React, Node.js or a full stack course? What project should a beginner be able to build? | /online-coding-courses-usa/web-development |
| 2 | Which live technology courses suit working U.S. learners? How do I compare cohort time, mentor access and full tuition from the United States? | /online-coding-courses-usa |

Check Dallas, Austin and Houston with Central Time and work-schedule constraints. Test Eastern Time fit in Atlanta, Raleigh and New York. Compare Chicago with the Texas markets on Central Time, and check Pacific Time viability for Seattle and San Francisco. Sample each city with the same national prompt and an online-intent city variant before proposing local content. All guidance describes remote attendance.

Keep under-$100 language out of published copy until the real charge and currency conversion can substantiate it. For business intelligence, point to the verified Power BI curriculum under the Data Analysis guide. Use the maintained syllabus to verify actual technology coverage before publishing mentions of Tableau, generative AI or AI agents.

## Entity, schema and access

The staging homepage and About page share an EducationalOrganization ID, with TCMOne and The Code Munk as aliases. A WebSite node, course providers and U.S. guide publishers reference that entity. The About page explains the provider, disciplines and learning process. Course pages retain their Course and Offer nodes and existing INR charges. A CourseInstance is included when a future public cohort is shown, with the same timezone-aware start in HTML and JSON-LD. Blanket in-stock status and generic instructor markup were removed from the course template because those claims require course-level evidence.

Live robots.txt has a wildcard policy that allows public learning pages and disallows app paths such as student, admin, config and storage. On the live U.S. guide, requests carrying OAI-SearchBot, ChatGPT-User, PerplexityBot and Claude-SearchBot user-agent strings got HTTP 200. Requests carrying Googlebot and bingbot user-agent strings got HTTP 403 from this connection. An ordinary client with those strings is not an authenticated crawler. Check verified bot events in the CDN, Google URL Inspection and Bing Webmaster Tools before changing the firewall. Search crawler and model-training directives were left as they were. The enrollment route is noindex and excluded from the sitemap. llms.txt was deferred because maintained public HTML and the sitemap already expose the canonical guides. [OpenAI crawler roles](https://developers.openai.com/api/docs/bots) · [Cloudflare bot reference](https://developers.cloudflare.com/ai-crawl-control/reference/bots/) · [Google AI search requirements](https://developers.google.com/search/docs/appearance/ai-features)

The prior production baseline recorded 100 sitemap URLs. The current public check found 39 production course-detail routes. A local crawl loaded 48 URLs, covering 41 course-detail pages, five U.S. guides, the homepage and About. The sample had parseable JSON-LD, an H1 and a canonical on each page. Course templates had curriculum text and an INR billing label. This is mechanical coverage of the renders. Editorial verification of every syllabus, mentor profile and offer is still required, and the external Schema Markup Validator and Rich Results Test were not run.

The new web development guide is an additive sitemap entry. Existing titles, canonicals and India course routes were retained. Keep the New Year homepage section limited to verified course availability and ask admissions to confirm future batches. The existing 90-day calendar includes 13 original article briefs and internal destinations. For GEO, add a reviewer credit and review date to each article, and link relevant guides to the published exercises. [2027 calendar and India baseline](TCMONE_2027_UPGRADE.md)

## Monitoring design

Collect the exact prompt, market, city modifier, engine, run date, answer status, brand mentions, competitor mentions, cited URLs and cited own page. Separate branded prompts and non-branded discovery prompts. Record failed requests and engines with no answer separately from answered prompts. For each engine and market, divide verified mentions and own-domain citations by successfully answered discovery prompts, showing both counts and denominators. Record observations for ChatGPT Search, Gemini, Google AI Overviews and AI Mode, Perplexity, Claude, Copilot and Bing only where the collection method is documented.

Pair visibility observations with qualified leads and verified enrollments. Use the self-reported enrollment discovery field to supplement referral data. Separate India, U.S. and UAE using trusted analytics segments or declared enrollment country as appropriate. Keep personally identifying form fields out of GA4. Connect Search Console and GA4 and verify usable attribution before reporting an acquisition lift.

## Verification and rollback

PHP lint, JS syntax and Git whitespace checks were run locally. HTTP checks exercised the public form, invalid course and invalid inputs, scoped CSRF rejection, and private cache/index directives. Browser checks covered desktop currency changes, time zone controls, and a phone-width layout with no horizontal overflow. An isolated synthetic MariaDB schema verified repeatable migration, exact INR order amount, one enrollment after repeating approval, and the order-enrollment link. Controlled PHP checks covered India, U.S. and UAE pricing, a U.S. residence with UAE billing, cross-account and cross-course quote rejection, expiry, and winter/summer New York conversion. These checks did not process a real transfer or call PayU.

The pre-enrollment snapshot is at C:/Users/user/AppData/Local/Temp/tcm-enrollment-backup-8bb59c07437e43d0b512b295cb8d4c71. It includes the base revision, the earlier tracked pricing/SEO patch, and the earlier untracked implementation and report. To roll back after an approved promotion, restore the previous code release and cached assets, remove the new guide from the sitemap by reverting its change, and leave any recorded payment metadata in place. Retain existing payment and enrollment records. Production was not deployed.
