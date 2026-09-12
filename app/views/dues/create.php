<?php $pageTitle = 'Create Dues'; $layout = 'admin'; ob_start(); ?>
<div class="mb-4">
    <a href="<?= url('dues') ?>" class="text-muted text-decoration-none small"><i class="bi bi-arrow-left me-1"></i>Dues</a>
    <h4 class="fw-bold">Create Dues</h4>
</div>
<div class="card table-card">
    <div class="card-body p-4">
        <form method="POST" action="<?= url('dues') ?>">
            <?= csrf_field() ?>
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label small fw-medium">Dues Type</label>
                    <select name="dues_type_id" class="form-select">
                        <option value="">Select Type</option>
                        <?php foreach ($types as $t): ?>
                            <option value="<?= $t['id'] ?>"><?= e($t['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label small fw-medium">Name *</label>
                    <input type="text" name="name" class="form-control" required placeholder="e.g. September Monthly Dues">
                </div>
                <div class="col-md-4">
                    <label class="form-label small fw-medium">Amount *</label>
                    <input type="number" name="amount" class="form-control" step="0.01" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-medium">Period</label>
                    <input type="text" name="period" class="form-control" placeholder="e.g. September 2026">
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-medium">Start Date</label>
                    <input type="date" name="start_date" class="form-control">
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-medium">Due Date</label>
                    <input type="date" name="due_date" class="form-control">
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-medium">Section</label>
                    <select name="section_id" class="form-select">
                        <option value="">All Sections</option>
                        <?php foreach ($sections as $s): ?>
                            <option value="<?= $s['id'] ?>"><?= e($s['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-12">
                    <label class="form-label small fw-medium">Assign To</label>
                    <select name="applicable_to" class="form-select">
                        <option value="All Active Members">All Active Members</option>
                        <option value="Section Only">Section Only (selected above)</option>
                        <option value="Specific Members">Specific Members (select below)</option>
                    </select>
                </div>
                <div class="col-12" id="memberSelect" style="display:none;">
                    <label class="form-label small fw-medium">Select Members</label>
                    <select name="member_ids[]" class="form-select" multiple size="6">
                        <?php foreach ($members as $m): ?>
                            <option value="<?= $m['id'] ?>"><?= e($m['member_number'] . ' - ' . $m['first_name'] . ' ' . $m['last_name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <small class="text-muted">Hold Ctrl/Cmd to select multiple members.</small>
                </div>
                <div class="col-12">
                    <label class="form-label small fw-medium">Notes</label>
                    <textarea name="notes" class="form-control" rows="2"></textarea>
                </div>
            </div>
            <hr>
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">Create & Assign</button>
                <a href="<?= url('dues') ?>" class="btn btn-outline-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>

<script>
document.querySelector('select[name="applicable_to"]').addEventListener('change', function() {
    document.getElementById('memberSelect').style.display = this.value === 'Specific Members' ? 'block' : 'none';
});
</script>

<?php $content = ob_get_clean(); ?>
<?php require __DIR__ . '/../layouts/admin.php'; ?>
