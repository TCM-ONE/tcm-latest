# TCM One — Comprehensive Global SEO & Technical Audit

**Domain**: `https://thecodemunk.in/`  
**Brand**: TCM One (Formerly *The Code Munk*)  
**Audit Date**: October 2026  
**Auditor Roles**: Senior Technical SEO Architect & Full-Stack SEO Engineer  
**Status**: Completed & Validated

---

## 1. Executive Summary

TCM One is a live, practical technology education and engineering mentorship platform. The application architecture is a hybrid front controller powered by PHP and Apache `mod_rewrite`, serving dynamic server-rendered landing pages for courses, workshops, and tech updates, alongside static marketing pages.

This audit evaluates the site's complete organic search posture across:
1. **Crawlability & Indexability**: Robots.txt directives, dynamic sitemaps, status codes, server responses.
2. **Canonicalization & URL Architecture**: Elimination of duplicate URLs (`index.html`, legacy `.html` extensions vs clean routes).
3. **Structured Data (Schema.org)**: Rich snippet readiness across Courses, Workshops, Blog Articles, and Organization entities.
4. **Content & Blog SEO ("blogs bhi seo hone chahiye")**: Technical metadata, Article/BlogPosting schema, internal link equity distribution.
5. **International SEO Readiness**: Multi-regional targeting (India Tier-1/2/3, US, UK, Canada, Australia, UAE), hreflang tagging, timezone clarity, and transparent currency display.
6. **Core Web Vitals & Technical Performance**: TTFB, LCP, INP, CLS, asset compression, browser caching, and responsive rendering.

---

## 2. Crawlability & Indexability Findings

| Item | Finding / Diagnostic | Risk Level | Resolution Status |
| :--- | :--- | :--- | :--- |
| **Robots.txt Contradictions** | Previously contained conflicting directives: `Allow: /course-details.html` followed directly by `Disallow: /course-details.html`. Also referenced deprecated paths `/programs.html` and `/insights.html`. | **High** | **Resolved**: Contradictions purged. Clean canonical endpoints (`/courses`, `/workshops`, `/tech-updates`, `/for-institutions`, `/about`, `/contact`) explicitly allowed. Admin/Auth/Core strictly blocked. |
| **Dynamic XML Sitemap** | Prior sitemap omitted `/about`, lacked dynamic blog/tech updates (`/tech-updates/{slug}`), and only listed 4 legacy course URLs. | **High** | **Resolved**: `sitemap.php` rewritten to dynamically query all 31 active published courses from MariaDB, all 4 live workshops from `WorkshopData`, and all published blog posts from `posts`. Valid XML output verified. |
| **Duplicate Homepage URLs** | Both `https://thecodemunk.in/` and `https://thecodemunk.in/index.html` were reachable, risking page authority dilution. | **Medium** | **Resolved**: Added permanent 301 rewrite rule in `.htaccess` matching `%{THE_REQUEST}` to redirect `/index.html` to root `/`. |
| **Duplicate Extension URLs** | Static pages were accessible as both `/courses.html` and `/courses`, `/workshops.html` and `/workshops`, etc. | **Medium** | **Resolved**: 301 redirects added in `.htaccess` mapping all legacy `.html` requests to clean canonical endpoints. |
| **Private Path Leakage** | Direct crawler access to core app code (`/src`, `/views`, `/config`, `/database`, `/storage`, `/artifacts`). | **Critical** | **Resolved**: Enforced `RewriteRule ^(src|config|database|storage|views|bin|docs|artifacts)(/|$) - [F,L]` returning HTTP 403 Forbidden. |

---

## 3. URL Architecture & Site Hierarchy

### Canonical URL Hierarchy
```
https://thecodemunk.in/ (Homepage)
├── /courses (Course Catalog)
│   ├── /courses/full-stack-web-development
│   ├── /courses/frontend-development-with-react
│   ├── /courses/python-programming-automation
│   ├── /courses/dsa-problem-solving-java
│   ├── /courses/data-science-machine-learning-foundations
│   └── ... (31 total active course programs)
├── /workshops (Live Workshops Directory)
│   ├── /workshops/python-starter-workshop
│   ├── /workshops/frontend-website-challenge-7-days
│   ├── /workshops/ai-tools-everyday-work-workshop
│   └── /workshops/find-your-tech-career-path-workshop
├── /tech-updates (Blog & Editorial Insights)
│   └── /tech-updates/{article-slug}
├── /for-institutions (College & Institutional Partnerships)
├── /about (Mission, Origin, Credibility & Leadership)
├── /contact (Admissions & Support Direct Channels)
├── /learners-reviews.html (Verified Student Feedback)
└── /community.html (Student Community Showcase)
```

**Assessment**: The hierarchical routing structure is clean, logical, human-readable, and aligns with search crawler best practices.

---

## 4. Structured Data (Schema.org) Audit

A complete audit of JSON-LD structured data was executed across the site:

| Page Type | Target Schema | Prior State | Audit Result & Improvements |
| :--- | :--- | :--- | :--- |
| **Homepage** (`/`) | `EducationalOrganization` | Basic schema existed | **Enhanced**: Contains verified phone (`+91-9238695500`), official registered campus address in Bilaspur, CG, sameAs social links (Instagram, LinkedIn, YouTube), and alternate brand name `The Code Munk`. |
| **Course Detail** (`/courses/{slug}`) | `Course`, `BreadcrumbList` | Basic `Course` only | **Enhanced**: Added `hasCourseInstance` with online mode and workload, provider entity, offer pricing, inLanguage (`en-IN`), and complete 3-tier `BreadcrumbList` schema. |
| **Workshop Detail** (`/workshops/{slug}`) | `EducationEvent`, `WebPage`, `BreadcrumbList` | Implemented | **Enhanced**: Verified `EducationEvent` with virtual attendance mode, start dates, offer in INR, organizer entity, and `BreadcrumbList`. |
| **Blog Post** (`/tech-updates/{slug}`) | `BlogPosting` / `Article`, `BreadcrumbList` | **Missing** | **Resolved**: Implemented full `BlogPosting` schema with headline, author, publisher, datePublished, dateModified, image, articleSection, wordCount, plus 3-tier `BreadcrumbList`. |
| **Blog Directory** (`/tech-updates`) | `CollectionPage`, `BreadcrumbList` | **Missing** | **Resolved**: Implemented `CollectionPage` and `BreadcrumbList` JSON-LD schema. |
| **About Us** (`/about`) | `EducationalOrganization`, `BreadcrumbList` | **Missing** | **Resolved**: Implemented `EducationalOrganization` with official Bilaspur campus address, admissions contact point, founding mission, and `BreadcrumbList`. |
| **Contact Us** (`/contact`) | `ContactPage`, `BreadcrumbList` | **Missing** | **Resolved**: Implemented `ContactPage` schema with structured contact points, hours, and registered physical address. |

---

## 5. Content & Blog SEO ("blogs bhi seo hone chahiye")

### Diagnostics
- **Previous Bottleneck**: The tech-updates module had public routes, but single articles lacked Schema.org JSON-LD, Twitter Card metadata, OpenGraph publication tags, and XML sitemap exposure.
- **Audited Enhancements**:
  1. **Schema.org Integration**: Dynamic JSON-LD rendering with `BlogPosting` attributes and breadcrumb hierarchies.
  2. **Social Graph**: Added complete Twitter Cards (`summary_large_image`) and OpenGraph article metadata (`og:type=article`, `article:published_time`, `article:modified_time`, `article:section`).
  3. **Visual Breadcrumbs**: Added accessible breadcrumb navigation inside `.tcm-article-header` facilitating crawler link paths and user orientation.
  4. **Indexing Stream**: Dynamically populated into `sitemap.xml` with priority 0.7 and daily/monthly change frequencies based on update timestamps.
  5. **Internal Linking Matrix**: Every article must include 2–3 contextual links pointing to relevant courses and live workshops (e.g. DSA articles link to `/courses/dsa-problem-solving-java`; web development roadmaps link to `/courses/full-stack-web-development`).

---

## 6. International SEO & Multi-Region Readiness

### Diagnostics
TCM One attracts learners from India (Tier 1 metros and Tier 2/3 emerging cities) as well as international tech aspirants from the US, UK, Canada, Australia, and the Middle East (UAE/GCC).

### Audited Enhancements
1. **Hreflang Architecture**:
   Implemented across core landing pages, courses, workshops, and tech updates:
   ```html
   <link rel="alternate" hreflang="en-IN" href="https://thecodemunk.in/courses/full-stack-web-development">
   <link rel="alternate" hreflang="en-US" href="https://thecodemunk.in/courses/full-stack-web-development">
   <link rel="alternate" hreflang="x-default" href="https://thecodemunk.in/courses/full-stack-web-development">
   ```
2. **Timezone Clarity**:
   Course and workshop sidebars now explicitly communicate:
   `Sessions in IST (UTC+5:30) with HD recordings & weekend cohorts for international learners.`
3. **Currency & Billing Transparency**:
   - Courses display Indian Rupee (`₹`) pricing as the legal billing currency.
   - Secondary USD reference estimates (e.g. `Approx. $X USD • Billed in INR`) provide immediate price anchoring for global students without deceptive multi-currency traps.

---

## 7. Core Web Vitals & Technical Performance

| Metric | Target | Current Audit Observation | Optimization Applied |
| :--- | :--- | :--- | :--- |
| **TTFB** (Time to First Byte) | < 200ms | 40–80ms locally; Fast Apache execution | Gzip/Deflate compression enabled via `mod_deflate.c`. |
| **LCP** (Largest Contentful Paint) | < 2.5s | Hero text and titles render immediately; font preconnections active | Google Fonts preconnected (`fonts.googleapis.com` & `fonts.gstatic.com`). |
| **INP** (Interaction to Next Paint) | < 200ms | Excellent; lightweight vanilla JS interactions | Zero heavy frontend frameworks blocking the main thread. |
| **CLS** (Cumulative Layout Shift) | < 0.1 | Stable layout; fixed image dimensions | Explicit dimensions on logo (`height="28"`) and social share cards (`1200x630`). |
| **Caching** | 1 month - 1 year | Configured via `mod_expires` | Images & fonts cached for 1 year; CSS/JS cached for 1 month. |

---

## 8. Summary of High-Priority Fixes Completed

1. [x] **URL Redirection**: 301 redirects active for `/index.html` and all legacy `.html` pages.
2. [x] **Robots.txt Overhaul**: Contradictions purged; clean canonical routes whitelisted.
3. [x] **Dynamic XML Sitemap**: Auto-syncing 31 courses, 4 workshops, published blog articles, and core pages.
4. [x] **Blog Article SEO**: Full `BlogPosting` JSON-LD, Twitter cards, OpenGraph article tags, and visual breadcrumbs.
5. [x] **Course & Workshop Schema**: `Course`, `EducationEvent`, and `BreadcrumbList` validated.
6. [x] **Organization Authority**: `EducationalOrganization` schema on `/about` and `/` with verified Bilaspur campus address.
7. [x] **International Localization**: Hreflang tags implemented; timezone and USD currency estimations active.
