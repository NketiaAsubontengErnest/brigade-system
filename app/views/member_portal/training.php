<?php $pageTitle = 'My Training'; $layout = 'admin'; ob_start(); ?>
<div class="mb-4"><h4 class="fw-bold">My Training</h4></div>
<div class="card table-card"><div class="table-responsive"><table class="table table-hover mb-0">
    <thead><tr><th>Course</th><th>Duration</th><th>Instructor</th><th>Status</th><th>Score</th></tr></thead>
    <tbody><?php foreach ($training as $t): ?>
        <tr><td class="fw-medium"><?= e($t['course_name']) ?></td><td class="text-muted"><?= e($t['duration'] ?? 'N/A') ?></td><td><?= e($t['instructor'] ?? 'N/A') ?></td>
        <td><span class="badge bg-<?= $t['status'] === 'Completed' ? 'success' : ($t['status'] === 'In Progress' ? 'primary' : 'secondary') ?> badge-status"><?= $t['status'] ?></span></td>
        <td><?= $t['score'] ?? '-' ?></td></tr>
    <?php endforeach; ?>
    <?php if (empty($training)): ?><tr><td colspan="5" class="text-center text-muted py-4">No training records</td></tr><?php endif; ?></tbody>
</table></div></div>
<?php $content = ob_get_clean(); ?>
<?php require __DIR__ . '/../layouts/admin.php'; ?>
