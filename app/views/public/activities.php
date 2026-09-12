<?php
$pageTitle = 'Activities';
$metaDescription = 'Parades, drills, Bible study, sports and community service - the weekly rhythm of the Brigade.';
$layout = 'public';
ob_start();
?>
<header class="page-hero">
    <div class="container">
        <p class="eyebrow">What We Do</p>
        <h1>Our Activities</h1>
        <p class="lede">
            Every week our sections gather for drill, Bible study, skills training, sport and service.
            This is the living programme of the company &mdash; published straight from our records as officers plan it.
        </p>
        <div class="hero-meta">
            <div>
                <div class="value"><?= number_format(count($upcoming)) ?></div>
                <div class="label">Coming Up</div>
            </div>
            <div>
                <div class="value"><?= number_format(count($past)) ?></div>
                <div class="label">Recently Held</div>
            </div>
            <div>
                <div class="value"><?= number_format(count($sections)) ?></div>
                <div class="label">Active Sections</div>
            </div>
        </div>
        <?php if (!empty($sections)): ?>
            <div class="hero-tags">
                <?php foreach ($sections as $s): ?>
                    <span class="pill"><i class="bi bi-people-fill"></i><?= e($s['name']) ?></span>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</header>

<!-- Upcoming -->
<section class="py-5">
    <div class="container">
        <div class="section-title">
            <h2>Coming Up</h2>
            <span class="rule"></span>
            <span class="count"><?= count($upcoming) ?> scheduled</span>
        </div>

        <?php if (empty($upcoming)): ?>
            <div class="empty-state">
                <div class="icon"><i class="bi bi-calendar3"></i></div>
                <h3>Nothing on the calendar yet</h3>
                <p>The next term&rsquo;s programme has not been published. Check back soon, or contact an officer for the parade schedule.</p>
            </div>
        <?php else: ?>
            <div class="row g-3 justify-content-center">
                <?php foreach ($upcoming as $a): ?>
                    <div class="col-lg-6">
                        <article class="activity-card">
                            <div class="date-block">
                                <span style="color: white;" class="day"><?= date('d', strtotime($a['date'])) ?></span>
                                <span class="month"><?= date('M', strtotime($a['date'])) ?></span>
                            </div>
                            <div class="flex-grow-1">
                                <div class="d-flex align-items-start justify-content-between gap-2">
                                    <h3 class="mb-1"><?= e($a['name']) ?></h3>
                                    <div class="d-flex align-items-center gap-1">
                                        <?php if (!empty($a['status'])): ?>
                                            <span class="pill pill-gold"><?= e($a['status']) ?></span>
                                        <?php endif; ?>
                                        <button type="button" class="btn btn-sm btn-outline-secondary border-0 p-1"
                                                onclick="triggerShare(event, <?= e(json_encode($a['name'])) ?>, <?= e(json_encode(url('activities'))) ?>)"
                                                title="Share Activity">
                                            <i class="bi bi-share"></i>
                                        </button>
                                    </div>
                                </div>
                                <?php if (!empty($a['description'])): ?>
                                    <p class="text-muted small mb-2"><?= e(truncate($a['description'], 140)) ?></p>
                                <?php endif; ?>
                                <div class="meta">
                                    <span><i class="bi bi-calendar-event"></i><?= formatDate($a['date'], 'D, d M Y') ?></span>
                                    <?php if (!empty($a['start_time'])): ?>
                                        <span><i class="bi bi-clock"></i><?= date('g:i A', strtotime($a['start_time'])) ?><?php
                                                                                                                            if (!empty($a['end_time'])) {
                                                                                                                                echo ' &ndash; ' . date('g:i A', strtotime($a['end_time']));
                                                                                                                            }
                                                                                                                            ?></span>
                                    <?php endif; ?>
                                    <?php if (!empty($a['location'])): ?>
                                        <span><i class="bi bi-geo-alt"></i><?= e($a['location']) ?></span>
                                    <?php endif; ?>
                                    <?php if (!empty($a['section_name'])): ?>
                                        <span><i class="bi bi-people"></i><?= e($a['section_name']) ?></span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </article>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- Recently held -->
<?php if (!empty($past)): ?>
    <section class="py-5 bg-cream">
        <div class="container">
            <div class="section-title">
                <h2>Recently Held</h2>
                <span class="rule"></span>
                <span class="count">Last <?= count($past) ?></span>
            </div>
            <div class="row g-3 justify-content-center">
                <?php foreach ($past as $a): ?>
                    <div class="col-lg-4 col-md-6">
                        <article class="activity-card is-past">
                            <div class="date-block">
                                <span class="day"><?= date('d', strtotime($a['date'])) ?></span>
                                <span class="month"><?= date('M', strtotime($a['date'])) ?></span>
                            </div>
                            <div class="flex-grow-1">
                                <h3 class="mb-1"><?= e($a['name']) ?></h3>
                                <div class="meta">
                                    <?php if (!empty($a['location'])): ?>
                                        <span><i class="bi bi-geo-alt"></i><?= e($a['location']) ?></span>
                                    <?php endif; ?>
                                    <?php if (!empty($a['section_name'])): ?>
                                        <span><i class="bi bi-people"></i><?= e($a['section_name']) ?></span>
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

<!-- CTA -->
<section class="pb-5">
    <div class="container">
        <div class="cta-strip">
            <div class="row align-items-center g-4">
                <div class="col-lg-8">
                    <h2>Come and parade with us</h2>
                    <p>Visitors are welcome at any of our weekly activities. Speak to an officer and we will point you to the right section.</p>
                </div>
                <div class="col-lg-4 text-lg-end">
                    <a href="<?= url('contact') ?>" class="btn btn-gold">Contact an Officer</a>
                </div>
            </div>
        </div>
    </div>
</section>
<?php $content = ob_get_clean(); ?>
<?php require __DIR__ . '/../layouts/public.php'; ?>