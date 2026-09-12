<?php $pageTitle = 'Activities'; $layout = 'admin'; ob_start(); ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-0">Activities</h4>
        <small class="text-muted">View and manage all brigade activities</small>
    </div>
    <?php if (auth()->hasAnyPermission(['activities.create'])): ?>
        <a href="<?= url('activities/create') ?>" class="btn btn-primary btn-sm">
            <i class="bi bi-plus-lg me-1"></i>Add Activity
        </a>
    <?php endif; ?>
</div>

<!-- Filters -->
<div class="card table-card p-3 mb-4">
    <form method="GET" class="row g-2 align-items-end">
        <div class="col-md-4">
            <input type="text" name="search" class="form-control form-control-sm" placeholder="Search activities..." value="<?= e($search) ?>">
        </div>
        <div class="col-md-3">
            <select name="section" class="form-select form-select-sm">
                <option value="">All Sections</option>
                <?php foreach ($sections as $s): ?>
                    <option value="<?= $s['id'] ?>" <?= $filterSection == $s['id'] ? 'selected' : '' ?>><?= e($s['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn btn-outline-primary btn-sm w-100">Filter</button>
        </div>
        <div class="col-md-2">
            <a href="<?= url('activities/manage') ?>" class="btn btn-outline-secondary btn-sm w-100">Clear</a>
        </div>
    </form>
</div>

<!-- Activities Table -->
<div class="card table-card">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>Activity</th>
                    <th>Date</th>
                    <th>Time</th>
                    <th>Location</th>
                    <th>Section</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($activities)): ?>
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">No activities found</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($activities as $a): ?>
                        <tr>
                            <td><a href="<?= url('activities/' . $a['id']) ?>" class="text-decoration-none fw-medium"><?= e($a['name']) ?></a></td>
                            <td><?= formatDate($a['date']) ?></td>
                            <td class="text-muted"><?= e($a['start_time'] ?? '') ?> <?= $a['end_time'] ? '- ' . e($a['end_time']) : '' ?></td>
                            <td class="text-muted"><?= e($a['location'] ?? '') ?></td>
                            <td><?= e($a['section_name'] ?? 'All') ?></td>
                            <td>
                                <?php $statusClass = $a['status'] === 'Completed' ? 'success' : ($a['status'] === 'Scheduled' ? 'primary' : ($a['status'] === 'Cancelled' ? 'danger' : 'warning')); ?>
                                <span class="badge bg-<?= $statusClass ?> badge-status"><?= e($a['status']) ?></span>
                            </td>
                            <td>
                                <div class="d-flex gap-1">
                                    <a href="<?= url('activities/' . $a['id']) ?>" class="btn btn-sm btn-outline-primary py-0">View</a>
                                    <?php if (auth()->hasAnyPermission(['activities.edit'])): ?>
                                        <a href="<?= url('activities/' . $a['id'] . '/edit') ?>" class="btn btn-sm btn-outline-secondary py-0">Edit</a>
                                    <?php endif; ?>
                                    <?php if (auth()->hasAnyPermission(['activities.delete'])): ?>
                                        <form method="POST" action="<?= url('activities/' . $a['id'] . '/delete') ?>" class="d-inline" onsubmit="return confirm('Cancel this activity?')">
                                            <?= csrf_field() ?>
                                            <button type="submit" class="btn btn-sm btn-outline-danger py-0">Cancel</button>
                                        </form>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <?= render_pagination($pagination ?? null) ?>
</div>

<?php $content = ob_get_clean(); ?>
<?php require __DIR__ . '/../layouts/admin.php'; ?>
