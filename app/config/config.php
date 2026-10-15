<?php
/**
 * OpenCropCare - Application Configuration
 */

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Application Constants
define('APP_NAME', 'OpenCropCare');
define('APP_TAGLINE', 'AI-Powered Plant Disease Detection System');
define('APP_VERSION', '1.0.0');
define('APP_URL', getenv('APP_URL') ?: 'http://localhost');

// Directory Paths
define('ROOT_PATH', dirname(dirname(__DIR__)));
define('APP_PATH', ROOT_PATH . '/app');
define('PUBLIC_PATH', ROOT_PATH . '/public');
define('UPLOAD_DIR', PUBLIC_PATH . '/uploads');
define('DATA_DIR', ROOT_PATH . '/database/data');

// ML Microservice Configuration
define('ML_SERVICE_URL', getenv('ML_SERVICE_URL') ?: 'http://127.0.0.1:5000');
define('ML_TIMEOUT_SECONDS', 15);

// CouchDB Configuration
define('COUCHDB_HOST', getenv('COUCHDB_HOST') ?: '127.0.0.1');
define('COUCHDB_PORT', getenv('COUCHDB_PORT') ?: '5984');
define('COUCHDB_USER', getenv('COUCHDB_USER') ?: 'DANASUSIHA');
define('COUCHDB_PASS', getenv('COUCHDB_PASS') ?: 'DANASUSIHA14');
define('COUCHDB_DB',   getenv('COUCHDB_DB')   ?: 'opencropcare');

// Upload Security
define('MAX_UPLOAD_SIZE_BYTES', 8 * 1024 * 1024); // 8MB
define('ALLOWED_IMAGE_TYPES', ['image/jpeg', 'image/png', 'image/webp']);
define('ALLOWED_IMAGE_EXTENSIONS', ['jpg', 'jpeg', 'png', 'webp']);

// Initialize Upload & Data Directories if needed
if (!file_exists(UPLOAD_DIR)) {
    @mkdir(UPLOAD_DIR, 0755, true);
}
if (!file_exists(DATA_DIR)) {
    @mkdir(DATA_DIR, 0755, true);
}

// CSRF Security Helpers
function generate_csrf_token(): string {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verify_csrf_token(?string $token): bool {
    if (empty($token) || empty($_SESSION['csrf_token'])) {
        return false;
    }
    return hash_equals($_SESSION['csrf_token'], $token);
}

// Helper to escape HTML output
function e(?string $string): string {
    return htmlspecialchars($string ?? '', ENT_QUOTES, 'UTF-8');
}

// Helper for active navigation link
function is_active_route(string $route): string {
    $current = $_GET['route'] ?? 'home';
    return ($current === $route) ? 'active' : '';
}
