<style>
.partner-directory{--partner-line:#deddd8;--partner-muted:#666;padding-bottom:clamp(52px,7vw,88px)}
.partner-hero{background:#f8f7f4;border-bottom:1px solid var(--partner-line);padding:clamp(54px,8vw,100px) 0 clamp(42px,6vw,68px)}
.partner-hero-inner,.partner-content{width:min(1160px,calc(100% - 40px));margin:0 auto}
.partner-eyebrow{display:flex;align-items:center;gap:9px;color:#666;font-size:.72rem;font-weight:700;letter-spacing:.12em;text-transform:uppercase}
.partner-eyebrow:before{content:"";width:9px;height:9px;background:#111;display:inline-block}
.partner-hero h1{font-family:var(--font-display,"Instrument Serif",Georgia,serif);font-weight:400;font-size:clamp(2.7rem,6vw,5rem);line-height:1.02;letter-spacing:-.03em;margin:18px 0 16px;max-width:760px}
.partner-hero h1 em{font-weight:400}
.partner-intro{max-width:670px;color:#666;font-size:clamp(1rem,1.5vw,1.12rem);line-height:1.7;margin:0}
.partner-content{padding-top:28px}
.partner-filter-panel{background:#fff;border:1px solid var(--partner-line);padding:18px;margin-bottom:26px}
.partner-filter{display:grid;grid-template-columns:minmax(220px,1.5fr) repeat(3,minmax(145px,1fr)) auto;align-items:end;gap:10px}
.partner-filter label{display:grid;gap:6px;font-size:.7rem;font-weight:700;letter-spacing:.04em;color:#555}
.partner-filter input,.partner-filter select{height:46px;width:100%;min-width:0;padding:0 12px;border:1px solid #d5d3ce;background:#fff;color:#111;font:inherit;font-size:.84rem}
.partner-filter input:focus,.partner-filter select:focus{outline:2px solid #111;outline-offset:1px}
.partner-filter button{height:46px;white-space:nowrap}
.partner-results{display:flex;justify-content:space-between;align-items:center;gap:12px;margin:0 0 14px;color:#666;font-size:.82rem}
.partner-results strong{color:#111}
.partner-grid{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:14px}
.partner-card{min-width:0;background:#fff;border:1px solid #deddd8;padding:22px;display:flex;flex-direction:column;min-height:270px;transition:border-color .16s,transform .16s}
.partner-card:hover{border-color:#aaa;transform:translateY(-2px)}
.partner-card-top{display:flex;align-items:flex-start;justify-content:space-between;gap:12px;margin-bottom:22px}
.partner-mark{width:58px;height:58px;display:grid;place-items:center;background:#f4f3ef;border:1px solid #ebe9e4;object-fit:contain;padding:6px;color:#555;font-size:1.3rem}
.partner-verified{display:inline-flex;align-items:center;gap:5px;font-size:.65rem;font-weight:700;letter-spacing:.06em;text-transform:uppercase;color:#456719}
.partner-card h2{font-size:1.15rem;line-height:1.35;letter-spacing:-.02em;margin:0 0 5px}
.partner-location{color:#777;font-size:.78rem}
.partner-card p{color:#666;font-size:.84rem;line-height:1.6;margin:14px 0 18px}
.provider-tag{font-size:.71rem;color:#555;letter-spacing:.03em;margin:0 0 18px}
.partner-card .tcm-btn{margin-top:auto;justify-content:center;width:100%}
.partner-empty{background:#fff;border:1px dashed #c9c7c1;padding:38px 24px;text-align:center;color:#666}
.partner-empty i{display:block;font-size:1.6rem;color:#111;margin-bottom:9px}
.partner-empty strong{display:block;color:#111;font-size:1rem;margin-bottom:5px}
@media(max-width:980px){.partner-filter{grid-template-columns:repeat(2,minmax(0,1fr))}.partner-grid{grid-template-columns:repeat(2,minmax(0,1fr))}}
@media(max-width:600px){.partner-hero-inner,.partner-content{width:min(100% - 32px,1160px)}.partner-filter-panel{padding:13px}.partner-filter{grid-template-columns:1fr}.partner-filter button{width:100%;justify-content:center}.partner-grid{grid-template-columns:1fr}.partner-card{min-height:0}.partner-card-top{margin-bottom:18px}}
</style>
<main class="partner-directory">
  <section class="partner-hero">
    <div class="partner-hero-inner">
      <div class="partner-eyebrow">TCM One · Institutional ecosystem</div>
      <h1>Learning grows <em>stronger together.</em></h1>
      <p class="partner-intro">Meet the approved institutions building practical technology education with TCM One. Explore partner programs and find the right learning community.</p>
    </div>
  </section>
  <section class="partner-content" aria-label="Partner institutions">
    <div class="partner-filter-panel">
      <form class="partner-filter" method="get" role="search">
        <label>Search institutions
          <input name="q" value="<?= e($search) ?>" placeholder="Name, city, or keyword" aria-label="Search institutions by name, city, or keyword">
        </label>
        <label>City
          <select name="city"><option value="">All cities</option><?php foreach ($cities as $c): ?><option value="<?= e($c['city']) ?>" <?= $city === $c['city'] ? 'selected' : '' ?>><?= e($c['city']) ?></option><?php endforeach ?></select>
        </label>
        <label>Partner type
          <select name="type"><option value="">All types</option><option value="academic" <?= $type === 'academic' ? 'selected' : '' ?>>Academic</option><option value="referral" <?= $type === 'referral' ? 'selected' : '' ?>>Referral</option><option value="custom" <?= $type === 'custom' ? 'selected' : '' ?>>Other</option></select>
        </label>
        <label>Course
          <input name="course" value="<?= e($courseFilter) ?>" placeholder="e.g. Web development">
        </label>
        <button class="tcm-btn tcm-btn-primary" type="submit"><i class="bi bi-search" aria-hidden="true"></i> Search</button>
      </form>
    </div>
    <div class="partner-results" role="status"><span><strong><?= count($partners) ?></strong> institution<?= count($partners) === 1 ? '' : 's' ?> found</span><?php if ($search !== '' || $city !== '' || $type !== '' || $courseFilter !== ''): ?><a href="<?= base_url('/partners') ?>">Clear filters</a><?php endif ?></div>
    <?php if (!$partners): ?>
      <div class="partner-empty"><i class="bi bi-buildings" aria-hidden="true"></i><strong>No matching institutions yet</strong>Try another city or search term, or clear the filters to browse all approved partners.</div>
    <?php else: ?>
      <div class="partner-grid">
        <?php foreach ($partners as $p): ?>
          <article class="partner-card">
            <div class="partner-card-top">
              <?php if (!empty($p['logo_path'])): ?><img class="partner-mark" src="<?= e(base_url('/' . ltrim($p['logo_path'], '/'))) ?>" alt="<?= e($p['display_name']) ?> logo" loading="lazy"><?php else: ?><div class="partner-mark" aria-hidden="true"><i class="bi bi-building"></i></div><?php endif ?>
              <span class="partner-verified"><i class="bi bi-patch-check-fill" aria-hidden="true"></i> Approved</span>
            </div>
            <h2><?= e($p['display_name']) ?></h2>
            <div class="partner-location"><i class="bi bi-geo-alt" aria-hidden="true"></i> <?= e($p['city'] ?? 'Location not listed') ?><?= !empty($p['state']) ? ', ' . e($p['state']) : '' ?></div>
            <p><?= e(mb_strimwidth((string) ($p['description'] ?? ''), 0, 130, '…')) ?></p>
            <div class="provider-tag"><i class="bi bi-journal-bookmark" aria-hidden="true"></i> <?= (int) $p['course_count'] ?> approved course<?= (int) $p['course_count'] === 1 ? '' : 's' ?></div>
            <a class="tcm-btn tcm-btn-outline" href="<?= base_url('/partners/' . rawurlencode($p['slug'])) ?>">View institution <i class="bi bi-arrow-right" aria-hidden="true"></i></a>
          </article>
        <?php endforeach ?>
      </div>
    <?php endif ?>
  </section>
</main>
