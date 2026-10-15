# TCM One — SEO & International Growth Dashboard Specification

**Platform Ecosystem**: Google Search Console (GSC), Google Analytics 4 (GA4), Looker Studio  
**GA4 Measurement ID**: `G-19F749QEBF`  
**Preferred Host**: `https://thecodemunk.in/`

---

## 1. Google Search Console (GSC) Monitoring Framework

### 1.1 Property Architecture
- **Primary Property**: Domain Property `thecodemunk.in` (captures root, subdomains, http/https protocols).
- **Secondary Prefix**: `https://thecodemunk.in/` (verifies hreflang and structured data reporting).

### 1.2 Core Indexing & Technical Health KPIs

| Monitor | Target Threshold | Alert Trigger | Response Action |
| :--- | :--- | :--- | :--- |
| **Sitemap Processing** | 100% Processed ("Success") | Any status other than Success | Inspect `sitemap.php` XML output for malformed tags. |
| **Indexed Pages vs Discovered** | > 95% ratio | Indexed count drops > 10% | Run URL inspection on dropped course or blog URLs. |
| **404 / 500 Crawl Errors** | 0 crawl errors | Any 5xx or unhandled 404 | Inspect `.htaccess` rewrites or missing route definitions. |
| **Schema Validation Errors** | 0 critical errors | Warning or Error on Course/Event/Article | Inspect view JSON-LD templates. |
| **Mobile Usability Issues** | 0 issues | Any viewport or clickable target alert | Audit responsive CSS drawer or padding. |

### 1.3 Query Performance Categorization
Queries are segmented in GSC Performance reports into three primary buckets:
1. **Brand Authority**:
   - Queries: `tcm one`, `the code munk`, `tcmone academy`, `thecodemunk.in`.
   - Goal: > 45% CTR; Position 1.0; zero SERP cannibalization.
2. **High-Intent Course Queries**:
   - Queries: `full stack web development live course`, `dsa java mentorship`, `python automation live class`, `react nextjs cohort`.
   - Goal: Top 5 ranking; > 8% CTR.
3. **Top-of-Funnel Blog & Learning Queries**:
   - Queries: `current it industry situation hiring`, `dsa patterns coding interview`, `build first full stack project`.
   - Goal: Featured snippet capture; > 5% CTR.

---

## 2. Google Analytics 4 (GA4) Event Taxonomy

### 2.1 Tracked Conversion Events

| Event Name | Trigger Location | Parameters Captured | Business Significance |
| :--- | :--- | :--- | :--- |
| `course_view` | `/courses/{slug}` | `course_slug`, `course_title`, `value`, `currency='INR'` | Gauges syllabus engagement by course. |
| `workshop_view` | `/workshops/{slug}` | `workshop_slug`, `workshop_title`, `value`, `currency='INR'` | Measures live workshop interest. |
| `workshop_lead_created`| Workshop Modal | `workshop_slug`, `lead_id` | Core lead acquisition for live cohorts. |
| `course_enquiry` | Course Page CTA | `course_slug`, `method` ('whatsapp', 'brochure') | High-intent commercial consultation. |
| `contact_lead_created` | `/contact` | `inquiry_type` ('student', 'institution') | Direct admissions inbound contact. |
| `partner_application_submitted`| `/for-institutions`| `institution_name`, `city` | B2B college partnership pipeline. |
| `blog_engagement` | `/tech-updates/{slug}` | `article_slug`, `action` ('like', 'comment') | User engagement on educational articles. |

### 2.2 Custom Dimensions to Register in GA4
1. `course_slug` (Event scope)
2. `course_title` (Event scope)
3. `workshop_slug` (Event scope)
4. `article_slug` (Event scope)
5. `user_country` (User scope, derived from IP/geo)
6. `student_city_tier` (User scope: Tier 1 vs Tier 2/3)

---

## 3. International Segmentation & Geo Reporting

In GA4 and Looker Studio, traffic is filtered through three geographic segments:

### Segment 1: International English Diaspora & Career Changers
- **Countries**: United States, United Kingdom, Canada, Australia, UAE, Saudi Arabia.
- **Key Metrics**: Organic sessions, Average session duration, Conversion rate to WhatsApp/Contact, Currency engagement.
- **Benchmark Goal**: International traffic to represent 15–20% of organic search acquisition within 90 days.

### Segment 2: Domestic Tier 1 Metros
- **Cities**: Bengaluru, Pune, Hyderabad, Delhi NCR, Mumbai, Chennai.
- **Key Focus**: Career transition courses (Full Stack, System Design, AI Agents).

### Segment 3: Emerging Regional Tech Hubs (Tier 2/3)
- **Cities**: Bilaspur, Raipur, Indore, Bhopal, Nagpur, Jaipur, Lucknow, Patna, Chandigarh.
- **Key Focus**: Foundational workshops, beginner Python, college partnerships.

---

## 4. Looker Studio Executive Dashboard Spec

```
+-----------------------------------------------------------------------------------+
| TCM ONE — GLOBAL ORGANIC SEARCH PERFORMANCE DASHBOARD                             |
+-----------------------------------------------------------------------------------+
| [Total Organic Clicks] | [Total Organic Impressions] | [Avg CTR] | [Avg Position] |
| 12,450 (+24% MoM)      | 245,000 (+38% MoM)          | 5.1%      | 14.2           |
+-----------------------------------------------------------------------------------+
| GSC IMPRESSIONS & CLICKS TIMELINE (Daily Trend vs 30-Day Moving Average)         |
| [==================================== Trend Chart ==============================] |
+-----------------------------------------------------------------------------------+
| TOP ORGANIC LANDING PAGES                | TOP SEARCH QUERIES                    |
| 1. /courses/full-stack-web-development   | 1. full stack web development live    |
| 2. /workshops/python-starter-workshop    | 2. dsa patterns interview             |
| 3. /tech-updates/what-is-the-current...  | 3. tcm one coding                     |
| 4. /courses/dsa-problem-solving-java     | 4. python starter workshop live       |
+-----------------------------------------------------------------------------------+
| INTERNATIONAL TRAFFIC SPLIT              | COURSE CONVERSION FUNNEL               |
| India: 78%                               | Landed: 100%                           |
| United States: 11%                       | Viewed Syllabus: 42%                   |
| UAE & Gulf: 5%                           | Clicked Enroll / WhatsApp: 14%         |
| UK & Canada: 4%                          | Completed Registration: 6.8%           |
+-----------------------------------------------------------------------------------+
```

### Alert Thresholds (Automated Email Alerts)
- **Drop Alert**: > 20% drop in organic impressions week-over-week triggers emergency crawl audit.
- **Spike Alert**: > 50% spike in impressions on any individual blog article signals breakout ranking (trigger instant review for CTA optimization).
