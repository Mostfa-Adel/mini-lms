#!/bin/sh
set -e

# Ensure storage dirs exist (volume mount may have immutable files — don't fail on chmod)
mkdir -p storage/framework/sessions storage/framework/views storage/framework/cache storage/app/tmp storage/framework/npm-cache
chmod -R 775 storage 2>/dev/null || true
export TMPDIR="${TMPDIR:-/var/www/storage/framework}"
# npm cache must be writable (container runs as non-root)
export NPM_CONFIG_CACHE="${NPM_CONFIG_CACHE:-/var/www/storage/framework/npm-cache}"


# Install PHP deps (with dev for tests, Pest, etc.) when composer.json is present
if [ -f composer.json ]; then
  composer install
fi

# Run migrations so cache table exists before queue worker starts (retry until DB is ready)
if [ -f artisan ]; then
  for i in 1 2 3 4 5 6 7 8 9 10; do
    if php artisan migrate 2>/dev/null; then
      break
    fi
    [ "$i" = 10 ] && exit 1
    sleep 2
  done
fi
# Install Node deps and build assets (so app works without the optional vite service)
if [ -f package.json ]; then
  npm ci && npm run build
fi

exec "$@"
