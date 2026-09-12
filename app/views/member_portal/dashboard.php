<?php $pageTitle = 'My Dashboard'; $layout = 'admin'; ob_start(); ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <div><h4 class="fw-bold mb-0">Welcome, <?= e($member['first_name']) ?>!</h4><small class="text-muted"><?= e($member['member_number'] ?? 'N/A') ?> · <?= e($member['section_name'] ?? '') ?></small></div>
    <a href="<?= url('portal/card') ?>" class="btn btn-outline-primary btn-sm"><i class="bi bi-credit-card me-1"></i>My Card</a>
</div>
<div class="row g-3 mb-4">
    <div class="col-md-4 col-sm-6"><div class="card stat-card p-3"><div class="stat-value text-success"><?= number_format($stats['total_attendance']) ?></div><div class="stat-label">Sessions Attended</div></div></div>
    <div class="col-md-4 col-sm-6"><div class="card stat-card p-3"><div class="stat-value"><?= formatCurrency($stats['total_paid']) ?></div><div class="stat-label">Total Paid</div></div></div>
    <div class="col-md-4 col-sm-6"><div class="card stat-card p-3"><div class="stat-value text-danger"><?= formatCurrency($stats['balance']) ?></div><div class="stat-label">Balance Due</div></div></div>
</div>
<div class="row g-3 mb-4">
    <div class="col-md-4 col-sm-6"><div class="card stat-card p-3"><div class="stat-value text-warning"><?= number_format($stats['badges']) ?></div><div class="stat-label">Badges</div></div></div>
    <div class="col-md-4 col-sm-6"><div class="card stat-card p-3"><div class="stat-value text-info"><?= number_format($stats['events']) ?></div><div class="stat-label">Events Joined</div></div></div>
</div>
<div class="row g-3">
    <div class="col-lg-6">
        <div class="card table-card"><div class="card-header bg-transparent"><h6 class="fw-bold mb-0">Announcements</h6></div><div class="list-group list-group-flush">
            <?php foreach ($announcements as $a): ?>
                <div class="list-group-item"><div class="fw-medium small"><?= e($a['title']) ?></div><small class="text-muted"><?= timeAgo($a['created_at']) ?></small></div>
            <?php endforeach; ?>
            <?php if (empty($announcements)): ?><div class="list-group-item text-muted small">No announcements</div><?php endif; ?>
        </div></div>
    </div>
    <div class="col-lg-6">
        <div class="card table-card"><div class="card-header bg-transparent"><h6 class="fw-bold mb-0">Upcoming Events</h6></div><div class="list-group list-group-flush">
            <?php foreach ($upcomingEvents as $e): ?>
                <div class="list-group-item"><div class="fw-medium small"><?= e($e['name']) ?></div><small class="text-muted"><?= formatDate($e['start_date']) ?></small></div>
            <?php endforeach; ?>
            <?php if (empty($upcomingEvents)): ?><div class="list-group-item text-muted small">No upcoming events</div><?php endif; ?>
        </div></div>
    </div>
</div>
<?php $content = ob_get_clean(); ?>
<?php require __DIR__ . '/../layouts/admin.php'; ?>
