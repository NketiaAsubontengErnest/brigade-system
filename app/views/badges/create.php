<?php $pageTitle = 'Create Badge'; $layout = 'admin'; ob_start(); ?>
<div class="mb-4"><a href="<?= url('badges') ?>" class="text-muted text-decoration-none small"><i class="bi bi-arrow-left me-1"></i>Badges</a><h4 class="fw-bold">Create Badge</h4></div>
<div class="card table-card"><div class="card-body p-4">
    <form method="POST" action="<?= url('badges') ?>">
        <?= csrf_field() ?>
        <div class="row g-3">
            <div class="col-md-6"><label class="form-label small fw-medium">Name *</label><input type="text" name="name" class="form-control" required></div>
            <div class="col-12"><label class="form-label small fw-medium">Description</label><textarea name="description" class="form-control" rows="2"></textarea></div>
            <div class="col-12"><label class="form-label small fw-medium">Requirements</label><textarea name="requirements" class="form-control" rows="3"></textarea></div>
        </div>
        <hr><div class="d-flex gap-2"><button type="submit" class="btn btn-primary">Create</button><a href="<?= url('badges') ?>" class="btn btn-outline-secondary">Cancel</a></div>
    </form>
</div></div>
<?php $content = ob_get_clean(); ?>
<?php require __DIR__ . '/../layouts/admin.php'; ?>
