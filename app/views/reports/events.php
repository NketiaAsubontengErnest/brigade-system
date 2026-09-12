<?php $pageTitle = 'Events Report'; $layout = 'admin'; ob_start(); ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <a href="<?= url('reports') ?>" class="text-muted text-decoration-none small"><i class="bi bi-arrow-left me-1"></i> Reports</a>
        <h4 class="fw-bold mb-0">Events & Attendance Log</h4>
    </div>
    <div class="d-flex gap-2">
        <a href="<?= url('reports/export/pdf?report=events&format=pdf') ?>" class="btn btn-outline-danger btn-sm">
            <i class="bi bi-file-pdf me-1"></i> PDF
        </a>
        <a href="<?= url('reports/export/excel?report=events&format=excel') ?>" class="btn btn-outline-success btn-sm">
            <i class="bi bi-file-excel me-1"></i> Excel
        </a>
        <a href="<?= url('reports/export/csv?report=events&format=csv') ?>" class="btn btn-outline-primary btn-sm">
            <i class="bi bi-filetype-csv me-1"></i> CSV
        </a>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Event Name</th>
                    <th>Date</th>
                    <th>Location</th>
                    <th>Status</th>
                    <th class="text-center">Registered</th>
                    <th class="text-center">Attended</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($events)): ?>
                    <tr>
                        <td colspan="6" class="text-center py-4 text-muted">No event records found.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($events as $e): ?>
                        <tr>
                            <td class="fw-bold text-dark"><?= e($e['name'] ?? $e['title'] ?? '') ?></td>
                            <td class="text-muted small"><?= formatDate($e['start_date']) ?></td>
                            <td><?= e($e['location'] ?? 'N/A') ?></td>
                            <td>
                                <span class="badge bg-<?= $e['status'] === 'Completed' ? 'success' : ($e['status'] === 'Upcoming' ? 'primary' : 'secondary') ?> badge-status">
                                    <?= e($e['status']) ?>
                                </span>
                            </td>
                            <td class="text-center fw-bold"><?= number_format((int)$e['registered']) ?></td>
                            <td class="text-center text-success fw-bold"><?= number_format((int)$e['attended']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php $content = ob_get_clean(); ?>
<?php require __DIR__ . '/../layouts/admin.php'; ?>
