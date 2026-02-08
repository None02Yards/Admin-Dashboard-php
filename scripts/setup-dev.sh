#!/usr/bin/env bash
# Quick local setup:
#  - create database and user (using a privileged account you provide)
#  - apply schema/sql
#  - seed demo data (positions & candidates)
#  - create an admin user (interactive)
#
# This script will try to read DB_* from the environment; otherwise it will read config/config.php.
set -euo pipefail
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"

# Helper to read PHP config constants if not provided via env
php_config() {
  php -r "require 'config/config.php'; echo defined('$1') ? constant('$1') : ''; "
}

DB_HOST="${DB_HOST:-$(php_config DB_HOST)}"
DB_NAME="${DB_NAME:-$(php_config DB_NAME)}"
DB_USER="${DB_USER:-$(php_config DB_USER)}"
DB_PASS="${DB_PASS:-$(php_config DB_PASS)}"

echo "Using:"
echo "  DB_HOST=${DB_HOST}"
echo "  DB_NAME=${DB_NAME}"
echo "  DB_USER=${DB_USER}"

read -p "Enter privileged MySQL user to create DB and user (default: root): " ROOT_USER
ROOT_USER="${ROOT_USER:-root}"
read -s -p "Enter password for ${ROOT_USER} (leave empty to prompt mysql client): " ROOT_PASS
echo

# Create DB and grant user permissions
echo "Creating database '${DB_NAME}' and granting permissions to '${DB_USER}'..."
if [ -n "${ROOT_PASS}" ]; then
  mysql -h "$DB_HOST" -u "$ROOT_USER" -p"$ROOT_PASS" -e "CREATE DATABASE IF NOT EXISTS \`$DB_NAME\` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci; GRANT ALL PRIVILEGES ON \`$DB_NAME\`.* TO '${DB_USER}'@'%' IDENTIFIED BY '${DB_PASS}'; FLUSH PRIVILEGES;"
else
  mysql -h "$DB_HOST" -u "$ROOT_USER" -e "CREATE DATABASE IF NOT EXISTS \`$DB_NAME\` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci; GRANT ALL PRIVILEGES ON \`$DB_NAME\`.* TO '${DB_USER}'@'%' IDENTIFIED BY '${DB_PASS}'; FLUSH PRIVILEGES;"
fi

# Apply schema
if [ ! -f sql/schema.sql ]; then
  echo "ERROR: sql/schema.sql not found."
  exit 1
fi
echo "Applying SQL schema..."
mysql -h "$DB_HOST" -u "$DB_USER" -p"$DB_PASS" "$DB_NAME" < sql/schema.sql
echo "Schema applied."

# Seed data
read -p "Seed example positions & candidates? [Y/n]: " seed
seed="${seed:-Y}"
if [[ "$seed" =~ ^[Yy]$ ]]; then
  echo "Seeding demo data..."
  "$SCRIPT_DIR/db/seed.sh"
fi

# Create admin
read -p "Create or update an admin user now? [Y/n]: " create_admin
create_admin="${create_admin:-Y}"
if [[ "$create_admin" =~ ^[Yy]$ ]]; then
  "$SCRIPT_DIR/make-admin.sh"
fi

echo "Done. Start dev server: ./scripts/dev-server.sh or php -S localhost:8000"