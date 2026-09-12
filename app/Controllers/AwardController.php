<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;
use App\Core\Auth;

class AwardController extends Controller
{
    private Database $db;

    public function __construct()
    {
        parent::__construct();
        $this->authorize('awards.view');
        $this->db = Database::getInstance();
    }

    public function index(): void
    {
        $awards = $this->db->fetchAll(
            "SELECT a.*, m.first_name, m.last_name, m.member_number
             FROM awards a JOIN members m ON m.id = a.member_id ORDER BY a.date_awarded DESC"
        );
        $this->setMenuActive('awards');
        $this->view('awards.index', ['awards' => $awards]);
    }

    public function create(): void
    {
        $this->authorize('awards.create');
        $members = $this->db->fetchAll("SELECT id, member_number, first_name, last_name FROM members WHERE status = 'Active' ORDER BY first_name");
        $this->setMenuActive('awards');
        $this->view('awards.create', ['members' => $members]);
    }

    public function store(): void
    {
        $this->authorize('awards.create');
        $this->verifyCsrf();

        $request = new \App\Core\Request();
        $data = $request->only(['member_id', 'name', 'description', 'date_awarded', 'awarded_by', 'notes']);

        $this->db->execute(
            "INSERT INTO awards (member_id, name, description, date_awarded, awarded_by, notes) VALUES (:m, :n, :d, :da, :ab, :nt)",
            [
                'm' => $data['member_id'], 'n' => $data['name'], 'd' => $data['description'] ?? null,
                'da' => $data['date_awarded'] ?: date('Y-m-d'), 'ab' => $data['awarded_by'] ?? null, 'nt' => $data['notes'] ?? null,
            ]
        );

        // Notify member
        $this->db->execute(
            "INSERT INTO notifications (user_id, title, message, type) VALUES (:uid, 'Award Received', :msg, 'success')",
            ['uid' => $data['member_id'], 'msg' => "You have received the award: {$data['name']}"]
        );

        (new Auth())->logAction('award_given', 'award', null, "Awarded: {$data['name']}");
        $this->redirect('/awards', 'Award given successfully.', 'success');
    }

    public function show(string $id): void
    {
        $award = $this->db->fetchOne(
            "SELECT a.*, m.first_name, m.last_name, m.member_number FROM awards a JOIN members m ON m.id = a.member_id WHERE a.id = :id",
            ['id' => (int)$id]
        );
        if (!$award) {
            $this->abort(404, 'Award not found');
        }
        $this->setMenuActive('awards');
        $this->view('awards.show', ['award' => $award]);
    }

    public function edit(string $id): void
    {
        $this->authorize('awards.create');
        $award = $this->db->fetchOne("SELECT * FROM awards WHERE id = :id", ['id' => (int)$id]);
        if (!$award) {
            $this->abort(404);
        }
        $this->setMenuActive('awards');
        $this->view('awards.edit', ['award' => $award]);
    }

    public function update(string $id): void
    {
        $this->authorize('awards.create');
        $this->verifyCsrf();

        $request = new \App\Core\Request();
        $data = $request->only(['name', 'description', 'date_awarded', 'awarded_by', 'notes']);

        $this->db->execute(
            "UPDATE awards SET name = :n, description = :d, date_awarded = :da, awarded_by = :ab, notes = :nt WHERE id = :id",
            ['n' => $data['name'], 'd' => $data['description'] ?? null, 'da' => $data['date_awarded'], 'ab' => $data['awarded_by'] ?? null, 'nt' => $data['notes'] ?? null, 'id' => (int)$id]
        );

        $this->redirect('/awards', 'Award updated.', 'success');
    }
}
