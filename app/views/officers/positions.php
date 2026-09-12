<?php $pageTitle = 'Positions'; $layout = 'admin'; ob_start(); ?>
<div class="d-flex justify-content-between align-items-center mb-4"><h4 class="fw-bold mb-0">Positions</h4></div>
<div class="card table-card"><div class="card-body">
    <form method="POST" action="<?= url('officers/positions') ?>" class="row g-2 mb-4">
        <?= csrf_field() ?>
        <div class="col-md-4"><input type="text" name="name" class="form-control form-control-sm" placeholder="Position name" required></div>
        <div class="col-md-4"><input type="text" name="description" class="form-control form-control-sm" placeholder="Description"></div>
        <div class="col-md-2"><button type="submit" class="btn btn-primary btn-sm w-100">Add Position</button></div>
    </form>
    <div class="table-responsive"><table class="table table-hover mb-0"><thead><tr><th>Name</th><th>Description</th><th>Status</th></tr></thead>
    <tbody><?php foreach ($positions as $p): ?><tr><td class="fw-medium"><?= e($p['name']) ?></td><td class="text-muted"><?= e($p['description'] ?? '') ?></td><td><span class="badge bg-<?= $p['status'] === 'Active' ? 'success' : 'secondary' ?> badge-status"><?= $p['status'] ?></span></td></tr><?php endforeach; ?></tbody>
    </table></div>
</div></div>
<?php $content = ob_get_clean(); ?>
<?php require __DIR__ . '/../layouts/admin.php'; ?>
