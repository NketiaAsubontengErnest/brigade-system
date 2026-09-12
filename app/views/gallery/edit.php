<?php $pageTitle = 'Edit Album - ' . e($album['name']); $layout = 'admin'; ob_start(); ?>
<div class="mb-4">
    <a href="<?= url('gallery/album/' . $album['id']) ?>" class="text-muted text-decoration-none small"><i class="bi bi-arrow-left me-1"></i>Back to Album</a>
    <h4 class="fw-bold mt-1">Edit Album</h4>
</div>

<div class="card table-card">
    <div class="card-body p-4">
        <form method="POST" action="<?= url('gallery/album/' . $album['id'] . '/edit') ?>">
            <?= csrf_field() ?>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label small fw-medium">Album Name *</label>
                    <input type="text" name="name" class="form-control" value="<?= e($album['name']) ?>" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label small fw-medium">Status</label>
                    <select name="status" class="form-select">
                        <option value="Active" <?= ($album['status'] ?? 'Active') === 'Active' ? 'selected' : '' ?>>Active</option>
                        <option value="Archived" <?= ($album['status'] ?? '') === 'Archived' ? 'selected' : '' ?>>Archived</option>
                    </select>
                </div>
                <div class="col-12">
                    <label class="form-label small fw-medium">Description</label>
                    <textarea name="description" class="form-control" rows="3"><?= e($album['description'] ?? '') ?></textarea>
                </div>
            </div>
            <hr>
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>Save Changes</button>
                <a href="<?= url('gallery/album/' . $album['id']) ?>" class="btn btn-outline-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
<?php $content = ob_get_clean(); ?>
<?php require __DIR__ . '/../layouts/admin.php'; ?>
