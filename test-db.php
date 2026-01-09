<?php

echo "Testing database connection...\n\n";

// Load .env
$env = parse_ini_file(__DIR__ . '/.env');

$host = $env['DB_HOST'] ?? 'localhost';
$database = $env['DB_DATABASE'] ?? 'tuufi_4ekim';
$username = $env['DB_USERNAME'] ?? '';
$password = $env['DB_PASSWORD'] ?? '';

echo "Host: {$host}\n";
echo "Database: {$database}\n";
echo "Username: {$username}\n\n";

try {
    $pdo = new PDO("mysql:host={$host};dbname={$database};charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "✅ Connection successful!\n\n";

    // Check tables
    $stmt = $pdo->query("SHOW TABLES LIKE 'muzibu_%'");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);

    echo "Found " . count($tables) . " muzibu tables:\n";
    foreach ($tables as $table) {
        $count = $pdo->query("SELECT COUNT(*) FROM {$table}")->fetchColumn();
        echo "  - {$table}: {$count} rows\n";
    }

} catch (PDOException $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
