# TCM One — Master Technical SEO Implementation Plan

**Domain**: `https://thecodemunk.in/`  
**Brand**: TCM One  
**Version**: 2.0 (Global Search Engine Architecture)  
**Status**: Executed & Production Ready

---

## 1. Architectural Blueprint & Philosophy

The TCM One SEO architecture is designed around four foundational pillars:
1. **Zero-Guesswork Crawlability**: Search engines receive unambiguous 200 OK responses on clean canonical paths, immediate 301 redirects on legacy paths, and instant 403 Forbidden on internal application secrets.
2. **Dynamic Truth from Database**: The sitemap and catalog pages automatically discover newly published courses and blog articles directly from MariaDB without manual XML maintenance.
3. **Rich Semantic Layer**: Every entity (Course, Event, Article, Organization, Breadcrumb) is clearly declared using Schema.org JSON-LD to unlock Google Rich Results (Course Snippets, Event Carousels, Article Badges, Breadcrumbs).
4. **Global Reach with Local Trust**: Multiregional targeting (India + US/UK/Canada/Australia/UAE) through hreflang, transparent currency billing, and dual timezone references.

---

## 2. Phase 1: Technical Foundation & Crawl Pipeline

### 2.1 Canonical 301 URL Consolidation
In `.htaccess`, ensure legacy static file requests and homepage aliases are permanently redirected to canonical clean routes before filesystem checks:
```apache
# Canonical homepage redirect: /index.html -> /
RewriteCond %{THE_REQUEST} \s/+(?:tcm/)?index\.html[\s?] [NC]
RewriteRule ^index\.html$ ./ [R=301,L]

# Clean extensionless route consolidation
RewriteRule ^courses\.html$ courses [R=301,L]
RewriteRule ^workshops\.html$ workshops [R=301,L]
RewriteRule ^about\.html$ about [R=301,L]
RewriteRule ^contact\.html$ contact [R=301,L]
RewriteRule ^partner\.html$ for-institutions [R=301,L]
RewriteRule ^partner$ for-institutions [R=301,L]
RewriteRule ^for-institutions\.html$ for-institutions [R=301,L]
RewriteRule ^insights\.html$ tech-updates [R=301,L]
RewriteRule ^insights$ tech-updates [R=301,L]
RewriteRule ^programs\.html$ courses [R=301,L]
RewriteRule ^programs$ courses [R=301,L]
```

### 2.2 Security and Secret Hardening
Ensure crawlers and external visitors are strictly forbidden from inspecting application guts:
```apache
RewriteRule ^(src|config|database|storage|views|bin|docs|artifacts)(/|$) - [F,L]
RewriteRule (^|/)\.(env|git) - [F,L]
```

### 2.3 Robots.txt Directives
Provide definitive crawl guidance with zero contradictory rules:
```txt
User-agent: *
Allow: /
Allow: /courses
Allow: /courses/
Allow: /workshops
Allow: /workshops/
Allow: /tech-updates
Allow: /tech-updates/
Allow: /for-institutions
Allow: /about
Allow: /contact
Allow: /learners-reviews.html
Allow: /community.html
Allow: /portfolio/
Allow: /assets/
Allow: /uploads/

Disallow: /admin
Disallow: /student
Disallow: /auth
Disallow: /api/
Disallow: /src/
Disallow: /views/
Disallow: /config/
Disallow: /database/
Disallow: /storage/
Disallow: /bin/
Disallow: /artifacts/
Disallow: /*.php$
Disallow: /course-details.html
Disallow: /event-details.html

Sitemap: https://thecodemunk.in/sitemap.xml
```

### 2.4 Dynamic Sitemaps Engine (`sitemap.php`)
The sitemap dynamically constructs XML from the database:
- **Core Static Pages**: `/`, `/courses`, `/workshops`, `/tech-updates`, `/for-institutions`, `/about`, `/contact`, `/learners-reviews.html`, `/community.html`, `/partner-policy.html`, `/privacy.html`, `/terms.html`, `/sitemap.html`.
- **Courses**: `SELECT slug, updated_at FROM courses WHERE status = 'published'`.
- **Workshops**: Iterates `WorkshopData::all()`.
- **Tech Updates**: `SELECT slug, published_at, updated_at FROM posts WHERE status = 'published'`.

---

## 3. Phase 2: Schema.org Structured Data Implementation

### 3.1 Course Schema (`views/public/courses/show.php`)
```json
{
  "@context": "https://schema.org",
  "@type": "Course",
  "name": "Full Stack Web Development (MERN)",
  "description": "Comprehensive practical full stack engineering curriculum...",
  "url": "https://thecodemunk.in/courses/full-stack-web-development",
  "image": "https://thecodemunk.in/assets/og-thumbs/full-stack.png",
  "inLanguage": "en-IN",
  "educationalLevel": "Beginner to Advanced",
  "teaches": "Full Stack Web Development",
  "courseMode": "online",
  "hasCourseInstance": {
    "@type": "CourseInstance",
    "courseMode": "online",
    "courseWorkload": "16 Weeks",
    "instructor": {
      "@type": "Organization",
      "name": "TCM One Mentors"
    }
  },
  "provider": {
    "@type": "Organization",
    "name": "TCM One",
    "url": "https://thecodemunk.in",
    "logo": "https://thecodemunk.in/assets/logo.png"
  },
  "offers": {
    "@type": "Offer",
    "price": "14999",
    "priceCurrency": "INR",
    "availability": "https://schema.org/InStock",
    "url": "https://thecodemunk.in/courses/full-stack-web-development"
  }
}
```

### 3.2 Blog Article Schema (`views/public/updates/show.php`)
```json
{
  "@context": "https://schema.org",
  "@type": "BlogPosting",
  "headline": "What is the Current IT Industry Situation? A Realistic Guide for Tech Aspirants",
  "description": "An unfiltered analysis of hiring trends, tech stacks, and practical survival tactics...",
  "image": "https://thecodemunk.in/uploads/it-situation-cover.webp",
  "datePublished": "2026-10-15T19:40:48+05:30",
  "dateModified": "2026-10-15T19:40:48+05:30",
  "mainEntityOfPage": {
    "@type": "WebPage",
    "@id": "https://thecodemunk.in/tech-updates/what-is-the-current-it-industry-situation"
  },
  "author": {
    "@type": "Organization",
    "name": "TCM One Editorial",
    "url": "https://thecodemunk.in/about"
  },
  "publisher": {
    "@type": "Organization",
    "name": "TCM One",
    "url": "https://thecodemunk.in",
    "logo": {
      "@type": "ImageObject",
      "url": "https://thecodemunk.in/assets/logo.png"
    }
  },
  "inLanguage": "en-IN",
  "articleSection": "Industry Insights",
  "wordCount": 1850
}
```

### 3.3 Universal BreadcrumbList Schema
Standardized 3-tier hierarchy applied across all detail views:
```json
{
  "@context": "https://schema.org",
  "@type": "BreadcrumbList",
  "itemListElement": [
    { "@type": "ListItem", "position": 1, "name": "Home", "item": "https://thecodemunk.in/" },
    { "@type": "ListItem", "position": 2, "name": "Tech Updates", "item": "https://thecodemunk.in/tech-updates" },
    { "@type": "ListItem", "position": 3, "name": "Article Title", "item": "https://thecodemunk.in/tech-updates/article-slug" }
  ]
}
```

---

## 4. Phase 3: Content & Blog SEO ("blogs bhi seo hone chahiye")

### 4.1 Content Engineering Guidelines
1. **Target Word Count**: Minimum 1,200 words for technical guides; minimum 800 words for industry news.
2. **Visual Hierarchy**: Exactly one H1 (the article title). Subsections use H2 for major themes and H3 for sub-points.
3. **Structured Excerpt**: 140–160 characters designed for meta description and Google snippet display.
4. **Contextual Internal Linking**:
   - Every article must contain at least 2 contextual links pointing to active courses (e.g. Full Stack, Python, DSA, AI/ML).
   - Every article must contain 1 link to a live workshop or career event.
5. **Reader Engagement**: Like button and verified student comment threads to generate user-generated content signals.

---

## 5. Phase 4: International SEO & Multi-Region Setup

1. **Hreflang Tags**:
   Implemented across homepage, courses, workshops, about, contact, and tech updates:
   ```html
   <link rel="alternate" hreflang="en-IN" href="https://thecodemunk.in/courses">
   <link rel="alternate" hreflang="en-US" href="https://thecodemunk.in/courses">
   <link rel="alternate" hreflang="x-default" href="https://thecodemunk.in/courses">
   ```
2. **Timezone Support**:
   - Primary: Indian Standard Time (IST, UTC+5:30).
   - Notice: "Sessions in IST (UTC+5:30) with HD recordings & weekend cohorts for global learners."
3. **Currency Anchoring**:
   - Legal Billing Currency: Indian Rupee (`INR / ₹`).
   - Secondary Reference: `Approx ~$X USD • Billed in INR`.

---

## 6. Phase 5: Verification & Deployment Checklist

- [x] PHP syntax linting on all modified controllers and views.
- [x] XML validation of `sitemap.php` using DOMDocument parser.
- [x] JSON-LD Schema verification using JSON linting.
- [x] HTTP status code verification for 301 redirects in `.htaccess`.
- [x] Git version control commit and remote push.
