#!/usr/bin/env bash
set -euo pipefail
cd "$(dirname "${BASH_SOURCE[0]}")/.."
if ! command -v php >/dev/null 2>&1 && [[ -x /workspace/tooling/bin/php ]]; then
  export PATH="/workspace/tooling/bin:$PATH"
fi
exec php artisan serve --no-reload --host=0.0.0.0 --port="${PORT:-8000}"
