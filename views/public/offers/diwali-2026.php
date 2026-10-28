<?php
$h = static fn (string $value): string => htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
$schema = [
    '@context' => 'https://schema.org',
    '@graph' => [
        [
            '@type' => 'WebPage',
            '@id' => $canonical . '#webpage',
            'url' => $canonical,
            'name' => $title,
            'description' => $description,
            'inLanguage' => 'en-IN',
            'publisher' => ['@type' => 'EducationalOrganization', 'name' => 'TCM One', 'url' => $appUrl],
        ],
        ['@type' => 'BreadcrumbList', 'itemListElement' => [
            ['@type' => 'ListItem', 'position' => 1, 'name' => 'Home', 'item' => $appUrl . '/'],
            ['@type' => 'ListItem', 'position' => 2, 'name' => 'Courses', 'item' => $appUrl . '/courses'],
            ['@type' => 'ListItem', 'position' => 3, 'name' => 'Diwali Course Offer 2026', 'item' => $canonical],
        ]],
    ],
];
?>
<!doctype html>
<html lang="en-IN">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= $h($title) ?></title>
  <meta name="description" content="<?= $h($description) ?>">
  <meta name="robots" content="<?= $active ? 'index,follow,max-image-preview:large' : 'noindex,follow' ?>">
  <link rel="canonical" href="<?= $h($canonical) ?>">
  <link rel="alternate" hreflang="en-IN" href="<?= $h($canonical) ?>">
  <link rel="alternate" hreflang="x-default" href="<?= $h($canonical) ?>">
  <meta property="og:type" content="website">
  <meta property="og:site_name" content="TCM One">
  <meta property="og:url" content="<?= $h($canonical) ?>">
  <meta property="og:title" content="<?= $h($title) ?>">
  <meta property="og:description" content="<?= $h($description) ?>">
  <meta property="og:image" content="<?= $h($appUrl . '/assets/tcm-og-share.png') ?>">
  <meta property="og:locale" content="en_IN">
  <meta name="twitter:card" content="summary_large_image">
  <meta name="twitter:title" content="<?= $h($title) ?>">
  <meta name="twitter:description" content="<?= $h($description) ?>">
  <meta name="twitter:image" content="<?= $h($appUrl . '/assets/tcm-og-share.png') ?>">
  <script type="application/ld+json"><?= json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?></script>
  <link rel="icon" type="image/svg+xml" href="<?= $h($appUrl . '/favicon.svg') ?>">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link rel="stylesheet" href="<?= $h($appUrl . '/assets/tcm-design-system.css?v=2.19') ?>">
  <link rel="stylesheet" href="<?= $h($appUrl . '/assets/tcm-editorial.css?v=2.19') ?>">
  <link rel="stylesheet" href="<?= $h($appUrl . '/assets/tcm-footer.css?v=1') ?>">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Instrument+Serif:ital@0;1&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="<?= $h($appUrl . '/assets/diwali-2026.css?v=1') ?>">
</head>
<body>
<?php \TCM\Core\View::partial('partials.public-header'); ?>
<main id="mainContent" class="wrap offer-main">
  <nav class="crumb" aria-label="Breadcrumb"><a href="<?= $h($appUrl . '/') ?>">Home</a> / <a href="<?= $h($appUrl . '/courses') ?>">Courses</a> / Diwali offer 2026</nav>
  <section class="hero">
    <div class="hero-content">
    <?php if ($active): ?>
      <span class="kicker"><span aria-hidden="true">✦</span> Diwali 2026 · Limited-time learning offer</span>
      <h1>Make this Diwali the start of <em>what’s next.</em></h1>
      <p>Build practical skills in Full Stack, Data Analytics, DSA, AI and more with TCM One’s live online programmes. The Diwali offer gives 60% off eligible courses whose regular fee is above ₹8,000, plus selected special prices.</p>
      <div class="hero-actions"><time class="date" datetime="<?= $h($endsAt) ?>"><span aria-hidden="true">◷</span> Ends 10 November 2026 · IST</time>
      <a class="cta" href="<?= $h($appUrl . '/courses') ?>">Explore courses <span aria-hidden="true">→</span></a></div>
    <?php else: ?>
      <span class="kicker"><span aria-hidden="true">✦</span> Diwali 2026 offer</span>
      <h1>This offer has ended.<br><em>Your next step hasn’t.</em></h1>
      <p>Explore current online course fees, schedules and learning paths in Full Stack, Data Analytics, DSA, AI and more.</p>
      <div class="hero-actions"><a class="cta" href="<?= $h($appUrl . '/courses') ?>">Browse current courses <span aria-hidden="true">→</span></a></div>
    <?php endif; ?>
    </div>
    <?php if ($active): ?><aside class="hero-note" aria-label="Offer summary"><span class="hero-note-icon" aria-hidden="true">✺</span><span class="hero-note-label">Save on your next skill</span><strong>Up to <b>60%</b><br>off</strong><span class="hero-note-caption">On eligible courses above ₹8,000</span></aside><?php endif; ?>
  </section>

  <?php if ($active): ?>
  <section class="section" aria-labelledby="eligible-courses">
    <span class="section-kicker">Choose your learning path</span>
    <h2 id="eligible-courses">A special price for your next skill.</h2>
    <p class="muted">Listed fees are course-specific. Eligible courses above ₹8,000 receive 60% off their regular fee; selected courses have a special Diwali price. Check each course page for syllabus, schedule and current availability.</p>
    <?php if ($courses): ?>
      <div class="course-list">
        <?php foreach ($courses as $course): ?>
          <?php $isSixtyOff = (float) $course['regular_price'] > \TCM\Services\DiwaliOffer::MINIMUM_REGULAR_PRICE; ?>
          <article class="course">
            <div class="course-topline">
            <span class="category"><?= $h((string) ($course['category_name'] ?? 'Tech course')) ?></span>
            <span class="course-offer-tag"><?= $isSixtyOff ? 'DIWALI · 60% OFF' : 'DIWALI SPECIAL' ?></span>
            </div>
            <h3><a href="<?= $h($appUrl . '/courses/' . rawurlencode((string) $course['slug'])) ?>"><?= $h((string) $course['title']) ?></a></h3>
            <div class="price"><strong class="offer-price">₹<?= number_format((float) $course['offer_price'], 0) ?></strong><span class="regular-price">₹<?= number_format((float) $course['regular_price'], 0) ?></span></div>
            <div class="course-bottom"><span class="saving"><?= $isSixtyOff ? 'You save 60% with full payment' : 'Limited-time Diwali price' ?></span>
            <a class="course-link" href="<?= $h($appUrl . '/courses/' . rawurlencode((string) $course['slug'])) ?>">View course <span aria-hidden="true">→</span></a></div>
          </article>
        <?php endforeach; ?>
      </div>
    <?php else: ?>
      <p class="muted">Course offers are being updated. <a href="<?= $h($appUrl . '/courses') ?>">Browse the course catalogue</a> or contact admissions for current fees.</p>
    <?php endif; ?>
  </section>

  <section class="section" aria-labelledby="offer-terms">
    <h2 id="offer-terms">Diwali offer dates and payment terms</h2>
    <p>The offer ends at 11:59 PM IST on 10 November 2026. During the offer, eligible discounted courses require full payment; EMI and instalment plans are unavailable. At expiry, course prices return automatically to their regular rates.</p>
  </section>

  <section class="section" aria-labelledby="offer-faq">
    <h2 id="offer-faq">Diwali course offer questions</h2>
    <article class="faq"><h3>Which courses get 60% off?</h3><p>Published courses with a regular fee above ₹8,000 are eligible for 60% off while the offer is active. Selected lower-priced courses may have a separate Diwali special price.</p></article>
    <article class="faq"><h3>Can I pay by EMI during the offer?</h3><p>No. EMI and instalments are unavailable for courses on the Diwali offer; the offer price requires full payment.</p></article>
    <article class="faq"><h3>When does the Diwali offer end?</h3><p>The offer is available through 10 November 2026, India Standard Time. After the countdown ends, the offer price is removed automatically.</p></article>
  </section>
  <?php endif; ?>

  <section class="section" aria-labelledby="online-cities">
    <h2 id="online-cities">Join online from cities across India</h2>
    <p class="muted">TCM One’s programmes are online. These city pages explain remote availability and do not imply a local campus.</p>
    <nav class="cities" aria-label="Online learning locations">
      <?php foreach (['bengaluru' => 'Bengaluru', 'mumbai' => 'Mumbai', 'delhi-ncr' => 'Delhi NCR', 'hyderabad' => 'Hyderabad', 'pune' => 'Pune', 'chennai' => 'Chennai', 'kolkata' => 'Kolkata', 'ahmedabad' => 'Ahmedabad', 'bilaspur' => 'Bilaspur', 'nagpur' => 'Nagpur', 'balaghat' => 'Balaghat', 'raipur' => 'Raipur', 'durg' => 'Durg'] as $citySlug => $cityName): ?>
        <a href="<?= $h($appUrl . '/locations/' . $citySlug) ?>">Online coding classes in <?= $h($cityName) ?></a>
      <?php endforeach; ?>
    </nav>
  </section>
</main>
<?php \TCM\Core\View::partial('partials.public-footer'); ?>
<script src="<?= $h($appUrl . '/assets/tcm-editorial.js?v=2.22') ?>"></script>
</body>
</html>
