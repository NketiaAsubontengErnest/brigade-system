<?php
$pageTitle = $event['name'];
$metaDescription = truncate((string)($event['description'] ?? ''), 150);
$layout = 'public';
$multiDay = !empty($event['end_date']) && $event['end_date'] !== $event['start_date'];
ob_start();
?>
<header class="page-hero">
    <div class="container">
        <div class="d-flex flex-wrap justify-content-between align-items-start gap-3">
            <div>
                <p class="crumb"><a href="<?= url('events') ?>"><i class="bi bi-arrow-left me-1"></i>All Events</a></p>
                <p class="eyebrow"><?= e($event['status'] ?? 'Event') ?></p>
                <h1><?= e($event['name']) ?></h1>
            </div>
            <div>
                <button type="button" class="btn btn-gold btn-sm mt-3" 
                        onclick="triggerShare(event, <?= e(json_encode($event['name'])) ?>, <?= e(json_encode(url('events/view/' . (int)$event['id']))) ?>)">
                    <i class="bi bi-share me-1"></i>Share Event
                </button>
            </div>
        </div>
        <div class="hero-meta mt-3">
            <div>
                <div class="value"><?= formatDate($event['start_date'], 'd M') ?></div>
                <div class="label"><?= $multiDay ? 'Starts' : formatDate($event['start_date'], 'l') ?></div>
            </div>
            <?php if ($multiDay): ?>
                <div>
                    <div class="value"><?= formatDate($event['end_date'], 'd M') ?></div>
                    <div class="label">Ends</div>
                </div>
            <?php endif; ?>
            <?php if (!empty($event['start_time'])): ?>
                <div>
                    <div class="value"><?= date('g:i', strtotime($event['start_time'])) ?><span style="font-size:.5em"><?= date('A', strtotime($event['start_time'])) ?></span></div>
                    <div class="label">Start Time</div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</header>

<section class="py-5">
    <div class="container">
        <div class="row g-5">
            <div class="col-lg-8">
                <?php if (!empty($event['cover_image'])): ?>
                    <img src="<?= e(upload_url($event['cover_image'])) ?>" alt="<?= e($event['name']) ?>" class="article-lead-image mb-4">
                <?php endif; ?>

                <?php if (!empty($event['description'])): ?>
                    <div class="article-body"><?= nl2br(e($event['description'])) ?></div>
                <?php else: ?>
                    <p class="text-muted">Full details for this event will be shared by the company officers.</p>
                <?php endif; ?>
            </div>

            <div class="col-lg-4">
                <div class="feature-card">
                    <h5>Event Details</h5>
                    <ul class="list-unstyled small mb-0">
                        <li class="d-flex gap-3 py-2 border-bottom">
                            <i class="bi bi-calendar-event text-gold"></i>
                            <span>
                                <?= formatDate($event['start_date'], 'D, d M Y') ?>
                                <?= $multiDay ? ' &ndash; ' . formatDate($event['end_date'], 'D, d M Y') : '' ?>
                            </span>
                        </li>
                        <?php if (!empty($event['start_time'])): ?>
                            <li class="d-flex gap-3 py-2 border-bottom">
                                <i class="bi bi-clock text-gold"></i>
                                <span><?= date('g:i A', strtotime($event['start_time'])) ?><?php
                                    if (!empty($event['end_time'])) { echo ' &ndash; ' . date('g:i A', strtotime($event['end_time'])); }
                                ?></span>
                            </li>
                        <?php endif; ?>
                        <?php if (!empty($event['location'])): ?>
                            <li class="d-flex gap-3 py-2 border-bottom">
                                <i class="bi bi-geo-alt text-gold"></i><span><?= e($event['location']) ?></span>
                            </li>
                        <?php endif; ?>
                        <li class="d-flex gap-3 py-2 border-bottom">
                            <i class="bi bi-cash-coin text-gold"></i>
                            <span><?= (float)($event['fee'] ?? 0) > 0 ? formatCurrency((float)$event['fee']) : 'No fee' ?></span>
                        </li>
                        <?php if (!empty($event['registration_deadline'])): ?>
                            <li class="d-flex gap-3 py-2 border-bottom">
                                <i class="bi bi-hourglass-split text-gold"></i>
                                <span>Register by <?= formatDate($event['registration_deadline'], 'd M Y') ?></span>
                            </li>
                        <?php endif; ?>
                        <?php if (!empty($event['maximum_participants'])): ?>
                            <li class="d-flex gap-3 py-2">
                                <i class="bi bi-people text-gold"></i>
                                <span>Limited to <?= (int)$event['maximum_participants'] ?> participants</span>
                            </li>
                        <?php endif; ?>
                    </ul>
                    <hr>
                    <div class="d-flex flex-column gap-2">
                        <button type="button" class="btn btn-outline-primary w-100 btn-sm"
                                onclick="triggerShare(event, <?= e(json_encode($event['name'])) ?>, <?= e(json_encode(url('events/view/' . (int)$event['id']))) ?>)">
                            <i class="bi bi-share me-1"></i>Share Link
                        </button>
                        <a href="<?= url('contact') ?>" class="btn btn-gold w-100 btn-sm">Ask About This Event</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php if (!empty($others)): ?>
<section class="py-5 bg-cream">
    <div class="container">
        <div class="section-title">
            <h2>Also Coming Up</h2>
            <span class="rule"></span>
        </div>
        <div class="row g-4">
            <?php foreach ($others as $ev): ?>
                <div class="col-lg-4 col-md-6">
                    <a class="event-tile" href="<?= url('events/view/' . (int)$ev['id']) ?>">
                        <div class="cover">
                            <?php if (!empty($ev['cover_image'])): ?>
                                <img src="<?= e(upload_url($ev['cover_image'])) ?>" alt="<?= e($ev['name']) ?>">
                            <?php else: ?>
                                <i class="bi bi-flag placeholder"></i>
                            <?php endif; ?>
                            <span class="date-chip">
                                <span class="day"><?= date('d', strtotime($ev['start_date'])) ?></span>
                                <span class="month"><?= date('M', strtotime($ev['start_date'])) ?></span>
                            </span>
                        </div>
                        <div class="body">
                            <h3 class="mb-0"><?= e($ev['name']) ?></h3>
                        </div>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>
<?php $content = ob_get_clean(); ?>
<?php require __DIR__ . '/../layouts/public.php'; ?>
