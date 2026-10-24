<?php
$h = static fn (string $value): string => htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
$difficultyCounts = ['Beginner' => 0, 'Intermediate' => 0, 'Advanced' => 0];
foreach ($topics as $topic) {
    foreach ($topic['questions'] as $question) {
        if (isset($difficultyCounts[$question['level']])) {
            $difficultyCounts[$question['level']]++;
        }
    }
}
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
  <link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin><link href="https://fonts.googleapis.com/css2?family=Instrument+Serif:ital@0;1&family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet"><link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"><link rel="stylesheet" href="<?= base_url('/assets/tcm-design-system.css?v=2.18') ?>"><link rel="stylesheet" href="<?= base_url('/assets/tcm-editorial.css?v=2.19') ?>"><link rel="stylesheet" href="<?= base_url('/assets/tcm-footer.css?v=1') ?>"><link rel="stylesheet" href="<?= base_url('/assets/tcm-learn.css?v=6') ?>">
  <script type="application/ld+json"><?= json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?></script>
</head>
<body>
<a class="skip" href="#mainContent">Skip to interview questions</a>
<?php \TCM\Core\View::partial('partials.public-header'); ?>
<div class="shell interview-wrap">
  <nav class="crumb" aria-label="Breadcrumb"><a href="<?= $h(base_url('/')) ?>">Home</a> / <a href="<?= $h(base_url('/learn')) ?>">Learn</a> / Interview Preparation</nav>
  <main id="mainContent">
    <section class="intro interview-intro">
      <div class="eyebrow">TCM ONE · CAREER PRACTICE · <?= count($topics) ?> LEARN TOPICS</div>
      <h1 class="tcm-h1">Coding interview questions, explained simply</h1>
      <p class="lede">Practice <?= $questionCount ?> common questions from the Learn library. Understand what the question is testing, study a small example, then use the sample wording as a starting point for your own answer.</p>
      <div class="intro-actions"><a class="tcm-btn tcm-btn-primary" href="#question-list">Start practising ↓</a><a class="tcm-btn tcm-btn-secondary" href="<?= $h(base_url('/learn')) ?>">Browse learning guides</a></div>
    </section>
    <section class="interview-howto" aria-label="How to use this interview guide">
      <article><span>01</span><h2>Understand</h2><p>Read the short explanation before memorising terms.</p></article>
      <article><span>02</span><h2>Practise</h2><p>Run or adapt the code example and explain what changes.</p></article>
      <article><span>03</span><h2>Answer clearly</h2><p>Use the sample answer as a guide, then speak in your own words.</p></article>
    </section>
    <section class="interview-controls" aria-label="Find interview questions">
      <div class="interview-search-field"><label for="questionSearch">Search questions</label><input id="questionSearch" type="search" placeholder="Try: list vs tuple, dependency injection…" autocomplete="off"></div>
      <div><label for="topicFilter">Learning topic</label><select id="topicFilter"><option value="all">All Learn topics</option><?php foreach ($topics as $slug => $topic): ?><option value="<?= $h($slug) ?>"><?= $h($topic['name']) ?></option><?php endforeach; ?></select></div>
      <div><label for="levelFilter">Difficulty</label><select id="levelFilter"><option value="all">All levels</option><option value="Beginner">Beginner (<?= $difficultyCounts['Beginner'] ?>)</option><option value="Intermediate">Intermediate (<?= $difficultyCounts['Intermediate'] ?>)</option><option value="Advanced">Advanced (<?= $difficultyCounts['Advanced'] ?>)</option></select></div>
      <button class="tcm-btn tcm-btn-secondary interview-clear" type="button" id="clearFilters">Clear filters</button>
    </section>
    <p class="interview-result" id="questionResult" aria-live="polite"><?= $questionCount ?> questions across <?= count($topics) ?> topics</p>
    <p class="interview-no-results" id="questionNoResults" hidden>No questions match those filters. Try a different topic, level or search phrase.</p>
    <nav class="interview-topic-nav" aria-label="Jump to a learning topic"><strong>Jump to a topic</strong><?php foreach ($topics as $slug => $topic): ?><a href="#topic-<?= $h($slug) ?>"><?= $h($topic['name']) ?></a><?php endforeach; ?></nav>
    <div id="question-list" class="interview-topics">
    <?php foreach ($topics as $slug => $topic): ?>
      <section class="interview-topic" id="topic-<?= $h($slug) ?>" data-topic-section="<?= $h($slug) ?>" aria-labelledby="topic-title-<?= $h($slug) ?>">
        <div class="interview-topic-heading"><div><div class="eyebrow">LEARN GUIDE</div><h2 id="topic-title-<?= $h($slug) ?>"><?= $h($topic['name']) ?> interview questions</h2></div><a href="<?= $h(base_url('/learn/' . $slug)) ?>">Study the <?= $h($topic['name']) ?> guide →</a></div>
        <p class="interview-topic-summary"><?= $h($topic['summary']) ?></p>
        <div class="interview-question-grid">
        <?php foreach ($topic['questions'] as $index => $question):
            $searchText = implode(' ', [$question['question'], $question['answer'], $question['say'], $question['note'], $topic['name']]);
        ?>
          <article class="interview-question" data-question-card data-topic="<?= $h($slug) ?>" data-level="<?= $h($question['level']) ?>" data-search="<?= $h($searchText) ?>">
            <details>
              <summary><span class="interview-question-number">Q<?= $index + 1 ?></span><span class="interview-question-title"><?= $h($question['question']) ?></span><span class="interview-level level-<?= strtolower($h($question['level'])) ?>"><?= $h($question['level']) ?></span></summary>
              <div class="interview-answer">
                <h3>Simple explanation</h3><p><?= $h($question['answer']) ?></p>
                <h3>Example</h3><pre class="code"><code><?= $h($question['code']) ?></code></pre>
                <p class="interview-example-note"><strong>What to notice:</strong> <?= $h($question['note']) ?></p>
                <div class="interview-say"><strong>One easy way to explain it</strong><p><?= $h($question['say']) ?></p></div>
              </div>
            </details>
          </article>
        <?php endforeach; ?>
        </div>
        <p class="interview-empty" data-topic-empty hidden>No questions match these filters in this topic.</p>
      </section>
    <?php endforeach; ?>
    </div>
    <section class="interview-bottom path-box"><h2>Turn practice into a strong answer</h2><p>Use a simple structure: define the idea, explain when you would use it, then show a small example or trade-off. Be honest when you do not know something; explain how you would investigate it.</p><div class="path-links"><a href="<?= $h(base_url('/learn')) ?>">Return to all Learn guides →</a><a href="<?= $h(base_url('/contact')) ?>">Talk to the learning team →</a></div></section>
  </main>
</div>
<?php \TCM\Core\View::partial('partials.public-footer'); ?>
<script src="<?= base_url('/assets/tcm-interview-prep.js?v=1') ?>" defer></script>
<script src="<?= base_url('/assets/tcm-editorial.js?v=2.21') ?>" defer></script>
</body>
</html>
