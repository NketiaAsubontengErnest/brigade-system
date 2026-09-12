<?php $pageTitle = 'Record Payment'; $layout = 'admin'; ob_start(); ?>
<div class="mb-4"><a href="<?= url('payments') ?>" class="text-muted text-decoration-none small"><i class="bi bi-arrow-left me-1"></i>Payments</a><h4 class="fw-bold">Record Payment</h4></div>
<div class="card table-card"><div class="card-body p-4">
    <form method="POST" action="<?= url('payments') ?>">
        <?= csrf_field() ?>
        <div class="row g-3">
            <div class="col-md-4"><label class="form-label small fw-medium">Member *</label><select name="member_id" class="form-select" id="memberSelect" required><option value="">Select Member</option><?php foreach ($members as $m): ?><option value="<?= $m['id'] ?>"><?= e($m['first_name'] . ' ' . $m['last_name'] . ' (' . $m['member_number'] . ')') ?></option><?php endforeach; ?></select></div>
            <div class="col-md-4"><label class="form-label small fw-medium">Dues (leave blank for general payment)</label><select name="member_dues_id" class="form-select" id="duesSelect"><option value="">General Payment</option></select></div>
            <div class="col-md-4"><label class="form-label small fw-medium">Amount *</label><input type="number" name="amount" class="form-control" step="0.01" min="0.01" required id="amountInput"></div>
            <div class="col-md-3"><label class="form-label small fw-medium">Payment Method *</label><select name="payment_method" class="form-select" required><option value="Cash">Cash</option><option value="Mobile Money">Mobile Money</option><option value="Bank Transfer">Bank Transfer</option><option value="Other">Other</option></select></div>
            <div class="col-md-3"><label class="form-label small fw-medium">Reference Number</label><input type="text" name="reference_number" class="form-control" placeholder="e.g. MOMO-12345"></div>
            <div class="col-md-3"><label class="form-label small fw-medium">Payment Date *</label><input type="date" name="payment_date" class="form-control" value="<?= date('Y-m-d') ?>" required></div>
            <div class="col-12"><label class="form-label small fw-medium">Notes</label><textarea name="notes" class="form-control" rows="2"></textarea></div>
        </div>
        <hr><div class="d-flex gap-2"><button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>Record Payment</button><a href="<?= url('payments') ?>" class="btn btn-outline-secondary">Cancel</a></div>
    </form>
</div></div>
<script>
document.getElementById('memberSelect').addEventListener('change', function() {
    const memberId = this.value;
    const duesSelect = document.getElementById('duesSelect');
    if (!memberId) { duesSelect.innerHTML = '<option value="">General Payment</option>'; return; }
    fetch('<?= url('members/api/search') ?>?q=' + encodeURIComponent(memberId)).then(r => r.json()).then(() => {});
});
</script>
<?php $content = ob_get_clean(); ?>
<?php require __DIR__ . '/../layouts/admin.php'; ?>
