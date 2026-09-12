<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;
use App\Core\Auth;

class SettingsController extends Controller
{
    private Database $db;

    public function __construct()
    {
        parent::__construct();
        $this->authorize('settings.view');
        $this->db = Database::getInstance();
    }

    public function index(): void
    {
        $profile = $this->db->fetchOne("SELECT * FROM company_profile LIMIT 1") ?? [];
        $this->setMenuActive('settings');
        $this->view('settings.index', ['profile' => $profile]);
    }

    public function updateCompany(): void
    {
        $this->authorize('settings.edit');
        $this->verifyCsrf();

        $request = new \App\Core\Request();
        $data = $request->only([
            'company_name', 'church_name', 'company_number', 'motto', 'description',
            'founded_date', 'address', 'location', 'phone', 'email', 'website',
            'mission', 'vision', 'member_number_prefix', 'currency_symbol',
        ]);

        $setClauses = [];
        $bindings = [];
        foreach ($data as $key => $value) {
            $setClauses[] = "{$key} = :{$key}";
            $bindings[$key] = $value;
        }
        $setClauses[] = "updated_at = NOW()";
        $bindings['id'] = 1;

        $this->db->execute(
            "UPDATE company_profile SET " . implode(', ', $setClauses) . " WHERE id = :id",
            $bindings
        );

        (new Auth())->logAction('company_updated', 'settings', 1, 'Updated company profile');
        $this->redirect('/settings', 'Company profile updated.', 'success');
    }

    public function uploadLogo(): void
    {
        $this->authorize('settings.edit');
        $this->verifyCsrf();

        $request = new \App\Core\Request();
        $file = $request->file('logo');

        if ($file && $file['error'] === UPLOAD_ERR_OK) {
            $path = uploadFile($file, 'images', ['jpg', 'jpeg', 'png', 'gif', 'webp'], 5242880);
            if ($path) {
                $this->db->execute(
                    "UPDATE company_profile SET logo = :l, updated_at = NOW() WHERE id = 1",
                    ['l' => $path]
                );
                $this->redirect('/settings', 'Logo uploaded.', 'success');
                return;
            }
        }
        $this->redirect('/settings', 'Logo upload failed.', 'danger');
    }

    public function uploadCover(): void
    {
        $this->authorize('settings.edit');
        $this->verifyCsrf();

        $request = new \App\Core\Request();
        $file = $request->file('cover_image');

        if ($file && $file['error'] === UPLOAD_ERR_OK) {
            $path = uploadFile($file, 'images', ['jpg', 'jpeg', 'png', 'gif', 'webp'], 10485760);
            if ($path) {
                $this->db->execute(
                    "UPDATE company_profile SET cover_image = :c, updated_at = NOW() WHERE id = 1",
                    ['c' => $path]
                );
                $this->redirect('/settings', 'Cover image uploaded.', 'success');
                return;
            }
        }
        $this->redirect('/settings', 'Cover image upload failed.', 'danger');
    }

    public function auditLogs(): void
    {
        $this->authorize('settings.edit');
        $request = new \App\Core\Request();
        $page = max(1, (int)$request->input('page', 1));

        $total = (int)$this->db->fetchOne("SELECT COUNT(*) as c FROM audit_logs")['c'];
        $pagination = $this->paginate($total, 30, $page);

        $logs = $this->db->fetchAll(
            "SELECT al.*, u.full_name as user_name FROM audit_logs al
             LEFT JOIN users u ON u.id = al.user_id
             ORDER BY al.created_at DESC
             LIMIT :limit OFFSET :offset",
            ['limit' => $pagination['per_page'], 'offset' => $pagination['offset']]
        );

        $this->setMenuActive('settings');
        $this->view('settings.audit_logs', ['logs' => $logs, 'pagination' => $pagination]);
    }

    public function notifications(): void
    {
        $userId = auth()->id();
        $notifications = $this->db->fetchAll(
            "SELECT * FROM notifications WHERE user_id = :uid ORDER BY created_at DESC LIMIT 50",
            ['uid' => $userId]
        );

        $this->setMenuActive('settings');
        $this->view('settings.notifications', ['notifications' => $notifications]);
    }

    public function markRead(string $id): void
    {
        $this->db->execute(
            "UPDATE notifications SET is_read = true WHERE id = :id AND user_id = :uid",
            ['id' => (int)$id, 'uid' => auth()->id()]
        );
        $this->redirect('/settings/notifications');
    }
}
