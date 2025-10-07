#!/usr/bin/env bash
set -euo pipefail
export APP_ENV=${APP_ENV:-dev}
php -d detect_unicode=0 -d variables_order=EGPCS vendor/bin/console messenger:consume async -vv --time-limit=60 --memory-limit=256M
