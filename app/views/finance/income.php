<?php $pageTitle = 'Income'; $layout = 'admin'; ob_start(); ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <div><a href="<?= url('finance') ?>" class="text-muted text-decoration-none small"><i class="bi bi-arrow-left me-1"></i>Finance</a><h4 class="fw-bold mb-0">Income</h4></div>
    <a href="<?= url('finance/income/create') ?>" class="btn btn-success btn-sm"><i class="bi bi-plus-lg me-1"></i>Record Income</a>
</div>
<div class="card table-card"><div class="table-responsive"><table class="table table-hover mb-0">
    <thead><tr><th>Date</th><th>Category</th><th>Description</th><th>Amount</th><th>Method</th><th>Reference</th></tr></thead>
    <tbody><?php foreach ($income as $i): ?>
        <tr><td class="text-muted"><?= formatDate($i['date']) ?></td><td class="fw-medium"><?= e($i['category']) ?></td><td class="text-muted small"><?= e($i['description'] ?? '') ?></td>
        <td class="text-success fw-bold"><?= formatCurrency((float)$i['amount']) ?></td><td><?= e($i['payment_method'] ?? '') ?></td><td><?= e($i['reference'] ?? '') ?></td></tr>
    <?php endforeach; ?></tbody>
</table></div>
<?= render_pagination($pagination ?? null) ?>
</div>
<?php $content = ob_get_clean(); ?>
<?php require __DIR__ . '/../layouts/admin.php'; ?>
