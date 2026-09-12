<?php $pageTitle = 'Documents'; $layout = 'public'; ob_start(); ?>
<section class="py-5"><div class="container">
    <div class="section-header text-center mb-4"><h2>Documents</h2><div class="divider mx-auto"></div></div>
    <div class="row g-3">
        <?php foreach ($documents as $d): ?>
        <div class="col-md-6">
            <div class="card feature-card p-3">
                <div class="d-flex align-items-center">
                    <i class="bi bi-file-earmark-text text-primary fs-3 me-3"></i>
                    <div><h6 class="fw-bold mb-0"><?= e($d['name']) ?></h6><small class="text-muted"><?= e($d['file_type'] ?? '') ?> · <?= $d['file_size'] ? round($d['file_size']/1024) . ' KB' : '' ?></small></div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
        <?php if (empty($documents)): ?><p class="text-center text-muted">No documents available.</p><?php endif; ?>
    </div>
</div></section>
<?php $content = ob_get_clean(); ?>
<?php require __DIR__ . '/../layouts/public.php'; ?>
