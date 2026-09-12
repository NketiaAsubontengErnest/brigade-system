<?php $pageTitle = 'Sections'; $layout = 'admin'; ob_start(); ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <div><h4 class="fw-bold mb-0">Sections</h4></div>
    <a href="<?= url('sections/create') ?>" class="btn btn-primary btn-sm"><i class="bi bi-plus-lg me-1"></i>Add Section</a>
</div>
<div class="row g-3">
    <?php if (empty($sections)): ?>
        <div class="col-12"><div class="card table-card p-4 text-center text-muted">No sections yet</div></div>
    <?php else: ?>
        <?php foreach ($sections as $s): ?>
            <div class="col-lg-3 col-md-4 col-sm-6">
                <div class="card table-card p-3 h-100">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <h6 class="fw-bold mb-0"><?= e($s['name']) ?></h6>
                        <div class="d-flex gap-1">
                            <span class="badge bg-<?= $s['status'] === 'Active' ? 'success' : 'secondary' ?> badge-status"><?= $s['status'] ?></span>
                            <?php if (!empty($s['type'])): ?>
                                <span class="badge bg-info badge-status"><?= e($s['type']) ?></span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <p class="text-muted small mb-2"><?= e($s['description'] ?? '') ?></p>
                    <p class="small mb-2"><i class="bi bi-people me-1"></i><?= number_format($s['member_count']) ?> members</p>
                    <?php if (!empty($s['age_range'])): ?>
                        <p class="text-muted small mb-2"><i class="bi bi-calendar3 me-1"></i>Ages <?= e($s['age_range']) ?></p>
                    <?php endif; ?>
                    <div class="mt-auto d-flex gap-1 flex-wrap">
                        <a href="<?= url('sections/' . $s['id']) ?>" class="btn btn-sm btn-outline-primary">View</a>
                        <?php if (auth()->hasAnyPermission(['sections.edit'])): ?>
                            <a href="<?= url('sections/' . $s['id'] . '/edit') ?>" class="btn btn-sm btn-outline-secondary">Edit</a>
                            <form method="POST" action="<?= url('/sections/' . $s['id'] . '/toggle') ?>" class="d-inline">
                                <?= csrf_field() ?>
                                <button type="submit" class="btn btn-sm btn-outline-<?= $s['status'] === 'Active' ? 'warning' : 'success' ?>"><?= $s['status'] === 'Active' ? 'Deactivate' : 'Activate' ?></button>
                            </form>
                        <?php endif; ?>
                        <?php if (auth()->hasAnyPermission(['sections.create'])): ?>
                            <form method="POST" action="<?= url('/sections/' . $s['id'] . '/delete') ?>" class="d-inline" onsubmit="return confirm('Delete this section? Members will be unassigned.')">
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
