<?php $pageTitle = 'Payments'; $layout = 'admin'; ob_start(); ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <div><h4 class="fw-bold mb-0">Payments</h4></div>
    <div><a href="<?= url('payments/receipts') ?>" class="btn btn-outline-secondary btn-sm me-1">Receipts</a><a href="<?= url('payments/create') ?>" class="btn btn-primary btn-sm"><i class="bi bi-plus-lg me-1"></i>Record Payment</a></div>
</div>
<div class="card table-card p-3 mb-4">
    <form method="GET" class="row g-2 align-items-end">
        <div class="col-md-6"><input type="text" name="search" class="form-control form-control-sm" placeholder="Search member, receipt, reference..." value="<?= e($search) ?>"></div>
        <div class="col-md-2"><button type="submit" class="btn btn-outline-primary btn-sm w-100">Search</button></div>
    </form>
</div>
<div class="card table-card"><div class="table-responsive"><table class="table table-hover mb-0">
    <thead><tr><th>Receipt #</th><th>Member</th><th>Amount</th><th>Method</th><th>Date</th><th>Status</th><th>Actions</th></tr></thead>
    <tbody><?php foreach ($payments as $p): ?>
        <tr>
            <td><a href="<?= url('payments/' . $p['id']) ?>" class="text-decoration-none fw-medium"><?= e($p['receipt_number']) ?></a></td>
            <td><?= e($p['first_name'] . ' ' . $p['last_name']) ?></td>
            <td class="text-success fw-bold"><?= formatCurrency((float)$p['amount']) ?></td>
            <td><?= e($p['payment_method']) ?></td>
            <td class="text-muted"><?= formatDate($p['payment_date']) ?></td>
            <td><span class="badge bg-<?= $p['status'] === 'Completed' ? 'success' : ($p['status'] === 'Void' ? 'danger' : 'warning') ?> badge-status"><?= $p['status'] ?></span></td>
            <td><a href="<?= url('payments/' . $p['id']) ?>" class="btn btn-sm btn-outline-primary">View</a></td>
        </tr>
    <?php endforeach; ?></tbody>
</table></div>
<?= render_pagination($pagination) ?>
</div>
<?php $content = ob_get_clean(); ?>
<?php require __DIR__ . '/../layouts/admin.php'; ?>
