<?php $pageTitle = 'Attendance Log'; ob_start(); ?>

<div class="mb-4">
    <h3 class="fw-bold mb-1"><i class="bi bi-clipboard-check text-primary me-2"></i>Attendance Log</h3>
    <p class="text-muted">Parade and meeting attendance history for your linked children</p>
</div>

<div class="card guardian-card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Date</th>
                        <th>Child Name</th>
                        <th>Activity / Parade</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($attendanceLogs)): ?>
                        <tr><td colspan="4" class="text-center py-4 text-muted">No attendance logs found.</td></tr>
                    <?php else: ?>
                        <?php foreach ($attendanceLogs as $log): ?>
                            <tr>
                                <td><?= date('d M Y', strtotime($log['date'])) ?></td>
                                <td><strong><?= htmlspecialchars($log['first_name'] . ' ' . $log['last_name']) ?></strong></td>
                                <td><?= htmlspecialchars($log['activity_name'] ?? 'General Parade') ?></td>
                                <td>
                                    <span class="badge bg-<?= $log['status'] === 'Present' ? 'success' : ($log['status'] === 'Late' ? 'warning' : 'danger') ?>">
                                        <?= htmlspecialchars($log['status']) ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php $content = ob_get_clean(); ?>
<?php require __DIR__ . '/../layouts/guardian.php'; ?>
