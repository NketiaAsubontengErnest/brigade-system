<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;
use App\Core\Auth;

class RankController extends Controller
{
    private Database $db;

    public function __construct()
    {
        parent::__construct();
        $this->authorize('ranks.view');
        $this->db = Database::getInstance();
    }

    public function index(): void
    {
        $ranks = $this->db->fetchAll("SELECT * FROM ranks ORDER BY level ASC, name ASC");
        $this->setMenuActive('ranks');
        $this->view('settings.ranks', ['ranks' => $ranks]);
    }

    public function store(): void
    {
        $this->authorize('ranks.manage');
        $this->verifyCsrf();

        $request = new \App\Core\Request();
        $name = trim($request->input('name', ''));
        $description = trim($request->input('description', ''));
        $level = (int)$request->input('level', 1);

        if (empty($name)) {
            $this->redirect('/settings/ranks', 'Rank name is required.', 'danger');
            return;
        }

        $this->db->execute(
            "INSERT INTO ranks (name, description, level) VALUES (:name, :desc, :level) ON DUPLICATE KEY UPDATE name = name",
            ['name' => $name, 'desc' => $description, 'level' => $level]
        );

        $this->redirect('/settings/ranks', 'Rank created successfully.', 'success');
    }

    public function update(string $id): void
    {
        $this->authorize('ranks.manage');
        $this->verifyCsrf();

        $request = new \App\Core\Request();
        $this->db->execute(
            "UPDATE ranks SET name = :name, description = :desc, level = :level, status = :status, updated_at = NOW() WHERE id = :id",
            [
                'name' => $request->input('name'),
                'desc' => $request->input('description'),
                'level' => (int)$request->input('level', 1),
                'status' => $request->input('status', 'Active'),
                'id' => (int)$id,
            ]
        );

        $this->redirect('/settings/ranks', 'Rank updated.', 'success');
    }

    public function delete(string $id): void
    {
        $this->authorize('ranks.manage');
        $this->verifyCsrf();

        $this->db->execute("DELETE FROM ranks WHERE id = :id", ['id' => (int)$id]);
        $this->redirect('/settings/ranks', 'Rank deleted.', 'warning');
    }
}
