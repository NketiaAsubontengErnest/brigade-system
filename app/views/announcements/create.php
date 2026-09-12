<?php $pageTitle = 'New Announcement'; $layout = 'admin'; ob_start(); ?>
<div class="mb-4"><a href="<?= url('announcements') ?>" class="text-muted text-decoration-none small"><i class="bi bi-arrow-left me-1"></i>Announcements</a><h4 class="fw-bold">New Announcement</h4></div>
<div class="card table-card"><div class="card-body p-4">
    <form method="POST" action="<?= url('announcements') ?>">
        <?= csrf_field() ?>
        <div class="row g-3">
            <div class="col-md-6"><label class="form-label small fw-medium">Title *</label><input type="text" name="title" class="form-control" required></div>
            <div class="col-md-3"><label class="form-label small fw-medium">Status</label><select name="status" class="form-select"><option value="Published">Published</option><option value="Draft">Draft</option></select></div>
            <div class="col-md-3"><label class="form-label small fw-medium">Publish Date</label><input type="date" name="publish_date" class="form-control" value="<?= date('Y-m-d') ?>"></div>
            <div class="col-md-4"><label class="form-label small fw-medium">Expiry Date</label><input type="date" name="expiry_date" class="form-control"></div>
            <div class="col-md-2"><label class="form-label small fw-medium">&nbsp;</label><div class="form-check mt-2"><input class="form-check-input" type="checkbox" name="is_public" value="1" checked><label class="form-check-label">Public</label></div></div>
            <div class="col-12"><label class="form-label small fw-medium">Content *</label><textarea name="content" class="form-control" rows="6" required></textarea></div>
        </div>
        <hr><div class="d-flex gap-2"><button type="submit" class="btn btn-primary">Create</button><a href="<?= url('announcements') ?>" class="btn btn-outline-secondary">Cancel</a></div>
    </form>
</div></div>
<?php $content = ob_get_clean(); ?>
<?php require __DIR__ . '/../layouts/admin.php'; ?>
