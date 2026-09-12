<?php $pageTitle = 'Membership Report'; $layout = 'admin'; ob_start(); ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <div><a href="<?= url('reports') ?>" class="text-muted text-decoration-none small"><i class="bi bi-arrow-left me-1"></i>Reports</a><h4 class="fw-bold">Membership Report</h4></div>
    <div class="d-flex gap-1">
        <a href="<?= url('/reports/export/pdf?report=membership&format=pdf') ?>" class="btn btn-outline-danger btn-sm"><i class="bi bi-file-pdf me-1"></i>PDF</a>
        <a href="<?= url('/reports/export/excel?report=membership&format=excel') ?>" class="btn btn-outline-success btn-sm"><i class="bi bi-file-excel me-1"></i>Excel</a>
        <a href="<?= url('/reports/export/csv?report=membership&format=csv') ?>" class="btn btn-outline-secondary btn-sm"><i class="bi bi-filetype-csv me-1"></i>CSV</a>
    </div>
</div>
<div class="card table-card p-3 mb-4">
    <form method="GET" class="row g-2 align-items-end">
        <div class="col-md-3"><select name="filter" class="form-select form-select-sm"><option value="all" <?= $filter === 'all' ? 'selected' : '' ?>>All Members</option><option value="active" <?= $filter === 'active' ? 'selected' : '' ?>>Active</option><option value="inactive" <?= $filter === 'inactive' ? 'selected' : '' ?>>Inactive</option><option value="pending" <?= $filter === 'pending' ? 'selected' : '' ?>>Pending</option><option value="male" <?= $filter === 'male' ? 'selected' : '' ?>>Male</option><option value="female" <?= $filter === 'female' ? 'selected' : '' ?>>Female</option></select></div>
        <div class="col-md-3"><select name="section" class="form-select form-select-sm"><option value="">All Sections</option><?php foreach ($sections as $s): ?><option value="<?= $s['id'] ?>" <?= $section == $s['id'] ? 'selected' : '' ?>><?= e($s['name']) ?></option><?php endforeach; ?></select></div>
        <div class="col-md-2"><button type="submit" class="btn btn-outline-primary btn-sm w-100">Filter</button></div>
    </form>
</div>
<div class="row g-3 mb-4">
    <div class="col-md-3"><div class="card stat-card p-3"><div class="stat-value"><?= $stats['total'] ?></div><div class="stat-label">Total</div></div></div>
    <div class="col-md-3"><div class="card stat-card p-3"><div class="stat-value text-primary"><?= $stats['male'] ?></div><div class="stat-label">Male</div></div></div>
    <div class="col-md-3"><div class="card stat-card p-3"><div class="stat-value text-danger"><?= $stats['female'] ?></div><div class="stat-label">Female</div></div></div>
    <div class="col-md-3"><div class="card stat-card p-3"><div class="stat-value text-success"><?= $stats['active'] ?></div><div class="stat-label">Active</div></div></div>
</div>
<div class="card table-card"><div class="table-responsive"><table class="table table-sm table-hover mb-0">
    <thead><tr><th>Member #</th><th>Name</th><th>Gender</th><th>Section</th><th>Phone</th><th>Status</th><th>Joined</th></tr></thead>
    <tbody><?php foreach ($members as $m): ?>
        <tr><td class="text-muted small"><?= e($m['member_number'] ?? '') ?></td><td class="fw-medium"><?= e($m['first_name'] . ' ' . $m['last_name']) ?></td>
        <td><?= e($m['gender'] ?? '') ?></td><td><?= e($m['section_name'] ?? '') ?></td><td class="text-muted small"><?= e($m['phone'] ?? '') ?></td>
        <td><span class="badge bg-<?= $m['status'] === 'Active' ? 'success' : 'secondary' ?> badge-status"><?= $m['status'] ?></span></td>
        <td class="text-muted small"><?= formatDate($m['date_joined']) ?></td></tr>
    <?php endforeach; ?></tbody>
</table></div></div>
<?php $content = ob_get_clean(); ?>
<?php require __DIR__ . '/../layouts/admin.php'; ?>
