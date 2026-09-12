<?php $pageTitle = 'Announcements'; $layout = 'admin'; ob_start(); ?>
<div class="mb-4"><h4 class="fw-bold">Announcements</h4></div>
<?php foreach ($announcements as $a): ?>
    <div class="card table-card p-4 mb-3">
        <h6 class="fw-bold"><?= e($a['title']) ?></h6>
        <small class="text-muted"><i class="bi bi-calendar me-1"></i><?= formatDate($a['publish_date']) ?></small>
        <p class="mt-2 mb-0"><?= nl2br(e($a['content'])) ?></p>
    </div>
<?php endforeach; ?>
<?php if (empty($announcements)): ?><div class="card table-card p-4 text-center text-muted">No announcements</div><?php endif; ?>
<?php $content = ob_get_clean(); ?>
<?php require __DIR__ . '/../layouts/admin.php'; ?>
