<?php $pageTitle = 'Ward Profile - ' . htmlspecialchars($ward['first_name'] . ' ' . $ward['last_name']); ob_start(); ?>

<div class="mb-4">
    <a href="<?= url('guardian') ?>" class="btn btn-outline-secondary btn-sm mb-3">
        <i class="bi bi-arrow-left me-1"></i> Back to Parent Dashboard
    </a>

    <div class="card guardian-card">
        <div class="card-body p-4">
            <div class="d-flex align-items-center">
                <?php if (!empty($ward['profile_photo'])): ?>
                    <img src="<?= asset($ward['profile_photo']) ?>" class="rounded-circle me-4" width="80" height="80" alt="Photo">
                <?php else: ?>
                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-4 display-6 font-weight-bold" style="width: 80px; height: 80px;">
                        <?= strtoupper(substr($ward['first_name'], 0, 1) . substr($ward['last_name'], 0, 1)) ?>
                    </div>
                <?php endif; ?>
                <div>
                    <h3 class="fw-bold mb-1"><?= htmlspecialchars($ward['first_name'] . ' ' . ($ward['middle_name'] ? $ward['middle_name'] . ' ' : '') . $ward['last_name']) ?></h3>
                    <p class="text-muted mb-2">
                        <span class="badge bg-primary me-2"><?= htmlspecialchars($ward['section_name'] ?? 'Section') ?></span>
                        <span class="badge bg-secondary me-2"><?= htmlspecialchars($ward['rank'] ?? 'Member') ?></span>
                        <span class="badge bg-success"><?= htmlspecialchars($ward['status']) ?></span>
                    </p>
                    <small class="text-muted">Member Number: <code><?= htmlspecialchars($ward['member_number']) ?></code> | Joined: <?= date('d M Y', strtotime($ward['date_joined'])) ?></small>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Badges & Achievements -->
    <div class="col-lg-6 mb-4">
        <div class="card guardian-card h-100">
            <div class="card-header bg-white py-3">
                <h5 class="card-title mb-0"><i class="bi bi-award text-warning me-2"></i>Badges &amp; Achievements</h5>
            </div>
            <div class="card-body">
                <?php if (empty($badges)): ?>
                    <p class="text-muted text-center py-4 mb-0">No badges awarded to this member yet.</p>
                <?php else: ?>
                    <div class="row g-3">
                        <?php foreach ($badges as $b): ?>
                            <div class="col-md-6">
                                <div class="p-3 border rounded bg-light">
                                    <h6 class="fw-bold mb-1 text-primary"><i class="bi bi-patch-check-fill text-warning me-1"></i><?= htmlspecialchars($b['badge_name']) ?></h6>
                                    <small class="text-muted d-block mb-1">Category: <?= htmlspecialchars($b['category'] ?? 'General') ?></small>
                                    <small class="text-success"><i class="bi bi-calendar-check me-1"></i>Awarded: <?= date('d M Y', strtotime($b['date_awarded'])) ?></small>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Training Courses -->
    <div class="col-lg-6 mb-4">
        <div class="card guardian-card h-100">
            <div class="card-header bg-white py-3">
                <h5 class="card-title mb-0"><i class="bi bi-book text-info me-2"></i>Training Courses</h5>
            </div>
            <div class="card-body">
                <?php if (empty($training)): ?>
                    <p class="text-muted text-center py-4 mb-0">No training enrollments found.</p>
                <?php else: ?>
                    <ul class="list-group list-group-flush">
                        <?php foreach ($training as $t): ?>
                            <li class="list-group-item d-flex justify-content-between align-items-center py-3">
                                <div>
                                    <h6 class="mb-0 fw-bold"><?= htmlspecialchars($t['course_name']) ?></h6>
                                    <small class="text-muted">Code: <?= htmlspecialchars($t['code'] ?? 'TRN') ?></small>
                                </div>
                                <span class="badge bg-<?= $t['status'] === 'Completed' ? 'success' : 'info' ?>">
                                    <?= htmlspecialchars($t['status'] ?? 'Enrolled') ?>
                                </span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php $content = ob_get_clean(); ?>
<?php require __DIR__ . '/../layouts/guardian.php'; ?>
