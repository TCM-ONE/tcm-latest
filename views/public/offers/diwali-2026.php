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
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Instrument+Serif:ital@0;1&display=swap" rel="stylesheet">
  <style>
    :root{color-scheme:light;--ink:#182337;--muted:#596579;--line:#e5e7eb;--paper:#fbf9f5;--saffron:#ffdc75;--wine:#671518}*{box-sizing:border-box}body{margin:0;background:var(--paper);color:var(--ink);font:16px/1.6 Inter,Arial,sans-serif}a{color:inherit}.top{padding:16px max(22px,calc((100% - 1120px)/2));display:flex;align-items:center;justify-content:space-between;gap:20px;background:#fff;border-bottom:1px solid var(--line)}.brand{font-size:20px;font-weight:800;text-decoration:none}.nav{display:flex;gap:20px;flex-wrap:wrap}.nav a{font-size:14px;font-weight:600;text-decoration:none}.wrap{width:min(100% - 40px,1000px);margin:auto}.crumb{padding-top:24px;color:var(--muted);font-size:13px}.crumb a{color:inherit}.hero{margin:24px 0 30px;padding:clamp(24px,5vw,52px);border-radius:18px;background:linear-gradient(120deg,#4d1013,#811e17 65%,#3b101b);color:white;box-shadow:0 16px 40px #42131922}.kicker{color:var(--saffron);font-size:12px;font-weight:800;letter-spacing:.1em;text-transform:uppercase}.hero h1{max-width:740px;margin:12px 0;font:clamp(36px,6vw,62px)/1.05 'Instrument Serif',Georgia,serif}.hero p{max-width:720px;color:#f8e9e6;font-size:17px}.date{display:inline-block;margin:4px 0 20px;padding:7px 12px;border:1px solid #ffffff4d;border-radius:999px;color:#fff4d5;font-size:13px;font-weight:700}.cta{display:inline-flex;padding:12px 18px;border-radius:8px;background:var(--saffron);color:#251313;text-decoration:none;font-weight:800}.section{padding:26px 0;border-bottom:1px solid var(--line)}h2{margin:0 0 12px;font:32px/1.2 'Instrument Serif',Georgia,serif}.muted{color:var(--muted)}.course-list{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:12px;margin:18px 0}.course{padding:17px;background:#fff;border:1px solid var(--line);border-radius:12px}.course h3{margin:0 0 6px;font-size:16px;line-height:1.35}.course h3 a{text-decoration:none}.course h3 a:hover{text-decoration:underline}.category{color:var(--muted);font-size:12px}.price{display:flex;align-items:baseline;flex-wrap:wrap;gap:8px;margin-top:12px}.offer-price{color:#9b241d;font-size:22px;font-weight:800}.regular-price{color:#7b8190;font-size:14px;text-decoration:line-through}.saving{color:#276747;font-size:12px;font-weight:700}.expired{padding:22px;border:1px solid var(--line);border-radius:12px;background:white}.faq{padding:14px 0;border-top:1px solid var(--line)}.faq h3{margin:0 0 5px;font-size:16px}.faq p{margin:0;color:var(--muted)}.cities{display:flex;gap:9px;flex-wrap:wrap}.cities a{padding:7px 11px;border:1px solid var(--line);border-radius:99px;background:white;text-decoration:none;font-size:13px}.foot{padding:28px 0 45px;color:var(--muted);font-size:13px}.foot nav{display:flex;gap:16px;flex-wrap:wrap;margin-top:8px}.foot nav a{text-decoration:none}@media(max-width:640px){.top{align-items:flex-start;flex-direction:column}.nav{gap:12px}.wrap{width:min(100% - 28px,1000px)}.course-list{grid-template-columns:1fr}.hero{margin-top:18px}.hero p{font-size:15px}h2{font-size:28px}}
  </style>
</head>
<body>
<header class="top"><a class="brand" href="<?= $h($appUrl . '/') ?>">TCM One</a><nav class="nav" aria-label="Main navigation"><a href="<?= $h($appUrl . '/courses') ?>">Courses</a><a href="<?= $h($appUrl . '/workshops') ?>">Workshops</a><a href="<?= $h($appUrl . '/locations') ?>">Locations</a><a href="<?= $h($appUrl . '/contact') ?>">Contact</a></nav></header>
<div class="wrap"><nav class="crumb" aria-label="Breadcrumb"><a href="<?= $h($appUrl . '/') ?>">Home</a> / <a href="<?= $h($appUrl . '/courses') ?>">Courses</a> / Diwali offer 2026</nav>
<main>
  <section class="hero">
    <?php if ($active): ?>
      <span class="kicker">Diwali 2026 · Online tech courses</span>
      <h1>Diwali course offers: save up to 60% on tech learning</h1>
      <p>Build practical skills in Full Stack, Data Analytics, DSA, AI and more with TCM One’s live online programmes. The Diwali offer gives 60% off eligible courses whose regular fee is above ₹8,000, plus selected special prices.</p>
      <time class="date" datetime="<?= $h($endsAt) ?>">Offer valid through 10 November 2026 · India Standard Time</time><br>
      <a class="cta" href="<?= $h($appUrl . '/courses') ?>">Compare all courses and fees →</a>
    <?php else: ?>
      <span class="kicker">Diwali 2026 offer</span>
      <h1>The Diwali course offer has ended</h1>
      <p>Explore current online course fees, schedules and learning paths in Full Stack, Data Analytics, DSA, AI and more.</p>
      <a class="cta" href="<?= $h($appUrl . '/courses') ?>">Browse current courses →</a>
    <?php endif; ?>
  </section>

  <?php if ($active): ?>
  <section class="section" aria-labelledby="eligible-courses">
    <h2 id="eligible-courses">Courses included in this Diwali offer</h2>
    <p class="muted">Listed fees are course-specific. Eligible courses above ₹8,000 receive 60% off their regular fee; selected courses have a special Diwali price. Check each course page for syllabus, schedule and current availability.</p>
    <?php if ($courses): ?>
      <div class="course-list">
        <?php foreach ($courses as $course): ?>
          <?php $isSixtyOff = (float) $course['regular_price'] > \TCM\Services\DiwaliOffer::MINIMUM_REGULAR_PRICE; ?>
          <article class="course">
            <span class="category"><?= $h((string) ($course['category_name'] ?? 'Tech course')) ?></span>
            <h3><a href="<?= $h($appUrl . '/courses/' . rawurlencode((string) $course['slug'])) ?>"><?= $h((string) $course['title']) ?></a></h3>
            <div class="price"><strong class="offer-price">₹<?= number_format((float) $course['offer_price'], 0) ?></strong><span class="regular-price">₹<?= number_format((float) $course['regular_price'], 0) ?></span><span class="saving"><?= $isSixtyOff ? '60% off' : 'Diwali special price' ?></span></div>
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
<footer class="foot">© <?= date('Y') ?> TCM One · Live online technology education.<nav><a href="<?= $h($appUrl . '/courses') ?>">All courses</a><a href="<?= $h($appUrl . '/course-information') ?>">Fees and course information</a><a href="<?= $h($appUrl . '/contact') ?>">Contact admissions</a></nav></footer></div>
</body>
</html>
