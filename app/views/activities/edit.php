<?php $pageTitle = 'Edit Activity'; $layout = 'admin'; ob_start(); ?>
<div class="mb-4"><a href="<?= url('activities/manage') ?>" class="text-muted text-decoration-none small"><i class="bi bi-arrow-left me-1"></i>Activities</a><h4 class="fw-bold">Edit Activity</h4></div>
<div class="card table-card"><div class="card-body p-4">
    <form method="POST" action="<?= url('activities/' . $activity['id']) ?>">
        <?= csrf_field() ?><input type="hidden" name="_method" value="PUT">
        <div class="row g-3">
            <div class="col-md-6"><label class="form-label small fw-medium">Name *</label><input type="text" name="name" class="form-control" value="<?= e($activity['name']) ?>" required></div>
            <div class="col-md-3"><label class="form-label small fw-medium">Date</label><input type="date" name="date" class="form-control" value="<?= e($activity['date']) ?>"></div>
            <div class="col-md-3"><label class="form-label small fw-medium">Status</label><select name="status" class="form-select"><?php foreach (['Scheduled', 'In Progress', 'Completed', 'Cancelled'] as $st): ?><option value="<?= $st ?>" <?= $activity['status'] === $st ? 'selected' : '' ?>><?= $st ?></option><?php endforeach; ?></select></div>
            <div class="col-md-3"><label class="form-label small fw-medium">Start Time</label><input type="time" name="start_time" class="form-control" value="<?= e($activity['start_time'] ?? '') ?>"></div>
            <div class="col-md-3"><label class="form-label small fw-medium">End Time</label><input type="time" name="end_time" class="form-control" value="<?= e($activity['end_time'] ?? '') ?>"></div>
            <div class="col-md-6"><label class="form-label small fw-medium">Location</label><input type="text" name="location" class="form-control" value="<?= e($activity['location'] ?? '') ?>"></div>
            <div class="col-12"><label class="form-label small fw-medium">Description</label><textarea name="description" class="form-control" rows="3"><?= e($activity['description'] ?? '') ?></textarea></div>
        </div>
        <hr><div class="d-flex gap-2"><button type="submit" class="btn btn-primary">Update</button><a href="<?= url('activities/' . $activity['id']) ?>" class="btn btn-outline-secondary">Cancel</a></div>
    </form>
</div></div>
<?php $content = ob_get_clean(); ?>
<?php require __DIR__ . '/../layouts/admin.php'; ?>
