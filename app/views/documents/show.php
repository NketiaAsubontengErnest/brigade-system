<?php $pageTitle = e($document['name']); $layout = 'admin'; ob_start(); ?>
<div class="mb-4"><a href="<?= url('documents') ?>" class="text-muted text-decoration-none small"><i class="bi bi-arrow-left me-1"></i>Documents</a><h4 class="fw-bold"><i class="bi bi-file-earmark me-2"></i><?= e($document['name']) ?></h4></div>
<div class="card table-card p-4"><table class="table table-sm">
    <tr><td class="text-muted" style="width:200px">Type</td><td><?= e($document['file_type'] ?? 'N/A') ?></td></tr>
    <tr><td class="text-muted">Size</td><td><?= $document['file_size'] ? round($document['file_size']/1024, 1) . ' KB' : 'N/A' ?></td></tr>
    <tr><td class="text-muted">Public</td><td><?= $document['is_public'] ? 'Yes' : 'No' ?></td></tr>
    <tr><td class="text-muted">Uploaded By</td><td><?= e($document['uploader_name'] ?? 'System') ?></td></tr>
    <tr><td class="text-muted">Date</td><td><?= formatDate($document['created_at']) ?></td></tr>
    <?php if ($document['description']): ?><tr><td class="text-muted">Description</td><td><?= e($document['description']) ?></td></tr><?php endif; ?>
</table>
<div class="mt-3"><a href="<?= url('documents/' . $document['id'] . 'download') ?>" class="btn btn-primary"><i class="bi bi-download me-1"></i>Download</a></div></div>
<?php $content = ob_get_clean(); ?>
<?php require __DIR__ . '/../layouts/admin.php'; ?>
