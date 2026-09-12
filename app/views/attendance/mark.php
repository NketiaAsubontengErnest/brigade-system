<?php $pageTitle = 'Mark Attendance'; $layout = 'admin'; ob_start(); ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-0">Mark Attendance</h4>
        <small class="text-muted">Select member attendance status for parade or activity</small>
    </div>
    <a href="<?= url('attendance') ?>" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left me-1"></i> Back to Attendance
    </a>
</div>

<form method="POST" action="<?= url('attendance/mark') ?>" id="attendanceForm">
    <?= csrf_field() ?>

    <!-- Session & Event Configuration Card -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body p-3 bg-light rounded-3">
            <div class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label small fw-bold text-muted mb-1">Attendance Date *</label>
                    <input type="date" name="date" class="form-control" value="<?= date('Y-m-d') ?>" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label small fw-bold text-muted mb-1">Activity (Optional)</label>
                    <select name="activity_id" class="form-select">
                        <option value="0">-- General Parade / Meeting --</option>
                        <?php foreach ($activities as $a): ?>
                            <option value="<?= $a['id'] ?>"><?= e($a['name']) ?> (<?= formatDate($a['date']) ?>)</option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-bold text-muted mb-1">Filter by Section</label>
                    <select id="sectionFilter" class="form-select">
                        <option value="">All Sections</option>
                        <?php foreach ($sections as $s): ?>
                            <option value="<?= $s['id'] ?>"><?= e($s['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-bold text-muted mb-1">Quick Search</label>
                    <input type="text" id="memberSearch" class="form-control" placeholder="Search name or #...">
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Action Controls -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-sm btn-outline-success" onclick="markAll('Present')">
                <i class="bi bi-check-all me-1"></i> Mark All Present
            </button>
            <button type="button" class="btn btn-sm btn-outline-danger" onclick="markAll('Absent')">
                <i class="bi bi-x-lg me-1"></i> Mark All Absent
            </button>
        </div>
        <span class="text-muted small" id="visibleCount">Showing <?= count($members) ?> active members</span>
    </div>

    <!-- Members Attendance Table -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" id="membersTable">
                <thead class="table-light">
                    <tr>
                        <th style="width: 50px;">Photo</th>
                        <th>Member Details</th>
                        <th>Section</th>
                        <th style="width: 320px;" class="text-center">Attendance Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($members)): ?>
                        <tr>
                            <td colspan="4" class="text-center py-5 text-muted">
                                <i class="bi bi-people display-4 d-block mb-2"></i>
                                No active members found in the company.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($members as $m): ?>
                            <tr class="member-row" data-section="<?= $m['section_id'] ?>" data-search="<?= strtolower(e($m['first_name'] . ' ' . $m['last_name'] . ' ' . ($m['member_number'] ?? ''))) ?>">
                                <td>
                                    <?php if (!empty($m['profile_photo'])): ?>
                                        <img src="<?= asset($m['profile_photo']) ?>" class="rounded-circle" width="40" height="40" alt="Member">
                                    <?php else: ?>
                                        <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center fw-bold" style="width: 40px; height: 40px;">
                                            <?= strtoupper(substr($m['first_name'], 0, 1) . substr($m['last_name'], 0, 1)) ?>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="fw-bold text-dark"><?= e($m['first_name'] . ' ' . ($m['middle_name'] ? $m['middle_name'] . ' ' : '') . $m['last_name']) ?></div>
                                    <small class="text-muted"><code><?= e($m['member_number'] ?? 'No #') ?></code> <?= $m['rank'] ? '· ' . e($m['rank']) : '' ?></small>
                                </td>
                                <td>
                                    <span class="badge bg-secondary-subtle text-secondary"><?= e($m['section_name'] ?? 'Unassigned') ?></span>
                                </td>
                                <td>
                                    <div class="btn-group w-100" role="group" aria-label="Status select">
                                        <input type="radio" class="btn-check" name="attendance[<?= $m['id'] ?>]" id="status_p_<?= $m['id'] ?>" value="Present" checked>
                                        <label class="btn btn-outline-success btn-sm" for="status_p_<?= $m['id'] ?>">Present</label>

                                        <input type="radio" class="btn-check" name="attendance[<?= $m['id'] ?>]" id="status_a_<?= $m['id'] ?>" value="Absent">
                                        <label class="btn btn-outline-danger btn-sm" for="status_a_<?= $m['id'] ?>">Absent</label>

                                        <input type="radio" class="btn-check" name="attendance[<?= $m['id'] ?>]" id="status_e_<?= $m['id'] ?>" value="Excused">
                                        <label class="btn btn-outline-warning btn-sm" for="status_e_<?= $m['id'] ?>">Excused</label>

                                        <input type="radio" class="btn-check" name="attendance[<?= $m['id'] ?>]" id="status_l_<?= $m['id'] ?>" value="Late">
                                        <label class="btn btn-outline-info btn-sm" for="status_l_<?= $m['id'] ?>">Late</label>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="d-flex gap-2">
        <button type="submit" class="btn btn-primary px-4">
            <i class="bi bi-save me-1"></i> Save Attendance Record
        </button>
        <a href="<?= url('attendance') ?>" class="btn btn-outline-secondary">Cancel</a>
    </div>
</form>

<script>
function markAll(status) {
    const statusMap = { 'Present': 'p', 'Absent': 'a', 'Excused': 'e', 'Late': 'l' };
    const prefix = statusMap[status] || 'p';
    document.querySelectorAll('.member-row').forEach(row => {
        if (row.style.display !== 'none') {
            const radioId = row.querySelector(`input[id^="status_${prefix}_"]`);
            if (radioId) radioId.checked = true;
        }
    });
}

document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('memberSearch');
    const sectionSelect = document.getElementById('sectionFilter');
    const rows = document.querySelectorAll('.member-row');
    const visibleCount = document.getElementById('visibleCount');

    function filterMembers() {
        const query = searchInput.value.toLowerCase().trim();
        const sectionId = sectionSelect.value;
        let count = 0;

        rows.forEach(row => {
            const matchSearch = !query || row.dataset.search.includes(query);
            const matchSection = !sectionId || row.dataset.section === sectionId;

            if (matchSearch && matchSection) {
                row.style.display = '';
                count++;
            } else {
                row.style.display = 'none';
            }
        });

        visibleCount.textContent = `Showing ${count} active member${count === 1 ? '' : 's'}`;
    }

    searchInput.addEventListener('input', filterMembers);
    sectionSelect.addEventListener('change', filterMembers);
});
</script>

<?php $content = ob_get_clean(); ?>
<?php require __DIR__ . '/../layouts/admin.php'; ?>
