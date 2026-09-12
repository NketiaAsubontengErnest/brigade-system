<?php $pageTitle = 'Notifications'; $layout = 'admin'; ob_start(); ?>
<div class="mb-4"><a href="<?= url('settings') ?>" class="text-muted text-decoration-none small"><i class="bi bi-arrow-left me-1"></i>Settings</a><h4 class="fw-bold">Notifications</h4></div>
<div class="card table-card"><div class="list-group list-group-flush">
    <?php if (empty($notifications)): ?>
        <div class="list-group-item text-center text-muted py-4">No notifications</div>
    <?php else: ?>
        <?php foreach ($notifications as $n): ?>
            <div class="list-group-item <?= !$n['is_read'] ? 'bg-light' : '' ?>">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="fw-medium small"><?= e($n['title']) ?></div>
                        <p class="mb-1 small text-muted"><?= e($n['message']) ?></p>
                        <small class="text-muted"><?= timeAgo($n['created_at']) ?></small>
                    </div>
                    <div>
                        <?php if (!$n['is_read']): ?>
                            <form method="POST" action="<?= url('/settings/notifications/' . $n['id'] . '/read') ?>" class="d-inline">
                                <?= csrf_field() ?><button type="submit" class="btn btn-sm btn-outline-primary">Mark Read</button>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div></div>
<?php $content = ob_get_clean(); ?>
<?php require __DIR__ . '/../layouts/admin.php'; ?>
