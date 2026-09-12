<?php $pageTitle = 'Dues'; $layout = 'admin'; ob_start(); ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <div><h4 class="fw-bold mb-0">Dues Management</h4></div>
    <div>
        <a href="<?= url('dues/dashboard') ?>" class="btn btn-outline-primary btn-sm me-1">Dashboard</a>
        <a href="<?= url('dues/types') ?>" class="btn btn-outline-secondary btn-sm me-1">Types</a>
        <a href="<?= url('dues/create') ?>" class="btn btn-primary btn-sm"><i class="bi bi-plus-lg me-1"></i>Create Dues</a>
    </div>
</div>
<div class="row g-3 mb-4">
    <div class="col-md-3"><a href="<?= url('dues/outstanding') ?>" class="card stat-card p-3 text-decoration-none"><div class="stat-value text-warning"><?= formatCurrency((float)($totalOutstanding ?? 0)) ?></div><div class="stat-label">Outstanding</div></a></div>
    <div class="col-md-3"><a href="<?= url('dues/member-dues') ?>" class="card stat-card p-3 text-decoration-none"><div class="stat-value text-primary">Member Dues</div><div class="stat-label">View All</div></a></div>
    <div class="col-md-3"><a href="<?= url('dues/overdue') ?>" class="card stat-card p-3 text-decoration-none"><div class="stat-value text-danger">Overdue</div><div class="stat-label">View All</div></a></div>
    <div class="col-md-3"><a href="<?= url('payments/create') ?>" class="card stat-card p-3 text-decoration-none"><div class="stat-value text-success">Record Payment</div><div class="stat-label">New Payment</div></a></div>
</div>
<div class="card table-card"><div class="table-responsive"><table class="table table-hover mb-0">
    <thead><tr><th>Dues Name</th><th>Type</th><th>Amount</th><th>Due Date</th><th>Assigned</th><th>Paid</th><th>Status</th><th>Actions</th></tr></thead>
    <tbody><?php if (empty($dues)): ?><tr><td colspan="8" class="text-center text-muted py-4">No dues records</td></tr>
    <?php else: ?><?php foreach ($dues as $d): ?>
        <tr>
            <td><a href="<?= url('dues/' . $d['id']) ?>" class="text-decoration-none fw-medium"><?= e($d['name']) ?></a></td>
            <td class="text-muted small"><?= e($d['type_name'] ?? '') ?></td>
            <td class="fw-medium"><?= formatCurrency((float)$d['amount']) ?></td>
            <td class="text-muted"><?= e($d['due_date'] ? formatDate($d['due_date']) : 'N/A') ?></td>
            <td><?= $d['assigned_count'] ?></td>
            <td class="text-success"><?= $d['paid_count'] ?></td>
            <td><span class="badge bg-<?= $d['status'] === 'Active' ? 'success' : 'secondary' ?> badge-status"><?= $d['status'] ?></span></td>
            <td><a href="<?= url('dues/' . $d['id']) ?>" class="btn btn-sm btn-outline-primary">View</a></td>
        </tr>
    <?php endforeach; ?><?php endif; ?></tbody>
</table></div></div>
<?php $content = ob_get_clean(); ?>
<?php require __DIR__ . '/../layouts/admin.php'; ?>
