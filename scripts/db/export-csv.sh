#!/usr/bin/env bash
# Export tables to CSV files under exports/
set -euo pipefail
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)/.."
OUT_DIR="${1:-$SCRIPT_DIR/exports}"
shift || true
TABLES=("$@")

if [ ${#TABLES[@]} -eq 0 ]; then
  echo "Usage: $0 table1 [table2 ...]"
  exit 2
fi

mkdir -p "$OUT_DIR"

php <<'PHP'
<?php
$argv = isset($argv) ? $argv : [];
$scriptDir = __DIR__;
$outDir = $argv[1] ?? $scriptDir . "/exports";
$tables = array_slice($argv, 2);
require $scriptDir . "/config/config.php";
try {
    $dsn = "mysql:host=".DB_HOST.";dbname=".DB_NAME.";charset=utf8mb4";
    $pdo = new PDO($dsn, DB_USER, DB_PASS, [PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC]);
    foreach ($tables as $t) {
        $safe = preg_replace('/[^a-zA-Z0-9_]/','_',$t);
        $fname = rtrim($outDir,'/')."/$safe.csv";
        $fh = fopen($fname,'w');
        if (!$fh) { echo "Failed to open $fname\n"; continue; }
        $stmt = $pdo->query("SELECT * FROM `$t`");
        $first = true;
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            if ($first) {
                fputcsv($fh, array_keys($row));
                $first = false;
            }
            fputcsv($fh, array_values($row));
        }
        fclose($fh);
        echo "Exported $t -> $fname\n";
    }
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    exit(1);
}
PHP