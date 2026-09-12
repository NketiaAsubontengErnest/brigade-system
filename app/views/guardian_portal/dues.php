<?php $pageTitle = 'Dues & Payments'; ob_start(); ?>

<div class="mb-4">
    <h3 class="fw-bold mb-1"><i class="bi bi-cash-stack text-success me-2"></i>Dues &amp; Payments Breakdown</h3>
    <p class="text-muted">Review assigned dues and payment records for your linked children</p>
</div>

<div class="card guardian-card mb-4">
    <div class="card-header bg-white py-3">
        <h5 class="card-title mb-0"><i class="bi bi-receipt me-2 text-primary"></i>Assigned Dues</h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Child Name</th>
                        <th>Dues Title</th>
                        <th>Due Date</th>
                        <th>Total Amount</th>
                        <th>Paid</th>
                        <th>Balance</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($duesList)): ?>
                        <tr><td colspan="7" class="text-center py-4 text-muted">No dues assigned yet.</td></tr>
                    <?php else: ?>
                        <?php foreach ($duesList as $d): ?>
                            <tr>
                                <td><strong><?= htmlspecialchars($d['first_name'] . ' ' . $d['last_name']) ?></strong></td>
                                <td><?= htmlspecialchars($d['dues_title']) ?></td>
                                <td><?= date('d M Y', strtotime($d['due_date'])) ?></td>
                                <td>GHS <?= number_format((float)$d['amount'], 2) ?></td>
                                <td class="text-success fw-bold">GHS <?= number_format((float)$d['paid_amount'], 2) ?></td>
                                <td class="text-danger fw-bold">GHS <?= number_format((float)$d['balance'], 2) ?></td>
                                <td>
                                    <span class="badge bg-<?= $d['status'] === 'Paid' ? 'success' : ($d['status'] === 'Partial' ? 'warning' : 'danger') ?>">
                                        <?= htmlspecialchars($d['status']) ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="card guardian-card">
    <div class="card-header bg-white py-3">
        <h5 class="card-title mb-0"><i class="bi bi-clock-history me-2 text-info"></i>Payment History</h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Receipt #</th>
                        <th>Child Name</th>
                        <th>Payment Date</th>
                        <th>Method</th>
                        <th>Amount Paid</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($payments)): ?>
                        <tr><td colspan="5" class="text-center py-4 text-muted">No payments recorded yet.</td></tr>
                    <?php else: ?>
                        <?php foreach ($payments as $p): ?>
                            <tr>
                                <td><code><?= htmlspecialchars($p['receipt_number']) ?></code></td>
                                <td><strong><?= htmlspecialchars($p['first_name'] . ' ' . $p['last_name']) ?></strong></td>
                                <td><?= date('d M Y', strtotime($p['payment_date'])) ?></td>
                                <td><span class="badge bg-secondary"><?= htmlspecialchars($p['payment_method']) ?></span></td>
                                <td class="text-success fw-bold">GHS <?= number_format((float)$p['amount'], 2) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php $content = ob_get_clean(); ?>
<?php require __DIR__ . '/../layouts/guardian.php'; ?>
