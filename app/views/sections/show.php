<?php $pageTitle = e($section['name']); $layout = 'admin'; ob_start(); ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <div><a href="<?= url('sections') ?>" class="text-muted text-decoration-none small"><i class="bi bi-arrow-left me-1"></i>Sections</a><h4 class="fw-bold mb-0"><?= e($section['name']) ?></h4><small class="text-muted"><?= e($section['description'] ?? '') ?></small></div>
    <a href="<?= url('sections/' . $section['id'] . 'edit') ?>" class="btn btn-outline-primary btn-sm">Edit</a>
</div>
<div class="card table-card"><div class="table-responsive"><table class="table table-hover mb-0">
    <thead><tr><th>Name</th><th>Member #</th><th>Gender</th><th>Phone</th><th>Status</th></tr></thead>
    <tbody><?php if (empty($members)): ?><tr><td colspan="5" class="text-center text-muted py-4">No members in this section</td></tr>
    <?php else: ?><?php foreach ($members as $m): ?>
        <tr><td><a href="<?= url('members/' . $m['id']) ?>" class="text-decoration-none"><?= e($m['first_name'] . ' ' . $m['last_name']) ?></a></td><td class="text-muted"><?= e($m['member_number'] ?? '') ?></td><td><?= e($m['gender']) ?></td><td><?= e($m['phone'] ?? '') ?></td><td><span class="badge bg-<?= $m['status'] === 'Active' ? 'success' : 'secondary' ?> badge-status"><?= $m['status'] ?></span></td></tr>
    <?php endforeach; ?><?php endif; ?></tbody>
</table></div></div>
<?php $content = ob_get_clean(); ?>
<?php require __DIR__ . '/../layouts/admin.php'; ?>
