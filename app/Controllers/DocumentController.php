<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;
use App\Core\Auth;

class DocumentController extends Controller
{
    private Database $db;

    public function __construct()
    {
        parent::__construct();
        $this->authorize('documents.view');
        $this->db = Database::getInstance();
    }

    public function index(): void
    {
        $page = (int)($this->request->get('page') ?? 1);
        $total = (int)$this->db->fetchOne("SELECT COUNT(*) as c FROM documents")['c'];
        $pagination = $this->paginate($total, 20, $page);

        $documents = $this->db->fetchAll(
            "SELECT d.*, u.full_name as uploader_name FROM documents d LEFT JOIN users u ON u.id = d.uploaded_by ORDER BY d.created_at DESC LIMIT :limit OFFSET :offset",
            ['limit' => $pagination['per_page'], 'offset' => $pagination['offset']]
        );
        $this->setMenuActive('documents');
        $this->view('documents.index', ['documents' => $documents, 'pagination' => $pagination]);
    }

    public function uploadForm(): void
    {
        $this->authorize('documents.create');
        $this->setMenuActive('documents');
        $this->view('documents.upload');
    }

    public function upload(): void
    {
        $this->authorize('documents.create');
        $this->verifyCsrf();

        $request = new \App\Core\Request();
        $file = $request->file('document');

        if (!$file || $file['error'] !== UPLOAD_ERR_OK) {
            $this->redirect('/documents/upload', 'Please select a file to upload.', 'danger');
            return;
        }

        $name = trim($request->input('name', $file['name']));
        $description = trim($request->input('description', ''));
        $isPublic = (bool)$request->input('is_public', false);

        $path = uploadFile($file, 'documents', ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'txt', 'csv', 'jpg', 'jpeg', 'png'], 20971520);

        if (!$path) {
            $this->redirect('/documents/upload', 'File upload failed. Check file type and size.', 'danger');
            return;
        }

        $this->db->execute(
            "INSERT INTO documents (name, description, file_path, file_type, file_size, is_public, uploaded_by)
             VALUES (:n, :d, :fp, :ft, :fs, :ip, :u)",
            [
                'n' => $name, 'd' => $description, 'fp' => $path,
                'ft' => $file['type'], 'fs' => $file['size'], 'ip' => $isPublic,
                'u' => auth()->id(),
            ]
        );

        (new Auth())->logAction('document_uploaded', 'document', null, "Uploaded: {$name}");
        $this->redirect('/documents', 'Document uploaded successfully.', 'success');
    }

    public function show(string $id): void
    {
        $doc = $this->db->fetchOne(
            "SELECT d.*, u.full_name as uploader_name FROM documents d LEFT JOIN users u ON u.id = d.uploaded_by WHERE d.id = :id",
            ['id' => (int)$id]
        );
        if (!$doc) {
            $this->abort(404, 'Document not found');
        }
        $this->setMenuActive('documents');
        $this->view('documents.show', ['document' => $doc]);
    }

    public function download(string $id): void
    {
        $doc = $this->db->fetchOne("SELECT * FROM documents WHERE id = :id", ['id' => (int)$id]);
        if (!$doc) {
            $this->abort(404);
        }

        $filePath = dirname(__DIR__, 2) . '/public/' . $doc['file_path'];
        if (!file_exists($filePath)) {
            $this->abort(404, 'File not found on disk');
        }

        header('Content-Type: ' . $doc['file_type']);
        header('Content-Disposition: attachment; filename="' . basename($doc['file_path']) . '"');
        header('Content-Length: ' . filesize($filePath));
        readfile($filePath);
        exit;
    }

    public function delete(string $id): void
    {
        $this->authorize('documents.delete');
        $this->verifyCsrf();

        $doc = $this->db->fetchOne("SELECT * FROM documents WHERE id = :id", ['id' => (int)$id]);
        if ($doc) {
            $filePath = dirname(__DIR__, 2) . '/public/' . $doc['file_path'];
            if (file_exists($filePath)) {
                unlink($filePath);
            }
            $this->db->execute("DELETE FROM documents WHERE id = :id", ['id' => (int)$id]);
        }

        $this->redirect('/documents', 'Document deleted.', 'warning');
    }
}
