<?php use TCM\Models\PaymentSubmission; ?>

<div class="tcm-page-head">
    <div>
        <h2>💳 Payments & Billing Hub</h2>
        <p>Monitor PayU gateway transactions, manage student installment schedules, audit webhooks, and review refunds.</p>
    </div>
    <div>
        <button onclick="document.getElementById('modal-create-installment').style.display='flex'" class="tcm-btn primary">
            <i class="bi bi-plus-circle"></i> Create Installment Plan
        </button>
    </div>
</div>

<!-- Stats -->
<div class="tcm-stat-grid" style="margin-bottom:20px;grid-template-columns:repeat(4,1fr);">
    <div class="tcm-stat" style="border-color:#bbf7d0;background:#f0fdf4;">
        <i class="bi bi-shield-check icon" style="color:#16a34a;"></i>
        <div class="label">PayU Collected</div>
        <div class="value">₹<?= number_format($stats['total_online'], 0) ?></div>
    </div>
    <div class="tcm-stat" style="border-color:#bfdbfe;background:#eff6ff;">
        <i class="bi bi-pie-chart-fill icon" style="color:#2563eb;"></i>
        <div class="label">Active Installments</div>
        <div class="value"><?= (int)$stats['active_installments'] ?></div>
    </div>
    <div class="tcm-stat" style="border-color:#fecaca;background:#fef2f2;">
        <i class="bi bi-exclamation-triangle-fill icon" style="color:#dc2626;"></i>
        <div class="label">Overdue Milestones</div>
        <div class="value"><?= (int)$stats['total_overdue'] ?></div>
    </div>
    <div class="tcm-stat" style="border-color:#fde68a;background:#fffbeb;">
        <i class="bi bi-clock-history icon" style="color:#d97706;"></i>
        <div class="label">Manual Submissions</div>
        <div class="value"><?= (int)$counts['pending'] ?></div>
    </div>
</div>

<!-- Navigation Tabs -->
<div style="border-bottom: 2px solid #e2e8f0; margin-bottom: 20px;">
    <div style="display: flex; gap: 6px; overflow-x: auto;">
        <button onclick="showTab('online')" id="tab-btn-online" class="tab-btn <?= $activeTab === 'online' ? 'active' : '' ?>">
            <i class="bi bi-credit-card"></i> PayU Online Orders (<?= count($onlineOrders) ?>)
        </button>
        <button onclick="showTab('installments')" id="tab-btn-installments" class="tab-btn <?= $activeTab === 'installments' ? 'active' : '' ?>">
            <i class="bi bi-pie-chart"></i> Installment Plans (<?= count($installmentPlans) ?>)
        </button>
        <button onclick="showTab('settings')" id="tab-btn-settings" class="tab-btn <?= $activeTab === 'settings' ? 'active' : '' ?>">
            <i class="bi bi-sliders"></i> Installment Settings
        </button>
        <button onclick="showTab('refunds')" id="tab-btn-refunds" class="tab-btn <?= $activeTab === 'refunds' ? 'active' : '' ?>">
            <i class="bi bi-arrow-counterclockwise"></i> Refunds & Disputes (<?= count($refunds) + count($disputes) ?>)
        </button>
        <button onclick="showTab('logs')" id="tab-btn-logs" class="tab-btn <?= $activeTab === 'logs' ? 'active' : '' ?>">
            <i class="bi bi-terminal"></i> Webhook Audit Logs (<?= count($auditLogs) ?>)
        </button>
        <button onclick="showTab('manual')" id="tab-btn-manual" class="tab-btn <?= $activeTab === 'manual' ? 'active' : '' ?>">
            <i class="bi bi-journal-text"></i> Manual Submissions (<?= count($payments) ?>)
        </button>
    </div>
</div>

<style>
.tab-btn {
    padding: 10px 18px;
    border: none;
    background: transparent;
    color: #64748b;
    font-weight: 600;
    font-size: 0.88rem;
    cursor: pointer;
    border-bottom: 3px solid transparent;
    transition: all 0.2s;
    white-space: nowrap;
    display: inline-flex;
    align-items: center;
    gap: 7px;
}
.tab-btn:hover {
    color: #0f172a;
    background: #f8fafc;
}
.tab-btn.active {
    color: #0f172a;
    border-bottom-color: #0f172a;
}
.tab-pane { display: none; }
.tab-pane.active { display: block; }
</style>

<!-- ===================================================================== -->
<!-- TAB 1: PayU Online Orders & Transactions -->
<!-- ===================================================================== -->
<div id="pane-online" class="tab-pane <?= $activeTab === 'online' ? 'active' : '' ?>">
    <div class="tcm-card">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;">
            <h3 style="font-size:1.05rem;font-weight:700;margin:0;">PayU Gateway Transactions</h3>
            <span style="font-size:.8rem;color:#64748b;">Verified server-to-server with PayU SHA-512 & PostService API</span>
        </div>

        <?php if (empty($onlineOrders)): ?>
            <div class="tcm-empty">
                <i class="bi bi-credit-card"></i>
                No PayU transactions recorded yet.
            </div>
        <?php else: ?>
        <div style="overflow-x:auto;">
            <table class="tcm-table">
                <thead>
                    <tr>
                        <th>Transaction ID</th>
                        <th>Student</th>
                        <th>Item</th>
                        <th>Amount</th>
                        <th>Mode</th>
                        <th>PayU Mihpayid</th>
                        <th>Verification</th>
                        <th>Date</th>
                        <th>Receipt</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($onlineOrders as $pt): 
                    $isSuccess = ($pt['status'] === 'success');
                    $statusCls = $isSuccess ? 'green' : ($pt['status'] === 'failure' ? 'red' : 'amber');
                ?>
                    <tr>
                        <td>
                            <div style="font-family:monospace;font-size:.82rem;font-weight:700;color:#111;"><?= e($pt['txnid']) ?></div>
                            <?php if (!empty($pt['order_number'])): ?>
                                <div style="font-size:.72rem;color:#64748b;"><?= e($pt['order_number']) ?></div>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div style="font-weight:600;font-size:.88rem;color:#111;"><?= e($pt['user_name'] ?? 'Student #' . $pt['user_id']) ?></div>
                            <div style="font-size:.74rem;color:var(--muted);"><?= e($pt['user_email'] ?? '') ?></div>
                        </td>
                        <td>
                            <span class="tcm-badge <?= ($pt['item_type'] ?? '') === 'course' ? 'purple' : 'amber' ?>" style="font-size:.65rem;margin-bottom:3px;display:inline-flex;">
                                <?= e(ucfirst((string)($pt['item_type'] ?? 'Course'))) ?>
                            </span>
                            <div style="font-size:.84rem;font-weight:500;color:#111;"><?= e($pt['item_title'] ?? 'Program Fee') ?></div>
                        </td>
                        <td style="font-weight:700;font-size:.95rem;color:<?= $isSuccess ? '#15803d' : '#111' ?>;">
                            ₹<?= number_format((float)$pt['amount'], 2) ?>
                        </td>
                        <td>
                            <span style="font-size:.78rem;font-weight:600;text-transform:uppercase;color:#475569;">
                                <?= e($pt['payment_mode'] ?: 'UPI / Online') ?>
                            </span>
                        </td>
                        <td>
                            <span style="font-family:monospace;font-size:.78rem;color:#64748b;"><?= e($pt['mihpayid'] ?: '—') ?></span>
                        </td>
                        <td>
                            <div style="display:flex;gap:4px;flex-wrap:wrap;">
                                <span class="tcm-badge <?= $pt['hash_verified'] ? 'green' : 'red' ?>" style="font-size:.65rem;">
                                    <?= $pt['hash_verified'] ? 'Hash Valid ✓' : 'Hash Invalid ✗' ?>
                                </span>
                                <?php if ($pt['api_verified']): ?>
                                    <span class="tcm-badge blue" style="font-size:.65rem;">API Verified ✓</span>
                                <?php endif; ?>
                            </div>
                        </td>
                        <td style="font-size:.8rem;color:var(--muted);"><?= e(date('d M Y, h:i A', strtotime($pt['created_at']))) ?></td>
                        <td>
                            <?php if ($isSuccess && !empty($pt['order_id'])): ?>
                                <a href="<?= base_url('/student/orders/' . (int)$pt['order_id'] . '/receipt') ?>" target="_blank" class="tcm-btn sm outline" title="View Official Receipt">
                                    <i class="bi bi-file-earmark-text"></i> Receipt
                                </a>
                            <?php else: ?>
                                <span style="font-size:.75rem;color:#cbd5e1;">—</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- ===================================================================== -->
<!-- TAB 2: Custom Installment Plans & Student Management -->
<!-- ===================================================================== -->
<div id="pane-installments" class="tab-pane <?= $activeTab === 'installments' ? 'active' : '' ?>">

    <!-- 6 Overview Metric Cards -->
    <div style="display:grid;grid-template-columns:repeat(auto-fit, minmax(180px, 1fr));gap:12px;margin-bottom:20px;">
        <div class="tcm-stat" style="border:1px solid #111;background:#fff;border-radius:0;">
            <i class="bi bi-people-fill icon" style="color:#111;"></i>
            <div class="label" style="font-size:0.68rem;text-transform:uppercase;font-weight:700;letter-spacing:0.05em;color:#71717a;">Total Enrollments</div>
            <div class="value" style="font-size:1.6rem;font-weight:800;color:#111;"><?= (int)($installmentStats['total_enrollments'] ?? 0) ?></div>
        </div>
        <div class="tcm-stat" style="border:1px solid #16a34a;background:#f0fdf4;border-radius:0;">
            <i class="bi bi-cash-stack icon" style="color:#16a34a;"></i>
            <div class="label" style="font-size:0.68rem;text-transform:uppercase;font-weight:700;letter-spacing:0.05em;color:#15803d;">Total Collected</div>
            <div class="value" style="font-size:1.6rem;font-weight:800;color:#16a34a;">₹<?= number_format((float)($installmentStats['total_collected'] ?? 0), 0) ?></div>
        </div>
        <div class="tcm-stat" style="border:1px solid #dc2626;background:#fef2f2;border-radius:0;">
            <i class="bi bi-hourglass-split icon" style="color:#dc2626;"></i>
            <div class="label" style="font-size:0.68rem;text-transform:uppercase;font-weight:700;letter-spacing:0.05em;color:#b91c1c;">Outstanding Balance</div>
            <div class="value" style="font-size:1.6rem;font-weight:800;color:#dc2626;">₹<?= number_format((float)($installmentStats['total_outstanding'] ?? 0), 0) ?></div>
        </div>
        <div class="tcm-stat" style="border:1px solid #2563eb;background:#eff6ff;border-radius:0;">
            <i class="bi bi-calendar-check icon" style="color:#2563eb;"></i>
            <div class="label" style="font-size:0.68rem;text-transform:uppercase;font-weight:700;letter-spacing:0.05em;color:#1d4ed8;">Upcoming Due</div>
            <div class="value" style="font-size:1.6rem;font-weight:800;color:#2563eb;"><?= (int)($installmentStats['upcoming_payments'] ?? 0) ?></div>
        </div>
        <div class="tcm-stat" style="border:1px solid #ea580c;background:#fff7ed;border-radius:0;">
            <i class="bi bi-exclamation-triangle-fill icon" style="color:#ea580c;"></i>
            <div class="label" style="font-size:0.68rem;text-transform:uppercase;font-weight:700;letter-spacing:0.05em;color:#c2410c;">Overdue Payments</div>
            <div class="value" style="font-size:1.6rem;font-weight:800;color:#ea580c;"><?= (int)($installmentStats['overdue_payments'] ?? 0) ?></div>
        </div>
        <div class="tcm-stat" style="border:1px solid #7c3aed;background:#f5f3ff;border-radius:0;">
            <i class="bi bi-check2-all icon" style="color:#7c3aed;"></i>
            <div class="label" style="font-size:0.68rem;text-transform:uppercase;font-weight:700;letter-spacing:0.05em;color:#6d28d9;">Completed Plans</div>
            <div class="value" style="font-size:1.6rem;font-weight:800;color:#7c3aed;"><?= (int)($installmentStats['completed_plans'] ?? 0) ?></div>
        </div>
    </div>

    <!-- Search, Filter & Action Toolbar -->
    <div class="tcm-card" style="border-radius:0;border:1px solid #e5e5e5;margin-bottom:20px;">
        <form method="get" action="<?= base_url('/admin/payments') ?>" style="display:flex;gap:10px;flex-wrap:wrap;align-items:center;">
            <input type="hidden" name="tab" value="installments">
            <div style="flex:1;min-width:240px;">
                <input class="tcm-input" name="q" placeholder="Search student name, email, course title, order #..." 
                       value="<?= e($_GET['q'] ?? '') ?>" style="width:100%;border-radius:0;">
            </div>
            <select class="tcm-select" name="inst_status" style="width:200px;border-radius:0;" onchange="this.form.submit()">
                <option value="all" <?= ($_GET['inst_status'] ?? 'all') === 'all' ? 'selected' : '' ?>>All Statuses</option>
                <option value="completed" <?= ($_GET['inst_status'] ?? '') === 'completed' ? 'selected' : '' ?>>Completed Plans</option>
                <option value="partially_paid" <?= ($_GET['inst_status'] ?? '') === 'partially_paid' ? 'selected' : '' ?>>Partially Paid</option>
                <option value="pending" <?= ($_GET['inst_status'] ?? '') === 'pending' ? 'selected' : '' ?>>Pending Initial Payment</option>
                <option value="overdue" <?= ($_GET['inst_status'] ?? '') === 'overdue' ? 'selected' : '' ?>>Has Overdue Milestones</option>
            </select>
            <button type="submit" class="tcm-btn primary" style="border-radius:0;">
                <i class="bi bi-funnel-fill"></i> Filter
            </button>
            <a href="<?= base_url('/admin/installments/export') ?>" class="tcm-btn outline" style="border-radius:0;" title="Download complete installment ledger CSV">
                <i class="bi bi-file-earmark-spreadsheet"></i> Export CSV
            </a>
            <button type="button" onclick="showTab('settings')" class="tcm-btn outline" style="border-radius:0;">
                <i class="bi bi-sliders"></i> Configuration
            </button>
            <button type="button" onclick="document.getElementById('modal-create-installment').style.display='flex'" class="tcm-btn primary" style="border-radius:0;">
                <i class="bi bi-plus-circle"></i> New Plan
            </button>
        </form>
    </div>

    <!-- Student Installment Plans Table -->
    <div class="tcm-card" style="border-radius:0;border:1px solid #111;">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;">
            <div>
                <h3 style="font-size:1.05rem;font-weight:800;margin:0;color:#111;">Student Payment Management</h3>
                <span style="font-size:.8rem;color:#64748b;">Course enrollment fee plans, real-time balances, next due dates, and milestone actions</span>
            </div>
            <span style="font-size:0.75rem;font-weight:700;padding:4px 10px;background:#111;color:#fff;border-radius:0;">
                <?= count($installmentPlans) ?> Enrolled Plan<?= count($installmentPlans) > 1 ? 's' : '' ?>
            </span>
        </div>

        <?php if (empty($installmentPlans)): ?>
            <div class="tcm-empty" style="padding:40px 20px;">
                <i class="bi bi-pie-chart" style="font-size:2rem;color:#aaa;display:block;margin-bottom:10px;"></i>
                No installment plans found matching the selected filters.
            </div>
        <?php else: ?>
        <div style="overflow-x:auto;">
            <table class="tcm-table" style="font-size:0.85rem;">
                <thead>
                    <tr style="background:#f4f4f5;">
                        <th style="border-radius:0;">Student</th>
                        <th>Course</th>
                        <th>Total Fee</th>
                        <th>Amount Paid</th>
                        <th>Remaining</th>
                        <th>Next Due Date</th>
                        <th>Status</th>
                        <th>Progress</th>
                        <th style="text-align:right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($installmentPlans as $plan): 
                    $totalFee = (float) $plan['total_amount'];
                    $paidFee = (float) $plan['paid_amount'];
                    $remFee = (float) ($plan['remaining_amount'] ?? ($totalFee - $paidFee));
                    $pct = $totalFee > 0 ? min(100, max(0, (int) round(($paidFee / $totalFee) * 100))) : 0;
                    $isCompleted = ($remFee <= 0.01 || ($plan['status'] ?? '') === 'completed');
                    $hasOverdue = !empty($plan['has_overdue']);

                    if ($isCompleted) {
                        $badgeBg = '#f0fdf4'; $badgeColor = '#15803d'; $badgeBorder = '#bbf7d0'; $badgeText = 'Completed ✓';
                    } elseif ($hasOverdue) {
                        $badgeBg = '#fef2f2'; $badgeColor = '#b91c1c'; $badgeBorder = '#fecaca'; $badgeText = 'Overdue ⚠️';
                    } elseif ($paidFee > 0) {
                        $badgeBg = '#eff6ff'; $badgeColor = '#1d4ed8'; $badgeBorder = '#bfdbfe'; $badgeText = 'Partially Paid';
                    } else {
                        $badgeBg = '#fffbeb'; $badgeColor = '#b45309'; $badgeBorder = '#fde68a'; $badgeText = 'Pending';
                    }
                ?>
                    <tr style="border-bottom:1px solid #e5e5e5;">
                        <td>
                            <div style="font-weight:700;font-size:.88rem;color:#111;"><?= e($plan['user_name'] ?? 'Student #' . $plan['user_id']) ?></div>
                            <div style="font-size:.73rem;color:#71717a;"><?= e($plan['user_email'] ?? '') ?></div>
                            <?php if (!empty($plan['user_phone'])): ?>
                                <div style="font-size:.7rem;color:#a1a1aa;"><i class="bi bi-telephone"></i> <?= e($plan['user_phone']) ?></div>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div style="font-size:.86rem;font-weight:700;color:#111;"><?= e($plan['course_title'] ?? 'Course #' . $plan['course_id']) ?></div>
                            <?php if (!empty($plan['order_number'])): ?>
                                <div style="font-size:.72rem;font-family:monospace;color:#71717a;">#<?= e($plan['order_number']) ?></div>
                            <?php endif; ?>
                        </td>
                        <td style="font-weight:800;font-size:.92rem;color:#111;">
                            ₹<?= number_format($totalFee, 2) ?>
                        </td>
                        <td style="font-weight:800;font-size:.92rem;color:#15803d;">
                            ₹<?= number_format($paidFee, 2) ?>
                        </td>
                        <td style="font-weight:800;font-size:.92rem;color:<?= $remFee > 0 ? '#dc2626' : '#71717a' ?>;">
                            ₹<?= number_format($remFee, 2) ?>
                        </td>
                        <td>
                            <?php if ($plan['next_due_date']): ?>
                                <div style="font-size:.82rem;font-weight:<?= $hasOverdue ? '800;color:#dc2626;' : '600;color:#111;' ?>">
                                    <?= date('d M Y', strtotime($plan['next_due_date'])) ?>
                                </div>
                            <?php else: ?>
                                <span style="font-size:.78rem;color:#15803d;font-weight:700;">Cleared ✓</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <span style="background:<?= $badgeBg ?>;color:<?= $badgeColor ?>;border:1px solid <?= $badgeBorder ?>;padding:3px 8px;font-size:.72rem;font-weight:800;border-radius:0;">
                                <?= e($badgeText) ?>
                            </span>
                        </td>
                        <td>
                            <div style="font-size:0.72rem;font-weight:700;color:#555;margin-bottom:3px;">
                                <?= (int)($plan['paid_count'] ?? 0) ?> of <?= (int)$plan['total_installments'] ?> (<?= $pct ?>%)
                            </div>
                            <div style="background:#e5e5e5;height:5px;width:75px;border-radius:0;overflow:hidden;">
                                <div style="background:#111;height:100%;width:<?= $pct ?>%;border-radius:0;"></div>
                            </div>
                        </td>
                        <td style="text-align:right;">
                            <button type="button" onclick="togglePlanMilestones(<?= (int)$plan['id'] ?>)" class="tcm-btn sm outline" style="border-radius:0;border:1px solid #111;font-weight:700;">
                                <i class="bi bi-list-task"></i> Milestones <i class="bi bi-chevron-down" id="chevron-plan-<?= (int)$plan['id'] ?>"></i>
                            </button>
                        </td>
                    </tr>

                    <!-- Expandable Milestones Row -->
                    <tr id="milestones-row-<?= (int)$plan['id'] ?>" style="display:none;background:#fafafa;">
                        <td colspan="9" style="padding:16px 20px;border-bottom:2px solid #111;">
                            <div style="background:#fff;border:1px solid #e5e5e5;padding:16px;border-radius:0;">
                                <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:12px;">
                                    <div style="font-size:0.75rem;font-weight:800;text-transform:uppercase;letter-spacing:0.06em;color:#111;">
                                        <i class="bi bi-calendar3-range"></i> Scheduled Milestones for <?= e($plan['user_name'] ?? 'Student') ?>
                                    </div>
                                    <span style="font-size:0.72rem;color:#71717a;">Total: <?= count($plan['milestones'] ?? []) ?> Milestones</span>
                                </div>
                                <table style="width:100%;border-collapse:collapse;font-size:0.82rem;">
                                    <thead>
                                        <tr style="background:#f4f4f5;border-bottom:1px solid #e5e5e5;color:#52525b;font-size:0.7rem;text-transform:uppercase;">
                                            <th style="padding:8px 12px;text-align:left;">Milestone</th>
                                            <th style="padding:8px 12px;text-align:left;">Amount Due</th>
                                            <th style="padding:8px 12px;text-align:left;">Due Date</th>
                                            <th style="padding:8px 12px;text-align:left;">Status</th>
                                            <th style="padding:8px 12px;text-align:left;">Gateway Ref / Audit</th>
                                            <th style="padding:8px 12px;text-align:right;">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <?php foreach ($plan['milestones'] ?? [] as $m): 
                                        $mPaid = ($m['status'] === 'paid');
                                        $mDueTs = strtotime((string)$m['due_date']);
                                        $mDaysOverdue = (int) round((strtotime(date('Y-m-d')) - $mDueTs) / 86400);
                                        $mOverdue = (!$mPaid && ($m['status'] === 'overdue' || $mDaysOverdue > 0));
                                    ?>
                                        <tr style="border-bottom:1px solid #f0f0f0;">
                                            <td style="padding:8px 12px;font-weight:700;">
                                                Installment <?= e($m['installment_number']) ?> of <?= e($m['total_installments']) ?>
                                            </td>
                                            <td style="padding:8px 12px;font-weight:800;color:<?= $mPaid ? '#15803d' : ($mOverdue ? '#dc2626' : '#111') ?>;">
                                                ₹<?= number_format((float)$m['amount'], 2) ?>
                                            </td>
                                            <td style="padding:8px 12px;">
                                                <?= date('d M Y', strtotime($m['due_date'])) ?>
                                                <?php if ($mOverdue): ?>
                                                    <span style="font-size:0.7rem;color:#dc2626;font-weight:700;display:block;">(<?= $mDaysOverdue ?>d overdue)</span>
                                                <?php endif; ?>
                                            </td>
                                            <td style="padding:8px 12px;">
                                                <?php if ($mPaid): ?>
                                                    <span style="background:#f0fdf4;border:1px solid #bbf7d0;color:#15803d;padding:2px 6px;font-size:0.7rem;font-weight:700;">PAID ✓</span>
                                                    <?php if (!empty($m['paid_at'])): ?>
                                                        <div style="font-size:0.68rem;color:#15803d;margin-top:2px;"><?= date('d M Y', strtotime($m['paid_at'])) ?></div>
                                                    <?php endif; ?>
                                                <?php elseif ($mOverdue): ?>
                                                    <span style="background:#fef2f2;border:1px solid #fecaca;color:#dc2626;padding:2px 6px;font-size:0.7rem;font-weight:700;">OVERDUE</span>
                                                <?php else: ?>
                                                    <span style="background:#fff;border:1px solid #cbd5e1;color:#475569;padding:2px 6px;font-size:0.7rem;font-weight:700;">PENDING</span>
                                                <?php endif; ?>
                                            </td>
                                            <td style="padding:8px 12px;font-family:monospace;font-size:0.72rem;color:#64748b;">
                                                <?= e($m['payu_payment_id'] ?: $m['txnid'] ?: '—') ?>
                                                <?php if (!empty($m['notes'])): ?>
                                                    <div style="font-size:0.68rem;font-family:sans-serif;color:#94a3b8;"><?= e($m['notes']) ?></div>
                                                <?php endif; ?>
                                            </td>
                                            <td style="padding:8px 12px;text-align:right;">
                                                <div style="display:inline-flex;gap:6px;align-items:center;">
                                                    <?php if (!$mPaid): ?>
                                                        <button type="button" 
                                                                onclick="openRescheduleModal(<?= (int)$m['id'] ?>, <?= (int)$m['installment_number'] ?>, '<?= e($m['due_date']) ?>', '<?= e(addslashes($plan['user_name'] ?? 'Student')) ?>', <?= (float)$m['amount'] ?>)" 
                                                                class="tcm-btn sm outline" style="border-radius:0;font-size:0.72rem;padding:4px 8px;" title="Extend milestone due date">
                                                            <i class="bi bi-clock-history"></i> Reschedule
                                                        </button>
                                                        <form method="post" action="<?= base_url('/admin/installments/' . (int)$m['id'] . '/remind') ?>" style="display:inline;">
                                                            <?= csrf_field() ?>
                                                            <button class="tcm-btn sm outline" style="border-radius:0;font-size:0.72rem;padding:4px 8px;" title="Dispatch manual email reminder">
                                                                <i class="bi bi-bell"></i> Remind
                                                            </button>
                                                        </form>
                                                    <?php else: ?>
                                                        <?php if (!empty($m['installment_order_id'])): ?>
                                                            <a href="<?= base_url('/student/orders/' . (int)$m['installment_order_id'] . '/receipt') ?>" target="_blank" class="tcm-btn sm outline" style="border-radius:0;font-size:0.72rem;padding:4px 8px;">
                                                                <i class="bi bi-file-earmark-text"></i> Receipt
                                                            </a>
                                                        <?php else: ?>
                                                            <span style="font-size:0.72rem;color:#15803d;font-weight:700;">Verified</span>
                                                        <?php endif; ?>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- ===================================================================== -->
<!-- TAB: Installment Configuration & Core Rule Guardrails -->
<!-- ===================================================================== -->
<div id="pane-settings" class="tab-pane <?= $activeTab === 'settings' ? 'active' : '' ?>">
    <div class="tcm-card" style="border-radius:0;border:1px solid #111;max-width:820px;margin:0 auto 24px;">
        <div style="border-bottom:1px solid #e5e5e5;padding-bottom:16px;margin-bottom:20px;">
            <div style="font-size:0.7rem;font-weight:800;letter-spacing:0.08em;text-transform:uppercase;color:#71717a;">SYSTEM PARAMETERS</div>
            <h3 style="font-size:1.2rem;font-weight:800;margin:3px 0 0;color:#111;">
                <i class="bi bi-sliders"></i> Installment Configuration Panel
            </h3>
            <p style="font-size:0.82rem;color:#64748b;margin:4px 0 0;">
                Configure the core operational settings for TCM One's 0% interest flexible milestone installment payments.
            </p>
        </div>

        <div style="background:#f8fafc;border-left:4px solid #111;padding:14px 18px;margin-bottom:24px;">
            <div style="font-size:0.82rem;font-weight:800;color:#111;margin-bottom:4px;">
                <i class="bi bi-shield-lock-fill"></i> Mandatory Core Rule Guardrails Enforced
            </div>
            <ul style="font-size:0.78rem;color:#475569;margin:0;padding-left:18px;line-height:1.6;">
                <li><strong>Maximum 3 Installments:</strong> Strict platform policy forbids financing with more than 3 milestones.</li>
                <li><strong>Minimum Upfront Payment:</strong> At least 40% must be paid upon enrollment to guarantee commitment.</li>
                <li><strong>Minimum Course Price:</strong> Strictly reserved for courses priced > ₹5,000 INR.</li>
                <li><strong>Strict 0% Interest:</strong> No financing fees, loan markups, or third-party NBFC dependencies.</li>
                <li><strong>Zero Floating-Point Drift:</strong> All milestone calculations executed with integer paise precision.</li>
            </ul>
        </div>

        <form method="post" action="<?= base_url('/admin/installments/settings') ?>">
            <?= csrf_field() ?>

            <div style="margin-bottom:20px;padding:16px;background:#fafafa;border:1px solid #e5e5e5;">
                <label style="display:flex;align-items:center;gap:10px;cursor:pointer;">
                    <input type="checkbox" name="installment_enabled" value="1" <?= !empty($installmentSettings['enabled']) ? 'checked' : '' ?> style="width:18px;height:18px;">
                    <div>
                        <strong style="font-size:0.9rem;color:#111;">Enable Installment Payments on Course Pages</strong>
                        <div style="font-size:0.76rem;color:#64748b;">Displays the "Pay in Installments" button and payment plan modal on all eligible course pages.</div>
                    </div>
                </label>
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:20px;">
                <div>
                    <label style="display:block;font-size:0.82rem;font-weight:700;color:#111;margin-bottom:6px;">
                        Minimum Course Price (₹ INR) *
                    </label>
                    <input type="number" step="1" min="5000" name="installment_min_amount" class="tcm-input" 
                           value="<?= (float)($installmentSettings['min_amount'] ?? 5000.00) ?>" required style="width:100%;border-radius:0;">
                    <span style="font-size:0.72rem;color:#64748b;display:block;margin-top:4px;">
                        Strict minimum is ₹5,000. Courses priced at or below ₹5,000 are ineligible.
                    </span>
                </div>
                <div>
                    <label style="display:block;font-size:0.82rem;font-weight:700;color:#111;margin-bottom:6px;">
                        Maximum Permitted Milestones *
                    </label>
                    <select name="installment_max_installments" class="tcm-select" style="width:100%;border-radius:0;">
                        <option value="3" <?= (int)($installmentSettings['max_installments'] ?? 3) === 3 ? 'selected' : '' ?>>
                            Up to 3 Installments (Default: 40% upfront, 30%, 30%)
                        </option>
                        <option value="2" <?= (int)($installmentSettings['max_installments'] ?? 3) === 2 ? 'selected' : '' ?>>
                            Up to 2 Installments (50% upfront, 50% milestone)
                        </option>
                    </select>
                    <span style="font-size:0.72rem;color:#64748b;display:block;margin-top:4px;">
                        Strict platform limit: maximum 3 milestones permitted.
                    </span>
                </div>
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:20px;">
                <div>
                    <label style="display:block;font-size:0.82rem;font-weight:700;color:#111;margin-bottom:6px;">
                        Minimum Upfront Payment (%) *
                    </label>
                    <input type="number" step="1" min="40" max="80" name="installment_min_upfront_pct" class="tcm-input" 
                           value="<?= (int)($installmentSettings['min_upfront_pct'] ?? 40) ?>" required style="width:100%;border-radius:0;">
                    <span style="font-size:0.72rem;color:#64748b;display:block;margin-top:4px;">
                        Enforces minimum 40% paid immediately to confirm enrollment.
                    </span>
                </div>
                <div>
                    <label style="display:block;font-size:0.82rem;font-weight:700;color:#111;margin-bottom:6px;">
                        Milestone Schedule Interval (Days) *
                    </label>
                    <input type="number" step="1" min="7" max="60" name="installment_interval_days" class="tcm-input" 
                           value="<?= (int)($installmentSettings['interval_days'] ?? 15) ?>" required style="width:100%;border-radius:0;">
                    <span style="font-size:0.72rem;color:#64748b;display:block;margin-top:4px;">
                        Standard days between scheduled milestones (e.g. 15 or 30 days).
                    </span>
                </div>
            </div>

            <div style="margin-bottom:24px;padding:16px;background:#fafafa;border:1px solid #e5e5e5;">
                <label style="display:flex;align-items:center;gap:10px;cursor:pointer;">
                    <input type="checkbox" name="installment_reminder_email_enabled" value="1" <?= !empty($installmentSettings['email_reminders']) ? 'checked' : '' ?> style="width:18px;height:18px;">
                    <div>
                        <strong style="font-size:0.9rem;color:#111;">Automated Multi-Tier Email Reminders</strong>
                        <div style="font-size:0.76rem;color:#64748b;">
                            Dispatches automated notifications 3 days before, 1 day before, on due date, and overdue with idempotent log tracking.
                        </div>
                    </div>
                </label>
            </div>

            <div style="display:flex;justify-content:flex-end;gap:12px;">
                <button type="submit" class="tcm-btn primary" style="border-radius:0;padding:12px 28px;font-weight:800;">
                    <i class="bi bi-check2"></i> Save Configuration Settings
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ===================================================================== -->
<!-- MODAL: Reschedule Milestone -->
<!-- ===================================================================== -->
<div id="modal-reschedule-milestone" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.6);backdrop-filter:blur(4px);z-index:9999;align-items:center;justify-content:center;padding:20px;">
    <div style="background:#fff;border-radius:0;border:1px solid #111;max-width:480px;width:100%;box-shadow:0 20px 50px rgba(0,0,0,0.25);overflow:hidden;">
        <div style="background:#111;color:#fff;padding:16px 20px;display:flex;justify-content:space-between;align-items:center;">
            <h3 style="margin:0;font-size:1.05rem;font-weight:800;color:#fff;">
                <i class="bi bi-calendar-event"></i> Reschedule Milestone
            </h3>
            <button type="button" onclick="document.getElementById('modal-reschedule-milestone').style.display='none'" style="background:none;border:none;font-size:1.4rem;cursor:pointer;color:#fff;">&times;</button>
        </div>
        <form method="post" action="<?= base_url('/admin/installments/reschedule') ?>" style="padding:24px;">
            <?= csrf_field() ?>
            <input type="hidden" name="schedule_id" id="reschedule_schedule_id" value="">

            <div style="background:#f8fafc;padding:12px 16px;border:1px solid #e2e8f0;margin-bottom:18px;">
                <div style="font-size:0.75rem;font-weight:700;color:#71717a;text-transform:uppercase;">Milestone Details</div>
                <div id="reschedule_info_target" style="font-size:0.95rem;font-weight:800;color:#111;margin-top:2px;">—</div>
                <div id="reschedule_current_due" style="font-size:0.78rem;color:#64748b;margin-top:2px;">Current Due: —</div>
            </div>

            <div style="margin-bottom:16px;">
                <label style="display:block;font-size:0.82rem;font-weight:700;color:#111;margin-bottom:6px;">New Due Date *</label>
                <input type="date" name="new_due_date" id="reschedule_new_due_date" class="tcm-input" required min="<?= date('Y-m-d') ?>" style="width:100%;border-radius:0;">
            </div>

            <div style="margin-bottom:20px;">
                <label style="display:block;font-size:0.82rem;font-weight:700;color:#111;margin-bottom:6px;">Extension Reason *</label>
                <input type="text" name="reason" id="reschedule_reason" class="tcm-input" placeholder="e.g. Student requested grace period / Hardship extension" value="Administrative extension" required style="width:100%;border-radius:0;">
            </div>

            <div style="display:flex;justify-content:flex-end;gap:10px;">
                <button type="button" onclick="document.getElementById('modal-reschedule-milestone').style.display='none'" class="tcm-btn outline" style="border-radius:0;">Cancel</button>
                <button type="submit" class="tcm-btn primary" style="border-radius:0;font-weight:800;">Confirm & Reschedule</button>
            </div>
        </form>
    </div>
</div>

<!-- ===================================================================== -->
<!-- TAB 3: Refunds & Disputes -->
<!-- ===================================================================== -->
<div id="pane-refunds" class="tab-pane <?= $activeTab === 'refunds' ? 'active' : '' ?>">
    <div class="tcm-card" style="margin-bottom:20px;">
        <h3 style="font-size:1.05rem;font-weight:700;margin-bottom:14px;"><i class="bi bi-arrow-counterclockwise" style="color:#d97706;"></i> Gateway Refunds</h3>
        <?php if (empty($refunds)): ?>
            <div class="tcm-empty" style="padding:24px;">No refunds recorded.</div>
        <?php else: ?>
        <table class="tcm-table">
            <thead>
                <tr>
                    <th>Refund ID</th>
                    <th>Order</th>
                    <th>Student</th>
                    <th>Amount</th>
                    <th>Reason</th>
                    <th>Status</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($refunds as $rf): ?>
                <tr>
                    <td style="font-family:monospace;font-weight:600;"><?= e($rf['payu_refund_id']) ?></td>
                    <td><?= e($rf['order_number'] ?? 'Order #' . $rf['order_id']) ?></td>
                    <td><?= e($rf['user_name'] ?? 'Student #' . $rf['user_id']) ?></td>
                    <td style="font-weight:700;color:#dc2626;">₹<?= number_format((float)$rf['amount'], 2) ?></td>
                    <td style="font-size:.82rem;color:#475569;"><?= e($rf['reason']) ?></td>
                    <td><span class="tcm-badge <?= $rf['status'] === 'success' ? 'green' : 'amber' ?>"><?= e(ucfirst($rf['status'])) ?></span></td>
                    <td style="font-size:.8rem;color:#64748b;"><?= e(date('d M Y, h:i A', strtotime($rf['created_at']))) ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>
    </div>

    <div class="tcm-card">
        <h3 style="font-size:1.05rem;font-weight:700;margin-bottom:14px;"><i class="bi bi-shield-slash" style="color:#dc2626;"></i> Chargebacks & Disputes</h3>
        <?php if (empty($disputes)): ?>
            <div class="tcm-empty" style="padding:24px;">No active payment disputes.</div>
        <?php else: ?>
        <table class="tcm-table">
            <thead>
                <tr>
                    <th>Dispute ID</th>
                    <th>Order</th>
                    <th>Student</th>
                    <th>Disputed Amount</th>
                    <th>Reason</th>
                    <th>Due Date</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($disputes as $dp): ?>
                <tr>
                    <td style="font-family:monospace;font-weight:700;"><?= e($dp['payu_dispute_id']) ?></td>
                    <td><?= e($dp['order_number'] ?? 'Order #' . $dp['order_id']) ?></td>
                    <td><?= e($dp['user_name'] ?? 'Student #' . $dp['user_id']) ?></td>
                    <td style="font-weight:700;color:#dc2626;">₹<?= number_format((float)$dp['amount'], 2) ?></td>
                    <td style="font-size:.82rem;color:#475569;"><?= e($dp['reason']) ?></td>
                    <td style="font-weight:600;color:#b91c1c;"><?= e($dp['due_date'] ? date('d M Y', strtotime($dp['due_date'])) : '—') ?></td>
                    <td><span class="tcm-badge red"><?= e(ucfirst($dp['dispute_status'])) ?></span></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>
    </div>
</div>

<!-- ===================================================================== -->
<!-- TAB 4: Webhook Audit Logs -->
<!-- ===================================================================== -->
<div id="pane-logs" class="tab-pane <?= $activeTab === 'logs' ? 'active' : '' ?>">
    <div class="tcm-card">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;">
            <h3 style="font-size:1.05rem;font-weight:700;margin:0;">PayU Webhook Audit Trail</h3>
            <span style="font-size:.8rem;color:#64748b;">Complete inbound request verification logs & replay audit</span>
        </div>

        <?php if (empty($auditLogs)): ?>
            <div class="tcm-empty">
                <i class="bi bi-terminal"></i>
                No webhook calls logged yet.
            </div>
        <?php else: ?>
        <div style="overflow-x:auto;">
            <table class="tcm-table">
                <thead>
                    <tr>
                        <th>Event</th>
                        <th>URL</th>
                        <th>IP Address</th>
                        <th>Status Code</th>
                        <th>Verification</th>
                        <th>Details / Note</th>
                        <th>Timestamp</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($auditLogs as $log): 
                    $vStatus = $log['verification_status'];
                    $vBadge = match($vStatus) {
                        'verified' => 'green',
                        'hash_mismatch' => 'red',
                        'api_mismatch' => 'red',
                        'invalid_order' => 'amber',
                        default => 'gray'
                    };
                ?>
                    <tr>
                        <td>
                            <span class="tcm-badge <?= str_contains($log['event_type'], 'success') ? 'green' : (str_contains($log['event_type'], 'failed') ? 'red' : 'blue') ?>" style="font-size:.7rem;font-weight:700;">
                                <?= e(strtoupper(str_replace('_', ' ', $log['event_type']))) ?>
                            </span>
                        </td>
                        <td style="font-family:monospace;font-size:.75rem;color:#475569;"><?= e($log['url']) ?></td>
                        <td style="font-size:.78rem;color:#64748b;"><?= e($log['ip_address'] ?: '—') ?></td>
                        <td>
                            <span style="font-weight:700;font-size:.82rem;color:<?= (int)$log['response_status'] < 400 ? '#16a34a' : '#dc2626' ?>;">
                                HTTP <?= (int)$log['response_status'] ?>
                            </span>
                        </td>
                        <td>
                            <span class="tcm-badge <?= $vBadge ?>" style="font-size:.68rem;">
                                <?= e(ucfirst(str_replace('_', ' ', $vStatus))) ?>
                            </span>
                        </td>
                        <td style="font-size:.78rem;color:#334155;max-width:320px;">
                            <?= e($log['error_details'] ?: 'Signature verified & payload accepted') ?>
                        </td>
                        <td style="font-size:.75rem;color:#64748b;"><?= e(date('d M Y, h:i:s A', strtotime($log['created_at']))) ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- ===================================================================== -->
<!-- TAB 5: Manual Payment Submissions -->
<!-- ===================================================================== -->
<div id="pane-manual" class="tab-pane <?= $activeTab === 'manual' ? 'active' : '' ?>">
    <div class="tcm-card">
        <form method="get" style="display:flex;gap:10px;flex-wrap:wrap;margin-bottom:16px;align-items:center;">
            <input type="hidden" name="tab" value="manual">
            <input class="tcm-input" name="q" placeholder="Search student, item..."
                   value="<?= e($_GET['q'] ?? '') ?>" style="flex:1;min-width:200px;">
            <select class="tcm-select" name="status" style="width:170px;" onchange="this.form.submit()">
                <option value="all" <?= ($_GET['status'] ?? 'all') === 'all' ? 'selected' : '' ?>>All Status</option>
                <?php foreach (PaymentSubmission::STATUSES as $k => $lbl): ?>
                    <option value="<?= $k ?>" <?= ($_GET['status'] ?? '') === $k ? 'selected' : '' ?>><?= e($lbl) ?></option>
                <?php endforeach; ?>
            </select>
            <button class="tcm-btn primary">Filter</button>
        </form>

        <?php if (empty($payments)): ?>
            <div class="tcm-empty">
                <i class="bi bi-receipt"></i>
                No manual payment submissions found.
            </div>
        <?php else: ?>
        <div style="overflow-x:auto;">
            <table class="tcm-table">
                <thead>
                    <tr>
                        <th>Student</th>
                        <th>Item</th>
                        <th>Amount</th>
                        <th>Method</th>
                        <th>Referral</th>
                        <th>Date</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($payments as $p):
                    $statusCls = match($p['status']) { 'approved' => 'green', 'rejected' => 'red', default => 'amber' };
                ?>
                    <tr>
                        <td>
                            <div style="font-weight:600;font-size:.88rem;color:#111;"><?= e($p['student_name']) ?></div>
                            <div style="font-size:.73rem;color:var(--muted);"><?= e($p['student_email']) ?></div>
                        </td>
                        <td>
                            <span class="tcm-badge purple" style="font-size:.65rem;margin-bottom:4px;display:inline-flex;">
                                <?= e(ucfirst($p['item_type'])) ?>
                            </span>
                            <div style="font-size:.84rem;font-weight:500;color:#111;"><?= e($p['item_title']) ?></div>
                        </td>
                        <td style="font-weight:700;font-size:.95rem;"><?= money($p['amount']) ?></td>
                        <td style="font-size:.82rem;color:var(--muted);"><?= e(PaymentSubmission::METHODS[$p['payment_method']] ?? $p['payment_method']) ?></td>
                        <td>
                            <?php if (!empty($p['referral_code']) && !empty($p['referrer_name'])): ?>
                                <div style="font-size:.78rem;font-weight:600;color:#059669;">
                                    <i class="bi bi-gift-fill"></i> <?= e($p['referral_code']) ?>
                                </div>
                            <?php else: ?>
                                <span style="font-size:.75rem;color:#cbd5e1;">—</span>
                            <?php endif; ?>
                        </td>
                        <td style="font-size:.82rem;color:var(--muted);"><?= e(date('d M Y', strtotime($p['payment_date']))) ?></td>
                        <td>
                            <span class="tcm-badge <?= $statusCls ?>"><?= e(PaymentSubmission::STATUSES[$p['status']]) ?></span>
                        </td>
                        <td>
                            <a href="<?= base_url('/admin/payments/' . $p['id']) ?>" class="tcm-btn sm outline">Review</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- ===================================================================== -->
<!-- MODAL: Create Installment Plan -->
<!-- ===================================================================== -->
<div id="modal-create-installment" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.6);backdrop-filter:blur(4px);z-index:9999;align-items:center;justify-content:center;padding:20px;">
    <div style="background:#fff;border-radius:0;border:1px solid #111;max-width:540px;width:100%;box-shadow:0 20px 50px rgba(0,0,0,0.25);overflow:hidden;">
        <div style="background:#111;color:#fff;padding:18px 24px;display:flex;justify-content:space-between;align-items:center;">
            <h3 style="margin:0;font-size:1.1rem;font-weight:800;color:#fff;">Create Custom Installment Plan</h3>
            <button type="button" onclick="document.getElementById('modal-create-installment').style.display='none'" style="background:none;border:none;font-size:1.4rem;cursor:pointer;color:#fff;">&times;</button>
        </div>
        <form method="post" action="<?= base_url('/admin/installments/create') ?>" style="padding:24px;">
            <?= csrf_field() ?>
            <div style="margin-bottom:16px;">
                <label style="display:block;font-size:.82rem;font-weight:700;color:#111;margin-bottom:6px;">Select Student *</label>
                <select name="user_id" class="tcm-select" required style="width:100%;border-radius:0;">
                    <option value="">-- Choose Student --</option>
                    <?php foreach ($students as $stu): ?>
                        <option value="<?= (int)$stu['id'] ?>"><?= e($stu['name']) ?> (<?= e($stu['email']) ?>)</option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div style="margin-bottom:16px;">
                <label style="display:block;font-size:.82rem;font-weight:700;color:#111;margin-bottom:6px;">Select Course *</label>
                <select name="course_id" id="inst_course_select" class="tcm-select" required style="width:100%;border-radius:0;" onchange="updateCourseFee(this)">
                    <option value="">-- Choose Course --</option>
                    <?php foreach ($courses as $c): ?>
                        <option value="<?= (int)$c['id'] ?>" data-price="<?= (float)$c['price'] ?>"><?= e($c['title']) ?> (₹<?= number_format((float)$c['price'], 0) ?>)</option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:16px;">
                <div>
                    <label style="display:block;font-size:.82rem;font-weight:700;color:#111;margin-bottom:6px;">Total Fee (₹ INR) *</label>
                    <input type="number" step="0.01" min="5000.01" name="total_fee" id="inst_total_fee" class="tcm-input" placeholder="e.g. 19999" required style="width:100%;border-radius:0;">
                </div>
                <div>
                    <label style="display:block;font-size:.82rem;font-weight:700;color:#111;margin-bottom:6px;">Milestone Schedule *</label>
                    <select name="num_installments" class="tcm-select" style="width:100%;border-radius:0;">
                        <option value="3">3 Installments (40% Upfront, 30%, 30%)</option>
                        <option value="2">2 Installments (50% Upfront, 50%)</option>
                    </select>
                </div>
            </div>

            <div style="background:#f8fafc;padding:12px 14px;border-radius:0;font-size:.78rem;color:#475569;margin-bottom:20px;border-left:3px solid #111;">
                <i class="bi bi-info-circle-fill" style="color:#111;"></i> Installment plans are strictly 0% interest and eligible exclusively for courses priced above ₹5,000 (INR). Milestones are scheduled at 30-day intervals.
            </div>

            <div style="display:flex;justify-content:flex-end;gap:10px;">
                <button type="button" onclick="document.getElementById('modal-create-installment').style.display='none'" class="tcm-btn outline" style="border-radius:0;">Cancel</button>
                <button type="submit" class="tcm-btn primary" style="border-radius:0;font-weight:800;"><i class="bi bi-check2"></i> Generate Plan</button>
            </div>
        </form>
    </div>
</div>

<script>
function showTab(name) {
    document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
    document.querySelectorAll('.tab-pane').forEach(pane => pane.classList.remove('active'));
    
    var targetBtn = document.getElementById('tab-btn-' + name);
    var targetPane = document.getElementById('pane-' + name);
    if (targetBtn) targetBtn.classList.add('active');
    if (targetPane) targetPane.classList.add('active');

    // Update query string in browser without refresh
    var url = new URL(window.location);
    url.searchParams.set('tab', name);
    window.history.replaceState({}, '', url);
}

function updateCourseFee(select) {
    var opt = select.options[select.selectedIndex];
    var price = opt.getAttribute('data-price');
    if (price) {
        document.getElementById('inst_total_fee').value = price;
    }
}

function togglePlanMilestones(planId) {
    var row = document.getElementById('milestones-row-' + planId);
    var chevron = document.getElementById('chevron-plan-' + planId);
    if (!row) return;
    if (row.style.display === 'none' || row.style.display === '') {
        row.style.display = 'table-row';
        if (chevron) {
            chevron.classList.remove('bi-chevron-down');
            chevron.classList.add('bi-chevron-up');
        }
    } else {
        row.style.display = 'none';
        if (chevron) {
            chevron.classList.remove('bi-chevron-up');
            chevron.classList.add('bi-chevron-down');
        }
    }
}

function openRescheduleModal(scheduleId, milestoneNum, dueDate, studentName, amount) {
    var idInput = document.getElementById('reschedule_schedule_id');
    var targetInfo = document.getElementById('reschedule_info_target');
    var currentDue = document.getElementById('reschedule_current_due');
    var newDue = document.getElementById('reschedule_new_due_date');
    var modal = document.getElementById('modal-reschedule-milestone');

    if (idInput) idInput.value = scheduleId;
    if (targetInfo) targetInfo.textContent = studentName + ' — Milestone #' + milestoneNum + ' (₹' + Number(amount).toLocaleString('en-IN', {minimumFractionDigits: 2}) + ')';
    if (currentDue) currentDue.textContent = 'Current Due Date: ' + dueDate;
    if (newDue) newDue.value = dueDate;
    if (modal) modal.style.display = 'flex';
}
</script>
