<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;
use App\Core\Validator;
use App\Core\Auth;

class MemberController extends Controller
{
    private Database $db;

    public function __construct()
    {
        parent::__construct();
        $this->db = Database::getInstance();
    }

    public function index(): void
    {
        $this->authorize('members.view');
        $request = new \App\Core\Request();
        $search = $request->input('search', '');
        $section = $request->input('section', '');
        $status = $request->input('status', '');
        $page = max(1, (int)$request->input('page', 1));

        $where = '1=1';
        $params = [];

        if ($search) {
            $where .= "  AND (LOWER(m.first_name) LIKE LOWER(:search1) OR LOWER(m.last_name) LIKE LOWER(:search2) OR LOWER(m.member_number) LIKE LOWER(:search3) OR LOWER(m.phone) LIKE LOWER(:search4))";
            $params['search1'] = $params['search2'] = $params['search3'] = $params['search4'] = "%{$search}%";
        }
        if ($section) {
            $where .= " AND m.section_id = :section";
            $params['section'] = $section;
        }
        if ($status) {
            $where .= " AND m.status = :status";
            $params['status'] = $status;
        }

        $total = (int)$this->db->fetchOne(
            "SELECT COUNT(*) as c FROM members m WHERE {$where}", $params
        )['c'];

        $pagination = $this->paginate($total, 20, $page);
        $params['limit'] = $pagination['per_page'];
        $params['offset'] = $pagination['offset'];

        $members = $this->db->fetchAll(
            "SELECT m.*, s.name as section_name 
             FROM members m 
             LEFT JOIN sections s ON s.id = m.section_id
             WHERE {$where}
             ORDER BY m.last_name, m.first_name
             LIMIT :limit OFFSET :offset",
            $params
        );

        $sections = $this->db->fetchAll("SELECT * FROM sections WHERE status = 'Active' ORDER BY name");

        $this->setMenuActive('members');
        $this->view('members.index', [
            'members' => $members,
            'sections' => $sections,
            'pagination' => $pagination,
            'search' => $search,
            'filterSection' => $section,
            'filterStatus' => $status,
            'total' => $total,
        ]);
    }

    public function create(): void
    {
        $this->authorize('members.create');
        $sections = $this->db->fetchAll("SELECT * FROM sections WHERE status = 'Active' ORDER BY name");
        $positions = $this->db->fetchAll("SELECT * FROM positions WHERE status = 'Active' ORDER BY name");
        $ranks = $this->db->fetchAll("SELECT * FROM ranks WHERE status = 'Active' ORDER BY level ASC, name ASC");
        $this->setMenuActive('members');
        $this->view('members.create', ['sections' => $sections, 'positions' => $positions, 'ranks' => $ranks]);
    }

    public function store(): void
    {
        $this->authorize('members.create');
        $this->verifyCsrf();

        $request = new \App\Core\Request();
        $data = $request->only([
            'first_name', 'middle_name', 'last_name', 'date_of_birth', 'gender',
            'phone', 'email', 'address', 'section_id', 'rank', 'date_joined', 'notes',
            'is_officer', 'position_id', 'rank_id',
        ]);
        $data = array_map(fn($v) => is_string($v) ? trim($v) : $v, $data);

        $validator = new Validator();
        if (!$validator->validate($data, [
            'first_name' => 'required|max:100',
            'last_name' => 'required|max:100',
            'gender' => 'required|in:Male,Female',
            'section_id' => 'required',
        ])) {
            Validator::flashInput($data);
            $this->redirect('/members/create', 'Please correct the errors below.', 'danger');
            return;
        }

        try {
            $memberNumber = generateMemberNumber();
            $photo = null;
            if ($file = $request->file('profile_photo')) {
                if ($file['error'] === UPLOAD_ERR_OK) {
                    $photo = uploadFile($file, 'profiles', ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                }
            }

            $this->db->execute(
                "INSERT INTO members (member_number, first_name, middle_name, last_name, date_of_birth, gender, phone, email, address, section_id, rank, date_joined, status, notes, profile_photo, is_officer, position_id, rank_id)
                 VALUES (:num, :first, :middle, :last, :dob, :gender, :phone, :email, :address, :section, :rank, :joined, 'Active', :notes, :photo, :is_officer, :position_id, :rank_id)",
                [
                    'num' => $memberNumber, 'first' => $data['first_name'],
                    'middle' => $data['middle_name'] ?? null, 'last' => $data['last_name'],
                    'dob' => $data['date_of_birth'] ?? null, 'gender' => $data['gender'],
                    'phone' => $data['phone'] ?? null, 'email' => $data['email'] ?? null,
                    'address' => $data['address'] ?? null, 'section' => $data['section_id'],
                    'rank' => $data['rank'] ?? null, 'joined' => $data['date_joined'] ?? date('Y-m-d'),
                    'notes' => $data['notes'] ?? null, 'photo' => $photo,
                    'is_officer' => !empty($data['is_officer']) ? 1 : 0,
                    'position_id' => $data['position_id'] ?: null,
                    'rank_id' => $data['rank_id'] ?: null,
                ]
            );
            $memberId = (int)$this->db->lastInsertId();

            // If member is an officer, also create an officers record
            if (!empty($data['is_officer']) && !empty($data['position_id'])) {
                $this->db->execute(
                    "INSERT INTO officers (member_id, position_id, start_date, status) VALUES (:m, :p, CURRENT_DATE, 'Active')",
                    ['m' => $memberId, 'p' => $data['position_id']]
                );
            }

            // Parent/guardian details - optional
            $guardians = $request->input('guardians');
            if (is_array($guardians)) {
                $this->saveGuardians($memberId, $guardians);
            }

            (new Auth())->logAction('member_created', 'member', $memberId, "Created member {$data['first_name']} {$data['last_name']}");
            $this->redirect('/members', 'Member created successfully with number: ' . $memberNumber, 'success');
        } catch (\Throwable $e) {
            error_log('Member create error: ' . $e->getMessage());
            $this->redirect('/members/create', 'An error occurred. Please try again.', 'danger');
        }
    }

    public function show(string $id): void
    {
        $this->authorize('members.view');
        $member = $this->db->fetchOne(
            "SELECT m.*, s.name as section_name, rk.name as rank_name, p.name as position_name
             FROM members m
             LEFT JOIN sections s ON s.id = m.section_id
             LEFT JOIN ranks rk ON rk.id = m.rank_id
             LEFT JOIN positions p ON p.id = m.position_id
             WHERE m.id = :id",
            ['id' => (int)$id]
        );
        if (!$member) {
            $this->abort(404, 'Member not found');
        }

        $guardians = $this->db->fetchAll(
            "SELECT * FROM guardians WHERE member_id = :id", ['id' => (int)$id]
        );
        $dues = $this->db->fetchAll(
            "SELECT md.*, d.name as dues_name FROM member_dues md 
             JOIN dues d ON d.id = md.dues_id WHERE md.member_id = :id ORDER BY md.created_at DESC",
            ['id' => (int)$id]
        );
        $payments = $this->db->fetchAll(
            "SELECT * FROM payments WHERE member_id = :id ORDER BY payment_date DESC LIMIT 10",
            ['id' => (int)$id]
        );
        $attendance = $this->db->fetchAll(
            "SELECT a.*, ats.date, ats.activity_id FROM attendance a
             JOIN attendance_sessions ats ON ats.id = a.session_id
             WHERE a.member_id = :id ORDER BY ats.date DESC LIMIT 10",
            ['id' => (int)$id]
        );
        $badges = $this->db->fetchAll(
            "SELECT mb.*, b.name as badge_name FROM member_badges mb
             JOIN badges b ON b.id = mb.badge_id WHERE mb.member_id = :id",
            ['id' => (int)$id]
        );
        $awards = $this->db->fetchAll(
            "SELECT * FROM awards WHERE member_id = :id ORDER BY date_awarded DESC",
            ['id' => (int)$id]
        );

        $this->setMenuActive('members');
        $this->view('members.show', [
            'member' => $member,
            'guardians' => $guardians,
            'dues' => $dues,
            'payments' => $payments,
            'attendance' => $attendance,
            'badges' => $badges,
            'awards' => $awards,
        ]);
    }

    public function edit(string $id): void
    {
        $this->authorize('members.edit');
        $member = $this->db->fetchOne("SELECT * FROM members WHERE id = :id", ['id' => (int)$id]);
        if (!$member) {
            $this->abort(404, 'Member not found');
        }
        $sections = $this->db->fetchAll("SELECT * FROM sections WHERE status = 'Active' ORDER BY name");
        $positions = $this->db->fetchAll("SELECT * FROM positions WHERE status = 'Active' ORDER BY name");
        $ranks = $this->db->fetchAll("SELECT * FROM ranks WHERE status = 'Active' ORDER BY level ASC, name ASC");
        $guardians = $this->db->fetchAll(
            "SELECT * FROM guardians WHERE member_id = :id ORDER BY is_primary DESC, id ASC",
            ['id' => (int)$id]
        );

        $this->setMenuActive('members');
        $this->view('members.edit', [
            'member' => $member,
            'sections' => $sections,
            'positions' => $positions,
            'ranks' => $ranks,
            'guardians' => $guardians,
        ]);
    }

    /**
     * Save the parent/guardian rows submitted with a member form.
     *
     * Guardians are entirely optional: rows with no name are skipped, and a
     * member with no guardians at all is perfectly valid. The submitted set
     * replaces what is on file, which is why the edit form renders every
     * existing guardian back to the user.
     */
    private function saveGuardians(int $memberId, array $guardians): void
    {
        $rows = [];
        foreach ($guardians as $g) {
            $name = trim((string)($g['full_name'] ?? ''));
            if ($name === '') {
                continue; // blank slot - nothing to save
            }
            $rows[] = [
                'full_name' => $name,
                'relationship' => trim((string)($g['relationship'] ?? '')) ?: null,
                'phone' => trim((string)($g['phone'] ?? '')) ?: null,
                'email' => trim((string)($g['email'] ?? '')) ?: null,
                'address' => trim((string)($g['address'] ?? '')) ?: null,
                'is_primary' => !empty($g['is_primary']) ? 1 : 0,
                'emergency_contact' => !empty($g['emergency_contact']) ? 1 : 0,
            ];
        }

        // Fall back to marking the first guardian primary if none was chosen
        if ($rows && !array_filter(array_column($rows, 'is_primary'))) {
            $rows[0]['is_primary'] = 1;
        }

        $this->db->execute("DELETE FROM guardians WHERE member_id = :id", ['id' => $memberId]);

        foreach ($rows as $row) {
            $this->db->execute(
                "INSERT INTO guardians (member_id, full_name, relationship, phone, email, address, is_primary, emergency_contact)
                 VALUES (:member_id, :full_name, :relationship, :phone, :email, :address, :is_primary, :emergency_contact)",
                ['member_id' => $memberId] + $row
            );
        }
    }

    public function update(string $id): void
    {
        $this->authorize('members.edit');
        $this->verifyCsrf();

        $request = new \App\Core\Request();
        $data = $request->only([
            'first_name', 'middle_name', 'last_name', 'date_of_birth', 'gender',
            'phone', 'email', 'address', 'section_id', 'status', 'notes',
            'rank_id', 'is_officer', 'position_id',
        ]);
        $data = array_map(fn($v) => is_string($v) ? trim($v) : $v, $data);

        // Optional foreign keys: an empty select means "none", not 0
        $data['rank_id'] = ($data['rank_id'] ?? '') !== '' ? (int)$data['rank_id'] : null;
        $data['position_id'] = ($data['position_id'] ?? '') !== '' ? (int)$data['position_id'] : null;
        $data['is_officer'] = !empty($data['is_officer']) ? 1 : 0;
        if (!$data['is_officer']) {
            $data['position_id'] = null;
        }

        $validator = new Validator();
        if (!$validator->validate($data, [
            'first_name' => 'required|max:100',
            'last_name' => 'required|max:100',
            'gender' => 'required|in:Male,Female',
        ])) {
            Validator::flashInput($data);
            $this->redirect("/members/{$id}/edit", 'Please correct the errors.', 'danger');
            return;
        }

        try {
            $this->db->beginTransaction();

            // Handle photo upload
            if ($file = $request->file('profile_photo')) {
                if ($file['error'] === UPLOAD_ERR_OK) {
                    $photo = uploadFile($file, 'profiles', ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                    if ($photo) {
                        $data['profile_photo'] = $photo;
                    }
                }
            }

            $setClauses = [];
            $bindings = ['id' => (int)$id];
            foreach ($data as $key => $value) {
                $setClauses[] = "{$key} = :{$key}";
                $bindings[$key] = $value;
            }
            $setClauses[] = "updated_at = NOW()";

            $this->db->execute(
                "UPDATE members SET " . implode(', ', $setClauses) . " WHERE id = :id",
                $bindings
            );

            // Keep the officers roster in step with the officer flag.
            // officers.member_id is not unique, so update in place when a row
            // already exists rather than relying on ON DUPLICATE KEY.
            if ($data['is_officer'] && $data['position_id']) {
                $existing = $this->db->fetchOne(
                    "SELECT id FROM officers WHERE member_id = :m ORDER BY status = 'Active' DESC, id DESC LIMIT 1",
                    ['m' => (int)$id]
                );
                if ($existing) {
                    $this->db->execute(
                        "UPDATE officers SET position_id = :p, status = 'Active', end_date = NULL, updated_at = NOW() WHERE id = :oid",
                        ['p' => $data['position_id'], 'oid' => (int)$existing['id']]
                    );
                } else {
                    $this->db->execute(
                        "INSERT INTO officers (member_id, position_id, start_date, status)
                         VALUES (:m, :p, CURRENT_DATE, 'Active')",
                        ['m' => (int)$id, 'p' => $data['position_id']]
                    );
                }
            } elseif (!$data['is_officer']) {
                $this->db->execute(
                    "UPDATE officers SET status = 'Inactive' WHERE member_id = :m AND status = 'Active'",
                    ['m' => (int)$id]
                );
            }

            // Parent/guardian details - optional, only touched when submitted
            $guardians = $request->input('guardians');
            if (is_array($guardians)) {
                $this->saveGuardians((int)$id, $guardians);
            }

            (new Auth())->logAction('member_updated', 'member', (int)$id, "Updated member #{$id}");
            $this->db->commit();
            $this->redirect("/members/{$id}", 'Member updated successfully.', 'success');
        } catch (\Throwable $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            error_log('Member update error: ' . $e->getMessage());
            $this->redirect("/members/{$id}/edit", 'An error occurred.', 'danger');
        }
    }

    public function pending(): void
    {
        $this->authorize('members.approve');
        $members = $this->db->fetchAll(
            "SELECT m.*, s.name as section_name 
             FROM members m 
             LEFT JOIN sections s ON s.id = m.section_id
             WHERE m.status = 'Pending' ORDER BY m.created_at DESC"
        );
        $this->setMenuActive('members');
        $this->view('members.pending', ['members' => $members]);
    }

    public function approve(string $id): void
    {
        $this->authorize('members.approve');
        $this->verifyCsrf();

        $member = $this->db->fetchOne("SELECT * FROM members WHERE id = :id AND status = 'Pending'", ['id' => (int)$id]);
        if (!$member) {
            $this->redirect('/members/pending', 'Member not found or already processed.', 'warning');
            return;
        }

        try {
            $this->db->beginTransaction();

            $memberNumber = generateMemberNumber();
            $this->db->execute(
                "UPDATE members SET status = 'Active', member_number = :num, updated_at = NOW() WHERE id = :id",
                ['num' => $memberNumber, 'id' => (int)$id]
            );

            // Notify the member
            $this->db->execute(
                "INSERT INTO notifications (user_id, title, message, type) VALUES (NULL, 'Member Approved', :msg, 'success')",
                ['msg' => "Member {$member['first_name']} {$member['last_name']} has been approved with number: {$memberNumber}"]
            );

            (new Auth())->logAction('member_approved', 'member', (int)$id, "Approved member, assigned number: {$memberNumber}");

            $this->db->commit();
            $this->redirect('/members/pending', "Member approved! Member Number: {$memberNumber}", 'success');
        } catch (\Throwable $e) {
            $this->db->rollBack();
            $this->redirect('/members/pending', 'Error approving member.', 'danger');
        }
    }

    public function reject(string $id): void
    {
        $this->authorize('members.approve');
        $this->verifyCsrf();

        $this->db->execute(
            "UPDATE members SET status = 'Former', updated_at = NOW() WHERE id = :id AND status = 'Pending'",
            ['id' => (int)$id]
        );

        (new Auth())->logAction('member_rejected', 'member', (int)$id, "Rejected member registration");
        $this->redirect('/members/pending', 'Member registration rejected.', 'warning');
    }

    public function deactivate(string $id): void
    {
        $this->authorize('members.edit');
        $this->verifyCsrf();

        $this->db->execute(
            "UPDATE members SET status = 'Inactive', updated_at = NOW() WHERE id = :id",
            ['id' => (int)$id]
        );

        (new Auth())->logAction('member_deactivated', 'member', (int)$id, "Deactivated member");
        $this->redirect("/members/{$id}", 'Member deactivated.', 'warning');
    }

    public function delete(string $id): void
    {
        $this->authorize('members.delete');
        $this->verifyCsrf();

        // Remove related records
        $this->db->execute("DELETE FROM attendance WHERE member_id = :id", ['id' => (int)$id]);
        $this->db->execute("DELETE FROM member_dues WHERE member_id = :id", ['id' => (int)$id]);
        $this->db->execute("DELETE FROM payments WHERE member_id = :id", ['id' => (int)$id]);
        $this->db->execute("DELETE FROM member_badges WHERE member_id = :id", ['id' => (int)$id]);
        $this->db->execute("DELETE FROM training_enrollments WHERE member_id = :id", ['id' => (int)$id]);
        $this->db->execute("DELETE FROM event_registrations WHERE member_id = :id", ['id' => (int)$id]);
        $this->db->execute("DELETE FROM officers WHERE member_id = :id", ['id' => (int)$id]);
        $this->db->execute("DELETE FROM guardians WHERE member_id = :id", ['id' => (int)$id]);
        $this->db->execute("DELETE FROM members WHERE id = :id", ['id' => (int)$id]);

        (new Auth())->logAction('member_deleted', 'member', (int)$id, "Deleted member");
        $this->redirect('/members', 'Member deleted permanently.', 'warning');
    }

    public function membershipCard(string $id): void
    {
        $this->authorize('members.view');
        $member = $this->db->fetchOne(
            "SELECT m.*, s.name as section_name, r.name as rank_name, p.name as position_name 
             FROM members m 
             LEFT JOIN sections s ON s.id = m.section_id
             LEFT JOIN ranks r ON r.id = m.rank_id
             LEFT JOIN positions p ON p.id = m.position_id
             WHERE m.id = :id",
            ['id' => (int)$id]
        );
        if (!$member) {
            $this->abort(404, 'Member not found');
        }
        $profile = $this->db->fetchOne("SELECT * FROM company_profile LIMIT 1") ?? [];
        $this->view('members.card', ['member' => $member, 'profile' => $profile]);
    }

    public function search(): void
    {
        $request = new \App\Core\Request();
        $q = $request->input('q', '');

        if (strlen($q) < 2) {
            $this->json([]);
            return;
        }

        $members = $this->db->fetchAll(
            "SELECT id, member_number, first_name, last_name, phone 
             FROM members 
             WHERE status = 'Active' 
             AND (LOWER(first_name) LIKE LOWER(:q1) OR LOWER(last_name) LIKE LOWER(:q2) OR LOWER(member_number) LIKE LOWER(:q3) OR LOWER(phone) LIKE LOWER(:q4))
             ORDER BY first_name LIMIT 20",
            ['q1' => "%{$q}%", 'q2' => "%{$q}%", 'q3' => "%{$q}%", 'q4' => "%{$q}%"]
        );

        $this->json($members);
    }

    public function importForm(): void
    {
        $this->authorize('members.create');
        $sections = $this->db->fetchAll("SELECT * FROM sections WHERE status = 'Active' ORDER BY name");
        $this->setMenuActive('members');
        $this->view('members.import', ['sections' => $sections, 'previewData' => $_SESSION['csv_import_preview'] ?? null]);
    }

    public function importTemplate(): void
    {
        $this->authorize('members.create');
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=brigade_members_import_template.csv');

        $output = fopen('php://output', 'w');
        fputcsv($output, [
            'first_name', 'middle_name', 'last_name', 'gender', 'date_of_birth',
            'phone', 'email', 'address', 'section', 'rank',
            'guardian_name', 'guardian_relationship', 'guardian_phone', 'guardian_email'
        ]);
        fputcsv($output, [
            'John', 'Kofi', 'Doe', 'Male', '2010-05-15',
            '0240000000', 'john.doe@example.com', 'Accra, Ghana', 'Junior Section', 'Lance Corporal',
            'Mary Doe', 'Mother', '0241111111', 'mary.doe@example.com'
        ]);
        fputcsv($output, [
            'Sarah', '', 'Mensah', 'Female', '2012-08-20',
            '0270000000', 'sarah.m@example.com', 'Osu, Accra', 'Company Section', 'Member',
            'David Mensah', 'Father', '0272222222', 'david.m@example.com'
        ]);
        fclose($output);
        exit;
    }

    public function importPreview(): void
    {
        $this->authorize('members.create');
        $this->verifyCsrf();

        $request = new \App\Core\Request();
        $file = $request->file('csv_file');

        if (!$file || $file['error'] !== UPLOAD_ERR_OK) {
            $this->redirect('/members/import', 'Please select a valid CSV file.', 'danger');
            return;
        }

        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        if (strtolower($extension) !== 'csv') {
            $this->redirect('/members/import', 'File must be a CSV format.', 'danger');
            return;
        }

        $handle = fopen($file['tmp_name'], 'r');
        if (!$handle) {
            $this->redirect('/members/import', 'Failed to read uploaded CSV file.', 'danger');
            return;
        }

        $headers = fgetcsv($handle);
        if (!$headers) {
            fclose($handle);
            $this->redirect('/members/import', 'CSV file is empty.', 'danger');
            return;
        }

        // Clean headers
        $headers = array_map(fn($h) => strtolower(trim(preg_replace('/[\x00-\x1F\x7F-\xFF]/', '', $h))), $headers);

        $sections = $this->db->fetchAll("SELECT * FROM sections");
        $sectionMap = [];
        foreach ($sections as $sec) {
            $sectionMap[strtolower($sec['name'])] = (int)$sec['id'];
        }

        $rows = [];
        $validCount = 0;
        $invalidCount = 0;

        while (($data = fgetcsv($handle)) !== false) {
            if (count($data) < 2) continue; // skip blank rows
            $row = [];
            foreach ($headers as $index => $key) {
                $row[$key] = isset($data[$index]) ? trim($data[$index]) : '';
            }

            $errors = [];
            if (empty($row['first_name'])) $errors[] = 'First name missing';
            if (empty($row['last_name'])) $errors[] = 'Last name missing';

            $gender = ucfirst(strtolower($row['gender'] ?? ''));
            if (!in_array($gender, ['Male', 'Female'])) {
                $errors[] = 'Gender must be Male or Female';
            }

            $secName = strtolower($row['section'] ?? '');
            $secId = $sectionMap[$secName] ?? null;
            if (!$secId) {
                // Pick default section if available
                $firstSec = current($sections);
                $secId = $firstSec ? (int)$firstSec['id'] : null;
                if (!$secId) $errors[] = 'Unknown section: ' . ($row['section'] ?? '');
            }

            $isValid = empty($errors);
            if ($isValid) $validCount++; else $invalidCount++;

            $rows[] = [
                'first_name' => $row['first_name'] ?? '',
                'middle_name' => $row['middle_name'] ?? '',
                'last_name' => $row['last_name'] ?? '',
                'gender' => $gender,
                'date_of_birth' => !empty($row['date_of_birth']) ? date('Y-m-d', strtotime($row['date_of_birth'])) : null,
                'phone' => $row['phone'] ?? '',
                'email' => $row['email'] ?? '',
                'address' => $row['address'] ?? '',
                'section_id' => $secId,
                'section_name' => $row['section'] ?? 'Default',
                'rank' => $row['rank'] ?? 'Member',
                'guardian_name' => $row['guardian_name'] ?? '',
                'guardian_relationship' => $row['guardian_relationship'] ?? 'Parent',
                'guardian_phone' => $row['guardian_phone'] ?? '',
                'guardian_email' => $row['guardian_email'] ?? '',
                'is_valid' => $isValid,
                'errors' => implode(', ', $errors),
            ];
        }

        fclose($handle);

        $_SESSION['csv_import_data'] = $rows;
        $_SESSION['csv_import_preview'] = [
            'total' => count($rows),
            'valid' => $validCount,
            'invalid' => $invalidCount,
        ];

        $this->redirect('/members/import');
    }

    public function importProcess(): void
    {
        $this->authorize('members.create');
        $this->verifyCsrf();

        $rows = $_SESSION['csv_import_data'] ?? [];
        if (empty($rows)) {
            $this->redirect('/members/import', 'No pending CSV import session found.', 'danger');
            return;
        }

        $imported = 0;

        try {
            $this->db->beginTransaction();

            foreach ($rows as $row) {
                if (!$row['is_valid']) continue;

                $memberNum = generateMemberNumber();
                $token = bin2hex(random_bytes(16));

                $this->db->execute(
                    "INSERT INTO members (member_number, first_name, middle_name, last_name, gender, date_of_birth, phone, email, address, section_id, rank, status, verification_token, date_joined)
                     VALUES (:num, :first, :middle, :last, :gender, :dob, :phone, :email, :addr, :sec, :rank, 'Active', :tok, CURRENT_DATE)",
                    [
                        'num' => $memberNum,
                        'first' => $row['first_name'],
                        'middle' => $row['middle_name'] ?: null,
                        'last' => $row['last_name'],
                        'gender' => $row['gender'],
                        'dob' => $row['date_of_birth'] ?: null,
                        'phone' => $row['phone'] ?: null,
                        'email' => $row['email'] ?: null,
                        'addr' => $row['address'] ?: null,
                        'sec' => $row['section_id'],
                        'rank' => $row['rank'] ?: 'Member',
                        'tok' => $token,
                    ]
                );

                $memberId = (int)$this->db->lastInsertId();

                // Add Guardian if present
                if (!empty($row['guardian_name'])) {
                    $this->db->execute(
                        "INSERT INTO guardians (member_id, full_name, relationship, phone, email, is_primary)
                         VALUES (:mid, :gname, :rel, :gphone, :gemail, 1)",
                        [
                            'mid' => $memberId,
                            'gname' => $row['guardian_name'],
                            'rel' => $row['guardian_relationship'] ?: 'Parent',
                            'gphone' => $row['guardian_phone'] ?: null,
                            'gemail' => $row['guardian_email'] ?: null,
                        ]
                    );
                }

                $imported++;
            }

            $this->db->commit();

            unset($_SESSION['csv_import_data']);
            unset($_SESSION['csv_import_preview']);

            (new Auth())->logAction('bulk_members_imported', 'member', 0, "Bulk imported {$imported} members via CSV");

            $this->redirect('/members', "Successfully imported {$imported} members!", 'success');
        } catch (\Throwable $e) {
            $this->db->rollBack();
            error_log('CSV import error: ' . $e->getMessage());
            $this->redirect('/members/import', 'Failed to process member import. Error: ' . $e->getMessage(), 'danger');
        }
    }
}

