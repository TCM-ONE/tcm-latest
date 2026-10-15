<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <link rel="icon" type="image/svg+xml" href="<?= base_url('/favicon.svg') ?>">
  <link rel="icon" type="image/png" sizes="32x32" href="<?= base_url('/favicon.png') ?>">
  <link rel="shortcut icon" href="<?= base_url('/favicon.ico') ?>">

  <title><?= e($title ?? 'Payment Confirmed — TCM One') ?></title>
  <meta name="description" content="<?= e($description ?? 'Your enrollment and payment have been verified.') ?>">
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
      max-width: 680px;
      margin: 0 auto;
      padding: clamp(24px, 5vw, 44px);
      box-shadow: 0 12px 32px rgba(0,0,0,0.03);
    }
    .tcm-receipt-table {
      width: 100%;
      border-collapse: collapse;
      margin: 24px 0;
    }
    .tcm-receipt-table th,
    .tcm-receipt-table td {
      padding: 12px 0;
      border-bottom: 1px solid var(--tcm-border);
      font-size: 14px;
      text-align: left;
    }
    .tcm-receipt-table td:last-child {
      text-align: right;
      font-weight: 600;
      color: var(--tcm-fg);
    }
    .tcm-receipt-status {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      background: #ECFDF5;
      color: #059669;
      border: 1px solid #A7F3D0;
      font-size: 12px;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 0.06em;
      padding: 4px 10px;
    }
    @media print {
      header, footer, .no-print { display: none !important; }
      body { background: #fff !important; }
      .tcm-receipt-card { border: none !important; box-shadow: none !important; padding: 0 !important; }
    }
  </style>
</head>
<body>

  <?php \TCM\Core\View::partial('partials.public-header'); ?>

  <main style="padding: clamp(40px, 6vw, 72px) 0; background: var(--tcm-bg); min-height: 75vh;">
    <div class="tcm-container">
      <div class="tcm-receipt-card">

        <div style="text-align: center; margin-bottom: 28px;">
          <div style="width: 56px; height: 56px; border-radius: 50%; background: #ECFDF5; color: #059669; display: inline-flex; align-items: center; justify-content: center; font-size: 28px; margin-bottom: 16px;">
            <i class="bi bi-check-lg"></i>
          </div>
          <div class="tcm-eyebrow" style="margin-bottom: 6px;">OFFICIAL RECEIPT &middot; VERIFIED</div>
          <h1 class="tcm-h2" style="font-size: clamp(26px, 4vw, 36px); margin: 0 0 10px 0;">
            Payment <span class="font-editorial-serif">Confirmed!</span>
          </h1>
          <p class="tcm-body-muted" style="margin: 0; font-size: 14.5px;">
            Thank you for enrolling with <strong>The Code Munk (TCM One)</strong>. Your seat has been reserved and your cohort access is ready.
          </p>
        </div>

        <?php if (!empty($order)): ?>
          <table class="tcm-receipt-table">
            <tr>
              <th style="font-weight: 500; color: var(--tcm-muted);">Order Number</th>
              <td><?= e((string)$order['order_number']) ?></td>
            </tr>
            <tr>
              <th style="font-weight: 500; color: var(--tcm-muted);">Program / Course</th>
              <td><?= e((string)$order['item_title']) ?></td>
            </tr>
            <tr>
              <th style="font-weight: 500; color: var(--tcm-muted);">Amount Paid</th>
              <td style="font-size: 18px; color: var(--tcm-fg);">₹<?= number_format((float)$order['amount'], 2) ?></td>
            </tr>
            <tr>
              <th style="font-weight: 500; color: var(--tcm-muted);">Payment Method</th>
              <td><?= strtoupper((string)($order['payment_method'] ?? 'PAYU')) ?> (Online Verified)</td>
            </tr>
            <?php if (!empty($order['payment_ref'])): ?>
            <tr>
              <th style="font-weight: 500; color: var(--tcm-muted);">Transaction Ref</th>
              <td style="font-family: monospace; font-size: 13px;"><?= e((string)$order['payment_ref']) ?></td>
            </tr>
            <?php endif; ?>
            <tr>
              <th style="font-weight: 500; color: var(--tcm-muted);">Status</th>
              <td><span class="tcm-receipt-status"><i class="bi bi-shield-check"></i> Paid &middot; Confirmed</span></td>
            </tr>
            <tr>
              <th style="font-weight: 500; color: var(--tcm-muted);">Date &amp; Time</th>
              <td><?= date('d M Y, h:i A', strtotime((string)$order['created_at'])) ?> IST</td>
            </tr>
          </table>
        <?php else: ?>
          <div style="padding: 20px; background: var(--tcm-surface); border: 1px solid var(--tcm-border); text-align: center; margin: 20px 0;">
            <p style="margin: 0; font-size: 14px;">Your payment was verified successfully. Confirmation has been sent to your email.</p>
          </div>
        <?php endif; ?>

        <div style="background: var(--tcm-surface); border: 1px solid var(--tcm-border); padding: 18px; margin-bottom: 28px; font-size: 13.5px; line-height: 1.6;">
          <strong style="display: block; margin-bottom: 6px; color: var(--tcm-fg);"><i class="bi bi-info-circle" style="margin-right: 4px;"></i> What Happens Next?</strong>
          <ul style="margin: 0; padding-left: 20px; color: var(--tcm-muted);">
            <li>A detailed tax invoice and registration receipt has been sent to your email.</li>
            <li>Log in to your <strong>Student Portal</strong> using your registered email to access course materials and schedule.</li>
            <li>Our academic coordinator will reach out on WhatsApp within 2 hours with cohort orientation details.</li>
          </ul>
        </div>

        <div class="no-print" style="display: flex; gap: 12px; flex-wrap: wrap;">
          <a href="<?= base_url('/student') ?>" class="tcm-btn tcm-btn-primary" style="flex: 1; height: 46px; justify-content: center;">
            <span>Go to Student Portal</span>
            <i class="bi bi-arrow-right" style="margin-left: 6px;"></i>
          </a>
          <button type="button" onclick="window.print()" class="tcm-btn tcm-btn-secondary" style="height: 46px; padding: 0 18px;">
            <i class="bi bi-printer"></i>
            <span>Print Receipt</span>
          </button>
          <a href="https://wa.me/919238695500?text=<?= rawurlencode('Hi TCM One team! I completed payment for ' . ($order['item_title'] ?? 'my enrollment') . '. Order Number: ' . ($order['order_number'] ?? '')) ?>" target="_blank" rel="noopener" class="tcm-btn tcm-btn-outline" style="height: 46px; padding: 0 16px;">
            <i class="bi bi-whatsapp"></i>
            <span>WhatsApp Support</span>
          </a>
        </div>

      </div>
    </div>
  </main>

  <?php \TCM\Core\View::partial('partials.public-footer'); ?>

</body>
</html>
