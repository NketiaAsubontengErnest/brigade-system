<?php $pageTitle = 'Documents'; $layout = 'admin'; ob_start(); ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <div><h4 class="fw-bold mb-0">Documents</h4></div>
    <a href="<?= url('documents/upload') ?>" class="btn btn-primary btn-sm"><i class="bi bi-upload me-1"></i>Upload Document</a>
</div>
<div class="card table-card"><div class="table-responsive"><table class="table table-hover mb-0">
    <thead><tr><th>Name</th><th>Type</th><th>Size</th><th>Public</th><th>Uploaded By</th><th>Date</th><th>Actions</th></tr></thead>
    <tbody><?php foreach ($documents as $d): ?>
        <tr><td class="fw-medium"><i class="bi bi-file-earmark me-1"></i><?= e($d['name']) ?></td>
        <td class="text-muted small"><?= e($d['file_type'] ?? '') ?></td>
        <td class="text-muted small"><?= $d['file_size'] ? round($d['file_size']/1024, 1) . ' KB' : 'N/A' ?></td>
        <td><?= $d['is_public'] ? '<i class="bi bi-check-circle-fill text-success"></i>' : '<i class="bi bi-lock text-muted"></i>' ?></td>
        <td class="text-muted small"><?= e($d['uploader_name'] ?? 'System') ?></td>
        <td class="text-muted small"><?= formatDate($d['created_at']) ?></td>
        <td><a href="<?= url('documents/' . $d['id'] . 'download') ?>" class="btn btn-sm btn-outline-success"><i class="bi bi-download"></i></a>
        <form method="POST" action="<?= url('/documents/' . $d['id'] . '/delete') ?>" class="d-inline" onsubmit="return confirm('Delete?')">
            <?= csrf_field() ?><button type="submit" class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
        </form></td></tr>
    <?php endforeach; ?></tbody>
</table></div>
<?= render_pagination($pagination ?? null) ?>
</div>
<?php $content = ob_get_clean(); ?>
<?php require __DIR__ . '/../layouts/admin.php'; ?>
