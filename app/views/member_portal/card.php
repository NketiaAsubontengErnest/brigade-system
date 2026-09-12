<?php $pageTitle = 'Membership Card'; $layout = 'admin'; ob_start(); ?>
<div class="mb-4"><h4 class="fw-bold">Digital Membership Card</h4></div>
<div class="row justify-content-center"><div class="col-lg-5">
    <div class="card border-0 shadow-lg" style="border-radius:20px;overflow:hidden">
        <div style="background:linear-gradient(135deg,#2c3e50,#3498db);padding:30px 25px 15px;color:white;text-align:center">
            <?php if (!empty($profile['logo'])): ?><img src="/<?= e($profile['logo']) ?>" style="max-height:50px;margin-bottom:10px" alt="Logo"><?php endif; ?>
            <h5 class="fw-bold mb-1"><?= e($profile['company_name'] ?? 'Brigade') ?></h5>
            <small class="opacity-75"><?= e($profile['motto'] ?? '') ?></small>
        </div>
        <div class="card-body text-center p-4">
            <?php if ($member['profile_photo']): ?>
                <img src="/<?= e($member['profile_photo']) ?>" class="rounded-circle mb-3" style="width:100px;height:100px;object-fit:cover;border:3px solid #3498db">
            <?php else: ?>
                <div class="rounded-circle bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-content-center mx-auto mb-3" style="width:100px;height:100px;border:3px solid #3498db"><i class="bi bi-person" style="font-size:2.5rem"></i></div>
            <?php endif; ?>
            <h5 class="fw-bold"><?= e($member['first_name'] . ' ' . $member['last_name']) ?></h5>
            <p class="text-muted mb-2"><?= e($member['member_number'] ?? 'Pending') ?></p>
            <span class="badge bg-<?= $member['status'] === 'Active' ? 'success' : 'warning' ?> mb-2"><?= $member['status'] ?></span>
            <div class="small text-muted mb-3">
                <div>Section: <strong><?= e($member['section_name'] ?? 'N/A') ?></strong></div>
            </div>
            <?php $verifyToken = $member['verification_token'] ?? ''; ?>
            <?php if ($verifyToken): ?>
                <div class="mt-3 p-3 bg-light rounded">
                    <p class="small mb-1 text-muted">Scan to verify</p>
                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=120x120&data=<?= urlencode(url('/verify/member/' . $verifyToken)) ?>" alt="QR Code" style="width:120px;height:120px">
                </div>
            <?php endif; ?>
        </div>
    </div>
</div></div>
<?php $content = ob_get_clean(); ?>
<?php require __DIR__ . '/../layouts/admin.php'; ?>
