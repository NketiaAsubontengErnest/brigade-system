<?php $pageTitle = e($activity['name']); $layout = 'admin'; ob_start(); ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <div><a href="<?= url('activities/manage') ?>" class="text-muted text-decoration-none small"><i class="bi bi-arrow-left me-1"></i>Activities</a><h4 class="fw-bold mb-0"><?= e($activity['name']) ?></h4></div>
    <div>
        <a href="<?= url('activities/' . $activity['id'] . 'edit') ?>" class="btn btn-outline-primary btn-sm">Edit</a>
        <form method="POST" action="<?= url('/activities/' . $activity['id'] . '/delete') ?>" class="d-inline" onsubmit="return confirm('Cancel this activity?')">
            <?= csrf_field() ?><button type="submit" class="btn btn-outline-danger btn-sm">Cancel Activity</button>
        </form>
    </div>
</div>
<div class="row g-4">
    <div class="col-lg-4">
        <div class="card table-card p-4">
            <h6 class="fw-bold mb-3">Details</h6>
            <table class="table table-sm small mb-0">
                <tr><td class="text-muted">Date</td><td><?= formatDate($activity['date']) ?></td></tr>
                <tr><td class="text-muted">Time</td><td><?= e($activity['start_time'] ?? 'N/A') ?> <?= $activity['end_time'] ? '- ' . e($activity['end_time']) : '' ?></td></tr>
                <tr><td class="text-muted">Location</td><td><?= e($activity['location'] ?? 'N/A') ?></td></tr>
                <tr><td class="text-muted">Section</td><td><?= e($activity['section_name'] ?? 'All') ?></td></tr>
                <tr><td class="text-muted">Status</td><td><span class="badge bg-primary badge-status"><?= $activity['status'] ?></span></td></tr>
            </table>
        </div>
    </div>
    <div class="col-lg-8">
        <h6 class="fw-bold mb-3">Attendance Sessions</h6>
        <div class="card table-card"><div class="table-responsive"><table class="table table-hover mb-0">
            <thead><tr><th>Date</th><th>Present</th><th>Total</th><th>Rate</th><th>Actions</th></tr></thead>
            <tbody><?php if (empty($sessions)): ?><tr><td colspan="5" class="text-center text-muted py-3">No attendance sessions</td></tr>
            <?php else: ?><?php foreach ($sessions as $s): ?>
                <tr><td><?= formatDate($s['date']) ?></td><td><?= $s['present_count'] ?></td><td><?= $s['total_count'] ?></td><td><?= $s['total_count'] > 0 ? round(($s['present_count']/$s['total_count'])*100) : 0 ?>%</td>
                <td><a href="<?= url('attendance/session/' . $s['id']) ?>" class="btn btn-sm btn-outline-primary">View</a></td></tr>
            <?php endforeach; ?><?php endif; ?></tbody>
        </table></div>
    </div>
</div>
<?php $content = ob_get_clean(); ?>
<?php require __DIR__ . '/../layouts/admin.php'; ?>
