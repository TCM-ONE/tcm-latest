<?php
$confirmed = $submission['status'] === 'approved' && $enrollmentId;
$rejected = $submission['status'] === 'rejected';
$preferredStart = empty($details['Cohort start']) ? null : (new DateTimeImmutable($details['Cohort start']))
    ->setTimezone(new DateTimeZone($details['Time zone']))->format('M j, Y, g:i A T');
?>
<div class="tcm-page-head">
  <div>
    <h2><?= $confirmed ? 'Welcome to TCMOne!' : ($rejected ? 'Payment was not approved' : 'Enrollment pending') ?></h2>
    <p><?= $confirmed ? 'Your enrollment has been confirmed. You can access your course from the dashboard.'
        : ($rejected ? 'Contact student support for help with your bank transfer.' : 'We received your transfer proof. Payment is pending review.') ?></p>
  </div>
</div>
<div class="tcm-card" style="max-width:680px">
  <dl>
    <dt>Student</dt><dd><?= e($user['name']) ?></dd>
    <dt>Course</dt><dd><?= e($submission['item_title']) ?></dd>
    <?php if ($confirmed): ?><dt>Enrollment ID</dt><dd><?= e((string) $enrollmentId) ?></dd><?php endif; ?>
    <dt><?= $confirmed ? 'Amount paid' : 'Amount submitted' ?></dt><dd>INR <?= e(number_format((float) $submission['amount'], 2)) ?></dd>
    <dt>Payment status</dt><dd><?= e(\TCM\Models\PaymentSubmission::STATUSES[$submission['status']] ?? $submission['status']) ?></dd>
    <dt>Time zone</dt><dd><?= e($details['Time zone'] ?? '') ?></dd>
    <?php if ($preferredStart): ?><dt>Preferred cohort start</dt><dd><?= e($preferredStart) ?></dd><?php endif; ?>
    <dt>Live cohort assignment</dt><dd>Contact admissions to confirm your cohort.</dd>
  </dl>
  <div style="display:flex;flex-wrap:wrap;gap:8px">
    <a class="tcm-btn primary" href="<?= e(base_url('/student')) ?>">Go to student dashboard</a>
    <?php if ($confirmed): ?>
      <a class="tcm-btn" href="<?= e(base_url('/student/payments/' . (int) $submission['id'] . '/receipt')) ?>">Download payment receipt</a>
    <?php endif; ?>
    <a class="tcm-btn" href="<?= e(base_url('/contact')) ?>">Contact student support</a>
  </div>
</div>
