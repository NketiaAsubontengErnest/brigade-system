<?php $pageTitle = 'News'; $layout = 'admin'; ob_start(); ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <div><h4 class="fw-bold mb-0">News</h4></div>
    <a href="<?= url('news/create') ?>" class="btn btn-primary btn-sm"><i class="bi bi-plus-lg me-1"></i>New Article</a>
</div>
<div class="card table-card">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr><th>Title</th><th>Author</th><th>Status</th><th>Date</th><th>Actions</th></tr>
            </thead>
            <tbody>
                <?php if (empty($articles)): ?>
                    <tr><td colspan="5" class="text-center text-muted py-4">No articles yet</td></tr>
                <?php else: ?>
                    <?php foreach ($articles as $a): ?>
                        <tr>
                            <td class="fw-medium"><a href="<?= url('news/' . $a['id']) ?>" class="text-decoration-none"><?= e($a['title']) ?></a></td>
                            <td class="text-muted small"><?= e($a['author_name'] ?? 'System') ?></td>
                            <td><span class="badge bg-<?= $a['status'] === 'Published' ? 'success' : ($a['status'] === 'Draft' ? 'secondary' : 'info') ?> badge-status"><?= $a['status'] ?></span></td>
                            <td class="text-muted small"><?= formatDate($a['published_date']) ?></td>
                            <td>
                                <div class="d-flex gap-1">
                                    <a href="<?= url('news/' . $a['id']) ?>" class="btn btn-sm btn-outline-primary py-0">View</a>
                                    <a href="<?= url('news/' . $a['id'] . '/edit') ?>" class="btn btn-sm btn-outline-secondary py-0">Edit</a>
                                    <?php if (auth()->hasAnyPermission(['news.delete'])): ?>
                                        <form method="POST" action="<?= url('news/' . $a['id'] . '/delete') ?>" class="d-inline" onsubmit="return confirm('Delete this article?')">
                                            <?= csrf_field() ?>
                                            <button type="submit" class="btn btn-sm btn-outline-danger py-0">Delete</button>
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
