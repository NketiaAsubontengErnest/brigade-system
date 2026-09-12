<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;
use App\Core\Auth;

class EventController extends Controller
{
    private Database $db;

    public function __construct()
    {
        parent::__construct();
        $this->authorize('events.view');
        $this->db = Database::getInstance();
    }

    public function index(): void
    {
        $request = new \App\Core\Request();
        $search = $request->input('search', '');
        $page = max(1, (int)$request->input('page', 1));

        $where = "1=1";
        $params = [];
        if ($search) {
            $where .= "  AND (LOWER(e.name) LIKE LOWER(:search1) OR LOWER(e.description) LIKE LOWER(:search2))";
            $params['search1'] = $params['search2'] = "%{$search}%";
        }

        $total = (int)$this->db->fetchOne("SELECT COUNT(*) as c FROM events e WHERE {$where}", $params)['c'];
        $pagination = $this->paginate($total, 20, $page);
        $params['limit'] = $pagination['per_page'];
        $params['offset'] = $pagination['offset'];

        $events = $this->db->fetchAll(
            "SELECT e.*, 
                (SELECT COUNT(*) FROM event_registrations er WHERE er.event_id = e.id AND er.status != 'Cancelled') as registered_count
             FROM events e WHERE {$where} ORDER BY e.start_date DESC LIMIT :limit OFFSET :offset",
            $params
        );

        $this->setMenuActive('events');
        $this->view('events.index', ['events' => $events, 'pagination' => $pagination, 'search' => $search]);
    }

    public function create(): void
    {
        $this->authorize('events.create');
        $this->setMenuActive('events');
        $this->view('events.create');
    }

    public function store(): void
    {
        $this->authorize('events.create');
        $this->verifyCsrf();

        $request = new \App\Core\Request();
        $data = $request->only(['name', 'description', 'start_date', 'end_date', 'start_time', 'end_time', 'location', 'registration_deadline', 'maximum_participants', 'fee']);

        $this->db->execute(
            "INSERT INTO events (name, description, start_date, end_date, start_time, end_time, location, registration_deadline, maximum_participants, fee)
             VALUES (:name, :desc, :sd, :ed, :st, :et, :loc, :rd, :mp, :fee)",
            [
                'name' => $data['name'], 'desc' => $data['description'] ?? null, 'sd' => $data['start_date'],
                'ed' => $data['end_date'] ?? null, 'st' => $data['start_time'] ?? null, 'et' => $data['end_time'] ?? null,
                'loc' => $data['location'] ?? null, 'rd' => $data['registration_deadline'] ?? null,
                'mp' => $data['maximum_participants'] ?: null, 'fee' => $data['fee'] ?? 0,
            ]
        );

        (new Auth())->logAction('event_created', 'event', null, "Created event: {$data['name']}");
        $this->redirect('/events/manage', 'Event created successfully.', 'success');
    }

    public function show(string $id): void
    {
        $event = $this->db->fetchOne(
            "SELECT e.*, 
                (SELECT COUNT(*) FROM event_registrations er WHERE er.event_id = e.id AND er.status != 'Cancelled') as registered_count
             FROM events e WHERE e.id = :id", ['id' => (int)$id]
        );
        if (!$event) {
            $this->abort(404, 'Event not found');
        }
        $registrations = $this->db->fetchAll(
            "SELECT er.*, m.first_name, m.last_name, m.member_number
             FROM event_registrations er
             JOIN members m ON m.id = er.member_id
             WHERE er.event_id = :id ORDER BY er.registered_at DESC",
            ['id' => (int)$id]
        );
        $members = $this->db->fetchAll("SELECT id, member_number, first_name, last_name FROM members WHERE status = 'Active' ORDER BY first_name");

        $this->setMenuActive('events');
        $this->view('events.show', ['event' => $event, 'registrations' => $registrations, 'members' => $members]);
    }

    public function edit(string $id): void
    {
        $this->authorize('events.edit');
        $event = $this->db->fetchOne("SELECT * FROM events WHERE id = :id", ['id' => (int)$id]);
        if (!$event) {
            $this->abort(404, 'Event not found');
        }
        $this->setMenuActive('events');
        $this->view('events.edit', ['event' => $event]);
    }

    public function update(string $id): void
    {
        $this->authorize('events.edit');
        $this->verifyCsrf();

        $request = new \App\Core\Request();
        $data = $request->only(['name', 'description', 'start_date', 'end_date', 'start_time', 'end_time', 'location', 'registration_deadline', 'maximum_participants', 'fee', 'status']);

        $setClauses = [];
        $bindings = ['id' => (int)$id];
        foreach ($data as $key => $value) {
            $setClauses[] = "{$key} = :{$key}";
            $bindings[$key] = $value ?: null;
        }
        $setClauses[] = "updated_at = NOW()";

        $this->db->execute("UPDATE events SET " . implode(', ', $setClauses) . " WHERE id = :id", $bindings);
        $this->redirect('/events/' . $id, 'Event updated successfully.', 'success');
    }

    public function delete(string $id): void
    {
        $this->authorize('events.delete');
        $this->verifyCsrf();
        $this->db->execute("UPDATE events SET status = 'Cancelled' WHERE id = :id", ['id' => (int)$id]);
        $this->redirect('/events/manage', 'Event cancelled.', 'warning');
    }

    public function registerMember(string $id): void
    {
        $this->authorize('events.create');
        $this->verifyCsrf();

        $request = new \App\Core\Request();
        $memberId = (int)$request->input('member_id');

        // Check max participants
        $event = $this->db->fetchOne("SELECT * FROM events WHERE id = :id", ['id' => (int)$id]);
        if ($event['maximum_participants']) {
            $count = (int)$this->db->fetchOne(
                "SELECT COUNT(*) as c FROM event_registrations WHERE event_id = :e AND status != 'Cancelled'",
                ['e' => (int)$id]
            )['c'];
            if ($count >= $event['maximum_participants']) {
                $this->redirect("/events/{$id}", 'Event is full.', 'warning');
                return;
            }
        }

        $this->db->execute(
            "INSERT INTO event_registrations (event_id, member_id) VALUES (:e, :m)
             ON DUPLICATE KEY UPDATE status = 'Registered'",
            ['e' => (int)$id, 'm' => $memberId]
        );

        $this->redirect("/events/{$id}", 'Member registered for event.', 'success');
    }

    public function registrations(string $id): void
    {
        $registrations = $this->db->fetchAll(
            "SELECT er.*, m.first_name, m.last_name, m.member_number
             FROM event_registrations er
             JOIN members m ON m.id = er.member_id
             WHERE er.event_id = :id ORDER BY er.registered_at DESC",
            ['id' => (int)$id]
        );
        $this->json(['registrations' => $registrations]);
    }
}
