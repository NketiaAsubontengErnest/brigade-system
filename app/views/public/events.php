<?php
$pageTitle = 'Events';
$metaDescription = 'Camps, anniversaries, competitions and church parades hosted by the company.';
$layout = 'public';
$nextEvent = $upcoming[0] ?? null;
ob_start();
?>
<header class="page-hero">
    <div class="container">
        <p class="eyebrow">Mark Your Calendar</p>
        <h1>Brigade Events</h1>
        <p class="lede">
            Camps, anniversaries, enrolment services, competitions and church parades.
            Everything listed here is published by our officers as it is confirmed.
        </p>
        <?php if ($nextEvent): ?>
            <div class="hero-meta">
                <div>
                    <div class="value"><?= formatDate($nextEvent['start_date'], 'd M') ?></div>
                    <div class="label">Next: <?= e(truncate($nextEvent['name'], 32)) ?></div>
                </div>
                <div>
                    <div class="value"><?= number_format(count($upcoming)) ?></div>
                    <div class="label">Upcoming</div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</header>

<!-- Upcoming events -->
<section class="py-5">
    <div class="container">
        <div class="section-title">
            <h2>Upcoming Events</h2>
            <span class="rule"></span>
            <span class="count"><?= count($upcoming) ?> listed</span>
        </div>

        <?php if (empty($upcoming)): ?>
            <div class="empty-state">
                <div class="icon"><i class="bi bi-calendar-heart"></i></div>
                <h3>No events announced right now</h3>
                <p>Our next camp or parade has not been published yet. Follow our news page or contact the company office for the latest programme.</p>
            </div>
        <?php else: ?>
            <div class="row g-4 justify-content-center">
                <?php foreach ($upcoming as $ev): ?>
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
                                        <p><?= e(truncate($ev['description'], 110)) ?></p>
                                    <?php endif; ?>
                                    <div class="foot">
                                        <?php if (!empty($ev['location'])): ?>
                                            <span><i class="bi bi-geo-alt"></i><?= e($ev['location']) ?></span>
                                        <?php endif; ?>
                                        <?php if ((float)($ev['fee'] ?? 0) > 0): ?>
                                            <span class="pill pill-green"><?= formatCurrency((float)$ev['fee']) ?></span>
                                        <?php else: ?>
                                            <span class="pill pill-gold">Free</span>
                                        <?php endif; ?>
                                    </div>
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
        <?php endif; ?>
    </div>
</section>

<!-- Past events -->
<?php if (!empty($past)): ?>
    <section class="py-5 bg-cream">
        <div class="container">
            <div class="section-title">
                <h2>Looking Back</h2>
                <span class="rule"></span>
                <span class="count">Past events</span>
            </div>
            <div class="row g-3">
                <?php foreach ($past as $ev): ?>
                    <div class="col-lg-4 col-md-6">
                        <a class="activity-card is-past text-decoration-none" href="<?= url('events/view/' . (int)$ev['id']) ?>">
                            <div class="date-block">
                                <span class="day"><?= date('d', strtotime($ev['start_date'])) ?></span>
                                <span class="month"><?= date('M', strtotime($ev['start_date'])) ?></span>
                            </div>
                            <div class="flex-grow-1">
                                <h3 class="mb-1"><?= e($ev['name']) ?></h3>
                                <div class="meta">
                                    <span><i class="bi bi-calendar-check"></i><?= formatDate($ev['start_date'], 'Y') ?></span>
                                    <?php if (!empty($ev['location'])): ?>
                                        <span><i class="bi bi-geo-alt"></i><?= e($ev['location']) ?></span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
<?php endif; ?>

<!-- CTA -->
<section class="pb-5">
    <div class="container">
        <div class="cta-strip">
            <div class="row align-items-center g-4">
                <div class="col-lg-8">
                    <h2>Want to join us at the next event?</h2>
                    <p>Members register through their section officer. Parents and visitors are always welcome &mdash; just let us know you are coming.</p>
                </div>
                <div class="col-lg-4 text-lg-end">
                    <a href="<?= url('contact') ?>" class="btn btn-gold">Get in Touch</a>
                </div>
            </div>
        </div>
    </div>
</section>
<?php $content = ob_get_clean(); ?>
<?php require __DIR__ . '/../layouts/public.php'; ?>