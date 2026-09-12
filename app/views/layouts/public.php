<?php
$companyName = $profile['company_name'] ?? '21st and 24th Accra Boys and Girls Brigade';
$brand = brand_parts($companyName);
$logo = brand_logo($profile ?? null);
$currentPath = trim(parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?? '/', '/');
$basePath = trim(base_url(), '/');
if ($basePath !== '' && strpos($currentPath, $basePath) === 0) {
    $currentPath = trim(substr($currentPath, strlen($basePath)), '/');
}
$navItems = [
    ['label' => 'Home', 'path' => '', 'match' => ['']],
    ['label' => 'About', 'path' => 'about', 'match' => ['about', 'leadership']],
    ['label' => 'Activities', 'path' => 'activities', 'match' => ['activities']],
    ['label' => 'Events', 'path' => 'events', 'match' => ['events']],
    ['label' => 'News', 'path' => 'news', 'match' => ['news']],
    ['label' => 'Gallery', 'path' => 'gallery', 'match' => ['gallery']],
    ['label' => 'Contact', 'path' => 'contact', 'match' => ['contact']],
];
$firstSegment = explode('/', $currentPath)[0];
?>
<?php
$titleString = isset($pageTitle) ? e($pageTitle) . ' · ' . e($companyName) : e($companyName);
$descString = e($metaDescription ?? $profile['description'] ?? 'Official website of the 21st and 24th Accra Boys and Girls Brigade. Empowering youth through Christian fellowship, leadership, drill, and community service in Accra, Ghana.');
$scheme = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
$currentUrl = $scheme . '://' . $host . ($_SERVER['REQUEST_URI'] ?? '/');
$ogImage = !empty($logo) ? (str_starts_with($logo, 'http') ? $logo : $scheme . '://' . $host . $logo) : $scheme . '://' . $host . url('assets/img/crest.png');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $titleString ?></title>
    <meta name="description" content="<?= $descString ?>">
    <meta name="keywords" content="Boys Brigade, Girls Brigade, Accra Brigade, Youth Development, Ghana Youth, Christian Fellowship, Drill, Leadership">
    <meta name="robots" content="index, follow, max-snippet:-1, max-image-preview:large, max-video-preview:-1">
    
    <!-- HTTP Security Header Fallback Meta Tags -->
    <meta http-equiv="Content-Security-Policy" content="default-src 'self' 'unsafe-inline' 'unsafe-eval' https: data: blob:; style-src 'self' 'unsafe-inline' https:; script-src 'self' 'unsafe-inline' 'unsafe-eval' https:;">
    <meta http-equiv="Referrer-Policy" content="strict-origin-when-cross-origin">
    <meta http-equiv="X-Content-Type-Options" content="nosniff">
    <meta http-equiv="X-Frame-Options" content="SAMEORIGIN">
    <meta http-equiv="Permissions-Policy" content="camera=(self), microphone=(), geolocation=(), payment=()">

    <!-- Canonical URL -->
    <link rel="canonical" href="<?= e($currentUrl) ?>">

    <!-- Open Graph (Social Media) -->
    <meta property="og:title" content="<?= $titleString ?>">
    <meta property="og:description" content="<?= $descString ?>">
    <meta property="og:type" content="website">
    <meta property="og:url" content="<?= e($currentUrl) ?>">
    <meta property="og:image" content="<?= e($ogImage) ?>">
    <meta property="og:site_name" content="<?= e($companyName) ?>">
    <meta property="og:locale" content="en_US">

    <!-- Twitter Cards -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?= $titleString ?>">
    <meta name="twitter:description" content="<?= $descString ?>">
    <meta name="twitter:image" content="<?= e($ogImage) ?>">

    <!-- Schema.org JSON-LD Structured Data -->
    <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@type": "Organization",
      "name": <?= json_encode($companyName) ?>,
      "url": <?= json_encode($currentUrl) ?>,
      "logo": <?= json_encode($ogImage) ?>,
      "description": <?= json_encode($descString) ?>,
      "address": {
        "@type": "PostalAddress",
        "addressLocality": "Accra",
        "addressCountry": "GH"
      }
    }
    </script>

    <?= favicon_tags() ?>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,400;9..144,500;9..144,600;9..144,700&family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="<?= url('assets/css/public.css') ?>" rel="stylesheet">
</head>
<body>
<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark fixed-top navbar-public">
    <div class="container">
        <a class="navbar-brand brand-lockup p-0" href="<?= url('/') ?>">
            <?php if ($logo !== ''): ?>
                <img class="brand-emblem" src="<?= e($logo) ?>" alt="<?= e($companyName) ?> crest">
            <?php else: ?>
                <span class="brand-mark"><i class="bi bi-shield-shaded"></i></span>
            <?php endif; ?>
            <span class="brand-text">
                <?php if ($brand['over'] !== ''): ?>
                    <span class="brand-over"><?= brand_ordinals($brand['over']) ?></span>
                <?php endif; ?>
                <span class="brand-main"><?= brand_ordinals($brand['main']) ?></span>
            </span>
        </a>
        <button class="navbar-toggler border-0" data-bs-toggle="collapse" data-bs-target="#mainNav" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="mainNav">
            <ul class="navbar-nav ms-auto align-items-lg-center">
                <?php foreach ($navItems as $item): ?>
                    <li class="nav-item">
                        <a class="nav-link <?= in_array($firstSegment, $item['match'], true) ? 'active' : '' ?>"
                           href="<?= url($item['path']) ?>"><?= e($item['label']) ?></a>
                    </li>
                <?php endforeach; ?>
                <?php if (auth()->check()): ?>
                    <li class="nav-item ms-lg-3"><a class="btn btn-nav" href="<?= url('dashboard') ?>">Dashboard</a></li>
                <?php else: ?>
                    <li class="nav-item ms-lg-3"><a class="btn btn-nav" href="<?= url('membership') ?>">Join Us</a></li>
                    <li class="nav-item ms-lg-1"><a class="nav-link" href="<?= url('login') ?>">Login</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>

<main>
    <!-- Flash Messages -->
    <?php
    $flashes = (new App\Core\Session())->getFlashes();
    foreach ($flashes as $type => $message): ?>
        <div class="alert alert-<?= e($type) ?> alert-dismissible fade show container mt-3 mb-0" role="alert">
            <?= e($message) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endforeach; ?>

    <?= $content ?>
</main>

<!-- Footer -->
<footer class="footer-classic">
    <div class="container">
        <div class="row">
            <div class="col-lg-4 mb-4">
                <div class="brand-display mb-3">
                    <?php if ($brand['over'] !== ''): ?>
                        <span class="brand-over"><?= brand_ordinals($brand['over']) ?></span>
                    <?php endif; ?>
                    <span class="brand-main"><?= brand_ordinals($brand['main']) ?></span>
                </div>
                <?php if (!empty($profile['description'])): ?>
                    <p class="small"><?= e(truncate($profile['description'], 180)) ?></p>
                <?php endif; ?>
                <?php if (!empty($profile['motto'])): ?>
                    <p class="fst-italic text-gold mb-0">&ldquo;<?= e($profile['motto']) ?>&rdquo;</p>
                <?php endif; ?>
            </div>
            <div class="col-lg-2 col-md-4 mb-4">
                <h6>Explore</h6>
                <ul class="list-unstyled small">
                    <li class="mb-2"><a href="<?= url('about') ?>">About Us</a></li>
                    <li class="mb-2"><a href="<?= url('activities') ?>">Activities</a></li>
                    <li class="mb-2"><a href="<?= url('events') ?>">Events</a></li>
                    <li class="mb-2"><a href="<?= url('news') ?>">News</a></li>
                    <li class="mb-2"><a href="<?= url('gallery') ?>">Gallery</a></li>
                </ul>
            </div>
            <?php
            // Only render the contact column when there is something to show,
            // otherwise the footer displays a heading with nothing under it.
            $hasContact = !empty($profile['address']) || !empty($profile['phone']) || !empty($profile['email']);
            ?>
            <div class="col-lg-3 col-md-4 mb-4">
                <h6>Contact</h6>
                <?php if ($hasContact): ?>
                    <ul class="list-unstyled small">
                        <?php if (!empty($profile['address'])): ?>
                            <li class="mb-2"><i class="bi bi-geo-alt me-2 text-gold"></i><?= e($profile['address']) ?></li>
                        <?php endif; ?>
                        <?php if (!empty($profile['phone'])): ?>
                            <li class="mb-2"><i class="bi bi-telephone me-2 text-gold"></i><?= e($profile['phone']) ?></li>
                        <?php endif; ?>
                        <?php if (!empty($profile['email'])): ?>
                            <li class="mb-2"><i class="bi bi-envelope me-2 text-gold"></i><?= e($profile['email']) ?></li>
                        <?php endif; ?>
                    </ul>
                <?php else: ?>
                    <p class="small mb-2">Our contact details are published on the contact page.</p>
                    <a href="<?= url('contact') ?>" class="read-more text-gold">Contact page <i class="bi bi-arrow-right"></i></a>
                <?php endif; ?>
            </div>
            <div class="col-lg-3 col-md-4 mb-4">
                <h6>Join the Brigade</h6>
                <p class="small">Enrolment is handled by our officers. Reach out and we will guide you through the next steps.</p>
                <a href="<?= url('contact') ?>" class="btn btn-gold btn-sm">Get in Touch</a>
            </div>
        </div>
        <div class="footer-divider text-center small">
            &copy; <?= date('Y') ?> <?= e($companyName) ?>. All rights reserved.
        </div>
    </div>
</footer>

<!-- Universal Public Share Modal -->
<div class="modal fade" id="universalShareModal" tabindex="-1" aria-labelledby="shareModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content rounded-4 border-0 shadow-lg">
            <div class="modal-header border-0 pb-0">
                <h6 class="modal-title fw-bold text-dark" id="shareModalLabel"><i class="bi bi-share me-1 text-primary"></i>Share Content</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center p-4">
                <p class="small text-muted mb-3" id="shareItemTitle">Share this link via social media or copy direct link:</p>

                <!-- Copy Link Input -->
                <div class="input-group mb-2">
                    <input type="text" id="shareLinkInput" class="form-control form-control-sm text-truncate bg-light" readonly>
                    <button class="btn btn-primary btn-sm" type="button" id="copyShareLinkBtn" onclick="copyShareUrl()">
                        <i class="bi bi-clipboard me-1"></i>Copy
                    </button>
                </div>
                <div id="copyAlertSuccess" class="alert alert-success py-1 px-2 small d-none text-center mb-3">
                    <i class="bi bi-check-circle me-1"></i>Link copied to clipboard!
                </div>

                <div class="d-flex justify-content-center gap-3 mt-3">
                    <a id="shareWaBtn" href="#" target="_blank" class="btn btn-outline-success btn-sm rounded-circle d-flex align-items-center justify-content-center" style="width:44px;height:44px;" title="Share on WhatsApp">
                        <i class="bi bi-whatsapp fs-5"></i>
                    </a>
                    <a id="shareFbBtn" href="#" target="_blank" class="btn btn-outline-primary btn-sm rounded-circle d-flex align-items-center justify-content-center" style="width:44px;height:44px;" title="Share on Facebook">
                        <i class="bi bi-facebook fs-5"></i>
                    </a>
                    <a id="shareXBtn" href="#" target="_blank" class="btn btn-outline-dark btn-sm rounded-circle d-flex align-items-center justify-content-center" style="width:44px;height:44px;" title="Share on X">
                        <i class="bi bi-twitter-x fs-5"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Solidify the navbar once the page is scrolled
    (function () {
        var nav = document.querySelector('.navbar-public');
        if (!nav) return;
        var onScroll = function () {
            nav.classList.toggle('scrolled', window.scrollY > 20);
        };
        onScroll();
        window.addEventListener('scroll', onScroll, { passive: true });
    })();

    // Universal Public Share Trigger Function
    function triggerShare(evt, title, url) {
        // Handle optional event parameter
        if (evt && typeof evt === 'object') {
            if (typeof evt.preventDefault === 'function') evt.preventDefault();
            if (typeof evt.stopPropagation === 'function') evt.stopPropagation();
        } else if (typeof evt === 'string' && typeof title === 'string') {
            // Shift arguments if evt was omitted: triggerShare(title, url)
            url = title;
            title = evt;
        }

        var absoluteUrl = url ? (url.startsWith('http') ? url : (window.location.origin + (url.startsWith('/') ? '' : '/') + url)) : window.location.href;
        var modalEl = document.getElementById('universalShareModal');
        if (!modalEl) return;
        
        var modalTitleEl = document.getElementById('shareModalLabel');
        var itemTitleEl = document.getElementById('shareItemTitle');
        var linkInput = document.getElementById('shareLinkInput');
        var waBtn = document.getElementById('shareWaBtn');
        var fbBtn = document.getElementById('shareFbBtn');
        var xBtn = document.getElementById('shareXBtn');
        var copyAlert = document.getElementById('copyAlertSuccess');

        if (modalTitleEl) modalTitleEl.innerHTML = '<i class="bi bi-share me-1 text-primary"></i>' + (title ? 'Share ' + title : 'Share');
        if (itemTitleEl) itemTitleEl.textContent = title ? title : 'Share this direct link';
        if (linkInput) linkInput.value = absoluteUrl;
        if (waBtn) waBtn.href = 'https://api.whatsapp.com/send?text=' + encodeURIComponent((title ? title + ' ' : '') + absoluteUrl);
        if (fbBtn) fbBtn.href = 'https://www.facebook.com/sharer/sharer.php?u=' + encodeURIComponent(absoluteUrl);
        if (xBtn) xBtn.href = 'https://twitter.com/intent/tweet?url=' + encodeURIComponent(absoluteUrl) + '&text=' + encodeURIComponent(title || '');
        if (copyAlert) copyAlert.classList.add('d-none');
        
        var modal = bootstrap.Modal.getOrCreateInstance(modalEl);
        modal.show();
    }

    function copyShareUrl() {
        var input = document.getElementById('shareLinkInput');
        if (!input) return;
        input.select();
        input.setSelectionRange(0, 99999);
        
        try {
            if (navigator.clipboard && navigator.clipboard.writeText) {
                navigator.clipboard.writeText(input.value);
            } else {
                document.execCommand('copy');
            }
        } catch (e) {
            document.execCommand('copy');
        }

        var alertBox = document.getElementById('copyAlertSuccess');
        if (alertBox) {
            alertBox.classList.remove('d-none');
            setTimeout(function() { alertBox.classList.add('d-none'); }, 3000);
        }
    }
</script>
</body>
</html>
