<?php $pageTitle = 'Users'; $layout = 'admin'; ob_start(); ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <div><h4 class="fw-bold mb-0">Users</h4></div>
    <a href="<?= url('users/create') ?>" class="btn btn-primary btn-sm"><i class="bi bi-plus-lg me-1"></i>Add User</a>
</div>
<div class="card table-card"><div class="table-responsive"><table class="table table-hover mb-0">
    <thead><tr><th>Name</th><th>Email</th><th>Role</th><th>Status</th><th>Last Login</th><th>Actions</th></tr></thead>
    <tbody><?php foreach ($users as $u): ?>
        <tr><td class="fw-medium"><?= e($u['full_name']) ?></td><td class="text-muted small"><?= e($u['email']) ?></td>
        <td><span class="badge bg-primary badge-status"><?= e($u['role_name'] ?? 'N/A') ?></span></td>
        <td><span class="badge bg-<?= $u['status'] === 'Active' ? 'success' : 'secondary' ?> badge-status"><?= $u['status'] ?></span></td>
        <td class="text-muted small"><?= $u['last_login'] ? timeAgo($u['last_login']) : 'Never' ?></td>
        <td><a href="<?= url('users/' . $u['id']) ?>" class="btn btn-sm btn-outline-primary">View</a> <a href="<?= url('users/' . $u['id'] . 'edit') ?>" class="btn btn-sm btn-outline-secondary">Edit</a></td></tr>
    <?php endforeach; ?></tbody>
</table></div>
<?= render_pagination($pagination ?? null) ?>
</div>
<?php $content = ob_get_clean(); ?>
<?php require __DIR__ . '/../layouts/admin.php'; ?>
