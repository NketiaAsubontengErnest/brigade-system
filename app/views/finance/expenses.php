<?php $pageTitle = 'Expenses'; $layout = 'admin'; ob_start(); ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <div><a href="<?= url('finance') ?>" class="text-muted text-decoration-none small"><i class="bi bi-arrow-left me-1"></i>Finance</a><h4 class="fw-bold mb-0">Expenses</h4></div>
    <a href="<?= url('finance/expenses/create') ?>" class="btn btn-danger btn-sm"><i class="bi bi-plus-lg me-1"></i>Record Expense</a>
</div>
<div class="card table-card"><div class="table-responsive"><table class="table table-hover mb-0">
    <thead><tr><th>Date</th><th>Category</th><th>Description</th><th>Amount</th><th>Method</th><th>Status</th><th>Actions</th></tr></thead>
    <tbody><?php foreach ($expenses as $e): ?>
        <tr><td class="text-muted"><?= formatDate($e['date']) ?></td><td class="fw-medium"><?= e($e['category']) ?></td><td class="text-muted small"><?= e($e['description'] ?? '') ?></td>
        <td class="text-danger fw-bold"><?= formatCurrency((float)$e['amount']) ?></td><td><?= e($e['payment_method'] ?? '') ?></td>
        <td><span class="badge bg-<?= $e['status'] === 'Approved' ? 'success' : ($e['status'] === 'Void' ? 'danger' : 'warning') ?> badge-status"><?= $e['status'] ?></span></td>
        <td>
            <?php if ($e['status'] === 'Recorded' && auth()->hasPermission('finance.approve')): ?>
                <form method="POST" action="<?= url('finance/expenses/' . $e['id'] . '/approve') ?>" class="d-inline"><input type="hidden" name="csrf_token" value="<?= csrf_token() ?>"><button type="submit" class="btn btn-sm btn-outline-success">Approve</button></form>
            <?php endif; ?>
        </td></tr>
    <?php endforeach; ?></tbody>
</table></div>
<?= render_pagination($pagination ?? null) ?>
</div>
<?php $content = ob_get_clean(); ?>
<?php require __DIR__ . '/../layouts/admin.php'; ?>
