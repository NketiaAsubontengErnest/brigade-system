<?php $pageTitle = 'Dashboard'; ?>
<?php $layout = 'admin'; ?>
<?php ob_start(); ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1 fw-bold">Dashboard</h4>
        <small class="text-muted">Welcome back, <?= e(auth()->user()['full_name'] ?? '') ?></small>
    </div>
    <div class="d-flex align-items-center gap-2">
        <?php if (auth()->hasAnyPermission(['activities.create'])): ?>
            <a href="<?= url('activities/create') ?>" class="btn btn-sm btn-outline-primary">
                <i class="bi bi-plus-lg me-1"></i>New Activity
            </a>
        <?php endif; ?>
        <?php if (auth()->hasAnyPermission(['events.create'])): ?>
            <a href="<?= url('events/create') ?>" class="btn btn-sm btn-outline-success">
                <i class="bi bi-plus-lg me-1"></i>New Event
            </a>
        <?php endif; ?>
        <div class="text-muted small"><?= date('l, d M Y') ?></div>
    </div>
</div>

<!-- Stats Row -->
<div class="row g-3 mb-4">
    <div class="col-xl-3 col-md-6">
        <div class="card stat-card p-3">
            <div class="d-flex align-items-center">
                <div class="stat-icon bg-primary bg-opacity-10 text-primary"><i class="bi bi-people"></i></div>
                <div class="ms-3">
                    <div class="stat-value"><?= number_format($activeMembers) ?></div>
                    <div class="stat-label">Active Members</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card stat-card p-3">
            <div class="d-flex align-items-center">
                <div class="stat-icon bg-success bg-opacity-10 text-success"><i class="bi bi-cash-stack"></i></div>
                <div class="ms-3">
                    <div class="stat-value"><?= formatCurrency($totalCollected) ?></div>
                    <div class="stat-label">Dues Collected</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card stat-card p-3">
            <div class="d-flex align-items-center">
                <div class="stat-icon bg-warning bg-opacity-10 text-warning"><i class="bi bi-exclamation-triangle"></i></div>
                <div class="ms-3">
                    <div class="stat-value"><?= formatCurrency($totalOutstanding) ?></div>
                    <div class="stat-label">Outstanding Dues</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card stat-card p-3">
            <div class="d-flex align-items-center">
                <div class="stat-icon bg-info bg-opacity-10 text-info"><i class="bi bi-wallet2"></i></div>
                <div class="ms-3">
                    <div class="stat-value"><?= formatCurrency($currentBalance) ?></div>
                    <div class="stat-label">Current Balance</div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Second Row -->
<div class="row g-3 mb-4">
    <div class="col-xl-3 col-md-6">
        <div class="card stat-card p-3">
            <div class="d-flex align-items-center">
                <div class="stat-icon bg-secondary bg-opacity-10 text-secondary"><i class="bi bi-person-badge"></i></div>
                <div class="ms-3">
                    <div class="stat-value"><?= number_format($totalOfficers) ?></div>
                    <div class="stat-label">Active Officers</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card stat-card p-3">
            <div class="d-flex align-items-center">
                <div class="stat-icon bg-primary bg-opacity-10 text-primary"><i class="bi bi-person-plus"></i></div>
                <div class="ms-3">
                    <div class="stat-value"><?= number_format($pendingMembers) ?></div>
                    <div class="stat-label">Pending Approvals</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card stat-card p-3">
            <div class="d-flex align-items-center">
                <div class="stat-icon bg-success bg-opacity-10 text-success"><i class="bi bi-calendar-check"></i></div>
                <div class="ms-3">
                    <div class="stat-value"><?= $attendanceRate ?>%</div>
                    <div class="stat-label">Attendance Rate</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card stat-card p-3">
            <div class="d-flex align-items-center">
                <div class="stat-icon bg-danger bg-opacity-10 text-danger"><i class="bi bi-gender-male"></i></div>
                <div class="ms-3">
                    <div class="stat-value"><?= number_format($maleMembers) ?> / <?= number_format($femaleMembers) ?></div>
                    <div class="stat-label">Male / Female</div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Charts Row -->
<div class="row g-3 mb-4">
    <div class="col-lg-8">
        <div class="card table-card p-3">
            <h6 class="card-title fw-bold mb-3">Income vs Expenses (Last 6 Months)</h6>
            <div class="chart-container"><canvas id="incomeExpensesChart"></canvas></div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card table-card p-3">
            <h6 class="card-title fw-bold mb-3">Member Growth</h6>
            <div class="chart-container"><canvas id="memberGrowthChart"></canvas></div>
        </div>
    </div>
</div>

<!-- Third Row: Events + Announcements -->
<div class="row g-3 mb-4">
    <div class="col-lg-6">
        <div class="card table-card">
            <div class="card-header bg-transparent border-0 pt-3 d-flex justify-content-between align-items-center">
                <h6 class="fw-bold mb-0">Upcoming Events</h6>
                <?php if (auth()->hasAnyPermission(['events.create'])): ?>
                    <a href="<?= url('events/create') ?>" class="btn btn-sm btn-outline-success"><i class="bi bi-plus me-1"></i>Add</a>
                <?php endif; ?>
            </div>
            <div class="card-body p-0">
                <?php if (empty($upcomingEvents)): ?>
                    <div class="text-center text-muted py-4">No upcoming events</div>
                <?php else: ?>
                    <div class="list-group list-group-flush">
                        <?php foreach ($upcomingEvents as $event): ?>
                            <div class="list-group-item py-3">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <a href="<?= url('events/' . $event['id']) ?>" class="fw-medium text-decoration-none"><?= e($event['name']) ?></a>
                                        <br><small class="text-muted"><i class="bi bi-calendar me-1"></i><?= formatDate($event['start_date']) ?></small>
                                    </div>
                                    <div class="d-flex align-items-center gap-1">
                                        <span class="badge bg-primary badge-status">Upcoming</span>
                                        <?php if (auth()->hasAnyPermission(['events.delete'])): ?>
                                            <form method="POST" action="<?= url('events/' . $event['id'] . '/delete') ?>" class="d-inline" onsubmit="return confirm('Cancel this event?')">
                                                <?= csrf_field() ?>
                                                <button type="submit" class="btn btn-sm btn-outline-danger py-0 px-1" title="Cancel Event"><i class="bi bi-trash"></i></button>
                                            </form>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card table-card">
            <div class="card-header bg-transparent border-0 pt-3">
                <h6 class="fw-bold mb-0">Recent Announcements</h6>
            </div>
            <div class="card-body p-0">
                <?php if (empty($recentAnnouncements)): ?>
                    <div class="text-center text-muted py-4">No announcements</div>
                <?php else: ?>
                    <div class="list-group list-group-flush">
                        <?php foreach ($recentAnnouncements as $ann): ?>
                            <a href="<?= url('announcements/' . $ann['id']) ?>" class="list-group-item list-group-item-action py-3">
                                <div class="fw-medium"><?= e($ann['title']) ?></div>
                                <small class="text-muted"><?= truncate(e($ann['content']), 80) ?></small>
                                <br><small class="text-muted"><i class="bi bi-clock me-1"></i><?= timeAgo($ann['created_at']) ?></small>
                            </a>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Recent Activities -->
<div class="card table-card mb-4">
    <div class="card-header bg-transparent border-0 pt-3 d-flex justify-content-between align-items-center">
        <h6 class="fw-bold mb-0">Recent Activities</h6>
        <?php if (auth()->hasAnyPermission(['activities.create'])): ?>
            <a href="<?= url('activities/create') ?>" class="btn btn-sm btn-outline-primary"><i class="bi bi-plus me-1"></i>Add Activity</a>
        <?php endif; ?>
    </div>
    <div class="card-body">
        <?php if (empty($recentActivities)): ?>
            <div class="text-center text-muted py-3">No recent activities</div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead><tr><th>Activity</th><th>Date</th><th>Location</th><th>Status</th><th>Actions</th></tr></thead>
                    <tbody>
                        <?php foreach ($recentActivities as $act): ?>
                            <tr>
                                <td><a href="<?= url('activities/' . $act['id']) ?>" class="text-decoration-none fw-medium"><?= e($act['name']) ?></a></td>
                                <td><?= formatDate($act['date']) ?></td>
                                <td class="text-muted"><?= e($act['location'] ?? 'N/A') ?></td>
                                <td><span class="badge bg-<?= $act['status'] === 'Completed' ? 'success' : ($act['status'] === 'Scheduled' ? 'primary' : 'secondary') ?> badge-status"><?= e($act['status']) ?></span></td>
                                <td>
                                    <div class="d-flex gap-1">
                                        <a href="<?= url('activities/' . $act['id']) ?>" class="btn btn-sm btn-outline-primary py-0">View</a>
                                        <?php if (auth()->hasAnyPermission(['activities.delete'])): ?>
                                            <form method="POST" action="<?= url('activities/' . $act['id'] . '/delete') ?>" class="d-inline" onsubmit="return confirm('Cancel this activity?')">
                                                <?= csrf_field() ?>
                                                <button type="submit" class="btn btn-sm btn-outline-danger py-0">Cancel</button>
                                            </form>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Charts Script -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const monthlyData = <?= json_encode($monthlyData) ?>;
    const memberGrowth = <?= json_encode($memberGrowth) ?>;

    // Income vs Expenses Chart
    new Chart(document.getElementById('incomeExpensesChart'), {
        type: 'bar',
        data: {
            labels: monthlyData.map(d => d.month_label),
            datasets: [
                { label: 'Income', data: monthlyData.map(d => parseFloat(d.income)), backgroundColor: 'rgba(46,204,113,.7)', borderRadius: 4 },
                { label: 'Expenses', data: monthlyData.map(d => parseFloat(d.expenses)), backgroundColor: 'rgba(231,76,60,.7)', borderRadius: 4 }
            ]
        },
        options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'top' } }, scales: { y: { beginAtZero: true } } }
    });

    // Member Growth Chart
    new Chart(document.getElementById('memberGrowthChart'), {
        type: 'line',
        data: {
            labels: memberGrowth.map(d => d.month_label),
            datasets: [{ label: 'Members', data: memberGrowth.map(d => parseInt(d.count)), borderColor: '#3498db', backgroundColor: 'rgba(52,152,219,.1)', fill: true, tension: 0.4 }]
        },
        options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true } } }
    });
});
</script>

<?php $content = ob_get_clean(); ?>
<?php require __DIR__ . '/../layouts/admin.php'; ?>
