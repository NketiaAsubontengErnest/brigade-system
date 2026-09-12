<?php $pageTitle = 'Record Expense'; $layout = 'admin'; ob_start(); ?>
<div class="mb-4"><a href="<?= url('finance/expenses') ?>" class="text-muted text-decoration-none small"><i class="bi bi-arrow-left me-1"></i>Expenses</a><h4 class="fw-bold">Record Expense</h4></div>
<div class="card table-card"><div class="card-body p-4">
    <form method="POST" action="<?= url('finance/expenses') ?>">
        <?= csrf_field() ?>
        <div class="row g-3">
            <div class="col-md-4"><label class="form-label small fw-medium">Category *</label><select name="category" class="form-select" required><option value="">Select</option><option>Uniforms</option><option>Equipment</option><option>Events</option><option>Training</option><option>Transport</option><option>Supplies</option><option>Other</option></select></div>
            <div class="col-md-4"><label class="form-label small fw-medium">Amount *</label><input type="number" name="amount" class="form-control" step="0.01" min="0.01" required></div>
            <div class="col-md-4"><label class="form-label small fw-medium">Date</label><input type="date" name="date" class="form-control" value="<?= date('Y-m-d') ?>"></div>
            <div class="col-md-4"><label class="form-label small fw-medium">Payment Method</label><select name="payment_method" class="form-select"><option>Cash</option><option>Mobile Money</option><option>Bank Transfer</option><option>Other</option></select></div>
            <div class="col-md-4"><label class="form-label small fw-medium">Reference</label><input type="text" name="reference" class="form-control"></div>
            <div class="col-12"><label class="form-label small fw-medium">Description</label><textarea name="description" class="form-control" rows="2"></textarea></div>
        </div>
        <hr><div class="d-flex gap-2"><button type="submit" class="btn btn-danger">Record Expense</button><a href="<?= url('finance/expenses') ?>" class="btn btn-outline-secondary">Cancel</a></div>
    </form>
</div></div>
<?php $content = ob_get_clean(); ?>
<?php require __DIR__ . '/../layouts/admin.php'; ?>
