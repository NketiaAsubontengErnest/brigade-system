<?php $pageTitle = e($album['name']); $layout = 'admin'; ob_start(); ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <a href="<?= url('gallery/manage') ?>" class="text-muted text-decoration-none small"><i class="bi bi-arrow-left me-1"></i>Gallery</a>
        <div class="d-flex align-items-center gap-2 mt-1">
            <h4 class="fw-bold mb-0"><?= e($album['name']) ?></h4>
            <?php if (($album['status'] ?? 'Active') === 'Active'): ?>
                <span class="badge bg-success">Active</span>
            <?php else: ?>
                <span class="badge bg-secondary">Deactivated</span>
            <?php endif; ?>
        </div>
        <small class="text-muted"><?= count($images) ?> images</small>
        <?php if (!empty($album['description'])): ?>
            <p class="text-muted small mt-2 mb-0"><?= e($album['description']) ?></p>
        <?php endif; ?>
    </div>
    <div class="d-flex gap-2">
        <form method="POST" action="<?= url('gallery/album/' . $album['id'] . '/toggle-status') ?>">
            <?= csrf_field() ?>
            <?php if (($album['status'] ?? 'Active') === 'Active'): ?>
                <button type="submit" class="btn btn-outline-warning btn-sm">
                    <i class="bi bi-power me-1"></i>Deactivate
                </button>
            <?php else: ?>
                <button type="submit" class="btn btn-outline-success btn-sm">
                    <i class="bi bi-check2-circle me-1"></i>Activate
                </button>
            <?php endif; ?>
        </form>
        <a href="<?= url('gallery/album/' . $album['id'] . '/edit') ?>" class="btn btn-outline-primary btn-sm"><i class="bi bi-pencil me-1"></i>Edit Album</a>
    </div>
</div>

<div class="card table-card p-4 mb-4">
    <form method="POST" action="<?= url('/gallery/album/' . $album['id'] . '/images') ?>" enctype="multipart/form-data">
        <?= csrf_field() ?>
        <label class="form-label small fw-medium">Upload Images</label>
        <input type="file" name="images[]" class="form-control mb-2" multiple accept="image/*">
        <div class="d-flex align-items-center gap-2">
            <button type="submit" class="btn btn-primary btn-sm"><i class="bi bi-upload me-1"></i>Upload Images</button>
            <small class="text-muted"><i class="bi bi-lightning-charge text-warning me-1"></i>Images are automatically compressed to ~100 KB or less upon upload.</small>
        </div>
    </form>
</div>

<div class="row g-3">
    <?php foreach ($images as $i => $img): ?>
        <div class="col-lg-3 col-md-4 col-sm-6">
            <div class="card table-card overflow-hidden h-100">
                <div class="position-relative cursor-pointer" onclick="openPhotoLightbox(<?= $i ?>)" title="Click to view full image">
                    <img src="<?= upload_url($img['file_path']) ?>" class="card-img-top" style="height:200px;object-fit:cover">
                    <div class="position-absolute bottom-0 end-0 p-2">
                        <span class="badge bg-dark bg-opacity-75 text-white"><i class="bi bi-arrows-angle-expand"></i></span>
                    </div>
                </div>
                <div class="card-body p-2 d-flex justify-content-between align-items-center">
                    <small class="text-muted"><?= formatDate($img['created_at']) ?></small>
                    <form method="POST" action="<?= url('/gallery/image/' . $img['id'] . '/delete') ?>" onsubmit="return confirm('Delete image?')">
                        <?= csrf_field() ?>
                        <button type="submit" class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                    </form>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
    <?php if (empty($images)): ?>
        <div class="col-12 text-center text-muted py-4">No images in this album</div>
    <?php endif; ?>
</div>

<div class="mt-4">
    <form method="POST" action="<?= url('/gallery/album/' . $album['id'] . '/delete') ?>" onsubmit="return confirm('Delete this album and all images permanently?')">
        <?= csrf_field() ?>
        <button type="submit" class="btn btn-danger btn-sm"><i class="bi bi-trash me-1"></i>Delete Album</button>
    </form>
</div>

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
<?php require __DIR__ . '/../layouts/admin.php'; ?>
