<?php $pageTitle = 'Add User'; $layout = 'admin'; ob_start(); ?>
<div class="mb-4"><a href="<?= url('users') ?>" class="text-muted text-decoration-none small"><i class="bi bi-arrow-left me-1"></i>Users</a><h4 class="fw-bold">Add User</h4></div>
<div class="card table-card"><div class="card-body p-4">
    <form method="POST" action="<?= url('users') ?>">
        <?= csrf_field() ?>
        <div class="row g-3">
            <div class="col-md-6"><label class="form-label small fw-medium">Full Name *</label><input type="text" name="full_name" class="form-control" required></div>
            <div class="col-md-6"><label class="form-label small fw-medium">Email *</label><input type="email" name="email" class="form-control" required></div>
            <div class="col-md-4"><label class="form-label small fw-medium">Phone</label><input type="tel" name="phone" class="form-control"></div>
            <div class="col-md-4"><label class="form-label small fw-medium">Role *</label><select name="role_id" class="form-select" required><?php foreach ($roles as $r): ?><option value="<?= $r['id'] ?>"><?= e($r['name']) ?></option><?php endforeach; ?></select></div>
            <div class="col-md-4"><label class="form-label small fw-medium">Password *</label><input type="password" name="password" class="form-control" required minlength="8"></div>
        </div>
        <hr><div class="d-flex gap-2"><button type="submit" class="btn btn-primary">Create User</button><a href="<?= url('users') ?>" class="btn btn-outline-secondary">Cancel</a></div>
    </form>
</div></div>
<?php $content = ob_get_clean(); ?>
<?php require __DIR__ . '/../layouts/admin.php'; ?>
