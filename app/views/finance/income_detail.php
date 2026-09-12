<?php $pageTitle = 'Income Detail'; $layout = 'admin'; ob_start(); ?>
<div class="mb-4"><a href="<?= url('finance/income') ?>" class="text-muted text-decoration-none small"><i class="bi bi-arrow-left me-1"></i>Income</a><h4 class="fw-bold">Income Details</h4></div>
<div class="card table-card p-4"><table class="table table-sm">
    <tr><td class="text-muted" style="width:200px">Category</td><td class="fw-medium"><?= e($record['category']) ?></td></tr>
    <tr><td class="text-muted">Amount</td><td class="text-success fw-bold fs-4"><?= formatCurrency((float)$record['amount']) ?></td></tr>
    <tr><td class="text-muted">Date</td><td><?= formatDate($record['date']) ?></td></tr>
    <tr><td class="text-muted">Method</td><td><?= e($record['payment_method'] ?? 'N/A') ?></td></tr>
    <tr><td class="text-muted">Reference</td><td><?= e($record['reference'] ?? 'N/A') ?></td></tr>
    <tr><td class="text-muted">Description</td><td><?= e($record['description'] ?? '') ?></td></tr>
    <tr><td class="text-muted">Recorded By</td><td><?= e($record['recorded_by_name'] ?? 'System') ?></td></tr>
    <tr><td class="text-muted">Status</td><td><span class="badge bg-success badge-status"><?= $record['status'] ?></span></td></tr>
</table></div>
<?php $content = ob_get_clean(); ?>
<?php require __DIR__ . '/../layouts/admin.php'; ?>
