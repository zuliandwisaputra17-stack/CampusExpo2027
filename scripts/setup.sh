#!/usr/bin/env bash
set -euo pipefail
cd "$(dirname "${BASH_SOURCE[0]}")/.."
if ! command -v php >/dev/null 2>&1 && [[ -x /workspace/tooling/bin/php ]]; then
  export PATH="/workspace/tooling/bin:$PATH"
fi
if ! command -v php >/dev/null 2>&1; then
  echo 'PHP 8.4+ beserta ekstensi Laravel, GD, SQLite/MySQL diperlukan.' >&2
  exit 1
fi
if command -v composer >/dev/null 2>&1; then
  composer install --no-interaction --prefer-dist
elif [[ -f /workspace/tooling/composer.phar ]]; then
  export COMPOSER_HOME=/workspace/tooling/composer COMPOSER_CACHE_DIR=/workspace/tooling/composer-cache
  php /workspace/tooling/composer.phar install --no-interaction --prefer-dist
else
  echo 'Composer 2 diperlukan.' >&2
  exit 1
fi
if [[ ! -f .env ]]; then cp .env.example .env; fi
if ! php -r '$s=file_get_contents(".env"); exit(preg_match("/^APP_KEY=.+$/m",$s) ? 0 : 1);'; then php artisan key:generate --force; fi
if [[ ! -f database/database.sqlite ]]; then touch database/database.sqlite; fi
php artisan migrate --force
if [[ "${1:-}" != '--no-demo' ]]; then php artisan expo:seed-demo; fi
if [[ ! -e public/storage && ! -L public/storage ]]; then php artisan storage:link; fi
if [[ -d /workspace/tooling ]]; then export npm_config_cache="${npm_config_cache:-/workspace/tooling/npm-cache}"; fi
npm ci --no-audit --no-fund
npm run build
