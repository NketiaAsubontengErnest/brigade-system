<?php $pageTitle = 'My Dues'; $layout = 'admin'; ob_start(); ?>
<div class="mb-4"><h4 class="fw-bold">My Dues</h4></div>
<div class="card table-card"><div class="table-responsive"><table class="table table-hover mb-0">
    <thead><tr><th>Dues</th><th>Due</th><th>Paid</th><th>Balance</th><th>Due Date</th><th>Status</th></tr></thead>
    <tbody><?php foreach ($dues as $d): ?>
        <tr><td class="fw-medium"><?= e($d['dues_name']) ?></td><td><?= formatCurrency((float)$d['amount_due']) ?></td>
        <td class="text-success"><?= formatCurrency((float)$d['amount_paid']) ?></td>
        <td class="<?= $d['balance'] > 0 ? 'text-danger fw-bold' : '' ?>"><?= formatCurrency((float)$d['balance']) ?></td>
        <td class="text-muted small"><?= $d['due_date'] ? formatDate($d['due_date']) : 'N/A' ?></td>
        <td><span class="badge bg-<?= $d['status'] === 'Paid' ? 'success' : ($d['status'] === 'Overdue' ? 'danger' : ($d['status'] === 'Partial' ? 'warning' : 'secondary')) ?> badge-status"><?= $d['status'] ?></span></td></tr>
    <?php endforeach; ?>
    <?php if (empty($dues)): ?><tr><td colspan="6" class="text-center text-muted py-4">No dues records</td></tr><?php endif; ?></tbody>
</table></div></div>
<?php $content = ob_get_clean(); ?>
<?php require __DIR__ . '/../layouts/admin.php'; ?>
