#!/usr/bin/env bash
# Detect schema drift by dumping current DB schema (no data) and diffing against sql/schema.sql
set -euo pipefail
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)/.."
php_config() {
  php -r "require 'config/config.php'; echo defined('$1') ? constant('$1') : ''; "
}

DB_HOST="${DB_HOST:-$(php_config DB_HOST)}"
DB_NAME="${DB_NAME:-$(php_config DB_NAME)}"
DB_USER="${DB_USER:-$(php_config DB_USER)}"
DB_PASS="${DB_PASS:-$(php_config DB_PASS)}"

if ! command -v mysqldump >/dev/null 2>&1; then
  echo "ERROR: mysqldump not found in PATH."
  exit 2
fi

if [ ! -f "$SCRIPT_DIR/sql/schema.sql" ]; then
  echo "ERROR: $SCRIPT_DIR/sql/schema.sql not found."
  exit 2
fi

TMP_CUR=$(mktemp /tmp/schema_current.XXXXXX.sql)
trap 'rm -f "$TMP_CUR"' EXIT

echo "Dumping current schema (no data) to $TMP_CUR ..."
mysqldump --no-data --skip-comments -h "$DB_HOST" -u "$DB_USER" -p"$DB_PASS" "$DB_NAME" > "$TMP_CUR"

echo "Comparing dumped schema to $SCRIPT_DIR/sql/schema.sql ..."
# Simplify expected schema by extracting CREATE TABLE blocks from sql/schema.sql for comparison
TMP_EXP=$(mktemp /tmp/schema_expected.XXXXXX.sql)
trap 'rm -f "$TMP_CUR" "$TMP_EXP"' EXIT

# Extract CREATE TABLE and related statements from schema.sql (simple heuristic)
awk '
/^CREATE TABLE/ { in=1 }
in { print }
in && /\);/ { in=0 }
' "$SCRIPT_DIR/sql/schema.sql" > "$TMP_EXP" || true

# Normalize: remove AUTO_INCREMENT values to avoid expected diffs
sed -E "s/ AUTO_INCREMENT=[0-9]+//g" "$TMP_CUR" > "${TMP_CUR}.norm"
sed -E "s/ AUTO_INCREMENT=[0-9]+//g" "$TMP_EXP" > "${TMP_EXP}.norm"

if diff -u --label current "${TMP_CUR}.norm" --label expected "${TMP_EXP}.norm"; then
  echo "OK: Schema matches (no diff)."
  rm -f "${TMP_CUR}.norm" "${TMP_EXP}.norm"
  exit 0
else
  echo "ERROR: Schema drift detected (see diff above)."
  rm -f "${TMP_CUR}.norm" "${TMP_EXP}.norm"
  exit 3
fi