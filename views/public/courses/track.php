<?php
$schema = [
    '@context' => 'https://schema.org',
    '@type' => 'CollectionPage',
    'name' => $track['name'] . ' courses | TCM One',
    'url' => $canonicalUrl,
    'description' => $description,
    'inLanguage' => 'en-IN',
    'isPartOf' => ['@type' => 'WebSite', 'name' => 'TCM One', 'url' => $appUrl],
    'mainEntity' => ['@type' => 'ItemList', 'itemListElement' => array_values(array_map(static fn (array $course, int $index): array => [
        '@type' => 'ListItem', 'position' => $index + 1,
        'item' => ['@type' => 'Course', 'name' => $course['title'], 'url' => $appUrl . '/courses/' . rawurlencode($course['slug'])],
    ], $courses, array_keys($courses)))],
];
$trackLabels = [
    'web-mobile-development' => 'Full Stack & Web Development',
    'programming-foundations' => 'Programming Foundations',
    'ai-automation' => 'AI & Automation',
    'data-analytics' => 'Data Analytics & Data Science',
    'cloud-devops' => 'Cloud & DevOps',
    'career-dsa' => 'DSA & Career Preparation',
    'design-business' => 'UI/UX Design & Digital Business',
];
?>
<script type="application/ld+json"><?= json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?></script>
<style>
.course-track-page{padding:clamp(32px,6vw,72px) 0 80px}.course-track-wrap{width:min(1120px,calc(100% - 40px));margin:auto}.course-track-crumb{font-size:.82rem;color:var(--tcm-muted,#686762);margin-bottom:28px}.course-track-crumb a{color:inherit}.course-track-eyebrow{font-size:.72rem;font-weight:700;letter-spacing:.13em;text-transform:uppercase;color:#777}.course-track-title{max-width:850px;margin:12px 0 16px;font-size:clamp(2.6rem,6vw,5rem);line-height:1.02;letter-spacing:-.04em}.course-track-intro{max-width:770px;color:var(--tcm-muted,#666);font-size:1.08rem;line-height:1.75}.course-track-grid{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:14px;margin:32px 0 50px}.course-track-card{min-width:0;display:flex;flex-direction:column;padding:22px;border:1px solid var(--tcm-border,#deddd8);background:#fff;color:inherit;text-decoration:none;transition:transform .16s,border-color .16s}.course-track-card:hover{transform:translateY(-2px);border-color:#a41f2d}.course-track-card h2{font-size:1.12rem;line-height:1.4;margin:0 0 10px}.course-track-card p{color:var(--tcm-muted,#666);font-size:.9rem;line-height:1.65;margin:0 0 16px}.course-track-card span{margin-top:auto;font-size:.82rem;font-weight:700;color:#a41f2d}.course-track-links{display:flex;flex-wrap:wrap;gap:9px}.course-track-links a{padding:8px 12px;border:1px solid var(--tcm-border,#deddd8);text-decoration:none;font-size:.82rem}.course-track-links a[aria-current=page]{background:#171717;color:white;border-color:#171717}@media(max-width:850px){.course-track-grid{grid-template-columns:repeat(2,minmax(0,1fr))}}@media(max-width:560px){.course-track-wrap{width:calc(100% - 30px)}.course-track-grid{grid-template-columns:1fr}.course-track-card{padding:18px}}
</style>
<main class="course-track-page">
  <div class="course-track-wrap">
    <nav class="course-track-crumb" aria-label="Breadcrumb"><a href="<?= e(base_url('/')) ?>">Home</a> / <a href="<?= e(base_url('/courses')) ?>">Courses</a> / <?= e($track['name']) ?></nav>
    <header>
      <div class="course-track-eyebrow">TCM One · Course track</div>
      <h1 class="course-track-title"><?= e($trackLabels[$currentSlug] ?? $track['name']) ?> Courses</h1>
      <p class="course-track-intro"><?= e($trackIntro) ?></p>
      <p><a class="tcm-btn tcm-btn-secondary" href="<?= e(base_url('/courses')) ?>">Browse the complete course catalog <i class="bi bi-arrow-right" aria-hidden="true"></i></a></p>
    </header>

    <section aria-labelledby="track-courses-heading">
      <div class="course-track-eyebrow">Published programmes</div>
      <h2 id="track-courses-heading" class="tcm-h2" style="margin:8px 0 0"><?= e($track['name']) ?> courses</h2>
      <?php if ($courses): ?>
        <div class="course-track-grid">
          <?php foreach ($courses as $course): ?>
            <a class="course-track-card" href="<?= e(base_url('/courses/' . rawurlencode((string) $course['slug']))) ?>">
              <h2><?= e($course['title']) ?></h2>
              <p><?= e(mb_strimwidth((string) ($course['subtitle'] ?: $course['description'] ?: ''), 0, 210, '…')) ?></p>
              <span>View curriculum &amp; course details <i class="bi bi-arrow-right" aria-hidden="true"></i></span>
            </a>
          <?php endforeach; ?>
        </div>
      <?php else: ?>
        <p>There are no published courses in this track at the moment. Browse the full catalog or contact admissions for current availability.</p>
      <?php endif; ?>
    </section>

    <nav aria-label="Other course categories">
      <div class="course-track-eyebrow">Explore other categories</div>
      <div class="course-track-links" style="margin-top:14px">
        <?php foreach ($trackLabels as $slug => $label): ?>
          <a href="<?= e(base_url('/courses/tracks/' . $slug)) ?>" <?= $slug === $currentSlug ? 'aria-current="page"' : '' ?>><?= e($label) ?></a>
        <?php endforeach; ?>
      </div>
    </nav>
  </div>
</main>
