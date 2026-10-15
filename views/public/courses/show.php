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

  <title><?= e($title ?? ($course['title'] . ' | TCM One')) ?></title>
  <meta name="description" content="<?= e($desc ?? ($course['subtitle'] ?: $course['description'] ?: '')) ?>">
  <meta name="robots" content="index, follow">
  <link rel="canonical" href="<?= e($canonical ?? ('https://thecodemunk.in/courses/' . rawurlencode((string)$course['slug']))) ?>">

  <!-- Open Graph -->
  <meta property="og:type" content="website">
  <meta property="og:site_name" content="TCM One">
  <meta property="og:url" content="<?= e($canonical) ?>">
  <meta property="og:title" content="<?= e($course['title']) ?> &mdash; TCM One">
  <meta property="og:description" content="<?= e($desc) ?>">
  <meta property="og:image" content="<?= e($thumbUrl) ?>">
  <meta property="og:image:width" content="1200">
  <meta property="og:image:height" content="630">
  <meta property="og:locale" content="en_IN">

  <!-- Twitter Card -->
  <meta name="twitter:card" content="summary_large_image">
  <meta name="twitter:site" content="@thecodemunk">
  <meta name="twitter:title" content="<?= e($course['title']) ?> — TCM One">
  <meta name="twitter:description" content="<?= e($desc) ?>">
  <meta name="twitter:image" content="<?= e($thumbUrl) ?>">

  <!-- International Hreflang Tags -->
  <link rel="alternate" hreflang="en-IN" href="<?= e($canonical) ?>">
  <link rel="alternate" hreflang="en-US" href="<?= e($canonical) ?>">
  <link rel="alternate" hreflang="x-default" href="<?= e($canonical) ?>">

  <!-- Typography & Icons -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Instrument+Serif:ital@0;1&family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

  <!-- Design System Stylesheets -->
  <link rel="stylesheet" href="<?= base_url('/assets/tcm-design-system.css?v=2.18') ?>">
  <link rel="stylesheet" href="<?= base_url('/assets/tcm-editorial.css?v=2.18') ?>">
  <link rel="stylesheet" href="<?= base_url('/assets/cai-assistant.css?v=2.18') ?>">

  <?php
    $price = (float)($course['price'] ?? 0);
    $origPrice = !empty($course['original_price']) ? (float)$course['original_price'] : 0;
    $duration = !empty($course['duration']) ? $course['duration'] : 'Confirm duration with admissions';
    $level = !empty($course['level']) ? ucfirst((string)$course['level']) : 'Beginner';
    $category = $course['category_name'] ?? $course['category'] ?? 'Engineering';

    $courseSchema = [
      '@context' => 'https://schema.org',
      '@type' => 'Course',
      'name' => $course['title'],
      'description' => $desc,
      'url' => $canonical,
      'image' => $thumbUrl,
      'inLanguage' => 'en-IN',
      'educationalLevel' => $level,
      'teaches' => $category,
      'courseMode' => 'online',
      'hasCourseInstance' => [
        '@type' => 'CourseInstance',
        'courseMode' => 'online',
        'courseWorkload' => $duration,
        'instructor' => [
          '@type' => 'Organization',
          'name' => 'TCM One Mentors',
        ],
      ],
      'provider' => [
        '@type' => 'Organization',
        'name' => 'TCM One',
        'url' => $appUrl,
        'logo' => $appUrl . '/assets/logo.png',
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
        [
          '@type' => 'ListItem',
          'position' => 1,
          'name' => 'Home',
          'item' => 'https://thecodemunk.in/',
        ],
        [
          '@type' => 'ListItem',
          'position' => 2,
          'name' => 'Courses',
          'item' => 'https://thecodemunk.in/courses',
        ],
        [
          '@type' => 'ListItem',
          'position' => 3,
          'name' => (string) $course['title'],
          'item' => $canonical,
        ],
      ],
    ];
  ?>
  <script type="application/ld+json"><?= json_encode($courseSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) ?></script>
  <script type="application/ld+json"><?= json_encode($breadcrumbSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) ?></script>
  <script>
    gtag('event', 'course_view', {
      course_slug: <?= json_encode((string) $course['slug']) ?>,
      course_title: <?= json_encode((string) $course['title']) ?>,
      value: <?= json_encode($price) ?>,
      currency: 'INR'
    });
  </script>

  <style>
    html, body {
      overflow-x: hidden !important;
      max-width: 100vw !important;
    }
    *, *::before, *::after {
      box-sizing: border-box;
    }
    .cd-layout-grid {
      display: grid;
      grid-template-columns: minmax(0, 1fr);
      gap: 32px;
      width: 100%;
      min-width: 0;
    }
    .cd-layout-grid > * {
      min-width: 0;
      max-width: 100%;
    }
    @media (min-width: 992px) {
      .cd-layout-grid {
        grid-template-columns: minmax(0, 1fr) 380px;
        gap: 48px;
      }
    }
    .cd-sticky-panel {
      position: sticky;
      top: 96px;
      background: var(--tcm-surface);
      border: 1px solid var(--tcm-border);
      padding: clamp(18px, 4vw, 28px);
      box-sizing: border-box;
      max-width: 100%;
    }
    .cd-curriculum-module {
      border: 1px solid var(--tcm-border);
      background: var(--tcm-surface);
      margin-bottom: 12px;
    }
    .cd-module-summary {
      padding: 16px 20px;
      cursor: pointer;
      display: flex;
      align-items: center;
      justify-content: space-between;
      list-style: none;
      font-weight: 600;
      font-size: 15px;
    }
    .cd-module-summary::-webkit-details-marker { display: none; }
    .cd-module-content {
      padding: 14px 20px 18px;
      border-top: 1px solid var(--tcm-border);
      background: var(--tcm-bg);
      font-size: 13.5px;
      color: var(--tcm-muted);
    }
    .cd-lesson-row {
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 8px 0;
      border-bottom: 1px solid rgba(222, 221, 216, 0.6);
    }
    .cd-lesson-row:last-child { border-bottom: none; }
    .cd-floating-mobile-bar {
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
      .cd-floating-mobile-bar { display: none !important; }
    }
  </style>
  <link rel="stylesheet" href="<?= base_url('/assets/tcm-footer.css?v=1') ?>">
</head>
<body style="padding-bottom: 72px;">

  <!-- Header Navigation & Mobile Drawer -->
  <?php \TCM\Core\View::partial('partials.public-header'); ?>

  <main id="mainContent" style="padding-bottom: 84px;">

    <!-- Course Hero -->
    <section class="tcm-section" style="padding-top: clamp(36px, 4.5vw, 52px); padding-bottom: clamp(24px, 3vw, 32px); border-bottom: 1px solid var(--tcm-border);">
      <div class="tcm-container">
        <!-- Breadcrumbs -->
        <nav aria-label="Breadcrumb" style="font-size: 12px; color: var(--tcm-muted); margin-bottom: 16px;">
          <a href="<?= base_url('/') ?>" style="color: inherit; text-decoration: none;">Home</a>
          <span style="margin: 0 6px;">/</span>
          <a href="<?= base_url('/courses') ?>" style="color: inherit; text-decoration: none;">Courses</a>
          <span style="margin: 0 6px;">/</span>
          <span style="color: var(--tcm-fg); font-weight: 500;"><?= e($course['title']) ?></span>
        </nav>

        <div style="max-width: 820px;">
          <div class="tcm-eyebrow" style="margin-bottom: 10px;">
            <?= e($category) ?> &middot; <?= e($level) ?>
          </div>
          <h1 class="tcm-h1" style="margin: 0 0 16px 0;">
            <?= e($course['title']) ?>
          </h1>
          <p class="tcm-lead" style="margin: 0 0 24px 0;">
            <?= e($course['subtitle'] ?: $course['description'] ?: '') ?>
          </p>

          <div style="display: flex; flex-wrap: wrap; gap: 20px; font-size: 14px; color: var(--tcm-muted); border-top: 1px solid var(--tcm-border); padding-top: 16px;">
            <div><i class="bi bi-clock"></i> <strong>Duration:</strong> <?= e($duration) ?></div>
            <div><i class="bi bi-broadcast"></i> <strong>Mode:</strong> Live Online Cohort</div>
            <div><i class="bi bi-translate"></i> <strong>Language:</strong> English &amp; Hindi</div>
            <div><i class="bi bi-patch-check"></i> <strong>Certificate:</strong> <?= !empty($course['certificate']) ? 'Completion certificate, subject to requirements' : 'Not included' ?></div>
          </div>
        </div>
      </div>
    </section>

    <!-- Main Content & Sticky Enrollment Sidebar -->
    <section class="tcm-section" style="padding-top: clamp(28px, 3.5vw, 40px); padding-bottom: clamp(48px, 6vw, 64px);">
      <div class="tcm-container">
        <div class="cd-layout-grid">

          <!-- Left Column: Detailed Course Modules -->
          <div>
            <!-- What You Will Learn -->
            <div style="background: var(--tcm-surface); border: 1px solid var(--tcm-border); padding: clamp(18px, 4vw, 32px); margin-bottom: 32px; box-sizing: border-box;">
              <h2 class="tcm-h3" style="margin: 0 0 16px 0;">What You Will Learn</h2>
              <div style="font-size: 15px; line-height: 1.7; color: var(--tcm-fg);">
                <?= nl2br(e($course['description'] ?: '')) ?>
              </div>
            </div>

            <!-- Curriculum Accordion -->
            <div style="margin-bottom: 36px;">
              <div style="display: flex; align-items: baseline; justify-content: space-between; margin-bottom: 16px;">
                <h2 class="tcm-h3" style="margin: 0;">Syllabus &amp; Curriculum</h2>
                <span style="font-size: 13px; color: var(--tcm-muted);"><?= count($curriculum ?? []) ?> Modules</span>
              </div>

              <?php if (!empty($curriculum) && is_array($curriculum)): ?>
                <?php foreach ($curriculum as $idx => $mod): ?>
                  <details class="cd-curriculum-module" <?= $idx === 0 ? 'open' : '' ?>>
                    <summary class="cd-module-summary">
                      <span>Module <?= str_pad((string)($idx + 1), 2, '0', STR_PAD_LEFT) ?> &mdash; <?= e($mod['title']) ?></span>
                      <i class="bi bi-chevron-down" style="font-size: 12px;"></i>
                    </summary>
                    <div class="cd-module-content">
                      <?php if (!empty($mod['lessons']) && is_array($mod['lessons'])): ?>
                        <?php foreach ($mod['lessons'] as $lesson): ?>
                          <div class="cd-lesson-row">
                            <span style="font-weight: 500; color: var(--tcm-fg);">
                              <i class="bi <?= ($lesson['type'] ?? '') === 'project' ? 'bi-folder2-open' : 'bi-play-circle' ?>" style="margin-right: 6px;"></i>
                              <?= e($lesson['title']) ?>
                            </span>
                            <span style="font-size: 11px; text-transform: uppercase; letter-spacing: 0.05em; padding: 2px 6px; border: 1px solid var(--tcm-border); background: #FFF;">
                              <?= e($lesson['type'] ?? 'Live') ?>
                            </span>
                          </div>
                        <?php endforeach; ?>
                      <?php else: ?>
                        <p style="margin: 0;">Detailed session topics, assignments, and practical code labs.</p>
                      <?php endif; ?>
                    </div>
                  </details>
                <?php endforeach; ?>
              <?php else: ?>
                <div style="padding: 24px; border: 1px solid var(--tcm-border); background: var(--tcm-surface);">
                  <p class="tcm-body-muted" style="margin: 0;">Interactive live sessions with code reviews, assignments, and real-world project deliverables.</p>
                </div>
              <?php endif; ?>
            </div>

            <!-- Learning Methodology & Projects -->
            <div style="background: var(--tcm-surface); border: 1px solid var(--tcm-border); padding: clamp(18px, 4vw, 32px); margin-bottom: 32px; box-sizing: border-box;">
              <h2 class="tcm-h3" style="margin: 0 0 16px 0;">Learning Methodology</h2>
              <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 20px;">
                <div>
                  <h4 style="font-size: 15px; font-weight: 600; margin: 0 0 6px 0;">Live Mentor Code Reviews</h4>
                  <p class="tcm-body-muted" style="font-size: 13.5px; margin: 0;">Push code to GitHub repositories and receive line-by-line feedback from industry developers.</p>
                </div>
                <div>
                  <h4 style="font-size: 15px; font-weight: 600; margin: 0 0 6px 0;">Capstone Deliverables</h4>
                  <p class="tcm-body-muted" style="font-size: 13.5px; margin: 0;">Complete production-ready applications that you can showcase directly in interviews and on your resume.</p>
                </div>
              </div>
            </div>

            <!-- Prerequisites & Audience -->
            <div style="background: var(--tcm-surface); border: 1px solid var(--tcm-border); padding: clamp(18px, 4vw, 32px); box-sizing: border-box;">
              <h2 class="tcm-h3" style="margin: 0 0 16px 0;">Prerequisites</h2>
              <p class="tcm-body-muted" style="font-size: 14.5px; line-height: 1.6; margin: 0;">
                No advanced background required unless specified. You will need a personal computer/laptop, internet connection, and dedication to attend live classes and submit assigned code tasks.
              </p>
            </div>
          </div>

          <!-- Right Column: Sticky Enrollment Panel (Desktop) -->
          <div>
            <aside class="cd-sticky-panel" aria-label="Course Enrollment">
              <div class="tcm-eyebrow" style="margin-bottom: 8px;">LIVE COHORT ENROLLMENT</div>
              <h3 style="font-size: 20px; font-weight: 700; margin: 0 0 14px 0; line-height: 1.3;">
                <?= e($course['title']) ?>
              </h3>

              <div style="margin-bottom: 20px; border-bottom: 1px solid var(--tcm-border); padding-bottom: 16px;">
                <div style="display: flex; align-items: baseline; gap: 8px;">
                  <span style="font-size: 32px; font-weight: 700; color: var(--tcm-fg); letter-spacing: -0.02em;">
                    ₹<?= number_format($price, 0) ?>
                  </span>
                  <?php if ($origPrice > $price): ?>
                    <span style="font-size: 16px; color: #999; text-decoration: line-through;">
                      ₹<?= number_format($origPrice, 0) ?>
                    </span>
                  <?php endif; ?>
                <div style="font-size: 12px; color: var(--tcm-muted); margin-top: 4px;">
                  All taxes included &middot; Billed in INR
                </div>
                <div style="font-size: 11.5px; color: var(--tcm-muted); margin-top: 6px;">
                  <i class="bi bi-globe2"></i> <strong>Global Learners:</strong> Sessions in IST (UTC+5:30) with HD recordings &amp; weekend cohorts
                </div>

                <p style="font-size:12px;line-height:1.6;margin-top:12px">Confirm the batch timetable and any written instalment arrangement with admissions before payment.</p>
              </div>

              <!-- Cohort Details -->
              <div style="display: flex; flex-direction: column; gap: 10px; font-size: 13.5px; color: var(--tcm-muted); margin-bottom: 24px;">
                <div style="display: flex; justify-content: space-between;">
                  <span>Duration:</span>
                  <strong style="color: var(--tcm-fg);"><?= e($duration) ?></strong>
                </div>
                <div style="display: flex; justify-content: space-between;">
                  <span>Level:</span>
                  <strong style="color: var(--tcm-fg);"><?= e($level) ?></strong>
                </div>
                <div style="display: flex; justify-content: space-between;">
                  <span>Next Batch:</span>
                  <strong style="color: var(--tcm-fg);">Enrolling for Upcoming Cohort</strong>
                </div>
              </div>

              <div style="font-size:12px;line-height:1.8;margin-bottom:20px"><p>Online course access is activated after payment verification, usually within 24 hours of complete payment details. Live classes follow the agreed batch timetable. No physical shipping applies.</p><a href="<?= base_url('/course-information') ?>">Fees &amp; delivery details</a> · <a href="<?= base_url('/refund-policy') ?>">Refund &amp; cancellation</a> · <a href="<?= base_url('/certificate-policy') ?>">Certificate requirements</a><p>Career assistance does not guarantee employment. <a href="<?= base_url('/internship-placement-policy') ?>">Read the career policy</a>.</p></div>
<!-- Actions -->
              <div style="display: flex; flex-direction: column; gap: 10px;">
                <button type="button" class="tcm-btn tcm-btn-primary tcm-trigger-course-enroll" data-slug="<?= e($course['slug']) ?>" data-title="<?= e($course['title']) ?>" data-price="<?= $price ?>" style="width: 100%; height: 50px;">
                  <span>Enroll Now &mdash; ₹<?= number_format($price, 0) ?></span>
                  <svg class="arrow-icon" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M1 7H13M13 7L7 1M13 7L7 13" stroke="currentColor" stroke-width="1.5" stroke-linecap="square"/></svg>
                </button>

                <a href="https://wa.me/919238695500?text=<?= rawurlencode('Hello TCM One! I have questions regarding enrollment for ' . $course['title']) ?>" target="_blank" rel="noopener" class="tcm-btn tcm-btn-secondary" style="width: 100%; justify-content: center; height: 44px;">
                  <i class="bi bi-whatsapp"></i> Chat on WhatsApp
                </a>

                <button type="button" class="tcm-btn tcm-btn-outline tcm-trigger-brochure" data-slug="<?= e($course['slug']) ?>" data-title="<?= e($course['title']) ?>" style="width: 100%; justify-content: center; height: 42px;">
                  <i class="bi bi-file-earmark-pdf"></i> Download Syllabus PDF
                </button>
              </div>

              <div style="margin-top: 20px; font-size: 12px; color: var(--tcm-muted); text-align: center;">
                <i class="bi bi-shield-check"></i> 100% verified curriculum &middot; Direct mentor access
              </div>
            </aside>
          </div>

        </div>
      </div>
    </section>

    <!-- Unified Sticky Bottom Enrollment Bar (Always Visible on Scroll) -->
    <div class="tcm-sticky-bottom-bar" role="complementary" aria-label="Course Enrollment Bar">
      <div class="tcm-container">
        <div class="tcm-sticky-bottom-inner">
          <div class="tcm-sticky-bottom-info d-none d-md-flex">
            <div class="tcm-sticky-bottom-title"><?= e($course['title']) ?></div>
          </div>
          <div class="tcm-sticky-bottom-right">
            <div class="tcm-sticky-bottom-pricing">
              <div class="tcm-sticky-bottom-price-row">
                <span class="tcm-sticky-bottom-price">₹<?= number_format($price, 0) ?></span>
                <?php if ($origPrice > $price): ?>
                  <span class="tcm-sticky-bottom-orig">₹<?= number_format($origPrice, 0) ?></span>
                <?php endif; ?>
              </div>
              <div class="tcm-sticky-bottom-sub">
                <span><?= e($duration) ?> Cohort</span>
              </div>
            </div>
            <div class="tcm-sticky-bottom-actions">
              <a href="https://wa.me/919238695500?text=<?= rawurlencode('Hello TCM One! I want to enquire about ' . $course['title']) ?>" target="_blank" rel="noopener" class="tcm-btn tcm-btn-secondary tcm-sticky-bottom-btn" aria-label="Enquire via WhatsApp">
                <i class="bi bi-whatsapp"></i>
                <span class="d-none d-sm-inline" style="margin-left: 2px;">WhatsApp</span>
              </a>
              <button type="button" class="tcm-btn tcm-btn-primary tcm-sticky-bottom-btn tcm-trigger-course-enroll" data-slug="<?= e($course['slug']) ?>" data-title="<?= e($course['title']) ?>" data-price="<?= $price ?>">
                <span>Enroll Now</span>
                <svg class="arrow-icon" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M1 7H13M13 7L7 1M13 7L7 13" stroke="currentColor" stroke-width="1.5" stroke-linecap="square"/></svg>
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

  </main>

  <!-- Footer -->
  <?php \TCM\Core\View::partial('partials.public-footer'); ?>

  <!-- Course Enrollment Modal -->
  <div class="tcm-modal-overlay" id="tcmCourseModalOverlay" role="dialog" aria-modal="true" aria-labelledby="tcmCourseModalTitle">
    <div class="tcm-modal">
      <button type="button" class="tcm-modal-close" id="tcmCourseCloseBtn" aria-label="Close modal">&times;</button>
      <div class="tcm-eyebrow" style="margin-bottom: 8px;">LIVE COHORT ENROLLMENT</div>
      <h3 id="tcmCourseModalTitle" class="tcm-h3" style="margin-bottom: 4px;">Enroll in <?= e($course['title']) ?></h3>
      <div id="tcmCoursePrice" style="font-size: 14.5px; font-weight: 700; color: var(--tcm-fg); margin-bottom: 16px;">
        Tuition Fee: ₹<?= number_format($price, 0) ?> (All Taxes Included)
      </div>
      <div id="tcmCourseFeedback" style="display: none;"></div>

      <form id="tcmCourseForm" novalidate>
        <input type="hidden" id="tcmCourseSlug" value="<?= e($course['slug']) ?>">
        <div class="tcm-form-group">
          <label for="tcmCourseName" class="tcm-form-label">Full Name *</label>
          <input type="text" id="tcmCourseName" class="tcm-form-input" placeholder="e.g. Rahul Sharma" required>
        </div>
        <div class="tcm-form-group">
          <label for="tcmCourseEmail" class="tcm-form-label">Email Address (for LMS &amp; Receipts) *</label>
          <input type="email" id="tcmCourseEmail" class="tcm-form-input" placeholder="e.g. rahul@example.com" required>
        </div>
        <div class="tcm-form-group">
          <label for="tcmCoursePhone" class="tcm-form-label">WhatsApp / Phone Number *</label>
          <input type="tel" id="tcmCoursePhone" class="tcm-form-input" placeholder="e.g. 9876543210" maxlength="14" required>
        </div>
        <button type="submit" class="tcm-btn tcm-btn-primary" id="tcmCourseSubmitBtn" style="width: 100%; height: 50px;">
          <span>Proceed to Pay &middot; ₹<?= number_format($price, 0) ?></span>
          <svg class="arrow-icon" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M1 7H13M13 7L7 1M13 7L7 13" stroke="currentColor" stroke-width="1.5" stroke-linecap="square"/></svg>
        </button>
      </form>

      <div style="margin-top: 16px; font-size: 11.5px; color: var(--tcm-muted); text-align: center; line-height: 1.5;">
        <i class="bi bi-shield-lock-fill" style="color: #10B981;"></i> 256-Bit SSL Encrypted &middot; Powered by PayU India<br>
        UPI (GPay / PhonePe / Paytm), Credit/Debit Cards, NetBanking supported.
      </div>
    </div>
  </div>

  <!-- Brochure Modal -->
  <div class="tcm-modal-overlay" id="tcmBrochureOverlay" role="dialog" aria-modal="true" aria-labelledby="tcmBrochureTitle">
    <div class="tcm-modal">
      <button type="button" class="tcm-modal-close" id="tcmBrochureCloseBtn" aria-label="Close modal">&times;</button>
      <div class="tcm-eyebrow" style="margin-bottom: 8px;">OFFICIAL CURRICULUM PDF</div>
      <h3 id="tcmBrochureTitle" class="tcm-h3" style="margin-bottom: 8px;">Download Course Brochure</h3>
      <div id="tcmBrochureFeedback" style="display: none;"></div>

      <form id="tcmBrochureForm" novalidate>
        <input type="hidden" id="tcmBrochureSlug" value="<?= e($course['slug']) ?>">
        <div class="tcm-form-group">
          <label for="tcmBrochureName" class="tcm-form-label">Full Name *</label>
          <input type="text" id="tcmBrochureName" class="tcm-form-input" placeholder="e.g. Rahul Sharma" required>
        </div>
        <div class="tcm-form-group">
          <label for="tcmBrochureEmail" class="tcm-form-label">Email Address *</label>
          <input type="email" id="tcmBrochureEmail" class="tcm-form-input" placeholder="e.g. rahul@example.com" required>
        </div>
        <div class="tcm-form-group">
          <label for="tcmBrochurePhone" class="tcm-form-label">WhatsApp / Mobile Number *</label>
          <input type="tel" id="tcmBrochurePhone" class="tcm-form-input" placeholder="e.g. 9876543210" maxlength="14" required>
        </div>
        <button type="submit" class="tcm-btn tcm-btn-primary" id="tcmBrochureSubmitBtn" style="width: 100%; height: 50px;">
          Download Brochure PDF
        </button>
      </form>
    </div>
  </div>

  <!-- Scripts -->
  <script src="<?= base_url('/assets/tcm-editorial.js?v=2.18') ?>"></script>
  <script src="<?= base_url('/assets/cai-assistant.js?v=2.18') ?>"></script>
</body>
</html>
