# TCM One India Keyword Research

**Snapshot:** 19 November 2026  
**Market:** India, English (`IN / en`)  
**Domain:** `https://thecodemunk.in`  
**Purpose:** Prioritize real course and institute search intents for site pages, not produce a keyword-stuffed list.

## Method and limits

Keyword ideas and demand estimates were pulled from OpenSEO's blended keyword data for India. Search volumes are estimated average monthly searches, not TCM One impressions, clicks, or guaranteed traffic. Similar variants can share one grouped volume, so do not add their figures together. Keyword difficulty is a third-party estimate, not a Google metric or a ranking guarantee. Search Console is not connected to this project, so this report cannot yet show the queries TCM One actually ranks for.

Live India search results and course-provider pages were reviewed to compare page intent and content patterns. Course pages that compete in this market commonly make format, syllabus, fees, duration, projects and batch details easy to compare. See [BootcampGuide's technology course directory](https://www.bootcampguide.com/courses/technology), [RST Forum's IT training catalogue](https://rstforum.net/), and the [Getin data analytics course page](https://www.getintechnologies.com/data-analytics-course-in-india/). This is a qualitative SERP review, not a claim that those providers are direct business competitors in every course.

## Demand snapshot and page assignment

| Page / cluster | Primary query target | India searches / month | KD | Intent and action |
|---|---|---:|---:|---|
| Homepage | software training institute | 2,900 | 4 | Provider discovery. Keep the page clear that TCM One offers live online training; support with IT training institute and India phrasing. |
| Course directory | software development courses | 2,900 | 38 | Course discovery. Lead with course categories and link to one landing page per subject. |
| Full Stack | full stack web development course | 3,600 | 36 | Course evaluation. Include MERN, online cohort, curriculum, price, duration and actual project details. |
| Full Stack | full stack web development course online | 320 | 24 | Transactional long tail. The dedicated course page should make enrolment, fees and format explicit. |
| Python | Python course online | 6,600 | 48 | Broad learning demand. Use the programming course page; mention beginner level and automation only where the syllabus supports it. |
| Data Analytics | data analytics course | 33,100 | 23 | Commercial course intent. Keep Excel, SQL and Power BI in the title/body; show actual fees and duration. |
| Data Analytics | data analytics course fees | 9,900 | 11 | High-value evaluation query. Ensure current price and payment terms are crawlable text, not only rendered client-side. |
| Data Analytics | data analytics course online | 8,100 | 51 | Commercial online-course variant; address on the same course page, not a duplicate landing page. |
| AI / ML | AI course online | 27,100 | 49 | Broad, mixed intent. Route to the AI course directory/track and distinguish fundamentals from engineering courses. |
| AI / ML | AI and machine learning course | 14,800 | 25 | Topic discovery. Use AI/ML course titles and curriculum coverage accurately. |
| Generative / agentic AI | agentic AI course online | 390 | 13 | Smaller but commercially useful niche. Align with the AI agents/workflow course and publish concrete syllabus details. |
| Cloud | cloud computing course | 12,100 | 40 | Broad course discovery. Route to Cloud & DevOps; distinguish AWS fundamentals from certification exam preparation. |
| Cloud | cloud computing online course | 880 | 43 | Online format modifier for the same cloud landing page. |
| Java | Java programming course online | 2,400 | 36 | Course intent. Use the Core Java page; keep DSA interview preparation on its separate course page. |
| Beginner coding | coding course for beginners | 480 | 41 | Beginner intent. Use beginner programming foundations and disclose prerequisites and expected practice. |
| IT training | IT training courses | 590 | 60 | Broad/informational and competitive. Support on the course directory; avoid making it the only acquisition target. |
| Online IT training | online IT training courses | 50 | n/a | Low reported volume in this dataset. Keep natural wording in page copy, but prioritize the stronger subject-specific terms above. |

Volumes and difficulty are estimates from the India research snapshot. Similar variants (for example, singular/plural or reversed word order) may be grouped; figures are deliberately not summed. Some broad queries return unrelated education, brand, free-course or tutorial terms. Those are excluded from the target plan even when their estimated volume is large.

## Course-page map

Keep one page per distinct course intent. The existing [master keyword map](KEYWORD_MAPPING.md) lists all 31 published course slugs and their subject-specific primary and supporting terms. The highest-priority clusters from this research are:

- `/courses/full-stack-web-development` — Full Stack web development course; MERN stack course; online Full Stack course; fees and syllabus.
- `/courses/python-programming-automation` — online Python course; Python programming course; Python for beginners; automation projects.
- `/courses/data-analytics-excel-sql-powerbi` — data analytics course; data analyst course; Excel, SQL and Power BI; course fees.
- `/courses/artificial-intelligence-machine-learning` and `/courses/ai-machine-learning-fundamentals` — AI/ML course intent, separated by level and syllabus.
- `/courses/generative-ai-agent-engineering` and `/courses/ai-agents-workflow-automation` — generative AI and agentic AI topics, distinguished by actual curriculum.
- `/courses/aws-cloud-deployment-fundamentals` and `/courses/docker-devops-cicd-essentials` — cloud/AWS and DevOps/Docker intent kept separate.
- `/courses/core-java-object-oriented-programming` and `/courses/dsa-problem-solving-java` — Java programming vs. DSA/interview preparation.
- Supporting routes should keep their own focus: React/frontend, Node.js backend, React Native, software testing, cybersecurity, UI/UX, system design, SQL, and digital skills.

Do not copy every variant onto every page. Each page should answer its own user's questions, use the course's real title and syllabus, and link to closely related courses where useful.

## Local intent

Search results for Raipur and Bilaspur show a mix of local classroom institutes, tutoring, and live-online courses. TCM One's local pages should state clearly whether a TCM classroom exists. The Bilaspur page has a distinct local partner option through JMK; present that partner's courses and admissions as its own offering. The TCM One cohorts remain online. Avoid creating more city pages unless they contain verified city-specific information beyond swapping the city name. No reliable city-level volume was collected in this pass; the keyword tool did not return a clear Bilaspur, Chhattisgarh city location.

## What not to target with unsupported claims

- Do not claim “best,” “No. 1,” guaranteed placement, job outcomes, or salary results without verifiable evidence.
- Use “certificate” or “certification” only on course pages whose actual course record includes the relevant completion certificate; do not imply vendor or university accreditation.
- Do not create pages for free courses, placements, exam certification, or offline campuses when TCM One does not offer those exact things.
- Do not use the HTML `meta keywords` tag as a keyword strategy. Put relevant phrases naturally in titles, headings, syllabus, pricing, FAQs, and internal links.

## Implementation status

- Homepage title and share title now target software training institute intent in India; the description explains the live online format and course subjects.
- Course directory title and dynamic title now target software development courses in India; the description covers actual course families.
- Course-detail metadata now includes India's live-online format, retains the course-specific summary, and removes the generic “Live Tech Course” title filler.
- Course-track titles now add the India market context while keeping each track topic distinct.
- Search Console integration and city-level keyword volume remain outstanding. Use Search Console query/page data before expanding geographic pages or choosing the next title revisions.
