<?php $pageTitle = 'Manage Ranks'; $layout = 'admin'; ob_start(); ?>

<div class="mb-4">
    <a href="<?= url('settings') ?>" class="text-muted text-decoration-none small"><i class="bi bi-arrow-left me-1"></i>Settings</a>
    <h4 class="fw-bold">Manage Ranks</h4>
    <small class="text-muted">Add and manage officer ranks that can be assigned to members.</small>
</div>

<!-- Add New Rank -->
<div class="card table-card mb-4">
    <div class="card-header bg-transparent border-0 pt-3">
        <h6 class="fw-bold mb-0">Add New Rank</h6>
    </div>
    <div class="card-body">
        <form method="POST" action="<?= url('settings/ranks') ?>">
            <?= csrf_field() ?>
            <div class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label small fw-medium">Rank Name *</label>
                    <input type="text" name="name" class="form-control" placeholder="e.g. Sergeant" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label small fw-medium">Description</label>
                    <input type="text" name="description" class="form-control" placeholder="Brief description">
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-medium">Level</label>
                    <input type="number" name="level" class="form-control" value="1" min="1">
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary w-100"><i class="bi bi-plus-lg me-1"></i>Add Rank</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Ranks List -->
<div class="card table-card">
    <div class="card-header bg-transparent border-0 pt-3">
        <h6 class="fw-bold mb-0">All Ranks</h6>
    </div>
    <div class="card-body">
        <?php if (empty($ranks)): ?>
            <div class="text-center text-muted py-4">No ranks added yet.</div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Level</th>
                            <th>Rank Name</th>
                            <th>Description</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($ranks as $rank): ?>
                            <tr>
                                <td><span class="badge bg-secondary"><?= $rank['level'] ?></span></td>
                                <td class="fw-medium"><?= e($rank['name']) ?></td>
                                <td class="text-muted"><?= e($rank['description'] ?? '') ?></td>
                                <td><span class="badge bg-<?= $rank['status'] === 'Active' ? 'success' : 'secondary' ?> badge-status"><?= $rank['status'] ?></span></td>
                                <td>
                                    <div class="d-flex gap-1">
                                        <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editRank<?= $rank['id'] ?>">Edit</button>
                                        <form method="POST" action="<?= url('settings/ranks/' . $rank['id'] . '/delete') ?>" class="d-inline" onsubmit="return confirm('Delete this rank?')">
                                            <?= csrf_field() ?>
                                            <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>

                            <!-- Edit Modal -->
                            <div class="modal fade" id="editRank<?= $rank['id'] ?>" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <form method="POST" action="<?= url('settings/ranks/' . $rank['id']) ?>">
                                            <?= csrf_field() ?>
                                            <div class="modal-header">
                                                <h6 class="modal-title fw-bold">Edit Rank</h6>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="mb-3">
                                                    <label class="form-label small fw-medium">Rank Name *</label>
                                                    <input type="text" name="name" class="form-control" value="<?= e($rank['name']) ?>" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label small fw-medium">Description</label>
                                                    <input type="text" name="description" class="form-control" value="<?= e($rank['description'] ?? '') ?>">
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label small fw-medium">Level</label>
                                                    <input type="number" name="level" class="form-control" value="<?= $rank['level'] ?>" min="1">
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label small fw-medium">Status</label>
                                                    <select name="status" class="form-select">
                                                        <option value="Active" <?= $rank['status'] === 'Active' ? 'selected' : '' ?>>Active</option>
                                                        <option value="Inactive" <?= $rank['status'] === 'Inactive' ? 'selected' : '' ?>>Inactive</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                                                <button type="submit" class="btn btn-primary">Save Changes</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php $content = ob_get_clean(); ?>
<?php require __DIR__ . '/../layouts/admin.php'; ?>
