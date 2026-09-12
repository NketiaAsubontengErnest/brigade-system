<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle ?? 'Dashboard') ?> - <?= e($_ENV['APP_NAME'] ?? 'Brigade System') ?></title>

    <!-- HTTP Security Header Fallback Meta Tags -->
    <meta http-equiv="Content-Security-Policy" content="default-src 'self' 'unsafe-inline' 'unsafe-eval' https: data: blob:; style-src 'self' 'unsafe-inline' https:; script-src 'self' 'unsafe-inline' 'unsafe-eval' https:;">
    <meta http-equiv="Referrer-Policy" content="strict-origin-when-cross-origin">
    <meta http-equiv="X-Content-Type-Options" content="nosniff">
    <meta http-equiv="X-Frame-Options" content="SAMEORIGIN">
    <meta http-equiv="Permissions-Policy" content="camera=(self), microphone=(), geolocation=(), payment=()">

    <?= favicon_tags() ?>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,400;9..144,500;9..144,600;9..144,700&family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="<?= url('assets/css/admin.css') ?>" rel="stylesheet">
</head>
<body>
<div class="d-flex" id="wrapper">
    <!-- Sidebar Overlay for Mobile -->
    <div id="sidebar-overlay"></div>
    
    <!-- Sidebar -->
    <nav id="sidebar" class="sidebar">
        <div class="sidebar-header">
            <a href="<?= url('dashboard') ?>" class="text-decoration-none d-flex align-items-center">
                <?php $adminLogo = brand_logo(); ?>
                <?php if ($adminLogo !== ''): ?>
                    <img class="brand-emblem" src="<?= e($adminLogo) ?>" alt="Company crest">
                <?php else: ?>
                    <div class="brand-icon">
                        <i class="bi bi-shield-check"></i>
                    </div>
                <?php endif; ?>
                <div class="brand-text ms-3">
                    <h6>21<sup>st</sup> &amp; 24<sup>th</sup> Accra<br>Boys &amp; Girls Brigade</h6>
                    <small>Management System</small>
                </div>
            </a>
        </div>
        <div class="sidebar-menu p-2">
            <?php
            $activeMenu = (new App\Core\Session())->get('active_menu', 'dashboard');
            $menuItems = [
                ['icon' => 'speedometer2', 'label' => 'Dashboard', 'url' => url('dashboard'), 'key' => 'dashboard', 'perm' => 'reports.view'],
                ['icon' => 'people', 'label' => 'Members', 'url' => url('members'), 'key' => 'members', 'perm' => 'members.view'],
                ['icon' => 'person-badge', 'label' => 'Officers', 'url' => url('officers'), 'key' => 'officers', 'perm' => 'officers.view'],
                ['icon' => 'diagram-3', 'label' => 'Sections', 'url' => url('sections'), 'key' => 'sections', 'perm' => 'sections.view'],
                ['icon' => 'clipboard-check', 'label' => 'Attendance', 'url' => url('attendance'), 'key' => 'attendance', 'perm' => 'attendance.view'],
                ['icon' => 'calendar-event', 'label' => 'Activities', 'url' => url('activities/manage'), 'key' => 'activities', 'perm' => 'activities.view'],
                ['icon' => 'calendar3', 'label' => 'Events', 'url' => url('events/manage'), 'key' => 'events', 'perm' => 'events.view'],
                ['icon' => 'cash-stack', 'label' => 'Dues', 'url' => url('dues'), 'key' => 'dues', 'perm' => 'dues.view'],
                ['icon' => 'credit-card', 'label' => 'Payments', 'url' => url('payments'), 'key' => 'payments', 'perm' => 'payments.view'],
                ['icon' => 'wallet2', 'label' => 'Finance', 'url' => url('finance'), 'key' => 'finance', 'perm' => 'finance.view'],
                ['icon' => 'book', 'label' => 'Training', 'url' => url('training'), 'key' => 'training', 'perm' => 'training.view'],
                ['icon' => 'award', 'label' => 'Badges & Awards', 'url' => url('badges'), 'key' => 'badges', 'perm' => 'badges.view'],
                ['icon' => 'megaphone', 'label' => 'Announcements', 'url' => url('announcements'), 'key' => 'announcements', 'perm' => 'announcements.view'],
                ['icon' => 'newspaper', 'label' => 'News', 'url' => url('news/manage'), 'key' => 'news', 'perm' => 'news.view'],
                ['icon' => 'images', 'label' => 'Gallery', 'url' => url('gallery/manage'), 'key' => 'gallery', 'perm' => 'gallery.view'],
                ['icon' => 'folder', 'label' => 'Documents', 'url' => url('documents'), 'key' => 'documents', 'perm' => 'documents.view'],
                ['icon' => 'bar-chart', 'label' => 'Reports', 'url' => url('reports'), 'key' => 'reports', 'perm' => 'reports.view'],
                ['icon' => 'person-gear', 'label' => 'Users', 'url' => url('users'), 'key' => 'users', 'perm' => 'users.view'],
                ['icon' => 'chevron-double-up', 'label' => 'Ranks', 'url' => url('settings/ranks'), 'key' => 'ranks', 'perm' => 'ranks.view'],
                ['icon' => 'gear', 'label' => 'Settings', 'url' => url('settings'), 'key' => 'settings', 'perm' => 'settings.view'],
            ];
            $auth = new App\Core\Auth();
            foreach ($menuItems as $item): ?>
                <?php if ($auth->hasPermission($item['perm'])): ?>
                    <a href="<?= $item['url'] ?>" class="sidebar-link d-flex align-items-center px-3 py-2 rounded mb-1 <?= $activeMenu === $item['key'] ? 'active' : '' ?>">
                        <i class="bi bi-<?= $item['icon'] ?> me-2"></i>
                        <span class="sidebar-label"><?= $item['label'] ?></span>
                    </a>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
        <div class="sidebar-footer mt-auto">
            <a href="<?= url('') ?>" target="_blank" class="d-flex align-items-center" style="color:rgba(255,255,255,0.5);text-decoration:none;font-size:0.85rem;">
                <i class="bi bi-globe me-2"></i>View Website
            </a>
        </div>
    </nav>

    <!-- Page Content -->
    <div id="page-content-wrapper" class="w-100">
        <!-- Top Navbar -->
        <div class="top-navbar">
            <button class="btn-toggle" id="toggle-sidebar">
                <i class="bi bi-list fs-5"></i>
            </button>
            <div class="navbar-actions">
                <!-- Notifications -->
                <div class="dropdown">
                    <button class="btn-notification position-relative" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-bell"></i>
                        <?php
                        $notifCount = 0;
                        try {
                            $db = App\Core\Database::getInstance();
                            $notifCount = (int)($db->fetchOne("SELECT COUNT(*) as c FROM notifications WHERE user_id = :uid AND is_read = false", ['uid' => auth()->id()])['c'] ?? 0);
                        } catch (\Throwable $e) {}
                        ?>
                        <?php if ($notifCount > 0): ?>
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                <?= $notifCount ?>
                            </span>
                        <?php endif; ?>
                    </button>
                    <div class="dropdown-menu dropdown-menu-end" style="width: 350px;">
                        <h6 class="dropdown-header">Notifications</h6>
                        <?php if ($notifCount > 0): ?>
                            <a href="<?= url('settings/notifications') ?>" class="dropdown-item small">View All (<?= $notifCount ?> new)</a>
                        <?php else: ?>
                            <div class="dropdown-item small text-muted">No new notifications</div>
                        <?php endif; ?>
                    </div>
                </div>
                <!-- User Menu -->
                <div class="dropdown">
                    <div class="user-menu dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                        <?php 
                        $userName = e(auth()->user()['full_name'] ?? 'User');
                        $userInitials = strtoupper(substr($userName, 0, 1));
                        ?>
                        <div class="user-avatar"><?= $userInitials ?></div>
                        <span><?= $userName ?></span>
                    </div>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="<?= url('profile') ?>"><i class="bi bi-person-badge me-2"></i>My Profile</a></li>
                        <li><a class="dropdown-item" href="<?= url('dashboard') ?>"><i class="bi bi-speedometer2 me-2"></i>Dashboard</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form method="POST" action="<?= url('logout') ?>">
                                <?= csrf_field() ?>
                                <button class="dropdown-item text-danger" type="submit"><i class="bi bi-box-arrow-right me-2"></i>Logout</button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="container-fluid p-4">
            <!-- Flash Messages -->
            <?php
            $flashes = (new App\Core\Session())->getFlashes();
            foreach ($flashes as $type => $message): ?>
                <div class="alert alert-<?= $type === 'danger' ? 'danger' : ($type === 'warning' ? 'warning' : ($type === 'success' ? 'success' : 'info')) ?> alert-dismissible fade show" role="alert">
                    <?= e($message) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endforeach; ?>

            <?= $content ?>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script src="<?= url('assets/js/admin.js') ?>"></script>
</body>
</html>
