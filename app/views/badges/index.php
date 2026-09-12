<?php $pageTitle = 'Badges'; $layout = 'admin'; ob_start(); ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <div><h4 class="fw-bold mb-0">Badges</h4></div>
    <a href="<?= url('badges/create') ?>" class="btn btn-primary btn-sm"><i class="bi bi-plus-lg me-1"></i>Create Badge</a>
</div>
<div class="row g-3">
    <?php if (empty($badges)): ?>
        <div class="col-12"><div class="card table-card p-4 text-center text-muted">No badges</div></div>
    <?php else: ?>
        <?php foreach ($badges as $b): ?>
            <div class="col-lg-3 col-md-4 col-sm-6">
                <div class="card table-card p-3 h-100 text-center">
                    <i class="bi bi-award-fill text-warning" style="font-size:2rem"></i>
                    <h6 class="fw-bold mt-2">
                        <a href="<?= url('badges/' . $b['id']) ?>" class="text-decoration-none text-dark"><?= e($b['name']) ?></a>
                    </h6>
                    <p class="text-muted small mb-2"><?= e(truncate($b['description'] ?? '', 50)) ?></p>
                    <span class="badge bg-primary badge-status"><?= $b['awarded_count'] ?> awarded</span>
                    <div class="mt-3 d-flex gap-1 justify-content-center">
                        <a href="<?= url('badges/' . $b['id']) ?>" class="btn btn-sm btn-outline-primary">View</a>
                        <?php if (auth()->hasAnyPermission(['badges.create'])): ?>
                            <a href="<?= url('badges/' . $b['id'] . '/edit') ?>" class="btn btn-sm btn-outline-secondary">Edit</a>
                        <?php endif; ?>
                        <?php if (auth()->hasAnyPermission(['badges.create'])): ?>
                            <form method="POST" action="<?= url('badges/' . $b['id'] . '/delete') ?>" class="d-inline" onsubmit="return confirm('Delete this badge?')">
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
