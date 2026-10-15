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

  <title><?= e($title ?? ($workshop['title'] . ' | TCM One')) ?></title>
  <meta name="description" content="<?= e($desc ?? ($workshop['tagline'] ?: $workshop['description'] ?: '')) ?>">
  <meta name="robots" content="index, follow">
  <link rel="canonical" href="<?= e($canonical ?? ('https://thecodemunk.in/workshops/' . rawurlencode((string)$workshop['slug']))) ?>">

  <!-- Open Graph / Social -->
  <meta property="og:type" content="website">
  <meta property="og:site_name" content="TCM One">
  <meta property="og:url" content="<?= e($canonical) ?>">
  <meta property="og:title" content="<?= e($workshop['title']) ?> &mdash; TCM One">
  <meta property="og:description" content="<?= e($desc ?? $workshop['description']) ?>">
  <meta property="og:image" content="<?= e($ogImage) ?>">
  <meta property="og:image:width" content="1200">
  <meta property="og:image:height" content="630">

  <!-- Twitter -->
  <meta name="twitter:card" content="summary_large_image">
  <meta name="twitter:url" content="<?= e($canonical) ?>">
  <meta name="twitter:title" content="<?= e($workshop['title']) ?>">
  <meta name="twitter:description" content="<?= e($desc ?? $workshop['description']) ?>">
  <meta name="twitter:image" content="<?= e($ogImage) ?>">

  <!-- Typography & Icons -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Instrument+Serif:ital@0;1&family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

  <!-- Design System Stylesheets -->
  <link rel="stylesheet" href="<?= base_url('/assets/tcm-design-system.css') ?>">
  <link rel="stylesheet" href="<?= base_url('/assets/tcm-editorial.css') ?>">
  <link rel="stylesheet" href="<?= base_url('/assets/cai-assistant.css') ?>">

  <?php
    $price = (float)($workshop['price'] ?? 0);
    $origPrice = !empty($workshop['original_price']) ? (float)$workshop['original_price'] : 0;
    $duration = $workshop['duration'] ?? 'Live Cohort';
    $schedule = $workshop['schedule'] ?? 'Evening live sessions';
    $startsAt = $workshop['starts_at'] ?? 'Next Batch Coming Soon';
    $mode = $workshop['mode'] ?? 'Live Online Cohort';
    $category = $workshop['category'] ?? 'Workshop';
    $badge = $workshop['badge'] ?? 'Live Intensive';
    // Strictly gate EMI: Price must be >= 5000. For workshops (₹99/₹999), showEmi is false.
    $showEmi = ($price >= 5000);

    $webPageSchema = [
      '@context' => 'https://schema.org',
      '@type' => 'WebPage',
      'name' => $workshop['title'],
      'description' => $desc ?? ($workshop['description'] ?? ''),
      'url' => $canonical,
      'publisher' => [
        '@type' => 'Organization',
        'name' => 'TCM One',
        'url' => $appUrl,
        'logo' => $appUrl . '/assets/logo.png',
      ],
    ];
    $eventSchema = [
      '@context' => 'https://schema.org',
      '@type' => 'EducationEvent',
      'name' => $workshop['title'],
      'description' => $desc ?? ($workshop['description'] ?? ''),
      'url' => $canonical,
      'image' => $ogImage,
      'eventAttendanceMode' => 'https://schema.org/OnlineEventAttendanceMode',
      'eventStatus' => 'https://schema.org/EventScheduled',
      'organizer' => [
        '@type' => 'EducationalOrganization',
        'name' => 'TCM One',
        'url' => $appUrl,
      ],
      'offers' => [
        '@type' => 'Offer',
        'price' => (string) $price,
        'priceCurrency' => 'INR',
        'availability' => 'https://schema.org/InStock',
        'url' => $canonical,
      ],
    ];
    $breadcrumbSchema = [
      '@context' => 'https://schema.org',
      '@type' => 'BreadcrumbList',
      'itemListElement' => [
        ['@type' => 'ListItem', 'position' => 1, 'name' => 'Home', 'item' => $appUrl . '/'],
        ['@type' => 'ListItem', 'position' => 2, 'name' => 'Workshops', 'item' => $appUrl . '/workshops'],
        ['@type' => 'ListItem', 'position' => 3, 'name' => $workshop['title'], 'item' => $canonical],
      ],
    ];
  ?>
  <script type="application/ld+json"><?= json_encode($webPageSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) ?></script>
  <script type="application/ld+json"><?= json_encode($eventSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) ?></script>
  <script type="application/ld+json"><?= json_encode($breadcrumbSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) ?></script>
  <script>
    gtag('event', 'workshop_view', {
      workshop_slug: <?= json_encode((string) $workshop['slug']) ?>,
      workshop_title: <?= json_encode((string) $workshop['title']) ?>,
      value: <?= json_encode($price) ?>,
      currency: 'INR'
    });
  </script>

  <style>
    .ws-layout-grid {
      display: grid;
      grid-template-columns: 1fr;
      gap: 40px;
    }
    @media (min-width: 992px) {
      .ws-layout-grid {
        grid-template-columns: 1fr 380px;
        gap: 48px;
      }
    }
    .ws-sticky-panel {
      position: sticky;
      top: 96px;
      background: var(--tcm-surface);
      border: 1px solid var(--tcm-border);
      padding: 28px;
    }
    .ws-curriculum-day {
      border: 1px solid var(--tcm-border);
      background: var(--tcm-surface);
      margin-bottom: 16px;
      transition: border-color 0.15s ease;
    }
    .ws-curriculum-day:hover {
      border-color: var(--tcm-fg);
    }
    .ws-day-header {
      padding: 16px 20px;
      background: var(--tcm-surface);
      display: flex;
      align-items: center;
      gap: 14px;
      border-bottom: 1px solid var(--tcm-border);
    }
    .ws-day-pill {
      font-size: 11px;
      font-weight: 700;
      letter-spacing: 0.08em;
      text-transform: uppercase;
      background: var(--tcm-fg);
      color: var(--tcm-bg);
      padding: 3px 8px;
    }
    .ws-day-title {
      font-size: 15px;
      font-weight: 700;
      color: var(--tcm-fg);
      line-height: 1.4;
    }
    .ws-day-body {
      padding: 16px 20px 18px;
      background: var(--tcm-bg);
    }
    .ws-topic-list {
      list-style: none;
      padding: 0;
      margin: 0;
      display: flex;
      flex-direction: column;
      gap: 8px;
    }
    .ws-topic-list li {
      font-size: 13.5px;
      color: var(--tcm-muted);
      line-height: 1.55;
      position: relative;
      padding-left: 18px;
    }
    .ws-topic-list li::before {
      content: "—";
      position: absolute;
      left: 0;
      color: var(--tcm-fg);
      font-weight: 600;
    }
    .ws-floating-mobile-bar {
      position: fixed;
      bottom: 0;
      left: 0;
      right: 0;
      background: #FFFFFF;
      border-top: 1px solid var(--tcm-border);
      padding: 12px 20px;
      display: flex;
      align-items: center;
      justify-content: space-between;
      z-index: 900;
    }
    @media (min-width: 992px) {
      .ws-floating-mobile-bar { display: none !important; }
    }
  </style>
  <link rel="stylesheet" href="<?= base_url('/assets/tcm-footer.css?v=1') ?>">
</head>
<body>

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
          <a href="<?= base_url('/workshops') ?>" class="tcm-nav-link active">Workshops</a>
          <a href="<?= base_url('/for-institutions') ?>" class="tcm-nav-link">For Institutions</a>
          <a href="<?= base_url('/about') ?>" class="tcm-nav-link">About</a>
          <a href="<?= base_url('/contact') ?>" class="tcm-nav-link">Contact</a>
        </nav>

        <div class="tcm-header-actions">
          <a href="<?= base_url('/login.php') ?>" class="tcm-btn tcm-btn-outline tcm-btn-sm tcm-auth-link d-none d-sm-inline-flex">Sign In</a>
          <button type="button" class="tcm-btn tcm-btn-primary tcm-btn-sm tcm-trigger-workshop" data-slug="<?= e($workshop['slug']) ?>" data-title="<?= e($workshop['title']) ?>" data-price="<?= $price ?>">
            <span>Reserve Seat</span>
            <svg class="arrow-icon" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M1 7H13M13 7L7 1M13 7L7 13" stroke="currentColor" stroke-width="1.5" stroke-linecap="square"/></svg>
          </button>
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
      <a href="<?= base_url('/workshops') ?>" class="tcm-nav-link active" style="font-size: 16px;">Workshops</a>
      <a href="<?= base_url('/for-institutions') ?>" class="tcm-nav-link" style="font-size: 16px;">For Institutions</a>
      <a href="<?= base_url('/about') ?>" class="tcm-nav-link" style="font-size: 16px;">About Us</a>
      <a href="<?= base_url('/contact') ?>" class="tcm-nav-link" style="font-size: 16px;">Contact</a>
    </nav>
    <div style="margin-top: 36px; padding-top: 24px; border-top: 1px solid var(--tcm-border); display: flex; flex-direction: column; gap: 12px;">
      <a href="<?= base_url('/login.php') ?>" class="tcm-btn tcm-btn-secondary" style="width: 100%;">Sign In</a>
      <button type="button" class="tcm-btn tcm-btn-primary tcm-trigger-workshop" data-slug="<?= e($workshop['slug']) ?>" data-title="<?= e($workshop['title']) ?>" data-price="<?= $price ?>" style="width: 100%;">Reserve Seat</button>
    </div>
  </aside>

  <main style="padding-bottom: 84px;">
    <!-- Workshop Detail Hero -->
    <section style="background: var(--tcm-surface); border-bottom: 1px solid var(--tcm-border); padding: clamp(36px, 4.5vw, 48px) 0 clamp(24px, 3vw, 32px); position: relative; overflow: hidden;">
      <div class="tcm-container">
        
        <!-- Breadcrumbs -->
        <div style="font-size: 13px; color: var(--tcm-muted); margin-bottom: 20px; display: flex; gap: 8px; align-items: center; flex-wrap: wrap;">
          <a href="<?= base_url('/') ?>" style="color: var(--tcm-muted); text-decoration: none;">Home</a>
          <span>/</span>
          <a href="<?= base_url('/workshops') ?>" style="color: var(--tcm-muted); text-decoration: none;">Workshops</a>
          <span>/</span>
          <span style="color: var(--tcm-fg); font-weight: 500;"><?= e($workshop['short_title'] ?? $workshop['title']) ?></span>
        </div>

        <div style="max-width: 860px;">
          <div style="display: flex; gap: 8px; margin-bottom: 18px; flex-wrap: wrap; align-items: center;">
            <span class="tcm-badge"><?= e($category) ?></span>
            <span class="tcm-badge tcm-badge-dark"><?= e($badge) ?></span>
          </div>

          <h1 class="tcm-h1" style="font-size: clamp(32px, 4.5vw, 48px); line-height: 1.15; margin-bottom: 18px;">
            <?= e($workshop['title']) ?>
          </h1>

          <p class="tcm-lead" style="font-size: 18px; line-height: 1.6; margin-bottom: 28px; max-width: 760px;">
            <?= e($workshop['full_description'] ?? $workshop['tagline'] ?? $workshop['description']) ?>
          </p>

          <!-- Stat Chips Grid -->
          <div style="display: flex; gap: 12px; flex-wrap: wrap; margin-bottom: 12px;">
            <div style="padding: 8px 14px; border: 1px solid var(--tcm-border); background: var(--tcm-bg); font-size: 13px; font-weight: 600;">
              <i class="bi bi-clock" style="margin-right: 6px;"></i> <?= e($duration) ?> Intensive
            </div>
            <div style="padding: 8px 14px; border: 1px solid var(--tcm-border); background: var(--tcm-bg); font-size: 13px; font-weight: 600;">
              <i class="bi bi-alarm" style="margin-right: 6px;"></i> <?= e($schedule) ?>
            </div>
            <div style="padding: 8px 14px; border: 1px solid var(--tcm-border); background: var(--tcm-bg); font-size: 13px; font-weight: 600;">
              <i class="bi bi-broadcast" style="margin-right: 6px;"></i> <?= e($mode) ?>
            </div>
            <div style="padding: 8px 14px; border: 1px solid var(--tcm-border); background: var(--tcm-bg); font-size: 13px; font-weight: 600;">
              <i class="bi bi-patch-check" style="margin-right: 6px;"></i> Verified Certificate
            </div>
          </div>
        </div>

      </div>
    </section>

    <!-- Main Content Layout -->
    <section style="padding: clamp(28px, 3.5vw, 40px) 0 clamp(48px, 6vw, 64px);">
      <div class="tcm-container">
        <div class="ws-layout-grid">

          <!-- Left Column: Syllabus, Outcomes, Audience -->
          <div>
            
            <!-- Highlights Section -->
            <?php if (!empty($workshop['highlights'])): ?>
              <div style="background: var(--tcm-surface); border: 1px solid var(--tcm-border); padding: 32px; margin-bottom: 36px;">
                <h2 class="tcm-h3" style="margin-bottom: 16px;">What You Will Master</h2>
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 16px;">
                  <?php foreach ($workshop['highlights'] as $highlight): ?>
                    <div style="display: flex; gap: 12px; align-items: flex-start;">
                      <i class="bi bi-check2-square" style="color: var(--tcm-fg); font-size: 16px; margin-top: 2px;"></i>
                      <span style="font-size: 14px; color: var(--tcm-fg); line-height: 1.5;"><?= e($highlight) ?></span>
                    </div>
                  <?php endforeach; ?>
                </div>
              </div>
            <?php endif; ?>

            <!-- Day-by-Day Detailed Curriculum -->
            <div style="margin-bottom: 40px;">
              <div style="display: flex; justify-content: space-between; align-items: baseline; margin-bottom: 20px;">
                <h2 class="tcm-h2" style="font-size: 28px;">Curriculum &amp; Hands-On Labs</h2>
                <span style="font-size: 13px; color: var(--tcm-muted);"><?= count($workshop['curriculum'] ?? []) ?> Structured Modules</span>
              </div>

              <?php if (!empty($workshop['curriculum'])): ?>
                <?php foreach ($workshop['curriculum'] as $idx => $day): ?>
                  <div class="ws-curriculum-day">
                    <div class="ws-day-header">
                      <span class="ws-day-pill"><?= e($day['day'] ?? ('Day ' . ($idx + 1))) ?></span>
                      <h3 class="ws-day-title"><?= e($day['title']) ?></h3>
                    </div>
                    <?php if (!empty($day['topics'])): ?>
                      <div class="ws-day-body">
                        <ul class="ws-topic-list">
                          <?php foreach ($day['topics'] as $topic): ?>
                            <li><?= e($topic) ?></li>
                          <?php endforeach; ?>
                        </ul>
                      </div>
                    <?php endif; ?>
                  </div>
                <?php endforeach; ?>
              <?php endif; ?>
            </div>

            <!-- Target Audience -->
            <div style="background: var(--tcm-surface); border: 1px solid var(--tcm-border); padding: 32px; margin-bottom: 36px;">
              <h2 class="tcm-h3" style="margin-bottom: 18px;">Who Is This Workshop Built For?</h2>
              <div style="display: flex; flex-direction: column; gap: 16px;">
                <?php if (!empty($workshop['target_audience'])): ?>
                  <?php foreach ($workshop['target_audience'] as $aud): ?>
                    <div style="border-left: 2px solid var(--tcm-fg); padding-left: 16px;">
                      <p style="font-size: 14.5px; line-height: 1.6; margin: 0; color: var(--tcm-fg);"><?= e($aud) ?></p>
                    </div>
                  <?php endforeach; ?>
                <?php else: ?>
                  <div style="border-left: 2px solid var(--tcm-fg); padding-left: 16px;">
                    <strong style="display: block; font-size: 14.5px; margin-bottom: 4px;">College Students &amp; Fresh Graduates</strong>
                    <p style="font-size: 13.5px; color: var(--tcm-muted); margin: 0;">Bridge the gap between theoretical college syllabi and real production codebases.</p>
                  </div>
                  <div style="border-left: 2px solid var(--tcm-fg); padding-left: 16px;">
                    <strong style="display: block; font-size: 14.5px; margin-bottom: 4px;">Beginners &amp; Non-Tech Career Switchers</strong>
                    <p style="font-size: 13.5px; color: var(--tcm-muted); margin: 0;">Step-by-step guidance starting from foundations with zero gatekeeping or jargon.</p>
                  </div>
                  <div style="border-left: 2px solid var(--tcm-fg); padding-left: 16px;">
                    <strong style="display: block; font-size: 14.5px; margin-bottom: 4px;">Self-Taught Developers</strong>
                    <p style="font-size: 13.5px; color: var(--tcm-muted); margin: 0;">Get structured accountability, live debugging sessions, and industry portfolio reviews.</p>
                  </div>
                <?php endif; ?>
              </div>
            </div>

            <!-- What You Receive Box -->
            <div style="background: #111111; color: #F8F7F4; padding: 36px; border: 1px solid #111111;">
              <div class="tcm-eyebrow" style="color: #999; margin-bottom: 8px;">DELIVERABLES INCLUDED</div>
              <h2 class="tcm-h3" style="color: #FFF; font-family: 'Instrument Serif', Georgia, serif; font-size: 28px; margin-bottom: 20px;">
                Everything Included with Your Workshop Pass
              </h2>
              <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 20px;">
                <div style="border-top: 1px solid rgba(255,255,255,0.2); padding-top: 14px;">
                  <strong style="font-size: 14.5px; display: block; color: #FFF; margin-bottom: 4px;">Live Interactive Classes</strong>
                  <span style="font-size: 12.5px; color: #BBB;">Direct audio/video question clearance with experienced instructors.</span>
                </div>
                <div style="border-top: 1px solid rgba(255,255,255,0.2); padding-top: 14px;">
                  <strong style="font-size: 14.5px; display: block; color: #FFF; margin-bottom: 4px;">Lifetime Class Recordings</strong>
                  <span style="font-size: 12.5px; color: #BBB;">Never miss a lesson; revisit all workshop recordings anytime on your student portal.</span>
                </div>
                <div style="border-top: 1px solid rgba(255,255,255,0.2); padding-top: 14px;">
                  <strong style="font-size: 14.5px; display: block; color: #FFF; margin-bottom: 4px;">Source Code &amp; Starter Repos</strong>
                  <span style="font-size: 12.5px; color: #BBB;">Clean, documented GitHub repositories ready to clone and adapt.</span>
                </div>
                <div style="border-top: 1px solid rgba(255,255,255,0.2); padding-top: 14px;">
                  <strong style="font-size: 14.5px; display: block; color: #FFF; margin-bottom: 4px;">Verified Certificate</strong>
                  <span style="font-size: 12.5px; color: #BBB;">Digital verifiable credential issued upon completion to add to your LinkedIn profile.</span>
                </div>
              </div>
            </div>

          </div>

          <!-- Right Column: Sticky Enrollment Sidebar -->
          <div>
            <aside class="ws-sticky-panel" aria-label="Workshop Enrollment Panel">
              
              <div style="border-bottom: 1px solid var(--tcm-border); padding-bottom: 20px; margin-bottom: 20px;">
                <div class="tcm-eyebrow" style="margin-bottom: 6px;">COHORT PASS</div>
                <div style="display: flex; align-items: baseline; gap: 10px;">
                  <span style="font-size: 34px; font-weight: 800; color: var(--tcm-fg); letter-spacing: -0.02em;">
                    ₹<?= number_format($price, 0) ?>
                  </span>
                  <?php if ($origPrice > $price): ?>
                    <span style="font-size: 16px; color: var(--tcm-muted); text-decoration: line-through;">
                      ₹<?= number_format($origPrice, 0) ?>
                    </span>
                    <span style="font-size: 11px; font-weight: 700; background: #EAE9E4; color: var(--tcm-fg); padding: 2px 6px;">
                      <?= round((($origPrice - $price) / $origPrice) * 100) ?>% OFF
                    </span>
                  <?php endif; ?>
                </div>

                <div style="font-size: 12px; color: var(--tcm-muted); margin-top: 6px;">
                  One-time cohort registration fee. Zero hidden charges.
                </div>
              </div>

              <!-- Cohort Specifications -->
              <div style="display: flex; flex-direction: column; gap: 12px; margin-bottom: 24px; font-size: 13.5px;">
                <div style="display: flex; justify-content: space-between; border-bottom: 1px solid rgba(222,221,216,0.6); padding-bottom: 8px;">
                  <span style="color: var(--tcm-muted);">Duration</span>
                  <strong><?= e($duration) ?></strong>
                </div>
                <div style="display: flex; justify-content: space-between; border-bottom: 1px solid rgba(222,221,216,0.6); padding-bottom: 8px;">
                  <span style="color: var(--tcm-muted);">Schedule</span>
                  <strong><?= e($schedule) ?></strong>
                </div>
                <div style="display: flex; justify-content: space-between; border-bottom: 1px solid rgba(222,221,216,0.6); padding-bottom: 8px;">
                  <span style="color: var(--tcm-muted);">Format</span>
                  <strong><?= e($mode) ?></strong>
                </div>
                <div style="display: flex; justify-content: space-between; border-bottom: 1px solid rgba(222,221,216,0.6); padding-bottom: 8px;">
                  <span style="color: var(--tcm-muted);">Batch Size</span>
                  <strong>Limited to 30 seats</strong>
                </div>
                <div style="display: flex; justify-content: space-between; border-bottom: 1px solid rgba(222,221,216,0.6); padding-bottom: 8px;">
                  <span style="color: var(--tcm-muted);">Recordings</span>
                  <strong>Lifetime Access</strong>
                </div>
              </div>

              <!-- CTA Actions -->
              <div style="display: flex; flex-direction: column; gap: 10px;">
                <button type="button" class="tcm-btn tcm-btn-primary tcm-trigger-workshop" data-slug="<?= e($workshop['slug']) ?>" data-title="<?= e($workshop['title']) ?>" data-price="<?= $price ?>" style="width: 100%; height: 50px;">
                  <span>Reserve Your Seat</span>
                  <svg class="arrow-icon" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M1 7H13M13 7L7 1M13 7L7 13" stroke="currentColor" stroke-width="1.5" stroke-linecap="square"/></svg>
                </button>

                <?php
                  $waMsg = "Hi TCM One team! I want to join the {$workshop['short_title']} (₹{$workshop['price']}). Please guide me with enrollment.";
                  $waUrl = "https://wa.me/919238695500?text=" . rawurlencode($waMsg);
                ?>
                <a href="<?= $waUrl ?>" target="_blank" rel="noopener noreferrer" class="tcm-btn tcm-btn-secondary" style="width: 100%; justify-content: center; height: 44px;">
                  <i class="bi bi-whatsapp"></i> Register via WhatsApp
                </a>

                <!-- Share Workshop Button -->
                <button type="button" class="tcm-btn tcm-btn-outline" style="width: 100%; justify-content: center; height: 42px;" onclick="if(navigator.share){navigator.share({title:<?= json_encode($workshop['title']) ?>,text:<?= json_encode($workshop['tagline']) ?>,url:window.location.href});}else{navigator.clipboard.writeText(window.location.href);alert('Workshop link copied to clipboard!');}">
                  <i class="bi bi-share"></i> Share Workshop
                </button>
              </div>

              <div style="margin-top: 20px; font-size: 12px; color: var(--tcm-muted); text-align: center; line-height: 1.5;">
                <i class="bi bi-shield-check"></i> 100% verified curriculum &middot; Direct instructor mentorship
              </div>
            </aside>
          </div>

        </div>
      </div>
    </section>

    <!-- Unified Sticky Bottom Enrollment Bar (Always Visible on Scroll) -->
    <div class="tcm-sticky-bottom-bar" role="complementary" aria-label="Workshop Enrollment Bar">
      <div class="tcm-container">
        <div class="tcm-sticky-bottom-inner">
          <div class="tcm-sticky-bottom-info d-none d-lg-flex">
            <span class="tcm-badge"><?= e($category) ?></span>
            <div class="tcm-sticky-bottom-title"><?= e($workshop['title']) ?></div>
          </div>
          <div class="tcm-sticky-bottom-pricing">
            <div class="tcm-sticky-bottom-price-row">
              <span class="tcm-sticky-bottom-price">₹<?= number_format($price, 0) ?></span>
              <?php if ($origPrice > $price): ?>
                <span class="tcm-sticky-bottom-orig">₹<?= number_format($origPrice, 0) ?></span>
              <?php endif; ?>
            </div>
            <div class="tcm-sticky-bottom-sub">
              <span><?= e($duration) ?> Intensive</span>
            </div>
          </div>
          <div class="tcm-sticky-bottom-actions">
            <?php
              $waMsg = "Hi TCM One team! I want to join the {$workshop['short_title']} (₹{$workshop['price']}). Please guide me with enrollment.";
              $waUrl = "https://wa.me/919238695500?text=" . rawurlencode($waMsg);
            ?>
            <a href="<?= $waUrl ?>" target="_blank" rel="noopener" class="tcm-btn tcm-btn-secondary tcm-sticky-bottom-btn" aria-label="Register via WhatsApp">
              <i class="bi bi-whatsapp"></i>
              <span class="d-none d-sm-inline" style="margin-left: 4px;">WhatsApp</span>
            </a>
            <button type="button" class="tcm-btn tcm-btn-primary tcm-sticky-bottom-btn tcm-trigger-workshop" data-slug="<?= e($workshop['slug']) ?>" data-title="<?= e($workshop['title']) ?>" data-price="<?= $price ?>">
              <span>Reserve Seat</span>
              <svg class="arrow-icon" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M1 7H13M13 7L7 1M13 7L7 13" stroke="currentColor" stroke-width="1.5" stroke-linecap="square"/></svg>
            </button>
          </div>
        </div>
      </div>
    </div>

  </main>

  <!-- Site Footer -->
  <?php \TCM\Core\View::partial('partials.public-footer'); ?>

  <!-- Modal: Workshop Seat Reservation -->
  <div class="tcm-modal-overlay" id="tcmWsModalOverlay" role="dialog" aria-modal="true" aria-labelledby="tcmWsTitle">
    <div class="tcm-modal">
      <button type="button" class="tcm-modal-close" id="tcmWsCloseBtn" aria-label="Close modal">&times;</button>
      <div class="tcm-eyebrow" style="margin-bottom: 8px;">LIVE COHORT SEAT RESERVATION</div>
      <h3 id="tcmWsTitle" class="tcm-h3" style="margin-bottom: 4px;">Reserve Your Workshop Seat</h3>
      <div id="tcmWsPrice" style="font-size: 14px; font-weight: 700; color: var(--tcm-fg); margin-bottom: 16px;"></div>
      <div id="tcmWsFeedback" style="display: none;"></div>

      <form id="tcmWsForm" novalidate>
        <input type="hidden" id="tcmWsSlug" value="<?= e($workshop['slug']) ?>">
        <div class="tcm-form-group">
          <label for="tcmWsName" class="tcm-form-label">Full Name *</label>
          <input type="text" id="tcmWsName" class="tcm-form-input" placeholder="e.g. Rahul Sharma" required>
        </div>
        <div class="tcm-form-group">
          <label for="tcmWsEmail" class="tcm-form-label">Email Address *</label>
          <input type="email" id="tcmWsEmail" class="tcm-form-input" placeholder="e.g. rahul@example.com" required>
        </div>
        <div class="tcm-form-group">
          <label for="tcmWsPhone" class="tcm-form-label">WhatsApp / Phone Number *</label>
          <input type="tel" id="tcmWsPhone" class="tcm-form-input" placeholder="e.g. 9876543210" maxlength="14" required>
        </div>
        <button type="submit" class="tcm-btn tcm-btn-primary" id="tcmWsSubmitBtn" style="width: 100%; height: 50px;">
          <span>Reserve My Seat</span>
          <svg class="arrow-icon" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M1 7H13M13 7L7 1M13 7L7 13" stroke="currentColor" stroke-width="1.5" stroke-linecap="square"/></svg>
        </button>
      </form>
    </div>
  </div>

  <!-- Scripts -->
  <script src="<?= base_url('/assets/tcm-editorial.js') ?>"></script>
  <script src="<?= base_url('/assets/cai-assistant.js') ?>"></script>
</body>
</html>
