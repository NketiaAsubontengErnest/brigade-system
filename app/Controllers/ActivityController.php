<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;
use App\Core\Auth;

class ActivityController extends Controller
{
    private Database $db;

    public function __construct()
    {
        parent::__construct();
        $this->authorize('activities.view');
        $this->db = Database::getInstance();
    }

    public function index(): void
    {
        $request = new \App\Core\Request();
        $search = $request->input('search', '');
        $section = $request->input('section', '');
        $page = max(1, (int)$request->input('page', 1));

        $where = "a.status != 'Cancelled'";
        $params = [];

        if ($search) {
            $where .= "  AND (LOWER(a.name) LIKE LOWER(:search1) OR LOWER(a.description) LIKE LOWER(:search2))";
            $params['search1'] = $params['search2'] = "%{$search}%";
        }
        if ($section) {
            $where .= " AND a.section_id = :section";
            $params['section'] = $section;
        }

        $total = (int)$this->db->fetchOne("SELECT COUNT(*) as c FROM activities a WHERE {$where}", $params)['c'];
        $pagination = $this->paginate($total, 20, $page);
        $params['limit'] = $pagination['per_page'];
        $params['offset'] = $pagination['offset'];

        $activities = $this->db->fetchAll(
            "SELECT a.*, s.name as section_name, m.first_name, m.last_name
             FROM activities a
             LEFT JOIN sections s ON s.id = a.section_id
             LEFT JOIN members m ON m.id = a.officer_in_charge
             WHERE {$where}
             ORDER BY a.date DESC, a.start_time DESC
             LIMIT :limit OFFSET :offset", $params
        );
        $sections = $this->db->fetchAll("SELECT * FROM sections WHERE status = 'Active' ORDER BY name");

        $this->setMenuActive('activities');
        $this->view('activities.index', ['activities' => $activities, 'sections' => $sections, 'pagination' => $pagination, 'search' => $search, 'filterSection' => $section]);
    }

    public function create(): void
    {
        $this->authorize('activities.create');
        $sections = $this->db->fetchAll("SELECT * FROM sections WHERE status = 'Active' ORDER BY name");
        $this->setMenuActive('activities');
        $this->view('activities.create', ['sections' => $sections]);
    }

    public function store(): void
    {
        $this->authorize('activities.create');
        $this->verifyCsrf();

        $request = new \App\Core\Request();
        $data = $request->only(['name', 'description', 'date', 'start_time', 'end_time', 'location', 'section_id', 'officer_in_charge']);
        $data = array_map(fn($v) => is_string($v) ? trim($v) : $v, $data);

        $this->db->execute(
            "INSERT INTO activities (name, description, date, start_time, end_time, location, section_id, officer_in_charge)
             VALUES (:name, :desc, :date, :start, :end, :loc, :section, :officer)",
            [
                'name' => $data['name'], 'desc' => $data['description'] ?? null, 'date' => $data['date'],
                'start' => $data['start_time'] ?: null, 'end' => $data['end_time'] ?: null,
                'loc' => $data['location'] ?? null, 'section' => $data['section_id'] ?: null,
                'officer' => $data['officer_in_charge'] ?: null,
            ]
        );

        (new Auth())->logAction('activity_created', 'activity', null, "Created activity: {$data['name']}");
        $this->redirect('/activities/manage', 'Activity created successfully.', 'success');
    }

    public function show(string $id): void
    {
        $activity = $this->db->fetchOne(
            "SELECT a.*, s.name as section_name, m.first_name, m.last_name
             FROM activities a LEFT JOIN sections s ON s.id = a.section_id LEFT JOIN members m ON m.id = a.officer_in_charge
             WHERE a.id = :id", ['id' => (int)$id]
        );
        if (!$activity) {
            $this->abort(404, 'Activity not found');
        }

        $sessions = $this->db->fetchAll(
            "SELECT ats.*, 
                (SELECT COUNT(*) FROM attendance WHERE session_id = ats.id AND status = 'Present') as present_count,
                (SELECT COUNT(*) FROM attendance WHERE session_id = ats.id) as total_count
             FROM attendance_sessions ats WHERE ats.activity_id = :id ORDER BY ats.date DESC",
            ['id' => (int)$id]
        );

        $this->setMenuActive('activities');
        $this->view('activities.show', ['activity' => $activity, 'sessions' => $sessions]);
    }

    public function edit(string $id): void
    {
        $this->authorize('activities.edit');
        $activity = $this->db->fetchOne("SELECT * FROM activities WHERE id = :id", ['id' => (int)$id]);
        if (!$activity) {
            $this->abort(404, 'Activity not found');
        }
        $sections = $this->db->fetchAll("SELECT * FROM sections WHERE status = 'Active' ORDER BY name");
        $this->setMenuActive('activities');
        $this->view('activities.edit', ['activity' => $activity, 'sections' => $sections]);
    }

    public function update(string $id): void
    {
        $this->authorize('activities.edit');
        $this->verifyCsrf();

        $request = new \App\Core\Request();
        $data = $request->only(['name', 'description', 'date', 'start_time', 'end_time', 'location', 'section_id', 'officer_in_charge', 'status']);

        $this->db->execute(
            "UPDATE activities SET name = :name, description = :desc, date = :date, start_time = :start, end_time = :end,
             location = :loc, section_id = :section, officer_in_charge = :officer, status = :status, updated_at = NOW()
             WHERE id = :id",
            [
                'name' => $data['name'], 'desc' => $data['description'] ?? null, 'date' => $data['date'],
                'start' => $data['start_time'] ?: null, 'end' => $data['end_time'] ?: null,
                'loc' => $data['location'] ?? null, 'section' => $data['section_id'] ?: null,
                'officer' => $data['officer_in_charge'] ?: null, 'status' => $data['status'] ?? 'Scheduled',
                'id' => (int)$id,
            ]
        );

        $this->redirect('/activities/' . $id, 'Activity updated successfully.', 'success');
    }

    public function delete(string $id): void
    {
        $this->authorize('activities.delete');
        $this->verifyCsrf();

        $this->db->execute("UPDATE activities SET status = 'Cancelled' WHERE id = :id", ['id' => (int)$id]);
        (new Auth())->logAction('activity_cancelled', 'activity', (int)$id, "Cancelled activity");
        $this->redirect('/activities/manage', 'Activity cancelled.', 'warning');
    }
}
