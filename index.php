<?php
declare(strict_types=1);
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/session.php';
require_once __DIR__ . '/database.php';
require_once __DIR__ . '/router.php';

// Start session
initializeSession();

// Make sure the database exists
ensureDatabaseExists();

// Open database connection
$pdo = getConnection();

// Set up database schema
initializeSchema($pdo);

// Populate lookup tables
seedLookupTables($pdo);

// Detect API request
if (isset($_GET['api'])) {
    // Route API requests
    $router = new Router($pdo);
    $router->dispatch();
} else {
    // Render HTML page
    $lookups = getLookupData($pdo);
    require __DIR__ . '/view.php';
}
