<?php $pageTitle = 'Bulk CSV Member Import'; ob_start(); ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-0 text-gray-800">Bulk CSV Member Import</h1>
        <p class="text-muted">Import multiple members and guardians at once using a CSV spreadsheet</p>
    </div>
    <div class="d-flex gap-2">
        <a href="<?= url('members/import/template') ?>" class="btn btn-outline-success">
            <i class="bi bi-file-earmark-excel me-1"></i> Download CSV Template
        </a>
        <a href="<?= url('members') ?>" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Back to Members
        </a>
    </div>
</div>

<div class="row">
    <!-- Step 1: Upload Form -->
    <div class="col-lg-5 mb-4">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-primary text-white py-3">
                <h5 class="card-title mb-0"><i class="bi bi-cloud-upload me-2"></i>Step 1: Upload CSV File</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="<?= url('members/import/preview') ?>" enctype="multipart/form-data">
                    <?= csrf_field() ?>
                    <div class="mb-3">
                        <label class="form-label font-weight-bold">Select CSV File</label>
                        <input type="file" name="csv_file" class="form-control" accept=".csv" required>
                        <div class="form-text mt-2">
                            Make sure your CSV columns match the downloadable template headers:
                            <code>first_name, middle_name, last_name, gender, date_of_birth, phone, email, address, section, rank, guardian_name, guardian_relationship, guardian_phone, guardian_email</code>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-search me-1"></i> Parse & Preview File
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Step 2 & 3: Preview Table -->
    <div class="col-lg-7 mb-4">
        <?php if (!empty($_SESSION['csv_import_data'])): ?>
            <?php $rows = $_SESSION['csv_import_data']; ?>
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                    <h5 class="card-title mb-0"><i class="bi bi-eye me-2 text-primary"></i>Step 2: Preview & Validation</h5>
                    <div>
                        <span class="badge bg-success me-1"><?= $previewData['valid'] ?? 0 ?> Valid</span>
                        <span class="badge bg-danger"><?= $previewData['invalid'] ?? 0 ?> Invalid</span>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive" style="max-height: 380px; overflow-y: auto;">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Status</th>
                                    <th>Name</th>
                                    <th>Gender</th>
                                    <th>Section</th>
                                    <th>Guardian</th>
                                    <th>Notes</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($rows as $index => $row): ?>
                                    <tr class="<?= $row['is_valid'] ? 'table-success' : 'table-danger' ?>">
                                        <td>
                                            <?php if ($row['is_valid']): ?>
                                                <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>Ready</span>
                                            <?php else: ?>
                                                <span class="badge bg-danger"><i class="bi bi-exclamation-circle me-1"></i>Error</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><strong><?= htmlspecialchars($row['first_name'] . ' ' . $row['last_name']) ?></strong></td>
                                        <td><?= htmlspecialchars($row['gender']) ?></td>
                                        <td><?= htmlspecialchars($row['section_name']) ?></td>
                                        <td><?= htmlspecialchars($row['guardian_name'] ?: 'None') ?></td>
                                        <td><small class="<?= $row['is_valid'] ? 'text-success' : 'text-danger fw-bold' ?>"><?= htmlspecialchars($row['errors'] ?: 'Valid record') ?></small></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer bg-light p-3 text-end">
                    <form method="POST" action="<?= url('members/import/process') ?>" class="d-inline">
                        <?= csrf_field() ?>
                        <button type="submit" class="btn btn-success" <?= ($previewData['valid'] ?? 0) === 0 ? 'disabled' : '' ?>>
                            <i class="bi bi-check-all me-1"></i> Confirm & Import <?= $previewData['valid'] ?? 0 ?> Valid Members
                        </button>
                    </form>
                </div>
            </div>
        <?php else: ?>
            <div class="card shadow-sm border-0 text-center py-5">
                <div class="card-body">
                    <i class="bi bi-file-earmark-spreadsheet display-3 text-muted mb-3 d-block"></i>
                    <h5 class="text-muted">No CSV Data Loaded Yet</h5>
                    <p class="text-muted small">Upload a CSV file on the left to preview validation status before committing to database.</p>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php $content = ob_get_clean(); ?>
<?php require __DIR__ . '/../layouts/admin.php'; ?>
