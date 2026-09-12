<?php $pageTitle = 'Create Section'; $layout = 'admin'; ob_start(); ?>
<div class="mb-4">
    <a href="<?= url('sections') ?>" class="text-muted text-decoration-none small"><i class="bi bi-arrow-left me-1"></i>Sections</a>
    <h4 class="fw-bold">Create Section</h4>
</div>
<div class="card table-card">
    <div class="card-body p-4">
        <form method="POST" action="<?= url('sections') ?>">
            <?= csrf_field() ?>
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label small fw-medium">Section Name *</label>
                    <input type="text" name="name" class="form-control" required placeholder="e.g. Junior Brigade">
                </div>
                <div class="col-md-4">
                    <label class="form-label small fw-medium">Type *</label>
                    <select name="type" class="form-select" required>
                        <option value="Section">Section</option>
                        <option value="Junior">Junior</option>
                    </select>
                    <small class="text-muted">"Section" for standard groups, "Junior" for younger age groups.</small>
                </div>
                <div class="col-md-4">
                    <label class="form-label small fw-medium">Age Range</label>
                    <input type="text" name="age_range" class="form-control" placeholder="e.g. 12-14">
                </div>
                <div class="col-12">
                    <label class="form-label small fw-medium">Description</label>
                    <textarea name="description" class="form-control" rows="2"></textarea>
                </div>
            </div>
            <hr>
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">Create Section</button>
                <a href="<?= url('sections') ?>" class="btn btn-outline-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
<?php $content = ob_get_clean(); ?>
<?php require __DIR__ . '/../layouts/admin.php'; ?>
