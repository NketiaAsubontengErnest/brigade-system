<?php $pageTitle = 'Edit Badge'; $layout = 'admin'; ob_start(); ?>
<div class="mb-4"><a href="<?= url('badges') ?>" class="text-muted text-decoration-none small"><i class="bi bi-arrow-left me-1"></i>Badges</a><h4 class="fw-bold">Edit Badge</h4></div>
<div class="card table-card"><div class="card-body p-4">
    <form method="POST" action="<?= url('badges/' . $badge['id']) ?>">
        <?= csrf_field() ?><input type="hidden" name="_method" value="PUT">
        <div class="row g-3">
            <div class="col-md-6"><label class="form-label small fw-medium">Name *</label><input type="text" name="name" class="form-control" value="<?= e($badge['name']) ?>" required></div>
            <div class="col-md-3"><label class="form-label small fw-medium">Status</label><select name="status" class="form-select"><option value="Active" <?= $badge['status'] === 'Active' ? 'selected' : '' ?>>Active</option><option value="Inactive" <?= $badge['status'] === 'Inactive' ? 'selected' : '' ?>>Inactive</option></select></div>
            <div class="col-12"><label class="form-label small fw-medium">Description</label><textarea name="description" class="form-control" rows="2"><?= e($badge['description'] ?? '') ?></textarea></div>
            <div class="col-12"><label class="form-label small fw-medium">Requirements</label><textarea name="requirements" class="form-control" rows="3"><?= e($badge['requirements'] ?? '') ?></textarea></div>
        </div>
        <hr><div class="d-flex gap-2"><button type="submit" class="btn btn-primary">Update</button><a href="<?= url('badges/' . $badge['id']) ?>" class="btn btn-outline-secondary">Cancel</a></div>
    </form>
</div></div>
<?php $content = ob_get_clean(); ?>
<?php require __DIR__ . '/../layouts/admin.php'; ?>
