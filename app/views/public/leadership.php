<?php 
$pageTitle = 'Leadership'; 
$metaDescription = 'Meet the Officers and Captains leading the Boys and Girls Brigade company in Accra.';
$layout = 'public'; 
ob_start(); 
?>
<header class="page-hero">
    <div class="container">
        <p class="eyebrow">Officers &amp; Leadership</p>
        <h1>Our Leadership Team</h1>
        <p class="lede">
            Dedicated Christian leaders committed to guiding, mentoring, and disciplining young people.
        </p>
    </div>
</header>

<section class="py-5">
    <div class="container">
        <div class="row g-4 justify-content-center">
            <?php foreach ($officers as $o): ?>
            <div class="col-lg-3 col-md-4 col-sm-6">
                <div class="feature-card p-4 text-center h-100">
                    <?php if ($o['profile_photo']): ?>
                        <img src="<?= e(upload_url($o['profile_photo'])) ?>" class="rounded-circle mb-3 mx-auto shadow-sm" style="width:90px;height:90px;object-fit:cover">
                    <?php else: ?>
                        <div class="rounded-circle bg-navy bg-opacity-10 text-navy d-flex align-items-center justify-content-center mx-auto mb-3" style="width:90px;height:90px">
                            <i class="bi bi-person-fill fs-2"></i>
                        </div>
                    <?php endif; ?>
                    <h6 class="fw-bold mb-1"><?= e($o['first_name'] . ' ' . $o['last_name']) ?></h6>
                    <span class="badge bg-gold text-navy fw-bold px-3 py-1 mt-1 d-inline-block"><?= e($o['position_name']) ?></span>
                </div>
            </div>
            <?php endforeach; ?>
            <?php if (empty($officers)): ?>
                <div class="empty-state">
                    <div class="icon"><i class="bi bi-people"></i></div>
                    <h3>Leadership Roster</h3>
                    <p>Officer information is currently being updated. Check back soon!</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>
<?php $content = ob_get_clean(); ?>
<?php require __DIR__ . '/../layouts/public.php'; ?>
