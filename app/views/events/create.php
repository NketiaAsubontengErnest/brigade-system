<?php $pageTitle = 'Create Event'; $layout = 'admin'; ob_start(); ?>
<div class="mb-4"><a href="<?= url('events/manage') ?>" class="text-muted text-decoration-none small"><i class="bi bi-arrow-left me-1"></i>Events</a><h4 class="fw-bold">Create Event</h4></div>
<div class="card table-card"><div class="card-body p-4">
    <form method="POST" action="<?= url('events') ?>" enctype="multipart/form-data">
        <?= csrf_field() ?>
        <div class="row g-3">
            <div class="col-md-6"><label class="form-label small fw-medium">Name *</label><input type="text" name="name" class="form-control" required></div>
            <div class="col-md-3"><label class="form-label small fw-medium">Start Date *</label><input type="date" name="start_date" class="form-control" required></div>
            <div class="col-md-3"><label class="form-label small fw-medium">End Date</label><input type="date" name="end_date" class="form-control"></div>
            <div class="col-md-3"><label class="form-label small fw-medium">Start Time</label><input type="time" name="start_time" class="form-control"></div>
            <div class="col-md-3"><label class="form-label small fw-medium">End Time</label><input type="time" name="end_time" class="form-control"></div>
            <div class="col-md-3"><label class="form-label small fw-medium">Location</label><input type="text" name="location" class="form-control"></div>
            <div class="col-md-3"><label class="form-label small fw-medium">Fee</label><input type="number" name="fee" class="form-control" step="0.01" value="0"></div>
            <div class="col-md-3"><label class="form-label small fw-medium">Max Participants</label><input type="number" name="maximum_participants" class="form-control"></div>
            <div class="col-md-3"><label class="form-label small fw-medium">Registration Deadline</label><input type="date" name="registration_deadline" class="form-control"></div>
            <div class="col-md-3"><label class="form-label small fw-medium">Cover Image</label><input type="file" name="cover_image" class="form-control" accept="image/*"></div>
            <div class="col-12"><label class="form-label small fw-medium">Description</label><textarea name="description" class="form-control" rows="4"></textarea></div>
        </div>
        <hr><div class="d-flex gap-2"><button type="submit" class="btn btn-primary">Create Event</button><a href="<?= url('events/manage') ?>" class="btn btn-outline-secondary">Cancel</a></div>
    </form>
</div></div>
<?php $content = ob_get_clean(); ?>
<?php require __DIR__ . '/../layouts/admin.php'; ?>
