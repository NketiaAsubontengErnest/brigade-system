<?php $pageTitle = 'My Awards'; $layout = 'admin'; ob_start(); ?>
<div class="mb-4"><h4 class="fw-bold">My Awards</h4></div>
<div class="card table-card"><div class="table-responsive"><table class="table table-hover mb-0">
    <thead><tr><th>Award</th><th>Description</th><th>Date</th><th>Awarded By</th></tr></thead>
    <tbody><?php foreach ($awards as $a): ?>
        <tr><td class="fw-medium"><i class="bi bi-trophy-fill text-success me-1"></i><?= e($a['name']) ?></td>
        <td class="text-muted"><?= e($a['description'] ?? '') ?></td><td class="text-muted small"><?= formatDate($a['date_awarded']) ?></td><td><?= e($a['awarded_by'] ?? '') ?></td></tr>
    <?php endforeach; ?>
    <?php if (empty($awards)): ?><tr><td colspan="4" class="text-center text-muted py-4">No awards</td></tr><?php endif; ?></tbody>
</table></div></div>
<?php $content = ob_get_clean(); ?>
<?php require __DIR__ . '/../layouts/admin.php'; ?>
