# TCM One — Technical SEO Operational Checklist & Audit Protocol

**Domain**: `https://thecodemunk.in/`  
**Purpose**: Rigorous quality assurance protocol for every release, page deployment, and CMS publishing cycle.

---

## 1. Crawlability & Indexability Checklist

- [x] **Robots.txt Accessibility**: Accessible at `https://thecodemunk.in/robots.txt` with HTTP 200 status code.
- [x] **Robots.txt Contradictions**: No conflicting `Allow` and `Disallow` directives targeting the same paths.
- [x] **Sitemap Linkage**: `robots.txt` concludes with `Sitemap: https://thecodemunk.in/sitemap.xml`.
- [x] **Dynamic Sitemap XML**: `sitemap.xml` generates valid XML conforming to Sitemaps.org 0.9 schema.
- [x] **Course Coverage**: All published courses from MariaDB dynamically appear in `sitemap.xml` with real `updated_at` dates.
- [x] **Workshop Coverage**: All live workshops from `WorkshopData` appear in `sitemap.xml`.
- [x] **Blog Coverage**: All published articles from `posts` table appear in `sitemap.xml` with proper timestamps.
- [x] **Security Shielding**: Private paths (`/admin`, `/student`, `/auth`, `/src`, `/views`, `/config`, `/database`, `/storage`, `/bin`, `/artifacts`) return HTTP 403 Forbidden.

---

## 2. Canonicalization & URL Hygiene

- [x] **Homepage Canonicalization**: Direct requests for `/index.html` issue an HTTP 301 redirect to `/`.
- [x] **Legacy Extension Consolidation**: Requests for `.html` versions of core pages (`courses.html`, `workshops.html`, `about.html`, `contact.html`, `partner.html`, `insights.html`, `programs.html`) issue HTTP 301 redirects to clean canonical paths.
- [x] **Self-Referential Canonical Tags**: Every public page includes a valid `<link rel="canonical" href="...">` matching its primary canonical URL.
- [x] **Protocol & Host Canonicalization**: `http://` redirects to `https://` and `www.` redirects to non-www `https://thecodemunk.in/`.

---

## 3. Structured Data (Schema.org JSON-LD)

- [x] **Course Pages** (`/courses/{slug}`):
  - [x] `@type: Course` with name, description, provider, educationalLevel, teaches, and inLanguage.
  - [x] `@type: CourseInstance` declaring courseMode ("online") and workload.
  - [x] `@type: Offer` with real price in INR and InStock availability.
  - [x] `@type: BreadcrumbList` with 3 levels (Home > Courses > Course Title).
- [x] **Workshop Pages** (`/workshops/{slug}`):
  - [x] `@type: EducationEvent` with attendance mode, start dates, and offers.
  - [x] `@type: BreadcrumbList` (Home > Workshops > Workshop Title).
- [x] **Tech Updates / Blog Articles** (`/tech-updates/{slug}`):
  - [x] `@type: BlogPosting` with headline, description, author, publisher, datePublished, dateModified, and image.
  - [x] `@type: BreadcrumbList` (Home > Tech Updates > Article Title).
- [x] **About Page** (`/about`):
  - [x] `@type: EducationalOrganization` with name, alternateName ("The Code Munk"), registered Bilaspur campus address, phone, and sameAs profiles.
  - [x] `@type: BreadcrumbList` (Home > About).
- [x] **Contact Page** (`/contact`):
  - [x] `@type: ContactPage` with structured ContactPoint and physical campus address.
  - [x] `@type: BreadcrumbList` (Home > Contact).

---

## 4. International SEO & Multi-Region Tags

- [x] **Reciprocal Hreflang Tags**:
  - `<link rel="alternate" hreflang="en-IN" href="...">`
  - `<link rel="alternate" hreflang="en-US" href="...">`
  - `<link rel="alternate" hreflang="x-default" href="...">`
- [x] **Timezone Disclosure**: Courses and workshops clearly communicate session timing in Indian Standard Time (IST, UTC+5:30) with recording availability.
- [x] **Currency Transparency**: Base billing displayed in INR (`₹`) with transparent approximate USD (`$`) reference estimates.

---

## 5. Performance & Core Web Vitals

- [x] **Asset Compression**: Apache `mod_deflate` enabled for text/html, text/css, application/javascript, image/svg+xml, and application/json.
- [x] **Browser Caching**: `mod_expires` active with 1-year caching for images/fonts and 1-month caching for CSS/JS.
- [x] **Font Optimization**: Google Fonts (`Instrument Serif`, `Inter`) preconnected to `fonts.googleapis.com` and `fonts.gstatic.com`.
- [x] **Layout Shift Elimination**: Key logos and cards have defined image dimensions preventing Cumulative Layout Shift (CLS < 0.1).
- [x] **Security & Trust Headers**: `X-Content-Type-Options: nosniff`, `X-Frame-Options: SAMEORIGIN`, and `Referrer-Policy: strict-origin-when-cross-origin`.

---

## 6. Social Sharing & Meta Graph

- [x] **Open Graph Meta Tags**: `og:site_name`, `og:type`, `og:url`, `og:title`, `og:description`, `og:image`, `og:image:width="1200"`, `og:image:height="630"`, `og:locale="en_IN"`.
- [x] **Twitter Cards**: `twitter:card="summary_large_image"`, `twitter:site="@thecodemunk"`, `twitter:title`, `twitter:description`, `twitter:image`.
- [x] **Favicon Suite**: SVG vector favicon, PNG 32x32, Apple Touch Icon 180x180, and legacy ICO fallback.

---

## 7. Analytics & Conversion Tracking

- [x] **GA4 Global Tag**: `G-19F749QEBF` embedded across all static and dynamic view templates.
- [x] **Conversion Telemetry**:
  - `course_view` on `/courses/{slug}`
  - `workshop_view` on `/workshops/{slug}`
  - `workshop_lead_created` on registration submit
  - `course_enquiry` on WhatsApp / brochure downloads
