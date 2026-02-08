#!/usr/bin/env bash
# Dump the configured database to a timestamped gzip file.
set -euo pipefail
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)/.."
php_config() {
  php -r "require 'config/config.php'; echo defined('$1') ? constant('$1') : ''; "
}

DB_HOST="${DB_HOST:-$(php_config DB_HOST)}"
DB_NAME="${DB_NAME:-$(php_config DB_NAME)}"
DB_USER="${DB_USER:-$(php_config DB_USER)}"
DB_PASS="${DB_PASS:-$(php_config DB_PASS)}"

OUT_DIR="${1:-$SCRIPT_DIR/backups}"
mkdir -p "$OUT_DIR"
TIMESTAMP="$(date +%Y%m%d-%H%M%S)"
OUT_FILE="${OUT_DIR}/dump-${DB_NAME}-${TIMESTAMP}.sql.gz"

echo "Backing up ${DB_NAME}@${DB_HOST} -> ${OUT_FILE} ..."
mysqldump -h "$DB_HOST" -u "$DB_USER" -p"$DB_PASS" "$DB_NAME" | gzip > "$OUT_FILE"
echo "Backup finished: $OUT_FILE"