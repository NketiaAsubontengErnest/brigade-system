<?php $pageTitle = e($badge['name']); $layout = 'admin'; ob_start(); ?>
<div class="mb-4"><a href="<?= url('badges') ?>" class="text-muted text-decoration-none small"><i class="bi bi-arrow-left me-1"></i>Badges</a><h4 class="fw-bold mb-0"><i class="bi bi-award-fill text-warning me-2"></i><?= e($badge['name']) ?></h4><small class="text-muted"><?= e($badge['description'] ?? '') ?></small></div>
<div class="row g-4">
    <div class="col-lg-6">
        <div class="card table-card p-4"><h6 class="fw-bold mb-2">Requirements</h6><p class="text-muted"><?= e($badge['requirements'] ?? 'None specified') ?></p></div>
        <div class="card table-card mt-3 p-4">
            <h6 class="fw-bold mb-3">Award Badge</h6>
            <form method="POST" action="<?= url('/badges/' . $badge['id'] . '/award') ?>">
                <?= csrf_field() ?>
                <div class="row g-2">
                    <div class="col-md-8"><select name="member_id" class="form-select form-select-sm"><option value="">Select Member</option><?php foreach ($allMembers as $m): ?><option value="<?= $m['id'] ?>"><?= e($m['first_name'] . ' ' . $m['last_name'] . ' (' . ($m['member_number'] ?? '') . ')') ?></option><?php endforeach; ?></select></div>
                    <div class="col-md-4"><button type="submit" class="btn btn-warning btn-sm w-100"><i class="bi bi-award me-1"></i>Award</button></div>
                </div>
                <input type="text" name="notes" class="form-control form-control-sm mt-2" placeholder="Notes (optional)">
            </form>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card table-card">
            <div class="card-header bg-transparent"><h6 class="fw-bold mb-0">Awarded to</h6></div>
            <div class="card-body p-0"><table class="table table-sm mb-0">
                <thead><tr><th>Member</th><th>Date</th><th>Awarded By</th></tr></thead>
                <tbody><?php foreach ($members as $m): ?>
                    <tr><td class="fw-medium"><?= e($m['first_name'] . ' ' . $m['last_name']) ?></td><td class="text-muted"><?= formatDate($m['date_awarded']) ?></td><td><?= e($m['awarded_by'] ?? '') ?></td></tr>
                <?php endforeach; ?></tbody>
            </table></div>
        </div>
    </div>
</div>
<?php $content = ob_get_clean(); ?>
<?php require __DIR__ . '/../layouts/admin.php'; ?>
