<?php $pageTitle = 'Overdue Dues'; $layout = 'admin'; ob_start(); ?>
<div class="mb-4"><a href="<?= url('dues') ?>" class="text-muted text-decoration-none small"><i class="bi bi-arrow-left me-1"></i>Dues</a><h4 class="fw-bold text-danger">Overdue Dues</h4></div>
<div class="card table-card"><div class="table-responsive"><table class="table table-hover mb-0">
    <thead><tr><th>Member</th><th>Number</th><th>Section</th><th>Dues</th><th>Due Date</th><th>Balance</th></tr></thead>
    <tbody><?php if (empty($overdue)): ?><tr><td colspan="6" class="text-center text-muted py-4">No overdue dues</td></tr>
    <?php else: ?><?php foreach ($overdue as $o): ?>
        <tr><td class="fw-medium"><?= e($o['first_name'] . ' ' . $o['last_name']) ?></td><td class="text-muted small"><?= e($o['member_number'] ?? '') ?></td>
        <td><?= e($o['section_name'] ?? '') ?></td><td><?= e($o['dues_name']) ?></td>
        <td class="text-danger"><?= formatDate($o['due_date']) ?></td>
        <td class="text-danger fw-bold"><?= formatCurrency((float)$o['balance']) ?></td></tr>
    <?php endforeach; ?><?php endif; ?></tbody>
</table></div></div>
<?php $content = ob_get_clean(); ?>
<?php require __DIR__ . '/../layouts/admin.php'; ?>
