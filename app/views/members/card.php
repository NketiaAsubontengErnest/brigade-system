<?php $pageTitle = 'Digital ID Card - ' . e($member['first_name'] . ' ' . $member['last_name']); $layout = 'admin'; ob_start(); ?>

<style>
@media print {
    body * { visibility: hidden; }
    #printableCard, #printableCard * { visibility: visible; }
    #printableCard { position: absolute; left: 0; top: 0; width: 100%; }
    .no-print { display: none !important; }
}

.id-card-wrapper {
    max-width: 420px;
    margin: 0 auto;
}

.id-card {
    width: 100%;
    min-height: 250px;
    background: linear-gradient(135deg, #1b2a47 0%, #0d1b2a 100%);
    color: #ffffff;
    border-radius: 16px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.25);
    position: relative;
    overflow: hidden;
    border: 2px solid #c5a059;
}

.id-card-header {
    background: rgba(197, 160, 89, 0.15);
    border-bottom: 1px solid rgba(197, 160, 89, 0.3);
    padding: 12px 16px;
}

.id-card-title {
    font-size: 11px;
    letter-spacing: 1px;
    color: #c5a059;
    font-weight: 700;
    text-transform: uppercase;
}

.id-card-body {
    padding: 16px;
}

.id-photo {
    width: 85px;
    height: 85px;
    border-radius: 12px;
    object-fit: cover;
    border: 3px solid #c5a059;
    box-shadow: 0 4px 12px rgba(0,0,0,0.3);
}

.barcode-container {
    background: #ffffff;
    padding: 6px 12px;
    border-radius: 8px;
    display: inline-block;
    margin-top: 10px;
}

.barcode-container svg {
    max-width: 100%;
    height: 40px;
}
</style>

<div class="d-flex justify-content-between align-items-center mb-4 no-print">
    <div>
        <a href="<?= url('members') ?>" class="text-muted text-decoration-none small"><i class="bi bi-arrow-left me-1"></i> Back to Members List</a>
        <h4 class="fw-bold mb-0">Member Digital ID Card</h4>
    </div>
    <div class="d-flex gap-2">
        <button onclick="window.print()" class="btn btn-primary btn-sm">
            <i class="bi bi-printer me-1"></i> Print ID Card
        </button>
        <a href="<?= url('members/' . $member['id']) ?>" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-person me-1"></i> View Profile
        </a>
    </div>
</div>

<div class="id-card-wrapper" id="printableCard">
    <!-- FRONT SIDE OF CARD -->
    <div class="card id-card mb-4">
        <div class="id-card-header d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center gap-2">
                <?php if (!empty($profile['logo'])): ?>
                    <img src="<?= asset($profile['logo']) ?>" height="26" alt="Logo">
                <?php else: ?>
                    <i class="bi bi-shield-fill text-warning fs-5"></i>
                <?php endif; ?>
                <div>
                    <div class="id-card-title"><?= e($profile['company_name'] ?? 'BOYS & GIRLS BRIGADE') ?></div>
                    <small class="text-white-50" style="font-size: 9px;">OFFICIAL MEMBERSHIP IDENTIFICATION</small>
                </div>
            </div>
            <span class="badge bg-warning text-dark font-monospace" style="font-size: 10px;"><?= e($member['status'] ?? 'Active') ?></span>
        </div>

        <div class="id-card-body">
            <div class="row align-items-center g-3">
                <div class="col-4 text-center">
                    <?php if (!empty($member['profile_photo'])): ?>
                        <img src="<?= asset($member['profile_photo']) ?>" class="id-photo" alt="Photo">
                    <?php else: ?>
                        <div class="id-photo bg-secondary text-white d-flex align-items-center justify-content-center mx-auto fw-bold fs-3">
                            <?= strtoupper(substr($member['first_name'], 0, 1) . substr($member['last_name'], 0, 1)) ?>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="col-8">
                    <h5 class="fw-bold text-white mb-1"><?= e($member['first_name'] . ' ' . ($member['middle_name'] ? $member['middle_name'] . ' ' : '') . $member['last_name']) ?></h5>
                    <div class="text-warning small fw-semibold mb-1"><?= e($member['member_number'] ?? 'N/A') ?></div>
                    
                    <div class="d-flex flex-wrap gap-1 mb-2">
                        <span class="badge bg-light bg-opacity-10 text-white font-monospace" style="font-size: 10px;">
                            <?= e($member['section_name'] ?? 'Unassigned Section') ?>
                        </span>
                        <?php if (!empty($member['rank_name'])): ?>
                            <span class="badge bg-warning bg-opacity-20 text-warning" style="font-size: 10px;">
                                <?= e($member['rank_name']) ?>
                            </span>
                        <?php endif; ?>
                    </div>

                    <div class="text-white-50" style="font-size: 10px;">
                        <span>Issued: <?= date('M Y', strtotime($member['date_joined'] ?? $member['created_at'])) ?></span>
                    </div>
                </div>
            </div>

            <!-- Barcode Display -->
            <div class="text-center mt-3">
                <div class="barcode-container shadow-sm">
                    <svg id="memberBarcode"></svg>
                </div>
            </div>
        </div>
    </div>

    <!-- BACK SIDE OF CARD -->
    <div class="card id-card bg-dark text-white p-3 no-print">
        <div class="d-flex justify-content-between align-items-center border-bottom border-secondary pb-2 mb-2">
            <small class="text-warning fw-bold" style="font-size: 10px;">CARD HOLDER INFORMATION</small>
            <small class="text-muted" style="font-size: 9px;">SURE & STEDFAST</small>
        </div>

        <div class="row g-2 text-white-50 small" style="font-size: 11px;">
            <div class="col-6">
                <strong class="text-white d-block">Gender:</strong> <?= e($member['gender'] ?? 'N/A') ?>
            </div>
            <div class="col-6">
                <strong class="text-white d-block">Phone:</strong> <?= e($member['phone'] ?? 'N/A') ?>
            </div>
            <div class="col-12">
                <strong class="text-white d-block">Company Address:</strong> <?= e($profile['address'] ?? 'Accra Company Headquarters') ?>
            </div>
        </div>

        <div class="border-top border-secondary pt-2 mt-3 text-center text-muted" style="font-size: 9px;">
            This card is non-transferable and remains the property of the Company. If found, please return to the Brigade Secretariat.
        </div>
    </div>
</div>

<!-- JsBarcode Script -->
<script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const memberNum = "<?= e($member['member_number'] ?: ('MBR-' . $member['id'])) ?>";
    if (typeof JsBarcode !== 'undefined') {
        JsBarcode("#memberBarcode", memberNum, {
            format: "CODE128",
            lineColor: "#000000",
            width: 1.8,
            height: 38,
            displayValue: true,
            fontSize: 11,
            margin: 2
        });
    }
});
</script>

<?php $content = ob_get_clean(); ?>
<?php require __DIR__ . '/../layouts/admin.php'; ?>
