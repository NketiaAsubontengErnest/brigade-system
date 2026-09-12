<?php $pageTitle = e($dues['name']); $layout = 'admin'; ob_start(); ?>
<div class="mb-4"><a href="<?= url('dues') ?>" class="text-muted text-decoration-none small"><i class="bi bi-arrow-left me-1"></i>Dues</a><h4 class="fw-bold mb-0"><?= e($dues['name']) ?></h4>
<small class="text-muted"><?= formatCurrency((float)$dues['amount']) ?> · Due: <?= e($dues['due_date'] ? formatDate($dues['due_date']) : 'N/A') ?> · <?= e($dues['type_name'] ?? '') ?></small></div>
<div class="card table-card"><div class="table-responsive"><table class="table table-hover mb-0">
    <thead><tr><th>Member</th><th>Number</th><th>Due</th><th>Paid</th><th>Balance</th><th>Status</th></tr></thead>
    <tbody><?php if (empty($members)): ?><tr><td colspan="6" class="text-center text-muted py-4">No members assigned</td></tr>
    <?php else: ?><?php foreach ($members as $m): ?>
        <tr><td><a href="<?= url('members/' . $m['member_id']) ?>" class="text-decoration-none"><?= e($m['first_name'] . ' ' . $m['last_name']) ?></a></td><td class="text-muted"><?= e($m['member_number'] ?? '') ?></td>
        <td><?= formatCurrency((float)$m['amount_due']) ?></td><td class="text-success"><?= formatCurrency((float)$m['amount_paid']) ?></td>
        <td class="<?= $m['balance'] > 0 ? 'text-danger fw-bold' : 'text-success' ?>"><?= formatCurrency((float)$m['balance']) ?></td>
        <td><span class="badge bg-<?= $m['status'] === 'Paid' ? 'success' : ($m['status'] === 'Overdue' ? 'danger' : ($m['status'] === 'Partial' ? 'warning' : 'secondary')) ?> badge-status"><?= $m['status'] ?></span></td></tr>
    <?php endforeach; ?><?php endif; ?></tbody>
</table></div></div>
<?php $content = ob_get_clean(); ?>
<?php require __DIR__ . '/../layouts/admin.php'; ?>
