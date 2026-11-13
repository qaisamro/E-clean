# Vendor POS access correction

Production diagnosis (2026-11-08, Asia/Jerusalem): `vendor_admin` has no `pos.index` or `pos.sales` permission. The sidebar displays these routes for that role, while `CheckPermission` correctly requires the matching permissions. Vendor accounts with and without a linked store were observed; no account associations were modified.

Local correction:
- `database/seeders/VendorPosPermissionSeeder.php` adds only the eight existing POS route permissions to the existing web `vendor_admin` role in a transaction. Existing permissions remain intact; repeated runs are safe. No direct user grants, price editing permission or administrator permissions are added.
- `database/seeders/DatabaseSeeder.php` includes this seeder for future installations.
- `tests/Feature/VendorOrderIsolationTest.php` exercises role permissions, repeated seeding, both pages, customer exclusion, absence of price editing permission and isolation of other vendors' orders.

No migration, controller, prices, orders, OTP or Passport changes.

Deployed with explicit approval on 2026-11-08 (Asia/Jerusalem). Backup: `/home/u205641829/backups/vendor-pos-20261107T221142Z/` (UTC timestamp). The existing DatabaseSeeder matched the reconstructed pre-change local file exactly. Only the new seeder and its registration line were uploaded. File hashes and PHP syntax were checked before installation. Existing permission tables and DatabaseSeeder were backed up. Executed only:

```sh
php artisan db:seed --class=VendorPosPermissionSeeder --force
php artisan permission:cache-reset
```

Do not execute the entire DatabaseSeeder on production; it performs unrelated setup including Passport installation. Verify using an existing linked vendor without creating or modifying real orders. Restore the backed-up role mappings to roll back the permission grant. Do not remove permissions that existed before deployment.

Validation: local targeted suite passed (5 tests, 83 assertions); full Laravel suite passed (266 tests, 1244 assertions). On production, requests through the Laravel HTTP kernel with the two existing linked vendor users both returned HTTP 200 for `/pos` and `/pos/sales`; returned sales were checked against each vendor ID. No production order was modified. Exactly eight grants were added to the vendor role; existing role grants and direct user grants were preserved. External login and vendors HTTP 200; unauthenticated POS HTTP 302. Laravel log stayed at 261039 bytes. Orphan vendor accounts were not associated with any store automatically.
