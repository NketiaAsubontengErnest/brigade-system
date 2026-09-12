<?php $pageTitle = 'My Attendance'; $layout = 'admin'; ob_start(); ?>
<div class="mb-4"><h4 class="fw-bold">My Attendance</h4><small class="text-muted">Attendance Rate: <strong><?= $rate ?>%</strong></small></div>
<div class="progress mb-4" style="height:10px"><div class="progress-bar bg-<?= $rate >= 70 ? 'success' : ($rate >= 50 ? 'warning' : 'danger') ?>" style="width:<?= $rate ?>%"></div></div>
<div class="card table-card"><div class="table-responsive"><table class="table table-hover mb-0">
    <thead><tr><th>Date</th><th>Activity</th><th>Status</th></tr></thead>
    <tbody><?php foreach ($records as $r): ?>
        <tr><td class="text-muted"><?= formatDate($r['date']) ?></td><td class="fw-medium"><?= e($r['activity_name'] ?? 'N/A') ?></td>
        <td><span class="badge bg-<?= $r['status'] === 'Present' ? 'success' : ($r['status'] === 'Absent' ? 'danger' : 'warning') ?> badge-status"><?= $r['status'] ?></span></td></tr>
    <?php endforeach; ?>
    <?php if (empty($records)): ?><tr><td colspan="3" class="text-center text-muted py-4">No attendance records</td></tr><?php endif; ?></tbody>
</table></div></div>
<?php $content = ob_get_clean(); ?>
<?php require __DIR__ . '/../layouts/admin.php'; ?>
