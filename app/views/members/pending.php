<?php $pageTitle = 'Pending Members'; $layout = 'admin'; ob_start(); ?>
<div class="mb-4"><h4 class="fw-bold">Pending Member Approvals</h4><small class="text-muted">Review and approve new member registrations</small></div>
<div class="card table-card">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead><tr><th>Member</th><th>Gender</th><th>Section</th><th>Phone</th><th>Registered</th><th>Actions</th></tr></thead>
            <tbody>
                <?php if (empty($members)): ?>
                    <tr><td colspan="6" class="text-center text-muted py-4">No pending members</td></tr>
                <?php else: ?>
                    <?php foreach ($members as $m): ?>
                        <tr>
                            <td class="fw-medium"><?= e($m['first_name'] . ' ' . $m['last_name']) ?></td>
                            <td><?= e($m['gender']) ?></td>
                            <td><?= e($m['section_name'] ?? '') ?></td>
                            <td><?= e($m['phone']) ?></td>
                            <td class="text-muted small"><?= timeAgo($m['created_at']) ?></td>
                            <td>
                                <form method="POST" action="<?= url('/members/' . $m['id'] . '/approve') ?>" class="d-inline">
                                    <?= csrf_field() ?>
                                    <button type="submit" class="btn btn-sm btn-success"><i class="bi bi-check-lg me-1"></i>Approve</button>
                                </form>
                                <form method="POST" action="<?= url('/members/' . $m['id'] . '/reject') ?>" class="d-inline" onsubmit="return confirm('Reject this member?')">
                                    <?= csrf_field() ?>
                                    <button type="submit" class="btn btn-sm btn-outline-danger"><i class="bi bi-x-lg me-1"></i>Reject</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?php $content = ob_get_clean(); ?>
<?php require __DIR__ . '/../layouts/admin.php'; ?>
