<?php
/**
 * Parent / guardian fields, shared by the member create and edit forms.
 *
 * Expects:
 *   $guardians   array of existing guardian rows (empty on create)
 *   $sections    active sections, used to auto-open the block for junior sections
 *   $currentSectionId  the member's section id, if any
 *
 * Every field here is optional. A member can be saved with no guardian at all;
 * rows left blank are simply ignored.
 */
$guardians = $guardians ?? [];
$sections = $sections ?? [];
$currentSectionId = $currentSectionId ?? null;

// Render every guardian on file, plus one blank slot to add another.
$slots = $guardians;
$slots[] = [];
if (count($slots) < 2) {
    $slots[] = [];
}

// Sections flagged as Junior in settings - the form opens automatically for these
$juniorSectionIds = [];
foreach ($sections as $s) {
    if (strcasecmp((string)($s['type'] ?? ''), 'Junior') === 0) {
        $juniorSectionIds[] = (int)$s['id'];
    }
}
$openByDefault = true; // Always open by default so parent details can be easily added (optional)
?>

<div class="col-12 mt-3">
    <div class="d-flex align-items-center justify-content-between border-bottom pb-2">
        <h6 class="fw-bold text-muted mb-0">
            Parent / Guardian
            <span class="badge bg-secondary-subtle text-secondary fw-normal ms-1">Optional</span>
        </h6>
        <button type="button" class="btn btn-sm btn-outline-secondary" id="toggleGuardians">
            <i class="bi bi-chevron-down me-1"></i><span>Show</span>
        </button>
    </div>
    <p class="text-muted small mt-2 mb-0" id="guardianHint">
        Recommended for members in a junior section. Leave blank if not applicable.
    </p>
</div>

<div class="col-12" id="guardianFields" style="display: <?= $openByDefault ? 'block' : 'none' ?>;">
    <?php foreach ($slots as $i => $g): ?>
        <div class="border rounded-3 p-3 mb-3 bg-light-subtle">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="small fw-bold text-muted">
                    <?= $i === 0 ? 'Primary Parent / Guardian' : 'Additional Parent / Guardian' ?>
                </span>
                <?php if (!empty($g['full_name'])): ?>
                    <span class="badge bg-success-subtle text-success">On file</span>
                <?php endif; ?>
            </div>
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label small fw-medium">Full Name</label>
                    <input type="text" name="guardians[<?= $i ?>][full_name]" class="form-control"
                           value="<?= e($g['full_name'] ?? '') ?>" placeholder="e.g. Mary Asante">
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-medium">Relationship</label>
                    <select name="guardians[<?= $i ?>][relationship]" class="form-select">
                        <option value="">Select...</option>
                        <?php foreach (['Mother', 'Father', 'Guardian', 'Grandparent', 'Sibling', 'Other'] as $rel): ?>
                            <option value="<?= $rel ?>" <?= ($g['relationship'] ?? '') === $rel ? 'selected' : '' ?>><?= $rel ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-medium">Phone</label>
                    <input type="tel" name="guardians[<?= $i ?>][phone]" class="form-control"
                           value="<?= e($g['phone'] ?? '') ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-medium">Email</label>
                    <input type="email" name="guardians[<?= $i ?>][email]" class="form-control"
                           value="<?= e($g['email'] ?? '') ?>">
                </div>
                <div class="col-md-8">
                    <label class="form-label small fw-medium">Address</label>
                    <input type="text" name="guardians[<?= $i ?>][address]" class="form-control"
                           value="<?= e($g['address'] ?? '') ?>">
                </div>
                <div class="col-md-4 d-flex align-items-end gap-3 pb-2">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" value="1"
                               id="guardianPrimary<?= $i ?>" name="guardians[<?= $i ?>][is_primary]"
                            <?= !empty($g['is_primary']) ? 'checked' : ($i === 0 && empty($guardians) ? 'checked' : '') ?>>
                        <label class="form-check-label small" for="guardianPrimary<?= $i ?>">Primary</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" value="1"
                               id="guardianEmergency<?= $i ?>" name="guardians[<?= $i ?>][emergency_contact]"
                            <?= !empty($g['emergency_contact']) ? 'checked' : '' ?>>
                        <label class="form-check-label small" for="guardianEmergency<?= $i ?>">Emergency contact</label>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
    <p class="text-muted small mb-0">
        <i class="bi bi-info-circle me-1"></i>Leave a block empty to skip it. Clearing a name removes that guardian on save.
    </p>
</div>

<script>
(function () {
    var toggle = document.getElementById('toggleGuardians');
    var fields = document.getElementById('guardianFields');
    var sectionSelect = document.querySelector('select[name="section_id"]');
    var juniorSections = <?= json_encode($juniorSectionIds) ?>;

    function setOpen(open) {
        fields.style.display = open ? 'block' : 'none';
        toggle.querySelector('span').textContent = open ? 'Hide' : 'Show';
        toggle.querySelector('i').className = open ? 'bi bi-chevron-up me-1' : 'bi bi-chevron-down me-1';
    }

    setOpen(fields.style.display !== 'none');
    toggle.addEventListener('click', function () {
        setOpen(fields.style.display === 'none');
    });

    // Opening a junior section reveals the guardian block automatically
    if (sectionSelect) {
        sectionSelect.addEventListener('change', function () {
            if (juniorSections.indexOf(parseInt(this.value, 10)) !== -1) {
                setOpen(true);
            }
        });
    }
})();
</script>
