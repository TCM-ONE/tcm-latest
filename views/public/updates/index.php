<?php
$postDate = static function (array $post): string {
    $date = $post['published_at'] ?: $post['created_at'] ?: null;
    return $date ? date('d M Y', strtotime((string) $date)) : '';
};
$readTime = static function (string $content): string {
    $words = str_word_count(strip_tags($content));
    return max(1, (int) ceil($words / 220)) . ' min read';
};
$coverUrl = static function (string $path): string {
    if (preg_match('#^https?://#i', $path)) {
        return $path;
    }
    if (str_starts_with($path, 'assets/') || str_starts_with($path, '/assets/')) {
        return base_url('/' . ltrim($path, '/'));
    }
    if (str_starts_with($path, 'uploads/') || str_starts_with($path, '/uploads/')) {
        return base_url('/' . ltrim($path, '/'));
    }
    return base_url('/uploads/' . ltrim($path, '/'));
};

$collectionSchema = [
    '@context' => 'https://schema.org',
    '@type' => 'CollectionPage',
    'name' => $title,
    'description' => $description,
    'url' => $canonical,
    'publisher' => [
        '@type' => 'Organization',
        'name' => 'TCM One',
        'url' => 'https://thecodemunk.in',
        'logo' => 'https://thecodemunk.in/assets/logo.png',
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
            'name' => 'Tech Updates',
            'item' => $canonical,
        ],
    ],
];
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="app-base" content="<?= e(base_url('')) ?>">
  <title><?= e($title) ?></title>
  <meta name="description" content="<?= e($description) ?>">
  <meta name="robots" content="index, follow">
  <link rel="canonical" href="<?= e($canonical) ?>">

  <!-- Open Graph -->
  <meta property="og:site_name" content="TCM One">
  <meta property="og:type" content="website">
  <meta property="og:url" content="<?= e($canonical) ?>">
  <meta property="og:title" content="<?= e($title) ?>">
  <meta property="og:description" content="<?= e($description) ?>">
  <meta property="og:image" content="<?= e($ogImage) ?>">
  <meta property="og:locale" content="en_IN">

  <!-- Twitter Card -->
  <meta name="twitter:card" content="summary_large_image">
  <meta name="twitter:site" content="@thecodemunk">
  <meta name="twitter:title" content="<?= e($title) ?>">
  <meta name="twitter:description" content="<?= e($description) ?>">
  <meta name="twitter:image" content="<?= e($ogImage) ?>">

  <!-- International Hreflang Tags -->
  <link rel="alternate" hreflang="en-IN" href="<?= e($canonical) ?>">
  <link rel="alternate" hreflang="en-US" href="<?= e($canonical) ?>">
  <link rel="alternate" hreflang="x-default" href="<?= e($canonical) ?>">

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Instrument+Serif:ital@0;1&family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link rel="stylesheet" href="<?= base_url('/assets/tcm-design-system.css?v=2.18') ?>">
  <link rel="stylesheet" href="<?= base_url('/assets/tcm-editorial.css?v=2.18') ?>">
  <link rel="stylesheet" href="<?= base_url('/assets/tcm-footer.css?v=1') ?>">
  <link rel="stylesheet" href="<?= base_url('/assets/tcm-updates.css?v=1') ?>">

  <!-- Structured Data (JSON-LD) -->
  <script type="application/ld+json"><?= json_encode($collectionSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) ?></script>
  <script type="application/ld+json"><?= json_encode($breadcrumbSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) ?></script>
</head>
<body>
<?php \TCM\Core\View::partial('partials.public-header'); ?>
<main id="mainContent">
  <section class="tcm-updates-hero"><div class="tcm-container"><div class="tcm-eyebrow">TCM ONE / TECH UPDATES</div><h1 class="tcm-h1">Ideas for the work<br><span class="font-editorial-serif">you want to build.</span></h1><p>Practical notes on software, AI, data, and the learning habits that help you turn knowledge into real work.</p></div></section>
  <section class="tcm-updates-content"><div class="tcm-container">
    <nav class="tcm-updates-filters" aria-label="Filter updates"><a href="<?= base_url('/tech-updates') ?>" class="<?= $activeCategory === '' ? 'active' : '' ?>">All updates</a><?php foreach ($categories as $item): $name = (string) $item['category']; ?><a href="<?= base_url('/tech-updates?category=' . rawurlencode($name)) ?>" class="<?= $activeCategory === $name ? 'active' : '' ?>"><?= e($name) ?></a><?php endforeach; ?></nav>
    <?php if ($posts !== []): ?>
      <div class="tcm-updates-grid">
      <?php foreach ($posts as $index => $post): ?>
        <article class="tcm-update-card <?= $index === 0 ? 'featured' : '' ?>">
          <?php if (!empty($post['cover_image'])): ?><img src="<?= e($coverUrl((string) $post['cover_image'])) ?>" alt="" class="tcm-update-cover"><?php else: ?><div class="tcm-update-cover placeholder"><span><?= e(strtoupper(substr((string) $post['category'], 0, 1))) ?></span></div><?php endif; ?>
          <div class="tcm-update-card-body"><div class="tcm-update-meta"><span><?= e((string) $post['category']) ?></span><span><?= e($postDate($post)) ?></span><span><?= e($readTime((string) $post['content'])) ?></span></div><h2><a href="<?= base_url('/tech-updates/' . rawurlencode((string) $post['slug'])) ?>"><?= e((string) $post['title']) ?></a></h2><p><?= e((string) ($post['excerpt'] ?: 'Read the complete update from TCM One.')) ?></p><a class="tcm-update-read" href="<?= base_url('/tech-updates/' . rawurlencode((string) $post['slug'])) ?>">Read update <i class="bi bi-arrow-right"></i></a></div>
        </article>
      <?php endforeach; ?>
      </div>
    <?php else: ?>
      <div class="tcm-updates-empty"><span>TCM ONE</span><h2>Updates are on their way.</h2><p>We are preparing practical articles and engineering notes for this space.</p><a href="<?= base_url('/courses') ?>" class="tcm-btn tcm-btn-primary">Explore programs</a></div>
    <?php endif; ?>
  </div></section>
</main>
<?php \TCM\Core\View::partial('partials.public-footer'); ?>
<script src="<?= base_url('/assets/tcm-editorial.js?v=2.22') ?>"></script>
</body></html>
