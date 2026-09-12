<?php $pageTitle = e($member['first_name']) . ' - Attendance'; $layout = 'admin'; ob_start(); ?>
<div class="mb-4"><h4 class="fw-bold"><?= e($member['first_name'] . ' ' . $member['last_name']) ?> - Attendance</h4></div>
<div class="card table-card"><div class="table-responsive"><table class="table table-hover mb-0">
    <thead><tr><th>Date</th><th>Activity</th><th>Status</th></tr></thead>
    <tbody><?php if (empty($records)): ?><tr><td colspan="3" class="text-center text-muted py-3">No attendance records</td></tr>
    <?php else: ?><?php foreach ($records as $r): ?>
        <tr><td><?= formatDate($r['date']) ?></td><td><?= e($r['activity_name'] ?? 'N/A') ?></td>
        <td><span class="badge bg-<?= $r['status'] === 'Present' ? 'success' : ($r['status'] === 'Absent' ? 'danger' : 'warning') ?> badge-status"><?= $r['status'] ?></span></td></tr>
    <?php endforeach; ?><?php endif; ?></tbody>
</table></div></div>
<?php $content = ob_get_clean(); ?>
<?php require __DIR__ . '/../layouts/admin.php'; ?>
