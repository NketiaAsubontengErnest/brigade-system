<?php $pageTitle = 'Upload Document'; $layout = 'admin'; ob_start(); ?>
<div class="mb-4"><a href="<?= url('documents') ?>" class="text-muted text-decoration-none small"><i class="bi bi-arrow-left me-1"></i>Documents</a><h4 class="fw-bold">Upload Document</h4></div>
<div class="card table-card"><div class="card-body p-4">
    <form method="POST" action="<?= url('documents/upload') ?>" enctype="multipart/form-data">
        <?= csrf_field() ?>
        <div class="row g-3">
            <div class="col-md-6"><label class="form-label small fw-medium">Name *</label><input type="text" name="name" class="form-control" required></div>
            <div class="col-md-6"><label class="form-label small fw-medium">File *</label><input type="file" name="document" class="form-control" required></div>
            <div class="col-md-8"><label class="form-label small fw-medium">Description</label><textarea name="description" class="form-control" rows="2"></textarea></div>
            <div class="col-md-4"><label class="form-label small fw-medium">&nbsp;</label><div class="form-check mt-2"><input class="form-check-input" type="checkbox" name="is_public" value="1"><label class="form-check-label">Make public</label></div></div>
        </div>
        <hr><div class="d-flex gap-2"><button type="submit" class="btn btn-primary">Upload</button><a href="<?= url('documents') ?>" class="btn btn-outline-secondary">Cancel</a></div>
    </form>
</div></div>
<?php $content = ob_get_clean(); ?>
<?php require __DIR__ . '/../layouts/admin.php'; ?>
