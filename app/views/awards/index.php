<?php $pageTitle = 'Awards'; $layout = 'admin'; ob_start(); ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <div><h4 class="fw-bold mb-0">Awards</h4></div>
    <a href="<?= url('awards/create') ?>" class="btn btn-primary btn-sm"><i class="bi bi-plus-lg me-1"></i>Give Award</a>
</div>
<div class="card table-card"><div class="table-responsive"><table class="table table-hover mb-0">
    <thead><tr><th>Member</th><th>Award</th><th>Description</th><th>Date</th><th>Awarded By</th></tr></thead>
    <tbody><?php if (empty($awards)): ?><tr><td colspan="5" class="text-center text-muted py-4">No awards</td></tr>
    <?php else: ?><?php foreach ($awards as $a): ?>
        <tr><td class="fw-medium"><a href="<?= url('members/' . $a['member_id']) ?>" class="text-decoration-none"><?= e($a['first_name'] . ' ' . $a['last_name']) ?></a></td>
        <td><i class="bi bi-trophy-fill text-success me-1"></i><?= e($a['name']) ?></td><td class="text-muted small"><?= e($a['description'] ?? '') ?></td>
        <td class="text-muted"><?= formatDate($a['date_awarded']) ?></td><td><?= e($a['awarded_by'] ?? '') ?></td></tr>
    <?php endforeach; ?><?php endif; ?></tbody>
</table></div></div>
<?php $content = ob_get_clean(); ?>
<?php require __DIR__ . '/../layouts/admin.php'; ?>
