<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Auth;
use App\Core\Database;
use App\Core\Session;
use App\Core\Validator;
use App\Services\EmailService;

class AuthController extends Controller
{
    private Auth $auth;
    private Database $db;

    public function __construct()
    {
        parent::__construct();
        $this->auth = new Auth();
        $this->db = Database::getInstance();
    }

    public function loginForm(): void
    {
        if ($this->auth->check()) {
            $user = $this->auth->user();
            if ($user['role_id'] == 8) {
                $this->redirect('/portal');
            } elseif ($this->isGuardianRole((int)$user['role_id'])) {
                $this->redirect('/guardian');
            } else {
                $this->redirect('/dashboard');
            }
        }
        $this->view('auth.login');
    }

    public function login(): void
    {
        $this->verifyCsrf();

        $request = new \App\Core\Request();
        $email = trim($request->input('email', ''));
        $password = $request->input('password', '');

        // Rate limiting check
        $session = new Session();
        $attempts = $session->get('login_attempts', 0);
        $lastAttempt = $session->get('last_login_attempt', 0);

        if ($attempts >= 5 && (time() - $lastAttempt) < 300) {
            $this->redirect('/login', 'Too many login attempts. Please wait 5 minutes.', 'danger');
            return;
        }

        if ($this->auth->attempt($email, $password)) {
            $session->remove('login_attempts');
            $session->remove('last_login_attempt');

            $user = $this->auth->user();

            $redirectTo = null;
            if (isset($_GET['redirect']) && $_GET['redirect'] !== '') {
                $redirectTo = filter_var($_GET['redirect'], FILTER_SANITIZE_URL);
            }

            if ($user['role_id'] == 8) { // Member role
                $this->redirect($redirectTo ?: '/portal');
            } elseif ($this->isGuardianRole((int)$user['role_id'])) {
                $this->redirect($redirectTo ?: '/guardian');
            } else {
                $this->redirect($redirectTo ?: '/dashboard', 'Welcome back, ' . htmlspecialchars($user['full_name']) . '!');
            }
        } else {
            $attempts++;
            $session->put([
                'login_attempts' => $attempts,
                'last_login_attempt' => time(),
            ]);
            $this->redirect('/login', 'Invalid email or password.', 'danger');
        }
    }

    public function logout(): void
    {
        $this->auth->logout();
        $this->redirect('/', 'You have been logged out successfully.');
    }

    public function forgotPasswordForm(): void
    {
        $this->view('auth.forgot_password');
    }

    public function forgotPassword(): void
    {
        $this->verifyCsrf();

        $request = new \App\Core\Request();
        $email = trim($request->input('email', ''));

        $validator = new Validator();
        if (!$validator->validate(['email' => $email], ['email' => 'required|email'])) {
            $this->redirect('/forgot-password', 'Please provide a valid email address.', 'danger');
            return;
        }

        // Check if user exists
        $user = $this->db->fetchOne("SELECT id, full_name, email FROM users WHERE LOWER(email) = LOWER(:email)", ['email' => $email]);

        if ($user) {
            // Delete old tokens for this email
            $this->db->execute("DELETE FROM password_resets WHERE LOWER(email) = LOWER(:email)", ['email' => $email]);

            // Generate cryptographically secure token
            $token = bin2hex(random_bytes(32));
            $expiresAt = date('Y-m-d H:i:s', time() + 3600); // 1 hour expiration

            $this->db->execute(
                "INSERT INTO password_resets (email, token, expires_at) VALUES (:email, :token, :expires_at)",
                [
                    'email' => $user['email'],
                    'token' => $token,
                    'expires_at' => $expiresAt,
                ]
            );

            // Build reset URL
            $baseUrl = rtrim($_ENV['APP_URL'] ?? 'http://localhost/brigade-system', '/');
            $resetUrl = $baseUrl . "/reset-password?token=" . urlencode($token) . "&email=" . urlencode($user['email']);

            // Send Email
            $emailService = new EmailService();
            $emailService->sendPasswordReset($user['email'], $user['full_name'], $resetUrl);
        }

        // Always show success view to prevent email enumeration
        $this->view('auth.forgot_password_sent', ['email' => $email]);
    }

    public function resetPasswordForm(): void
    {
        $request = new \App\Core\Request();
        $token = $request->input('token', '');
        $email = $request->input('email', '');

        if (!$token || !$email) {
            $this->redirect('/forgot-password', 'Invalid or missing password reset parameters.', 'danger');
            return;
        }

        // Check token in DB
        $reset = $this->db->fetchOne(
            "SELECT * FROM password_resets WHERE token = :token AND LOWER(email) = LOWER(:email) AND expires_at > CURRENT_TIMESTAMP",
            ['token' => $token, 'email' => $email]
        );

        if (!$reset) {
            $this->redirect('/forgot-password', 'This password reset link is invalid or has expired.', 'danger');
            return;
        }

        $this->view('auth.reset_password', ['token' => $token, 'email' => $email]);
    }

    public function resetPassword(): void
    {
        $this->verifyCsrf();

        $request = new \App\Core\Request();
        $token = $request->input('token', '');
        $email = trim($request->input('email', ''));
        $password = $request->input('password', '');
        $confirm = $request->input('password_confirmation', '');

        $validator = new Validator();
        if (!$validator->validate(
            ['password' => $password, 'password_confirmation' => $confirm],
            ['password' => 'required|min:8', 'password_confirmation' => 'required|same:password']
        )) {
            $this->redirect('/reset-password?token=' . urlencode($token) . '&email=' . urlencode($email), 'Password must be at least 8 characters and match confirmation.', 'danger');
            return;
        }

        // Verify token
        $reset = $this->db->fetchOne(
            "SELECT * FROM password_resets WHERE token = :token AND LOWER(email) = LOWER(:email) AND expires_at > CURRENT_TIMESTAMP",
            ['token' => $token, 'email' => $email]
        );

        if (!$reset) {
            $this->redirect('/forgot-password', 'This password reset link is invalid or has expired. Please request a new one.', 'danger');
            return;
        }

        // Update user password
        $hashed = password_hash($password, PASSWORD_DEFAULT);
        $this->db->execute(
            "UPDATE users SET password = :password, updated_at = CURRENT_TIMESTAMP WHERE LOWER(email) = LOWER(:email)",
            ['password' => $hashed, 'email' => $email]
        );

        // Delete used token
        $this->db->execute("DELETE FROM password_resets WHERE LOWER(email) = LOWER(:email)", ['email' => $email]);

        $this->redirect('/login', 'Password reset successfully. You can now log in with your new password.', 'success');
    }

    private function isGuardianRole(int $roleId): bool
    {
        $role = $this->db->fetchOne("SELECT name FROM roles WHERE id = :id", ['id' => $roleId]);
        return $role && strtolower($role['name']) === 'guardian';
    }
}
