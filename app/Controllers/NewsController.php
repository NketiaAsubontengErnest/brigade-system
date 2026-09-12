<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;
use App\Core\Auth;

class NewsController extends Controller
{
    private Database $db;

    public function __construct()
    {
        parent::__construct();
        $this->authorize('news.view');
        $this->db = Database::getInstance();
    }

    public function index(): void
    {
        $page = (int)($this->request->get('page') ?? 1);
        $total = (int)$this->db->fetchOne("SELECT COUNT(*) as c FROM news")['c'];
        $pagination = $this->paginate($total, 20, $page);

        $articles = $this->db->fetchAll(
            "SELECT n.*, u.full_name as author_name FROM news n LEFT JOIN users u ON u.id = n.author ORDER BY n.published_date DESC LIMIT :limit OFFSET :offset",
            ['limit' => $pagination['per_page'], 'offset' => $pagination['offset']]
        );
        $this->setMenuActive('news');
        $this->view('news.index', ['articles' => $articles, 'pagination' => $pagination]);
    }

    public function create(): void
    {
        $this->authorize('news.create');
        $this->setMenuActive('news');
        $this->view('news.create');
    }

    public function store(): void
    {
        $this->authorize('news.create');
        $this->verifyCsrf();

        $request = new \App\Core\Request();
        $data = $request->only(['title', 'content', 'status', 'published_date']);

        $slug = $this->createSlug($data['title']);

        // Handle featured image
        $featuredImage = null;
        if ($file = $request->file('featured_image')) {
            if ($file['error'] === UPLOAD_ERR_OK) {
                $featuredImage = uploadFile($file, 'gallery', ['jpg', 'jpeg', 'png', 'gif', 'webp']);
            }
        }

        $this->db->execute(
            "INSERT INTO news (title, slug, content, featured_image, author, published_date, status) VALUES (:t, :s, :c, :fi, :a, :pd, :st)",
            [
                't' => $data['title'], 's' => $slug, 'c' => $data['content'],
                'fi' => $featuredImage, 'a' => auth()->id(),
                'pd' => $data['published_date'] ?: date('Y-m-d'), 'st' => $data['status'] ?? 'Draft',
            ]
        );

        (new Auth())->logAction('news_created', 'news', null, "Created news: {$data['title']}");
        $this->redirect('/news/manage', 'News article created.', 'success');
    }

    public function show(string $id): void
    {
        $article = $this->db->fetchOne(
            "SELECT n.*, u.full_name as author_name FROM news n LEFT JOIN users u ON u.id = n.author WHERE n.id = :id",
            ['id' => (int)$id]
        );
        if (!$article) {
            $this->abort(404, 'Article not found');
        }
        $this->setMenuActive('news');
        $this->view('news.show', ['article' => $article]);
    }

    public function edit(string $id): void
    {
        $this->authorize('news.edit');
        $article = $this->db->fetchOne("SELECT * FROM news WHERE id = :id", ['id' => (int)$id]);
        if (!$article) {
            $this->abort(404);
        }
        $this->setMenuActive('news');
        $this->view('news.edit', ['article' => $article]);
    }

    public function update(string $id): void
    {
        $this->authorize('news.edit');
        $this->verifyCsrf();

        $request = new \App\Core\Request();
        $data = $request->only(['title', 'content', 'status', 'published_date']);

        $slug = $this->createSlug($data['title']);
        if ($featuredImage = $request->file('featured_image')) {
            if ($featuredImage['error'] === UPLOAD_ERR_OK) {
                $fi = uploadFile($featuredImage, 'gallery', ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                if ($fi) {
                    $data['featured_image'] = $fi;
                }
            }
        }

        $setClauses = ['title = :t', 'slug = :s', 'content = :c', 'published_date = :pd', 'status = :st', 'updated_at = NOW()'];
        $bindings = ['t' => $data['title'], 's' => $slug, 'c' => $data['content'], 'pd' => $data['published_date'] ?? date('Y-m-d'), 'st' => $data['status'], 'id' => (int)$id];

        if (isset($data['featured_image'])) {
            $setClauses[] = 'featured_image = :fi';
            $bindings['fi'] = $data['featured_image'];
        }

        $this->db->execute("UPDATE news SET " . implode(', ', $setClauses) . " WHERE id = :id", $bindings);
        $this->redirect('/news/' . $id, 'Article updated.', 'success');
    }

    public function delete(string $id): void
    {
        $this->authorize('news.delete');
        $this->verifyCsrf();
        $this->db->execute("DELETE FROM news WHERE id = :id", ['id' => (int)$id]);
        $this->redirect('/news/manage', 'Article deleted.', 'warning');
    }

    private function createSlug(string $title): string
    {
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $title), '-'));
        $base = $slug;
        $i = 1;
        while ($this->db->fetchOne("SELECT id FROM news WHERE slug = :s", ['s' => $slug])) {
            $slug = $base . '-' . $i++;
        }
        return $slug;
    }
}
