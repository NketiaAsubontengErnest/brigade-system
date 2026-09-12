<?php $pageTitle = 'Receipts'; $layout = 'admin'; ob_start(); ?>
<div class="mb-4"><a href="<?= url('payments') ?>" class="text-muted text-decoration-none small"><i class="bi bi-arrow-left me-1"></i>Payments</a><h4 class="fw-bold">Receipts</h4></div>
<div class="card table-card"><div class="table-responsive"><table class="table table-hover mb-0">
    <thead><tr><th>Receipt #</th><th>Member</th><th>Amount</th><th>Method</th><th>Date</th><th>Actions</th></tr></thead>
    <tbody><?php foreach ($payments as $p): ?>
        <tr><td class="fw-medium"><?= e($p['receipt_number']) ?></td><td><?= e($p['first_name'] . ' ' . $p['last_name']) ?></td>
        <td class="text-success fw-bold"><?= formatCurrency((float)$p['amount']) ?></td><td><?= e($p['payment_method']) ?></td>
        <td class="text-muted"><?= formatDate($p['payment_date']) ?></td>
        <td><a href="<?= url('payments/' . $p['id'] . 'receipt') ?>" class="btn btn-sm btn-outline-primary">View</a> <a href="<?= url('payments/' . $p['id'] . 'receipt/download') ?>" class="btn btn-sm btn-outline-success">PDF</a></td></tr>
    <?php endforeach; ?></tbody>
</table></div></div>
<?php $content = ob_get_clean(); ?>
<?php require __DIR__ . '/../layouts/admin.php'; ?>
