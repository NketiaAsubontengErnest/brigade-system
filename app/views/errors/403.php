<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>403 - Access Denied</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body class="bg-light d-flex align-items-center" style="min-height:100vh">
<div class="container text-center">
    <div class="mb-4"><i class="bi bi-shield-x text-danger" style="font-size:5rem"></i></div>
    <h1 class="display-4 fw-bold text-danger">403</h1>
    <h3>Access Denied</h3>
    <p class="text-muted">You do not have permission to access this page.</p>
    <a href="<?= url('dashboard') ?>" class="btn btn-primary mt-3">Back to Dashboard</a>
    <a href="<?= url('') ?>" class="btn btn-outline-secondary mt-3">Home</a>
</div>
</body>
</html>
