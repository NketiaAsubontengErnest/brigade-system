<?php $pageTitle = 'Appoint Officer'; $layout = 'admin'; ob_start(); ?>
<div class="mb-4"><a href="<?= url('officers') ?>" class="text-muted text-decoration-none small"><i class="bi bi-arrow-left me-1"></i>Officers</a><h4 class="fw-bold">Appoint Officer</h4></div>
<div class="card table-card"><div class="card-body p-4">
    <form method="POST" action="<?= url('officers') ?>">
        <?= csrf_field() ?>
        <div class="row g-3">
            <div class="col-md-4"><label class="form-label small fw-medium">Member *</label><select name="member_id" class="form-select" required><option value="">Select Member</option><?php foreach ($members as $m): ?><option value="<?= $m['id'] ?>"><?= e($m['first_name'] . ' ' . $m['last_name'] . ' (' . $m['member_number'] . ')') ?></option><?php endforeach; ?></select></div>
            <div class="col-md-4"><label class="form-label small fw-medium">Position *</label><select name="position_id" class="form-select" required><option value="">Select Position</option><?php foreach ($positions as $p): ?><option value="<?= $p['id'] ?>"><?= e($p['name']) ?></option><?php endforeach; ?></select></div>
            <div class="col-md-4"><label class="form-label small fw-medium">Start Date</label><input type="date" name="start_date" class="form-control" value="<?= date('Y-m-d') ?>"></div>
        </div>
        <hr><div class="d-flex gap-2"><button type="submit" class="btn btn-primary">Appoint</button><a href="<?= url('officers') ?>" class="btn btn-outline-secondary">Cancel</a></div>
    </form>
</div></div>
<?php $content = ob_get_clean(); ?>
<?php require __DIR__ . '/../layouts/admin.php'; ?>
