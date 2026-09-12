<?php $pageTitle = 'Documents'; $layout = 'admin'; ob_start(); ?>
<div class="mb-4"><h4 class="fw-bold">Documents</h4></div>
<div class="card table-card"><div class="table-responsive"><table class="table table-hover mb-0">
    <thead><tr><th>Name</th><th>Type</th><th>Size</th><th>Date</th><th>Action</th></tr></thead>
    <tbody><?php foreach ($documents as $d): ?>
        <tr><td class="fw-medium"><i class="bi bi-file-earmark me-1"></i><?= e($d['name']) ?></td><td class="text-muted small"><?= e($d['file_type'] ?? '') ?></td>
        <td class="text-muted small"><?= $d['file_size'] ? round($d['file_size']/1024) . ' KB' : '' ?></td><td class="text-muted small"><?= formatDate($d['created_at']) ?></td>
        <td><a href="<?= url('documents/' . $d['id'] . 'download') ?>" class="btn btn-sm btn-outline-success"><i class="bi bi-download"></i></a></td></tr>
    <?php endforeach; ?>
    <?php if (empty($documents)): ?><tr><td colspan="5" class="text-center text-muted py-4">No documents available</td></tr><?php endif; ?></tbody>
</table></div></div>
<?php $content = ob_get_clean(); ?>
<?php require __DIR__ . '/../layouts/admin.php'; ?>
