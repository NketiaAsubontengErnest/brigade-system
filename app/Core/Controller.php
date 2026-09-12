<?php

declare(strict_types=1);

namespace App\Core;

/**
 * Base Controller class.
 */
class Controller
{
    protected View $view;
    protected Session $session;
    protected Request $request;
    protected array $authUser = [];

    public function __construct()
    {
        $this->view = new View();
        $this->session = new Session();
        $this->request = new Request();
    }

    /**
     * Render a view with data.
     */
    protected function view(string $path, array $data = []): void
    {
        $this->view->render($path, $data);
    }

    /**
     * Return a JSON response.
     */
    protected function json(mixed $data, int $statusCode = 200): void
    {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }

    /**
     * Redirect to a URL.
     */
    protected function redirect(string $url, string $message = '', string $type = 'success'): void
    {
        if ($message) {
            $this->session->flash($type, $message);
        }
        // Ensure URLs always include the base path
        if (strpos($url, 'http') !== 0 && strpos($url, '/') === 0) {
            $url = \base_url() . $url;
        }
        header('Location: ' . $url);
        exit;
    }

    /**
     * Abort with a status code.
     */
    protected function abort(int $code, string $message = ''): void
    {
        http_response_code($code);
        $viewPath = __DIR__ . '/../views/errors/' . $code . '.php';
        if (file_exists($viewPath)) {
            require $viewPath;
        } else {
            echo $message ?: "Error {$code}";
        }
        exit;
    }

    /**
     * Authorize the current user has a permission.
     */
    protected function authorize(string $permission): void
    {
        $auth = new Auth();
        if (!$auth->check()) {
            $this->redirect('/login', 'Please login to continue.', 'warning');
            exit;
        }
        if (!$auth->hasPermission($permission)) {
            $this->abort(403, 'You do not have permission to perform this action.');
        }
    }

    /**
     * Get the authenticated user.
     */
    protected function user(): ?array
    {
        $auth = new Auth();
        return $auth->user();
    }

    /**
     * Get the CSRF token.
     */
    protected function csrfToken(): string
    {
        return CSRF::token();
    }

    /**
     * Verify CSRF token for POST requests.
     */
    protected function verifyCsrf(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            CSRF::verify();
        }
    }

    /**
     * Load a model.
     */
    protected function model(string $name): object
    {
        $class = 'App\\Models\\' . $name;
        return new $class();
    }

    /**
     * Set the active menu item for the sidebar.
     */
    protected function setMenuActive(string $menu): void
    {
        $this->session->set('active_menu', $menu);
    }

    /**
     * Get pagination data.
     */
    protected function paginate(int $total, int $perPage = 15, int $currentPage = 1): array
    {
        $totalPages = (int)ceil($total / $perPage);
        $currentPage = max(1, min($currentPage, $totalPages));
        $offset = ($currentPage - 1) * $perPage;

        return [
            'total' => $total,
            'per_page' => $perPage,
            'current_page' => $currentPage,
            'total_pages' => $totalPages,
            'offset' => $offset,
            'has_prev' => $currentPage > 1,
            'has_next' => $currentPage < $totalPages,
            'prev_page' => max(1, $currentPage - 1),
            'next_page' => min($totalPages, $currentPage + 1),
        ];
    }
}
