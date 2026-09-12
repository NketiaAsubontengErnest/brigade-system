<?php $pageTitle = 'Members'; $layout = 'admin'; ob_start(); ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div><h4 class="fw-bold mb-0">Members</h4><small class="text-muted"><?= number_format($total) ?> total members</small></div>
    <div class="d-flex gap-2">
        <a href="<?= url('members/import') ?>" class="btn btn-outline-success btn-sm"><i class="bi bi-file-earmark-spreadsheet me-1"></i>Import CSV</a>
        <a href="<?= url('members/create') ?>" class="btn btn-primary btn-sm"><i class="bi bi-plus-lg me-1"></i>Add Member</a>
    </div>
</div>

<!-- Filters -->
<div class="card table-card p-3 mb-4">
    <form method="GET" class="row g-2 align-items-end">
        <div class="col-md-4">
            <input type="text" name="search" class="form-control form-control-sm" placeholder="Search name, number, phone..." value="<?= e($search) ?>">
        </div>
        <div class="col-md-2">
            <select name="section" class="form-select form-select-sm">
                <option value="">All Sections</option>
                <?php foreach ($sections as $s): ?>
                    <option value="<?= $s['id'] ?>" <?= $filterSection == $s['id'] ? 'selected' : '' ?>><?= e($s['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-2">
            <select name="status" class="form-select form-select-sm">
                <option value="">All Status</option>
                <?php foreach (['Active', 'Inactive', 'Pending', 'Former', 'Suspended'] as $st): ?>
                    <option value="<?= $st ?>" <?= $filterStatus === $st ? 'selected' : '' ?>><?= $st ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-2"><button type="submit" class="btn btn-outline-primary btn-sm w-100"><i class="bi bi-search me-1"></i>Filter</button></div>
        <div class="col-md-2"><a href="<?= url('members') ?>" class="btn btn-outline-secondary btn-sm w-100">Clear</a></div>
    </form>
</div>

<!-- Members Table -->
<div class="card table-card">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead>
                <tr><th>Member</th><th>Number</th><th>Section</th><th>Gender</th><th>Phone</th><th>Status</th><th>Actions</th></tr>
            </thead>
            <tbody>
                <?php if (empty($members)): ?>
                    <tr><td colspan="7" class="text-center text-muted py-4">No members found</td></tr>
                <?php else: ?>
                    <?php foreach ($members as $m): ?>
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <?php if ($m['profile_photo']): ?>
                                        <img src="/<?= e($m['profile_photo']) ?>" class="rounded-circle me-2" style="width:36px;height:36px;object-fit:cover">
                                    <?php else: ?>
                                        <div class="rounded-circle bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-content-center me-2" style="width:36px;height:36px"><i class="bi bi-person"></i></div>
                                    <?php endif; ?>
                                    <a href="<?= url('members/' . $m['id']) ?>" class="text-decoration-none fw-medium"><?= e($m['first_name'] . ' ' . $m['last_name']) ?></a>
                                </div>
                            </td>
                            <td class="text-muted small"><?= e($m['member_number'] ?? 'N/A') ?></td>
                            <td><?= e($m['section_name'] ?? 'N/A') ?></td>
                            <td><i class="bi bi-gender-<?= strtolower($m['gender'] ?? '') ?>"></i> <?= e($m['gender'] ?? '') ?></td>
                            <td class="text-muted"><?= e($m['phone'] ?? '') ?></td>
                            <td><span class="badge bg-<?= $m['status'] === 'Active' ? 'success' : ($m['status'] === 'Pending' ? 'warning' : ($m['status'] === 'Inactive' ? 'secondary' : 'danger')) ?> badge-status"><?= e($m['status']) ?></span></td>
                            <td>
                                <div class="d-flex gap-1">
                                    <a href="<?= url('members/' . $m['id']) ?>" class="btn btn-sm btn-outline-primary py-0" title="View"><i class="bi bi-eye"></i></a>
                                    <a href="<?= url('members/' . $m['id'] . '/edit') ?>" class="btn btn-sm btn-outline-secondary py-0" title="Edit"><i class="bi bi-pencil"></i></a>
                                    <a href="<?= url('members/' . $m['id'] . '/card') ?>" class="btn btn-sm btn-outline-info py-0" title="Generate ID Card" target="_blank"><i class="bi bi-card-heading"></i></a>
                                    <?php if (auth()->hasAnyPermission(['members.delete'])): ?>
                                        <form method="POST" action="<?= url('members/' . $m['id'] . '/delete') ?>" class="d-inline" onsubmit="return confirm('Permanently delete this member? This cannot be undone.')">
                                            <?= csrf_field() ?>
                                            <button type="submit" class="btn btn-sm btn-outline-danger py-0" title="Delete"><i class="bi bi-trash"></i></button>
                                        </form>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <?= render_pagination($pagination) ?>
</div>

<?php $content = ob_get_clean(); ?>
<?php require __DIR__ . '/../layouts/admin.php'; ?>
