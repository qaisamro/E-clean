# Installer routes removal deployment

Deployed to `https://elitecleanps.com` on 2026-11-10 (Asia/Jerusalem).

## Production file

- `app/Providers/AppServiceProvider.php`

No package files, migrations, database records, Flutter files, or tests were uploaded.

## Backup and rollback

Backup:

`/home/u205641829/backups/installer-routes-20261109T234145Z/app/Providers/AppServiceProvider.php`

Rollback:

```bash
cp /home/u205641829/backups/installer-routes-20261109T234145Z/app/Providers/AppServiceProvider.php \
  /home/u205641829/domains/elitecleanps.com/laravel/app/Providers/AppServiceProvider.php

cd /home/u205641829/domains/elitecleanps.com/laravel
php artisan optimize:clear
php artisan route:cache
```

## Verification

- Production SHA-256: `380442ddf7decf083c62af877a923ca7e08729c22ba43c0adb5a949f8c5ca8ab`
- `php artisan route:list --path=install --json` returned `[]`.
- All nine installer endpoints returned HTTP 404.
- `/api/vendors` returned HTTP 200.
- `/` returned HTTP 302 to the existing authentication flow.
- No new Laravel error appeared in the inspected log tail.
