#!/bin/sh
set -e

# Wait for app entrypoint to finish (vendor/ exists) so we don't run artisan before composer install
for i in 1 2 3 4 5 6 7 8 9 10 11 12 13 14 15; do
  [ -f vendor/autoload.php ] && break
  [ "$i" = 15 ] && echo "queue: vendor/ not ready after 30s" && exit 1
  sleep 2
done

# Wait for DB and run migrations so cache table exists (queue:work uses it for restart signal)
for i in 1 2 3 4 5 6 7 8 9 10; do
  if php artisan migrate 2>/dev/null; then
    break
  fi
  [ "$i" = 10 ] && exit 1
  sleep 2
done

exec "$@"
