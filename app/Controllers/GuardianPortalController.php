<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;
use App\Core\Auth;

class GuardianPortalController extends Controller
{
    private Database $db;
    private Auth $auth;

    public function __construct()
    {
        parent::__construct();
        $this->auth = new Auth();
        if (!$this->auth->check()) {
            $this->redirect('/login');
        }
        $this->db = Database::getInstance();
    }

    /**
     * Get all members (wards/children) linked to the logged-in guardian user.
     */
    private function getWards(): array
    {
        $user = $this->auth->user();
        $userId = (int)$user['id'];
        $userEmail = trim($user['email'] ?? '');

        return $this->db->fetchAll(
            "SELECT DISTINCT m.*, s.name as section_name, g.relationship
             FROM members m
             JOIN guardians g ON g.member_id = m.id
             LEFT JOIN sections s ON s.id = m.section_id
             WHERE g.user_id = :uid OR (g.email IS NOT NULL AND LOWER(g.email) = LOWER(:email))
             ORDER BY m.first_name, m.last_name",
            ['uid' => $userId, 'email' => $userEmail]
        );
    }

    public function dashboard(): void
    {
        $wards = $this->getWards();
        $wardIds = array_column($wards, 'id');

        $totalDues = 0.0;
        $totalPaid = 0.0;
        $recentAttendance = [];
        $upcomingEvents = [];

        if (!empty($wardIds)) {
            $inClause = implode(',', array_map('intval', $wardIds));

            // Dues summary
            $duesStats = $this->db->fetchOne(
                "SELECT SUM(amount) as total, SUM(paid_amount) as paid, SUM(balance) as balance 
                 FROM member_dues WHERE member_id IN ({$inClause})"
            );
            $totalDues = (float)($duesStats['total'] ?? 0);
            $totalPaid = (float)($duesStats['paid'] ?? 0);

            // Recent attendance
            $recentAttendance = $this->db->fetchAll(
                "SELECT att.*, ats.date, a.name as activity_name, m.first_name, m.last_name
                 FROM attendance att
                 JOIN attendance_sessions ats ON ats.id = att.session_id
                 LEFT JOIN activities a ON a.id = ats.activity_id
                 JOIN members m ON m.id = att.member_id
                 WHERE att.member_id IN ({$inClause})
                 ORDER BY ats.date DESC LIMIT 10"
            );

            // Upcoming events
            $upcomingEvents = $this->db->fetchAll(
                "SELECT * FROM events WHERE start_date >= CURRENT_DATE AND status = 'Upcoming' ORDER BY start_date ASC LIMIT 5"
            );
        }

        $this->view('guardian_portal.dashboard', [
            'wards' => $wards,
            'totalDues' => $totalDues,
            'totalPaid' => $totalPaid,
            'balance' => $totalDues - $totalPaid,
            'recentAttendance' => $recentAttendance,
            'upcomingEvents' => $upcomingEvents,
        ]);
    }

    public function ward(string $id): void
    {
        $wards = $this->getWards();
        $wardId = (int)$id;

        $targetWard = null;
        foreach ($wards as $w) {
            if ((int)$w['id'] === $wardId) {
                $targetWard = $w;
                break;
            }
        }

        if (!$targetWard) {
            $this->redirect('/guardian', 'Ward profile not found or access denied.', 'danger');
            return;
        }

        // Fetch badges
        $badges = $this->db->fetchAll(
            "SELECT mb.*, b.name as badge_name, b.category 
             FROM member_badges mb 
             JOIN badges b ON b.id = mb.badge_id 
             WHERE mb.member_id = :mid ORDER BY mb.date_awarded DESC",
            ['mid' => $wardId]
        );

        // Fetch training courses
        $training = $this->db->fetchAll(
            "SELECT te.*, tc.course_name, tc.code 
             FROM training_enrollments te 
             JOIN training_courses tc ON tc.id = te.course_id 
             WHERE te.member_id = :mid ORDER BY te.created_at DESC",
            ['mid' => $wardId]
        );

        // Fetch attendance stats
        $attStats = $this->db->fetchOne(
            "SELECT COUNT(*) as total, 
                    SUM(CASE WHEN status = 'Present' THEN 1 ELSE 0 END) as present_count
             FROM attendance WHERE member_id = :mid",
            ['mid' => $wardId]
        );

        $this->view('guardian_portal.ward', [
            'ward' => $targetWard,
            'badges' => $badges,
            'training' => $training,
            'attStats' => $attStats,
        ]);
    }

    public function dues(): void
    {
        $wards = $this->getWards();
        $wardIds = array_column($wards, 'id');

        $duesList = [];
        $payments = [];

        if (!empty($wardIds)) {
            $inClause = implode(',', array_map('intval', $wardIds));

            $duesList = $this->db->fetchAll(
                "SELECT md.*, d.title as dues_title, d.due_date, m.first_name, m.last_name
                 FROM member_dues md
                 JOIN dues d ON d.id = md.dues_id
                 JOIN members m ON m.id = md.member_id
                 WHERE md.member_id IN ({$inClause})
                 ORDER BY d.due_date DESC"
            );

            $payments = $this->db->fetchAll(
                "SELECT p.*, m.first_name, m.last_name
                 FROM payments p
                 JOIN members m ON m.id = p.member_id
                 WHERE p.member_id IN ({$inClause}) AND p.status != 'Void'
                 ORDER BY p.payment_date DESC"
            );
        }

        $this->view('guardian_portal.dues', [
            'wards' => $wards,
            'duesList' => $duesList,
            'payments' => $payments,
        ]);
    }

    public function attendance(): void
    {
        $wards = $this->getWards();
        $wardIds = array_column($wards, 'id');

        $attendanceLogs = [];
        if (!empty($wardIds)) {
            $inClause = implode(',', array_map('intval', $wardIds));
            $attendanceLogs = $this->db->fetchAll(
                "SELECT att.*, ats.date, a.name as activity_name, m.first_name, m.last_name
                 FROM attendance att
                 JOIN attendance_sessions ats ON ats.id = att.session_id
                 LEFT JOIN activities a ON a.id = ats.activity_id
                 JOIN members m ON m.id = att.member_id
                 WHERE att.member_id IN ({$inClause})
                 ORDER BY ats.date DESC"
            );
        }

        $this->view('guardian_portal.attendance', [
            'wards' => $wards,
            'attendanceLogs' => $attendanceLogs,
        ]);
    }

    public function events(): void
    {
        $wards = $this->getWards();
        $events = $this->db->fetchAll(
            "SELECT * FROM events WHERE status != 'Cancelled' ORDER BY start_date DESC"
        );

        $this->view('guardian_portal.events', [
            'wards' => $wards,
            'events' => $events,
        ]);
    }
}
