<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;

class ReportController extends Controller
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
        $stats = [
            'total_members' => (int)($this->db->fetchOne("SELECT COUNT(*) as c FROM members WHERE status = 'Active'")['c'] ?? 0),
            'attendance_rate' => 0,
            'total_collected' => (float)($this->db->fetchOne("SELECT COALESCE(SUM(amount_paid), 0) as t FROM member_dues")['t'] ?? 0),
            'total_events' => (int)($this->db->fetchOne("SELECT COUNT(*) as c FROM events WHERE status != 'Cancelled'")['c'] ?? 0),
        ];

        // Calculate average attendance rate
        $att = $this->db->fetchOne(
            "SELECT COUNT(*) as total, 
                    SUM(CASE WHEN status = 'Present' THEN 1 ELSE 0 END) as present 
             FROM attendance"
        );
        if ($att && (int)$att['total'] > 0) {
            $stats['attendance_rate'] = round(((int)$att['present'] / (int)$att['total']) * 100);
        }

        // 1. Section distribution
        $sectionsData = $this->db->fetchAll(
            "SELECT s.name, COUNT(m.id) as count 
             FROM sections s 
             LEFT JOIN members m ON m.section_id = s.id AND m.status = 'Active' 
             WHERE s.status = 'Active' 
             GROUP BY s.id, s.name 
             ORDER BY s.name"
        );

        // 2. Attendance trends
        $attendanceTrends = $this->db->fetchAll(
            "SELECT ats.date, 
                    SUM(CASE WHEN att.status = 'Present' THEN 1 ELSE 0 END) as present,
                    SUM(CASE WHEN att.status = 'Absent' THEN 1 ELSE 0 END) as absent,
                    SUM(CASE WHEN att.status = 'Late' THEN 1 ELSE 0 END) as late,
                    SUM(CASE WHEN att.status = 'Excused' THEN 1 ELSE 0 END) as excused
             FROM attendance_sessions ats
             JOIN attendance att ON att.session_id = ats.id
             GROUP BY ats.id, ats.date
             ORDER BY ats.date DESC
             LIMIT 7"
        );
        $attendanceTrends = array_reverse($attendanceTrends);

        // 3. Dues status distribution
        $duesStatusData = $this->db->fetchAll(
            "SELECT status, COUNT(*) as count, SUM(amount_paid) as paid, SUM(balance) as balance 
             FROM member_dues 
             GROUP BY status"
        );

        // 4. Financial monthly totals
        $incomeMonthly = $this->db->fetchAll(
            "SELECT DATE_FORMAT(date, '%b %Y') as month, SUM(amount) as total 
             FROM income WHERE status = 'Recorded' 
             GROUP BY DATE_FORMAT(date, '%Y-%m'), DATE_FORMAT(date, '%b %Y') 
             ORDER BY DATE_FORMAT(date, '%Y-%m') ASC LIMIT 6"
        );
        $expenseMonthly = $this->db->fetchAll(
            "SELECT DATE_FORMAT(date, '%b %Y') as month, SUM(amount) as total 
             FROM expenses WHERE status IN ('Recorded', 'Approved') 
             GROUP BY DATE_FORMAT(date, '%Y-%m'), DATE_FORMAT(date, '%b %Y') 
             ORDER BY DATE_FORMAT(date, '%Y-%m') ASC LIMIT 6"
        );

        // 5. Gender distribution
        $genderData = $this->db->fetchAll(
            "SELECT gender, COUNT(*) as count FROM members WHERE status = 'Active' GROUP BY gender"
        );

        $this->setMenuActive('reports');
        $this->view('reports.index', [
            'stats' => $stats,
            'sectionsData' => $sectionsData,
            'attendanceTrends' => $attendanceTrends,
            'duesStatusData' => $duesStatusData,
            'incomeMonthly' => $incomeMonthly,
            'expenseMonthly' => $expenseMonthly,
            'genderData' => $genderData,
        ]);
    }

    public function membership(): void
    {
        $request = new \App\Core\Request();
        $filter = $request->input('filter', 'all');
        $section = $request->input('section', '');

        $where = "1=1";
        $params = [];

        switch ($filter) {
            case 'active': $where .= " AND m.status = 'Active'"; break;
            case 'inactive': $where .= " AND m.status = 'Inactive'"; break;
            case 'pending': $where .= " AND m.status = 'Pending'"; break;
            case 'male': $where .= " AND m.gender = 'Male' AND m.status = 'Active'"; break;
            case 'female': $where .= " AND m.gender = 'Female' AND m.status = 'Active'"; break;
        }
        if ($section) {
            $where .= " AND m.section_id = :section";
            $params['section'] = $section;
        }

        $members = $this->db->fetchAll(
            "SELECT m.*, s.name as section_name FROM members m LEFT JOIN sections s ON s.id = m.section_id
             WHERE {$where} ORDER BY m.last_name, m.first_name", $params
        );

        $stats = [
            'total' => count($members),
            'male' => count(array_filter($members, fn($m) => $m['gender'] === 'Male')),
            'female' => count(array_filter($members, fn($m) => $m['gender'] === 'Female')),
            'active' => count(array_filter($members, fn($m) => $m['status'] === 'Active')),
        ];

        $sections = $this->db->fetchAll("SELECT * FROM sections WHERE status = 'Active' ORDER BY name");
        $profile = $this->db->fetchOne("SELECT * FROM company_profile LIMIT 1") ?? [];

        $this->setMenuActive('reports');
        $this->view('reports.membership', [
            'members' => $members,
            'stats' => $stats,
            'sections' => $sections,
            'profile' => $profile,
            'filter' => $filter,
            'section' => $section,
        ]);
    }

    public function attendance(): void
    {
        $request = new \App\Core\Request();
        $dateFrom = $request->input('date_from', date('Y-m-d', strtotime('-30 days')));
        $dateTo = $request->input('date_to', date('Y-m-d'));

        $records = $this->db->fetchAll(
            "SELECT ats.date, a.name as activity_name, s.name as section_name,
                COUNT(att.id) as total,
                SUM(CASE WHEN att.status = 'Present' THEN 1 ELSE 0 END) as present,
                SUM(CASE WHEN att.status = 'Absent' THEN 1 ELSE 0 END) as absent,
                SUM(CASE WHEN att.status = 'Late' THEN 1 ELSE 0 END) as late,
                SUM(CASE WHEN att.status = 'Excused' THEN 1 ELSE 0 END) as excused
             FROM attendance att
             JOIN attendance_sessions ats ON ats.id = att.session_id
             LEFT JOIN activities a ON a.id = ats.activity_id
             LEFT JOIN sections s ON s.id = ats.section_id
             WHERE ats.date BETWEEN :from AND :to
             GROUP BY ats.id, ats.date, a.name, s.name
             ORDER BY ats.date DESC",
            ['from' => $dateFrom, 'to' => $dateTo]
        );

        $profile = $this->db->fetchOne("SELECT * FROM company_profile LIMIT 1") ?? [];
        $this->setMenuActive('reports');
        $this->view('reports.attendance', [
            'records' => $records,
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
            'profile' => $profile,
        ]);
    }

    public function dues(): void
    {
        $request = new \App\Core\Request();
        $filter = $request->input('filter', 'all');

        $where = "1=1";
        switch ($filter) {
            case 'paid': $where .= " AND md.status = 'Paid'"; break;
            case 'unpaid': $where .= " AND md.status = 'Unpaid'"; break;
            case 'partial': $where .= " AND md.status = 'Partial'"; break;
            case 'overdue': $where .= " AND md.status = 'Overdue'"; break;
        }

        $records = $this->db->fetchAll(
            "SELECT md.*, m.first_name, m.last_name, m.member_number, d.name as dues_name, s.name as section_name
             FROM member_dues md
             JOIN members m ON m.id = md.member_id
             JOIN dues d ON d.id = md.dues_id
             LEFT JOIN sections s ON s.id = m.section_id
             WHERE {$where}
             ORDER BY m.last_name, m.first_name"
        );

        $summary = [
            'total_expected' => (float)($this->db->fetchOne("SELECT COALESCE(SUM(amount_due), 0) as t FROM member_dues")['t'] ?? 0),
            'total_collected' => (float)($this->db->fetchOne("SELECT COALESCE(SUM(amount_paid), 0) as t FROM member_dues")['t'] ?? 0),
            'total_outstanding' => (float)($this->db->fetchOne("SELECT COALESCE(SUM(balance), 0) as t FROM member_dues WHERE balance > 0")['t'] ?? 0),
        ];

        $profile = $this->db->fetchOne("SELECT * FROM company_profile LIMIT 1") ?? [];
        $this->setMenuActive('reports');
        $this->view('reports.dues', ['records' => $records, 'summary' => $summary, 'profile' => $profile, 'filter' => $filter]);
    }

    public function finance(): void
    {
        $request = new \App\Core\Request();
        $dateFrom = $request->input('date_from', date('Y-m-d', strtotime('-365 days')));
        $dateTo = $request->input('date_to', date('Y-m-d'));

        $income = $this->db->fetchAll(
            "SELECT * FROM income WHERE status = 'Recorded' AND date BETWEEN :from AND :to ORDER BY date DESC",
            ['from' => $dateFrom, 'to' => $dateTo]
        );
        $expenses = $this->db->fetchAll(
            "SELECT * FROM expenses WHERE status IN ('Recorded', 'Approved') AND date BETWEEN :from AND :to ORDER BY date DESC",
            ['from' => $dateFrom, 'to' => $dateTo]
        );

        $totalIncome = array_sum(array_column($income, 'amount'));
        $totalExpenses = array_sum(array_column($expenses, 'amount'));

        $profile = $this->db->fetchOne("SELECT * FROM company_profile LIMIT 1") ?? [];
        $this->setMenuActive('reports');
        $this->view('reports.finance', [
            'income' => $income, 'expenses' => $expenses,
            'totalIncome' => $totalIncome, 'totalExpenses' => $totalExpenses,
            'balance' => $totalIncome - $totalExpenses, 'profile' => $profile,
            'dateFrom' => $dateFrom, 'dateTo' => $dateTo,
        ]);
    }

    public function training(): void
    {
        $records = $this->db->fetchAll(
            "SELECT te.*, m.first_name, m.last_name, m.member_number, tc.name as course_name
             FROM training_enrollments te
             JOIN members m ON m.id = te.member_id
             JOIN training_courses tc ON tc.id = te.course_id
             ORDER BY te.created_at DESC"
        );

        $profile = $this->db->fetchOne("SELECT * FROM company_profile LIMIT 1") ?? [];
        $this->setMenuActive('reports');
        $this->view('reports.training', ['records' => $records, 'profile' => $profile]);
    }

    public function events(): void
    {
        $events = $this->db->fetchAll(
            "SELECT e.*, 
                (SELECT COUNT(*) FROM event_registrations WHERE event_id = e.id AND status != 'Cancelled') as registered,
                (SELECT COUNT(*) FROM event_registrations WHERE event_id = e.id AND status = 'Attended') as attended
             FROM events e ORDER BY e.start_date DESC"
        );

        $profile = $this->db->fetchOne("SELECT * FROM company_profile LIMIT 1") ?? [];
        $this->setMenuActive('reports');
        $this->view('reports.events', ['events' => $events, 'profile' => $profile]);
    }

    public function export(string $type): void
    {
        $request = new \App\Core\Request();
        $reportType = $request->input('report', 'membership');
        $format = strtolower($request->input('format', $type ?: 'pdf'));

        $data = [];
        $headers = [];
        $title = 'Report';

        switch ($reportType) {
            case 'membership':
                $rows = $this->db->fetchAll(
                    "SELECT m.member_number, m.first_name, m.last_name, m.gender, m.phone, m.email, 
                        m.date_of_birth, m.date_joined, m.status, s.name as section_name
                     FROM members m LEFT JOIN sections s ON s.id = m.section_id ORDER BY m.last_name, m.first_name"
                );
                $headers = ['Member #', 'First Name', 'Last Name', 'Gender', 'Phone', 'Email', 'DOB', 'Joined', 'Status', 'Section'];
                $data = array_map(fn($r) => [
                    $r['member_number'], $r['first_name'], $r['last_name'], $r['gender'],
                    $r['phone'], $r['email'], $r['date_of_birth'], $r['date_joined'], $r['status'], $r['section_name']
                ], $rows);
                $title = 'Membership Report';
                break;

            case 'attendance':
                $dateFrom = $request->input('date_from', date('Y-m-d', strtotime('-30 days')));
                $dateTo = $request->input('date_to', date('Y-m-d'));
                $rows = $this->db->fetchAll(
                    "SELECT ats.date, a.name as activity_name, s.name as section_name,
                        COUNT(att.id) as total,
                        SUM(CASE WHEN att.status = 'Present' THEN 1 ELSE 0 END) as present,
                        SUM(CASE WHEN att.status = 'Absent' THEN 1 ELSE 0 END) as absent,
                        SUM(CASE WHEN att.status = 'Late' THEN 1 ELSE 0 END) as late,
                        SUM(CASE WHEN att.status = 'Excused' THEN 1 ELSE 0 END) as excused
                     FROM attendance att
                     JOIN attendance_sessions ats ON ats.id = att.session_id
                     LEFT JOIN activities a ON a.id = ats.activity_id
                     LEFT JOIN sections s ON s.id = ats.section_id
                     WHERE ats.date BETWEEN :from AND :to
                     GROUP BY ats.id, ats.date, a.name, s.name
                     ORDER BY ats.date DESC",
                    ['from' => $dateFrom, 'to' => $dateTo]
                );
                $headers = ['Date', 'Activity', 'Section', 'Total', 'Present', 'Absent', 'Late', 'Excused', 'Rate %'];
                $data = array_map(fn($r) => [
                    $r['date'], $r['activity_name'] ?? 'General Parade', $r['section_name'] ?? 'All',
                    $r['total'], $r['present'], $r['absent'], $r['late'], $r['excused'],
                    ($r['total'] > 0 ? round(($r['present'] / $r['total']) * 100) : 0) . '%'
                ], $rows);
                $title = 'Attendance Report';
                break;

            case 'finance':
                $dateFrom = $request->input('date_from', date('Y-m-d', strtotime('-365 days')));
                $dateTo = $request->input('date_to', date('Y-m-d'));
                $income = $this->db->fetchAll("SELECT * FROM income WHERE status = 'Recorded' AND date BETWEEN :f AND :t ORDER BY date", ['f' => $dateFrom, 't' => $dateTo]);
                $expenses = $this->db->fetchAll("SELECT * FROM expenses WHERE status IN ('Recorded', 'Approved') AND date BETWEEN :f AND :t ORDER BY date", ['f' => $dateFrom, 't' => $dateTo]);
                $data = array_merge(
                    array_map(fn($i) => ['Income', $i['category'], $i['description'], $i['amount'], $i['date'], $i['payment_method']], $income),
                    array_map(fn($e) => ['Expense', $e['category'], $e['description'], $e['amount'], $e['date'], $e['payment_method']], $expenses)
                );
                $headers = ['Type', 'Category', 'Description', 'Amount', 'Date', 'Method'];
                $title = 'Financial Report';
                break;

            case 'dues':
                $filter = $request->input('filter', 'all');
                $where = "1=1";
                if ($filter === 'paid') $where .= " AND md.status = 'Paid'";
                elseif ($filter === 'unpaid') $where .= " AND md.status = 'Unpaid'";
                elseif ($filter === 'partial') $where .= " AND md.status = 'Partial'";
                elseif ($filter === 'overdue') $where .= " AND md.status = 'Overdue'";

                $rows = $this->db->fetchAll(
                    "SELECT m.member_number, m.first_name, m.last_name, d.name as dues_name, md.amount_due, md.amount_paid, md.balance, md.status
                     FROM member_dues md JOIN members m ON m.id = md.member_id JOIN dues d ON d.id = md.dues_id WHERE {$where} ORDER BY m.last_name"
                );
                $headers = ['Member #', 'First Name', 'Last Name', 'Dues', 'Due', 'Paid', 'Balance', 'Status'];
                $data = array_map(fn($r) => [
                    $r['member_number'], $r['first_name'], $r['last_name'], $r['dues_name'],
                    $r['amount_due'], $r['amount_paid'], $r['balance'], $r['status']
                ], $rows);
                $title = 'Dues Report';
                break;

            case 'training':
                $rows = $this->db->fetchAll(
                    "SELECT m.member_number, m.first_name, m.last_name, tc.name as course_name, te.start_date, te.completion_date, te.score, te.status
                     FROM training_enrollments te
                     JOIN members m ON m.id = te.member_id
                     JOIN training_courses tc ON tc.id = te.course_id
                     ORDER BY te.created_at DESC"
                );
                $headers = ['Member #', 'First Name', 'Last Name', 'Course', 'Start Date', 'Completion Date', 'Score', 'Status'];
                $data = array_map(fn($r) => [
                    $r['member_number'], $r['first_name'], $r['last_name'], $r['course_name'],
                    $r['start_date'], $r['completion_date'] ?? 'N/A', $r['score'] ?? 'N/A', $r['status']
                ], $rows);
                $title = 'Training Report';
                break;

            case 'events':
                $rows = $this->db->fetchAll(
                    "SELECT e.name as title, e.start_date, e.location, e.status,
                        (SELECT COUNT(*) FROM event_registrations WHERE event_id = e.id AND status != 'Cancelled') as registered,
                        (SELECT COUNT(*) FROM event_registrations WHERE event_id = e.id AND status = 'Attended') as attended
                     FROM events e ORDER BY e.start_date DESC"
                );
                $headers = ['Event Title', 'Date', 'Location', 'Status', 'Registered', 'Attended'];
                $data = array_map(fn($r) => [
                    $r['title'], $r['start_date'], $r['location'] ?? 'N/A', $r['status'],
                    $r['registered'], $r['attended']
                ], $rows);
                $title = 'Events Report';
                break;

            default:
                $this->redirect('/reports', 'Invalid report type.', 'danger');
                return;
        }

        if ($format === 'excel' && class_exists('\PhpOffice\PhpSpreadsheet\Spreadsheet')) {
            $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->fromArray($headers, null, 'A1');
            foreach ($data as $row => $item) {
                $sheet->fromArray($item, null, 'A' . ($row + 2));
            }
            $writer = new \PhpOffice\PhpSpreadsheet\Writer_Xlsx($spreadsheet);
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment; filename="' . strtolower(str_replace(' ', '_', $title)) . '.xlsx"');
            $writer->save('php://output');
            exit;
        }

        if ($format === 'csv') {
            header('Content-Type: text/csv');
            header('Content-Disposition: attachment; filename="' . strtolower(str_replace(' ', '_', $title)) . '.csv"');
            $output = fopen('php://output', 'w');
            fputcsv($output, $headers);
            foreach ($data as $row) {
                fputcsv($output, $row);
            }
            fclose($output);
            exit;
        }

        // Default: PDF
        if (class_exists('\Dompdf\Dompdf')) {
            $dompdf = new \Dompdf\Dompdf();
            $profile = $this->db->fetchOne("SELECT * FROM company_profile LIMIT 1") ?? [];
            ob_start();
            $reportTitle = $title;
            $reportData = $data;
            $reportHeaders = $headers;
            require __DIR__ . '/../views/reports/report_pdf.php';
            $html = ob_get_clean();
            $dompdf->loadHtml($html);
            $dompdf->setPaper('A4', 'landscape');
            $dompdf->render();
            $dompdf->stream(strtolower(str_replace(' ', '_', $title)) . '.pdf', ['Attachment' => true]);
            exit;
        }

        $this->redirect('/reports', 'Export format not available.', 'warning');
    }
}
