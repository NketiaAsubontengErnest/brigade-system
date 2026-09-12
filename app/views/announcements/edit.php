<?php $pageTitle = 'Edit Announcement'; $layout = 'admin'; ob_start(); ?>
<div class="mb-4"><a href="<?= url('announcements') ?>" class="text-muted text-decoration-none small"><i class="bi bi-arrow-left me-1"></i>Announcements</a><h4 class="fw-bold">Edit Announcement</h4></div>
<div class="card table-card"><div class="card-body p-4">
    <form method="POST" action="<?= url('announcements/' . $announcement['id']) ?>">
        <?= csrf_field() ?><input type="hidden" name="_method" value="PUT">
        <div class="row g-3">
            <div class="col-md-6"><label class="form-label small fw-medium">Title *</label><input type="text" name="title" class="form-control" value="<?= e($announcement['title']) ?>" required></div>
            <div class="col-md-3"><label class="form-label small fw-medium">Status</label><select name="status" class="form-select"><option value="Published" <?= $announcement['status'] === 'Published' ? 'selected' : '' ?>>Published</option><option value="Draft" <?= $announcement['status'] === 'Draft' ? 'selected' : '' ?>>Draft</option><option value="Archived" <?= $announcement['status'] === 'Archived' ? 'selected' : '' ?>>Archived</option></select></div>
            <div class="col-md-3"><label class="form-label small fw-medium">Public</label><div class="form-check mt-2"><input class="form-check-input" type="checkbox" name="is_public" value="1" <?= $announcement['is_public'] ? 'checked' : '' ?>><label class="form-check-label">Show on public website</label></div></div>
            <div class="col-md-4"><label class="form-label small fw-medium">Publish Date</label><input type="date" name="publish_date" class="form-control" value="<?= e($announcement['publish_date']) ?>"></div>
            <div class="col-md-4"><label class="form-label small fw-medium">Expiry Date</label><input type="date" name="expiry_date" class="form-control" value="<?= e($announcement['expiry_date'] ?? '') ?>"></div>
            <div class="col-12"><label class="form-label small fw-medium">Content *</label><textarea name="content" class="form-control" rows="6" required><?= e($announcement['content']) ?></textarea></div>
        </div>
        <hr><div class="d-flex gap-2"><button type="submit" class="btn btn-primary">Update</button><a href="<?= url('announcements/' . $announcement['id']) ?>" class="btn btn-outline-secondary">Cancel</a></div>
    </form>
</div></div>
<?php $content = ob_get_clean(); ?>
<?php require __DIR__ . '/../layouts/admin.php'; ?>
