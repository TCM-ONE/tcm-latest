<!doctype html>
<html lang="en-US">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="app-base" content="<?= e(base_url('/')) ?>">
  <meta name="robots" content="noindex, nofollow">
  <title>International enrollment | TCM One</title>
  <link rel="stylesheet" href="<?= e(base_url('/assets/tcm-design-system.css?v=2.18')) ?>">
  <style>
    body{margin:0;background:#f8f7f5;color:#191919;font:16px/1.5 system-ui,sans-serif}
    .checkout{max-width:1040px;margin:auto;padding:clamp(20px,4vw,52px) 16px 56px}
    .checkout-header{margin-bottom:24px}
    .checkout-header h1{font-size:clamp(2rem,5vw,3rem);line-height:1.1;margin:12px 0}
    .checkout-layout{display:grid;gap:20px;align-items:start;grid-template-columns:minmax(0,1.6fr) minmax(0,1fr)}
    .checkout-panel{padding:clamp(16px,3vw,28px);border:1px solid #dedbd6;background:#fff;border-radius:12px;min-width:0}
    .checkout-panel img{width:100%;height:auto;aspect-ratio:16/9;object-fit:cover;border-radius:8px}
    .checkout-panel h2{font-size:1.25rem;margin:0 0 14px}
    .checkout-panel h3{margin:12px 0 4px}
    .checkout-panel a,.checkout-header a{color:#841b29;text-underline-offset:3px}
    .form-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:14px}
    .form-grid label{display:grid;gap:5px}
    .form-grid label.full{grid-column:1/-1}
    .form-grid :is(input,select,textarea){width:100%;min-height:44px;padding:8px;border:1px solid #aaa49d;border-radius:5px;background:#fff;color:#191919;font:inherit}
    .checkout-consent{display:flex;align-items:flex-start;gap:8px;margin:18px 0}
    .checkout-consent input{margin-top:5px}
    .checkout-button{display:inline-block;background:#841b29;color:#fff;border:0;border-radius:6px;min-height:44px;padding:10px 16px;font:600 1rem system-ui;cursor:pointer}
    .checkout-price{font-size:1.75rem;font-weight:700;margin:4px 0}
    .checkout-errors{border-left:4px solid #841b29;padding:8px 16px;background:#fdf1f2;margin-bottom:16px}
    :focus-visible{outline:2px solid #841b29;outline-offset:3px}
    @media(max-width:720px){.checkout-layout{grid-template-columns:1fr}.checkout-summary{grid-row:1}}
    @media(max-width:430px){.form-grid{grid-template-columns:1fr}}
  </style>
</head>
<body>
<main class="checkout">
  <header class="checkout-header">
    <a href="<?= e(base_url('/courses/' . rawurlencode($course['slug']))) ?>">Back to course details</a>
    <h1>International enrollment</h1>
    <p>Choose a cohort and review your fee. If the available schedule does not fit, send admissions your preferred time.</p>
  </header>
  <?php if ($notice !== null): ?><p role="status"><?= e($notice) ?></p><?php endif; ?>
  <div class="checkout-layout">
    <form method="post" class="checkout-panel" data-international-enrollment="<?= e($course['slug']) ?>">
      <?= csrf_field() ?>
      <input type="hidden" name="reviewed_amount" value="<?= e(number_format($amount, 2, '.', '')) ?>">
      <h2>Your details</h2>
      <?php if ($errors): ?>
        <div class="checkout-errors" role="alert"><ul><?php foreach ($errors as $error): ?><li><?= e($error) ?></li><?php endforeach; ?></ul></div>
      <?php endif; ?>
      <div class="form-grid">
        <label>Full name<input name="name" autocomplete="name" maxlength="120" value="<?= e($fields['name']) ?>" required></label>
        <label>Email<input name="email" type="email" autocomplete="email" maxlength="190" value="<?= e($fields['email']) ?>" required></label>
        <label class="full">Phone or WhatsApp with calling code<input name="phone" type="tel" autocomplete="tel" placeholder="+1 202 555 0123" maxlength="32" value="<?= e($fields['phone']) ?>" required></label>
        <label>Country of residence (2-letter code)<input name="residence" autocomplete="country" maxlength="2" pattern="[A-Za-z]{2}" placeholder="US" value="<?= e($fields['residence']) ?>" required></label>
        <label>Billing country (2-letter code)<input name="billing" autocomplete="billing country" maxlength="2" pattern="[A-Za-z]{2}" placeholder="US" value="<?= e($fields['billing']) ?>" required></label>
        <label>Display currency<select name="currency" id="enrollment-currency">
          <?php foreach ($estimates as $code => $value): ?><option value="<?= e($code) ?>" data-estimate="<?= e($value) ?>" <?= $fields['currency'] === $code ? 'selected' : '' ?>><?= e($code) ?></option><?php endforeach; ?>
        </select></label>
        <label>Time zone<select name="timezone" id="enrollment-timezone" data-detect="<?= $fields['name'] === '' ? '1' : '0' ?>">
          <?php $zoneLabels = ['America/New_York' => 'Eastern · New York', 'America/Chicago' => 'Central · Chicago', 'America/Denver' => 'Mountain · Denver', 'America/Los_Angeles' => 'Pacific · Los Angeles', 'Asia/Kolkata' => 'India · Kolkata']; ?>
          <?php foreach ($zoneLabels + array_combine($zones, $zones) as $zone => $label): ?><option value="<?= e($zone) ?>" <?= $fields['timezone'] === $zone ? 'selected' : '' ?>><?= e($label) ?></option><?php endforeach; ?>
        </select></label>
        <label class="full">Cohort preference<select name="batch" id="enrollment-batch" required>
          <option value="">Choose a cohort or request a time</option>
          <?php if ($canTransfer): ?>
            <?php foreach ($batches as $id => $start): ?><option value="<?= e((string) $id) ?>" data-start="<?= e($start) ?>" <?= $fields['batch'] === (string) $id ? 'selected' : '' ?>><?= e((new DateTimeImmutable($start))->format('M j, Y, g:i A')) ?> IST</option><?php endforeach; ?>
          <?php endif; ?>
          <option value="request" <?= $fields['batch'] === 'request' ? 'selected' : '' ?>>Request a different time</option>
        </select></label>
        <label class="full">Preferred days and times if requesting a different cohort<textarea name="timing" maxlength="240" rows="2"><?= e($fields['timing']) ?></textarea></label>
        <label class="full">How did you discover TCMOne? (optional)<select name="discovery">
          <option value="">Select an option</option>
          <?php foreach ($discoveryOptions as $source): ?><option <?= $fields['discovery'] === $source ? 'selected' : '' ?>><?= e($source) ?></option><?php endforeach; ?>
        </select></label>
      </div>
      <p><a href="<?= e(base_url('/terms')) ?>">Terms and conditions</a> · <a href="<?= e(base_url('/privacy')) ?>">Privacy policy</a> · <a href="<?= e(base_url('/refund-policy')) ?>">Refund and cancellation policy</a></p>
      <label class="checkout-consent"><input type="checkbox" name="consent" value="1" required> I’ve reviewed the policies and understand that any payment is made in INR.</label>
      <button type="submit" class="checkout-button">Continue</button>
    </form>
    <aside class="checkout-panel checkout-summary" aria-labelledby="enrollment-summary">
      <h2 id="enrollment-summary">Enrollment summary</h2>
      <img src="<?= e(course_thumbnail_url($course['thumbnail'] ?? null, $course['slug'])) ?>" alt="">
      <h3><?= e($course['title']) ?></h3>
      <p><?= e($course['duration'] ?: 'Confirm duration with admissions') ?> · Online</p>
      <?php if (!$batches): ?><p>There are currently no selectable cohorts. You can request a preferred time.</p><?php endif; ?>
      <p><?= $canTransfer ? 'Pay in full by bank transfer' : 'Tuition estimate · Contact admissions for payment availability' ?></p>
      <p class="checkout-price">INR <?= e(number_format($amount, 2)) ?></p>
      <p id="enrollment-estimate" aria-live="polite"><?= $fields['currency'] !== 'INR' ? e($estimates[$fields['currency']] ?? '') : '' ?></p>
      <?php if ($canTransfer): ?>
        <p>Your bank may apply conversion or transfer fees. Sign in with the email above to view transfer instructions and submit proof. Admissions confirms payment and cohort assignment after review.</p>
      <?php endif; ?>
      <a href="<?= e(base_url('/contact')) ?>">Contact student support</a>
    </aside>
  </div>
</main>
<script src="<?= e(base_url('/assets/tcm-editorial.js?v=2.26')) ?>" defer></script>
<script src="<?= e(base_url('/assets/international-enrollment.js?v=2')) ?>" defer></script>
</body>
</html>
