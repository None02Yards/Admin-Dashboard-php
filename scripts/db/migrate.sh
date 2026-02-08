#!/usr/bin/env bash
# Apply the SQL schema (sql/schema.sql) to the configured database.
set -euo pipefail
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)/.."

php_config() {
  php -r "require 'config/config.php'; echo defined('$1') ? constant('$1') : ''; "
}

DB_HOST="${DB_HOST:-$(php_config DB_HOST)}"
DB_NAME="${DB_NAME:-$(php_config DB_NAME)}"
DB_USER="${DB_USER:-$(php_config DB_USER)}"
DB_PASS="${DB_PASS:-$(php_config DB_PASS)}"

SCHEMA_FILE="sql/schema.sql"

if [ ! -f "$SCHEMA_FILE" ]; then
  echo "ERROR: $SCHEMA_FILE not found."
  exit 1
fi

echo "Applying schema ($SCHEMA_FILE) to ${DB_NAME}@${DB_HOST}..."
mysql -h "$DB_HOST" -u "$DB_USER" -p"$DB_PASS" "$DB_NAME" < "$SCHEMA_FILE"
echo "Schema applied."