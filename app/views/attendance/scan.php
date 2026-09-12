<?php $pageTitle = 'QR Code Attendance Scanner'; ob_start(); ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-0 text-gray-800">QR Code Attendance Scanner</h1>
        <p class="text-muted">Scan member digital cards using camera or barcode reader</p>
    </div>
    <a href="<?= url('attendance') ?>" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i> Back to Attendance
    </a>
</div>

<div class="row">
    <div class="col-lg-6 mb-4">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0"><i class="bi bi-camera me-2"></i>Live Camera Scanner</h5>
                <span class="badge bg-light text-primary" id="scannerStatus">Camera Ready</span>
            </div>
            <div class="card-body">
                <!-- Settings / Session Selector -->
                <div class="mb-3">
                    <label class="form-label font-weight-bold">Attendance Session</label>
                    <select id="sessionSelect" class="form-select mb-2">
                        <option value="0">-- Create New Session for Today --</option>
                        <?php foreach ($recentSessions as $sess): ?>
                            <option value="<?= $sess['id'] ?>">
                                <?= htmlspecialchars($sess['activity_name'] ?? 'Parade') ?> (<?= date('d M Y', strtotime($sess['date'])) ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="mb-3" id="activitySelectGroup">
                    <label class="form-label font-weight-bold">Activity (if creating new session)</label>
                    <select id="activitySelect" class="form-select">
                        <?php foreach ($activities as $act): ?>
                            <option value="<?= $act['id'] ?>"><?= htmlspecialchars($act['name']) ?> (<?= date('d M Y', strtotime($act['date'])) ?>)</option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label font-weight-bold">Default Mark Status</label>
                    <select id="statusSelect" class="form-select">
                        <option value="Present" selected>Present</option>
                        <option value="Late">Late</option>
                        <option value="Excused">Excused</option>
                    </select>
                </div>

                <!-- Camera viewport -->
                <div id="reader" style="width: 100%; min-height: 280px; background: #f8f9fa; border: 2px dashed #dee2e6; border-radius: 8px;" class="mb-3 d-flex align-items-center justify-content-center"></div>

                <div class="d-flex gap-2">
                    <button id="startScanBtn" class="btn btn-success flex-fill">
                        <i class="bi bi-camera-video me-1"></i> Start Camera
                    </button>
                    <button id="stopScanBtn" class="btn btn-secondary flex-fill" disabled>
                        <i class="bi bi-camera-video-off me-1"></i> Stop Camera
                    </button>
                </div>

                <hr class="my-4">

                <!-- Manual Input Fallback -->
                <form id="manualScanForm">
                    <label class="form-label font-weight-bold">Manual Entry (Member # or QR Token)</label>
                    <div class="input-group">
                        <input type="text" id="manualCode" class="form-control" placeholder="e.g. 21-24-001 or scan token..." required>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-lg me-1"></i> Mark
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Scanned Log Side -->
    <div class="col-lg-6 mb-4">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                <h5 class="card-title mb-0"><i class="bi bi-card-checklist me-2 text-primary"></i>Scanned Members Log</h5>
                <span class="badge bg-primary rounded-pill" id="scanCount">0</span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive" style="max-height: 480px; overflow-y: auto;">
                    <table class="table table-hover align-middle mb-0" id="scanLogTable">
                        <thead class="table-light">
                            <tr>
                                <th>Photo</th>
                                <th>Member Name</th>
                                <th>Member #</th>
                                <th>Section</th>
                                <th>Status</th>
                                <th>Time</th>
                            </tr>
                        </thead>
                        <tbody id="scanLogBody">
                            <tr id="emptyRow">
                                <td colspan="6" class="text-center py-4 text-muted">
                                    <i class="bi bi-qr-code-scan display-4 d-block mb-2 text-secondary"></i>
                                    No scans recorded in this session yet.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Load html5-qrcode library -->
<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    let html5QrCode = null;
    let isScanning = false;
    let lastScannedCode = '';
    let lastScannedTime = 0;
    let scanCount = 0;

    const startBtn = document.getElementById('startScanBtn');
    const stopBtn = document.getElementById('stopScanBtn');
    const statusBadge = document.getElementById('scannerStatus');
    const manualForm = document.getElementById('manualScanForm');
    const manualInput = document.getElementById('manualCode');

    // Toggle Activity select depending on session choice
    document.getElementById('sessionSelect').addEventListener('change', function() {
        const group = document.getElementById('activitySelectGroup');
        group.style.display = this.value === '0' ? 'block' : 'none';
    });

    startBtn.addEventListener('click', function() {
        if (isScanning) return;
        
        html5QrCode = new Html5Qrcode("reader");
        const config = { fps: 10, qrbox: { width: 250, height: 250 } };

        statusBadge.textContent = 'Starting Camera...';
        statusBadge.className = 'badge bg-warning text-dark';

        html5QrCode.start(
            { facingMode: "environment" },
            config,
            onScanSuccess,
            onScanFailure
        ).then(() => {
            isScanning = true;
            startBtn.disabled = true;
            stopBtn.disabled = false;
            statusBadge.textContent = 'Camera Active';
            statusBadge.className = 'badge bg-success';
        }).catch(err => {
            console.error("Camera start error: ", err);
            alert("Could not access camera: " + err + ". Please check permissions or use Manual Entry.");
            statusBadge.textContent = 'Camera Error';
            statusBadge.className = 'badge bg-danger';
        });
    });

    stopBtn.addEventListener('click', function() {
        if (!html5QrCode || !isScanning) return;
        html5QrCode.stop().then(() => {
            isScanning = false;
            startBtn.disabled = false;
            stopBtn.disabled = true;
            statusBadge.textContent = 'Camera Stopped';
            statusBadge.className = 'badge bg-secondary';
        });
    });

    function onScanSuccess(decodedText, decodedResult) {
        const now = Date.now();
        // Prevent duplicate scan spam within 3 seconds
        if (decodedText === lastScannedCode && (now - lastScannedTime) < 3000) {
            return;
        }
        lastScannedCode = decodedText;
        lastScannedTime = now;

        processCode(decodedText);
    }

    function onScanFailure(error) {
        // Quiet failure on frame scanner
    }

    manualForm.addEventListener('submit', function(e) {
        e.preventDefault();
        const code = manualInput.value.trim();
        if (code) {
            processCode(code);
            manualInput.value = '';
        }
    });

    function processCode(code) {
        const sessionId = document.getElementById('sessionSelect').value;
        const activityId = document.getElementById('activitySelect').value;
        const status = document.getElementById('statusSelect').value;

        const formData = new FormData();
        formData.append('code', code);
        formData.append('session_id', sessionId);
        formData.append('activity_id', activityId);
        formData.append('status', status);

        fetch('<?= url('attendance/api/scan-mark') ?>', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                playBeep();
                addScanToLog(data.member, data.already_marked);
            } else {
                alert('Scan Failed: ' + data.message);
            }
        })
        .catch(err => {
            console.error(err);
            alert('Error processing scan request.');
        });
    }

    function addScanToLog(member, isUpdate) {
        const emptyRow = document.getElementById('emptyRow');
        if (emptyRow) emptyRow.remove();

        scanCount++;
        document.getElementById('scanCount').textContent = scanCount;

        const tbody = document.getElementById('scanLogBody');
        const tr = document.createElement('tr');
        tr.className = isUpdate ? 'table-warning' : 'table-success';

        const timeStr = new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit', second: '2-digit' });
        const photoUrl = member.photo ? member.photo : 'https://via.placeholder.com/40';

        tr.innerHTML = `
            <td><img src="${photoUrl}" class="rounded-circle" width="36" height="36" alt="Member"></td>
            <td><strong>${escapeHtml(member.name)}</strong> <br><small class="text-muted">${escapeHtml(member.rank)}</small></td>
            <td><code>${escapeHtml(member.member_number)}</code></td>
            <td>${escapeHtml(member.section)}</td>
            <td><span class="badge bg-success">${escapeHtml(member.status)}</span></td>
            <td><small class="text-muted">${timeStr}</small></td>
        `;

        tbody.insertBefore(tr, tbody.firstChild);

        // Highlight flash effect
        setTimeout(() => {
            tr.className = '';
        }, 2000);
    }

    function playBeep() {
        try {
            const ctx = new (window.AudioContext || window.webkitAudioContext)();
            const osc = ctx.createOscillator();
            osc.type = 'sine';
            osc.frequency.setValueAtTime(880, ctx.currentTime); // A5 tone
            osc.connect(ctx.destination);
            osc.start();
            osc.stop(ctx.currentTime + 0.15);
        } catch (e) {
            // Audio context not allowed or unsupported
        }
    }

    function escapeHtml(str) {
        return (str || '').replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/"/g, "&quot;");
    }
});
</script>

<?php $content = ob_get_clean(); ?>
<?php require __DIR__ . '/../layouts/admin.php'; ?>
