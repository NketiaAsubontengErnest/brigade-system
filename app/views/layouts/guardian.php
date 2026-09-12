<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle ?? 'Parent Portal') ?> - Brigade System</title>

    <!-- HTTP Security Header Fallback Meta Tags -->
    <meta http-equiv="Content-Security-Policy" content="default-src 'self' 'unsafe-inline' 'unsafe-eval' https: data: blob:; style-src 'self' 'unsafe-inline' https:; script-src 'self' 'unsafe-inline' 'unsafe-eval' https:;">
    <meta http-equiv="Referrer-Policy" content="strict-origin-when-cross-origin">
    <meta http-equiv="X-Content-Type-Options" content="nosniff">
    <meta http-equiv="X-Frame-Options" content="SAMEORIGIN">
    <meta http-equiv="Permissions-Policy" content="camera=(self), microphone=(), geolocation=(), payment=()">

    <?= favicon_tags() ?>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="<?= url('assets/css/admin.css') ?>" rel="stylesheet">
    <style>
        .guardian-header { background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%); color: white; padding: 25px 0; }
        .guardian-card { border-radius: 12px; border: none; box-shadow: 0 4px 12px rgba(0,0,0,0.05); }
    </style>
</head>
<body class="bg-light">
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold d-flex align-items-center" href="<?= url('guardian') ?>">
                <i class="bi bi-shield-heart-fill me-2 fs-4"></i> Parent / Guardian Portal
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#guardianNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="guardianNav">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link <?= str_contains($_SERVER['REQUEST_URI'], '/guardian/dues') ? '' : (str_contains($_SERVER['REQUEST_URI'], '/guardian/attendance') ? '' : (str_contains($_SERVER['REQUEST_URI'], '/guardian/events') ? '' : 'active')) ?>" href="<?= url('guardian') ?>">
                            <i class="bi bi-speedometer2 me-1"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= str_contains($_SERVER['REQUEST_URI'], '/guardian/attendance') ? 'active' : '' ?>" href="<?= url('guardian/attendance') ?>">
                            <i class="bi bi-clipboard-check me-1"></i> Attendance Log
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= str_contains($_SERVER['REQUEST_URI'], '/guardian/dues') ? 'active' : '' ?>" href="<?= url('guardian/dues') ?>">
                            <i class="bi bi-cash-stack me-1"></i> Dues & Payments
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= str_contains($_SERVER['REQUEST_URI'], '/guardian/events') ? 'active' : '' ?>" href="<?= url('guardian/events') ?>">
                            <i class="bi bi-calendar-event me-1"></i> Events & Notices
                        </a>
                    </li>
                </ul>
                <div class="d-flex align-items-center gap-3">
                    <span class="text-white small fw-medium">
                        <i class="bi bi-person-circle me-1"></i> <?= htmlspecialchars(auth()->user()['full_name'] ?? 'Guardian') ?>
                    </span>
                    <form method="POST" action="<?= url('logout') ?>" class="d-inline">
                        <?= csrf_field() ?>
                        <button type="submit" class="btn btn-outline-light btn-sm">
                            <i class="bi bi-box-arrow-right me-1"></i> Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Container -->
    <main class="container my-4">
        <?php if (flash('success')): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?= flash('success') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        <?php if (flash('danger')): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?= flash('danger') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?= $content ?>
    </main>

    <footer class="bg-white border-top py-3 mt-auto">
        <div class="container text-center text-muted small">
            &copy; <?= date('Y') ?> 21st &amp; 24th Accra Boys' &amp; Girls' Brigade Company. All rights reserved.
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
