#!/usr/bin/env bash
# Seed demo positions & candidates. Uses PDO via PHP to perform safe inserts.
set -euo pipefail
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)/.."

php -r '
require __DIR__ . "/../config/config.php";
try {
    $dsn = "mysql:host=".DB_HOST.";dbname=".DB_NAME.";charset=utf8mb4";
    $pdo = new PDO($dsn, DB_USER, DB_PASS, [PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION]);
    // Insert positions if not exists
    $positions = ["President","Treasurer","Secretary"];
    $insPos = $pdo->prepare("INSERT INTO positions (name) SELECT :name FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM positions WHERE name = :name)");
    foreach ($positions as $p) { $insPos->execute([":name"=>$p]); }

    // Insert some candidates for each position if not exists
    $candMap = [
        "President" => ["Alice","Bob"],
        "Treasurer" => ["Charlie"],
        "Secretary" => ["Denise","Eve"]
    ];
    $findPos = $pdo->prepare("SELECT id FROM positions WHERE name = ? LIMIT 1");
    $insCand = $pdo->prepare("INSERT INTO candidates (position_id, name) SELECT :position_id, :name FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM candidates WHERE position_id = :position_id AND name = :name)");
    foreach ($candMap as $posName => $cands) {
        $findPos->execute([$posName]);
        $pid = $findPos->fetchColumn();
        if (!$pid) continue;
        foreach ($cands as $c) {
            $insCand->execute([":position_id"=>$pid, ":name"=>$c]);
        }
    }
    echo "Seeding completed.\n";
} catch (Exception $e) {
    echo "Seeding failed: ".$e->getMessage()."\n";
    exit(1);
}
'