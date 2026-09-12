<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;
use App\Core\Auth;

class OfficerController extends Controller
{
    private Database $db;

    public function __construct()
    {
        parent::__construct();
        $this->authorize('officers.view');
        $this->db = Database::getInstance();
    }

    public function index(): void
    {
        $page = (int)($this->request->get('page') ?? 1);
        $search = trim((string)($this->request->get('search') ?? ''));

        $where = "o.status = 'Active'";
        $params = [];
        if ($search !== '') {
            $where .= " AND (LOWER(m.first_name) LIKE LOWER(:search1) OR LOWER(m.last_name) LIKE LOWER(:search2) OR LOWER(p.name) LIKE LOWER(:search3))";
            $params['search1'] = $params['search2'] = $params['search3'] = "%{$search}%";
        }

        $total = (int)$this->db->fetchOne("SELECT COUNT(*) as c FROM officers o JOIN members m ON m.id = o.member_id LEFT JOIN positions p ON p.id = o.position_id WHERE {$where}", $params)['c'];
        $pagination = $this->paginate($total, 20, $page);
        $params['limit'] = $pagination['per_page'];
        $params['offset'] = $pagination['offset'];

        $officers = $this->db->fetchAll(
            "SELECT o.*, m.first_name, m.last_name, m.profile_photo, m.member_number, p.name as position_name
             FROM officers o
             JOIN members m ON m.id = o.member_id
             LEFT JOIN positions p ON p.id = o.position_id
             WHERE {$where}
             ORDER BY p.id ASC, m.last_name ASC
             LIMIT :limit OFFSET :offset",
            $params
        );
        $this->setMenuActive('officers');
        $this->view('officers.index', ['officers' => $officers, 'pagination' => $pagination, 'search' => $search]);
    }

    public function create(): void
    {
        $this->authorize('officers.create');
        $members = $this->db->fetchAll("SELECT id, member_number, first_name, last_name FROM members WHERE status = 'Active' ORDER BY first_name");
        $positions = $this->db->fetchAll("SELECT * FROM positions WHERE status = 'Active' ORDER BY name");
        $this->setMenuActive('officers');
        $this->view('officers.create', ['members' => $members, 'positions' => $positions]);
    }

    public function store(): void
    {
        $this->authorize('officers.create');
        $this->verifyCsrf();

        $request = new \App\Core\Request();
        $memberId = (int)$request->input('member_id');
        $positionId = (int)$request->input('position_id');
        $startDate = $request->input('start_date') ?: date('Y-m-d');

        if (!$memberId || !$positionId) {
            $this->redirect('/officers/create', 'Member and Position are required.', 'danger');
            return;
        }

        // Check if member is already an active officer for this position
        $exists = $this->db->fetchOne(
            "SELECT id FROM officers WHERE member_id = :m AND position_id = :p AND status = 'Active'",
            ['m' => $memberId, 'p' => $positionId]
        );
        if ($exists) {
            $this->redirect('/officers/create', 'This member already holds this position.', 'warning');
            return;
        }

        $this->db->execute(
            "INSERT INTO officers (member_id, position_id, start_date, status) VALUES (:m, :p, :d, 'Active')",
            ['m' => $memberId, 'p' => $positionId, 'd' => $startDate]
        );

        (new Auth())->logAction('officer_appointed', 'officer', null, "Appointed officer position");
        $this->redirect('/officers', 'Officer appointed successfully.', 'success');
    }

    public function show(string $id): void
    {
        $officer = $this->db->fetchOne(
            "SELECT o.*, m.first_name, m.last_name, m.profile_photo, m.member_number, m.phone, m.email, p.name as position_name
             FROM officers o JOIN members m ON m.id = o.member_id LEFT JOIN positions p ON p.id = o.position_id
             WHERE o.id = :id", ['id' => (int)$id]
        );
        if (!$officer) {
            $this->abort(404, 'Officer not found');
        }
        $this->setMenuActive('officers');
        $this->view('officers.show', ['officer' => $officer]);
    }

    public function edit(string $id): void
    {
        $this->authorize('officers.edit');
        $officer = $this->db->fetchOne("SELECT * FROM officers WHERE id = :id", ['id' => (int)$id]);
        if (!$officer) {
            $this->abort(404, 'Officer not found');
        }
        $positions = $this->db->fetchAll("SELECT * FROM positions WHERE status = 'Active' ORDER BY name");
        $this->setMenuActive('officers');
        $this->view('officers.edit', ['officer' => $officer, 'positions' => $positions]);
    }

    public function update(string $id): void
    {
        $this->authorize('officers.edit');
        $this->verifyCsrf();

        $request = new \App\Core\Request();
        $this->db->execute(
            "UPDATE officers SET position_id = :p, start_date = :sd, end_date = :ed, status = :st, updated_at = NOW() WHERE id = :id",
            [
                'p' => $request->input('position_id'), 'sd' => $request->input('start_date'),
                'ed' => $request->input('end_date') ?: null, 'st' => $request->input('status'), 'id' => (int)$id,
            ]
        );

        $this->redirect('/officers', 'Officer updated successfully.', 'success');
    }

    public function deactivate(string $id): void
    {
        $this->authorize('officers.edit');
        $this->verifyCsrf();

        $this->db->execute(
            "UPDATE officers SET status = 'Inactive', end_date = CURRENT_DATE, updated_at = NOW() WHERE id = :id",
            ['id' => (int)$id]
        );

        $this->redirect('/officers', 'Officer deactivated.', 'warning');
    }

    public function positions(): void
    {
        $positions = $this->db->fetchAll("SELECT * FROM positions ORDER BY id");
        $this->setMenuActive('officers');
        $this->view('officers.positions', ['positions' => $positions]);
    }

    public function storePosition(): void
    {
        $this->authorize('officers.create');
        $this->verifyCsrf();

        $request = new \App\Core\Request();
        $name = trim($request->input('name', ''));
        $description = trim($request->input('description', ''));

        if (empty($name)) {
            $this->redirect('/officers/positions', 'Position name is required.', 'danger');
            return;
        }

        $this->db->execute(
            "INSERT INTO positions (name, description) VALUES (:n, :d) ON DUPLICATE KEY UPDATE name = name",
            ['n' => $name, 'd' => $description]
        );

        $this->redirect('/officers/positions', 'Position created.', 'success');
    }

    public function updatePosition(string $id): void
    {
        $this->authorize('officers.edit');
        $this->verifyCsrf();

        $request = new \App\Core\Request();
        $this->db->execute(
            "UPDATE positions SET name = :n, description = :d, status = :s WHERE id = :id",
            ['n' => $request->input('name'), 'd' => $request->input('description'), 's' => $request->input('status', 'Active'), 'id' => (int)$id]
        );

        $this->redirect('/officers/positions', 'Position updated.', 'success');
    }
}
