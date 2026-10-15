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

  <title><?= e($title ?? 'All Courses & Career Paths — TCM One') ?></title>
  <meta name="description" content="<?= e($description ?? 'Explore practical tech courses in Full Stack, Python, AI, Data Analytics, and Cloud with live mentorship at TCM One.') ?>">
  <meta name="robots" content="index, follow">
  <link rel="canonical" href="<?= e($canonical ?? 'https://thecodemunk.in/courses') ?>">

  <!-- Open Graph -->
  <meta property="og:type" content="website">
  <meta property="og:site_name" content="TCM One">
  <meta property="og:url" content="<?= e($canonical ?? 'https://thecodemunk.in/courses') ?>">
  <meta property="og:title" content="<?= e($title ?? 'All Courses & Career Paths — TCM One') ?>">
  <meta property="og:description" content="<?= e($description ?? 'Explore practical tech courses with live mentorship at TCM One.') ?>">
  <meta property="og:image" content="<?= e($ogImage ?? 'https://thecodemunk.in/assets/tcm-og-share.png') ?>">

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
    foreach (($courses ?? []) as $c) {
      $itemList['itemListElement'][] = [
        '@type' => 'ListItem',
        'position' => $pos++,
        'url' => ($appUrl ?? 'https://thecodemunk.in') . '/courses/' . rawurlencode((string)$c['slug']),
        'name' => $c['title'],
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
            <a href="<?= base_url('/courses') ?>" class="tcm-nav-link active">
              <span>Courses</span>
            </a>
          </div>
          <div class="tcm-nav-item">
            <a href="<?= base_url('/workshops') ?>" class="tcm-nav-link">Workshops</a>
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
          <a href="<?= base_url('/courses') ?>" class="tcm-btn tcm-btn-primary tcm-btn-sm">
            <span>Explore Courses</span>
            <svg class="arrow-icon" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M1 7H13M13 7L7 1M13 7L7 13" stroke="currentColor" stroke-width="1.5" stroke-linecap="square"/></svg>
          </a>
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
    <a href="<?= base_url('/courses') ?>" class="tcm-drawer-link" style="color: var(--tcm-fg); font-weight: 700;">Courses</a>
    <a href="<?= base_url('/workshops') ?>" class="tcm-drawer-link">Workshops</a>
    <a href="<?= base_url('/for-institutions') ?>" class="tcm-drawer-link">For Institutions</a>
    <a href="<?= base_url('/about') ?>" class="tcm-drawer-link">About TCM One</a>
    <a href="<?= base_url('/contact') ?>" class="tcm-drawer-link">Contact</a>
  </aside>

  <main id="mainContent">

    <!-- Catalog Hero -->
    <section class="tcm-section" style="padding-top: clamp(48px, 6vw, 80px); padding-bottom: 32px; border-bottom: 1px solid var(--tcm-border);">
      <div class="tcm-container">
        <div style="max-width: 780px;">
          <div class="tcm-eyebrow">COURSE CATALOG</div>
          <h1 class="tcm-h1" style="margin: 8px 0 16px 0;">
            All Courses &amp; <span class="font-editorial-serif">Career Paths</span>
          </h1>
          <p class="tcm-lead" style="margin: 0;">
            Practical, project-based engineering curricula with live mentored sessions, hands-on repositories, and verified portfolio outcomes.
          </p>
        </div>

        <!-- Filter & Search Controls -->
        <div style="margin-top: 40px; padding: 24px; background-color: var(--tcm-surface); border: 1px solid var(--tcm-border);">
          <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 14px;">
            <!-- Search -->
            <div style="grid-column: 1 / -1;" class="d-md-block">
              <div style="position: relative;">
                <input type="text" id="catalogSearch" class="tcm-form-input" placeholder="Search by course title, skill, or tool (e.g. Python, React, SQL, DevOps)..." style="padding-left: 42px;">
                <i class="bi bi-search" style="position: absolute; left: 16px; top: 50%; transform: translateY(-50%); color: var(--tcm-muted); font-size: 15px;"></i>
              </div>
            </div>

            <!-- Category -->
            <div>
              <label for="catalogCategory" class="tcm-form-label" style="font-size: 11px;">Category Track</label>
              <select id="catalogCategory" class="tcm-form-select">
                <option value="">All Categories</option>
                <?php foreach (($categories ?? []) as $cat): ?>
                  <option value="<?= e($cat['name']) ?>"><?= e($cat['name']) ?></option>
                <?php endforeach; ?>
              </select>
            </div>

            <!-- Skill Level -->
            <div>
              <label for="catalogLevel" class="tcm-form-label" style="font-size: 11px;">Skill Level</label>
              <select id="catalogLevel" class="tcm-form-select">
                <option value="">All Levels</option>
                <option value="beginner">Beginner</option>
                <option value="intermediate">Intermediate</option>
                <option value="advanced">Advanced</option>
              </select>
            </div>

            <!-- Price Range -->
            <div>
              <label for="catalogPrice" class="tcm-form-label" style="font-size: 11px;">Price Range</label>
              <select id="catalogPrice" class="tcm-form-select">
                <option value="">All Pricing</option>
                <option value="under-2k">Under ₹2,000 (&lt;$25 USD)</option>
                <option value="2k-5k">₹2,000 – ₹5,000 ($25–$60 USD)</option>
                <option value="5k-10k">₹5,000 – ₹10,000 ($60–$120 USD)</option>
                <option value="above-10k">Above ₹10,000 ($120+ USD)</option>
              </select>
            </div>

            <!-- Sorting -->
            <div>
              <label for="catalogSort" class="tcm-form-label" style="font-size: 11px;">Sort By</label>
              <select id="catalogSort" class="tcm-form-select">
                <option value="relevance">Relevance</option>
                <option value="price-low">Price: Low to High</option>
                <option value="price-high">Price: High to Low</option>
                <option value="newest">Newest Cohorts</option>
              </select>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Catalog Results Section -->
    <section class="tcm-section">
      <div class="tcm-container">
        <!-- Results header & count -->
        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 28px; border-bottom: 1px solid var(--tcm-border); padding-bottom: 14px;">
          <div id="catalogCount" style="font-size: 14px; font-weight: 600; color: var(--tcm-fg);">
            <?= count($courses ?? []) ?> courses available
          </div>
          <div style="font-size: 12px; color: var(--tcm-muted);">
            Live online instruction &middot; Real-time mentor reviews
          </div>
        </div>

        <!-- 3-Column Grid -->
        <div class="tcm-course-grid" id="courseCatalogGrid">
          <?php foreach (($courses ?? []) as $c): ?>
            <?php
              $priceInr = (float)($c['price'] ?? 0);
              $origInr = !empty($c['original_price']) ? (float)$c['original_price'] : 0;
              $slug = $c['slug'] ?? '';
              $category = $c['category_name'] ?? $c['category'] ?? 'General';
              $level = !empty($c['level']) ? ucfirst((string)$c['level']) : 'All Levels';
              $duration = $c['duration'] ?? '6 Weeks';
            ?>
            <div class="tcm-course-card" data-slug="<?= e($slug) ?>" data-cat="<?= e(strtolower($category)) ?>" data-price="<?= $priceInr ?>" data-level="<?= e(strtolower($level)) ?>">
              <div class="tcm-course-top">
                <span class="tcm-course-cat"><?= e($category) ?></span>
                <span class="tcm-course-level"><?= e($level) ?></span>
              </div>
              <h3 class="tcm-course-title">
                <a href="<?= base_url('/courses/' . rawurlencode($slug)) ?>"><?= e($c['title']) ?></a>
              </h3>
              <p class="tcm-course-desc"><?= e($c['subtitle'] ?: $c['description'] ?: '') ?></p>
              <div class="tcm-course-meta">
                <span class="tcm-course-meta-item"><i class="bi bi-clock"></i> <?= e($duration) ?></span>
                <span class="tcm-course-meta-item"><i class="bi bi-broadcast"></i> Live Cohort</span>
              </div>
              <div class="tcm-course-bottom">
                <div class="tcm-course-price-wrap">
                  <div>
                    <span class="tcm-course-price">₹<?= number_format($priceInr, 0) ?></span>
                    <?php if ($origInr > $priceInr): ?>
                      <span class="tcm-course-orig-price">₹<?= number_format($origInr, 0) ?></span>
                    <?php endif; ?>
                  </div>
                  <span class="tcm-course-usd">$<?= round($priceInr / 83.5) ?> USD</span>
                </div>
                <a href="<?= base_url('/courses/' . rawurlencode($slug)) ?>" class="tcm-btn tcm-btn-secondary tcm-btn-sm">
                  <span>View Course</span>
                  <svg class="arrow-icon" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M1 7H13M13 7L7 1M13 7L7 13" stroke="currentColor" stroke-width="1.5" stroke-linecap="square"/></svg>
                </a>
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
          <h4 class="tcm-footer-col-title">Help &amp; Admissions</h4>
          <p style="font-size: 14px; color: var(--tcm-muted); margin: 0 0 12px 0;">
            Admissions Counselor: <a href="https://wa.me/919238695500" target="_blank" rel="noopener" style="color: var(--tcm-fg); font-weight: 600;">+91 9238695500</a>
          </p>
          <p style="font-size: 14px; color: var(--tcm-muted); margin: 0;">
            Email: <a href="mailto:hello@thecodemunk.com" style="color: var(--tcm-fg);">hello@thecodemunk.com</a>
          </p>
        </div>
      </div>
      <div class="tcm-footer-bottom">
        <div>&copy; 2026 TCM One (The Code Munk). All rights reserved.</div>
      </div>
    </div>
  </footer>

  <!-- Scripts -->
  <script src="<?= base_url('/assets/tcm-editorial.js') ?>"></script>
  <script src="<?= base_url('/assets/cai-assistant.js') ?>"></script>
</body>
</html>
