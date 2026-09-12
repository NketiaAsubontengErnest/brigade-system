<?php $pageTitle = e($user['full_name']); $layout = 'admin'; ob_start(); ?>
<div class="mb-4"><a href="<?= url('users') ?>" class="text-muted text-decoration-none small"><i class="bi bi-arrow-left me-1"></i>Users</a><h4 class="fw-bold"><?= e($user['full_name']) ?></h4></div>
<div class="card table-card p-4"><table class="table table-sm">
    <tr><td class="text-muted" style="width:200px">Email</td><td><?= e($user['email']) ?></td></tr>
    <tr><td class="text-muted">Phone</td><td><?= e($user['phone'] ?? 'N/A') ?></td></tr>
    <tr><td class="text-muted">Role</td><td><span class="badge bg-primary badge-status"><?= e($user['role_name'] ?? 'N/A') ?></span></td></tr>
    <tr><td class="text-muted">Status</td><td><span class="badge bg-<?= $user['status'] === 'Active' ? 'success' : 'secondary' ?> badge-status"><?= $user['status'] ?></span></td></tr>
    <tr><td class="text-muted">Last Login</td><td><?= $user['last_login'] ? formatDateTime($user['last_login']) : 'Never' ?></td></tr>
</table>
<div class="mt-3 d-flex gap-2">
    <a href="<?= url('users/' . $user['id'] . 'edit') ?>" class="btn btn-outline-primary btn-sm">Edit</a>
    <form method="POST" action="<?= url('/users/' . $user['id'] . '/reset-password') ?>" class="d-inline" onsubmit="return confirm('Reset this user\\'s password?')">
        <?= csrf_field() ?><button type="submit" class="btn btn-outline-warning btn-sm">Reset Password</button>
    </form>
    <form method="POST" action="<?= url('/users/' . $user['id'] . '/toggle-status') ?>" class="d-inline">
        <?= csrf_field() ?><button type="submit" class="btn btn-outline-<?= $user['status'] === 'Active' ? 'danger' : 'success' ?> btn-sm"><?= $user['status'] === 'Active' ? 'Deactivate' : 'Activate' ?></button>
    </form>
</div></div>
<?php $content = ob_get_clean(); ?>
<?php require __DIR__ . '/../layouts/admin.php'; ?>
