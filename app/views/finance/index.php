<?php $pageTitle = 'Finance'; $layout = 'admin'; ob_start(); ?>
<div class="mb-4"><h4 class="fw-bold">Financial Dashboard</h4></div>
<div class="row g-3 mb-4">
    <div class="col-lg-4"><div class="card stat-card p-3"><div class="stat-value text-success"><?= formatCurrency($totalIncome) ?></div><div class="stat-label">Total Income</div></div></div>
    <div class="col-lg-4"><div class="card stat-card p-3"><div class="stat-value text-danger"><?= formatCurrency($totalExpenses) ?></div><div class="stat-label">Total Expenses</div></div></div>
    <div class="col-lg-4"><div class="card stat-card p-3"><div class="stat-value <?= $balance >= 0 ? 'text-primary' : 'text-danger' ?>"><?= formatCurrency($balance) ?></div><div class="stat-label">Current Balance</div></div></div>
</div>
<div class="row g-3 mb-4">
    <div class="col-lg-8"><div class="card table-card p-3"><h6 class="fw-bold mb-3">Income vs Expenses</h6><div class="chart-container"><canvas id="finChart"></canvas></div></div></div>
    <div class="col-lg-4">
        <div class="card table-card p-3 mb-3"><a href="<?= url('finance/income/create') ?>" class="btn btn-success w-100 mb-2"><i class="bi bi-plus-lg me-1"></i>Record Income</a><a href="<?= url('finance/income') ?>" class="btn btn-outline-success w-100">View Income</a></div>
        <div class="card table-card p-3"><a href="<?= url('finance/expenses/create') ?>" class="btn btn-danger w-100 mb-2"><i class="bi bi-plus-lg me-1"></i>Record Expense</a><a href="<?= url('finance/expenses') ?>" class="btn btn-outline-danger w-100">View Expenses</a></div>
    </div>
</div>
<div class="row g-3">
    <div class="col-md-6"><div class="card table-card"><div class="card-header bg-transparent"><h6 class="fw-bold mb-0">Recent Income</h6></div><div class="card-body p-0"><table class="table table-sm mb-0"><tbody><?php foreach ($recentIncome as $i): ?><tr><td><?= e($i['category']) ?></td><td class="text-success fw-medium"><?= formatCurrency((float)$i['amount']) ?></td><td class="text-muted small"><?= formatDate($i['date']) ?></td></tr><?php endforeach; ?></tbody></table></div></div></div>
    <div class="col-md-6"><div class="card table-card"><div class="card-header bg-transparent"><h6 class="fw-bold mb-0">Recent Expenses</h6></div><div class="card-body p-0"><table class="table table-sm mb-0"><tbody><?php foreach ($recentExpenses as $x): ?><tr><td><?= e($x['category']) ?></td><td class="text-danger fw-medium"><?= formatCurrency((float)$x['amount']) ?></td><td class="text-muted small"><?= formatDate($x['date']) ?></td></tr><?php endforeach; ?></tbody></table></div></div></div>
</div>
<script>document.addEventListener('DOMContentLoaded',function(){const md=<?=json_encode($monthlyData)?>;new Chart(document.getElementById('finChart'),{type:'bar',data:{labels:md.map(d=>d.label),datasets:[{label:'Income',data:md.map(d=>parseFloat(d.income)),backgroundColor:'rgba(46,204,113,.7)',borderRadius:4},{label:'Expenses',data:md.map(d=>parseFloat(d.expenses)),backgroundColor:'rgba(231,76,60,.7)',borderRadius:4}]},options:{responsive:true,maintainAspectRatio:false}})});</script>
<?php $content = ob_get_clean(); ?>
<?php require __DIR__ . '/../layouts/admin.php'; ?>
