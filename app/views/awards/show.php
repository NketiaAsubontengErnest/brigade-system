<?php $pageTitle = e($award['name']); $layout = 'admin'; ob_start(); ?>
<div class="mb-4"><a href="<?= url('awards') ?>" class="text-muted text-decoration-none small"><i class="bi bi-arrow-left me-1"></i>Awards</a><h4 class="fw-bold"><i class="bi bi-trophy-fill text-success me-2"></i><?= e($award['name']) ?></h4></div>
<div class="card table-card p-4"><table class="table table-sm">
    <tr><td class="text-muted" style="width:200px">Member</td><td class="fw-medium"><a href="<?= url('members/' . $award['member_id']) ?>" class="text-decoration-none"><?= e($award['first_name'] . ' ' . $award['last_name']) ?></a></td></tr>
    <tr><td class="text-muted">Description</td><td><?= e($award['description'] ?? '') ?></td></tr>
    <tr><td class="text-muted">Date Awarded</td><td><?= formatDate($award['date_awarded']) ?></td></tr>
    <tr><td class="text-muted">Awarded By</td><td><?= e($award['awarded_by'] ?? 'N/A') ?></td></tr>
    <tr><td class="text-muted">Notes</td><td><?= e($award['notes'] ?? '') ?></td></tr>
</table></div>
<?php $content = ob_get_clean(); ?>
<?php require __DIR__ . '/../layouts/admin.php'; ?>
