# Removal of additional services and sub-products

Implemented and deployed to the Laravel production backend on 2026-11-10.
See `LEGACY_ADDITIONALS_SUBPRODUCTS_DEPLOYMENT.md` for the backup and verification.

## Visible behavior

- Removed all web management routes for additional services and sub-products.
- Removed their links from the sidebar, services, products, order details, and
  vendor dashboard.
- Added a Services link to the super-admin/admin sidebar.
- Made the safe service delete action visible on the super-admin services page.
- Services linked to products cannot be deleted; the existing clear warning
  directs the admin to deactivate them instead.
- Product API keeps `sub_products` as an empty array for old-client response
  compatibility, but never exposes retired sub-products.
- Legacy `additional_service_id` input is accepted harmlessly for installed
  clients but is ignored and cannot affect order totals or create associations.

## Data safety

No migration deletes tables or records. Dormant models, relationships, and old
database tables remain solely to preserve historical orders and allow safe data
auditing. They have no active management route or current UI.

## Verification

- Removed-feature suite: 4 tests passed, 20 assertions.
- Combined catalog and vendor-isolation suite before the last added amount test:
  11 tests passed, 129 assertions.
- PHP syntax checks passed for all changed PHP classes and routes.
- Blade templates compiled successfully.
- Route inventory contains no `additional.*` or `product.subproduct.*` route.
- Active relevant views contain no additional-service or sub-product link/text.

