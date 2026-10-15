<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <link rel="icon" type="image/svg+xml" href="<?= base_url('/favicon.svg') ?>">
  <link rel="icon" type="image/png" sizes="32x32" href="<?= base_url('/favicon.png') ?>">
  <link rel="shortcut icon" href="<?= base_url('/favicon.ico') ?>">

  <title><?= e($title ?? 'Payment Incomplete — TCM One') ?></title>
  <meta name="description" content="<?= e($description ?? 'Payment could not be completed.') ?>">
  <meta name="robots" content="noindex, follow">

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Instrument+Serif:ital@0;1&family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

  <link rel="stylesheet" href="<?= base_url('/assets/tcm-design-system.css?v=2.18') ?>">
  <link rel="stylesheet" href="<?= base_url('/assets/tcm-editorial.css?v=2.18') ?>">
  <link rel="stylesheet" href="<?= base_url('/assets/tcm-footer.css?v=1') ?>">

  <style>
    .tcm-receipt-card {
      background: #FFFFFF;
      border: 1px solid var(--tcm-border);
      max-width: 620px;
      margin: 0 auto;
      padding: clamp(24px, 5vw, 44px);
      box-shadow: 0 12px 32px rgba(0,0,0,0.03);
    }
  </style>
</head>
<body>

  <?php \TCM\Core\View::partial('partials.public-header'); ?>

  <main style="padding: clamp(40px, 6vw, 72px) 0; background: var(--tcm-bg); min-height: 75vh;">
    <div class="tcm-container">
      <div class="tcm-receipt-card">

        <div style="text-align: center; margin-bottom: 24px;">
          <div style="width: 56px; height: 56px; border-radius: 50%; background: #FEF2F2; color: #DC2626; display: inline-flex; align-items: center; justify-content: center; font-size: 28px; margin-bottom: 16px;">
            <i class="bi bi-exclamation-triangle"></i>
          </div>
          <div class="tcm-eyebrow" style="margin-bottom: 6px; color: #DC2626;">TRANSACTION INCOMPLETE</div>
          <h1 class="tcm-h2" style="font-size: clamp(26px, 4vw, 34px); margin: 0 0 10px 0;">
            Payment Not <span class="font-editorial-serif">Completed</span>
          </h1>
          <p class="tcm-body-muted" style="margin: 0; font-size: 14.5px;">
            Your transaction was declined, cancelled, or timed out. No funds were debited, or any deducted funds will be refunded by your bank within 3–5 working days.
          </p>
        </div>

        <div style="background: #FFF8F8; border: 1px solid #FECACA; padding: 16px; margin-bottom: 24px; font-size: 13.5px; color: #991B1B;">
          <strong>Error Details:</strong> <?= e($reason ?? 'Transaction was cancelled or declined by user/bank.') ?>
          <?php if (!empty($order['order_number'])): ?>
            <div style="margin-top: 6px; font-size: 12px; color: #7F1D1D;">Order Reference: <?= e((string)$order['order_number']) ?></div>
          <?php endif; ?>
        </div>

        <div style="display: flex; gap: 12px; flex-wrap: wrap;">
          <a href="<?= base_url('/courses') ?>" class="tcm-btn tcm-btn-primary" style="flex: 1; height: 46px; justify-content: center;">
            <i class="bi bi-arrow-repeat" style="margin-right: 6px;"></i>
            <span>Browse Courses &amp; Try Again</span>
          </a>
          <a href="https://wa.me/919238695500?text=<?= rawurlencode('Hi TCM One, my online payment failed. Please assist me with payment options.') ?>" target="_blank" rel="noopener" class="tcm-btn tcm-btn-secondary" style="height: 46px; padding: 0 20px;">
            <i class="bi bi-whatsapp"></i>
            <span>Help on WhatsApp</span>
          </a>
        </div>

      </div>
    </div>
  </main>

  <?php \TCM\Core\View::partial('partials.public-footer'); ?>

</body>
</html>
