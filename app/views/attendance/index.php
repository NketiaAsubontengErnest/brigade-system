<?php $pageTitle = 'Attendance'; $layout = 'admin'; ob_start(); ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <div><h4 class="fw-bold mb-0">Attendance</h4></div>
    <div class="d-flex gap-2">
        <a href="<?= url('attendance/scan') ?>" class="btn btn-success btn-sm"><i class="bi bi-qr-code-scan me-1"></i>QR Code Scanner</a>
        <a href="<?= url('attendance/mark') ?>" class="btn btn-primary btn-sm"><i class="bi bi-plus-lg me-1"></i>Mark Attendance</a>
    </div>
</div>
<div class="card table-card"><div class="table-responsive"><table class="table table-hover mb-0">
    <thead><tr><th>Date</th><th>Activity</th><th>Section</th><th>Present</th><th>Total</th><th>Rate</th><th>Actions</th></tr></thead>
    <tbody><?php if (empty($sessions)): ?><tr><td colspan="7" class="text-center text-muted py-4">No attendance sessions</td></tr>
    <?php else: ?><?php foreach ($sessions as $s): ?>
        <tr>
            <td><?= formatDate($s['date']) ?></td>
            <td class="fw-medium"><?= e($s['activity_name'] ?? 'N/A') ?></td>
            <td><?= e($s['section_name'] ?? 'All') ?></td>
            <td><strong><?= $s['present_count'] ?></strong></td>
            <td><?= $s['total_count'] ?></td>
            <td><?= $s['total_count'] > 0 ? round(($s['present_count']/$s['total_count'])*100) : 0 ?>%</td>
            <td><a href="<?= url('attendance/session/' . $s['id']) ?>" class="btn btn-sm btn-outline-primary">View</a></td>
        </tr>
    <?php endforeach; ?><?php endif; ?></tbody>
</table></div>
<?= render_pagination($pagination) ?>
</div>
<?php $content = ob_get_clean(); ?>
<?php require __DIR__ . '/../layouts/admin.php'; ?>
