<?php $pageTitle = e($announcement['title']); $layout = 'admin'; ob_start(); ?>
<div class="mb-4"><a href="<?= url('announcements') ?>" class="text-muted text-decoration-none small"><i class="bi bi-arrow-left me-1"></i>Announcements</a><h4 class="fw-bold"><?= e($announcement['title']) ?></h4>
<small class="text-muted"><?= formatDate($announcement['publish_date']) ?> · <?= e($announcement['creator_name'] ?? 'System') ?></small></div>
<div class="card table-card p-4">
    <div class="mb-3"><span class="badge bg-<?= $announcement['status'] === 'Published' ? 'success' : 'secondary' ?> badge-status"><?= $announcement['status'] ?></span>
    <?= $announcement['is_public'] ? '<span class="badge bg-info badge-status">Public</span>' : '' ?></div>
    <div class="mb-3"><?= nl2br(e($announcement['content'])) ?></div>
    <?php if ($announcement['expiry_date']): ?><p class="text-muted small">Expires: <?= formatDate($announcement['expiry_date']) ?></p><?php endif; ?>
    <div class="d-flex gap-2 mt-4">
        <a href="<?= url('announcements/' . $announcement['id'] . 'edit') ?>" class="btn btn-outline-primary btn-sm">Edit</a>
        <form method="POST" action="<?= url('/announcements/' . $announcement['id'] . '/delete') ?>" class="d-inline" onsubmit="return confirm('Delete?')">
            <?= csrf_field() ?><button type="submit" class="btn btn-outline-danger btn-sm">Delete</button>
        </form>
    </div>
</div>
<?php $content = ob_get_clean(); ?>
<?php require __DIR__ . '/../layouts/admin.php'; ?>
