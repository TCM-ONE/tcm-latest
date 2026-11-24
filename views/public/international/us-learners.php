<!DOCTYPE html>
<html lang="en-US">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="app-base" content="<?= e(base_url('/')) ?>">
  <title><?= e($title) ?></title>
  <meta name="description" content="<?= e($description) ?>">
  <meta name="robots" content="index, follow">
  <link rel="canonical" href="<?= e($canonical) ?>">

  <meta property="og:type" content="website">
  <meta property="og:site_name" content="TCM One">
  <meta property="og:url" content="<?= e($canonical) ?>">
  <meta property="og:title" content="<?= e($title) ?>">
  <meta property="og:description" content="<?= e($description) ?>">
  <meta property="og:image" content="<?= e($ogImage) ?>">
  <meta property="og:locale" content="en_US">
  <meta name="twitter:card" content="summary_large_image">
  <meta name="twitter:title" content="<?= e($title) ?>">
  <meta name="twitter:description" content="<?= e($description) ?>">
  <meta name="twitter:image" content="<?= e($ogImage) ?>">

  <?php
    $pageSchema = [
      '@context' => 'https://schema.org',
      '@type' => 'WebPage',
      '@id' => $canonical . '#webpage',
      'name' => $title,
      'description' => $description,
      'url' => $canonical,
      'inLanguage' => 'en-US',
      'isPartOf' => ['@type' => 'WebSite', 'name' => 'TCM One', 'url' => $appUrl],
    ];
    $breadcrumbSchema = [
      '@context' => 'https://schema.org',
      '@type' => 'BreadcrumbList',
      'itemListElement' => [
        ['@type' => 'ListItem', 'position' => 1, 'name' => 'Home', 'item' => $appUrl . '/'],
        ['@type' => 'ListItem', 'position' => 2, 'name' => 'Courses', 'item' => $appUrl . '/courses'],
        ['@type' => 'ListItem', 'position' => 3, 'name' => 'U.S. Learners', 'item' => $canonical],
      ],
    ];
  ?>
  <script type="application/ld+json"><?= json_encode($pageSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?></script>
  <script type="application/ld+json"><?= json_encode($breadcrumbSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?></script>

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Instrument+Serif:ital@0;1&family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link rel="stylesheet" href="<?= base_url('/assets/tcm-design-system.css?v=2.18') ?>">
  <link rel="stylesheet" href="<?= base_url('/assets/tcm-editorial.css?v=2.22') ?>">
  <link rel="stylesheet" href="<?= base_url('/assets/cai-assistant.css?v=2.18') ?>">
  <link rel="stylesheet" href="<?= base_url('/assets/tcm-footer.css?v=1') ?>">
  <style>
    .us-learning-page{padding:clamp(42px,7vw,88px) 0 76px}
    .us-learning-wrap{width:min(1080px,calc(100% - 36px));margin:auto}
    .us-learning-hero{max-width:820px}
    .us-learning-hero h1{font-size:clamp(2.5rem,6.5vw,5.5rem);line-height:1.02;letter-spacing:-.045em;margin:12px 0 18px}
    .us-learning-hero p{max-width:740px;font-size:clamp(1rem,2vw,1.2rem);line-height:1.7;color:var(--tcm-muted,#686762)}
    .us-learning-actions{display:flex;flex-wrap:wrap;gap:10px;margin-top:24px}
    .us-learning-note{max-width:790px;margin:20px 0 0;padding-left:14px;border-left:2px solid var(--tcm-border,#deddd8);font-size:.92rem;line-height:1.7;color:var(--tcm-muted,#686762)}
    .us-learning-section{margin-top:56px}
    .us-learning-section h2{font-size:clamp(1.55rem,3vw,2.2rem);letter-spacing:-.025em;margin:8px 0 12px}
    .us-learning-section>p{max-width:760px;color:var(--tcm-muted,#686762);line-height:1.75}
    .us-learning-grid{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:12px;margin-top:20px}
    .us-learning-card{display:block;padding:20px;border:1px solid var(--tcm-border,#deddd8);color:inherit;text-decoration:none;background:#fff}
    .us-learning-card:hover{border-color:#a41f2d}
    .us-learning-card h3{font-size:1rem;margin:0 0 8px}
    .us-learning-card p{font-size:.88rem;line-height:1.6;color:var(--tcm-muted,#686762);margin:0}
    .us-learning-steps{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:18px;margin:20px 0 0;padding:0;list-style:none;counter-reset:usstep}
    .us-learning-steps li{counter-increment:usstep;border-top:1px solid var(--tcm-border,#deddd8);padding-top:14px;line-height:1.65}
    .us-learning-steps li:before{content:'0' counter(usstep);display:block;color:var(--tcm-muted,#686762);font-size:.75rem;font-weight:700;letter-spacing:.1em;margin-bottom:8px}
    .us-learning-faq{max-width:820px;margin-top:18px;border-top:1px solid var(--tcm-border,#deddd8)}
    .us-learning-faq details{border-bottom:1px solid var(--tcm-border,#deddd8);padding:16px 0}
    .us-learning-faq summary{font-weight:650;cursor:pointer}
    .us-learning-faq p{max-width:750px;color:var(--tcm-muted,#686762);line-height:1.7;margin:12px 0 0}
    @media(max-width:720px){.us-learning-grid{grid-template-columns:1fr 1fr}.us-learning-steps{grid-template-columns:1fr;gap:12px}}
    @media(max-width:480px){.us-learning-wrap{width:calc(100% - 30px)}.us-learning-grid{grid-template-columns:1fr}}
  </style>
</head>
<body>
  <?php \TCM\Core\View::partial('partials.public-header'); ?>

  <main id="mainContent" class="us-learning-page">
    <div class="us-learning-wrap">
      <nav aria-label="Breadcrumb" style="font-size:.84rem;color:var(--tcm-muted,#686762);margin-bottom:26px">
        <a href="<?= e(base_url('/')) ?>">Home</a> / <a href="<?= e(base_url('/courses')) ?>">Courses</a> / U.S. learners
      </nav>

      <header class="us-learning-hero">
        <div class="tcm-eyebrow">ONLINE LEARNING FOR THE UNITED STATES</div>
        <h1>Mentor-led online coding courses for U.S. learners</h1>
        <p>Explore practical online courses in Full Stack development, Python, AI, data analytics, cloud and programming foundations. Review the curriculum and current cohort details before choosing a course.</p>
        <div class="us-learning-actions">
          <a class="tcm-btn tcm-btn-primary" href="<?= e(base_url('/courses')) ?>">Browse courses <i class="bi bi-arrow-right" aria-hidden="true"></i></a>
          <a class="tcm-btn tcm-btn-secondary" href="<?= e(base_url('/contact')) ?>">Ask about schedule and payment</a>
        </div>
        <p class="us-learning-note">TCM One is an India-based online education provider, not a U.S. campus or in-person U.S. training institute. Online access is remote; ask admissions to confirm cohort timings for your U.S. time zone.</p>
      </header>

      <section class="us-learning-section" aria-labelledby="us-course-paths">
        <div class="tcm-eyebrow">CHOOSE A LEARNING PATH</div>
        <h2 id="us-course-paths">Online programming courses and technology tracks</h2>
        <p>Start with the subject you want to learn. Course pages show the available curriculum, prerequisites, delivery format, fee and schedule.</p>
        <div class="us-learning-grid">
          <a class="us-learning-card" href="<?= e(base_url('/courses/tracks/web-mobile-development')) ?>"><h3>Full Stack &amp; web development</h3><p>Explore frontend and full-stack web development courses.</p></a>
          <a class="us-learning-card" href="<?= e(base_url('/courses/tracks/programming-foundations')) ?>"><h3>Python &amp; programming foundations</h3><p>Build core programming skills and learn practical tools.</p></a>
          <a class="us-learning-card" href="<?= e(base_url('/courses/tracks/ai-automation')) ?>"><h3>Artificial intelligence &amp; automation</h3><p>Compare courses covering AI tools, machine learning and automation.</p></a>
          <a class="us-learning-card" href="<?= e(base_url('/courses/tracks/data-analytics')) ?>"><h3>Data analytics &amp; data science</h3><p>Review learning paths in analytics, SQL, Python and data science.</p></a>
          <a class="us-learning-card" href="<?= e(base_url('/courses/tracks/cloud-devops')) ?>"><h3>Cloud &amp; DevOps</h3><p>Explore cloud, deployment and software delivery fundamentals.</p></a>
          <a class="us-learning-card" href="<?= e(base_url('/courses/tracks/career-dsa')) ?>"><h3>DSA &amp; career preparation</h3><p>Find problem-solving and interview-preparation courses.</p></a>
        </div>
      </section>

      <section class="us-learning-section" aria-labelledby="us-how-to-start">
        <div class="tcm-eyebrow">HOW TO GET STARTED</div>
        <h2 id="us-how-to-start">Check the course, cohort time and payment details</h2>
        <ol class="us-learning-steps">
          <li><strong>Compare the course pages.</strong> Check the curriculum, prerequisites and teaching format for the subject you want to learn.</li>
          <li><strong>Confirm the class time.</strong> Cohorts are coordinated from India. Contact admissions to verify the current schedule in Eastern, Central, Mountain or Pacific Time.</li>
          <li><strong>Confirm the amount and payment method.</strong> Displayed USD prices are estimates; checkout and the final charge are in INR. Ask admissions which payment methods work for you before enrolling.</li>
        </ol>
      </section>

      <section class="us-learning-section" aria-labelledby="us-faq">
        <div class="tcm-eyebrow">COMMON QUESTIONS</div>
        <h2 id="us-faq">Questions from U.S. learners</h2>
        <div class="us-learning-faq">
          <details><summary>Can I take a TCM One course from the United States?</summary><p>Courses are delivered online, so you can review and join remotely. TCM One is based in India and does not operate a U.S. campus. Confirm the format and cohort availability on the course page.</p></details>
          <details><summary>Are live class times scheduled for U.S. time zones?</summary><p>Schedules vary by cohort. Contact admissions before enrolling to confirm the current class time converted to your local U.S. time zone.</p></details>
          <details><summary>Do I pay the displayed USD amount in dollars?</summary><p>No. The USD figure is an estimate based on the INR course fee. Checkout and the final charge remain in INR. Contact admissions to confirm an available payment method for your situation.</p></details>
          <details><summary>Which online coding courses can I compare?</summary><p>The catalog includes courses and tracks in Full Stack development, Python, AI, data analytics, cloud and programming foundations. Availability and details can change by cohort, so check the current course pages.</p></details>
        </div>
      </section>

      <section class="us-learning-section" aria-label="Browse TCM One courses">
        <h2>Compare the current online courses</h2>
        <p>Review available courses and contact admissions if you need help checking a course schedule from the United States.</p>
        <a class="tcm-btn tcm-btn-primary" href="<?= e(base_url('/courses')) ?>">View the course catalog <i class="bi bi-arrow-right" aria-hidden="true"></i></a>
      </section>
    </div>
  </main>

  <?php \TCM\Core\View::partial('partials.public-footer'); ?>
  <script src="<?= base_url('/assets/cai-assistant.js?v=2.18') ?>"></script>
</body>
</html>
