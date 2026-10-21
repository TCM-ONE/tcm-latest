<?php
$h = static fn (string $value): string => htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
$guides = $guides ?? [];
$references = [
    'python' => [
        ['Python 3.15 documentation', 'https://docs.python.org/3.15/'],
        ['What’s new in Python 3.15', 'https://docs.python.org/3.15/whatsnew/3.15.html'],
        ['Python downloads and supported releases', 'https://www.python.org/downloads/'],
    ],
    'html' => [
        ['WHATWG HTML Living Standard', 'https://html.spec.whatwg.org/multipage/'],
        ['MDN: HTML basics', 'https://developer.mozilla.org/en-US/docs/Learn_web_development/Core/Structuring_content'],
        ['W3C Web Accessibility Initiative', 'https://www.w3.org/WAI/fundamentals/'],
    ],
    'css' => [
        ['W3C CSS Snapshot 2026', 'https://www.w3.org/TR/css-2026/'],
        ['MDN: CSS styling basics', 'https://developer.mozilla.org/en-US/docs/Learn_web_development/Core/Styling_basics'],
        ['MDN: CSS layout', 'https://developer.mozilla.org/en-US/docs/Learn_web_development/Core/CSS_layout'],
    ],
    'scss' => [
        ['Sass documentation', 'https://sass-lang.com/documentation/'],
        ['Sass syntax: SCSS and indented syntax', 'https://sass-lang.com/documentation/syntax/'],
        ['Dart Sass command-line guide', 'https://sass-lang.com/documentation/cli/'],
    ],
    'bootstrap' => [
        ['Bootstrap 5.3 documentation', 'https://getbootstrap.com/docs/5.3/'],
        ['Bootstrap versions and release status', 'https://getbootstrap.com/docs/versions/'],
        ['Bootstrap accessibility guidance', 'https://getbootstrap.com/docs/5.3/getting-started/accessibility/'],
    ],
    'java' => [
        ['Java SE 27 documentation', 'https://docs.oracle.com/en/java/javase/27/'],
        ['JDK 27 release notes', 'https://www.oracle.com/java/technologies/javase/27-relnote-issues.html'],
        ['Oracle Java downloads and LTS releases', 'https://www.oracle.com/java/technologies/downloads/'],
    ],
];
$related = array_filter($guides, static fn (array $item, string $key): bool => $key !== $slug, ARRAY_FILTER_USE_BOTH);
$project = [
    'python' => 'Build a command-line study planner that saves tasks, validates user input, reports progress and has automated tests.',
    'html' => 'Create a semantic, accessible multi-section portfolio page with a working table of contents, labelled contact form and responsive media.',
    'css' => 'Design a responsive learning dashboard using Grid, Flexbox, design tokens and reduced-motion support.',
    'scss' => 'Build a small Sass design system with token modules, reusable component mixins and generated CSS utilities.',
    'bootstrap' => 'Create a responsive course catalogue with a filter form, cards, accessible navigation and one keyboard-friendly dialog.',
    'java' => 'Build a console-based library tracker with validated models, collections, file persistence and unit tests.',
][$slug] ?? 'Build a small project that combines the concepts from this guide.';
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
  <meta property="og:type" content="article"><meta property="og:site_name" content="TCM One"><meta property="og:url" content="<?= $h($canonical) ?>"><meta property="og:title" content="<?= $h($title) ?>"><meta property="og:description" content="<?= $h($description) ?>"><meta property="og:image" content="<?= $h($ogImage) ?>"><meta property="og:locale" content="en_IN">
  <meta name="twitter:card" content="summary_large_image"><meta name="twitter:title" content="<?= $h($title) ?>"><meta name="twitter:description" content="<?= $h($description) ?>"><meta name="twitter:image" content="<?= $h($ogImage) ?>">
  <meta name="theme-color" content="#101b2c"><link rel="icon" href="<?= $h(base_url('/favicon.svg')) ?>" type="image/svg+xml">
  <link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin><link href="https://fonts.googleapis.com/css2?family=DM+Mono:wght@400;500&family=DM+Sans:wght@400;500;600;700;800&family=Instrument+Serif:ital@0;1&display=swap" rel="stylesheet">
  <script type="application/ld+json"><?= json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?></script>
  <style>
    :root{color-scheme:light;--ink:#17253a;--muted:#5b6d80;--line:#dbe4e8;--accent:#c5f467;--green:#1d624c;--paper:#f7f8f5;--white:#fff;--mono:'DM Mono',monospace;--sans:'DM Sans',Arial,sans-serif;--serif:'Instrument Serif',Georgia,serif}*{box-sizing:border-box}html{scroll-behavior:smooth;scroll-padding-top:24px}body{margin:0;background:var(--paper);color:var(--ink);font:16px/1.7 var(--sans)}a{color:var(--green)}a:hover{text-decoration-thickness:2px}.skip{position:absolute;left:-10000px;top:0;background:#fff;padding:12px;z-index:100}.skip:focus{left:12px}.learn-header{background:var(--ink);color:#fff;padding:18px max(24px,calc((100% - 1180px)/2));display:flex;justify-content:space-between;align-items:center;gap:18px}.learn-brand{color:#fff;text-decoration:none;font-size:18px;font-weight:800;letter-spacing:.04em}.learn-brand b{color:var(--accent)}.learn-header nav{display:flex;gap:22px;flex-wrap:wrap}.learn-header nav a{color:#e0e8ea;font-size:14px;font-weight:600;text-decoration:none}.shell{max-width:1180px;margin:auto;padding:0 24px}.crumb{padding-top:24px;color:var(--muted);font-size:13px}.crumb a{color:inherit}.intro{padding:42px 0 30px;max-width:900px}.eyebrow{font:500 12px/1.4 var(--mono);text-transform:uppercase;letter-spacing:.1em;color:var(--green)}h1{font:clamp(42px,6.5vw,68px)/1 var(--serif);letter-spacing:-.025em;margin:13px 0 15px}.lede{font-size:19px;color:var(--muted);margin:0;max-width:780px}.intro-actions{display:flex;gap:10px;flex-wrap:wrap;margin-top:22px}.btn{display:inline-flex;align-items:center;justify-content:center;border-radius:8px;padding:11px 16px;text-decoration:none;font-weight:700;background:var(--ink);color:#fff}.btn.secondary{background:#fff;color:var(--ink);border:1px solid var(--line)}.snapshot{margin:22px 0 32px;padding:14px 17px;background:#edf2eb;border:1px solid #d5e2d4;border-radius:10px;color:#405548;font-size:14px}.columns{display:grid;grid-template-columns:minmax(0,1fr) 285px;gap:36px;align-items:start}.contents{position:sticky;top:18px;max-height:calc(100vh - 36px);overflow:auto;background:#fff;border:1px solid var(--line);border-radius:12px;padding:19px}.contents h2{font:700 14px/1.4 var(--sans);margin:0 0 9px}.contents ol{padding-left:20px;margin:0}.contents li{padding:3px 0;font-size:12px;line-height:1.5}.contents a{color:#455a6b;text-decoration:none}.contents a:hover{text-decoration:underline}.lesson{border:1px solid var(--line);background:#fff;border-radius:14px;margin:0 0 17px;overflow:hidden;scroll-margin-top:18px}.lesson-head{padding:21px 22px 0}.lesson h2{font:29px/1.15 var(--serif);margin:0 0 10px;letter-spacing:-.01em}.lesson p{margin:0 0 14px}.lesson-body{padding:0 22px 21px}.code-label{font:500 10px/1.5 var(--mono);letter-spacing:.08em;text-transform:uppercase;color:#97a9b6;margin:14px 0 5px}.code{overflow-x:auto;margin:0 0 15px;padding:16px;background:#17253a;color:#edf5ed;border-radius:9px;font:12px/1.65 var(--mono);tab-size:4;white-space:pre}.task{padding:12px 14px;border-left:3px solid #8daa63;background:#f3f6ef;color:#405548;font-size:14px}.task strong{color:#254b3a}.roadmap{margin:28px 0;padding:24px;background:var(--ink);color:#eaf0ec;border-radius:14px}.roadmap h2{font:30px/1.1 var(--serif);margin:0 0 9px;color:white}.roadmap p{margin:0 0 11px;color:#cfdbd9}.refs,.faq-section,.related{border-top:1px solid var(--line);padding:30px 0}.refs h2,.faq-section h2,.related h2{font:31px/1.15 var(--serif);margin:0 0 13px}.refs ul{padding-left:20px;margin:0}.refs li{padding:5px 0}.faq{border-bottom:1px solid var(--line);padding:14px 0}.faq h3{font:700 16px/1.4 var(--sans);margin:0 0 4px}.faq p{margin:0;color:var(--muted)}.related-grid{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:10px}.related-grid a{background:#fff;border:1px solid var(--line);border-radius:9px;padding:12px;text-decoration:none;font-weight:700}.learn-foot{border-top:1px solid var(--line);padding:24px 0 50px;color:var(--muted);font-size:13px;margin-top:10px}.learn-foot nav{display:flex;gap:18px;flex-wrap:wrap;margin-top:8px}.learn-foot a{color:inherit}@media(max-width:900px){.columns{grid-template-columns:1fr}.contents{position:static;max-height:unset}.contents ol{columns:2;column-gap:24px}.related-grid{grid-template-columns:repeat(2,minmax(0,1fr))}}@media(max-width:620px){.learn-header{align-items:flex-start;flex-direction:column}.learn-header nav{gap:14px}.intro{padding-top:32px}.lede{font-size:17px}.contents ol{columns:1}.lesson-head{padding:18px 17px 0}.lesson-body{padding:0 17px 18px}.lesson h2{font-size:26px}.related-grid{grid-template-columns:1fr}.code{font-size:11px}}@media(prefers-reduced-motion:reduce){html{scroll-behavior:auto}}
  </style>
</head>
<body>
<a class="skip" href="#guide-content">Skip to guide</a>
<header class="learn-header"><a class="learn-brand" href="<?= $h(base_url('/')) ?>" aria-label="TCM One home">TCM<b>ONE</b></a><nav aria-label="Primary navigation"><a href="<?= $h(base_url('/courses')) ?>">Courses</a><a href="<?= $h(base_url('/learn')) ?>" aria-current="page">Learn</a><a href="<?= $h(base_url('/tech-updates')) ?>">Tech Updates</a><a href="<?= $h(base_url('/about')) ?>">About</a></nav></header>
<div class="shell"><nav class="crumb" aria-label="Breadcrumb"><a href="<?= $h(base_url('/')) ?>">Home</a> / <a href="<?= $h(base_url('/learn')) ?>">Learn</a> / <?= $h($guide['name']) ?></nav>
<main id="guide-content"><section class="intro"><div class="eyebrow"><?= $h($guide['category']) ?> · <?= $h($guide['level']) ?></div><h1><?= $h($guide['name']) ?>: from fundamentals to advanced</h1><p class="lede"><?= $h($guide['summary']) ?></p><div class="intro-actions"><a class="btn" href="#module-1">Start with module 1 ↓</a><a class="btn secondary" href="<?= $h(base_url('/courses?cat=' . rawurlencode($guide['courseCategory']))) ?>">Explore related courses</a><a class="btn secondary" href="<?= $h(base_url('/learn')) ?>">All learning guides</a></div></section>
<div class="snapshot"><strong>Version snapshot:</strong> <?= $h($guide['version']) ?>. <?= $h($guide['versionNote']) ?></div>
<div class="columns"><nav class="contents" aria-label="Guide contents"><h2>In this guide · <?= count($guide['lessons']) ?> modules</h2><ol><?php foreach ($guide['lessons'] as $index => $lesson): ?><li><a href="#module-<?= $index + 1 ?>"><?= $h(preg_replace('/^\d+\.\s*/', '', $lesson['title']) ?? $lesson['title']) ?></a></li><?php endforeach; ?><li><a href="#project">Final project</a></li><li><a href="#references">Official references</a></li></ol></nav>
<div class="lessons">
<?php foreach ($guide['lessons'] as $index => $lesson): $lessonTitle = preg_replace('/^\d+\.\s*/', '', $lesson['title']) ?? $lesson['title']; ?>
  <article class="lesson" id="module-<?= $index + 1 ?>"><div class="lesson-head"><div class="eyebrow">MODULE <?= str_pad((string)($index + 1), 2, '0', STR_PAD_LEFT) ?> / <?= count($guide['lessons']) ?></div><h2><?= $h($lessonTitle) ?></h2><p><?= $h($lesson['explanation']) ?></p></div><div class="lesson-body"><div class="code-label">Example</div><pre class="code"><code><?= $h($lesson['example']) ?></code></pre><div class="task"><strong>Try it:</strong> <?= $h($lesson['practice']) ?></div></div></article>
<?php endforeach; ?>
<section class="roadmap" id="project"><div class="eyebrow" style="color:#c5f467">PUT THE MODULES TOGETHER</div><h2>Your final project</h2><p><?= $h($project) ?> Write down what the project should do, build it in small steps, and include a README that explains how to run it.</p><a class="btn secondary" href="<?= $h(base_url('/courses?cat=' . rawurlencode($guide['courseCategory']))) ?>">Find a guided course →</a></section>
</div></div>
<section class="refs" id="references"><h2>Official documentation and further reading</h2><p>Use primary documentation for current language rules and version changes. The examples in this guide are learning starting points; verify details for the exact runtime and browser support your project needs.</p><ul><?php foreach ($references[$slug] ?? [] as [$label, $url]): ?><li><a href="<?= $h($url) ?>" target="_blank" rel="noopener noreferrer"><?= $h($label) ?></a></li><?php endforeach; ?></ul></section>
<section class="faq-section"><h2>Frequently asked questions</h2><?php foreach ($guide['faq'] as $item): ?><article class="faq"><h3><?= $h($item['q']) ?></h3><p><?= $h($item['a']) ?></p></article><?php endforeach; ?></section>
<section class="related"><h2>Continue learning</h2><div class="related-grid"><a href="<?= $h(base_url('/learn')) ?>">Browse all guides →</a><?php foreach (array_slice($related, 0, 5, true) as $otherSlug => $otherGuide): ?><a href="<?= $h(base_url('/learn/' . $otherSlug)) ?>"><?= $h($otherGuide['name']) ?> guide →</a><?php endforeach; ?></div></section>
</main><footer class="learn-foot">© <?= date('Y') ?> TCM One · Practical technology education.<nav aria-label="Footer"><a href="<?= $h(base_url('/learn')) ?>">Learn</a><a href="<?= $h(base_url('/courses')) ?>">Courses</a><a href="<?= $h(base_url('/locations')) ?>">Learning locations</a><a href="<?= $h(base_url('/contact')) ?>">Contact</a><a href="<?= $h(base_url('/sitemap.html')) ?>">Sitemap</a></nav></footer></div></body></html>
