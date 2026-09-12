<?php $pageTitle = e($course['name']); $layout = 'admin'; ob_start(); ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <div><a href="<?= url('training') ?>" class="text-muted text-decoration-none small"><i class="bi bi-arrow-left me-1"></i>Training</a><h4 class="fw-bold mb-0"><?= e($course['name']) ?></h4><small class="text-muted"><?= e($course['duration'] ?? '') ?> · <?= e($course['instructor'] ?? '') ?></small></div>
    <a href="<?= url('training/' . $course['id'] . 'edit') ?>" class="btn btn-outline-primary btn-sm">Edit</a>
</div>
<div class="row g-4">
    <div class="col-lg-4"><div class="card table-card p-4">
        <h6 class="fw-bold mb-3">Course Details</h6>
        <table class="table table-sm small mb-0">
            <tr><td class="text-muted">Status</td><td><span class="badge bg-primary badge-status"><?= $course['status'] ?></span></td></tr>
            <tr><td class="text-muted">Duration</td><td><?= e($course['duration'] ?? 'N/A') ?></td></tr>
            <tr><td class="text-muted">Instructor</td><td><?= e($course['instructor'] ?? 'N/A') ?></td></tr>
            <tr><td class="text-muted">Description</td><td><?= e($course['description'] ?? '') ?></td></tr>
        </table>
    </div></div>
    <div class="col-lg-8">
        <div class="card table-card">
            <div class="card-header bg-transparent d-flex justify-content-between"><h6 class="fw-bold mb-0">Enrollments</h6></div>
            <div class="card-body p-0"><table class="table table-hover mb-0">
                <thead><tr><th>Member</th><th>Status</th><th>Score</th><th>Action</th></tr></thead>
                <tbody><?php foreach ($enrollments as $e): ?>
                    <tr><td class="fw-medium"><?= e($e['first_name'] . ' ' . $e['last_name']) ?></td>
                    <td><span class="badge bg-<?= $e['status'] === 'Completed' ? 'success' : ($e['status'] === 'In Progress' ? 'primary' : ($e['status'] === 'Failed' ? 'danger' : 'secondary')) ?> badge-status"><?= $e['status'] ?></span></td>
                    <td><?= $e['score'] ?? 'N/A' ?></td>
                    <td>
                        <form method="POST" action="<?= url('training/enrollment/' . $e['id']) ?>" class="d-inline">
                            <?= csrf_field() ?>
                            <input type="hidden" name="status" value="Completed">
                            <button type="submit" class="btn btn-sm btn-outline-success">Complete</button>
                        </form>
                    </td></tr>
                <?php endforeach; ?></tbody>
            </table></div>
            <div class="card-footer bg-transparent">
                <form method="POST" action="<?= url('/training/' . $course['id'] . '/enroll') ?>" class="row g-2">
                    <?= csrf_field() ?>
                    <div class="col-md-8"><select name="member_id" class="form-select form-select-sm"><option value="">Select Member</option><?php foreach ($members as $m): ?><option value="<?= $m['id'] ?>"><?= e($m['first_name'] . ' ' . $m['last_name']) ?></option><?php endforeach; ?></select></div>
                    <div class="col-md-4"><button type="submit" class="btn btn-primary btn-sm w-100">Enroll</button></div>
                </form>
            </div>
        </div>
    </div>
</div>
<?php $content = ob_get_clean(); ?>
<?php require __DIR__ . '/../layouts/admin.php'; ?>
