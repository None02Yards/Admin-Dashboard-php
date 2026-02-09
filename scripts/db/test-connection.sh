#!/usr/bin/env bash
# Validate DB connectivity via PDO (SELECT 1)
set -euo pipefail

php -r '
require __DIR__ . "/config/config.php";
try {
    $dsn = "mysql:host=".DB_HOST.";dbname=".DB_NAME.";charset=utf8mb4";
    $pdo = new PDO($dsn, DB_USER, DB_PASS, [PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION]);
    $stmt = $pdo->query("SELECT 1");
    $ok = $stmt->fetchColumn();
    if ($ok == 1) {
        echo "OK: Connected to ".DB_NAME." on ".DB_HOST." as ".DB_USER.PHP_EOL;
        exit(0);
    } else {
        echo "ERROR: SELECT 1 failed".PHP_EOL;
        exit(2);
    }
} catch (Exception $e) {
    fwrite(STDERR, "ERROR: " . $e->getMessage() . PHP_EOL);
    exit(1);
}
'