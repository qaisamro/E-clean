<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Unify laundry items into the products table.
 *
 * After this migration there is ONE product entity:
 *  - kind = retail  → منتج للبيع (يُباع على الكاشير)
 *  - kind = supply  → مستلزمات المغسلة (استهلاك داخلي)
 *  - kind = laundry → أصناف المغسلة (القميص/الفستان/السجاد …)
 *
 * Items are COPIED — with SHIFTED ids (+offset) — into products, so
 * order_details / item_service rows are atomically rebound to the
 * new product rows inside the same transaction.
 */
return new class extends Migration
{
    public function up(): void
    {
        DB::transaction(function () {
            $offset = (int) (DB::table('products')->max('id') ?? 0) + 100;
            $maxItem = (int) (DB::table('items')->max('id') ?? 0);
            if ($maxItem >= $offset) {
                $offset = $maxItem + 100;
            }

            // Safety: rewrite legacy ITEM morph references to the merged PRODUCT ones
            DB::statement("UPDATE \"stock_movements\" SET \"stockable_type\" = 'App\\Models\\Product', \"stockable_id\" = \"stockable_id\" + ? WHERE \"stockable_type\" = 'App\\Models\\Item'", [$offset]);

            // 1) Carry laundry items forward as products, preserving shape + stock
            DB::statement(
                'INSERT INTO products
                    (id, inventory_category_id, name, sku, kind, unit, purchase_price, sale_price,
                     current_stock, min_stock_alert, is_active, notes, created_at, updated_at)
                 SELECT id + ?, NULL, name, NULL, \'laundry\', unit, purchase_price, NULL,
                        current_stock, min_stock_alert, 1, NULL, created_at, updated_at
                 FROM items',
                [$offset]
            );

            // 2) Rebuild order_details with FK -> products(id), carrying item_id + offset
            DB::statement('CREATE TABLE "order_details_new" (
                "id" integer primary key autoincrement not null,
                "order_id" integer not null,
                "item_id" integer,
                "service_id" integer,
                "price" integer,
                "quantity" integer not null,
                "total_price" integer,
                "is_payment_deferred" tinyint(1) not null default \'0\',
                "description" text,
                "created_at" datetime,
                "updated_at" datetime,
                foreign key("order_id") references "orders"("id") on delete cascade,
                foreign key("item_id") references "products"("id") on delete set null,
                foreign key("service_id") references "services"("id") on delete set null
            )');
            DB::statement('INSERT INTO "order_details_new" SELECT "id", "order_id", "item_id" + ?, "service_id", "price", "quantity", "total_price", "is_payment_deferred", "description", "created_at", "updated_at" FROM "order_details"', [$offset]);
            DB::statement('DROP TABLE "order_details"');
            DB::statement('ALTER TABLE "order_details_new" RENAME TO "order_details"');
            DB::statement('CREATE UNIQUE INDEX "order_details_order_id_item_id_quantity_unique" ON "order_details" ("order_id", "item_id", "quantity")');

            // 3) Rebuild item_service with FK -> products(id), carrying item_id + offset
            DB::statement('CREATE TABLE "item_service_new" (
                "id" integer primary key autoincrement not null,
                "service_id" integer not null,
                "item_id" integer not null,
                "price" integer,
                "note" varchar,
                "created_at" datetime,
                "updated_at" datetime,
                foreign key("service_id") references "services"("id") on delete cascade,
                foreign key("item_id") references "products"("id") on delete cascade
            )');
            DB::statement('INSERT INTO "item_service_new" SELECT "id", "service_id", "item_id" + ?, "price", "note", "created_at", "updated_at" FROM "item_service"', [$offset]);
            DB::statement('DROP TABLE "item_service"');
            DB::statement('ALTER TABLE "item_service_new" RENAME TO "item_service"');
            DB::statement('CREATE UNIQUE INDEX "item_service_item_id_service_id_unique" ON "item_service" ("item_id", "service_id")');

            // 4) items are now products — drop the legacy table
            DB::statement('DROP TABLE "items"');
        });
    }

    public function down(): void
    {
        DB::transaction(function () {
            // offset used by up() = min(laundry product id) - 1 (laundry ids start at 1+offset)
            $offset = (int) DB::table('products')->where('kind', 'laundry')->min('id') - 1;
            if (! $offset) {
                return;
            }

            // Rebuild items from merged laundry products, carrying item_id - offset
            DB::statement('CREATE TABLE "items" (
                "id" integer primary key autoincrement not null,
                "name" varchar not null,
                "created_at" datetime,
                "updated_at" datetime,
                "current_stock" numeric not null default \'0\',
                "min_stock_alert" numeric not null default \'0\',
                "unit" varchar not null default \'قطعة\',
                "purchase_price" numeric
            )');
            DB::statement('INSERT INTO "items" ("id", "name", "created_at", "updated_at", "current_stock", "min_stock_alert", "unit", "purchase_price")
                            SELECT id - ?, name, created_at, updated_at, current_stock, min_stock_alert, unit, purchase_price
                            FROM products WHERE kind = \'laundry\'', [$offset]);

            DB::statement('CREATE TABLE "order_details_new" (
                "id" integer primary key autoincrement not null,
                "order_id" integer not null,
                "item_id" integer,
                "service_id" integer,
                "price" integer,
                "quantity" integer not null,
                "total_price" integer,
                "is_payment_deferred" tinyint(1) not null default \'0\',
                "description" text,
                "created_at" datetime,
                "updated_at" datetime,
                foreign key("order_id") references "orders"("id") on delete cascade,
                foreign key("item_id") references "items"("id") on delete set null,
                foreign key("service_id") references "services"("id") on delete set null
            )');
            DB::statement('INSERT INTO "order_details_new" SELECT "id", "order_id", "item_id" - ?, "service_id", "price", "quantity", "total_price", "is_payment_deferred", "description", "created_at", "updated_at" FROM "order_details"', [$offset]);
            DB::statement('DROP TABLE "order_details"');
            DB::statement('ALTER TABLE "order_details_new" RENAME TO "order_details"');
            DB::statement('CREATE UNIQUE INDEX "order_details_order_id_item_id_quantity_unique" ON "order_details" ("order_id", "item_id", "quantity")');

            DB::statement('CREATE TABLE "item_service_new" (
                "id" integer primary key autoincrement not null,
                "service_id" integer not null,
                "item_id" integer not null,
                "price" integer,
                "note" varchar,
                "created_at" datetime,
                "updated_at" datetime,
                foreign key("service_id") references "services"("id") on delete cascade,
                foreign key("item_id") references "items"("id") on delete cascade
            )');
            DB::statement('INSERT INTO "item_service_new" SELECT "id", "service_id", "item_id" - ?, "price", "note", "created_at", "updated_at" FROM "item_service"', [$offset]);
            DB::statement('DROP TABLE "item_service"');
            DB::statement('ALTER TABLE "item_service_new" RENAME TO "item_service"');
            DB::statement('CREATE UNIQUE INDEX "item_service_item_id_service_id_unique" ON "item_service" ("item_id", "service_id")');

            DB::statement("DELETE FROM \"products\" WHERE kind = 'laundry'");
        });
    }
};
