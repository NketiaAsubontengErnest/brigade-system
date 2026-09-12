<?php $pageTitle = 'Reports & Analytics Hub'; $layout = 'admin'; ob_start(); ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-0">Reports & Analytics Hub</h4>
        <small class="text-muted">Real-time charts, performance visualizers, and exportable company reports</small>
    </div>
</div>

<!-- Overview Stats Row -->
<div class="row g-3 mb-4">
    <div class="col-md-3 col-sm-6">
        <div class="card stat-card p-3 border-0 shadow-sm">
            <div class="d-flex align-items-center">
                <div class="stat-icon bg-primary bg-opacity-10 text-primary rounded-3 p-3 me-3">
                    <i class="bi bi-people-fill fs-3"></i>
                </div>
                <div>
                    <h4 class="fw-bold mb-0"><?= number_format($stats['total_members']) ?></h4>
                    <small class="text-muted fw-medium">Active Members</small>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6">
        <div class="card stat-card p-3 border-0 shadow-sm">
            <div class="d-flex align-items-center">
                <div class="stat-icon bg-success bg-opacity-10 text-success rounded-3 p-3 me-3">
                    <i class="bi bi-clipboard-check-fill fs-3"></i>
                </div>
                <div>
                    <h4 class="fw-bold mb-0"><?= $stats['attendance_rate'] ?>%</h4>
                    <small class="text-muted fw-medium">Attendance Rate</small>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6">
        <div class="card stat-card p-3 border-0 shadow-sm">
            <div class="d-flex align-items-center">
                <div class="stat-icon bg-warning bg-opacity-10 text-warning rounded-3 p-3 me-3">
                    <i class="bi bi-cash-stack fs-3"></i>
                </div>
                <div>
                    <h4 class="fw-bold mb-0"><?= formatCurrency($stats['total_collected']) ?></h4>
                    <small class="text-muted fw-medium">Dues Collected</small>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6">
        <div class="card stat-card p-3 border-0 shadow-sm">
            <div class="d-flex align-items-center">
                <div class="stat-icon bg-info bg-opacity-10 text-info rounded-3 p-3 me-3">
                    <i class="bi bi-calendar-event-fill fs-3"></i>
                </div>
                <div>
                    <h4 class="fw-bold mb-0"><?= number_format($stats['total_events']) ?></h4>
                    <small class="text-muted fw-medium">Active Events</small>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Interactive Analytics Charts Row 1 -->
<div class="row g-4 mb-4">
    <!-- Attendance Performance Trend Chart -->
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-transparent border-0 pt-3 pb-0 d-flex justify-content-between align-items-center">
                <h6 class="fw-bold mb-0"><i class="bi bi-graph-up-arrow me-2 text-primary"></i>Attendance Performance Trend</h6>
                <span class="badge bg-light text-muted fw-normal">Recent Parades</span>
            </div>
            <div class="card-body">
                <div style="height: 280px; position: relative;">
                    <canvas id="attendanceTrendChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Section Distribution Doughnut Chart -->
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-transparent border-0 pt-3 pb-0">
                <h6 class="fw-bold mb-0"><i class="bi bi-pie-chart-fill me-2 text-success"></i>Section Membership</h6>
            </div>
            <div class="card-body d-flex align-items-center justify-content-center">
                <div style="height: 240px; width: 100%; position: relative;">
                    <canvas id="sectionsChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Interactive Analytics Charts Row 2 -->
<div class="row g-4 mb-5">
    <!-- Financial Income vs Expenses Chart -->
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-transparent border-0 pt-3 pb-0">
                <h6 class="fw-bold mb-0"><i class="bi bi-bar-chart-line-fill me-2 text-info"></i>Income vs Expenses Cashflow</h6>
            </div>
            <div class="card-body">
                <div style="height: 250px; position: relative;">
                    <canvas id="financialChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Dues Payment Status Breakdown Chart -->
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-transparent border-0 pt-3 pb-0">
                <h6 class="fw-bold mb-0"><i class="bi bi-donut-chart me-2 text-warning"></i>Dues Payment Status</h6>
            </div>
            <div class="card-body">
                <div style="height: 250px; position: relative;">
                    <canvas id="duesStatusChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Report Category Hub -->
<h5 class="fw-bold mb-3"><i class="bi bi-file-earmark-spreadsheet me-2 text-secondary"></i>Company Report Archives</h5>

<div class="row g-4">
    <?php 
    $reports = [
        [
            'type' => 'membership',
            'title' => 'Membership Roster Report',
            'icon' => 'people',
            'color' => 'primary',
            'desc' => 'Complete roster of active, pending, and inactive members sorted by section, rank, and gender.',
            'url' => url('reports/membership'),
        ],
        [
            'type' => 'attendance',
            'title' => 'Parade & Meeting Attendance',
            'icon' => 'clipboard-check',
            'color' => 'success',
            'desc' => 'Track weekly parade attendance rates, present/absent counts, and section-by-section participation.',
            'url' => url('reports/attendance'),
        ],
        [
            'type' => 'dues',
            'title' => 'Dues & Payment Balances',
            'icon' => 'cash-stack',
            'color' => 'warning',
            'desc' => 'Detailed statement of expected dues, collected amounts, outstanding balances, and payment records.',
            'url' => url('reports/dues'),
        ],
        [
            'type' => 'finance',
            'title' => 'Financial Income & Expenses',
            'icon' => 'wallet2',
            'color' => 'info',
            'desc' => 'General ledger report covering income receipts, operational expenses, and net cash balance.',
            'url' => url('reports/finance'),
        ],
        [
            'type' => 'training',
            'title' => 'Training & Badge Completions',
            'icon' => 'book',
            'color' => 'secondary',
            'desc' => 'Summary of member training course enrollments, assessment scores, completion status, and awards.',
            'url' => url('reports/training'),
        ],
        [
            'type' => 'events',
            'title' => 'Events & Attendance Log',
            'icon' => 'calendar3',
            'color' => 'dark',
            'desc' => 'Registration and attendance summary for company camps, parades, and special competitions.',
            'url' => url('reports/events'),
        ],
    ];
    ?>

    <?php foreach ($reports as $r): ?>
        <div class="col-lg-4 col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-4 d-flex flex-column">
                    <div class="d-flex align-items-center mb-3">
                        <div class="stat-icon bg-<?= $r['color'] ?> bg-opacity-10 text-<?= $r['color'] ?> rounded-3 p-3 me-3">
                            <i class="bi bi-<?= $r['icon'] ?> fs-3"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold mb-1 text-dark"><?= $r['title'] ?></h6>
                            <span class="badge bg-<?= $r['color'] ?> bg-opacity-10 text-<?= $r['color'] ?> font-monospace">Report</span>
                        </div>
                    </div>
                    <p class="text-muted small flex-grow-1 mb-4"><?= $r['desc'] ?></p>

                    <div class="border-top pt-3 d-flex justify-content-between align-items-center">
                        <a href="<?= $r['url'] ?>" class="btn btn-sm btn-primary">
                            <i class="bi bi-eye me-1"></i> View Report
                        </a>
                        <div class="dropdown">
                            <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                <i class="bi bi-download me-1"></i> Export
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                                <li>
                                    <a class="dropdown-item small" href="<?= url('reports/export/pdf?report=' . $r['type'] . '&format=pdf') ?>">
                                        <i class="bi bi-file-pdf text-danger me-2"></i> Download PDF
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item small" href="<?= url('reports/export/excel?report=' . $r['type'] . '&format=excel') ?>">
                                        <i class="bi bi-file-excel text-success me-2"></i> Download Excel
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item small" href="<?= url('reports/export/csv?report=' . $r['type'] . '&format=csv') ?>">
                                        <i class="bi bi-filetype-csv text-primary me-2"></i> Download CSV
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<!-- Chart.js Script -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // 1. Attendance Trend Chart
    const attCtx = document.getElementById('attendanceTrendChart');
    if (attCtx) {
        const attData = <?= json_encode($attendanceTrends) ?>;
        const labels = attData.length > 0 ? attData.map(d => d.date) : ['No Data'];
        const present = attData.length > 0 ? attData.map(d => parseInt(d.present || 0)) : [0];
        const absent = attData.length > 0 ? attData.map(d => parseInt(d.absent || 0)) : [0];

        new Chart(attCtx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [
                    { label: 'Present', data: present, backgroundColor: '#198754' },
                    { label: 'Absent', data: absent, backgroundColor: '#dc3545' }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { position: 'top' } },
                scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } }
            }
        });
    }

    // 2. Section Membership Chart
    const secCtx = document.getElementById('sectionsChart');
    if (secCtx) {
        const secData = <?= json_encode($sectionsData) ?>;
        const labels = secData.length > 0 ? secData.map(s => s.name) : ['No Sections'];
        const counts = secData.length > 0 ? secData.map(s => parseInt(s.count || 0)) : [0];

        new Chart(secCtx, {
            type: 'doughnut',
            data: {
                labels: labels,
                datasets: [{
                    data: counts,
                    backgroundColor: ['#0d6efd', '#198754', '#ffc107', '#0dcaf0', '#6c757d']
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { position: 'bottom' } }
            }
        });
    }

    // 3. Financial Income vs Expenses Chart
    const finCtx = document.getElementById('financialChart');
    if (finCtx) {
        const income = <?= json_encode($incomeMonthly) ?>;
        const expenses = <?= json_encode($expenseMonthly) ?>;

        const months = Array.from(new Set([...income.map(i => i.month), ...expenses.map(e => e.month)]));
        const incMap = Object.fromEntries(income.map(i => [i.month, parseFloat(i.total)]));
        const expMap = Object.fromEntries(expenses.map(e => [e.month, parseFloat(e.total)]));

        new Chart(finCtx, {
            type: 'bar',
            data: {
                labels: months.length > 0 ? months : ['Current Month'],
                datasets: [
                    { label: 'Income', data: months.map(m => incMap[m] || 0), backgroundColor: '#0d6efd' },
                    { label: 'Expenses', data: months.map(m => expMap[m] || 0), backgroundColor: '#dc3545' }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { position: 'top' } },
                scales: { y: { beginAtZero: true } }
            }
        });
    }

    // 4. Dues Status Breakdown Chart
    const duesCtx = document.getElementById('duesStatusChart');
    if (duesCtx) {
        const duesData = <?= json_encode($duesStatusData) ?>;
        const labels = duesData.length > 0 ? duesData.map(d => d.status) : ['Paid', 'Unpaid', 'Overdue'];
        const counts = duesData.length > 0 ? duesData.map(d => parseInt(d.count || 0)) : [0, 0, 0];

        new Chart(duesCtx, {
            type: 'pie',
            data: {
                labels: labels,
                datasets: [{
                    data: counts,
                    backgroundColor: ['#198754', '#ffc107', '#dc3545', '#0dcaf0']
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { position: 'bottom' } }
            }
        });
    }
});
</script>

<?php $content = ob_get_clean(); ?>
<?php require __DIR__ . '/../layouts/admin.php'; ?>
