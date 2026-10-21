<?php
$h = static fn (string $value): string => htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
$itemListElements = [];
foreach ($guides as $slug => $guide) {
    $itemListElements[] = [
        '@type' => 'ListItem',
        'position' => count($itemListElements) + 1,
        'name' => $guide['name'] . ' Tutorial',
        'url' => $appUrl . '/learn/' . $slug,
    ];
}
$listSchema = [
    '@context' => 'https://schema.org',
    '@type' => 'CollectionPage',
    'name' => $title,
    'description' => $description,
    'url' => $canonical,
    'inLanguage' => 'en-IN',
    'mainEntity' => [
        '@type' => 'ItemList',
        'itemListElement' => $itemListElements,
    ],
];
$breadcrumbSchema = [
    '@context' => 'https://schema.org',
    '@type' => 'BreadcrumbList',
    'itemListElement' => [
        ['@type' => 'ListItem', 'position' => 1, 'name' => 'Home', 'item' => $appUrl . '/'],
        ['@type' => 'ListItem', 'position' => 2, 'name' => 'Learn', 'item' => $canonical],
    ],
];
?>
<!doctype html>
<html lang="en-IN">
<head>
  <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= $h($title) ?></title>
  <meta name="description" content="<?= $h($description) ?>">
  <meta name="robots" content="index,follow,max-image-preview:large">
  <link rel="canonical" href="<?= $h($canonical) ?>">
  <link rel="alternate" hreflang="en-IN" href="<?= $h($canonical) ?>"><link rel="alternate" hreflang="x-default" href="<?= $h($canonical) ?>">
  <meta property="og:type" content="website"><meta property="og:site_name" content="TCM One"><meta property="og:url" content="<?= $h($canonical) ?>"><meta property="og:title" content="<?= $h($title) ?>"><meta property="og:description" content="<?= $h($description) ?>"><meta property="og:image" content="<?= $h($ogImage) ?>"><meta property="og:locale" content="en_IN">
  <meta name="twitter:card" content="summary_large_image"><meta name="twitter:title" content="<?= $h($title) ?>"><meta name="twitter:description" content="<?= $h($description) ?>"><meta name="twitter:image" content="<?= $h($ogImage) ?>">
  <meta name="theme-color" content="#101b2c"><link rel="icon" href="<?= $h(base_url('/favicon.svg')) ?>" type="image/svg+xml">
  <link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin><link href="https://fonts.googleapis.com/css2?family=Instrument+Serif:ital@0;1&family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet"><link rel="stylesheet" href="<?= base_url('/assets/tcm-design-system.css?v=2.18') ?>"><link rel="stylesheet" href="<?= base_url('/assets/tcm-editorial.css?v=2.18') ?>"><link rel="stylesheet" href="<?= base_url('/assets/tcm-footer.css?v=1') ?>"><link rel="stylesheet" href="<?= base_url('/assets/tcm-learn.css?v=1') ?>">
  <script type="application/ld+json"><?= json_encode([$listSchema, $breadcrumbSchema], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?></script>

</head>
<body>
<a class="skip" href="#mainContent">Skip to content</a>
<?php \TCM\Core\View::partial('partials.public-header'); ?>
<div class="learn-wrap"><nav class="crumb" aria-label="Breadcrumb"><a href="<?= $h(base_url('/')) ?>">Home</a> / Learn</nav>
<main id="mainContent"><section class="hero"><div class="eyebrow">TCM ONE · LEARNING LIBRARY · UPDATED 21 OCTOBER 2026</div><h1 class="tcm-h1">Free coding tutorials: Python, Java &amp; web development</h1><p>Learn Python, Java, HTML, CSS, Sass/SCSS and Bootstrap from the first steps to advanced topics. Every guide uses plain-language explanations, clear code examples, practice tasks and a final project—so you can learn a concept and put it to work.</p><div class="hero-actions"><a class="tcm-btn tcm-btn-primary" href="#guides">Choose a learning guide ↓</a><a class="tcm-btn tcm-btn-secondary" href="<?= $h(base_url('/courses')) ?>">Explore courses</a></div><div class="quick-facts"><span class="pill">6 structured guides</span><span class="pill">Beginner to advanced</span><span class="pill">Examples + practice in every module</span><span class="pill">Free to read</span></div></section>
<div class="version-note"><strong>Version note:</strong> Version references were checked against official standards and release pages on 21 October 2026. Web standards such as HTML and CSS evolve continuously; use each guide’s reference links to check current support.</div>
<section class="path-box" aria-labelledby="web-path"><h2 id="web-path">A practical path into web development</h2><p>Start with page structure, make it responsive and accessible, then add a framework or a CSS authoring workflow when the project needs one.</p><div class="path-links"><a href="<?= $h(base_url('/learn/html')) ?>">1. HTML foundations</a><a href="<?= $h(base_url('/learn/css')) ?>">2. CSS layout</a><a href="<?= $h(base_url('/learn/bootstrap')) ?>">3. Bootstrap components</a><a href="<?= $h(base_url('/learn/scss')) ?>">4. SCSS architecture</a></div></section>
<section id="guides" aria-labelledby="guides-title"><div class="section-head"><div class="eyebrow">PICK UP WHERE YOU ARE</div><h2 id="guides-title">Choose a guide</h2><p>Each guide is a standalone reference. Follow the modules in order or jump straight to the topic you need.</p></div><div class="guide-grid">
<?php foreach ($guides as $slug => $guide): ?>
  <article class="guide-card"><div class="guide-kicker"><?= $h($guide['category']) ?></div><h3><?= $h($guide['name']) ?></h3><p><?= $h($guide['summary']) ?></p><div class="guide-meta"><span><?= $h($guide['version']) ?></span><span><?= $h($guide['duration']) ?></span></div><a href="<?= $h(base_url('/learn/' . $slug)) ?>">Open the <?= $h($guide['name']) ?> guide →</a></article>
<?php endforeach; ?>
</div></section>
<section class="path-box" aria-labelledby="learning-help"><h2 id="learning-help">What helps you learn faster?</h2><p>Read the explanation, type the example yourself, change one thing and predict what will happen. Then complete the practice task before moving on. Debugging your own small mistakes is part of learning, not a detour.</p><div class="path-links"><a href="<?= $h(base_url('/courses')) ?>">See instructor-led courses</a><a href="<?= $h(base_url('/contact')) ?>">Ask the learning team</a></div></section>
</main></div><?php \TCM\Core\View::partial('partials.public-footer'); ?></body></html>
