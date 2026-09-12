<?php $pageTitle = 'Edit Member'; $layout = 'admin'; ob_start(); ?>
<div class="mb-4"><a href="<?= url('members') ?>" class="text-muted text-decoration-none small"><i class="bi bi-arrow-left me-1"></i>Members</a><h4 class="fw-bold">Edit Member</h4></div>
<div class="card table-card">
    <div class="card-body p-4">
        <form method="POST" action="<?= url('members/' . $member['id']) ?>" enctype="multipart/form-data">
            <?= csrf_field() ?>
            <input type="hidden" name="_method" value="PUT">
            <div class="row g-3">
                <div class="col-md-4"><label class="form-label small fw-medium">First Name *</label><input type="text" name="first_name" class="form-control" value="<?= e($member['first_name']) ?>" required></div>
                <div class="col-md-4"><label class="form-label small fw-medium">Middle Name</label><input type="text" name="middle_name" class="form-control" value="<?= e($member['middle_name'] ?? '') ?>"></div>
                <div class="col-md-4"><label class="form-label small fw-medium">Last Name *</label><input type="text" name="last_name" class="form-control" value="<?= e($member['last_name']) ?>" required></div>
                <div class="col-md-3"><label class="form-label small fw-medium">Date of Birth</label><input type="date" name="date_of_birth" class="form-control" value="<?= e($member['date_of_birth'] ?? '') ?>"></div>
                <div class="col-md-3"><label class="form-label small fw-medium">Gender *</label><select name="gender" class="form-select" required><option value="">Select...</option><option value="Male" <?= ($member['gender'] ?? '') === 'Male' ? 'selected' : '' ?>>Male</option><option value="Female" <?= ($member['gender'] ?? '') === 'Female' ? 'selected' : '' ?>>Female</option></select></div>
                <div class="col-md-3"><label class="form-label small fw-medium">Phone</label><input type="tel" name="phone" class="form-control" value="<?= e($member['phone'] ?? '') ?>"></div>
                <div class="col-md-3"><label class="form-label small fw-medium">Email</label><input type="email" name="email" class="form-control" value="<?= e($member['email'] ?? '') ?>"></div>
                <div class="col-md-4"><label class="form-label small fw-medium">Section</label><select name="section_id" class="form-select"><option value="">Select...</option><?php foreach ($sections as $s): ?><option value="<?= $s['id'] ?>" <?= ($member['section_id'] ?? '') == $s['id'] ? 'selected' : '' ?>><?= e($s['name']) ?></option><?php endforeach; ?></select></div>
                <div class="col-md-4">
                    <label class="form-label small fw-medium">Rank</label>
                    <select name="rank_id" class="form-select">
                        <option value="">No rank</option>
                        <?php foreach ($ranks as $r): ?>
                            <option value="<?= $r['id'] ?>" <?= (string)($member['rank_id'] ?? '') === (string)$r['id'] ? 'selected' : '' ?>><?= e($r['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <?php if (auth()->hasPermission('ranks.manage')): ?>
                        <div class="form-text"><a href="<?= url('settings/ranks') ?>" class="text-decoration-none">Manage ranks</a></div>
                    <?php endif; ?>
                </div>
                <div class="col-md-4"><label class="form-label small fw-medium">Status</label><select name="status" class="form-select"><?php foreach (['Active', 'Inactive', 'Suspended', 'Former', 'Pending'] as $st): ?><option value="<?= $st ?>" <?= ($member['status'] ?? '') === $st ? 'selected' : '' ?>><?= $st ?></option><?php endforeach; ?></select></div>
                <div class="col-md-6"><label class="form-label small fw-medium">Address</label><textarea name="address" class="form-control" rows="2"><?= e($member['address'] ?? '') ?></textarea></div>
                <div class="col-md-6"><label class="form-label small fw-medium">Notes</label><textarea name="notes" class="form-control" rows="2"><?= e($member['notes'] ?? '') ?></textarea></div>
                <div class="col-md-4"><label class="form-label small fw-medium">Profile Photo</label><input type="file" name="profile_photo" class="form-control" accept="image/*"></div>

                <!-- Officer assignment -->
                <div class="col-12 mt-3">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="isOfficer" name="is_officer" value="1" <?= !empty($member['is_officer']) ? 'checked' : '' ?>>
                        <label class="form-check-label fw-bold" for="isOfficer">This member is an Officer</label>
                    </div>
                </div>
                <div class="col-12" id="officerFields" style="display: <?= !empty($member['is_officer']) ? 'block' : 'none' ?>;">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label small fw-medium">Position</label>
                            <select name="position_id" class="form-select">
                                <option value="">Select Position</option>
                                <?php foreach ($positions as $p): ?>
                                    <option value="<?= $p['id'] ?>" <?= (string)($member['position_id'] ?? '') === (string)$p['id'] ? 'selected' : '' ?>><?= e($p['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>

                <?php
                $currentSectionId = $member['section_id'] ?? null;
                require __DIR__ . '/_guardians.php';
                ?>
            </div>
            <hr><div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>Update Member</button>
                <a href="<?= url('members/' . $member['id']) ?>" class="btn btn-outline-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>

<script>
document.getElementById('isOfficer').addEventListener('change', function () {
    document.getElementById('officerFields').style.display = this.checked ? 'block' : 'none';
});
</script>
<?php $content = ob_get_clean(); ?>
<?php require __DIR__ . '/../layouts/admin.php'; ?>
