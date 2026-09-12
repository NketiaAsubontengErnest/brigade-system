<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;
use App\Core\Auth;

class AttendanceController extends Controller
{
    private Database $db;

    public function __construct()
    {
        parent::__construct();
        $this->authorize('attendance.view');
        $this->db = Database::getInstance();
    }

    public function index(): void
    {
        $request = new \App\Core\Request();
        $page = max(1, (int)$request->input('page', 1));

        $total = (int)$this->db->fetchOne("SELECT COUNT(*) as c FROM attendance_sessions")['c'];
        $pagination = $this->paginate($total, 20, $page);

        $sessions = $this->db->fetchAll(
            "SELECT ats.*, a.name as activity_name, s.name as section_name,
                (SELECT COUNT(*) FROM attendance WHERE session_id = ats.id AND status = 'Present') as present_count,
                (SELECT COUNT(*) FROM attendance WHERE session_id = ats.id) as total_count
             FROM attendance_sessions ats
             LEFT JOIN activities a ON a.id = ats.activity_id
             LEFT JOIN sections s ON s.id = ats.section_id
             ORDER BY ats.date DESC
             LIMIT :limit OFFSET :offset",
            ['limit' => $pagination['per_page'], 'offset' => $pagination['offset']]
        );

        $this->setMenuActive('attendance');
        $this->view('attendance.index', ['sessions' => $sessions, 'pagination' => $pagination]);
    }

    public function markForm(): void
    {
        $this->authorize('attendance.create');
        $activities = $this->db->fetchAll("SELECT * FROM activities WHERE status != 'Cancelled' ORDER BY date DESC LIMIT 50");
        $sections = $this->db->fetchAll("SELECT * FROM sections WHERE status = 'Active' ORDER BY name");
        $members = $this->db->fetchAll(
            "SELECT m.*, s.name as section_name 
             FROM members m 
             LEFT JOIN sections s ON s.id = m.section_id 
             WHERE m.status = 'Active' 
             ORDER BY s.name ASC, m.first_name ASC, m.last_name ASC"
        );
        $this->setMenuActive('attendance');
        $this->view('attendance.mark', [
            'activities' => $activities,
            'sections' => $sections,
            'members' => $members,
        ]);
    }

    public function mark(): void
    {
        $this->authorize('attendance.create');
        $this->verifyCsrf();

        $request = new \App\Core\Request();
        $activityId = (int)$request->input('activity_id');
        $date = $request->input('date') ?: date('Y-m-d');
        $sectionId = (int)$request->input('section_id') ?: null;
        $attendanceData = $request->input('attendance') ?? [];

        if (empty($attendanceData)) {
            $this->redirect('/attendance/mark', 'Please mark attendance for at least one member.', 'danger');
            return;
        }

        try {
            $this->db->beginTransaction();

            // If no activity selected, find or create default activity for this date
            if (!$activityId) {
                $existing = $this->db->fetchOne("SELECT id FROM activities WHERE date = :dt LIMIT 1", ['dt' => $date]);
                if ($existing) {
                    $activityId = (int)$existing['id'];
                } else {
                    $this->db->execute(
                        "INSERT INTO activities (name, date, status) VALUES ('General Parade Meeting', :dt, 'Completed')",
                        ['dt' => $date]
                    );
                    $activityId = (int)$this->db->lastInsertId();
                }
            }

            // Create session
            $this->db->execute(
                "INSERT INTO attendance_sessions (activity_id, date, section_id, recorded_by)
                 VALUES (:activity, :date, :section, :user)",
                ['activity' => $activityId, 'date' => $date, 'section' => $sectionId, 'user' => auth()->id()]
            );
            $sessionId = (int)$this->db->lastInsertId();

            // Insert attendance records
            $markedCount = 0;
            foreach ($attendanceData as $memberId => $status) {
                if (in_array($status, ['Present', 'Absent', 'Excused', 'Late'])) {
                    $this->db->execute(
                        "INSERT INTO attendance (session_id, member_id, status)
                         VALUES (:session, :member, :status)
                         ON DUPLICATE KEY UPDATE status = :status_update",
                        [
                            'session' => $sessionId,
                            'member' => (int)$memberId,
                            'status' => $status,
                            'status_update' => $status,
                        ]
                    );
                    $markedCount++;
                }
            }

            (new Auth())->logAction('attendance_marked', 'attendance', $sessionId, "Marked attendance for {$markedCount} members");
            $this->db->commit();

            $this->redirect('/attendance', "Attendance recorded successfully for {$markedCount} members.", 'success');
        } catch (\Throwable $e) {
            $this->db->rollBack();
            error_log('Attendance error: ' . $e->getMessage());
            $this->redirect('/attendance/mark', 'Error recording attendance.', 'danger');
        }
    }

    public function scanForm(): void
    {
        $this->authorize('attendance.create');
        $activities = $this->db->fetchAll("SELECT * FROM activities WHERE status != 'Cancelled' ORDER BY date DESC LIMIT 50");
        $recentSessions = $this->db->fetchAll(
            "SELECT ats.*, a.name as activity_name 
             FROM attendance_sessions ats 
             LEFT JOIN activities a ON a.id = ats.activity_id 
             ORDER BY ats.date DESC, ats.id DESC LIMIT 10"
        );
        $this->setMenuActive('attendance');
        $this->view('attendance.scan', ['activities' => $activities, 'recentSessions' => $recentSessions]);
    }

    public function scanMarkApi(): void
    {
        $this->authorize('attendance.create');
        header('Content-Type: application/json');

        $request = new \App\Core\Request();
        $code = trim((string)$request->input('code', ''));
        $sessionId = (int)$request->input('session_id', 0);
        $activityId = (int)$request->input('activity_id', 0);
        $status = $request->input('status', 'Present');

        if (!$code) {
            echo json_encode(['success' => false, 'message' => 'No QR code or member number provided.']);
            return;
        }

        // Extract token if code is a URL like http://.../verify/member/TOKEN
        $token = $code;
        if (preg_match('/verify\/member\/([a-zA-Z0-9_-]+)/', $code, $matches)) {
            $token = $matches[1];
        }

        // Search for member by verification token or member_number
        $member = $this->db->fetchOne(
            "SELECT m.*, s.name as section_name 
             FROM members m 
             LEFT JOIN sections s ON s.id = m.section_id 
             WHERE m.verification_token = :token OR LOWER(m.member_number) = LOWER(:code)",
            ['token' => $token, 'code' => $code]
        );

        if (!$member) {
            echo json_encode(['success' => false, 'message' => 'Member not found with code: ' . htmlspecialchars($code)]);
            return;
        }

        // Ensure session exists or create one for today
        if (!$sessionId) {
            if (!$activityId) {
                // Get most recent activity or default
                $latestActivity = $this->db->fetchOne("SELECT id FROM activities ORDER BY date DESC LIMIT 1");
                $activityId = $latestActivity ? (int)$latestActivity['id'] : 1;
            }

            $dateToday = date('Y-m-d');
            $existingSession = $this->db->fetchOne(
                "SELECT id FROM attendance_sessions WHERE activity_id = :act AND date = :dt LIMIT 1",
                ['act' => $activityId, 'dt' => $dateToday]
            );

            if ($existingSession) {
                $sessionId = (int)$existingSession['id'];
            } else {
                $this->db->execute(
                    "INSERT INTO attendance_sessions (activity_id, date, recorded_by) VALUES (:act, :dt, :user)",
                    ['act' => $activityId, 'dt' => $dateToday, 'user' => auth()->id()]
                );
                $sessionId = (int)$this->db->lastInsertId();
            }
        }

        // Check existing status in session
        $existing = $this->db->fetchOne(
            "SELECT status FROM attendance WHERE session_id = :sid AND member_id = :mid",
            ['sid' => $sessionId, 'mid' => $member['id']]
        );

        // Insert or update attendance
        $this->db->execute(
            "INSERT INTO attendance (session_id, member_id, status) VALUES (:sid, :mid, :st)
             ON DUPLICATE KEY UPDATE status = :st_up",
            ['sid' => $sessionId, 'mid' => $member['id'], 'st' => $status, 'st_up' => $status]
        );

        $fullName = trim($member['first_name'] . ' ' . ($member['middle_name'] ? $member['middle_name'] . ' ' : '') . $member['last_name']);
        
        echo json_encode([
            'success' => true,
            'message' => 'Attendance marked for ' . $fullName,
            'already_marked' => (bool)$existing,
            'member' => [
                'id' => $member['id'],
                'name' => $fullName,
                'member_number' => $member['member_number'],
                'section' => $member['section_name'] ?? 'N/A',
                'rank' => $member['rank'] ?? 'Member',
                'status' => $status,
                'photo' => $member['profile_photo'] ? asset($member['profile_photo']) : null,
            ],
            'session_id' => $sessionId,
        ]);
    }

    public function session(string $id): void
    {
        $session = $this->db->fetchOne(
            "SELECT ats.*, a.name as activity_name, s.name as section_name
             FROM attendance_sessions ats
             LEFT JOIN activities a ON a.id = ats.activity_id
             LEFT JOIN sections s ON s.id = ats.section_id
             WHERE ats.id = :id", ['id' => (int)$id]
        );
        if (!$session) {
            $this->abort(404, 'Attendance session not found');
        }

        $records = $this->db->fetchAll(
            "SELECT att.*, m.first_name, m.last_name, m.member_number
             FROM attendance att
             JOIN members m ON m.id = att.member_id
             WHERE att.session_id = :id ORDER BY m.last_name, m.first_name",
            ['id' => (int)$id]
        );

        $this->setMenuActive('attendance');
        $this->view('attendance.session', ['session' => $session, 'records' => $records]);
    }

    public function memberAttendance(string $id): void
    {
        $member = $this->db->fetchOne("SELECT * FROM members WHERE id = :id", ['id' => (int)$id]);
        if (!$member) {
            $this->abort(404, 'Member not found');
        }

        $records = $this->db->fetchAll(
            "SELECT att.*, ats.date, a.name as activity_name
             FROM attendance att
             JOIN attendance_sessions ats ON ats.id = att.session_id
             LEFT JOIN activities a ON a.id = ats.activity_id
             WHERE att.member_id = :id ORDER BY ats.date DESC",
            ['id' => (int)$id]
        );

        $this->setMenuActive('attendance');
        $this->view('attendance.member', ['member' => $member, 'records' => $records]);
    }

    public function bulkMark(): void
    {
        $this->authorize('attendance.create');
        $this->verifyCsrf();

        $request = new \App\Core\Request();
        $sessionId = (int)$request->input('session_id');
        $status = $request->input('status', 'Present');
        $memberIds = $request->input('member_ids') ?? [];

        foreach ($memberIds as $memberId) {
            $this->db->execute(
                "INSERT INTO attendance (session_id, member_id, status)
                 VALUES (:session, :member, :status)
                 ON DUPLICATE KEY UPDATE status = :status_update",
                [
                    'session' => $sessionId,
                    'member' => (int)$memberId,
                    'status' => $status,
                    'status_update' => $status,
                ]
            );
        }

        $this->redirect("/attendance/session/{$sessionId}", 'Bulk attendance updated.', 'success');
    }
}
