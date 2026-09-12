<?php $pageTitle = 'Edit Officer'; $layout = 'admin'; ob_start(); ?>
<div class="mb-4"><a href="<?= url('officers') ?>" class="text-muted text-decoration-none small"><i class="bi bi-arrow-left me-1"></i>Officers</a><h4 class="fw-bold">Edit Officer</h4></div>
<div class="card table-card"><div class="card-body p-4">
    <form method="POST" action="<?= url('officers/' . $officer['id']) ?>">
        <?= csrf_field() ?><input type="hidden" name="_method" value="PUT">
        <div class="row g-3">
            <div class="col-md-4"><label class="form-label small fw-medium">Position</label><select name="position_id" class="form-select"><?php foreach ($positions as $p): ?><option value="<?= $p['id'] ?>" <?= $officer['position_id'] == $p['id'] ? 'selected' : '' ?>><?= e($p['name']) ?></option><?php endforeach; ?></select></div>
            <div class="col-md-3"><label class="form-label small fw-medium">Start Date</label><input type="date" name="start_date" class="form-control" value="<?= e($officer['start_date'] ?? '') ?>"></div>
            <div class="col-md-3"><label class="form-label small fw-medium">End Date</label><input type="date" name="end_date" class="form-control" value="<?= e($officer['end_date'] ?? '') ?>"></div>
            <div class="col-md-2"><label class="form-label small fw-medium">Status</label><select name="status" class="form-select"><option value="Active" <?= $officer['status'] === 'Active' ? 'selected' : '' ?>>Active</option><option value="Inactive" <?= $officer['status'] === 'Inactive' ? 'selected' : '' ?>>Inactive</option></select></div>
        </div>
        <hr><div class="d-flex gap-2"><button type="submit" class="btn btn-primary">Update</button><a href="<?= url('officers') ?>" class="btn btn-outline-secondary">Cancel</a></div>
    </form>
</div></div>
<?php $content = ob_get_clean(); ?>
<?php require __DIR__ . '/../layouts/admin.php'; ?>
