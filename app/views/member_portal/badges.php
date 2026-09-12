<?php $pageTitle = 'My Badges & Awards'; $layout = 'admin'; ob_start(); ?>
<div class="mb-4"><h4 class="fw-bold">My Badges & Awards</h4></div>
<div class="row g-3">
    <div class="col-lg-6">
        <h6 class="fw-bold">Badges</h6>
        <?php if (empty($badges)): ?>
            <div class="card table-card p-4 text-center text-muted">No badges awarded yet</div>
        <?php else: ?>
            <?php foreach ($badges as $b): ?>
                <div class="card table-card p-3 mb-2"><div class="d-flex align-items-center">
                    <i class="bi bi-award-fill text-warning fs-4 me-3"></i>
                    <div><div class="fw-bold"><?= e($b['badge_name']) ?></div><small class="text-muted"><?= e($b['badge_description'] ?? '') ?></small><br><small class="text-muted">Awarded: <?= formatDate($b['date_awarded']) ?> by <?= e($b['awarded_by'] ?? '') ?></small></div>
                </div></div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
    <div class="col-lg-6">
        <h6 class="fw-bold">Awards</h6>
        <?php if (empty($awards)): ?>
            <div class="card table-card p-4 text-center text-muted">No awards received yet</div>
        <?php else: ?>
            <?php foreach ($awards as $a): ?>
                <div class="card table-card p-3 mb-2"><div class="d-flex align-items-center">
                    <i class="bi bi-trophy-fill text-success fs-4 me-3"></i>
                    <div><div class="fw-bold"><?= e($a['name']) ?></div><small class="text-muted"><?= e($a['description'] ?? '') ?></small><br><small class="text-muted">Awarded: <?= formatDate($a['date_awarded']) ?></small></div>
                </div></div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>
<?php $content = ob_get_clean(); ?>
<?php require __DIR__ . '/../layouts/admin.php'; ?>
