<?php $pageTitle = 'Finance Report'; $layout = 'admin'; ob_start(); ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <div><a href="<?= url('reports') ?>" class="text-muted text-decoration-none small"><i class="bi bi-arrow-left me-1"></i>Reports</a><h4 class="fw-bold">Finance Report</h4></div>
    <div class="d-flex gap-1"><a href="<?= url('reports/export/pdf?report=finance&format=pdf&date_from=' . e($dateFrom) . '&date_to=' . e($dateTo)) ?>" class="btn btn-outline-danger btn-sm"><i class="bi bi-file-pdf me-1"></i>PDF</a><a href="<?= url('reports/export/excel?report=finance&format=excel&date_from=' . e($dateFrom) . '&date_to=' . e($dateTo)) ?>" class="btn btn-outline-success btn-sm"><i class="bi bi-file-excel me-1"></i>Excel</a></div>
</div>
<div class="card table-card p-3 mb-4">
    <form method="GET" class="row g-2 align-items-end">
        <div class="col-md-3"><label class="form-label small">From</label><input type="date" name="date_from" class="form-control form-control-sm" value="<?= e($dateFrom) ?>"></div>
        <div class="col-md-3"><label class="form-label small">To</label><input type="date" name="date_to" class="form-control form-control-sm" value="<?= e($dateTo) ?>"></div>
        <div class="col-md-2"><button type="submit" class="btn btn-outline-primary btn-sm w-100">Filter</button></div>
    </form>
</div>
<div class="row g-3 mb-4">
    <div class="col-md-4"><div class="card stat-card p-3"><div class="stat-value text-success"><?= formatCurrency($totalIncome) ?></div><div class="stat-label">Total Income</div></div></div>
    <div class="col-md-4"><div class="card stat-card p-3"><div class="stat-value text-danger"><?= formatCurrency($totalExpenses) ?></div><div class="stat-label">Total Expenses</div></div></div>
    <div class="col-md-4"><div class="card stat-card p-3"><div class="stat-value <?= $balance >= 0 ? 'text-primary' : 'text-danger' ?>"><?= formatCurrency($balance) ?></div><div class="stat-label">Balance</div></div></div>
</div>
<div class="row g-3">
    <div class="col-md-6"><h6 class="fw-bold">Income</h6><div class="card table-card"><div class="table-responsive"><table class="table table-sm mb-0"><thead><tr><th>Date</th><th>Category</th><th>Amount</th><th>Method</th></tr></thead><tbody><?php foreach ($income as $i): ?><tr><td class="text-muted small"><?= formatDate($i['date']) ?></td><td><?= e($i['category']) ?></td><td class="text-success"><?= formatCurrency((float)$i['amount']) ?></td><td><?= e($i['payment_method'] ?? '') ?></td></tr><?php endforeach; ?></tbody></table></div></div></div>
    <div class="col-md-6"><h6 class="fw-bold">Expenses</h6><div class="card table-card"><div class="table-responsive"><table class="table table-sm mb-0"><thead><tr><th>Date</th><th>Category</th><th>Amount</th><th>Method</th></tr></thead><tbody><?php foreach ($expenses as $e): ?><tr><td class="text-muted small"><?= formatDate($e['date']) ?></td><td><?= e($e['category']) ?></td><td class="text-danger"><?= formatCurrency((float)$e['amount']) ?></td><td><?= e($e['payment_method'] ?? '') ?></td></tr><?php endforeach; ?></tbody></table></div></div></div>
</div>
<?php $content = ob_get_clean(); ?>
<?php require __DIR__ . '/../layouts/admin.php'; ?>
