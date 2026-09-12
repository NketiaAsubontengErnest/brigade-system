<?php $pageTitle = 'My Profile'; $layout = 'admin'; ob_start(); ?>
<div class="mb-4"><h4 class="fw-bold">My Profile</h4></div>
<div class="row g-4">
    <div class="col-lg-4">
        <div class="card table-card p-4 text-center">
            <?php if ($member['profile_photo']): ?><img src="/<?= e($member['profile_photo']) ?>" class="rounded-circle mb-3" style="width:120px;height:120px;object-fit:cover"><?php else: ?><div class="rounded-circle bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-content-center mx-auto mb-3" style="width:120px;height:120px"><i class="bi bi-person" style="font-size:3rem"></i></div><?php endif; ?>
            <h5 class="fw-bold"><?= e($member['first_name'] . ' ' . $member['last_name']) ?></h5>
            <p class="text-muted"><?= e($member['member_number'] ?? 'N/A') ?></p>
            <span class="badge bg-<?= $member['status'] === 'Active' ? 'success' : 'secondary' ?> mb-3"><?= $member['status'] ?></span>
        </div>
    </div>
    <div class="col-lg-8">
        <div class="card table-card p-4">
            <table class="table table-sm">
                <tr><td class="text-muted" style="width:180px">Section</td><td><?= e($member['section_name'] ?? 'N/A') ?></td></tr>
                <tr><td class="text-muted">Gender</td><td><?= e($member['gender'] ?? '') ?></td></tr>
                <tr><td class="text-muted">Date of Birth</td><td><?= e($member['date_of_birth'] ? formatDate($member['date_of_birth']) : 'N/A') ?></td></tr>
                <tr><td class="text-muted">Phone</td><td><?= e($member['phone'] ?? 'N/A') ?></td></tr>
                <tr><td class="text-muted">Email</td><td><?= e($member['email'] ?? 'N/A') ?></td></tr>
                <tr><td class="text-muted">Address</td><td><?= e($member['address'] ?? 'N/A') ?></td></tr>
                <tr><td class="text-muted">Date Joined</td><td><?= formatDate($member['date_joined']) ?></td></tr>
                <tr><td class="text-muted">Rank</td><td><?= e($member['rank'] ?? 'N/A') ?></td></tr>
            </table>
        </div>
        <?php if (!empty($guardians)): ?>
        <div class="card table-card p-4 mt-3">
            <h6 class="fw-bold mb-3">Guardian Information</h6>
            <?php foreach ($guardians as $g): ?>
                <div class="mb-2"><strong><?= e($g['full_name']) ?></strong><br><small class="text-muted"><?= e($g['relationship'] ?? '') ?> · <?= e($g['phone'] ?? '') ?></small></div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
</div>
<?php $content = ob_get_clean(); ?>
<?php require __DIR__ . '/../layouts/admin.php'; ?>
