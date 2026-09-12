<?php
$pageTitle = 'Gallery';
$metaDescription = 'Photographs from parades, camps, competitions and company life.';
$layout = 'public';
ob_start();
?>
<header class="page-hero">
    <div class="container">
        <p class="eyebrow">In Pictures</p>
        <h1>Photo Gallery</h1>
        <p class="lede">
            Parades, camps, enrolment services and everyday company life, captured by our officers
            and organised into albums.
        </p>
        <div class="hero-meta">
            <div>
                <div class="value"><?= number_format(count($albums)) ?></div>
                <div class="label">Albums</div>
            </div>
            <div>
                <div class="value"><?= number_format($totalPhotos ?? 0) ?></div>
                <div class="label">Photographs</div>
            </div>
        </div>
    </div>
</header>

<section class="py-5">
    <div class="container">
        <?php if (empty($albums)): ?>
            <div class="empty-state">
                <div class="icon"><i class="bi bi-images"></i></div>
                <h3>The gallery is being prepared</h3>
                <p>Albums from our parades and camps will be published here as soon as they are uploaded.</p>
            </div>
        <?php else: ?>
            <div class="section-title">
                <h2>Albums</h2>
                <span class="rule"></span>
                <span class="count"><?= count($albums) ?> collections</span>
            </div>
            <div class="row g-4 justify-content-center">
                <?php foreach ($albums as $album): ?>
                    <div class="col-lg-4 col-md-6">
                        <div class="position-relative">
                            <a class="album-card" href="<?= url('gallery/view/' . (int)$album['id']) ?>">
                                <?php if (!empty($album['cover'])): ?>
                                    <img src="<?= e(upload_url($album['cover'])) ?>" alt="<?= e($album['name']) ?>">
                                <?php else: ?>
                                    <span class="placeholder"><i class="bi bi-images"></i></span>
                                <?php endif; ?>
                                <span class="scrim"></span>
                                <span class="caption">
                                    <h3><?= e($album['name']) ?></h3>
                                    <span class="count"><?= (int)$album['image_count'] ?> photo<?= (int)$album['image_count'] === 1 ? '' : 's' ?></span>
                                </span>
                            </a>
                            <button type="button" class="btn btn-sm btn-light border shadow-sm position-absolute top-0 end-0 m-3 z-3" 
                                    onclick="triggerShare(event, <?= e(json_encode($album['name'])) ?>, <?= e(json_encode(url('gallery/view/' . (int)$album['id']))) ?>)"
                                    title="Share Album">
                                <i class="bi bi-share-fill text-primary"></i>
                            </button>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>
<?php $content = ob_get_clean(); ?>
<?php require __DIR__ . '/../layouts/public.php'; ?>
