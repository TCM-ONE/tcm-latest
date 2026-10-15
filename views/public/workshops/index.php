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

  <title><?= e($title ?? 'Live Tech Workshops — TCM One') ?></title>
  <meta name="description" content="<?= e($description ?? 'Explore live technical workshops in Python, Web Development, and AI with practical coding and mentor reviews.') ?>">
  <meta name="robots" content="index, follow">
  <link rel="canonical" href="<?= e($canonical ?? 'https://thecodemunk.in/workshops') ?>">

  <!-- Open Graph -->
  <meta property="og:type" content="website">
  <meta property="og:site_name" content="TCM One">
  <meta property="og:url" content="<?= e($canonical) ?>">
  <meta property="og:title" content="<?= e($title) ?>">
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

  <!-- Structured Data (JSON-LD) -->
  <?php
    $itemList = [
      '@context' => 'https://schema.org',
      '@type' => 'ItemList',
      'itemListElement' => [],
    ];
    $pos = 1;
    foreach (($workshops ?? []) as $w) {
      $itemList['itemListElement'][] = [
        '@type' => 'ListItem',
        'position' => $pos++,
        'url' => ($appUrl ?? 'https://thecodemunk.in') . '/workshops/' . rawurlencode((string)$w['slug']),
        'name' => $w['title'],
      ];
    }
  ?>
  <script type="application/ld+json"><?= json_encode($itemList, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) ?></script>
</head>
<body>

  <!-- Header -->
  <header class="tcm-header" role="banner">
    <div class="tcm-container">
      <div class="tcm-header-inner">
        <a href="<?= base_url('/') ?>" class="tcm-brand" aria-label="TCM One Home">
          <img src="<?= base_url('/assets/logo.png') ?>" alt="TCM One" class="tcm-brand-logo">
          <span class="tcm-brand-text">TCM<span class="brand-accent">ONE</span></span>
        </a>

        <nav class="tcm-nav" role="navigation" aria-label="Main Navigation">
          <div class="tcm-nav-item">
            <a href="<?= base_url('/courses') ?>" class="tcm-nav-link">Courses</a>
          </div>
          <div class="tcm-nav-item">
            <a href="<?= base_url('/workshops') ?>" class="tcm-nav-link active">Workshops</a>
          </div>
          <div class="tcm-nav-item">
            <a href="<?= base_url('/for-institutions') ?>" class="tcm-nav-link">For Institutions</a>
          </div>
          <div class="tcm-nav-item">
            <a href="<?= base_url('/about') ?>" class="tcm-nav-link">About</a>
          </div>
        </nav>

        <div class="tcm-header-actions">
          <a href="<?= base_url('/contact') ?>" class="tcm-btn-link d-none d-sm-inline-flex" style="font-size: 14px; margin-right: 8px;">Contact</a>
          <a href="<?= base_url('/courses') ?>" class="tcm-btn tcm-btn-primary tcm-btn-sm">Explore Courses</a>
          <button type="button" class="tcm-menu-toggle" aria-label="Toggle Navigation Drawer">
            <i class="bi bi-list" style="font-size: 22px;"></i>
          </button>
        </div>
      </div>
    </div>
  </header>

  <!-- Mobile Drawer -->
  <div class="tcm-drawer-backdrop"></div>
  <aside class="tcm-mobile-drawer" aria-label="Mobile Navigation">
    <button type="button" class="tcm-drawer-close" aria-label="Close menu">&times;</button>
    <a href="<?= base_url('/') ?>" class="tcm-drawer-link">Home</a>
    <a href="<?= base_url('/courses') ?>" class="tcm-drawer-link">Courses</a>
    <a href="<?= base_url('/workshops') ?>" class="tcm-drawer-link" style="color: var(--tcm-fg); font-weight: 700;">Workshops</a>
    <a href="<?= base_url('/for-institutions') ?>" class="tcm-drawer-link">For Institutions</a>
    <a href="<?= base_url('/about') ?>" class="tcm-drawer-link">About TCM One</a>
    <a href="<?= base_url('/contact') ?>" class="tcm-drawer-link">Contact</a>
  </aside>

  <main id="mainContent">

    <!-- Workshop Hero -->
    <section class="tcm-section" style="padding-top: clamp(36px, 4.5vw, 52px); padding-bottom: clamp(24px, 3vw, 36px); border-bottom: 1px solid var(--tcm-border);">
      <div class="tcm-container">
        <div style="max-width: 780px;">
          <div class="tcm-eyebrow">LIVE SHORT-FORMAT LEARNING</div>
          <h1 class="tcm-h1" style="margin: 8px 0 16px 0;">
            Learn something valuable.<br>
            <span class="font-editorial-serif">Start with one workshop.</span>
          </h1>
          <p class="tcm-lead" style="margin: 0;">
            Focused live cohorts covering practical, job-ready techniques with direct mentor code reviews and real projects.
          </p>
        </div>
      </div>
    </section>

    <!-- Workshop Listings -->
    <section class="tcm-section" style="padding-top: clamp(28px, 3.5vw, 40px);">
      <div class="tcm-container">
        <div class="tcm-workshop-grid">
          <?php foreach (($workshops ?? []) as $w): ?>
            <?php
              $price = (float)($w['price'] ?? 0);
              $origPrice = !empty($w['original_price']) ? (float)$w['original_price'] : 0;
              $slug = $w['slug'] ?? '';
              $badge = $w['badge'] ?? 'Live Cohort';
              $duration = $w['duration'] ?? 'Live Workshop';
              $mode = $w['mode'] ?? 'Live Online Cohort';
            ?>
            <div class="tcm-workshop-card">
              <div class="tcm-workshop-badge"><?= e($badge) ?></div>
              <h2 class="tcm-workshop-title">
                <a href="<?= base_url('/workshops/' . rawurlencode($slug)) ?>" style="color: inherit; text-decoration: none;">
                  <?= e($w['title']) ?>
                </a>
              </h2>
              <p class="tcm-workshop-desc"><?= e($w['tagline'] ?? $w['description'] ?? '') ?></p>

              <div style="display: flex; gap: 16px; font-size: 12.5px; color: var(--tcm-muted); margin-bottom: 16px; border-bottom: 1px solid var(--tcm-border); padding-bottom: 12px;">
                <span><i class="bi bi-clock"></i> <?= e($duration) ?></span>
                <span><i class="bi bi-broadcast"></i> <?= e($mode) ?></span>
              </div>

              <?php if (!empty($w['highlights']) && is_array($w['highlights'])): ?>
                <ul class="tcm-workshop-highlights">
                  <?php foreach (array_slice($w['highlights'], 0, 4) as $h): ?>
                    <li><?= e($h) ?></li>
                  <?php endforeach; ?>
                </ul>
              <?php endif; ?>

              <div class="tcm-workshop-footer">
                <div class="tcm-workshop-pricing">
                  <div class="tcm-workshop-price-row">
                    <span class="tcm-workshop-price-tag">₹<?= number_format($price, 0) ?></span>
                    <?php if ($origPrice > $price): ?>
                      <span class="tcm-workshop-price-orig">
                        ₹<?= number_format($origPrice, 0) ?>
                      </span>
                    <?php endif; ?>
                  </div>
                  <div class="tcm-workshop-batch-meta">
                    Next Cohort Enrolling
                  </div>
                </div>

                <div class="tcm-workshop-actions">
                  <a href="<?= base_url('/workshops/' . rawurlencode($slug)) ?>" class="tcm-btn tcm-btn-secondary tcm-btn-sm">
                    Details
                  </a>
                  <button type="button" class="tcm-btn tcm-btn-primary tcm-btn-sm tcm-trigger-workshop" data-slug="<?= e($slug) ?>" data-title="<?= e($w['title']) ?>" data-price="<?= $price ?>">
                    <span>Reserve Seat</span>
                    <svg class="arrow-icon" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M1 7H13M13 7L7 1M13 7L7 13" stroke="currentColor" stroke-width="1.5" stroke-linecap="square"/></svg>
                  </button>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
    </section>

  </main>

  <!-- Footer -->
  <footer class="tcm-footer" role="contentinfo">
    <div class="tcm-container">
      <div class="tcm-footer-grid">
        <div>
          <a href="<?= base_url('/') ?>" class="tcm-brand">
            <img src="<?= base_url('/assets/logo.png') ?>" alt="TCM One" class="tcm-brand-logo">
            <span class="tcm-brand-text">TCM<span class="brand-accent">ONE</span></span>
          </a>
          <p class="tcm-footer-brand-p">
            Practical tech education through live classes, guided projects, and industry-focused learning.
          </p>
        </div>
        <div>
          <h4 class="tcm-footer-col-title">Platform</h4>
          <ul class="tcm-footer-links">
            <li><a href="<?= base_url('/courses') ?>">All Courses</a></li>
            <li><a href="<?= base_url('/workshops') ?>">Live Workshops</a></li>
            <li><a href="<?= base_url('/for-institutions') ?>">For Institutions</a></li>
            <li><a href="<?= base_url('/about') ?>">About Us</a></li>
            <li><a href="<?= base_url('/contact') ?>">Contact</a></li>
          </ul>
        </div>
        <div>
          <h4 class="tcm-footer-col-title">Admissions</h4>
          <p style="font-size: 14px; color: var(--tcm-muted); margin: 0 0 12px 0;">
            WhatsApp: <a href="https://wa.me/919238695500" target="_blank" rel="noopener" style="color: var(--tcm-fg); font-weight: 600;">+91 9238695500</a>
          </p>
          <p style="font-size: 14px; color: var(--tcm-muted); margin: 0;">
            Email: <a href="mailto:hello@thecodemunk.com" style="color: var(--tcm-fg);">hello@thecodemunk.com</a>
          </p>
        </div>
      </div>
      <div class="tcm-footer-bottom">
        <div>&copy; 2026 TCM One. All rights reserved.</div>
      </div>
    </div>
  </footer>

  <!-- Modal: Workshop Seat Reservation -->
  <div class="tcm-modal-overlay" id="tcmWsModalOverlay" role="dialog" aria-modal="true" aria-labelledby="tcmWsTitle">
    <div class="tcm-modal">
      <button type="button" class="tcm-modal-close" id="tcmWsCloseBtn" aria-label="Close modal">&times;</button>
      <div class="tcm-eyebrow" style="margin-bottom: 8px;">LIVE SEAT RESERVATION</div>
      <h3 id="tcmWsTitle" class="tcm-h3" style="margin-bottom: 4px;">Reserve Your Workshop Seat</h3>
      <div id="tcmWsPrice" style="font-size: 14px; font-weight: 700; color: var(--tcm-fg); margin-bottom: 16px;"></div>
      <div id="tcmWsFeedback" style="display: none;"></div>

      <form id="tcmWsForm" novalidate>
        <input type="hidden" id="tcmWsSlug" value="">
        <div class="tcm-form-group">
          <label for="tcmWsName" class="tcm-form-label">Full Name *</label>
          <input type="text" id="tcmWsName" class="tcm-form-input" placeholder="e.g. Rahul Sharma" required>
        </div>
        <div class="tcm-form-group">
          <label for="tcmWsEmail" class="tcm-form-label">Email Address *</label>
          <input type="email" id="tcmWsEmail" class="tcm-form-input" placeholder="e.g. rahul@example.com" required>
        </div>
        <div class="tcm-form-group">
          <label for="tcmWsPhone" class="tcm-form-label">WhatsApp / Mobile Number *</label>
          <input type="tel" id="tcmWsPhone" class="tcm-form-input" placeholder="e.g. 9876543210" maxlength="14" required>
        </div>
        <button type="submit" class="tcm-btn tcm-btn-primary" id="tcmWsSubmitBtn" style="width: 100%; height: 50px;">
          Reserve My Seat
        </button>
      </form>
    </div>
  </div>

  <!-- Scripts -->
  <script src="<?= base_url('/assets/tcm-editorial.js') ?>"></script>
  <script src="<?= base_url('/assets/cai-assistant.js') ?>"></script>
</body>
</html>
