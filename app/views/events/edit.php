<?php $pageTitle = 'Edit Event'; $layout = 'admin'; ob_start(); ?>
<div class="mb-4"><a href="<?= url('events/manage') ?>" class="text-muted text-decoration-none small"><i class="bi bi-arrow-left me-1"></i>Events</a><h4 class="fw-bold">Edit Event</h4></div>
<div class="card table-card"><div class="card-body p-4">
    <form method="POST" action="<?= url('events/' . $event['id']) ?>">
        <?= csrf_field() ?><input type="hidden" name="_method" value="PUT">
        <div class="row g-3">
            <div class="col-md-6"><label class="form-label small fw-medium">Name *</label><input type="text" name="name" class="form-control" value="<?= e($event['name']) ?>" required></div>
            <div class="col-md-3"><label class="form-label small fw-medium">Status</label><select name="status" class="form-select"><?php foreach (['Upcoming', 'Ongoing', 'Completed', 'Cancelled'] as $s): ?><option value="<?= $s ?>" <?= $event['status'] === $s ? 'selected' : '' ?>><?= $s ?></option><?php endforeach; ?></select></div>
            <div class="col-md-3"><label class="form-label small fw-medium">Fee</label><input type="number" name="fee" class="form-control" step="0.01" value="<?= $event['fee'] ?>"></div>
            <div class="col-md-3"><label class="form-label small fw-medium">Start Date</label><input type="date" name="start_date" class="form-control" value="<?= e($event['start_date']) ?>"></div>
            <div class="col-md-3"><label class="form-label small fw-medium">End Date</label><input type="date" name="end_date" class="form-control" value="<?= e($event['end_date'] ?? '') ?>"></div>
            <div class="col-md-3"><label class="form-label small fw-medium">Location</label><input type="text" name="location" class="form-control" value="<?= e($event['location'] ?? '') ?>"></div>
            <div class="col-md-3"><label class="form-label small fw-medium">Max Participants</label><input type="number" name="maximum_participants" class="form-control" value="<?= $event['maximum_participants'] ?? '' ?>"></div>
            <div class="col-12"><label class="form-label small fw-medium">Description</label><textarea name="description" class="form-control" rows="4"><?= e($event['description'] ?? '') ?></textarea></div>
        </div>
        <hr><div class="d-flex gap-2"><button type="submit" class="btn btn-primary">Update</button><a href="<?= url('events/' . $event['id']) ?>" class="btn btn-outline-secondary">Cancel</a></div>
    </form>
</div></div>
<?php $content = ob_get_clean(); ?>
<?php require __DIR__ . '/../layouts/admin.php'; ?>
