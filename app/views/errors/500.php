<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>500 - Server Error</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body class="bg-light d-flex align-items-center" style="min-height:100vh">
<div class="container text-center">
    <div class="mb-4"><i class="bi bi-exclamation-triangle text-danger" style="font-size:5rem"></i></div>
    <h1 class="display-4 fw-bold text-danger">500</h1>
    <h3>Server Error</h3>
    <p class="text-muted">Something went wrong. Please try again later.</p>
    <a href="<?= url('') ?>" class="btn btn-primary mt-3">Go Home</a>
    <a href="javascript:location.reload()" class="btn btn-outline-secondary mt-3">Retry</a>
</div>
</body>
</html>
