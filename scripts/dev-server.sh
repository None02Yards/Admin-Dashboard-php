#!/usr/bin/env bash
# Start PHP built-in server for quick local testing.
# Usage: ./scripts/dev-server.sh [host] [port]
# Example: ./scripts/dev-server.sh 127.0.0.1 8000
set -euo pipefail

HOST="${1:-127.0.0.1}"
PORT="${2:-8000}"
DOCROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)" # project root

echo "Starting PHP dev server at http://${HOST}:${PORT}/ (docroot: ${DOCROOT})"
echo "Use Ctrl+C to stop."

# Use router.php so requests for existing files are served directly,
# otherwise forwarded to index.php (your front controller).
php -S "${HOST}:${PORT}" -t "${DOCROOT}" "${DOCROOT}/router.php"