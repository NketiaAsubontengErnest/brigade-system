<?php $pageTitle = 'Events & Notices'; ob_start(); ?>

<div class="mb-4">
    <h3 class="fw-bold mb-1"><i class="bi bi-calendar-event text-danger me-2"></i>Events &amp; Notices</h3>
    <p class="text-muted">Upcoming company events, camps, and announcements</p>
</div>

<div class="row g-3">
    <?php if (empty($events)): ?>
        <div class="col-12 text-center py-5 text-muted">
            <i class="bi bi-calendar-x display-4 d-block mb-2"></i>
            <p>No active events scheduled at this time.</p>
        </div>
    <?php else: ?>
        <?php foreach ($events as $e): ?>
            <div class="col-md-6">
                <div class="card guardian-card h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <h5 class="card-title fw-bold text-primary mb-0"><?= htmlspecialchars($e['title']) ?></h5>
                            <span class="badge bg-<?= $e['status'] === 'Upcoming' ? 'info' : 'secondary' ?>"><?= htmlspecialchars($e['status']) ?></span>
                        </div>
                        <p class="text-muted small mb-3"><?= htmlspecialchars($e['description'] ?? 'No description available.') ?></p>
                        <div class="border-top pt-2 small text-secondary">
                            <div class="mb-1"><i class="bi bi-calendar-check me-2"></i>Date: <strong><?= date('d M Y', strtotime($e['start_date'])) ?></strong></div>
                            <?php if (!empty($e['location'])): ?>
                                <div class="mb-1"><i class="bi bi-geo-alt me-2"></i>Location: <?= htmlspecialchars($e['location']) ?></div>
                            <?php endif; ?>
                            <?php if (!empty($e['fee'])): ?>
                                <div class="mb-1 text-success font-weight-bold"><i class="bi bi-tag me-2"></i>Event Fee: GHS <?= number_format((float)$e['fee'], 2) ?></div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<?php $content = ob_get_clean(); ?>
<?php require __DIR__ . '/../layouts/guardian.php'; ?>
