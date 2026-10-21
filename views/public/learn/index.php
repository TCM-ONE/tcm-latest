<?php
$h = static fn (string $value): string => htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
$listSchema = [
    '@context' => 'https://schema.org',
    '@type' => 'CollectionPage',
    'name' => $title,
    'description' => $description,
    'url' => $canonical,
    'inLanguage' => 'en-IN',
    'mainEntity' => [
        '@type' => 'ItemList',
        'itemListElement' => array_values(array_map(static fn (string $slug, array $guide, int $index): array => [
            '@type' => 'ListItem',
            'position' => $index + 1,
            'name' => $guide['name'] . ' Tutorial',
            'url' => $appUrl . '/learn/' . $slug,
        ], array_keys($guides), $guides, array_keys($guides))),
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
  <link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin><link href="https://fonts.googleapis.com/css2?family=DM+Mono:wght@400;500&family=DM+Sans:wght@400;500;600;700;800&family=Instrument+Serif:ital@0;1&display=swap" rel="stylesheet">
  <script type="application/ld+json"><?= json_encode([$listSchema, $breadcrumbSchema], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?></script>
  <style>
    :root{color-scheme:light;--ink:#17253a;--muted:#5b6d80;--line:#dbe4e8;--accent:#c5f467;--green:#1d624c;--paper:#f7f8f5;--white:#fff;--mono:'DM Mono',monospace;--sans:'DM Sans',Arial,sans-serif;--serif:'Instrument Serif',Georgia,serif}*{box-sizing:border-box}html{scroll-behavior:smooth}body{margin:0;background:var(--paper);color:var(--ink);font:16px/1.65 var(--sans)}a{color:var(--green)}a:hover{text-decoration-thickness:2px}.skip{position:absolute;left:-10000px;top:0;background:#fff;padding:12px;z-index:100}.skip:focus{left:12px}.learn-header{background:var(--ink);color:#fff;padding:18px max(24px,calc((100% - 1180px)/2));display:flex;justify-content:space-between;align-items:center;gap:18px}.learn-brand{color:#fff;text-decoration:none;font-size:18px;font-weight:800;letter-spacing:.04em}.learn-brand b{color:var(--accent)}.learn-header nav{display:flex;gap:22px;flex-wrap:wrap}.learn-header nav a{color:#e0e8ea;font-size:14px;font-weight:600;text-decoration:none}.learn-wrap{max-width:1180px;margin:auto;padding:0 24px}.crumb{padding-top:26px;color:var(--muted);font-size:13px}.crumb a{color:inherit}.hero{padding:54px 0 50px;max-width:890px}.eyebrow{font:500 12px/1.4 var(--mono);text-transform:uppercase;letter-spacing:.1em;color:var(--green)}h1{font:clamp(44px,7vw,76px)/.98 var(--serif);letter-spacing:-.025em;margin:16px 0}.hero p{max-width:760px;font-size:19px;color:var(--muted);margin:0}.hero-actions{display:flex;gap:12px;flex-wrap:wrap;margin-top:25px}.btn{display:inline-flex;align-items:center;justify-content:center;border-radius:8px;padding:12px 17px;text-decoration:none;font-weight:700;background:var(--ink);color:#fff}.btn.secondary{background:#fff;color:var(--ink);border:1px solid var(--line)}.quick-facts{display:flex;gap:10px;flex-wrap:wrap;margin-top:28px}.pill{border:1px solid var(--line);background:#fff;border-radius:99px;padding:7px 12px;font-size:12px;color:#3d5265}.section-head{border-top:1px solid var(--line);padding:35px 0 20px}.section-head h2{font:36px/1.12 var(--serif);margin:0 0 8px}.section-head p{margin:0;color:var(--muted);max-width:740px}.guide-grid{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:16px;padding-bottom:48px}.guide-card{display:flex;flex-direction:column;background:var(--white);border:1px solid var(--line);border-radius:14px;padding:22px;min-height:290px;transition:transform .18s ease,border-color .18s ease}.guide-card:hover{transform:translateY(-2px);border-color:#9eb6ab}.guide-kicker{font:12px/1.5 var(--mono);color:var(--green);text-transform:uppercase;letter-spacing:.07em}.guide-card h3{font:30px/1.1 var(--serif);margin:10px 0 8px}.guide-card p{font-size:14px;line-height:1.65;color:var(--muted);margin:0 0 14px}.guide-meta{display:flex;gap:7px;flex-wrap:wrap;margin-top:auto;margin-bottom:15px}.guide-meta span{font:11px/1.5 var(--mono);color:#495d70;background:#f1f4f0;border-radius:5px;padding:4px 7px}.guide-card a{font-weight:700;text-decoration:none}.path-box{background:#edf2eb;border:1px solid #d5e2d4;border-radius:16px;padding:26px;margin-bottom:50px}.path-box h2{font:30px/1.1 var(--serif);margin:0 0 8px}.path-box p{margin:0 0 12px;color:#485c69}.path-links{display:flex;gap:9px;flex-wrap:wrap}.path-links a{border:1px solid #c7d4ca;border-radius:7px;background:#fff;padding:8px 11px;text-decoration:none;font-weight:700;font-size:13px}.version-note{border-left:3px solid #8daa63;padding:12px 16px;background:#fff;margin:0 0 22px;color:#526373;font-size:14px}.learn-foot{border-top:1px solid var(--line);padding:24px 0 50px;color:var(--muted);font-size:13px}.learn-foot nav{display:flex;gap:18px;flex-wrap:wrap;margin-top:8px}.learn-foot a{color:inherit}@media(max-width:900px){.guide-grid{grid-template-columns:repeat(2,minmax(0,1fr))}}@media(max-width:620px){.learn-header{align-items:flex-start;flex-direction:column}.learn-header nav{gap:14px}.guide-grid{grid-template-columns:1fr}.hero{padding-top:36px}.hero p{font-size:17px}.guide-card{min-height:unset}.section-head h2{font-size:31px}}@media(prefers-reduced-motion:reduce){html{scroll-behavior:auto}.guide-card{transition:none}}
  </style>
</head>
<body>
<a class="skip" href="#main">Skip to content</a>
<header class="learn-header"><a class="learn-brand" href="<?= $h(base_url('/')) ?>" aria-label="TCM One home">TCM<b>ONE</b></a><nav aria-label="Primary navigation"><a href="<?= $h(base_url('/courses')) ?>">Courses</a><a href="<?= $h(base_url('/learn')) ?>" aria-current="page">Learn</a><a href="<?= $h(base_url('/tech-updates')) ?>">Tech Updates</a><a href="<?= $h(base_url('/about')) ?>">About</a></nav></header>
<div class="learn-wrap"><nav class="crumb" aria-label="Breadcrumb"><a href="<?= $h(base_url('/')) ?>">Home</a> / Learn</nav>
<main id="main"><section class="hero"><div class="eyebrow">TCM ONE · LEARNING LIBRARY · UPDATED 21 OCTOBER 2026</div><h1>Learn the web.<br>Build what’s next.</h1><p>Clear, practical guides for learning Python, Java and modern web development. Move from the basics to advanced concepts with explanations, runnable examples, hands-on tasks and project ideas.</p><div class="hero-actions"><a class="btn" href="#guides">Choose a learning guide ↓</a><a class="btn secondary" href="<?= $h(base_url('/courses')) ?>">Explore courses</a></div><div class="quick-facts"><span class="pill">6 structured guides</span><span class="pill">Beginner to advanced</span><span class="pill">Examples + practice in every module</span><span class="pill">Free to read</span></div></section>
<div class="version-note"><strong>Version note:</strong> Version references were checked against official standards and release pages on 21 October 2026. Web standards such as HTML and CSS evolve continuously; use each guide’s reference links to check current support.</div>
<section class="path-box" aria-labelledby="web-path"><h2 id="web-path">A practical path into web development</h2><p>Start with page structure, make it responsive and accessible, then add a framework or a CSS authoring workflow when the project needs one.</p><div class="path-links"><a href="<?= $h(base_url('/learn/html')) ?>">1. HTML foundations</a><a href="<?= $h(base_url('/learn/css')) ?>">2. CSS layout</a><a href="<?= $h(base_url('/learn/bootstrap')) ?>">3. Bootstrap components</a><a href="<?= $h(base_url('/learn/scss')) ?>">4. SCSS architecture</a></div></section>
<section id="guides" aria-labelledby="guides-title"><div class="section-head"><div class="eyebrow">PICK UP WHERE YOU ARE</div><h2 id="guides-title">Choose a guide</h2><p>Each guide is a standalone reference. Follow the modules in order or jump straight to the topic you need.</p></div><div class="guide-grid">
<?php foreach ($guides as $slug => $guide): ?>
  <article class="guide-card"><div class="guide-kicker"><?= $h($guide['category']) ?></div><h3><?= $h($guide['name']) ?></h3><p><?= $h($guide['summary']) ?></p><div class="guide-meta"><span><?= $h($guide['version']) ?></span><span><?= $h($guide['duration']) ?></span></div><a href="<?= $h(base_url('/learn/' . $slug)) ?>">Open the <?= $h($guide['name']) ?> guide →</a></article>
<?php endforeach; ?>
</div></section>
<section class="path-box" aria-labelledby="learning-help"><h2 id="learning-help">What helps you learn faster?</h2><p>Read the explanation, type the example yourself, change one thing and predict what will happen. Then complete the practice task before moving on. Debugging your own small mistakes is part of learning, not a detour.</p><div class="path-links"><a href="<?= $h(base_url('/courses')) ?>">See instructor-led courses</a><a href="<?= $h(base_url('/contact')) ?>">Ask the learning team</a></div></section>
</main><footer class="learn-foot">© <?= date('Y') ?> TCM One · Practical technology education.<nav aria-label="Footer"><a href="<?= $h(base_url('/learn')) ?>">Learn</a><a href="<?= $h(base_url('/courses')) ?>">Courses</a><a href="<?= $h(base_url('/locations')) ?>">Learning locations</a><a href="<?= $h(base_url('/contact')) ?>">Contact</a><a href="<?= $h(base_url('/sitemap.html')) ?>">Sitemap</a></nav></footer></div></body></html>
