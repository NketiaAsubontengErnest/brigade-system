<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Page Not Found</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body class="bg-light d-flex align-items-center" style="min-height:100vh">
<div class="container text-center">
    <div class="mb-4"><i class="bi bi-question-circle text-warning" style="font-size:5rem"></i></div>
    <h1 class="display-4 fw-bold">404</h1>
    <h3>Page Not Found</h3>
    <p class="text-muted">The page you're looking for doesn't exist.</p>
    <a href="<?= url('') ?>" class="btn btn-primary mt-3">Go Home</a>
    <a href="<?= url('dashboard') ?>" class="btn btn-outline-secondary mt-3">Dashboard</a>
</div>
</body>
</html>
