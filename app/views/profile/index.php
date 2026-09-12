<?php $pageTitle = 'Manage Profile'; $layout = 'admin'; ob_start(); ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-0">My Profile</h4>
        <small class="text-muted">Manage your personal account details and password</small>
    </div>
</div>

<div class="row">
    <!-- User Information Card -->
    <div class="col-lg-4 mb-4">
        <div class="card shadow-sm border-0 text-center p-4">
            <div class="user-avatar-lg mx-auto mb-3 bg-primary text-white rounded-circle d-flex align-items-center justify-content-center fw-bold display-6" style="width: 90px; height: 90px;">
                <?= strtoupper(substr($user['full_name'], 0, 1)) ?>
            </div>
            <h5 class="fw-bold mb-1"><?= htmlspecialchars($user['full_name']) ?></h5>
            <p class="text-muted small mb-2"><?= htmlspecialchars($user['email']) ?></p>
            <div>
                <span class="badge bg-primary px-3 py-2"><?= htmlspecialchars($user['role_name']) ?></span>
            </div>
            <hr class="my-4">
            <div class="text-start small text-muted">
                <div class="mb-2"><i class="bi bi-telephone me-2"></i>Phone: <strong><?= htmlspecialchars($user['phone'] ?? 'Not provided') ?></strong></div>
                <div class="mb-2"><i class="bi bi-clock-history me-2"></i>Last Login: <strong><?= !empty($user['last_login']) ? date('d M Y, h:i A', strtotime($user['last_login'])) : 'N/A' ?></strong></div>
                <div><i class="bi bi-calendar-check me-2"></i>Account Created: <strong><?= date('d M Y', strtotime($user['created_at'])) ?></strong></div>
            </div>
        </div>
    </div>

    <!-- Edit Profile & Change Password Forms -->
    <div class="col-lg-8 mb-4">
        <!-- Edit Personal Details -->
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white py-3">
                <h5 class="card-title mb-0"><i class="bi bi-person-gear text-primary me-2"></i>Edit Profile Information</h5>
            </div>
            <div class="card-body p-4">
                <form method="POST" action="<?= url('profile') ?>">
                    <?= csrf_field() ?>
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label font-weight-bold">Full Name</label>
                            <input type="text" name="full_name" class="form-control" value="<?= htmlspecialchars($user['full_name']) ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label font-weight-bold">Email Address</label>
                            <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($user['email']) ?>" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label font-weight-bold">Phone Number</label>
                        <input type="text" name="phone" class="form-control" value="<?= htmlspecialchars($user['phone'] ?? '') ?>" placeholder="e.g. 0240000000">
                    </div>
                    <div class="text-end">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle me-1"></i> Save Profile Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Change Password -->
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white py-3">
                <h5 class="card-title mb-0"><i class="bi bi-shield-lock text-warning me-2"></i>Change Password</h5>
            </div>
            <div class="card-body p-4">
                <form method="POST" action="<?= url('profile/password') ?>">
                    <?= csrf_field() ?>
                    <div class="mb-3">
                        <label class="form-label font-weight-bold">Current Password</label>
                        <input type="password" name="current_password" class="form-control" required>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label font-weight-bold">New Password</label>
                            <input type="password" name="password" class="form-control" required minlength="8">
                            <div class="form-text">Must be at least 8 characters.</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label font-weight-bold">Confirm New Password</label>
                            <input type="password" name="password_confirmation" class="form-control" required>
                        </div>
                    </div>
                    <div class="text-end">
                        <button type="submit" class="btn btn-warning text-dark">
                            <i class="bi bi-key me-1"></i> Update Password
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php $content = ob_get_clean(); ?>
<?php require __DIR__ . '/../layouts/admin.php'; ?>
