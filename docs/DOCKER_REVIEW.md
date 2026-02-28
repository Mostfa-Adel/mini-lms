# Docker setup review

## What’s correct

- **Dockerfile**: PHP 8.3, required extensions (intl, redis, etc.), Composer, entrypoint scripts. Base is appropriate for Laravel + Filament.
- **Compose**: Clear split (app, web, db, redis, queue). Named volumes for `dbdata` and `composer_cache`. App uses non-root user and writable paths for TMPDIR, NPM_CONFIG_CACHE, COMPOSER_HOME.
- **entrypoint.sh**: Creates storage dirs, sets writable npm cache, runs composer → migrate → npm, then `exec`. Order is correct (composer before migrate so `artisan` exists).
- **queue-entrypoint.sh**: Waits for `vendor/autoload.php` (up to ~30s), then runs migrate (with retry), then worker; no composer/npm, reuses app’s vendor via shared volume.

---

## Theoretical issues

### 1. **Race: queue (and web) can start before app is ready** — mitigated

`depends_on: app` only waits for the app **container** to start, not for the **entrypoint** to finish. **Queue** now waits for `vendor/autoload.php` before running migrate and starting the worker, so it no longer fails with "artisan not found" on first start. **Web** can still return 502 until php-fpm is up (after app entrypoint finishes); with `restart: unless-stopped` it usually recovers.

---

### 2. **Dockerfile: no build-time composer** — current state

The Dockerfile does **not** run `composer install` or copy app code at build time. Dependencies are installed only at **runtime** by the app entrypoint when `.:/var/www` is mounted. The image builds quickly and is dev-oriented. For production you would use a different Dockerfile (copy app, run `composer install --no-dev`, no volume mount).

---

### 3. **Migration errors hidden**

In both entrypoints, `php artisan migrate 2>/dev/null` hides the real error when migrate fails. On the 10th failed attempt you exit 1 but the user doesn’t see the MySQL (or other) error. For debugging, you could run migrate without `2>/dev/null` on the last attempt, or log the output.

---

### 4. **Compose `command` for queue**

Queue uses:

```yaml
command: php artisan queue:work --sleep=3 --tries=3 --max-time=3600
```

This is a string; Compose runs it via the shell. Functionally fine. Using the exec form (array) would be slightly more explicit and avoid an extra shell, but not required.

---

## Summary

The **startup race** for the queue is addressed: `queue-entrypoint.sh` waits for `vendor/autoload.php` before running migrate and the worker. The Dockerfile no longer runs composer at build time. Remaining points (hidden migrate errors, command format) are optional hardening or clarity.
