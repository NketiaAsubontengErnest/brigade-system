<?php $pageTitle = 'Guardian Dashboard'; ob_start(); ?>

<!-- Header Welcome -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card bg-primary text-white shadow-sm border-0">
            <div class="card-body p-4 d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="h4 mb-1">Welcome, <?= htmlspecialchars(auth()->user()['full_name'] ?? 'Guardian') ?></h2>
                    <p class="mb-0 text-white-50">Manage your children's brigade attendance, badges, training, and dues payments.</p>
                </div>
                <div class="display-6 d-none d-md-block opacity-50">
                    <i class="bi bi-people-fill"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Linked Wards Cards -->
<div class="mb-4">
    <h5 class="fw-bold mb-3"><i class="bi bi-people me-2 text-primary"></i>My Linked Children (Wards)</h5>
    <?php if (empty($wards)): ?>
        <div class="card shadow-sm border-0 text-center py-4">
            <div class="card-body text-muted">
                <i class="bi bi-person-x display-4 d-block mb-2"></i>
                <p class="mb-1">No children linked to your account yet.</p>
                <small>Please contact the Company Captain or Secretary to associate your member records with your email address (<?= htmlspecialchars(auth()->user()['email'] ?? '') ?>).</small>
            </div>
        </div>
    <?php else: ?>
        <div class="row g-3">
            <?php foreach ($wards as $w): ?>
                <div class="col-md-6 col-lg-4">
                    <div class="card guardian-card h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-3">
                                <?php if (!empty($w['profile_photo'])): ?>
                                    <img src="<?= asset($w['profile_photo']) ?>" class="rounded-circle me-3" width="50" height="50" alt="Photo">
                                <?php else: ?>
                                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3 font-weight-bold" style="width: 50px; height: 50px; font-size: 1.2rem;">
                                        <?= strtoupper(substr($w['first_name'], 0, 1) . substr($w['last_name'], 0, 1)) ?>
                                    </div>
                                <?php endif; ?>
                                <div>
                                    <h6 class="mb-0 fw-bold"><?= htmlspecialchars($w['first_name'] . ' ' . $w['last_name']) ?></h6>
                                    <small class="text-muted"><?= htmlspecialchars($w['section_name'] ?? 'Section') ?> | <?= htmlspecialchars($w['rank'] ?? 'Member') ?></small>
                                </div>
                            </div>
                            <div class="border-top pt-2 mt-2 small">
                                <div class="d-flex justify-content-between py-1">
                                    <span class="text-muted">Member #:</span>
                                    <code><?= htmlspecialchars($w['member_number']) ?></code>
                                </div>
                                <div class="d-flex justify-content-between py-1">
                                    <span class="text-muted">Relationship:</span>
                                    <span class="badge bg-secondary"><?= htmlspecialchars($w['relationship'] ?? 'Guardian') ?></span>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer bg-white border-top-0 pt-0 text-end">
                            <a href="<?= url('guardian/ward/' . $w['id']) ?>" class="btn btn-outline-primary btn-sm w-100">
                                <i class="bi bi-person-badge me-1"></i> View Full Ward Profile
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<!-- Dues & Attendance Summary Row -->
<div class="row">
    <div class="col-lg-6 mb-4">
        <div class="card guardian-card h-100">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-bold"><i class="bi bi-cash-stack text-success me-2"></i>Dues Summary</h6>
                <a href="<?= url('guardian/dues') ?>" class="btn btn-sm btn-link text-decoration-none">View All</a>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-4 border-end">
                        <small class="text-muted d-block mb-1">Total Dues</small>
                        <h5 class="fw-bold mb-0">GHS <?= number_format($totalDues, 2) ?></h5>
                    </div>
                    <div class="col-4 border-end">
                        <small class="text-muted d-block mb-1">Total Paid</small>
                        <h5 class="fw-bold text-success mb-0">GHS <?= number_format($totalPaid, 2) ?></h5>
                    </div>
                    <div class="col-4">
                        <small class="text-muted d-block mb-1">Outstanding</small>
                        <h5 class="fw-bold text-danger mb-0">GHS <?= number_format(max(0, $balance), 2) ?></h5>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-6 mb-4">
        <div class="card guardian-card h-100">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-bold"><i class="bi bi-clipboard-check text-primary me-2"></i>Recent Attendance Log</h6>
                <a href="<?= url('guardian/attendance') ?>" class="btn btn-sm btn-link text-decoration-none">View Log</a>
            </div>
            <div class="card-body p-0">
                <?php if (empty($recentAttendance)): ?>
                    <p class="text-muted text-center py-4 mb-0 small">No attendance records found.</p>
                <?php else: ?>
                    <ul class="list-group list-group-flush small">
                        <?php foreach (array_slice($recentAttendance, 0, 5) as $att): ?>
                            <li class="list-group-item d-flex justify-content-between align-items-center py-2">
                                <div>
                                    <strong><?= htmlspecialchars($att['first_name'] . ' ' . $att['last_name']) ?></strong>
                                    <span class="text-muted ms-2"><?= htmlspecialchars($att['activity_name'] ?? 'Parade') ?></span>
                                </div>
                                <div>
                                    <span class="badge bg-<?= $att['status'] === 'Present' ? 'success' : ($att['status'] === 'Late' ? 'warning' : 'danger') ?>">
                                        <?= htmlspecialchars($att['status']) ?>
                                    </span>
                                    <small class="text-muted ms-2"><?= date('d M Y', strtotime($att['date'])) ?></small>
                                </div>
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
