<?php
$pageTitle = 'Contact';
$metaDescription = 'Get in touch with the 21st and 24th Accra Boys and Girls Brigade company officers and church premises.';
$layout = 'public';
ob_start();
?>
<header class="page-hero">
    <div class="container">
        <p class="eyebrow">Get in Touch</p>
        <h1>Contact Us</h1>
        <p class="lede">
            Have questions about enrolment, meeting times, or company events? We would love to hear from you.
        </p>
    </div>
</header>

<section class="py-5">
    <div class="container">
        <div class="row g-4 justify-content-center">
            <div class="col-md-4 text-center">
                <div class="feature-card p-4 h-100">
                    <div class="card-icon mx-auto"><i class="bi bi-geo-alt-fill"></i></div>
                    <h5 class="fw-bold mb-2">Location &amp; Address</h5>
                    <p class="text-muted small mb-0"><?= e($profile['address'] ?? ($profile['location'] ?? 'Newtown-Accra, Ghana')) ?></p>
                </div>
            </div>
            <div class="col-md-4 text-center">
                <div class="feature-card p-4 h-100">
                    <div class="card-icon mx-auto"><i class="bi bi-telephone-fill"></i></div>
                    <h5 class="fw-bold mb-2">Phone Lines</h5>
                    <p class="text-muted small mb-0"><?= e($profile['phone'] ?? 'Contact officers directly') ?></p>
                </div>
            </div>
            <div class="col-md-4 text-center">
                <div class="feature-card p-4 h-100">
                    <div class="card-icon mx-auto"><i class="bi bi-envelope-fill"></i></div>
                    <h5 class="fw-bold mb-2">Email Address</h5>
                    <p class="text-muted small mb-0"><?= e($profile['email'] ?? 'info@brigade.org') ?></p>
                </div>
            </div>
        </div>
    </div>
</section>
<?php $content = ob_get_clean(); ?>
<?php require __DIR__ . '/../layouts/public.php'; ?>