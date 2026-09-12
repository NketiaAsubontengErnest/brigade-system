<?php

use App\Core\Session;
use App\Core\Auth;
use App\Core\CSRF;
use App\Core\Database;

if (!function_exists('e')) {
    /**
     * Escape HTML output.
     */
    function e(?string $value): string
    {
        return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('sanitize_input')) {
    /**
     * Defense-in-depth input sanitization (removes script tags, dangerous HTML, trims whitespace).
     */
    function sanitize_input(mixed $input): mixed
    {
        if (is_array($input)) {
            return array_map('sanitize_input', $input);
        }
        if (is_string($input)) {
            $cleaned = trim($input);
            // Strip script & style body tags
            $cleaned = preg_replace('/<(script|style)\b[^>]*>.*?<\/\1>/is', '', $cleaned);
            // Strip HTML tags
            $cleaned = strip_tags($cleaned);
            return $cleaned;
        }
        return $input;
    }
}

if (!function_exists('csrf_token')) {
    /**
     * Get active CSRF token string.
     */
    function csrf_token(): string
    {
        return CSRF::token();
    }
}

if (!function_exists('csrf_field')) {
    /**
     * Generate HTML input field for CSRF.
     */
    function csrf_field(): string
    {
        $tokenName = CSRF::fieldName();
        $tokenValue = CSRF::token();
        return '<input type="hidden" name="' . e($tokenName) . '" value="' . e($tokenValue) . '">';
    }
}

if (!function_exists('verify_csrf')) {
    /**
     * Verify CSRF token.
     */
    function verify_csrf(): bool
    {
        return CSRF::verify();
    }
}

if (!function_exists('base_url')) {
    /**
     * Get the application base URL (auto-detected from request).
     */
    function base_url(): string
    {
        static $base = null;
        if ($base !== null) return $base;

        // Method 1: Compute from SCRIPT_FILENAME and DOCUMENT_ROOT
        $scriptFilename = $_SERVER['SCRIPT_FILENAME'] ?? '';
        $docRoot = $_SERVER['DOCUMENT_ROOT'] ?? '';

        if ($scriptFilename && $docRoot) {
            // Normalize separators
            $scriptFilename = str_replace('\\', '/', $scriptFilename);
            $docRoot = str_replace('\\', '/', $docRoot);
            // Remove trailing slash from doc root
            $docRoot = rtrim($docRoot, '/');
            // Get the directory of the script
            $scriptDir = rtrim(dirname($scriptFilename), '/');
            // Strip '/public' suffix
            $scriptDir = preg_replace('#/public$#i', '', $scriptDir);
            // Remove the document root prefix to get relative base path
            if (strpos($scriptDir, $docRoot) === 0) {
                $base = substr($scriptDir, strlen($docRoot));
            } else {
                $base = '';
            }
        } else {
            // Fallback: use SCRIPT_NAME
            $scriptName = $_SERVER['SCRIPT_NAME'] ?? '/index.php';
            $base = rtrim(dirname($scriptName), '/\\');
            $base = preg_replace('#/public$#', '', $base);
        }

        return $base;
    }
}

if (!function_exists('url')) {
    /**
     * Generate a URL with the base path.
     */
    function url(string $path = ''): string
    {
        return base_url() . '/' . ltrim($path, '/');
    }
}

if (!function_exists('asset')) {
    /**
     * Generate an asset URL.
     */
    function asset(string $path): string
    {
        return url('assets/' . ltrim($path, '/'));
    }
}

if (!function_exists('csrf_field')) {
    /**
     * Generate CSRF token hidden field.
     */
    function csrf_field(): string
    {
        return '<input type="hidden" name="csrf_token" value="' . e(CSRF::token()) . '">';
    }
}

if (!function_exists('csrf_token')) {
    /**
     * Get CSRF token.
     */
    function csrf_token(): string
    {
        return CSRF::token();
    }
}

if (!function_exists('auth')) {
    /**
     * Get the Auth instance.
     */
    function auth(): Auth
    {
        return new Auth();
    }
}

if (!function_exists('old')) {
    /**
     * Get old input value.
     */
    function old(string $key, mixed $default = null): mixed
    {
        $session = new Session();
        $oldData = $session->get('_old_input', []);
        return $oldData[$key] ?? $default;
    }
}

if (!function_exists('flash')) {
    /**
     * Set a flash message.
     */
    function flash(string $type, string $message): void
    {
        (new Session())->flash($type, $message);
    }
}

if (!function_exists('redirect')) {
    /**
     * Redirect to a URL.
     */
    function redirect(string $url): void
    {
        header('Location: ' . $url);
        exit;
    }
}

if (!function_exists('formatCurrency')) {
    /**
     * Format a currency amount.
     */
    function formatCurrency(float $amount, string $symbol = 'GHS '): string
    {
        return $symbol . number_format($amount, 2);
    }
}

if (!function_exists('formatDate')) {
    /**
     * Format a date.
     */
    function formatDate(?string $date, string $format = 'd M, Y'): string
    {
        if (!$date) {
            return '';
        }
        return date($format, strtotime($date));
    }
}

if (!function_exists('formatDateTime')) {
    /**
     * Format a date and time.
     */
    function formatDateTime(?string $datetime): string
    {
        if (!$datetime) {
            return '';
        }
        return date('d M Y, h:i A', strtotime($datetime));
    }
}

if (!function_exists('generateReceiptNumber')) {
    /**
     * Generate a unique receipt number.
     */
    function generateReceiptNumber(): string
    {
        $prefix = 'RCPT';
        $date = date('Ymd');
        $random = strtoupper(substr(uniqid(), -4));
        return "{$prefix}-{$date}-{$random}";
    }
}

if (!function_exists('generateMemberNumber')) {
    /**
     * Generate a unique member number.
     */
    function generateMemberNumber(): string
    {
        $prefix = $_ENV['COMPANY_PREFIX'] ?? 'BGB';
        $year = date('Y');
        $db = Database::getInstance();
        $last = $db->fetchOne(
            "SELECT member_number FROM members ORDER BY id DESC LIMIT 1"
        );
        
        if ($last && preg_match('/(\d+)$/', $last['member_number'], $matches)) {
            $nextNum = (int)$matches[1] + 1;
        } else {
            $nextNum = 1;
        }
        
        return sprintf("%s-%s-%04d", $prefix, $year, $nextNum);
    }
}

if (!function_exists('truncate')) {
    /**
     * Truncate text with ellipsis.
     */
    function truncate(string $text, int $length = 100): string
    {
        if (strlen($text) <= $length) {
            return $text;
        }
        return substr($text, 0, $length) . '...';
    }
}

if (!function_exists('timeAgo')) {
    /**
     * Get a human-readable time ago string.
     */
    function timeAgo(string $datetime): string
    {
        $now = new DateTime();
        $past = new DateTime($datetime);
        $diff = $now->diff($past);

        if ($diff->y > 0) {
            return $diff->y . ' year' . ($diff->y > 1 ? 's' : '') . ' ago';
        }
        if ($diff->m > 0) {
            return $diff->m . ' month' . ($diff->m > 1 ? 's' : '') . ' ago';
        }
        if ($diff->d > 0) {
            return $diff->d . ' day' . ($diff->d > 1 ? 's' : '') . ' ago';
        }
        if ($diff->h > 0) {
            return $diff->h . ' hour' . ($diff->h > 1 ? 's' : '') . ' ago';
        }
        if ($diff->i > 0) {
            return $diff->i . ' minute' . ($diff->i > 1 ? 's' : '') . ' ago';
        }
        return 'Just now';
    }
}

if (!function_exists('compressUploadedImage')) {
    /**
     * Compress an image file to ~100 KB or less while keeping image quality sharp.
     */
    function compressUploadedImage(string $filepath, int $targetBytes = 102400): bool
    {
        if (!file_exists($filepath) || filesize($filepath) <= $targetBytes) {
            return true;
        }

        $imageInfo = @getimagesize($filepath);
        if (!$imageInfo) {
            return false;
        }

        $mime = $imageInfo['mime'] ?? '';
        $srcImage = null;

        switch ($mime) {
            case 'image/jpeg':
            case 'image/jpg':
                $srcImage = @imagecreatefromjpeg($filepath);
                break;
            case 'image/png':
                $srcImage = @imagecreatefrompng($filepath);
                break;
            case 'image/webp':
                $srcImage = @imagecreatefromwebp($filepath);
                break;
            default:
                return false;
        }

        if (!$srcImage) {
            return false;
        }

        $origWidth = imagesx($srcImage);
        $origHeight = imagesy($srcImage);

        // Step 1: Downscale max dimension if larger than 1600px
        $maxDimension = 1600;
        $width = $origWidth;
        $height = $origHeight;

        if ($origWidth > $maxDimension || $origHeight > $maxDimension) {
            if ($origWidth >= $origHeight) {
                $width = $maxDimension;
                $height = (int)round(($origHeight / $origWidth) * $maxDimension);
            } else {
                $height = $maxDimension;
                $width = (int)round(($origWidth / $origHeight) * $maxDimension);
            }
        }

        $dstImage = imagecreatetruecolor($width, $height);
        if ($mime === 'image/png' || $mime === 'image/webp') {
            imagealphablending($dstImage, false);
            imagesavealpha($dstImage, true);
            $transparent = imagecolorallocatealpha($dstImage, 255, 255, 255, 127);
            imagefilledrectangle($dstImage, 0, 0, $width, $height, $transparent);
        }

        imagecopyresampled($dstImage, $srcImage, 0, 0, 0, 0, $width, $height, $origWidth, $origHeight);

        // Step 2: Compress quality iteratively until file size <= 100 KB
        $quality = 85;
        $tempPath = $filepath . '_tmp.jpg';

        while ($quality >= 20) {
            imagejpeg($dstImage, $tempPath, $quality);
            if (filesize($tempPath) <= $targetBytes) {
                break;
            }
            $quality -= 10;
        }

        // Step 3: If still over target size, iteratively scale dimensions down by 15%
        while (file_exists($tempPath) && filesize($tempPath) > $targetBytes && $width > 200 && $height > 200) {
            $width = (int)max(200, round($width * 0.82));
            $height = (int)max(200, round($height * 0.82));
            $scaledDst = imagecreatetruecolor($width, $height);
            imagecopyresampled($scaledDst, $dstImage, 0, 0, 0, 0, $width, $height, imagesx($dstImage), imagesy($dstImage));
            imagedestroy($dstImage);
            $dstImage = $scaledDst;
            imagejpeg($dstImage, $tempPath, 50);
        }

        imagedestroy($srcImage);
        imagedestroy($dstImage);

        if (file_exists($tempPath)) {
            rename($tempPath, $filepath);
            return true;
        }

        return false;
    }
}

if (!function_exists('uploadFile')) {
    /**
     * Handle file upload and automatically compress image files to <= 100KB.
     */
    function uploadFile(array $file, string $directory, array $allowedTypes = [], int $maxSize = 10485760): ?string
    {
        if ($file['error'] !== UPLOAD_ERR_OK) {
            return null;
        }

        if ($file['size'] > $maxSize) {
            return null;
        }

        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!empty($allowedTypes)) {
            if (!in_array($ext, $allowedTypes)) {
                return null;
            }
        }

        $uploadDir = dirname(__DIR__, 2) . '/public/uploads/' . $directory;
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $filename = uniqid() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '', basename($file['name']));
        $filepath = $uploadDir . '/' . $filename;

        if (move_uploaded_file($file['tmp_name'], $filepath)) {
            // Compress all image uploads (JPEG, PNG, WebP) to ~100KB or less automatically
            if (in_array($ext, ['jpg', 'jpeg', 'png', 'webp'], true)) {
                compressUploadedImage($filepath, 100 * 1024);
            }
            return 'uploads/' . $directory . '/' . $filename;
        }

        return null;
    }
}

if (!function_exists('upload_url')) {
    /**
     * Build a URL for an uploaded file, tolerating paths stored with or
     * without a leading slash. Returns '' when there is no file.
     */
    function upload_url(?string $path): string
    {
        $path = trim((string)$path);
        if ($path === '') {
            return '';
        }
        if (preg_match('#^https?://#i', $path)) {
            return $path;
        }
        return url(ltrim($path, '/'));
    }
}

if (!function_exists('brand_parts')) {
    /**
     * Split the company name into a small overline and a display line so the
     * brand can be set as a two-part lockup (e.g. "21st & 24th Accra" over
     * "Boys & Girls Brigade"). Falls back to a single display line.
     */
    function brand_parts(?string $name): array
    {
        $name = trim((string)$name) ?: '21st and 24th Accra Boys and Girls Brigade';

        if (preg_match('/^(.*?\bAccra\b)\s+(.+)$/i', $name, $m)) {
            return ['over' => $m[1], 'main' => $m[2]];
        }
        if (preg_match('/^(.*?)\s+(Boys.*Brigade)$/i', $name, $m)) {
            return ['over' => $m[1], 'main' => $m[2]];
        }
        return ['over' => '', 'main' => $name];
    }
}

if (!function_exists('brand_ordinals')) {
    /**
     * Render ordinal suffixes (1st, 24th) as superscript for display headings.
     * Input is escaped first, so the return value is safe to echo directly.
     */
    function brand_ordinals(?string $text): string
    {
        return preg_replace('/(\d+)(st|nd|rd|th)\b/i', '$1<sup>$2</sup>', e($text));
    }
}

if (!function_exists('site_logo')) {
    /**
     * URL of the bundled company crest in public/assets/images.
     * Accepts any common extension so dropping in logo.png, logo.svg or
     * logo.webp all work. Returns '' when no file has been added yet.
     */
    function site_logo(): string
    {
        static $cached = null;
        if ($cached !== null) {
            return $cached;
        }
        $dir = dirname(__DIR__, 2) . '/public/assets/images/';
        foreach (['logo.svg', 'logo.png', 'logo.webp', 'logo.jpg', 'logo.jpeg'] as $file) {
            if (is_file($dir . $file)) {
                return $cached = asset('images/' . $file);
            }
        }
        return $cached = '';
    }
}

if (!function_exists('brand_logo')) {
    /**
     * The logo to show for the company. A logo uploaded through
     * Settings wins; otherwise the crest bundled with the project.
     * Returns '' when neither exists, so callers can fall back to an icon.
     */
    function brand_logo(?array $profile = null): string
    {
        if (!empty($profile['logo'])) {
            return upload_url($profile['logo']);
        }
        return site_logo();
    }
}

if (!function_exists('favicon_url')) {
    /**
     * Browser tab icon. Prefers a purpose-made favicon file, then the crest.
     */
    function favicon_url(): string
    {
        static $cached = null;
        if ($cached !== null) {
            return $cached;
        }
        $dir = dirname(__DIR__, 2) . '/public/assets/images/';
        foreach (['favicon.svg', 'favicon.png', 'favicon.ico'] as $file) {
            if (is_file($dir . $file)) {
                return $cached = asset('images/' . $file);
            }
        }
        return $cached = site_logo();
    }
}

if (!function_exists('favicon_tags')) {
    /**
     * Render the favicon <link> tags, or nothing when no icon is available.
     */
    function favicon_tags(): string
    {
        $icon = favicon_url();
        if ($icon === '') {
            return '';
        }
        $ext = strtolower(pathinfo(parse_url($icon, PHP_URL_PATH) ?? '', PATHINFO_EXTENSION));
        $type = match ($ext) {
            'svg'  => 'image/svg+xml',
            'ico'  => 'image/x-icon',
            'webp' => 'image/webp',
            'jpg', 'jpeg' => 'image/jpeg',
            default => 'image/png',
        };
        return '<link rel="icon" type="' . $type . '" href="' . e($icon) . '">' . "\n"
             . '    <link rel="apple-touch-icon" href="' . e($icon) . '">';
    }
}

if (!function_exists('render_pagination')) {
    /**
     * Render standard responsive pagination bar with entry counts and query parameter preservation.
     */
    function render_pagination(?array $pagination, ?string $baseUrl = null, array $extraParams = []): string
    {
        if (empty($pagination)) {
            return '';
        }

        $total = (int)($pagination['total'] ?? 0);
        $perPage = max(1, (int)($pagination['per_page'] ?? 20));
        $currentPage = max(1, (int)($pagination['current_page'] ?? 1));
        $totalPages = max(1, (int)($pagination['total_pages'] ?? (int)ceil($total / $perPage)));
        $offset = (int)($pagination['offset'] ?? (($currentPage - 1) * $perPage));

        if ($total <= 0) {
            return '';
        }

        $from = $offset + 1;
        $to = min($offset + $perPage, $total);

        // Build base query params preserving current $_GET filters except page
        $queryParams = $_GET ?? [];
        unset($queryParams['page'], $queryParams['url']);
        if (!empty($extraParams)) {
            $queryParams = array_merge($queryParams, $extraParams);
        }

        $buildUrl = function(int $page) use ($baseUrl, $queryParams): string {
            $params = array_merge($queryParams, ['page' => $page]);
            $qs = http_build_query($params);
            $url = $baseUrl ?? strtok($_SERVER['REQUEST_URI'] ?? '', '?');
            return e($url . ($qs !== '' ? '?' . $qs : ''));
        };

        $html = '<div class="d-flex flex-column flex-md-row align-items-center justify-content-between pt-3 mt-3 border-top gap-3 pagination-container">';
        $html .= '  <div class="text-muted small">Showing <span class="fw-semibold text-dark">' . $from . '</span> to <span class="fw-semibold text-dark">' . $to . '</span> of <span class="fw-semibold text-dark">' . $total . '</span> entries</div>';

        if ($totalPages <= 1) {
            $html .= '</div>';
            return $html;
        }

        $html .= '  <nav aria-label="Table pagination">';
        $html .= '    <ul class="pagination pagination-sm mb-0 flex-wrap justify-content-center">';

        // Prev Button
        if ($currentPage > 1) {
            $html .= '      <li class="page-item"><a class="page-link" href="' . $buildUrl($currentPage - 1) . '" aria-label="Previous"><i class="bi bi-chevron-left"></i></a></li>';
        } else {
            $html .= '      <li class="page-item disabled"><span class="page-link"><i class="bi bi-chevron-left"></i></span></li>';
        }

        // Smart range window
        $startPage = max(1, $currentPage - 2);
        $endPage = min($totalPages, $currentPage + 2);

        if ($startPage > 1) {
            $html .= '      <li class="page-item"><a class="page-link" href="' . $buildUrl(1) . '">1</a></li>';
            if ($startPage > 2) {
                $html .= '      <li class="page-item disabled"><span class="page-link">…</span></li>';
            }
        }

        for ($p = $startPage; $p <= $endPage; $p++) {
            if ($p === $currentPage) {
                $html .= '      <li class="page-item active"><span class="page-link">' . $p . '</span></li>';
            } else {
                $html .= '      <li class="page-item"><a class="page-link" href="' . $buildUrl($p) . '">' . $p . '</a></li>';
            }
        }

        if ($endPage < $totalPages) {
            if ($endPage < $totalPages - 1) {
                $html .= '      <li class="page-item disabled"><span class="page-link">…</span></li>';
            }
            $html .= '      <li class="page-item"><a class="page-link" href="' . $buildUrl($totalPages) . '">' . $totalPages . '</a></li>';
        }

        // Next Button
        if ($currentPage < $totalPages) {
            $html .= '      <li class="page-item"><a class="page-link" href="' . $buildUrl($currentPage + 1) . '" aria-label="Next"><i class="bi bi-chevron-right"></i></a></li>';
        } else {
            $html .= '      <li class="page-item disabled"><span class="page-link"><i class="bi bi-chevron-right"></i></span></li>';
        }

        $html .= '    </ul>';
        $html .= '  </nav>';
        $html .= '</div>';

        return $html;
    }
}
