#!/usr/bin/env bash
set -e

DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"

echo "Copying Supervisor configuration files to /etc/supervisor/conf.d/..."
cp "${DIR}"/*.conf /etc/supervisor/conf.d/

echo "Updating Supervisor..."
supervisorctl reread
supervisorctl update

echo "Checking status of dev workers..."
supervisorctl status dev-seal-queue dev-seal-schedule
