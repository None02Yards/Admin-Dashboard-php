#!/usr/bin/env bash
# Validate environment variables & config consistency, and PHP compatibility
set -euo pipefail

php -r '
$min = "8.0.0";
if (version_compare(PHP_VERSION, $min, "<")) {
    fwrite(STDERR, "ERROR: PHP version ".PHP_VERSION." < required ".$min.PHP_EOL);
    exit(2);
}
echo "PHP version: ".PHP_VERSION.PHP_EOL;
require __DIR__ . "/config/config.php";
$vars = ["DB_HOST","DB_NAME","DB_USER","DB_PASS"];
$missing = [];
foreach ($vars as $v) {
    $val = defined($v) ? constant($v) : null;
    if (empty($val)) $missing[] = $v;
}
if (!empty($missing)) {
    echo "WARNING: Missing config constants: ".implode(", ", $missing).PHP_EOL;
} else {
    echo "Config constants: OK".PHP_EOL;
}
// Check for conflicting env vars vs config file values
foreach (["DB_HOST"=>"DB_HOST","MYSQL_DATABASE"=>"DB_NAME","MYSQL_USER"=>"DB_USER","MYSQL_PASSWORD"=>"DB_PASS"] as $env=>$const) {
    $envv = getenv($env);
    $constv = defined($const) ? constant($const) : null;
    if ($envv !== false && $constv !== null && $envv !== $constv) {
        echo "NOTICE: Env $env=\"$envv\" differs from config constant $const=\"$constv\"".PHP_EOL;
    }
}
exit(0);
'