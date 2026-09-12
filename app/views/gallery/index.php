<?php 
$pageTitle = 'Photo Gallery Management'; 
$layout = 'admin'; 
$activeFilter = $currentStatus ?? 'all';
ob_start(); 
?>

<!-- Header -->
<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
    <div>
        <h4 class="fw-bold mb-1">Photo Gallery Management</h4>
        <p class="text-muted small mb-0">Organize albums, upload photos, and manage active/deactivated status</p>
    </div>
    <a href="<?= url('gallery/create') ?>" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i>New Album
    </a>
</div>

<!-- Filter Tabs -->
<div class="card table-card mb-4">
    <div class="card-body p-3">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
            <div class="btn-group" role="group" aria-label="Status Filters">
                <a href="<?= url('gallery/manage?status=all') ?>" class="btn btn-sm <?= $activeFilter === 'all' ? 'btn-primary' : 'btn-outline-secondary' ?>">
                    All Albums
                </a>
                <a href="<?= url('gallery/manage?status=Active') ?>" class="btn btn-sm <?= $activeFilter === 'Active' ? 'btn-success' : 'btn-outline-secondary' ?>">
                    Active
                </a>
                <a href="<?= url('gallery/manage?status=Archived') ?>" class="btn btn-sm <?= $activeFilter === 'Archived' ? 'btn-secondary' : 'btn-outline-secondary' ?>">
                    Deactivated / Archived
                </a>
            </div>
            <span class="text-muted small">Showing <?= count($albums) ?> album(s)</span>
        </div>
    </div>
</div>

<!-- Album Cards Grid -->
<div class="row g-4">
    <?php if (empty($albums)): ?>
        <div class="col-12">
            <div class="card table-card p-5 text-center text-muted">
                <i class="bi bi-images fs-1 d-block mb-2 text-muted"></i>
                <h5 class="fw-bold mb-1">No Albums Found</h5>
                <p class="small mb-3">No photo albums match your selected filter.</p>
                <div>
                    <a href="<?= url('gallery/create') ?>" class="btn btn-sm btn-primary">Create First Album</a>
                </div>
            </div>
        </div>
    <?php else: ?>
        <?php foreach ($albums as $a): ?>
            <div class="col-xl-3 col-lg-4 col-md-6">
                <div class="card table-card h-100 border-0 shadow-sm overflow-hidden d-flex flex-column">
                    <!-- Image Cover Container -->
                    <div class="position-relative bg-dark" style="height: 190px;">
                        <?php if ($a['cover']): ?>
                            <img src="<?= upload_url($a['cover']) ?>" class="w-100 h-100" style="object-fit: cover;" alt="<?= e($a['name']) ?>">
                        <?php else: ?>
                            <div class="w-100 h-100 d-flex flex-column align-items-center justify-content-center bg-light text-muted">
                                <i class="bi bi-image-fill" style="font-size: 2.8rem; opacity: 0.4;"></i>
                                <span class="small text-muted mt-1">No Cover Photo</span>
                            </div>
                        <?php endif; ?>

                        <!-- Status Badge Overlay -->
                        <div class="position-absolute top-0 start-0 p-2">
                            <?php if (($a['status'] ?? 'Active') === 'Active'): ?>
                                <span class="badge bg-success shadow-sm"><i class="bi bi-check-circle me-1"></i>Active</span>
                            <?php else: ?>
                                <span class="badge bg-secondary shadow-sm"><i class="bi bi-slash-circle me-1"></i>Deactivated</span>
                            <?php endif; ?>
                        </div>

                        <!-- Image Count Overlay -->
                        <div class="position-absolute bottom-0 end-0 p-2">
                            <span class="badge bg-dark bg-opacity-75 text-white shadow-sm">
                                <i class="bi bi-camera me-1"></i><?= (int)$a['image_count'] ?> photo<?= (int)$a['image_count'] === 1 ? '' : 's' ?>
                            </span>
                        </div>
                    </div>

                    <!-- Album Info Body -->
                    <div class="card-body p-3 d-flex flex-column flex-grow-1">
                        <h6 class="fw-bold mb-1">
                            <a href="<?= url('gallery/album/' . $a['id']) ?>" class="text-decoration-none text-dark hover-primary">
                                <?= e($a['name']) ?>
                            </a>
                        </h6>
                        <?php if (!empty($a['description'])): ?>
                            <p class="text-muted small mb-0 line-clamp-2"><?= e(truncate($a['description'], 85)) ?></p>
                        <?php else: ?>
                            <p class="text-muted small fst-italic mb-0">No description provided</p>
                        <?php endif; ?>
                        <div class="mt-auto pt-2 text-muted small border-top mt-2">
                            <i class="bi bi-clock me-1"></i><?= formatDate($a['created_at'], 'd M Y') ?>
                        </div>
                    </div>

                    <!-- Actions Footer -->
                    <div class="card-footer bg-light border-0 p-3 pt-0">
                        <div class="d-flex flex-wrap gap-1 justify-content-between">
                            <a href="<?= url('gallery/album/' . $a['id']) ?>" class="btn btn-sm btn-outline-primary flex-fill">
                                <i class="bi bi-eye"></i> View
                            </a>
                            <a href="<?= url('gallery/album/' . $a['id'] . '/edit') ?>" class="btn btn-sm btn-outline-secondary flex-fill">
                                <i class="bi bi-pencil"></i> Edit
                            </a>
                            
                            <!-- Toggle Status Form -->
                            <form method="POST" action="<?= url('gallery/album/' . $a['id'] . '/toggle-status') ?>" class="d-inline flex-fill">
                                <?= csrf_field() ?>
                                <?php if (($a['status'] ?? 'Active') === 'Active'): ?>
                                    <button type="submit" class="btn btn-sm btn-outline-warning w-100" title="Deactivate Album">
                                        <i class="bi bi-power"></i> Deactivate
                                    </button>
                                <?php else: ?>
                                    <button type="submit" class="btn btn-sm btn-outline-success w-100" title="Activate Album">
                                        <i class="bi bi-check2-circle"></i> Activate
                                    </button>
                                <?php endif; ?>
                            </form>

                            <?php if (auth()->hasAnyPermission(['gallery.delete'])): ?>
                                <form method="POST" action="<?= url('gallery/album/' . $a['id'] . '/delete') ?>" class="d-inline" onsubmit="return confirm('Delete this album and all its images permanently?')">
                                    <?= csrf_field() ?>
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete Album">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<!-- Pagination -->
<div class="mt-4">
    <?= render_pagination($pagination ?? null) ?>
</div>

<?php $content = ob_get_clean(); ?>
<?php require __DIR__ . '/../layouts/admin.php'; ?>
