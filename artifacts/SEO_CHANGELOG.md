# TCM One — Technical SEO Implementation Changelog

**Release Version**: Global SEO 2.0  
**Implementation Date**: October 2026  
**Auditor / Engineer**: Antigravity Technical SEO & Full-Stack Engineering Team  
**Scope**: Codebase optimization across routing, indexing, structured data, metadata, and internationalization.

---

## 1. Routing & Server Configuration

### File: `.htaccess`
- **Canonical 301 Homepage Consolidation**: Added rewrite condition and rule matching `%{THE_REQUEST}` to 301 redirect requests for `/index.html` to root `/` without breaking internal Apache `DirectoryIndex` execution.
- **Canonical 301 Extensionless Routing**: Added permanent 301 redirect rules mapping legacy `.html` requests to clean canonical endpoints:
  - `courses.html` &rarr; `courses`
  - `workshops.html` &rarr; `workshops`
  - `about.html` &rarr; `about`
  - `contact.html` &rarr; `contact`
  - `partner.html` & `partner` &rarr; `for-institutions`
  - `for-institutions.html` &rarr; `for-institutions`
  - `insights.html` & `insights` &rarr; `tech-updates`
  - `programs.html` &rarr; `courses`
- **Internal Directory Protection**: Added `artifacts` to the forbidden paths rewrite rule: `RewriteRule ^(src|config|database|storage|views|bin|docs|artifacts)(/|$) - [F,L]`.
- **Security & Trust Headers**: Injected `X-Content-Type-Options: nosniff`, `X-Frame-Options: SAMEORIGIN`, and `Referrer-Policy: strict-origin-when-cross-origin`.

---

## 2. Crawler Guidance & Sitemaps

### File: `robots.txt`
- **Purged Direct Contradictions**: Removed mutually conflicting `Allow: /course-details.html` followed by `Disallow: /course-details.html`.
- **Whitelisted Canonical Clean Routes**:
  - `Allow: /courses` & `Allow: /courses/`
  - `Allow: /workshops` & `Allow: /workshops/`
  - `Allow: /tech-updates` & `Allow: /tech-updates/`
  - `Allow: /for-institutions`
  - `Allow: /about`
  - `Allow: /contact`
  - `Allow: /learners-reviews.html`
  - `Allow: /community.html`
  - `Allow: /portfolio/`
- **Shielded Application Internals**: Disallowed `/admin`, `/student`, `/auth`, `/api/`, `/src/`, `/views/`, `/config/`, `/database/`, `/storage/`, `/bin/`, `/artifacts/`, `/*.php$`, `/course-details.html`, `/event-details.html`.
- **Sitemap Declaration**: Linked to `https://thecodemunk.in/sitemap.xml`.

### File: `sitemap.php`
- **Dynamic Database Architecture**: Replaced static hardcoded course list with dynamic MariaDB query pulling all 31 active published courses from `courses` table with live `updated_at` timestamps.
- **Dynamic Workshop Feed**: Pulls all active live workshops directly from `WorkshopData::all()`.
- **Dynamic Blog / Tech Updates Feed ("blogs bhi seo hone chahiye")**: Automatically queries all published articles from `posts` table (`SELECT slug, published_at, updated_at FROM posts WHERE status = 'published'`) and generates `/tech-updates/{slug}` URLs with priority 0.7.
- **Modernized Core Pages**: Updated static routes from deprecated `.html` versions to clean canonical URLs (`/courses`, `/workshops`, `/tech-updates`, `/for-institutions`, `/about`, `/contact`).

---

## 3. Blog & Content Engine SEO

### File: `views/public/updates/show.php`
- **Schema.org JSON-LD**: Added comprehensive `BlogPosting` / `Article` schema with `headline`, `description`, `image`, `datePublished`, `dateModified`, `author` (TCM One Editorial), `publisher` (TCM One), `inLanguage` (`en-IN`), and `articleSection`.
- **Breadcrumbs Schema**: Implemented 3-tier `BreadcrumbList` schema (Home > Tech Updates > Article Title).
- **Social Graph Metadata**: Added full Twitter Cards (`summary_large_image`) with `@thecodemunk` attribution and OpenGraph article metadata (`og:type=article`, `article:published_time`, `article:modified_time`, `article:section`, `og:locale=en_IN`).
- **International Hreflang**: Added `en-IN`, `en-US`, and `x-default` alternate link tags.
- **Accessible UI Breadcrumbs**: Injected styled breadcrumb navigation strip in article header.

### File: `views/public/updates/index.php`
- **Schema.org JSON-LD**: Added `CollectionPage` and `BreadcrumbList` schema.
- **Social Metadata**: Added complete Twitter Cards and OpenGraph tags.
- **International Hreflang**: Added `en-IN`, `en-US`, and `x-default` alternate link tags.

---

## 4. Course & Workshop Landing Pages

### File: `views/public/courses/show.php`
- **Enhanced Course Schema**: Extended `Course` schema with `hasCourseInstance` (declaring online mode, workload duration, and mentor team).
- **Breadcrumbs Schema**: Added 3-tier `BreadcrumbList` schema (Home > Courses > Course Title).
- **Twitter Cards & Hreflang**: Added `twitter:card="summary_large_image"` and international hreflang tags (`en-IN`, `en-US`, `x-default`).
- **International Learner Helper**: Added transparent billing notice in enrollment sidebar: `Approx. $X USD · All taxes included · Billed in INR` plus `Sessions in IST (UTC+5:30) with HD recordings & weekend cohorts`.

### File: `views/public/courses/index.php`
- **Schema & Social**: Added `BreadcrumbList` schema, complete Twitter Card tags, and international hreflang links.

### File: `views/public/workshops/show.php`
- **Social & Hreflang**: Added Twitter Card metadata (`summary_large_image`), `og:locale="en_IN"`, and international hreflang tags (`en-IN`, `en-US`, `x-default`).

### File: `views/public/workshops/index.php`
- **Schema & Social**: Added `BreadcrumbList` schema, Twitter Card tags, `og:locale="en_IN"`, and international hreflang links.

---

## 5. Entity & Brand Knowledge Graph

### File: `views/public/about/index.php`
- **Schema.org EducationalOrganization**: Implemented rich organization schema declaring brand name "TCM One", alternate name "The Code Munk", official registered address in Bilaspur, CG, telephone `+91-9238695500`, admissions contact point, and verified social profiles (Instagram, LinkedIn, YouTube).
- **Breadcrumbs & Hreflang**: Added `BreadcrumbList` schema and international hreflang tags.

### File: `views/public/about/contact.php`
- **Schema.org ContactPage**: Implemented `ContactPage` schema with admissions ContactPoint, physical campus address, and official phone/email.
- **Breadcrumbs & Hreflang**: Added `BreadcrumbList` schema and international hreflang tags.

### File: `views/public/institutions/index.php`
- **Social & Hreflang**: Added Twitter Card metadata, `og:locale="en_IN"`, and international hreflang tags.

### Files: `index.html`, `learners-reviews.html`, `community.html`
- **Hreflang Tags**: Added reciprocal `en-IN`, `en-US`, and `x-default` alternate link tags across all core marketing pages.
- **Twitter Card Consistency**: Verified and synchronized Twitter Card tags.
