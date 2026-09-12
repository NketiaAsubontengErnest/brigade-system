<?php $pageTitle = 'Forgot Password'; ob_start(); ?>
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
                Don't worry, we'll help you get back into your account.
            </p>
        </div>
    </div>
    <div class="auth-form-side">
        <div class="auth-form-container">
            <div class="auth-form-header">
                <h2>Forgot Password?</h2>
                <p>Enter your email address and we'll send you a link to reset your password.</p>
            </div>
            <form method="POST" action="<?= url('forgot-password') ?>" class="auth-form">
                <?= csrf_field() ?>
                <div class="mb-4">
                    <label class="form-label">Email Address</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                        <input type="email" name="email" class="form-control" placeholder="Enter your registered email" required>
                    </div>
                </div>
                <button type="submit" class="btn btn-auth w-100 text-white mb-3">
                    <i class="bi bi-send me-2"></i>Send Reset Link
                </button>
            </form>
            <div class="auth-footer">
                <a href="<?= url('login') ?>"><i class="bi bi-arrow-left me-1"></i>Back to Login</a>
            </div>
        </div>
    </div>
</div>
<?php $content = ob_get_clean(); ?>
<?php require __DIR__ . '/../layouts/auth.php'; ?>
