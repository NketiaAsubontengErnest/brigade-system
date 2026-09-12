<?php $pageTitle = e($member['first_name'] . ' ' . $member['last_name']); $layout = 'admin'; ob_start(); ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <a href="<?= url('members') ?>" class="text-muted text-decoration-none small"><i class="bi bi-arrow-left me-1"></i>Members</a>
        <h4 class="fw-bold mb-0 mt-1"><?= e($member['first_name'] . ' ' . ($member['middle_name'] ?? '') . ' ' . $member['last_name']) ?></h4>
        <small class="text-muted"><?= e($member['member_number'] ?? 'Not yet assigned') ?> · <?= e($member['section_name'] ?? 'No section') ?></small>
    </div>
    <div>
        <a href="<?= url('members/' . $member['id'] . '/edit') ?>" class="btn btn-outline-primary btn-sm"><i class="bi bi-pencil me-1"></i>Edit</a>
        <a href="<?= url('members/' . $member['id'] . '/card') ?>" class="btn btn-outline-success btn-sm" target="_blank"><i class="bi bi-credit-card me-1"></i>Card</a>
    </div>
</div>

<div class="row g-4">
    <!-- Profile Card -->
    <div class="col-lg-4">
        <div class="card table-card text-center p-4">
            <?php if ($member['profile_photo']): ?>
                <img src="/<?= e($member['profile_photo']) ?>" class="rounded-circle mx-auto mb-3" style="width:120px;height:120px;object-fit:cover">
            <?php else: ?>
                <div class="rounded-circle bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-content-center mx-auto mb-3" style="width:120px;height:120px"><i class="bi bi-person" style="font-size:3rem"></i></div>
            <?php endif; ?>
            <h5 class="fw-bold"><?= e($member['first_name'] . ' ' . $member['last_name']) ?></h5>
            <p class="text-muted mb-2"><?= e($member['member_number'] ?? 'N/A') ?></p>
            <span class="badge bg-<?= $member['status'] === 'Active' ? 'success' : ($member['status'] === 'Pending' ? 'warning' : 'secondary') ?> mb-3"><?= e($member['status']) ?></span>
            <table class="table table-sm text-start small">
                <tr><td class="text-muted">Gender</td><td><?= e($member['gender'] ?? 'N/A') ?></td></tr>
                <tr><td class="text-muted">DOB</td><td><?= e($member['date_of_birth'] ? formatDate($member['date_of_birth']) : 'N/A') ?></td></tr>
                <tr><td class="text-muted">Phone</td><td><?= e($member['phone'] ?? 'N/A') ?></td></tr>
                <tr><td class="text-muted">Email</td><td><?= e($member['email'] ?? 'N/A') ?></td></tr>
                <tr><td class="text-muted">Joined</td><td><?= formatDate($member['date_joined']) ?></td></tr>
                <tr><td class="text-muted">Section</td><td><?= e($member['section_name'] ?? 'N/A') ?></td></tr>
                <tr><td class="text-muted">Rank</td><td><?= e($member['rank_name'] ?? $member['rank'] ?? '') ?: 'N/A' ?></td></tr>
            </table>
        </div>

        <!-- Guardians -->
        <div class="card table-card p-4 mt-3">
            <h6 class="fw-bold mb-3">Guardians</h6>
            <?php if (empty($guardians)): ?>
                <p class="text-muted small">No guardians on file</p>
            <?php else: ?>
                <?php foreach ($guardians as $g): ?>
                    <div class="mb-2 pb-2 border-bottom">
                        <div class="fw-medium small"><?= e($g['full_name']) ?></div>
                        <div class="text-muted small"><?= e($g['relationship'] ?? '') ?> · <?= e($g['phone'] ?? '') ?></div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <!-- Details -->
    <div class="col-lg-8">
        <ul class="nav nav-tabs mb-3" id="memberTabs">
            <li class="nav-item"><button class="nav-link active" data-bs-toggle="tab" data-bs-target="#dues">Dues & Payments</button></li>
            <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#attendance">Attendance</button></li>
            <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#badges">Badges & Awards</button></li>
        </ul>
        <div class="tab-content">
            <div class="tab-pane fade show active" id="dues">
                <div class="card table-card">
                    <div class="table-responsive">
                        <table class="table table-sm mb-0">
                            <thead><tr><th>Dues</th><th>Due</th><th>Paid</th><th>Balance</th><th>Status</th></tr></thead>
                            <tbody>
                                <?php if (empty($dues)): ?>
                                    <tr><td colspan="5" class="text-center text-muted py-3">No dues records</td></tr>
                                <?php else: ?>
                                    <?php foreach ($dues as $d): ?>
                                        <tr>
                                            <td class="fw-medium"><?= e($d['dues_name']) ?></td>
                                            <td><?= formatCurrency((float)$d['amount_due']) ?></td>
                                            <td><?= formatCurrency((float)$d['amount_paid']) ?></td>
                                            <td><?= formatCurrency((float)$d['balance']) ?></td>
                                            <td><span class="badge bg-<?= $d['status'] === 'Paid' ? 'success' : ($d['status'] === 'Overdue' ? 'danger' : ($d['status'] === 'Partial' ? 'warning' : 'secondary')) ?> badge-status"><?= $d['status'] ?></span></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <?php if (!empty($payments)): ?>
                    <h6 class="fw-bold mt-4 mb-2">Recent Payments</h6>
                    <div class="card table-card">
                        <div class="table-responsive">
                            <table class="table table-sm mb-0">
                                <thead><tr><th>Receipt</th><th>Amount</th><th>Method</th><th>Date</th></tr></thead>
                                <tbody>
                                    <?php foreach ($payments as $p): ?>
                                        <tr>
                                            <td><a href="<?= url('payments/' . $p['id']) ?>"><?= e($p['receipt_number']) ?></a></td>
                                            <td class="text-success fw-medium"><?= formatCurrency((float)$p['amount']) ?></td>
                                            <td><?= e($p['payment_method']) ?></td>
                                            <td class="text-muted"><?= formatDate($p['payment_date']) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
            <div class="tab-pane fade" id="attendance">
                <div class="card table-card">
                    <div class="table-responsive">
                        <table class="table table-sm mb-0">
                            <thead><tr><th>Date</th><th>Activity</th><th>Status</th></tr></thead>
                            <tbody>
                                <?php if (empty($attendance)): ?>
                                    <tr><td colspan="3" class="text-center text-muted py-3">No attendance records</td></tr>
                                <?php else: ?>
                                    <?php foreach ($attendance as $a): ?>
                                        <tr>
                                            <td><?= formatDate($a['date']) ?></td>
                                            <td><?= e($a['activity_name'] ?? 'N/A') ?></td>
                                            <td><span class="badge bg-<?= $a['status'] === 'Present' ? 'success' : ($a['status'] === 'Absent' ? 'danger' : 'warning') ?> badge-status"><?= $a['status'] ?></span></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="tab-pane fade" id="badges">
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="fw-bold">Badges</h6>
                        <?php if (empty($badges)): ?>
                            <p class="text-muted small">No badges awarded</p>
                        <?php else: ?>
                            <?php foreach ($badges as $b): ?>
                                <div class="d-flex align-items-center mb-2 p-2 bg-light rounded">
                                    <i class="bi bi-award-fill text-warning me-2 fs-5"></i>
                                    <div>
                                        <div class="fw-medium small"><?= e($b['badge_name']) ?></div>
                                        <div class="text-muted small">Awarded: <?= formatDate($b['date_awarded']) ?></div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                    <div class="col-md-6">
                        <h6 class="fw-bold">Awards</h6>
                        <?php if (empty($awards)): ?>
                            <p class="text-muted small">No awards received</p>
                        <?php else: ?>
                            <?php foreach ($awards as $a): ?>
                                <div class="d-flex align-items-center mb-2 p-2 bg-light rounded">
                                    <i class="bi bi-trophy-fill text-success me-2 fs-5"></i>
                                    <div>
                                        <div class="fw-medium small"><?= e($a['name']) ?></div>
                                        <div class="text-muted small">Awarded: <?= formatDate($a['date_awarded']) ?></div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $content = ob_get_clean(); ?>
<?php require __DIR__ . '/../layouts/admin.php'; ?>
