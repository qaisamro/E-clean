# E-Clean Global Service Catalog

## Scope

This change makes services a super-admin-managed global catalog. Vendors
can only associate products with active catalog services and cannot manage
services or variants. The Laravel backend was deployed on 2026-11-10; Flutter
has not been deployed. See `Web_Admin/docs/GLOBAL_SERVICE_CATALOG_DEPLOYMENT.md`.

## Backend behavior

- Service management is restricted to root/admin accounts.
- Vendor service and variant routes return HTTP 403, including direct URLs.
- Vendor product create/edit forms list active global services only.
- The variant field is hidden for vendors; a compatible internal variant is
  selected by the backend so existing product/order schemas remain intact.
- `GET /api/services` returns the fixed active global catalog.
- `GET /api/services/{service}/vendors` returns active stores that have an
  active top-level product associated with that service and respects `city_id`.
- Existing product prices, order data, delivery fees, and customer data are not
  modified.

## Flutter behavior

- Home services come from the global service catalog.
- Selecting a service opens a city-aware list of stores that offer it.
- Selecting a store reuses the existing `VendorStoreScreen` with that vendor.
- Loading, retry, and empty-result states are included with Arabic RTL support.

## Production data and migration

Production was inspected read-only. Existing services are partly vendor-owned,
so the migration `2026_11_09_210000_adopt_existing_services_as_global_catalog`
is required before releasing the new Flutter behavior. It backs up each legacy
service owner in `service_catalog_legacy_owners`, then makes the catalog global.
It does not delete services or modify products, prices, or orders, and its
rollback restores the previous owners.

The existing catalog includes duplicate/test-looking entries. They are not
deleted automatically. After deployment the super admin should review, rename,
deactivate, or merge them deliberately while preserving product associations.

## Verification

- Laravel focused suite: 15 tests passed, 202 assertions.
- Global service catalog suite: 3 tests passed, 28 assertions.
- Flutter city and stores-map suites: 16 tests passed.
- Dart analysis of the five changed feature files: no errors; existing project
  lint/info notices remain.
- Blade views compiled successfully and the local compiled-view cache was
  cleared.

## Safe release order

1. Back up the production database and affected Laravel files.
2. Deploy only the backend catalog/API/form changes and the single migration.
3. Run only the named migration and clear Laravel caches.
4. Verify admin service management, vendor 403 isolation, vendor product
   creation, `/api/services`, and `/api/services/{id}/vendors?city_id=...`.
5. Verify legacy Flutter/API calls and a complete order flow.
6. Release Flutter only after backend verification succeeds.

