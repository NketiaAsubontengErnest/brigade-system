<?php $pageTitle = 'Payment Details'; $layout = 'admin'; ob_start(); ?>
<div class="mb-4"><a href="<?= url('payments') ?>" class="text-muted text-decoration-none small"><i class="bi bi-arrow-left me-1"></i>Payments</a><h4 class="fw-bold">Payment Details</h4></div>
<div class="row g-4">
    <div class="col-lg-6">
        <div class="card table-card p-4">
            <h6 class="fw-bold mb-3"><i class="bi bi-receipt me-2"></i>Receipt: <?= e($payment['receipt_number']) ?></h6>
            <table class="table table-sm mb-0">
                <tr><td class="text-muted">Member</td><td class="fw-medium"><?= e($payment['first_name'] . ' ' . $payment['last_name']) ?> (<?= e($payment['member_number'] ?? '') ?>)</td></tr>
                <tr><td class="text-muted">Amount</td><td class="text-success fw-bold fs-5"><?= formatCurrency((float)$payment['amount']) ?></td></tr>
                <tr><td class="text-muted">Method</td><td><?= e($payment['payment_method']) ?></td></tr>
                <tr><td class="text-muted">Date</td><td><?= formatDate($payment['payment_date']) ?></td></tr>
                <?php if ($payment['reference_number']): ?><tr><td class="text-muted">Reference</td><td><?= e($payment['reference_number']) ?></td></tr><?php endif; ?>
                <?php if ($payment['dues_name']): ?><tr><td class="text-muted">For</td><td><?= e($payment['dues_name']) ?></td></tr><?php endif; ?>
                <tr><td class="text-muted">Recorded By</td><td><?= e($payment['recorded_by_name'] ?? 'System') ?></td></tr>
                <tr><td class="text-muted">Status</td><td><span class="badge bg-<?= $payment['status'] === 'Completed' ? 'success' : 'danger' ?> badge-status"><?= $payment['status'] ?></span></td></tr>
            </table>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card table-card p-4">
            <h6 class="fw-bold mb-3">Actions</h6>
            <div class="d-grid gap-2">
                <a href="<?= url('payments/' . $payment['id'] . 'receipt') ?>" class="btn btn-outline-primary"><i class="bi bi-printer me-1"></i>View Receipt</a>
                <a href="<?= url('payments/' . $payment['id'] . 'receipt/download') ?>" class="btn btn-outline-success"><i class="bi bi-download me-1"></i>Download PDF</a>
                <?php if ($payment['status'] === 'Completed'): ?>
                    <form method="POST" action="<?= url('/payments/' . $payment['id'] . '/void') ?>" onsubmit="return confirm('Are you sure you want to void this payment? This will reverse the dues balance.')">
                        <?= csrf_field() ?>
                        <button type="submit" class="btn btn-outline-danger w-100"><i class="bi bi-x-circle me-1"></i>Void Payment</button>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?php $content = ob_get_clean(); ?>
<?php require __DIR__ . '/../layouts/admin.php'; ?>
