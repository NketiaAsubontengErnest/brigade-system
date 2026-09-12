<?php $pageTitle = e($event['name']); $layout = 'admin'; ob_start(); ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <div><a href="<?= url('events/manage') ?>" class="text-muted text-decoration-none small"><i class="bi bi-arrow-left me-1"></i>Events</a><h4 class="fw-bold mb-0"><?= e($event['name']) ?></h4></div>
    <div>
        <a href="<?= url('events/' . $event['id'] . 'edit') ?>" class="btn btn-outline-primary btn-sm">Edit</a>
        <form method="POST" action="<?= url('/events/' . $event['id'] . '/delete') ?>" class="d-inline" onsubmit="return confirm('Cancel this event?')">
            <?= csrf_field() ?><button type="submit" class="btn btn-outline-danger btn-sm">Cancel</button>
        </form>
    </div>
</div>
<div class="row g-4">
    <div class="col-lg-5">
        <div class="card table-card p-4">
            <h6 class="fw-bold mb-3">Event Details</h6>
            <table class="table table-sm small mb-0">
                <tr><td class="text-muted">Start</td><td><?= formatDate($event['start_date']) ?> <?= e($event['start_time'] ?? '') ?></td></tr>
                <?php if ($event['end_date']): ?><tr><td class="text-muted">End</td><td><?= formatDate($event['end_date']) ?></td></tr><?php endif; ?>
                <tr><td class="text-muted">Location</td><td><?= e($event['location'] ?? 'N/A') ?></td></tr>
                <tr><td class="text-muted">Fee</td><td><?= formatCurrency((float)$event['fee']) ?></td></tr>
                <tr><td class="text-muted">Registered</td><td><?= $event['registered_count'] ?> / <?= $event['maximum_participants'] ?? 'Unlimited' ?></td></tr>
                <tr><td class="text-muted">Status</td><td><span class="badge bg-primary badge-status"><?= $event['status'] ?></span></td></tr>
            </table>
            <?php if ($event['description']): ?><p class="mt-3 small text-muted"><?= nl2br(e($event['description'])) ?></p><?php endif; ?>
        </div>
    </div>
    <div class="col-lg-7">
        <div class="card table-card">
            <div class="card-header bg-transparent d-flex justify-content-between align-items-center">
                <h6 class="fw-bold mb-0">Registrations</h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive"><table class="table table-hover mb-0">
                    <thead><tr><th>Member</th><th>Number</th><th>Status</th><th>Action</th></tr></thead>
                    <tbody><?php if (empty($registrations)): ?><tr><td colspan="4" class="text-center text-muted py-3">No registrations</td></tr>
                    <?php else: ?><?php foreach ($registrations as $r): ?>
                        <tr><td class="fw-medium"><?= e($r['first_name'] . ' ' . $r['last_name']) ?></td><td class="text-muted"><?= e($r['member_number'] ?? '') ?></td>
                        <td><span class="badge bg-<?= $r['status'] === 'Attended' ? 'success' : 'primary' ?> badge-status"><?= $r['status'] ?></span></td><td></td></tr>
                    <?php endforeach; ?><?php endif; ?></tbody>
                </table></div>
                <div class="p-3 border-top">
                    <form method="POST" action="<?= url('/events/' . $event['id'] . '/register') ?>" class="row g-2">
                        <?= csrf_field() ?>
                        <div class="col-md-8"><select name="member_id" class="form-select form-select-sm"><option value="">Select Member to Register</option><?php foreach ($members as $m): ?><option value="<?= $m['id'] ?>"><?= e($m['first_name'] . ' ' . $m['last_name'] . ' (' . $m['member_number'] . ')') ?></option><?php endforeach; ?></select></div>
                        <div class="col-md-4"><button type="submit" class="btn btn-primary btn-sm w-100">Register</button></div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $content = ob_get_clean(); ?>
<?php require __DIR__ . '/../layouts/admin.php'; ?>
