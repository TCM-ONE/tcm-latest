<div class="tcm-page-head">
    <div>
        <h2>Workshop Leads & Registrations</h2>
        <p>Inbound registrations for Python Starter Workshop (₹99) and 7-Day Frontend Challenge (₹999).</p>
    </div>
    <div class="d-flex gap-8">
        <a href="<?= base_url('/admin/workshop-leads/export' . (!empty($_SERVER['QUERY_STRING']) ? '?' . $_SERVER['QUERY_STRING'] : '')) ?>" class="tcm-btn sm">
            <i class="bi bi-download"></i> Export CSV
        </a>
    </div>
</div>

<!-- KPI Summary Cards -->
<div class="tcm-stat-grid" style="margin-bottom:18px;">
    <div class="tcm-stat">
        <i class="bi bi-people icon"></i>
        <div class="label">Total Leads</div>
        <div class="value"><?= (int)$stats['total'] ?></div>
    </div>
    <div class="tcm-stat">
        <i class="bi bi-stars icon"></i>
        <div class="label">New Registrations</div>
        <div class="value"><?= (int)$stats['new'] ?></div>
    </div>
    <div class="tcm-stat">
        <i class="bi bi-code-slash icon"></i>
        <div class="label">Python (₹99)</div>
        <div class="value"><?= (int)$stats['w99'] ?></div>
    </div>
    <div class="tcm-stat">
        <i class="bi bi-window-sidebar icon"></i>
        <div class="label">Frontend (₹999)</div>
        <div class="value"><?= (int)$stats['w999'] ?></div>
    </div>
    <div class="tcm-stat">
        <i class="bi bi-check2-circle icon"></i>
        <div class="label">Enrolled / Paid</div>
        <div class="value"><?= (int)$stats['enrolled'] ?></div>
    </div>
</div>

<!-- Filters & Search -->
<div class="tcm-card" style="margin-bottom:20px;">
    <form method="get" class="d-flex gap-8 flex-wrap">
        <input class="tcm-input" name="q" placeholder="Search name / email / phone / college..." value="<?= e($_GET['q'] ?? '') ?>" style="flex:1;min-width:200px;">
        
        <select class="tcm-select" name="workshop_slug" style="width:200px;" onchange="this.form.submit()">
            <option value="">All Workshops</option>
            <?php foreach ($workshops as $ws): ?>
                <option value="<?= e($ws['slug']) ?>" <?= ($_GET['workshop_slug'] ?? '') === $ws['slug'] ? 'selected' : '' ?>>
                    <?= e($ws['title']) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <select class="tcm-select" name="lead_status" style="width:160px;" onchange="this.form.submit()">
            <option value="">All Lead Status</option>
            <?php foreach (['new'=>'New', 'contacted'=>'Contacted', 'interested'=>'Interested', 'payment_pending'=>'Payment Pending', 'enrolled'=>'Enrolled', 'dropped'=>'Dropped'] as $val => $lbl): ?>
                <option value="<?= $val ?>" <?= ($_GET['lead_status'] ?? '') === $val ? 'selected' : '' ?>><?= $lbl ?></option>
            <?php endforeach; ?>
        </select>

        <select class="tcm-select" name="payment_status" style="width:160px;" onchange="this.form.submit()">
            <option value="">All Payments</option>
            <?php foreach (['not_initiated'=>'Not Initiated', 'pending'=>'Pending', 'paid'=>'Paid', 'failed'=>'Failed'] as $val => $lbl): ?>
                <option value="<?= $val ?>" <?= ($_GET['payment_status'] ?? '') === $val ? 'selected' : '' ?>><?= $lbl ?></option>
            <?php endforeach; ?>
        </select>

        <button class="tcm-btn primary">Filter</button>
        <?php if (!empty($_GET['q']) || !empty($_GET['workshop_slug']) || !empty($_GET['lead_status']) || !empty($_GET['payment_status'])): ?>
            <a href="<?= base_url('/admin/workshop-leads') ?>" class="tcm-btn ghost">Reset</a>
        <?php endif; ?>
    </form>
</div>

<!-- Leads Table -->
<div class="tcm-card">
    <div class="adm-table-wrap">
        <table class="tcm-table">
            <thead>
                <tr>
                    <th>Lead / Contact</th>
                    <th>Workshop & Fee</th>
                    <th>Status & College</th>
                    <th>Lead Status</th>
                    <th>Payment</th>
                    <th>Registered</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php if (empty($leads)): ?>
                <tr>
                    <td colspan="7" class="text-center py-4 text-muted">
                        No workshop leads found matching your criteria.
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach ($leads as $l): ?>
                <tr>
                    <td>
                        <strong style="color:#111;"><?= e($l['full_name']) ?></strong><br>
                        <span class="muted" style="font-size:.78rem;">
                            <?= e($l['phone'] ?? '') ?><?= $l['phone'] && $l['email'] ? ' · ' : '' ?><?= e($l['email'] ?? '') ?>
                        </span>
                    </td>
                    <td>
                        <div style="font-weight:600;font-size:.85rem;color:#111;"><?= e($l['workshop_title']) ?></div>
                        <span class="tcm-badge dark" style="font-size:.72rem;">₹<?= (int)$l['selected_workshop_price'] ?></span>
                    </td>
                    <td>
                        <span class="tcm-badge gray" style="text-transform:capitalize;"><?= e($l['current_status'] ?? 'student') ?></span>
                        <?php if (!empty($l['college'])): ?>
                            <div class="muted" style="font-size:.76rem;max-width:180px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;" title="<?= e($l['college']) ?>">
                                <?= e($l['college']) ?>
                            </div>
                        <?php endif; ?>
                        <?php if (!empty($l['city'])): ?>
                            <div class="muted" style="font-size:.72rem;"><?= e($l['city']) ?></div>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php 
                        $statusCls = [
                            'new'             => 'amber',
                            'contacted'       => 'purple',
                            'interested'      => 'cyan',
                            'payment_pending' => 'amber',
                            'enrolled'        => 'green',
                            'dropped'         => 'red',
                        ][$l['lead_status']] ?? 'gray'; 
                        ?>
                        <form method="post" action="<?= base_url('/admin/workshop-leads/' . $l['id'] . '/status') ?>" style="display:inline;">
                            <?= csrf_field() ?>
                            <select name="lead_status" onchange="this.form.submit()" class="tcm-select" style="padding:2px 6px;font-size:.75rem;height:26px;border-radius:4px;">
                                <?php foreach (['new'=>'New', 'contacted'=>'Contacted', 'interested'=>'Interested', 'payment_pending'=>'Payment Pending', 'enrolled'=>'Enrolled', 'dropped'=>'Dropped'] as $sVal => $sLbl): ?>
                                    <option value="<?= $sVal ?>" <?= $l['lead_status'] === $sVal ? 'selected' : '' ?>><?= $sLbl ?></option>
                                <?php endforeach; ?>
                            </select>
                        </form>
                    </td>
                    <td>
                        <?php 
                        $payCls = [
                            'not_initiated' => 'gray',
                            'pending'       => 'amber',
                            'paid'          => 'green',
                            'failed'        => 'red',
                            'refunded'      => 'purple',
                        ][$l['payment_status']] ?? 'gray';
                        ?>
                        <form method="post" action="<?= base_url('/admin/workshop-leads/' . $l['id'] . '/payment') ?>" style="display:inline;">
                            <?= csrf_field() ?>
                            <select name="payment_status" onchange="this.form.submit()" class="tcm-select" style="padding:2px 6px;font-size:.75rem;height:26px;border-radius:4px;">
                                <?php foreach (['not_initiated'=>'Not Initiated', 'pending'=>'Pending', 'paid'=>'Paid', 'failed'=>'Failed'] as $pVal => $pLbl): ?>
                                    <option value="<?= $pVal ?>" <?= $l['payment_status'] === $pVal ? 'selected' : '' ?>><?= $pLbl ?></option>
                                <?php endforeach; ?>
                            </select>
                        </form>
                    </td>
                    <td class="muted" style="font-size:.78rem;white-space:nowrap;">
                        <?= e(date('d M Y, h:i A', strtotime($l['created_at']))) ?>
                    </td>
                    <td>
                        <div class="d-flex gap-8 align-items-center">
                            <?php if (!empty($l['wa_link'])): ?>
                                <a class="tcm-btn sm" target="_blank" href="<?= e($l['wa_link']) ?>" title="Message on WhatsApp">
                                    <i class="bi bi-whatsapp" style="color:#25d366;"></i>
                                </a>
                            <?php endif; ?>
                            <form method="post" action="<?= base_url('/admin/workshop-leads/' . $l['id'] . '/delete') ?>" onsubmit="return confirm('Delete this workshop lead?');">
                                <?= csrf_field() ?>
                                <button class="tcm-btn sm ghost text-danger" title="Delete lead"><i class="bi bi-trash"></i></button>
                            </form>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>