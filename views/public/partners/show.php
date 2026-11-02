<style>
.partner-profile{--pp-line:#e4e1dc;--pp-muted:#686762;--pp-red:#a41f2d;padding-bottom:clamp(52px,7vw,88px);color:#171717}
.partner-profile *{box-sizing:border-box}
.pp-wrap{width:min(1120px,calc(100% - 40px));margin-inline:auto}
.pp-hero{background:#f8f7f4;border-bottom:1px solid var(--pp-line);padding:clamp(38px,6vw,72px) 0}
.pp-breadcrumb{font-size:.78rem;color:var(--pp-muted);margin-bottom:24px}.pp-breadcrumb a{color:inherit;text-decoration:none}
.pp-brand{display:flex;align-items:center;gap:22px}.pp-logo{width:100px;height:100px;flex:0 0 auto;object-fit:contain;background:#fff;border:1px solid var(--pp-line);padding:8px}
.pp-kicker,.pp-eyebrow{font-size:.7rem;font-weight:700;letter-spacing:.13em;text-transform:uppercase;color:#686762}
.pp-heading h1{font-family:var(--font-display,"Instrument Serif",Georgia,serif);font-size:clamp(2.5rem,5vw,4.6rem);line-height:.98;font-weight:400;letter-spacing:-.03em;margin:9px 0 10px}
.pp-location{color:var(--pp-muted);font-size:.95rem}.pp-actions{display:flex;flex-wrap:wrap;gap:10px;margin-top:24px}
.pp-share-status{align-self:center;min-height:1.2em;color:var(--pp-muted);font-size:.78rem}
.pp-whatsapp-note{font-size:.76rem!important;line-height:1.55!important;margin:10px 0!important}
.pp-content{display:grid;grid-template-columns:minmax(0,1.7fr) minmax(270px,.8fr);gap:24px;padding-top:32px}
.pp-main,.pp-aside{min-width:0}.pp-panel{background:#fff;border:1px solid var(--pp-line);padding:clamp(20px,3vw,30px);margin-bottom:18px}
.pp-panel h2{font-family:var(--font-display,"Instrument Serif",Georgia,serif);font-size:clamp(1.7rem,3vw,2.2rem);font-weight:400;margin:0 0 12px}
.pp-panel p{color:var(--pp-muted);line-height:1.75;margin:0}.pp-courses{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:12px;margin-top:18px}
.pp-course{border:1px solid var(--pp-line);padding:18px;min-width:0}.pp-course-icon{width:42px;height:42px;display:grid;place-items:center;background:#f7eeee;color:var(--pp-red);font-size:1.15rem;margin-bottom:15px}
.pp-course h3{font-size:1rem;line-height:1.4;margin:0 0 7px}.pp-course p{font-size:.86rem;line-height:1.65}
.pp-contact{display:grid;gap:10px;margin-top:18px}.pp-contact a{display:flex;gap:10px;align-items:flex-start;color:#171717;text-decoration:none;font-size:.9rem;line-height:1.5}.pp-contact i{color:var(--pp-red);font-size:1.05rem}
.pp-map{margin-top:18px;border:0;width:100%;height:270px;display:block;background:#f2f0eb}.pp-review{display:flex;align-items:center;justify-content:space-between;gap:14px;margin-top:16px;padding:16px;background:#f8f7f4;border:1px solid var(--pp-line)}.pp-review strong{display:block;margin-bottom:3px}.pp-review span{font-size:.82rem;color:var(--pp-muted)}
.pp-enquiry{position:sticky;top:18px}.pp-enquiry label{display:grid;gap:6px;margin:12px 0;font-size:.78rem;font-weight:700}.pp-enquiry input,.pp-enquiry textarea,.pp-enquiry select{width:100%;padding:11px 12px;border:1px solid #d8d5cf;background:#fff;font:inherit;font-size:.9rem}.pp-enquiry button{width:100%;justify-content:center;margin-top:8px}.pp-consent{display:flex!important;grid-template-columns:auto 1fr!important;align-items:flex-start;gap:8px!important;font-weight:400!important;line-height:1.5}.pp-consent input{width:auto;margin-top:3px}
@media(max-width:760px){.pp-content{grid-template-columns:1fr}.pp-aside{order:-1}.pp-enquiry{position:static}.pp-logo{width:78px;height:78px}.pp-brand{align-items:flex-start;gap:15px}}
@media(max-width:480px){.pp-wrap{width:calc(100% - 30px)}.pp-brand{display:grid;grid-template-columns:66px 1fr;gap:13px}.pp-logo{width:66px;height:66px}.pp-heading h1{font-size:2.4rem}.pp-actions .tcm-btn{flex:1;justify-content:center}.pp-courses{grid-template-columns:1fr}.pp-review{align-items:flex-start;flex-direction:column}.pp-review .tcm-btn{width:100%;justify-content:center}}
</style>
<?php if ($isJmk): ?>
<script type="application/ld+json"><?php
$partnerSchema = [
    '@context' => 'https://schema.org',
    '@type' => ['EducationalOrganization', 'LocalBusiness'],
    'name' => $partner['display_name'],
    'url' => base_url('/partners/' . rawurlencode($partner['slug'])),
    'logo' => base_url('/' . ltrim((string)$partner['logo_path'], '/')),
    'description' => $partner['description'],
    'telephone' => '+' . preg_replace('/\D/', '', (string)$partner['whatsapp']),
    'areaServed' => ['@type' => 'City', 'name' => 'Bilaspur, Chhattisgarh'],
    'sameAs' => [$googleReviewsUrl],
    'address' => [
        '@type' => 'PostalAddress',
        'streetAddress' => 'Near DLS College Road, Ashok Nagar, Sarkanda',
        'addressLocality' => 'Bilaspur',
        'addressRegion' => 'Chhattisgarh',
        'postalCode' => (string)$partner['postal_code'],
        'addressCountry' => 'IN',
    ],
    'hasOfferCatalog' => [
        '@type' => 'OfferCatalog',
        'name' => 'Education programs',
        'itemListElement' => array_map(static fn(array $course): array => [
            '@type' => 'Course',
            'name' => $course['title'],
            'description' => $course['description'],
            'provider' => ['@type' => 'EducationalOrganization', 'name' => $partner['display_name']],
        ], $courses),
    ],
];
echo json_encode($partnerSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
?></script>
<?php endif ?>
<main class="partner-profile">
  <header class="pp-hero">
    <div class="pp-wrap">
      <nav class="pp-breadcrumb" aria-label="Breadcrumb"><a href="<?= base_url('/') ?>">Home</a> / <a href="<?= base_url('/partners') ?>">Partners</a> / <?= e($partner['display_name']) ?></nav>
      <div class="pp-brand">
        <?php if (!empty($partner['logo_path'])): ?><img class="pp-logo" src="<?= e(base_url('/'.ltrim($partner['logo_path'],'/'))) ?>" alt="<?= e($partner['display_name']) ?> logo">
        <?php else: ?><div class="pp-logo" aria-hidden="true" style="display:grid;place-items:center;color:#a41f2d;font-weight:800;font-size:1.4rem"> <?= e($isJmk?'JMK':'TCM') ?> </div><?php endif ?>
        <div class="pp-heading"><div class="pp-kicker"><i class="bi bi-patch-check-fill" aria-hidden="true"></i> TCM One Partner</div><h1><?= e($partner['display_name']) ?></h1><div class="pp-location"><i class="bi bi-geo-alt" aria-hidden="true"></i> <?= e($partner['city']??'') ?><?= !empty($partner['state'])?', '.e($partner['state']):'' ?></div></div>
      </div>
      <div class="pp-actions">
        <?php if (!empty($partner['public_phone'])): ?><a class="tcm-btn tcm-btn-primary" href="tel:<?= e(preg_replace('/[^0-9+]/','',(string)$partner['public_phone'])) ?>"><i class="bi bi-telephone" aria-hidden="true"></i> Call the centre</a><?php endif ?>
        <a class="tcm-btn tcm-btn-secondary" href="#location"><i class="bi bi-map" aria-hidden="true"></i> View map</a>
        <button class="tcm-btn tcm-btn-secondary" type="button" data-share-partner data-share-url="<?= e($shareUrl) ?>" data-share-title="<?= e($socialTitle) ?>"><i class="bi bi-share" aria-hidden="true"></i> Share profile</button>
        <span class="pp-share-status" data-share-status role="status" aria-live="polite"></span>
      </div>
    </div>
  </header>
  <div class="pp-wrap pp-content">
    <div class="pp-main">
      <section class="pp-panel" aria-labelledby="about-partner"><div class="pp-eyebrow">ABOUT THE INSTITUTION</div><h2 id="about-partner">Learning in the local community.</h2><p><?= nl2br(e($partner['description']??'Institution profile')) ?></p></section>
      <section class="pp-panel" aria-labelledby="partner-offerings"><div class="pp-eyebrow">LEARNING OPTIONS</div><h2 id="partner-offerings">Courses &amp; classes</h2>
        <div class="pp-courses">
          <?php if (!$courses): ?><p>Contact the institution for current course availability and schedules.</p><?php endif ?>
          <?php foreach ($courses as $course): ?><article class="pp-course"><div class="pp-course-icon"><i class="bi bi-journal-code" aria-hidden="true"></i></div><h3><?= e($course['title']) ?></h3><p><?= e(mb_strimwidth((string)($course['description']??''),0,190,'…')) ?></p><?php if(!empty($course['duration'])): ?><p style="margin-top:8px"><?= e($course['duration']) ?><?= !empty($course['delivery_mode'])?' · '.e($course['delivery_mode']):'' ?></p><?php endif ?><?php if($course['current_fee']!==null): ?><strong style="display:block;margin-top:10px">₹<?= number_format((float)$course['current_fee']) ?></strong><?php endif ?></article><?php endforeach ?>
        </div>
      </section>
      <section class="pp-panel" id="location" aria-labelledby="partner-location"><div class="pp-eyebrow">FIND THE CENTRE</div><h2 id="partner-location">Location &amp; contact</h2>
        <p><?= e($partner['address']??'') ?><?= !empty($partner['postal_code'])?' · '.e($partner['postal_code']):'' ?></p>
        <div class="pp-contact">
          <?php if(!empty($partner['public_phone'])): ?><a href="tel:<?= e(preg_replace('/[^0-9+]/','',(string)$partner['public_phone'])) ?>"><i class="bi bi-telephone" aria-hidden="true"></i><span><?= e($partner['public_phone']) ?></span></a><?php endif ?>
          <?php if(!empty($partner['whatsapp'])): ?><a href="https://wa.me/<?= e(preg_replace('/\D/','',(string)$partner['whatsapp'])) ?>" target="_blank" rel="noopener"><i class="bi bi-whatsapp" aria-hidden="true"></i><span>Message on WhatsApp</span></a><?php endif ?>
        </div>
        <iframe class="pp-map" title="Map to <?= e($partner['display_name']) ?>" src="<?= e($mapEmbedUrl) ?>" loading="lazy" referrerpolicy="no-referrer-when-downgrade" allowfullscreen></iframe>
        <div class="pp-review"><div><strong><i class="bi bi-star" aria-hidden="true"></i> Google reviews</strong><span><?= $isJmk?'Google listing shows 3 reviews.':'Read learner reviews on Google.' ?> The review count and latest feedback are shown on Google.</span></div><a class="tcm-btn tcm-btn-outline" href="<?= e($googleReviewsUrl) ?>" target="_blank" rel="noopener">See reviews <i class="bi bi-arrow-up-right" aria-hidden="true"></i></a></div>
      </section>
    </div>
    <aside class="pp-aside"><div class="pp-panel pp-enquiry"><div class="pp-eyebrow">GET IN TOUCH</div><h2>Ask about a course</h2><p>Send your question to the institution and they can follow up with you.</p>
      <form method="post" action="<?= base_url('/partners/'.rawurlencode($partner['slug']).'/enquiry') ?>"><?= csrf_field() ?>
        <label>Your name<input name="name" autocomplete="name" required></label><label>Email<input type="email" name="email" autocomplete="email" required></label><label>Phone<input name="phone" autocomplete="tel"></label>
        <label>Course<select name="course_id"><option value="">General enquiry</option><?php foreach($courses as $course): ?><option value="<?= (int)$course['id'] ?>"><?= e($course['title']) ?></option><?php endforeach ?></select></label>
        <label>Message<textarea name="message" rows="3" maxlength="1000" placeholder="What would you like to know?"></textarea></label><label class="pp-consent"><input type="checkbox" name="consent" value="1" required><span>I agree that TCM One may share these enquiry details with admissions on WhatsApp at +91 92386 95500 for follow-up.</span></label><p class="pp-whatsapp-note">After saving, WhatsApp opens with your enquiry filled in. Tap Send to share it with admissions.</p><button class="tcm-btn tcm-btn-primary" type="submit">Continue to WhatsApp <i class="bi bi-whatsapp" aria-hidden="true"></i></button>
      </form>
    </div></aside>
  </div>
</main>
<script>
(() => {
  const button = document.querySelector('[data-share-partner]');
  const status = document.querySelector('[data-share-status]');
  if (!button || !status) return;
  button.addEventListener('click', async () => {
    const url = button.dataset.shareUrl || window.location.href;
    const title = button.dataset.shareTitle || document.title;
    try {
      if (navigator.share) {
        await navigator.share({ title, text: 'View this partner profile on TCM One.', url });
        status.textContent = 'Profile shared.';
      } else if (navigator.clipboard?.writeText) {
        await navigator.clipboard.writeText(url);
        status.textContent = 'Share link copied.';
      } else {
        window.prompt('Copy this partner profile link:', url);
      }
    } catch (error) {
      if (error?.name !== 'AbortError') status.textContent = 'Copy this page address to share the profile.';
    }
  });
})();
</script>
