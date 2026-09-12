<?php $pageTitle = 'Reset Password'; ob_start(); ?>
<div class="auth-wrapper">
    <div class="auth-branding">
        <div class="brand-content">
            <div class="brand-shield">
                <i class="bi bi-shield-check"></i>
            </div>
            <h1>21st & 24th Accra<br>Boys & Girls Brigade</h1>
            <div class="brand-subtitle">Management System</div>
            <div class="brand-divider"></div>
            <p class="brand-desc">
                Create a strong new password to secure your account.
            </p>
        </div>
    </div>
    <div class="auth-form-side">
        <div class="auth-form-container">
            <div class="auth-form-header">
                <h2>Reset Password</h2>
                <p>Enter your new password below for <?= htmlspecialchars($email ?? '') ?></p>
            </div>
            <form method="POST" action="<?= url('reset-password') ?>" class="auth-form">
                <?= csrf_field() ?>
                <input type="hidden" name="token" value="<?= htmlspecialchars($token ?? '') ?>">
                <input type="hidden" name="email" value="<?= htmlspecialchars($email ?? '') ?>">
                <div class="mb-3">
                    <label class="form-label">New Password</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-lock"></i></span>
                        <input type="password" name="password" class="form-control" placeholder="Enter new password" required minlength="8">
                    </div>
                    <div class="form-text mt-1">Must be at least 8 characters long.</div>
                </div>
                <div class="mb-4">
                    <label class="form-label">Confirm Password</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
                        <input type="password" name="password_confirmation" class="form-control" placeholder="Confirm new password" required>
                    </div>
                </div>
                <button type="submit" class="btn btn-auth w-100 text-white">
                    <i class="bi bi-check-circle me-2"></i>Reset Password
                </button>
            </form>
        </div>
    </div>
</div>
<?php $content = ob_get_clean(); ?>
<?php require __DIR__ . '/../layouts/auth.php'; ?>
