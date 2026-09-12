<!DOCTYPE html>
<html><head><meta charset="UTF-8"><style>
body{font-family:'Helvetica',sans-serif;font-size:11px;color:#333;padding:20px}
.receipt{max-width:500px;margin:0 auto;border:2px solid #2c3e50;padding:25px}
.header{text-align:center;border-bottom:2px dashed #ddd;padding-bottom:15px;margin-bottom:15px}
.logo{max-height:50px;margin-bottom:5px}
h2{color:#2c3e50;margin-bottom:5px;font-size:16px}
h3{text-align:center;margin:10px 0;font-size:14px}
table{width:100%;margin:8px 0;border-collapse:collapse}
td{padding:4px 0}
.text-right{text-align:right}
.total{font-size:18px;font-weight:bold;border-top:2px solid #2c3e50;padding-top:10px;margin-top:10px}
.footer{text-align:center;margin-top:25px;padding-top:15px;border-top:2px dashed #ddd;color:#666;font-size:9px}
hr{border:none;border-top:1px dashed #ddd;margin:8px 0}
</style></head><body>
<div class="receipt">
    <div class="header">
        <?php if (!empty($profileData['logo'])): ?><img src="<?= $profileData['logo'] ?>" class="logo"><?php endif; ?>
        <h2><?= e($profileData['company_name'] ?? '21st and 24th Accra Boys and Girls Brigade') ?></h2>
        <p><?= e($profileData['address'] ?? '') ?></p>
        <p><?= e($profileData['phone'] ?? '') ?></p>
    </div>
    <h3>PAYMENT RECEIPT</h3>
    <table><tr><td><strong>Receipt No:</strong></td><td class="text-right"><?= e($paymentData['receipt_number']) ?></td></tr>
        <tr><td><strong>Date:</strong></td><td class="text-right"><?= e($paymentData['payment_date']) ?></td></tr></table>
    <hr>
    <table><tr><td>Member:</td><td class="text-right"><?= e($paymentData['first_name'] . ' ' . $paymentData['last_name']) ?></td></tr>
        <tr><td>Member No:</td><td class="text-right"><?= e($paymentData['member_number'] ?? 'N/A') ?></td></tr>
        <tr><td>Dues:</td><td class="text-right"><?= e($paymentData['dues_name'] ?? 'General Payment') ?></td></tr>
        <tr><td>Method:</td><td class="text-right"><?= e($paymentData['payment_method']) ?></td></tr></table>
    <div class="total"><span>Amount Paid:</span><span class="text-right" style="color:#27ae60">GHS <?= number_format((float)$paymentData['amount'], 2) ?></span></div>
    <div class="footer"><p>Received by: <?= e($paymentData['recorded_by_name'] ?? 'System') ?></p><p style="margin-top:8px"><em>Thank you for your payment!</em></p></div>
</div>
</body></html>
