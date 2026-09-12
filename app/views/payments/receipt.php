<!DOCTYPE html>
<html><head><meta charset="UTF-8"><title>Receipt <?= e($payment['receipt_number']) ?></title>
<style>*{margin:0;padding:0;box-sizing:border-box}body{font-family:'Segoe UI',sans-serif;padding:20px;font-size:13px}.receipt{max-width:400px;margin:0 auto;border:2px solid #2c3e50;padding:20px}.header{text-align:center;border-bottom:2px dashed #ddd;padding-bottom:15px;margin-bottom:15px}.header h2{color:#2c3e50;margin-bottom:5px}.logo{max-height:60px;margin-bottom:10px}table{width:100%;margin:10px 0}td{padding:4px 0}.text-right{text-align:right}.total{font-size:18px;font-weight:bold;border-top:2px solid #2c3e50;padding-top:10px;margin-top:10px}.footer{text-align:center;margin-top:20px;padding-top:15px;border-top:2px dashed #ddd;color:#666;font-size:11px}@media print{body{padding:0}.receipt{border:none}}</style></head>
<body>
<div class="receipt">
    <div class="header">
        <?php if (!empty($profile['logo'])): ?><img src="/<?= e($profile['logo']) ?>" class="logo" alt="Logo"><?php endif; ?>
        <h2><?= e($profile['company_name'] ?? '21st and 24th Accra Boys and Girls Brigade') ?></h2>
        <p><?= e($profile['address'] ?? '') ?></p>
        <p><?= e($profile['phone'] ?? '') ?></p>
    </div>
    <h3 style="text-align:center;margin-bottom:10px">PAYMENT RECEIPT</h3>
    <table>
        <tr><td><strong>Receipt No:</strong></td><td class="text-right"><?= e($payment['receipt_number']) ?></td></tr>
        <tr><td><strong>Date:</strong></td><td class="text-right"><?= formatDate($payment['payment_date']) ?></td></tr>
    </table>
    <hr style="border:1px dashed #ddd">
    <table>
        <tr><td>Member:</td><td class="text-right"><?= e($payment['first_name'] . ' ' . $payment['last_name']) ?></td></tr>
        <tr><td>Member No:</td><td class="text-right"><?= e($payment['member_number'] ?? 'N/A') ?></td></tr>
        <tr><td>Dues:</td><td class="text-right"><?= e($payment['dues_name'] ?? 'General Payment') ?></td></tr>
        <tr><td>Method:</td><td class="text-right"><?= e($payment['payment_method']) ?></td></tr>
        <?php if ($payment['reference_number']): ?><tr><td>Reference:</td><td class="text-right"><?= e($payment['reference_number']) ?></td></tr><?php endif; ?>
    </table>
    <div class="total">
        <span>Amount Paid:</span>
        <span class="text-right" style="color:#27ae60"><?= formatCurrency((float)$payment['amount']) ?></span>
    </div>
    <div class="footer">
        <p>Received by: <?= e($payment['recorded_by_name'] ?? 'System') ?></p>
        <p style="margin-top:10px"><em>Thank you for your payment!</em></p>
        <p style="margin-top:15px">Print: <button onclick="window.print()" style="padding:5px 15px;cursor:pointer">Print Receipt</button></p>
    </div>
</div>
</body></html>
