<?php
$companyName = $profile['company_name'] ?? '21st and 24th Accra Boys and Girls Brigade';
$brand = brand_parts($companyName);
$pageTitle = null; // the layout falls back to the company name on the home page
$metaDescription = truncate((string)($profile['description'] ?? 'Boys and Girls Brigade company in Accra.'), 150);
$layout = 'public';
ob_start();
?>

<!-- Hero -->
<section class="hero-section">
    <div class="container">
        <div class="row align-items-center g-5">
            <div class="col-lg-7">
                <h1 class="brand-display mb-4">
                    <?php if ($brand['over'] !== ''): ?>
                        <span class="brand-over"><?= brand_ordinals($brand['over']) ?></span>
                    <?php endif; ?>
                    <span class="brand-main"><?= brand_ordinals($brand['main']) ?></span>
                </h1>
                <?php if (!empty($profile['motto'])): ?>
                    <p class="lead fst-italic text-gold mb-3">&ldquo;<?= e($profile['motto']) ?>&rdquo;</p>
                <?php endif; ?>
                <?php if (!empty($profile['description'])): ?>
                    <p class="mb-4"><?= e(truncate($profile['description'], 220)) ?></p>
                <?php endif; ?>
                <div class="d-flex flex-wrap gap-3">
                    <a href="<?= url('membership') ?>" class="btn btn-hero">Join the Brigade</a>
                    <a href="<?= url('about') ?>" class="btn btn-hero-outline">Our Story</a>
                </div>
            </div>
            <div class="col-lg-5 text-center">
                <?php $heroLogo = brand_logo($profile); ?>
                <?php if ($heroLogo !== ''): ?>
                    <img src="<?= e($heroLogo) ?>" alt="<?= e($companyName) ?> crest" class="hero-crest">
                <?php else: ?>
                    <i class="bi bi-shield-shaded text-gold" style="font-size:9rem;opacity:.35"></i>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<!-- Stats -->
<section class="py-5">
    <div class="container">
        <div class="row text-center">
            <div class="col-6 col-lg-3 stat-counter">
                <div class="number"><?= number_format($stats['members']) ?></div>
                <div class="label">Active Members</div>
            </div>
            <div class="col-6 col-lg-3 stat-counter">
                <div class="number"><?= number_format($stats['sections']) ?></div>
                <div class="label">Sections</div>
            </div>
            <div class="col-6 col-lg-3 stat-counter">
                <div class="number"><?= number_format($stats['officers']) ?></div>
                <div class="label">Officers</div>
            </div>
            <div class="col-6 col-lg-3 stat-counter">
                <div class="number"><?= number_format($stats['years']) ?></div>
                <div class="label">Years of Service</div>
            </div>
        </div>
    </div>
</section>

<!-- Mission & Vision -->
<?php if (!empty($profile['mission']) || !empty($profile['vision'])): ?>
    <section class="py-5 bg-cream">
        <div class="container">
            <div class="row g-4">
                <?php if (!empty($profile['mission'])): ?>
                    <div class="col-md-6">
                        <div class="feature-card h-100">
                            <div class="card-icon"><i class="bi bi-bullseye"></i></div>
                            <h5>Our Mission</h5>
                            <p class="mb-0"><?= e($profile['mission']) ?></p>
                        </div>
                    </div>
                <?php endif; ?>
                <?php if (!empty($profile['vision'])): ?>
                    <div class="col-md-6">
                        <div class="feature-card h-100">
                            <div class="card-icon"><i class="bi bi-eye"></i></div>
                            <h5>Our Vision</h5>
                            <p class="mb-0"><?= e($profile['vision']) ?></p>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>
<?php endif; ?>

<!-- Activities -->
<?php if (!empty($activities)): ?>
    <section class="py-5">
        <div class="container">
            <div class="section-title">
                <h2>Our Activities</h2>
                <span class="rule"></span>
                <a href="<?= url('activities') ?>" class="read-more">See all <i class="bi bi-arrow-right"></i></a>
            </div>
            <div class="row g-3">
                <?php foreach ($activities as $act): ?>
                    <div class="col-lg-4 col-md-6">
                        <article class="activity-card">
                            <div class="date-block">
                                <span style="color: white;" class="day"><?= date('d', strtotime($act['date'])) ?></span>
                                <span class="month"><?= date('M', strtotime($act['date'])) ?></span>
                            </div>
                            <div class="flex-grow-1">
                                <h3 class="mb-1"><?= e($act['name']) ?></h3>
                                <div class="meta">
                                    <?php if (!empty($act['location'])): ?>
                                        <span><i class="bi bi-geo-alt"></i><?= e($act['location']) ?></span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </article>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
<?php endif; ?>

<!-- Upcoming Events -->
<?php if (!empty($upcomingEvents)): ?>
    <section class="py-5 bg-cream">
        <div class="container">
            <div class="section-title">
                <h2>Upcoming Events</h2>
                <span class="rule"></span>
                <a href="<?= url('events') ?>" class="read-more">See all <i class="bi bi-arrow-right"></i></a>
            </div>
            <div class="row g-4">
                <?php foreach (array_slice($upcomingEvents, 0, 3) as $ev): ?>
                    <div class="col-lg-4 col-md-6">
                        <div class="position-relative">
                            <a class="event-tile" href="<?= url('events/view/' . (int)$ev['id']) ?>">
                                <div class="cover">
                                    <?php if (!empty($ev['cover_image'])): ?>
                                        <img src="<?= e(upload_url($ev['cover_image'])) ?>" alt="<?= e($ev['name']) ?>">
                                    <?php else: ?>
                                        <i class="bi bi-flag placeholder"></i>
                                    <?php endif; ?>
                                    <span class="date-chip">
                                        <span style="color: white;" class="day"><?= date('d', strtotime($ev['start_date'])) ?></span>
                                        <span class="month"><?= date('M', strtotime($ev['start_date'])) ?></span>
                                    </span>
                                </div>
                                <div class="body">
                                    <h3><?= e($ev['name']) ?></h3>
                                    <?php if (!empty($ev['description'])): ?>
                                        <p><?= e(truncate($ev['description'], 90)) ?></p>
                                    <?php endif; ?>
                                    <?php if (!empty($ev['location'])): ?>
                                        <div class="foot"><span><i class="bi bi-geo-alt"></i><?= e($ev['location']) ?></span></div>
                                    <?php endif; ?>
                                </div>
                            </a>
                            <button type="button" class="btn btn-sm btn-light border shadow-sm position-absolute top-0 end-0 m-3 z-3"
                                    onclick="triggerShare(event, <?= e(json_encode($ev['name'])) ?>, <?= e(json_encode(url('events/view/' . (int)$ev['id']))) ?>)"
                                    title="Share Event">
                                <i class="bi bi-share-fill text-primary"></i>
                            </button>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
<?php endif; ?>

<!-- Latest News -->
<?php if (!empty($latestNews)): ?>
    <section class="py-5">
        <div class="container">
            <div class="section-title">
                <h2>Latest News</h2>
                <span class="rule"></span>
                <a href="<?= url('news') ?>" class="read-more">All stories <i class="bi bi-arrow-right"></i></a>
            </div>
            <div class="row g-4">
                <?php foreach ($latestNews as $n): ?>
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
                                    <p><?= e(truncate(strip_tags($n['content']), 110)) ?></p>
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
        </div>
    </section>
<?php endif; ?>

<!-- Gallery strip -->
<?php if (!empty($galleryImages)): ?>
    <section class="py-5 bg-cream">
        <div class="container">
            <div class="section-title">
                <h2>From the Gallery</h2>
                <span class="rule"></span>
                <a href="<?= url('gallery') ?>" class="read-more">View gallery <i class="bi bi-arrow-right"></i></a>
            </div>
            <div class="row g-3">
                <?php foreach ($galleryImages as $img): ?>
                    <div class="col-lg-3 col-md-4 col-6">
                        <div class="position-relative">
                            <a class="photo-tile" href="<?= url('gallery/view/' . (int)$img['album_id']) ?>">
                                <img src="<?= e(upload_url($img['file_path'])) ?>" alt="<?= e($img['caption'] ?: ($img['album_name'] ?? 'Gallery photo')) ?>" loading="lazy">
                                <span class="cap"><?= e($img['album_name'] ?? '') ?></span>
                            </a>
                            <button type="button" class="btn btn-sm btn-light border shadow-sm position-absolute top-0 end-0 m-2 z-3"
                                    onclick="triggerShare(event, <?= e(json_encode($img['album_name'] ?? 'Gallery Album')) ?>, <?= e(json_encode(url('gallery/view/' . (int)$img['album_id']))) ?>)"
                                    title="Share Album">
                                <i class="bi bi-share-fill text-primary"></i>
                            </button>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
<?php endif; ?>

<!-- Announcements -->
<?php if (!empty($announcements)): ?>
    <section class="py-5">
        <div class="container">
            <div class="section-title">
                <h2>Announcements</h2>
                <span class="rule"></span>
            </div>
            <div class="row g-4">
                <?php foreach ($announcements as $a): ?>
                    <div class="col-md-4">
                        <div class="feature-card h-100 position-relative">
                            <div class="d-flex justify-content-between align-items-start">
                                <span class="news-date"><?= formatDate($a['publish_date'], 'd M Y') ?></span>
                                <button type="button" class="btn btn-sm btn-outline-secondary border-0 p-1"
                                        onclick="triggerShare(event, <?= e(json_encode($a['title'])) ?>, <?= e(json_encode(url('/#announcements'))) ?>)"
                                        title="Share Announcement">
                                    <i class="bi bi-share"></i>
                                </button>
                            </div>
                            <h5 class="mt-2"><?= e($a['title']) ?></h5>
                            <p class="mb-0"><?= e(truncate($a['content'], 130)) ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
<?php endif; ?>

<!-- Contact -->
<section class="py-5">
    <div class="container">
        <div class="cta-strip">
            <div class="row g-4 align-items-center">
                <div class="col-lg-7">
                    <h2>Visit us this week</h2>
                    <p class="mb-4">We would love to meet you and your family. Here is how to reach the company office.</p>
                    <div class="row g-4 small">
                        <?php if (!empty($profile['address'])): ?>
                            <div class="col-sm-4">
                                <i class="bi bi-geo-alt-fill text-gold d-block fs-5 mb-2"></i>
                                <span class="text-white-50"><?= e($profile['address']) ?></span>
                            </div>
                        <?php endif; ?>
                        <?php if (!empty($profile['phone'])): ?>
                            <div class="col-sm-4">
                                <i class="bi bi-telephone-fill text-gold d-block fs-5 mb-2"></i>
                                <span class="text-white-50"><?= e($profile['phone']) ?></span>
                            </div>
                        <?php endif; ?>
                        <?php if (!empty($profile['email'])): ?>
                            <div class="col-sm-4">
                                <i class="bi bi-envelope-fill text-gold d-block fs-5 mb-2"></i>
                                <span class="text-white-50"><?= e($profile['email']) ?></span>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="col-lg-5 text-lg-end">
                    <a href="<?= url('contact') ?>" class="btn btn-gold btn-lg">Get in Touch</a>
                </div>
            </div>
        </div>
    </div>
</section>
<?php $content = ob_get_clean(); ?>
<?php require __DIR__ . '/../layouts/public.php'; ?>