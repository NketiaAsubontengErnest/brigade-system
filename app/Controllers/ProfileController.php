<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;
use App\Core\Auth;
use App\Core\Validator;

class ProfileController extends Controller
{
    private Database $db;
    private Auth $auth;

    public function __construct()
    {
        parent::__construct();
        $this->auth = new Auth();
        if (!$this->auth->check()) {
            $this->redirect('/login');
        }
        $this->db = Database::getInstance();
    }

    public function index(): void
    {
        $user = $this->auth->user();
        $role = $this->db->fetchOne("SELECT name FROM roles WHERE id = :id", ['id' => (int)($user['role_id'] ?? 0)]);
        $user['role_name'] = $role['name'] ?? 'User';

        // Fetch recent audit logs for this user
        $logs = $this->db->fetchAll(
            "SELECT * FROM audit_logs WHERE user_id = :uid ORDER BY created_at DESC LIMIT 10",
            ['uid' => (int)$user['id']]
        );

        $this->setMenuActive('');
        $this->view('profile.index', [
            'user' => $user,
            'logs' => $logs,
        ]);
    }

    public function updateProfile(): void
    {
        $this->verifyCsrf();
        $user = $this->auth->user();
        $userId = (int)$user['id'];

        $request = new \App\Core\Request();
        $fullName = trim($request->input('full_name', ''));
        $email = trim($request->input('email', ''));
        $phone = trim($request->input('phone', ''));

        $validator = new Validator();
        if (!$validator->validate(
            ['full_name' => $fullName, 'email' => $email],
            ['full_name' => 'required|max:200', 'email' => 'required|email']
        )) {
            $this->redirect('/profile', 'Please provide a valid name and email address.', 'danger');
            return;
        }

        // Check unique email if changed
        $existing = $this->db->fetchOne(
            "SELECT id FROM users WHERE LOWER(email) = LOWER(:email) AND id != :id",
            ['email' => $email, 'id' => $userId]
        );

        if ($existing) {
            $this->redirect('/profile', 'This email address is already in use by another user.', 'danger');
            return;
        }

        $this->db->execute(
            "UPDATE users SET full_name = :name, email = :email, phone = :phone, updated_at = CURRENT_TIMESTAMP WHERE id = :id",
            ['name' => $fullName, 'email' => $email, 'phone' => $phone ?: null, 'id' => $userId]
        );

        // Update session user data
        $_SESSION['user']['full_name'] = $fullName;
        $_SESSION['user']['email'] = $email;
        $_SESSION['user']['phone'] = $phone;

        $this->auth->logAction('profile_updated', 'user', $userId, "Updated profile information");

        $this->redirect('/profile', 'Profile updated successfully!', 'success');
    }

    public function updatePassword(): void
    {
        $this->verifyCsrf();
        $user = $this->auth->user();
        $userId = (int)$user['id'];

        $request = new \App\Core\Request();
        $currentPassword = $request->input('current_password', '');
        $newPassword = $request->input('password', '');
        $confirmPassword = $request->input('password_confirmation', '');

        // Fetch user password hash
        $userData = $this->db->fetchOne("SELECT password FROM users WHERE id = :id", ['id' => $userId]);

        if (!password_verify($currentPassword, $userData['password'] ?? '')) {
            $this->redirect('/profile', 'Your current password is incorrect.', 'danger');
            return;
        }

        $validator = new Validator();
        if (!$validator->validate(
            ['password' => $newPassword, 'password_confirmation' => $confirmPassword],
            ['password' => 'required|min:8', 'password_confirmation' => 'required|same:password']
        )) {
            $this->redirect('/profile', 'New password must be at least 8 characters and match confirmation.', 'danger');
            return;
        }

        $hashed = password_hash($newPassword, PASSWORD_DEFAULT);
        $this->db->execute(
            "UPDATE users SET password = :password, updated_at = CURRENT_TIMESTAMP WHERE id = :id",
            ['password' => $hashed, 'id' => $userId]
        );

        $this->auth->logAction('password_changed', 'user', $userId, "Changed account password");

        $this->redirect('/profile', 'Password updated successfully!', 'success');
    }
}
