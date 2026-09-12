<?php $pageTitle = 'Give Award'; $layout = 'admin'; ob_start(); ?>
<div class="mb-4"><a href="<?= url('awards') ?>" class="text-muted text-decoration-none small"><i class="bi bi-arrow-left me-1"></i>Awards</a><h4 class="fw-bold">Give Award</h4></div>
<div class="card table-card"><div class="card-body p-4">
    <form method="POST" action="<?= url('awards') ?>">
        <?= csrf_field() ?>
        <div class="row g-3">
            <div class="col-md-6"><label class="form-label small fw-medium">Member *</label><select name="member_id" class="form-select" required><option value="">Select Member</option><?php foreach ($members as $m): ?><option value="<?= $m['id'] ?>"><?= e($m['first_name'] . ' ' . $m['last_name']) ?></option><?php endforeach; ?></select></div>
            <div class="col-md-6"><label class="form-label small fw-medium">Award Name *</label><input type="text" name="name" class="form-control" required></div>
            <div class="col-md-4"><label class="form-label small fw-medium">Date Awarded</label><input type="date" name="date_awarded" class="form-control" value="<?= date('Y-m-d') ?>"></div>
            <div class="col-md-4"><label class="form-label small fw-medium">Awarded By</label><input type="text" name="awarded_by" class="form-control" value="<?= e(auth()->user()['full_name'] ?? '') ?>"></div>
            <div class="col-12"><label class="form-label small fw-medium">Description</label><textarea name="description" class="form-control" rows="2"></textarea></div>
            <div class="col-12"><label class="form-label small fw-medium">Notes</label><textarea name="notes" class="form-control" rows="2"></textarea></div>
        </div>
        <hr><div class="d-flex gap-2"><button type="submit" class="btn btn-primary">Give Award</button><a href="<?= url('awards') ?>" class="btn btn-outline-secondary">Cancel</a></div>
    </form>
</div></div>
<?php $content = ob_get_clean(); ?>
<?php require __DIR__ . '/../layouts/admin.php'; ?>
