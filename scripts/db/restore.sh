#!/usr/bin/env bash
# Restore a gzip-compressed SQL dump into the configured database.
# Usage: scripts/db/restore.sh /path/to/dump.sql.gz
# If no file provided, list backups/ and prompt to choose one.
set -euo pipefail
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)/.."
php_config() {
  php -r "require 'config/config.php'; echo defined('$1') ? constant('$1') : ''; "
}

DB_HOST="${DB_HOST:-$(php_config DB_HOST)}"
DB_NAME="${DB_NAME:-$(php_config DB_NAME)}"
DB_USER="${DB_USER:-$(php_config DB_USER)}"
DB_PASS="${DB_PASS:-$(php_config DB_PASS)}"

if [ "${1:-}" == "" ]; then
  echo "No dump supplied. Listing files in backups/ ..."
  ls -1 -- $SCRIPT_DIR/backups/*.sql.gz 2>/dev/null || true
  read -p "Enter path to .sql.gz to restore: " FILE
else
  FILE="$1"
fi

if [ ! -f "$FILE" ]; then
  echo "ERROR: File not found: $FILE"
  exit 1
fi

echo "WARNING: This will overwrite data in database ${DB_NAME}@${DB_HOST}."
read -p "Type 'YES' to proceed: " confirm
if [ "$confirm" != "YES" ]; then
  echo "Aborted."
  exit 0
fi

echo "Restoring $FILE -> ${DB_NAME} ..."
gunzip -c "$FILE" | mysql -h "$DB_HOST" -u "$DB_USER" -p"$DB_PASS" "$DB_NAME"
echo "Restore complete."