<?php
$pageTitle = 'News';
$metaDescription = 'Announcements, reports and stories from the company.';
$layout = 'public';
$articles = $news;
$lead = array_shift($articles);
ob_start();
?>
<header class="page-hero">
    <div class="container">
        <p class="eyebrow">From the Company</p>
        <h1>News &amp; Stories</h1>
        <p class="lede">
            Reports from parades and camps, enrolment announcements, achievements and notices &mdash;
            written and published by the company office.
        </p>
    </div>
</header>

<section class="py-5">
    <div class="container">
        <?php if (!$lead): ?>
            <div class="empty-state">
                <div class="icon"><i class="bi bi-newspaper"></i></div>
                <h3>No articles published yet</h3>
                <p>When our officers publish a report or announcement it will appear here straight away.</p>
            </div>
        <?php else: ?>
            <!-- Lead story -->
            <a class="news-lead text-decoration-none" href="<?= url('news/article/' . rawurlencode($lead['slug'])) ?>">
                <div class="media">
                    <?php if (!empty($lead['featured_image'])): ?>
                        <img src="<?= e(upload_url($lead['featured_image'])) ?>" alt="<?= e($lead['title']) ?>">
                    <?php else: ?>
                        <i class="bi bi-newspaper placeholder"></i>
                    <?php endif; ?>
                </div>
                <div class="body">
                    <span class="pill pill-gold">Latest</span>
                    <h2><?= e($lead['title']) ?></h2>
                    <p><?= e(truncate(strip_tags($lead['content']), 230)) ?></p>
                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                        <span class="news-date">
                            <?= formatDate($lead['published_date'], 'd M Y') ?>
                            <?php if (!empty($lead['author_name'])): ?>
                                &middot; <?= e($lead['author_name']) ?>
                            <?php endif; ?>
                        </span>
                        <span class="read-more">Read the story <i class="bi bi-arrow-right"></i></span>
                    </div>
                </div>
            </a>

            <!-- Remaining stories -->
            <?php if (!empty($articles)): ?>
                <div class="section-title">
                    <h2>More Stories</h2>
                    <span class="rule"></span>
                    <span class="count"><?= count($articles) ?> articles</span>
                </div>
                <div class="row g-4 justify-content-center">
                    <?php foreach ($articles as $n): ?>
                        <div class="col-lg-4 col-md-6">
                            <div class="position-relative">
                                <a class="news-tile text-decoration-none" href="<?= url('news/article/' . rawurlencode($n['slug'])) ?>">
                                    <div class="media">
                                        <?php if (!empty($n['featured_image'])): ?>
                                            <img src="<?= e(upload_url($n['featured_image'])) ?>" alt="<?= e($n['title']) ?>">
                                        <?php else: ?>
                                            <i class="bi bi-journal-text placeholder"></i>
                                        <?php endif; ?>
                                    </div>
                                    <div class="body">
                                        <span class="news-date"><?= formatDate($n['published_date'], 'd M Y') ?></span>
                                        <h3><?= e($n['title']) ?></h3>
                                        <p><?= e(truncate(strip_tags($n['content']), 120)) ?></p>
                                        <span class="read-more mt-3">Read more <i class="bi bi-arrow-right"></i></span>
                                    </div>
                                </a>
                                <button type="button" class="btn btn-sm btn-light border shadow-sm position-absolute top-0 end-0 m-3 z-3"
                                        onclick="triggerShare(event, <?= e(json_encode($n['title'])) ?>, <?= e(json_encode(url('news/article/' . rawurlencode($n['slug'])))) ?>)"
                                        title="Share Article">
                                    <i class="bi bi-share-fill text-primary"></i>
                                </button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</section>
<?php $content = ob_get_clean(); ?>
<?php require __DIR__ . '/../layouts/public.php'; ?>
