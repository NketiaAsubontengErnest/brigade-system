<?php $pageTitle = 'Register Member'; $layout = 'admin'; ob_start(); ?>
<div class="mb-4">
    <a href="<?= url('members') ?>" class="text-muted text-decoration-none small"><i class="bi bi-arrow-left me-1"></i>Members</a>
    <h4 class="fw-bold">Register New Member</h4>
</div>

<div class="card table-card">
    <div class="card-body p-4">
        <form method="POST" action="<?= url('members') ?>" enctype="multipart/form-data">
            <?= csrf_field() ?>
            <div class="row g-3">
                <!-- Personal Info -->
                <div class="col-12"><h6 class="fw-bold text-muted border-bottom pb-2">Personal Information</h6></div>
                <div class="col-md-4">
                    <label class="form-label small fw-medium">First Name *</label>
                    <input type="text" name="first_name" class="form-control" value="<?= e(old('first_name')) ?>" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label small fw-medium">Middle Name</label>
                    <input type="text" name="middle_name" class="form-control" value="<?= e(old('middle_name')) ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label small fw-medium">Last Name *</label>
                    <input type="text" name="last_name" class="form-control" value="<?= e(old('last_name')) ?>" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-medium">Date of Birth</label>
                    <input type="date" name="date_of_birth" class="form-control" value="<?= e(old('date_of_birth')) ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-medium">Gender *</label>
                    <select name="gender" class="form-select" required>
                        <option value="">Select...</option>
                        <option value="Male" <?= old('gender') === 'Male' ? 'selected' : '' ?>>Male</option>
                        <option value="Female" <?= old('gender') === 'Female' ? 'selected' : '' ?>>Female</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-medium">Phone</label>
                    <input type="tel" name="phone" class="form-control" value="<?= e(old('phone')) ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-medium">Email</label>
                    <input type="email" name="email" class="form-control" value="<?= e(old('email')) ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label small fw-medium">Address</label>
                    <textarea name="address" class="form-control" rows="2"><?= e(old('address')) ?></textarea>
                </div>

                <!-- Section & Assignment -->
                <div class="col-12 mt-3"><h6 class="fw-bold text-muted border-bottom pb-2">Section & Assignment</h6></div>
                <div class="col-md-4">
                    <label class="form-label small fw-medium">Section *</label>
                    <select name="section_id" class="form-select" required>
                        <option value="">Select Section</option>
                        <?php foreach ($sections as $s): ?>
                            <option value="<?= $s['id'] ?>" <?= old('section_id') == $s['id'] ? 'selected' : '' ?>><?= e($s['name']) ?> (<?= e($s['type'] ?? 'Section') ?>)</option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-medium">Rank</label>
                    <select name="rank_id" class="form-select">
                        <option value="">No rank</option>
                        <?php foreach ($ranks as $r): ?>
                            <option value="<?= $r['id'] ?>" <?= old('rank_id') == $r['id'] ? 'selected' : '' ?>><?= e($r['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <?php if (auth()->hasPermission('ranks.manage')): ?>
                        <div class="form-text">
                            <a href="<?= url('settings/ranks') ?>" class="text-decoration-none">Manage ranks</a>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-medium">Date Joined</label>
                    <input type="date" name="date_joined" class="form-control" value="<?= e(old('date_joined', date('Y-m-d'))) ?>">
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-medium">Profile Photo</label>
                    <input type="file" name="profile_photo" class="form-control" accept="image/*">
                </div>

                <?php require __DIR__ . '/_guardians.php'; ?>

                <!-- Officer Info -->
                <div class="col-12 mt-3">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="isOfficer" name="is_officer" value="1" <?= old('is_officer') ? 'checked' : '' ?>>
                        <label class="form-check-label fw-bold" for="isOfficer">This member is an Officer</label>
                    </div>
                </div>
                <div class="col-12" id="officerFields" style="display: <?= old('is_officer') ? 'block' : 'none' ?>;">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label small fw-medium">Position</label>
                            <select name="position_id" class="form-select">
                                <option value="">Select Position</option>
                                <?php foreach ($positions as $p): ?>
                                    <option value="<?= $p['id'] ?>" <?= old('position_id') == $p['id'] ? 'selected' : '' ?>><?= e($p['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="col-12">
                    <label class="form-label small fw-medium">Notes</label>
                    <textarea name="notes" class="form-control" rows="2"><?= e(old('notes')) ?></textarea>
                </div>
            </div>
            <hr>
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>Register Member</button>
                <a href="<?= url('members') ?>" class="btn btn-outline-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>

<script>
document.getElementById('isOfficer').addEventListener('change', function() {
    document.getElementById('officerFields').style.display = this.checked ? 'block' : 'none';
});
</script>

<?php $content = ob_get_clean(); ?>
<?php require __DIR__ . '/../layouts/admin.php'; ?>
