<?php $pageTitle = 'Events'; $layout = 'admin'; ob_start(); ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-0">Events</h4>
        <small class="text-muted">View and manage all brigade events</small>
    </div>
    <?php if (auth()->hasAnyPermission(['events.create'])): ?>
        <a href="<?= url('events/create') ?>" class="btn btn-primary btn-sm">
            <i class="bi bi-plus-lg me-1"></i>Add Event
        </a>
    <?php endif; ?>
</div>

<!-- Filters -->
<div class="card table-card p-3 mb-4">
    <form method="GET" class="row g-2 align-items-end">
        <div class="col-md-6">
            <input type="text" name="search" class="form-control form-control-sm"
                   placeholder="Search events by name or description..." value="<?= e($search ?? '') ?>">
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn btn-outline-primary btn-sm w-100">Search</button>
        </div>
        <div class="col-md-2">
            <a href="<?= url('events/manage') ?>" class="btn btn-outline-secondary btn-sm w-100">Clear</a>
        </div>
    </form>
</div>

<!-- Events Grid -->
<div class="row g-3">
    <?php if (empty($events)): ?>
        <div class="col-12">
            <div class="card table-card p-4 text-center text-muted">
                <i class="bi bi-calendar3" style="font-size:2rem"></i><br>
                No events found
            </div>
        </div>
    <?php else: ?>
        <?php foreach ($events as $ev): ?>
            <div class="col-lg-4 col-md-6">
                <div class="card table-card h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="badge bg-<?= $ev['status'] === 'Upcoming' ? 'primary' : ($ev['status'] === 'Completed' ? 'success' : ($ev['status'] === 'Cancelled' ? 'danger' : 'warning')) ?> badge-status">
                                <?= $ev['status'] ?>
                            </span>
                            <small class="text-muted"><?= formatDate($ev['start_date']) ?></small>
                        </div>
                        <h6 class="fw-bold">
                            <a href="<?= url('events/' . $ev['id']) ?>" class="text-decoration-none text-dark">
                                <?= e($ev['name']) ?>
                            </a>
                        </h6>
                        <p class="text-muted small mb-2"><?= truncate(e($ev['description'] ?? ''), 80) ?></p>
                        <div class="small text-muted">
                            <?php if ($ev['location']): ?>
                                <i class="bi bi-geo-alt me-1"></i><?= e($ev['location']) ?><br>
                            <?php endif; ?>
                            <i class="bi bi-people me-1"></i><?= $ev['registered_count'] ?> registered
                            <?php if ($ev['fee'] > 0): ?>
                                <br><i class="bi bi-cash me-1"></i><?= formatCurrency((float)$ev['fee']) ?>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="card-footer bg-transparent border-0 pt-0">
                        <div class="d-flex gap-1">
                            <a href="<?= url('events/' . $ev['id']) ?>" class="btn btn-sm btn-outline-primary">View</a>
                            <?php if (auth()->hasAnyPermission(['events.edit'])): ?>
                                <a href="<?= url('events/' . $ev['id'] . '/edit') ?>" class="btn btn-sm btn-outline-secondary">Edit</a>
                            <?php endif; ?>
                            <?php if (auth()->hasAnyPermission(['events.delete'])): ?>
                                <form method="POST" action="<?= url('events/' . $ev['id'] . '/delete') ?>" class="d-inline" onsubmit="return confirm('Cancel this event?')">
                                    <?= csrf_field() ?>
                                    <button type="submit" class="btn btn-sm btn-outline-danger">Cancel</button>
                                </form>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>
<?= render_pagination($pagination ?? null) ?>

<?php $content = ob_get_clean(); ?>
<?php require __DIR__ . '/../layouts/admin.php'; ?>
