<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <!-- Favicon & App Icons -->
  <link rel="icon" type="image/svg+xml" href="<?= base_url('/favicon.svg') ?>">
  <link rel="icon" type="image/png" sizes="32x32" href="<?= base_url('/favicon.png') ?>">
  <link rel="shortcut icon" href="<?= base_url('/favicon.ico') ?>">
  <link rel="apple-touch-icon" sizes="180x180" href="<?= base_url('/assets/logo.png') ?>">

  <!-- Google tag (gtag.js) -->
  <script async src="https://www.googletagmanager.com/gtag/js?id=G-19F749QEBF"></script>
  <script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    gtag('js', new Date());
    gtag('config', 'G-19F749QEBF');
  </script>

  <meta name="app-base" content="<?= e(base_url('/')) ?>">

  <title><?= e($title ?? 'About TCM One — Practical Education For Modern Careers') ?></title>
  <meta name="description" content="<?= e($description ?? 'Discover the mission, teaching philosophy, and verified learning approach behind TCM One.') ?>">
  <meta name="robots" content="index, follow">
  <link rel="canonical" href="<?= e($canonical ?? 'https://thecodemunk.in/about') ?>">

  <!-- Open Graph -->
  <meta property="og:type" content="website">
  <meta property="og:site_name" content="TCM One">
  <meta property="og:url" content="<?= e($canonical ?? 'https://thecodemunk.in/about') ?>">
  <meta property="og:title" content="About TCM One &mdash; Practical Education For Modern Careers">
  <meta property="og:description" content="<?= e($description ?? 'Discover the mission, teaching philosophy, and verified learning approach behind TCM One.') ?>">
  <meta property="og:image" content="<?= e($ogImage ?? base_url('/assets/logo.png')) ?>">
  <meta property="og:image:height" content="630">
  <meta property="og:locale" content="en_IN">

  <!-- Twitter Card -->
  <meta name="twitter:card" content="summary_large_image">
  <meta name="twitter:site" content="@thecodemunk">
  <meta name="twitter:title" content="About TCM One — Practical Education For Modern Careers">
  <meta name="twitter:description" content="<?= e($description ?? 'Discover the mission, teaching philosophy, and verified learning approach behind TCM One.') ?>">
  <meta name="twitter:image" content="<?= e($ogImage ?? 'https://thecodemunk.in/assets/logo.png') ?>">

  <!-- International Hreflang Tags -->
  <link rel="alternate" hreflang="en-IN" href="<?= e($canonical ?? 'https://thecodemunk.in/about') ?>">
  <link rel="alternate" hreflang="en-US" href="<?= e($canonical ?? 'https://thecodemunk.in/about') ?>">
  <link rel="alternate" hreflang="x-default" href="<?= e($canonical ?? 'https://thecodemunk.in/about') ?>">

  <!-- Structured Data (JSON-LD) -->
  <script type="application/ld+json">
  {
    "@context": "https://schema.org",
    "@type": "EducationalOrganization",
    "name": "TCM One",
    "alternateName": "The Code Munk",
    "url": "https://thecodemunk.in",
    "logo": "https://thecodemunk.in/assets/logo.png",
    "description": "Practical tech education through live classes, guided projects, and industry-focused learning in software engineering, AI, and data.",
    "address": {
      "@type": "PostalAddress",
      "streetAddress": "Seepat Rd, near DLS college, Shree Vihar, Ashok Nagar, Sarkanda",
      "addressLocality": "Bilaspur",
      "addressRegion": "Chhattisgarh",
      "postalCode": "495006",
      "addressCountry": "IN"
    },
    "contactPoint": {
      "@type": "ContactPoint",
      "telephone": "+91-9238695500",
      "contactType": "admissions",
      "areaServed": ["IN", "US", "AE", "CA", "GB"],
      "availableLanguage": ["English", "Hindi"]
    },
    "sameAs": [
      "https://www.instagram.com/tcmone.academy",
      "https://www.linkedin.com/company/thecodemunk",
      "https://www.youtube.com/@thecodemunk"
    ]
  }
  </script>
  <script type="application/ld+json">
  {
    "@context": "https://schema.org",
    "@type": "BreadcrumbList",
    "itemListElement": [
      {
        "@type": "ListItem",
        "position": 1,
        "name": "Home",
        "item": "https://thecodemunk.in/"
      },
      {
        "@type": "ListItem",
        "position": 2,
        "name": "About",
        "item": "https://thecodemunk.in/about"
      }
    ]
  }
  </script>

  <!-- Typography & Icons -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Instrument+Serif:ital@0;1&family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

  <!-- Core Bootstrap 5 CSS for Grid & Utilities -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

  <!-- Design System Stylesheets -->
  <link rel="stylesheet" href="<?= base_url('/assets/tcm-design-system.css?v=2.18') ?>">
  <link rel="stylesheet" href="<?= base_url('/assets/tcm-editorial.css?v=2.18') ?>">
  <link rel="stylesheet" href="<?= base_url('/assets/tcm-footer.css?v=1') ?>">
  <link rel="stylesheet" href="<?= base_url('/assets/cai-assistant.css?v=2.18') ?>">

  <style>
    *, *::before, *::after {
      border-radius: 0px !important;
    }

    body {
      background-color: var(--tcm-bg);
      color: var(--tcm-fg);
      font-family: var(--tcm-font-sans);
      margin: 0;
      padding: 0;
    }

    .about-story-grid {
      display: grid;
      grid-template-columns: 1fr;
      gap: 40px;
    }
    @media (min-width: 992px) {
      .about-story-grid {
        grid-template-columns: 1fr 380px;
        gap: 64px;
      }
    }
    .about-pillar-box {
      background: #FFFFFF;
      border: 1px solid var(--tcm-border);
      padding: 28px;
      transition: border-color 0.15s ease;
    }
    .about-pillar-box:hover {
      border-color: var(--tcm-fg);
    }
  </style>
</head>
<body class="tcm-editorial">

  <!-- Header Navigation & Mobile Drawer -->
  <?php \TCM\Core\View::partial('partials.public-header'); ?>

  <main id="main-content">
    <!-- About Hero -->
    <section style="background: var(--tcm-bg); border-bottom: 1px solid var(--tcm-border); padding: clamp(48px, 6vw, 76px) 0 clamp(32px, 4vw, 52px);">
      <div class="tcm-container">
        <div style="max-width: 820px;">
          <div class="tcm-eyebrow">OUR MISSION &amp; ORIGIN</div>
          <h1 class="tcm-h1" style="margin: 8px 0 18px 0; font-size: clamp(2.4rem, 4.8vw, 3.8rem); line-height: 1.12;">
            Practical Education for Modern <span class="font-editorial-serif">Engineering Careers.</span>
          </h1>
          <p class="tcm-lead" style="font-size: 17.5px; line-height: 1.65; color: var(--tcm-muted); margin: 0 0 28px;">
            TCM One (The Code Munk) was founded to correct a fundamental flaw in technical education: students spend years memorizing slide decks and pseudo-code, yet graduate without knowing how to deploy a database, write production APIs, or navigate an industry Git repository.
          </p>
          <div style="display: flex; gap: 12px; flex-wrap: wrap;">
            <a href="<?= base_url('/courses') ?>" class="tcm-btn tcm-btn-primary tcm-btn-sm">
              <span>View Course Catalog</span>
              <svg class="arrow-icon" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M1 7H13M13 7L7 1M13 7L7 13" stroke="currentColor" stroke-width="1.5" stroke-linecap="square"/></svg>
            </a>
            <a href="<?= base_url('/contact') ?>" class="tcm-btn tcm-btn-secondary tcm-btn-sm">
              <span>Contact Admissions</span>
              <svg class="arrow-icon" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M1 7H13M13 7L7 1M13 7L7 13" stroke="currentColor" stroke-width="1.5" stroke-linecap="square"/></svg>
            </a>
          </div>
        </div>
      </div>
    </section>

    <!-- The Narrative & Proof Points -->
    <section style="padding: clamp(48px, 6vw, 72px) 0; border-bottom: 1px solid var(--tcm-border);">
      <div class="tcm-container">
        <div class="about-story-grid">
          
          <!-- Left: Narrative -->
          <div>
            <div class="tcm-eyebrow">THE FOUNDING CONVICTION</div>
            <h2 class="tcm-h2" style="margin: 8px 0 20px 0;">
              We Don't Sell Miracles. <span class="font-editorial-serif">We Teach Craft.</span>
            </h2>
            <div style="font-size: 15px; line-height: 1.75; color: var(--tcm-fg); display: flex; flex-direction: column; gap: 18px;">
              <p>
                The tech education ecosystem is flooded with predatory claims: 100% placement guarantees, exaggerated salary packages, and massive locked-in loan agreements. Students are pushed into high-pressure debt before writing a single line of working code.
              </p>
              <p>
                At TCM One, we rejected that model entirely. We believe high-grade technical education should be honest, accessible, and grounded in the daily realities of software engineering.
              </p>
              <p>
                When you enroll in a TCM One cohort, you don't watch pre-recorded monologues or answer multiple-choice quizzes. You clone GitHub repositories, design database schemas, handle live exceptions, write clean REST APIs, and debug your code alongside experienced mentors who treat you like a junior engineer on their team.
              </p>
              <p>
                Our accessible ₹99 starter workshops give students the chance to experience rigorous hands-on coding before committing to semester-long programs. No high-pressure sales calls. No fake statistics. Just craft.
              </p>
            </div>
          </div>

          <!-- Right: Key Numbers & Credibility -->
          <div>
            <div style="background: #FFFFFF; border: 1px solid var(--tcm-border); padding: 32px; display: flex; flex-direction: column; gap: 24px;">
              
              <div style="border-bottom: 1px solid var(--tcm-border); padding-bottom: 20px;">
                <div class="font-editorial-serif" style="font-size: 48px; line-height: 1; color: var(--tcm-fg); margin-bottom: 6px;">
                  52+
                </div>
                <div style="font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.06em; color: var(--tcm-fg); margin-bottom: 4px;">
                  Published Courses
                </div>
                <div style="font-size: 13px; color: var(--tcm-muted); line-height: 1.5;">
                  Covering Full Stack Web, Python, Data Science, AI, Cloud DevOps, and Core CS.
                </div>
              </div>

              <div style="border-bottom: 1px solid var(--tcm-border); padding-bottom: 20px;">
                <div class="font-editorial-serif" style="font-size: 48px; line-height: 1; color: var(--tcm-fg); margin-bottom: 6px;">
                  100%
                </div>
                <div style="font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.06em; color: var(--tcm-fg); margin-bottom: 4px;">
                  Live Mentored Sessions
                </div>
                <div style="font-size: 13px; color: var(--tcm-muted); line-height: 1.5;">
                  Interactive cohorts with screen sharing, live code debugging, and direct Q&amp;A.
                </div>
              </div>

              <div>
                <div class="font-editorial-serif" style="font-size: 48px; line-height: 1; color: var(--tcm-fg); margin-bottom: 6px;">
                  ₹99
                </div>
                <div style="font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.06em; color: var(--tcm-fg); margin-bottom: 4px;">
                  Accessible Entry Price
                </div>
                <div style="font-size: 13px; color: var(--tcm-muted); line-height: 1.5;">
                  Democratizing software engineering foundations for every ambitious student.
                </div>
              </div>

            </div>
          </div>

        </div>
      </div>
    </section>

    <!-- Core Pillars / Principles -->
    <section style="padding: clamp(48px, 6vw, 72px) 0; background: var(--tcm-surface); border-bottom: 1px solid var(--tcm-border);">
      <div class="tcm-container">
        <div style="margin-bottom: 40px; max-width: 680px;">
          <div class="tcm-eyebrow">THE TCM STANDARD</div>
          <h2 class="tcm-h2" style="margin: 8px 0 12px 0;">
            Four Principles We Never <span class="font-editorial-serif">Compromise</span>
          </h2>
          <p class="tcm-body-muted" style="margin: 0; font-size: 15px; line-height: 1.6;">
            Our academic standards are modeled after engineering apprenticeships, prioritizing working codebases and demonstrable problem-solving.
          </p>
        </div>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 24px;">
          <div class="about-pillar-box">
            <div style="display:inline-block; font-size:11px; font-weight:700; letter-spacing:0.06em; text-transform:uppercase; color:#111; background:#f5f5f5; border:1px solid #111; padding:2px 8px; margin-bottom:14px;">PRINCIPLE 01</div>
            <h3 style="font-size: 18px; font-weight: 700; margin-bottom: 10px; color: var(--tcm-fg);">Code Over Slide Decks</h3>
            <p style="font-size: 13.5px; color: var(--tcm-muted); line-height: 1.65; margin: 0;">
              Slides are for executive summaries. Real software is learned in terminal prompts, code editors, and browser developer consoles. 90% of cohort time is spent actively writing code.
            </p>
          </div>

          <div class="about-pillar-box">
            <div style="display:inline-block; font-size:11px; font-weight:700; letter-spacing:0.06em; text-transform:uppercase; color:#111; background:#f5f5f5; border:1px solid #111; padding:2px 8px; margin-bottom:14px;">PRINCIPLE 02</div>
            <h3 style="font-size: 18px; font-weight: 700; margin-bottom: 10px; color: var(--tcm-fg);">Radical Transparency</h3>
            <p style="font-size: 13.5px; color: var(--tcm-muted); line-height: 1.65; margin: 0;">
              Every course syllabus is published down to lesson-level topics. No gatekept pricing, no hidden EMI penalties, and zero manipulated placement numbers.
            </p>
          </div>

          <div class="about-pillar-box">
            <div style="display:inline-block; font-size:11px; font-weight:700; letter-spacing:0.06em; text-transform:uppercase; color:#111; background:#f5f5f5; border:1px solid #111; padding:2px 8px; margin-bottom:14px;">PRINCIPLE 03</div>
            <h3 style="font-size: 18px; font-weight: 700; margin-bottom: 10px; color: var(--tcm-fg);">Verifiable Portfolio Proof</h3>
            <p style="font-size: 13.5px; color: var(--tcm-muted); line-height: 1.65; margin: 0;">
              Recruiters don't hire attendance certificates; they hire engineers who can demonstrate working deployments and clean Git histories. Our capstones are built to stand out in technical interviews.
            </p>
          </div>

          <div class="about-pillar-box">
            <div style="display:inline-block; font-size:11px; font-weight:700; letter-spacing:0.06em; text-transform:uppercase; color:#111; background:#f5f5f5; border:1px solid #111; padding:2px 8px; margin-bottom:14px;">PRINCIPLE 04</div>
            <h3 style="font-size: 18px; font-weight: 700; margin-bottom: 10px; color: var(--tcm-fg);">Lifetime Alumni Community</h3>
            <p style="font-size: 13.5px; color: var(--tcm-muted); line-height: 1.65; margin: 0;">
              Learning doesn't end when the cohort closes. Students retain permanent access to recorded video sessions, documentation updates, and dedicated peer Discord/WhatsApp developer channels.
            </p>
          </div>
        </div>
      </div>
    </section>

    <!-- Operational Presence & Locations -->
    <section style="padding: clamp(48px, 6vw, 72px) 0;">
      <div class="tcm-container">
        <div style="background: #111111; color: #FFFFFF; padding: clamp(32px, 5vw, 56px) clamp(24px, 4vw, 48px); border: 1px solid #111111;">
          <div style="max-width: 820px;">
            <div class="tcm-eyebrow" style="color: rgba(255,255,255,0.7); margin-bottom: 8px;">LOCATIONS &amp; REACH</div>
            <h2 class="tcm-h2" style="color: #FFFFFF; font-size: clamp(28px, 4vw, 40px); margin-bottom: 16px;">
              Built in India. Teaching Learners <span class="font-editorial-serif">Everywhere.</span>
            </h2>
            <p style="font-size: 15px; color: rgba(255,255,255,0.8); line-height: 1.7; margin-bottom: 36px;">
              TCM One operates its flagship learning campus in Bilaspur (Chhattisgarh). Our live engineering infrastructure allows students from colleges across India and worldwide to learn directly from practicing engineers without relocating.
            </p>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 24px; border-top: 1px solid rgba(255,255,255,0.2); padding-top: 24px;">
              <div>
                <strong style="display: block; color: #FFFFFF; font-size: 15px; margin-bottom: 6px;">Bilaspur Learning Campus</strong>
                <span style="font-size: 13px; color: rgba(255,255,255,0.7); line-height: 1.5; display: block;">Seepat Rd, near DLS college, Shree Vihar, Ashok Nagar, Sarkanda, Bilaspur, Chhattisgarh 495006.</span>
              </div>
              <div>
                <strong style="display: block; color: #FFFFFF; font-size: 15px; margin-bottom: 6px;">Online Live Cohorts</strong>
                <span style="font-size: 13px; color: rgba(255,255,255,0.7); line-height: 1.5; display: block;">100% accessible via any computer with a modern browser and internet.</span>
              </div>
              <div>
                <strong style="display: block; color: #FFFFFF; font-size: 15px; margin-bottom: 6px;">Placement &amp; Industry Links</strong>
                <span style="font-size: 13px; color: rgba(255,255,255,0.7); line-height: 1.5; display: block;">Active hiring networks, portfolio reviews, and technical project review sessions.</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Bottom CTA Banner -->
    <section style="padding: clamp(40px, 5vw, 64px) 0; background: var(--tcm-surface); border-top: 1px solid var(--tcm-border); text-align: center;">
      <div class="tcm-container" style="max-width: 680px;">
        <div class="tcm-eyebrow">TAKE THE NEXT STEP</div>
        <h2 class="tcm-h2" style="margin: 8px 0 14px 0;">
          Start Building Production <span class="font-editorial-serif">Software</span>
        </h2>
        <p class="tcm-body-muted" style="margin: 0 0 24px; font-size: 15px; line-height: 1.6;">
          Explore practical engineering tracks or speak directly with our team to find the cohort that matches your career goals.
        </p>
        <div style="display: flex; gap: 12px; justify-content: center; flex-wrap: wrap;">
          <a href="<?= base_url('/courses') ?>" class="tcm-btn tcm-btn-primary tcm-btn-sm">
            <span>Explore All Courses</span>
            <svg class="arrow-icon" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M1 7H13M13 7L7 1M13 7L7 13" stroke="currentColor" stroke-width="1.5" stroke-linecap="square"/></svg>
          </a>
          <a href="<?= base_url('/contact') ?>" class="tcm-btn tcm-btn-secondary tcm-btn-sm">
            <span>Speak With Admissions</span>
            <svg class="arrow-icon" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M1 7H13M13 7L7 1M13 7L7 13" stroke="currentColor" stroke-width="1.5" stroke-linecap="square"/></svg>
          </a>
        </div>
      </div>
    </section>
  <?php readfile(BASE_PATH . '/views/partials/business-identity.html'); ?>
</main>

  <!-- Site Footer -->
  <?php \TCM\Core\View::partial('partials.public-footer'); ?>

  <!-- Scripts -->
  <script src="<?= base_url('/assets/tcm-editorial.js?v=2.18') ?>"></script>
  <script src="<?= base_url('/assets/cai-assistant.js?v=2.18') ?>"></script>
</body>
</html>
