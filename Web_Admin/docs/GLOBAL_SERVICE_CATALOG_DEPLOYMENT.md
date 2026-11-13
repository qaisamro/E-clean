# Global Service Catalog — production deployment

Deployed to `https://elitecleanps.com` on 2026-11-10 (Asia/Jerusalem).
Flutter was not deployed.

## Scope

The deployment contains only the global-service catalog backend, vendor product
form restrictions, one API route, and the single catalog adoption migration.
The production `VendorController` was used as the base and patched only for this
feature; unrelated local rating/phone changes were deliberately excluded.

Published files:

- `app/Models/Service.php`
- `app/Repositories/ServiceRepository.php`
- `app/Http/Controllers/Web/Services/ServiceController.php`
- `app/Http/Middleware/CheckPermission.php`
- `app/Http/Controllers/Web/Products/ProductController.php`
- `app/Http/Requests/ProductRequest.php`
- `app/Repositories/ProductRepository.php`
- `app/Http/Controllers/API/Service/ServiceController.php`
- `app/Http/Controllers/API/VendorController.php`
- `resources/views/layouts/partials/sidebar.blade.php`
- `resources/views/services/create.blade.php`
- `resources/views/services/edit.blade.php`
- `resources/views/products/create.blade.php`
- `resources/views/products/edit.blade.php`
- `routes/api.php`
- `database/migrations/2026_11_09_210000_adopt_existing_services_as_global_catalog.php`

## Backup and rollback

The protected server backup is:

`/home/u205641829/backups/service-catalog-20261109T220502Z`

The UTC suffix corresponds to 2026-11-10 local time. It contains copies of all
15 replaced files, the release archive, before/after hashes, a complete logical
SQL backup of all 77 tables, data invariants, and `rollback.sh`/`ROLLBACK.txt`.
The database SHA-256 is:

`9dc43f5135092d1e65662b6a02d59807e42c14c4843020e5cce39159ab497943`

Ordinary rollback command:

`/home/u205641829/backups/service-catalog-20261109T220502Z/rollback.sh`

It rolls back the named migration, restores the replaced files, removes the new
migration file, and clears scoped Laravel caches. OPcache must then be invalidated
through Web SAPI. The SQL dump is disaster-recovery material and should not be
imported for an ordinary code rollback.

## Migration result

- Migration ran in batch 18.
- Six existing services are now global (`vendor_id = null`).
- Five previous non-null owners are preserved in
  `service_catalog_legacy_owners` for rollback.
- No service, product, price, order, or delivery fee was deleted or edited.

## Verification

- All 15 existing production files matched the pre-deployment hashes immediately
  before activation.
- All 16 release PHP files passed syntax checking.
- Laravel route, view, and config caches were cleared successfully.
- Web-SAPI OPcache invalidated all 16 published files; the temporary public tool
  was removed.
- `GET /api/services`: HTTP 200, five active global services.
- `GET /api/services/11/vendors`: HTTP 200 and returned the matching store.
- City-filtered service vendors for Dura: HTTP 200.
- Invalid `city_id`: HTTP 422.
- Existing vendor-services, products, vendors, and master APIs: HTTP 200.
- Existing vendor response fields are all present.
- Unauthenticated addresses and orders APIs remain HTTP 401.
- Products, orders, order-products, delivery costs, users, roles, and permissions
  matched the pre-deployment backup byte-for-byte at row-dump level.
- No new Laravel error or critical log entry appeared during deployment checks.
- No temporary service-catalog file remains under the public directory or `/tmp`.

## Release dependency

The backend is ready for the new Flutter service-store screen. Flutter still
requires a separate build/release approval.

