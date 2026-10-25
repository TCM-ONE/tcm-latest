<?php
$isActivePartner = ($ctx['partner']['status'] ?? '') === 'active';
$canViewFinance = ($ctx['membership']['member_role'] ?? '') === 'partner_owner'
    || (int) ($ctx['membership']['can_view_finance'] ?? 0) === 1;
$quickActions = [
    ['label' => 'Institution profile', 'hint' => 'Update your public details', 'href' => '/partner/profile', 'icon' => 'bi-building'],
    ['label' => 'Account settings', 'hint' => 'Manage your team and access', 'href' => '/partner/settings', 'icon' => 'bi-gear'],
];
if ($isActivePartner) {
    $quickActions = [
        ['label' => 'Add a student', 'hint' => 'Register a learner', 'href' => '/partner/students', 'icon' => 'bi-person-plus'],
        ['label' => 'Batches', 'hint' => 'View or request a batch', 'href' => '/partner/batches', 'icon' => 'bi-calendar3'],
        ['label' => 'Courses', 'hint' => 'Submit an institute course', 'href' => '/partner/courses', 'icon' => 'bi-journal-bookmark'],
        ['label' => 'Mentor request', 'hint' => 'Connect learners with a mentor', 'href' => '/partner/mentor', 'icon' => 'bi-person-workspace'],
        ['label' => 'Certifications', 'hint' => 'Request learner certificates', 'href' => '/partner/certifications', 'icon' => 'bi-award'],
        ['label' => 'Campus visit', 'hint' => 'Plan a TCM visit', 'href' => '/partner/visits', 'icon' => 'bi-geo-alt'],
        ['label' => 'Institution profile', 'hint' => 'Update your public details', 'href' => '/partner/profile', 'icon' => 'bi-building'],
        ['label' => 'Account settings', 'hint' => 'Manage your team and access', 'href' => '/partner/settings', 'icon' => 'bi-gear'],
    ];
    if ($canViewFinance) {
        array_splice($quickActions, 3, 0, [[
            'label' => 'Finance & ledger', 'hint' => 'Review collections and settlements', 'href' => '/partner/finance', 'icon' => 'bi-cash-coin',
        ]]);
    }
}
?>
<div class="p-info">Welcome back, <strong><?= e($ctx['partner']['display_name']) ?></strong>. These figures use submitted partner records; only reviewed enrollments and verified collections are reflected in operational reporting.</div>
<?php if (!$isActivePartner): ?><div class="p-info">Your partner login is ready. You can update your institution profile while the agreement is pending; student, batch, course, request, and finance actions unlock after TCM One activates the partnership.</div><?php endif ?>
<section class="p-section p-quick-section" aria-labelledby="partner-quick-actions">
  <div class="p-quick-heading"><div><h2 id="partner-quick-actions">Quick actions</h2><p><?= $isActivePartner ? 'Your most-used partner tools, ready to open.' : 'Update your profile or manage account access while activation is pending.' ?></p></div><span class="p-quick-caption"><i class="bi bi-lightning-charge-fill" aria-hidden="true"></i> Shortcuts</span></div>
  <div class="p-quick-grid">
    <?php foreach ($quickActions as $action): ?>
      <a class="p-quick-action" href="<?= base_url($action['href']) ?>">
        <span class="p-quick-icon"><i class="bi <?= e($action['icon']) ?>" aria-hidden="true"></i></span>
        <span class="p-quick-copy"><strong><?= e($action['label']) ?></strong><small><?= e($action['hint']) ?></small></span>
        <i class="bi bi-arrow-up-right p-quick-arrow" aria-hidden="true"></i>
      </a>
    <?php endforeach ?>
  </div>
</section>
<div class="p-grid">
  <section class="p-card"><h3>Total Students</h3><div class="p-value"><?= number_format($stats['students']) ?></div><div class="p-trend">Partner-attributed records</div></section>
  <section class="p-card"><h3>Active Batches</h3><div class="p-value"><?= number_format($stats['batches']) ?></div><div class="p-trend">Admissions open, upcoming, or active</div></section>
  <section class="p-card"><h3>Verified Collections</h3><div class="p-value">₹<?= number_format($stats['collections'], 2) ?></div><div class="p-trend">Lifetime verified eligible amount</div></section>
  <?php if ($finance): ?>
    <section class="p-card"><h3>Partner Earnings</h3><div class="p-value">₹<?= number_format($finance['partner_earnings'], 2) ?></div><div class="p-trend">Verified lifetime entitlements</div></section>
    <section class="p-card"><h3>Pending Settlement</h3><div class="p-value">₹<?= number_format($finance['owes_partner'], 2) ?></div><div class="p-trend">TCM One owes partner after actual transfers</div></section>
  <?php else: ?>
    <section class="p-card"><h3>Partner Earnings</h3><div class="p-value">Restricted</div><div class="p-trend">Ask your owner to enable finance access</div></section>
    <section class="p-card"><h3>Pending Settlement</h3><div class="p-value">Restricted</div><div class="p-trend">Ask your owner to enable finance access</div></section>
  <?php endif ?>
  <section class="p-card"><h3>Pending Requests</h3><div class="p-value"><?= number_format($stats['pending_requests']) ?></div><div class="p-trend">Service requests awaiting completion</div></section>
</div>
<section class="p-section"><h2>Recent activity</h2><?php if (!$activity): ?><div class="p-empty">No partner activity yet.</div><?php else: ?><div class="p-table-wrap"><table class="p-table"><thead><tr><th>Activity</th><th>Date</th></tr></thead><tbody><?php foreach ($activity as $item): ?><tr><td><?= e(ucwords(str_replace('_', ' ', $item['action']))) ?></td><td><?= e(date('d M Y, g:i a', strtotime($item['created_at']))) ?></td></tr><?php endforeach ?></tbody></table></div><?php endif ?></section>
