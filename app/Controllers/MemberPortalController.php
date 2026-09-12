<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;
use App\Core\Auth;

class MemberPortalController extends Controller
{
    private Database $db;

    public function __construct()
    {
        parent::__construct();
        $this->db = Database::getInstance();
    }

    /**
     * Get the current member record for the logged-in user.
     */
    private function getMember(): ?array
    {
        $userId = auth()->id();
        return $this->db->fetchOne(
            "SELECT m.*, s.name as section_name FROM members m LEFT JOIN sections s ON s.id = m.section_id WHERE m.user_id = :uid OR m.email = :email",
            ['uid' => $userId, 'email' => auth()->user()['email'] ?? '']
        );
    }

    public function dashboard(): void
    {
        $member = $this->getMember();
        if (!$member) {
            $this->view('member_portal.no_profile');
            return;
        }

        $stats = [
            'total_attendance' => (int)$this->db->fetchOne("SELECT COUNT(*) as c FROM attendance WHERE member_id = :m AND status = 'Present'", ['m' => $member['id']])['c'],
            'total_dues' => (float)($this->db->fetchOne("SELECT COALESCE(SUM(amount_due), 0) as t FROM member_dues WHERE member_id = :m", ['m' => $member['id']])['t'] ?? 0),
            'total_paid' => (float)($this->db->fetchOne("SELECT COALESCE(SUM(amount_paid), 0) as t FROM member_dues WHERE member_id = :m", ['m' => $member['id']])['t'] ?? 0),
            'balance' => (float)($this->db->fetchOne("SELECT COALESCE(SUM(balance), 0) as t FROM member_dues WHERE member_id = :m AND balance > 0", ['m' => $member['id']])['t'] ?? 0),
            'badges' => (int)$this->db->fetchOne("SELECT COUNT(*) as c FROM member_badges WHERE member_id = :m", ['m' => $member['id']])['c'],
            'events' => (int)$this->db->fetchOne("SELECT COUNT(*) as c FROM event_registrations WHERE member_id = :m AND status != 'Cancelled'", ['m' => $member['id']])['c'],
        ];

        $announcements = $this->db->fetchAll(
            "SELECT * FROM announcements WHERE status = 'Published' AND (expiry_date IS NULL OR expiry_date >= CURRENT_DATE) ORDER BY publish_date DESC LIMIT 5"
        );
        $upcomingEvents = $this->db->fetchAll(
            "SELECT * FROM events WHERE start_date >= CURRENT_DATE AND status = 'Upcoming' ORDER BY start_date LIMIT 5"
        );

        $this->setMenuActive('portal');
        $this->view('member_portal.dashboard', ['member' => $member, 'stats' => $stats, 'announcements' => $announcements, 'upcomingEvents' => $upcomingEvents]);
    }

    public function profile(): void
    {
        $member = $this->getMember();
        if (!$member) {
            $this->view('member_portal.no_profile');
            return;
        }
        $guardians = $this->db->fetchAll("SELECT * FROM guardians WHERE member_id = :m", ['m' => $member['id']]);
        $this->setMenuActive('portal');
        $this->view('member_portal.profile', ['member' => $member, 'guardians' => $guardians]);
    }

    public function attendance(): void
    {
        $member = $this->getMember();
        if (!$member) {
            $this->view('member_portal.no_profile');
            return;
        }
        $records = $this->db->fetchAll(
            "SELECT att.*, ats.date, a.name as activity_name FROM attendance att
             JOIN attendance_sessions ats ON ats.id = att.session_id
             LEFT JOIN activities a ON a.id = ats.activity_id
             WHERE att.member_id = :m ORDER BY ats.date DESC",
            ['m' => $member['id']]
        );
        $totalSessions = count($records);
        $present = count(array_filter($records, fn($r) => $r['status'] === 'Present'));
        $rate = $totalSessions > 0 ? round(($present / $totalSessions) * 100) : 0;

        $this->setMenuActive('portal');
        $this->view('member_portal.attendance', ['member' => $member, 'records' => $records, 'rate' => $rate]);
    }

    public function dues(): void
    {
        $member = $this->getMember();
        if (!$member) {
            $this->view('member_portal.no_profile');
            return;
        }
        $dues = $this->db->fetchAll(
            "SELECT md.*, d.name as dues_name FROM member_dues md
             JOIN dues d ON d.id = md.dues_id WHERE md.member_id = :m ORDER BY md.created_at DESC",
            ['m' => $member['id']]
        );
        $this->setMenuActive('portal');
        $this->view('member_portal.dues', ['member' => $member, 'dues' => $dues]);
    }

    public function payments(): void
    {
        $member = $this->getMember();
        if (!$member) {
            $this->view('member_portal.no_profile');
            return;
        }
        $payments = $this->db->fetchAll(
            "SELECT * FROM payments WHERE member_id = :m AND status = 'Completed' ORDER BY payment_date DESC",
            ['m' => $member['id']]
        );
        $this->setMenuActive('portal');
        $this->view('member_portal.payments', ['member' => $member, 'payments' => $payments]);
    }

    public function events(): void
    {
        $member = $this->getMember();
        if (!$member) {
            $this->view('member_portal.no_profile');
            return;
        }
        $events = $this->db->fetchAll(
            "SELECT e.*, er.status as reg_status FROM events e
             LEFT JOIN event_registrations er ON er.event_id = e.id AND er.member_id = :m
             WHERE e.status != 'Cancelled' ORDER BY e.start_date DESC",
            ['m' => $member['id']]
        );
        $this->setMenuActive('portal');
        $this->view('member_portal.events', ['member' => $member, 'events' => $events]);
    }

    public function training(): void
    {
        $member = $this->getMember();
        if (!$member) {
            $this->view('member_portal.no_profile');
            return;
        }
        $training = $this->db->fetchAll(
            "SELECT te.*, tc.name as course_name, tc.duration, tc.instructor
             FROM training_enrollments te JOIN training_courses tc ON tc.id = te.course_id
             WHERE te.member_id = :m ORDER BY te.created_at DESC",
            ['m' => $member['id']]
        );
        $this->setMenuActive('portal');
        $this->view('member_portal.training', ['member' => $member, 'training' => $training]);
    }

    public function badges(): void
    {
        $member = $this->getMember();
        if (!$member) {
            $this->view('member_portal.no_profile');
            return;
        }
        $badges = $this->db->fetchAll(
            "SELECT mb.*, b.name as badge_name, b.description as badge_description
             FROM member_badges mb JOIN badges b ON b.id = mb.badge_id
             WHERE mb.member_id = :m ORDER BY mb.date_awarded DESC",
            ['m' => $member['id']]
        );
        $awards = $this->db->fetchAll(
            "SELECT * FROM awards WHERE member_id = :m ORDER BY date_awarded DESC",
            ['m' => $member['id']]
        );
        $this->setMenuActive('portal');
        $this->view('member_portal.badges', ['member' => $member, 'badges' => $badges, 'awards' => $awards]);
    }

    public function awards(): void
    {
        $member = $this->getMember();
        if (!$member) {
            $this->view('member_portal.no_profile');
            return;
        }
        $awards = $this->db->fetchAll(
            "SELECT * FROM awards WHERE member_id = :m ORDER BY date_awarded DESC",
            ['m' => $member['id']]
        );
        $this->setMenuActive('portal');
        $this->view('member_portal.awards', ['member' => $member, 'awards' => $awards]);
    }

    public function card(): void
    {
        $member = $this->getMember();
        if (!$member) {
            $this->view('member_portal.no_profile');
            return;
        }
        $profile = $this->db->fetchOne("SELECT * FROM company_profile LIMIT 1") ?? [];
        $this->setMenuActive('portal');
        $this->view('member_portal.card', ['member' => $member, 'profile' => $profile]);
    }

    public function announcements(): void
    {
        $announcements = $this->db->fetchAll(
            "SELECT * FROM announcements WHERE status = 'Published' ORDER BY publish_date DESC"
        );
        $this->setMenuActive('portal');
        $this->view('member_portal.announcements', ['announcements' => $announcements]);
    }

    public function documents(): void
    {
        $documents = $this->db->fetchAll(
            "SELECT * FROM documents WHERE is_public = true ORDER BY created_at DESC"
        );
        $this->setMenuActive('portal');
        $this->view('member_portal.documents', ['documents' => $documents]);
    }
}
