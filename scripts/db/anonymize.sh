#!/usr/bin/env bash
# Scrub sensitive data in local DB
# - Replace usernames with anon ids
# - Reset passwords to a known value (or random)
# - Mask candidate names
set -euo pipefail
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)/.."

read -p "This will irreversibly anonymize data in DB. Type ANON to proceed: " confirm
if [ "$confirm" != "ANON" ]; then
  echo "Aborted."
  exit 0
fi

DEFAULT_PW="password"
read -p "Set all user passwords to (default: password): " PW
PW="${PW:-$DEFAULT_PW}"

php <<'PHP'
<?php
require __DIR__ . '/config/config.php';
try {
    $dsn = "mysql:host=".DB_HOST.";dbname=".DB_NAME.";charset=utf8mb4";
    $pdo = new PDO($dsn, DB_USER, DB_PASS, [PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION]);
    $pdo->beginTransaction();

    // Anonymize users: username -> anon{id}, password -> hashed PW
    $pw = $argv[1] ?? 'password';
    $hash = password_hash($pw, PASSWORD_DEFAULT);
    $stmt = $pdo->query("SELECT id FROM users");
    $ids = $stmt->fetchAll(PDO::FETCH_COLUMN);
    $upd = $pdo->prepare("UPDATE users SET username = ?, password_hash = ? WHERE id = ?");
    foreach ($ids as $id) {
        $newu = 'anon' . $id;
        $upd->execute([$newu, $hash, $id]);
    }

    // Mask candidate names
    $stmt = $pdo->query("SELECT id FROM candidates");
    $ids = $stmt->fetchAll(PDO::FETCH_COLUMN);
    $updC = $pdo->prepare("UPDATE candidates SET name = ? WHERE id = ?");
    foreach ($ids as $id) {
        $updC->execute(['candidate_' . $id, $id]);
    }

    $pdo->commit();
    echo "Anonymization complete. Users and candidates updated.\n";
} catch (Exception $e) {
    if ($pdo && $pdo->inTransaction()) $pdo->rollBack();
    echo "ERROR: " . $e->getMessage() . "\n";
    exit(1);
}
PHP