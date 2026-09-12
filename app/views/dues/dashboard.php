<?php $pageTitle = 'Dues Dashboard'; $layout = 'admin'; ob_start(); ?>
<div class="mb-4"><h4 class="fw-bold">Dues Dashboard</h4></div>
<div class="row g-3 mb-4">
    <div class="col-lg-3 col-md-6"><div class="card stat-card p-3"><div class="stat-value"><?= formatCurrency($totalExpected) ?></div><div class="stat-label">Total Expected</div></div></div>
    <div class="col-lg-3 col-md-6"><div class="card stat-card p-3"><div class="stat-value text-success"><?= formatCurrency($totalCollected) ?></div><div class="stat-label">Total Collected</div></div></div>
    <div class="col-lg-3 col-md-6"><div class="card stat-card p-3"><div class="stat-value text-danger"><?= formatCurrency($totalOutstanding) ?></div><div class="stat-label">Outstanding</div></div></div>
    <div class="col-lg-3 col-md-6"><div class="card stat-card p-3"><div class="stat-value text-primary"><?= $collectionPct ?>%</div><div class="stat-label">Collection Rate</div></div></div>
</div>
<div class="row g-3 mb-4">
    <div class="col-lg-3 col-md-6"><div class="card table-card p-3 text-center"><div class="text-success fw-bold fs-4"><?= $paidCount ?></div><small class="text-muted">Paid</small></div></div>
    <div class="col-lg-3 col-md-6"><div class="card table-card p-3 text-center"><div class="text-warning fw-bold fs-4"><?= $partialCount ?></div><small class="text-muted">Partial</small></div></div>
    <div class="col-lg-3 col-md-6"><div class="card table-card p-3 text-center"><div class="text-secondary fw-bold fs-4"><?= $unpaidCount ?></div><small class="text-muted">Unpaid</small></div></div>
    <div class="col-lg-3 col-md-6"><div class="card table-card p-3 text-center"><div class="text-danger fw-bold fs-4"><?= $overdueCount ?></div><small class="text-muted">Overdue</small></div></div>
</div>
<div class="row g-3 mb-4">
    <div class="col-lg-8"><div class="card table-card p-3"><h6 class="fw-bold mb-3">Monthly Collection</h6><div class="chart-container"><canvas id="collectionChart"></canvas></div></div></div>
    <div class="col-lg-4"><div class="card table-card p-3"><h6 class="fw-bold mb-3">Outstanding by Type</h6><div class="chart-container"><canvas id="outstandingChart"></canvas></div></div></div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const mc = <?= json_encode($monthlyCollection) ?>;
    new Chart(document.getElementById('collectionChart'), { type: 'bar', data: { labels: mc.map(d => d.label), datasets: [{ label: 'Collected', data: mc.map(d => parseFloat(d.total)), backgroundColor: 'rgba(46,204,113,.7)', borderRadius: 4 }] }, options: { responsive: true, maintainAspectRatio: false } });
    const ot = <?= json_encode($outstandingByType) ?>;
    new Chart(document.getElementById('outstandingChart'), { type: 'doughnut', data: { labels: ot.map(d => d.name), datasets: [{ data: ot.map(d => parseFloat(d.total)), backgroundColor: ['#e74c3c','#f39c12','#3498db','#2ecc71','#9b59b6','#1abc9c'] }] }, options: { responsive: true, maintainAspectRatio: false } });
});
</script>
<?php $content = ob_get_clean(); ?>
<?php require __DIR__ . '/../layouts/admin.php'; ?>
