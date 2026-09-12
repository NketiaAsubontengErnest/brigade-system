<?php $pageTitle = e($officer['position_name']); $layout = 'admin'; ob_start(); ?>
<div class="mb-4"><a href="<?= url('officers') ?>" class="text-muted text-decoration-none small"><i class="bi bi-arrow-left me-1"></i>Officers</a><h4 class="fw-bold mb-0"><?= e($officer['position_name']) ?></h4></div>
<div class="row g-4">
    <div class="col-lg-4"><div class="card table-card p-4 text-center">
        <?php if ($officer['profile_photo']): ?><img src="/<?= e($officer['profile_photo']) ?>" class="rounded-circle mb-3" style="width:100px;height:100px;object-fit:cover"><?php else: ?><div class="rounded-circle bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-content-center mx-auto mb-3" style="width:100px;height:100px"><i class="bi bi-person" style="font-size:2.5rem"></i></div><?php endif; ?>
        <h5 class="fw-bold"><?= e($officer['first_name'] . ' ' . $officer['last_name']) ?></h5>
        <p class="text-muted"><?= e($officer['member_number'] ?? '') ?></p>
        <span class="badge bg-<?= $officer['status'] === 'Active' ? 'success' : 'secondary' ?>"><?= $officer['status'] ?></span>
        <table class="table table-sm text-start small mt-3"><tr><td class="text-muted">Phone</td><td><?= e($officer['phone'] ?? 'N/A') ?></td></tr><tr><td class="text-muted">Email</td><td><?= e($officer['email'] ?? 'N/A') ?></td></tr><tr><td class="text-muted">Start Date</td><td><?= formatDate($officer['start_date']) ?></td></tr></table>
    </div></div>
</div>
<?php $content = ob_get_clean(); ?>
<?php require __DIR__ . '/../layouts/admin.php'; ?>
