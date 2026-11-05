<style>
.partner-profile{--pp-line:#e7e2dd;--pp-muted:#686762;--pp-red:#a41f2d;color:#171717;padding-bottom:clamp(48px,7vw,84px);overflow-x:clip}
.partner-profile *{box-sizing:border-box}
.pp-wrap{width:min(1160px,calc(100% - 48px));margin-inline:auto}
.pp-hero{position:relative;isolation:isolate;overflow:hidden;background:linear-gradient(115deg,#fbfaf8 0%,#f7f5f1 68%,#f5ecea 100%);border-bottom:1px solid var(--pp-line);padding:clamp(34px,5vw,64px) 0 clamp(30px,4vw,48px)}
.pp-hero:after{position:absolute;z-index:-1;right:-100px;top:-240px;width:520px;height:520px;border:1px solid rgba(164,31,45,.1);border-radius:50%;box-shadow:0 0 0 36px rgba(164,31,45,.025),0 0 0 76px rgba(164,31,45,.02);content:"";pointer-events:none}
.pp-breadcrumb{font-size:.78rem;color:var(--pp-muted);margin:0 0 28px;line-height:1.6;overflow-wrap:anywhere}.pp-breadcrumb a{color:inherit;text-decoration:none}.pp-breadcrumb a:hover{color:var(--pp-red)}
.pp-brand{display:grid;grid-template-columns:112px minmax(0,1fr);align-items:center;gap:24px;max-width:900px}.pp-logo{width:112px;height:112px;display:block;object-fit:contain;flex:0 0 auto;background:#fff;border:1px solid var(--pp-line);padding:10px;box-shadow:0 10px 30px rgba(24,18,16,.06)}
.pp-heading{min-width:0}.pp-kicker,.pp-eyebrow{font-size:.68rem;font-weight:750;letter-spacing:.14em;text-transform:uppercase;color:#686762}.pp-kicker{display:flex;align-items:center;gap:8px}.pp-kicker i{color:var(--pp-red)}
.pp-heading h1{font-family:var(--font-display,"Instrument Serif",Georgia,serif);font-size:clamp(2.7rem,5.5vw,5rem);line-height:.98;font-weight:400;letter-spacing:-.035em;margin:10px 0 12px;max-width:780px;overflow-wrap:anywhere}
.pp-location{display:flex;align-items:center;gap:7px;color:var(--pp-muted);font-size:.96rem;line-height:1.5;overflow-wrap:anywhere}.pp-location i{color:var(--pp-red)}
.pp-actions{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:10px;max-width:790px;margin-top:28px}.pp-actions .tcm-btn{min-width:0;min-height:52px;display:flex;justify-content:center;align-items:center;gap:9px;padding:12px 16px;text-align:center;white-space:normal;overflow-wrap:anywhere;line-height:1.25}.pp-actions .tcm-btn-primary{background:#171717;border-color:#171717;color:#fff}.pp-actions .tcm-btn-primary:hover{background:var(--pp-red);border-color:var(--pp-red)}.pp-actions .tcm-btn-secondary{background:#fff;border-color:#dcd7d2;color:#171717}.pp-actions .tcm-btn-secondary:hover{border-color:var(--pp-red);color:var(--pp-red)}
.pp-share-status{grid-column:1/-1;min-height:1.2em;color:var(--pp-muted);font-size:.78rem}.pp-content{display:grid;grid-template-columns:minmax(0,1.65fr) minmax(300px,.8fr);gap:24px;padding-top:30px}.pp-main,.pp-aside{min-width:0}.pp-panel{background:#fff;border:1px solid var(--pp-line);padding:clamp(22px,3vw,32px);margin-bottom:18px;box-shadow:0 8px 22px rgba(30,26,22,.025)}
.pp-panel h2{font-family:var(--font-display,"Instrument Serif",Georgia,serif);font-size:clamp(1.8rem,3vw,2.35rem);line-height:1.05;font-weight:400;letter-spacing:-.02em;margin:7px 0 13px}.pp-panel p{color:var(--pp-muted);line-height:1.75;margin:0;overflow-wrap:anywhere}
.pp-courses{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:12px;margin-top:20px}.pp-course{position:relative;min-width:0;background:#fbfaf8;border:1px solid var(--pp-line);padding:18px;transition:border-color .18s,transform .18s}.pp-course:hover{border-color:#c9a5a2;transform:translateY(-2px)}.pp-course-icon{width:42px;height:42px;display:grid;place-items:center;background:#f5eaea;color:var(--pp-red);font-size:1.15rem;margin-bottom:14px}.pp-course h3{font-size:1rem;line-height:1.4;margin:0 0 7px;overflow-wrap:anywhere}.pp-course p{font-size:.86rem;line-height:1.65}.pp-course strong{color:#171717}
.pp-contact{display:grid;gap:10px;margin-top:18px}.pp-contact a{display:flex;min-width:0;gap:10px;align-items:flex-start;color:#171717;text-decoration:none;font-size:.9rem;line-height:1.5;overflow-wrap:anywhere}.pp-contact a:hover{color:var(--pp-red)}.pp-contact i{flex:0 0 auto;color:var(--pp-red);font-size:1.05rem}.pp-map{margin-top:20px;border:1px solid var(--pp-line);width:100%;height:300px;display:block;background:#f2f0eb}.pp-review{display:flex;align-items:center;justify-content:space-between;gap:16px;margin-top:16px;padding:17px;background:#fbfaf8;border:1px solid var(--pp-line)}.pp-review>div{min-width:0}.pp-review strong{display:block;margin-bottom:5px}.pp-review span{display:block;font-size:.82rem;line-height:1.55;color:var(--pp-muted)}.pp-review .tcm-btn{flex:0 0 auto;justify-content:center;gap:7px}
.pp-enquiry{position:sticky;top:18px}.pp-enquiry form{margin-top:16px}.pp-enquiry label{display:grid;gap:7px;margin:13px 0;font-size:.78rem;font-weight:700}.pp-enquiry input,.pp-enquiry textarea,.pp-enquiry select{width:100%;min-width:0;padding:12px;border:1px solid #d8d5cf;border-radius:0;background:#fff;color:#171717;font:inherit;font-size:.92rem}.pp-enquiry input:focus,.pp-enquiry textarea:focus,.pp-enquiry select:focus{outline:2px solid rgba(164,31,45,.22);border-color:var(--pp-red)}.pp-enquiry textarea{resize:vertical}.pp-enquiry button{width:100%;min-height:50px;display:flex;justify-content:center;align-items:center;gap:8px;margin-top:10px}.pp-consent{display:grid!important;grid-template-columns:18px minmax(0,1fr)!important;align-items:start;gap:9px!important;font-weight:400!important;line-height:1.5}.pp-consent input{width:16px;height:16px;margin:2px 0 0;accent-color:var(--pp-red)}.pp-whatsapp-note{font-size:.76rem!important;line-height:1.55!important;margin:10px 0!important}
@media(max-width:900px){.pp-content{grid-template-columns:minmax(0,1fr) minmax(270px,.8fr);gap:16px}.pp-panel{padding:22px}.pp-courses{grid-template-columns:1fr}}
@media(max-width:760px){.pp-content{grid-template-columns:1fr;gap:4px;padding-top:20px}.pp-aside{order:-1}.pp-enquiry{position:static}.pp-enquiry form{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));column-gap:12px}.pp-enquiry form>label,.pp-enquiry form>p,.pp-enquiry form>button{min-width:0}.pp-enquiry form>label:nth-of-type(5),.pp-enquiry form>.pp-consent,.pp-enquiry form>.pp-whatsapp-note,.pp-enquiry form>button{grid-column:1/-1}}
@media(max-width:560px){.partner-profile{padding-bottom:34px}.pp-wrap{width:calc(100% - 32px)}.pp-hero{padding:24px 0 22px}.pp-breadcrumb{font-size:.72rem;margin-bottom:20px}.pp-brand{grid-template-columns:58px minmax(0,1fr);align-items:start;gap:12px}.pp-logo{width:58px;height:58px;padding:5px}.pp-kicker{font-size:.59rem;letter-spacing:.1em;line-height:1.4}.pp-heading h1{font-size:clamp(2rem,8vw,2.65rem);line-height:1.01;margin:6px 0 8px}.pp-location{font-size:.82rem;line-height:1.4}.pp-actions{grid-template-columns:repeat(2,minmax(0,1fr));gap:8px;margin-top:20px}.pp-actions .tcm-btn{min-height:48px;padding:10px 8px;font-size:.82rem;gap:6px}.pp-actions .pp-action-call{grid-column:1/-1}.pp-share-status{font-size:.72rem;text-align:center}.pp-content{padding-top:14px}.pp-panel{padding:20px 17px;margin-bottom:13px}.pp-panel h2{font-size:1.85rem;margin-bottom:10px}.pp-panel p{font-size:.91rem}.pp-courses{grid-template-columns:1fr;gap:9px;margin-top:15px}.pp-course{padding:15px}.pp-course-icon{margin-bottom:10px}.pp-map{height:240px;margin-top:16px}.pp-review{align-items:stretch;flex-direction:column;gap:12px;padding:14px}.pp-review .tcm-btn{width:100%;min-height:46px}.pp-enquiry form{grid-template-columns:1fr}.pp-enquiry form>label,.pp-enquiry form>p,.pp-enquiry form>button{grid-column:1}.pp-enquiry button{min-height:48px}.pp-enquiry input,.pp-enquiry textarea,.pp-enquiry select{font-size:16px}}
@media(max-width:360px){.pp-wrap{width:calc(100% - 26px)}.pp-brand{grid-template-columns:50px minmax(0,1fr);gap:10px}.pp-logo{width:50px;height:50px}.pp-heading h1{font-size:1.95rem}.pp-actions .tcm-btn{font-size:.76rem;padding:9px 6px}.pp-panel{padding:17px 14px}}
@media(prefers-reduced-motion:reduce){.pp-course{transition:none}}
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
        <?php if (!empty($partner['public_phone'])): ?><a class="tcm-btn tcm-btn-primary pp-action-call" href="tel:<?= e(preg_replace('/[^0-9+]/','',(string)$partner['public_phone'])) ?>"><i class="bi bi-telephone" aria-hidden="true"></i> Call the centre</a><?php endif ?>
        <a class="tcm-btn tcm-btn-secondary pp-action-map" href="#location"><i class="bi bi-map" aria-hidden="true"></i> View map</a>
        <button class="tcm-btn tcm-btn-secondary pp-action-share" type="button" data-share-partner data-share-url="<?= e($shareUrl) ?>" data-share-title="<?= e($socialTitle) ?>"><i class="bi bi-share" aria-hidden="true"></i> Share profile</button>
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
