<?php $pageTitle = 'Attendance Report'; $layout = 'admin'; ob_start(); ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <div><a href="<?= url('reports') ?>" class="text-muted text-decoration-none small"><i class="bi bi-arrow-left me-1"></i>Reports</a><h4 class="fw-bold">Attendance Report</h4></div>
    <div class="d-flex gap-1"><a href="<?= url('reports/export/pdf?report=attendance&format=pdf&date_from=' . e($dateFrom) . '&date_to=' . e($dateTo)) ?>" class="btn btn-outline-danger btn-sm"><i class="bi bi-file-pdf me-1"></i>PDF</a><a href="<?= url('reports/export/excel?report=attendance&format=excel&date_from=' . e($dateFrom) . '&date_to=' . e($dateTo)) ?>" class="btn btn-outline-success btn-sm"><i class="bi bi-file-excel me-1"></i>Excel</a></div>
</div>
<div class="card table-card p-3 mb-4">
    <form method="GET" class="row g-2 align-items-end">
        <div class="col-md-3"><label class="form-label small">From</label><input type="date" name="date_from" class="form-control form-control-sm" value="<?= e($dateFrom) ?>"></div>
        <div class="col-md-3"><label class="form-label small">To</label><input type="date" name="date_to" class="form-control form-control-sm" value="<?= e($dateTo) ?>"></div>
        <div class="col-md-2"><button type="submit" class="btn btn-outline-primary btn-sm w-100">Filter</button></div>
    </form>
</div>
<div class="card table-card"><div class="table-responsive"><table class="table table-sm table-hover mb-0">
    <thead><tr><th>Date</th><th>Activity</th><th>Section</th><th>Total</th><th>Present</th><th>Absent</th><th>Late</th><th>Excused</th><th>Rate</th></tr></thead>
    <tbody><?php foreach ($records as $r): ?>
        <tr><td class="text-muted small"><?= formatDate($r['date']) ?></td><td class="fw-medium"><?= e($r['activity_name'] ?? '') ?></td>
        <td><?= e($r['section_name'] ?? 'All') ?></td><td><?= $r['total'] ?></td><td class="text-success"><?= $r['present'] ?></td>
        <td class="text-danger"><?= $r['absent'] ?></td><td class="text-warning"><?= $r['late'] ?></td><td><?= $r['excused'] ?></td>
        <td><strong><?= $r['total'] > 0 ? round(($r['present']/$r['total'])*100) : 0 ?>%</strong></td></tr>
    <?php endforeach; ?></tbody>
</table></div></div>
<?php $content = ob_get_clean(); ?>
<?php require __DIR__ . '/../layouts/admin.php'; ?>
