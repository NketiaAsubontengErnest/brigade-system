<!DOCTYPE html>
<html><head><meta charset="UTF-8"><style>
body{font-family:'Helvetica',sans-serif;font-size:10px;color:#333}
h1{color:#2c3e50;font-size:18px;margin-bottom:5px}
h2{color:#3498db;font-size:14px;margin-bottom:10px}
.header{text-align:center;margin-bottom:20px;border-bottom:2px solid #2c3e50;padding-bottom:15px}
.logo{max-height:50px;margin-bottom:5px}
table{width:100%;border-collapse:collapse;margin-top:10px}
th,td{border:1px solid #ddd;padding:5px 8px;text-align:left;font-size:9px}
th{background:#2c3e50;color:#fff;font-weight:bold}
tr:nth-child(even){background:#f9f9f9}
.footer{margin-top:20px;text-align:center;color:#666;font-size:8px;border-top:1px solid #ddd;padding-top:10px}
</style></head>
<body>
<div class="header">
<?php if (!empty($profile['logo'])): ?><img src="<?= $profile['logo'] ?>" class="logo"><?php endif; ?>
<h1><?= e($profile['company_name'] ?? '21st and 24th Accra Boys and Girls Brigade') ?></h1>
<h2><?= e($reportTitle ?? 'Report') ?></h2>
<p>Generated: <?= date('d M Y, h:i A') ?></p>
</div>
<table>
<thead><tr><?php foreach ($reportHeaders as $h): ?><th><?= e($h) ?></th><?php endforeach; ?></tr></thead>
<tbody>
<?php foreach ($reportData as $row): ?>
<tr><?php foreach ($row as $cell): ?><td><?= e((string)$cell) ?></td><?php endforeach; ?></tr>
<?php endforeach; ?>
</tbody>
</table>
<div class="footer"><?= e($profile['company_name'] ?? '') ?> · <?= e($profile['address'] ?? '') ?></div>
</body></html>
