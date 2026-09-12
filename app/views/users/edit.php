<?php $pageTitle = 'Edit User'; $layout = 'admin'; ob_start(); ?>
<div class="mb-4"><a href="<?= url('users') ?>" class="text-muted text-decoration-none small"><i class="bi bi-arrow-left me-1"></i>Users</a><h4 class="fw-bold">Edit User</h4></div>
<div class="card table-card"><div class="card-body p-4">
    <form method="POST" action="<?= url('users/' . $user['id']) ?>">
        <?= csrf_field() ?><input type="hidden" name="_method" value="PUT">
        <div class="row g-3">
            <div class="col-md-6"><label class="form-label small fw-medium">Full Name *</label><input type="text" name="full_name" class="form-control" value="<?= e($user['full_name']) ?>" required></div>
            <div class="col-md-6"><label class="form-label small fw-medium">Email *</label><input type="email" name="email" class="form-control" value="<?= e($user['email']) ?>" required></div>
            <div class="col-md-4"><label class="form-label small fw-medium">Phone</label><input type="tel" name="phone" class="form-control" value="<?= e($user['phone'] ?? '') ?>"></div>
            <div class="col-md-4"><label class="form-label small fw-medium">Role</label><select name="role_id" class="form-select"><?php foreach ($roles as $r): ?><option value="<?= $r['id'] ?>" <?= $user['role_id'] == $r['id'] ? 'selected' : '' ?>><?= e($r['name']) ?></option><?php endforeach; ?></select></div>
            <div class="col-md-4"><label class="form-label small fw-medium">Status</label><select name="status" class="form-select"><option value="Active" <?= $user['status'] === 'Active' ? 'selected' : '' ?>>Active</option><option value="Inactive" <?= $user['status'] === 'Inactive' ? 'selected' : '' ?>>Inactive</option><option value="Suspended" <?= $user['status'] === 'Suspended' ? 'selected' : '' ?>>Suspended</option></select></div>
        </div>
        <hr><div class="d-flex gap-2"><button type="submit" class="btn btn-primary">Update</button><a href="<?= url('users/' . $user['id']) ?>" class="btn btn-outline-secondary">Cancel</a></div>
    </form>
</div></div>
<?php $content = ob_get_clean(); ?>
<?php require __DIR__ . '/../layouts/admin.php'; ?>
