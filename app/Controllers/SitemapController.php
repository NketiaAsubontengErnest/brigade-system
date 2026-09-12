<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;

class SitemapController extends Controller
{
    private Database $db;

    public function __construct()
    {
        parent::__construct();
        $this->db = Database::getInstance();
    }

    /**
     * Generate dynamic sitemap.xml.
     */
    public function index(): void
    {
        if (!headers_sent()) {
            header('Content-Type: application/xml; charset=utf-8');
        }

        $scheme = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
        $path = rtrim(\base_url(), '/');
        $baseUrl = $scheme . '://' . $host . ($path !== '' ? $path : '');

        $staticPages = [
            ['loc' => '/', 'changefreq' => 'daily', 'priority' => '1.0'],
            ['loc' => '/about', 'changefreq' => 'monthly', 'priority' => '0.8'],
            ['loc' => '/leadership', 'changefreq' => 'monthly', 'priority' => '0.7'],
            ['loc' => '/membership', 'changefreq' => 'monthly', 'priority' => '0.9'],
            ['loc' => '/activities', 'changefreq' => 'weekly', 'priority' => '0.8'],
            ['loc' => '/events', 'changefreq' => 'weekly', 'priority' => '0.9'],
            ['loc' => '/news', 'changefreq' => 'daily', 'priority' => '0.8'],
            ['loc' => '/gallery', 'changefreq' => 'weekly', 'priority' => '0.7'],
            ['loc' => '/contact', 'changefreq' => 'monthly', 'priority' => '0.8'],
        ];

        // Fetch dynamic published news
        $newsItems = $this->db->fetchAll(
            "SELECT slug, updated_at, created_at FROM news WHERE status = 'Published' ORDER BY updated_at DESC"
        );

        // Fetch dynamic active events
        $events = $this->db->fetchAll(
            "SELECT id, updated_at, created_at FROM events WHERE status != 'Cancelled' ORDER BY updated_at DESC"
        );

        echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

        // Static routes
        foreach ($staticPages as $page) {
            echo "  <url>\n";
            echo "    <loc>" . htmlspecialchars($baseUrl . $page['loc']) . "</loc>\n";
            echo "    <lastmod>" . date('Y-m-d') . "</lastmod>\n";
            echo "    <changefreq>" . $page['changefreq'] . "</changefreq>\n";
            echo "    <priority>" . $page['priority'] . "</priority>\n";
            echo "  </url>\n";
        }

        // Dynamic news
        foreach ($newsItems as $news) {
            $lastmod = date('Y-m-d', strtotime($news['updated_at'] ?? $news['created_at'] ?? 'now'));
            echo "  <url>\n";
            echo "    <loc>" . htmlspecialchars($baseUrl . '/news/article/' . $news['slug']) . "</loc>\n";
            echo "    <lastmod>" . $lastmod . "</lastmod>\n";
            echo "    <changefreq>weekly</changefreq>\n";
            echo "    <priority>0.7</priority>\n";
            echo "  </url>\n";
        }

        // Dynamic events
        foreach ($events as $event) {
            $lastmod = date('Y-m-d', strtotime($event['updated_at'] ?? $event['created_at'] ?? 'now'));
            echo "  <url>\n";
            echo "    <loc>" . htmlspecialchars($baseUrl . '/events/view/' . $event['id']) . "</loc>\n";
            echo "    <lastmod>" . $lastmod . "</lastmod>\n";
            echo "    <changefreq>weekly</changefreq>\n";
            echo "    <priority>0.8</priority>\n";
            echo "  </url>\n";
        }

        echo '</urlset>';
    }
}
