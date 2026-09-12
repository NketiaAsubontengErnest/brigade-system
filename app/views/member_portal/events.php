<?php $pageTitle = 'My Events'; $layout = 'admin'; ob_start(); ?>
<div class="mb-4"><h4 class="fw-bold">Events</h4></div>
<div class="row g-3"><?php foreach ($events as $e): ?>
    <div class="col-lg-4 col-md-6">
        <div class="card table-card p-3 h-100">
            <div class="d-flex justify-content-between"><h6 class="fw-bold"><?= e($e['name']) ?></h6>
                <?= $e['reg_status'] ? '<span class="badge bg-success badge-status">Registered</span>' : '' ?></div>
            <small class="text-muted"><i class="bi bi-calendar me-1"></i><?= formatDate($e['start_date']) ?></small>
            <?php if ($e['location']): ?><small class="text-muted d-block"><i class="bi bi-geo-alt me-1"></i><?= e($e['location']) ?></small><?php endif; ?>
        </div>
    </div>
<?php endforeach; ?>
<?php if (empty($events)): ?><p class="text-center text-muted py-4">No events available</p><?php endif; ?></div>
<?php $content = ob_get_clean(); ?>
<?php require __DIR__ . '/../layouts/admin.php'; ?>
