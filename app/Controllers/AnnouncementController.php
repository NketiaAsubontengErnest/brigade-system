<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;
use App\Core\Auth;

class AnnouncementController extends Controller
{
    private Database $db;

    public function __construct()
    {
        parent::__construct();
        $this->authorize('announcements.view');
        $this->db = Database::getInstance();
    }

    public function index(): void
    {
        $page = (int)($this->request->get('page') ?? 1);
        $total = (int)$this->db->fetchOne("SELECT COUNT(*) as c FROM announcements")['c'];
        $pagination = $this->paginate($total, 20, $page);

        $announcements = $this->db->fetchAll(
            "SELECT a.*, u.full_name as creator_name FROM announcements a LEFT JOIN users u ON u.id = a.created_by ORDER BY a.created_at DESC LIMIT :limit OFFSET :offset",
            ['limit' => $pagination['per_page'], 'offset' => $pagination['offset']]
        );
        $this->setMenuActive('announcements');
        $this->view('announcements.index', ['announcements' => $announcements, 'pagination' => $pagination]);
    }

    public function create(): void
    {
        $this->authorize('announcements.create');
        $this->setMenuActive('announcements');
        $this->view('announcements.create');
    }

    public function store(): void
    {
        $this->authorize('announcements.create');
        $this->verifyCsrf();

        $request = new \App\Core\Request();
        $data = $request->only(['title', 'content', 'publish_date', 'expiry_date', 'status', 'is_public']);

        $this->db->execute(
            "INSERT INTO announcements (title, content, publish_date, expiry_date, status, is_public, created_by)
             VALUES (:t, :c, :pd, :ed, :s, :ip, :cb)",
            [
                't' => $data['title'], 'c' => $data['content'], 'pd' => $data['publish_date'] ?: date('Y-m-d'),
                'ed' => $data['expiry_date'] ?: null, 's' => $data['status'] ?? 'Published',
                'ip' => isset($data['is_public']) ? true : false, 'cb' => auth()->id(),
            ]
        );

        (new Auth())->logAction('announcement_created', 'announcement', null, "Created announcement: {$data['title']}");
        $this->redirect('/announcements', 'Announcement created.', 'success');
    }

    public function show(string $id): void
    {
        $announcement = $this->db->fetchOne(
            "SELECT a.*, u.full_name as creator_name FROM announcements a LEFT JOIN users u ON u.id = a.created_by WHERE a.id = :id",
            ['id' => (int)$id]
        );
        if (!$announcement) {
            $this->abort(404, 'Announcement not found');
        }
        $this->setMenuActive('announcements');
        $this->view('announcements.show', ['announcement' => $announcement]);
    }

    public function edit(string $id): void
    {
        $this->authorize('announcements.edit');
        $announcement = $this->db->fetchOne("SELECT * FROM announcements WHERE id = :id", ['id' => (int)$id]);
        if (!$announcement) {
            $this->abort(404);
        }
        $this->setMenuActive('announcements');
        $this->view('announcements.edit', ['announcement' => $announcement]);
    }

    public function update(string $id): void
    {
        $this->authorize('announcements.edit');
        $this->verifyCsrf();

        $request = new \App\Core\Request();
        $data = $request->only(['title', 'content', 'publish_date', 'expiry_date', 'status', 'is_public']);

        $this->db->execute(
            "UPDATE announcements SET title = :t, content = :c, publish_date = :pd, expiry_date = :ed, status = :s, is_public = :ip WHERE id = :id",
            [
                't' => $data['title'], 'c' => $data['content'], 'pd' => $data['publish_date'] ?? date('Y-m-d'),
                'ed' => $data['expiry_date'] ?: null, 's' => $data['status'], 'ip' => isset($data['is_public']) ? true : false, 'id' => (int)$id,
            ]
        );

        $this->redirect('/announcements', 'Announcement updated.', 'success');
    }

    public function delete(string $id): void
    {
        $this->authorize('announcements.delete');
        $this->verifyCsrf();

        $this->db->execute("DELETE FROM announcements WHERE id = :id", ['id' => (int)$id]);
        $this->redirect('/announcements', 'Announcement deleted.', 'warning');
    }
}
