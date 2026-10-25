<?php if (!empty($diwaliOfferActive)): ?>
<section class="diwali-offer" data-diwali-campaign data-reload-on-expiry="1" data-ends-at="<?= e($diwaliOfferEndsAt ?? '2026-11-11T00:00:00+05:30') ?>" aria-label="Diwali course offer">
  <div class="diwali-offer-copy">
    <span class="diwali-offer-kicker">🪔 DIWALI SPECIAL · 60% OFF ON FEES ABOVE ₹8K</span>
    <h2>Learn today. Build tomorrow.</h2>
    <p>₹8,000 se zyada fee wale courses par 60% off, saath hi selected Diwali prices. Ends 10 November · full payment only.</p>
  </div>
  <div class="diwali-offer-right">
    <div class="diwali-offer-timer" aria-live="polite">
      <span class="diwali-countdown-unit"><strong data-count-days>--</strong><small>Days</small></span>
      <span class="diwali-countdown-unit"><strong data-count-hours>--</strong><small>Hours</small></span>
      <span class="diwali-countdown-unit"><strong data-count-minutes>--</strong><small>Min</small></span>
      <span class="diwali-countdown-unit"><strong data-count-seconds>--</strong><small>Sec</small></span>
    </div>
    <a href="<?= base_url('/courses') ?>" class="diwali-offer-cta">Explore offer <span aria-hidden="true">→</span></a>
  </div>
</section>
<?php endif; ?>
