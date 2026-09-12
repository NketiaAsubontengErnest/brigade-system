<?php
$pageTitle = $album['name'];
$metaDescription = truncate((string)($album['description'] ?? 'Photographs from the company gallery.'), 150);
$layout = 'public';
ob_start();
?>
<header class="page-hero">
    <div class="container">
        <div class="d-flex flex-wrap justify-content-between align-items-start gap-3">
            <div>
                <p class="crumb"><a href="<?= url('gallery') ?>"><i class="bi bi-arrow-left me-1"></i>All Albums</a></p>
                <p class="eyebrow">Album</p>
                <h1><?= e($album['name']) ?></h1>
                <?php if (!empty($album['description'])): ?>
                    <p class="lede"><?= e($album['description']) ?></p>
                <?php endif; ?>
            </div>
            <div>
                <button type="button" class="btn btn-gold btn-sm mt-3" 
                        onclick="triggerShare(event, <?= e(json_encode($album['name'])) ?>, <?= e(json_encode(url('gallery/view/' . (int)$album['id']))) ?>)">
                    <i class="bi bi-share me-1"></i>Share Album
                </button>
            </div>
        </div>
        <div class="hero-meta mt-3">
            <div>
                <div class="value"><?= number_format(count($images)) ?></div>
                <div class="label">Photograph<?= count($images) === 1 ? '' : 's' ?></div>
            </div>
            <?php if (!empty($album['created_at'])): ?>
                <div>
                    <div class="value"><?= formatDate($album['created_at'], 'M Y') ?></div>
                    <div class="label">Published</div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</header>

<section class="py-5">
    <div class="container">
        <?php if (empty($images)): ?>
            <div class="empty-state">
                <div class="icon"><i class="bi bi-camera"></i></div>
                <h3>This album is still empty</h3>
                <p>Photographs for this album have not been uploaded yet. Please check back shortly.</p>
            </div>
        <?php else: ?>
            <div class="row g-3">
                <?php foreach ($images as $i => $img): ?>
                    <div class="col-lg-3 col-md-4 col-6">
                        <a class="photo-tile cursor-pointer" onclick="openPhotoLightbox(<?= $i ?>)">
                            <img src="<?= e(upload_url($img['file_path'])) ?>"
                                 alt="<?= e($img['caption'] ?: $album['name'] . ' photo ' . ($i + 1)) ?>" loading="lazy">
                            <?php if (!empty($img['caption'])): ?>
                                <span class="cap"><?= e($img['caption']) ?></span>
                            <?php endif; ?>
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <div class="text-center mt-5">
            <a href="<?= url('gallery') ?>" class="read-more"><i class="bi bi-arrow-left"></i> Back to all albums</a>
        </div>
    </div>
</section>

<!-- Lightbox Modal with Next / Prev Controls -->
<div class="modal fade" id="photoModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content bg-dark bg-opacity-95 text-white border-0 rounded-4 shadow-lg overflow-hidden">
            <!-- Modal Header -->
            <div class="modal-header border-0 pb-0 pt-3 px-4 d-flex justify-content-between align-items-center">
                <span class="text-white-50 small fw-medium" id="photoModalCounter">Photo 1 of 1</span>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <!-- Modal Body with Prev/Next Navigation Buttons -->
            <div class="modal-body p-2 p-md-4 text-center position-relative d-flex align-items-center justify-content-center" style="min-height: 400px; max-height: 78vh;">
                <!-- Previous Button -->
                <button type="button" class="btn btn-dark bg-opacity-75 text-white rounded-circle position-absolute top-50 start-0 translate-middle-y ms-2 ms-md-4 p-2 shadow-lg border-0 z-3" 
                        id="prevPhotoBtn" onclick="navigatePhoto(-1)" title="Previous Image (Left Arrow Key)">
                    <i class="bi bi-chevron-left fs-3"></i>
                </button>

                <!-- Image -->
                <img src="" alt="" id="photoModalImage" class="img-fluid rounded-3 shadow-lg" style="max-height: 70vh; object-fit: contain; transition: opacity 0.15s ease-in-out;">

                <!-- Next Button -->
                <button type="button" class="btn btn-dark bg-opacity-75 text-white rounded-circle position-absolute top-50 end-0 translate-middle-y me-2 me-md-4 p-2 shadow-lg border-0 z-3" 
                        id="nextPhotoBtn" onclick="navigatePhoto(1)" title="Next Image (Right Arrow Key)">
                    <i class="bi bi-chevron-right fs-3"></i>
                </button>
            </div>

            <!-- Modal Footer Caption -->
            <div class="modal-footer border-0 pt-0 pb-3 px-4 justify-content-center">
                <p class="text-white-50 small mb-0 text-center" id="photoModalCaption"></p>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var images = <?= json_encode(array_map(function($img) {
        return [
            'src' => upload_url($img['file_path']),
            'caption' => $img['caption'] ?? ''
        ];
    }, $images)) ?>;

    var currentIndex = 0;
    var modalEl = document.getElementById('photoModal');
    if (!modalEl || !images || images.length === 0) return;

    var modalImg = document.getElementById('photoModalImage');
    var modalCap = document.getElementById('photoModalCaption');
    var modalCounter = document.getElementById('photoModalCounter');
    var prevBtn = document.getElementById('prevPhotoBtn');
    var nextBtn = document.getElementById('nextPhotoBtn');

    window.openPhotoLightbox = function (index) {
        currentIndex = parseInt(index, 10);
        if (isNaN(currentIndex) || currentIndex < 0) currentIndex = 0;
        if (currentIndex >= images.length) currentIndex = images.length - 1;
        updateLightbox();
        var modal = bootstrap.Modal.getOrCreateInstance(modalEl);
        modal.show();
    };

    window.navigatePhoto = function (direction) {
        if (images.length === 0) return;
        currentIndex += direction;
        if (currentIndex < 0) currentIndex = images.length - 1;
        if (currentIndex >= images.length) currentIndex = 0;
        updateLightbox();
    };

    function updateLightbox() {
        if (!images[currentIndex]) return;
        modalImg.style.opacity = '0.3';
        setTimeout(function() {
            modalImg.src = images[currentIndex].src;
            modalCap.textContent = images[currentIndex].caption || '';
            modalCounter.textContent = 'Photo ' + (currentIndex + 1) + ' of ' + images.length;
            modalImg.style.opacity = '1';
        }, 100);

        if (images.length <= 1) {
            if (prevBtn) prevBtn.style.display = 'none';
            if (nextBtn) nextBtn.style.display = 'none';
        } else {
            if (prevBtn) prevBtn.style.display = 'block';
            if (nextBtn) nextBtn.style.display = 'block';
        }
    }

    // Keyboard Arrow Keys Listener
    document.addEventListener('keydown', function (e) {
        if (!modalEl.classList.contains('show')) return;
        if (e.key === 'ArrowLeft' || e.keyCode === 37) {
            e.preventDefault();
            window.navigatePhoto(-1);
        } else if (e.key === 'ArrowRight' || e.keyCode === 39) {
            e.preventDefault();
            window.navigatePhoto(1);
        }
    });
});
</script>
<?php $content = ob_get_clean(); ?>
<?php require __DIR__ . '/../layouts/public.php'; ?>
