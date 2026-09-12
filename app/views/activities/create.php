<?php $pageTitle = 'Create Activity'; $layout = 'admin'; ob_start(); ?>
<div class="mb-4"><a href="<?= url('activities/manage') ?>" class="text-muted text-decoration-none small"><i class="bi bi-arrow-left me-1"></i>Activities</a><h4 class="fw-bold">Create Activity</h4></div>
<div class="card table-card"><div class="card-body p-4">
    <form method="POST" action="<?= url('activities') ?>">
        <?= csrf_field() ?>
        <div class="row g-3">
            <div class="col-md-6"><label class="form-label small fw-medium">Name *</label><input type="text" name="name" class="form-control" required></div>
            <div class="col-md-3"><label class="form-label small fw-medium">Date *</label><input type="date" name="date" class="form-control" value="<?= date('Y-m-d') ?>" required></div>
            <div class="col-md-3"><label class="form-label small fw-medium">Section</label><select name="section_id" class="form-select"><option value="">All Sections</option><?php foreach ($sections as $s): ?><option value="<?= $s['id'] ?>"><?= e($s['name']) ?></option><?php endforeach; ?></select></div>
            <div class="col-md-3"><label class="form-label small fw-medium">Start Time</label><input type="time" name="start_time" class="form-control"></div>
            <div class="col-md-3"><label class="form-label small fw-medium">End Time</label><input type="time" name="end_time" class="form-control"></div>
            <div class="col-md-6"><label class="form-label small fw-medium">Location</label><input type="text" name="location" class="form-control"></div>
            <div class="col-12"><label class="form-label small fw-medium">Description</label><textarea name="description" class="form-control" rows="3"></textarea></div>
        </div>
        <hr><div class="d-flex gap-2"><button type="submit" class="btn btn-primary">Create</button><a href="<?= url('activities/manage') ?>" class="btn btn-outline-secondary">Cancel</a></div>
    </form>
</div></div>
<?php $content = ob_get_clean(); ?>
<?php require __DIR__ . '/../layouts/admin.php'; ?>
