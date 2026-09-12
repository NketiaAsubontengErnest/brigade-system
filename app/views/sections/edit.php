<?php $pageTitle = 'Edit Section'; $layout = 'admin'; ob_start(); ?>
<div class="mb-4"><a href="<?= url('sections') ?>" class="text-muted text-decoration-none small"><i class="bi bi-arrow-left me-1"></i>Sections</a><h4 class="fw-bold">Edit Section</h4></div>
<div class="card table-card"><div class="card-body p-4">
    <form method="POST" action="<?= url('sections/' . $section['id']) ?>">
        <?= csrf_field() ?><input type="hidden" name="_method" value="PUT">
        <div class="row g-3">
            <div class="col-md-4"><label class="form-label small fw-medium">Name *</label><input type="text" name="name" class="form-control" value="<?= e($section['name']) ?>" required></div>
            <div class="col-md-4"><label class="form-label small fw-medium">Age Range</label><input type="text" name="age_range" class="form-control" value="<?= e($section['age_range'] ?? '') ?>"></div>
            <div class="col-md-4"><label class="form-label small fw-medium">Status</label><select name="status" class="form-select"><option value="Active" <?= $section['status'] === 'Active' ? 'selected' : '' ?>>Active</option><option value="Inactive" <?= $section['status'] === 'Inactive' ? 'selected' : '' ?>>Inactive</option></select></div>
            <div class="col-12"><label class="form-label small fw-medium">Description</label><textarea name="description" class="form-control" rows="2"><?= e($section['description'] ?? '') ?></textarea></div>
        </div>
        <hr><div class="d-flex gap-2"><button type="submit" class="btn btn-primary">Update</button><a href="<?= url('sections') ?>" class="btn btn-outline-secondary">Cancel</a></div>
    </form>
</div></div>
<?php $content = ob_get_clean(); ?>
<?php require __DIR__ . '/../layouts/admin.php'; ?>
