<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;
use App\Core\Auth;

class TrainingController extends Controller
{
    private Database $db;

    public function __construct()
    {
        parent::__construct();
        $this->authorize('training.view');
        $this->db = Database::getInstance();
    }

    public function index(): void
    {
        $page = (int)($this->request->get('page') ?? 1);
        $total = (int)$this->db->fetchOne("SELECT COUNT(*) as c FROM training_courses")['c'];
        $pagination = $this->paginate($total, 20, $page);

        $courses = $this->db->fetchAll(
            "SELECT tc.*, 
                (SELECT COUNT(*) FROM training_enrollments te WHERE te.course_id = tc.id AND te.status != 'Failed') as enrolled_count,
                (SELECT COUNT(*) FROM training_enrollments te WHERE te.course_id = tc.id AND te.status = 'Completed') as completed_count
             FROM training_courses tc ORDER BY tc.created_at DESC
             LIMIT :limit OFFSET :offset",
            ['limit' => $pagination['per_page'], 'offset' => $pagination['offset']]
        );
        $this->setMenuActive('training');
        $this->view('training.index', ['courses' => $courses, 'pagination' => $pagination]);
    }

    public function create(): void
    {
        $this->authorize('training.create');
        $this->setMenuActive('training');
        $this->view('training.create');
    }

    public function store(): void
    {
        $this->authorize('training.create');
        $this->verifyCsrf();

        $request = new \App\Core\Request();
        $data = $request->only(['name', 'description', 'duration', 'instructor']);

        $this->db->execute(
            "INSERT INTO training_courses (name, description, duration, instructor) VALUES (:n, :d, :dur, :i)",
            ['n' => $data['name'], 'd' => $data['description'] ?? null, 'dur' => $data['duration'] ?? null, 'i' => $data['instructor'] ?? null]
        );

        (new Auth())->logAction('training_created', 'training', null, "Created training course: {$data['name']}");
        $this->redirect('/training', 'Training course created.', 'success');
    }

    public function show(string $id): void
    {
        $course = $this->db->fetchOne("SELECT * FROM training_courses WHERE id = :id", ['id' => (int)$id]);
        if (!$course) {
            $this->abort(404, 'Course not found');
        }
        $enrollments = $this->db->fetchAll(
            "SELECT te.*, m.first_name, m.last_name, m.member_number
             FROM training_enrollments te JOIN members m ON m.id = te.member_id
             WHERE te.course_id = :id ORDER BY te.created_at DESC", ['id' => (int)$id]
        );
        $members = $this->db->fetchAll("SELECT id, member_number, first_name, last_name FROM members WHERE status = 'Active' ORDER BY first_name");

        $this->setMenuActive('training');
        $this->view('training.show', ['course' => $course, 'enrollments' => $enrollments, 'members' => $members]);
    }

    public function edit(string $id): void
    {
        $this->authorize('training.edit');
        $course = $this->db->fetchOne("SELECT * FROM training_courses WHERE id = :id", ['id' => (int)$id]);
        if (!$course) {
            $this->abort(404);
        }
        $this->setMenuActive('training');
        $this->view('training.edit', ['course' => $course]);
    }

    public function update(string $id): void
    {
        $this->authorize('training.edit');
        $this->verifyCsrf();

        $request = new \App\Core\Request();
        $data = $request->only(['name', 'description', 'duration', 'instructor', 'status']);

        $this->db->execute(
            "UPDATE training_courses SET name = :n, description = :d, duration = :dur, instructor = :i, status = :s, updated_at = NOW() WHERE id = :id",
            ['n' => $data['name'], 'd' => $data['description'] ?? null, 'dur' => $data['duration'] ?? null, 'i' => $data['instructor'] ?? null, 's' => $data['status'] ?? 'Active', 'id' => (int)$id]
        );

        $this->redirect('/training/' . $id, 'Course updated.', 'success');
    }

    public function enrollMember(string $id): void
    {
        $this->authorize('training.edit');
        $this->verifyCsrf();

        $request = new \App\Core\Request();
        $memberId = (int)$request->input('member_id');

        $this->db->execute(
            "INSERT INTO training_enrollments (member_id, course_id) VALUES (:m, :c) ON DUPLICATE KEY UPDATE member_id = member_id",
            ['m' => $memberId, 'c' => (int)$id]
        );

        $this->redirect("/training/{$id}", 'Member enrolled successfully.', 'success');
    }

    public function updateEnrollment(string $id): void
    {
        $this->authorize('training.edit');
        $this->verifyCsrf();

        $request = new \App\Core\Request();
        $status = $request->input('status', 'Enrolled');
        $score = $request->input('score') ?: null;
        $completionDate = $completionDate = $status === 'Completed' ? ($request->input('completion_date') ?: date('Y-m-d')) : null;

        $this->db->execute(
            "UPDATE training_enrollments SET status = :s, score = :sc, completion_date = :cd, notes = :n WHERE id = :id",
            ['s' => $status, 'sc' => $score, 'cd' => $completionDate, 'n' => $request->input('notes') ?? null, 'id' => (int)$id]
        );

        // Notify member if completed
        if ($status === 'Completed') {
            $enrollment = $this->db->fetchOne(
                "SELECT te.member_id, tc.name as course_name FROM training_enrollments te 
                 JOIN training_courses tc ON tc.id = te.course_id WHERE te.id = :id", ['id' => (int)$id]
            );
            if ($enrollment) {
                $this->db->execute(
                    "INSERT INTO notifications (user_id, title, message, type) VALUES (:uid, 'Training Completed', :msg, 'success')",
                    ['uid' => $enrollment['member_id'], 'msg' => "You have completed: {$enrollment['course_name']}"]
                );
            }
        }

        $this->redirect($_SERVER['HTTP_REFERER'] ?? '/training', 'Enrollment updated.', 'success');
    }

    public function enrollments(string $id): void
    {
        $enrollments = $this->db->fetchAll(
            "SELECT te.*, m.first_name, m.last_name FROM training_enrollments te 
             JOIN members m ON m.id = te.member_id WHERE te.course_id = :id", ['id' => (int)$id]
        );
        $this->json(['enrollments' => $enrollments]);
    }

    public function delete(string $id): void
    {
        $this->authorize('training.create');
        $this->verifyCsrf();

        $this->db->execute("DELETE FROM training_enrollments WHERE course_id = :id", ['id' => (int)$id]);
        $this->db->execute("DELETE FROM training_courses WHERE id = :id", ['id' => (int)$id]);

        (new Auth())->logAction('training_deleted', 'training', (int)$id, "Deleted training course");
        $this->redirect('/training', 'Training course deleted.', 'warning');
    }
}
