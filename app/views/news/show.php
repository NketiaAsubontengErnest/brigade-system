<?php $pageTitle = e($article['title']); $layout = 'admin'; ob_start(); ?>
<div class="mb-4"><a href="<?= url('news/manage') ?>" class="text-muted text-decoration-none small"><i class="bi bi-arrow-left me-1"></i>News</a><h4 class="fw-bold"><?= e($article['title']) ?></h4>
<small class="text-muted">By <?= e($article['author_name'] ?? 'System') ?> · <?= formatDate($article['published_date']) ?> · <span class="badge bg-<?= $article['status'] === 'Published' ? 'success' : 'secondary' ?> badge-status"><?= $article['status'] ?></span></small></div>
<div class="card table-card p-4">
    <?php if ($article['featured_image']): ?><img src="/<?= e($article['featured_image']) ?>" class="img-fluid rounded mb-3" style="max-height:400px;width:100%;object-fit:cover"><?php endif; ?>
    <div><?= nl2br(e($article['content'])) ?></div>
    <div class="d-flex gap-2 mt-4">
        <a href="<?= url('news/' . $article['id'] . '/edit') ?>" class="btn btn-outline-primary btn-sm">Edit</a>
        <form method="POST" action="<?= url('/news/' . $article['id'] . '/delete') ?>" class="d-inline" onsubmit="return confirm('Delete?')">
            <?= csrf_field() ?><button type="submit" class="btn btn-outline-danger btn-sm">Delete</button>
        </form>
    </div>
</div>
<?php $content = ob_get_clean(); ?>
<?php require __DIR__ . '/../layouts/admin.php'; ?>
