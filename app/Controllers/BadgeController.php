<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;
use App\Core\Auth;

class BadgeController extends Controller
{
    private Database $db;

    public function __construct()
    {
        parent::__construct();
        $this->authorize('badges.view');
        $this->db = Database::getInstance();
    }

    public function index(): void
    {
        $page = (int)($this->request->get('page') ?? 1);
        $total = (int)$this->db->fetchOne("SELECT COUNT(*) as c FROM badges")['c'];
        $pagination = $this->paginate($total, 20, $page);

        $badges = $this->db->fetchAll(
            "SELECT b.*, (SELECT COUNT(*) FROM member_badges mb WHERE mb.badge_id = b.id) as awarded_count
             FROM badges b ORDER BY b.name
             LIMIT :limit OFFSET :offset",
            ['limit' => $pagination['per_page'], 'offset' => $pagination['offset']]
        );
        $this->setMenuActive('badges');
        $this->view('badges.index', ['badges' => $badges, 'pagination' => $pagination]);
    }

    public function create(): void
    {
        $this->authorize('badges.create');
        $this->setMenuActive('badges');
        $this->view('badges.create');
    }

    public function store(): void
    {
        $this->authorize('badges.create');
        $this->verifyCsrf();

        $request = new \App\Core\Request();
        $data = $request->only(['name', 'description', 'requirements']);

        $this->db->execute(
            "INSERT INTO badges (name, description, requirements) VALUES (:n, :d, :r)",
            ['n' => $data['name'], 'd' => $data['description'] ?? null, 'r' => $data['requirements'] ?? null]
        );

        (new Auth())->logAction('badge_created', 'badge', null, "Created badge: {$data['name']}");
        $this->redirect('/badges', 'Badge created.', 'success');
    }

    public function show(string $id): void
    {
        $badge = $this->db->fetchOne("SELECT * FROM badges WHERE id = :id", ['id' => (int)$id]);
        if (!$badge) {
            $this->abort(404, 'Badge not found');
        }
        $members = $this->db->fetchAll(
            "SELECT mb.*, m.first_name, m.last_name, m.member_number
             FROM member_badges mb JOIN members m ON m.id = mb.member_id
             WHERE mb.badge_id = :id ORDER BY mb.date_awarded DESC", ['id' => (int)$id]
        );
        $allMembers = $this->db->fetchAll("SELECT id, member_number, first_name, last_name FROM members WHERE status = 'Active' ORDER BY first_name");

        $this->setMenuActive('badges');
        $this->view('badges.show', ['badge' => $badge, 'members' => $members, 'allMembers' => $allMembers]);
    }

    public function edit(string $id): void
    {
        $this->authorize('badges.create');
        $badge = $this->db->fetchOne("SELECT * FROM badges WHERE id = :id", ['id' => (int)$id]);
        if (!$badge) {
            $this->abort(404);
        }
        $this->setMenuActive('badges');
        $this->view('badges.edit', ['badge' => $badge]);
    }

    public function update(string $id): void
    {
        $this->authorize('badges.create');
        $this->verifyCsrf();

        $request = new \App\Core\Request();
        $data = $request->only(['name', 'description', 'requirements', 'status']);

        $this->db->execute(
            "UPDATE badges SET name = :n, description = :d, requirements = :r, status = :s WHERE id = :id",
            ['n' => $data['name'], 'd' => $data['description'] ?? null, 'r' => $data['requirements'] ?? null, 's' => $data['status'] ?? 'Active', 'id' => (int)$id]
        );

        $this->redirect('/badges/' . $id, 'Badge updated.', 'success');
    }

    public function award(string $id): void
    {
        $this->authorize('badges.award');
        $this->verifyCsrf();

        $request = new \App\Core\Request();
        $memberId = (int)$request->input('member_id');
        $notes = trim($request->input('notes', ''));

        $this->db->execute(
            "INSERT INTO member_badges (member_id, badge_id, awarded_by, notes) VALUES (:m, :b, :a, :n)
             ON DUPLICATE KEY UPDATE member_id = member_id",
            ['m' => $memberId, 'b' => (int)$id, 'a' => auth()->user()['full_name'] ?? 'Officer', 'n' => $notes]
        );

        // Notify member
        $badge = $this->db->fetchOne("SELECT name FROM badges WHERE id = :id", ['id' => (int)$id]);
        $this->db->execute(
            "INSERT INTO notifications (user_id, title, message, type) VALUES (:uid, 'Badge Awarded', :msg, 'success')",
            ['uid' => $memberId, 'msg' => "You have been awarded the {$badge['name']} badge!"]
        );

        $this->redirect("/badges/{$id}", 'Badge awarded successfully.', 'success');
    }

    public function members(string $id): void
    {
        $members = $this->db->fetchAll(
            "SELECT mb.*, m.first_name, m.last_name, m.member_number FROM member_badges mb 
             JOIN members m ON m.id = mb.member_id WHERE mb.badge_id = :id", ['id' => (int)$id]
        );
        $this->json(['members' => $members]);
    }

    public function delete(string $id): void
    {
        $this->authorize('badges.create');
        $this->verifyCsrf();

        $this->db->execute("DELETE FROM member_badges WHERE badge_id = :id", ['id' => (int)$id]);
        $this->db->execute("DELETE FROM badges WHERE id = :id", ['id' => (int)$id]);

        (new Auth())->logAction('badge_deleted', 'badge', (int)$id, "Deleted badge");
        $this->redirect('/badges', 'Badge deleted.', 'warning');
    }
}
