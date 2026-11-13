# Web live notifications

## Scope

This local change adds authenticated, role-scoped web notifications without requiring a page refresh or a paid real-time service.

- Super admin: pending join requests whose source is `mobile_app` only; new-order notifications are intentionally excluded.
- Vendor owner: unseen orders belonging to that vendor only.
- Driver: orders currently assigned to that driver and unread records from `driver_notifications`.
- Other roles: the feed is denied.

The browser polls `GET /web-notifications/feed` every 8 seconds while the page is visible and backs off when it is hidden or the request fails. Notification content is inserted with `textContent`, not raw HTML.

## Web sound

The sound is generated locally with the browser Web Audio API and is not added to Flutter or mobile notifications. Browsers require a user interaction before audio may play, so the first click or key press unlocks sound. A volume button beside the bell saves the mute preference in `localStorage` per signed-in user.

The first feed load establishes a baseline and does not play historical notifications. A sound and toast are produced only when a new stable notification key appears after that baseline.

## Files

- `Web_Admin/app/Http/Controllers/Web/WebNotificationController.php`
- `Web_Admin/app/Http/Middleware/CheckPermission.php`
- `Web_Admin/routes/web.php`
- `Web_Admin/resources/views/layouts/app.blade.php`
- `Web_Admin/tests/Feature/WebNotificationFeedTest.php`

## Database and deployment

No migration or database data change is required. The implementation reuses `orders`, `driver_orders`, `driver_notifications`, and `customer_join_requests`.

## Production deployment

Deployed to `elitecleanps.com` on 2026-11-09 after explicit approval.

- Production path: `/home/u205641829/domains/elitecleanps.com/laravel`
- Backup: `/home/u205641829/backups/web-notifications-20261109T195545Z`
- No migration was run and no production data was modified.
- Only the four runtime files listed below were uploaded; tests and documentation were not uploaded.
- The three shared production files were downloaded and compared first. Their only differences from the local files were the notification feature lines, so no newer production edit was overwritten.
- Laravel route, view, and config caches were cleared; Blade compilation succeeded.

Production verification:

- The release archive SHA-256 was verified before extraction.
- PHP syntax passed for the controller, middleware, and routes.
- `web-notifications.feed` is registered behind `auth` and `permission_check`.
- A read-only production smoke test returned HTTP 200 for available root, vendor owner, and driver accounts.
- The vendor and driver payload validation found no cross-role notification types or cross-vendor order exposure.
- Unauthenticated feed access returns HTTP 302 to login; `/login` and `/api/vendors` return HTTP 200.
- Laravel log contains no entry naming `WebNotificationController` or `/web-notifications/feed`.
- Temporary deployment and smoke-test files were removed from `/tmp`.

Rollback restores the three shared files from the backup and removes the new controller, then clears route/view/config caches. No database rollback is required.

## Verification

- `php artisan test --filter=WebNotificationFeedTest`: 3 passed, 14 assertions.
- PHP syntax checks passed.
- `php artisan route:list --name=web-notifications.feed`: route registered.
- Blade cache compilation passed and the compiled cache was cleared afterward.

The broader related test command is currently blocked before test logic by two existing MySQL-specific `ALTER TABLE ... MODIFY ENUM` migrations when the suite uses SQLite. This is unrelated to the notification implementation; the notification feature test uses the repository's SQLite-safe migration helper and passes.

The production log also contains an unrelated pre-existing order-list view error for an order whose address relation is null (`orders/index.blade.php`, `address_name`). It is not emitted by the notification endpoint and was not changed as part of this deployment.

### Super-admin order exclusion correction

After clarification, production was updated so new orders are notified only to their owning vendor and never to root/admin accounts. Root/admin continue to receive mobile-app join requests only. The correction changed `WebNotificationController.php` alone; no migration or data change was performed.

- Correction backup: `/home/u205641829/backups/web-notifications-admin-order-exclusion-20261109T203000Z`
- Local regression result: 3 tests passed, 16 assertions.
- Production read-only smoke result: root, vendor owner, and driver feeds returned HTTP 200; the root feed contained no `order` item, and no notification-controller error appeared in Laravel logs.
