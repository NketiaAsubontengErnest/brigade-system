<?php

declare(strict_types=1);

namespace App\Core;

/**
 * View rendering engine.
 */
class View
{
    private string $viewsPath;
    private array $shared = [];
    private static ?self $instance = null;

    public function __construct()
    {
        $this->viewsPath = __DIR__ . '/../views';
        self::$instance = $this;
    }

    /**
     * Render a view with optional layout.
     */
    public function render(string $path, array $data = []): void
    {
        \App\Middleware\SecurityHeadersMiddleware::apply();
        $data = array_merge($this->shared, $data);

        // Make data available as variables
        extract($data);

        $filePath = $this->viewsPath . '/' . str_replace('.', '/', $path) . '.php';

        if (!file_exists($filePath)) {
            throw new \RuntimeException("View not found: {$path}");
        }

        ob_start();
        require $filePath;
        $output = ob_get_clean();

        // If the view already rendered its own layout (echoed output), just output it
        // If $content was set, the view used ob_start/ob_get_clean and may have a layout
        if (isset($layout) && empty($content)) {
            $this->renderLayout($layout, $output, $data);
        } else {
            echo $output;
        }
    }

    /**
     * Render with explicit layout.
     */
    public function renderWith(string $layout, string $path, array $data = []): void
    {
        $data = array_merge($this->shared, $data);
        extract($data);

        $filePath = $this->viewsPath . '/' . str_replace('.', '/', $path) . '.php';

        if (!file_exists($filePath)) {
            throw new \RuntimeException("View not found: {$path}");
        }

        ob_start();
        require $filePath;
        $content = ob_get_clean();

        $this->renderLayout($layout, $content, $data);
    }

    /**
     * Render a layout.
     */
    private function renderLayout(string $layout, string $content, array $data): void
    {
        extract($data);
        $layoutPath = $this->viewsPath . '/layouts/' . $layout . '.php';

        if (!file_exists($layoutPath)) {
            throw new \RuntimeException("Layout not found: {$layout}");
        }

        require $layoutPath;
    }

    /**
     * Share data across all views.
     */
    public function share(string $key, mixed $value): void
    {
        $this->shared[$key] = $value;
    }

    /**
     * Get the singleton instance.
     */
    public static function getInstance(): self
    {
        return self::$instance ?? new self();
    }
}
