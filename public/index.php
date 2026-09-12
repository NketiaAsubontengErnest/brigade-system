<?php

declare(strict_types=1);

/**
 * Brigade Company Management System - Front Controller
 */

// Error reporting
error_reporting(E_ALL);
ini_set('display_errors', '0');
ini_set('log_errors', '1');

// Set timezone
date_default_timezone_set('Africa/Accra');

// Load Composer autoloader
require_once dirname(__DIR__) . '/vendor/autoload.php';

// Load .env
$dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__));
$dotenv->safeLoad();

// Load helpers
require_once dirname(__DIR__) . '/app/helpers/functions.php';

// Apply HTTP Security Headers (A+ Security Rating)
App\Middleware\SecurityHeadersMiddleware::apply();

// Initialize logger
App\Core\Logger::init();

// Load routes
require_once dirname(__DIR__) . '/routes/web.php';

// Get the request URI and strip the base path
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];

// Strip base path: auto-detect from SCRIPT_NAME
$scriptName = $_SERVER['SCRIPT_NAME'] ?? '/index.php';
$basePath = rtrim(dirname($scriptName), '/\\');
// Remove '/public' from the base path
$basePath = preg_replace('#/public$#', '', $basePath);
// Also try to strip common paths
if ($basePath && $basePath !== '/' && $basePath !== '\\') {
    if (strpos($uri, $basePath) === 0) {
        $uri = substr($uri, strlen($basePath));
    }
}
$uri = '/' . trim($uri, '/');



// Handle PUT/DELETE via _method
if ($method === 'POST' && isset($_POST['_method'])) {
    $method = strtoupper($_POST['_method']);
}

// Dispatch the route
try {
    $router->dispatch($method, $uri);
} catch (Throwable $e) {
    App\Core\Logger::error('Application error', [
        'message' => $e->getMessage(),
        'file' => $e->getFile(),
        'line' => $e->getLine(),
        'uri' => $uri,
    ]);

    http_response_code(500);
    if (($_ENV['APP_DEBUG'] ?? 'false') === 'true') {
        echo '<pre>' . htmlspecialchars($e->getMessage() . "\n" . $e->getTraceAsString()) . '</pre>';
    } else {
        require __DIR__ . '/../app/views/errors/500.php';
    }
}
