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

  <meta name="app-base" content="<?= e(rtrim((string) config('app.url', ''), '/')) ?>">

  <title><?= e($title ?? 'About TCM One — Practical Education For Modern Careers') ?></title>
  <meta name="description" content="<?= e($description ?? 'Discover the mission, teaching philosophy, and verified learning approach behind TCM One.') ?>">
  <meta name="robots" content="index, follow">
  <link rel="canonical" href="<?= e($canonical ?? 'https://thecodemunk.in/about') ?>">

  <!-- Open Graph -->
  <meta property="og:type" content="website">
  <meta property="og:site_name" content="TCM One">
  <meta property="og:url" content="<?= e($canonical) ?>">
  <meta property="og:title" content="About TCM One &mdash; Practical Education For Modern Careers">
  <meta property="og:description" content="<?= e($description) ?>">
  <meta property="og:image" content="<?= e($ogImage) ?>">
  <meta property="og:image:width" content="1200">
  <meta property="og:image:height" content="630">

  <!-- Typography & Icons -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Instrument+Serif:ital@0;1&family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

  <!-- Design System Stylesheets -->
  <link rel="stylesheet" href="<?= base_url('/assets/tcm-design-system.css') ?>">
  <link rel="stylesheet" href="<?= base_url('/assets/tcm-editorial.css') ?>">
  <link rel="stylesheet" href="<?= base_url('/assets/cai-assistant.css') ?>">

  <style>
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
      background: var(--tcm-surface);
      border: 1px solid var(--tcm-border);
      padding: 32px;
      transition: border-color 0.15s ease;
    }
    .about-pillar-box:hover {
      border-color: var(--tcm-fg);
    }
  </style>
  <link rel="stylesheet" href="<?= base_url('/assets/tcm-footer.css?v=1') ?>">
</head>
<body>

  <!-- Site Announcement -->
  <div class="tcm-announcement" role="region" aria-label="Announcement">
    <div class="tcm-container tcm-announcement-content">
      <span><strong>The TCM Philosophy:</strong> Learn by writing production software, not memorizing slides.</span>
      <a href="<?= base_url('/courses') ?>">Explore Live Tracks &rarr;</a>
    </div>
  </div>

  <!-- Header Navigation -->
  <header class="tcm-header" role="banner">
    <div class="tcm-container">
      <div class="tcm-header-inner">
        <a href="<?= base_url('/') ?>" class="tcm-brand">
          <img src="<?= base_url('/assets/logo.png') ?>" alt="TCM One Logo" class="tcm-brand-logo">
          <span class="tcm-brand-text">TCM<span class="brand-accent">ONE</span></span>
        </a>

        <nav class="tcm-nav" aria-label="Main Navigation">
          <a href="<?= base_url('/courses') ?>" class="tcm-nav-link">Courses</a>
          <a href="<?= base_url('/workshops') ?>" class="tcm-nav-link">Workshops</a>
          <a href="<?= base_url('/for-institutions') ?>" class="tcm-nav-link">For Institutions</a>
          <a href="<?= base_url('/about') ?>" class="tcm-nav-link active">About</a>
          <a href="<?= base_url('/contact') ?>" class="tcm-nav-link">Contact</a>
        </nav>

        <div class="tcm-header-actions">
          <a href="<?= base_url('/login.php') ?>" class="tcm-btn tcm-btn-outline tcm-btn-sm tcm-auth-link d-none d-sm-inline-flex">Sign In</a>
          <a href="<?= base_url('/courses') ?>" class="tcm-btn tcm-btn-primary tcm-btn-sm">
            <span>Explore Courses</span>
            <svg class="arrow-icon" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M1 7H13M13 7L7 1M13 7L7 13" stroke="currentColor" stroke-width="1.5" stroke-linecap="square"/></svg>
          </a>
          <button type="button" class="tcm-menu-toggle" aria-label="Toggle Navigation">
            <i class="bi bi-list" style="font-size: 22px;"></i>
          </button>
        </div>
      </div>
    </div>
  </header>

  <!-- Mobile Drawer Backdrop & Drawer -->
  <div class="tcm-drawer-backdrop" aria-hidden="true"></div>
  <aside class="tcm-mobile-drawer" aria-label="Mobile Menu">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 32px;">
      <a href="<?= base_url('/') ?>" class="tcm-brand">
        <span class="tcm-brand-text">TCM<span class="brand-accent">ONE</span></span>
      </a>
      <button type="button" class="tcm-drawer-close" aria-label="Close Menu">&times;</button>
    </div>
    <nav style="display: flex; flex-direction: column; gap: 18px;">
      <a href="<?= base_url('/courses') ?>" class="tcm-nav-link" style="font-size: 16px;">Courses</a>
      <a href="<?= base_url('/workshops') ?>" class="tcm-nav-link" style="font-size: 16px;">Workshops</a>
      <a href="<?= base_url('/for-institutions') ?>" class="tcm-nav-link" style="font-size: 16px;">For Institutions</a>
      <a href="<?= base_url('/about') ?>" class="tcm-nav-link active" style="font-size: 16px;">About Us</a>
      <a href="<?= base_url('/contact') ?>" class="tcm-nav-link" style="font-size: 16px;">Contact</a>
    </nav>
    <div style="margin-top: 36px; padding-top: 24px; border-top: 1px solid var(--tcm-border); display: flex; flex-direction: column; gap: 12px;">
      <a href="<?= base_url('/login.php') ?>" class="tcm-btn tcm-btn-secondary" style="width: 100%;">Sign In</a>
      <a href="<?= base_url('/courses') ?>" class="tcm-btn tcm-btn-primary" style="width: 100%;">Explore Courses</a>
    </div>
  </aside>

  <main>
    <!-- About Hero -->
    <section style="background: var(--tcm-surface); border-bottom: 1px solid var(--tcm-border); padding: clamp(36px, 4.5vw, 52px) 0 clamp(24px, 3vw, 36px);">
      <div class="tcm-container">
        <div style="max-width: 820px;">
          <div class="tcm-eyebrow" style="margin-bottom: 12px;">OUR MISSION &amp; ORIGIN</div>
          <h1 class="tcm-h1" style="font-size: clamp(34px, 5vw, 56px); line-height: 1.12; margin-bottom: 22px;">
            Practical Education for Modern Engineering Careers.
          </h1>
          <p class="tcm-lead" style="font-size: 19px; line-height: 1.6; color: var(--tcm-muted); margin: 0 0 32px;">
            TCM One (The Code Munk) was founded to correct a fundamental flaw in Indian tech education: students spend four years memorizing slide decks and pseudo-code, yet graduate without knowing how to deploy a database, write an API, or navigate a production Git repository.
          </p>
          <div style="display: flex; gap: 12px; flex-wrap: wrap;">
            <a href="<?= base_url('/courses') ?>" class="tcm-btn tcm-btn-primary">
              <span>View Course Catalog</span>
              <svg class="arrow-icon" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M1 7H13M13 7L7 1M13 7L7 13" stroke="currentColor" stroke-width="1.5" stroke-linecap="square"/></svg>
            </a>
            <a href="<?= base_url('/contact') ?>" class="tcm-btn tcm-btn-secondary">
              Contact Team
            </a>
          </div>
        </div>
      </div>
    </section>

    <!-- The Narrative & Proof Points -->
    <section style="padding: clamp(40px, 4.5vw, 56px) 0; border-bottom: 1px solid var(--tcm-border);">
      <div class="tcm-container">
        <div class="about-story-grid">
          
          <!-- Left: Narrative -->
          <div>
            <div class="tcm-eyebrow" style="margin-bottom: 12px;">THE FOUNDING CONVICTION</div>
            <h2 class="tcm-h2" style="font-size: 30px; margin-bottom: 20px;">
              We Don't Sell Miracles. We Teach Craft.
            </h2>
            <div style="font-size: 15.5px; line-height: 1.75; color: var(--tcm-fg); display: flex; flex-direction: column; gap: 20px;">
              <p>
                The Indian EdTech ecosystem is filled with predatory claims: 100% placement guarantees, exaggerated CTC numbers, and massive locked-in loan agreements. Students are pushed into high-pressure debt before writing a single line of working code.
              </p>
              <p>
                At TCM One, we rejected that model entirely. We believe high-grade technical education should be honest, accessible, and grounded in the daily realities of software development.
              </p>
              <p>
                When you enroll in a TCM One cohort, you don't watch pre-recorded monologues or answer multiple-choice quizzes. You clone GitHub repositories, design database schemas, handle live exceptions, write clean REST APIs, and debug your code in front of an experienced mentor who treats you like a junior engineer on their team.
              </p>
              <p>
                Our accessible ₹99 starter workshops give students the chance to experience rigorous hands-on coding before committing to deeper semester-long programs. No high-pressure sales calls. No fake statistics. Just craft.
              </p>
            </div>
          </div>

          <!-- Right: Key Numbers & Credibility -->
          <div>
            <div style="background: var(--tcm-surface); border: 1px solid var(--tcm-border); padding: 32px; display: flex; flex-direction: column; gap: 24px;">
              
              <div style="border-bottom: 1px solid var(--tcm-border); padding-bottom: 20px;">
                <div style="font-family: 'Instrument Serif', Georgia, serif; font-size: 44px; font-weight: 700; line-height: 1; margin-bottom: 4px;">
                  31+
                </div>
                <div style="font-size: 13px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 4px;">
                  Published Courses
                </div>
                <div style="font-size: 12px; color: var(--tcm-muted);">
                  Covering Full Stack Web, Python, Data Science, AI, Cloud DevOps, and Core CS.
                </div>
              </div>

              <div style="border-bottom: 1px solid var(--tcm-border); padding-bottom: 20px;">
                <div style="font-family: 'Instrument Serif', Georgia, serif; font-size: 44px; font-weight: 700; line-height: 1; margin-bottom: 4px;">
                  100%
                </div>
                <div style="font-size: 13px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 4px;">
                  Live Mentored Sessions
                </div>
                <div style="font-size: 12px; color: var(--tcm-muted);">
                  Interactive cohorts with screen sharing, live code debugging, and Q&amp;A.
                </div>
              </div>

              <div>
                <div style="font-family: 'Instrument Serif', Georgia, serif; font-size: 44px; font-weight: 700; line-height: 1; margin-bottom: 4px;">
                  ₹99
                </div>
                <div style="font-size: 13px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 4px;">
                  Accessible Entry Price
                </div>
                <div style="font-size: 12px; color: var(--tcm-muted);">
                  Democratizing software engineering foundations for every ambitious student.
                </div>
              </div>

            </div>
          </div>

        </div>
      </div>
    </section>

    <!-- Core Pillars / Principles -->
    <section style="padding: clamp(40px, 4.5vw, 56px) 0; background: var(--tcm-surface); border-bottom: 1px solid var(--tcm-border);">
      <div class="tcm-container">
        <div style="margin-bottom: 44px; max-width: 680px;">
          <div class="tcm-eyebrow" style="margin-bottom: 8px;">THE TCM STANDARD</div>
          <h2 class="tcm-h2">Four Principles We Never Compromise</h2>
        </div>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 24px;">
          <div class="about-pillar-box">
            <span class="tcm-badge" style="margin-bottom: 16px;">PRINCIPLE 01</span>
            <h3 style="font-size: 18px; font-weight: 700; margin-bottom: 10px;">Code Over Slide Decks</h3>
            <p style="font-size: 13.5px; color: var(--tcm-muted); line-height: 1.6; margin: 0;">
              Slides are for executive summaries. Real software is learned in terminal prompts, code editors, and browser developer consoles. 90% of cohort time is spent actively writing code.
            </p>
          </div>

          <div class="about-pillar-box">
            <span class="tcm-badge" style="margin-bottom: 16px;">PRINCIPLE 02</span>
            <h3 style="font-size: 18px; font-weight: 700; margin-bottom: 10px;">Radical Transparency</h3>
            <p style="font-size: 13.5px; color: var(--tcm-muted); line-height: 1.6; margin: 0;">
              Every course syllabus is published down to lesson-level topics. No gatekept pricing, no hidden EMI penalties, and zero manipulated placement numbers.
            </p>
          </div>

          <div class="about-pillar-box">
            <span class="tcm-badge" style="margin-bottom: 16px;">PRINCIPLE 03</span>
            <h3 style="font-size: 18px; font-weight: 700; margin-bottom: 10px;">Verifiable Portfolio Proof</h3>
            <p style="font-size: 13.5px; color: var(--tcm-muted); line-height: 1.6; margin: 0;">
              Recruiters don't hire attendance certificates; they hire engineers who can show working deployments and clean Git histories. Our capstones are built to stand out in technical interviews.
            </p>
          </div>

          <div class="about-pillar-box">
            <span class="tcm-badge" style="margin-bottom: 16px;">PRINCIPLE 04</span>
            <h3 style="font-size: 18px; font-weight: 700; margin-bottom: 10px;">Lifetime Alumni Community</h3>
            <p style="font-size: 13.5px; color: var(--tcm-muted); line-height: 1.6; margin: 0;">
              Learning doesn't end when the cohort closes. Students retain permanent access to recorded video sessions, documentation updates, and dedicated peer Discord/WhatsApp developer channels.
            </p>
          </div>
        </div>
      </div>
    </section>

    <!-- Operational Presence & Locations -->
    <section style="padding: clamp(40px, 4.5vw, 56px) 0;">
      <div class="tcm-container">
        <div style="background: #111111; color: #F8F7F4; padding: 48px 40px; border: 1px solid #111111;">
          <div style="max-width: 780px;">
            <div class="tcm-eyebrow" style="color: #999; margin-bottom: 8px;">LOCATIONS &amp; REACH</div>
            <h2 class="tcm-h2" style="color: #FFF; font-family: 'Instrument Serif', Georgia, serif; font-size: 34px; margin-bottom: 16px;">
              Built in India. Teaching Learners Everywhere.
            </h2>
            <p style="font-size: 15px; color: #CCC; line-height: 1.7; margin-bottom: 32px;">
              TCM One operates hub teams in Bengaluru (Karnataka) and Bhubaneswar (Odisha). Our live infrastructure allows students from tier-1, tier-2, and tier-3 colleges across India to learn directly from practicing engineers without relocating.
            </p>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 24px; border-top: 1px solid rgba(255,255,255,0.15); padding-top: 24px;">
              <div>
                <strong style="display: block; color: #FFF; font-size: 15px; margin-bottom: 4px;">Bengaluru Tech Hub</strong>
                <span style="font-size: 13px; color: #AAA;">Curriculum development, mentor networks &amp; industry hiring linkages.</span>
              </div>
              <div>
                <strong style="display: block; color: #FFF; font-size: 15px; margin-bottom: 4px;">Bhubaneswar Academic Hub</strong>
                <span style="font-size: 13px; color: #AAA;">Student support, institutional partnerships &amp; live cohort operations.</span>
              </div>
              <div>
                <strong style="display: block; color: #FFF; font-size: 15px; margin-bottom: 4px;">Online Live Cohorts</strong>
                <span style="font-size: 13px; color: #AAA;">100% accessible via any computer with a modern browser and internet.</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

  </main>

  <!-- Site Footer -->
  <?php \TCM\Core\View::partial('partials.public-footer'); ?>

  <!-- Scripts -->
  <script src="<?= base_url('/assets/tcm-editorial.js') ?>"></script>
  <script src="<?= base_url('/assets/cai-assistant.js') ?>"></script>
</body>
</html>
