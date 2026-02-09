#!/usr/bin/env bash
# Show current DB state at a glance: DB name, table count, row counts, latest table creation time
set -euo pipefail

php -r '
require __DIR__ . "/../config/config.php";
try {
    $dsn = "mysql:host=".DB_HOST.";dbname=".DB_NAME.";charset=utf8mb4";
    $pdo = new PDO($dsn, DB_USER, DB_PASS, [PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC]);

    // table count
    $stmt = $pdo->prepare("SELECT COUNT(*) AS cnt FROM information_schema.tables WHERE table_schema = ?");
    $stmt->execute([DB_NAME]);
    $tableCount = $stmt->fetchColumn();

    echo "Database: " . DB_NAME . PHP_EOL;
    echo "Tables: " . $tableCount . PHP_EOL;

    // row counts for important tables (if they exist)
    $tables = ['users','positions','candidates','votes'];
    foreach ($tables as $t) {
        $exists = $pdo->prepare("SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = ? AND table_name = ?");
        $exists->execute([DB_NAME, $t]);
        if ($exists->fetchColumn() > 0) {
            $c = $pdo->query("SELECT COUNT(*) FROM `$t`")->fetchColumn();
            echo str_pad($t, 12) . ": " . $c . PHP_EOL;
        } else {
            echo str_pad($t, 12) . ": (missing)" . PHP_EOL;
        }
    }

    // Latest table creation / update time (best-effort)
    $stmt = $pdo->prepare("SELECT table_name, create_time FROM information_schema.tables WHERE table_schema = ? ORDER BY create_time DESC LIMIT 1");
    $stmt->execute([DB_NAME]);
    $row = $stmt->fetch();
    if ($row && $row['create_time']) {
        echo "Latest table created: " . $row['table_name'] . " at " . $row['create_time'] . PHP_EOL;
    } else {
        echo "Latest table creation time: not available" . PHP_EOL;
    }

    // migration tracking (optional)
    $migrations = ['migrations','schema_migrations'];
    $found = false;
    foreach ($migrations as $m) {
        $exists = $pdo->prepare("SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = ? AND table_name = ?");
        $exists->execute([DB_NAME, $m]);
        if ($exists->fetchColumn() > 0) {
            $found = true;
            $latest = $pdo->query("SELECT * FROM `$m` ORDER BY id DESC LIMIT 1")->fetch();
            echo "Migration table: $m (latest row: " . json_encode($latest) . ")" . PHP_EOL;
            break;
        }
    }
    if (!$found) {
        echo "Migration table: not tracked (no migrations/schema_migrations table found)" . PHP_EOL;
    }
    exit(0);
} catch (Exception $e) {
    fwrite(STDERR, "ERROR: " . $e->getMessage() . PHP_EOL);
    exit(1);
}
'