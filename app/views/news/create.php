<?php $pageTitle = 'New Article'; $layout = 'admin'; ob_start(); ?>
<div class="mb-4"><a href="<?= url('news/manage') ?>" class="text-muted text-decoration-none small"><i class="bi bi-arrow-left me-1"></i>News</a><h4 class="fw-bold">New Article</h4></div>
<div class="card table-card"><div class="card-body p-4">
    <form method="POST" action="<?= url('news') ?>" enctype="multipart/form-data">
        <?= csrf_field() ?>
        <div class="row g-3">
            <div class="col-md-6"><label class="form-label small fw-medium">Title *</label><input type="text" name="title" class="form-control" required></div>
            <div class="col-md-3"><label class="form-label small fw-medium">Status</label><select name="status" class="form-select"><option value="Draft">Draft</option><option value="Published">Published</option></select></div>
            <div class="col-md-3"><label class="form-label small fw-medium">Published Date</label><input type="date" name="published_date" class="form-control" value="<?= date('Y-m-d') ?>"></div>
            <div class="col-md-6"><label class="form-label small fw-medium">Featured Image</label><input type="file" name="featured_image" class="form-control" accept="image/*"></div>
            <div class="col-12"><label class="form-label small fw-medium">Content *</label><textarea name="content" class="form-control" rows="10" required></textarea></div>
        </div>
        <hr><div class="d-flex gap-2"><button type="submit" class="btn btn-primary">Create</button><a href="<?= url('news/manage') ?>" class="btn btn-outline-secondary">Cancel</a></div>
    </form>
</div></div>
<?php $content = ob_get_clean(); ?>
<?php require __DIR__ . '/../layouts/admin.php'; ?>
