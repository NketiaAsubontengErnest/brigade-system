<?php $pageTitle = 'Email Sent'; ob_start(); ?>
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
                A comprehensive platform for managing your brigade operations.
            </p>
        </div>
    </div>
    <div class="auth-form-side">
        <div class="auth-form-container">
            <div class="auth-form-header text-center">
                <div style="width:72px;height:72px;background:rgba(40,167,69,0.1);border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 20px;">
                    <i class="bi bi-envelope-check" style="font-size:2rem;color:#28a745;"></i>
                </div>
                <h2>Email Sent</h2>
                <p>If an account exists with that email address, you'll receive a password reset link shortly. Please check your inbox.</p>
            </div>
            <div class="text-center">
                <a href="<?= url('login') ?>" class="btn btn-auth text-white" style="display:inline-block;padding:12px 40px;text-decoration:none;">
                    <i class="bi bi-arrow-left me-2"></i>Back to Login
                </a>
            </div>
        </div>
    </div>
</div>
<?php $content = ob_get_clean(); ?>
<?php require __DIR__ . '/../layouts/auth.php'; ?>
