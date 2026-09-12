<?php $pageTitle = 'Training & Badges Report'; $layout = 'admin'; ob_start(); ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <a href="<?= url('reports') ?>" class="text-muted text-decoration-none small"><i class="bi bi-arrow-left me-1"></i> Reports</a>
        <h4 class="fw-bold mb-0">Training & Badges Report</h4>
    </div>
    <div class="d-flex gap-2">
        <a href="<?= url('reports/export/pdf?report=training&format=pdf') ?>" class="btn btn-outline-danger btn-sm">
            <i class="bi bi-file-pdf me-1"></i> PDF
        </a>
        <a href="<?= url('reports/export/excel?report=training&format=excel') ?>" class="btn btn-outline-success btn-sm">
            <i class="bi bi-file-excel me-1"></i> Excel
        </a>
        <a href="<?= url('reports/export/csv?report=training&format=csv') ?>" class="btn btn-outline-primary btn-sm">
            <i class="bi bi-filetype-csv me-1"></i> CSV
        </a>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Member Details</th>
                    <th>Course Name</th>
                    <th>Start Date</th>
                    <th>Completion Date</th>
                    <th>Score</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($records)): ?>
                    <tr>
                        <td colspan="6" class="text-center py-4 text-muted">No training enrollment records found.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($records as $r): ?>
                        <tr>
                            <td>
                                <div class="fw-bold text-dark"><?= e($r['first_name'] . ' ' . $r['last_name']) ?></div>
                                <small class="text-muted"><code><?= e($r['member_number'] ?? 'No #') ?></code></small>
                            </td>
                            <td class="fw-medium text-primary"><?= e($r['course_name']) ?></td>
                            <td class="text-muted small"><?= formatDate($r['start_date']) ?></td>
                            <td class="text-muted small"><?= $r['completion_date'] ? formatDate($r['completion_date']) : 'In Progress' ?></td>
                            <td><strong><?= $r['score'] !== null ? e($r['score']) . '%' : 'N/A' ?></strong></td>
                            <td>
                                <span class="badge bg-<?= $r['status'] === 'Completed' ? 'success' : ($r['status'] === 'In Progress' ? 'primary' : ($r['status'] === 'Failed' ? 'danger' : 'secondary')) ?> badge-status">
                                    <?= e($r['status']) ?>
                                </span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php $content = ob_get_clean(); ?>
<?php require __DIR__ . '/../layouts/admin.php'; ?>
