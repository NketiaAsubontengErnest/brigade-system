<?php $pageTitle = 'My Payments'; $layout = 'admin'; ob_start(); ?>
<div class="mb-4"><h4 class="fw-bold">My Payments</h4></div>
<div class="card table-card"><div class="table-responsive"><table class="table table-hover mb-0">
    <thead><tr><th>Receipt</th><th>Amount</th><th>Method</th><th>Date</th><th>Actions</th></tr></thead>
    <tbody><?php foreach ($payments as $p): ?>
        <tr><td class="fw-medium"><?= e($p['receipt_number']) ?></td><td class="text-success fw-bold"><?= formatCurrency((float)$p['amount']) ?></td>
        <td><?= e($p['payment_method']) ?></td><td class="text-muted"><?= formatDate($p['payment_date']) ?></td>
        <td><a href="<?= url('payments/' . $p['id'] . 'receipt') ?>" class="btn btn-sm btn-outline-primary">Receipt</a></td></tr>
    <?php endforeach; ?>
    <?php if (empty($payments)): ?><tr><td colspan="5" class="text-center text-muted py-4">No payment records</td></tr><?php endif; ?></tbody>
</table></div></div>
<?php $content = ob_get_clean(); ?>
<?php require __DIR__ . '/../layouts/admin.php'; ?>
