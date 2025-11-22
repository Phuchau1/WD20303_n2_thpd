<?php
// Simple import script to run database.sql into MySQL.
// Usage: open http://localhost/webdogiadung/import_db.php in browser (only for development).
require_once __DIR__ . '/core/config.php';

$host = DB_HOST;
$user = DB_USER;
$pass = DB_PASS;
$sqlFile = __DIR__ . '/database.sql';

if (!file_exists($sqlFile)) {
    die('database.sql not found');
}

try {
    // connect without database to allow CREATE DATABASE
    $pdo = new PDO("mysql:host={$host}", $user, $pass, [PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION]);
} catch (PDOException $e) {
    die('Connection failed: ' . $e->getMessage());
}

$sql = file_get_contents($sqlFile);
// Split statements by semicolon. This is a simple parser and works for typical dumps without complex delimiters.
$statements = array_filter(array_map('trim', explode(";", $sql)));

foreach ($statements as $stmt) {
    if ($stmt === '') continue;
    try {
        $pdo->exec($stmt);
    } catch (PDOException $e) {
        // continue on errors to allow if tables already exist
        echo "Error executing statement: " . htmlspecialchars($e->getMessage()) . "<br>";
    }
}

echo "Import finished. If there were errors, check the messages above.<br>";
echo "Now open your site at " . (defined('BASE_URL')? BASE_URL : '/') . "<br>";
?>