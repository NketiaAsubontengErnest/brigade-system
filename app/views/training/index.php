<?php $pageTitle = 'Training'; $layout = 'admin'; ob_start(); ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <div><h4 class="fw-bold mb-0">Training Courses</h4></div>
    <a href="<?= url('training/create') ?>" class="btn btn-primary btn-sm"><i class="bi bi-plus-lg me-1"></i>Create Course</a>
</div>
<div class="row g-3">
    <?php if (empty($courses)): ?>
        <div class="col-12"><div class="card table-card p-4 text-center text-muted">No training courses</div></div>
    <?php else: ?>
        <?php foreach ($courses as $c): ?>
            <div class="col-lg-4 col-md-6">
                <div class="card table-card p-3 h-100">
                    <div class="d-flex justify-content-between mb-2">
                        <h6 class="fw-bold mb-0">
                            <a href="<?= url('training/' . $c['id']) ?>" class="text-decoration-none text-dark"><?= e($c['name']) ?></a>
                        </h6>
                        <span class="badge bg-<?= $c['status'] === 'Active' ? 'success' : 'secondary' ?> badge-status"><?= $c['status'] ?></span>
                    </div>
                    <p class="text-muted small mb-2"><?= e($c['description'] ?? '') ?></p>
                    <div class="small text-muted">
                        <i class="bi bi-clock me-1"></i><?= e($c['duration'] ?? 'N/A') ?><br>
                        <i class="bi bi-person me-1"></i><?= e($c['instructor'] ?? 'N/A') ?>
                    </div>
                    <div class="mt-2 small">
                        <span class="text-success"><?= $c['enrolled_count'] ?? 0 ?> enrolled</span> · 
                        <span class="text-primary"><?= $c['completed_count'] ?? 0 ?> completed</span>
                    </div>
                    <div class="mt-3 d-flex gap-1">
                        <a href="<?= url('training/' . $c['id']) ?>" class="btn btn-sm btn-outline-primary">View</a>
                        <?php if (auth()->hasAnyPermission(['training.edit'])): ?>
                            <a href="<?= url('training/' . $c['id'] . '/edit') ?>" class="btn btn-sm btn-outline-secondary">Edit</a>
                        <?php endif; ?>
                        <?php if (auth()->hasAnyPermission(['training.create'])): ?>
                            <form method="POST" action="<?= url('training/' . $c['id'] . '/delete') ?>" class="d-inline" onsubmit="return confirm('Delete this training course?')">
                                <?= csrf_field() ?>
                                <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>
<?= render_pagination($pagination ?? null) ?>
<?php $content = ob_get_clean(); ?>
<?php require __DIR__ . '/../layouts/admin.php'; ?>
