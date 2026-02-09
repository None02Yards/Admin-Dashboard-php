#!/usr/bin/env bash
# Nuke & pave the database for a clean slate:
#  - Drop DB
#  - Recreate DB & user
#  - Run migrate.sh
#  - Run seed.sh
#  - Optionally run make-admin.sh
set -euo pipefail
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
ROOT_USER_DEFAULT="root"

php_config() {
  php -r "require 'config/config.php'; echo defined('$1') ? constant('$1') : ''; "
}

DB_HOST="${DB_HOST:-$(php_config DB_HOST)}"
DB_NAME="${DB_NAME:-$(php_config DB_NAME)}"
DB_USER="${DB_USER:-$(php_config DB_USER)}"
DB_PASS="${DB_PASS:-$(php_config DB_PASS)}"

echo "WARNING: This will DROP and RECREATE database '${DB_NAME}' on host ${DB_HOST}."
read -p "Type RESET to proceed: " confirm
if [ "$confirm" != "RESET" ]; then
  echo "Aborted."
  exit 0
fi

read -p "Enter privileged MySQL user to perform DROP/CREATE (default: ${ROOT_USER_DEFAULT}): " ROOT_USER
ROOT_USER="${ROOT_USER:-$ROOT_USER_DEFAULT}"
read -s -p "Enter password for ${ROOT_USER} (leave blank to use interactive mysql client prompt): " ROOT_PASS
echo

echo "Dropping and recreating database '${DB_NAME}'..."
if [ -n "$ROOT_PASS" ]; then
  mysql -h "$DB_HOST" -u "$ROOT_USER" -p"$ROOT_PASS" -e "DROP DATABASE IF EXISTS \`$DB_NAME\`; CREATE DATABASE \`$DB_NAME\` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci; GRANT ALL PRIVILEGES ON \`$DB_NAME\`.* TO '${DB_USER}'@'%' IDENTIFIED BY '${DB_PASS}'; FLUSH PRIVILEGES;"
else
  mysql -h "$DB_HOST" -u "$ROOT_USER" -e "DROP DATABASE IF EXISTS \`$DB_NAME\`; CREATE DATABASE \`$DB_NAME\` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci; GRANT ALL PRIVILEGES ON \`$DB_NAME\`.* TO '${DB_USER}'@'%' IDENTIFIED BY '${DB_PASS}'; FLUSH PRIVILEGES;"
fi

echo "Running migrations..."
"$SCRIPT_DIR/migrate.sh"

echo "Seeding demo data..."
"$SCRIPT_DIR/seed.sh"

read -p "Create/update admin now? [Y/n]: " create_admin
create_admin="${create_admin:-Y}"
if [[ "$create_admin" =~ ^[Yy]$ ]]; then
  "$SCRIPT_DIR/../make-admin.sh"
fi

echo "Reset complete."