<?php $pageTitle = 'Audit Logs'; $layout = 'admin'; ob_start(); ?>
<div class="mb-4"><a href="<?= url('settings') ?>" class="text-muted text-decoration-none small"><i class="bi bi-arrow-left me-1"></i>Settings</a><h4 class="fw-bold">Audit Logs</h4></div>
<div class="card table-card"><div class="table-responsive"><table class="table table-hover table-sm mb-0">
    <thead><tr><th>Date</th><th>User</th><th>Action</th><th>Entity</th><th>Description</th><th>IP</th></tr></thead>
    <tbody><?php foreach ($logs as $l): ?>
        <tr><td class="text-muted small"><?= formatDateTime($l['created_at']) ?></td><td><?= e($l['user_name'] ?? 'System') ?></td>
        <td><span class="badge bg-secondary badge-status"><?= e($l['action']) ?></span></td>
        <td class="text-muted small"><?= e($l['entity']) ?> #<?= $l['entity_id'] ?? '' ?></td>
        <td class="small"><?= e($l['description'] ?? '') ?></td><td class="text-muted small"><?= e($l['ip_address'] ?? '') ?></td></tr>
    <?php endforeach; ?></tbody>
</table></div>
<?= render_pagination($pagination) ?>
</div>
<?php $content = ob_get_clean(); ?>
<?php require __DIR__ . '/../layouts/admin.php'; ?>
