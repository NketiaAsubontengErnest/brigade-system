<!DOCTYPE html>
<html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>Member Verification</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.css" rel="stylesheet"></head>
<body class="bg-light d-flex align-items-center justify-content-center min-vh-100">
<div class="card shadow-sm border-0" style="max-width:400px;width:100%;border-radius:16px">
    <div class="card-body p-4 text-center">
        <div class="bg-success bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width:60px;height:60px"><i class="bi bi-shield-check text-success fs-3"></i></div>
        <h5 class="fw-bold">Member Verified</h5>
        <hr>
        <p class="small text-muted">This member is registered with:</p>
        <h4 class="fw-bold"><?= e(($profile['company_name'] ?? '21st and 24th Accra Boys and Girls Brigade')) ?></h4>
        <?php if (!empty($profile['logo'])): ?><img src="<?= e(upload_url($profile['logo'])) ?>" style="max-height:50px" class="my-2"><?php endif; ?>
        <hr>
        <table class="table table-sm small mb-0"><tbody>
            <tr><td class="text-muted">Name</td><td class="fw-bold"><?= e($member['first_name'] . ' ' . $member['last_name']) ?></td></tr>
            <tr><td class="text-muted">Number</td><td><?= e($member['member_number'] ?? 'N/A') ?></td></tr>
            <tr><td class="text-muted">Section</td><td><?= e($member['section_name'] ?? 'N/A') ?></td></tr>
            <tr><td class="text-muted">Status</td><td><span class="badge bg-<?= $member['status'] === 'Active' ? 'success' : 'secondary' ?>"><?= $member['status'] ?></span></td></tr>
        </tbody></table>
        <p class="text-muted small mt-3 mb-0"><i class="bi bi-info-circle me-1"></i>Only basic verification information is shown.</p>
    </div>
</div>
</body></html>
