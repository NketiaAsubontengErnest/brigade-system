<?php $pageTitle = 'Join Us'; $layout = 'public'; ob_start(); ?>
<section class="py-5 bg-light"><div class="container">
    <div class="row justify-content-center"><div class="col-lg-8">
        <div class="card border-0 shadow-sm p-4">
            <h3 class="fw-bold text-center mb-4">Member Registration</h3>
            <form method="POST" action="<?= url('register') ?>">
                <?= csrf_field() ?>
                <h5 class="fw-bold mb-3 mt-4"><i class="bi bi-person-plus me-2"></i>Personal Information</h5>
                <div class="row g-3">
                    <div class="col-md-4"><label class="form-label small fw-medium">First Name *</label><input type="text" name="first_name" class="form-control" value="<?= e(old('first_name')) ?>" required></div>
                    <div class="col-md-4"><label class="form-label small fw-medium">Middle Name</label><input type="text" name="middle_name" class="form-control" value="<?= e(old('middle_name')) ?>"></div>
                    <div class="col-md-4"><label class="form-label small fw-medium">Last Name *</label><input type="text" name="last_name" class="form-control" value="<?= e(old('last_name')) ?>" required></div>
                    <div class="col-md-4"><label class="form-label small fw-medium">Date of Birth *</label><input type="date" name="date_of_birth" class="form-control" value="<?= e(old('date_of_birth')) ?>" required></div>
                    <div class="col-md-4"><label class="form-label small fw-medium">Gender *</label><select name="gender" class="form-select" required><option value="">Select...</option><option value="Male" <?= old('gender') === 'Male' ? 'selected' : '' ?>>Male</option><option value="Female" <?= old('gender') === 'Female' ? 'selected' : '' ?>>Female</option></select></div>
                    <div class="col-md-4"><label class="form-label small fw-medium">Section *</label><select name="section_id" class="form-select" required><option value="">Select Section</option><?php foreach ($sections as $s): ?><option value="<?= $s['id'] ?>" <?= old('section_id') == $s['id'] ? 'selected' : '' ?>><?= e($s['name']) ?></option><?php endforeach; ?></select></div>
                    <div class="col-md-6"><label class="form-label small fw-medium">Phone *</label><input type="tel" name="phone" class="form-control" value="<?= e(old('phone')) ?>" required></div>
                    <div class="col-md-6"><label class="form-label small fw-medium">Email</label><input type="email" name="email" class="form-control" value="<?= e(old('email')) ?>"></div>
                    <div class="col-12"><label class="form-label small fw-medium">Address</label><textarea name="address" class="form-control" rows="2"><?= e(old('address')) ?></textarea></div>
                </div>
                <h5 class="fw-bold mb-3 mt-4"><i class="bi bi-people me-2"></i>Guardian / Parent Information</h5>
                <div class="row g-3">
                    <div class="col-md-6"><label class="form-label small fw-medium">Guardian Name *</label><input type="text" name="guardian_name" class="form-control" value="<?= e(old('guardian_name')) ?>" required></div>
                    <div class="col-md-3"><label class="form-label small fw-medium">Relationship</label><input type="text" name="guardian_relationship" class="form-control" placeholder="e.g. Father" value="<?= e(old('guardian_relationship')) ?>"></div>
                    <div class="col-md-3"><label class="form-label small fw-medium">Phone *</label><input type="tel" name="guardian_phone" class="form-control" value="<?= e(old('guardian_phone')) ?>" required></div>
                    <div class="col-md-6"><label class="form-label small fw-medium">Email</label><input type="email" name="guardian_email" class="form-control" value="<?= e(old('guardian_email')) ?>"></div>
                </div>
                <hr>
                <button type="submit" class="btn btn-primary btn-lg w-100"><i class="bi bi-check-lg me-1"></i>Submit Registration</button>
                <p class="text-muted small text-center mt-3">After registration, your application will be reviewed by an officer.</p>
            </form>
        </div>
    </div></div>
</div></section>
<?php $content = ob_get_clean(); ?>
<?php require __DIR__ . '/../layouts/public.php'; ?>
