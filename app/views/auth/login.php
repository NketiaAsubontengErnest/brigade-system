<?php $layout = 'auth'; $pageTitle = 'Login'; ?>
<?php ob_start(); ?>
<div class="auth-wrapper">
    <!-- Branding Side -->
    <div class="auth-branding">
        <div class="brand-content">
            <?php $authLogo = brand_logo(); ?>
            <?php if ($authLogo !== ''): ?>
                <img src="<?= e($authLogo) ?>" alt="Company crest" class="brand-crest">
            <?php else: ?>
                <div class="brand-shield">
                    <i class="bi bi-shield-check"></i>
                </div>
            <?php endif; ?>
            <h1>21st & 24th Accra<br>Boys & Girls Brigade</h1>
            <div class="brand-subtitle">Management System</div>
            <div class="brand-divider"></div>
            <p class="brand-desc">
                A comprehensive platform for managing members, activities, finances, and operations of your brigade company.
            </p>
        </div>
    </div>

    <!-- Form Side -->
    <div class="auth-form-side">
        <div class="auth-form-container">
            <div class="auth-form-header">
                <h2>Welcome Back</h2>
                <p>Sign in to your account to continue</p>
            </div>

            <?php $flashes = (new App\Core\Session())->getFlashes(); ?>
            <?php foreach ($flashes as $type => $message): ?>
                <div class="alert alert-<?= $type ?> py-2 small mb-3" style="border-radius:8px;border-left:4px solid <?= $type === 'danger' ? '#dc3545' : ($type === 'success' ? '#28a745' : 'var(--auth-navy)') ?>;">
                    <?= e($message) ?>
                </div>
            <?php endforeach; ?>

            <form method="POST" action="<?= url('login') ?>" class="auth-form">
                <?= csrf_field() ?>
                <div class="mb-3">
                    <label class="form-label">Email Address</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                        <input type="email" name="email" class="form-control" placeholder="Enter your email" required autofocus>
                    </div>
                </div>
                <div class="mb-4">
                    <label class="form-label">Password</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-lock"></i></span>
                        <input type="password" name="password" class="form-control" placeholder="Enter your password" required>
                    </div>
                </div>
                <button type="submit" class="btn btn-auth w-100 text-white mb-3">
                    <i class="bi bi-box-arrow-in-right me-2"></i>Sign In
                </button>
                <div class="text-center">
                    <a href="<?= url('forgot-password') ?>" style="color:var(--auth-muted);text-decoration:none;font-size:0.875rem;">Forgot your password?</a>
                </div>
            </form>

            <div class="auth-footer">
                <a href="<?= url('') ?>"><i class="bi bi-arrow-left me-1"></i>Back to Website</a>
            </div>
        </div>
    </div>
</div>
<?php $content = ob_get_clean(); ?>
<?php require __DIR__ . '/../layouts/auth.php'; ?>
