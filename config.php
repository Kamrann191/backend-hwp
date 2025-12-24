<?php
declare(strict_types=1);

// Load local config file (kept out of Git)
$localConfig = null;
if (file_exists(__DIR__ . '/config.local.php')) {
    $localConfig = require __DIR__ . '/config.local.php';
}

// Define constants with precedence: env vars > local config > defaults
define('DB_HOST', getenv('DB_HOST') ?: ($localConfig['DB_HOST'] ?? 'localhost'));
define('DB_PORT', (int)(getenv('DB_PORT') ?: ($localConfig['DB_PORT'] ?? 3306)));
define('DB_NAME', getenv('DB_NAME') ?: ($localConfig['DB_NAME'] ?? 'driving_experience'));
define('DB_USER', getenv('DB_USER') ?: ($localConfig['DB_USER'] ?? 'root'));
define('DB_PASS', getenv('DB_PASS') ?: ($localConfig['DB_PASS'] ?? ''));

function getConnection(): PDO {
    try {
        $pdo = new PDO(
            "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";charset=utf8mb4",
            DB_USER,
            DB_PASS,
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]
        );
        return $pdo;
    } catch (Throwable $e) {
        http_response_code(500);
        echo "Database connection error: " . htmlspecialchars($e->getMessage());
        exit;
    }
}

function ensureDatabaseExists(): void {
    try {
        $pdo0 = new PDO(
            "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";charset=utf8mb4",
            DB_USER,
            DB_PASS,
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
        );
        $pdo0->exec("CREATE DATABASE IF NOT EXISTS `" . DB_NAME . "` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    } catch (Throwable $e) {
        http_response_code(500);
        echo "Initial DB error: " . htmlspecialchars($e->getMessage());
        exit;
    }
}
