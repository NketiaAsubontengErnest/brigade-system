<?php $pageTitle = 'Edit Award'; $layout = 'admin'; ob_start(); ?>
<div class="mb-4"><a href="<?= url('awards') ?>" class="text-muted text-decoration-none small"><i class="bi bi-arrow-left me-1"></i>Awards</a><h4 class="fw-bold">Edit Award</h4></div>
<div class="card table-card"><div class="card-body p-4">
    <form method="POST" action="<?= url('awards/' . $award['id']) ?>">
        <?= csrf_field() ?><input type="hidden" name="_method" value="PUT">
        <div class="row g-3">
            <div class="col-md-6"><label class="form-label small fw-medium">Name *</label><input type="text" name="name" class="form-control" value="<?= e($award['name']) ?>" required></div>
            <div class="col-md-3"><label class="form-label small fw-medium">Date</label><input type="date" name="date_awarded" class="form-control" value="<?= e($award['date_awarded']) ?>"></div>
            <div class="col-md-3"><label class="form-label small fw-medium">Awarded By</label><input type="text" name="awarded_by" class="form-control" value="<?= e($award['awarded_by'] ?? '') ?>"></div>
            <div class="col-12"><label class="form-label small fw-medium">Description</label><textarea name="description" class="form-control" rows="2"><?= e($award['description'] ?? '') ?></textarea></div>
            <div class="col-12"><label class="form-label small fw-medium">Notes</label><textarea name="notes" class="form-control" rows="2"><?= e($award['notes'] ?? '') ?></textarea></div>
        </div>
        <hr><div class="d-flex gap-2"><button type="submit" class="btn btn-primary">Update</button><a href="<?= url('awards') ?>" class="btn btn-outline-secondary">Cancel</a></div>
    </form>
</div></div>
<?php $content = ob_get_clean(); ?>
<?php require __DIR__ . '/../layouts/admin.php'; ?>
