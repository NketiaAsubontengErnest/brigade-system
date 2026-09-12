<?php
$pageTitle = $article['title'];
$metaDescription = truncate(strip_tags((string)$article['content']), 150);
$layout = 'public';
ob_start();
?>
<header class="page-hero">
    <div class="container">
        <div class="d-flex flex-wrap justify-content-between align-items-start gap-3">
            <div>
                <p class="crumb"><a href="<?= url('news') ?>"><i class="bi bi-arrow-left me-1"></i>All News</a></p>
                <p class="eyebrow"><?= formatDate($article['published_date'], 'd F Y') ?></p>
                <h1><?= e($article['title']) ?></h1>
                <p class="lede mb-0">
                    By <?= e($article['author_name'] ?? 'The Company Office') ?>
                </p>
            </div>
            <div>
                <button type="button" class="btn btn-gold btn-sm mt-3" 
                        onclick="triggerShare(event, <?= e(json_encode($article['title'])) ?>, <?= e(json_encode(url('news/article/' . rawurlencode($article['slug'])))) ?>)">
                    <i class="bi bi-share me-1"></i>Share Article
                </button>
            </div>
        </div>
    </div>
</header>

<section class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <?php if (!empty($article['featured_image'])): ?>
                    <img src="<?= e(upload_url($article['featured_image'])) ?>" alt="<?= e($article['title']) ?>" class="article-lead-image mb-4">
                <?php endif; ?>

                <div class="article-body"><?= nl2br(e($article['content'])) ?></div>

                <hr class="my-5">
                <div class="d-flex flex-wrap gap-3 align-items-center justify-content-between">
                    <a href="<?= url('news') ?>" class="read-more"><i class="bi bi-arrow-left"></i> Back to all news</a>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-outline-primary btn-sm" 
                                onclick="triggerShare(event, <?= e(json_encode($article['title'])) ?>, <?= e(json_encode(url('news/article/' . rawurlencode($article['slug'])))) ?>)">
                            <i class="bi bi-share me-1"></i>Share Article
                        </button>
                        <a href="<?= url('contact') ?>" class="btn btn-gold btn-sm">Contact the Company</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<?php $content = ob_get_clean(); ?>
<?php require __DIR__ . '/../layouts/public.php'; ?>
