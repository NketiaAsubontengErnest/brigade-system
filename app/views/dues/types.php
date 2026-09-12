<?php $pageTitle = 'Dues Types'; $layout = 'admin'; ob_start(); ?>
<div class="mb-4"><a href="<?= url('dues') ?>" class="text-muted text-decoration-none small"><i class="bi bi-arrow-left me-1"></i>Dues</a><h4 class="fw-bold">Dues Types</h4></div>
<div class="card table-card"><div class="card-body">
    <form method="POST" action="<?= url('dues/types') ?>" class="row g-2 mb-4">
        <?= csrf_field() ?>
        <div class="col-md-3"><input type="text" name="name" class="form-control form-control-sm" placeholder="Type name" required></div>
        <div class="col-md-3"><input type="text" name="description" class="form-control form-control-sm" placeholder="Description"></div>
        <div class="col-md-2"><input type="number" name="default_amount" class="form-control form-control-sm" placeholder="Default amount" step="0.01" value="0"></div>
        <div class="col-md-2"><button type="submit" class="btn btn-primary btn-sm w-100">Add Type</button></div>
    </form>
    <div class="table-responsive"><table class="table table-hover mb-0">
        <thead><tr><th>Name</th><th>Description</th><th>Default Amount</th><th>Status</th></tr></thead>
        <tbody><?php foreach ($types as $t): ?>
            <tr><td class="fw-medium"><?= e($t['name']) ?></td><td class="text-muted"><?= e($t['description'] ?? '') ?></td><td><?= formatCurrency((float)$t['default_amount']) ?></td>
            <td><span class="badge bg-<?= $t['status'] === 'Active' ? 'success' : 'secondary' ?> badge-status"><?= $t['status'] ?></span></td></tr>
        <?php endforeach; ?></tbody>
    </table></div>
</div></div>
<?php $content = ob_get_clean(); ?>
<?php require __DIR__ . '/../layouts/admin.php'; ?>
