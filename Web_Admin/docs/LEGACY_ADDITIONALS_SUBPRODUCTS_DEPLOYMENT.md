# Additional services and sub-products removal — deployment

Deployed to `https://elitecleanps.com` on 2026-11-10 (Asia/Jerusalem).
No Flutter build was published and no migration was run.

## Published scope

Eleven files were published:

- `routes/web.php`
- `app/Http/Controllers/Web/Services/ServiceController.php`
- `resources/views/services/edit.blade.php`
- `resources/views/services/index.blade.php`
- `resources/views/layouts/partials/sidebar.blade.php`
- `resources/views/products/index.blade.php`
- `resources/views/vendor/dashboard.blade.php`
- `app/Http/Resources/ProductResource.php`
- `app/Http/Controllers/API/Order/OrderController.php`
- `app/Repositories/OrderRepository.php`
- `app/Http/Requests/ProductRequest.php`

`resources/views/orders/show.blade.php` changed concurrently on production during
the pre-deployment comparison. Its newest production version already removed
the additional-service section, so it was deliberately excluded rather than
overwritten. The current production Blade set compiles successfully.

## Backup

Protected server backup:

`/home/u205641829/backups/legacy-removal-20261109T224020Z`

The suffix is UTC. The backup includes all replaced files, before/after hashes,
the release archive, a complete logical SQL backup of 78 tables, data
invariants, and `rollback.sh`/`ROLLBACK.txt`.

Database SHA-256:

`edd7bcc579d8c691d7e5d2735858d63eec8f65617db41c93dfabbf7359c02f31`

Rollback command:

`/home/u205641829/backups/legacy-removal-20261109T224020Z/rollback.sh`

No database import is needed for ordinary rollback because this release did not
write production data.

## Production verification

- All eleven original files matched their downloaded hashes immediately before
  activation.
- All release PHP files passed syntax checks.
- Laravel route, config, and view caches were cleared.
- Web-SAPI OPcache invalidated the eleven published files; the temporary public
  endpoint was deleted.
- `/additional-services`, `/products/1/subproducts`, and `/sub-product/1/edit`
  return HTTP 404.
- No `additional.*` or `product.subproduct.*` route remains.
- `service.delete` remains available as a DELETE route.
- The admin sidebar contains the Services link and no additional-services link.
- `GET /api/products` returns HTTP 200; all eight current products expose the
  backward-compatible `sub_products` key as an empty array.
- Additional-service input cannot affect new-order totals or associations.
- Additionals, their pivot tables, products/prices, order-products, services,
  users, roles, and permissions matched the pre-deployment backup exactly.
- The order count remained unchanged; one existing order row changed during the
  deployment window while `order_products` stayed identical, consistent with a
  normal concurrent status/update operation and not a deployment write.
- The final production Blade compilation succeeded.

The log contains unrelated earlier application entries (`web.cart`) and the
deployment diagnostic's failed `sum(id)` query against a pivot table. Neither
was emitted by the published feature. No temporary deployment file remains in
the public directory or `/tmp`.

