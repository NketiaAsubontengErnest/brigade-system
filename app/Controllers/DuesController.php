<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;
use App\Core\Auth;

class DuesController extends Controller
{
    private Database $db;

    public function __construct()
    {
        parent::__construct();
        $this->authorize('dues.view');
        $this->db = Database::getInstance();
    }

    public function index(): void
    {
        $dues = $this->db->fetchAll(
            "SELECT d.*, dt.name as type_name,
                (SELECT COUNT(*) FROM member_dues WHERE dues_id = d.id) as assigned_count,
                (SELECT COUNT(*) FROM member_dues WHERE dues_id = d.id AND status = 'Paid') as paid_count
             FROM dues d LEFT JOIN dues_types dt ON dt.id = d.dues_type_id
             ORDER BY d.created_at DESC"
        );
        $this->setMenuActive('dues');
        $this->view('dues.index', ['dues' => $dues]);
    }

    public function dashboard(): void
    {
        $totalExpected = (float)($this->db->fetchOne("SELECT COALESCE(SUM(amount_due), 0) as t FROM member_dues")['t'] ?? 0);
        $totalCollected = (float)($this->db->fetchOne("SELECT COALESCE(SUM(amount_paid), 0) as t FROM member_dues")['t'] ?? 0);
        $totalOutstanding = $totalExpected - $totalCollected;
        $paidCount = (int)$this->db->fetchOne("SELECT COUNT(*) as c FROM member_dues WHERE status = 'Paid'")['c'];
        $unpaidCount = (int)$this->db->fetchOne("SELECT COUNT(*) as c FROM member_dues WHERE status IN ('Unpaid', 'Overdue')")['c'];
        $partialCount = (int)$this->db->fetchOne("SELECT COUNT(*) as c FROM member_dues WHERE status = 'Partial'")['c'];
        $overdueCount = (int)$this->db->fetchOne("SELECT COUNT(*) as c FROM member_dues WHERE status = 'Overdue'")['c'];
        $collectionPct = $totalExpected > 0 ? round(($totalCollected / $totalExpected) * 100) : 0;

        // Monthly collection chart - MariaDB compatible
        $monthlyCollection = $this->db->fetchAll("
            SELECT DATE_FORMAT(payment_date, '%b') as label, SUM(amount) as total
            FROM payments WHERE status = 'Completed' AND payment_date >= DATE_SUB(CURDATE(), INTERVAL 5 MONTH)
            GROUP BY DATE_FORMAT(payment_date, '%Y-%m'), DATE_FORMAT(payment_date, '%b')
            ORDER BY MIN(payment_date)
        ");

        // Outstanding by dues type
        $outstandingByType = $this->db->fetchAll("
            SELECT dt.name, SUM(md.balance) as total
            FROM member_dues md JOIN dues d ON d.id = md.dues_id JOIN dues_types dt ON dt.id = d.dues_type_id
            WHERE md.balance > 0 GROUP BY dt.name ORDER BY total DESC
        ");

        // Payment methods
        $paymentMethods = $this->db->fetchAll("
            SELECT payment_method, COUNT(*) as count, SUM(amount) as total
            FROM payments WHERE status = 'Completed' GROUP BY payment_method ORDER BY total DESC
        ");

        $this->setMenuActive('dues');
        $this->view('dues.dashboard', [
            'totalExpected' => $totalExpected, 'totalCollected' => $totalCollected,
            'totalOutstanding' => $totalOutstanding, 'paidCount' => $paidCount,
            'unpaidCount' => $unpaidCount, 'partialCount' => $partialCount,
            'overdueCount' => $overdueCount, 'collectionPct' => $collectionPct,
            'monthlyCollection' => $monthlyCollection, 'outstandingByType' => $outstandingByType,
            'paymentMethods' => $paymentMethods,
        ]);
    }

    public function types(): void
    {
        $types = $this->db->fetchAll("SELECT * FROM dues_types ORDER BY id");
        $this->setMenuActive('dues');
        $this->view('dues.types', ['types' => $types]);
    }

    public function storeType(): void
    {
        $this->authorize('dues.create');
        $this->verifyCsrf();

        $request = new \App\Core\Request();
        $name = trim($request->input('name', ''));
        $desc = trim($request->input('description', ''));
        $amount = (float)$request->input('default_amount', 0);

        $this->db->execute(
            "INSERT INTO dues_types (name, description, default_amount) VALUES (:n, :d, :a)",
            ['n' => $name, 'd' => $desc, 'a' => $amount]
        );

        $this->redirect('/dues/types', 'Dues type created.', 'success');
    }

    public function updateType(string $id): void
    {
        $this->authorize('dues.edit');
        $this->verifyCsrf();

        $request = new \App\Core\Request();
        $this->db->execute(
            "UPDATE dues_types SET name = :n, description = :d, default_amount = :a, status = :s, updated_at = NOW() WHERE id = :id",
            [
                'n' => $request->input('name'), 'd' => $request->input('description'),
                'a' => $request->input('default_amount', 0), 's' => $request->input('status', 'Active'), 'id' => (int)$id,
            ]
        );
        $this->redirect('/dues/types', 'Dues type updated.', 'success');
    }

    public function create(): void
    {
        $this->authorize('dues.create');
        $types = $this->db->fetchAll("SELECT * FROM dues_types WHERE status = 'Active' ORDER BY name");
        $sections = $this->db->fetchAll("SELECT * FROM sections WHERE status = 'Active' ORDER BY name");
        $members = $this->db->fetchAll("SELECT id, member_number, first_name, last_name FROM members WHERE status = 'Active' ORDER BY first_name, last_name");
        $this->setMenuActive('dues');
        $this->view('dues.create', ['types' => $types, 'sections' => $sections, 'members' => $members]);
    }

    public function store(): void
    {
        $this->authorize('dues.create');
        $this->verifyCsrf();

        $request = new \App\Core\Request();
        $data = $request->only(['dues_type_id', 'name', 'amount', 'period', 'start_date', 'due_date', 'section_id', 'applicable_to', 'notes']);

        try {
            $this->db->beginTransaction();

            $this->db->execute(
                "INSERT INTO dues (dues_type_id, name, amount, period, start_date, due_date, section_id, applicable_to, notes)
                 VALUES (:type, :name, :amount, :period, :sd, :dd, :section, :app, :notes)",
                [
                    'type' => $data['dues_type_id'] ?: null, 'name' => $data['name'],
                    'amount' => $data['amount'], 'period' => $data['period'] ?? null,
                    'sd' => $data['start_date'] ?: null, 'dd' => $data['due_date'] ?: null,
                    'section' => $data['section_id'] ?: null,
                    'app' => $data['applicable_to'] ?? 'All Active Members',
                    'notes' => $data['notes'] ?? null,
                ]
            );
            $duesId = (int)$this->db->lastInsertId();

            // Assign to applicable members
            if ($data['applicable_to'] === 'Specific Members' && !empty($_POST['member_ids'])) {
                // Specific members selected
                $memberIds = array_map('intval', $_POST['member_ids']);
                $placeholders = implode(',', array_fill(0, count($memberIds), '?'));
                $members = $this->db->fetchAll(
                    "SELECT m.id FROM members m WHERE m.id IN ({$placeholders}) AND m.status = 'Active'",
                    $memberIds
                );
            } else {
                $memberWhere = "m.status = 'Active'";
                $memberParams = [];
                if ($data['section_id'] && ($data['applicable_to'] ?? '') !== 'All Active Members') {
                    $memberWhere .= " AND m.section_id = :section";
                    $memberParams['section'] = $data['section_id'];
                }

                $members = $this->db->fetchAll(
                    "SELECT m.id FROM members m WHERE {$memberWhere}",
                    $memberParams
                );
            }

            foreach ($members as $member) {
                $this->db->execute(
                    "INSERT INTO member_dues (member_id, dues_id, amount_due, amount_paid, balance, status, due_date)
                     VALUES (:m, :d, :due, 0, :balance, 'Unpaid', :dd)
                     ON DUPLICATE KEY UPDATE member_id = member_id",
                    [
                        'm' => $member['id'],
                        'd' => $duesId,
                        'due' => $data['amount'],
                        'balance' => $data['amount'],
                        'dd' => $data['due_date'] ?: null,
                    ]
                );
            }

            (new Auth())->logAction('dues_created', 'dues', $duesId, "Created dues: {$data['name']} for " . count($members) . " members");
            $this->db->commit();

            $this->redirect('/dues', 'Dues created and assigned to ' . count($members) . ' members.', 'success');
        } catch (\Throwable $e) {
            $this->db->rollBack();
            error_log('Dues error: ' . $e->getMessage());
            $this->redirect('/dues/create', 'Error creating dues.', 'danger');
        }
    }

    public function show(string $id): void
    {
        $dues = $this->db->fetchOne(
            "SELECT d.*, dt.name as type_name FROM dues d LEFT JOIN dues_types dt ON dt.id = d.dues_type_id WHERE d.id = :id",
            ['id' => (int)$id]
        );
        if (!$dues) {
            $this->abort(404, 'Dues not found');
        }
        $members = $this->db->fetchAll(
            "SELECT md.*, m.first_name, m.last_name, m.member_number
             FROM member_dues md JOIN members m ON m.id = md.member_id
             WHERE md.dues_id = :id ORDER BY md.status, m.last_name",
            ['id' => (int)$id]
        );
        $this->setMenuActive('dues');
        $this->view('dues.show', ['dues' => $dues, 'members' => $members]);
    }

    public function members(string $id = ''): void
    {
        $request = new \App\Core\Request();
        $page = max(1, (int)$request->input('page', 1));

        $where = $id ? "md.dues_id = :did" : '1=1';
        $params = $id ? ['did' => (int)$id] : [];

        $total = (int)$this->db->fetchOne("SELECT COUNT(*) as c FROM member_dues md WHERE {$where}", $params)['c'];
        $pagination = $this->paginate($total, 20, $page);
        $params['limit'] = $pagination['per_page'];
        $params['offset'] = $pagination['offset'];

        $memberDues = $this->db->fetchAll(
            "SELECT md.*, m.first_name, m.last_name, m.member_number, d.name as dues_name
             FROM member_dues md
             JOIN members m ON m.id = md.member_id
             JOIN dues d ON d.id = md.dues_id
             WHERE {$where}
             ORDER BY md.status, m.last_name
             LIMIT :limit OFFSET :offset",
            $params
        );

        $this->setMenuActive('dues');
        $this->view('dues.member_dues', ['memberDues' => $memberDues, 'pagination' => $pagination]);
    }

    public function outstanding(): void
    {
        $outstanding = $this->db->fetchAll(
            "SELECT md.*, m.first_name, m.last_name, m.member_number, d.name as dues_name, s.name as section_name
             FROM member_dues md
             JOIN members m ON m.id = md.member_id
             JOIN dues d ON d.id = md.dues_id
             LEFT JOIN sections s ON s.id = m.section_id
             WHERE md.balance > 0
             ORDER BY md.balance DESC"
        );
        $this->setMenuActive('dues');
        $this->view('dues.outstanding', ['outstanding' => $outstanding]);
    }

    public function memberDues(): void
    {
        $request = new \App\Core\Request();
        $page = max(1, (int)$request->input('page', 1));

        $total = (int)$this->db->fetchOne("SELECT COUNT(*) as c FROM member_dues")['c'];
        $pagination = $this->paginate($total, 20, $page);

        $memberDues = $this->db->fetchAll(
            "SELECT md.*, m.first_name, m.last_name, m.member_number, d.name as dues_name
             FROM member_dues md
             JOIN members m ON m.id = md.member_id
             JOIN dues d ON d.id = md.dues_id
             ORDER BY md.status, m.last_name
             LIMIT :limit OFFSET :offset",
            ['limit' => $pagination['per_page'], 'offset' => $pagination['offset']]
        );

        $this->setMenuActive('dues');
        $this->view('dues.member_dues', ['memberDues' => $memberDues, 'pagination' => $pagination]);
    }

    public function overdue(): void
    {
        // Update overdue status first
        $this->db->execute(
            "UPDATE member_dues SET status = 'Overdue', updated_at = NOW()
             WHERE status IN ('Unpaid', 'Partial') AND due_date < CURRENT_DATE AND balance > 0"
        );

        $overdue = $this->db->fetchAll(
            "SELECT md.*, m.first_name, m.last_name, m.member_number, d.name as dues_name, s.name as section_name
             FROM member_dues md
             JOIN members m ON m.id = md.member_id
             JOIN dues d ON d.id = md.dues_id
             LEFT JOIN sections s ON s.id = m.section_id
             WHERE md.status = 'Overdue' AND md.balance > 0
             ORDER BY md.due_date"
        );
        $this->setMenuActive('dues');
        $this->view('dues.overdue', ['overdue' => $overdue]);
    }
}
