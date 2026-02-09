#!/usr/bin/env bash
# One-command system readiness check running multiple checks and reporting PASS/FAIL
set -euo pipefail
SCRIPTS_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"

echo "Running health checks..."
FAIL=0

echo "1) check-deps.sh"
if bash "$SCRIPTS_DIR/check-deps.sh"; then
  echo "  OK"
else
  echo "  FAIL"
  FAIL=1
fi

echo "2) env-check.sh"
if bash "$SCRIPTS_DIR/env-check.sh"; then
  echo "  OK"
else
  echo "  FAIL"
  FAIL=1
fi

echo "3) test-connection.sh"
if bash "$SCRIPTS_DIR/test-connection.sh"; then
  echo "  OK"
else
  echo "  FAIL"
  FAIL=1
fi

echo "4) lint.sh"
if bash "$SCRIPTS_DIR/lint.sh"; then
  echo "  OK"
else
  echo "  FAIL"
  FAIL=1
fi

if [ "$FAIL" -eq 0 ]; then
  echo "HEALTHCHECK: ALL PASSED"
  exit 0
else
  echo "HEALTHCHECK: SOME CHECKS FAILED"
  exit 2
fi