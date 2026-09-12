<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;
use App\Core\Validator;
use App\Core\Auth;

class SectionController extends Controller
{
    private Database $db;

    public function __construct()
    {
        parent::__construct();
        $this->authorize('sections.view');
        $this->db = Database::getInstance();
    }

    public function index(): void
    {
        $page = (int)($this->request->get('page') ?? 1);
        $total = (int)$this->db->fetchOne("SELECT COUNT(*) as c FROM sections")['c'];
        $pagination = $this->paginate($total, 20, $page);

        $sections = $this->db->fetchAll(
            "SELECT s.*, COUNT(m.id) as member_count 
             FROM sections s 
             LEFT JOIN members m ON m.section_id = s.id AND m.status = 'Active'
             GROUP BY s.id ORDER BY s.name
             LIMIT :limit OFFSET :offset",
            ['limit' => $pagination['per_page'], 'offset' => $pagination['offset']]
        );
        $this->setMenuActive('sections');
        $this->view('sections.index', ['sections' => $sections, 'pagination' => $pagination]);
    }

    public function create(): void
    {
        $this->authorize('sections.create');
        $this->setMenuActive('sections');
        $this->view('sections.create');
    }

    public function store(): void
    {
        $this->authorize('sections.create');
        $this->verifyCsrf();

        $request = new \App\Core\Request();
        $data = $request->only(['name', 'description', 'age_range', 'type']);
        $data = array_map(fn($v) => is_string($v) ? trim($v) : $v, $data);

        $validator = new Validator();
        if (!$validator->validate($data, ['name' => 'required|max:100'])) {
            Validator::flashInput($data);
            $this->redirect('/sections/create', 'Section name is required.', 'danger');
            return;
        }

        $this->db->execute(
            "INSERT INTO sections (name, description, age_range, type) VALUES (:name, :desc, :age, :type)",
            ['name' => $data['name'], 'desc' => $data['description'] ?? null, 'age' => $data['age_range'] ?? null, 'type' => $data['type'] ?? 'Section']
        );

        (new Auth())->logAction('section_created', 'section', null, "Created section: {$data['name']}");
        $this->redirect('/sections', 'Section created successfully.', 'success');
    }

    public function show(string $id): void
    {
        $section = $this->db->fetchOne("SELECT * FROM sections WHERE id = :id", ['id' => (int)$id]);
        if (!$section) {
            $this->abort(404, 'Section not found');
        }
        $members = $this->db->fetchAll(
            "SELECT * FROM members WHERE section_id = :id AND status = 'Active' ORDER BY last_name, first_name",
            ['id' => (int)$id]
        );
        $this->setMenuActive('sections');
        $this->view('sections.show', ['section' => $section, 'members' => $members]);
    }

    public function edit(string $id): void
    {
        $this->authorize('sections.edit');
        $section = $this->db->fetchOne("SELECT * FROM sections WHERE id = :id", ['id' => (int)$id]);
        if (!$section) {
            $this->abort(404, 'Section not found');
        }
        $this->setMenuActive('sections');
        $this->view('sections.edit', ['section' => $section]);
    }

    public function update(string $id): void
    {
        $this->authorize('sections.edit');
        $this->verifyCsrf();

        $request = new \App\Core\Request();
        $data = $request->only(['name', 'description', 'age_range', 'status', 'type']);

        $this->db->execute(
            "UPDATE sections SET name = :name, description = :desc, age_range = :age, status = :status, type = :type, updated_at = NOW() WHERE id = :id",
            ['name' => $data['name'], 'desc' => $data['description'] ?? null, 'age' => $data['age_range'] ?? null, 'status' => $data['status'] ?? 'Active', 'type' => $data['type'] ?? 'Section', 'id' => (int)$id]
        );

        (new Auth())->logAction('section_updated', 'section', (int)$id, "Updated section: {$data['name']}");
        $this->redirect('/sections', 'Section updated successfully.', 'success');
    }

    public function toggle(string $id): void
    {
        $this->authorize('sections.edit');
        $this->verifyCsrf();

        $section = $this->db->fetchOne("SELECT * FROM sections WHERE id = :id", ['id' => (int)$id]);
        if (!$section) {
            $this->abort(404, 'Section not found');
        }

        $newStatus = $section['status'] === 'Active' ? 'Inactive' : 'Active';
        $this->db->execute(
            "UPDATE sections SET status = :status, updated_at = NOW() WHERE id = :id",
            ['status' => $newStatus, 'id' => (int)$id]
        );

        $this->redirect('/sections', "Section {$newStatus}.", 'success');
    }

    public function delete(string $id): void
    {
        $this->authorize('sections.create');
        $this->verifyCsrf();

        // Unassign members from this section
        $this->db->execute("UPDATE members SET section_id = NULL WHERE section_id = :id", ['id' => (int)$id]);
        $this->db->execute("DELETE FROM sections WHERE id = :id", ['id' => (int)$id]);

        (new Auth())->logAction('section_deleted', 'section', (int)$id, "Deleted section");
        $this->redirect('/sections', 'Section deleted.', 'warning');
    }
}
