<?php $pageTitle = 'Profile Not Found'; $layout = 'admin'; ob_start(); ?>
<div class="text-center py-5">
    <i class="bi bi-person-x text-muted" style="font-size:4rem"></i>
    <h4 class="fw-bold mt-3">No Member Profile Found</h4>
    <p class="text-muted">Your account is not linked to a member profile yet.</p>
    <p class="text-muted">Please contact an administrator to link your account to your member record.</p>
    <a href="<?= url('dashboard') ?>" class="btn btn-primary mt-3">Go to Dashboard</a>
</div>
<?php $content = ob_get_clean(); ?>
<?php require __DIR__ . '/../layouts/admin.php'; ?>
