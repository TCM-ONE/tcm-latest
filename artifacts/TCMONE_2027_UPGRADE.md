# TCM One 2027 staging changes and release notes

## India baseline · November 27, 2026

The production homepage, catalog, Data Analysis detail, contact page and four U.S. guides returned HTTP 200 with self-canonicals. The homepage title is “Software Training Institute in India | TCM One” and the catalog title is “Software Development Courses in India | TCM One.” The sampled India pages declare en-IN and x-default. The U.S. guides cover distinct topics and have their own canonicals. The production sitemap parsed as XML with 100 entries, including all four U.S. guides. The seven courses and three tracks linked from those guides returned HTTP 200 directly. [Production sitemap](https://thecodemunk.in/sitemap.xml)

The November 19 India research estimated 2,900 monthly searches for “software training institute,” 33,100 for “data analytics course” and 14,800 for “AI and machine learning course.” These estimates describe search demand. Country clicks, impressions, leads, revenue and conversion rates require property access. [India keyword research](KEYWORD_RESEARCH_2026-11-19.md)

## U.S. keyword map

The terms below are unmeasured U.S. English seeds for market code 2840. OpenSEO rejected the national metrics batch for insufficient credits. Keyword Planner, Trends, Ahrefs and Semrush did not provide metrics for this run. City volume, difficulty, competition, rankings and 2027 seasonal demand remain unknown. [Existing U.S. keyword research](https://app.openseo.so/p/a3071e7c-4e06-41c9-b7ea-9341d9da7314/reports)

| Intent | U.S. seed terms | Destination |
|---|---|---|
| Compare live technology classes | live online technology courses, online coding classes for working professionals | /online-coding-courses-usa |
| Compare beginner analytics courses | live online data analytics classes, data analytics course for beginners, Excel SQL Python Power BI course | /online-coding-courses-usa/data-analysis |
| Compare AI and ML curricula | online AI and machine learning course, live machine learning classes, AI training for working professionals | /online-coding-courses-usa/ai-machine-learning |
| Compare Python courses | live Python coding classes, online Python course for beginners, Python for AI and ML | /online-coding-courses-usa/python |
| Evaluate web development courses | full stack web development course online, React training with projects | /courses/tracks/web-mobile-development |
| Compare reporting skills | Power BI course online, Excel and SQL for data analytics | Data Analysis guide and its linked Power BI course |

Pair online-intent and local-intent seeds for analytics in Dallas, coding in Austin, AI in Houston, Python in Atlanta, online technology training in Raleigh, analytics in Chicago and New York, AI in Seattle and machine learning in San Francisco. For each, measure intent and demand before pursuing city content. The existing national guides serve remote students in all nine cities.

Noble Desktop specifies live versus self-paced delivery, instructor access and practice. General Assembly's U.S. catalog documents instructional hours and assessment outcomes. TCM One has an opportunity to offer comparable facts for shorter cohort-based courses, particularly confirmed hours, prerequisites, examples of work and the total INR charge. This comparison is based on published provider content. [Noble Desktop](https://www.nobledesktop.com/classes/data-analysis-basic-course) · [General Assembly U.S. catalog](https://ga-public-downloads.s3.us-east-1.amazonaws.com/regulatory/US-Catalog.pdf)

## AI discovery and technical SEO

OpenSEO has no configured AI tracker or recorded checks. Production robots.txt uses general agent rules that allow public courses. CDN-specific access and actual AI citations are unverified. Candidate prompts for a future measured run cover affordable live data analytics, beginner Python with mentor feedback, live AI courses and practical machine learning projects. Keep “under $100” as a research prompt until verified course pricing supports that claim. [OpenAI crawler reference](https://developers.openai.com/api/docs/bots)

The local U.S. guide's WebPage and BreadcrumbList JSON-LD parsed. Google supports Course List with eligible Course and carousel markup and phased out the Course Info search feature. Rich Results Test, Schema Markup Validator, Bing Webmaster Tools and field performance tools did not produce results in this run. [Google Course List](https://developers.google.com/search/docs/appearance/structured-data/course) · [Google feature change](https://developers.google.com/search/blog/2025/06/simplifying-search-results)

## Sampled U.S. student experience

Editorial scores measure the information and controls observable in the sample. The overall is the sum of ten equally weighted categories. Checkout execution and field performance are outside this score.

| Category | Score | Inspection |
|---|---:|---|
| Homepage and branding | 8/10 | Homepage explains the learning offer and retains its India-oriented title. |
| Navigation and discovery | 8/10 | U.S. guides connect to working course links. The staging homepage adds a direct guide link. |
| Readability and course information | 8/10 | Topic guides include comparisons, practice examples and prerequisites. Confirm cohort details before payment. |
| Mentors and learning evidence | 6/10 | Guides explain instructor-led delivery and projects. The sampled comparisons lack named instructors per option. |
| Testimonials and certificates | 7/10 | Homepage has learner reviews and course details link to certificate requirements. |
| Pricing and transparency | 6/10 | Course pages display tuition and settlement details. International fees depend on the transfer provider. |
| Enrollment and financing information | 6/10 | Enrollment controls and financing text are visible. Completion remains unverified. |
| Desktop and mobile header | 8/10 | Desktop exposed seven currency options and persisted CAD into the mobile drawer. |
| Public response | 7/10 | Seven sampled routes returned zero redirects and time to first byte of 0.30–0.70 seconds from this connection. |
| International support | 7/10 | The U.S. hub explains online delivery from India and provides admissions links. |
| **Sampled overall** | **71/100** | **Equal-weight total.** |

## Change log and file map

| Files | Change |
|---|---|
| src/Services/RegionalPricing.php, src/Services/ExchangeRates.php, .env.example, .gitignore | Separate country-based INR pricing from display currency. Validate global and category markup. Fetch and cache Frankfurter rates. |
| app.php, src/Controllers/Api/PublicController.php | Serve a private currency endpoint, add localized course display fields and store optional discovery answers with leads. |
| assets/tcm-editorial.js, assets/tcm-editorial.css, consuming templates | Add selectors beside desktop and drawer language controls, persist the preference and record consent-gated currency and lead events. Version shared assets together. |
| views/public/courses/, views/student/courses/, src/Controllers/Student/PaymentController.php, views/student/payments/submit.php | Use category pricing on public and student course surfaces and bank-transfer submissions. Show exact payable INR with a separate estimate when available. |
| src/Controllers/PublicWorkshopController.php, views/public/courses/show.php | Display an available future cohort start in Eastern, Central, Mountain and Pacific time with IANA time zones. |
| views/public/international/us-learners.php, index.html, contact.html, views/public/about/contact.php | Add U.S. pricing guidance and refund link, link to the U.S. guides, add a 2027 campaign and collect optional discovery attribution. |
| .htaccess | Deny direct HTTP access to SQL dumps, including supported compressed suffixes. |

## Staging and release checks

PHP lint, Node syntax, Apache config and Git whitespace checks passed. Controlled PHP checks covered nine country defaults, INR amounts, category configuration, invalid currency preferences and proxy trust. Frankfurter supplied all six foreign rates. Simulated provider failure verified the stale-cache fallback, bounded optional USD estimates ending in 9, and fallback to INR after the cache expired. Browser QA confirmed desktop CAD selection and persistence in the mobile drawer. The local homepage, contact page, currency endpoint and U.S. guide loaded. An unknown U.S. guide returned 404 and the SQL dump route returned 403. Database-backed local catalog and sitemap requests timed out.

Before approval, test with a dedicated staging database and synthetic students, cohorts and orders. Verify Indian PayU, reserve-seat, two- and three-installment flows and historical records. Test international transfer submission and amount validation. Confirm merchant-specific card and currency support. Verify regional CDN traffic, full mobile language behavior, contact submission, Core Web Vitals and structured-data validators. [PayU merchant requirements](https://docs.payu.in/docs/payment-links-faqs)

Remaining scope includes international EMI, commercial settlement rounding, locking quotes across changes to INR tuition or configured markup, full coverage of legacy forms and invoice displays, and verified payment and revenue analytics. Historical invoices and receipts should retain their original charge currency. Enable Cloudflare country trust once the origin is restricted to the trusted proxy. Without trusted country information, pricing defaults to INR. Retain 35% on first rollout if previous bank-transfer instructions are outstanding and coordinate any transition with admissions. Review the seasonal homepage section after January.

## Rollback

The source baseline is commit 646287d51ce52ce619b1247b27e264bf08cbd58a, archived outside the web root at C:/Users/user/AppData/Local/Temp/tcmone-pre-upgrade-646287d.zip. No migration was applied. Before promotion, retain the active release and back up production configuration and data via the host. To roll back, restore the previous application release and pricing environment configuration, invalidate public asset caches and verify the homepage, catalog, sitemap, PayU and bank transfer paths. Preserve orders received after rollout. The exchange-rate cache can be cleared independently.

## First 90 days of content

| Week | Article and original contribution | Destination |
|---|---|---|
| 1 | How to Become a Data Analyst in 2027, with a study plan by available hours | Data Analysis guide |
| 2 | Python vs SQL for Data Analytics, using the same dataset | Data Analysis and Python guides |
| 3 | Data Analytics vs Data Science, comparing deliverables from one business problem | Data Analysis and AI/ML guides |
| 4 | How to Build a Data Analytics Portfolio, with a reproducibility checklist | Data Analysis guide |
| 5 | Power BI Learning Roadmap, from import to a documented dashboard | Power BI course |
| 6 | Live Classes vs Recorded Courses, comparing practice and feedback | U.S. guide |
| 7 | How to Learn AI Without a CS Degree, with prerequisite exercises | AI/ML guide |
| 8 | Machine Learning Roadmap 2027, with a baseline model and evaluation rubric | AI/ML guide |
| 9 | AI and Automation Skills for Working Professionals, with a worked workflow | Published AI automation course |
| 10 | Affordable Online Tech Courses for U.S. Students, with fee and schedule comparisons | U.S. guide |
| 11 | Affordable Alternatives to Coding Bootcamps, comparing learning hours and costs | U.S. guide |
| 12 | How to Become a Full Stack Developer in 2027, with a project checklist | Web development track |
| 13 | Top Technology Skills to Learn in 2027, with paths by goal | Course catalog |

Have instructors verify the exercises. Credit the author and record the review date. Track article-to-course clicks and qualified leads when analytics is connected.

## Performance reports

**India** · Pending Search Console and GA4 access. Compare the 28 days before release and the first 28 after release for protected pages by query and device. Record impressions, clicks, CTR, organic sessions and verified enrollments.

**United States** · Pending Search Console and GA4 access. Track impressions and clicks for the guides, organic sessions by landing page and aggregate city, currency switches, discovery answers, leads, checkout starts and verified enrollments. Reconcile INR revenue with settlement records. Leave city demand and AI visibility blank until measured.
