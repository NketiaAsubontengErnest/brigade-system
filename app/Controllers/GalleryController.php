<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;
use App\Core\Auth;

class GalleryController extends Controller
{
    private Database $db;

    public function __construct()
    {
        parent::__construct();
        $this->authorize('gallery.view');
        $this->db = Database::getInstance();
    }

    public function index(): void
    {
        $page = (int)($this->request->get('page') ?? 1);
        $status = trim((string)($this->request->get('status') ?? 'all'));

        $whereClause = "";
        $params = [];

        if (in_array($status, ['Active', 'Archived'], true)) {
            $whereClause = "WHERE ga.status = :status";
            $params['status'] = $status;
        }

        $countSql = "SELECT COUNT(*) as c FROM gallery_albums ga {$whereClause}";
        $total = (int)($this->db->fetchOne($countSql, $params)['c'] ?? 0);
        $pagination = $this->paginate($total, 20, $page);

        $params['limit'] = $pagination['per_page'];
        $params['offset'] = $pagination['offset'];

        $albums = $this->db->fetchAll(
            "SELECT ga.*, 
                (SELECT COUNT(*) FROM gallery_images WHERE album_id = ga.id) as image_count,
                (SELECT file_path FROM gallery_images WHERE album_id = ga.id ORDER BY sort_order LIMIT 1) as cover
             FROM gallery_albums ga {$whereClause} ORDER BY ga.created_at DESC
             LIMIT :limit OFFSET :offset",
            $params
        );
        $this->setMenuActive('gallery');
        $this->view('gallery.index', [
            'albums' => $albums,
            'pagination' => $pagination,
            'currentStatus' => $status
        ]);
    }

    public function createAlbum(): void
    {
        $this->authorize('gallery.create');
        $this->setMenuActive('gallery');
        $this->view('gallery.create');
    }

    public function storeAlbum(): void
    {
        $this->authorize('gallery.create');
        $this->verifyCsrf();

        $request = new \App\Core\Request();
        $name = trim($request->input('name', ''));
        $description = trim($request->input('description', ''));

        $this->db->execute(
            "INSERT INTO gallery_albums (name, description) VALUES (:n, :d)",
            ['n' => $name, 'd' => $description]
        );

        $albumId = (int)$this->db->lastInsertId();
        $this->redirect("/gallery/album/{$albumId}", 'Album created.', 'success');
    }

    public function showAlbum(string $id): void
    {
        $album = $this->db->fetchOne("SELECT * FROM gallery_albums WHERE id = :id", ['id' => (int)$id]);
        if (!$album) {
            $this->abort(404, 'Album not found');
        }
        $images = $this->db->fetchAll(
            "SELECT * FROM gallery_images WHERE album_id = :id ORDER BY sort_order, created_at DESC",
            ['id' => (int)$id]
        );
        $this->setMenuActive('gallery');
        $this->view('gallery.show', ['album' => $album, 'images' => $images]);
    }

    public function editAlbum(string $id): void
    {
        $this->authorize('gallery.create');
        $album = $this->db->fetchOne("SELECT * FROM gallery_albums WHERE id = :id", ['id' => (int)$id]);
        if (!$album) {
            $this->abort(404, 'Album not found');
        }
        $this->setMenuActive('gallery');
        $this->view('gallery.edit', ['album' => $album]);
    }

    public function updateAlbum(string $id): void
    {
        $this->authorize('gallery.create');
        $this->verifyCsrf();

        $album = $this->db->fetchOne("SELECT * FROM gallery_albums WHERE id = :id", ['id' => (int)$id]);
        if (!$album) {
            $this->abort(404, 'Album not found');
        }

        $request = new \App\Core\Request();
        $name = trim($request->input('name', ''));
        $description = trim($request->input('description', ''));
        $status = trim($request->input('status', 'Active'));

        if (empty($name)) {
            $this->redirect("/gallery/album/{$id}/edit", 'Album name is required.', 'danger');
            return;
        }

        $this->db->execute(
            "UPDATE gallery_albums SET name = :n, description = :d, status = :s, updated_at = CURRENT_TIMESTAMP WHERE id = :id",
            ['n' => $name, 'd' => $description, 's' => $status, 'id' => (int)$id]
        );

        $this->redirect("/gallery/album/{$id}", 'Album details updated successfully.', 'success');
    }

    public function toggleStatusAlbum(string $id): void
    {
        $this->authorize('gallery.create');
        $this->verifyCsrf();

        $album = $this->db->fetchOne("SELECT * FROM gallery_albums WHERE id = :id", ['id' => (int)$id]);
        if (!$album) {
            $this->abort(404, 'Album not found');
        }

        $newStatus = ($album['status'] === 'Active') ? 'Archived' : 'Active';
        $this->db->execute(
            "UPDATE gallery_albums SET status = :s, updated_at = CURRENT_TIMESTAMP WHERE id = :id",
            ['s' => $newStatus, 'id' => (int)$id]
        );

        $msg = $newStatus === 'Active' ? 'Album activated successfully.' : 'Album deactivated (archived) successfully.';
        $this->redirect($_SERVER['HTTP_REFERER'] ?? '/gallery/manage', $msg, 'warning');
    }

    public function uploadImages(string $id): void
    {
        $this->authorize('gallery.create');
        $this->verifyCsrf();

        $request = new \App\Core\Request();
        $files = $request->file('images');

        if (!$files) {
            $this->redirect("/gallery/album/{$id}", 'No files uploaded.', 'danger');
            return;
        }

        // Handle multiple file uploads
        $count = 0;
        if (isset($files['name']) && is_array($files['name'])) {
            for ($i = 0; $i < count($files['name']); $i++) {
                if ($files['error'][$i] === UPLOAD_ERR_OK) {
                    $fileData = [
                        'name' => $files['name'][$i],
                        'type' => $files['type'][$i],
                        'tmp_name' => $files['tmp_name'][$i],
                        'error' => $files['error'][$i],
                        'size' => $files['size'][$i],
                    ];
                    $path = uploadFile($fileData, 'gallery', ['jpg', 'jpeg', 'png', 'gif', 'webp'], 10485760);
                    if ($path) {
                        $this->db->execute(
                            "INSERT INTO gallery_images (album_id, file_path, caption, uploaded_by) VALUES (:a, :fp, :c, :u)",
                            ['a' => (int)$id, 'fp' => $path, 'c' => null, 'u' => auth()->id()]
                        );
                        $count++;
                    }
                }
            }
        } else {
            if ($files['error'] === UPLOAD_ERR_OK) {
                $path = uploadFile($files, 'gallery', ['jpg', 'jpeg', 'png', 'gif', 'webp'], 10485760);
                if ($path) {
                    $this->db->execute(
                        "INSERT INTO gallery_images (album_id, file_path, caption, uploaded_by) VALUES (:a, :fp, :c, :u)",
                        ['a' => (int)$id, 'fp' => $path, 'c' => null, 'u' => auth()->id()]
                    );
                    $count++;
                }
            }
        }

        $this->redirect("/gallery/album/{$id}", "{$count} image(s) uploaded.", 'success');
    }

    public function deleteImage(string $id): void
    {
        $this->authorize('gallery.delete');
        $this->verifyCsrf();

        $image = $this->db->fetchOne("SELECT * FROM gallery_images WHERE id = :id", ['id' => (int)$id]);
        if ($image) {
            // Delete physical file
            $filePath = dirname(__DIR__, 2) . '/public/' . $image['file_path'];
            if (file_exists($filePath)) {
                unlink($filePath);
            }
            $this->db->execute("DELETE FROM gallery_images WHERE id = :id", ['id' => (int)$id]);
        }

        $this->redirect($_SERVER['HTTP_REFERER'] ?? '/gallery', 'Image deleted.', 'warning');
    }

    public function deleteAlbum(string $id): void
    {
        $this->authorize('gallery.delete');
        $this->verifyCsrf();

        // Delete all images in album
        $images = $this->db->fetchAll("SELECT file_path FROM gallery_images WHERE album_id = :id", ['id' => (int)$id]);
        foreach ($images as $img) {
            $filePath = dirname(__DIR__, 2) . '/public/' . $img['file_path'];
            if (file_exists($filePath)) {
                unlink($filePath);
            }
        }

        $this->db->execute("DELETE FROM gallery_albums WHERE id = :id", ['id' => (int)$id]);
        $this->redirect('/gallery/manage', 'Album deleted.', 'warning');
    }
}
