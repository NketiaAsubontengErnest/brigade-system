<?php 
$pageTitle = 'Membership'; 
$metaDescription = 'Explore Brigade sections from Anchors and Explorers to Juniors, Company, and Seniors.';
$layout = 'public'; 
ob_start(); 
?>
<header class="page-hero">
    <div class="container">
        <p class="eyebrow">Join the Company</p>
        <h1>Brigade Sections &amp; Membership</h1>
        <p class="lede">
            Join our Brigade family and develop as a responsible, disciplined, and God-fearing young person.
        </p>
    </div>
</header>

<section class="py-5">
    <div class="container">
        <div class="section-title">
            <h2>Age Sections</h2>
            <span class="rule"></span>
        </div>
        <div class="row g-4">
            <?php foreach ($sections as $s): ?>
            <div class="col-lg-3 col-md-4 col-sm-6">
                <div class="feature-card p-4 text-center h-100">
                    <div class="card-icon mx-auto"><i class="bi bi-shield-fill"></i></div>
                    <h5 class="fw-bold mb-1"><?= e($s['name']) ?></h5>
                    <?php if ($s['age_range']): ?>
                        <span class="badge bg-navy text-gold fw-semibold px-3 py-1 mb-2 d-inline-block">Ages <?= e($s['age_range']) ?></span>
                    <?php endif; ?>
                    <p class="text-muted small mb-0"><?= e($s['description'] ?? '') ?></p>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <div class="text-center mt-5">
            <a href="<?= url('contact') ?>" class="btn btn-gold btn-lg">Enquire About Joining</a>
        </div>
    </div>
</section>
<?php $content = ob_get_clean(); ?>
<?php require __DIR__ . '/../layouts/public.php'; ?>
