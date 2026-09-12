<?php $pageTitle = 'Dues Report'; $layout = 'admin'; ob_start(); ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <div><a href="<?= url('reports') ?>" class="text-muted text-decoration-none small"><i class="bi bi-arrow-left me-1"></i>Reports</a><h4 class="fw-bold">Dues Report</h4></div>
    <div class="d-flex gap-1"><a href="<?= url('reports/export/pdf?report=dues&format=pdf&filter=' . e($filter)) ?>" class="btn btn-outline-danger btn-sm"><i class="bi bi-file-pdf me-1"></i>PDF</a><a href="<?= url('reports/export/excel?report=dues&format=excel&filter=' . e($filter)) ?>" class="btn btn-outline-success btn-sm"><i class="bi bi-file-excel me-1"></i>Excel</a></div>
</div>
<div class="card table-card p-3 mb-4">
    <form method="GET" class="row g-2 align-items-end">
        <div class="col-md-3"><select name="filter" class="form-select form-select-sm"><option value="all" <?= $filter === 'all' ? 'selected' : '' ?>>All</option><option value="paid" <?= $filter === 'paid' ? 'selected' : '' ?>>Paid</option><option value="unpaid" <?= $filter === 'unpaid' ? 'selected' : '' ?>>Unpaid</option><option value="partial" <?= $filter === 'partial' ? 'selected' : '' ?>>Partial</option><option value="overdue" <?= $filter === 'overdue' ? 'selected' : '' ?>>Overdue</option></select></div>
        <div class="col-md-2"><button type="submit" class="btn btn-outline-primary btn-sm w-100">Filter</button></div>
    </form>
</div>
<div class="row g-3 mb-4">
    <div class="col-md-4"><div class="card stat-card p-3"><div class="stat-value"><?= formatCurrency($summary['total_expected']) ?></div><div class="stat-label">Total Expected</div></div></div>
    <div class="col-md-4"><div class="card stat-card p-3"><div class="stat-value text-success"><?= formatCurrency($summary['total_collected']) ?></div><div class="stat-label">Collected</div></div></div>
    <div class="col-md-4"><div class="card stat-card p-3"><div class="stat-value text-danger"><?= formatCurrency($summary['total_outstanding']) ?></div><div class="stat-label">Outstanding</div></div></div>
</div>
<div class="card table-card"><div class="table-responsive"><table class="table table-sm table-hover mb-0">
    <thead><tr><th>Member #</th><th>Name</th><th>Section</th><th>Dues</th><th>Due</th><th>Paid</th><th>Balance</th><th>Status</th></tr></thead>
    <tbody><?php foreach ($records as $r): ?>
        <tr><td class="text-muted small"><?= e($r['member_number'] ?? '') ?></td><td class="fw-medium"><?= e($r['first_name'] . ' ' . $r['last_name']) ?></td>
        <td><?= e($r['section_name'] ?? '') ?></td><td><?= e($r['dues_name']) ?></td>
        <td><?= formatCurrency((float)$r['amount_due']) ?></td><td><?= formatCurrency((float)$r['amount_paid']) ?></td>
        <td class="<?= $r['balance'] > 0 ? 'text-danger fw-bold' : '' ?>"><?= formatCurrency((float)$r['balance']) ?></td>
        <td><span class="badge bg-<?= $r['status'] === 'Paid' ? 'success' : ($r['status'] === 'Overdue' ? 'danger' : 'warning') ?> badge-status"><?= $r['status'] ?></span></td></tr>
    <?php endforeach; ?></tbody>
</table></div></div>
<?php $content = ob_get_clean(); ?>
<?php require __DIR__ . '/../layouts/admin.php'; ?>
