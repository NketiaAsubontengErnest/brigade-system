<?php $pageTitle = 'Member Dues'; $layout = 'admin'; ob_start(); ?>
<div class="mb-4"><a href="<?= url('dues') ?>" class="text-muted text-decoration-none small"><i class="bi bi-arrow-left me-1"></i>Dues</a><h4 class="fw-bold">Member Dues</h4></div>
<div class="card table-card"><div class="table-responsive"><table class="table table-hover mb-0">
    <thead><tr><th>Member</th><th>Number</th><th>Dues</th><th>Due</th><th>Paid</th><th>Balance</th><th>Status</th></tr></thead>
    <tbody><?php foreach ($memberDues as $md): ?>
        <tr><td class="fw-medium"><?= e($md['first_name'] . ' ' . $md['last_name']) ?></td><td class="text-muted small"><?= e($md['member_number'] ?? '') ?></td>
        <td><?= e($md['dues_name']) ?></td><td><?= formatCurrency((float)$md['amount_due']) ?></td>
        <td class="text-success"><?= formatCurrency((float)$md['amount_paid']) ?></td>
        <td class="<?= $md['balance'] > 0 ? 'text-danger fw-bold' : '' ?>"><?= formatCurrency((float)$md['balance']) ?></td>
        <td><span class="badge bg-<?= $md['status'] === 'Paid' ? 'success' : ($md['status'] === 'Overdue' ? 'danger' : ($md['status'] === 'Partial' ? 'warning' : 'secondary')) ?> badge-status"><?= $md['status'] ?></span></td></tr>
    <?php endforeach; ?></tbody>
</table></div>
<?= render_pagination($pagination ?? null) ?>
</div>
<?php $content = ob_get_clean(); ?>
<?php require __DIR__ . '/../layouts/admin.php'; ?>
