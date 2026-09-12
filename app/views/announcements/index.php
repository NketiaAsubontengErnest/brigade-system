<?php $pageTitle = 'Announcements'; $layout = 'admin'; ob_start(); ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <div><h4 class="fw-bold mb-0">Announcements</h4></div>
    <a href="<?= url('announcements/create') ?>" class="btn btn-primary btn-sm"><i class="bi bi-plus-lg me-1"></i>New Announcement</a>
</div>
<div class="card table-card"><div class="table-responsive"><table class="table table-hover mb-0">
    <thead><tr><th>Title</th><th>Status</th><th>Public</th><th>Created By</th><th>Date</th><th>Actions</th></tr></thead>
    <tbody><?php foreach ($announcements as $a): ?>
        <tr><td class="fw-medium"><a href="<?= url('announcements/' . $a['id']) ?>" class="text-decoration-none"><?= e($a['title']) ?></a></td>
        <td><span class="badge bg-<?= $a['status'] === 'Published' ? 'success' : ($a['status'] === 'Draft' ? 'secondary' : 'info') ?> badge-status"><?= $a['status'] ?></span></td>
        <td><?= $a['is_public'] ? '<i class="bi bi-check-circle-fill text-success"></i>' : '<i class="bi bi-x-circle text-muted"></i>' ?></td>
        <td class="text-muted small"><?= e($a['creator_name'] ?? 'System') ?></td><td class="text-muted small"><?= formatDate($a['publish_date']) ?></td>
        <td><a href="<?= url('announcements/' . $a['id']) ?>" class="btn btn-sm btn-outline-primary">View</a> <a href="<?= url('announcements/' . $a['id'] . 'edit') ?>" class="btn btn-sm btn-outline-secondary">Edit</a></td></tr>
    <?php endforeach; ?></tbody>
</table></div>
<?= render_pagination($pagination ?? null) ?>
</div>
<?php $content = ob_get_clean(); ?>
<?php require __DIR__ . '/../layouts/admin.php'; ?>
