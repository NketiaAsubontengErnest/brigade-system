<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;

class DashboardController extends Controller
{
    private Database $db;

    public function __construct()
    {
        parent::__construct();
        $this->authorize('reports.view');
        $this->db = Database::getInstance();
    }

    public function index(): void
    {
        // Member statistics
        $totalMembers = (int)$this->db->fetchOne("SELECT COUNT(*) as c FROM members")['c'];
        $activeMembers = (int)$this->db->fetchOne("SELECT COUNT(*) as c FROM members WHERE status = 'Active'")['c'];
        $maleMembers = (int)$this->db->fetchOne("SELECT COUNT(*) as c FROM members WHERE gender = 'Male' AND status = 'Active'")['c'];
        $femaleMembers = (int)$this->db->fetchOne("SELECT COUNT(*) as c FROM members WHERE gender = 'Female' AND status = 'Active'")['c'];
        $pendingMembers = (int)$this->db->fetchOne("SELECT COUNT(*) as c FROM members WHERE status = 'Pending'")['c'];
        $totalOfficers = (int)$this->db->fetchOne("SELECT COUNT(*) as c FROM officers WHERE status = 'Active'")['c'];

        // Dues statistics
        $totalExpected = (float)($this->db->fetchOne("SELECT COALESCE(SUM(amount_due), 0) as t FROM member_dues")['t'] ?? 0);
        $totalCollected = (float)($this->db->fetchOne("SELECT COALESCE(SUM(amount_paid), 0) as t FROM member_dues")['t'] ?? 0);
        $totalOutstanding = $totalExpected - $totalCollected;
        $paidMembers = (int)$this->db->fetchOne("SELECT COUNT(*) as c FROM member_dues WHERE status = 'Paid'")['c'];
        $unpaidMembers = (int)$this->db->fetchOne("SELECT COUNT(*) as c FROM member_dues WHERE status IN ('Unpaid', 'Overdue')")['c'];

        // Financial statistics
        $totalIncome = (float)($this->db->fetchOne("SELECT COALESCE(SUM(amount), 0) as t FROM income WHERE status = 'Recorded'")['t'] ?? 0);
        $totalExpenses = (float)($this->db->fetchOne("SELECT COALESCE(SUM(amount), 0) as t FROM expenses WHERE status IN ('Recorded', 'Approved')")['t'] ?? 0);
        $currentBalance = $totalIncome - $totalExpenses;

        // Monthly income/expenses (last 6 months) - MariaDB compatible
        $monthlyData = $this->db->fetchAll("
            SELECT 
                DATE_FORMAT(d, '%b') as month_label,
                COALESCE(i.income, 0) as income,
                COALESCE(e.expenses, 0) as expenses
            FROM (
                SELECT DATE_SUB(CURDATE(), INTERVAL 5 MONTH) as d
                UNION ALL SELECT DATE_SUB(CURDATE(), INTERVAL 4 MONTH)
                UNION ALL SELECT DATE_SUB(CURDATE(), INTERVAL 3 MONTH)
                UNION ALL SELECT DATE_SUB(CURDATE(), INTERVAL 2 MONTH)
                UNION ALL SELECT DATE_SUB(CURDATE(), INTERVAL 1 MONTH)
                UNION ALL SELECT CURDATE()
            ) months
            LEFT JOIN (
                SELECT DATE_FORMAT(date, '%Y-%m-01') as m, SUM(amount) as income
                FROM income WHERE status = 'Recorded'
                GROUP BY m
            ) i ON i.m = DATE_FORMAT(DATE_FORMAT(d, '%Y-%m-01'), '%Y-%m-%d')
            LEFT JOIN (
                SELECT DATE_FORMAT(date, '%Y-%m-01') as m, SUM(amount) as expenses
                FROM expenses WHERE status IN ('Recorded', 'Approved')
                GROUP BY m
            ) e ON e.m = DATE_FORMAT(DATE_FORMAT(d, '%Y-%m-01'), '%Y-%m-%d')
            ORDER BY d
        ");

        // Member growth (last 6 months) - MariaDB compatible
        $memberGrowth = $this->db->fetchAll("
            SELECT 
                DATE_FORMAT(d, '%b') as month_label,
                COUNT(m.id) as count
            FROM (
                SELECT DATE_SUB(CURDATE(), INTERVAL 5 MONTH) as d
                UNION ALL SELECT DATE_SUB(CURDATE(), INTERVAL 4 MONTH)
                UNION ALL SELECT DATE_SUB(CURDATE(), INTERVAL 3 MONTH)
                UNION ALL SELECT DATE_SUB(CURDATE(), INTERVAL 2 MONTH)
                UNION ALL SELECT DATE_SUB(CURDATE(), INTERVAL 1 MONTH)
                UNION ALL SELECT CURDATE()
            ) months
            LEFT JOIN members m ON DATE_FORMAT(m.created_at, '%Y-%m-01') = DATE_FORMAT(DATE_FORMAT(d, '%Y-%m-01'), '%Y-%m-%d')
            GROUP BY d ORDER BY d
        ");

        // Attendance rate
        $totalAttendance = (int)$this->db->fetchOne("SELECT COUNT(*) as c FROM attendance WHERE status = 'Present'")['c'] ?? 0;
        $totalPossible = (int)$this->db->fetchOne("SELECT COUNT(*) as c FROM attendance")['c'] ?? 1;
        $attendanceRate = $totalPossible > 0 ? round(($totalAttendance / $totalPossible) * 100) : 0;

        // Upcoming events
        $upcomingEvents = $this->db->fetchAll(
            "SELECT * FROM events WHERE start_date >= CURRENT_DATE AND status = 'Upcoming' ORDER BY start_date ASC LIMIT 5"
        );

        // Recent activities
        $recentActivities = $this->db->fetchAll(
            "SELECT * FROM activities WHERE status != 'Cancelled' ORDER BY date DESC LIMIT 5"
        );

        // Recent announcements
        $recentAnnouncements = $this->db->fetchAll(
            "SELECT * FROM announcements WHERE status = 'Published' ORDER BY publish_date DESC LIMIT 5"
        );

        // Notifications
        $userId = auth()->id();
        $notifications = $this->db->fetchAll(
            "SELECT * FROM notifications WHERE user_id = :uid ORDER BY created_at DESC LIMIT 10",
            ['uid' => $userId]
        );

        // Dues collection by month - MariaDB compatible
        $duesCollection = $this->db->fetchAll("
            SELECT DATE_FORMAT(p.payment_date, '%b') as month_label, SUM(p.amount) as total
            FROM payments p WHERE p.status = 'Completed'
            AND p.payment_date >= DATE_SUB(CURDATE(), INTERVAL 5 MONTH)
            GROUP BY DATE_FORMAT(p.payment_date, '%Y-%m'), DATE_FORMAT(p.payment_date, '%b')
            ORDER BY MIN(p.payment_date)
        ");

        $this->setMenuActive('dashboard');
        $this->view('dashboard.index', [
            'totalMembers' => $totalMembers,
            'activeMembers' => $activeMembers,
            'maleMembers' => $maleMembers,
            'femaleMembers' => $femaleMembers,
            'pendingMembers' => $pendingMembers,
            'totalOfficers' => $totalOfficers,
            'totalExpected' => $totalExpected,
            'totalCollected' => $totalCollected,
            'totalOutstanding' => $totalOutstanding,
            'paidMembers' => $paidMembers,
            'unpaidMembers' => $unpaidMembers,
            'totalIncome' => $totalIncome,
            'totalExpenses' => $totalExpenses,
            'currentBalance' => $currentBalance,
            'attendanceRate' => $attendanceRate,
            'monthlyData' => $monthlyData,
            'memberGrowth' => $memberGrowth,
            'duesCollection' => $duesCollection,
            'upcomingEvents' => $upcomingEvents,
            'recentActivities' => $recentActivities,
            'recentAnnouncements' => $recentAnnouncements,
            'notifications' => $notifications,
        ]);
    }
}
