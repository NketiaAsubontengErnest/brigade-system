<?php $pageTitle = 'Attendance Session'; $layout = 'admin'; ob_start(); ?>
<div class="mb-4"><a href="<?= url('attendance') ?>" class="text-muted text-decoration-none small"><i class="bi bi-arrow-left me-1"></i>Attendance</a><h4 class="fw-bold">Attendance: <?= e($session['activity_name'] ?? 'Session') ?></h4><small class="text-muted"><?= formatDate($session['date']) ?> · <?= e($session['section_name'] ?? 'All Sections') ?></small></div>
<div class="card table-card"><div class="table-responsive"><table class="table table-hover mb-0">
    <thead><tr><th>Member</th><th>Number</th><th>Status</th></tr></thead>
    <tbody><?php foreach ($records as $r): ?>
        <tr><td class="fw-medium"><?= e($r['first_name'] . ' ' . $r['last_name']) ?></td><td class="text-muted"><?= e($r['member_number'] ?? '') ?></td>
        <td><span class="badge bg-<?= $r['status'] === 'Present' ? 'success' : ($r['status'] === 'Absent' ? 'danger' : ($r['status'] === 'Late' ? 'warning' : 'info')) ?> badge-status"><?= $r['status'] ?></span></td></tr>
    <?php endforeach; ?></tbody>
</table></div></div>
<?php $content = ob_get_clean(); ?>
<?php require __DIR__ . '/../layouts/admin.php'; ?>
