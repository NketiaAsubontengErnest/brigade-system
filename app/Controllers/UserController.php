<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;
use App\Core\Validator;
use App\Core\Auth;

class UserController extends Controller
{
    private Database $db;

    public function __construct()
    {
        parent::__construct();
        $this->authorize('users.view');
        $this->db = Database::getInstance();
    }

    public function index(): void
    {
        $page = (int)($this->request->get('page') ?? 1);
        $total = (int)$this->db->fetchOne("SELECT COUNT(*) as c FROM users")['c'];
        $pagination = $this->paginate($total, 20, $page);

        $users = $this->db->fetchAll(
            "SELECT u.*, r.name as role_name FROM users u LEFT JOIN roles r ON r.id = u.role_id ORDER BY u.full_name LIMIT :limit OFFSET :offset",
            ['limit' => $pagination['per_page'], 'offset' => $pagination['offset']]
        );
        $this->setMenuActive('users');
        $this->view('users.index', ['users' => $users, 'pagination' => $pagination]);
    }

    public function create(): void
    {
        $this->authorize('users.create');
        $roles = $this->db->fetchAll("SELECT * FROM roles ORDER BY name");
        $this->setMenuActive('users');
        $this->view('users.create', ['roles' => $roles]);
    }

    public function store(): void
    {
        $this->authorize('users.create');
        $this->verifyCsrf();

        $request = new \App\Core\Request();
        $data = $request->only(['full_name', 'email', 'phone', 'password', 'role_id']);

        $validator = new Validator();
        if (!$validator->validate($data, [
            'full_name' => 'required|max:200',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8',
            'role_id' => 'required',
        ])) {
            Validator::flashInput($data);
            $this->redirect('/users/create', 'Please correct the errors.', 'danger');
            return;
        }

        $passwordHash = password_hash($data['password'], PASSWORD_DEFAULT);
        $this->db->execute(
            "INSERT INTO users (full_name, email, phone, password, role_id) VALUES (:n, :e, :p, :pw, :r)",
            ['n' => $data['full_name'], 'e' => $data['email'], 'p' => $data['phone'] ?? null, 'pw' => $passwordHash, 'r' => $data['role_id']]
        );

        (new Auth())->logAction('user_created', 'user', null, "Created user: {$data['full_name']}");
        $this->redirect('/users', 'User created successfully.', 'success');
    }

    public function show(string $id): void
    {
        $user = $this->db->fetchOne(
            "SELECT u.*, r.name as role_name FROM users u LEFT JOIN roles r ON r.id = u.role_id WHERE u.id = :id",
            ['id' => (int)$id]
        );
        if (!$user) {
            $this->abort(404, 'User not found');
        }
        $this->setMenuActive('users');
        $this->view('users.show', ['user' => $user]);
    }

    public function edit(string $id): void
    {
        $this->authorize('users.edit');
        $user = $this->db->fetchOne("SELECT * FROM users WHERE id = :id", ['id' => (int)$id]);
        if (!$user) {
            $this->abort(404);
        }
        $roles = $this->db->fetchAll("SELECT * FROM roles ORDER BY name");
        $this->setMenuActive('users');
        $this->view('users.edit', ['user' => $user, 'roles' => $roles]);
    }

    public function update(string $id): void
    {
        $this->authorize('users.edit');
        $this->verifyCsrf();

        $request = new \App\Core\Request();
        $data = $request->only(['full_name', 'email', 'phone', 'role_id', 'status']);

        $setClauses = [];
        $bindings = ['id' => (int)$id];
        foreach ($data as $key => $value) {
            $setClauses[] = "{$key} = :{$key}";
            $bindings[$key] = $value;
        }
        $setClauses[] = "updated_at = NOW()";

        $this->db->execute("UPDATE users SET " . implode(', ', $setClauses) . " WHERE id = :id", $bindings);

        (new Auth())->logAction('user_updated', 'user', (int)$id, "Updated user: {$data['full_name']}");
        $this->redirect('/users', 'User updated.', 'success');
    }

    public function toggleStatus(string $id): void
    {
        $this->authorize('users.edit');
        $this->verifyCsrf();

        $user = $this->db->fetchOne("SELECT * FROM users WHERE id = :id", ['id' => (int)$id]);
        $newStatus = $user['status'] === 'Active' ? 'Inactive' : 'Active';

        $this->db->execute(
            "UPDATE users SET status = :s, updated_at = NOW() WHERE id = :id",
            ['s' => $newStatus, 'id' => (int)$id]
        );

        $this->redirect('/users', "User {$newStatus}.", 'success');
    }

    public function resetPassword(string $id): void
    {
        $this->authorize('users.edit');
        $this->verifyCsrf();

        $newPassword = bin2hex(random_bytes(8));
        $hash = password_hash($newPassword, PASSWORD_DEFAULT);

        $this->db->execute(
            "UPDATE users SET password = :pw, updated_at = NOW() WHERE id = :id",
            ['pw' => $hash, 'id' => (int)$id]
        );

        (new Auth())->logAction('password_reset', 'user', (int)$id, "Reset password for user #{$id}");
        $this->redirect('/users/' . $id, "Password reset. New password: {$newPassword}", 'success');
    }
}
