#!/usr/bin/env bash
# Create or update an admin user using a secure PDO-based PHP script.
# Prompts for username and password. Uses prepared statements.
set -euo pipefail
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"

read -p "Admin username (default: admin): " USERNAME
USERNAME="${USERNAME:-admin}"
read -s -p "Password for ${USERNAME}: " PASSWORD
echo
if [ -z "$PASSWORD" ]; then
  echo "Password required."
  exit 1
fi

php -r '
require __DIR__ . "/config/config.php";
$username = $argv[1];
$password = $argv[2];
try {
    $dsn = "mysql:host=".DB_HOST.";dbname=".DB_NAME.";charset=utf8mb4";
    $pdo = new PDO($dsn, DB_USER, DB_PASS, [PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION]);
    // Upsert user safely using transaction and prepared statements
    $pdo->beginTransaction();
    $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ? LIMIT 1");
    $stmt->execute([$username]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $hash = password_hash($password, PASSWORD_DEFAULT);
    if ($row) {
        $upd = $pdo->prepare("UPDATE users SET password_hash = ?, role = 'admin' WHERE id = ?");
        $upd->execute([$hash, $row["id"]]);
        echo "Updated existing user: $username\n";
    } else {
        $ins = $pdo->prepare("INSERT INTO users (username, password_hash, role) VALUES (?, ?, 'admin')");
        $ins->execute([$username, $hash]);
        echo "Created admin user: $username\n";
    }
    $pdo->commit();
} catch (Exception $e) {
    if ($pdo && $pdo->inTransaction()) $pdo->rollBack();
    echo "Error: " . $e->getMessage() . "\\n";
    exit(1);
}
' -- "$USERNAME" "$PASSWORD"