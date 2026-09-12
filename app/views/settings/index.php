<?php $pageTitle = 'Settings'; $layout = 'admin'; ob_start(); ?>
<div class="mb-4"><h4 class="fw-bold">Settings</h4></div>
<ul class="nav nav-tabs mb-4">
    <li class="nav-item"><a class="nav-link active" href="#company" data-bs-toggle="tab">Company Profile</a></li>
    <li class="nav-item"><a class="nav-link" href="<?= url('settings/ranks') ?>">Ranks</a></li>
    <li class="nav-item"><a class="nav-link" href="<?= url('settings/audit-logs') ?>">Audit Logs</a></li>
    <li class="nav-item"><a class="nav-link" href="<?= url('settings/notifications') ?>">Notifications</a></li>
</ul>
<div class="tab-content">
    <div class="tab-pane fade show active" id="company">
        <div class="card table-card p-4">
            <form method="POST" action="<?= url('settings/company') ?>">
                <?= csrf_field() ?>
                <div class="row g-3">
                    <div class="col-md-6"><label class="form-label small fw-medium">Company Name</label><input type="text" name="company_name" class="form-control" value="<?= e($profile['company_name'] ?? '') ?>"></div>
                    <div class="col-md-6"><label class="form-label small fw-medium">Church Name</label><input type="text" name="church_name" class="form-control" value="<?= e($profile['church_name'] ?? '') ?>"></div>
                    <div class="col-md-4"><label class="form-label small fw-medium">Company Number</label><input type="text" name="company_number" class="form-control" value="<?= e($profile['company_number'] ?? '') ?>"></div>
                    <div class="col-md-4"><label class="form-label small fw-medium">Motto</label><input type="text" name="motto" class="form-control" value="<?= e($profile['motto'] ?? '') ?>"></div>
                    <div class="col-md-4"><label class="form-label small fw-medium">Founded Date</label><input type="date" name="founded_date" class="form-control" value="<?= e($profile['founded_date'] ?? '') ?>"></div>
                    <div class="col-md-6"><label class="form-label small fw-medium">Address</label><textarea name="address" class="form-control" rows="2"><?= e($profile['address'] ?? '') ?></textarea></div>
                    <div class="col-md-6"><label class="form-label small fw-medium">Location</label><input type="text" name="location" class="form-control" value="<?= e($profile['location'] ?? '') ?>"></div>
                    <div class="col-md-4"><label class="form-label small fw-medium">Phone</label><input type="text" name="phone" class="form-control" value="<?= e($profile['phone'] ?? '') ?>"></div>
                    <div class="col-md-4"><label class="form-label small fw-medium">Email</label><input type="email" name="email" class="form-control" value="<?= e($profile['email'] ?? '') ?>"></div>
                    <div class="col-md-4"><label class="form-label small fw-medium">Website</label><input type="url" name="website" class="form-control" value="<?= e($profile['website'] ?? '') ?>"></div>
                    <div class="col-md-6"><label class="form-label small fw-medium">Member Number Prefix</label><input type="text" name="member_number_prefix" class="form-control" value="<?= e($profile['member_number_prefix'] ?? 'BGB') ?>"></div>
                    <div class="col-md-6"><label class="form-label small fw-medium">Currency Symbol</label><input type="text" name="currency_symbol" class="form-control" value="<?= e($profile['currency_symbol'] ?? 'GH₵') ?>"></div>
                    <div class="col-12"><label class="form-label small fw-medium">Mission</label><textarea name="mission" class="form-control" rows="3"><?= e($profile['mission'] ?? '') ?></textarea></div>
                    <div class="col-12"><label class="form-label small fw-medium">Vision</label><textarea name="vision" class="form-control" rows="3"><?= e($profile['vision'] ?? '') ?></textarea></div>
                    <div class="col-12"><label class="form-label small fw-medium">Description</label><textarea name="description" class="form-control" rows="3"><?= e($profile['description'] ?? '') ?></textarea></div>
                </div>
                <hr><button type="submit" class="btn btn-primary">Save Changes</button>
            </form>
        </div>
        <div class="card table-card p-4 mt-4">
            <h6 class="fw-bold mb-3">Upload Logo</h6>
            <form method="POST" action="<?= url('settings/logo') ?>" enctype="multipart/form-data">
                <?= csrf_field() ?>
                <div class="row g-2 align-items-end">
                    <div class="col-md-6"><input type="file" name="logo" class="form-control form-control-sm" accept="image/*"></div>
                    <div class="col-md-2"><button type="submit" class="btn btn-outline-primary btn-sm w-100">Upload</button></div>
                </div>
            </form>
            <?php if (!empty($profile['logo'])): ?><img src="/<?= e($profile['logo']) ?>" class="mt-2" style="max-height:80px"><?php endif; ?>
        </div>
    </div>
</div>
<?php $content = ob_get_clean(); ?>
<?php require __DIR__ . '/../layouts/admin.php'; ?>
