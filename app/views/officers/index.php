<?php $pageTitle = 'Officers'; $layout = 'admin'; ob_start(); ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <div><h4 class="fw-bold mb-0">Officers</h4></div>
    <div><a href="<?= url('officers/positions') ?>" class="btn btn-outline-secondary btn-sm me-2">Positions</a><a href="<?= url('officers/create') ?>" class="btn btn-primary btn-sm"><i class="bi bi-plus-lg me-1"></i>Appoint Officer</a></div>
</div>
<!-- Filters -->
<div class="card table-card p-3 mb-4">
    <form method="GET" class="row g-2 align-items-end">
        <div class="col-md-6">
            <input type="text" name="search" class="form-control form-control-sm"
                   placeholder="Search officers by name or position..." value="<?= e($search ?? '') ?>">
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn btn-outline-primary btn-sm w-100">Search</button>
        </div>
        <div class="col-md-2">
            <a href="<?= url('officers') ?>" class="btn btn-outline-secondary btn-sm w-100">Clear</a>
        </div>
    </form>
</div>

<?php if (empty($officers)): ?>
    <div class="card table-card p-4 text-center text-muted">
        <i class="bi bi-person-badge" style="font-size:2rem"></i><br>
        No officers found
    </div>
<?php endif; ?>

<div class="row g-3"><?php foreach ($officers as $o): ?>
    <div class="col-lg-4 col-md-6">
        <div class="card table-card p-3">
            <div class="d-flex align-items-center">
                <?php if ($o['profile_photo']): ?>
                    <img src="/<?= e($o['profile_photo']) ?>" class="rounded-circle me-3" style="width:50px;height:50px;object-fit:cover">
                <?php else: ?>
                    <div class="rounded-circle bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-content-center me-3" style="width:50px;height:50px"><i class="bi bi-person"></i></div>
                <?php endif; ?>
                <div>
                    <div class="fw-bold"><?= e($o['first_name'] . ' ' . $o['last_name']) ?></div>
                    <div class="text-primary small fw-medium"><?= e($o['position_name']) ?></div>
                    <div class="text-muted small"><?= e($o['member_number'] ?? '') ?></div>
                </div>
            </div>
        </div>
    </div>
<?php endforeach; ?></div>
<?= render_pagination($pagination ?? null) ?>
<?php $content = ob_get_clean(); ?>
<?php require __DIR__ . '/../layouts/admin.php'; ?>
