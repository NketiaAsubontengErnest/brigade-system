<?php 
$pageTitle = 'About Us'; 
$metaDescription = 'Learn about our mission, vision, history, and Christian foundation for young people in Accra.';
$layout = 'public'; 
ob_start(); 
?>
<header class="page-hero">
    <div class="container">
        <p class="eyebrow">Our Identity &amp; Purpose</p>
        <h1>About the Brigade</h1>
        <p class="lede">
            Learn about our mission, vision, history, and Christian foundation for young people in Accra.
        </p>
    </div>
</header>

<section class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8 text-center">
                <?php $aboutLogo = brand_logo($profile); ?>
                <?php if ($aboutLogo !== ''): ?>
                    <img src="<?= e($aboutLogo) ?>" alt="Brigade Crest" style="max-height:110px" class="mb-4">
                <?php endif; ?>
                <h2 class="fw-bold mb-2"><?= e($profile['company_name'] ?? '') ?></h2>
                <?php if (!empty($profile['motto'])): ?>
                    <p class="fst-italic text-gold fw-semibold fs-5 mb-3">&ldquo;<?= e($profile['motto']) ?>&rdquo;</p>
                <?php endif; ?>
                <p class="lead text-muted"><?= e($profile['description'] ?? '') ?></p>
            </div>
        </div>
        
        <div class="row g-4 mt-4">
            <?php if (!empty($profile['mission'])): ?>
            <div class="col-md-6">
                <div class="feature-card p-4 h-100">
                    <div class="card-icon"><i class="bi bi-bullseye"></i></div>
                    <h5>Our Mission</h5>
                    <p class="mb-0"><?= e($profile['mission']) ?></p>
                </div>
            </div>
            <?php endif; ?>
            <?php if (!empty($profile['vision'])): ?>
            <div class="col-md-6">
                <div class="feature-card p-4 h-100">
                    <div class="card-icon"><i class="bi bi-eye"></i></div>
                    <h5>Our Vision</h5>
                    <p class="mb-0"><?= e($profile['vision']) ?></p>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</section>
<?php $content = ob_get_clean(); ?>
<?php require __DIR__ . '/../layouts/public.php'; ?>
