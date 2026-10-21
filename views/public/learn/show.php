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
  <link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin><link href="https://fonts.googleapis.com/css2?family=Instrument+Serif:ital@0;1&family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet"><link rel="stylesheet" href="<?= base_url('/assets/tcm-design-system.css?v=2.18') ?>"><link rel="stylesheet" href="<?= base_url('/assets/tcm-editorial.css?v=2.18') ?>"><link rel="stylesheet" href="<?= base_url('/assets/tcm-footer.css?v=1') ?>"><link rel="stylesheet" href="<?= base_url('/assets/tcm-learn.css?v=1') ?>">
  <script type="application/ld+json"><?= json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?></script>

</head>
<body>
<a class="skip" href="#mainContent">Skip to guide</a>
<?php \TCM\Core\View::partial('partials.public-header'); ?>
<div class="shell"><nav class="crumb" aria-label="Breadcrumb"><a href="<?= $h(base_url('/')) ?>">Home</a> / <a href="<?= $h(base_url('/learn')) ?>">Learn</a> / <?= $h($guide['name']) ?></nav>
<main id="mainContent"><section class="intro"><div class="eyebrow"><?= $h($guide['category']) ?> · <?= $h($guide['level']) ?></div><h1 class="tcm-h1"><?= $h($guide['name']) ?>: from fundamentals to advanced</h1><p class="lede"><?= $h($guide['summary']) ?></p><div class="intro-actions"><a class="tcm-btn tcm-btn-primary" href="#module-1">Start with module 1 ↓</a><a class="tcm-btn tcm-btn-secondary" href="<?= $h(base_url('/courses?cat=' . rawurlencode($guide['courseCategory']))) ?>">Explore related courses</a><a class="tcm-btn tcm-btn-secondary" href="<?= $h(base_url('/learn')) ?>">All learning guides</a></div></section>
<div class="snapshot"><strong>Version snapshot:</strong> <?= $h($guide['version']) ?>. <?= $h($guide['versionNote']) ?></div>
<div class="columns"><nav class="contents" aria-label="Guide contents"><h2>In this guide · <?= count($guide['lessons']) ?> modules</h2><ol><?php foreach ($guide['lessons'] as $index => $lesson): ?><li><a href="#module-<?= $index + 1 ?>"><?= $h(preg_replace('/^\d+\.\s*/', '', $lesson['title']) ?? $lesson['title']) ?></a></li><?php endforeach; ?><li><a href="#project">Final project</a></li><li><a href="#references">Official references</a></li></ol></nav>
<div class="lessons">
<?php foreach ($guide['lessons'] as $index => $lesson): $lessonTitle = preg_replace('/^\d+\.\s*/', '', $lesson['title']) ?? $lesson['title']; ?>
  <article class="lesson" id="module-<?= $index + 1 ?>"><div class="lesson-head"><div class="eyebrow">MODULE <?= str_pad((string)($index + 1), 2, '0', STR_PAD_LEFT) ?> / <?= count($guide['lessons']) ?></div><h2><?= $h($lessonTitle) ?></h2><p><?= $h($lesson['explanation']) ?></p></div><div class="lesson-body"><div class="code-label">Example</div><pre class="code"><code><?= $h($lesson['example']) ?></code></pre><div class="task"><strong>Try it:</strong> <?= $h($lesson['practice']) ?></div></div></article>
<?php endforeach; ?>
<section class="roadmap" id="project"><div class="eyebrow" style="color:#c5f467">PUT THE MODULES TOGETHER</div><h2>Your final project</h2><p><?= $h($project) ?> Write down what the project should do, build it in small steps, and include a README that explains how to run it.</p><a class="tcm-btn tcm-btn-secondary" href="<?= $h(base_url('/courses?cat=' . rawurlencode($guide['courseCategory']))) ?>">Find a guided course →</a></section>
</div></div>
<section class="refs" id="references"><h2>Official documentation and further reading</h2><p>Use primary documentation for current language rules and version changes. The examples in this guide are learning starting points; verify details for the exact runtime and browser support your project needs.</p><ul><?php foreach ($references[$slug] ?? [] as [$label, $url]): ?><li><a href="<?= $h($url) ?>" target="_blank" rel="noopener noreferrer"><?= $h($label) ?></a></li><?php endforeach; ?></ul></section>
<section class="faq-section"><h2>Frequently asked questions</h2><?php foreach ($guide['faq'] as $item): ?><article class="faq"><h3><?= $h($item['q']) ?></h3><p><?= $h($item['a']) ?></p></article><?php endforeach; ?></section>
<section class="related"><h2>Continue learning</h2><div class="related-grid"><a href="<?= $h(base_url('/learn')) ?>">Browse all guides →</a><?php foreach (array_slice($related, 0, 5, true) as $otherSlug => $otherGuide): ?><a href="<?= $h(base_url('/learn/' . $otherSlug)) ?>"><?= $h($otherGuide['name']) ?> guide →</a><?php endforeach; ?></div></section>
</main></div><?php \TCM\Core\View::partial('partials.public-footer'); ?></body></html>
