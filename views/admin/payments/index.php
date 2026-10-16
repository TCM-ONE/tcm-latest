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
            <i class="bi bi-pie-chart"></i> Installment Plans (<?= count($installments) ?>)
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
<!-- TAB 2: Custom Installment Plans -->
<!-- ===================================================================== -->
<div id="pane-installments" class="tab-pane <?= $activeTab === 'installments' ? 'active' : '' ?>">
    <div class="tcm-card">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;">
            <div>
                <h3 style="font-size:1.05rem;font-weight:700;margin:0;">Custom Installment Schedules</h3>
                <span style="font-size:.8rem;color:#64748b;">Milestones, payment tracking, overdue monitoring & reminder triggers</span>
            </div>
            <button onclick="document.getElementById('modal-create-installment').style.display='flex'" class="tcm-btn primary sm">
                <i class="bi bi-plus-circle"></i> New Plan
            </button>
        </div>

        <?php if (empty($installments)): ?>
            <div class="tcm-empty">
                <i class="bi bi-pie-chart"></i>
                No custom installment plans created yet. Click "Create Installment Plan" to set one up.
            </div>
        <?php else: ?>
        <div style="overflow-x:auto;">
            <table class="tcm-table">
                <thead>
                    <tr>
                        <th>Student</th>
                        <th>Course</th>
                        <th>Milestone</th>
                        <th>Amount</th>
                        <th>Due Date</th>
                        <th>Status</th>
                        <th>PayU Link / Ref</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($installments as $inst): 
                    $isPaid = ($inst['status'] === 'paid');
                    $isOverdue = ($inst['status'] === 'overdue' || (!$isPaid && strtotime($inst['due_date']) < time()));
                    $badgeCls = $isPaid ? 'green' : ($isOverdue ? 'red' : 'amber');
                    $statusLabel = $isPaid ? 'Paid ✓' : ($isOverdue ? 'Overdue ⚠️' : 'Due');
                ?>
                    <tr>
                        <td>
                            <div style="font-weight:600;font-size:.88rem;color:#111;"><?= e($inst['user_name'] ?? 'Student #' . $inst['user_id']) ?></div>
                            <div style="font-size:.73rem;color:var(--muted);"><?= e($inst['user_email'] ?? '') ?></div>
                        </td>
                        <td>
                            <div style="font-size:.84rem;font-weight:600;color:#111;"><?= e($inst['course_title'] ?? 'Course #' . $inst['course_id']) ?></div>
                        </td>
                        <td>
                            <span class="tcm-badge blue" style="font-size:.72rem;">
                                Installment <?= e($inst['installment_number']) ?> of <?= e($inst['total_installments']) ?>
                            </span>
                        </td>
                        <td style="font-weight:700;font-size:.95rem;color:<?= $isPaid ? '#15803d' : '#111' ?>;">
                            ₹<?= number_format((float)$inst['amount'], 2) ?>
                        </td>
                        <td>
                            <div style="font-size:.82rem;font-weight:<?= $isOverdue ? '700;color:#dc2626;' : '500;color:#111;' ?>">
                                <?= e(date('d M Y', strtotime($inst['due_date']))) ?>
                            </div>
                        </td>
                        <td>
                            <span class="tcm-badge <?= $badgeCls ?>"><?= e($statusLabel) ?></span>
                        </td>
                        <td>
                            <?php if ($isPaid): ?>
                                <span style="font-family:monospace;font-size:.74rem;color:#16a34a;">
                                    <?= e($inst['txnid'] ?: $inst['payment_ref'] ?: 'PAID') ?>
                                </span>
                            <?php else: ?>
                                <span style="font-size:.75rem;color:#64748b;">Direct PayU checkout link available on student portal</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div style="display:flex;gap:6px;align-items:center;">
                                <?php if (!$isPaid): ?>
                                    <form method="post" action="<?= base_url('/admin/installments/' . (int)$inst['id'] . '/remind') ?>" style="display:inline;">
                                        <?= csrf_field() ?>
                                        <button class="tcm-btn sm outline" title="Dispatch reminder email to student">
                                            <i class="bi bi-bell"></i> Remind
                                        </button>
                                    </form>
                                <?php else: ?>
                                    <?php if (!empty($inst['installment_order_id'])): ?>
                                        <a href="<?= base_url('/student/orders/' . (int)$inst['installment_order_id'] . '/receipt') ?>" target="_blank" class="tcm-btn sm outline">
                                            <i class="bi bi-file-earmark-text"></i> Receipt
                                        </a>
                                    <?php endif; ?>
                                <?php endif; ?>
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
    <div style="background:#fff;border-radius:14px;max-width:520px;width:100%;box-shadow:0 20px 50px rgba(0,0,0,0.25);overflow:hidden;">
        <div style="padding:20px 24px;border-bottom:1px solid #e2e8f0;display:flex;justify-content:space-between;align-items:center;">
            <h3 style="margin:0;font-size:1.1rem;font-weight:700;color:#0f172a;">Create Custom Installment Plan</h3>
            <button onclick="document.getElementById('modal-create-installment').style.display='none'" style="background:none;border:none;font-size:1.4rem;cursor:pointer;color:#64748b;">&times;</button>
        </div>
        <form method="post" action="<?= base_url('/admin/installments/create') ?>" style="padding:24px;">
            <?= csrf_field() ?>
            <div style="margin-bottom:16px;">
                <label style="display:block;font-size:.82rem;font-weight:600;color:#334155;margin-bottom:6px;">Select Student *</label>
                <select name="user_id" class="tcm-select" required style="width:100%;">
                    <option value="">-- Choose Student --</option>
                    <?php foreach ($students as $stu): ?>
                        <option value="<?= (int)$stu['id'] ?>"><?= e($stu['name']) ?> (<?= e($stu['email']) ?>)</option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div style="margin-bottom:16px;">
                <label style="display:block;font-size:.82rem;font-weight:600;color:#334155;margin-bottom:6px;">Select Course *</label>
                <select name="course_id" id="inst_course_select" class="tcm-select" required style="width:100%;" onchange="updateCourseFee(this)">
                    <option value="">-- Choose Course --</option>
                    <?php foreach ($courses as $c): ?>
                        <option value="<?= (int)$c['id'] ?>" data-price="<?= (float)$c['price'] ?>"><?= e($c['title']) ?> (₹<?= number_format((float)$c['price'], 0) ?>)</option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:20px;">
                <div>
                    <label style="display:block;font-size:.82rem;font-weight:600;color:#334155;margin-bottom:6px;">Total Fee (₹) *</label>
                    <input type="number" step="0.01" name="total_fee" id="inst_total_fee" class="tcm-input" placeholder="e.g. 19999" required style="width:100%;">
                </div>
                <div>
                    <label style="display:block;font-size:.82rem;font-weight:600;color:#334155;margin-bottom:6px;">Number of Milestones *</label>
                    <select name="num_installments" class="tcm-select" style="width:100%;">
                        <option value="2">2 Installments (50% each)</option>
                        <option value="3">3 Installments (33% each)</option>
                        <option value="4">4 Installments (25% each)</option>
                    </select>
                </div>
            </div>

            <div style="background:#f8fafc;padding:12px 14px;border-radius:8px;font-size:.78rem;color:#64748b;margin-bottom:20px;border:1px solid #e2e8f0;">
                <i class="bi bi-info-circle-fill" style="color:#2563eb;"></i> Milestones will be scheduled at 30-day intervals. An email with secure PayU checkout links will be dispatched automatically to the student.
            </div>

            <div style="display:flex;justify-content:flex-end;gap:10px;">
                <button type="button" onclick="document.getElementById('modal-create-installment').style.display='none'" class="tcm-btn outline">Cancel</button>
                <button type="submit" class="tcm-btn primary"><i class="bi bi-check2"></i> Generate Plan</button>
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
</script>
