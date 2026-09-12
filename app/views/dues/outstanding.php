<?php $pageTitle = 'Outstanding Dues'; $layout = 'admin'; ob_start(); ?>
<div class="mb-4"><a href="<?= url('dues') ?>" class="text-muted text-decoration-none small"><i class="bi bi-arrow-left me-1"></i>Dues</a><h4 class="fw-bold">Outstanding Dues</h4></div>
<div class="card table-card"><div class="table-responsive"><table class="table table-hover mb-0">
    <thead><tr><th>Member</th><th>Number</th><th>Section</th><th>Dues</th><th>Due</th><th>Paid</th><th>Balance</th><th>Status</th></tr></thead>
    <tbody><?php if (empty($outstanding)): ?><tr><td colspan="8" class="text-center text-muted py-4">No outstanding dues</td></tr>
    <?php else: ?><?php foreach ($outstanding as $o): ?>
        <tr><td class="fw-medium"><?= e($o['first_name'] . ' ' . $o['last_name']) ?></td><td class="text-muted small"><?= e($o['member_number'] ?? '') ?></td>
        <td><?= e($o['section_name'] ?? '') ?></td><td><?= e($o['dues_name']) ?></td>
        <td><?= formatCurrency((float)$o['amount_due']) ?></td><td><?= formatCurrency((float)$o['amount_paid']) ?></td>
        <td class="text-danger fw-bold"><?= formatCurrency((float)$o['balance']) ?></td>
        <td><span class="badge bg-<?= $o['status'] === 'Overdue' ? 'danger' : 'warning' ?> badge-status"><?= $o['status'] ?></span></td></tr>
    <?php endforeach; ?><?php endif; ?></tbody>
</table></div></div>
<?php $content = ob_get_clean(); ?>
<?php require __DIR__ . '/../layouts/admin.php'; ?>
