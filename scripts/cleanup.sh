#!/usr/bin/env bash
# Repo hygiene automation: remove old backups, temp files etc.
set -euo pipefail
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
BACKUP_DIR="$SCRIPT_DIR/backups"
DAYS=${1:-30}

echo "This will remove backups older than $DAYS days from $BACKUP_DIR and clean temp artefacts."
read -p "Proceed? [y/N]: " ans
ans="${ans:-N}"
if [[ ! "$ans" =~ ^[Yy]$ ]]; then
  echo "Aborted."
  exit 0
fi

if [ -d "$BACKUP_DIR" ]; then
  echo "Removing files older than $DAYS days in $BACKUP_DIR ..."
  find "$BACKUP_DIR" -type f -mtime +"$DAYS" -print -exec rm -f {} \;
else
  echo "No backups directory at $BACKUP_DIR"
fi

echo "Removing .tmp files..."
find . -type f -name '*.tmp' -print -exec rm -f {} \;

echo "Removing PHPUnit cache if present..."
[ -f phpunit.result.cache ] && rm -f phpunit.result.cache && echo "Removed phpunit.result.cache"

echo "Cleanup done."